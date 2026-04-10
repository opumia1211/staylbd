<?php

namespace App\Services;

/**
 * Professional Ecommerce Banner System – constants and helpers.
 * Single source for validation, storage paths, and banner content schema.
 */
class BannerService
{
    public const DATA_KEY_ELEMENT = 'banner.element';
    public const DATA_KEY_CONTENT = 'banner.content';

    /** Max file size: 5MB */
    public const MAX_FILE_SIZE = 5 * 1024 * 1024;

    /** Allowed extensions for image/video banner */
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'mp4'];

    /** MIME types for validation (extension => mimes) */
    public const MIME_MAP = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
        'mp4' => ['video/mp4'],
    ];

    /** Base path: project root assets (staylbd/assets) so URL http://localhost/staylbd/assets/... is served correctly. */
    public const UPLOAD_BASE = 'assets/images/frontend/banner';

    /** Row split promo images (large slider + small card) — same root assets pattern as hero banners. */
    public const ROW_SPLIT_RELATIVE = 'assets/images/frontend/row_split_banner';

    /** Subfolders: desktop = main, mobile = optional separate, thumb = preview */
    public const DESKTOP_DIR = 'desktop';
    public const MOBILE_DIR = 'mobile';
    public const THUMB_DIR = 'thumb';

    /** Layout types for banner */
    public const LAYOUT_TYPES = [
        'hero_full_width',
        'centered_content',
        'left_content',
        'right_content',
        'split_banner',
        'image_only',
        'video_banner',
    ];

    /** Visibility options */
    public const VISIBILITY_OPTIONS = [
        'public',
        'logged_in_only',
        'guest_only',
        'campaign_only',
    ];

    /** Default banner content schema (text overlay builder) */
    public static function defaultBannerContent(): array
    {
        return [
            'title' => '',
            'subtitle' => '',
            'description' => '',
            'badge' => '',
            'button_text' => '',
            'button_url' => '',
            'icon' => '',
            'overlay_color' => 'rgba(0,0,0,0.3)',
            'overlay_opacity' => '0.3',
            'title_font_size' => '',
            'title_font_weight' => '700',
            'title_align' => 'center',
            'text_color' => '#ffffff',
        ];
    }

    /** Full path for banner upload. Uses project root (base_path('../assets')) so XAMPP serves /staylbd/assets/... correctly. */
    public static function uploadPath(string $sub = 'desktop'): string
    {
        $base = base_path('../' . self::UPLOAD_BASE);
        $path = $base . '/' . $sub;
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
        return $path;
    }

    /** Physical directory for homepage row split banner uploads (project root assets). */
    public static function rowSplitUploadPath(): string
    {
        $path = base_path('../' . self::ROW_SPLIT_RELATIVE);
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }

        return $path;
    }

    /**
     * Public URL for row split image (served via row-split-banner route — works with subdirectory installs).
     */
    public static function rowSplitImageUrl(string $filename): string
    {
        $filename = basename($filename);
        if ($filename === '') {
            return '';
        }
        $routeUrl = route('row.split.image', ['filename' => $filename]);
        if (request()->getBasePath() !== '' && strpos($routeUrl, request()->getBasePath()) === false) {
            return rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/') . '/row-split-banner/' . $filename;
        }

        return $routeUrl;
    }

    /** Relative path for asset URL (e.g. assets/images/frontend/banner/desktop/) */
    public static function assetPath(string $sub = 'desktop'): string
    {
        return self::UPLOAD_BASE . '/' . $sub;
    }

    /** Check if file extension is allowed */
    public static function isAllowedExtension(string $ext): bool
    {
        return in_array(strtolower($ext), self::ALLOWED_EXTENSIONS, true);
    }

    /** Get MIME types for extension (for validation) */
    public static function getMimesForExtension(string $ext): array
    {
        return self::MIME_MAP[strtolower($ext)] ?? [];
    }

    /** Asset URL for banner image. Uses Laravel route; subdirectory (e.g. /staylbd) এ কাজ করার জন্য request base ব্যবহার। */
    public static function bannerImageUrl($filename): string
    {
        if (is_array($filename)) {
            $filename = $filename['desktop'] ?? $filename['image'] ?? '';
        } elseif (is_object($filename)) {
            $filename = $filename->desktop ?? $filename->image ?? '';
        }
        if ($filename === '' || !is_string($filename)) {
            return '';
        }
        $filename = trim($filename);
        if ($filename === '') {
            return '';
        }
        $filename = basename($filename);
        $routeUrl = route('banner.image', ['filename' => $filename]);
        // যখন অ্যাপ সাবডিরেক্টরিতে (যেমন /staylbd) চলে, route() শুধু APP_URL দিয়ে তৈরি করে; request base দিয়ে আবার বানানো যাতে ইমেজ লোড হয়
        if (request()->getBasePath() !== '' && strpos($routeUrl, request()->getBasePath()) === false) {
            return rtrim(request()->getSchemeAndHttpHost() . request()->getBasePath(), '/') . '/banner-image/' . $filename;
        }
        return $routeUrl;
    }

    /** Thumb URL for admin preview (thumb_ prefix in thumb folder). */
    public static function thumbImageUrl(string $filename): string
    {
        if ($filename === '') {
            return '';
        }
        $thumbFile = 'thumb_' . $filename;
        $thumb = self::UPLOAD_BASE . '/' . self::THUMB_DIR . '/' . $thumbFile;
        $projectRootThumb = base_path('../' . $thumb);
        if (file_exists($projectRootThumb) && is_file($projectRootThumb)) {
            return asset($thumb);
        }
        $publicThumb = public_path($thumb);
        if (file_exists($publicThumb) && is_file($publicThumb)) {
            return asset($thumb);
        }
        return self::bannerImageUrl($filename);
    }

    /** Mobile image URL if separate file exists; otherwise desktop URL */
    public static function mobileImageUrl(?string $mobileFilename, string $desktopFilename): string
    {
        if ($mobileFilename !== null && $mobileFilename !== '') {
            $mobile = self::UPLOAD_BASE . '/' . self::MOBILE_DIR . '/' . $mobileFilename;
            $projectRootMobile = base_path('../' . $mobile);
            if (file_exists($projectRootMobile) && is_file($projectRootMobile)) {
                return asset($mobile);
            }
            $publicMobile = public_path($mobile);
            if (file_exists($publicMobile) && is_file($publicMobile)) {
                return asset($mobile);
            }
        }
        return self::bannerImageUrl($desktopFilename);
    }

    /** Max width for generated thumb (admin grid preview) */
    public const THUMB_MAX_WIDTH = 400;

    /** Max width for optional mobile variant */
    public const MOBILE_MAX_WIDTH = 768;
}
