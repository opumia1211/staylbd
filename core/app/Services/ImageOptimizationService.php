<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageOptimizationService
{
    /** Default high quality (92) for clear, professional output – user-facing images. */
    public const QUALITY_HIGH = 92;

    /**
     * Convert and optimize image to WebP format (high quality, clear output).
     *
     * @param string $imagePath Original image path
     * @param int $quality Quality 0-100 (92 = high clarity, still light)
     * @return string|false WebP image path or false on failure
     */
    public function convertToWebP($imagePath, $quality = 92)
    {
        try {
            if (!file_exists($imagePath)) {
                return false;
            }

            $pathInfo = pathinfo($imagePath);
            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

            if (strtolower($pathInfo['extension'] ?? '') === 'webp') {
                return $imagePath;
            }

            $image = Image::make($imagePath);
            $width = $image->width();
            $height = $image->height();

            if ($width > 2000 || $height > 2000) {
                $image->resize(2000, 2000, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            $image->encode('webp', min(100, max(1, $quality)))->save($webpPath);
            return $webpPath;

        } catch (\Exception $e) {
            Log::error('WebP conversion failed', ['image' => $imagePath, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Create responsive image sizes
     * 
     * @param string $imagePath Original image path
     * @param array $sizes Array of sizes ['thumbnail' => 150, 'medium' => 500, 'large' => 1000]
     * @return array Array of generated image paths
     */
    public function createResponsiveSizes($imagePath, $sizes = [])
    {
        $defaultSizes = [
            'thumbnail' => 150,
            'medium' => 500,
            'large' => 1000,
        ];

        $sizes = array_merge($defaultSizes, $sizes);
        $generated = [];

        try {
            if (!file_exists($imagePath)) {
                return [];
            }

            $pathInfo = pathinfo($imagePath);
            $image = Image::make($imagePath);

            foreach ($sizes as $sizeName => $maxDimension) {
                $resizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $sizeName . '.webp';

                $resized = clone $image;
                $resized->resize($maxDimension, $maxDimension, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                $resized->encode('webp', 85)->save($resizedPath);
                $generated[$sizeName] = $resizedPath;
            }

            return $generated;

        } catch (\Exception $e) {
            Log::error('Responsive image creation failed', [
                'image' => $imagePath,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Optional sharpening for clearer look (professional quality). Use sparingly.
     */
    public function sharpenImage($image, int $amount = 1)
    {
        if ($amount < 1) {
            return $image;
        }
        try {
            if (method_exists($image, 'sharpen')) {
                return $image->sharpen($amount);
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return $image;
    }

    /**
     * Optimize existing image: high quality encode + optional sharpen for clarity.
     *
     * @param string $imagePath Image path
     * @param int $quality Quality 0-100 (92 = high clarity)
     * @return bool Success status
     */
    public function optimizeImage($imagePath, $quality = 92)
    {
        try {
            if (!file_exists($imagePath)) {
                return false;
            }

            $image = Image::make($imagePath);
            $image = $this->sharpenImage($image, 1);

            $pathInfo = pathinfo($imagePath);
            $extension = strtolower($pathInfo['extension'] ?? '');

            $q = min(100, max(1, (int) $quality));
            switch ($extension) {
                case 'jpg':
                case 'jpeg':
                    $image->encode('jpg', $q)->save($imagePath);
                    break;
                case 'png':
                    $image->encode('png')->save($imagePath);
                    break;
                case 'webp':
                    $image->encode('webp', $q)->save($imagePath);
                    break;
                default:
                    return true;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Image optimization failed', ['image' => $imagePath, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get optimized image URL with WebP fallback
     *
     * @param string $imagePath Original image path
     * @return array ['webp' => 'path/to/image.webp', 'fallback' => 'path/to/image.jpg']
     */
    public function getOptimizedImageUrls($imagePath)
    {
        $pathInfo = pathinfo($imagePath);
        $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';

        return [
            'webp' => file_exists($webpPath) ? $webpPath : null,
            'fallback' => file_exists($imagePath) ? $imagePath : null,
        ];
    }

    /**
     * Full pipeline: optimize (high quality + sharpen) then create WebP. SVG kept as-is; raster → WebP.
     * Quality 92 for clear, professional output (equivalent to “120–140” clarity target).
     *
     * @param string $fullPath Full filesystem path to the image
     * @param int $quality 1–100 (92 = high clarity, default)
     * @return bool True if optimization ran or format skipped safely
     */
    public function optimizeProductImage(string $fullPath, int $quality = 92): bool
    {
        if (!is_string($fullPath) || $fullPath === '' || !file_exists($fullPath) || !is_file($fullPath)) {
            return false;
        }
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION) ?? '');
        if (in_array($ext, ['svg', 'gif', 'avif'], true)) {
            return true; // SVG/GIF/AVIF: keep as uploaded (vector, animation, modern raster)
        }
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return true;
        }
        try {
            $q = min(100, max(1, $quality));
            $this->optimizeImage($fullPath, $q);
            $this->convertToWebP($fullPath, $q);
            return true;
        } catch (\Throwable $e) {
            Log::warning('Image optimization skipped', ['path' => $fullPath, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
