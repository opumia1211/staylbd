<?php

namespace App\Modules\Banner;

use App\Models\Frontend;
use Illuminate\Support\Collection;

/**
 * Standalone Banner Module – homepage banner data and settings.
 * প্রতিটি ফিচার আলাদা মডিউল; ব্যানার লজিক শুধু এখানে।
 */
class BannerModuleService
{
    public const DATA_KEY_ELEMENT = 'banner.element';
    public const DATA_KEY_CONTENT = 'banner.content';

    /** Homepage-এ দেখানোর জন্য ব্যানার এলিমেন্টগুলো (active, date, visibility ফিল্টার সহ)। অ্যাডমিনের display_order অনুযায়ী সাজানো। */
    public function getBannersForHomepage(): Collection
    {
        $now = now()->format('Y-m-d');
        $userLoggedIn = auth()->check();

        try {
            $elements = Frontend::where('data_keys', self::DATA_KEY_ELEMENT)
                ->orderByRaw('CAST(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")), "999") AS UNSIGNED) ASC')
                ->orderBy('id', 'asc')
                ->get();
        } catch (\Throwable $e) {
            $elements = Frontend::where('data_keys', self::DATA_KEY_ELEMENT)->orderBy('id', 'asc')->get();
        }

        $elements = $elements->sortBy(function ($b) {
            $dv = $this->normalizeDataValues($b->data_values);
            return (int) ($dv['display_order'] ?? 999);
        })->values();

        return $elements->filter(function ($b) use ($now, $userLoggedIn) {
            $dv = $this->normalizeDataValues($b->data_values);
            if ((int)($dv['is_active'] ?? 1) === 0) {
                return false;
            }
            if (!empty($dv['start_date']) && $now < $dv['start_date']) {
                return false;
            }
            if (!empty($dv['end_date']) && $now > $dv['end_date']) {
                return false;
            }
            $vis = strtolower(trim((string)($dv['visibility'] ?? 'public')));
            if ($vis === 'logged_in_only' && !$userLoggedIn) {
                return false;
            }
            if ($vis === 'guest_only' && $userLoggedIn) {
                return false;
            }
            if ($vis === 'campaign_only') {
                return false;
            }
            return true;
        })->values();
    }

    /** যে ব্যানারগুলোর ইমেজ ফাইলের নাম আছে শুধু সেগুলো */
    public function getBannersWithImage(Collection $banners): Collection
    {
        return $banners->filter(function ($b) {
            $filename = $this->getImageFilename($b);
            return $filename !== null && $filename !== '';
        })->values();
    }

    /** একটি ব্যানার থেকে ইমেজ ফাইলের নাম (string)। অ্যাডমিনে যেভাবে সেভ হয় (image = filename) সেটাই পড়া হয়। */
    public function getImageFilename($banner): ?string
    {
        $imageRaw = null;
        if ($banner instanceof \Illuminate\Database\Eloquent\Model && isset($banner->attributes['data_values'])) {
            $raw = $banner->attributes['data_values'];
            if (is_string($raw)) {
                $dec = json_decode($raw, true);
                $imageRaw = is_array($dec) ? ($dec['image'] ?? null) : null;
            }
        }
        if ($imageRaw === null) {
            $dv = $this->normalizeDataValues($banner->data_values ?? null);
            $imageRaw = $dv['image'] ?? null;
        }
        if ($imageRaw === null && isset($banner->data_values)) {
            $raw = $banner->data_values;
            if (is_object($raw) && isset($raw->image)) {
                $imageRaw = $raw->image;
            }
        }
        if (is_array($imageRaw)) {
            $imageRaw = $imageRaw['desktop'] ?? $imageRaw['image'] ?? reset($imageRaw);
        }
        if (is_object($imageRaw)) {
            $imageRaw = $imageRaw->desktop ?? $imageRaw->image ?? null;
        }
        if (!is_string($imageRaw) || trim($imageRaw) === '') {
            return null;
        }
        $filename = trim($imageRaw);
        return $filename !== '' ? $filename : null;
    }

    /** ব্যানার সেটিংস (slide interval, size, autoplay) – পাবলিক পেজে এই মান ব্যবহার হয় */
    public function getSettings(): array
    {
        $row = Frontend::where('data_keys', self::DATA_KEY_CONTENT)->orderBy('id', 'desc')->first();
        $dv = $row ? $this->normalizeDataValues($row->data_values) : [];
        $slideInterval = (int)($dv['slide_interval_seconds'] ?? 5);
        $slideInterval = max(1, min(60, $slideInterval));
        $autoplay = isset($dv['autoplay']) ? (int)$dv['autoplay'] : 1;
        if ($autoplay !== 0) {
            $autoplay = 1;
        }
        $width = (int)($dv['banner_width'] ?? 2560);
        $height = (int)($dv['banner_height'] ?? 400);
        if ($width < 100) {
            $width = 2560;
        }
        if ($height < 50) {
            $height = 400;
        }
        return [
            'slide_interval_seconds' => $slideInterval,
            'autoplay'               => $autoplay,
            'banner_width'           => $width,
            'banner_height'          => $height,
        ];
    }

    /** data_values কে অ্যারে হিসেবে নিশ্চিত করা */
    private function normalizeDataValues($data): array
    {
        if ($data === null) {
            return [];
        }
        if (is_array($data)) {
            return $data;
        }
        if (is_object($data)) {
            return (array) $data;
        }
        if (is_string($data)) {
            $dec = json_decode($data, true);
            return is_array($dec) ? $dec : [];
        }
        return [];
    }
}
