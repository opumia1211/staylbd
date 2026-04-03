<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'attributes',
        'price',
        'discount',
        'discount_type',
        'quantity',
        'image',
        'status'
    ];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'quantity' => 'integer',
        'status' => 'integer'
    ];

    /**
     * Parent product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope: Active variants
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope: In stock
     */
    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Get final price after discount
     */
    public function getFinalPriceAttribute()
    {
        if ($this->discount <= 0) {
            return $this->price;
        }

        if ($this->discount_type == 1) {
            // Fixed discount
            return max(0, $this->price - $this->discount);
        } else {
            // Percentage discount
            return $this->price - ($this->price * $this->discount / 100);
        }
    }

    /**
     * Get variant name (e.g., "Red - Large")
     */
    public function getNameAttribute()
    {
        if (!$this->attributes) {
            return 'Default';
        }

        return collect($this->attributes)->map(function ($value, $key) {
            return ucfirst($value);
        })->implode(' - ');
    }

    /**
     * Get variant image or fallback to product image
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return getImage(getFilePath('product') . '/' . $this->image);
        }

        return $this->product ? $this->product->imageShow() : null;
    }

    /**
     * Check if variant is available
     */
    public function isAvailable()
    {
        return $this->status == 1 && $this->quantity > 0;
    }

    /**
     * Decrease quantity
     */
    public function decreaseQuantity($amount)
    {
        $this->quantity = max(0, $this->quantity - $amount);
        $this->save();
    }

    /**
     * Increase quantity
     */
    public function increaseQuantity($amount)
    {
        $this->quantity += $amount;
        $this->save();
    }
}
