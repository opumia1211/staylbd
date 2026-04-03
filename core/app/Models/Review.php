<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'stars',
        'title',
        'review_comment',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
        'images',
        'is_featured',
        'is_private',
    ];

    protected $casts = [
        'is_verified_purchase' => 'boolean',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'is_private' => 'boolean',
        'images' => 'array',
        'helpful_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /** Reviews visible on product page: approved and not private (admin-only). */
    public function scopeVisibleOnProduct($query)
    {
        return $query->where('is_approved', true)->where('is_private', false);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
