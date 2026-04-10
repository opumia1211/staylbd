<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Subcategory extends Model
{
    use GlobalStatus, Searchable;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('storefront.header_nav_categories_v1'));
        static::deleted(fn () => Cache::forget('storefront.header_nav_categories_v1'));
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'subcategory_id');
    }
}
