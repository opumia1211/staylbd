<?php

namespace App\Console\Commands;

use App\Models\SupportAttachment;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Console\Command;

class CleanupOldTicketsCommand extends Command
{
    protected $signature = 'tickets:cleanup {--days=60 : Delete tickets older than this many days}';

    protected $description = 'Delete support tickets and related data older than 60 days (admin retention)';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        if ($days < 1) {
            $days = 60;
        }
        $cutoff = now()->subDays($days);

        $tickets = SupportTicket::where('created_at', '<', $cutoff)->pluck('id');
        $count = $tickets->count();

        if ($count === 0) {
            $this->info("No tickets older than {$days} days found.");
            return Command::SUCCESS;
        }

        foreach ($tickets as $ticketId) {
            $messages = SupportMessage::where('support_ticket_id', $ticketId)->get();
            foreach ($messages as $msg) {
                $attachments = SupportAttachment::where('support_message_id', $msg->id)->get();
                foreach ($attachments as $att) {
                    $path = getFilePath('ticket');
                    if ($path && $att->attachment) {
                        fileManager()->removeFile($path . '/' . $att->attachment);
                    }
                    $att->delete();
                }
                $msg->delete();
            }
            SupportTicket::where('id', $ticketId)->delete();
        }

        $this->info("Deleted {$count} tickets older than {$days} days.");
        return Command::SUCCESS;
    }
}
