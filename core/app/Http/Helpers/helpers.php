<?php

use App\Constants\Status;
use App\Lib\GoogleAuthenticator;
use App\Models\Extension;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use Carbon\Carbon;
use App\Lib\Captcha;
use App\Lib\ClientInfo;
use Illuminate\Support\Facades\Auth;

// Include african_pg helpers
use App\Lib\CurlRequest;
use App\Lib\FileManager;
use App\Notify\Notify;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

function systemDetails()
{
    $system['name'] = 'dealshop';
    $system['version'] = '2.0';
    $system['build_version'] = '4.4.3';

    return $system;
}

/**
 * URL for a compiled storefront stylesheet in public/css.
 * Uses asset() so ASSET_URL (CDN) applies when set.
 * Falls back to serve-css/* when config(app.storefront_css_via_serve_route) is true.
 *
 * @param  string  $name  tailwind-homepage|tailwind-product|tailwind-storefront|critical-storefront|tailwind-storefront-deferred|tailwind-storefront-deferred-{home|cart|account|compare}
 */
function storefront_compiled_stylesheet_url(string $name, ?string $version = null): string
{
    $path = 'css/' . $name . '.css';
    $full = public_path($path);
    $v = $version ?? ((is_file($full) ? (string) filemtime($full) : null) ?: (string) (config('app.asset_version') ?? config('app.version', '1')));

    if (config('app.storefront_css_via_serve_route')) {
        $route = match ($name) {
            'tailwind-homepage' => 'serve-css/tailwind-homepage',
            'tailwind-product' => 'serve-css/tailwind-product',
            'tailwind-storefront' => 'serve-css/tailwind-storefront',
            'critical-storefront' => 'serve-css/critical-storefront',
            'tailwind-storefront-deferred' => 'serve-css/tailwind-storefront-deferred',
            'tailwind-storefront-deferred-cart' => 'serve-css/tailwind-storefront-deferred-cart',
            'tailwind-storefront-deferred-account' => 'serve-css/tailwind-storefront-deferred-account',
            'tailwind-storefront-deferred-compare' => 'serve-css/tailwind-storefront-deferred-compare',
            'tailwind-storefront-deferred-home' => 'serve-css/tailwind-storefront-deferred-home',
            default => 'serve-css/tailwind-product',
        };

        return url($route) . '?v=' . rawurlencode($v);
    }

    return asset($path) . '?v=' . rawurlencode($v);
}

function slug($string)
{
    return Illuminate\Support\Str::slug($string);
}

/**
 * Map positional route() args to named params (skips optional {locale?} prefix).
 */
function storefront_normalize_route_parameters(string $name, $parameters): array
{
    if (!is_array($parameters)) {
        $parameters = [$parameters];
    }

    if ($parameters === [] || !array_is_list($parameters)) {
        return $parameters;
    }

    try {
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName($name);
        if ($route === null) {
            return $parameters;
        }

        $names = array_values(array_filter(
            $route->parameterNames(),
            static fn (string $param): bool => $param !== 'locale'
        ));

        if ($names === []) {
            return $parameters;
        }

        $normalized = [];
        foreach ($names as $i => $paramName) {
            if (array_key_exists($i, $parameters)) {
                $normalized[$paramName] = $parameters[$i];
            }
        }

        return $normalized !== [] ? $normalized : $parameters;
    } catch (\Throwable $e) {
        return $parameters;
    }
}

/**
 * Locale-aware storefront route (routes use {locale?}/… prefix).
 */
function storefront_route(string $name, $parameters = [], bool $absolute = true): string
{
    $parameters = storefront_normalize_route_parameters($name, $parameters);

    if (!array_key_exists('locale', $parameters)) {
        $defaults = \Illuminate\Support\Facades\URL::getDefaultParameters();
        if (empty($defaults['locale'])) {
            $parameters['locale'] = session('locale', config('app.locale', 'en'));
        }
    }

    return route($name, $parameters, $absolute);
}

/**
 * Local SVG flag asset URL (assets/global/flags/4x3/{iso}.svg) — no external CDN.
 */
function country_flag_url(string $isoCode): string
{
    $iso = strtolower(preg_replace('/[^a-z]/', '', $isoCode));
    if (strlen($iso) !== 2) {
        return '';
    }

    static $cache = [];
    if (isset($cache[$iso])) {
        return $cache[$iso];
    }

    $relative = 'assets/global/flags/4x3/' . $iso . '.svg';
    $publicFile = public_path($relative);

    if (!is_file($publicFile)) {
        $fallback = dirname(base_path()) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (is_file($fallback)) {
            $dir = dirname($publicFile);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            @copy($fallback, $publicFile);
        }
    }

    if (!is_file($publicFile)) {
        $relative = 'assets/global/flags/4x3/xx.svg';
        $publicFile = public_path($relative);
        if (!is_file($publicFile)) {
            return $cache[$iso] = '';
        }
    }

    return $cache[$iso] = asset($relative);
}

/**
 * Canonical product URL: /{locale}/product/{slug}
 */
function product_detail_url($product): string
{
    if (!is_object($product) || !isset($product->id) || (int) $product->id <= 0) {
        return storefront_route('products');
    }
    $id = (int) $product->id;
    $slug = trim((string) ($product->slug ?? ''), '');
    // Detail URL must end with -{id} matching this product; legacy slugs without suffix 404.
    if ($slug === '' || !preg_match('/-(\d+)$/', $slug, $m) || (int) $m[1] !== $id) {
        $slug = \App\Models\Product::buildShortSlugForProduct($product);
    }

    return storefront_route('product.detail', ['slug' => $slug]);
}

/**
 * Product URL when only id (and optional name) is known — e.g. guest cart rows.
 */
function product_detail_url_for_id(int $productId, ?string $fallbackName = null): string
{
    if ($productId <= 0) {
        return url('/all/products');
    }
    static $memo = [];
    if (!array_key_exists($productId, $memo)) {
        $row = \App\Models\Product::query()->where('id', $productId)->first(['id', 'slug', 'name']);
        $memo[$productId] = $row ? product_detail_url($row) : url('/all/products');
    }

    return $memo[$productId];
}

function verificationCode($length)
{

    if ($length == 0)
        return 0;
    $min = pow(10, $length - 1);
    $max = (int) ($min - 1) . '9';
    return random_int($min, $max);
}

