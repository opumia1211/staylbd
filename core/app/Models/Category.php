<?php

namespace App\Models;

use App\Constants\Status;
use App\Models\ProductAttribute;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Category extends Model
{
    use Searchable, GlobalStatus;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('storefront.header_nav_categories_v1'));
        static::deleted(fn () => Cache::forget('storefront.header_nav_categories_v1'));
    }

    public const PUBLISH_PENDING = 'pending';
    public const PUBLISH_PUBLIC = 'public';
    public const PUBLISH_SCHEDULED = 'scheduled';

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class)->active();
    }

    public function product()
    {
        return $this->hasMany(Product::class);
    }

    /** Attributes assigned to this category (for filters & variants). */
    public function attributes()
    {
        return $this->belongsToMany(ProductAttribute::class, 'category_attributes', 'category_id', 'attribute_id')
            ->withPivot('is_required', 'is_variant', 'sort_order')
            ->withTimestamps();
    }

    public function imageShow()
    {
        return getImage(getFilePath('category') . '/' . $this->image, getFileSize('category'));
    }

    //Scope

    public function scopeFeatured($query)
    {
        return $query->where('featured', Status::YES);
    }

    public function scopeAvailable($query)
    {
        return $query->active()->whereHas('product', function ($product) {
            $product->active()->whereHas('brand', function ($brand) {
                $brand->active();
            })->whereHas('subcategory', function ($subcategory) {
                $subcategory->active();
            });
        });
    }

    /** Scope: visible on frontend = Public (with optional schedule passed) or Scheduled and schedule time passed. */
    public function scopePublicPublished($query)
    {
        if (!Schema::hasColumn((new self)->getTable(), 'publish_status')) {
            return $query;
        }
        return $query->where(function ($q) {
            $q->where(function ($q2) {
                $q2->where('publish_status', self::PUBLISH_PUBLIC)
                    ->where(function ($q3) {
                        $q3->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
                    });
            })->orWhere(function ($q2) {
                $q2->where('publish_status', self::PUBLISH_SCHEDULED)
                    ->whereNotNull('scheduled_at')
                    ->where('scheduled_at', '<=', now());
            });
        });
    }

    public function isPending(): bool
    {
        return ($this->publish_status ?? self::PUBLISH_PUBLIC) === self::PUBLISH_PENDING;
    }

    public function isScheduled(): bool
    {
        return ($this->publish_status ?? self::PUBLISH_PUBLIC) === self::PUBLISH_SCHEDULED;
    }
}
