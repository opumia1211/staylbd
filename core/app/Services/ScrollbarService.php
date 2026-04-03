<?php

namespace App\Services;

use App\Models\Frontend;

/**
 * Single source of truth for scrollbar data structure.
 * Save, Edit API, and Frontend all use this schema.
 *
 * Caching: Public scrollbar list and settings are cached (CACHE_TTL) to reduce DB load.
 * Admin actions (save, delete, toggle, duplicate, settings) call Cache::forget so changes
 * appear immediately. Admin routes receive no-cache headers via CacheHeaders middleware.
 * Static assets (scrollbar.css, images) are cacheable; dynamic admin/session data is not.
 */
class ScrollbarService
{
    public const DATA_KEY = 'scrollbar.element';
    public const CUSTOM_DATA_KEY = 'scrollbar.custom.element';
    public const SETTINGS_KEY = 'scrollbar.settings';

    /** Cache key for raw bars list (invalidated on save/delete/toggle/duplicate/settings). */
    public const CACHE_KEY_RAW = 'scrollbar_bars';
    /** Cache key for global enable/disable settings. */
    public const CACHE_KEY_SETTINGS = 'scrollbar_settings';
    /** TTL in seconds for public scrollbar list (5 min). Admin actions call Cache::forget. */
    public const CACHE_TTL = 300;

    /** All allowed positions (site-wide + per-page). */
    public const POSITIONS = [
        'header_above' => 'Top of Website (Everywhere)',
        'header_below' => 'Under Header (Everywhere)',
        'banner_above' => 'Above Home Banner',
        'banner_below' => 'Below Home Banner',
        'product_line' => 'Above Every Product Line (Home)',
        'content_top' => 'Top of Content Area',
        'content_bottom' => 'Bottom of Content Area',
        'footer_above' => 'Above Footer (Everywhere)',
        'footer_below' => 'Bottom of Website (Everywhere)',
        'product_listing_above' => 'Products Page - Top',
        'product_listing_below' => 'Products Page - Bottom',
        'category_above' => 'Category Page - Top',
        'category_below' => 'Category Page - Bottom',
        'product_detail_above' => 'Product Details - Top',
        'product_detail_below' => 'Product Details - Bottom',
        'product_listing' => 'Products Page (Old Style)',
        'category_page' => 'Category Page (Old Style)',
        'custom' => 'Custom Screen Position (Float Anywhere)',
    ];

    /** Position values for validation (keys only). */
    public static function positionValues(): array
    {
        return array_keys(self::POSITIONS);
    }

    /** Default layout for new bars */
    public const LAYOUT_DEFAULTS = [
        'container_mode' => 'full',
        'align' => 'center',
        'z_index' => 10,
        'sticky' => false,
        'offset_top' => '0px',
    ];

    /** Default animation options */
    public const ANIMATION_DEFAULTS = [
        'loop_delay' => 0,
        'item_animation' => 'none',
        'icon_animation' => 'none',
        'hover_effect' => 'pause',
    ];

