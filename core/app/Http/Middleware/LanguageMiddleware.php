<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use App\Models\Language;
use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LanguageMiddleware
{
    private const DEFAULT_LANG_CACHE_KEY = 'app.default_lang_code';
    private const DEFAULT_LANG_CACHE_TTL = 60;

    public function handle($request, Closure $next)
    {
        $code = $this->resolveLocale($this->getCode());
        session()->put('lang', $code);
        app()->setLocale($code);
        return $next($request);
    }

    /** Current locale: session first, then cached/default from DB. */
    public function getCode(): string
    {
        if (session()->has('lang')) {
            return (string) session('lang');
        }
        try {
            if (!DB::connection()->getPdo()) {
                return 'en';
            }
            $code = Cache::remember(self::DEFAULT_LANG_CACHE_KEY, self::DEFAULT_LANG_CACHE_TTL, function () {
                $lang = Language::where('is_default', Status::YES)->first();
                return $lang ? $lang->code : 'en';
            });
            return (string) $code;
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                Log::warning('LanguageMiddleware: using fallback locale', ['error' => $e->getMessage()]);
            }
            return 'en';
        }
    }

    private function resolveLocale(string $code): string
    {
        $normalized = $this->normalizeLocaleCode($code);
        if ($this->localeExists($normalized)) {
            return $normalized;
        }
        return 'en';
    }

    private function normalizeLocaleCode(string $code): string
    {
        $code = strtolower(trim($code));
        return match ($code) {
            'hi' => 'hn', // keep legacy DB/code compatibility
            default => $code !== '' ? $code : 'en',
        };
    }

    private function localeExists(string $code): bool
    {
        return File::exists(resource_path("lang/{$code}.json")) || File::isDirectory(resource_path("lang/{$code}"));
    }
}
