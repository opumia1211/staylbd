<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'name', 'type', 'status', 'base_price', 'estimated_days', 'free_shipping', 'cod_enabled',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'free_shipping' => 'boolean',
        'cod_enabled' => 'boolean',
    ];



    public function countries()
    {
        return $this->hasMany(ShippingZoneCountry::class, 'shipping_zone_id');
    }

    public function areas()
    {
        return $this->hasMany(ShippingZoneArea::class, 'shipping_zone_id');
    }

    public function methods()
    {
        return $this->hasMany(ShippingMethod::class, 'shipping_zone_id');
    }

    public function scopeNational($query)
    {
        return $query->where('type', 'national');
    }

    public function scopeInternational($query)
    {
        return $query->where('type', 'international');
    }
}
