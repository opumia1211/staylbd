<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AuditVerifyCommand extends Command
{
    protected $signature = 'audit:verify';
    protected $description = 'Verify audit log integrity (hash chain)';

    public function handle(): int
    {
        $errors = AuditLog::verifyIntegrity();
        if (empty($errors)) {
            $this->info('Audit log integrity: OK');
            return 0;
        }
        $this->error('Audit log integrity FAILED. Errors: ' . count($errors));
        foreach ($errors as $e) {
            $this->line("  Log ID {$e['log_id']}: expected {$e['expected']}, got {$e['got']}");
        }
        Log::channel('security')->critical('Audit log integrity verification failed', ['errors' => $errors]);
        return 1;
    }
}
