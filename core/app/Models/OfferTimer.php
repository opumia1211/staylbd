<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class OfferTimer extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'end_at',
        'style',
        'bar_width',
        'bar_height',
        'position',
        'show_on_pages',
        'product_ids',
        'category_ids',
        'link_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'end_at' => 'datetime',
        'show_on_pages' => 'array',
        'product_ids' => 'array',
        'category_ids' => 'array',
    ];

    public const STYLE_SMALL = 'bar_small';
    public const STYLE_LARGE = 'bar_large';
    public const STYLE_FULL = 'full_width';

    public const POSITION_HEADER = 'header';
    public const POSITION_BELOW_HEADER = 'below_header';
    public const POSITION_CART_TOP = 'cart_top';
    public const POSITION_CHECKOUT_TOP = 'checkout_top';
    public const POSITION_PRODUCT_DETAIL = 'product_detail';
    public const POSITION_CATEGORY_TOP = 'category_top';
    public const POSITION_CONTENT_TOP = 'content_top';
    public const POSITION_CONTENT_BOTTOM = 'content_bottom';
    public const POSITION_USER_DASHBOARD_TOP = 'user_dashboard_top';
    public const POSITION_FLOATING = 'floating';

    public const PAGE_HOME = 'home';
    public const PAGE_CART = 'cart';
    public const PAGE_CHECKOUT = 'checkout';
    public const PAGE_PRODUCT_DETAIL = 'product_detail';
    public const PAGE_CATEGORY = 'category';
    public const PAGE_USER_DASHBOARD = 'user_dashboard';
    public const PAGE_ALL = 'all';

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', 1)->where('end_at', '>', now());
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeForPage(Builder $query, string $page): Builder
    {
        return $query->whereNotNull('show_on_pages')
            ->where(function ($q) use ($page) {
                $q->whereJsonContains('show_on_pages', self::PAGE_ALL)
                    ->orWhereJsonContains('show_on_pages', $page);
            });
    }

    public function scopeForPosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
    }

    public function scopeForProduct(Builder $query, ?int $productId): Builder
    {
        if (!$productId) {
            return $query->whereNull('product_ids')->orWhere('product_ids', '[]');
        }
        return $query->where(function ($q) use ($productId) {
            $q->whereNull('product_ids')->orWhereJsonContains('product_ids', $productId);
        });
    }

    public function scopeForCategory(Builder $query, ?int $categoryId): Builder
    {
        if (!$categoryId) {
            return $query->where(function ($q) {
                $q->whereNull('category_ids')->orWhere('category_ids', '[]');
            });
        }
        return $query->where(function ($q) use ($categoryId) {
            $q->whereNull('category_ids')->orWhere('category_ids', '[]')->orWhereJsonContains('category_ids', $categoryId);
        });
    }

    public function isVisibleOnPage(string $page): bool
    {
        $pages = $this->show_on_pages ?? [];
        return in_array(self::PAGE_ALL, $pages) || in_array($page, $pages);
    }

    public function isVisibleForProduct(?int $productId): bool
    {
        $ids = $this->product_ids;
        if (empty($ids) || $ids === []) {
            return true;
        }
        return $productId && in_array($productId, $ids);
    }

    public function isVisibleForCategory(?int $categoryId): bool
    {
        $ids = $this->category_ids;
        if (empty($ids) || $ids === []) {
            return true;
        }
        return $categoryId && in_array($categoryId, $ids);
    }

    public function getLinkUrl(): string
    {
        if ($this->link_url) {
            return $this->link_url;
        }
        return '#';
    }
}
