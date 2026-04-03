<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminLockout;
use App\Models\AdminSession;
use App\Models\PaymentEvent;
use App\Models\SecurityEvent;
use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SecurityScanCommand extends Command
{
    protected $signature = 'security:scan {--email : Send report via email}';
    protected $description = 'Scan for security issues: expired sessions, failed attempts, duplicate webhooks, error spike, disabled 2FA for mandatory roles';

    public function handle(): int
    {
        $issues = [];
        $summary = [];

        // 1. Expired sessions (admin_sessions orphaned)
        $orphanedSessions = AdminSession::where('last_activity_at', '<', now()->subDays(7))->count();
        if ($orphanedSessions > 0) {
            $issues[] = "Expired/stale admin sessions: {$orphanedSessions}";
        }

        // 2. Too many failed attempts (lockouts)
        $activeLockouts = AdminLockout::whereNotNull('locked_at')
            ->where('locked_at', '>', now())
            ->count();
        if ($activeLockouts > 0) {
            $issues[] = "Active IP lockouts: {$activeLockouts}";
        }

        // 3. Duplicate webhook attempts (payment_events with same idempotency - shouldn't have duplicates due to unique)
        $duplicateAttempts = SecurityEvent::where('event_type', 'payment_replay_attempt')->where('created_at', '>', now()->subDay())->count();
        if ($duplicateAttempts > 0) {
            $issues[] = "Payment replay attempts (24h): {$duplicateAttempts}";
        }

        // 4. Large error spike (security_events critical in last hour)
        $criticalCount = SecurityEvent::where('severity', 'critical')->where('created_at', '>', now()->subHour())->count();
        if ($criticalCount > 5) {
            $issues[] = "Critical security events (1h): {$criticalCount}";
        }

        // 5. Disabled 2FA for mandatory roles
        $mandatoryRoles = config('admin.two_factor_mandatory_roles', ['owner', 'super_admin']);
        $without2FA = Admin::whereIn('role', $mandatoryRoles)
            ->where(function ($q) {
                $q->whereNull('two_factor_confirmed_at')->orWhereNull('two_factor_secret');
            })
            ->count();
        if ($without2FA > 0) {
            $issues[] = "Owner/SuperAdmin without 2FA: {$without2FA}";
        }

        // 6. Audit log integrity
        $auditErrors = AuditLog::verifyIntegrity();
        if (!empty($auditErrors)) {
            $issues[] = "Audit log integrity failures: " . count($auditErrors);
        }

        $summary['issues'] = $issues;
        $summary['issue_count'] = count($issues);

        $this->output->writeln('');
        $this->output->writeln('=== Security Scan Report ===');
        if (empty($issues)) {
            $this->output->writeln('No issues detected.');
        } else {
            foreach ($issues as $issue) {
                $this->output->writeln('  - ' . $issue);
            }
        }
        $this->output->writeln('');

        Log::channel('security')->info('Security scan completed', $summary);

        if ($this->option('email') && !empty($issues)) {
            $email = config('mail.from.address') ?: env('CRITICAL_ERROR_EMAIL');
            if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    \Illuminate\Support\Facades\Mail::raw(
                        "Security Scan Report:\n\n" . implode("\n", $issues),
                        fn ($m) => $m->to($email)->subject('StayLBD Security Scan Report')
                    );
                } catch (\Throwable $e) {
                    $this->error('Failed to send email: ' . $e->getMessage());
                }
            }
        }

        return 0;
    }
}
