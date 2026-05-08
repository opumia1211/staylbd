<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = strtolower(trim((string) session('locale', request()->cookie('stayl_display_language_code') ?: 'en')));
        try {
            $allowedLanguages = cache()->remember('allowed_languages', 3600, function () {
                return \App\Models\Language::pluck('code')->map(function ($code) {
                    return strtolower(trim((string) $code));
                })->filter()->unique()->values()->toArray();
            });
            if (!in_array($locale, $allowedLanguages)) {
                $locale = 'en';
            }
        } catch (\Exception $e) {
            $locale = 'en';
        }
        
        App::setLocale($locale);

        return $next($request);
    }
}
