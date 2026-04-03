<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CodBlacklist extends Model
{
    protected $fillable = ['type', 'value', 'reason', 'admin_id'];

    public const TYPE_MOBILE = 'mobile';
    public const TYPE_ADDRESS = 'address';
    public const TYPE_IP = 'ip';

    public static function isBlacklisted(string $type, string $value): bool
    {
        if (!Schema::hasTable('cod_blacklists')) {
            return false;
        }
        $value = trim($value);
        if ($value === '') {
            return false;
        }
        if ($type === self::TYPE_MOBILE) {
            $value = preg_replace('/\D/', '', $value);
        }
        return self::where('type', $type)->where('value', $value)->exists();
    }
}
