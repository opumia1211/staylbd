<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderChannel;
use App\Services\OrderChannelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderChannelWebhookController extends Controller
{
    public function __construct(protected OrderChannelService $channelService)
    {
    }

    public function receive(Request $request, string $token): JsonResponse
    {
        if (!$this->channelService->isAvailable()) {
            return response()->json(['ok' => false, 'message' => 'Order channels not configured.'], 503);
        }

        $channel = OrderChannel::where('webhook_token', $token)->first();
        if (!$channel) {
            return response()->json(['ok' => false, 'message' => 'Invalid channel token.'], 404);
        }

        $secret = $channel->settings['webhook_secret'] ?? null;
        if ($secret && $request->header('X-Channel-Secret') !== $secret) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized.'], 401);
        }

        $result = $this->channelService->handleWebhook($channel, $request->all());

        return response()->json($result, ($result['ok'] ?? false) ? 200 : 422);
    }
}