function getNumber($length = 8)
{
    $characters = '1234567890';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function activeTemplate($asset = false)
{
    $template = gs('active_template') ?: 'basic';
    if ($asset)
        return 'assets/templates/' . $template . '/';
    return 'templates.' . $template . '.';
}

function activeTemplateName()
{
    $template = gs('active_template') ?: 'basic';
    return $template;
}

function loadReCaptcha()
{
    return Captcha::reCaptcha();
}

function loadCustomCaptcha($width = '100%', $height = 46, $bgColor = '#003')
{
    return Captcha::customCaptcha($width, $height, $bgColor);
}

function verifyCaptcha()
{
    return Captcha::verify();
}

function loadExtension($key)
{
    $extension = Extension::where('act', $key)->where('status', Status::ENABLE)->first();
    return $extension ? $extension->generateScript() : '';
}

/**
 * Output script for all active extensions (no need to edit code when adding new extensions).
 * Use in layout/plugins so every enabled extension loads automatically.
 */
function loadAllActiveExtensions()
{
    $extensions = Extension::where('status', Status::ENABLE)->orderBy('name')->get();
    $output = '';
    foreach ($extensions as $ext) {
        $script = $ext->generateScript();
        if ($script !== null && trim((string) $script) !== '') {
            $output .= $script . "\n";
        }
    }
    return $output;
}

function getTrx($length = 12)
{
    $characters = 'ABCDEFGHJKMNOPQRSTUVWXYZ123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getAmount($amount, $length = 2)
{
    $amount = round($amount ?? 0, $length);
    return $amount + 0;
}

function showAmount($amount, $decimal = 2, $separate = true, $exceptZeros = false)
{
    $separator = '';
    if ($separate) {
        $separator = ',';
    }
    
    // Multi-currency support: convert amount using session rate
    $rate = (float) (session('stayl_display_currency_rate') ?: 1);
    $amount = $amount * $rate;
    
    $printAmount = number_format($amount, $decimal, '.', $separator);
    if ($exceptZeros) {
        $exp = explode('.', $printAmount);
        if ($exp[1] * 1 == 0) {
            $printAmount = $exp[0];
        } else {
            $printAmount = rtrim($printAmount, '0');
        }
    }
    return $printAmount;
}

/**
 * Returns current display currency symbol (session based).
 */
function currency_symbol()
{
    $general = gs();
    return session('stayl_display_currency_symbol') ?: $general->cur_sym ?: '৳';
}

/**
 * Returns current display currency code (session based).
 */
function currency_text()
{
    $general = gs();
    return session('stayl_display_currency_code') ?: $general->cur_text ?: 'BDT';
}

/**
 * Returns current display currency conversion rate (session based).
 */
function currency_rate()
{
    return (float) (session('stayl_display_currency_rate') ?: 1);
}

/**
 * Format number for compact display (e.g. 1200 -> 1.2k, 1500000 -> 1.5M).
 */
function shortNumber($num)
{
    $num = (int) $num;
    if ($num >= 1000000) {
        return round($num / 1000000, 1) . 'M';
    }
    if ($num >= 1000) {
        return round($num / 1000, 1) . 'k';
    }
    return (string) $num;
}

function removeElement($array, $value)
{
    return array_diff($array, (is_array($value) ? $value : array($value)));
}

function cryptoQR($wallet)
{
    return "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$wallet&choe=UTF-8";
}

function keyToTitle($text)
{
    return ucfirst(preg_replace("/[^A-Za-z0-9 ]/", ' ', $text));
}

/**
 * Sanitize policy/details HTML for safe output (XSS prevention).
 * Allows common formatting tags; strips script, iframe, form, object, etc.
 */
function safe_policy_html($html)
{
    if (empty($html) || !is_string($html)) {
        return '';
    }
    $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><span><div><table><thead><tbody><tr><th><td><hr><sub><sup>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace_callback('/<a\s+([^>]*)\s*>/i', function ($m) {
        $attrs = $m[1];
        if (preg_match('/href\s*=\s*["\']?\s*(javascript|data|vbscript):/i', $attrs)) {
            return '<a href="#" rel="noopener">';
        }
        if (strpos($attrs, 'rel=') === false) {
            $attrs .= ' rel="noopener noreferrer"';
        }
        return '<a ' . trim($attrs) . '>';
    }, $html);
    // Plain text (e.g. from policy textarea): newlines to <br> for display
    if (strpos($html, '<') === false) {
        $html = nl2br(e($html));
    }
    return $html;
}

function titleToKey($text)
{
    return strtolower(str_replace(' ', '_', $text));
}

function strLimit($title = null, $length = 10)
{
    return Str::limit($title, $length);
}

function getIpInfo()
{
    $ipInfo = ClientInfo::ipInfo();
    return $ipInfo;
}

function osBrowser()
{
    $osBrowser = ClientInfo::osBrowser();
    return $osBrowser;
}

function getTemplates()
{
    // License check removed - returning empty array to prevent external calls
    return [];
}

function getPageSections($arr = false)
{
    $jsonUrl = resource_path('views/') . str_replace('.', '/', activeTemplate()) . 'sections.json';
    if (!file_exists($jsonUrl) || !is_readable($jsonUrl)) {
        return $arr ? [] : (object) [];
    }
    $content = @file_get_contents($jsonUrl);
    if ($content === false) {
        return $arr ? [] : (object) [];
    }
    $sections = $arr ? json_decode($content, true) : json_decode($content);
    if ($sections === null && json_last_error() !== JSON_ERROR_NONE) {
        return $arr ? [] : (object) [];
    }
    if ($arr && is_array($sections)) {
        ksort($sections);
    }
    return $sections ?? ($arr ? [] : (object) []);
}

/**
 * Robust logo/favicon retrieval for XAMPP subdirectory and production.
 * Uses url() instead of asset() to ensure resolution relative to APP_URL (project root),
 * bypassing potential ASSET_URL (core/public) mismatches.
 *
 * @param string $type logo|logo_dark|favicon|invoice_logo|invoice_signature
 * @param string|null $size minimum|standard|maximum (optional)
 * @param bool $audit If true, returns [url, exists, path, mtime]
 * @return string|array|null
 */
function getLogo($type = 'logo', $size = null, $audit = false)
{
    try {
        $logoPath = getLogoIconPath();
        $filename = null;

        // 1. Try specific size first
        if ($size) {
            $sizeMap = [
                'minimum' => 'logo_minimum',
                'standard' => 'logo_standard',
                'maximum' => 'logo_maximum',
            ];
            if (isset($sizeMap[$size])) {
                $filename = gs($sizeMap[$size]);
            }
        }

        // 2. Fallback to requested type
        if (!$filename) {
            $filename = gs($type);
        }

        // 3. Fallback for 'logo' type to standard sizes if main logo is missing
        if (!$filename && $type === 'logo') {
            foreach (['logo_standard', 'logo_maximum', 'logo_minimum'] as $fallback) {
                $filename = gs($fallback);
                if ($filename && file_exists($logoPath . '/' . $filename)) break;
                $filename = null;
            }
        }

        if ($filename) {
            $fullPath = $logoPath . '/' . $filename;
            $exists = file_exists($fullPath);
            // Use url() to ensure it points to {APP_URL}/assets/...
            $url = url('assets/images/logoIcon/' . $filename);
            
            if ($exists) {
                $url .= '?v=' . filemtime($fullPath);
            }

            if ($audit) {
                return [
                    'url' => $url,
                    'exists' => $exists,
                    'path' => $fullPath,
                    'filename' => $filename,
                    'mtime' => $exists ? filemtime($fullPath) : null,
                    'size' => $exists ? filesize($fullPath) : 0,
                ];
            }

            return $exists ? $url : null;
        }
    } catch (\Exception $e) {
        if ($audit) return ['error' => $e->getMessage(), 'exists' => false];
    }
    return $audit ? ['exists' => false, 'url' => null] : null;
}

/**
 * Technical audit of all site logos and icons.
 */
function getLogoAudit()
{
    $types = ['logo', 'logo_dark', 'favicon', 'invoice_logo', 'invoice_signature'];
    $results = [];
    foreach ($types as $type) {
        $results[$type] = getLogo($type, null, true);
    }
    
    // Check directory permissions
    $path = getLogoIconPath();
    $results['system'] = [
        'path' => $path,
        'is_writable' => is_writable($path),
        'is_dir' => is_dir($path),
        'asset_url_config' => config('app.asset_url'),
        'app_url_config' => config('app.url'),
    ];
    
    return $results;
}

function getThemeLogo($isDark = false, $size = null)
{
    try {
        if ($isDark) {
            $darkLogo = getLogo('logo_dark', $size);
            return $darkLogo ?: getLogo('logo', $size);
        }
        $lightLogo = getLogo('logo', $size);
        return $lightLogo ?: getLogo('logo_dark', $size);
    } catch (\Exception $e) {
        return null;
    }
}

function getResponsiveLogo($preferredSize = 'standard')
{
    try {
        // Try preferred size first
        $logo = getLogo('logo', $preferredSize);
        if ($logo) {
            return $logo;
        }

        // Fallback order: standard -> maximum -> minimum -> legacy
        $fallbackOrder = ['standard', 'maximum', 'minimum'];
        foreach ($fallbackOrder as $size) {
            if ($size !== $preferredSize) {
                $logo = getLogo('logo', $size);
                if ($logo) {
                    return $logo;
                }
            }
        }

        // Last resort: legacy logo
        return getLogo('logo');
    } catch (\Exception $e) {
        return null;
    }
}

function getLogoEffectClasses()
{
    try {
        $general = gs();
        $classes = [];
        if (isset($general->logo_effects_enabled) && $general->logo_effects_enabled) {
            if (!empty($general->logo_hover_effect) && $general->logo_hover_effect !== 'none') {
                $classes[] = 'logo-hover-' . $general->logo_hover_effect;
            }
            if (!empty($general->logo_animation) && $general->logo_animation !== 'none') {
                $classes[] = 'logo-animate-' . $general->logo_animation;
            }
            if (!empty($general->logo_animation_speed) && $general->logo_animation_speed !== 'normal') {
                $classes[] = 'logo-speed-' . $general->logo_animation_speed;
            }
        }
        return implode(' ', $classes);
    } catch (\Exception $e) {
        return '';
    }
}

function getLogoStyle()
{
    try {
        $general = gs();
        $styles = [];

        if (isset($general->logo_opacity) && $general->logo_opacity && (float) $general->logo_opacity != 1.0) {
            $styles[] = 'opacity: ' . (float) $general->logo_opacity;
        }

        return implode('; ', $styles);
    } catch (\Exception $e) {
    }
    return '';
}

function getLogoMaxWidth()
{
    try {
        $general = gs();
        return isset($general->logo_max_width) ? (int) $general->logo_max_width : 200;
    } catch (\Exception $e) {
        return 200;
    }
}

function getLogoMaxHeight()
{
    try {
        $general = gs();
        return isset($general->logo_max_height) ? (int) $general->logo_max_height : 60;
    } catch (\Exception $e) {
        return 60;
    }
}

function getFooterLogoHeight()
{
    try {
        $general = gs();
        return isset($general->footer_logo_height) ? (int) $general->footer_logo_height : 35;
    } catch (\Exception $e) {
        return 35;
    }
}

function getImage($image, $size = null)
{
    $clean = '';
    if (!is_string($image) || $image === '') {
        if ($size) {
            return storefront_route('placeholder.image', ['size' => $size]);
        }
        return asset('assets/images/default.png');
    }

    // Reject absolute Windows/unix paths; use filename only so path is relative to public
    if (preg_match('#[\\\\:]|^/#', $image)) {
        $dir = trim(str_replace('\\', '/', dirname($image)), '/');
        $base = basename(str_replace('\\', '/', $image));
        if (preg_match('#^[a-zA-Z0-9_./-]+$#', $dir) && $base !== '' && $base !== '.') {
            $image = $dir . '/' . $base;
        } else {
            $image = $base;
        }
    }

    $imagePath = ltrim(str_replace('\\', '/', trim($image)), '/');

    $candidates = [];
    if (function_exists('public_path')) {
        $candidates[] = public_path($imagePath);
    }
    $projectRoot = dirname(base_path());
    $candidates[] = rtrim($projectRoot, '/\\') . '/' . $imagePath;

    foreach ($candidates as $absPath) {
        if (file_exists($absPath) && is_file($absPath)) {
            return asset($imagePath) . $clean;
        }
    }

    if ($size) {
        return storefront_route('placeholder.image', ['size' => $size]);
    }
    return asset('assets/images/default.png');
}

/**
 * Return WebP URL if a .webp version of the file exists (same path with .webp), else return getImage().
 * Use in <picture><source type="image/webp" srcset="{{ getImageWebP(...) }}"> for optimized delivery.
 */
function getImageWebP($image, $size = null)
{
    if (!is_string($image) || $image === '') {
        return getImage($image, $size);
    }
    // Normalize: strip absolute paths so we only use path relative to public
    if (preg_match('#[\\\\:]|^/#', $image)) {
        $dir = trim(str_replace('\\', '/', dirname($image)), '/');
        $base = basename(str_replace('\\', '/', $image));
        if (preg_match('#^[a-zA-Z0-9_./-]+$#', $dir) && $base !== '' && $base !== '.') {
            $image = $dir . '/' . $base;
        } else {
            $image = $base;
        }
    }
    $path = pathinfo($image, PATHINFO_DIRNAME);
    $filename = pathinfo($image, PATHINFO_FILENAME);
    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    if ($ext === 'webp') {
        return getImage($image, $size);
    }
    $relativeDir = ltrim(str_replace('\\', '/', $path), '/');
    $webpRelative = ($relativeDir ? $relativeDir . '/' : '') . $filename . '.webp';

    $candidates = [];
    if (function_exists('public_path')) {
        $candidates[] = public_path($webpRelative);
    }
    $projectRoot = dirname(base_path());
    $candidates[] = rtrim($projectRoot, '/\\') . '/' . $webpRelative;

    foreach ($candidates as $abs) {
        if (file_exists($abs) && is_file($abs)) {
            return asset($webpRelative);
        }
    }

    return getImage($image, $size);
}

/**
 * Return tiny Base64 data-URL for an image (LQIP). Best for style="background-image: url(...)".
 * Uses caching to avoid repeated Intervention Image processing.
 */
function getImageLQIP($image)
{
    if (!is_string($image) || $image === '') {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
    }

    $cacheKey = 'lqip:' . md5($image);
    return Cache::remember($cacheKey, 86400, function () use ($image) {
        // Resolve absolute path
        $imagePath = ltrim(str_replace('\\', '/', trim($image)), '/');
        $fullPath = public_path($imagePath);
        if (!file_exists($fullPath)) {
            $projectRoot = dirname(base_path());
            $fullPath = rtrim($projectRoot, '/\\') . '/' . $imagePath;
        }

    return app(\App\Services\ImageOptimizationService::class)->generateLQIP($fullPath);
    });
}

/**
 * Return srcset string for responsive images (WebP).
 * Generates thumbnail, medium, and large versions if not present.
 */
function getImageSrcset($image, $pathKey)
{
    if (!is_string($image) || $image === '') {
        return '';
    }

    $cacheKey = 'srcset:' . md5($image . $pathKey);
    return Cache::remember($cacheKey, 86400, function () use ($image, $pathKey) {
        $imagePath = getFilePath($pathKey) . '/' . $image;
        $fullPath = public_path($imagePath);
        if (!file_exists($fullPath)) {
            $projectRoot = dirname(base_path());
            $fullPath = rtrim($projectRoot, '/\\') . '/' . $imagePath;
        }

        if (!file_exists($fullPath)) {
            return '';
        }

        $service = app(\App\Services\ImageOptimizationService::class);
        $sizes = $service->createResponsiveSizes($fullPath);
        
        $srcset = [];
        $publicBase = asset('/');
        foreach ($sizes as $name => $path) {
            $width = match($name) {
                'thumbnail' => '150w',
                'medium' => '500w',
                'large' => '1000w',
                default => '1000w'
            };
            // Convert absolute path back to relative URL
            $rel = str_replace([public_path(), str_replace('\\', '/', public_path())], '', str_replace('\\', '/', $path));
            $rel = ltrim($rel, '/');
            $srcset[] = asset($rel) . ' ' . $width;
        }
        
        return implode(', ', $srcset);
    });
}



/** Return asset URL for official platform logo (Android, iOS, Windows, Mac, Desktop) if file exists; else null. Place files in public: assets/images/frontend/footer/platforms/android.png, ios.png, windows.png, mac.png, desktop.png */
function getPlatformLogoUrl($platform)
{
    $key = strtolower(trim((string) $platform));
    if ($key === '') {
        return null;
    }
    $fileNames = [];
    if (str_contains($key, 'android')) {
        $fileNames[] = 'android';
    }
    if (in_array($key, ['ios', 'apple']) || str_contains($key, 'ios') || str_contains($key, 'apple')) {
        $fileNames[] = 'ios';
    }
    if (str_contains($key, 'mac')) {
        $fileNames[] = 'mac';
    }
    if (str_contains($key, 'windows')) {
        $fileNames[] = 'windows';
    }
    if (str_contains($key, 'desktop')) {
        $fileNames[] = 'desktop';
    }
    $relativePath = 'assets/images/frontend/footer/platforms/';
    $baseDirs = [public_path($relativePath), base_path('../' . $relativePath)];
    foreach (['.png', '.svg', '.webp'] as $ext) {
        foreach ($fileNames as $name) {
            $file = $name . $ext;
            foreach ($baseDirs as $baseDir) {
                $path = rtrim($baseDir, '/\\') . '/' . $file;
                if (file_exists($path) && is_file($path)) {
                    return asset($relativePath . $file);
                }
            }
        }
    }
    return null;
}

function notify($user, $templateName, $shortCodes = null, $sendVia = null, $createLog = true)
{
    $general = gs();
    $globalShortCodes = [
        'site_name' => $general->site_name,
        'site_currency' => $general->cur_text,
        'currency_symbol' => $general->cur_sym,
    ];

    if (gettype($user) == 'array') {
        $user = (object) $user;
    }

    $shortCodes = array_merge($shortCodes ?? [], $globalShortCodes);

    $notify = new Notify($sendVia);
    $notify->templateName = $templateName;
    $notify->shortCodes = $shortCodes;
    $notify->user = $user;
    $notify->createLog = $createLog;
    $notify->userColumn = isset($user->id) ? $user->getForeignKey() : 'user_id';
    $notify->send();
}

function getPaginate($paginate = 20)
{
    return $paginate;
}

function paginateLinks($data)
{
    return $data->appends(request()->all())->links();
}

function menuActive($routeName, $type = null, $param = null)
{
    if ($type == 3)
        $class = 'side-menu--open';
    elseif ($type == 2)
        $class = 'sidebar-submenu__open';
    else
        $class = 'active';

    if (is_array($routeName)) {
        foreach ($routeName as $key => $value) {
            if (request()->routeIs($value))
                return $class;
        }
    } elseif (request()->routeIs($routeName)) {
        if ($param) {
            $routeParam = array_values(@request()->route()->parameters ?? []);
            if (strtolower(@$routeParam[0]) == strtolower($param))
                return $class;
            else
                return;
        }
        return $class;
    }
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null)
{
    $fileManager = new FileManager($file);
    $pathNormalized = ltrim(str_replace('\\', '/', trim((string) $location)), '/');
    $primaryRoot = function_exists('public_path') ? public_path($pathNormalized) : $pathNormalized;
    $projectRoot = dirname(base_path());
    $legacyRoot = rtrim($projectRoot, '/\\') . '/' . $pathNormalized;

    $fileManager->path = $primaryRoot;
    $fileManager->size = $size;
    $fileManager->old = $old;
    $fileManager->thumb = $thumb;
    $fileManager->upload();

    $savedName = $fileManager->filename;
    $primaryFile = rtrim($primaryRoot, '/\\') . '/' . $savedName;
    $legacyDir = rtrim($legacyRoot, '/\\');
    if (is_file($primaryFile) && !is_dir($legacyDir)) {
        mkdir($legacyDir, 0755, true);
    }
    if (is_file($primaryFile) && is_dir($legacyDir)) {
        $legacyFile = $legacyDir . '/' . $savedName;
        if (!file_exists($legacyFile)) {
            copy($primaryFile, $legacyFile);
        }
    }

    return $fileManager->filename;
}

/**
 * Run high-quality image optimization + WebP on any uploaded image (logo, banner, product, etc.).
 * Call with path relative to public or base (e.g. assets/images/product/xyz.jpg). No-op for SVG/non-raster.
 */
function optimizeUploadedImage($pathRelativeOrFull)
{
    $path = str_replace('\\', '/', trim($pathRelativeOrFull));
    if ($path === '') {
        return;
    }
    $full = null;
    if (file_exists($path) && is_file($path)) {
        $full = realpath($path);
    }
    if (!$full && function_exists('public_path')) {
        $try = public_path($path);
        if (file_exists($try) && is_file($try)) {
            $full = $try;
        }
    }
    if (!$full && function_exists('base_path')) {
        $try = base_path($path);
        if (file_exists($try) && is_file($try)) {
            $full = $try;
        }
    }
    if (!$full) {
        return;
    }
    try {
        $service = app(\App\Services\ImageOptimizationService::class);
        $service->optimizeProductImage($full, $service::QUALITY_HIGH);
    } catch (\Throwable $e) {
        // best-effort; do not break upload
    }
}

function fileManager()
{
    return new FileManager();
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

/**
 * Full absolute path for logo/favicon upload (project assets folder)
 */
function getLogoIconPath()
{
    $path = base_path('../assets/images/logoIcon');
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
    return $path;
}

/**
 * Get country ISO code from country name (uses views/partials/country.json).
 */
function getCountryIsoByName($countryName)
{
    $path = resource_path('views/partials/country.json');
    if (!is_file($path)) {
        return $countryName;
    }
    $data = (array) json_decode(file_get_contents($path));
    foreach ($data as $iso => $obj) {
        if (isset($obj->country) && strcasecmp(trim($obj->country), trim($countryName)) === 0) {
            return $iso;
        }
    }
    return $countryName;
}

/**
 * District label for checkout (admin-configured: English + Bengali).
 * Used when site is used in another country so label can be changed from admin.
 */
function getDistrictLabels(): object
{
    $row = \App\Models\Frontend::where('data_keys', 'district.settings')->first();
    $v = $row && isset($row->data_values) ? (object) $row->data_values : (object) [];
    return (object) [
        'label_en' => $v->label_en ?? 'District',
        'label_bn' => $v->label_bn ?? 'জেলা',
        'help_en' => $v->help_en ?? 'All Bangladesh districts — select for delivery charge',
        'help_bn' => $v->help_bn ?? 'বাংলাদেশের সব জেলা — ডেলিভারি চার্জের জন্য নির্বাচন করুন',
    ];
}

/**
 * Default 64 Bangladesh districts: English => Bengali (অটোমেটিক যুক্ত).
 */
function getDefaultDistrictsEnBn(): array
{
    return [
        ['en' => 'Bagerhat', 'bn' => 'বাগেরহাট'],
        ['en' => 'Bandarban', 'bn' => 'বান্দরবান'],
        ['en' => 'Barguna', 'bn' => 'বরগুনা'],
        ['en' => 'Barisal', 'bn' => 'বরিশাল'],
        ['en' => 'Bhola', 'bn' => 'ভোলা'],
        ['en' => 'Bogra', 'bn' => 'বগুড়া'],
        ['en' => 'Brahmanbaria', 'bn' => 'ব্রাহ্মণবাড়িয়া'],
        ['en' => 'Chandpur', 'bn' => 'চাঁদপুর'],
        ['en' => 'Chapainawabganj', 'bn' => 'চাঁপাইনবাবগঞ্জ'],
        ['en' => 'Chittagong', 'bn' => 'চট্টগ্রাম'],
        ['en' => 'Chuadanga', 'bn' => 'চুয়াডাঙ্গা'],
        ['en' => 'Comilla', 'bn' => 'কুমিল্লা'],
        ['en' => 'Cox\'s Bazar', 'bn' => 'কক্সবাজার'],
        ['en' => 'Dhaka', 'bn' => 'ঢাকা'],
        ['en' => 'Dinajpur', 'bn' => 'দিনাজপুর'],
        ['en' => 'Faridpur', 'bn' => 'ফরিদপুর'],
        ['en' => 'Feni', 'bn' => 'ফেনী'],
        ['en' => 'Gaibandha', 'bn' => 'গাইবান্ধা'],
        ['en' => 'Gazipur', 'bn' => 'গাজীপুর'],
        ['en' => 'Gopalganj', 'bn' => 'গোপালগঞ্জ'],
        ['en' => 'Habiganj', 'bn' => 'হবিগঞ্জ'],
        ['en' => 'Jamalpur', 'bn' => 'জামালপুর'],
        ['en' => 'Jessore', 'bn' => 'যশোর'],
        ['en' => 'Jhalokati', 'bn' => 'ঝালকাঠি'],
        ['en' => 'Jhenaidah', 'bn' => 'ঝিনাইদহ'],
        ['en' => 'Joypurhat', 'bn' => 'জয়পুরহাট'],
        ['en' => 'Khagrachhari', 'bn' => 'খাগড়াছড়ি'],
        ['en' => 'Khulna', 'bn' => 'খুলনা'],
        ['en' => 'Kishoreganj', 'bn' => 'কিশোরগঞ্জ'],
        ['en' => 'Kurigram', 'bn' => 'কুড়িগ্রাম'],
        ['en' => 'Kushtia', 'bn' => 'কুষ্টিয়া'],
        ['en' => 'Lakshmipur', 'bn' => 'লক্ষ্মীপুর'],
        ['en' => 'Lalmonirhat', 'bn' => 'লালমনিরহাট'],
        ['en' => 'Madaripur', 'bn' => 'মাদারীপুর'],
        ['en' => 'Magura', 'bn' => 'মাগুরা'],
        ['en' => 'Manikganj', 'bn' => 'মানিকগঞ্জ'],
        ['en' => 'Meherpur', 'bn' => 'মেহেরপুর'],
        ['en' => 'Moulvibazar', 'bn' => 'মৌলভীবাজার'],
        ['en' => 'Munshiganj', 'bn' => 'মুন্সিগঞ্জ'],
        ['en' => 'Mymensingh', 'bn' => 'ময়মনসিংহ'],
        ['en' => 'Naogaon', 'bn' => 'নওগাঁ'],
        ['en' => 'Narail', 'bn' => 'নড়াইল'],
        ['en' => 'Narayanganj', 'bn' => 'নারায়ণগঞ্জ'],
        ['en' => 'Narsingdi', 'bn' => 'নরসিংদী'],
        ['en' => 'Natore', 'bn' => 'নাটোর'],
        ['en' => 'Netrokona', 'bn' => 'নেত্রকোণা'],
        ['en' => 'Nilphamari', 'bn' => 'নীলফামারী'],
        ['en' => 'Noakhali', 'bn' => 'নোয়াখালী'],
        ['en' => 'Pabna', 'bn' => 'পাবনা'],
        ['en' => 'Panchagarh', 'bn' => 'পঞ্চগড়'],
        ['en' => 'Patuakhali', 'bn' => 'পটুয়াখালী'],
        ['en' => 'Pirojpur', 'bn' => 'পিরোজপুর'],
        ['en' => 'Rajbari', 'bn' => 'রাজবাড়ী'],
        ['en' => 'Rajshahi', 'bn' => 'রাজশাহী'],
        ['en' => 'Rangamati', 'bn' => 'রাঙ্গামাটি'],
        ['en' => 'Rangpur', 'bn' => 'রংপুর'],
        ['en' => 'Satkhira', 'bn' => 'সাতক্ষীরা'],
        ['en' => 'Shariatpur', 'bn' => 'শরীয়তপুর'],
        ['en' => 'Sherpur', 'bn' => 'শেরপুর'],
        ['en' => 'Sirajganj', 'bn' => 'সিরাজগঞ্জ'],
        ['en' => 'Sunamganj', 'bn' => 'সুনামগঞ্জ'],
        ['en' => 'Sylhet', 'bn' => 'সিলেট'],
        ['en' => 'Tangail', 'bn' => 'টাঙ্গাইল'],
        ['en' => 'Thakurgaon', 'bn' => 'ঠাকুরগাঁও'],
    ];
}

/**
 * Bangladesh 8 divisions for checkout. From DB (divisions table) if available, else default list.
 * Returns array of ['id' => int, 'name_en' => string, 'name_bn' => string].
 */
function getDivisionList(): array
{
    if (\Illuminate\Support\Facades\Schema::hasTable('divisions')) {
        $query = \App\Models\Division::orderBy('sort_order')->orderBy('name_en');
        if (\Illuminate\Support\Facades\Schema::hasColumn('divisions', 'status')) {
            $query->where('status', 1);
        }
        $rows = $query->get();
        if ($rows->isNotEmpty()) {
            return $rows->map(fn($d) => ['id' => $d->id, 'name_en' => $d->name_en, 'name_bn' => $d->name_bn ?? ''])->toArray();
        }
    }
    return [
        ['id' => 1, 'name_en' => 'Dhaka', 'name_bn' => 'ঢাকা'],
        ['id' => 2, 'name_en' => 'Chittagong', 'name_bn' => 'চট্টগ্রাম'],
        ['id' => 3, 'name_en' => 'Rajshahi', 'name_bn' => 'রাজশাহী'],
        ['id' => 4, 'name_en' => 'Khulna', 'name_bn' => 'খুলনা'],
        ['id' => 5, 'name_en' => 'Barisal', 'name_bn' => 'বরিশাল'],
        ['id' => 6, 'name_en' => 'Sylhet', 'name_bn' => 'সিলেট'],
        ['id' => 7, 'name_en' => 'Rangpur', 'name_bn' => 'রংপুর'],
        ['id' => 8, 'name_en' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ'],
    ];
}

/**
 * Districts grouped by division_id for checkout. From DB or built from getDistrictList().
 * Returns array keyed by division id: [ division_id => [ ['en'=>'','bn'=>''], ... ], ... ]
 */
function getDistrictsByDivision(): array
{
    if (\Illuminate\Support\Facades\Schema::hasTable('divisions') && \Illuminate\Support\Facades\Schema::hasTable('districts')) {
        $query = \App\Models\District::orderBy('sort_order')->orderBy('name_en');
        if (\Illuminate\Support\Facades\Schema::hasColumn('districts', 'status')) {
            $query->where('status', 1);
        }
        $rows = $query->get();
        $out = [];
        foreach ($rows as $d) {
            $out[$d->division_id][] = ['id' => $d->id, 'en' => $d->name_en, 'bn' => $d->name_bn ?? ''];
        }
        if (!empty($out)) {
            return $out;
        }
    }
    $all = getDistrictList();
    $divisionList = getDivisionList();
    $byName = [
        'Dhaka' => 1,
        'Chittagong' => 2,
        'Rajshahi' => 3,
        'Khulna' => 4,
        'Barisal' => 5,
        'Sylhet' => 6,
        'Rangpur' => 7,
        'Mymensingh' => 8,
    ];
    $out = [];
    foreach (array_column($divisionList, 'id') as $id) {
        $out[$id] = [];
    }
    $districtToDivision = getDefaultDistrictToDivisionMap();
    foreach ($all as $d) {
        $en = is_array($d) ? ($d['en'] ?? '') : '';
        $divId = $districtToDivision[$en] ?? 1;
        if (!isset($out[$divId])) {
            $out[$divId] = [];
        }
        $out[$divId][] = ['en' => $en, 'bn' => is_array($d) ? ($d['bn'] ?? '') : ''];
    }
    return $out;
}

/** Default mapping: district name_en => division id (1-8) for Bangladesh. */
function getDefaultDistrictToDivisionMap(): array
{
    return [
        'Bagerhat' => 4,
        'Bandarban' => 2,
        'Barguna' => 5,
        'Barisal' => 5,
        'Bhola' => 5,
        'Bogra' => 3,
        'Brahmanbaria' => 1,
        'Chandpur' => 1,
        'Chapainawabganj' => 3,
        'Chittagong' => 2,
        'Chuadanga' => 4,
        'Comilla' => 1,
        'Cox\'s Bazar' => 2,
        'Dhaka' => 1,
        'Dinajpur' => 7,
        'Faridpur' => 1,
        'Feni' => 2,
        'Gaibandha' => 7,
        'Gazipur' => 1,
        'Gopalganj' => 1,
        'Habiganj' => 6,
        'Jamalpur' => 8,
        'Jessore' => 4,
        'Jhalokati' => 5,
        'Jhenaidah' => 4,
        'Joypurhat' => 3,
        'Khagrachhari' => 2,
        'Khulna' => 4,
        'Kishoreganj' => 1,
        'Kurigram' => 7,
        'Kushtia' => 4,
        'Lakshmipur' => 1,
        'Lalmonirhat' => 7,
        'Madaripur' => 1,
        'Magura' => 4,
        'Manikganj' => 1,
        'Meherpur' => 4,
        'Moulvibazar' => 6,
        'Munshiganj' => 1,
        'Mymensingh' => 8,
        'Naogaon' => 3,
        'Narail' => 4,
        'Narayanganj' => 1,
        'Narsingdi' => 1,
        'Natore' => 3,
        'Netrokona' => 8,
        'Nilphamari' => 7,
        'Noakhali' => 1,
        'Pabna' => 3,
        'Panchagarh' => 7,
        'Patuakhali' => 5,
        'Pirojpur' => 5,
        'Rajbari' => 1,
        'Rajshahi' => 3,
        'Rangamati' => 2,
        'Rangpur' => 7,
        'Satkhira' => 4,
        'Shariatpur' => 1,
        'Sherpur' => 8,
        'Sirajganj' => 3,
        'Sunamganj' => 6,
        'Sylhet' => 6,
        'Tangail' => 1,
        'Thakurgaon' => 7,
    ];
}

/**
 * Bangladesh 64 districts for checkout. From DB (districts table) if available, else Frontend/list fallback.
 * Returns array of [en, bn].
 */
function getDistrictList(): array
{
    if (\Illuminate\Support\Facades\Schema::hasTable('districts')) {
        $query = \App\Models\District::orderBy('division_id')->orderBy('sort_order')->orderBy('name_en');
        if (\Illuminate\Support\Facades\Schema::hasColumn('districts', 'status')) {
            $query->where('status', 1);
        }
        $rows = $query->get();
        if ($rows->isNotEmpty()) {
            return $rows->map(fn($d) => ['en' => $d->name_en, 'bn' => $d->name_bn])->toArray();
        }
    }
    $row = \App\Models\Frontend::where('data_keys', 'district.list')->first();
    if ($row && !empty($row->data_values)) {
        $raw = $row->data_values;
        $list = is_array($raw) ? $raw : [];
        $out = [];
        foreach ($list as $item) {
            $arr = is_array($item) ? $item : (array) $item;
            $out[] = ['en' => $arr['en'] ?? '', 'bn' => $arr['bn'] ?? ''];
        }
        if (!empty($out)) {
            return $out;
        }
    }
    return getDefaultDistrictsEnBn();
}

/**
 * Bangladesh 64 districts – English only (for backward compatibility / zone matching).
 */
function getBangladeshDistricts(): array
{
    return array_column(getDistrictList(), 'en');
}

/**
 * Default thanas/upazilas by district (EN + BN). Loaded from bangladesh_thanas.php.
 */
function getDefaultThanasByDistrict(): array
{
    $path = __DIR__ . '/bangladesh_thanas.php';
    if (!is_file($path)) {
        return [];
    }
    $data = require $path;
    return is_array($data) ? $data : [];
}

/**
 * Thana list for one district (admin-editable). Returns array of ['en'=>'...','bn'=>'...'].
 */
function getThanaList(string $districtEn): array
{
    $byDistrict = getThanaListByDistrict();
    $list = $byDistrict[$districtEn] ?? [];
    $out = [];
    foreach ($list as $item) {
        $arr = is_array($item) ? $item : (array) $item;
        $out[] = ['en' => $arr['en'] ?? '', 'bn' => $arr['bn'] ?? ''];
    }
    return $out;
}

/**
 * All thanas by district (for admin & checkout). From DB (thanas table) if available.
 * Keyed by district name_en; value = array of [en, bn].
 */
function getThanaListByDistrict(): array
{
    if (\Illuminate\Support\Facades\Schema::hasTable('districts') && \Illuminate\Support\Facades\Schema::hasTable('thanas')) {
        $districtQuery = \App\Models\District::with([
            'thanas' => function ($q) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('thanas', 'status')) {
                    $q->where('status', 1);
                }
            }
        ])->orderBy('division_id')->orderBy('sort_order')->orderBy('name_en');
        if (\Illuminate\Support\Facades\Schema::hasColumn('districts', 'status')) {
            $districtQuery->where('status', 1);
        }
        $districts = $districtQuery->get();
        $byDistrict = [];
        foreach ($districts as $d) {
            $byDistrict[$d->name_en] = $d->thanas->map(fn($t) => [
                'en' => $t->name_en,
                'bn' => $t->name_bn,
                'postal_code' => $t->postal_code ?? null,
                'id' => $t->id,
            ])->toArray();
        }
        if (!empty($byDistrict)) {
            return $byDistrict;
        }
    }
    $districts = getDistrictList();
    $defaults = getDefaultThanasByDistrict();
    $byDistrict = [];
    foreach ($districts as $d) {
        $en = is_array($d) ? ($d['en'] ?? '') : (is_object($d) ? ($d->en ?? '') : '');
        if ($en === '') {
            continue;
        }
        $byDistrict[$en] = [];
    }
    $row = \App\Models\Frontend::where('data_keys', 'thana.list')->first();
    if ($row && !empty($row->data_values)) {
        $raw = $row->data_values;
        $saved = is_array($raw) ? $raw : (array) $raw;
        foreach ($saved as $districtEn => $list) {
            $districtEn = is_string($districtEn) ? $districtEn : (string) $districtEn;
            if (!isset($byDistrict[$districtEn])) {
                $byDistrict[$districtEn] = [];
            }
            $normalized = [];
            $arrList = is_array($list) ? $list : [];
            foreach ($arrList as $item) {
                $arr = is_array($item) ? $item : (array) $item;
                $normalized[] = ['en' => $arr['en'] ?? '', 'bn' => $arr['bn'] ?? ''];
            }
            $byDistrict[$districtEn] = $normalized;
        }
    }
    foreach ($byDistrict as $districtEn => $list) {
        if (!empty($list)) {
            continue;
        }
        $byDistrict[$districtEn] = $defaults[$districtEn] ?? [];
    }
    return $byDistrict;
}

/**
 * Gateway logo URL for payment method selection.
 * Uses admin-uploaded official logo first; fallback: assets/images/gateways/{alias}.png or null.
 */
function getGatewayLogoUrl($alias, $logoFilename = null)
{
    if (!empty($logoFilename)) {
        $path = getFilePath('gatewayLogo') . '/' . $logoFilename;
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            return asset($path);
        }
    }
    if (empty($alias)) {
        return null;
    }
    $path = 'assets/images/gateways/' . str_replace([' ', '/', '\\'], ['_', '_', '_'], $alias) . '.png';
    $fullPath = base_path('../' . $path);
    if (!file_exists($fullPath)) {
        $fullPath = public_path($path);
    }
    return file_exists($fullPath) ? asset($path) : null;
}

function getFileSize($key)
{
    return fileManager()->$key()->size;
}

/**
 * Check if user has purchased the product (delivered order).
 */
function hasPurchasedProduct($userId, $productId)
{
    return \App\Models\OrderDetail::where('product_id', $productId)
        ->whereHas('order', function ($q) use ($userId) {
            $q->where('user_id', $userId)->delivered();
        })
        ->exists();
}

function getFileExt($key)
{
    return fileManager()->$key()->extensions;
}

function diffForHumans($date)
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->diffForHumans();
}

