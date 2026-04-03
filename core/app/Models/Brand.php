<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use Searchable, GlobalStatus;

    protected static function boot()
    {
        parent::boot();
        // ব্র্যান্ড ডিলিট হলে ডাটাবেজ থেকে রেকর্ড যাবে, সাথে সাথে ফাইলও ডিলিট
        static::deleting(function ($brand) {
            if ($brand->image) {
                $path = public_path(getFilePath('brand') . '/' . $brand->image);
                if (file_exists($path) && is_file($path)) {
                    @unlink($path);
                }
            }
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', Status::YES);
    }
    public function product()
    {
        return $this->hasMany(Product::class);
    }

    public function imageShow()
    {
        return getImage(getFilePath('brand') . '/' . $this->image, getFileSize('brand'));
    }

    //Scope
    public function scopeAvailable($query)
    {
        return $query->active()->whereHas('product', function ($product) {
            $product->active()->whereHas('category', function ($category) {
                $category->active();
            })->whereHas('subcategory', function ($subcategory) {
                $subcategory->active();
            });
        });
    }
}
