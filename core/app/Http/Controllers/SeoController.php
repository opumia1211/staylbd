<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Brand;
use App\Models\Frontend;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Lightweight SEO: sitemap.xml and robots.txt for top search engine visibility.
 * Output is cached and minimal for fast crawl.
 */
class SeoController extends Controller
{
    /** Cache TTL in seconds (1 hour). */
    private const SITEMAP_CACHE_TTL = 3600;

    /**
     * Dynamic sitemap.xml: home, products, categories, subcategories, brands, policy pages.
     * Google/Bing recommend lastmod and changefreq for better indexing.
     */
    public function sitemap()
    {
        $xml = Cache::remember('seo.sitemap.xml', self::SITEMAP_CACHE_TTL, function () {
            $baseUrl = rtrim(url('/'), '/');
            $now = now()->toW3cString();

            $urls = [];

            // Homepage
            $urls[] = [
                'loc'        => $baseUrl . '/',
                'lastmod'    => $now,
                'changefreq' => 'daily',
                'priority'   => '1.0',
            ];

            // Static key pages (high priority for discovery)
            $staticPages = [
                ['path' => '/all/products', 'priority' => '0.95', 'changefreq' => 'daily'],
                ['path' => '/category/all', 'priority' => '0.9', 'changefreq' => 'weekly'],
                ['path' => '/brand/all', 'priority' => '0.85', 'changefreq' => 'weekly'],
                ['path' => '/contact', 'priority' => '0.7', 'changefreq' => 'monthly'],
                ['path' => '/track/order', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ];
            foreach ($staticPages as $p) {
                $urls[] = [
                    'loc'        => $baseUrl . $p['path'],
                    'lastmod'    => $now,
                    'changefreq' => $p['changefreq'],
                    'priority'   => $p['priority'],
                ];
            }

            // Products (canonical: /product/{slug})
            $products = Product::where('status', 1)->select(['id', 'name', 'slug', 'updated_at'])->orderBy('id')->get();
            foreach ($products as $product) {
                $pSlug = trim((string) ($product->slug ?? ''));
                if ($pSlug === '' || !preg_match('/-\d+$/', $pSlug)) {
                    $pSlug = \App\Models\Product::buildShortSlugForProduct($product);
                    $product->slug = $pSlug;
                    $product->saveQuietly();
                }
                $urls[] = [
                    'loc'        => $baseUrl . '/product/' . $pSlug,
                    'lastmod'    => $product->updated_at ? $product->updated_at->toW3cString() : $now,
                    'changefreq' => 'weekly',
                    'priority'   => '0.9',
                ];
            }

            // Categories
            $categories = Category::where('status', 1)->select(['id', 'name', 'updated_at'])->orderBy('id')->get();
            foreach ($categories as $cat) {
                $slug = slug($cat->name);
                $urls[] = [
                    'loc'        => $baseUrl . '/category/product/' . $slug . '/' . $cat->id,
                    'lastmod'    => $cat->updated_at ? $cat->updated_at->toW3cString() : $now,
                    'changefreq' => 'weekly',
                    'priority'   => '0.85',
                ];
            }

            // Subcategories
            if (class_exists(Subcategory::class)) {
                $subs = Subcategory::where('status', 1)->select(['id', 'name', 'updated_at'])->orderBy('id')->get();
                foreach ($subs as $sub) {
                    $slug = slug($sub->name);
                    $urls[] = [
                        'loc'        => $baseUrl . '/subcategory/product/' . $slug . '/' . $sub->id,
                        'lastmod'    => $sub->updated_at ? $sub->updated_at->toW3cString() : $now,
                        'changefreq' => 'weekly',
                        'priority'   => '0.8',
                    ];
                }
            }

            // Brands
            if (class_exists(Brand::class)) {
                $brands = Brand::where('status', 1)->select(['id', 'name', 'updated_at'])->orderBy('id')->get();
                foreach ($brands as $brand) {
                    $slug = slug($brand->name);
                    $urls[] = [
                        'loc'        => $baseUrl . '/brand/product/' . $slug . '/' . $brand->id,
                        'lastmod'    => $brand->updated_at ? $brand->updated_at->toW3cString() : $now,
                        'changefreq' => 'weekly',
                        'priority'   => '0.8',
                    ];
                }
            }

            // Policy pages from Frontend
            $policies = Frontend::where('data_keys', 'policy_pages.element')->get();
            foreach ($policies as $p) {
                $vals = $p->data_values ?? (object)[];
                $id = $p->id;
                $slug = slug($vals->title ?? 'policy');
                $urls[] = [
                    'loc'        => $baseUrl . '/policy/' . $slug . '/' . $id,
                    'lastmod'    => $p->updated_at ? $p->updated_at->toW3cString() : $now,
                    'changefreq' => 'monthly',
                    'priority'   => '0.5',
                ];
            }

            return $this->buildSitemapXml($urls);
        });

        return response($xml, 200, [
            'Content-Type'        => 'application/xml; charset=utf-8',
            'Cache-Control'       => 'public, max-age=3600',
            'X-Robots-Tag'        => 'all',
        ]);
    }

    private function buildSitemapXml(array $urls): string
    {
        $base = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        $out = $base;
        foreach ($urls as $u) {
            $out .= '  <url>' . "\n"
                . '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8') . '</loc>' . "\n"
                . '    <lastmod>' . ($u['lastmod'] ?? '') . '</lastmod>' . "\n"
                . '    <changefreq>' . ($u['changefreq'] ?? 'weekly') . '</changefreq>' . "\n"
                . '    <priority>' . ($u['priority'] ?? '0.5') . '</priority>' . "\n"
                . '  </url>' . "\n";
        }
        $out .= '</urlset>';
        return $out;
    }

    /**
     * robots.txt: allow all, point to sitemap, optional disallow for admin/auth.
     */
    public function robots()
    {
        $sitemapUrl = rtrim(url('/'), '/') . '/sitemap.xml';
        $txt = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /' . ltrim(config('admin.prefix', 'admin'), '/') . '/',
            'Disallow: /user/',
            'Disallow: /api/',
            'Disallow: /cart-list/',
            'Disallow: /wish-list/',
            'Disallow: /compare',
            'Sitemap: ' . $sitemapUrl,
            '',
        ]);

        return response($txt, 200, [
            'Content-Type'  => 'text/plain; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