function showDateTime($date, $format = 'Y-m-d h:i A')
{
    $lang = session()->get('lang');
    Carbon::setlocale($lang);
    return Carbon::parse($date)->translatedFormat($format);
}

function getContent($dataKeys, $singleQuery = false, $limit = null, $orderById = false)
{
    if ($singleQuery) {
        $content = Frontend::where('data_keys', $dataKeys)->orderBy('id', 'desc')->first();
    } else {
        $article = Frontend::query();
        $article->when($limit != null, function ($q) use ($limit) {
            return $q->limit($limit);
        });
        if ($orderById) {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id')->get();
        } else {
            $content = $article->where('data_keys', $dataKeys)->orderBy('id', 'desc')->get();
        }
    }
    return $content;
}

/**
 * data_values for header_icons.content (storefront + admin icon manager).
 * No request-static cache: avoids stale icons after admin save; safe for Octane/long-lived workers.
 */
function header_icon_values(): array
{
    $content = getContent('header_icons.content', true);
    if (!$content) {
        return [];
    }

    return (array) ($content->data_values ?? []);
}

/**
 * Resolve uploaded header icon file (public first, then project-root assets/ mirror — same URLs as asset() on typical XAMPP subfolder installs).
 */
function header_icon_storage_absolute_path(string $safeBasename): ?string
{
    $safeBasename = basename($safeBasename);
    if ($safeBasename === '' || str_contains($safeBasename, '..')) {
        return null;
    }
    $rel = 'assets/images/frontend/header_icons/' . $safeBasename;
    $public = public_path($rel);
    if (is_file($public) && is_readable($public)) {
        return $public;
    }
    $legacy = rtrim(dirname(base_path()), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    if (is_file($legacy) && is_readable($legacy)) {
        return $legacy;
    }

    return null;
}

/**
 * Copy all files from core/public header_icons dir to project-root assets/… so /{app}/assets/… URLs resolve (mirrors fileUploader behaviour after upload).
 */
function mirror_header_icons_public_to_legacy(): void
{
    $pubDir = public_path('assets/images/frontend/header_icons');
    if (!is_dir($pubDir)) {
        return;
    }
    $legacyDir = rtrim(dirname(base_path()), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'header_icons';
    if (!is_dir($legacyDir) && !@mkdir($legacyDir, 0755, true)) {
        return;
    }
    foreach (glob($pubDir . DIRECTORY_SEPARATOR . '*') ?: [] as $file) {
        if (!is_file($file)) {
            continue;
        }
        $dest = $legacyDir . DIRECTORY_SEPARATOR . basename($file);
        @copy($file, $dest);
    }
}

/**
 * Public URL for an uploaded header icon file (cache-bust from disk mtime).
 */
function header_icon_uploaded_asset_url(?string $filename): string
{
    if ($filename === null) {
        return '';
    }
    $filename = trim($filename);
    if ($filename === '') {
        return '';
    }
    $safe = basename($filename);
    if ($safe === '' || str_contains($safe, '..')) {
        return '';
    }
    $path = header_icon_storage_absolute_path($safe);
    $v = ($path && is_file($path)) ? filemtime($path) : time();

    return asset('assets/images/frontend/header_icons/' . $safe) . '?v=' . $v;
}

function header_icon_svg(string $key, string $fallback): string
{
    $v = trim((string) (header_icon_values()[$key] ?? ''));

    return $v !== '' ? $v : $fallback;
}

/**
 * Map a generic / FA-style icon name to a Lucide kebab icon id (for data-lucide + npm lucide).
 */
function lucide_icon_kebab(string $rawName): string
{
    $raw = str_replace(['fa ', 'fas ', 'far ', 'fab ', 'fa-'], '', trim($rawName));
    $nameLower = strtolower($raw);

    $iconAliases = [
        'angle-double-up' => 'chevrons-up',
        'angle-left' => 'chevron-left',
        'angle-right' => 'chevron-right',
        'angle-up' => 'chevron-up',
        'angle-down' => 'chevron-down',
        'times' => 'x',
        'close' => 'x',
        'th-large' => 'layout-grid',
        'exchange-alt' => 'arrow-left-right',
        'shipping-fast' => 'truck',
        'sliders-h' => 'sliders-horizontal',
        'filter' => 'funnel',
        'sign-in-alt' => 'log-in',
        'facebook-f' => 'facebook',
        'whatsapp' => 'message-circle',
        'print' => 'printer',
        'exclamation-circle' => 'circle-alert',
        'check-circle' => 'circle-check',
        'check-double' => 'check-check',
        'grid' => 'layout-grid',
        'mobile-alt' => 'smartphone',
        'map-marker-alt' => 'map-pin',
        'map-marker' => 'map-pin',
        'haykal' => 'sparkles',
        'cart-plus' => 'circle-plus',
        'list-alt' => 'list',
        'money-bill-wave' => 'banknote',
        'sign-out-alt' => 'log-out',
        'user-tie' => 'user-round',
        'circle' => 'circle',
        'paper-plane' => 'send',
        'android' => 'smartphone',
        'microphone' => 'mic',
        'scan' => 'scan-line',
        'user-plus' => 'user-plus',
        'user-minus' => 'user-minus',
        'comments' => 'messages-square',
        'language' => 'languages',
        'twitter' => 'twitter',
        'x-twitter' => 'twitter',
        'bolt' => 'zap',
        'key' => 'key-round',
        'box' => 'package',
    ];

    if (isset($iconAliases[$nameLower])) {
        return $iconAliases[$nameLower];
    }

    return match (true) {
        in_array($nameLower, ['cart', 'cart_icon', 'shopping-cart', 'shopping_cart'], true)
            || str_contains($nameLower, 'shopping-cart')
            || str_contains($nameLower, 'add-to-cart') => 'shopping-cart',
        $nameLower === 'wishlist_icon' || str_contains($nameLower, 'heart') => 'heart',
        str_contains($nameLower, 'exchange') => 'arrow-left-right',
        $nameLower === 'quick_view_icon' || $nameLower === 'eye' => 'eye',
        $nameLower === 'buy_now_icon' || str_contains($nameLower, 'buy-now') => 'zap',
        str_contains($nameLower, 'bag') => 'shopping-bag',
        str_contains($nameLower, 'user') => 'user',
        str_contains($nameLower, 'search') => 'search',
        str_contains($nameLower, 'map') || str_contains($nameLower, 'marker') => 'map-pin',
        str_contains($nameLower, 'phone') => 'phone',
        str_contains($nameLower, 'mail') || str_contains($nameLower, 'envelope') => 'mail',
        str_contains($nameLower, 'truck') => 'truck',
        str_contains($nameLower, 'star') => 'star',
        str_contains($nameLower, 'chevron-down') || str_contains($nameLower, 'angle-down') => 'chevron-down',
        str_contains($nameLower, 'chevron-up') || str_contains($nameLower, 'angle-up') => 'chevron-up',
        str_contains($nameLower, 'chevron-left') || str_contains($nameLower, 'angle-left') => 'chevron-left',
        str_contains($nameLower, 'chevron-right') || str_contains($nameLower, 'angle-right') => 'chevron-right',
        str_contains($nameLower, 'menu') || str_contains($nameLower, 'bars') => 'menu',
        str_contains($nameLower, 'times') || str_contains($nameLower, 'close') => 'x',
        default => str_replace('_', '-', $nameLower),
    };
}

/**
 * Lucide name for header / product chrome keys (admin iconKey + FA fallback).
 */
/**
 * Neutral SVG data-URL for broken custom header / button images.
 */
function stayl_placeholder_icon_data_url(): string
{
    static $cached = null;
    if ($cached !== null) {
        return $cached;
    }
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-3.086-3.086a.5.5 0 0 0-.707 0L9 20"/></svg>';

    return $cached = 'data:image/svg+xml,' . rawurlencode($svg);
}

function header_icon_lucide_name(string $iconKey, string $fallback = ''): string
{
    $key = strtolower(trim($iconKey));
    $map = [
        'wishlist_icon' => 'heart',
        'compare_icon' => 'arrow-left-right',
        'cart_icon' => 'shopping-cart',
        'buy_now_icon' => 'zap',
        'quick_view_icon' => 'eye',
        'search_icon' => 'search',
        'voice_search_icon' => 'mic',
        'image_search_icon' => 'scan-line',
        'products_icon' => 'package',
        'contact_icon' => 'phone',
        'track_order_icon' => 'truck',
        'language_icon' => 'languages',
        'notification_icon' => 'bell',
        'orders_icon' => 'clipboard-list',
        'login_icon' => 'log-in',
        'register_icon' => 'user-plus',
        'home_icon' => 'home',
        'messages_icon' => 'messages-square',
        'transactions_icon' => 'banknote',
        'review_icon' => 'star',
        'profile_icon' => 'user-round',
        'change_password_icon' => 'key-round',
        'logout_icon' => 'log-out',
        'scroll_top_icon' => 'chevrons-up',
        'category_icon' => 'layout-grid',
        'categories_icon' => 'layout-grid',
    ];

    if (isset($map[$key])) {
        return $map[$key];
    }

    $fb = trim($fallback);

    return lucide_icon_kebab($fb !== '' ? $fb : $iconKey);
}

function header_icon_uploaded(string $key): ?string
{
    $file = trim((string) (header_icon_values()[$key . '_image'] ?? ''));

    return $file !== '' ? $file : null;
}

/**
 * Shipped Lucide SVG defaults (repo files, no DB). Used when admin has not uploaded an image
 * or after upload is removed — same professional look survives DB reset.
 *
 * @see public/assets/images/frontend/header_icons/bundled/{iconKey}.svg
 */
function header_icon_bundled_default_svg_path(string $iconKey): ?string
{
    $iconKey = trim($iconKey);
    if ($iconKey === '' || ! preg_match('/^[a-z0-9_]+$/i', $iconKey)) {
        return null;
    }
    $rel = 'assets/images/frontend/header_icons/bundled/' . $iconKey . '.svg';
    $public = public_path($rel);
    if (is_file($public) && is_readable($public)) {
        return $public;
    }
    $legacy = rtrim(dirname(base_path()), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rel);
    if (is_file($legacy) && is_readable($legacy)) {
        return $legacy;
    }

    return null;
}

/**
 * Inline SVG for uploaded header icons so Lucide currentColor follows parent text/icon color (img tags cannot).
 */
function header_icon_inline_svg_html(string $iconKey, string $imgClass, int $w, int $h, string $alt = ''): ?string
{
    $path = null;
    $file = header_icon_uploaded($iconKey);
    if ($file !== null && $file !== '' && preg_match('/\.svg$/i', $file)) {
        $safe = basename($file);
        if ($safe !== '' && ! str_contains($safe, '..')) {
            $path = header_icon_storage_absolute_path($safe);
        }
    }
    if ($path === null || ! is_file($path)) {
        $path = header_icon_bundled_default_svg_path($iconKey);
    }
    if ($path === null || ! is_readable($path)) {
        return null;
    }
    $raw = @file_get_contents($path);
    if ($raw === false || $raw === '') {
        return null;
    }
    if (preg_match('/<script[\s>]/i', $raw) || preg_match('/\bon\w+\s*=/i', $raw)) {
        return null;
    }
    $raw = preg_replace('/<\?xml[^?]*\?>\s*/i', '', $raw);
    $raw = trim($raw);
    if (!preg_match('/^<\s*svg\b/is', $raw)) {
        return null;
    }
    $replaced = preg_replace_callback(
        '/<\s*svg\b([^>]*)>/is',
        static function (array $m) use ($imgClass, $w, $h, $alt): string {
            $inner = $m[1];
            $inner = preg_replace('/\s+(width|height)="[^"]*"/i', '', $inner);
            $inner = is_string($inner) ? $inner : '';
            $a11y = $alt !== ''
                ? ' role="img" aria-label="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '"'
                : ' aria-hidden="true"';
            $cls = trim($imgClass . ' staylbd-inline-svg staylbd-icon-premium');
            $cls = htmlspecialchars($cls, ENT_QUOTES, 'UTF-8');

            return '<svg class="' . $cls . '" width="' . (int) $w . '" height="' . (int) $h . '" focusable="false"' . $a11y . $inner . '>';
        },
        $raw,
        1
    );

    return is_string($replaced) ? $replaced : null;
}

/**
 * Default registration form fields. 1 = enabled, 0 = disabled.
 * Existing/core fields default 1; future/extra fields default 0.
 */
/**
 * Default = যা ইউজার ভাসমান রেজিস্ট্রেশন পেজে বর্তমানে দেখা যায় (অ্যাডমিনে অটো টিক থাকবে)
 */
function defaultRegistrationFields()
{
    return [
        'referBy' => 1,
        'firstname' => 1,
        'lastname' => 0,
        'username' => 1,
        'email' => 1,
        'country' => 1,
        'mobile' => 1,
        'age' => 1,
        'gender' => 1,
        'password' => 1,
        'captcha' => 1,
        'agree' => 1,
        'address' => 0,
        'city' => 0,
        'state' => 0,
        'zip' => 0,
        'division' => 0,
        'district' => 0,
        'thana' => 0,
        'date_of_birth' => 0,
        'occupation' => 0,
        'company_name' => 0,
        'website' => 0,
        'telegram' => 0,
        'whatsapp' => 0,
        'newsletter_subscribe' => 0,
        'how_heard' => 0,
        'profile_photo' => 0,
        'nid_number' => 0,
        'alternate_phone' => 0,
        'preferred_language' => 0,
        'tax_id' => 0,
    ];
}

/**
 * Default fields shown on user profile for editing (after registration).
 * Same keys as registration; captcha/password/agree/referBy are not editable on profile.
 */
function defaultProfileFields()
{
    $reg = defaultRegistrationFields();
    $out = [];
    foreach (array_keys($reg) as $k) {
        if (in_array($k, ['captcha', 'password', 'agree', 'referBy'], true)) {
            $out[$k] = 0;
        } else {
            $out[$k] = in_array($k, ['firstname', 'lastname', 'country', 'address', 'city', 'state', 'zip', 'mobile', 'division', 'district', 'thana', 'telegram', 'whatsapp'], true) ? 1 : 0;
        }
    }
    return $out;
}

/**
 * Get profile fields config from register.content (admin: which fields show on user profile).
 * Cached; admin save clears 'profile_fields_config'.
 */
function profileFieldsConfig()
{
    return \Illuminate\Support\Facades\Cache::remember('profile_fields_config', 86400, function () {
        $content = getContent('register.content', true);
        if (!$content || !isset($content->data_values->profile_fields)) {
            return defaultProfileFields();
        }
        $raw = $content->data_values->profile_fields;
        $stored = is_array($raw) ? $raw : (array) $raw;
        $merged = array_merge(defaultProfileFields(), $stored);
        foreach ($merged as $k => $v) {
            $merged[$k] = ((int) $v === 1 || $v === '1') ? 1 : 0;
        }
        return $merged;
    });
}

/**
 * Check if a field is enabled for user profile (editable after registration).
 */
function isProfileFieldEnabled($field)
{
    $config = profileFieldsConfig();
    return !empty($config[$field]) && ((int) $config[$field] === 1 || $config[$field] === '1');
}

/**
 * All Quick Order form fields that can be toggled in admin (field key => label).
 * Used in admin Quick Order control and on the public Quick Order modal.
 */
function quickOrderFieldsList()
{
    return [
        'guest_phone' => __('Mobile Number'),
        'guest_name' => __('Full Name'),
        'guest_email' => __('Email'),
        'guest_alternate_phone' => __('Alternate Phone'),
        'guest_preferred_contact_time' => __('Preferred contact time'),
        'guest_address' => __('Delivery Address'),
        'guest_area_city' => __('Area / City'),
        'guest_landmark' => __('Landmark / Nearby place'),
        'postal_code' => __('Postal Code'),
        'guest_delivery_note' => __('Delivery instructions'),
        'guest_preferred_delivery_time' => __('Preferred delivery time'),
        'guest_order_note' => __('Order note / Special request'),
    ];
}

/**
 * Quick Order fields grouped by category for admin control board UI.
 */
function quickOrderFieldsGrouped()
{
    return [
        'contact' => [
            'title' => __('Contact Information'),
            'icon' => 'las la-phone-volume',
            'summary' => __('Phone, name, email and when to contact'),
            'fields' => [
                'guest_phone' => ['label' => __('Mobile Number'), 'required' => true],
                'guest_name' => ['label' => __('Full Name'), 'required' => true],
                'guest_email' => ['label' => __('Email'), 'required' => false],
                'guest_alternate_phone' => ['label' => __('Alternate Phone'), 'required' => false],
                'guest_preferred_contact_time' => ['label' => __('Preferred contact time'), 'required' => false],
            ],
        ],
        'address' => [
            'title' => __('Delivery Address'),
            'icon' => 'las la-map-marker-alt',
            'summary' => __('Address, area, landmark and postal code'),
            'fields' => [
                'guest_address' => ['label' => __('Delivery Address'), 'required' => true],
                'guest_area_city' => ['label' => __('Area / City'), 'required' => true],
                'guest_landmark' => ['label' => __('Landmark / Nearby place'), 'required' => false],
                'postal_code' => ['label' => __('Postal Code'), 'required' => false],
            ],
        ],
        'delivery' => [
            'title' => __('Delivery & Notes'),
            'icon' => 'las la-truck',
            'summary' => __('Instructions, time preference and order notes'),
            'fields' => [
                'guest_delivery_note' => ['label' => __('Delivery instructions'), 'required' => false],
                'guest_preferred_delivery_time' => ['label' => __('Preferred delivery time'), 'required' => false],
                'guest_order_note' => ['label' => __('Order note / Special request'), 'required' => false],
            ],
        ],
    ];
}

/**
 * Quick Order settings (subtitle text, show register button, etc.).
 * Stored in Frontend row: data_keys = 'quick_order.settings'.
 */
function quickOrderSettings()
{
    $default = (object) [
        'subtitle' => __('Place your order in seconds — no account needed. Our team will confirm by phone.'),
        'show_register_link' => true,
    ];

    $row = \App\Models\Frontend::where('data_keys', 'quick_order.settings')->orderBy('id', 'desc')->first();
    if (!$row || !isset($row->data_values)) {
        return $default;
    }

    $data = (array) $row->data_values;
    $subtitle = isset($data['subtitle']) && is_string($data['subtitle']) && trim($data['subtitle']) !== ''
        ? $data['subtitle']
        : $default->subtitle;

    $showRegister = array_key_exists('show_register_link', $data)
        ? (bool) $data['show_register_link']
        : $default->show_register_link;

    return (object) [
        'subtitle' => $subtitle,
        'show_register_link' => $showRegister,
    ];
}

/**
 * Get list of enabled Quick Order field keys.
 * Reads from general_settings.quick_order_fields if column exists, else from Frontend (quick_order.fields).
 * When not set, returns default visible fields.
 */
function getQuickOrderEnabledFields()
{
    $default = ['guest_phone', 'guest_name', 'guest_address', 'guest_area_city', 'guest_delivery_note'];

    if (\Illuminate\Support\Facades\Schema::hasColumn('general_settings', 'quick_order_fields')) {
        $raw = gs('quick_order_fields');
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }
        if (is_array($raw) && !empty($raw)) {
            return $raw;
        }
    }

    $row = \App\Models\Frontend::where('data_keys', 'quick_order.fields')->orderBy('id', 'desc')->first();
    if ($row && isset($row->data_values->fields) && is_array($row->data_values->fields) && !empty($row->data_values->fields)) {
        return $row->data_values->fields;
    }

    return $default;
}

/**
 * Check if a Quick Order form field is enabled (shown on Quick Order page).
 */
function isQuickOrderFieldEnabled($fieldKey)
{
    return in_array($fieldKey, getQuickOrderEnabledFields(), true);
}

function registrationFieldsList()
{
    $list = [];
    foreach (registrationFieldsListGrouped() as $group) {
        foreach ($group['fields'] as $fkey => $label) {
            $list[$fkey] = $label;
        }
    }
    return $list;
}

/**
 * Registration fields grouped by category for admin UI.
 */
function registrationFieldsListGrouped()
{
    return [
        'basic' => [
            'title' => __('Basic (required from user)'),
            'icon' => 'las la-user',
            'fields' => [
                'referBy' => __('Reference by'),
                'firstname' => __('Name'),
                'username' => __('Username'),
                'email' => __('E-Mail Address'),
                'mobile' => __('Phone number'),
                'password' => __('Password'),
                'captcha' => __('Captcha'),
                'agree' => __('Agree with terms & conditions'),
            ],
        ],
        'additional' => [
            'title' => __('Additional information'),
            'icon' => 'las la-list-alt',
            'fields' => [
                'country' => __('Country'),
                'address' => __('Address'),
                'city' => __('City'),
                'state' => __('State / Province'),
                'zip' => __('ZIP / Postal code'),
                'division' => __('Division (Bangladesh)'),
                'district' => __('District (Bangladesh)'),
                'thana' => __('Thana (Bangladesh)'),
                'alternate_phone' => __('Alternate phone'),
                'age' => __('Age'),
                'gender' => __('Gender'),
                'date_of_birth' => __('Date of birth'),
                'occupation' => __('Occupation'),
                'company_name' => __('Company name'),
                'website' => __('Website'),
                'tax_id' => __('Tax ID / VAT number'),
                'profile_photo' => __('Profile photo'),
                'nid_number' => __('NID / Passport number'),
                'preferred_language' => __('Preferred language'),
                'newsletter_subscribe' => __('Subscribe to newsletter'),
                'how_heard' => __('How did you hear about us?'),
            ],
        ],
    ];
}

/**
 * Get registration fields config from register.content (admin toggles).
 * Cached; admin save clears 'registration_fields_config' so changes apply immediately.
 */
function registrationFieldsConfig()
{
    return \Illuminate\Support\Facades\Cache::remember('registration_fields_config', 86400, function () {
        $content = getContent('register.content', true);
        if (!$content || !isset($content->data_values->registration_fields)) {
            return defaultRegistrationFields();
        }
        $raw = $content->data_values->registration_fields;
        $stored = is_array($raw) ? $raw : (array) $raw;
        $merged = array_merge(defaultRegistrationFields(), $stored);
        foreach ($merged as $k => $v) {
            $merged[$k] = ((int) $v === 1 || $v === '1') ? 1 : 0;
        }
        return $merged;
    });
}

/**
 * Check if a registration form field is enabled in admin.
 */
function isRegistrationFieldEnabled($field)
{
    $config = registrationFieldsConfig();
    return !empty($config[$field]) && ((int) $config[$field] === 1 || $config[$field] === '1');
}

/**
 * Registration field keys that can be used as login credentials (must be enabled in registration to allow login with them).
 */
function loginCredentialCapableKeys()
{
    return ['username', 'email', 'mobile'];
}

/**
 * Default login credential options (admin can enable/disable each). Only keys that exist in registration.
 */
function defaultLoginFields()
{
    return [
        'username' => 1,
        'email' => 1,
        'mobile' => 0,
    ];
}

/**
 * Get login_fields as stored in Frontend → Login only (no merge with Registration).
 * Used for admin form display and for user login page label so that what admin toggles in Login section is reflected.
 */
function getLoginFieldsFromSection()
{
    $content = getLoginContent();
    $defaults = defaultLoginFields();
    if (!$content || !isset($content->data_values->login_fields)) {
        return $defaults;
    }
    $raw = $content->data_values->login_fields;
    $stored = is_array($raw) ? $raw : (array) $raw;
    $result = array_merge($defaults, $stored);
    foreach (loginCredentialCapableKeys() as $k) {
        if (!array_key_exists($k, $result)) {
            $result[$k] = isset($defaults[$k]) ? (int) $defaults[$k] : 0;
        }
        $result[$k] = ((int) $result[$k] === 1 || $result[$k] === '1') ? 1 : 0;
    }
    return $result;
}

/**
 * Whether a login credential is enabled in Frontend → Login section only (for admin form and user page label).
 * Use this for display; use isLoginFieldEnabled() for actual auth (which also requires Registration).
 */
function isLoginFieldEnabledForDisplay($field)
{
    $fields = getLoginFieldsFromSection();
    return !empty($fields[$field]) && ((int) $fields[$field] === 1 || $fields[$field] === '1');
}

/**
 * Get login fields config: only credentials that are BOTH enabled for login AND enabled in registration.
 * So user can only login with info that was collected during registration.
 */
function loginFieldsConfig()
{
    $capable = loginCredentialCapableKeys();
    $defaults = defaultLoginFields();
    $regConfig = registrationFieldsConfig();

    $content = getLoginContent();
    $loginStored = [];
    if ($content && isset($content->data_values->login_fields)) {
        $raw = $content->data_values->login_fields;
        $loginStored = is_array($raw) ? $raw : (array) $raw;
    }
    // Ensure all capable keys exist (JSON/object may omit 0 values)
    foreach ($capable as $k) {
        if (!array_key_exists($k, $loginStored)) {
            $loginStored[$k] = isset($defaults[$k]) ? (int) $defaults[$k] : 0;
        }
    }

    $merged = [];
    foreach ($capable as $k) {
        $regEnabled = !empty($regConfig[$k]) && ((int) $regConfig[$k] === 1 || $regConfig[$k] === '1');
        $loginRequested = isset($loginStored[$k]) ? ((int) $loginStored[$k] === 1 || $loginStored[$k] === '1') : (isset($defaults[$k]) ? (int) $defaults[$k] : 0);
        $merged[$k] = ($regEnabled && $loginRequested) ? 1 : 0;
    }
    return $merged;
}

/**
 * Check if a login credential type is enabled in admin.
 */
function isLoginFieldEnabled($field)
{
    $config = loginFieldsConfig();
    return !empty($config[$field]) && ((int) $config[$field] === 1 || $config[$field] === '1');
}

/**
 * Label for the single login input (e.g. "Username or Email") based on enabled fields.
 * Uses Frontend → Login section toggles only so the user page reflects what admin set there.
 */
function getLoginFieldLabel()
{
    $u = isLoginFieldEnabledForDisplay('username');
    $e = isLoginFieldEnabledForDisplay('email');
    $m = isLoginFieldEnabledForDisplay('mobile');
    if ($u && $e && $m) {
        return __('Username, Email or Mobile');
    }
    if ($u && $e) {
        return __('Username or Email');
    }
    if ($u && $m) {
        return __('Username or Mobile');
    }
    if ($e && $m) {
        return __('Email or Mobile');
    }
    if ($u) {
        return __('Username');
    }
    if ($e) {
        return __('Email');
    }
    if ($m) {
        return __('Mobile');
    }
    return __('Username or Email');
}

/**
 * Check if captcha is enabled for user login (floating & full page).
 * Controlled from admin frontend/login first; fallback to frontend/register (login_captcha_enabled).
 */
function isLoginCaptchaEnabled()
{
    $content = getLoginContent();
    if ($content && isset($content->data_values->captcha_enabled)) {
        return (int) $content->data_values->captcha_enabled === 1;
    }
    $content = getContent('register.content', true);
    if ($content && isset($content->data_values->login_captcha_enabled)) {
        return (int) $content->data_values->login_captcha_enabled === 1;
    }
    return true; // default: show captcha on login
}

/**
 * Login content (heading, subheading, login_fields, captcha_enabled, social_login_buttons).
 * Always read from DB so admin ON/OFF toggles apply immediately on user login page (no cache).
 */
function getLoginContent()
{
    return getContent('login.content', true);
}

/** Default which social login buttons to show on user login page (1 = show when provider is configured). */
function defaultSocialLoginButtons()
{
    return [
        'google' => 1,
        'facebook' => 1,
        'twitter' => 1,
        'apple' => 1,
        'github' => 1,
    ];
}

/** Which social buttons to show on login page (from admin frontend/login). No cache so changes apply immediately. */
function getSocialLoginButtonsConfig()
{
    $content = getLoginContent();
    if (!$content || !isset($content->data_values->social_login_buttons)) {
        return defaultSocialLoginButtons();
    }
    $raw = $content->data_values->social_login_buttons;
    $stored = is_array($raw) ? $raw : (array) $raw;
    $merged = array_merge(defaultSocialLoginButtons(), $stored);
    foreach ($merged as $k => $v) {
        $merged[$k] = ((int) $v === 1 || $v === '1' || $v === true) ? 1 : 0;
    }
    return $merged;
}

/**
 * Whether to show a social login button on user login page.
 * Controlled by Frontend → Login (admin). When ON, button is shown; if provider not configured in Settings → Social Login, user gets a message on click.
 */
function isSocialLoginButtonEnabled($provider)
{
    $config = getSocialLoginButtonsConfig();
    return !empty($config[$provider]) && ((int) $config[$provider] === 1 || $config[$provider] === '1' || $config[$provider] === true);
}

/** Cache key for footer partial data (cleared when footer/contact/social/policy/service sections are saved in admin). */
const FOOTER_CACHE_KEY = 'frontend_footer_data_v6';

/** TTL in seconds for footer data cache (10 min). */
const FOOTER_CACHE_TTL = 600;

/**
 * Order a Frontend query by display_order (JSON) with safe fallback to id (avoids SQL error when JSON missing).
 */
function frontendOrderByDisplayOrder($query)
{
    try {
        return $query->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')->orderBy('id', 'asc');
    } catch (\Throwable $e) {
        return $query->orderBy('id', 'asc');
    }
}

/**
 * Default footer data structure when DB/cache fails (so footer partial never crashes).
 */
function getDefaultFooterData(): array
{
    return [
        'contact' => null,
        'footer_element' => collect([]),
        'footer_content' => null,
        'footer_company_info' => null,
        'footer_quick_links' => collect([]),
        'footer_support_center' => null,
        'footer_security_badges' => collect([]),
        'footer_shipping_payment' => null,
        'footer_app_promotion' => null,
        'footer_app_promotion_items' => collect([]),
        'footer_custom_ads' => collect([]),
        'footer_return_policy' => null,
        'social_element' => collect([]),
        'policy_pages' => collect([]),
        'services' => collect([]),
        'cookie_data' => null,
    ];
}

/**
 * Cached footer data for the footer partial: contact, footer content/elements (ordered), social, policy pages, services.
 * Use this in the footer partial to reduce DB queries and respect display_order for payment icons.
 * On any DB/exception returns safe defaults so the footer always renders.
 *
 * @return array{contact: \App\Models\Frontend|null, footer_element: \Illuminate\Support\Collection, footer_content: \App\Models\Frontend|null, social_element: \Illuminate\Support\Collection, policy_pages: \Illuminate\Support\Collection, services: \Illuminate\Support\Collection, cookie_data: \App\Models\Frontend|null, ...}
 */
function getCachedFooterData()
{
    try {
        $cacheKey = FOOTER_CACHE_KEY . '.' . app()->getLocale();
        return Cache::remember($cacheKey, FOOTER_CACHE_TTL, function () {
            try {
                $footerElements = frontendOrderByDisplayOrder(Frontend::where('data_keys', 'footer.element'))->get();
                $footerQuickLinks = frontendOrderByDisplayOrder(Frontend::where('data_keys', 'footer.quick_links'))->get();
                $footerSecurityBadges = frontendOrderByDisplayOrder(Frontend::where('data_keys', 'footer.security_badges'))->get();
                $footerCustomAds = frontendOrderByDisplayOrder(Frontend::where('data_keys', 'footer.custom_ads'))->get();
                $footerServices = frontendOrderByDisplayOrder(Frontend::where('data_keys', 'service.element'))->get();
            } catch (\Throwable $e) {
                $footerElements = Frontend::where('data_keys', 'footer.element')->orderBy('id')->get();
                $footerQuickLinks = Frontend::where('data_keys', 'footer.quick_links')->orderBy('id')->get();
                $footerSecurityBadges = Frontend::where('data_keys', 'footer.security_badges')->orderBy('id')->get();
                $footerCustomAds = Frontend::where('data_keys', 'footer.custom_ads')->orderBy('id')->get();
                $footerServices = Frontend::where('data_keys', 'service.element')->orderBy('id')->get();
            }
            return [
                'contact' => Frontend::where('data_keys', 'contact_us.content')->orderBy('id', 'desc')->first(),
                'footer_element' => $footerElements,
                'footer_content' => Frontend::where('data_keys', 'footer.content')->orderBy('id', 'desc')->first(),
                'footer_company_info' => Frontend::where('data_keys', 'footer.company_info')->orderBy('id', 'desc')->first(),
                'footer_quick_links' => $footerQuickLinks,
                'footer_support_center' => Frontend::where('data_keys', 'footer.support_center')->orderBy('id', 'desc')->first(),
                'footer_security_badges' => $footerSecurityBadges,
                'footer_shipping_payment' => Frontend::where('data_keys', 'footer.shipping_payment')->orderBy('id', 'desc')->first(),
                'footer_app_promotion' => Frontend::where('data_keys', 'footer.app_promotion')->orderBy('id', 'desc')->first(),
                'footer_app_promotion_items' => frontendOrderByDisplayOrder(Frontend::where('data_keys', 'footer.app_promotion_item'))->get(),
                'footer_custom_ads' => $footerCustomAds,
                'footer_return_policy' => Frontend::where('data_keys', 'footer.return_policy')->orderBy('id', 'desc')->first(),
                'social_element' => Frontend::where('data_keys', 'social_icon.element')->orderBy('id', 'asc')->get()->filter(function ($row) {
                    $dv = $row->data_values ?? null;
                    if ($dv === null) {
                        return true;
                    }
                    $v = is_array($dv) ? ($dv['show_on_public'] ?? null) : ($dv->show_on_public ?? null);
                    if ($v === null || $v === '') {
                        return true;
                    }

                    return (int) $v === 1;
                })->values(),
                'policy_pages' => Frontend::where('data_keys', 'policy_pages.element')->orderBy('id', 'asc')->get(),
                'services' => $footerServices,
                'cookie_data' => Frontend::where('data_keys', 'cookie.data')->first(),
            ];
        });
    } catch (\Throwable $e) {
        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::warning('getCachedFooterData failed: ' . $e->getMessage());
        }
        return getDefaultFooterData();
    }
}

