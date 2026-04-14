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
        $live = self::readByKey(self::LIVE_KEY);
        if ($live !== []) {
            return self::hydrateMissingFromLegacy(self::normalize($live));
        }

        $seed = self::seedFromLegacy();
        self::saveLive($seed);

        return $seed;
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
                'show_seller_button' => false,
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
        $merged['main_bar']['logo_max_height'] = max(28, min(90, (int) $merged['main_bar']['logo_max_height']));
        $merged['main_bar']['icon_size'] = max(28, min(72, (int) $merged['main_bar']['icon_size']));
        $merged['top_bar']['custom_buttons'] = self::normalizeButtons($merged['top_bar']['custom_buttons'] ?? []);
        $merged['menu_bar']['custom_buttons'] = self::normalizeButtons($merged['menu_bar']['custom_buttons'] ?? []);

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
                foreach ($button['items'] as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $itemLabel = trim((string) ($item['label'] ?? ''));
                    $itemUrl = trim((string) ($item['url'] ?? ''));
                    if ($itemLabel === '') {
                        continue;
                    }
                    $items[] = [
                        'label' => mb_substr($itemLabel, 0, 60),
                        'url' => $itemUrl !== '' ? mb_substr($itemUrl, 0, 255) : '#',
                    ];
                }
            }
            $out[] = [
                'label' => mb_substr($label, 0, 60),
                'url' => mb_substr((string) ($button['url'] ?? '#'), 0, 255),
                'type' => $type,
                'items' => $items,
            ];
        }

        return array_slice($out, 0, 20);
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
                'show_seller_button' => true,
                'seller_text' => 'BECOME A SELLER',
                'seller_url' => '/seller/apply',
                'custom_buttons' => [],
            ],
            'main_bar' => [
                'enabled' => true,
                'logo_max_height' => 48,
                'icon_size' => 48,
                'show_language_icon' => false,
            ],
            'menu_bar' => [
                'enabled' => true,
                'show_seller_button' => false,
                'custom_buttons' => [],
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

