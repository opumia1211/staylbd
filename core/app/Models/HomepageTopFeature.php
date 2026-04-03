<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HomepageTopFeature extends Model
{
    protected $fillable = [
        'title',
        'icon_image',
        'background_style',
        'product_id',
        'category_id',
        'offer_price',
        'discount_percentage',
        'offer_start',
        'offer_end',
        'redirect_url',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'offer_start' => 'datetime',
        'offer_end'   => 'datetime',
        'offer_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /** Only show if status active and offer not expired (or no offer dates) */
    public function scopeVisibleOnFront(Builder $query): Builder
    {
        return $query->active()->ordered()->where(function ($q) {
            $q->whereNull('offer_end')->orWhere('offer_end', '>', now());
        });
    }

    public function isOfferActive(): bool
    {
        if (!$this->offer_start && !$this->offer_end) {
            return false;
        }
        $now = now();
        if ($this->offer_start && $now->lt($this->offer_start)) {
            return false;
        }
        if ($this->offer_end && $now->gt($this->offer_end)) {
            return false;
        }
        return true;
    }

    public function imageShow(): string
    {
        if (!$this->icon_image) {
            return getImage(getFilePath('default'));
        }
        return getImage(getFilePath('topFeature') . '/' . $this->icon_image, getFileSize('topFeature'));
    }

    public function getRedirectUrl(): string
    {
        if ($this->redirect_url) {
            return $this->redirect_url;
        }
        if ($this->product_id && $this->product) {
            return product_detail_url($this->product);
        }
        if ($this->category_id && $this->category) {
            return route('category.products', [slug($this->category->name), $this->category->id]);
        }
        return '#';
    }
}
