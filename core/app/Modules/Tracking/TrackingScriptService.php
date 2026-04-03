<?php

namespace App\Modules\Tracking;

use App\Models\GeneralSetting;

/**
 * Provides tracking script config for Meta Pixel, Google Ads, TikTok.
 * Safe when general_settings or columns missing.
 */
class TrackingScriptService
{
    protected ?object $general = null;

    public function __construct()
    {
        try {
            if (function_exists('gs')) {
                $this->general = gs();
            } elseif (\Illuminate\Support\Facades\Schema::hasTable('general_settings')) {
                $this->general = GeneralSetting::first();
            }
        } catch (\Throwable $e) {
            \Log::debug('TrackingScriptService: could not load general_settings', ['message' => $e->getMessage()]);
        }
    }

    public function metaPixelId(): ?string
    {
        if (!$this->general || !\Schema::hasColumn('general_settings', 'meta_pixel_id')) {
            return null;
        }
        $id = $this->general->meta_pixel_id ?? null;
        return $id ? trim((string) $id) : null;
    }

    public function facebookAccessToken(): ?string
    {
        if (!$this->general || !\Schema::hasColumn('general_settings', 'facebook_access_token')) {
            return null;
        }
        $t = $this->general->facebook_access_token ?? null;
        return $t ? trim((string) $t) : null;
    }

    public function googleAdsId(): ?string
    {
        if (!$this->general || !\Schema::hasColumn('general_settings', 'google_ads_id')) {
            return null;
        }
        $id = $this->general->google_ads_id ?? null;
        return $id ? trim((string) $id) : null;
    }

    public function tiktokPixelId(): ?string
    {
        if (!$this->general || !\Schema::hasColumn('general_settings', 'tiktok_pixel_id')) {
            return null;
        }
        $id = $this->general->tiktok_pixel_id ?? null;
        return $id ? trim((string) $id) : null;
    }

    public function hasAny(): bool
    {
        return $this->metaPixelId() !== null
            || $this->googleAdsId() !== null
            || $this->tiktokPixelId() !== null;
    }
}
