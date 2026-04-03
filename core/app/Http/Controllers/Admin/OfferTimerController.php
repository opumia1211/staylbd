<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OfferTimer;
use App\Models\Product;
use Illuminate\Http\Request;

class OfferTimerController extends Controller
{
    public function index()
    {
        $pageTitle = __('Offer Timers');
        $timers = OfferTimer::ordered()->get();
        $products = Product::available()->select('id', 'name')->orderBy('name')->limit(300)->get();
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);
        return view('admin.offer_timers.index', compact('pageTitle', 'timers', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'end_at' => 'required|date|after:now',
            'style' => 'required|in:bar_small,bar_large,full_width',
            'position' => 'required|in:header,below_header,cart_top,checkout_top,product_detail,category_top,content_top,content_bottom,user_dashboard_top,floating',
            'bar_width' => 'nullable|string|max:50',
            'bar_height' => 'nullable|string|max:50',
            'show_on_pages' => 'required|array',
            'show_on_pages.*' => 'in:all,home,cart,checkout,product_detail,category,user_dashboard',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'link_url' => 'nullable|string|max:500',
            'is_active' => 'required|in:0,1',
        ]);

        $timer = new OfferTimer();
        $this->fillFromRequest($timer, $request);
        $timer->sort_order = (int) (OfferTimer::max('sort_order') ?? 0) + 1;
        $timer->save();

        $notify[] = ['success', __('Offer timer added successfully.')];
        return back()->withNotify($notify);
    }

    public function create()
    {
        $pageTitle = __('Add Offer Timer');
        $timer = null;
        $products = Product::available()->select('id', 'name')->orderBy('name')->limit(300)->get();
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);
        return view('admin.offer_timers.form', compact('pageTitle', 'timer', 'products', 'categories'));
    }

    public function edit($id)
    {
        $timer = OfferTimer::findOrFail($id);
        $pageTitle = __('Edit Offer Timer');
        $products = Product::available()->select('id', 'name')->orderBy('name')->limit(300)->get();
        $categories = Category::active()->orderBy('name')->get(['id', 'name']);
        return view('admin.offer_timers.form', compact('pageTitle', 'timer', 'products', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $timer = OfferTimer::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'end_at' => 'required|date',
            'style' => 'required|in:bar_small,bar_large,full_width',
            'position' => 'required|in:header,below_header,cart_top,checkout_top,product_detail,category_top,content_top,content_bottom,user_dashboard_top,floating',
            'bar_width' => 'nullable|string|max:50',
            'bar_height' => 'nullable|string|max:50',
            'show_on_pages' => 'required|array',
            'show_on_pages.*' => 'in:all,home,cart,checkout,product_detail,category,user_dashboard',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'link_url' => 'nullable|string|max:500',
            'is_active' => 'required|in:0,1',
        ]);

        $this->fillFromRequest($timer, $request);
        $timer->save();

        $notify[] = ['success', __('Offer timer updated successfully.')];
        return redirect()->route('admin.offer-timers.index')->withNotify($notify);
    }

    public function destroy($id)
    {
        $timer = OfferTimer::findOrFail($id);
        $timer->delete();
        $notify[] = ['success', __('Offer timer deleted.')];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $timer = OfferTimer::findOrFail($id);
        $timer->is_active = $timer->is_active ? 0 : 1;
        $timer->save();
        $notify[] = ['success', __('Status updated.')];
        return back()->withNotify($notify);
    }

    private function fillFromRequest(OfferTimer $timer, Request $request): void
    {
        $timer->title = $request->title;
        $timer->subtitle = $request->subtitle ?: null;
        $timer->end_at = $request->end_at;
        $timer->style = $request->style;
        $timer->bar_width = $request->filled('bar_width') ? $request->bar_width : null;
        $timer->bar_height = $request->filled('bar_height') ? $request->bar_height : null;
        $timer->position = $request->position;
        $timer->show_on_pages = $request->show_on_pages ?: [];
        $timer->product_ids = $request->filled('product_ids') ? array_map('intval', (array) $request->product_ids) : null;
        $timer->category_ids = $request->filled('category_ids') ? array_map('intval', (array) $request->category_ids) : null;
        $timer->link_url = $request->filled('link_url') ? $request->link_url : null;
        $timer->is_active = (int) $request->is_active;
    }
}
