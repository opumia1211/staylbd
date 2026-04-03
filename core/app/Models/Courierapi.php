<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courierapi extends Model
{
    use HasFactory;

    protected $table = 'courierapis';

    protected $fillable = [
        'type', 'name', 'country_code', 'region', 'api_key', 'secret_key', 'url', 'token',
        'config', 'status', 'show_to_user', 'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'show_to_user' => 'boolean',
        'config' => 'array',
        'sort_order' => 'integer',
    ];

    /** Display name (fallback to type) */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: ucfirst($this->type);
    }

    /** Whether this provider is configured enough to use */
    public function isConfigured(): bool
    {
        $url = trim($this->url ?? '');
        if ($url === '') {
            return false;
        }
        if (in_array($this->type ?? '', ['pathao'], true)) {
            return !empty(trim($this->token ?? ''));
        }
        if (in_array($this->type ?? '', ['steadfast', 'sundarban', 'ecourier'], true)) {
            return !empty(trim($this->token ?? '')) || !empty(trim($this->api_key ?? ''));
        }
        return true; // custom / other: URL is enough to try
    }

    /** Scope: active only */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /** Scope: by country */
    public function scopeCountry($query, string $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    /** Scope: show to user at checkout (for future use) */
    public function scopeVisibleToUser($query)
    {
        return $query->where('status', true)->where('show_to_user', true);
    }
}
