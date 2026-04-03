<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class ShippingZoneCountry extends Model
{
    use GlobalStatus;

    protected $fillable = ['shipping_zone_id', 'country_iso', 'country_name', 'shipping_price', 'status'];

    protected $casts = [
        'shipping_price' => 'decimal:2',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }
}
