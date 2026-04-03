<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use Searchable;
    protected $fillable = [
        'name',
        'slug',
        'type',
        'values',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'values' => 'array',
        'status' => 'integer',
        'sort_order' => 'integer'
    ];

    // Attribute types
    const TYPE_SELECT = 'select';
    const TYPE_COLOR = 'color';
    const TYPE_TEXT = 'text';
    const TYPE_NUMBER = 'number';

    /**
     * Categories that use this attribute (pivot: category_attributes.attribute_id, category_id)
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attributes', 'attribute_id', 'category_id')
            ->withPivot('is_required', 'is_variant', 'sort_order')
            ->withTimestamps();
    }

    /**
     * Products using this attribute
     */
    public function productValues()
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    /**
     * Scope: Active attributes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get formatted values for display
     */
    public function getFormattedValuesAttribute()
    {
        if (!$this->values) {
            return [];
        }

        return collect($this->values)->map(function ($value) {
            return [
                'value' => $value,
                'label' => ucfirst($value)
            ];
        });
    }
}
