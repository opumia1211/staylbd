<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    use GlobalStatus;

    protected $fillable = [
        'act', 'name', 'description', 'image', 'script', 'shortcode', 'support', 'status',
        'version', 'dependency', 'last_updated',
    ];

    protected $casts = [
        'shortcode'   => 'object',
        'dependency'  => 'array',
        'last_updated'=> 'datetime',
    ];

    protected $hidden = ['script', 'shortcode'];

    /** Default image for admin-created extensions (must exist in assets/images/extensions). */
    public const DEFAULT_IMAGE = 'ganalytics.png';
    
    /**
     * Generate script for this extension. Skips Tawk.to on localhost to avoid CORS.
     */
    public function generateScript(): string
    {
        // Tawk.to embed blocks localhost (no CORS header); skip to avoid console error
        if ($this->act === 'tawk-chat') {
            $host = request()->getHttpHost();
            if (app()->environment('local')
                || stripos($host, 'localhost') !== false
                || stripos($host, '127.0.0.1') !== false) {
                return '';
            }
        }

        $script = $this->script ?? '';
        if (!$this->shortcode) {
            return $script;
        }
        foreach ($this->shortcode as $key => $item) {
            $value = is_object($item) ? ($item->value ?? '') : ($item['value'] ?? '');
            $script = str_replace('{{' . $key . '}}', $value, $script);
        }
        return $script;
    }

    /** Category for grouping in admin (Analytics, Chat, Security, Marketing, Custom). */
    public function getCategoryAttribute(): string
    {
        $map = [
            'google-analytics' => 'Analytics',
            'facebook-pixel' => 'Marketing',
            'gtag-manager' => 'Analytics',
            'tawk-chat' => 'Chat',
            'google-recaptcha2' => 'Security',
            'recaptcha3' => 'Security',
            'custom-captcha' => 'Security',
            'custom-code' => 'Custom',
            'cookie-consent' => 'Compliance',
        ];
        return $map[$this->act] ?? 'General';
    }
}
