@extends('admin.layouts.app')
@section('panel')
@php
    $isEdit = $row->exists;
    $action = $isEdit ? route('admin.frontend.sections.homepageCustomRows.update', $row->id) : route('admin.frontend.sections.homepageCustomRows.store');
    $idsText = old('product_ids_text', $isEdit && $row->product_ids ? implode(', ', array_map('intval', (array) $row->product_ids)) : '');
    $ia = old('is_active');
    if ($ia !== null) {
        $activeOn = ($ia === '1' || $ia === true || $ia === 1 || $ia === 'on');
    } else {
        $activeOn = $row->exists ? (bool) $row->is_active : true;
    }
@endphp
<div class="row justify-content-center hp-form-pro">
    <div class="col-lg-11 col-xl-9">
        <div class="mb-3">
            <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="las la-arrow-left"></i> @lang('Back to layout')
            </a>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="hp-form-pro__banner px-4 py-4 text-white">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="hp-form-pro__icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="las la-stream la-2x"></i>
                    </div>
                    <div>
                        <h4 class="h5 fw-bold mb-1 text-white">{{ $pageTitle }}</h4>
                        <p class="mb-0 small text-white text-opacity-90">@lang('Adds a titled horizontal product row on the customer homepage. Choose category or exact product IDs.')</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4 p-md-4">
                <div class="alert alert-light border d-flex gap-2 mb-4 small">
                    <i class="las la-info-circle text-primary mt-1"></i>
                    <div>
                        <strong>@lang('After saving')</strong> — @lang('you will return to the layout page. Drag your new line in the section list to set its position, then click “Save section order”.')
                    </div>
                </div>

                <form method="post" action="{{ $action }}">
                    @csrf
                    <div class="hp-form-pro__section mb-4 pb-4 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="hp-form-pro__step">1</span>
                            <h6 class="mb-0 fw-bold">@lang('Title & visibility')</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">@lang('Section title') <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $row->title) }}" required maxlength="191" placeholder="@lang('e.g. Flash sale · New drops · Shop by room')">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">@lang('List sort')</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $row->sort_order) }}" min="0" max="65535">
                                <small class="text-muted">@lang('Among custom rows only (admin table order)')</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">@lang('Subtitle') <span class="text-muted fw-normal">(@lang('optional'))</span></label>
                                <input type="text" name="subtitle" class="form-control" value="{{ old('subtitle', $row->subtitle) }}" maxlength="255" placeholder="@lang('Short line under the title')">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @if($activeOn) checked @endif>
                                    <label class="form-check-label fw-medium" for="is_active">@lang('Active — show on homepage & appear in section list')</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="hp-form-pro__section mb-4 pb-4 border-bottom">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="hp-form-pro__step">2</span>
                            <h6 class="mb-0 fw-bold">@lang('Which products?')</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold d-block">@lang('Source')</label>
                                <div class="btn-group flex-wrap" role="group">
                                    <input type="radio" class="btn-check" name="source_type" id="src_cat" value="category" autocomplete="off" {{ old('source_type', $row->source_type) === 'category' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="src_cat"><i class="las la-folder me-1"></i>@lang('Category')</label>
                                    <input type="radio" class="btn-check" name="source_type" id="src_man" value="manual" autocomplete="off" {{ old('source_type', $row->source_type) === 'manual' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary" for="src_man"><i class="las la-list-ol me-1"></i>@lang('Manual product IDs')</label>
                                </div>
                            </div>
                            <div class="col-12 js-field-category">
                                <label class="form-label fw-semibold">@lang('Category')</label>
                                <select name="category_id" class="form-select form-select-lg">
                                    <option value="">— @lang('Select category') —</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}" {{ (int) old('category_id', $row->category_id) === (int) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12 js-field-manual" style="display:none;">
                                <label class="form-label fw-semibold">@lang('Product IDs')</label>
                                <textarea name="product_ids_text" class="form-control font-monospace" rows="4" placeholder="101, 205, 340">{{ $idsText }}</textarea>
                                <small class="text-muted">@lang('Comma, space, or line separated. In-stock / active products only.')</small>
                                @error('product_ids_text')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="hp-form-pro__section mb-0">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="hp-form-pro__step">3</span>
                            <h6 class="mb-0 fw-bold">@lang('Display & link')</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">@lang('Max products')</label>
                                <input type="number" name="product_limit" class="form-control" value="{{ old('product_limit', $row->product_limit ?? 12) }}" min="1" max="24">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">@lang('Auto-scroll (sec)')</label>
                                <input type="number" name="interval_seconds" class="form-control" value="{{ old('interval_seconds', $row->interval_seconds) }}" min="2" max="30" placeholder="@lang('Default')">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">@lang('“View all” URL') <span class="text-muted fw-normal">(@lang('optional'))</span></label>
                                <input type="text" name="view_all_url" class="form-control" value="{{ old('view_all_url', $row->view_all_url) }}" placeholder="https://…">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">@lang('“View all” button text')</label>
                                <input type="text" name="view_all_label" class="form-control" value="{{ old('view_all_label', $row->view_all_label) }}" placeholder="@lang('View all')">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-top d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn--primary btn-lg px-5 fw-semibold">
                            <i class="las la-check-circle me-1"></i>{{ $isEdit ? __('Update product line') : __('Create product line') }}
                        </button>
                        <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}" class="btn btn-outline-secondary btn-lg">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@push('style')
<style>
    .hp-form-pro__banner { background: linear-gradient(135deg, #334155 0%, #475569 50%, #1e293b 100%); }
    .hp-form-pro__icon { width: 56px; height: 56px; background: rgba(255,255,255,.15); }
    .hp-form-pro__step {
        display: inline-flex; align-items: center; justify-content: center;
        width: 28px; height: 28px; border-radius: 8px;
        background: var(--primary, #6366f1); color: #fff;
        font-size: 0.8rem; font-weight: 800;
    }
    .hp-form-pro .btn-check:checked + .btn-outline-secondary {
        background: var(--primary, #6366f1); border-color: var(--primary, #6366f1); color: #fff;
    }
</style>
@endpush
@push('script')
<script>
(function() {
    function sync() {
        var manual = document.getElementById('src_man') && document.getElementById('src_man').checked;
        document.querySelectorAll('.js-field-category').forEach(function(el) { el.style.display = manual ? 'none' : 'block'; });
        document.querySelectorAll('.js-field-manual').forEach(function(el) { el.style.display = manual ? 'block' : 'none'; });
    }
    document.querySelectorAll('input[name="source_type"]').forEach(function(r) { r.addEventListener('change', sync); });
    sync();
})();
</script>
@endpush
@endsection
