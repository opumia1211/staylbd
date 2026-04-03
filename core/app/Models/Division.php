<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name_en', 'name_bn', 'sort_order', 'status'];

    protected $attributes = [
        'status' => 1,
    ];

    public function districts()
    {
        return $this->hasMany(District::class)->orderBy('sort_order')->orderBy('name_en');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
