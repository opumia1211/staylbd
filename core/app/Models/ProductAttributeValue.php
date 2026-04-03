<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'product_id',
        'attribute_id',
        'value'
    ];

    /**
     * Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Attribute
     */
    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }
}
