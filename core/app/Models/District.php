<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['division_id', 'name_en', 'name_bn', 'sort_order', 'status'];

    protected $attributes = [
        'status' => 1,
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function thanas()
    {
        return $this->hasMany(Thana::class)->orderBy('sort_order')->orderBy('name_en');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
