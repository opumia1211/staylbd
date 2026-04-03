<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use App\Models\ShippingZoneArea;
use App\Models\ShippingZoneCountry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ShippingZoneController extends Controller
{
    public function index()
    {
        $pageTitle = __('Shipping Zones');
        if (!Schema::hasTable('shipping_zones')) {
            $notify[] = ['info', __('Run one-time setup:') . ' php artisan migrate'];
            return redirect()->route('admin.shipping.index')->withNotify($notify);
        }
        $zones = ShippingZone::withCount(['countries', 'areas', 'methods'])->orderBy('type')->orderBy('name')->paginate(getPaginate());
        return view('admin.shipping.zones_index', compact('pageTitle', 'zones'));
    }

    public function create()
    {
        $pageTitle = 'Add Shipping Zone';
        return view('admin.shipping.zone_form', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:national,international',
            'base_price' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|string|max:50',
            'free_shipping' => 'nullable|in:0,1',
            'cod_enabled' => 'nullable|in:0,1',
        ]);

        $data = [
            'name' => $request->name,
            'type' => $request->type,
            'base_price' => $request->base_price ?? 0,
            'estimated_days' => $request->estimated_days,
            'free_shipping' => (bool) ($request->free_shipping ?? 0),
            'status' => 1,
        ];
        if (Schema::hasColumn((new ShippingZone)->getTable(), 'cod_enabled')) {
            $data['cod_enabled'] = (bool) ($request->cod_enabled ?? 1);
        }
        ShippingZone::create($data);

        $notify[] = ['success', 'Shipping zone created successfully.'];
        return redirect()->route('admin.shipping.zones.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $zone = ShippingZone::with(['countries', 'areas'])->findOrFail($id);
        $pageTitle = 'Edit Zone: ' . $zone->name;
        return view('admin.shipping.zone_form', compact('pageTitle', 'zone'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:national,international',
            'base_price' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|string|max:50',
            'free_shipping' => 'nullable|in:0,1',
            'cod_enabled' => 'nullable|in:0,1',
        ]);

        $zone = ShippingZone::findOrFail($id);
        $zone->name = $request->name;
        $zone->type = $request->type;
        $zone->base_price = $request->base_price ?? 0;
        $zone->estimated_days = $request->estimated_days;
        $zone->free_shipping = (bool) ($request->free_shipping ?? 0);
        if (Schema::hasColumn($zone->getTable(), 'cod_enabled')) {
            $zone->cod_enabled = (bool) ($request->cod_enabled ?? 1);
        }
        $zone->save();

        $notify[] = ['success', 'Shipping zone updated successfully.'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return ShippingZone::changeStatus($id);
    }

    public function addCountry(Request $request, $zoneId)
    {
        $request->validate([
            'country_iso' => 'required|string|size:2',
            'country_name' => 'nullable|string|max:100',
            'shipping_price' => 'nullable|numeric|min:0',
        ]);
        $zone = ShippingZone::findOrFail($zoneId);
        ShippingZoneCountry::updateOrCreate(
            ['shipping_zone_id' => $zone->id, 'country_iso' => strtoupper($request->country_iso)],
            [
                'country_name' => $request->country_name ?? $request->country_iso,
                'shipping_price' => $request->filled('shipping_price') ? $request->shipping_price : null,
                'status' => 1,
            ]
        );
        $notify[] = ['success', 'Country added to zone.'];
        return back()->withNotify($notify);
    }

    public function removeCountry($zoneId, $countryId)
    {
        ShippingZoneCountry::where('shipping_zone_id', $zoneId)->where('id', $countryId)->delete();
        $notify[] = ['success', 'Country removed from zone.'];
        return back()->withNotify($notify);
    }

    public function addArea(Request $request, $zoneId)
    {
        $request->validate([
            'area_name' => 'required|string|max:100',
            'district_names' => 'nullable|string',
            'shipping_price' => 'nullable|numeric|min:0',
            'free_shipping' => 'nullable|in:0,1',
        ]);
        $zone = ShippingZone::findOrFail($zoneId);
        $districts = array_filter(array_map('trim', explode(',', $request->district_names ?? '')));
        ShippingZoneArea::create([
            'shipping_zone_id' => $zone->id,
            'area_name' => $request->area_name,
            'district_names' => $districts,
            'shipping_price' => $request->filled('shipping_price') ? $request->shipping_price : null,
            'free_shipping' => (bool) ($request->free_shipping ?? 0),
            'status' => 1,
        ]);
        $notify[] = ['success', 'Area added to zone.'];
        return back()->withNotify($notify);
    }

    public function removeArea($zoneId, $areaId)
    {
        ShippingZoneArea::where('shipping_zone_id', $zoneId)->where('id', $areaId)->delete();
        $notify[] = ['success', 'Area removed from zone.'];
        return back()->withNotify($notify);
    }
}
