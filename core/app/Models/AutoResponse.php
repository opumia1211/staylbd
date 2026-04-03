<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoResponse extends Model
{
    protected $fillable = ['name', 'trigger_type', 'keyword', 'keywords', 'message', 'channel', 'config', 'is_active', 'is_public'];

    protected $casts = ['config' => 'object', 'is_active' => 'boolean', 'is_public' => 'boolean', 'keywords' => 'array'];

    public const TRIGGER_KEYWORD = 'keyword';

    public const TRIGGER_WELCOME = 'welcome';

    public const TRIGGER_OFFLINE = 'offline';

    /**
     * Returns list of keywords that trigger this reply (supports multiple; any language).
     * Uses keywords JSON array if set, otherwise falls back to single keyword.
     */
    public function getKeywordsList(): array
    {
        $list = $this->keywords;
        if (is_array($list) && count($list) > 0) {
            return array_values(array_filter(array_map('trim', $list)));
        }
        if (!empty($this->keyword)) {
            return [trim($this->keyword)];
        }
        return [];
    }

    public function scopeKeyword($query)
    {
        return $query->where('trigger_type', self::TRIGGER_KEYWORD);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Only rules that are sent to users when keyword matches (excludes private/draft) */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
