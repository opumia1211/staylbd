<?php

namespace App\Http\Controllers;

use App\Models\ContactChannelIntegration;
use App\Services\ContactChannelService;
use Illuminate\Http\Request;
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
}
