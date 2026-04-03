<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * List products (paginated). Optional filters: category_id, search, per_page.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 15), 50);
        $query = Product::available()
            ->with(['category:id,name', 'brand:id,name'])
            ->withCount('reviews')
            ->latest('id');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('product_sku', 'like', $term)
                    ->orWhere('summary', 'like', $term);
            });
        }
        if ($request->filled('featured')) {
            $query->where('featured_product', 1);
        }
        if ($request->filled('today_deals')) {
            $query->todayDeal();
        }

        $products = $query->paginate($perPage);

        $items = $products->getCollection()->map(function ($product) {
            return $this->formatProduct($product);
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Featured products (for homepage / app).
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 20);
        $products = Product::available()
            ->featured()
            ->with(['category:id,name', 'brand:id,name'])
            ->withCount('reviews')
            ->latest('id')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(fn ($p) => $this->formatProduct($p)),
        ]);
    }

    /**
     * Today's deal products.
     */
    public function todayDeals(Request $request): JsonResponse
    {
        $limit = min((int) $request->get('limit', 10), 20);
        $products = Product::available()
            ->todayDeal()
            ->with(['category:id,name', 'brand:id,name'])
            ->withCount('reviews')
            ->latest('id')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(fn ($p) => $this->formatProduct($p)),
        ]);
    }

    /**
     * Single product detail.
     */
    public function show(int $id): JsonResponse
    {
        $product = Product::available()
            ->with(['category:id,name,slug', 'brand:id,name', 'subcategory:id,name,slug', 'activeVariants', 'reviews' => fn ($q) => $q->latest()->take(10)])
            ->withCount('reviews')
            ->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $data = $this->formatProduct($product, true);
        $data['description'] = $product->description;
        $data['key_features'] = $product->key_features;
        $data['features'] = $product->features;
        $data['gallery'] = $product->gallery ?? [];
        $data['variants'] = $product->activeVariants->map(function ($v) {
            return [
                'id' => $v->id,
                'price' => $v->price,
                'quantity' => $v->quantity,
                'attributes' => $v->attributes ?? [],
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    private function formatProduct(Product $product, bool $includeSummary = false): array
    {
        $price = $product->price;
        $discount = $product->discount ?? 0;
        $discountType = $product->discount_type ?? 1;
        $finalPrice = showDiscountPrice($price, $discount, $discountType);

        $out = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => slug($product->name),
            'sku' => $product->product_sku,
            'image' => getImage(getFilePath('product') . '/' . $product->image, getFileSize('product')),
            'price' => $price,
            'discount' => $discount,
            'discount_type' => $discountType,
            'final_price' => $finalPrice,
            'quantity' => $product->quantity,
            'has_variants' => (bool) $product->has_variants,
            'category' => $product->category ? ['id' => $product->category->id, 'name' => $product->category->name] : null,
            'brand' => $product->brand ? ['id' => $product->brand->id, 'name' => $product->brand->name] : null,
            'reviews_count' => $product->reviews_count ?? 0,
            'avg_rate' => $product->avg_rate ?? 0,
        ];
        if ($includeSummary) {
            $out['summary'] = $product->summary;
        }
        return $out;
    }
}
