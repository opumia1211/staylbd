<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PopupAd extends Model
{
    public const POSITION_CENTER = 'center';
    public const POSITION_TOP_LEFT = 'top-left';
    public const POSITION_TOP_RIGHT = 'top-right';
    public const POSITION_BOTTOM_LEFT = 'bottom-left';
    public const POSITION_BOTTOM_RIGHT = 'bottom-right';

    public const DISPLAY_POPUP = 'popup';
    public const DISPLAY_INLINE = 'inline';

    public const INLINE_SIDEBAR_RIGHT = 'sidebar_right';
    public const INLINE_SIDEBAR_LEFT = 'sidebar_left';
    public const INLINE_CONTENT_TOP = 'content_top';
    public const INLINE_CONTENT_BOTTOM = 'content_bottom';

    protected $fillable = [
        'name',
        'delay_seconds',
        'image',
        'link_url',
        'width',
        'height',
        'position',
        'display_type',
        'inline_placement',
        'show_on_pages',
        'is_active',
        'sort_order',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'show_on_pages' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public const PAGE_ALL = 'all';
    public const PAGE_HOME = 'home';
    public const PAGE_CART = 'cart';
    public const PAGE_CHECKOUT = 'checkout';
    public const PAGE_PRODUCT_DETAIL = 'product_detail';
    public const PAGE_CATEGORY = 'category';
    public const PAGE_USER_DASHBOARD = 'user_dashboard';
    public const PAGE_SEARCH = 'search';
    public const PAGE_CONTACT = 'contact';
    public const PAGE_WISHLIST = 'wishlist';
    public const PAGE_OTHER = 'other';

    public static function positionOptions(): array
    {
        return [
            self::POSITION_CENTER => __('Center (middle of screen)'),
            self::POSITION_TOP_LEFT => __('Top Left'),
            self::POSITION_TOP_RIGHT => __('Top Right'),
            self::POSITION_BOTTOM_LEFT => __('Bottom Left'),
            self::POSITION_BOTTOM_RIGHT => __('Bottom Right'),
        ];
    }

    public static function displayTypeOptions(): array
    {
        return [
            self::DISPLAY_POPUP => __('Popup (user can close)'),
            self::DISPLAY_INLINE => __('Inline (stays on page, e.g. sidebar)'),
        ];
    }

    public static function inlinePlacementOptions(): array
    {
        return [
            self::INLINE_SIDEBAR_RIGHT => __('Sidebar right (e.g. user dashboard / payment page)'),
            self::INLINE_SIDEBAR_LEFT => __('Sidebar left'),
            self::INLINE_CONTENT_TOP => __('Content top'),
            self::INLINE_CONTENT_BOTTOM => __('Content bottom'),
        ];
    }

    public function getDisplayType(): string
    {
        return in_array($this->display_type ?? '', [self::DISPLAY_POPUP, self::DISPLAY_INLINE], true)
            ? $this->display_type
            : self::DISPLAY_POPUP;
    }

    public function getInlinePlacement(): ?string
    {
        $valid = [self::INLINE_SIDEBAR_RIGHT, self::INLINE_SIDEBAR_LEFT, self::INLINE_CONTENT_TOP, self::INLINE_CONTENT_BOTTOM];
        $p = $this->inline_placement ?? null;
        return $p && in_array($p, $valid, true) ? $p : null;
    }

    public static function pageOptions(): array
    {
        return [
            self::PAGE_ALL => __('All pages'),
            self::PAGE_HOME => __('Home'),
            self::PAGE_CATEGORY => __('Category / Subcategory / Brand / All products'),
            self::PAGE_PRODUCT_DETAIL => __('Product Detail'),
            self::PAGE_CART => __('Cart'),
            self::PAGE_CHECKOUT => __('Checkout'),
            self::PAGE_WISHLIST => __('Wishlist'),
            self::PAGE_SEARCH => __('Search'),
            self::PAGE_CONTACT => __('Contact'),
            self::PAGE_USER_DASHBOARD => __('User Dashboard'),
            self::PAGE_OTHER => __('Other pages'),
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1)
            ->where(function ($q) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', now());
            });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeForPage(Builder $query, string $page): Builder
    {
        return $query->where(function ($q) use ($page) {
            $q->whereNull('show_on_pages')
                ->orWhere('show_on_pages', '=', '[]')
                ->orWhereJsonContains('show_on_pages', self::PAGE_ALL)
                ->orWhereJsonContains('show_on_pages', $page);
        });
    }

    public function getLinkUrl(): string
    {
        return $this->link_url ?: '#';
    }

    public function getPosition(): string
    {
        $valid = [self::POSITION_CENTER, self::POSITION_TOP_LEFT, self::POSITION_TOP_RIGHT, self::POSITION_BOTTOM_LEFT, self::POSITION_BOTTOM_RIGHT];
        return in_array($this->position ?? '', $valid, true) ? $this->position : self::POSITION_CENTER;
    }

    /**
     * Safe CSS width for display (e.g. 400px, 90%, 80vw). Sanitized to prevent XSS.
     */
    public function getWidth(): string
    {
        return self::sanitizeCssSize($this->width, '400px');
    }

    /**
     * Safe CSS height for display (e.g. 300px, 80vh, auto). Sanitized to prevent XSS.
     */
    public function getHeight(): string
    {
        return self::sanitizeCssSize($this->height, '300px');
    }

    /**
     * Allow only safe CSS length/percentage/auto for popup dimensions.
     */
    public static function sanitizeCssSize(?string $value, string $default): string
    {
        if ($value === null || $value === '') {
            return $default;
        }
        $value = trim($value);
        if (strtolower($value) === 'auto') {
            return 'auto';
        }
        if (preg_match('/^\d+(\.\d+)?(px|%|vw|vh|em|rem)?$/i', $value)) {
            return $value;
        }
        if (preg_match('/^\d+(\.\d+)?$/', $value)) {
            return $value . 'px';
        }
        return $default;
    }
}
