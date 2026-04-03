<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use Searchable, GlobalStatus;

    public const SLUG_MAX_LENGTH = 50;

    protected static function boot()
    {
        parent::boot();

        static::created(function (Product $product) {
            $product->slug = static::buildShortSlugForProduct($product);
            $product->saveQuietly();
        });

        static::saving(function (Product $product) {
            if ($product->exists && empty(trim((string) ($product->slug ?? ''))) && (int) $product->id > 0) {
                $product->slug = static::buildShortSlugForProduct($product);
            }
        });
    }

    /**
     * Short SEO slug: up to 2 keywords from name + "-" + id (max 50 chars total).
     * Does NOT use full product title.
     */
    public static function buildShortSlugForProduct(Product $product): string
    {
        $id = max(1, (int) $product->id);
        $suffix = '-' . $id;
        $maxBase = max(3, self::SLUG_MAX_LENGTH - strlen($suffix));

        $name = (string) ($product->name ?? '');
        $normalized = mb_strtolower(trim($name), 'UTF-8');
        $words = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $remove = ['with', 'and', 'the', 'for', 'a', 'an', 'of', 'in', 'on', 'at', 'to', 'is', 'are', 'was', 'were', 'print', 'printing', 'or', 'by', 'from', 'as', 'be'];

        $filtered = [];
        foreach ($words as $w) {
            $w = preg_replace('/[^\p{L}\p{N}]+/u', '', $w);
            if ($w === '' || mb_strlen($w) < 2) {
                continue;
            }
            if (in_array(mb_strtolower($w, 'UTF-8'), $remove, true)) {
                continue;
            }
            $filtered[] = $w;
            if (count($filtered) >= 2) {
                break;
            }
        }

        $shortName = implode(' ', $filtered);
        $base = Str::slug($shortName);
        if ($base === '') {
            $base = 'item';
        }
        if (strlen($base) > $maxBase) {
            $base = substr($base, 0, $maxBase);
            $base = rtrim($base, '-');
            if ($base === '') {
                $base = 'item';
            }
        }

        $slug = $base . $suffix;

        return strlen($slug) <= self::SLUG_MAX_LENGTH ? $slug : (substr($base, 0, max(1, self::SLUG_MAX_LENGTH - strlen($suffix))) . $suffix);
    }

    /**
     * Parse product id from URL slug (…-123 at end).
     */
    public static function parseIdFromProductSlug(string $slug): ?int
    {
        if (!preg_match('/-(\d+)$/', $slug, $m)) {
            return null;
        }
        $id = (int) $m[1];

        return $id > 0 ? $id : null;
    }

    protected $fillable = [
        'name',
        'slug',
        'brand_id',
        'category_id',
        'subcategory_id',
        'product_sku',
        'quantity',
        'has_variants',
        'variant_attributes',
        'price',
        'original_price',
        'discount',
        'discount_type',
        'profit_margin',
        'low_stock_alert',
        'warehouse_location',
        'shipping_weight',
        'shipping_class',
        'delivery_time',
        'delivery_type',
        'delivery_charge',
        'digital_item',
        'cod_disabled',
        'file_type',
        'link',
        'file',
        'summary',
        'key_features',
        'description',
        'features',
        'gallery',
        'image',
        'video',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'product_type',
        'fabric_type',
        'material',
        'season',
        'color_variants',
        'source_url',
        'target_gender',
        'target_age_min',
        'target_age_max',
        'hot_deals',
        'featured_product',
        'today_deals',
        'trending_now',
        'home_section_override',
        'home_section_rank',
        'home_exclude_from_auto',
        'status',
        'sale_count',
        'avg_rate'
    ];

    protected $casts = [
        'features' => 'array',
        'gallery' => 'array',
        'variant_attributes' => 'array',
        'color_variants' => 'array',
        'meta_keywords' => 'array',
        'has_variants' => 'integer',
        'home_section_rank' => 'integer',
        'home_exclude_from_auto' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Product variants
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Active variants only
     */
    public function activeVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('status', 1);
    }

    /**
     * Available variants (active + in stock)
     */
    public function availableVariants()
    {
        return $this->hasMany(ProductVariant::class)
            ->where('status', 1)
            ->where('quantity', '>', 0);
    }

    /**
     * Product attribute values
     */
    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Product main image URL. Uses only filename from DB (no absolute paths).
     * DB must store: filename only e.g. "abc123.jpg" or "assets/images/product/abc123.jpg" is normalized.
     */
    public function imageShow()
    {
        $image = $this->image;
        if ($image && (str_contains($image, '\\') || str_contains($image, '/') || str_contains($image, ':'))) {
            $image = basename(str_replace('\\', '/', $image));
        }
        return getImage(getFilePath('product') . '/' . $image, getFileSize('product'));
    }

    /** WebP URL when available (faster load on user pages); falls back to original. */
    public function imageShowWebP()
    {
        $image = $this->image;
        if ($image && (str_contains($image, '\\') || str_contains($image, '/') || str_contains($image, ':'))) {
            $image = basename(str_replace('\\', '/', $image));
        }
        return getImageWebP(getFilePath('product') . '/' . $image, getFileSize('product'));
    }

    // Scopes
    public function scopeTodayDeal($query)
    {
        return $query->where('today_deals', Status::YES);
    }

    public function scopeHotDeal($query)
    {
        return $query->where('hot_deals', Status::YES);
    }

    public function scopeBestSelling($query)
    {
        return $query->where('sale_count', '!=', 0)->orderBy('sale_count', 'DESC');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured_product', Status::YES);
    }

    /**
     * Products marked as Trending Now (admin manual + used in homepage section).
     */
    public function scopeTrendingNow($query)
    {
        return $query->where('trending_now', Status::YES);
    }

    /**
     * Products not in any spotlight (featured / hot deal / today deal).
     * Use for Best Selling, Recommended etc. so same product does not repeat across sections.
     */
    public function scopeNotSpotlight($query)
    {
        return $query->where('featured_product', Status::NO)
            ->where('hot_deals', Status::NO)
            ->where('today_deals', Status::NO);
    }

    public function scopeAvailable($query)
    {
        return $query->active()->whereHas('category', function ($category) {
            $category->active();
        })->whereHas('brand', function ($brand) {
            $brand->active();
        })->whereHas('subcategory', function ($subcategory) {
            $subcategory->active();
        });
    }

    /**
     * Check if product has variants
     */
    public function hasVariants()
    {
        return $this->has_variants == 1 && $this->variants()->count() > 0;
    }

    /**
     * Get available stock (sum of all variant quantities or product quantity)
     */
    public function getAvailableStockAttribute()
    {
        if ($this->hasVariants()) {
            return $this->variants()->sum('quantity');
        }
        return $this->quantity;
    }

    /**
     * Get price range for variants
     */
    public function getPriceRangeAttribute()
    {
        if (!$this->hasVariants()) {
            return null;
        }

        $variants = $this->activeVariants;
        if ($variants->isEmpty()) {
            return null;
        }

        $prices = $variants->pluck('final_price');
        $min = $prices->min();
        $max = $prices->max();

        if ($min == $max) {
            return showAmount($min);
        }

        return showAmount($min) . ' - ' . showAmount($max);
    }

    /**
     * SEO: auto-generate meta keywords from name + summary if empty (product meta auto-generate)
     */
    public function getMetaKeywordsAttribute($value)
    {
        if ($value !== null && $value !== '') {
            $decoded = is_string($value) ? json_decode($value, true) : $value;
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }
        $name = $this->attributes['name'] ?? '';
        $summary = $this->attributes['summary'] ?? '';
        $text = $name . ' ' . strip_tags((string) $summary);
        $words = array_filter(explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', ' ', $text)), function ($w) {
            return strlen($w) >= 2;
        });
        return array_values(array_slice(array_unique($words), 0, 10));
    }
}
