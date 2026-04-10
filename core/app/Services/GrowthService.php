<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoyaltyTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Growth & Monetization Service: Referral programs, loyalty points, and subscription plans.
 */
class GrowthService
{
    /**
     * Process referral for a new user.
     */
    public function processReferral(User $newUser, string $referralCode)
    {
        $referrer = User::where('referral_code', $referralCode)->first();
        if (!$referrer) return;

        $newUser->referrer_id = $referrer->id;
        $newUser->save();

        // Award points to referrer (Marketing Incentive)
        $this->awardPoints($referrer, 50, 'Referral bonus: ' . $newUser->username);
    }

    /**
     * Award loyalty points to a user.
     */
    public function awardPoints(User $user, float $points, string $description, $orderId = null)
    {
        return DB::transaction(function() use ($user, $points, $description, $orderId) {
            $newBalance = ($user->loyalty_points ?? 0) + $points;
            
            $user->loyalty_points = $newBalance;
            $user->save();

            return LoyaltyTransaction::create([
                'user_id' => $user->id,
                'order_id' => $orderId,
                'points' => $points,
                'type' => LoyaltyTransaction::TYPE_EARNED,
                'description' => $description,
                'balance_after' => $newBalance
            ]);
        });
    }

    /**
     * Get Subscription Plan features (Feature Gating).
     */
    public function getPlanFeatures(string $planName): array
    {
        return match (strtolower($planName)) {
            'pro' => [
                'premium_support' => true,
                'unlimited_wishlist' => true,
                'priority_shipping' => true,
                'exclusive_deals' => true,
                'cashback_percent' => 5,
            ],
            'enterprise' => [
                'premium_support' => true,
                'unlimited_wishlist' => true,
                'priority_shipping' => true,
                'exclusive_deals' => true,
                'personal_shopper' => true,
                'cashback_percent' => 10,
            ],
            default => [
                'premium_support' => false,
                'cashback_percent' => 0,
            ],
        };
    }

    /**
     * Check if user has access to a specific premium feature.
     */
    public function hasFeature(User $user, string $feature): bool
    {
        $plan = $user->subscription_plan ?? 'basic';
        $features = $this->getPlanFeatures($plan);
        return $features[$feature] ?? false;
    }
}
