<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thana extends Model
{
    protected $fillable = ['district_id', 'name_en', 'name_bn', 'postal_code', 'sort_order', 'status'];

    protected $attributes = [
        'status' => 1,
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function deliveryZones()
    {
        return $this->hasMany(DeliveryZone::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
