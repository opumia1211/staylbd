<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomepageTopFeature;
use App\Models\Product;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class TopFeatureController extends Controller
{
    /**
     * Validation rules for store/update (DRY).
     */
    private function getValidationRules(Request $request, ?int $id = null): array
    {
        $rules = [
            'title'               => 'required|string|max:255',
            'icon_image'          => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png', 'webp', 'gif'])],
            'background_style'     => 'nullable|string|max:100',
            'product_id'          => 'nullable|exists:products,id',
            'category_id'         => 'nullable|exists:categories,id',
            'offer_price'         => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'offer_start'         => 'nullable|date',
            'offer_end'           => $id ? 'nullable|date' : 'nullable|date|after_or_equal:offer_start',
            'redirect_url'        => 'nullable|string|max:500',
            'status'              => 'required|in:0,1',
        ];
        return $rules;
    }

    /**
     * Fill model from request (excluding file).
     */
    private function fillFeatureFromRequest(HomepageTopFeature $feature, Request $request): void
    {
        $feature->title = $request->title;
        $feature->background_style = $request->background_style ?: null;
        $feature->product_id = $request->product_id ?: null;
        $feature->category_id = $request->category_id ?: null;
        $feature->offer_price = $request->offer_price !== '' && $request->offer_price !== null ? $request->offer_price : null;
        $feature->discount_percentage = $request->discount_percentage !== '' && $request->discount_percentage !== null ? $request->discount_percentage : null;
        $feature->offer_start = $request->offer_start ?: null;
        $feature->offer_end = $request->offer_end ?: null;
        $feature->redirect_url = $request->filled('redirect_url') ? $request->redirect_url : null;
        $feature->status = (int) $request->status;
    }

    public function index()
    {
        $pageTitle = __('Top Feature Boxes');
        $features = HomepageTopFeature::ordered()->with(['product:id,name', 'category:id,name'])->get();
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);
        $products = Product::available()->orderBy('name')->limit(500)->get(['id', 'name']);
        $stats = [
            'total'   => $features->count(),
            'active'  => $features->where('status', 1)->count(),
            'hidden'  => $features->where('status', 0)->count(),
        ];
        return view('admin.product.top_feature.index', compact('pageTitle', 'features', 'categories', 'products', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate($this->getValidationRules($request));

        $feature = new HomepageTopFeature();
        $this->fillFeatureFromRequest($feature, $request);
        $feature->sort_order = (int) (HomepageTopFeature::max('sort_order') ?? 0) + 1;

        if ($request->hasFile('icon_image')) {
            try {
                $feature->icon_image = fileUploader(
                    $request->icon_image,
                    getFilePath('topFeature'),
                    getFileSize('topFeature')
                );
            } catch (\Exception $e) {
                $notify[] = ['error', __('Image could not be uploaded.')];
                return back()->withNotify($notify);
            }
        }

        $feature->save();
        clearHomeSectionCache();
        $notify[] = ['success', __('Feature box added successfully.')];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $feature = HomepageTopFeature::findOrFail($id);
        $request->validate($this->getValidationRules($request, $id));

        $this->fillFeatureFromRequest($feature, $request);

        if ($request->hasFile('icon_image')) {
            try {
                $feature->icon_image = fileUploader(
                    $request->icon_image,
                    getFilePath('topFeature'),
                    getFileSize('topFeature'),
                    $feature->icon_image
                );
            } catch (\Exception $e) {
                $notify[] = ['error', __('Image could not be uploaded.')];
                return back()->withNotify($notify);
            }
        }

        $feature->save();
        clearHomeSectionCache();
        $notify[] = ['success', __('Feature box updated successfully.')];
        return back()->withNotify($notify);
    }

    public function destroy($id)
    {
        $feature = HomepageTopFeature::findOrFail($id);
        if ($feature->icon_image) {
            fileManager()->removeFile(getFilePath('topFeature') . '/' . $feature->icon_image);
        }
        $feature->delete();
        clearHomeSectionCache();
        $notify[] = ['success', __('Feature box deleted.')];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $feature = HomepageTopFeature::findOrFail($id);
        $feature->status = $feature->status ? 0 : 1;
        $feature->save();
        clearHomeSectionCache();
        $notify[] = ['success', __('Status updated.')];
        return back()->withNotify($notify);
    }

    public function reorder(Request $request)
    {
        $order = $request->input('order');
        if (!is_array($order)) {
            return response()->json(['success' => false, 'message' => __('Invalid order')], 422);
        }
        $request->merge(['order' => $order]);
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'order'   => 'required|array',
            'order.*' => 'integer|exists:homepage_top_features,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }
        foreach ($order as $sort => $id) {
            HomepageTopFeature::where('id', $id)->update(['sort_order' => $sort]);
        }
        clearHomeSectionCache();
        return response()->json(['success' => true, 'message' => __('Order saved.')]);
    }
}