/**
 * Sanitize admin-pasted inline SVG or data-URI <img> for footer social icons (stored in DB, output raw).
 * Rejects scripts, event handlers, iframes; allows root <svg> or <img src="data:image/...;base64,..."> only.
 */
function sanitizeSocialIconInlineMarkup(?string $raw): string
{
    $raw = trim((string) ($raw ?? ''));
    if ($raw === '' || strlen($raw) > 65535) {
        return '';
    }
    if (!preg_match('/^\s*</s', $raw)) {
        return '';
    }
    if (preg_match('/<script\b/i', $raw) || preg_match('/<\?/i', $raw) || preg_match('/<iframe\b/i', $raw) || preg_match('/<object\b/i', $raw) || preg_match('/<embed\b/i', $raw)) {
        return '';
    }
    $out = preg_replace('/\s*on[a-z][a-z0-9_-]*\s*=\s*("|\')(?:\\\\.|(?!\1).)*\1/is', '', $raw);
    $out = preg_replace('/\s*on[a-z][a-z0-9_-]*\s*=\s*[^\s>]+\s*/i', '', $out);
    $out = preg_replace('/\s*href\s*=\s*("|\')\s*javascript:[^"\']*\1/i', '', $out);
    $out = trim($out);
    if (preg_match('/^\s*<svg\b/is', $out)) {
        return $out;
    }
    if (preg_match('/^\s*<img\b/is', $out) && preg_match('/src\s*=\s*("|\')(data:image\/(?:png|jpeg|jpg|webp|svg\+xml);base64,[A-Za-z0-9+\/\s=]+)\1/is', $out, $m)) {
        $src = preg_replace('/\s+/', '', $m[2]);

        return '<img src="' . htmlspecialchars($src, ENT_QUOTES, 'UTF-8') . '" alt="" class="stayl-footer-social-img object-contain" width="22" height="22" loading="lazy" decoding="async" />';
    }

    return '';
}

