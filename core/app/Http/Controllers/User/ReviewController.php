<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Rules\NoLinksInText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function index()
    {
        $pageTitle = "Review products";

        $productId = OrderDetail::with('order')->whereHas('order', function ($order) {
            $order->where('user_id', auth()->id())->delivered();
        })->distinct('product_id')->pluck('product_id');
        $products = Product::active()->whereIn('id', $productId)->with('category')->with('reviews', function ($q) {
            $q->where('user_id', auth()->id());
        })->paginate(getPaginate());

        $emptyMessage = __('You have no delivered orders to review. Add a review after your order is delivered.');
        return view($this->activeTemplate . 'user.review.index', compact('pageTitle', 'products', 'emptyMessage'));
    }

    public function create($slug, $id)
    {
        $pageTitle = 'Add Review';
        $product   = Product::active()->findOrFail($id);
        return view($this->activeTemplate . 'user.review.create', compact('pageTitle', 'product'));
    }

    public function store(Request $request, $id)
    {
        $rules = [
            'rating'        => 'required|integer|in:1,2,3,4,5',
            'title'        => ['nullable', 'string', 'max:255', new NoLinksInText(__('Review Title'))],
            'review_comment' => ['required', 'string', 'max:5000', new NoLinksInText(__('Your Review'))],
            'review_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ];
        $request->merge(['rating' => $request->input('rating', $request->input('stars'))]);
        $request->validate($rules);

        $product = Product::findOrFail($id);
        $userId = auth()->id();

        $existing = Review::where('user_id', $userId)->where('product_id', $id)->first();
        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => __('You have already reviewed this product. You can edit it from below.')]);
            }
            $notify[] = ['error', 'You have already reviewed this product.'];
            return back()->withNotify($notify);
        }

        $hasPurchased = hasPurchasedProduct($userId, $product->id);

        $review = new Review();
        $review->user_id = $userId;
        $review->product_id = $product->id;
        $review->stars = (int) $request->rating;
        $review->title = strip_tags($request->title ?? strLimit(strip_tags($request->review_comment), 100) ?: 'Review');
        $review->review_comment = strip_tags($request->review_comment);
        $review->is_verified_purchase = $hasPurchased;
        $review->is_approved = true;
        $review->helpful_count = 0;

        if ($request->hasFile('review_image')) {
            try {
                $path = getFilePath('reviewImage');
                $size = getFileSize('reviewImage');
                $uploaded = fileUploader($request->review_image, $path, $size);
                $review->images = [$uploaded];
                // Ensure optimization + WebP conversion (FileManager may already run it; run again for correct path)
                $fullPath = public_path(rtrim($path, '/') . '/' . $uploaded);
                if (function_exists('optimizeUploadedImage') && $fullPath && is_file($fullPath)) {
                    optimizeUploadedImage($path . '/' . $uploaded);
                }
            } catch (\Exception $e) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => __('Image upload failed.')]);
                }
                $notify[] = ['error', 'Image upload failed.'];
                return back()->withNotify($notify);
            }
        }

        $review->save();
        $this->updateProductAvgRate($product);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Review submitted successfully. Thank you!'),
                'review' => [
                    'id' => $review->id,
                    'stars' => $review->stars,
                    'title' => $review->title,
                    'review_comment' => $review->review_comment,
                    'is_verified_purchase' => $review->is_verified_purchase,
                    'created_at' => $review->created_at->format('M d, Y'),
                    'user' => ['username' => auth()->user()->username],
                ],
            ]);
        }

        $notify[] = ['success', 'Review added successfully.'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id)
    {
        $review = Review::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $rules = [
            'rating'        => 'required|integer|in:1,2,3,4,5',
            'title'        => ['required', 'string', 'max:255', new NoLinksInText(__('Review Title'))],
            'review_comment' => ['required', 'string', 'max:5000', new NoLinksInText(__('Your Review'))],
            'review_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ];
        $request->validate($rules);

        $review->stars = (int) $request->rating;
        $review->title = strip_tags($request->title);
        $review->review_comment = strip_tags($request->review_comment);

        if ($request->hasFile('review_image')) {
            try {
                $path = getFilePath('reviewImage');
                $size = getFileSize('reviewImage');
                $old = ($review->images && isset($review->images[0])) ? $review->images[0] : null;
                $newImg = fileUploader($request->review_image, $path, $size, $old);
                $review->images = [$newImg];
                if (function_exists('optimizeUploadedImage') && $newImg) {
                    optimizeUploadedImage($path . '/' . $newImg);
                }
            } catch (\Exception $e) {
                // keep existing images on failure
            }
        }

        $review->save();
        $this->updateProductAvgRate($review->product);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Review updated successfully.'),
            ]);
        }
        $notify[] = ['success', 'Review updated successfully.'];
        return back()->withNotify($notify);
    }

    public function destroy($id)
    {
        $review = Review::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $product = $review->product;
        $review->delete();
        $this->updateProductAvgRate($product);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => __('Review deleted.')]);
        }
        $notify[] = ['success', 'Review removed.'];
        return back()->withNotify($notify);
    }

    protected function updateProductAvgRate(Product $product)
    {
        $approved = Review::where('product_id', $product->id)->visibleOnProduct()->get();
        $total = $approved->count();
        if ($total === 0) {
            $product->avg_rate = 0;
        } else {
            $product->avg_rate = round($approved->sum('stars') / $total, 2);
        }
        $product->save();
        Cache::forget('product.detail.' . $product->id);
    }
}
