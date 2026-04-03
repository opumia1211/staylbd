<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * List active categories (for filters / navigation).
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name', 'image']);

        $data = $categories->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => \Illuminate\Support\Str::slug($c->name ?? ''),
                'image' => !empty($c->image) ? getImage(getFilePath('category') . '/' . $c->image, getFileSize('category')) : null,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Products in a category (paginated).
     */
    public function products(Request $request, int $id): JsonResponse
    {
        $category = Category::active()->find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found.'], 404);
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $products = Product::available()
            ->where('category_id', $id)
            ->with(['category:id,name', 'brand:id,name'])
            ->withCount('reviews')
            ->latest('id')
            ->paginate($perPage);

        $items = $products->getCollection()->map(function ($product) {
            $price = $product->price;
            $discount = $product->discount ?? 0;
            $discountType = $product->discount_type ?? 1;
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => slug($product->name),
                'sku' => $product->product_sku,
                'image' => getImage(getFilePath('product') . '/' . $product->image, getFileSize('product')),
                'price' => $price,
                'final_price' => showDiscountPrice($price, $discount, $discountType),
                'quantity' => $product->quantity,
                'has_variants' => (bool) $product->has_variants,
                'category' => ['id' => $category->id, 'name' => $category->name],
                'brand' => $product->brand ? ['id' => $product->brand->id, 'name' => $product->brand->name] : null,
                'reviews_count' => $product->reviews_count ?? 0,
            ];
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
}
