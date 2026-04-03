<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class ShippingZoneArea extends Model
{
    use GlobalStatus;

    protected $fillable = ['shipping_zone_id', 'area_name', 'district_names', 'shipping_price', 'free_shipping', 'status'];

    protected $casts = [
        'district_names' => 'array',
        'shipping_price' => 'decimal:2',
        'free_shipping' => 'boolean',
    ];

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function matchesCityOrState(string $city, string $state): bool
    {
        $districts = $this->district_names ?? [];
        $city = trim($city);
        $state = trim($state);
        foreach ($districts as $d) {
            if (stripos($d, $city) !== false || stripos($city, $d) !== false) return true;
            if (stripos($d, $state) !== false || stripos($state, $d) !== false) return true;
        }
        return false;
    }
}
