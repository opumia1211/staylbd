<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    use Searchable;

    protected $fillable = [
        'user_id', 'user_ip', 'city', 'country', 'country_code',
        'latitude', 'longitude', 'browser', 'os',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Display location: City, Country or fallback for local/unknown.
     */
    public function getLocationDisplayAttribute(): string
    {
        $city = trim((string) ($this->attributes['city'] ?? ''));
        $country = trim((string) ($this->attributes['country'] ?? ''));
        if ($city !== '' || $country !== '') {
            $parts = array_filter([$city, $country]);
            return implode(', ', $parts);
        }
        $ip = $this->user_ip ?? '';
        if ($ip === '127.0.0.1' || $ip === '::1' || $ip === '') {
            return __('Local');
        }
        return __('Unknown');
    }
}
