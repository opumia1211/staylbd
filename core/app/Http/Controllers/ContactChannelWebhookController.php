<?php

namespace App\Http\Controllers;

use App\Models\ContactChannelIntegration;
use App\Services\ContactChannelService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ContactChannelWebhookController extends Controller
{
    protected ContactChannelService $contactChannelService;

    public function __construct(ContactChannelService $contactChannelService)
    {
        $this->contactChannelService = $contactChannelService;
    }

    public function whatsapp(Request $request)
    {
        if ($request->isMethod('get')) {
            $verifyToken = $request->get('hub_verify_token') ?? $request->get('hub.verify_token');
            $challenge = $request->get('hub_challenge') ?? $request->get('hub.challenge');

            if ($this->verifyToken('whatsapp', $verifyToken)) {
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }

            return response('Invalid verify token', Response::HTTP_FORBIDDEN);
        }

        $bypassAllowed = (bool) config('contact_channels.whatsapp.bypass_signature', false)
            && ! app()->environment('production');

        if ($bypassAllowed) {
            Log::channel('security')->warning('WhatsApp webhook signature verification bypassed (non-production only; WHATSAPP_WEBHOOK_BYPASS_SIGNATURE)');
        } elseif (! $this->whatsappWebhookSignatureValid($request)) {
            Log::channel('security')->warning('WhatsApp webhook rejected: invalid or missing signature', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['success' => false, 'message' => 'Invalid signature'], Response::HTTP_FORBIDDEN);
        }

        $entries = $request->input('entry', []);
        foreach ($entries as $entry) {
            foreach ($entry['changes'] ?? [] as $change) {
                $value = $change['value'] ?? [];
                $metadata = $value['metadata'] ?? [];
                $phoneNumberId = $metadata['phone_number_id'] ?? null;
                $integration = $this->resolveIntegrationByPhone($phoneNumberId);
                if (!$integration) {
                    continue;
                }

                $contacts = $value['contacts'][0] ?? [];
                $profileName = $contacts['profile']['name'] ?? null;

                foreach ($value['messages'] ?? [] as $message) {
                    $text = $message['text']['body'] ?? $message['button']['text'] ?? $message['interactive']['body'] ?? '';
                    $attachments = [];
                    if (!empty($message['image'])) {
                        $attachments[] = [
                            'type' => 'image',
                            'id' => $message['image']['id'] ?? null,
                            'mime' => $message['image']['mime_type'] ?? null,
                        ];
                    }

                    $this->contactChannelService->logMessage([
                        'contact_channel_integration_id' => $integration->id,
                        'channel' => 'whatsapp',
                        'direction' => 'inbound',
                        'remote_chat_id' => $message['from'] ?? null,
                        'remote_message_id' => $message['id'] ?? null,
                        'sender_name' => $profileName,
                        'message' => $text,
                        'attachments' => $attachments,
                        'metadata' => $message,
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function telegram(Request $request)
    {
        $integration = ContactChannelIntegration::where('channel', 'telegram')->where('is_active', true)->first();
        if (!$integration) {
            return response()->json(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $secret = $integration->getSecret('webhook_secret');
        if ($secret) {
            $headerSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
            if (!$headerSecret || !hash_equals($secret, $headerSecret)) {
                return response()->json(['success' => false, 'message' => 'Invalid secret'], Response::HTTP_FORBIDDEN);
            }
        }

        $payload = $request->all();
        $message = $payload['message'] ?? $payload['edited_message'] ?? null;

        if (!$message) {
            return response()->json(['success' => true]);
        }

        $from = $message['from'] ?? [];
        $text = $message['text'] ?? $message['caption'] ?? '';
        $attachments = [];
        if (!empty($message['photo'])) {
            $attachments[] = [
                'type' => 'photo',
                'file_id' => $message['photo'][0]['file_id'],
            ];
        }

        $this->contactChannelService->logMessage([
            'contact_channel_integration_id' => $integration->id,
            'channel' => 'telegram',
            'direction' => 'inbound',
            'remote_chat_id' => $message['chat']['id'] ?? null,
            'remote_message_id' => $message['message_id'] ?? null,
            'sender_name' => trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? '')) ?: ($from['username'] ?? null),
            'sender_handle' => $from['username'] ?? null,
            'message' => $text,
            'attachments' => $attachments,
            'metadata' => $message,
        ]);

        return response()->json(['success' => true]);
    }

    protected function verifyToken(string $channel, ?string $token): bool
    {
        if (!$token) {
            return false;
        }

        return ContactChannelIntegration::where('channel', $channel)
            ->where('settings->verify_token', $token)
            ->exists();
    }

    protected function resolveIntegrationByPhone(?string $phoneNumberId): ?ContactChannelIntegration
    {
        if (!$phoneNumberId) {
            return null;
        }

        return ContactChannelIntegration::where('channel', 'whatsapp')
            ->where('settings->phone_number_id', $phoneNumberId)
            ->first();
    }

    /**
     * Meta Cloud API: X-Hub-Signature-256 = "sha256=" + hex(HMAC-SHA256(raw_body, app_secret)).
     *
     * @see https://developers.facebook.com/docs/graph-api/webhooks/getting-started#verification-requests
     */
    protected function whatsappWebhookSignatureValid(Request $request): bool
    {
        $secrets = $this->whatsappAppSecrets();
        if ($secrets->isEmpty()) {
            if (app()->environment('production')) {
                Log::channel('security')->error('WhatsApp webhook: no app secret configured (auth_meta.whatsapp_app_secret on active integrations)');

                return false;
            }

            Log::channel('security')->warning('WhatsApp webhook: no app secret; accepting in non-production (configure whatsapp_app_secret for production)');

            return true;
        }

        $header = (string) $request->header(config('contact_channels.whatsapp.signature_header', 'X-Hub-Signature-256'), '');
        if (! str_starts_with($header, 'sha256=')) {
            return false;
        }

        $providedHex = strtolower(substr($header, 7));
        if ($providedHex === '' || strlen($providedHex) !== 64 || ! ctype_xdigit($providedHex)) {
            return false;
        }

        $payload = $request->getContent();
        foreach ($secrets as $secret) {
            $expected = hash_hmac('sha256', $payload, $secret);
            if (hash_equals(strtolower($expected), $providedHex)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, non-empty-string>
     */
    protected function whatsappAppSecrets(): Collection
    {
        return ContactChannelIntegration::query()
            ->where('channel', 'whatsapp')
            ->where('is_active', true)
            ->get()
            ->map(fn (ContactChannelIntegration $i) => $i->getSecret('whatsapp_app_secret'))
            ->filter(fn ($s) => is_string($s) && $s !== '')
            ->unique()
            ->values();
    }
}
