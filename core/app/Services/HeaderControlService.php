<?php

namespace App\Services;

use App\Models\Frontend;
use Illuminate\Support\Collection;

class HeaderControlService
{
    public const LIVE_KEY = 'header_control.live';
    public const DRAFT_KEY = 'header_control.draft';

    public static function getLiveConfig(): array
    {
        $cacheKey = 'header_control_live_v1';
        $live = \Illuminate\Support\Facades\Cache::get($cacheKey);
        
        if (!$live) {
            $live = self::readByKey(self::LIVE_KEY);
            if ($live !== []) {
                $live = self::hydrateMissingFromLegacy(self::normalize($live));
            } else {
                $live = self::seedFromLegacy();
                self::saveLive($live);
            }
            \Illuminate\Support\Facades\Cache::put($cacheKey, $live, 3600);
        }

        return $live;
    }

    public static function getDraftConfig(): array
    {
        $draft = self::readByKey(self::DRAFT_KEY);
        if ($draft !== []) {
            return self::hydrateMissingFromLegacy(self::normalize($draft));
        }

        return self::getLiveConfig();
    }

    public static function saveDraft(array $config): void
    {
        self::upsert(self::DRAFT_KEY, self::hydrateMissingFromLegacy(self::normalize($config)));
    }

    public static function saveLive(array $config): void
    {
        self::upsert(self::LIVE_KEY, self::hydrateMissingFromLegacy(self::normalize($config)));
        \Illuminate\Support\Facades\Cache::forget('header_control_live_v1');
    }

    public static function publishDraft(): array
    {
        $draft = self::getDraftConfig();
        self::saveLive($draft);

        return $draft;
    }

    private static function readByKey(string $key): array
    {
        $row = Frontend::query()->where('data_keys', $key)->orderByDesc('id')->first();
        if (!$row || empty($row->data_values)) {
            return [];
        }
        $data = json_decode(json_encode($row->data_values), true);

        return is_array($data) ? $data : [];
    }

    private static function upsert(string $key, array $data): void
    {
        $row = Frontend::query()->where('data_keys', $key)->orderByDesc('id')->first();
        if (!$row) {
            $row = new Frontend();
            $row->data_keys = $key;
        }
        $row->data_values = (object) $data;
        $row->save();
    }

    private static function seedFromLegacy(): array
    {
        $legacyButtons = self::loadLegacyHeaderButtons();

        return self::normalize([
            'appearance' => [
                'top_bg' => '#0f172a',
                'main_bg' => '#f8fafc',
                'menu_bg' => '#c7eafe',
                'top_height' => 38,
                'main_height' => 56,
                'menu_height' => 38,
                'width_desktop' => 1920,
                'width_laptop' => 1600,
                'width_tablet' => 1200,
                'width_mobile' => 100,
            ],
            'top_bar' => [
                'enabled' => true,
                'show_language' => true,
                'show_currency' => true,
                'show_apps' => true,
                'language_mode' => 'code',
                'currency_mode' => 'code',
                'support_label' => '24/7 Support',
                'support_phone' => '',
                'support_email' => '',
                'show_seller_button' => true,
                'seller_text' => 'BECOME A SELLER',
                'seller_url' => '/seller/apply',
                'custom_buttons' => $legacyButtons['top'],
            ],
            'main_bar' => [
                'enabled' => true,
                'logo_max_height' => 48,
                'icon_size' => 48,
                'show_language_icon' => false,
            ],
            'menu_bar' => [
                'enabled' => true,
                'show_sidebar_trigger' => true,
                'show_category_button' => true,
                'category_button_label' => 'ALL CATEGORIES',
                'category_items' => [],
                'show_seller_button' => false,
                'seller_text' => 'BECOME A SELLER',
                'seller_url' => '/seller/apply',
                'nav_links' => [
                    ['label' => 'Homepage', 'url' => '/', 'type' => 'link', 'display_order' => 1, 'dropdown_style' => 'dropdown', 'items' => []],
                    ['label' => 'Shop Products', 'url' => '/products', 'type' => 'link', 'display_order' => 2, 'dropdown_style' => 'dropdown', 'items' => []],
                    ['label' => 'Pages', 'url' => '#', 'type' => 'dropdown', 'items' => [
                        ['label' => 'All Categories', 'url' => '/categories'],
                        ['label' => 'Track Order', 'url' => '/track-order'],
                        ['label' => 'Customer Support', 'url' => '/contact'],
                    ], 'display_order' => 3, 'dropdown_style' => 'dropdown'],
                    ['label' => 'About Us', 'url' => '#', 'type' => 'link', 'display_order' => 4, 'dropdown_style' => 'dropdown', 'items' => []],
                    ['label' => 'Latest Blog', 'url' => '#', 'type' => 'link', 'display_order' => 5, 'dropdown_style' => 'dropdown', 'items' => []],
                    ['label' => 'Contact Us', 'url' => '/contact', 'type' => 'link', 'display_order' => 6, 'dropdown_style' => 'dropdown', 'items' => []],
                ],
                'custom_buttons' => $legacyButtons['menu'],
            ],
        ]);
    }

