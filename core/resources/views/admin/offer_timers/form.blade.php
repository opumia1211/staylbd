@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
            </div>
            <div class="card-body">
                @include('partials.notify')
                <form action="{{ $timer ? route('admin.offer-timers.update', $timer->id) : route('admin.offer-timers.store') }}" method="POST">
                    @csrf
                    @if($timer) @method('PUT') @endif
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Title') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ old('title', $timer->title ?? '') }}" required maxlength="255" placeholder="@lang('e.g. Discount Offer Ends in')">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Subtitle')</label>
                                <input type="text" class="form-control" name="subtitle" value="{{ old('subtitle', $timer->subtitle ?? '') }}" maxlength="500" placeholder="@lang('e.g. Complete your order within time to save more')">
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('End Date & Time') <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" name="end_at" value="{{ old('end_at', $timer ? $timer->end_at->format('Y-m-d\TH:i') : '') }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Style')</label>
                                        <select class="form-select" name="style">
                                            <option value="bar_small" {{ old('style', $timer->style ?? '') == 'bar_small' ? 'selected' : '' }}>@lang('Small Bar')</option>
                                            <option value="bar_large" {{ old('style', $timer->style ?? 'bar_large') == 'bar_large' ? 'selected' : '' }}>@lang('Large Bar')</option>
                                            <option value="full_width" {{ old('style', $timer->style ?? '') == 'full_width' ? 'selected' : '' }}>@lang('Full Width')</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Bar Width')</label>
                                        <input type="text" class="form-control" name="bar_width" value="{{ old('bar_width', $timer->bar_width ?? '') }}" placeholder="@lang('e.g. 100%, 80%, 500px, auto')">
                                        <small class="text-muted">@lang('Optional. Leave empty for default.')</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Bar Height')</label>
                                        <input type="text" class="form-control" name="bar_height" value="{{ old('bar_height', $timer->bar_height ?? '') }}" placeholder="@lang('e.g. auto, 60px, 80px')">
                                        <small class="text-muted">@lang('Optional.')</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">@lang('Position')</label>
                                        <select class="form-select" name="position">
                                            <option value="header" {{ old('position', $timer->position ?? '') == 'header' ? 'selected' : '' }}>@lang('Header')</option>
                                            <option value="below_header" {{ old('position', $timer->position ?? '') == 'below_header' ? 'selected' : '' }}>@lang('Below Header')</option>
                                            <option value="content_top" {{ old('position', $timer->position ?? '') == 'content_top' ? 'selected' : '' }}>@lang('Content Top (all pages)')</option>
                                            <option value="content_bottom" {{ old('position', $timer->position ?? '') == 'content_bottom' ? 'selected' : '' }}>@lang('Content Bottom (all pages)')</option>
                                            <option value="cart_top" {{ old('position', $timer->position ?? 'cart_top') == 'cart_top' ? 'selected' : '' }}>@lang('Cart Top')</option>
                                            <option value="checkout_top" {{ old('position', $timer->position ?? '') == 'checkout_top' ? 'selected' : '' }}>@lang('Checkout Top')</option>
                                            <option value="product_detail" {{ old('position', $timer->position ?? '') == 'product_detail' ? 'selected' : '' }}>@lang('Product Detail')</option>
                                            <option value="category_top" {{ old('position', $timer->position ?? '') == 'category_top' ? 'selected' : '' }}>@lang('Category Page Top')</option>
                                            <option value="user_dashboard_top" {{ old('position', $timer->position ?? '') == 'user_dashboard_top' ? 'selected' : '' }}>@lang('User Dashboard Top')</option>
                                            <option value="floating" {{ old('position', $timer->position ?? '') == 'floating' ? 'selected' : '' }}>@lang('Floating')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Show on pages') <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['all' => __('All pages'), 'home' => __('Home'), 'cart' => __('Cart'), 'checkout' => __('Checkout'), 'product_detail' => __('Product Detail'), 'category' => __('Category'), 'user_dashboard' => __('User Dashboard')] as $val => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="show_on_pages[]" value="{{ $val }}" id="page_{{ $val }}"
                                            {{ in_array($val, old('show_on_pages', $timer->show_on_pages ?? ['cart'])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="page_{{ $val }}">{{ $label }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Product IDs') <small class="text-muted">@lang('Optional, comma or leave empty for all')</small></label>
                                <select class="form-select select2-multi" name="product_ids[]" multiple>
                                    @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ in_array($p->id, old('product_ids', $timer->product_ids ?? [])) ? 'selected' : '' }}>{{ __($p->name) }} ({{ $p->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Category IDs') <small class="text-muted">@lang('Optional')</small></label>
                                <select class="form-select select2-multi" name="category_ids[]" multiple>
                                    @foreach($categories as $c)
                                    <option value="{{ $c->id }}" {{ in_array($c->id, old('category_ids', $timer->category_ids ?? [])) ? 'selected' : '' }}>{{ __($c->name) }} ({{ $c->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">@lang('Link URL')</label>
                                <input type="url" class="form-control" name="link_url" value="{{ old('link_url', $timer->link_url ?? '') }}" placeholder="https://...">
                            </div>
                            <div class="form-group mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $timer->is_active ?? 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">@lang('Active')</label>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn--primary">{{ $timer ? __('Update') : __('Save') }}</button>
                                <a href="{{ route('admin.offer-timers.index') }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script-lib')
<script src="{{ asset('assets/admin/js/vendor/select2.min.js') }}?v={{ $assetVersion ?? config('app.version') }}"></script>
@endpush
@push('script')
<script>
(function() {
    document.querySelectorAll('.select2-multi').forEach(function(el) {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(el).select2({ width: '100%', placeholder: '@lang('Select')' });
        }
    });
})();
</script>
@endpush
