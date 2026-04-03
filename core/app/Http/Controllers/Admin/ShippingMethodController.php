<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShippingMethodController extends Controller
{
	/** Shipping hub: Zones | Methods | Rules (landing page) */
	public function hub()
	{
		$pageTitle = __('Shipping');
		$hasZones = Schema::hasTable('shipping_zones');
		$hasRules = Schema::hasTable('shipping_rules');
		$methodsCount = \App\Models\ShippingMethod::count();
		$zonesCount = $hasZones ? \App\Models\ShippingZone::count() : 0;
		return view('admin.shipping.hub', compact('pageTitle', 'hasZones', 'hasRules', 'methodsCount', 'zonesCount'));
	}

	public function index(Request $request)
	{
		$pageTitle = __('Manage Shipping Method');
		$hasZonesTable = Schema::hasTable('shipping_zones');
		$hasZoneColumn = Schema::hasColumn('shipping_methods', 'shipping_zone_id');

		$query = ShippingMethod::query()->searchable(['name']);

		// Filter: status
		if ($request->filled('status') && in_array($request->status, ['0', '1'])) {
			$query->where('status', (int) $request->status);
		}

		// Filter: express (only if column exists)
		if (Schema::hasColumn('shipping_methods', 'is_express') && $request->filled('express')) {
			$query->where('is_express', $request->express === '1' ? 1 : 0);
		}

		// Filter: zone (only if zones table and column exist)
		if ($hasZonesTable && $hasZoneColumn && $request->filled('zone_id')) {
			$query->where('shipping_zone_id', $request->zone_id);
		}

		// Eager load zone only when table exists
		if ($hasZonesTable && $hasZoneColumn) {
			$query->with('zone:id,name,type');
		}

		$perPage = in_array((int) $request->per_page, [10, 20, 50, 100], true) ? (int) $request->per_page : getPaginate();
		$shippings = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

		$zones = $hasZonesTable
			? \App\Models\ShippingZone::orderBy('type')->orderBy('name')->get(['id', 'name', 'type'])
			: collect();

		return view('admin.shipping', compact('pageTitle', 'shippings', 'zones', 'hasZonesTable'));
	}

	public function store(Request $request, $id = 0)
	{
		$rules = [
			'name'  => 'required|unique:shipping_methods,name,' . $id,
			'price' => 'required|numeric|min:0',
			'estimated_days' => 'nullable|string|max:50',
			'courier_name' => 'nullable|string|max:100',
			'is_express' => 'nullable|in:0,1',
		];
		if (\Illuminate\Support\Facades\Schema::hasTable('shipping_zones')) {
			$rules['shipping_zone_id'] = 'nullable|exists:shipping_zones,id';
		}
		$request->validate($rules);

		$hasZonesTable = Schema::hasTable('shipping_zones');
		$hasZoneColumn = Schema::hasColumn('shipping_methods', 'shipping_zone_id');
		$hasExpressCol = Schema::hasColumn('shipping_methods', 'is_express');

		$message = DB::transaction(function () use ($request, $id, $hasZonesTable, $hasZoneColumn, $hasExpressCol) {
			if ($id) {
				$shipping = ShippingMethod::findOrFail($id);
				$message  = "Shipping method updated successfully";
			} else {
				$shipping = new ShippingMethod();
				$message  = "Shipping method added successfully";
			}
			$shipping->name  = $request->name;
			$shipping->price = $request->price;
			if ($hasZonesTable && $hasZoneColumn) {
				$shipping->shipping_zone_id = $request->shipping_zone_id ?: null;
			}
			$shipping->base_price = $request->price;
			$shipping->estimated_days = $request->estimated_days;
			$shipping->courier_name = $request->courier_name;
			if ($hasExpressCol) {
				$shipping->is_express = (bool) $request->is_express;
			}
			$shipping->save();
			return $message;
		});

		$notify[] = ["success", $message];
		return back()->withNotify($notify);
	}

	public function status($id)
	{
		return ShippingMethod::changeStatus($id);
	}
}