    private static function normalize(array $config): array
    {
        $defaults = self::seedFromLegacyDefaults();
        $merged = array_replace_recursive($defaults, $config);

        $merged['appearance']['top_height'] = max(30, min(80, (int) $merged['appearance']['top_height']));
        $merged['appearance']['main_height'] = max(40, min(100, (int) $merged['appearance']['main_height']));
        $merged['appearance']['menu_height'] = max(30, min(80, (int) $merged['appearance']['menu_height']));
        $merged['appearance']['width_desktop'] = max(1280, min(1920, (int) ($merged['appearance']['width_desktop'] ?? 1920)));
        $merged['appearance']['width_laptop'] = max(1024, min(1800, (int) ($merged['appearance']['width_laptop'] ?? 1600)));
        $merged['appearance']['width_tablet'] = max(768, min(1400, (int) ($merged['appearance']['width_tablet'] ?? 1200)));
        $merged['appearance']['width_mobile'] = max(320, min(900, (int) ($merged['appearance']['width_mobile'] ?? 100)));
        $merged['main_bar']['logo_max_height'] = max(28, min(90, (int) $merged['main_bar']['logo_max_height']));
        $merged['main_bar']['icon_size'] = max(28, min(72, (int) $merged['main_bar']['icon_size']));
        $merged['top_bar']['custom_buttons'] = self::normalizeButtons($merged['top_bar']['custom_buttons'] ?? []);
        $merged['menu_bar']['category_items'] = self::normalizeSimpleItems($merged['menu_bar']['category_items'] ?? []);
        $merged['menu_bar']['nav_links'] = self::normalizeButtons($merged['menu_bar']['nav_links'] ?? []);
        $merged['menu_bar']['custom_buttons'] = self::normalizeButtons($merged['menu_bar']['custom_buttons'] ?? []);
        $menuInput = is_array($config['menu_bar'] ?? null) ? $config['menu_bar'] : [];
        if (array_key_exists('nav_links', $menuInput) && is_array($menuInput['nav_links']) && empty($menuInput['nav_links'])) {
            $merged['menu_bar']['nav_links'] = [];
        }
        if (array_key_exists('custom_buttons', $menuInput) && is_array($menuInput['custom_buttons']) && empty($menuInput['custom_buttons'])) {
            $merged['menu_bar']['custom_buttons'] = [];
        }
        if (array_key_exists('category_items', $menuInput) && is_array($menuInput['category_items']) && empty($menuInput['category_items'])) {
            $merged['menu_bar']['category_items'] = [];
        }

        return $merged;
    }

    private static function normalizeButtons($buttons): array
    {
        if (!is_array($buttons)) {
            return [];
        }
        $out = [];
        foreach ($buttons as $button) {
            if (!is_array($button)) {
                continue;
            }
            $label = trim((string) ($button['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $type = (string) ($button['type'] ?? 'link');
            if (!in_array($type, ['link', 'dropdown'], true)) {
                $type = 'link';
            }
            $items = [];
            if ($type === 'dropdown' && is_array($button['items'] ?? null)) {
                $items = self::normalizeDropdownItems($button['items'], 1);
            }
            $out[] = [
                'label' => mb_substr($label, 0, 60),
                'url' => mb_substr((string) ($button['url'] ?? '#'), 0, 255),
                'type' => $type,
                'is_active' => (int) (!empty($button['is_active'] ?? 1)),
                'display_order' => max(0, (int) ($button['display_order'] ?? 0)),
                'tracking_key' => mb_substr((string) ($button['tracking_key'] ?? ''), 0, 80),
                'dropdown_style' => in_array((string) ($button['dropdown_style'] ?? 'dropdown'), ['dropdown', 'mega'], true)
                    ? (string) ($button['dropdown_style'] ?? 'dropdown')
                    : 'dropdown',
                'items' => $items,
            ];
        }

        usort($out, static function (array $a, array $b): int {
            return (int) ($a['display_order'] ?? 0) <=> (int) ($b['display_order'] ?? 0);
        });

        return array_slice($out, 0, 20);
    }

    private static function normalizeDropdownItems(array $items, int $depth = 1): array
    {
        if ($depth > 4) {
            return [];
        }
        $out = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $itemLabel = trim((string) ($item['label'] ?? ''));
            $itemUrl = trim((string) ($item['url'] ?? ''));
            if ($itemLabel === '') {
                continue;
            }
            $children = is_array($item['children'] ?? null) ? $item['children'] : [];
            $out[] = [
                'label' => mb_substr($itemLabel, 0, 60),
                'url' => $itemUrl !== '' ? mb_substr($itemUrl, 0, 255) : '#',
                'children' => self::normalizeDropdownItems($children, $depth + 1),
            ];
        }

        return array_slice($out, 0, 40);
    }

    private static function normalizeSimpleItems($items): array
    {
        if (!is_array($items)) {
            return [];
        }
        $out = [];
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? ''));
            $url = trim((string) ($item['url'] ?? ''));
            if ($label === '') {
                continue;
            }
            $out[] = [
                'label' => mb_substr($label, 0, 60),
                'url' => $url !== '' ? mb_substr($url, 0, 255) : '#',
            ];
        }

