<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSavedAddress extends Model
{
    protected $fillable = [
        'user_id', 'country', 'division_id', 'district_id', 'thana_id',
        'postal_code', 'address_line', 'address_line_2', 'state', 'city',
        'device_lat', 'device_lng', 'verified_status', 'is_default', 'label',
    ];

    protected $casts = [
        'device_lat' => 'decimal:7',
        'device_lng' => 'decimal:7',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function thana()
    {
        return $this->belongsTo(Thana::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', 1);
    }
}
