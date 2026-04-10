<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * High-performance analytics tracking endpoint.
 */
class AnalyticsController extends Controller
{
    /**
     * Store behavioral event from storefront-business.js.
     */
    public function trackEvent(Request $request)
    {
        $type = $request->input('type');
        $sessionId = $request->input('session_id');
        $data = $request->input('data', []);
        
        // Log to activity system
        UserActivityLog::create([
            'user_id' => Auth::id(),
            'session_id' => $sessionId,
            'action_type' => $type,
            'description' => json_encode($data),
            'url' => $request->input('url'),
            'ip_address' => $request->ip(),
            'device' => $request->header('User-Agent'),
            'browser' => $this->getBrowser($request->header('User-Agent')),
        ]);

        return response()->json(['status' => 'success']);
    }

    protected function getBrowser($ua)
    {
        if (str_contains($ua, 'MSIE') || str_contains($ua, 'Trident')) return 'IE';
        if (str_contains($ua, 'Edge')) return 'Edge';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Chrome')) return 'Chrome';
        if (str_contains($ua, 'Safari')) return 'Safari';
        if (str_contains($ua, 'Opera')) return 'Opera';
        return 'Other';
    }
}
