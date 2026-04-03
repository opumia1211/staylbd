<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PopupAd;
use Illuminate\Http\Request;

class PopupAdController extends Controller
{
    public function index()
    {
        $pageTitle = __('Popup Ads');
        $ads = PopupAd::ordered()->get();
        return view('admin.popup_ads.index', compact('pageTitle', 'ads'));
    }

    public function create()
    {
        $pageTitle = __('Add Popup Ad');
        $ad = null;
        return view('admin.popup_ads.form', compact('pageTitle', 'ad'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'delay_seconds' => 'required|integer|min:1|max:60',
            'image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,gif,svg', 'max:5120'],
            'link_url' => 'nullable|string|max:500',
            'width' => 'nullable|string|max:100',
            'height' => 'nullable|string|max:100',
            'position' => 'nullable|in:center,top-left,top-right,bottom-left,bottom-right',
            'display_type' => 'nullable|in:popup,inline',
            'inline_placement' => 'nullable|in:sidebar_right,sidebar_left,content_top,content_bottom',
            'show_on_pages' => 'nullable|array',
            'show_on_pages.*' => 'in:all,home,cart,checkout,product_detail,category,user_dashboard,search,contact,wishlist,other',
            'is_active' => 'required|in:0,1',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $ad = new PopupAd();
        $this->fillFromRequest($ad, $request);
        $ad->sort_order = (int) (PopupAd::max('sort_order') ?? 0) + 1;

        if ($request->hasFile('image')) {
            $ad->image = fileUploader($request->image, getFilePath('popupAd'), getFileSize('popupAd'), null);
        }
        $ad->save();

        $notify[] = ['success', __('Popup ad added successfully.')];
        return redirect()->route('admin.popup-ads.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $ad = PopupAd::findOrFail($id);
        $pageTitle = __('Edit Popup Ad');
        return view('admin.popup_ads.form', compact('pageTitle', 'ad'));
    }

    public function update(Request $request, $id)
    {
        $ad = PopupAd::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'delay_seconds' => 'required|integer|min:1|max:60',
            'image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp,gif,svg', 'max:5120'],
            'link_url' => 'nullable|string|max:500',
            'width' => 'nullable|string|max:100',
            'height' => 'nullable|string|max:100',
            'position' => 'nullable|in:center,top-left,top-right,bottom-left,bottom-right',
            'display_type' => 'nullable|in:popup,inline',
            'inline_placement' => 'nullable|in:sidebar_right,sidebar_left,content_top,content_bottom',
            'show_on_pages' => 'nullable|array',
            'show_on_pages.*' => 'in:all,home,cart,checkout,product_detail,category,user_dashboard,search,contact,wishlist,other',
            'is_active' => 'required|in:0,1',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
        ]);

        $this->fillFromRequest($ad, $request);
        if ($request->hasFile('image')) {
            $ad->image = fileUploader($request->image, getFilePath('popupAd'), getFileSize('popupAd'), $ad->image);
        }
        $ad->save();

        $notify[] = ['success', __('Popup ad updated successfully.')];
        return redirect()->route('admin.popup-ads.index')->withNotify($notify);
    }

    public function destroy($id)
    {
        $ad = PopupAd::findOrFail($id);
        if ($ad->image) {
            fileManager()->removeFile(getFilePath('popupAd') . '/' . $ad->image);
        }
        $ad->delete();
        $notify[] = ['success', __('Popup ad deleted.')];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $ad = PopupAd::findOrFail($id);
        $ad->is_active = $ad->is_active ? 0 : 1;
        $ad->save();
        $notify[] = ['success', __('Status updated.')];
        return back()->withNotify($notify);
    }

    private function fillFromRequest(PopupAd $ad, Request $request): void
    {
        $ad->name = $request->name;
        $ad->delay_seconds = (int) $request->delay_seconds;
        $ad->link_url = $request->filled('link_url') ? $request->link_url : null;
        $ad->width = $request->filled('width') ? $request->width : null;
        $ad->height = $request->filled('height') ? $request->height : null;
        $ad->position = $request->filled('position') ? $request->position : 'center';
        $ad->display_type = $request->filled('display_type') && in_array($request->display_type, ['popup', 'inline'], true)
            ? $request->display_type
            : 'popup';
        $ad->inline_placement = ($ad->display_type === 'inline' && $request->filled('inline_placement'))
            ? $request->inline_placement
            : null;
        $raw = $request->input('show_on_pages');
        $pages = is_array($raw) ? array_values(array_filter($raw)) : [];
        $ad->show_on_pages = $pages === [] ? ['all'] : $pages;
        $ad->is_active = (int) $request->is_active;
        $ad->start_at = $request->filled('start_at') ? $request->start_at : null;
        $ad->end_at = $request->filled('end_at') ? $request->end_at : null;
    }
}
