<?php

namespace App\Console\Commands;

use App\Models\SecurityEvent;
use App\Models\PaymentEvent;
use App\Models\AuditLog;
use Illuminate\Console\Command;

class DataRetentionEnforceCommand extends Command
{
    protected $signature = 'data:retention:enforce';
    protected $description = 'Enforce data retention policy – purge security_events, retain payment_events and audit_logs per config';

    public function handle(): int
    {
        $securityDays = (int) env('DATA_RETENTION_SECURITY_DAYS', 90);
        $auditYears = (int) env('DATA_RETENTION_AUDIT_YEARS', 7);
        $paymentYears = (int) env('DATA_RETENTION_PAYMENT_YEARS', 7);

        $securityCutoff = now()->subDays($securityDays);
        $auditCutoff = now()->subYears($auditYears);
        $paymentCutoff = now()->subYears($paymentYears);

        $secDeleted = SecurityEvent::where('created_at', '<', $securityCutoff)->delete();
        $auditDeleted = AuditLog::where('created_at', '<', $auditCutoff)->delete();
        $paymentDeleted = PaymentEvent::where('created_at', '<', $paymentCutoff)->delete();

        $this->info("Security events older than {$securityDays} days deleted: {$secDeleted}");
        $this->info("Audit logs older than {$auditYears} years deleted: {$auditDeleted}");
        $this->info("Payment events older than {$paymentYears} years deleted: {$paymentDeleted}");

        return 0;
    }
}
