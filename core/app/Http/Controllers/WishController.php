<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishController extends Controller
{

	public function addWishList(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'product_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()]);
		}

		$userId = auth()->id();

		$product = Product::active()->find($request->product_id);
		if (!$product) {
			return response()->json(['error' => __('Product not found or inactive')]);
		}

		if ($userId) {

			$wishlist = Wishlist::where('user_id', $userId)->where('product_id', $product->id)->first();

			if ($wishlist) {
				return response()->json(['error' => 'Already exists in wishlist']);
			}

			if (Wishlist::where('user_id', $userId)->count() >= Wishlist::WISHLIST_MAX) {
				return response()->json(['error' => __('Maximum :max products allowed in wishlist. Remove some to add more.', ['max' => Wishlist::WISHLIST_MAX])]);
			}

			$wishlist             = new Wishlist();
			$wishlist->user_id    = $userId;
			$wishlist->product_id = $product->id;
			$wishlist->save();
		} else {

			$wishlist = session()->get('wishlist', []);

			if (!is_array($wishlist)) {
				$wishlist = [];
			}

			if (isset($wishlist[$product->id])) {
				return response()->json(['error' => 'Already exists in wishlist']);
			}

			if (count($wishlist) >= Wishlist::WISHLIST_MAX) {
				return response()->json(['error' => __('Maximum :max products allowed in wishlist. Remove some to add more.', ['max' => Wishlist::WISHLIST_MAX])]);
			}

			$wishlist[$product->id] = [
				"product_id" => $product->id,
			];

			session()->put('wishlist', $wishlist);
		}

		activity_log(\App\Models\UserActivityLog::WISHLIST_ADD, 'Added to wishlist: ' . $product->name, 'product', $product->id);

		return response()->json(['success' => 'Product added in wishlist']);
	}

	public function wishListCount()
	{
		try {
			$userId = auth()->id();

			if ($userId) {
				$wishlist = Wishlist::where('user_id', $userId)->select('product_id')->get();
				return response()->json($wishlist);
			}

			$data = session()->get('wishlist', []);
			if (!is_array($data)) {
				$data = [];
			}
			$list = array_values(array_map(function ($id) {
				return ['product_id' => (int) $id];
			}, array_keys($data)));
			return response()->json($list);
		} catch (\Throwable $e) {
			report($e);
			return response()->json([]);
		}
	}

	public function wishListProduct()
	{
		$pageTitle   = 'My Wishlist';
		$emptyMessage = 'No product in your wishlist yet';
		$userId      = auth()->id();
		$productIds  = [];

		if ($userId) {
			$productIds = Wishlist::where('user_id', $userId)->pluck('product_id')->toArray();
		} else {
			$wishlist = session()->get('wishlist', []);
			$productIds = is_array($wishlist) ? array_keys($wishlist) : [];
		}

		$products = collect();
		if (!empty($productIds)) {
			$productIds = array_values(array_map('intval', $productIds));
			$products = Product::active()->whereIn('id', $productIds)->with(['category:id,name', 'brand:id,name'])->withCount('reviews')->get();
			// Keep order same as wishlist (first added = first shown)
			$products = $products->sortBy(function ($p) use ($productIds) {
				$pos = array_search((int) $p->id, $productIds);
				return $pos !== false ? $pos : 999;
			})->values();
		}

		return view($this->activeTemplate . 'wishlist', compact('pageTitle', 'products', 'emptyMessage'));
	}

	/** Wishlist inside user dashboard (sidebar + menu bar stay). */
	public function wishListProductDashboard()
	{
		$pageTitle   = 'My Wishlist';
		$emptyMessage = 'No product in your wishlist yet';
		$userId      = auth()->id();
		$productIds  = [];

		if ($userId) {
			$productIds = Wishlist::where('user_id', $userId)->pluck('product_id')->toArray();
		} else {
			$wishlist = session()->get('wishlist', []);
			$productIds = is_array($wishlist) ? array_keys($wishlist) : [];
		}

		$products = collect();
		if (!empty($productIds)) {
			$productIds = array_values(array_map('intval', $productIds));
			$products = Product::active()->whereIn('id', $productIds)->with(['category:id,name', 'brand:id,name', 'activeVariants'])->withCount('reviews')->get();
			$products = $products->sortBy(function ($p) use ($productIds) {
				$pos = array_search((int) $p->id, $productIds);
				return $pos !== false ? $pos : 999;
			})->values();
		}

		$headerActions = ''; // Toolbar is inside wishlist_page_content
		$wishlistMax = Wishlist::WISHLIST_MAX;
		return view($this->activeTemplate . 'user.wishlist', compact('pageTitle', 'products', 'emptyMessage', 'headerActions', 'wishlistMax'));
	}



	public function removeWishlist(Request $request)
	{

		$validator = Validator::make($request->all(), [
			'product_id' => 'required|integer',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->first()]);
		}

		$userId = auth()->id();

		if ($userId) {
			$wishlist = Wishlist::where('product_id', $request->product_id)->where('user_id', $userId)->first();
			if ($wishlist) {
				$wishlist->delete();
			}
		} else {
			$wishlists = session()->get('wishlist', []);
			$wishlists = is_array($wishlists) ? $wishlists : [];
			unset($wishlists[$request->product_id]);
			session()->put('wishlist', $wishlists);
		}

		activity_log(\App\Models\UserActivityLog::WISHLIST_REMOVE, 'Removed from wishlist', 'product', (int) $request->product_id);

		return response()->json(['success' => 'Product remove from wishlist']);
	}

	/**
	 * Clear all items from wishlist (logged-in user or session).
	 */
	public function clearWishlist(Request $request)
	{
		$userId = auth()->id();
		if ($userId) {
			Wishlist::where('user_id', $userId)->delete();
		} else {
			session()->forget('wishlist');
		}
		activity_log(\App\Models\UserActivityLog::WISHLIST_REMOVE, 'Wishlist cleared', null, null);
		return response()->json(['success' => __('Wishlist cleared successfully.')]);
	}

	/**
	 * Restore guest wishlist from localStorage so it persists after browser refresh/clear.
	 * Only for guests; logged-in users use DB. Payload: { "product_ids": [ 1, 2, 3 ] }
	 */
	public function restoreGuestWishlist(Request $request)
	{
		if (auth()->id()) {
			return response()->json(['success' => true, 'message' => 'User wishlist is in database']);
		}
		$rules = [
			'product_ids'   => 'required|array',
			'product_ids.*' => 'integer|min:1',
		];
		$request->validate($rules);
		$productIds = array_values(array_unique(array_filter(array_map('intval', $request->input('product_ids', [])))));
		if (count($productIds) > Wishlist::WISHLIST_MAX) {
			$productIds = array_slice($productIds, 0, Wishlist::WISHLIST_MAX);
		}
		$wishlist = session()->get('wishlist', []);
		if (!is_array($wishlist)) {
			$wishlist = [];
		}
		$added = 0;
		foreach ($productIds as $productId) {
			if (isset($wishlist[$productId])) {
				continue;
			}
			$product = Product::active()->find($productId);
			if (!$product) {
				continue;
			}
			$wishlist[$productId] = ['product_id' => $productId];
			$added++;
		}
		session()->put('wishlist', $wishlist);
		return response()->json(['success' => true, 'count' => count($wishlist)]);
	}
}