    /** Normalize a single segment (for multi-segment items) */
    public static function normalizeSegment(array $raw): array
    {
        $text = (string)($raw['text'] ?? '');
        // Keep user spacing, but prevent line-break based layout breaks on frontend.
        $text = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $text);
        return [
            // Preserve exact spacing typed by admin (emoji পাশে extra gap avoid).
            'text' => $text,
            'color' => $raw['color'] ?? '#333333',
            'weight' => (string)($raw['weight'] ?? $raw['font_weight'] ?? '400'),
            'font_family' => (string)($raw['font_family'] ?? 'inherit'),
            'font_size' => (string)($raw['font_size'] ?? ''),
        ];
    }

    /** Default item schema - one unified structure for Save / Edit / Frontend. Supports segments (TV-grade multi-style). */
    public static function normalizeItem(array $raw, int $idx, array $existingItems = []): ?array
    {
        $type = $raw['type'] ?? 'text';
        $content = '';
        $segments = [];

        if (!empty($raw['segments']) && is_array($raw['segments'])) {
            foreach ($raw['segments'] as $seg) {
                $seg = is_array($seg) ? $seg : (array)$seg;
                $normalized = self::normalizeSegment($seg);
                if ($normalized['text'] !== '') {
                    $segments[] = $normalized;
                }
            }
        }

        if ($type === 'image') {
            $content = trim((string)($raw['content_image'] ?? $raw['content'] ?? ''));
        } else {
            $content = trim((string)($raw['content_text'] ?? $raw['content'] ?? ''));
        }

        if ($content === '' && $type !== 'image' && empty($segments)) {
            return null;
        }

        $item = [
            'type' => $type,
            'content' => $content,
            'content_text' => $content, // persist so edit form always finds text
            'color' => $raw['color'] ?? '#333333',
            'font_size' => $raw['font_size'] ?? '',
            'font_weight' => (string)($raw['font_weight'] ?? '400'),
            'font_family' => $raw['font_family'] ?? 'inherit',
            'font_style' => in_array($raw['font_style'] ?? '', ['bold', 'italic']) ? $raw['font_style'] : 'normal',
            'letter_spacing' => $raw['letter_spacing'] ?? '',
            'text_transform' => in_array($raw['text_transform'] ?? '', ['uppercase', 'lowercase', 'capitalize']) ? $raw['text_transform'] : 'none',
            'is_active' => isset($raw['is_active']) ? (int)$raw['is_active'] : 1,
        ];
        if (!empty($segments)) {
            $item['segments'] = $segments;
        }
        return $item;
    }

    /** Convert stored item (may be stdClass) to array for API/Blade. Always include content + content_text so edit form shows saved text. */
    public static function itemToArray($it): array
    {
        $it = is_array($it) ? $it : (array)$it;
        $content = (string)($it['content'] ?? $it['content_text'] ?? $it['content_image'] ?? '');
        $content = trim($content);
        $out = [
            'type' => $it['type'] ?? 'text',
            'content' => $content,
            'content_text' => $content, // so edit form textarea shows saved text
            'color' => $it['color'] ?? '#333333',
            'font_size' => (string)($it['font_size'] ?? ''),
            'font_weight' => (string)($it['font_weight'] ?? '400'),
            'font_family' => $it['font_family'] ?? 'inherit',
            'font_style' => $it['font_style'] ?? 'normal',
            'letter_spacing' => (string)($it['letter_spacing'] ?? ''),
            'text_transform' => $it['text_transform'] ?? 'none',
            'is_active' => isset($it['is_active']) ? (int)$it['is_active'] : 1,
        ];
        if (!empty($it['segments']) && is_array($it['segments'])) {
            $out['segments'] = array_values(array_map(function ($s) {
                $s = is_array($s) ? $s : (array)$s;
                return [
                    'text' => (string)($s['text'] ?? ''),
                    'color' => $s['color'] ?? '#333333',
                    'weight' => (string)($s['weight'] ?? $s['font_weight'] ?? '400'),
                    'font_family' => (string)($s['font_family'] ?? 'inherit'),
                    'font_size' => (string)($s['font_size'] ?? ''),
                ];
            }, $it['segments']));
        }
        return $out;
    }

    /** Get first ~80 chars of item content for admin preview (single item or segments) */
    public static function itemPreviewSummary($it, int $maxLen = 80): string
    {
        $it = is_array($it) ? $it : (array)$it;
        if (!empty($it['segments']) && is_array($it['segments'])) {
            $parts = [];
            foreach ($it['segments'] as $s) {
                $s = is_array($s) ? $s : (array)$s;
                $parts[] = (string)($s['text'] ?? '');
            }
            $text = implode(' ', $parts);
        } else {
            $text = (string)($it['content'] ?? $it['content_text'] ?? '');
        }
        if (strlen($text) <= $maxLen) {
            return $text;
        }
        return substr($text, 0, $maxLen) . '…';
    }

    public static function getScrollbarImagePath(): string
    {
        $path = base_path('../assets/images/frontend/scrollbar');
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
        return $path;
    }
}
