<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CodOtpVerification extends Model
{
    protected $fillable = ['mobile', 'otp', 'session_id', 'expires_at', 'verified_at'];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public static function createOtp(string $mobile, int $expireMinutes = 10): ?string
    {
        if (!Schema::hasTable('cod_otp_verifications')) {
            return null;
        }
        $mobile = preg_replace('/\D/', '', $mobile);
        if (strlen($mobile) < 10) {
            return null;
        }
        $otp = (string) random_int(100000, 999999);
        self::where('mobile', $mobile)->whereNull('verified_at')->where('expires_at', '>', now())->delete();
        self::create([
            'mobile' => $mobile,
            'otp' => $otp,
            'session_id' => session()->getId(),
            'expires_at' => now()->addMinutes($expireMinutes),
        ]);
        return $otp;
    }

    public static function verifyOtp(string $mobile, string $otp): bool
    {
        if (!Schema::hasTable('cod_otp_verifications')) {
            return true;
        }
        $mobile = preg_replace('/\D/', '', $mobile);
        $record = self::where('mobile', $mobile)->where('otp', $otp)
            ->whereNull('verified_at')->where('expires_at', '>', now())->first();
        if (!$record) {
            return false;
        }
        $record->verified_at = now();
        $record->save();
        return true;
    }
}
