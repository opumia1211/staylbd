<?php

namespace App\Http\Controllers;

/**
 * Placeholder public page for footer “Seller account” when admin enables the feature.
 * Replace with full seller onboarding when ready.
 */
class SellerApplyController extends Controller
{
    public function show()
    {
        $pageTitle = __('Become a seller');

        return view(activeTemplate() . 'seller_apply', compact('pageTitle'));
    }
}