/** Clear footer partial cache (call after saving footer, contact, social, policy, or service in admin). */
function clearFooterCache()
{
    $locales = ['en', 'bn']; // Clear for all supported locales
    foreach ($locales as $l) {
        Cache::forget(FOOTER_CACHE_KEY . '.' . $l);
    }
}

const HOMEPAGE_SECTION_CACHE_KEY = 'frontend_home_section_data';
const HOMEPAGE_SECTION_CACHE_TTL = 600;

/**
 * Default values for homepage section settings (admin-controllable).
 */
function getHomeSectionSettingsDefaults()
{
    return [
        'power_zone_enabled' => 1,
        'show_category_icons' => 1,
        'show_flash_deals' => 1,
        'show_trending' => 1,
        'show_quick_services' => 1,
        'show_promo_blocks' => 1,
        'show_quick_category_boxes' => 1,
        'flash_sale_end_date' => now()->endOfDay()->toIso8601String(),
        'flash_sale_title' => 'Flash Sale',
        'trust_section_enabled' => 1,
        'social_proof_enabled' => 1,
        'live_purchase_enabled' => 0,
        'reviews_slider_enabled' => 1,
        'top_rated_enabled' => 1,
        'recommendation_enabled' => 1,
        'recently_viewed_enabled' => 1,
        'similar_products_enabled' => 1,
        'sticky_cart_enabled' => 1,
        'quick_view_enabled' => 1,
        'wishlist_popup_enabled' => 1,
        'compare_enabled' => 1,
        'floating_cart_enabled' => 1,
        'conversion_enabled' => 1,
        'limited_stock_enabled' => 1,
        'only_x_left_enabled' => 1,
        'people_viewing_enabled' => 0,
        'recently_sold_enabled' => 0,
        'flash_deals_limit' => 8,
        'trending_limit' => 8,
        'top_rated_limit' => 8,
        'reviews_slider_limit' => 6,
    ];
}

