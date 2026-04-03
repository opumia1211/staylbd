<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

/**
 * Admin: Assign Product Attributes to Categories.
 * Which category uses which attribute (Size, Color, etc.) – required/variant/sort.
 */
class CategoryAttributeController extends Controller
{
    public function index()
    {
        $pageTitle = __('Category Attributes');
        $categories = Category::query()
            ->select('id', 'name')
            ->with(['attributes' => function ($q) {
                $q->orderBy('category_attributes.sort_order');
            }])
            ->orderBy('name')
            ->get();

        $allAttributes = ProductAttribute::active()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'type', 'sort_order', 'status']);

        return view('admin.category_attributes.index', compact('pageTitle', 'categories', 'allAttributes'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|exists:categories,id',
            'attribute_ids' => 'nullable|array',
            'attribute_ids.*' => 'integer|exists:product_attributes,id',
            'pivot' => 'nullable|array',
            'pivot.*.is_required' => 'nullable|in:0,1',
            'pivot.*.is_variant'  => 'nullable|in:0,1',
            'pivot.*.sort_order'  => 'nullable|integer|min:0',
        ]);

        $category = Category::findOrFail($request->category_id);
        $attributeIds = array_map('intval', (array) $request->input('attribute_ids', []));
        $attributeIds = array_filter($attributeIds);
        $pivot = (array) $request->input('pivot', []);

        $sync = [];
        foreach ($attributeIds as $idx => $attrId) {
            $p = $pivot[$attrId] ?? $pivot[(string) $attrId] ?? [];
            $sync[$attrId] = [
                'is_required' => (int) (is_array($p) ? ($p['is_required'] ?? 0) : 0),
                'is_variant'  => (int) (is_array($p) ? ($p['is_variant'] ?? 1) : 1),
                'sort_order'  => (int) (is_array($p) ? ($p['sort_order'] ?? $idx) : $idx),
            ];
        }

        $category->attributes()->sync($sync);

        $notify[] = ['success', __('Category attributes updated.')];
        return back()->withNotify($notify);
    }
}
