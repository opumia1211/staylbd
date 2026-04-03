<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminLockout;
use App\Models\AuditLog;
use App\Models\PaymentEvent;
use App\Models\PaymentLedger;
use App\Models\SecurityEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SecurityReportCommand extends Command
{
    protected $signature = 'security:report {--month= : YYYY-MM} {--email : Send to Owner}';
    protected $description = 'Generate monthly security report';

    public function handle(): int
    {
        $month = $this->option('month') ?: now()->format('Y-m');
        if (!preg_match('/^\d{4}-\d{2}$/', $month) || $month === 'YYYY-MM') {
            $month = now()->format('Y-m');
            $this->warn("Invalid --month format. Use YYYY-MM (e.g. 2026-02). Using current month: {$month}");
        }
        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $report = [
            'period' => $month,
            'failed_logins' => SecurityEvent::where('event_type', 'failed_admin_login')->whereBetween('created_at', [$start, $end])->count(),
            'lockouts' => AdminLockout::whereBetween('locked_at', [$start, $end])->count(),
            'replay_attempts' => SecurityEvent::where('event_type', 'payment_replay_attempt')->whereBetween('created_at', [$start, $end])->count(),
            'payment_sig_failures' => PaymentEvent::where('event_type', 'signature_failed')->whereBetween('created_at', [$start, $end])->count(),
            'high_risk_actions' => SecurityEvent::where('event_type', 'high_risk_action_verified')->whereBetween('created_at', [$start, $end])->count(),
            'audit_integrity' => empty(AuditLog::verifyIntegrity()),
            'ledger_integrity' => empty(PaymentLedger::verifyIntegrity()),
        ];

        $lines = [
            "=== Security Report: {$month} ===",
            "Failed logins: {$report['failed_logins']}",
            "Lockouts: {$report['lockouts']}",
            "Replay attempts: {$report['replay_attempts']}",
            "Payment signature failures: {$report['payment_sig_failures']}",
            "High-risk actions verified: {$report['high_risk_actions']}",
            "Audit integrity: " . ($report['audit_integrity'] ? 'OK' : 'FAILED'),
            "Ledger integrity: " . ($report['ledger_integrity'] ? 'OK' : 'FAILED'),
        ];

        $this->output->writeln('');
        foreach ($lines as $line) {
            $this->line($line);
        }
        $this->output->writeln('');

        Log::channel('security')->info('Security report generated', $report);

        if ($this->option('email')) {
            $owner = Admin::where('role', 'owner')->first();
            $email = $owner?->email ?: config('mail.from.address') ?: env('CRITICAL_ERROR_EMAIL');
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    $monthVal = $month;
                    Mail::raw(implode("\n", $lines), function ($m) use ($email, $monthVal) {
                        $m->to($email)->subject("StayLBD Security Report – {$monthVal}");
                    });
                } catch (\Throwable $e) {
                    $this->error('Failed to send email: ' . $e->getMessage());
                }
            }
        }

        return 0;
    }
}
