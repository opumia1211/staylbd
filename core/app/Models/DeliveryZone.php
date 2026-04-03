<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = ['thana_id', 'delivery_charge', 'estimated_days', 'status'];

    protected $casts = [
        'delivery_charge' => 'decimal:2',
    ];

    public function thana()
    {
        return $this->belongsTo(Thana::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
