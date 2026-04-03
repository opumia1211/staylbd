<?php

namespace App\Console\Commands;

use App\Models\PaymentLedger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LedgerVerifyCommand extends Command
{
    protected $signature = 'ledger:verify';
    protected $description = 'Verify payment ledger integrity (hash chain)';

    public function handle(): int
    {
        $errors = PaymentLedger::verifyIntegrity();
        if (empty($errors)) {
            $this->info('Payment ledger integrity: OK');
            return 0;
        }
        $this->error('Payment ledger integrity FAILED. Errors: ' . count($errors));
        foreach ($errors as $e) {
            $this->line("  ID {$e['id']}: expected {$e['expected']}, got {$e['got']}");
        }
        Log::channel('security')->critical('Payment ledger integrity verification failed', ['errors' => $errors]);
        return 1;
    }
}