        return array_slice($out, 0, 30);
    }

    private static function seedFromLegacyDefaults(): array
    {
        return [
            'appearance' => [
                'top_bg' => '#0f172a',
                'main_bg' => '#f8fafc',
                'menu_bg' => '#c7eafe',
                'top_height' => 38,
                'main_height' => 56,
                'menu_height' => 38,
                'width_desktop' => 1920,
                'width_laptop' => 1600,
                'width_tablet' => 1200,
                'width_mobile' => 100,
            ],
            'top_bar' => [
                'enabled' => true,
                'is_public' => true,
                'show_language' => true,
                'show_currency' => true,
                'show_apps' => true,
                'language_mode' => 'code',
                'currency_mode' => 'code',
                'support_label' => '24/7 Support',
                'support_phone' => '',
                'support_email' => '',
                'show_seller_button' => true,
                'seller_text' => 'BECOME A SELLER',
                'seller_url' => '/seller/apply',
                'custom_buttons' => [],
            ],
            'main_bar' => [
                'enabled' => true,
                'is_public' => true,
                'logo_max_height' => 48,
                'icon_size' => 48,
                'show_language_icon' => false,
            ],
            'menu_bar' => [
                'enabled' => true,
                'is_public' => true,
                'show_sidebar_trigger' => true,
                'show_category_button' => true,
                'category_button_label' => 'ALL CATEGORIES',
                'category_items' => [],
                'show_seller_button' => false,
                'seller_text' => 'BECOME A SELLER',
                'seller_url' => '/seller/apply',
                'nav_links' => self::defaultMenuNavLinks(),
                'custom_buttons' => [],
            ],
        ];
    }

    private static function defaultMenuNavLinks(): array
    {
        return [
            [
                'label' => 'Homepage',
                'url' => '/',
                'type' => 'link',
                'display_order' => 1,
                'dropdown_style' => 'dropdown',
                'items' => [],
            ],
            [
                'label' => 'Shop Products',
                'url' => '/products',
                'type' => 'link',
                'display_order' => 2,
                'dropdown_style' => 'dropdown',
                'items' => [],
            ],
            [
                'label' => 'Pages',
                'url' => '#',
                'type' => 'dropdown',
                'display_order' => 3,
                'dropdown_style' => 'dropdown',
                'items' => [
                    ['label' => 'All Categories', 'url' => '/categories'],
                    ['label' => 'Track Order', 'url' => '/track-order'],
                    ['label' => 'Customer Support', 'url' => '/contact'],
                ],
            ],
            [
                'label' => 'About Us',
                'url' => '#',
                'type' => 'link',
                'display_order' => 4,
                'dropdown_style' => 'dropdown',
                'items' => [],
            ],
            [
                'label' => 'Latest Blog',
                'url' => '#',
                'type' => 'link',
                'display_order' => 5,
                'dropdown_style' => 'dropdown',
                'items' => [],
            ],
            [
                'label' => 'Contact Us',
                'url' => '/contact',
                'type' => 'link',
                'display_order' => 6,
                'dropdown_style' => 'dropdown',
                'items' => [],
            ],
        ];
    }

    private static function hydrateMissingFromLegacy(array $config): array
    {
        $legacyButtons = self::loadLegacyHeaderButtons();

        if (empty($config['top_bar']['custom_buttons']) && !empty($legacyButtons['top'])) {
            $config['top_bar']['custom_buttons'] = $legacyButtons['top'];
        }
        if (empty($config['menu_bar']['custom_buttons']) && !empty($legacyButtons['menu'])) {
            $config['menu_bar']['custom_buttons'] = $legacyButtons['menu'];
        }

        return $config;
    }

    private static function loadLegacyHeaderButtons(): array
    {
        $rows = Frontend::query()
            ->where('data_keys', 'custom_buttons.element')
            ->orderBy('id')
            ->get();

        if (!$rows instanceof Collection || $rows->isEmpty()) {
            return ['top' => [], 'menu' => []];
        }

        $top = [];
        $menu = [];
        foreach ($rows as $row) {
            $dv = json_decode(json_encode($row->data_values), true);
            if (!is_array($dv)) {
                continue;
            }
            if (($dv['target'] ?? '') !== 'header') {
                continue;
            }
            $label = trim((string) ($dv['button_text'] ?? ''));
            $url = trim((string) ($dv['button_url'] ?? '#')) ?: '#';
            if ($label === '') {
                continue;
            }
            $button = [
                'label' => mb_substr($label, 0, 60),
                'url' => mb_substr($url, 0, 255),
                'type' => 'link',
                'is_active' => 1,
                'items' => [],
            ];
            $position = (string) ($dv['position'] ?? 'right');
            if ($position === 'nav') {
                $menu[] = $button;
            } else {
                $top[] = $button;
            }
        }

        return ['top' => array_slice($top, 0, 20), 'menu' => array_slice($menu, 0, 20)];
    }
}

