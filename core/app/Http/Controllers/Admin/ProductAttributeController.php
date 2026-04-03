<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Admin: Product Attributes (Size, Color, Storage, etc.)
 * Professional eCommerce – dynamic attribute system for filters and variants.
 */
class ProductAttributeController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Product Attributes');
        $perPage = (int) $request->get('per_page', getPaginate());
        $perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : getPaginate();

        $query = ProductAttribute::query()->withCount('categories');

        if ($request->filled('search')) {
            $query->searchable(['name', 'slug']);
        }
        if ($request->filled('type') && in_array($request->type, ['select', 'color', 'text', 'number'], true)) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status') && in_array($request->status, ['0', '1'], true)) {
            $query->where('status', (int) $request->status);
        }

        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowedSort = ['name', 'slug', 'type', 'sort_order', 'created_at', 'categories_count'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'sort_order';
        }
        if ($sortBy === 'categories_count') {
            $query->orderBy('categories_count', $sortDir);
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $attributes = $query->paginate($perPage)->withQueryString();

        $stats = Cache::remember('admin.attributes.stats', 60, function () {
            return [
                'total'   => ProductAttribute::count(),
                'active'  => ProductAttribute::where('status', 1)->count(),
                'by_type' => ProductAttribute::selectRaw('type, count(*) as c')->groupBy('type')->pluck('c', 'type'),
            ];
        });

        return view('admin.attributes.index', compact('pageTitle', 'attributes', 'stats', 'sortBy', 'sortDir'));
    }

    public function create()
    {
        $pageTitle = __('Add Attribute');
        $attribute = new ProductAttribute();
        $attribute->type = ProductAttribute::TYPE_SELECT;
        $attribute->sort_order = 0;
        $attribute->status = 1;
        return view('admin.attributes.form', compact('pageTitle', 'attribute'));
    }

    public function store(Request $request)
    {
        $data = $this->validateAttribute($request);
        $attribute = new ProductAttribute();
        $this->fillAttribute($attribute, $data);
        $attribute->save();

        $notify[] = ['success', __('Attribute created successfully.')];
        return redirect()->route('admin.attributes.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $pageTitle = __('Edit Attribute') . ' – ' . $attribute->name;
        return view('admin.attributes.form', compact('pageTitle', 'attribute'));
    }

    public function update(Request $request, $id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $data = $this->validateAttribute($request, $id);
        $this->fillAttribute($attribute, $data);
        $attribute->save();

        $notify[] = ['success', __('Attribute updated successfully.')];
        return redirect()->route('admin.attributes.index')->withNotify($notify);
    }

    public function status($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->status = $attribute->status ? 0 : 1;
        $attribute->save();

        $notify[] = ['success', $attribute->status ? __('Attribute enabled.') : __('Attribute disabled.')];
        return back()->withNotify($notify);
    }

    public function destroy($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->categories()->detach();
        $attribute->productValues()->delete();
        $attribute->delete();

        $notify[] = ['success', __('Attribute deleted.')];
        return back()->withNotify($notify);
    }

    /** Bulk status toggle (enable/disable selected) */
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:product_attributes,id',
            'status' => 'required|in:0,1',
        ]);
        ProductAttribute::whereIn('id', $request->ids)->update(['status' => (int) $request->status]);
        $notify[] = ['success', __(':count attribute(s) updated.', ['count' => count($request->ids)])];
        return back()->withNotify($notify);
    }

    /** Duplicate an attribute */
    public function duplicate($id)
    {
        $source = ProductAttribute::findOrFail($id);
        $new = $source->replicate();
        $new->name = $source->name . ' (Copy)';
        $new->slug = $source->slug . '-copy-' . substr(uniqid(), -4);
        $new->save();
        $notify[] = ['success', __('Attribute duplicated.')];
        return redirect()->route('admin.attributes.edit', $new->id)->withNotify($notify);
    }

    protected function validateAttribute(Request $request, $id = null)
    {
        $uniqueSlug = 'unique:product_attributes,slug,' . (int) $id;
        $request->validate([
            'name'       => 'required|string|max:100',
            'slug'       => 'required|string|max:100|' . $uniqueSlug,
            'type'       => 'required|in:select,color,text,number',
            'values'     => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'status'     => 'required|in:0,1',
        ]);

        $values = $request->values;
        $valuesArray = [];
        if ($values) {
            $valuesArray = array_map('trim', array_filter(explode(',', $values)));
        }

        return [
            'name'       => $request->name,
            'slug'       => Str::slug($request->slug ?: $request->name),
            'type'       => $request->type,
            'values'     => $valuesArray,
            'sort_order' => (int) ($request->sort_order ?? 0),
            'status'     => (int) $request->status,
        ];
    }

    protected function fillAttribute(ProductAttribute $attribute, array $data)
    {
        $attribute->name = $data['name'];
        $attribute->slug = $data['slug'];
        $attribute->type = $data['type'];
        $attribute->values = $data['values'];
        $attribute->sort_order = $data['sort_order'];
        $attribute->status = $data['status'];
    }
}