/**
 * Cached homepage section settings and trust/promo/quick_service elements (single query).
 */
function getCachedHomeSectionData()
{
    $cacheKey = HOMEPAGE_SECTION_CACHE_KEY . '.' . app()->getLocale();
    return Cache::remember($cacheKey, HOMEPAGE_SECTION_CACHE_TTL, function () {
        $keys = [
            'home_section.settings',
            'home_section.trust',
            'home_section.quick_service',
            'home_section.promo_banner',
            'home_section.quick_category',
        ];
        $all = Frontend::whereIn('data_keys', $keys)->get();
        $grouped = $all->groupBy('data_keys');

        $settingsRow = $grouped->get('home_section.settings')?->sortByDesc('id')->first();
        $dv = $settingsRow ? (array) ($settingsRow->data_values ?? []) : [];
        $merged = array_merge(getHomeSectionSettingsDefaults(), $dv);

        $sortByOrder = function ($items) {
            $getOrder = function ($row) {
                $dv = $row->data_values;
                if (is_array($dv))
                    return (int) ($dv['display_order'] ?? 999);
                return (int) ($dv->display_order ?? 999);
            };
            return $items->sort(fn($a, $b) => $getOrder($a) <=> $getOrder($b) ?: $a->id <=> $b->id)->values();
        };

        $topFeatures = collect();
        try {
            $topFeatures = \App\Models\HomepageTopFeature::visibleOnFront()->get();
        } catch (\Throwable $e) {
            if (config('app.debug')) {
                \Illuminate\Support\Facades\Log::warning('getCachedHomeSectionData topFeatures: ' . $e->getMessage());
            }
        }

        return [
            'settings' => (object) $merged,
            'trust_elements' => $sortByOrder($grouped->get('home_section.trust') ?? collect()),
            'quick_service_elements' => $sortByOrder($grouped->get('home_section.quick_service') ?? collect()),
            'promo_banner_elements' => $sortByOrder($grouped->get('home_section.promo_banner') ?? collect()),
            'quick_category_elements' => $sortByOrder($grouped->get('home_section.quick_category') ?? collect()),
            'top_features' => $topFeatures,
        ];
    });
}

function clearHomeSectionCache()
{
    $locales = ['en', 'bn'];
    foreach ($locales as $l) {
        Cache::forget(HOMEPAGE_SECTION_CACHE_KEY . '.' . $l);
    }
}

const PRODUCT_SLIDER_CACHE_KEY = 'frontend_product_slider_settings';
const PRODUCT_SLIDER_CACHE_TTL = 600;

/**
 * Default values for product slider (auto-scroll carousel) settings.
 * Per-section: hot_deal_interval_seconds, featured_interval_seconds, etc. (2–30).
 */
function getProductSliderSettingsDefaults()
{
    return [
        'auto_scroll_enabled' => 1,
        'scroll_interval_seconds' => 4,
        'scroll_animation_speed_ms' => 600,
        'products_per_row_desktop' => 6,
        'products_per_row_tablet' => 4,
        'products_per_row_mobile' => 2,
        'hot_deal_interval_seconds' => 3,
        'featured_interval_seconds' => 5,
        'new_arrivals_interval_seconds' => 4,
        'trending_interval_seconds' => 4,
        'best_selling_interval_seconds' => 5,
        'recommended_interval_seconds' => 5,
    ];
}

/**
 * Scroll interval (seconds) for a homepage product section. Uses per-section setting if set, else global.
 */
function getSectionScrollIntervalSeconds(?string $sectionKey): int
{
    $settings = getProductSliderSettings();
    $key = $sectionKey ? $sectionKey . '_interval_seconds' : null;
    if ($key && isset($settings->$key)) {
        return max(2, min(30, (int) $settings->$key));
    }
    return max(2, min(30, (int) ($settings->scroll_interval_seconds ?? 4)));
}

/**
 * Cached product slider settings from Frontend (data_keys: product_slider.settings).
 */
function getProductSliderSettings()
{
    return Cache::remember(PRODUCT_SLIDER_CACHE_KEY, PRODUCT_SLIDER_CACHE_TTL, function () {
        $row = Frontend::where('data_keys', 'product_slider.settings')->orderBy('id', 'desc')->first();
        $dv = $row && isset($row->data_values) ? (array) $row->data_values : [];
        return (object) array_merge(getProductSliderSettingsDefaults(), $dv);
    });
}

function clearProductSliderCache()
{
    Cache::forget(PRODUCT_SLIDER_CACHE_KEY);
}

/**
 * Normalized Tawk.to embed ID for script URL: must be "PropertyID/WidgetID".
 * If .env has a single 40-char string (PropertyID+WidgetID), splits at 24 chars.
 */
function getTawkEmbedId(): string
{
    $id = trim((string) config('services.tawk.property_id', ''));
    if ($id === '') {
        return '';
    }
    if (str_contains($id, '/')) {
        return rtrim($id, '/');
    }
    if (strlen($id) === 40) {
        return substr($id, 0, 24) . '/' . substr($id, 24);
    }
    return $id . '/default';
}

/**
 * Power Zone / Top Feature boxes for frontend. Returns empty collection if table missing or query fails.
 */
function getPowerZoneTopFeatures()
{
    try {
        return \App\Models\HomepageTopFeature::visibleOnFront()->get();
    } catch (\Throwable $e) {
        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::warning('getPowerZoneTopFeatures: ' . $e->getMessage());
        }
        return collect([]);
    }
}

function gatewayRedirectUrl($type = false, $orderId = null)
{
    if ($type) {
        return 'user.transactions';
    } else {
        // If orderId is provided, redirect to specific order payment page
        if ($orderId) {
            return 'user.deposit.index';
        }
        // Otherwise redirect to orders list
        return 'user.order.index';
    }
}

function verifyG2fa($user, $code, $secret = null)
{
    $authenticator = new GoogleAuthenticator();
    if (!$secret) {
        $secret = $user->tsc;
    }
    $oneCode = $authenticator->getCode($secret);
    $userCode = $code;
    if ($oneCode == $userCode) {
        $user->tv = 1;
        $user->save();
        return true;
    } else {
        return false;
    }
}

function urlPath($routeName, $routeParam = null)
{
    if ($routeParam == null) {
        $url = route($routeName);
    } else {
        $url = route($routeName, $routeParam);
    }
    $basePath = route('home');
    $path = str_replace($basePath, '', $url);
    return $path;
}

/**
 * Return route URL if the route exists, otherwise '#' (avoids ViewException when route is missing).
 */
function safe_route($name, $parameters = [], $absolute = true)
{
    if (!\Illuminate\Support\Facades\Route::has($name)) {
        return $absolute ? url('#') : '#';
    }

    return storefront_route($name, $parameters, $absolute);
}

function showMobileNumber($number)
{
    $length = strlen($number);
    return substr_replace($number, '***', 2, $length - 4);
}

function showEmailAddress($email)
{
    $endPosition = strpos($email, '@') - 1;
    return substr_replace($email, '***', 1, $endPosition);
}

function getRealIP()
{
    $ip = $_SERVER["REMOTE_ADDR"];
    //Deep detect ip
    if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    }
    if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ($ip == '::1') {
        $ip = '127.0.0.1';
    }

    return $ip;
}

function appendQuery($key, $value)
{
    return request()->fullUrlWithQuery([$key => $value]);
}

/**
 * Check if message text contains blocked content (dangerous links, scripts, hacking-related).
 * Used in contact/live chat to auto-block malicious messages.
 */
function messageContainsBlockedContent($text)
{
    if (!is_string($text) || $text === '') {
        return false;
    }
    $lower = mb_strtolower($text);
    $patterns = [
        'javascript:',
        'vbscript:',
        'data:text/html',
        'data:image/svg+xml',
        '<script',
        '</script',
        'onerror=',
        'onload=',
        'onclick=',
        '<iframe',
        'document.cookie',
        'eval(',
        '<?php',
        'sql injection',
        'drop table',
        'union select',
    ];
    foreach ($patterns as $p) {
        if (str_contains($lower, $p)) {
            return true;
        }
    }
    if (preg_match('#https?://[^\s]+#i', $text) && preg_match('#(phish|malware|hack|exploit|shell|script\.php)#i', $text)) {
        return true;
    }
    return false;
}

function dateSort($a, $b)
{
    return strtotime($a) - strtotime($b);
}

function dateSorting($arr)
{
    usort($arr, "dateSort");
    return $arr;
}

function productPrice($product)
{
    if ($product === null) {
        return 0;
    }
    $discountPrice = showDiscountPrice($product->price ?? 0, $product->discount ?? 0, $product->discount_type ?? 1);

    if (($product->today_deals ?? 0) == Status::YES) {
        $general = gs();
        $discountPrice = showDiscountPrice($product->price, $general->discount, $general->discount_type);
    }

    if ($discountPrice < 0) {
        $discountPrice = 0;
    }

    $baseList = (float) ($product->price ?? 0);
    if ($baseList > 0 && (float) $discountPrice > $baseList) {
        $discountPrice = $baseList;
    }

    return $discountPrice;
}

/**
 * Storefront-safe pricing: never show a "sale" above list; strike price only when compare > effective.
 * Uses selling price + optional original_price (admin "Original price") + discount/today_deals.
 */
function productDisplayPricing($product): array
{
    if ($product === null) {
        return [
            'effective' => 0.0,
            'compare_at' => null,
            'show_strike' => false,
            'save_amount' => 0.0,
            'save_percent' => 0,
            'has_savings' => false,
        ];
    }

    $basePrice = (float) ($product->price ?? 0);
    $originalPrice = (float) ($product->original_price ?? 0);
    $effective = (float) productPrice($product);

    if ($basePrice <= 0 && $effective <= 0) {
        return [
            'effective' => 0.0,
            'compare_at' => null,
            'show_strike' => false,
            'save_amount' => 0.0,
            'save_percent' => 0,
            'has_savings' => false,
        ];
    }

    if ($basePrice > 0 && $effective > $basePrice) {
        $effective = $basePrice;
    }

    $candidates = [];
    if ($basePrice > $effective + 0.000001) {
        $candidates[] = $basePrice;
    }
    if ($originalPrice > $effective + 0.000001) {
        $candidates[] = $originalPrice;
    }
    // Fallback: list price from product.price when discount reduces effective but float compare failed
    if (empty($candidates) && $basePrice > 0) {
        $discountedFromList = (float) showDiscountPrice($basePrice, (float) ($product->discount ?? 0), (int) ($product->discount_type ?? 1));
        if ($discountedFromList < $basePrice - 0.000001 && abs($discountedFromList - $effective) < 0.02) {
            $candidates[] = $basePrice;
        }
    }
    $compareAt = empty($candidates) ? null : max($candidates);
    $showStrike = $compareAt !== null && $compareAt > $effective + 0.000001;
    $saveAmount = $showStrike ? max(0.0, $compareAt - $effective) : 0.0;
    $savePercent = ($showStrike && $compareAt > 0) ? (int) round(($saveAmount / $compareAt) * 100) : 0;

    return [
        'effective' => $effective,
        'compare_at' => $compareAt,
        'show_strike' => $showStrike,
        'save_amount' => $saveAmount,
        'save_percent' => $savePercent,
        'has_savings' => $showStrike && $saveAmount > 0.000001,
    ];
}

function showDiscountPrice($price, $discount, $discount_type)
{
    if ($discount != 0) {
        if ($discount_type == 1) {
            $discountPrice = $price - $discount;
        } else {
            $discountPrice = $price - ($price * $discount / 100);
        }
        return $discountPrice;
    }

    return $price;
}

function showProductRatings($avgRate)
{
    $rate = (float) $avgRate;
    $rate = max(0, min(5, $rate));
    $pathFull = 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z';
    $normalized = round($rate * 2) / 2;
    $label = 'Rating ' . number_format($rate, 1) . ' of 5';
    $html = '<span class="product-card__stars-inline stayl-rating-stars" role="img" aria-label="' . e($label) . '" data-rating="' . e(number_format($rate, 1)) . '">';
    $starPx = 16;
    for ($i = 1; $i <= 5; $i++) {
        if ($normalized >= $i) {
            $html .= '<svg class="product-card__star product-card__star--full" viewBox="0 0 24 24" width="' . $starPx . '" height="' . $starPx . '" aria-hidden="true" focusable="false"><path d="' . $pathFull . '"/></svg>';
            continue;
        }

        if ($normalized >= ($i - 0.5)) {
            $clipId = 'star-half-' . preg_replace('/[^a-zA-Z0-9_-]/', '', uniqid('', true));
            $html .= '<svg class="product-card__star product-card__star--half" viewBox="0 0 24 24" width="' . $starPx . '" height="' . $starPx . '" aria-hidden="true" focusable="false"><defs><clipPath id="' . $clipId . '"><rect x="0" y="0" width="12" height="24"></rect></clipPath></defs><path class="product-card__star-base" d="' . $pathFull . '"></path><path class="product-card__star-fill" d="' . $pathFull . '" clip-path="url(#' . $clipId . ')"></path></svg>';
            continue;
        }

        $html .= '<svg class="product-card__star product-card__star--empty" viewBox="0 0 24 24" width="' . $starPx . '" height="' . $starPx . '" aria-hidden="true" focusable="false"><path d="' . $pathFull . '"/></svg>';
    }
    $html .= '</span>';

    return $html;
}

function discountText($product, $general)
{

    if ($product->discount != 0) {
        if ($product->discount_type == 1) {
            $discount = $general->cur_sym . showAmount($product->discount);
        } else {
            $discount = showAmount($product->discount) . '%';
        }
    } else if ($product->today_deals == 1) {
        if ($general->discount_type == 1) {
            $discount = $general->cur_sym . showAmount($general->discount);
        } else {
            $discount = showAmount($general->discount) . '%';
        }
    } else {
        $discount = '0' . '%';
    }


    return "<span class='badge badge--discount'><i class='las la-minus'></i>
                $discount
            </span>";
}

/**
 * Safely check if general_settings table has a column (avoids errors when DB is unavailable).
 */
function has_gs_column($column)
{
    try {
        return \Illuminate\Support\Facades\Schema::hasColumn('general_settings', $column);
    } catch (\Throwable $e) {
        return false;
    }
}

/**
 * Fast in-request cached table column check.
 * Useful inside loops (e.g. product cards) to avoid repeated schema calls.
 */
function has_table_column_cached(string $table, string $column): bool
{
    static $memo = [];
    $key = $table . '.' . $column;
    if (array_key_exists($key, $memo)) {
        return $memo[$key];
    }

    try {
        $memo[$key] = \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    } catch (\Throwable $e) {
        $memo[$key] = false;
    }

    return $memo[$key];
}

function gs($key = null)
{
    try {
        // Check if database connection is available
        \Illuminate\Support\Facades\DB::connection()->getPdo();

        $general = Cache::get('GeneralSetting');
        if (!$general) {
            $general = GeneralSetting::first();
            if ($general) {
                Cache::put('GeneralSetting', $general);
            }
        }

        if ($key) {
            return $general ? @$general->$key : null;
        }
        return $general;
    } catch (\Exception $e) {
        // Database connection failed, return null or empty object
        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::warning('gs() helper: Database connection failed', [
                'error' => $e->getMessage()
            ]);
        }

        // Return a basic object structure to prevent errors
        $fallback = (object) [];
        if ($key) {
            return null;
        }
        return $fallback;
    }
}

function isImage($string)
{
    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
    $fileExtension = pathinfo($string, PATHINFO_EXTENSION);
    if (in_array($fileExtension, $allowedExtensions)) {
        return true;
    } else {
        return false;
    }
}

