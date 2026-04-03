<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderShipmentTracking extends Model
{
    protected $fillable = [
        'order_id', 'status', 'location_name', 'location_address',
        'latitude', 'longitude', 'notes', 'tracking_number', 'courier_name', 'tracking_link',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    const STATUS_PROCESSING = 'processing';
    const STATUS_PICKED = 'picked';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    const STATUS_DELIVERED = 'delivered';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PROCESSING       => __('Processing'),
            self::STATUS_PICKED           => __('Picked'),
            self::STATUS_IN_TRANSIT       => __('In Transit'),
            self::STATUS_OUT_FOR_DELIVERY => __('Out for Delivery'),
            self::STATUS_DELIVERED        => __('Delivered'),
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getMapUrlAttribute(): ?string
    {
        if ($this->latitude && $this->longitude) {
            return 'https://www.google.com/maps?q=' . $this->latitude . ',' . $this->longitude;
        }
        return null;
    }
}
