<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    /** Maximum cart items (distinct product/variant lines) allowed per user or session. */
    public const CART_MAX = 200;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
