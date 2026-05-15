<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\LoyaltyTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoyaltyPointsService
{
    /**
     * Award points to user for an order
     */
    public function awardPointsForOrder(Order $order)
    {
        try {
            $general = gs();

            // Check if loyalty points are enabled
            if (!$general || !$general->loyalty_points_status) {
                return false;
            }

            $user = $order->user;
            if (!$user) {
                return false;
            }

            // Calculate points based on order total
            $pointsPerCurrency = $general->loyalty_points_per_currency ?? 1;
            $pointsToAward = floor($order->total * $pointsPerCurrency);

            if ($pointsToAward <= 0) {
                return false;
            }

            return DB::transaction(function () use ($user, $order, $pointsToAward) {
                // Update user points
                $user->points = ($user->points ?? 0) + $pointsToAward;
                $user->save();

                // Create transaction record
                LoyaltyTransaction::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'points' => $pointsToAward,
                    'type' => LoyaltyTransaction::TYPE_EARNED,
                    'description' => 'Points earned from order #' . $order->order_no,
                    'balance_after' => $user->points,
                ]);

                return true;
            });
        } catch (\Exception $e) {
            Log::error('Loyalty points award failed', [
                'order_id' => $order->id ?? null,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Redeem points for discount
     */
    public function redeemPoints(User $user, $pointsToRedeem, $description = 'Points redeemed')
    {
        try {
            if ($pointsToRedeem <= 0) {
                return ['success' => false, 'message' => 'Invalid points amount'];
            }

            if ($user->points < $pointsToRedeem) {
                return ['success' => false, 'message' => 'Insufficient points'];
            }

            return DB::transaction(function () use ($user, $pointsToRedeem, $description) {
                // Deduct points
                $user->points = $user->points - $pointsToRedeem;
                $user->save();

                // Create transaction record
                LoyaltyTransaction::create([
                    'user_id' => $user->id,
                    'points' => -$pointsToRedeem,
                    'type' => LoyaltyTransaction::TYPE_REDEEMED,
                    'description' => $description,
                    'balance_after' => $user->points,
                ]);

                return [
                    'success' => true,
                    'discount_amount' => $this->pointsToDiscount($pointsToRedeem),
                    'remaining_points' => $user->points
                ];
            });
        } catch (\Exception $e) {
            Log::error('Points redemption failed', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'message' => 'Redemption failed'];
        }
    }

    /**
     * Convert points to discount amount
     */
    public function pointsToDiscount($points)
    {
        $general = gs();
        $pointsPerCurrency = $general->loyalty_points_per_currency ?? 1;

        if ($pointsPerCurrency > 0) {
            return $points / $pointsPerCurrency;
        }

        return 0;
    }

    /**
     * Get user's points history
     */
    public function getUserHistory(User $user, $limit = 20)
    {
        return LoyaltyTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's total earned points
     */
    public function getTotalEarned(User $user)
    {
        return LoyaltyTransaction::where('user_id', $user->id)
            ->where('type', LoyaltyTransaction::TYPE_EARNED)
            ->sum('points');
    }

    /**
     * Get user's total redeemed points
     */
    public function getTotalRedeemed(User $user)
    {
        return abs(LoyaltyTransaction::where('user_id', $user->id)
            ->where('type', LoyaltyTransaction::TYPE_REDEEMED)
            ->sum('points'));
    }
}
