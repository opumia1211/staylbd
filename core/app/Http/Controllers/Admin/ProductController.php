<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductComparison;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Rules\FileTypeValidate;
use App\Rules\VideoMaxDuration;
use App\Services\HomepageDataService;
use App\Services\ProductCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /** Product table columns cache for legacy/master DB compatibility. */
    protected static ?array $productColumnsCache = null;

    protected function productTableColumns(): array
    {
        if (self::$productColumnsCache !== null) {
            return self::$productColumnsCache;
        }
        try {
            self::$productColumnsCache = Schema::getColumnListing('products');
        } catch (\Throwable $e) {
            self::$productColumnsCache = [];
        }

        return self::$productColumnsCache;
    }

    protected function productHasColumn(string $column): bool
    {
        return in_array($column, $this->productTableColumns(), true);
    }

    public function index(Request $request)
    {
        $pageTitle = request()->filled('low_stock') ? 'Low Stock Products' : 'Manage Product';
        $products  = $this->productData();
        $brands    = Brand::orderBy('name')->pluck('name', 'id');
        $categories = Category::orderBy('name')->pluck('name', 'id');
        return view('admin.product.index', compact('pageTitle', 'products', 'brands', 'categories'));
    }

    /** Columns needed for product list only – avoids loading description/gallery/features (scales to millions). */
    protected function productListColumns(): array
    {
        $desired = [
            'id', 'name', 'slug', 'image', 'product_sku', 'category_id', 'brand_id', 'subcategory_id',
            'price', 'quantity', 'status', 'featured_product', 'hot_deals', 'today_deals', 'trending_now',
            'sale_count', 'created_at',
        ];
        if ($this->productHasColumn('low_stock_alert')) {
            $desired[] = 'low_stock_alert';
        }
        $existing = $this->productTableColumns();

        return array_values(array_intersect($desired, $existing));
    }

    public function todayDealProduct()
    {
        $pageTitle    = __('Today Deal Products');
        $products     = $this->productData('todayDeal');
        $brands       = Brand::orderBy('name')->pluck('name', 'id');
        $categories   = Category::orderBy('name')->pluck('name', 'id');
        $productScope = 'todayDeal';
        return view('admin.product.index', compact('pageTitle', 'products', 'brands', 'categories', 'productScope'));
    }

    public function featureProduct()
    {
        $pageTitle    = __('Featured Products');
        $products     = $this->productData('featured');
        $brands       = Brand::orderBy('name')->pluck('name', 'id');
        $categories   = Category::orderBy('name')->pluck('name', 'id');
        $productScope = 'featured';
        return view('admin.product.index', compact('pageTitle', 'products', 'brands', 'categories', 'productScope'));
    }

    public function hotProduct()
    {
        $pageTitle    = __('Hot Deal Products');
        $products     = $this->productData('hotDeal');
        $brands       = Brand::orderBy('name')->pluck('name', 'id');
        $categories   = Category::orderBy('name')->pluck('name', 'id');
        $productScope = 'hotDeal';
        return view('admin.product.index', compact('pageTitle', 'products', 'brands', 'categories', 'productScope'));
    }

    /** Best Selling products (by sale count) – plan: professional eCommerce sections */
    public function bestSellingProduct()
    {
        $pageTitle    = __('Best Selling Products');
        $products     = $this->productData('bestSelling');
        $brands       = Brand::orderBy('name')->pluck('name', 'id');
        $categories   = Category::orderBy('name')->pluck('name', 'id');
        $productScope = 'bestSelling';
        return view('admin.product.index', compact('pageTitle', 'products', 'brands', 'categories', 'productScope'));
    }

    /** Trending Now products (admin-marked + shown on homepage Trending Now section) */
    public function trendingProduct()
    {
        $pageTitle    = __('Trending Now Products');
        $products     = $this->productData('trendingNow');
        $brands       = Brand::orderBy('name')->pluck('name', 'id');
        $categories   = Category::orderBy('name')->pluck('name', 'id');
        $productScope = 'trendingNow';
        return view('admin.product.index', compact('pageTitle', 'products', 'brands', 'categories', 'productScope'));
    }

    protected function productData($scope = null)
    {
        if ($scope && in_array($scope, ['todayDeal', 'featured', 'hotDeal', 'bestSelling', 'trendingNow'])) {
            $product = Product::$scope();
        } else {
            $product = Product::query();
        }
        $product->select($this->productListColumns());
        if (request()->filled('low_stock')) {
            $product->where('quantity', '<=', 5)->where('quantity', '>=', 0);
        }
        if (request()->filled('category_id')) {
            $product->where('category_id', request('category_id'));
        }
        if (request()->filled('brand_id')) {
            $product->where('brand_id', request('brand_id'));
        }
        if (request()->filled('status') && in_array(request('status'), ['0', '1'])) {
            $product->where('status', (int) request('status'));
        }
        if (request()->filled('stock')) {
            $stock = request('stock');
            if ($stock === 'out') {
                $product->where('quantity', '<=', 0);
            } elseif ($stock === 'low') {
                $product->whereBetween('quantity', [1, 5]);
            } elseif ($stock === 'ok') {
                $product->where('quantity', '>', 5);
            }
        }
        $perPage = (int) request('per_page', getPaginate());
        $allowedPerPage = [10, 20, 50, 100];
        $perPage = in_array($perPage, $allowedPerPage) ? $perPage : getPaginate();
        $product->with(['category:id,name', 'brand:id,name'])
            ->searchable(['name', 'product_sku', 'category:name', 'subcategory:name', 'brand:name']);
        if (request()->filled('low_stock')) {
            $product->orderBy('quantity', 'asc');
        } elseif (!in_array($scope, ['bestSelling', 'trendingNow'], true)) {
            $product->latest('id');
        }
        return $product->paginate($perPage)->withQueryString();
    }

    /**
     * Clothing-only product create page. Categories filtered to clothing.
     */
    public function create()
    {
        $pageTitle  = __('Add Clothing Product');
        $brands     = Brand::active()->orderBy('name')->get(['id', 'name']);
        $allCategories = Category::active()->orderBy('name')->get(['id', 'name']);
        $categoryIds = $allCategories->pluck('id');
        $categorySubcategoriesMap = \App\Models\Subcategory::active()
            ->whereIn('category_id', $categoryIds)
            ->orderBy('name')
            ->get(['id', 'category_id', 'name'])
            ->groupBy('category_id')
            ->map(fn ($subs) => $subs->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values())
            ->toArray();
        $categories = $allCategories->filter(fn ($cat) => $this->isClothingCategory($cat))->values();
        if ($categories->isEmpty()) {
            $categories = $allCategories;
        }
        $seasons = config('product_upload.seasons', [
            'all' => 'All Season', 'spring' => 'Spring', 'summer' => 'Summer', 'fall' => 'Fall', 'winter' => 'Winter',
        ]);

        return view('admin.product.create', compact('pageTitle', 'brands', 'categories', 'categorySubcategoriesMap', 'seasons'));
    }

    /**
     * Universal product create – single page for any product: physical, digital, clothing, resell from any website.
     */
    public function generalCreate()
    {
        $pageTitle = __('Add Product (Universal)');
        $general = gs();
        $brands = Brand::active()->orderBy('name')->get(['id', 'name']);
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);
        $categorySubcategoriesMap = \App\Models\Subcategory::active()
            ->whereIn('category_id', $categories->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'category_id', 'name'])
            ->groupBy('category_id')
            ->map(fn ($subs) => $subs->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values())
            ->toArray();
        $seasons = config('product_upload.seasons', [
            'all' => 'All Season', 'spring' => 'Spring', 'summer' => 'Summer', 'fall' => 'Fall', 'winter' => 'Winter',
        ]);

        return view('admin.product.general_create', compact('pageTitle', 'general', 'brands', 'categories', 'categorySubcategoriesMap', 'seasons'));
    }

    /**
     * Stock alerts page: products at or below low_stock_alert (or default threshold if column missing).
     */
    public function stockAlerts()
    {
        $pageTitle = __('Stock Alerts');
        $defaultThreshold = (int) config('product_upload.low_stock_min', 5);
        $hasLowStockColumn = Schema::hasColumn('products', 'low_stock_alert');

        $products = Product::where('status', 1)
            ->select($this->productListColumns())
            ->where(function ($q) use ($defaultThreshold, $hasLowStockColumn) {
                $q->where('quantity', '<=', 0);
                if ($hasLowStockColumn) {
                    $q->orWhereRaw('quantity <= COALESCE(low_stock_alert, ?)', [$defaultThreshold]);
                } else {
                    $q->orWhere('quantity', '<=', $defaultThreshold);
                }
            })
            ->with(['category:id,name', 'brand:id,name'])
            ->orderBy('quantity')
            ->paginate(20);

        return view('admin.product.stock_alerts', compact('pageTitle', 'products', 'defaultThreshold'));
    }

    protected function isClothingCategory(Category $category): bool
    {
        $slugs = config('product_upload.clothing_category_slugs', []);
        $keywords = config('product_upload.clothing_category_keywords', []);
        $name = $category->name ?? '';
        $slug = Str::slug($name);
        $nameLower = strtolower($name);
        if (in_array($slug, $slugs, true)) {
            return true;
        }
        foreach ($keywords as $kw) {
            if (str_contains($nameLower, strtolower($kw))) {
                return true;
            }
        }
        return false;
    }

    public function edit($id)
    {
        $pageTitle = "Edit Product";
        $product   = Product::findOrFail($id);

        // Lightweight: only id and name for dropdowns; avoid loading full models
        $brands = Brand::orderBy('name')->get(['id', 'name']);
        $categories = Category::orderBy('name')->get(['id', 'name']);

        // Single map category_id => subcategories (id, name) for JS – no duplicate data in DOM
        $categorySubcategoriesMap = \App\Models\Subcategory::active()
            ->whereIn('category_id', $categories->pluck('id'))
            ->orderBy('name')
            ->get(['id', 'category_id', 'name'])
            ->groupBy('category_id')
            ->map(fn ($subs) => $subs->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values())
            ->toArray();

        $galleries = [];
        foreach ($product->gallery ?? [] as $gallery) {
            $galleries[] = [
                'id'  => $gallery,
                'src' => getImage(getFilePath('productGallery') . '/' . $gallery),
            ];
        }

        return view('admin.product.edit', compact('pageTitle', 'product', 'brands', 'categories', 'categorySubcategoriesMap', 'galleries'));
    }

    public function store(Request $request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';

        // Normalize size_qty: empty string or non-numeric → 0 so validation (integer|min:0) passes
        if (is_array($request->size_qty ?? null)) {
            $request->merge([
                'size_qty' => collect($request->size_qty)->map(function ($v) {
                    if ($v === '' || $v === null) {
                        return 0;
                    }
                    return is_numeric($v) ? (int) $v : 0;
                })->all(),
            ]);
        }

        $request->validate([
            'name'                   => 'required|max:255',
            'brand_id'               => 'required|exists:brands,id',
            'category_id'            => 'required|exists:categories,id',
            'subcategory_id'         => 'required|exists:subcategories,id',
            'product_sku'            => 'required|string',
            'quantity'               => 'required|integer|min:0',
            'price'                  => 'required|numeric|gt:0',
            'discount'               => 'nullable|numeric|min:0',
            'discount_type'          => 'required|in:1,2',
            'digital_item'           => 'required|in:0,1',
            'file_type'              => 'required_if:digital_item,1|in:1,2',
            'link'                   => 'required_if:file_type,2|url|max:255',
            'summary'                => 'required',
            'key_features'           => 'nullable|string',
            'description'            => 'required',
            'features'               => 'nullable|array|min:1',
            'features.*.title'       => 'required_with:features|string',
            'features.*.description' => 'required_with:features|string',
            'image'                  => [$isRequired, 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:51200', new FileTypeValidate(['jpeg', 'jpg', 'png', 'webp', 'svg'])],
            'gallery'                => "$isRequired|array|min:1|max:6",
            'gallery.*'              => [$isRequired, 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:51200', new FileTypeValidate(['jpeg', 'jpg', 'png', 'webp', 'svg'])],
            'video'                  => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:102400', new VideoMaxDuration(30)],
            'size_qty'               => 'nullable|array',
            'size_qty.*'             => 'nullable|integer|min:0',
            'target_gender'          => 'nullable|in:male,female,unisex,kids',
            'target_age_min'         => 'nullable|integer|min:0|max:100',
            'target_age_max'         => 'nullable|integer|min:0|max:100',
            'product_type'           => 'nullable|in:physical,clothing,general,digital,service',
            'fabric_type'            => 'nullable|string|max:100',
            'material'               => 'nullable|string|max:255',
            'season'                 => 'nullable|string|max:50',
            'color_variants'         => 'nullable',
            'color_variants.*'       => 'nullable|string|max:100',
            'slug'                   => 'nullable|string|max:255',
            'original_price'         => 'nullable|numeric|min:0',
            'profit_margin'          => 'nullable|numeric|min:0',
            'low_stock_alert'        => 'nullable|integer|min:0',
            'delivery_type'          => 'nullable|in:free,paid',
            'delivery_charge'        => 'nullable|numeric|min:0',
            'meta_title'             => 'nullable|string|max:255',
            'meta_description'      => 'nullable|string|max:500',
            'meta_keywords'          => 'nullable|string|max:500',
            'home_section_override'  => 'nullable|in:new_arrivals,best_selling,recommended,trending',
            'home_section_rank'      => 'nullable|integer|min:0|max:1000000',
            'home_exclude_from_auto' => 'nullable|in:0,1',
            'shipping_weight'        => 'nullable|numeric|min:0',
            'shipping_class'         => 'nullable|string|max:100',
            'warehouse_location'     => 'nullable|string|max:255',
            'source_url'             => 'nullable|url|max:500',
            'delivery_time'          => 'nullable|string|max:100',
        ], [
            'image.max'     => __('The main image must not be greater than 50 MB.'),
            'gallery.*.max' => __('Each gallery image must not be greater than 50 MB.'),
            'image.mimes'   => __('Main image allowed formats: PNG, JPG, WebP, SVG.'),
            'gallery.*.mimes' => __('Gallery image allowed formats: PNG, JPG, WebP, SVG.'),
        ]);

        if (!$request->old && !$request->gallery) {
            $notify[] = ['error', 'Minimum one gallery image is required'];
            return back()->withNotify($notify);
        }

        $hasSizes = $request->has_sizes && is_array($request->size_qty);
        $sizeSum = 0;
        if ($hasSizes) {
            foreach ($request->size_qty ?? [] as $q) { $sizeSum += (int) $q; }
        }
        if ($hasSizes && $sizeSum < 1) {
            $notify[] = ['error', 'Enter at least one quantity for size(s).'];
            return back()->withNotify($notify);
        }
        if (!$hasSizes && (int) $request->quantity < 1) {
            $notify[] = ['error', 'Stock quantity must be at least 1 or use sizes.'];
            return back()->withNotify($notify);
        }

        if ($request->discount) {
            if ($request->discount_type == 1) {
                $discount = $request->price - $request->discount;
            } else {
                $discount = $request->price - (($request->price * $request->discount) / 100);
            }

            if ($discount <= 0) {
                $notify[] = ['error', 'Discount price can\'t be grater than main price'];
                return back()->withNotify($notify);
            }
        }
        $isFileRequired = 'required_if:file_type,1';
        $oldValues = null;
        if ($id) {
            $product = Product::findOrFail($id);
            $oldValues = $product->only(['name', 'product_sku', 'price', 'quantity', 'status', 'featured_product', 'hot_deals', 'today_deals']);

            if (($product->file_type == 2) && !$product->file && ($request->file_type == 1)) {
                $isFileRequired = 'required';
            }

            if (($product->file_type == 1) && $product->file && ($request->file_type == 1)) {
                $isFileRequired = 'nullable';
            }

            $request->validate([
                'file' => [$isFileRequired, new FileTypeValidate(['pdf', 'docx', 'txt', 'zip', 'xlx', 'csv', 'ai', 'psd', 'pptx'])],
            ]);

            $message       = "Product updated successfully";
            $imageToRemove = $request->old ? array_values(removeElement($product->gallery, $request->old)) : $product->gallery;


            if ($imageToRemove != null && count($imageToRemove)) {
                foreach ($imageToRemove as $singleImage) {
                    fileManager()->removeFile(getFilePath('productGallery') . '/' . $singleImage);
                }

                $product->gallery = removeElement($product->gallery, $imageToRemove);
            }

            if (!$request->digital_item && $product->file) {
                fileManager()->removeFile(getFilePath('productFile') . '/' . $product->file);
                $product->file = null;
            }

            if ($request->file_type == 2 && $product->file) {
                fileManager()->removeFile(getFilePath('productFile') . '/' . $product->file);
                $product->file = null;
            }
            if (Schema::hasColumn('products', 'video') && $request->hasFile('video') && $product->video) {
                @unlink(public_path(getFilePath('productVideo') . '/' . $product->video));
                $product->video = null;
            }
        } else {
            $request->validate([
                'file' => [$isFileRequired, new FileTypeValidate(['pdf', 'docx', 'txt', 'zip', 'xlx', 'csv', 'ai', 'psd', 'pptx'])],
            ]);

            $product = new Product();
            $message = "Product added successfully";
        }

        if ($request->hasFile('file')) {
            try {
                $product->file = fileUploader($request->file, getFilePath('productFile'), null, @$product->file);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your file'];
                return back()->withNotify($notify);
            }
        }

        if ($request->hasFile('image')) {
            try {
                $product->image = fileUploader($request->image, getFilePath('product'), getFileSize('product'), @$product->image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $gallery = $id ? $product->gallery : [];

        if ($request->hasFile('gallery')) {
            foreach ($request->gallery as $singleImage) {
                try {
                    $gallery[] = fileUploader($singleImage, getFilePath('productGallery'), getFileSize('productGallery'));
                } catch (\Exception $exp) {
                    $notify[] = ['error', 'Couldn\'t upload your image'];
                    return back()->withNotify($notify);
                }
            }
        }

        $hasSizes = $request->has_sizes && is_array($request->size_qty);
        $sizeQty = $hasSizes ? $request->size_qty : [];
        $totalQty = (int) $request->quantity;
        if ($hasSizes) {
            $totalQty = 0;
            foreach ($sizeQty as $q) {
                $totalQty += (int) $q;
            }
        }

        $product->name           = $request->name;
        $product->brand_id       = $request->brand_id;
        $product->category_id    = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->product_sku    = $request->product_sku;
        $product->quantity       = $totalQty;
        $product->has_variants   = $hasSizes && $totalQty > 0 ? 1 : 0;
        $product->variant_attributes = $hasSizes ? ['size' => 'Size'] : null;
        $product->price          = $request->price;
        $product->discount       = $request->discount ?? 0;
        $product->discount_type  = $request->discount_type;
        $product->digital_item   = $request->digital_item;
        $product->file_type      = $request->file_type ?? 0;
        $product->link           = $request->link;
        $product->summary        = $request->summary;
        $product->key_features   = $request->key_features;
        $product->description    = $request->description;
        $product->features       = $request->features;
        $product->gallery        = $gallery;
        if (Schema::hasColumn('products', 'target_gender')) {
            $product->target_gender  = $request->target_gender ?: null;
            $product->target_age_min = $request->target_age_min ?: null;
            $product->target_age_max = $request->target_age_max ?: null;
        } else {
            $product->offsetUnset('target_gender');
            $product->offsetUnset('target_age_min');
            $product->offsetUnset('target_age_max');
        }
        if (Schema::hasColumn('products', 'cod_disabled')) {
            $product->cod_disabled = (bool) ($request->cod_disabled ?? 0);
        }
        if (Schema::hasColumn('products', 'product_type')) {
            $validTypes = ['physical', 'clothing', 'general', 'digital', 'service'];
            $product->product_type = in_array($request->product_type, $validTypes, true) ? $request->product_type : ($product->product_type ?? 'general');
        }
        if (Schema::hasColumn('products', 'fabric_type')) {
            $product->fabric_type = $request->fabric_type ?: null;
        }
        if (Schema::hasColumn('products', 'material')) {
            $product->material = $request->material ?: null;
        }
        if (Schema::hasColumn('products', 'season')) {
            $product->season = $request->season ?: null;
        }
        if (Schema::hasColumn('products', 'color_variants')) {
            $colors = $request->color_variants ?? $request->color_variants_input ?? null;
            if (is_array($colors)) {
                $product->color_variants = array_values(array_filter(array_map('trim', $colors)));
            } elseif (is_string($colors) && $colors !== '') {
                $product->color_variants = array_values(array_filter(array_map('trim', explode(',', $colors))));
            } else {
                $product->color_variants = null;
            }
        }
        if (Schema::hasColumn('products', 'slug')) {
            $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
            if (Product::where('slug', $slug)->where('id', '!=', $product->id ?? 0)->exists()) {
                $slug = $slug . '-' . ($product->id ?? uniqid());
            }
            $product->slug = $slug;
        }
        if (Schema::hasColumn('products', 'original_price')) {
            $product->original_price = $request->original_price ?: null;
        }
        if (Schema::hasColumn('products', 'profit_margin')) {
            $product->profit_margin = $request->profit_margin ?: null;
        }
        if (Schema::hasColumn('products', 'low_stock_alert')) {
            $product->low_stock_alert = $request->low_stock_alert ?: null;
        }
        if (Schema::hasColumn('products', 'delivery_type')) {
            $product->delivery_type = in_array($request->delivery_type, ['free', 'paid'], true) ? $request->delivery_type : 'free';
            $product->delivery_charge = (float) ($request->delivery_charge ?? 0);
        }
        if (Schema::hasColumn('products', 'meta_title')) {
            $product->meta_title = $request->meta_title ?: null;
        }
        if (Schema::hasColumn('products', 'meta_description')) {
            $product->meta_description = $request->meta_description ?: null;
        }
        if (Schema::hasColumn('products', 'meta_keywords') && $request->filled('meta_keywords')) {
            $product->meta_keywords = is_array($request->meta_keywords) ? $request->meta_keywords : array_values(array_filter(array_map('trim', explode(',', $request->meta_keywords))));
        }
        // Visibility / homepage badges (single spotlight: today > hot > featured)
        $product->featured_product = (int) ($request->featured_product ?? 0);
        $product->hot_deals = (int) ($request->hot_deals ?? 0);
        $product->today_deals = (int) ($request->today_deals ?? 0);
        if (Schema::hasColumn('products', 'trending_now')) {
            $product->trending_now = (int) ($request->trending_now ?? 0);
        }
        if (Schema::hasColumn('products', 'home_section_override')) {
            $override = $request->input('home_section_override');
            $product->home_section_override = in_array($override, ['new_arrivals', 'best_selling', 'recommended', 'trending'], true) ? $override : null;
            $product->home_section_rank = max(0, (int) $request->input('home_section_rank', 0));
            $product->home_exclude_from_auto = (int) $request->input('home_exclude_from_auto', 0) ? 1 : 0;
        }
        if ($product->today_deals) {
            $product->featured_product = Status::DISABLE;
            $product->hot_deals = Status::DISABLE;
        } elseif ($product->hot_deals) {
            $product->featured_product = Status::DISABLE;
        }

        if (Schema::hasColumn('products', 'shipping_weight')) {
            $product->shipping_weight = $request->shipping_weight ?: 0;
        }
        if (Schema::hasColumn('products', 'shipping_class')) {
            $product->shipping_class = $request->shipping_class ?: null;
        }
        if (Schema::hasColumn('products', 'warehouse_location')) {
            $product->warehouse_location = $request->warehouse_location ?: null;
        }
        if (Schema::hasColumn('products', 'source_url')) {
            $product->source_url = $request->source_url ?: null;
        }
        if (Schema::hasColumn('products', 'delivery_time')) {
            $product->delivery_time = $request->delivery_time ?: null;
        }
        $product->save();
        ProductCacheService::clearProductDetail($product->id);
        $this->clearSpotlightCaches();

        // Video: stored as uploaded. For advanced compression/re-encode (e.g. H.264/WebM), integrate FFmpeg in a queue job.
        if ($request->hasFile('video') && Schema::hasColumn('products', 'video')) {
            $file = $request->file('video');
            $allowedExt = ['mp4', 'webm', 'ogv', 'mov'];
            $allowedMime = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-msvideo'];
            $ext = strtolower($file->getClientOriginalExtension() ?: '');
            $mime = $file->getMimeType();

            if (!in_array($ext, $allowedExt, true) || !in_array($mime, $allowedMime, true)) {
                $notify[] = ['error', 'Invalid video. Allowed: MP4, WebM, OGV, MOV'];
                return back()->withNotify($notify)->withInput();
            }

            try {
                $product->video = fileUploader($file, getFilePath('productVideo'), null, @$product->video);
                $product->save();
            } catch (\Exception $exp) {
                $path = public_path(getFilePath('productVideo'));
                if (!is_dir($path)) {
                    @mkdir($path, 0755, true);
                }
                $name = uniqid() . time() . '.' . $ext;
                $file->move($path, $name);
                $product->video = $name;
                $product->save();
            }
        }

        if ($hasSizes && $totalQty > 0) {
            ProductVariant::where('product_id', $product->id)->delete();
            $price = $product->price;
            $discount = $product->discount ?? 0;
            $discountType = $product->discount_type ?? 1;
            foreach ($sizeQty as $size => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) continue;
                ProductVariant::create([
                    'product_id'     => $product->id,
                    'sku'            => $product->product_sku . '-' . $size,
                    'attributes'     => ['size' => $size],
                    'price'          => $price,
                    'discount'       => $discount,
                    'discount_type'  => $discountType,
                    'quantity'       => $qty,
                    'status'         => 1,
                ]);
            }
        }

        log_admin_activity($id ? 'update' : 'create', 'Product', $product->id, $oldValues, $product->only(['name', 'product_sku', 'price', 'quantity', 'status', 'featured_product', 'hot_deals', 'today_deals']));

        $notify[] = ["success", $message];
        if (!$id) {
            return redirect()->route('admin.product.index')->withNotify($notify);
        }
        return back()->withNotify($notify);
    }

    /** Homepage + listing caches (Quick Deals, Hot/Featured sections, etc.) */
    protected function clearSpotlightCaches(): void
    {
        Cache::forget('homepage.today_deals');
        HomepageDataService::clearCache();
        HomepageDataService::clearBelowFoldFragmentCache();
        ProductCacheService::clearProductListings();
    }

    /**
     * Store general product (from general-create form). Tabbed form with full fields, variants, SEO, import URL.
     */
    public function generalStore(Request $request, $id = 0)
    {
        $isRequired = $id ? 'nullable' : 'required';
        $request->validate([
            'name'             => 'required|max:255',
            'slug'             => 'nullable|string|max:255',
            'brand_id'         => 'required|exists:brands,id',
            'category_id'      => 'required|exists:categories,id',
            'subcategory_id'   => 'required|exists:subcategories,id',
            'product_sku'      => 'nullable|string|max:100',
            'quantity'         => 'required|integer|min:0',
            'price'            => 'required|numeric|gt:0',
            'original_price'   => 'nullable|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'discount_type'    => 'nullable|in:1,2',
            'profit_margin'    => 'nullable|numeric|min:0',
            'low_stock_alert'  => 'nullable|integer|min:0',
            'warehouse_location' => 'nullable|string|max:255',
            'shipping_weight'  => 'nullable|numeric|min:0',
            'shipping_class'   => 'nullable|string|max:100',
            'delivery_time'    => 'nullable|string|max:100',
            'delivery_type'    => 'nullable|in:free,paid',
            'delivery_charge'  => 'nullable|numeric|min:0',
            'summary'          => 'required|string',
            'description'      => 'required|string',
            'key_features'     => 'nullable|string',
            'features'         => 'nullable|array',
            'features.*.title' => 'nullable|string',
            'features.*.description' => 'nullable|string',
            'image'            => [$isRequired, 'file', 'mimes:jpg,jpeg,png,webp', 'max:51200', new FileTypeValidate(['jpeg', 'jpg', 'png', 'webp'])],
            'gallery'          => 'nullable|array|max:6',
            'gallery.*'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:51200', new FileTypeValidate(['jpeg', 'jpg', 'png', 'webp'])],
            'meta_title'       => 'nullable|string|max:255',
            'meta_description'=> 'nullable|string',
            'meta_keywords'    => 'nullable|string',
            'source_url'       => 'nullable|url|max:500',
            'home_section_override'  => 'nullable|in:new_arrivals,best_selling,recommended,trending',
            'home_section_rank'      => 'nullable|integer|min:0|max:1000000',
            'home_exclude_from_auto' => 'nullable|in:0,1',
            'variant_rows'     => 'nullable|array',
            'variant_rows.*.attr'  => 'nullable|string|max:100',
            'variant_rows.*.value' => 'nullable|string|max:100',
            'variant_rows.*.price' => 'nullable|numeric|min:0',
            'variant_rows.*.qty'   => 'nullable|integer|min:0',
            'digital_item'     => 'nullable|in:0,1',
            'file_type'        => 'nullable|in:1,2',
            'link'             => 'nullable|url|max:500',
            'fabric_type'      => 'nullable|string|max:100',
            'material'         => 'nullable|string|max:255',
            'season'           => 'nullable|string|max:50',
            'color_variants'   => 'nullable',
            'video'            => ['nullable', 'file', 'mimes:mp4,mov,avi,webm', 'max:102400', new VideoMaxDuration(30)],
        ], [
            'image.max' => __('Main image must not be greater than 50 MB.'),
            'gallery.*.max' => __('Each gallery image must not be greater than 50 MB.'),
        ]);

        if ((int) ($request->digital_item ?? 0) === 1 && (int) ($request->file_type ?? 0) === 2 && !$request->filled('link')) {
            $notify[] = ['error', __('For digital product with link, please enter the product URL.')];
            return back()->withInput()->withNotify($notify);
        }

        $hasVariants = $request->has_variants && is_array($request->variant_rows);
        $variantRows = $hasVariants ? array_values(array_filter($request->variant_rows ?? [], function ($r) {
            return !empty($r['attr'] ?? '') && (isset($r['value']) || isset($r['price']));
        })) : [];

        if ($hasVariants && count($variantRows) === 0) {
            $notify[] = ['error', __('Add at least one variant row (attribute, value, price, quantity).')];
            return back()->withInput()->withNotify($notify);
        }

        $totalQty = (int) $request->quantity;
        if ($hasVariants) {
            $totalQty = 0;
            foreach ($variantRows as $r) {
                $totalQty += (int) ($r['qty'] ?? 0);
            }
        }
        if ($totalQty < 1) {
            $notify[] = ['error', __('Stock quantity must be at least 1.')];
            return back()->withInput()->withNotify($notify);
        }

        if ($id) {
            $product = Product::findOrFail($id);
            $message = __('Product updated successfully.');
        } else {
            $product = new Product();
            $message = __('Product added successfully.');
        }

        $slug = null;
        if ($this->productHasColumn('slug')) {
            $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->name);
            if (Product::where('slug', $slug)->where('id', '!=', $product->id ?? 0)->exists()) {
                $slug = $slug . '-' . ($product->id ?? uniqid());
            }
        }

        if ($request->hasFile('image')) {
            try {
                $product->image = fileUploader($request->image, getFilePath('product'), getFileSize('product'), $product->image ?? null);
            } catch (\Exception $e) {
                $notify[] = ['error', __('Could not upload main image.')];
                return back()->withInput()->withNotify($notify);
            }
        } elseif (!$product->image) {
            if (!$id) {
                $notify[] = ['error', __('Main image is required.')];
                return back()->withInput()->withNotify($notify);
            }
        }

        $gallery = $id ? ($product->gallery ?? []) : [];
        if ($request->hasFile('gallery')) {
            $files = $request->file('gallery');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        try {
                            $gallery[] = fileUploader($file, getFilePath('productGallery'), getFileSize('productGallery'));
                        } catch (\Exception $e) {
                            // skip invalid
                        }
                    }
                }
            }
        }

        if (Schema::hasColumn('products', 'video') && $request->hasFile('video')) {
            $file = $request->file('video');
            $allowedMime = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-msvideo'];
            if (in_array($file->getMimeType(), $allowedMime)) {
                try {
                    $product->video = fileUploader($file, getFilePath('productVideo'), null, $product->video ?? null);
                } catch (\Exception $e) {
                    // ignore
                }
            }
        }

        $product->name = $request->name;
        if ($this->productHasColumn('slug')) {
            $product->slug = $slug;
        }
        $product->brand_id = $request->brand_id;
        $product->category_id = $request->category_id;
        $product->subcategory_id = $request->subcategory_id;
        $product->product_sku = $request->filled('product_sku') ? $request->product_sku : (Str::slug($request->name) . '-' . strtoupper(Str::random(6)));
        $product->quantity = $totalQty;
        $product->price = $request->price;
        $product->discount = $request->discount ?? 0;
        $product->discount_type = $request->discount_type ?? 1;
        $product->digital_item = (int) ($request->digital_item ?? 0);
        $product->file_type = (int) ($request->file_type ?? 0);
        $product->link = $request->filled('link') ? $request->link : null;
        $product->fabric_type = $request->filled('fabric_type') ? $request->fabric_type : null;
        $product->material = $request->filled('material') ? $request->material : null;
        $product->season = $request->filled('season') ? $request->season : null;
        if ($request->filled('color_variants')) {
            $cv = $request->color_variants;
            $product->color_variants = is_array($cv) ? array_values(array_filter($cv)) : array_values(array_filter(array_map('trim', explode(',', $cv))));
        } else {
            $product->color_variants = null;
        }
        $product->summary = $request->summary;
        $product->key_features = $request->key_features;
        $product->description = $request->description;
        $product->features = $request->features ? array_values(array_filter($request->features, fn($f) => !empty($f['title']) || !empty($f['description']))) : null;
        $product->gallery = $gallery;
        $product->product_type = 'general';
        $product->original_price = $request->original_price;
        $product->profit_margin = $request->profit_margin;
        $product->low_stock_alert = $request->low_stock_alert;
        $product->warehouse_location = $request->warehouse_location;
        $product->shipping_weight = $request->shipping_weight;
        $product->shipping_class = $request->shipping_class;
        $product->delivery_time = $request->delivery_time;
        if (Schema::hasColumn('products', 'delivery_type')) {
            $product->delivery_type = in_array($request->delivery_type, ['free', 'paid'], true) ? $request->delivery_type : 'free';
            $product->delivery_charge = (float) ($request->delivery_charge ?? 0);
        }
        $product->meta_title = $request->meta_title;
        $product->meta_description = $request->meta_description;
        if ($request->filled('meta_keywords')) {
            $product->meta_keywords = is_array($request->meta_keywords) ? $request->meta_keywords : array_values(array_filter(array_map('trim', explode(',', $request->meta_keywords))));
        }
        $product->source_url = $request->source_url;
        $product->status = 1;
        $product->featured_product = (int) ($request->featured_product ?? 0);
        $product->hot_deals = (int) ($request->hot_deals ?? 0);
        $product->today_deals = (int) ($request->today_deals ?? 0);
        if (Schema::hasColumn('products', 'trending_now')) {
            $product->trending_now = (int) ($request->trending_now ?? 0);
        }
        if (Schema::hasColumn('products', 'home_section_override')) {
            $override = $request->input('home_section_override');
            $product->home_section_override = in_array($override, ['new_arrivals', 'best_selling', 'recommended', 'trending'], true) ? $override : null;
            $product->home_section_rank = max(0, (int) $request->input('home_section_rank', 0));
            $product->home_exclude_from_auto = (int) $request->input('home_exclude_from_auto', 0) ? 1 : 0;
        }
        if ($product->today_deals) {
            $product->featured_product = Status::DISABLE;
            $product->hot_deals = Status::DISABLE;
        } elseif ($product->hot_deals) {
            $product->featured_product = Status::DISABLE;
        }
        $product->has_variants = $hasVariants && count($variantRows) > 0 ? 1 : 0;
        $product->variant_attributes = null;
        if ($hasVariants && count($variantRows) > 0) {
            $attrs = [];
            foreach ($variantRows as $r) {
                $attr = trim($r['attr'] ?? '');
                if ($attr !== '') $attrs[$attr] = ucfirst($attr);
            }
            $product->variant_attributes = $attrs ?: null;
        }
        $product->save();

        if ($hasVariants && count($variantRows) > 0) {
            ProductVariant::where('product_id', $product->id)->delete();
            foreach ($variantRows as $idx => $r) {
                $attr = trim($r['attr'] ?? '');
                $value = trim($r['value'] ?? '');
                $price = (float) ($r['price'] ?? $product->price);
                $qty = (int) ($r['qty'] ?? 0);
                if ($attr === '' || $qty < 0) continue;
                ProductVariant::create([
                    'product_id'    => $product->id,
                    'sku'          => $product->product_sku . '-' . Str::slug($attr . '-' . $value) . '-' . $idx,
                    'attributes'   => [$attr => $value],
                    'price'        => $price,
                    'discount'     => $product->discount ?? 0,
                    'discount_type'=> $product->discount_type ?? 1,
                    'quantity'     => $qty,
                    'status'       => 1,
                ]);
            }
        }

        ProductCacheService::clearProductDetail($product->id);
        $this->clearSpotlightCaches();
        $notify[] = ['success', $message];
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message, 'redirect' => route('admin.product.index')]);
        }
        return redirect()->route('admin.product.index')->withNotify($notify);
    }

    public function status($id)
    {
        Cache::forget('product.detail.' . $id);
        $this->clearSpotlightCaches();
        return Product::changeStatus($id);
    }

    /**
     * Toggle featured. When enabling featured, product is removed from hot deal & today deal
     * so each product appears in only one spotlight section (professional e-commerce behavior).
     */
    public function featured($id)
    {
        $product = Product::findOrFail($id);
        if ((int) $product->featured_product !== Status::ENABLE) {
            Product::where('id', $id)->update([
                'hot_deals' => Status::DISABLE,
                'today_deals' => Status::DISABLE,
            ]);
        }
        $this->clearSpotlightCaches();
        Cache::forget('product.detail.' . $id);
        return Product::changeStatus($id, 'featured_product');
    }

    /**
     * Toggle hot deal. When enabling, product is removed from featured & today deal.
     */
    public function hotDeal($id)
    {
        $product = Product::findOrFail($id);
        if ((int) $product->hot_deals !== Status::ENABLE) {
            Product::where('id', $id)->update([
                'featured_product' => Status::DISABLE,
                'today_deals' => Status::DISABLE,
            ]);
        }
        $this->clearSpotlightCaches();
        Cache::forget('product.detail.' . $id);
        return Product::changeStatus($id, 'hot_deals');
    }

    /**
     * Toggle today deal. When enabling, product is removed from featured & hot deal.
     */
    public function todayDeal($id)
    {
        $product = Product::findOrFail($id);
        if ((int) $product->today_deals !== Status::ENABLE) {
            Product::where('id', $id)->update([
                'featured_product' => Status::DISABLE,
                'hot_deals' => Status::DISABLE,
            ]);
        }
        $this->clearSpotlightCaches();
        Cache::forget('product.detail.' . $id);
        return Product::changeStatus($id, 'today_deals');
    }

    /**
     * Toggle Trending Now. Independent of Featured/Hot Deal/Today Deal (product can be in Trending + one spotlight).
     */
    public function trendingDeal($id)
    {
        $this->clearSpotlightCaches();
        Cache::forget('product.detail.' . $id);
        return Product::changeStatus($id, 'trending_now');
    }

    /** All product reviews (admin) – filter by status, rating, product. */
    public function reviewsIndex(Request $request)
    {
        $pageTitle = __('All Product Reviews');
        $reviewProductCols = ['id', 'name'];
        if ($this->productHasColumn('slug')) {
            $reviewProductCols[] = 'slug';
        }
        $query     = Review::with(['user:id,name', 'product:' . implode(',', $reviewProductCols)]);
        if ($request->filled('rating') && in_array((int) $request->rating, [1, 2, 3, 4, 5], true)) {
            $query->where('stars', (int) $request->rating);
        }
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true)->where('is_private', false);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'private') {
                $query->where('is_private', true);
            }
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->product_id);
        }
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('review_comment', 'like', '%' . $search . '%')
                    ->orWhere('title', 'like', '%' . $search . '%');
            });
        }
        $reviews = $query->latest()->paginate(getPaginate())->withQueryString();
        $products = Product::select('id', 'name')->orderBy('name')->limit(2000)->pluck('name', 'id');
        return view('admin.product.reviews_index', compact('pageTitle', 'reviews', 'products'));
    }

    public function reviews(Request $request, $id)
    {
        $product   = Product::findOrFail($id);
        $pageTitle = 'Reviews of ' . $product->name;
        $query     = Review::where('product_id', $id)->with('user');
        if ($request->filled('rating') && in_array((int) $request->rating, [1, 2, 3, 4, 5], true)) {
            $query->where('stars', (int) $request->rating);
        }
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true)->where('is_private', false);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'private') {
                $query->where('is_private', true);
            }
        }
        $reviews = $query->latest()->paginate(getPaginate());
        return view('admin.product.reviews', compact('pageTitle', 'reviews', 'product'));
    }

    public function reviewApprove($id)
    {
        $review = Review::findOrFail($id);
        $review->is_approved = true;
        $review->save();
        $this->recalculateProductAvgRate($review->product_id);
        $notify[] = ['success', 'Review approved.'];
        return back()->withNotify($notify);
    }

    public function reviewReject($id)
    {
        $review = Review::findOrFail($id);
        $review->is_approved = false;
        $review->save();
        $this->recalculateProductAvgRate($review->product_id);
        $notify[] = ['success', 'Review rejected (hidden from product page).'];
        return back()->withNotify($notify);
    }

    /** Toggle private: only admin sees the review; not shown on product page. */
    public function reviewTogglePrivate($id)
    {
        $review = Review::findOrFail($id);
        $review->is_private = !$review->is_private;
        $review->save();
        $this->recalculateProductAvgRate($review->product_id);
        $notify[] = ['success', $review->is_private ? __('Review is now private (only admin can see).') : __('Review is now public.')];
        return back()->withNotify($notify);
    }

    public function reviewFeatured($id)
    {
        $review = Review::findOrFail($id);
        $review->is_featured = !$review->is_featured;
        $review->save();
        $notify[] = ['success', $review->is_featured ? 'Review marked as featured.' : 'Review unmarked as featured.'];
        return back()->withNotify($notify);
    }

    public function reviewRemove($id)
    {
        $review = Review::findOrFail($id);
        $productId = $review->product_id;
        $review->delete();
        $this->recalculateProductAvgRate($productId);
        $notify[] = ['success', 'Review removed successfully'];
        return back()->withNotify($notify);
    }

    public function reviewBulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:reviews,id']);
        $reviews = Review::whereIn('id', $request->ids)->get();
        $productIds = $reviews->pluck('product_id')->unique();
        $reviews->each->delete();
        foreach ($productIds as $pid) {
            $this->recalculateProductAvgRate($pid);
        }
        $notify[] = ['success', count($request->ids) . ' review(s) deleted.'];
        return back()->withNotify($notify);
    }

    protected function recalculateProductAvgRate($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }
        $approved = Review::where('product_id', $productId)->visibleOnProduct()->get();
        $total = $approved->count();
        $product->avg_rate = $total > 0 ? round($approved->sum('stars') / $total, 2) : 0;
        $product->save();
        Cache::forget('product.detail.' . $productId);
    }

    /**
     * Delete single product (with files and related data)
     */
    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $this->deleteProductAndFiles($product);
        $this->clearSpotlightCaches();

        $notify[] = ['success', 'Product deleted successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Bulk delete selected products
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer|exists:products,id']);
        $ids = $request->ids;
        $count = 0;
        foreach ($ids as $id) {
            $product = Product::find($id);
            if ($product) {
                $this->deleteProductAndFiles($product);
                $count++;
            }
        }
        if ($count > 0) {
            $this->clearSpotlightCaches();
        }
        $notify[] = ['success', $count . ' product(s) deleted successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Bulk edit selected products (status, featured, hot_deals, today_deals)
     */
    public function bulkEdit(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:products,id',
            'action' => 'required|in:enable,disable,featured_on,featured_off,hot_deal_on,hot_deal_off,today_deal_on,today_deal_off,trending_on,trending_off',
        ]);
        $ids = $request->ids;
        $action = $request->action;
        $count = 0;

        foreach ($ids as $id) {
            $product = Product::find($id);
            if (!$product) continue;
            switch ($action) {
                case 'enable':
                    $product->status = Status::ENABLE;
                    break;
                case 'disable':
                    $product->status = Status::DISABLE;
                    break;
                case 'featured_on':
                    $product->featured_product = Status::ENABLE;
                    $product->hot_deals = Status::DISABLE;
                    $product->today_deals = Status::DISABLE;
                    break;
                case 'featured_off':
                    $product->featured_product = Status::DISABLE;
                    break;
                case 'hot_deal_on':
                    $product->hot_deals = Status::ENABLE;
                    $product->featured_product = Status::DISABLE;
                    $product->today_deals = Status::DISABLE;
                    break;
                case 'hot_deal_off':
                    $product->hot_deals = Status::DISABLE;
                    break;
                case 'today_deal_on':
                    $product->today_deals = Status::ENABLE;
                    $product->featured_product = Status::DISABLE;
                    $product->hot_deals = Status::DISABLE;
                    break;
                case 'today_deal_off':
                    $product->today_deals = Status::DISABLE;
                    break;
                case 'trending_on':
                    $product->trending_now = Status::ENABLE;
                    break;
                case 'trending_off':
                    $product->trending_now = Status::DISABLE;
                    break;
            }
            $product->save();
            Cache::forget('product.detail.' . $id);
            $count++;
        }
        $this->clearSpotlightCaches();
        $notify[] = ['success', $count . ' product(s) updated successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Remove product, related records, and files
     */
    protected function deleteProductAndFiles(Product $product)
    {
        $id = $product->id;
        // Remove main image
        if ($product->image) {
            fileManager()->removeFile(getFilePath('product') . '/' . $product->image);
        }
        // Remove gallery images
        foreach ($product->gallery ?? [] as $img) {
            fileManager()->removeFile(getFilePath('productGallery') . '/' . $img);
        }
        // Remove digital file
        if ($product->file) {
            fileManager()->removeFile(getFilePath('productFile') . '/' . $product->file);
        }
        // Remove video
        if (Schema::hasColumn('products', 'video') && $product->video) {
            fileManager()->removeFile(getFilePath('productVideo') . '/' . $product->video);
        }
        // Delete related records (variants, comparisons, reviews; OrderDetail kept for history)
        ProductVariant::where('product_id', $id)->delete();
        ProductComparison::where('product_id', $id)->delete();
        Review::where('product_id', $id)->delete();
        \App\Models\HomepageTopFeature::where('product_id', $id)->update(['product_id' => null]);
        if (class_exists(\App\Models\ProductAttributeValue::class)) {
            \App\Models\ProductAttributeValue::where('product_id', $id)->delete();
        }
        $product->delete();
        Cache::forget('product.detail.' . $id);
    }
}