function isHtml($string)
{
    if (preg_match('/<.*?>/', $string)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get route name for frontend section based on key
 */
/**
 * Safely normalize data_values to ensure it's always an object
 * Prevents "Cannot use object of type stdClass as array" errors
 */
function normalizeDataValues($dataValues)
{
    if (is_string($dataValues)) {
        return json_decode($dataValues);
    } elseif (is_array($dataValues)) {
        return (object) $dataValues;
    } elseif (is_object($dataValues)) {
        return $dataValues;
    }
    return (object) [];
}

/**
 * Safely get value from data_values object
 * Handles both object and array access patterns
 */
function getDataValue($dataValues, $key, $default = null)
{
    $normalized = normalizeDataValues($dataValues);
    return $normalized->$key ?? $default;
}

function getFrontendSectionRoute($key, $type = 'sections')
{
    $routeMapping = [
        'banner' => 'admin.frontend.sections.banner',
        'contact_us' => 'admin.frontend.sections.contact',
        'footer' => 'admin.frontend.sections.footer',
        'header_icons' => 'admin.frontend.sections.headericons',
        'login' => 'admin.frontend.sections.login',
        'policy_pages' => 'admin.frontend.sections.policy',
        'register' => 'admin.frontend.sections.register',
        'service' => 'admin.frontend.sections.service',
        'social_icon' => 'admin.frontend.sections.social_icon',
        'ticker' => 'admin.frontend.sections.scrollbar',
        'scrollbar' => 'admin.frontend.sections.scrollbar',
        'middle_banner' => 'admin.frontend.sections.middle_banner',
        'bottom_banner' => 'admin.frontend.sections.bottom_banner',
    ];

    if ($type == 'content') {
        $contentMapping = [
            'banner' => 'admin.frontend.sections.content.banner',
            'contact_us' => 'admin.frontend.sections.content.contact',
            'footer' => 'admin.frontend.sections.content.footer',
            'header_icons' => 'admin.frontend.sections.content.headericons',
            'login' => 'admin.frontend.sections.content.login',
            'policy_pages' => 'admin.frontend.sections.content.policy',
            'register' => 'admin.frontend.sections.content.register',
            'service' => 'admin.frontend.sections.content.service',
            'social_icon' => 'admin.frontend.sections.content.social_icon',
            'ticker' => 'admin.frontend.sections.scrollbar.save',
            'scrollbar' => 'admin.frontend.sections.scrollbar.save',
        ];
        return $contentMapping[$key] ?? 'admin.frontend.sections.content';
    } elseif ($type == 'element') {
        $elementMapping = [
            'banner' => 'admin.frontend.sections.element.banner',
            'contact_us' => 'admin.frontend.sections.element.contact',
            'footer' => 'admin.frontend.sections.element.footer',
            'login' => 'admin.frontend.sections.element.login',
            'policy_pages' => 'admin.frontend.sections.element.policy',
            'register' => 'admin.frontend.sections.element.register',
            'service' => 'admin.frontend.sections.element.service',
            'social_icon' => 'admin.frontend.sections.element.social_icon',
            'ticker' => 'admin.frontend.sections.scrollbar',
            'scrollbar' => 'admin.frontend.sections.scrollbar',
        ];
        return $elementMapping[$key] ?? 'admin.frontend.sections.element';
    }
    // Fallback: use general section (route 'admin.frontend.sections' requires {key} param)
    return $routeMapping[$key] ?? 'admin.frontend.sections.general';
}

/**
 * Get scroll bars (headline ticker) for frontend by position.
 * Positions: header_below, banner_below, footer_above, custom, product_listing, category_page
 *
 * @param string|null $position If null, returns all active bars for any position
 * @param array $options Optional: 'page' => current page key (home, product, category, or route name), 'user_logged_in' => bool
 * @return \Illuminate\Support\Collection
 */
function getScrollbars($position = null, array $options = [])
{
    static $memo = [];
    static $versionMemo = [];
    $memoKey = md5(json_encode([
        'position' => $position,
        'page' => $options['page'] ?? null,
        'user' => isset($options['user_logged_in']) ? (int) $options['user_logged_in'] : (\Illuminate\Support\Facades\Auth::check() ? 1 : 0),
    ]));
    if (isset($memo[$memoKey])) {
        return $memo[$memoKey];
    }
    try {
        if (!array_key_exists('settingsUpdatedAt', $versionMemo)) {
            $versionMemo['settingsUpdatedAt'] = \App\Models\Frontend::where('data_keys', \App\Services\ScrollbarService::SETTINGS_KEY)->max('updated_at');
        }
        $settingsUpdatedAt = $versionMemo['settingsUpdatedAt'];
        $settingsVersion = $settingsUpdatedAt ? strtotime((string) $settingsUpdatedAt) : 0;
        $settings = Cache::remember(
            \App\Services\ScrollbarService::CACHE_KEY_SETTINGS . ':' . $settingsVersion,
            \App\Services\ScrollbarService::CACHE_TTL,
            function () {
                return \App\Models\Frontend::where('data_keys', \App\Services\ScrollbarService::SETTINGS_KEY)->first();
            }
        );
        if ($settings && isset($settings->data_values->enabled) && (int) $settings->data_values->enabled === 0) {
            return collect([]);
        }
        if (!array_key_exists('barsUpdatedAt', $versionMemo)) {
            $versionMemo['barsUpdatedAt'] = \App\Models\Frontend::where('data_keys', \App\Services\ScrollbarService::DATA_KEY)
                ->orWhere('data_keys', \App\Services\ScrollbarService::CUSTOM_DATA_KEY)
                ->max('updated_at');
        }
        $barsUpdatedAt = $versionMemo['barsUpdatedAt'];
        $barsVersion = $barsUpdatedAt ? strtotime((string) $barsUpdatedAt) : 0;
        $bars = Cache::remember(
            \App\Services\ScrollbarService::CACHE_KEY_RAW . ':' . $barsVersion,
            \App\Services\ScrollbarService::CACHE_TTL,
            function () {
                return \App\Models\Frontend::where('data_keys', \App\Services\ScrollbarService::DATA_KEY)
                    ->orWhere('data_keys', \App\Services\ScrollbarService::CUSTOM_DATA_KEY)
                    ->orderBy('id', 'asc')
                    ->get();
            }
        );
        $bars = $bars instanceof \Illuminate\Support\Collection ? $bars : collect($bars);
        $now = now()->format('Y-m-d');
        $userLoggedIn = $options['user_logged_in'] ?? (\Illuminate\Support\Facades\Auth::check());
        $currentPage = $options['page'] ?? null;
        if ($currentPage === null && function_exists('request') && request()->route()) {
            $name = request()->route()->getName() ?? '';
            $path = request()->path();
            if (str_contains($name, 'home') || $name === 'frontend.home' || $path === '' || $path === '/') {
                $currentPage = 'home';
            } elseif ($name === 'product.detail' || preg_match('#^product/details/#', $path) || preg_match('#^product/[a-zA-Z0-9][a-zA-Z0-9\-]*-\d+$#', $path)) {
                $currentPage = 'product_detail';
            } elseif ($name === 'products' || $name === 'products.featured' || $name === 'products.best.selling' || $name === 'all.products.filter' || $path === 'all/products') {
                $currentPage = 'all_products';
            } elseif (str_contains($name, 'category') || str_contains($name, 'brand.') || str_contains($name, 'subcategory.') || preg_match('#^(category|brand|subcategory)/#', $path)) {
                $currentPage = 'category';
            } elseif (str_contains($name, 'cart.list') || str_contains($path, 'cart-list')) {
                $currentPage = 'cart';
            } elseif (str_contains($name, 'checkout') || str_contains($path, 'checkout')) {
                $currentPage = 'checkout';
            } elseif (str_contains($name, 'product')) {
                $currentPage = 'product';
            }
        }

        $bars = $bars->filter(function ($bar) use ($position, $now, $userLoggedIn, $currentPage) {
            $dv = $bar->data_values ?? (object) [];
            if (isset($dv->status) && (int) $dv->status !== 1) {
                return false;
            }
            if (isset($dv->visibility) && $dv->visibility === 'private') {
                return false;
            }
            if (!empty($dv->schedule_start) && $now < $dv->schedule_start) {
                return false;
            }
            if (!empty($dv->schedule_end) && $now > $dv->schedule_end) {
                return false;
            }
            $visUsers = $dv->visibility_users ?? 'all';
            if ($visUsers === 'guest' && $userLoggedIn) {
                return false;
            }
            if ($visUsers === 'logged_in' && !$userLoggedIn) {
                return false;
            }
            $visPages = $dv->visibility_pages ?? 'all';
            if ($visPages === 'custom_urls') {
                $rawCustom = (string) ($dv->custom_urls ?? '');
                $lines = preg_split('/\r\n|\r|\n/', $rawCustom);
                $lines = array_values(array_filter(array_map('trim', $lines), function ($v) {
                    return $v !== ''; }));
                if (empty($lines)) {
                    return false;
                }
                $currentUrl = url()->current();
                $currentPath = request()->path();
                $mode = (string) ($dv->custom_url_mode ?? 'contains');
                $matched = false;
                foreach ($lines as $rule) {
                    if ($mode === 'exact' && strcasecmp($currentUrl, $rule) === 0) {
                        $matched = true;
                        break;
                    }
                    if ($mode === 'path' && trim($currentPath, '/') === trim(parse_url($rule, PHP_URL_PATH) ?: $rule, '/')) {
                        $matched = true;
                        break;
                    }
                    if ($mode === 'contains' && (stripos($currentUrl, $rule) !== false || stripos('/' . trim($currentPath, '/'), '/' . trim($rule, '/')) !== false)) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    return false;
                }
            } elseif ($visPages !== 'all' && $currentPage) {
                // Backward compatibility: old "product" works for products listing pages too.
                $productCompat = ($visPages === 'product' && in_array($currentPage, ['product', 'all_products'], true))
                    || ($visPages === 'all_products' && in_array($currentPage, ['all_products', 'product'], true));
                if (!$productCompat && $visPages !== $currentPage) {
                    return false;
                }
            }
            if ($position && (!isset($dv->position) || $dv->position !== $position)) {
                return false;
            }
            return true;
        });
        $bars = $bars->sortBy(function ($b) {
            try {
                $dv = $b->data_values ?? (object) [];
                $order = $dv->display_order ?? 999;
                return is_numeric($order) ? (int) $order : 999;
            } catch (\Throwable $e) {
                return 999;
            }
        })->values();
        return $memo[$memoKey] = $bars;
    } catch (\Throwable $e) {
        return $memo[$memoKey] = collect([]);
    }
}

/**
 * Get visibility reasons for a scroll bar (admin debug overlay).
 * Returns ['visible' => bool, 'reasons' => array of strings].
 */
function getScrollbarVisibilityReasons($bar, array $options = [])
{
    $reasons = [];
    $visible = true;
    $dv = $bar->data_values ?? (object) [];
    $now = now()->format('Y-m-d');
    $userLoggedIn = $options['user_logged_in'] ?? (\Illuminate\Support\Facades\Auth::check());
    $currentPage = $options['page'] ?? null;

    if (isset($dv->status) && (int) $dv->status !== 1) {
        $visible = false;
        $reasons[] = __('Draft (not published)');
    }
    if (isset($dv->visibility) && $dv->visibility === 'private') {
        $visible = false;
        $reasons[] = __('Private visibility');
    }
    if (!empty($dv->schedule_start) && $now < $dv->schedule_start) {
        $visible = false;
        $reasons[] = __('Scheduled start date not reached');
    }
    if (!empty($dv->schedule_end) && $now > $dv->schedule_end) {
        $visible = false;
        $reasons[] = __('Schedule ended');
    }
    $visUsers = $dv->visibility_users ?? 'all';
    if ($visUsers === 'guest' && $userLoggedIn) {
        $visible = false;
        $reasons[] = __('Guests only (you are logged in)');
    }
    if ($visUsers === 'logged_in' && !$userLoggedIn) {
        $visible = false;
        $reasons[] = __('Logged-in only');
    }
    $visPages = $dv->visibility_pages ?? 'all';
    if ($visPages === 'custom_urls') {
        $rawCustom = (string) ($dv->custom_urls ?? '');
        $lines = preg_split('/\r\n|\r|\n/', $rawCustom);
        $lines = array_values(array_filter(array_map('trim', $lines), function ($v) {
            return $v !== ''; }));
        if (empty($lines)) {
            $visible = false;
            $reasons[] = __('Custom URL list is empty');
        } else {
            $currentUrl = function_exists('url') ? url()->current() : '';
            $currentPath = function_exists('request') ? request()->path() : '';
            $mode = (string) ($dv->custom_url_mode ?? 'contains');
            $matched = false;
            foreach ($lines as $rule) {
                if ($mode === 'exact' && strcasecmp($currentUrl, $rule) === 0) {
                    $matched = true;
                    break;
                }
                if ($mode === 'path' && trim($currentPath, '/') === trim(parse_url($rule, PHP_URL_PATH) ?: $rule, '/')) {
                    $matched = true;
                    break;
                }
                if ($mode === 'contains' && stripos($currentUrl, $rule) !== false) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                $visible = false;
                $reasons[] = __('Custom URL filter did not match current URL');
            }
        }
    } elseif ($visPages !== 'all' && $currentPage && $visPages !== $currentPage) {
        $visible = false;
        $reasons[] = __('Page filter') . ': ' . $visPages . ' (current: ' . ($currentPage ?: '—') . ')';
    }
    if ($visible && empty($reasons)) {
        $reasons[] = __('Visible') . ': ' . __('Published') . ', ' . __('schedule OK') . ', ' . __('visibility match');
    }
    return ['visible' => $visible, 'reasons' => $reasons];
}

/**
 * Standard API success response (success / error structure একরকম)
 */
function api_success($data = null, $message = 'Success', $code = 200)
{
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    return response()->json($response, $code);
}

/**
 * Standard API error response
 */
function api_error($message = 'Error', $errors = null, $code = 400)
{
    $response = ['success' => false, 'message' => $message];
    if ($errors !== null) {
        $response['errors'] = is_array($errors) ? $errors : ['detail' => $errors];
    }
    return response()->json($response, $code);
}

/**
 * CDN/Asset URL - use ASSET_URL in .env for CDN (CSS, JS, Image ultra fast)
 */
function cdn_asset($path)
{
    $url = config('app.asset_url');
    if ($url) {
        return rtrim($url, '/') . '/' . ltrim($path, '/');
    }
    return asset($path);
}

/**
 * Feature toggle helper (dot notation): feature_enabled('assets.library_only_mode')
 */
function feature_enabled(string $key, bool $default = false): bool
{
    return (bool) config('features.' . $key, $default);
}

/**
 * Admin Activity Log - কোন admin কী change করেছে track করা
 */
function log_admin_activity(string $action, ?string $model = null, ?int $modelId = null, $oldValues = null, $newValues = null): void
{
    \App\Models\AdminActivityLog::logAction($action, $model, $modelId, $oldValues, $newValues);
}

/**
 * Homepage Sections admin page: show English + Bengali (ইংলিশের পাশাপাশি বাংলা)
 * Usage: {!! lang_en_bn('Save Settings') !!}
 */
function lang_en_bn(string $key): string
{
    static $bn = null;
    if ($bn === null) {
        $bn = [
            'Homepage Sections' => 'হোমপেজ সেকশন',
            'Control homepage sections: Power Zone, Trust, Flash Sale, Social Proof, UX toggles.' => 'হোমপেজ সেকশন নিয়ন্ত্রণ: পাওয়ার জোন, ট্রাস্ট, ফ্ল্যাশ সেল, সোশ্যাল প্রুফ, ইউএক্স।',
            'General Settings' => 'সাধারণ সেটিংস',
            'Trust Section' => 'ট্রাস্ট সেকশন',
            'Quick Services' => 'কুইক সার্ভিস',
            'Promo Banners' => 'প্রমো ব্যানার',
            'Quick Category' => 'কুইক ক্যাটাগরি',
            'Power Zone (below hero banner)' => 'পাওয়ার জোন (হিরো ব্যানারের নিচে)',
            'Enable Power Zone' => 'পাওয়ার জোন চালু',
            'Category Icons Slider' => 'ক্যাটাগরি আইকন স্লাইডার',
            'Flash Deals / Today Deals' => 'ফ্ল্যাশ ডিল / আজকের অফার',
            'Trending Section' => 'ট্রেন্ডিং সেকশন',
            'Trending Now' => 'ট্রেন্ডিং নাউ',
            'Trending Now Products' => 'ট্রেন্ডিং নাউ প্রোডাক্ট',
            'Trending Now Enable' => 'ট্রেন্ডিং নাউ চালু',
            'Trending Now Disable' => 'ট্রেন্ডিং নাউ বন্ধ',
            'Enable Trending Now' => 'ট্রেন্ডিং নাউ চালু করুন',
            'Disable Trending Now' => 'ট্রেন্ডিং নাউ বন্ধ করুন',
            'Show in Trending Now?' => 'ট্রেন্ডিং নাউতে দেখাবেন?',
            'Remove from Trending Now?' => 'ট্রেন্ডিং নাউ থেকে সরাবেন?',
            'Toggle Trending Now?' => 'ট্রেন্ডিং নাউ টগল করবেন?',
            'Quick Service Shortcuts' => 'কুইক সার্ভিস শর্টকাট',
            'Promo / Trust Blocks' => 'প্রমো / ট্রাস্ট ব্লক',
            'Quick Category Boxes' => 'কুইক ক্যাটাগরি বক্স',
            'Flash Sale Title' => 'ফ্ল্যাশ সেল শিরোনাম',
            'Flash Sale End' => 'ফ্ল্যাশ সেল শেষের সময়',
            'Flash Deals Limit' => 'ফ্ল্যাশ ডিল সংখ্যা',
            'Trending Limit' => 'ট্রেন্ডিং সংখ্যা',
            'Top Rated Limit' => 'টপ রেটেড সংখ্যা',
            'Reviews Slider Limit' => 'রিভিউ স্লাইডার সংখ্যা',
            'Trust & Social Proof' => 'ট্রাস্ট ও সোশ্যাল প্রুফ',
            'Trust Section (above footer)' => 'ট্রাস্ট সেকশন (ফুটারের ওপরে)',
            'Social Proof Section' => 'সোশ্যাল প্রুফ সেকশন',
            'Customer Reviews Slider' => 'গ্রাহক রিভিউ স্লাইডার',
            'Top Rated Products' => 'টপ রেটেড প্রোডাক্ট',
            'Recommendations & UX' => 'রিকমেন্ডেশন ও ইউএক্স',
            'Recommended / Similar Products' => 'রিকমেন্ডেড / একই ধরনের প্রোডাক্ট',
            'Recently Viewed' => 'সম্প্রতি দেখা',
            'Sticky Add To Cart' => 'স্টিকি অ্যাড টু কার্ট',
            'Quick View Product' => 'কুইক ভিউ প্রোডাক্ট',
            'Wishlist Popup' => 'উইশলিস্ট পপআপ',
            'Compare Products' => 'প্রোডাক্ট তুলনা',
            'Floating Cart' => 'ফ্লোটিং কার্ট',
            'Conversion Boost' => 'কনভার্শন বূস্ট',
            'Show conversion cues' => 'কনভার্শন কিউ দেখান',
            'Limited Stock Warning' => 'সীমিত স্টক সতর্কতা',
            'Only X Left' => 'মাত্র X টি বাকি',
            'Save Settings' => 'সেটিংস সংরক্ষণ',
            'Trust items above footer: Secure Payment, Fast Delivery, etc.' => 'ফুটারের ওপরে ট্রাস্ট আইটেম: সুরক্ষিত পেমেন্ট, দ্রুত ডেলিভারি ইত্যাদি।',
            'Title' => 'শিরোনাম',
            'Icon' => 'আইকন',
            'Short detail' => 'সংক্ষিপ্ত বর্ণনা',
            'Action' => 'কর্ম',
            'Edit' => 'সম্পাদনা',
            'Delete this item?' => 'এই আইটেম মুছবেন?',
            'Delete' => 'মুছুন',
            'No trust items. Add one below.' => 'কোনো ট্রাস্ট আইটেম নেই। নিচে যোগ করুন।',
            'Add / Edit Trust Item' => 'ট্রাস্ট আইটেম যোগ / সম্পাদনা',
            'URL' => 'ইউআরএল',
            'Save' => 'সংরক্ষণ',
            'Clear' => 'পরিষ্কার',
            'Power Zone shortcuts: Track Order, Support, Return, Coupons.' => 'পাওয়ার জোন শর্টকাট: অর্ডার ট্র্যাক, সাপোর্ট, রিটার্ন, কুপন।',
            'No quick services. Add one below.' => 'কুইক সার্ভিস নেই। নিচে যোগ করুন।',
            'Add / Edit Quick Service' => 'কুইক সার্ভিস যোগ / সম্পাদনা',
            'Promo blocks: Free Shipping, Cash on Delivery, etc.' => 'প্রমো ব্লক: বিনামূল্যে শিপিং, ক্যাশ অন ডেলিভারি ইত্যাদি।',
            'Subtitle' => 'সাবটাইটেল',
            'No promo banners. Add one below.' => 'প্রমো ব্যানার নেই। নিচে যোগ করুন।',
            'Add / Edit Promo Banner' => 'প্রমো ব্যানার যোগ / সম্পাদনা',
            'Square boxes below banner: Hot Deals, Top Selling, New Arrival, Category or URL.' => 'ব্যানারের নিচে বক্স: হট ডিল, টপ সেলিং, নিউ অ্যারাইভাল, ক্যাটাগরি বা ইউআরএল।',
            'Link Type' => 'লিংক টাইপ',
            'No quick category boxes. Add below.' => 'কুইক ক্যাটাগরি বক্স নেই। নিচে যোগ করুন।',
            'Add / Edit Quick Category Box' => 'কুইক ক্যাটাগরি বক্স যোগ / সম্পাদনা',
            'Hot Deals' => 'হট ডিল',
            'Top Selling' => 'টপ সেলিং',
            'New Arrival' => 'নিউ অ্যারাইভাল',
            'Featured' => 'ফিচার্ড',
            'Discount & Offers' => 'ডিসকাউন্ট ও অফার',
            'Specific Category' => 'নির্দিষ্ট ক্যাটাগরি',
            'Custom URL' => 'কাস্টম ইউআরএল',
            'Category' => 'ক্যাটাগরি',
            'Select' => 'নির্বাচন',
            'Delete?' => 'মুছবেন?',
            'Power Zone, trust, promos, sliders — one place.' => 'পাওয়ার জোন, ট্রাস্ট, প্রমো, স্লাইডার — এক পেজে।',
            'How rows work' => 'রো কীভাবে কাজ করে',
            'Today Deal on product' => 'প্রোডাক্টে টুডে ডিল',
            'Hot Deal flag' => 'হট ডিল চিহ্ন',
            'Featured on product' => 'প্রোডাক্টে ফিচার্ড',
            'Auto list' => 'স্বয়ংক্রিয় তালিকা',
            'Trending + sales' => 'ট্রেন্ডিং + বিক্রয়',
            'sale_count from orders' => 'অর্ডার থেকে sale_count',
            'Active + image' => 'সক্রিয় + ছবি',
            'General' => 'সাধারণ',
            'Trust' => 'ট্রাস্ট',
            'Shortcuts' => 'শর্টকাট',
            'Promo' => 'প্রমো',
            'Quick boxes' => 'কুইক বক্স',
            'Sliders' => 'স্লাইডার',
            'Flash sale' => 'ফ্ল্যাশ সেল',
            'Product Slider' => 'প্রোডাক্ট স্লাইডার',
            'Auto Scroll' => 'অটো স্ক্রল',
            'Enable Auto Scroll' => 'অটো স্ক্রল চালু',
            'Scroll Interval (seconds)' => 'স্ক্রল বিরতি (সেকেন্ড)',
            'Scroll Animation Speed (ms)' => 'অ্যানিমেশন গতি (মি.সে.)',
            'Products Per Row' => 'প্রতি সারিতে প্রোডাক্ট',
            'Desktop' => 'ডেস্কটপ',
            'Tablet' => 'ট্যাবলেট',
            'Mobile' => 'মোবাইল',
            'Per-section scroll speed (seconds)' => 'প্রতি সেকশন স্ক্রল গতি (সেকেন্ড)',
            'Each product row can have its own auto-scroll interval. Default used if empty.' => 'প্রতি সারির আলাদা স্ক্রল বিরতি। খালি থাকলে ডিফল্ট।',
            'Save Product Slider Settings' => 'স্লাইডার সেটিংস সংরক্ষণ',
            'New Arrivals' => 'নিউ অ্যারাইভাল',
            'Recommended' => 'রিকমেন্ডেড',
            'Best Selling' => 'বেস্ট সেলিং',
            'Control automatic horizontal product scrolling (right → left) on home, category, product detail and dashboard sections.' => 'হোম, ক্যাটাগরি, ডিটেইল ও ড্যাশবোর্ডে আনুভূমিক অটো স্ক্রল নিয়ন্ত্রণ।',
            // System Configuration (setting/system-configuration)
            'System Configuration' => 'সিস্টেম কনফিগারেশন',
            'Security & Access' => 'নিরাপত্তা ও অ্যাক্সেস',
            'Force SSL' => 'ফোর্স এসএসএল',
            'Force Secure Password' => 'সুরক্ষিত পাসওয়ার্ড বাধ্যতামূলক',
            'User Registration' => 'ইউজার রেজিস্ট্রেশন',
            'Agree Policy' => 'নীতিমালায় সম্মতি',
            'Verification' => 'যাচাইকরণ',
            'Email Verification' => 'ইমেইল ভেরিফিকেশন',
            'Mobile Verification' => 'মোবাইল ভেরিফিকেশন',
            'Notifications' => 'নোটিফিকেশন',
            'Email Notification' => 'ইমেইল নোটিফিকেশন',
            'SMS Notification' => 'এসএমএস নোটিফিকেশন',
            'Product, Language & UX' => 'প্রোডাক্ট, ভাষা ও ইউএক্স',
            'Display Stock Quantity' => 'স্টক সংখ্যা দেখান',
            'Language Option' => 'ভাষা অপশন',
            'Floating Login' => 'ফ্লোটিং লগইন',
            'Floating Register' => 'ফ্লোটিং রেজিস্ট্রেশন',
            'Admin Online (Green Light)' => 'এডমিন অনলাইন (সবুজ সিগন্যাল)',
            'Save Configuration' => 'কনফিগারেশন সংরক্ষণ',
            'Enable' => 'চালু',
            'Disable' => 'বন্ধ',
            'Online' => 'অনলাইন',
            'Offline' => 'অফলাইন',
        ];
    }
    $en = __($key);
    $b = $bn[$key] ?? '';
    return $en . ($b ? ' <span class="text-muted small">(' . $b . ')</span>' : '');
}

/**
 * Update a key in .env file. Runs config:clear and cache:clear after update.
 * Only use from admin context; log changes separately via SecurityAuditLog::log().
 *
 * @param string $key   e.g. ADMIN_PREFIX, APP_DEBUG
 * @param string $value New value (will be written as-is; no quotes added)
 * @return bool True if .env was updated, false on failure
 */
function updateEnv(string $key, string $value): bool
{
    $path = base_path('.env');
    if (!file_exists($path) || !is_writable($path)) {
        return false;
    }

    $content = file_get_contents($path);
    $line = $key . '=' . $value;
    $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';

    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $line, $content, 1);
    } else {
        $content .= "\n" . $line . "\n";
    }

    if (file_put_contents($path, $content) === false) {
        return false;
    }

    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
    } catch (\Throwable $e) {
        // Continue; env was written
    }

    return true;
}

