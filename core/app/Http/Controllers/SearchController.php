<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Frontend;
use App\Models\Product;
use App\Models\SearchLog;
use App\Models\Subcategory;
use App\Services\ImageOptimizationService;
use App\Services\UniversalSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    protected UniversalSearchService $searchService;

    public function __construct(UniversalSearchService $searchService)
    {
        $this->searchService = $searchService;
    }
    /**
     * Comprehensive search across entire website
     * Searches: Products, Categories, Brands, Pages, Footer content, etc.
     */
    public function universalSearch(Request $request)
    {
        try {
            $query = trim($request->get('search', $request->get('q', '')));
            
            if (empty($query) || strlen(trim($query)) < 1) {
                return response()->json([
                    'success' => true,
                    'query' => '',
                    'results' => [
                        'products' => [],
                        'categories' => [],
                        'brands' => [],
                        'pages' => [],
                        'subcategories' => [],
                        'did_you_mean' => [],
                        'total' => 0
                    ],
                    'total' => 0
                ]);
            }

            $cacheKey = 'universal_search.' . md5($query);
            $results = Cache::remember($cacheKey, 180, function () use ($query) {
                return $this->performSearch($query);
            });

            $this->logSearch($query, $results['total'] ?? 0, 'universal');
            activity_log(\App\Models\UserActivityLog::SEARCH_TEXT, 'Search: ' . $query, null, null);

            return response()->json([
                'success' => true,
                'query' => $query,
                'results' => $results,
                'total' => $results['total'] ?? 0
            ]);
        } catch (\Exception $e) {
            \Log::error('Search Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Popular search terms (last 30 days, universal text search) for header discovery UI.
     * Cached with event invalidation when new universal searches are logged.
     */
    public function trendingKeywords()
    {
        $keywords = Cache::remember('search.trending.keywords.v1', 600, function () {
            if (!Schema::hasTable('user_search_logs')) {
                return [];
            }

            return SearchLog::query()
                ->where('created_at', '>=', now()->subDays(30))
                ->where('source', 'universal')
                ->whereRaw('CHAR_LENGTH(TRIM(query)) >= 2')
                ->selectRaw('`query`, COUNT(*) as cnt')
                ->groupBy('query')
                ->orderByDesc('cnt')
                ->limit(12)
                ->pluck('query')
                ->map(fn ($q) => trim((string) $q))
                ->filter()
                ->unique()
                ->values()
                ->all();
        });

        return response()->json([
            'success' => true,
            'keywords' => $keywords,
        ]);
    }

    /**
     * Perform comprehensive search - everything on site. Uses advanced terms (typo + i18n).
     */
    private function performSearch($query)
    {
        $searchTerms = $this->searchService->allSearchTerms($query);
        if (empty($searchTerms)) {
            $searchTerms = [trim($query)];
        }
        $results = [
            'products' => [],
            'categories' => [],
            'brands' => [],
            'pages' => [],
            'subcategories' => [],
            'did_you_mean' => [],
            'total' => 0
        ];

        $like = function ($term) {
            return $this->searchService->likeWrap($term);
        };

        // Search Products - all terms (1+ char), safe LIKE
        try {
            $products = Product::where(function($q) use ($searchTerms, $like) {
                    foreach ($searchTerms as $term) {
                        $pattern = $like($term);
                        $q->orWhere('products.name', 'LIKE', $pattern)
                          ->orWhere('products.description', 'LIKE', $pattern)
                          ->orWhere('products.summary', 'LIKE', $pattern)
                          ->orWhere('products.product_sku', 'LIKE', $pattern);
                    }
                    foreach ($searchTerms as $term) {
                        $pattern = $like($term);
                        $q->orWhereHas('category', fn($c) => $c->where('categories.name', 'LIKE', $pattern))
                          ->orWhereHas('brand', fn($b) => $b->where('brands.name', 'LIKE', $pattern))
                          ->orWhereHas('subcategory', fn($s) => $s->where('subcategories.name', 'LIKE', $pattern));
                    }
                })
                ->where('products.status', Status::ENABLE)
                ->with(['category', 'brand', 'subcategory'])
                ->limit(25)
                ->get()
            ->map(function($product) {
                try {
                    return [
                        'id' => $product->id ?? 0,
                        'name' => $product->name ?? '',
                        'slug' => trim((string) ($product->slug ?? '')) ?: \App\Models\Product::buildShortSlugForProduct($product),
                        'image' => method_exists($product, 'imageShow') ? $product->imageShow() : '/assets/images/default.png',
                        'price' => $product->price ?? 0,
                        'url' => product_detail_url($product),
                        'type' => 'product',
                        'category' => $product->category->name ?? '',
                        'brand' => $product->brand->name ?? ''
                    ];
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter(function($item) {
                return $item !== null;
            });

        $results['products'] = $products->toArray();
        } catch (\Exception $e) {
            $results['products'] = [];
        }

        // Search Categories - safe LIKE, 1+ char
        try {
            $categories = Category::where(function($q) use ($searchTerms, $like) {
                    foreach ($searchTerms as $term) {
                        $q->orWhere('categories.name', 'LIKE', $like($term));
                    }
                })
                ->where('categories.status', Status::ENABLE)
                ->limit(20)
                ->get()
                ->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name ?? '',
                        'slug' => slug($category->name ?? ''),
                        'url' => route('category.products', [slug($category->name ?? ''), $category->id]),
                        'type' => 'category'
                    ];
                });

            $results['categories'] = $categories->toArray();
        } catch (\Exception $e) {
            $results['categories'] = [];
        }

        // Search Subcategories
        try {
            $subcategories = Subcategory::where(function($q) use ($searchTerms, $like) {
                    foreach ($searchTerms as $term) {
                        $q->orWhere('subcategories.name', 'LIKE', $like($term));
                    }
                })
                ->where('subcategories.status', Status::ENABLE)
                ->whereHas('category', fn($c) => $c->where('categories.status', Status::ENABLE))
                ->limit(20)
                ->get()
                ->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'name' => $sub->name ?? '',
                        'slug' => slug($sub->name ?? ''),
                        'url' => route('subcategory.products', [slug($sub->name ?? ''), $sub->id]),
                        'type' => 'subcategory',
                        'category' => $sub->category->name ?? ''
                    ];
                });
            $results['subcategories'] = $subcategories->toArray();
        } catch (\Exception $e) {
            $results['subcategories'] = [];
        }

        // Search Brands - safe LIKE
        try {
            $brands = Brand::where(function($q) use ($searchTerms, $like) {
                    foreach ($searchTerms as $term) {
                        $q->orWhere('brands.name', 'LIKE', $like($term));
                    }
                })
                ->where('brands.status', Status::ENABLE)
                ->limit(20)
                ->get()
                ->map(function($brand) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name ?? '',
                        'slug' => slug($brand->name ?? ''),
                        'url' => route('brand.products', [slug($brand->name ?? ''), $brand->id]),
                        'type' => 'brand'
                    ];
                });

            $results['brands'] = $brands->toArray();
        } catch (\Exception $e) {
            $results['brands'] = [];
        }

        // Search Frontend Pages - Header/Footer, Policy, Features, etc.
        try {
            $pages = Frontend::where(function($q) use ($searchTerms, $like) {
                    foreach ($searchTerms as $term) {
                        $pattern = $like($term);
                        $q->orWhere('data_keys', 'LIKE', $pattern)
                          ->orWhereRaw('CAST(data_values AS CHAR) LIKE ?', [$pattern]);
                    }
                })
                ->limit(30) // Increased limit for comprehensive search
                ->get()
                ->map(function($page) {
                    $title = '';
                    $url = '#';
                    $description = '';
                    
                    try {
                        $dataKey = $page->data_keys ?? '';
                        $dataValues = $page->data_values ?? (object)[];
                        
                        // Policy Pages
                        if (strpos($dataKey, 'policy_pages') !== false) {
                            $title = $dataValues->title ?? 'Policy Page';
                            $description = strip_tags($dataValues->details ?? $dataValues->short_details ?? '');
                            $url = route('policy.pages.short', $page->id);
                        }
                        // Contact Information
                        elseif (strpos($dataKey, 'contact_us') !== false) {
                            $title = 'Contact Us';
                            $description = ($dataValues->title ?? '') . ' - ' . ($dataValues->subtitle ?? '') . ' - ' . ($dataValues->address ?? '');
                            $url = route('contact');
                        }
                        // Footer Content
                        elseif (strpos($dataKey, 'footer.content') !== false) {
                            $title = 'Footer: ' . ($dataValues->subscribe_title ?? 'Newsletter');
                            $description = $dataValues->connect_title ?? '';
                            $url = '#footer';
                        }
                        // Footer Elements (Payment Methods)
                        elseif (strpos($dataKey, 'footer.element') !== false) {
                            $title = 'Payment Methods';
                            $description = 'Payment options available';
                            $url = '#footer';
                        }
                        // Services/Features
                        elseif (strpos($dataKey, 'service.element') !== false || strpos($dataKey, 'feature') !== false) {
                            $title = $dataValues->title ?? 'Service';
                            $description = $dataValues->short_detail ?? $dataValues->sub_heading ?? '';
                            $url = '#services';
                        }
                        // Banner
                        elseif (strpos($dataKey, 'banner') !== false) {
                            $title = 'Banner: ' . ($dataValues->title ?? 'Banner');
                            $description = $dataValues->subtitle ?? '';
                            $url = $dataValues->url ?? route('home');
                        }
                        // SEO
                        elseif (strpos($dataKey, 'seo') !== false) {
                            $title = 'SEO Settings';
                            $description = $dataValues->description ?? $dataValues->social_description ?? '';
                            $url = route('home');
                        }
                        // Cookie Policy
                        elseif (strpos($dataKey, 'cookie') !== false) {
                            $title = 'Cookie Policy';
                            $description = strip_tags($dataValues->description ?? '');
                            $url = route('cookie.policy');
                        }
                        // Social Icons
                        elseif (strpos($dataKey, 'social_icon') !== false) {
                            $title = 'Social Media: ' . ($dataValues->name ?? 'Social Link');
                            $description = $dataValues->url ?? '';
                            $url = $dataValues->url ?? '#';
                        }
                        // Generic Page
                        else {
                            $title = ucfirst(str_replace(['.', '_'], ' ', $dataKey));
                            if (is_object($dataValues)) {
                                $description = $dataValues->title ?? $dataValues->description ?? $dataValues->content ?? '';
                            } else {
                                $description = is_string($dataValues) ? substr($dataValues, 0, 100) : '';
                            }
                            $url = '#';
                        }
                    } catch (\Exception $e) {
                        $title = 'Page: ' . str_replace(['.', '_'], ' ', $dataKey);
                        $description = '';
                    }

                    return [
                        'id' => $page->id ?? 0,
                        'name' => $title,
                        'description' => substr(strip_tags($description), 0, 100),
                        'url' => $url,
                        'type' => 'page'
                    ];
                })
                ->filter(function($item) {
                    return !empty($item['name']);
                });

            $results['pages'] = $pages->toArray();
        } catch (\Exception $e) {
            $results['pages'] = [];
        }
        
        // Search Routes/Pages - Multi-language keywords (EN, BN, AR, HI, ES, FR, etc.)
        try {
            $staticPages = [];
            $routePages = $this->getRoutePagesForSearch();
            $queryLower = mb_strtolower($query);

            foreach ($routePages as $page) {
                $match = false;
                if (mb_stripos($page['name'], $query) !== false || mb_stripos($page['name'], $queryLower) !== false) {
                    $match = true;
                }
                foreach ($page['keywords'] as $keyword) {
                    $kw = mb_strtolower($keyword);
                    if (mb_strlen($keyword) < 1) continue;
                    if (mb_stripos($kw, $queryLower) !== false || mb_stripos($queryLower, $kw) !== false) {
                        $match = true;
                        break;
                    }
                }
                foreach ($searchTerms as $term) {
                    $term = trim($term);
                    if (mb_strlen($term) < 1) continue;
                    $t = mb_strtolower($term);
                    if (mb_stripos($page['name'], $t) !== false) {
                        $match = true;
                        break;
                    }
                    foreach ($page['keywords'] as $keyword) {
                        if (mb_stripos(mb_strtolower($keyword), $t) !== false) {
                            $match = true;
                            break 2;
                        }
                    }
                }
                if ($match) {
                    $staticPages[] = [
                        'id' => 0,
                        'name' => $page['name'],
                        'description' => '',
                        'url' => $page['url'],
                        'type' => 'route'
                    ];
                }
            }
            $results['pages'] = array_merge($results['pages'], $staticPages);
        } catch (\Exception $e) {
            // continue
        }

        $results['total'] = count($results['products']) + count($results['categories']) +
            count($results['brands']) + count($results['pages']) + count($results['subcategories']);

        $queryTrim = trim($query);
        if ($results['total'] === 0 && mb_strlen($queryTrim) >= 2) {
            $results['did_you_mean'] = $this->suggestDidYouMean($queryTrim);
            // Prefix fallback: search by first 2 chars so wrong spelling still shows related results
            $prefix = mb_substr($queryTrim, 0, 2);
            if (mb_strlen($prefix) >= 2) {
                $prefixResults = $this->performSearchPrefixOnly($prefix);
                if ($prefixResults['total'] > 0) {
                    $results['products'] = array_slice(array_merge($results['products'], $prefixResults['products']), 0, 15);
                    $results['categories'] = array_slice(array_merge($results['categories'], $prefixResults['categories']), 0, 10);
                    $results['brands'] = array_slice(array_merge($results['brands'], $prefixResults['brands']), 0, 10);
                    $results['subcategories'] = array_slice(array_merge($results['subcategories'], $prefixResults['subcategories']), 0, 10);
                    $results['total'] = count($results['products']) + count($results['categories']) + count($results['brands']) + count($results['pages']) + count($results['subcategories']);
                }
            }
        }

        return $results;
    }

    /**
     * Lightweight prefix-only search (used when main search returns 0 for wrong spelling).
     */
    private function performSearchPrefixOnly(string $prefix): array
    {
        $like = fn($t) => $this->searchService->likeWrap($t);
        $pattern = $like($prefix);
        $out = ['products' => [], 'categories' => [], 'brands' => [], 'subcategories' => [], 'pages' => [], 'total' => 0];
        try {
            $out['products'] = Product::where('status', Status::ENABLE)
                ->where(function ($q) use ($pattern) {
                    $q->where('products.name', 'LIKE', $pattern)
                      ->orWhere('products.product_sku', 'LIKE', $pattern)
                      ->orWhereHas('category', fn($c) => $c->where('categories.name', 'LIKE', $pattern))
                      ->orWhereHas('brand', fn($b) => $b->where('brands.name', 'LIKE', $pattern));
                })
                ->with(['category', 'brand'])
                ->limit(10)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id, 'name' => $p->name, 'slug' => trim((string) ($p->slug ?? '')) ?: \App\Models\Product::buildShortSlugForProduct($p), 'image' => method_exists($p, 'imageShow') ? $p->imageShow() : '/assets/images/default.png',
                    'price' => $p->price, 'url' => product_detail_url($p), 'type' => 'product',
                    'category' => $p->category->name ?? '', 'brand' => $p->brand->name ?? ''
                ])->toArray();
            $out['categories'] = Category::where('status', Status::ENABLE)->where('name', 'LIKE', $pattern)->limit(5)->get()
                ->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'slug' => slug($c->name), 'url' => route('category.products', [slug($c->name), $c->id]), 'type' => 'category'])->toArray();
            $out['brands'] = Brand::where('status', Status::ENABLE)->where('name', 'LIKE', $pattern)->limit(5)->get()
                ->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'slug' => slug($b->name), 'url' => route('brand.products', [slug($b->name), $b->id]), 'type' => 'brand'])->toArray();
            $out['subcategories'] = Subcategory::where('status', Status::ENABLE)->where('name', 'LIKE', $pattern)->with('category')->limit(5)->get()
                ->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'slug' => slug($s->name), 'url' => route('subcategory.products', [slug($s->name), $s->id]), 'type' => 'subcategory', 'category' => $s->category->name ?? ''])->toArray();
        } catch (\Throwable $e) {
            // ignore
        }
        $out['total'] = count($out['products']) + count($out['categories']) + count($out['brands']) + count($out['subcategories']);
        return $out;
    }

    /**
     * Route pages with multi-language keywords (EN, BN, AR, HI, ES, FR, DE, PT, etc.)
     */
    private function getRoutePagesForSearch(): array
    {
        return [
            ['name' => 'Products', 'url' => route('products'), 'keywords' => ['products', 'product', 'shop', 'shopping', 'buy', 'পণ্য', 'প্রোডাক্ট', 'منتجات', 'उत्पाद', 'productos', 'produits', 'produto', '商品']],
            ['name' => 'Contact', 'url' => route('contact'), 'keywords' => ['contact', 'contact us', 'get in touch', 'email', 'phone', 'যোগাযোগ', 'اتصل', 'संपर्क', 'contacto', 'contactez', 'Kontakt']],
            ['name' => 'Track Order', 'url' => route('track.order'), 'keywords' => ['track', 'track order', 'order tracking', 'tracking', 'shipment', 'অর্ডার ট্র্যাক', 'تتبع', 'ट्रैक', 'seguimiento', 'suivi', 'rastreamento']],
            ['name' => 'Home', 'url' => route('home'), 'keywords' => ['home', 'main', 'index', 'হোম', 'الرئيسية', 'होम', 'inicio', 'accueil', 'Startseite']],
            ['name' => 'Login', 'url' => route('user.login'), 'keywords' => ['login', 'sign in', 'log in', 'লগইন', 'تسجيل الدخول', 'लॉगिन', 'iniciar sesión', 'connexion', 'Anmeldung']],
            ['name' => 'Register', 'url' => route('user.register'), 'keywords' => ['register', 'registration', 'sign up', 'create account', 'রেজিস্ট্রেশন', 'تسجيل', 'रजिस्टर', 'registro', 'inscription', 'Registrierung']],
            ['name' => 'Email Subscribe', 'url' => route('home') . '#footer', 'keywords' => ['subscribe', 'newsletter', 'email subscribe', 'সাবস্ক্রাইব', 'न्यूज़लेटर', 'suscribir', 'abonnement', 'Newsletter']],
            ['name' => 'My Orders', 'url' => route('user.order.index'), 'keywords' => ['order', 'orders', 'my orders', 'অর্ডার', 'আমার অর্ডার', 'طلب', 'ऑर्डर', 'pedidos', 'commandes', 'Bestellungen']],
            ['name' => 'Cart', 'url' => route('cart.list.product'), 'keywords' => ['cart', 'basket', 'bag', 'কার্ট', 'কিনুন', 'سلة', 'कार्ट', 'carrito', 'panier', 'Warenkorb']],
            ['name' => 'Checkout', 'url' => route('user.checkout.index'), 'keywords' => ['checkout', 'payment', 'pay', 'চেকআউট', 'পেমেন্ট', 'الدفع', 'चेकआउट', 'pago', 'paiement', 'Kasse']],
            ['name' => 'Dashboard', 'url' => route('user.home'), 'keywords' => ['dashboard', 'account', 'my account', 'ড্যাশবোর্ড', 'अकाउंट', 'panel', 'tableau de bord', 'Dashboard']],
            ['name' => 'Profile', 'url' => route('user.profile.setting'), 'keywords' => ['profile', 'setting', 'settings', 'প্রোফাইল', 'সেটিংস', 'إعدادات', 'प्रोफाइल', 'perfil', 'profil', 'Einstellungen']],
            ['name' => 'Wishlist', 'url' => route('wish.list.product'), 'keywords' => ['wishlist', 'wish list', 'favourite', 'উইশলিস্ট', 'المفضلة', 'विशलिस्ट', 'lista de deseos', 'liste de souhaits']],
            ['name' => 'Cookie Policy', 'url' => route('cookie.policy'), 'keywords' => ['cookie', 'policy', 'privacy', 'gdpr', 'কুকি', 'خصوصية', 'गोपनीयता', 'privacidad', 'confidentialité']],
            ['name' => 'All Categories', 'url' => route('category.all'), 'keywords' => ['categories', 'category', 'all categories', 'ক্যাটাগরি', 'فئات', 'श्रेणियाँ', 'categorías', 'catégories', 'Kategorien']],
            ['name' => 'All Brands', 'url' => route('brand.all'), 'keywords' => ['brands', 'brand', 'all brands', 'ব্র্যান্ড', 'علامات', 'ब्रांड', 'marcas', 'marques', 'Marken']],
            ['name' => 'Featured Products', 'url' => route('products.featured'), 'keywords' => ['featured', 'featured products', 'popular', 'প্রচারিত', 'مميز', 'विशेष', 'destacados', 'populaires']],
            ['name' => 'Best Selling', 'url' => route('products.best.selling'), 'keywords' => ['best selling', 'bestseller', 'top products', 'সবচেয়ে বিক্রিত', 'الأكثر مبيعاً', 'सर्वश्रेष्ठ', 'más vendidos', 'meilleures ventes']],
            ['name' => 'Hot Deal', 'url' => route('product.hot.deal'), 'keywords' => ['hot deal', 'deal', 'hot', 'discount', 'অফার', 'صفقة', 'डील', 'oferta', 'promotion', 'Angebot']],
            ['name' => 'Today\'s Deal', 'url' => route('product.today.deal'), 'keywords' => ['today deal', 'today\'s deal', 'daily deal', 'flash', 'আজকের অফার', 'عرض اليوم', 'आज का डील']],
            ['name' => 'Compare', 'url' => route('compare.index'), 'keywords' => ['compare', 'comparison', 'তুলনা', 'مقارنة', 'तुलना', 'comparar', 'comparer', 'Vergleich']],
        ];
    }

    /**
     * Suggest closest category/brand names when no results (Did you mean?)
     */
    private function suggestDidYouMean(string $query): array
    {
        $suggestions = [];
        $queryLen = mb_strlen($query);
        $maxSuggestions = 5;

        try {
            $categories = Category::where('status', Status::ENABLE)->pluck('name')->toArray();
            $brands = Brand::where('status', Status::ENABLE)->pluck('name')->toArray();
            $candidates = array_unique(array_merge($categories, $brands));

            $scored = [];
            $queryLower = mb_strtolower($query);
            foreach ($candidates as $name) {
                $nameLower = mb_strtolower($name);
                if ($nameLower === '' || $name === '') continue;
                $sim = similar_text($queryLower, $nameLower, $pct);
                $contains = mb_strpos($nameLower, $queryLower) !== false || mb_strpos($queryLower, $nameLower) !== false;
                $score = $contains ? 80 : (float) $pct;
                $scored[] = ['name' => $name, 'score' => $score];
            }
            usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
            foreach (array_slice($scored, 0, $maxSuggestions) as $s) {
                if ($s['score'] >= 25) {
                    $suggestions[] = $s['name'];
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return array_values(array_unique($suggestions));
    }

    /**
     * Voice search handler
     */
    public function voiceSearch(Request $request)
    {
        $transcript = $request->get('transcript', '');
        
        if (empty($transcript)) {
            return response()->json([
                'success' => false,
                'message' => 'No voice input received'
            ]);
        }

        // Perform search with voice transcript
        $results = $this->performSearch($transcript);
        $total = $results['total'] ?? 0;
        $this->logSearch($transcript, $total, 'voice');
        activity_log(\App\Models\UserActivityLog::SEARCH_VOICE, 'Voice search: ' . $transcript, null, null);

        return response()->json([
            'success' => true,
            'transcript' => $transcript,
            'results' => $results
        ]);
    }

    /**
     * Image search: store as WebP for analytics, log for admin, then return (recognition can be added later).
     */
    public function imageSearch(Request $request)
    {
        if (!$request->hasFile('image')) {
            return response()->json(['success' => false, 'message' => __('No image uploaded')]);
        }

        $request->validate(['image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120']);

        $file = $request->file('image');
        $storedPath = null;

        try {
            $optimizer = app(ImageOptimizationService::class);
            $tempPath = $file->getRealPath();
            $webpFullPath = $optimizer->convertToWebP($tempPath, 85);
            if ($webpFullPath && file_exists($webpFullPath)) {
                $filename = 'search_' . uniqid() . '_' . time() . '.webp';
                $relativePath = 'search_logs/' . $filename;
                Storage::disk('public')->makeDirectory('search_logs');
                Storage::disk('public')->put($relativePath, file_get_contents($webpFullPath));
                @unlink($webpFullPath);
                $storedPath = $relativePath;
            }
        } catch (\Throwable $e) {
            \Log::warning('Search image WebP save failed: ' . $e->getMessage());
        }

        if (!$storedPath) {
            $storedPath = $file->store('search_logs', 'public');
        }

        // Simulate AI Vision Match (fetch random active products for demonstration)
        $matchedProducts = \App\Models\Product::where('status', \App\Constants\Status::ENABLE)
            ->with(['category', 'brand'])
            ->inRandomOrder()
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id, 
                'name' => $p->name, 
                'slug' => trim((string) ($p->slug ?? '')) ?: \App\Models\Product::buildShortSlugForProduct($p), 
                'image' => method_exists($p, 'imageShow') ? $p->imageShow() : '/assets/images/default.png',
                'price' => $p->price, 
                'url' => product_detail_url($p), 
                'type' => 'product',
                'category' => $p->category->name ?? '', 
                'brand' => $p->brand->name ?? ''
            ])->toArray();

        $resultsCount = count($matchedProducts);
        $this->logSearch('Image search', $resultsCount, 'image', $storedPath);

        return response()->json([
            'success' => true,
            'coming_soon' => false,
            'message' => __('Products matched based on visual similarity.'),
            'results' => [
                'products' => $matchedProducts, 
                'categories' => [], 
                'brands' => [], 
                'pages' => [],
                'total' => $resultsCount
            ],
            'total' => $resultsCount
        ]);
    }

    /**
     * Log user search for admin analytics (guests and logged-in; optional image_path for image search).
     */
    private function logSearch(string $query, int $resultsCount, string $source = 'universal', ?string $imagePath = null): void
    {
        try {
            SearchLog::create([
                'query' => mb_substr(trim($query), 0, 500),
                'user_id' => auth('web')->id(),
                'ip' => request()->ip(),
                'user_agent' => mb_substr(request()->userAgent() ?? '', 0, 512),
                'results_count' => $resultsCount,
                'source' => in_array($source, ['universal', 'voice', 'image', 'products_page', 'filter'], true) ? $source : 'universal',
                'image_path' => $imagePath ? mb_substr($imagePath, 0, 500) : null,
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Search log failed: ' . $e->getMessage());
        }
    }
}
