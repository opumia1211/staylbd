<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Batch snapshot for storefront polling (listings): id, effective price, aggregate stock.
 */
class ProductsRealtimeController extends Controller
{
    public const MAX_IDS = 60;

    public function index(Request $request): JsonResponse
    {
        $ids = $this->parseIds($request->query('ids'));

        if ($ids === []) {
            return $this->jsonResponse(['products' => []]);
        }

        // Light existence check, then full rows + variants only for storefront-available products.
        $existingIdSet = Product::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->flip()
            ->all();

        $products = Product::query()
            ->available()
            ->with(['activeVariants'])
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');

        $out = [];
        foreach ($ids as $id) {
            if (! isset($existingIdSet[$id])) {
                continue;
            }
            $product = $products->get($id);
            if (! $product) {
                $out[] = [
                    'id' => $id,
                    'price' => 0.0,
                    'stock' => 0,
                ];
                continue;
            }

            $pricing = productDisplayPricing($product);
            $hasVariants = (bool) $product->has_variants
                && $product->activeVariants->isNotEmpty();
            $stock = $hasVariants
                ? (int) $product->activeVariants->sum('quantity')
                : (int) $product->quantity;

            $compareAt = $pricing['compare_at'];
            $out[] = [
                'id' => $id,
                'price' => round((float) $pricing['effective'], 2),
                'compare_at' => $compareAt !== null ? round((float) $compareAt, 2) : null,
                'has_savings' => (bool) $pricing['has_savings'],
                'save_percent' => (int) $pricing['save_percent'],
                'stock' => $stock,
            ];
        }

        return $this->jsonResponse(['products' => $out]);
    }

    /**
     * @return list<int>
     */
    private function parseIds(mixed $raw): array
    {
        if (is_array($raw)) {
            $parts = $raw;
        } else {
            $parts = preg_split('/\s*,\s*/', (string) $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        $ids = [];
        foreach ($parts as $part) {
            $n = (int) $part;
            if ($n > 0) {
                $ids[] = $n;
            }
        }

        $ids = array_values(array_unique($ids));

        return array_slice($ids, 0, self::MAX_IDS);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function jsonResponse(array $data): JsonResponse
    {
        return response()
            ->json($data)
            ->header('Cache-Control', 'no-store, private');
    }
}
