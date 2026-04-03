<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Services\AbandonedCartService;
use Illuminate\Http\Request;

class RecoverCartController extends Controller
{
    /**
     * Restore cart from recovery link and redirect to cart or checkout.
     */
    public function recover(string $token, Request $request)
    {
        $abandoned = AbandonedCart::where('recovery_token', $token)
            ->whereIn('status', [AbandonedCart::STATUS_PENDING, AbandonedCart::STATUS_ABANDONED])
            ->first();

        if (!$abandoned || empty($abandoned->cart_snapshot)) {
            $notify[] = ['error', __('This recovery link is invalid or has already been used.')];
            return redirect()->route('home')->withNotify($notify);
        }

        $service = app(AbandonedCartService::class);
        $user = auth()->user();
        $service->restoreCartFromAbandoned($abandoned, $user);

        $notify[] = ['success', __('Your cart has been restored. Complete your order now!')];

        if ($user && $user->id) {
            return redirect()->route('user.checkout.index')->withNotify($notify);
        }
        return redirect()->route('user.cart')->withNotify($notify);
    }
}
