<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageCustomProductRow extends Model
{
    protected $table = 'homepage_custom_product_rows';

    protected $fillable = [
        'title',
        'subtitle',
        'is_active',
        'sort_order',
        'source_type',
        'category_id',
        'product_ids',
        'product_limit',
        'interval_seconds',
        'view_all_url',
        'view_all_label',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'product_ids' => 'array',
        'category_id' => 'integer',
        'sort_order' => 'integer',
        'product_limit' => 'integer',
        'interval_seconds' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function sectionKey(): string
    {
        return 'custom_row_' . $this->id;
    }
}
