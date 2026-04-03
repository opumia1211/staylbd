<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    public function index()
    {
        $pageTitle = __('Location Management');
        $divisions = Division::orderBy('sort_order')->orderBy('name_en')->get();
        $districts = District::with('division')->orderBy('division_id')->orderBy('sort_order')->orderBy('name_en')->get();
        $thanas = Thana::with('district.division')->orderBy('district_id')->orderBy('sort_order')->orderBy('name_en')->get();
        $deliveryZones = DeliveryZone::with('thana.district.division')->orderBy('thana_id')->get();
        return view('admin.location.index', compact('pageTitle', 'divisions', 'districts', 'thanas', 'deliveryZones'));
    }

    // ---------- Divisions ----------
    public function createDivision()
    {
        $pageTitle = __('Add Division');
        $division = null;
        return view('admin.location.form_division', compact('pageTitle', 'division'));
    }

    public function editDivision($id)
    {
        $pageTitle = __('Edit Division');
        $division = Division::findOrFail($id);
        return view('admin.location.form_division', compact('pageTitle', 'division'));
    }

    public function storeDivision(Request $request)
    {
        $request->validate([
            'name_en' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
        ]);
        $maxOrder = Division::max('sort_order') ?? 0;
        Division::create([
            'name_en' => trim($request->name_en),
            'name_bn' => trim($request->name_bn ?? ''),
            'sort_order' => $maxOrder + 1,
            'status' => 1,
        ]);
        $this->clearLocationCache();
        $notify[] = ['success', __('Division added successfully.')];
        return redirect()->route('admin.locations.index')->withNotify($notify);
    }

    public function updateDivision(Request $request, $id)
    {
        $division = Division::findOrFail($id);
        $request->validate([
            'name_en' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
        ]);
        $division->update([
            'name_en' => trim($request->name_en),
            'name_bn' => trim($request->name_bn ?? ''),
        ]);
        $this->clearLocationCache();
        $notify[] = ['success', __('Division updated.')];
        return redirect()->route('admin.locations.index')->withNotify($notify);
    }

    public function destroyDivision($id)
    {
        $division = Division::findOrFail($id);
        if ($division->districts()->count() > 0) {
            $notify[] = ['error', __('Remove districts under this division first.')];
            return back()->withNotify($notify);
        }
        $division->delete();
        $this->clearLocationCache();
        $notify[] = ['success', __('Division deleted.')];
        return back()->withNotify($notify);
    }

    public function toggleDivisionStatus($id)
    {
        $division = Division::findOrFail($id);
        $division->status = $division->status ? 0 : 1;
        $division->save();
        $this->clearLocationCache();
        $notify[] = ['success', $division->status ? __('Division activated.') : __('Division deactivated.')];
        return back()->withNotify($notify);
    }

    // ---------- Districts ----------
    public function createDistrict()
    {
        $pageTitle = __('Add District');
        $district = null;
        $divisions = Division::orderBy('sort_order')->orderBy('name_en')->get();
        return view('admin.location.form_district', compact('pageTitle', 'district', 'divisions'));
    }

    public function editDistrict($id)
    {
        $pageTitle = __('Edit District');
        $district = District::with('division')->findOrFail($id);
        $divisions = Division::orderBy('sort_order')->orderBy('name_en')->get();
        return view('admin.location.form_district', compact('pageTitle', 'district', 'divisions'));
    }

    public function storeDistrict(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name_en' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
        ]);
        $maxOrder = District::where('division_id', $request->division_id)->max('sort_order') ?? 0;
        District::create([
            'division_id' => $request->division_id,
            'name_en' => trim($request->name_en),
            'name_bn' => trim($request->name_bn ?? ''),
            'sort_order' => $maxOrder + 1,
            'status' => 1,
        ]);
        $this->clearLocationCache();
        $notify[] = ['success', __('District added successfully.')];
        return redirect()->route('admin.locations.index')->withNotify($notify);
    }

    public function updateDistrict(Request $request, $id)
    {
        $district = District::findOrFail($id);
        $request->validate([
            'name_en' => 'required|string|max:100',
            'name_bn' => 'nullable|string|max:100',
        ]);
        $district->update([
            'name_en' => trim($request->name_en),
            'name_bn' => trim($request->name_bn ?? ''),
        ]);
        $this->clearLocationCache();
        $notify[] = ['success', __('District updated.')];
        return redirect()->route('admin.locations.index')->withNotify($notify);
    }

    public function destroyDistrict($id)
    {
        $district = District::findOrFail($id);
        if ($district->thanas()->count() > 0) {
            $notify[] = ['error', __('Remove thanas under this district first.')];
            return back()->withNotify($notify);
        }
        $district->delete();
        $this->clearLocationCache();
        $notify[] = ['success', __('District deleted.')];
        return back()->withNotify($notify);
    }

    public function toggleDistrictStatus($id)
    {
        $district = District::findOrFail($id);
        $district->status = $district->status ? 0 : 1;
        $district->save();
        $this->clearLocationCache();
        $notify[] = ['success', $district->status ? __('District activated.') : __('District deactivated.')];
        return back()->withNotify($notify);
    }

    // ---------- Thanas ----------
    public function createThana()
    {
        $pageTitle = __('Add Thana');
        $thana = null;
        $districts = District::with('division')->orderBy('division_id')->orderBy('sort_order')->orderBy('name_en')->get();
        return view('admin.location.form_thana', compact('pageTitle', 'thana', 'districts'));
    }

    public function editThana($id)
    {
        $pageTitle = __('Edit Thana');
        $thana = Thana::with('district.division')->findOrFail($id);
        $districts = District::with('division')->orderBy('division_id')->orderBy('sort_order')->orderBy('name_en')->get();
        return view('admin.location.form_thana', compact('pageTitle', 'thana', 'districts'));
    }

    public function storeThana(Request $request)
    {
        $request->validate([
            'district_id' => 'required|exists:districts,id',
            'name_en' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'postal_code' => 'nullable|string|max:20',
        ]);
        $maxOrder = Thana::where('district_id', $request->district_id)->max('sort_order') ?? 0;
        Thana::create([
            'district_id' => $request->district_id,
            'name_en' => trim($request->name_en),
            'name_bn' => trim($request->name_bn ?? ''),
            'postal_code' => trim($request->postal_code ?? ''),
            'sort_order' => $maxOrder + 1,
            'status' => 1,
        ]);
        $this->clearLocationCache();
        $notify[] = ['success', __('Thana added successfully.')];
        return redirect()->route('admin.locations.index')->withNotify($notify);
    }

    public function updateThana(Request $request, $id)
    {
        $thana = Thana::findOrFail($id);
        $request->validate([
            'name_en' => 'required|string|max:150',
            'name_bn' => 'nullable|string|max:150',
            'postal_code' => 'nullable|string|max:20',
        ]);
        $thana->update([
            'name_en' => trim($request->name_en),
            'name_bn' => trim($request->name_bn ?? ''),
            'postal_code' => trim($request->postal_code ?? ''),
        ]);
        $this->clearLocationCache();
        $notify[] = ['success', __('Thana updated.')];
        return redirect()->route('admin.locations.index')->withNotify($notify);
    }

    public function destroyThana($id)
    {
        $thana = Thana::findOrFail($id);
        $thana->deliveryZones()->delete();
        $thana->delete();
        $this->clearLocationCache();
        $notify[] = ['success', __('Thana deleted.')];
        return back()->withNotify($notify);
    }

    public function toggleThanaStatus($id)
    {
        $thana = Thana::findOrFail($id);
        $thana->status = $thana->status ? 0 : 1;
        $thana->save();
        $this->clearLocationCache();
        $notify[] = ['success', $thana->status ? __('Thana activated.') : __('Thana deactivated.')];
        return back()->withNotify($notify);
    }

    // ---------- Delivery Zones ----------
    public function createDeliveryZone()
    {
        $pageTitle = __('Add Delivery Zone');
        $zone = null;
        $thanas = Thana::with('district.division')->orderBy('district_id')->orderBy('sort_order')->orderBy('name_en')->get();
        $general = gs();
        return view('admin.location.form_delivery', compact('pageTitle', 'zone', 'thanas', 'general'));
    }

    public function editDeliveryZone($id)
    {
        $pageTitle = __('Edit Delivery Zone');
        $zone = DeliveryZone::with('thana.district.division')->findOrFail($id);
        $thanas = Thana::with('district.division')->orderBy('district_id')->orderBy('sort_order')->orderBy('name_en')->get();
        $general = gs();
        return view('admin.location.form_delivery', compact('pageTitle', 'zone', 'thanas', 'general'));
    }

    public function storeDeliveryZone(Request $request)
    {
        $request->validate([
            'thana_id' => 'required|exists:thanas,id',
            'delivery_charge' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|string|max:50',
        ]);
        DeliveryZone::create([
            'thana_id' => $request->thana_id,
            'delivery_charge' => $request->delivery_charge,
            'estimated_days' => trim($request->estimated_days ?? ''),
            'status' => 1,
        ]);
        $this->clearLocationCache();
        $notify[] = ['success', __('Delivery zone added.')];
        return redirect()->route('admin.locations.index')->withNotify($notify);
    }

    public function updateDeliveryZone(Request $request, $id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $request->validate([
            'delivery_charge' => 'required|numeric|min:0',
            'estimated_days' => 'nullable|string|max:50',
        ]);
        $zone->update([
            'delivery_charge' => $request->delivery_charge,
            'estimated_days' => trim($request->estimated_days ?? ''),
        ]);
        $this->clearLocationCache();
        $notify[] = ['success', __('Delivery zone updated.')];
        return redirect()->route('admin.locations.index')->withNotify($notify);
    }

    public function destroyDeliveryZone($id)
    {
        DeliveryZone::findOrFail($id)->delete();
        $this->clearLocationCache();
        $notify[] = ['success', __('Delivery zone deleted.')];
        return back()->withNotify($notify);
    }

    public function toggleDeliveryZoneStatus($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        $zone->status = $zone->status ? 0 : 1;
        $zone->save();
        $this->clearLocationCache();
        $notify[] = ['success', $zone->status ? __('Delivery zone activated.') : __('Delivery zone deactivated.')];
        return back()->withNotify($notify);
    }

    protected function clearLocationCache()
    {
        Cache::forget('GeneralSetting');
    }
}
