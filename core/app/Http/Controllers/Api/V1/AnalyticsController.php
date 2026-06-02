<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Storefront behavioral events (storefront-business.js).
 */
class AnalyticsController extends Controller
{
    private const ALLOWED_TYPES = [
        'user_click',
        'page_scroll_final',
        'exit_intent_trigger',
        'exit_intent_claim',
        'time_on_page',
        'product_view',
    ];

    public function trackEvent(Request $request)
    {
        $type = (string) $request->input('type', '');
        if ($type === '' || strlen($type) > 64) {
            return response()->json(['status' => 'ignored'], 422);
        }

        if (!in_array($type, self::ALLOWED_TYPES, true) && !str_starts_with($type, 'custom_')) {
            return response()->json(['status' => 'ignored'], 422);
        }

        $sessionId = (string) $request->input('session_id', '');
        if (strlen($sessionId) > 128) {
            $sessionId = substr($sessionId, 0, 128);
        }

        $data = $request->input('data', []);
        if (!is_array($data)) {
            $data = [];
        }

        $ua = (string) $request->userAgent();
        $device = $this->detectDevice($ua);

        try {
            $description = json_encode($data, JSON_UNESCAPED_UNICODE);
            if ($description === false) {
                $description = '{}';
            }

            $modelType = null;
            $modelId = null;
            if ($type === 'product_view' && !empty($data['product_id'])) {
                $modelType = 'product';
                $modelId = (int) $data['product_id'];
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'session_id' => $sessionId !== '' ? $sessionId : ($request->hasSession() ? $request->session()->getId() : null),
                'action_type' => $type,
                'description' => mb_substr($description, 0, 1000),
                'model_type' => $modelType,
                'model_id' => $modelId,
                'ip_address' => $request->ip(),
                'device' => $device,
                'browser' => $this->getBrowser($ua),
                'os' => $this->getOs($ua),
                'url' => mb_substr((string) $request->input('url', $request->fullUrl()), 0, 500),
            ]);
        } catch (\Throwable $e) {
            Log::channel('daily')->warning('Analytics track failed: ' . $e->getMessage());

            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'success']);
    }

    protected function detectDevice(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'watch') || (str_contains($ua, 'wear') && str_contains($ua, 'android'))) {
            return 'watch';
        }
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return str_contains($ua, 'ipad') ? 'tablet' : 'mobile';
        }
        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }

        return 'desktop';
    }

    protected function getBrowser(?string $ua): string
    {
        if (!$ua) {
            return 'Other';
        }
        if (str_contains($ua, 'Edg/')) {
            return 'Edge';
        }
        if (str_contains($ua, 'Firefox')) {
            return 'Firefox';
        }
        if (str_contains($ua, 'Chrome')) {
            return 'Chrome';
        }
        if (str_contains($ua, 'Safari')) {
            return 'Safari';
        }
        if (str_contains($ua, 'Opera') || str_contains($ua, 'OPR')) {
            return 'Opera';
        }

        return 'Other';
    }

    protected function getOs(?string $ua): ?string
    {
        if (!$ua) {
            return null;
        }
        if (str_contains($ua, 'Windows')) {
            return 'Windows';
        }
        if (str_contains($ua, 'Android')) {
            return 'Android';
        }
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
            return 'iOS';
        }
        if (str_contains($ua, 'Mac OS')) {
            return 'macOS';
        }
        if (str_contains($ua, 'Linux')) {
            return 'Linux';
        }

        return null;
    }
}