/**
 * Log user activity for admin reports (who did what, from where, when).
 * Uses ActivityLoggerService; safe to call from any controller.
 * @param int|null $userIdOverride If set (e.g. from gateway callback), use this instead of auth()->id()
 */
function activity_log(string $actionType, ?string $description = null, ?string $modelType = null, ?int $modelId = null, ?int $userIdOverride = null): void
{
    \App\Services\ActivityLoggerService::log($actionType, $description, $modelType, $modelId, request(), $userIdOverride);
}

/**
 * Get current "page" slug for offer timers and popup ads from route name.
 * Covers all public pages: home, contact, track/order, all/products, wish-list, compare, cart, user/order, checkout, deposit, product/details, policy, featured, hot-deal, best-selling, etc.
 * @return string One of: home, cart, checkout, product_detail, category, user_dashboard, search, contact, wishlist, other
 */
function get_offer_timer_page_from_route(): string
{
    $name = request()->route() ? request()->route()->getName() : '';
    if (str_contains($name, 'cart.list')) {
        return 'cart';
    }
    if (str_contains($name, 'checkout')) {
        return 'checkout';
    }
    if (str_contains($name, 'product.detail')) {
        return 'product_detail';
    }
    if (
        $name === 'products' || str_contains($name, 'category.products') || str_contains($name, 'subcategory.products')
        || str_contains($name, 'brand.products') || str_contains($name, 'all.products')
        || str_contains($name, 'products.featured') || str_contains($name, 'product.hot.deal')
        || str_contains($name, 'products.best.selling') || str_contains($name, 'products.new')
        || str_contains($name, 'product.today.deal') || str_contains($name, 'products.discount')
    ) {
        return 'category';
    }
    if (str_starts_with($name, 'user.') && $name !== 'user.login' && $name !== 'user.register') {
        return 'user_dashboard';
    }
    if (str_contains($name, 'wish.list') || str_contains($name, 'wishlist')) {
        return 'wishlist';
    }
    if (str_contains($name, 'search') || str_contains($name, 'product.search')) {
        return 'search';
    }
    if (str_contains($name, 'contact')) {
        return 'contact';
    }
    if ($name === 'home' || $name === '') {
        return 'home';
    }
    return 'other';
}

/**
 * Get offer timers to display on a given page/position (for discount/special offer countdown bars).
 * @param string $page home|cart|checkout|product_detail|category|user_dashboard
 * @param string $position header|below_header|cart_top|checkout_top|product_detail|category_top|content_top|content_bottom|user_dashboard_top|floating
 * @param int|null $productId For product_detail page
 * @param int|null $categoryId For category page
 * @return \Illuminate\Support\Collection
 */
function get_offer_timers_for_display(string $page, string $position, ?int $productId = null, ?int $categoryId = null)
{
    $cacheKey = 'offer_timers.display:' . md5(json_encode([
        'page' => $page,
        'position' => $position,
        'productId' => $productId,
        'categoryId' => $categoryId,
    ]));
    $timers = Cache::remember($cacheKey, 60, function () use ($page, $position) {
        return \App\Models\OfferTimer::active()->ordered()->forPage($page)->forPosition($position)->get();
    });
    $timers = $timers instanceof \Illuminate\Support\Collection ? $timers : collect($timers);
    return $timers->filter(function ($t) use ($productId, $categoryId) {
        if ($productId !== null && !$t->isVisibleForProduct($productId)) {
            return false;
        }
        if ($categoryId !== null && !$t->isVisibleForCategory($categoryId)) {
            return false;
        }
        return true;
    })->values();
}

/**
 * Get popup ads (modal, user can close) to display on the current public page.
 * @param string $page home|cart|checkout|product_detail|category|user_dashboard|search|contact|wishlist|other
 * @return \Illuminate\Support\Collection
 */
function get_popup_ads_for_display(string $page = 'home')
{
    $ads = get_active_popup_ads_cached();
    return $ads->filter(function ($ad) use ($page) {
        if ($ad->getDisplayType() !== \App\Models\PopupAd::DISPLAY_POPUP) {
            return false;
        }
        $pages = $ad->show_on_pages;
        if ($pages === null || !is_array($pages) || count($pages) === 0) {
            return true;
        }
        if (in_array('all', $pages, true)) {
            return true;
        }
        return in_array($page, $pages, true);
    })->values();
}

/**
 * Get inline ads (stay on page, no close – e.g. sidebar on payment/dashboard) for a placement.
 * @param string $page home|cart|checkout|product_detail|category|user_dashboard|search|contact|wishlist|other
 * @param string $placement sidebar_right|sidebar_left|content_top|content_bottom
 * @return \Illuminate\Support\Collection
 */
function get_inline_ads_for_display(string $page, string $placement)
{
    $ads = get_active_popup_ads_cached();
    return $ads->filter(function ($ad) use ($page, $placement) {
        if ($ad->getDisplayType() !== \App\Models\PopupAd::DISPLAY_INLINE) {
            return false;
        }
        if ($ad->getInlinePlacement() !== $placement) {
            return false;
        }
        $pages = $ad->show_on_pages;
        if ($pages === null || !is_array($pages) || count($pages) === 0) {
            return true;
        }
        if (in_array('all', $pages, true)) {
            return true;
        }
        return in_array($page, $pages, true);
    })->values();
}

/**
 * Shared source for active popup/inline ads to avoid duplicate queries in a single request.
 */
function get_active_popup_ads_cached()
{
    static $memo = null;
    if ($memo instanceof \Illuminate\Support\Collection) {
        return $memo;
    }

    $memo = Cache::remember('popup_ads.active.ordered', 60, function () {
        return \App\Models\PopupAd::active()->ordered()->get();
    });

    return $memo instanceof \Illuminate\Support\Collection ? $memo : collect($memo);
}

/**
 * Get custom site messages for the current request (public/user pages, optional route filter).
 * Used by partials.custom_site_messages to show admin-configured messages.
 *
 * @return \Illuminate\Support\Collection
 */
function getCustomSiteMessages()
{
    $items = \App\Models\Frontend::where('data_keys', 'custom_message.element')
        ->orderByRaw('CAST(JSON_UNQUOTE(JSON_EXTRACT(data_values, "$.display_order")) AS UNSIGNED) ASC')
        ->orderBy('id')
        ->get();

    $isUser = \Illuminate\Support\Facades\Auth::guard('web')->check();
    $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();

    return $items->filter(function ($row) use ($isUser, $currentRoute) {
        $dv = $row->data_values;
        if (($dv->status ?? 0) != \App\Constants\Status::ENABLE) {
            return false;
        }
        $showOn = $dv->show_on ?? 'all';
        if ($showOn === 'public_only' && $isUser) {
            return false;
        }
        if ($showOn === 'user_only' && !$isUser) {
            return false;
        }
        $routeFilter = trim($dv->route_filter ?? '');
        if ($routeFilter !== '') {
            $allowed = array_map('trim', explode(',', $routeFilter));
            if (!in_array($currentRoute, $allowed, true)) {
                return false;
            }
        }
        return true;
    })->values();
}
