<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use GlobalStatus, Searchable;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function deposit()
    {
        return $this->hasOne(Deposit::class, 'order_id');
    }

    public function orderDetail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function shipmentTrackings()
    {
        return $this->hasMany(OrderShipmentTracking::class)->orderBy('created_at', 'asc');
    }

    public function scopePending()
    {
        return $this->where('order_status', Status::ORDER_PENDING)->where(function($qOne) {
                    $qOne->where('payment_type', Status::PAYMENT_OFFLINE)->orWhere(function ($qTwo) {
                        $qTwo->where('payment_type', Status::PAYMENT_ONLINE)->where('payment_status', Status::ORDER_PAYMENT_SUCCESS);
                    });
                });
    }

    public function scopeConfirmed()
    {
        return $this->where('order_status', Status::ORDER_CONFIRMED);
    }

    public function scopeProcessing()
    {
        return $this->where('order_status', Status::ORDER_PROCESSING);
    }

    public function scopePackaging()
    {
        return $this->where('order_status', Status::ORDER_PACKAGING);
    }

    public function scopeShipped()
    {
        return $this->where('order_status', Status::ORDER_SHIPPED);
    }

    public function scopeDelivered()
    {
        return $this->where('order_status', Status::ORDER_DELIVERED);
    }

    public function scopeCancel()
    {
        return $this->where('order_status', Status::ORDER_CANCEL);
    }

    public function isCod(): bool
    {
        return (int) $this->payment_type === Status::PAYMENT_OFFLINE;
    }

    /** Whether this order was placed by a guest (no user account). */
    public function isGuest(): bool
    {
        return ($this->user_type ?? 'registered') === 'guest';
    }

    /** Display label for customer (guest name/phone or user). */
    public function getCustomerDisplayAttribute(): string
    {
        if ($this->isGuest()) {
            return $this->guest_name . ' (' . ($this->guest_phone ?? '—') . ')';
        }
        return $this->user ? $this->user->username : '—';
    }

    public function ordersBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';

            if ($this->order_status == Status::ORDER_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->order_status == Status::ORDER_CONFIRMED) {
                $html = '<span class="badge badge--success">' . trans('Confirmed') . '</span>';
            } elseif ($this->order_status == Status::ORDER_PROCESSING) {
                $html = '<span class="badge badge--info">' . trans('Processing') . '</span>';
            } elseif ($this->order_status == Status::ORDER_PACKAGING) {
                $html = '<span class="badge badge--primary">' . trans('Packaging') . '</span>';
            } elseif ($this->order_status == Status::ORDER_DELIVERED) {
                $html = '<span class="badge badge--primary">' . trans('Delivered') . '</span>';
            } elseif ($this->order_status == Status::ORDER_SHIPPED) {
                $html = '<span class="badge badge--dark">' . trans('Shipped') . '</span>';
            } elseif ($this->order_status == Status::ORDER_OUT_FOR_DELIVERY) {
                $html = '<span class="badge badge--info">' . trans('Out for Delivery') . '</span>';
            } elseif ($this->order_status == Status::ORDER_DELIVERY_FAILED) {
                $html = '<span class="badge badge--warning">' . trans('Delivery Failed') . '</span>';
            } elseif ($this->order_status == Status::ORDER_RETURNED) {
                $html = '<span class="badge badge--secondary">' . trans('Returned') . '</span>';
            } elseif ($this->order_status == Status::ORDER_CANCEL) {
                $html = '<span class="badge badge--danger">' . trans('Cancelled') . '</span>';
            } else {
                $html = '<span class="badge badge--danger">' . trans('Rejected') . '</span>';
            }

            return $html;
        });
    }

    public function paymentBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->payment_status == Status::ORDER_PAYMENT_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->payment_status == Status::ORDER_PAYMENT_SUCCESS) {
                $html = '<span class="badge badge--success">' . trans('Success') . '</span>';
            } else {
                $html = '<span class="badge badge--danger">' . trans('Cancel') . '</span>';
            }

            return $html;
        });
    }
}
