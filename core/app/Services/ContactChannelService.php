<?php

namespace App\Services;

use App\Constants\Status;
use App\Models\ContactChannelIntegration;
use App\Models\ContactChannelMessage;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ContactChannelService
{
    public function getActiveIntegrations(bool $useCache = true): Collection
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('contact_channel_integrations')) {
                return collect();
            }
            if (!$useCache) {
                return ContactChannelIntegration::where('is_active', true)->orderBy('channel')->get();
            }
            return Cache::remember('contact_channels.active', now()->addMinutes(10), function () {
                return ContactChannelIntegration::where('is_active', true)->orderBy('channel')->get();
            });
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function getIntegrationForChannel(string $channel): ?ContactChannelIntegration
    {
        $channel = strtolower($channel);
        return $this->getActiveIntegrations()->firstWhere('channel', $channel);
    }

    /**
     * Persist a unified contact-channel message and mirror it into support tickets.
     *
     * @param  array  $payload
     * @return \App\Models\ContactChannelMessage
     */
    public function logMessage(array $payload): ContactChannelMessage
    {
        $channel = strtolower((string) ($payload['channel'] ?? 'web'));
        $remoteMessageId = $payload['remote_message_id'] ?? null;

        if ($remoteMessageId) {
            $existing = ContactChannelMessage::where('channel', $channel)
                ->where('remote_message_id', $remoteMessageId)
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        $userId = $payload['user_id'] ?? $this->resolveUserId($payload);
        $reference = $payload['remote_chat_id'] ?? ($payload['sender_handle'] ?? null);
        $ticket = $this->ensureTicket(
            $channel,
            $payload['subject'] ?? __('Channel Message'),
            $payload['sender_name'] ?? null,
            $payload['email'] ?? null,
            $userId,
            $reference
        );

        $message = ContactChannelMessage::create([
            'contact_channel_integration_id' => $payload['contact_channel_integration_id'] ?? optional($this->getIntegrationForChannel($channel))->id,
            'support_ticket_id' => $ticket?->id,
            'user_id' => $userId,
            'channel' => $channel,
            'direction' => strtolower((string) ($payload['direction'] ?? 'inbound')),
            'remote_chat_id' => $reference,
            'remote_message_id' => $remoteMessageId,
            'sender_name' => $payload['sender_name'] ?? null,
            'sender_handle' => $payload['sender_handle'] ?? null,
            'subject' => $payload['subject'] ?? null,
            'message' => $payload['message'] ?? null,
            'attachments' => $payload['attachments'] ?? null,
            'metadata' => $payload['metadata'] ?? null,
            'status' => $payload['status'] ?? 'queued',
            'delivered_at' => $payload['delivered_at'] ?? null,
            'read_at' => $payload['read_at'] ?? null,
        ]);

        if ($ticket) {
            $this->mirrorSupportMessage($ticket, $message, $payload);
        }

        return $message;
    }

    public function buildChatFeedForUser(User $user): array
    {
        $ticketMessages = $this->formatTicketMessages($user);
        $channelMessages = ContactChannelMessage::where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if ($user->whatsapp_identity) {
                    $query->orWhere('remote_chat_id', $this->normalizePhone($user->whatsapp_identity));
                }
                if ($user->telegram_username) {
                    $query->orWhere('remote_chat_id', strtolower($user->telegram_username));
                }
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function (ContactChannelMessage $msg) {
                $dt = $msg->created_at ?? now();
                return [
                    'id' => 'channel-' . $msg->id,
                    'message' => $msg->message,
                    'is_admin' => $msg->direction === 'outbound',
                    'name' => $msg->direction === 'outbound' ? __('Support Team') : ($msg->sender_name ?: __('You')),
                    'created_at' => $dt->format('g:i A'),
                    'created_at_full' => $dt->format('M d, H:i'),
                    'date_label' => $this->dateLabel($dt),
                    'attachments' => Arr::wrap($msg->attachments),
                    'channel' => $msg->channel,
                    'direction' => $msg->direction,
                    'timestamp' => $dt->getTimestamp(),
                ];
            })->toArray();

        $merged = collect(array_merge($ticketMessages, $channelMessages))
            ->sortBy('timestamp')
            ->values()
            ->toArray();

        return $merged;
    }

    protected function formatTicketMessages(User $user): array
    {
        $ticketIds = SupportTicket::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->pluck('id');

        if ($ticketIds->isEmpty()) {
            return [];
        }

        return SupportMessage::whereIn('support_ticket_id', $ticketIds)
            ->with(['admin', 'ticket', 'attachments'])
            ->orderBy('id')
            ->get()
            ->map(function (SupportMessage $msg) {
                $ticket = $msg->ticket;
                $dt = $msg->created_at ?? now();
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_admin' => (bool) $msg->admin_id,
                    'name' => $msg->admin_id ? ($msg->admin->name ?? 'Staff') : ($ticket->name ?? 'You'),
                    'created_at' => $dt->format('g:i A'),
                    'created_at_full' => $dt->format('Y-m-d H:i:s'),
                    'date_label' => $this->dateLabel($dt),
                    'attachments' => $msg->attachments->map(fn ($attachment) => route('ticket.download', encrypt($attachment->id)))->toArray(),
                    'channel' => $ticket->channel ?? SupportTicket::CHANNEL_WEB,
                    'direction' => $msg->admin_id ? 'outbound' : 'inbound',
                    'timestamp' => $dt->getTimestamp(),
                ];
            })->toArray();
    }

    protected function dateLabel(Carbon $dt): string
    {
        if ($dt->isToday()) {
            return __('Today');
        }
        if ($dt->isYesterday()) {
            return __('Yesterday');
        }
        return $dt->format('d/m/Y');
    }

    protected function mirrorSupportMessage(?SupportTicket $ticket, ContactChannelMessage $message, array $payload): void
    {
        if (!$ticket) {
            return;
        }

        $supportMessage = new SupportMessage();
        $supportMessage->support_ticket_id = $ticket->id;
        $supportMessage->message = $this->buildSupportBody($message, $payload);

        if (($payload['direction'] ?? 'inbound') === 'outbound') {
            $supportMessage->admin_id = 0;
        }

        $supportMessage->save();

        $ticket->last_reply = now();
        $ticket->status = ($payload['direction'] ?? 'inbound') === 'outbound'
            ? Status::TICKET_ANSWER
            : Status::TICKET_REPLY;
        $ticket->save();
    }

    protected function buildSupportBody(ContactChannelMessage $message, array $payload): string
    {
        $lines = [];
        $lines[] = '[' . strtoupper($message->channel) . '][' . strtoupper($message->direction) . ']';
        if (!empty($payload['sender_name'])) {
            $lines[] = __('From: :name', ['name' => $payload['sender_name']]);
        }
        if (!empty($payload['sender_handle'])) {
            $lines[] = __('Handle: :handle', ['handle' => $payload['sender_handle']]);
        }
        if (!empty($payload['subject'])) {
            $lines[] = __('Subject: :subject', ['subject' => $payload['subject']]);
        }
        if (!empty($payload['message'])) {
            $lines[] = PHP_EOL . trim($payload['message']);
        }
        if (!empty($payload['attachments'])) {
            $lines[] = PHP_EOL . __('Attachments:');
            foreach (Arr::wrap($payload['attachments']) as $att) {
                $lines[] = is_array($att) && isset($att['url'])
                    ? '- ' . ($att['name'] ?? __('File')) . ': ' . $att['url']
                    : '- ' . $att;
            }
        }
        return implode(PHP_EOL, $lines);
    }

    protected function ensureTicket(
        string $channel,
        ?string $subject,
        ?string $name,
        ?string $email,
        ?int $userId = null,
        ?string $reference = null
    ): ?SupportTicket {
        $query = SupportTicket::where('channel', $channel)
            ->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY, Status::TICKET_ANSWER])
            ->where('created_at', '>=', Carbon::now()->subDays(45));

        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($reference) {
            $query->where('channel_reference', $reference);
        }

        $ticket = $query->orderByDesc('id')->first();
        if ($ticket) {
            return $ticket;
        }

        $ticket = new SupportTicket();
        $ticket->ticket = getNumber();
        $ticket->user_id = $userId ?? 0;
        $ticket->name = $name ?: __('Visitor');
        $ticket->email = $email ?: 'noreply@' . parse_url(url('/'), PHP_URL_HOST);
        $ticket->priority = Status::PRIORITY_MEDIUM;
        $ticket->subject = $subject ?: __('Channel Message');
        $ticket->channel = $channel;
        $ticket->channel_reference = $reference;
        $ticket->status = Status::TICKET_OPEN;
        $ticket->last_reply = now();
        $ticket->save();

        return $ticket;
    }

    protected function resolveUserId(array $payload): ?int
    {
        if (!empty($payload['user_id'])) {
            return (int) $payload['user_id'];
        }

        $channel = strtolower((string) ($payload['channel'] ?? ''));
        $handle = $payload['sender_handle'] ?? $payload['remote_chat_id'] ?? null;
        $email = $payload['email'] ?? null;

        if (!$handle && !$email) {
            return null;
        }

        $query = User::query();
        if ($channel === 'whatsapp') {
            $normalized = $this->normalizePhone((string) $handle);
            return $query->where(function ($q) use ($normalized) {
                $q->where('whatsapp_identity', $normalized)
                  ->orWhere('mobile', $normalized);
            })->value('id');
        }

        if ($channel === 'telegram') {
            $tg = ltrim(strtolower((string) $handle), '@');
            return $query->where(function ($q) use ($tg) {
                $q->where('telegram_username', $tg)
                  ->orWhere('username', $tg);
            })->value('id');
        }

        if ($email) {
            return $query->where('email', strtolower($email))->value('id');
        }

        return null;
    }

    protected function normalizePhone(?string $value): ?string
    {
        if (!$value) {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $value);
        if (!$digits) {
            return null;
        }
        if (Str::startsWith($digits, '00')) {
            $digits = substr($digits, 2);
        }
        return $digits;
    }
}
