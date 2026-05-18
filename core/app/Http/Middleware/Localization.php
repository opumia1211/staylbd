<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class Localization
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
        $allowedLocales = ['en', 'bn', 'hi', 'ar', 'ur', 'ru', 'zh', 'es', 'fr', 'de', 'pt', 'ja'];
        
        // 1. Get locale from route parameter or first segment
        $locale = $request->route('locale') ?: $request->segment(1);
        $sessionLocale = session('locale', 'en');

        // 2. Determine target locale
        if (!in_array($locale, $allowedLocales, true)) {
            $locale = $sessionLocale;
            
            // Redirect to prefixed URL for public GET requests if not already prefixed correctly
            $excludedPrefixes = ['admin', 'user', 'sajaladminopu', 'api', 'assets', 'webhooks', 'serve-js', 'serve-css', 'placeholder-image', 'banner-image', 'row-split-banner'];
            if ($request->method() === 'GET' && !$request->expectsJson() && !in_array($request->segment(1), $excludedPrefixes, true)) {
                $path = trim($request->path(), '/');
                $targetUrl = url($locale . ($path !== '' ? '/' . $path : ''));
                if ($qs = $request->getQueryString()) $targetUrl .= '?' . $qs;
                return redirect()->to($targetUrl);
            }
        }

        // 3. Set application locale and sync session
        App::setLocale($locale);
        session(['locale' => $locale, 'lang' => $locale]);
        
        // 4. Set default route parameter for URL generation
        URL::defaults(['locale' => $locale]);

        // 5. Sync Currency from cookie to session
        $currencyCode = $request->cookie('stayl_display_currency_code') ?: session('stayl_display_currency_code');
        if ($currencyCode) {
            $currencyCode = strtoupper($currencyCode);
            if ($currencyCode !== session('stayl_display_currency_code')) {
                session(['stayl_display_currency_code' => $currencyCode]);
                // Set default symbols (can be improved with a mapper)
                $symbols = ['BDT' => '৳', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'INR' => '₹'];
                session(['stayl_display_currency_symbol' => $symbols[$currencyCode] ?? ($currencyCode . ' ')]);
                
                // Rates should ideally be synced via an AJAX call from the JS side or fetched here
                // For now, we rely on the JS to handle the heavy lifting on client, 
                // but we keep the session code for PHP rendering consistency.
            }
        }

        return $next($request);
    }
}
