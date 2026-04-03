<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\ProductCacheService;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
	protected array $sortColumns = ['name', 'status', 'products_count', 'created_at', 'category_id'];

	public function index(Request $request)
	{
		$pageTitle = "Manage Subcategory";

		$perPage = (int) $request->get('per_page', getPaginate());
		$perPage = $perPage >= 5 && $perPage <= 100 ? $perPage : getPaginate();

		$sortBy = $request->get('sort_by', 'name');
		if (!in_array($sortBy, $this->sortColumns, true)) {
			$sortBy = 'name';
		}
		$sortDir = strtolower($request->get('sort_dir', 'asc'));
		if (!in_array($sortDir, ['asc', 'desc'], true)) {
			$sortDir = 'asc';
		}

		$categories = Category::orderBy('name')->get();

		$subcategories = Subcategory::query()
			->with('category')
			->withCount('products')
			->searchable(['name', 'category:name'])
			->orderBy($sortBy, $sortDir)
			->paginate($perPage)
			->withQueryString();

		return view('admin.sub_category', compact('pageTitle', 'subcategories', 'categories', 'sortBy', 'sortDir'));
	}

	public function store(Request $request, $id = null)
	{
		$request->validate([
			'name'        => 'required|max:255',
			'category_id' => 'required|integer|exists:categories,id',
		]);

		if ($id) {
			$subcategory = Subcategory::findOrFail($id);
			$message     = "Subcategory updated successfully";
		} else {
			$subcategory = new Subcategory();
			$message     = "Subcategory created successfully";
		}

		$subcategory->name        = $request->name;
		$subcategory->category_id  = $request->category_id;
		$subcategory->save();

		ProductCacheService::clearProductListings();

		$notify[] = ["success", $message];
		return back()->withNotify($notify);
	}

	public function status($id)
	{
		$response = Subcategory::changeStatus($id);
		ProductCacheService::clearProductListings();
		return $response;
	}

	public function bulkStatus(Request $request)
	{
		$request->validate([
			'ids'   => 'required|array',
			'ids.*' => 'integer|exists:subcategories,id',
			'value' => 'required|in:0,1',
		]);

		$value = (int) $request->value;
		Subcategory::whereIn('id', $request->ids)->update(['status' => $value]);

		$notify[] = ['success', count($request->ids) . ' subcategory/subcategories updated successfully.'];
		return back()->withNotify($notify);
	}
}
