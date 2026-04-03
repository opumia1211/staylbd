<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
	public function index(Request $request)
	{
		$pageTitle = __('Manage Coupon');
		$query = Coupon::query()->withCount('ordersUsed')->searchable(['name']);

		// Filter: status (1=enabled, 0=disabled)
		if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
			$query->where('status', (int) $request->status);
		}

		// Filter: date range (active, expired, upcoming)
		if ($request->filled('date_filter')) {
			$today = now()->format('Y-m-d');
			if ($request->date_filter === 'active') {
				$query->where('start_date', '<=', $today)->where('end_date', '>=', $today);
			} elseif ($request->date_filter === 'expired') {
				$query->where('end_date', '<', $today);
			} elseif ($request->date_filter === 'upcoming') {
				$query->where('start_date', '>', $today);
			}
		}

		// Filter: type (welcome, flash, seasonal, etc.)
		if ($request->filled('type')) {
			$query->where('type', $request->type);
		}

		$perPage = (int) $request->get('per_page', getPaginate());
		$perPage = in_array($perPage, [10, 20, 50, 100], true) ? $perPage : getPaginate();
		$coupons = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

		return view('admin.coupon', compact('coupons', 'pageTitle'));
	}

	public function store(Request $request, $id = null)
	{
        $startDateValidation = $id ? 'required|date|date_format:Y-m-d' : 'required|date|date_format:Y-m-d|after_or_equal:today';

		$rules = [
			'name'               => 'required|alpha_dash|max:255|unique:coupons,name,' . $id,
			'discount'           => 'required|numeric|gt:0',
			'start_date'         => $startDateValidation,
			'end_date'           => 'required|date|date_format:Y-m-d|after_or_equal:start_date',
			'discount_type'      => 'required|in:1,2',
			'min_order'          => 'required|numeric|gte:0',
			'usage_limit'        => 'nullable|integer|min:1',
			'max_discount'       => 'nullable|numeric|gte:0',
			'per_user_limit'     => 'nullable|integer|min:1',
			'description'        => 'nullable|string|max:1000',
			'type'               => 'nullable|string|max:50',
			'is_first_order_only'=> 'nullable|boolean',
		];
		$request->validate($rules);

		if ($id) {
			$coupon  = Coupon::findOrFail($id);
			$message = __('Coupon updated successfully');
		} else {
			$coupon  = new Coupon();
			$message = __('Coupon added successfully');
		}

		$coupon->name          = $request->name;
		$coupon->discount      = $request->discount;
		$coupon->start_date    = $request->start_date;
		$coupon->end_date      = $request->end_date;
		$coupon->discount_type = $request->discount_type;
		$coupon->min_order     = $request->min_order;
		$coupon->usage_limit   = $request->filled('usage_limit') ? (int) $request->usage_limit : null;
		$coupon->max_discount  = $request->filled('max_discount') ? $request->max_discount : null;
		$coupon->per_user_limit= $request->filled('per_user_limit') ? (int) $request->per_user_limit : null;
		$coupon->description   = $request->filled('description') ? trim($request->description) : null;
		$coupon->type               = $request->filled('type') ? trim($request->type) : null;
		$coupon->is_first_order_only = $request->boolean('is_first_order_only');
		$coupon->save();

		$notify[] = ['success', $message];
		return back()->withNotify($notify);
	}

	public function duplicate($id)
	{
		$original = Coupon::findOrFail($id);
		$coupon = $original->replicate();
		$coupon->name = $original->name . '-COPY-' . substr(uniqid(), -4);
		$coupon->status = Status::DISABLE;
		$coupon->save();

		$notify[] = ['success', __('Coupon duplicated successfully')];
		return back()->withNotify($notify);
	}

	public function status($id)
	{
		return Coupon::changeStatus($id);
	}
}
