<?php

namespace App\Services;

use App\Models\Frontend;
use App\Models\HomepageAdSlot;
use App\Models\HomepageCustomProductRow;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class HomepageLayoutService
{
    public const DATA_KEYS = 'homepage.layout_order';

    /** Header bar ids (shared by all public pages). */
    public const HEADER_BARS = [
        'header_bar_top_notice',
        'header_bar_main',
        'header_bar_menu',
    ];

    /** Built-in section ids (fixed order for defaults) */
    public const BUILTIN_BEFORE_CUSTOM = [
        'scrollbar',
        'home_category',
        'quick_deals',
        'power_zone',
        'hot_deal',
        'featured',
        'new_arrivals',
        'trending',
        'best_selling',
    ];

    public const BUILTIN_AFTER_CUSTOM = [
        'social_proof',
        'recommendations',
    ];

    public static function sectionLabels(): array
    {
        return [
            'header_bar_top_notice' => __('Header bar: top notice'),
            'header_bar_main' => __('Header bar: main (logo + search)'),
            'header_bar_menu' => __('Header bar: menu (categories + nav)'),
            'scrollbar' => __('Scrollbar (under banner)'),
            'home_category' => __('Category row'),
            'quick_deals' => __('Quick Deals'),
            'power_zone' => __('Power zone / banner below'),
            'hot_deal' => __('Hot Deals'),
            'featured' => __('Featured Products'),
            'new_arrivals' => __('New Arrivals'),
            'trending' => __('Trending Now'),
            'best_selling' => __('Best Selling'),
            'social_proof' => __('Social proof'),
            'recommendations' => __('Recommended For You'),
        ];
    }

    public static function labelFor(string $id): string
    {
        if (preg_match('/^ad_slot_(\d+)$/', $id, $m)) {
            if (!Schema::hasTable('homepage_ad_slots')) {
                return $id;
            }
            $ad = HomepageAdSlot::query()->find((int) $m[1]);
            return $ad ? (string) ($ad->admin_title ?: __('Sponsored')) : $id;
        }
        if (preg_match('/^custom_row_(\d+)$/', $id, $m)) {
            $row = HomepageCustomProductRow::query()->find((int) $m[1]);

            return $row ? __('Custom: :title', ['title' => $row->title]) : $id;
        }

        return __((self::sectionLabels()[$id] ?? $id));
    }

    /**
     * Label shown on homepage/admin: uses saved override (if present) else fallback.
     *
     * @param  array{id?:mixed,label?:mixed}  $slot
     */
    public static function displayLabel(string $id, array $slot = []): string
    {
        $label = isset($slot['label']) ? trim((string) $slot['label']) : '';
        if ($label !== '') {
            return __($label);
        }

        if (preg_match('/^ad_slot_(\d+)$/', $id, $m)) {
            if (!Schema::hasTable('homepage_ad_slots')) {
                return $id;
            }
            $ad = HomepageAdSlot::query()->find((int) $m[1]);
            if ($ad) {
                return (string) ($ad->admin_title ?: __('Sponsored'));
            }
        }

        // For custom rows, default label should be the row title (without "Custom:" prefix).
        if (preg_match('/^custom_row_(\d+)$/', $id, $m)) {
            $row = HomepageCustomProductRow::query()->find((int) $m[1]);
            if ($row) {
                return (string) $row->title;
            }
        }

        return __((self::sectionLabels()[$id] ?? $id));
    }

    public static function allowedIds(): array
    {
        $custom = HomepageCustomProductRow::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn (int $id) => 'custom_row_' . $id)
            ->all();

        $ads = [];
        try {
            if (Schema::hasTable('homepage_ad_slots')) {
                $ads = HomepageAdSlot::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->pluck('id')
                    ->map(fn (int $id) => 'ad_slot_' . $id)
                    ->all();
            }
        } catch (\Throwable $e) {
            $ads = [];
        }

        return array_merge(
            self::HEADER_BARS,
            self::BUILTIN_BEFORE_CUSTOM,
            $custom,
            $ads,
            self::BUILTIN_AFTER_CUSTOM
        );
    }

    /** @return list<array{id:string,enabled:bool,label?:string,interval_seconds?:int,speed_ms?:int}> */
    public static function getResolvedLayout(): array
    {
        $allowed = array_flip(self::allowedIds());
        $customKeys = array_filter(array_keys($allowed), fn ($k) => str_starts_with($k, 'custom_row_'));

        $raw = self::getRawSaved();
        if ($raw === []) {
            return self::buildDefaultLayout(array_values($customKeys));
        }

        $seen = [];
        $out = [];
        foreach ($raw as $row) {
            $id = (string) ($row['id'] ?? '');
            if ($id === '' || !isset($allowed[$id])) {
                continue;
            }
            if (isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $slot = [
                'id' => $id,
                'enabled' => filter_var($row['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ];
            $label = isset($row['label']) ? trim((string) $row['label']) : '';
            if ($label !== '') {
                $slot['label'] = $label;
            }
            if (isset($row['interval_seconds']) && $row['interval_seconds'] !== '' && $row['interval_seconds'] !== null) {
                $slot['interval_seconds'] = max(2, min(30, (int) $row['interval_seconds']));
            }
            if (isset($row['speed_ms']) && $row['speed_ms'] !== '' && $row['speed_ms'] !== null) {
                $slot['speed_ms'] = max(300, min(2000, (int) $row['speed_ms']));
            }
            $out[] = $slot;
        }

        foreach (self::allowedIds() as $id) {
            if (isset($seen[$id])) {
                continue;
            }
            if (str_starts_with($id, 'custom_row_') || str_starts_with($id, 'ad_slot_')) {
                $out = self::insertBefore($out, $id, ['social_proof', 'recommendations']);
                $seen[$id] = true;
            }
        }

        return $out;
    }

    /**
     * Full ordered list for admin + home (every allowed section exactly once).
     */
    public static function getOrderedSections(): array
    {
        $allowedList = self::allowedIds();
        $partial = self::getResolvedLayout();
        $seen = [];
        $out = [];
        foreach ($partial as $r) {
            $id = (string) ($r['id'] ?? '');
            if ($id === '' || !in_array($id, $allowedList, true) || isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $slot = [
                'id' => $id,
                'enabled' => !empty($r['enabled']),
            ];
            $label = isset($r['label']) ? trim((string) $r['label']) : '';
            if ($label !== '') {
                $slot['label'] = $label;
            }
            if (isset($r['interval_seconds']) && $r['interval_seconds'] !== '' && $r['interval_seconds'] !== null) {
                $slot['interval_seconds'] = max(2, min(30, (int) $r['interval_seconds']));
            }
            if (isset($r['speed_ms']) && $r['speed_ms'] !== '' && $r['speed_ms'] !== null) {
                $slot['speed_ms'] = max(300, min(2000, (int) $r['speed_ms']));
            }
            $out[] = $slot;
        }
        foreach ($allowedList as $id) {
            if (!isset($seen[$id])) {
                $out[] = ['id' => $id, 'enabled' => true];
            }
        }

        return $out;
    }

    /**
     * @param  list<array{id:string,enabled:bool}>  $out
     * @return list<array{id:string,enabled:bool}>
     */
    private static function insertBefore(array $out, string $newId, array $beforeIds): array
    {
        foreach ($out as $i => $row) {
            if (in_array($row['id'], $beforeIds, true)) {
                array_splice($out, $i, 0, [['id' => $newId, 'enabled' => true]]);

                return $out;
            }
        }
        $out[] = ['id' => $newId, 'enabled' => true];

        return $out;
    }

    /** @param  list<string>  $customKeys  e.g. custom_row_1 */
    private static function buildDefaultLayout(array $customKeys): array
    {
        $a = [];
        foreach (self::HEADER_BARS as $id) {
            $a[] = ['id' => $id, 'enabled' => true];
        }
        foreach (self::BUILTIN_BEFORE_CUSTOM as $id) {
            $a[] = ['id' => $id, 'enabled' => true];
        }
        foreach ($customKeys as $ck) {
            $a[] = ['id' => $ck, 'enabled' => true];
        }
        foreach (self::BUILTIN_AFTER_CUSTOM as $id) {
            $a[] = ['id' => $id, 'enabled' => true];
        }

        return $a;
    }

    /** @return list<array{id:string,enabled:mixed,label?:mixed,interval_seconds?:mixed,speed_ms?:mixed}> */
    private static function getRawSaved(): array
    {
        $row = Frontend::query()->where('data_keys', self::DATA_KEYS)->orderByDesc('id')->first();
        if (!$row || empty($row->data_values)) {
            return [];
        }
        // Model casts data_values to object — nested section rows are stdClass; must be arrays.
        $dv = json_decode(json_encode($row->data_values), true);
        if (!is_array($dv)) {
            return [];
        }
        $sections = $dv['sections'] ?? [];
        if (!is_array($sections)) {
            return [];
        }
        $out = [];
        foreach ($sections as $item) {
            if (is_object($item)) {
                $item = (array) $item;
            }
            if (!is_array($item) || ($item['id'] ?? '') === '') {
                continue;
            }
            $out[] = [
                'id' => (string) $item['id'],
                'enabled' => filter_var($item['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'label' => $item['label'] ?? null,
                'interval_seconds' => $item['interval_seconds'] ?? null,
                'speed_ms' => $item['speed_ms'] ?? null,
            ];
        }

        return $out;
    }

    /**
     * @param  list<array{id:string,enabled:bool,label?:string,interval_seconds?:int,speed_ms?:int}>  $sections
     */
    public static function saveLayout(array $sections): void
    {
        $allowed = array_flip(self::allowedIds());
        $clean = [];
        $seen = [];
        foreach ($sections as $row) {
            $id = (string) ($row['id'] ?? '');
            if ($id === '' || !isset($allowed[$id]) || isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $slot = [
                'id' => $id,
                'enabled' => !empty($row['enabled']),
            ];
            $label = isset($row['label']) ? trim((string) $row['label']) : '';
            if ($label !== '') {
                $slot['label'] = $label;
            }
            if (isset($row['interval_seconds']) && $row['interval_seconds'] !== '' && $row['interval_seconds'] !== null) {
                $slot['interval_seconds'] = max(2, min(30, (int) $row['interval_seconds']));
            }
            if (isset($row['speed_ms']) && $row['speed_ms'] !== '' && $row['speed_ms'] !== null) {
                $slot['speed_ms'] = max(300, min(2000, (int) $row['speed_ms']));
            }
            $clean[] = $slot;
        }

        // Must match getRawSaved(): same row (latest id). firstOrNew() would update the *oldest* row if duplicates exist.
        $frontend = Frontend::query()
            ->where('data_keys', self::DATA_KEYS)
            ->orderByDesc('id')
            ->first();
        if (!$frontend) {
            $frontend = new Frontend;
            $frontend->data_keys = self::DATA_KEYS;
        }
        $frontend->data_values = (object) ['sections' => $clean];
        $frontend->save();

        Frontend::query()
            ->where('data_keys', self::DATA_KEYS)
            ->where('id', '!=', $frontend->id)
            ->delete();

        Cache::forget('homepage.sections.data');
        HomepageDataService::clearBelowFoldFragmentCache();
    }

    /** Sync layout file after new custom row (inserts before Social proof). */
    public static function persistLayoutAfterCustomRowChange(): void
    {
        self::saveLayout(self::getResolvedLayout());
    }

    /** Sync layout file after new ad slot (inserts before Social proof). */
    public static function persistLayoutAfterAdSlotChange(): void
    {
        self::saveLayout(self::getResolvedLayout());
    }

    /** @return list<array{id:string,enabled:bool}> for admin editor */
    public static function getLayoutForEditor(): array
    {
        return self::getResolvedLayout();
    }
}
