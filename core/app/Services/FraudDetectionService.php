<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Log;

/**
 * Advanced Fraud Detection: analyzes orders and behavior for suspicious patterns.
 */
class FraudDetectionService
{
    /**
     * Audit an order for potential fraud.
     * Returns a score from 0 (Safe) to 100 (High Risk).
     */
    public function auditOrder(Order $order): int
    {
        $score = 0;
        $user = $order->user;

        // 1. Check if user is very new
        if ($user && $user->created_at->diffInDays(now()) < 1) {
            $score += 20;
        }

        // 2. High total amount for first order
        if ($order->total_amount > 500 && $user && $user->orders()->count() === 1) {
            $score += 30;
        }

        // 3. Multiple orders in a very short time from same IP
        $recentOrdersCount = Order::where('ip_address', $order->ip_address)
            ->where('created_at', '>', now()->subMinutes(30))
            ->count();
        
        if ($recentOrdersCount > 3) {
            $score += 40;
        }

        // 4. Repeated payment failures in session
        $failures = UserActivityLog::where('session_id', request()->session()->getId())
            ->where('action_type', UserActivityLog::PAYMENT_FAILURE)
            ->count();
        
        if ($failures > 3) {
            $score += 50;
        }

        if ($score >= 70) {
            Log::warning("High risk order detected! Order ID: {$order->id}, Score: {$score}");
            $order->is_suspicious = 1;
            $order->save();
        }

        return min(100, $score);
    }

    /**
     * Check if an IP is blacklisted (e.g. from known botnets or high-fraud regions).
     */
    public function isIpBlacklisted(string $ip): bool
    {
        // Placeholder for external IP intelligence API
        $blacklistedIps = ['123.123.123.123']; 
        return in_array($ip, $blacklistedIps);
    }
}
