<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Rules\FileTypeValidate;
use App\Services\HomepageDataService;
use App\Services\ProductCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
	/**
	 * Allowed sort columns for category list (advanced optimization).
	 */
	protected array $sortColumns = ['name', 'status', 'featured', 'products_count', 'subcategories_count', 'created_at'];

	public function index(Request $request)
	{
		$pageTitle = "All Categories";

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

		$categories = Category::query()
			->withCount(['subcategories', 'product'])
			->searchable(['name'])
			->orderBy($sortBy, $sortDir)
			->paginate($perPage)
			->appends(request()->all());

		return view('admin.category', compact('pageTitle', 'categories', 'sortBy', 'sortDir'));
	}

	public function store(Request $request, $id = 0)
	{
		$isRequired = $id ? 'nullable' : 'required';

		$request->validate([
			'name'           => 'required|max:255|unique:categories,name,' . (int) $id,
			'name_bn'        => 'nullable|max:255',
			'image'          => [$isRequired, 'file', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp', 'svg'])],
			'publish_status' => 'nullable|in:pending,public,scheduled',
			'scheduled_at'   => 'nullable|date',
			'home_line'      => 'nullable|integer|min:1|max:10',
			'home_order'     => 'nullable|integer|min:0|max:9999',
		]);

		if ($id) {
			$category = Category::findOrFail($id);
			$message  = "Category update successfully";
		} else {
			$category = new Category();
			$message  = "Category added successfully";
		}

		if ($request->hasFile('image')) {
			try {
				$category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'), @$category->image);
				// Auto WebP conversion for faster loading (skip SVG)
				$path = getFilePath('category');
				$fullPath = public_path($path . '/' . $category->image);
				$ext = strtolower(pathinfo($category->image, PATHINFO_EXTENSION) ?? '');
				if (in_array($ext, ['jpg', 'jpeg', 'png'], true) && file_exists($fullPath) && is_file($fullPath)) {
					try {
						$optimizer = app(\App\Services\ImageOptimizationService::class);
						$webpPath = $optimizer->convertToWebP($fullPath, 85);
						if ($webpPath && file_exists($webpPath)) {
							@unlink($fullPath);
							$category->image = basename($webpPath);
						}
					} catch (\Throwable $e) {
						// keep original on failure
					}
				}
			} catch (\Exception $exp) {
				$notify[] = ['error', 'Couldn\'t upload your image'];
				return back()->withNotify($notify);
			}
		}

		$category->name = $request->name;
		$category->name_bn = $request->name_bn;
		if (\Illuminate\Support\Facades\Schema::hasColumn($category->getTable(), 'home_line')) {
			$category->home_line = (int) $request->input('home_line', 1);
		}
		if (\Illuminate\Support\Facades\Schema::hasColumn($category->getTable(), 'home_order')) {
			$category->home_order = (int) $request->input('home_order', 0);
		}
		if (\Illuminate\Support\Facades\Schema::hasColumn($category->getTable(), 'publish_status')) {
			$category->publish_status = $request->get('publish_status', 'public');
		}
		if (\Illuminate\Support\Facades\Schema::hasColumn($category->getTable(), 'scheduled_at')) {
			$category->scheduled_at = $request->filled('scheduled_at') ? $request->scheduled_at : null;
		}
		$category->save(); // ডাটাবেজে সঙ্গে সঙ্গে সেভ হয়; ইউজার পেজে আপডেট দেখাবে

		$this->invalidateCategoryAllCache();

		$notify[] = ["success", $message];
		return back()->withNotify($notify);
	}

	/** Invalidate frontend All Categories & product listing cache when admin changes categories. */
	protected function invalidateCategoryAllCache(): void
	{
		Cache::put('category_all_updated', time());
		ProductCacheService::clearProductListings();
		HomepageDataService::clearCache();
	}

	public function status($id)
	{
		$response = Category::changeStatus($id);
		$this->invalidateCategoryAllCache();
		return $response;
	}

	public function featured($id)
	{
		$response = Category::changeStatus($id, 'featured');
		$this->invalidateCategoryAllCache();
		return $response;
	}

	/**
	 * Bulk update status (enable/disable) for selected categories.
	 */
	public function bulkStatus(Request $request)
	{
		$request->validate([
			'ids'   => 'required|array',
			'ids.*' => 'integer|exists:categories,id',
			'value' => 'required|in:0,1',
		]);

		$value = (int) $request->value;
		Category::whereIn('id', $request->ids)->update(['status' => $value]);

		$this->invalidateCategoryAllCache();

		$notify[] = ['success', count($request->ids) . ' category/categories updated successfully.'];
		return back()->withNotify($notify);
	}

	/**
	 * Bulk update featured flag for selected categories.
	 */
	public function bulkFeatured(Request $request)
	{
		$request->validate([
			'ids'   => 'required|array',
			'ids.*' => 'integer|exists:categories,id',
			'value' => 'required|in:0,1',
		]);

		$value = (int) $request->value;
		Category::whereIn('id', $request->ids)->update(['featured' => $value]);

		$this->invalidateCategoryAllCache();

		$notify[] = ['success', count($request->ids) . ' category/categories featured state updated.'];
		return back()->withNotify($notify);
	}
}
