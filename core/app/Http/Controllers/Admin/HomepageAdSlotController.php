<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageAdSlot;
use App\Services\HomepageDataService;
use App\Services\HomepageLayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomepageAdSlotController extends Controller
{
    public function index()
    {
        $pageTitle = __('Homepage ads');
        $ads = collect();
        if (Schema::hasTable('homepage_ad_slots')) {
            $ads = HomepageAdSlot::query()->orderBy('sort_order')->orderByDesc('id')->get();
        }

        return view('admin.frontend.homepage_ads.index', compact('pageTitle', 'ads'));
    }

    public function create()
    {
        $pageTitle = __('Add homepage ad');
        if (!Schema::hasTable('homepage_ad_slots')) {
            $notify[] = ['error', __('Homepage ads table not found. Run migrate first.')];
            return redirect()->route('admin.frontend.sections.homepageAds')->withNotify($notify);
        }
        $ad = new HomepageAdSlot([
            'is_active' => true,
            'open_new_tab' => true,
            'frame_style' => 'none',
            'width_mode' => 'full',
            'source_type' => 'upload',
            'sort_order' => (int) HomepageAdSlot::query()->max('sort_order') + 1,
            'position' => 'inline',
            'side' => 'bottom',
            'size_type' => 'auto',
            'display_pages' => 'all',
            'z_index' => 1100,
        ]);

        return view('admin.frontend.homepage_ads.form', compact('pageTitle', 'ad'));
    }

    public function edit(int $id)
    {
        if (!Schema::hasTable('homepage_ad_slots')) {
            $notify[] = ['error', __('Homepage ads table not found. Run migrate first.')];
            return redirect()->route('admin.frontend.sections.homepageAds')->withNotify($notify);
        }
        $ad = HomepageAdSlot::query()->findOrFail($id);
        $pageTitle = __('Edit homepage ad');

        return view('admin.frontend.homepage_ads.form', compact('pageTitle', 'ad'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('homepage_ad_slots')) {
            $notify[] = ['error', __('Homepage ads table not found. Run migrate first.')];
            return redirect()->route('admin.frontend.sections.homepageAds')->withNotify($notify);
        }
        $data = $this->validatedData($request, null);
        $created = HomepageAdSlot::query()->create($data);

        HomepageLayoutService::persistLayoutAfterAdSlotChange();
        HomepageDataService::clearCache();

        $notify[] = ['success', __('Ad created. Add it into Block order and Save layout.')];
        return redirect()->route('admin.frontend.sections.homepageAds')->withNotify($notify)->with('hp_highlight_ad', $created->id);
    }

    public function update(Request $request, int $id)
    {
        if (!Schema::hasTable('homepage_ad_slots')) {
            $notify[] = ['error', __('Homepage ads table not found. Run migrate first.')];
            return redirect()->route('admin.frontend.sections.homepageAds')->withNotify($notify);
        }
        $ad = HomepageAdSlot::query()->findOrFail($id);
        $ad->update($this->validatedData($request, $ad));

        HomepageLayoutService::persistLayoutAfterAdSlotChange();
        HomepageDataService::clearCache();

        $notify[] = ['success', __('Ad updated.')];
        return redirect()->route('admin.frontend.sections.homepageAds')->withNotify($notify)->with('hp_highlight_ad', $id);
    }

    public function destroy(int $id)
    {
        if (!Schema::hasTable('homepage_ad_slots')) {
            $notify[] = ['error', __('Homepage ads table not found. Run migrate first.')];
            return back()->withNotify($notify);
        }
        $ad = HomepageAdSlot::query()->findOrFail($id);
        $old = $ad->image;
        $ad->delete();

        // Best-effort delete image file
        if (is_string($old) && trim($old) !== '') {
            $base = basename($old);
            $relDir = HomepageAdSlot::imageDiskPath(); // assets/images/...
            $paths = [];
            if (function_exists('public_path')) {
                $paths[] = rtrim(public_path($relDir), '/\\') . DIRECTORY_SEPARATOR . $base;
            }
            $paths[] = rtrim(dirname(base_path()), '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relDir) . DIRECTORY_SEPARATOR . $base;
            foreach ($paths as $p) {
                if (is_file($p)) {
                    @unlink($p);
                }
            }
        }

        HomepageLayoutService::persistLayoutAfterAdSlotChange();
        HomepageDataService::clearCache();

        $notify[] = ['success', __('Ad removed.')];
        return back()->withNotify($notify);
    }

    public function toggleActive(Request $request, int $id)
    {
        if (!Schema::hasTable('homepage_ad_slots')) {
            $msg = __('Homepage ads table not found. Run migrate first.');
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            $notify[] = ['error', $msg];
            return back()->withNotify($notify);
        }

        $request->validate([
            'is_active' => 'required|in:0,1',
        ]);

        $ad = HomepageAdSlot::query()->findOrFail($id);
        $ad->is_active = (int) $request->input('is_active') === 1;
        $ad->save();

        HomepageLayoutService::persistLayoutAfterAdSlotChange();
        HomepageDataService::clearCache();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'id' => $ad->id,
                'is_active' => (bool) $ad->is_active,
            ]);
        }

        $notify[] = ['success', __('Ad status updated.')];
        return back()->withNotify($notify)->with('hp_highlight_ad', $ad->id);
    }

    private function validatedData(Request $request, ?HomepageAdSlot $existing): array
    {
        $validated = $request->validate([
            'admin_title' => 'required|string|max:191',
            'advertiser_name' => 'nullable|string|max:191',
            'link_url' => 'nullable|string|max:512',
            'open_new_tab' => 'nullable|boolean',
            'frame_style' => 'required|in:none,thin,card,minimal,bordered',
            'width_mode' => 'required|in:full,wide,half,third,quarter',
            'source_type' => 'required|in:upload,image_url,embed_url',
            'position' => 'required|in:inline,custom,fixed,floating',
            'display_pages' => 'required|in:all,homepage,non_home,custom_path',
            'custom_path' => 'nullable|string|max:255',
            'side' => 'nullable|in:top,bottom,left,right,top-left,top-right,bottom-left,bottom-right,center',
            'top' => 'nullable|integer',
            'bottom' => 'nullable|integer',
            'left' => 'nullable|integer',
            'right' => 'nullable|integer',
            'max_height_px' => 'nullable|integer|min:20|max:1200',
            'size_type' => 'required|in:auto,custom',
            'custom_width' => 'nullable|string|max:16',
            'custom_height' => 'nullable|string|max:16',
            'z_index' => 'nullable|integer|min:1|max:99999',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0|max:65535',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:6144',
            'external_url' => 'nullable|string|max:2048',
        ]);

        $out = [
            'admin_title' => trim($validated['admin_title']),
            'advertiser_name' => $validated['advertiser_name'] ? trim($validated['advertiser_name']) : null,
            'link_url' => $validated['link_url'] ? trim($validated['link_url']) : null,
            'open_new_tab' => $request->boolean('open_new_tab', true),
            'frame_style' => $validated['frame_style'],
            'width_mode' => $validated['width_mode'],
            'source_type' => $validated['source_type'],
            'position' => $validated['position'],
            'display_pages' => $validated['display_pages'],
            'custom_path' => $validated['custom_path'] ? trim($validated['custom_path']) : null,
            'side' => $validated['side'],
            'top' => isset($validated['top']) ? (int) $validated['top'] : null,
            'bottom' => isset($validated['bottom']) ? (int) $validated['bottom'] : null,
            'left' => isset($validated['left']) ? (int) $validated['left'] : null,
            'right' => isset($validated['right']) ? (int) $validated['right'] : null,
            'max_height_px' => isset($validated['max_height_px']) ? (int) $validated['max_height_px'] : null,
            'size_type' => $validated['size_type'],
            'custom_width' => $validated['custom_width'],
            'custom_height' => $validated['custom_height'],
            'z_index' => isset($validated['z_index']) ? (int) $validated['z_index'] : 1100,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) ($validated['sort_order'] ?? 0),
            'external_url' => $validated['external_url'] ? trim($validated['external_url']) : null,
        ];

        if ($request->hasFile('image')) {
            $dir = HomepageAdSlot::imageDiskPath();
            $out['image'] = fileUploader($request->file('image'), $dir, null, $existing?->image);
            $out['image'] = basename((string) $out['image']);
        } else {
            if ($existing) {
                $out['image'] = $existing->image;
            }
        }

        if ($out['source_type'] === 'upload' && trim((string) ($out['image'] ?? '')) === '') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'image' => __('Image is required for uploaded ad type.'),
            ]);
        }

        // Backward compatibility: if migrations were not run yet, save only existing columns.
        $advancedCols = [
            'source_type',
            'position',
            'display_pages',
            'custom_path',
            'side',
            'top',
            'bottom',
            'left',
            'right',
            'size_type',
            'custom_width',
            'custom_height',
            'z_index',
            'external_url',
        ];
        foreach ($advancedCols as $col) {
            if (!Schema::hasColumn('homepage_ad_slots', $col)) {
                unset($out[$col]);
            }
        }

        // Legacy fallback defaults for older schema.
        if (!Schema::hasColumn('homepage_ad_slots', 'max_height_px')) {
            unset($out['max_height_px']);
        }

        return $out;
    }
}

