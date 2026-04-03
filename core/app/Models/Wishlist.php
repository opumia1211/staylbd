<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    /** Maximum products allowed in wishlist per user or session. */
    public const WISHLIST_MAX = 200;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
