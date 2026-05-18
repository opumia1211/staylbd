<?php

namespace App\Services;

/**
 * RFC 6238 TOTP implementation.
 * Google Authenticator compatible.
 */
class TOTPService
{
    private const PERIOD = 30;
    private const LENGTH = 6;

    /**
     * Generate base32-encoded secret (16 chars = 80 bits).
     */
    public static function generateSecret(): string
    {
        $bytes = random_bytes(10);
        return self::base32Encode($bytes);
    }

    /**
     * Get local time adjusted for clock drift using cached difference against internet time.
     */
    public static function getSyncedTime(): int
    {
        try {
            $drift = \Illuminate\Support\Facades\Cache::get('totp_time_drift');
            if ($drift === null) {
                $drift = 0;
                $ch = curl_init('https://timeapi.io/api/Time/current/zone?timeZone=UTC');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);
                if ($response) {
                    $data = json_decode($response, true);
                    if (isset($data['dateTime'])) {
                        $internetTime = strtotime($data['dateTime'] . ' UTC');
                        if ($internetTime > 0) {
                            $drift = $internetTime - time();
                        }
                    }
                }
                \Illuminate\Support\Facades\Cache::put('totp_time_drift', $drift, now()->addMinutes(10));
            }
            return time() + $drift;
        } catch (\Throwable $e) {
            return time();
        }
    }

    /**
     * Verify using admin config window (clock skew tolerance).
     */
    public static function adminVerify(string $secret, string $code): bool
    {
        $w = max(1, min(4, (int) config('admin.admin_2fa_totp_window', 2)));

        return self::verify($secret, $code, $w);
    }

    /**
     * Verify TOTP code.
     */
    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        if (!ctype_digit($code) || strlen($code) !== self::LENGTH) {
            return false;
        }
        $timeSlice = floor(self::getSyncedTime() / self::PERIOD);
        for ($i = -$window; $i <= $window; $i++) {
            $expected = self::generateCode($secret, $timeSlice + $i);
            if (hash_equals((string) $expected, $code)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate current TOTP code.
     */
    public static function generateCode(string $secret, ?int $timeSlice = null): string
    {
        $timeSlice = $timeSlice ?? floor(self::getSyncedTime() / self::PERIOD);
        $secretKey = self::base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncated = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );
        return str_pad((string) ($truncated % pow(10, self::LENGTH)), self::LENGTH, '0', STR_PAD_LEFT);
    }

    public static function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(str_shuffle(str_repeat($chars, 4)), 0, 8));
        }
        return $codes;
    }

    /**
     * Verify recovery code. Returns [used: bool, updated_codes_json: ?string].
     * If used, updated_codes_json has the remaining codes; caller must persist.
     */
    public static function verifyRecoveryCode(string $stored, string $input): array
    {
        $list = json_decode($stored, true);
        if (!is_array($list)) {
            return [false, null];
        }
        $normalized = strtoupper(preg_replace('/[\s-]+/', '', $input));
        $normalizedList = array_map(fn ($c) => strtoupper(preg_replace('/[\s-]+/', '', $c)), $list);
        $idx = array_search($normalized, $normalizedList, true);
        if ($idx !== false) {
            unset($list[$idx]);
            return [true, json_encode(array_values($list))];
        }
        return [false, null];
    }

    private static function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $result = '';
        $v = 0;
        $vbits = 0;
        for ($i = 0, $len = strlen($data); $i < $len; $i++) {
            $v = ($v << 8) | ord($data[$i]);
            $vbits += 8;
            while ($vbits >= 5) {
                $vbits -= 5;
                $result .= $alphabet[($v >> $vbits) & 31];
            }
        }
        if ($vbits > 0) {
            $result .= $alphabet[($v << (5 - $vbits)) & 31];
        }
        return $result;
    }

    private static function base32Decode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $data = strtoupper(preg_replace('/[^A-Z2-7]/', '', $data));
        $v = 0;
        $vbits = 0;
        $result = '';
        for ($i = 0, $len = strlen($data); $i < $len; $i++) {
            $idx = strpos($alphabet, $data[$i]);
            if ($idx === false) {
                continue;
            }
            $v = ($v << 5) | $idx;
            $vbits += 5;
            if ($vbits >= 8) {
                $vbits -= 8;
                $result .= chr(($v >> $vbits) & 0xFF);
            }
        }
        return $result;
    }
}
