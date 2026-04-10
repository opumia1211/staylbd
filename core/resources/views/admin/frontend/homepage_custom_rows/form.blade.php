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
        $split = $row->split_banner_json ?? [];
        if (!is_array($split)) {
            $split = [];
        }
        if (old('split_banner_interval') !== null) {
            $splitEnabled = old('split_banner_enabled') === '1' || old('split_banner_enabled') === true || old('split_banner_enabled') === 1;
            $splitInterval = old('split_banner_interval', 5);
        } else {
            $splitEnabled = !empty($split['enabled']);
            $splitInterval = $split['interval'] ?? 5;
        }
        $splitLarge = isset($split['large']) && is_array($split['large']) ? $split['large'] : [];
        $splitSmall = isset($split['small']) && is_array($split['small']) ? $split['small'] : [];
        if (old('split_banner_is_public') !== null) {
            $splitIsPublic = old('split_banner_is_public') === '1' || old('split_banner_is_public') === true || old('split_banner_is_public') === 1;
        } else {
            $splitIsPublic = !isset($split['is_public']) || $split['is_public'] === true || $split['is_public'] === 1 || $split['is_public'] === '1';
        }
        $splitCdTitle = old('split_banner_countdown_title', $split['countdown_title'] ?? '');
        $splitCdEnd = old('split_banner_countdown_end');
        if ($splitCdEnd === null && !empty($split['countdown_ends_at'] ?? null)) {
            try {
                $splitCdEnd = \Carbon\Carbon::parse($split['countdown_ends_at'])->format('Y-m-d\TH:i');
            } catch (\Throwable $e) {
                $splitCdEnd = '';
            }
        } elseif ($splitCdEnd === null) {
            $splitCdEnd = '';
        }
    @endphp
    <div class="row justify-content-center hp-form-pro">
        <div class="col-lg-11 col-xl-9">
            <div class="mb-3">
                <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}"
                    class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="las la-arrow-left"></i> @lang('Back to layout')
                </a>
            </div>

            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="hp-form-pro__banner px-4 py-4 text-white">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div
                            class="hp-form-pro__icon rounded-3 d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="las la-stream la-2x"></i>
                        </div>
                        <div>
                            <h4 class="h5 fw-bold mb-1 text-white">{{ $pageTitle }}</h4>
                            <p class="mb-0 small text-white text-opacity-90">
                                @lang('Adds a titled horizontal product row on the customer homepage. Choose category or exact product IDs.')
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 p-md-4">
                    <div class="alert alert-light border d-flex gap-2 mb-4 small">
                        <i class="las la-info-circle text-primary mt-1"></i>
                        <div>
                            <strong>@lang('After saving')</strong> —
                            @lang('you will return to the layout page. Drag your new line in the section list to set its position, then click “Save section order”.')
                        </div>
                    </div>

                    <form method="post" action="{{ $action }}" enctype="multipart/form-data">
                        @csrf
                        <div class="hp-form-pro__section mb-4 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="hp-form-pro__step">1</span>
                                <h6 class="mb-0 fw-bold">@lang('Title & visibility')</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">@lang('Section title') <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control form-control-lg"
                                        value="{{ old('title', $row->title) }}" required maxlength="191"
                                        placeholder="@lang('e.g. Flash sale · New drops · Shop by room')">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">@lang('List sort')</label>
                                    <input type="number" name="sort_order" class="form-control"
                                        value="{{ old('sort_order', $row->sort_order) }}" min="0" max="65535">
                                    <small class="text-muted">@lang('Among custom rows only (admin table order)')</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">@lang('Subtitle') <span
                                            class="text-muted fw-normal">(@lang('optional'))</span></label>
                                    <input type="text" name="subtitle" class="form-control"
                                        value="{{ old('subtitle', $row->subtitle) }}" maxlength="255"
                                        placeholder="@lang('Short line under the title')">
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                            id="is_active" @if($activeOn) checked @endif>
                                        <label class="form-check-label fw-medium"
                                            for="is_active">@lang('Active — show on homepage & appear in section list')</label>
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
                                        <input type="radio" class="btn-check" name="source_type" id="src_cat"
                                            value="category" autocomplete="off" {{ old('source_type', $row->source_type) === 'category' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary" for="src_cat"><i
                                                class="las la-folder me-1"></i>@lang('Category')</label>
                                        <input type="radio" class="btn-check" name="source_type" id="src_man" value="manual"
                                            autocomplete="off" {{ old('source_type', $row->source_type) === 'manual' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary" for="src_man"><i
                                                class="las la-list-ol me-1"></i>@lang('Manual product IDs')</label>
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
                                    <textarea name="product_ids_text" class="form-control font-monospace" rows="4"
                                        placeholder="101, 205, 340">{{ $idsText }}</textarea>
                                    <small
                                        class="text-muted">@lang('Comma, space, or line separated. In-stock / active products only.')</small>
                                    @error('product_ids_text')<div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="hp-form-pro__section mb-4 pb-4 border-bottom">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="hp-form-pro__step">3</span>
                                <h6 class="mb-0 fw-bold">@lang('Display & link')</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">@lang('Max products')</label>
                                    <input type="number" name="product_limit" class="form-control"
                                        value="{{ old('product_limit', $row->product_limit ?? 12) }}" min="1" max="24">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">@lang('Auto-scroll (sec)')</label>
                                    <input type="number" name="interval_seconds" class="form-control"
                                        value="{{ old('interval_seconds', $row->interval_seconds) }}" min="2" max="30"
                                        placeholder="@lang('Default')">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">@lang('“View all” URL') <span
                                            class="text-muted fw-normal">(@lang('optional'))</span></label>
                                    <input type="text" name="view_all_url" class="form-control"
                                        value="{{ old('view_all_url', $row->view_all_url) }}" placeholder="https://…">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">@lang('“View all” button text')</label>
                                    <input type="text" name="view_all_label" class="form-control"
                                        value="{{ old('view_all_label', $row->view_all_label) }}"
                                        placeholder="@lang('View all')">
                                </div>
                            </div>
                        </div>

                        <div class="hp-form-pro__section mb-0">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="hp-form-pro__step">4</span>
                                <h6 class="mb-0 fw-bold">@lang('Split promo banners (this line only)')</h6>
                            </div>
                            <p class="small text-muted mb-3">@lang('Above the product strip for this line. Empty slots show size hints; after upload hints hide.')</p>
                            <input type="hidden" name="split_banner_enabled" value="0">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="split_banner_enabled" value="1"
                                    id="split_banner_enabled" @if($splitEnabled) checked @endif>
                                <label class="form-check-label fw-medium" for="split_banner_enabled">@lang('Enable split banners on this product line')</label>
                            </div>
                            <input type="hidden" name="split_banner_is_public" value="0">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="split_banner_is_public" value="1"
                                    id="split_banner_is_public" @if($splitIsPublic) checked @endif>
                                <label class="form-check-label fw-medium" for="split_banner_is_public">@lang('Show on public storefront')</label>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-3">
                                    <label class="form-label small">@lang('Slide interval (sec)')</label>
                                    <input type="number" name="split_banner_interval" class="form-control form-control-sm"
                                        value="{{ (int) $splitInterval }}" min="2" max="30">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">@lang('Offer countdown end')</label>
                                    <input type="datetime-local" name="split_banner_countdown_end" class="form-control form-control-sm"
                                        value="{{ $splitCdEnd }}">
                                    <small class="text-muted">@lang('Optional. Uses same live countdown as site offer timers.')</small>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label small">@lang('Countdown title')</label>
                                    <input type="text" name="split_banner_countdown_title" class="form-control form-control-sm"
                                        value="{{ $splitCdTitle }}" placeholder="{{ __('Offer ends in') }}" maxlength="120">
                                </div>
                            </div>
                            <h6 class="small fw-bold text-uppercase text-muted mb-2">@lang('Large banner (slider, left)') — @lang('up to 5 slides')</h6>
                            <div class="row g-3 mb-4">
                                @for($s = 0; $s < 5; $s++)
                                    @php
                                        $slot = $splitLarge[$s] ?? [];
                                        $slotImg = $slot['image'] ?? '';
                                    @endphp
                                    <div class="col-md-6 col-lg-4 border rounded-3 p-3 bg-light">
                                        <div class="small fw-semibold mb-2">@lang('Slide') {{ $s + 1 }}</div>
                                        @if(!$slotImg)
                                            <div class="small text-muted border border-dashed rounded p-2 mb-2 bg-white">
                                                <strong>@lang('Large art')</strong>: <code>1200–1600 × 600–800</code> @lang('px landscape') · JPG/PNG/WebP · 5MB
                                            </div>
                                        @endif
                                        @if($slotImg)
                                            <div class="mb-2">
                                                <img src="{{ \App\Services\BannerService::rowSplitImageUrl($slotImg) }}" alt="" class="img-fluid rounded border" style="max-height:100px;">
                                                <input type="hidden" name="split_banner_large_{{ $s }}_keep" value="{{ $slotImg }}">
                                            </div>
                                        @endif
                                        <input type="file" name="split_banner_large_{{ $s }}_image" class="form-control form-control-sm mb-2" accept="image/jpeg,image/png,image/webp">
                                        <input type="text" name="split_banner_large_{{ $s }}_kicker" class="form-control form-control-sm mb-1" placeholder="@lang('Kicker (e.g. New Arrivals)')" value="{{ old('split_banner_large_'.$s.'_kicker', $slot['kicker'] ?? '') }}">
                                        <input type="text" name="split_banner_large_{{ $s }}_heading" class="form-control form-control-sm mb-1" placeholder="@lang('Heading')" value="{{ old('split_banner_large_'.$s.'_heading', $slot['heading'] ?? '') }}">
                                        <div class="row g-1">
                                            <div class="col-6">
                                                <input type="text" name="split_banner_large_{{ $s }}_btn" class="form-control form-control-sm" placeholder="@lang('Button')" value="{{ old('split_banner_large_'.$s.'_btn', $slot['btn'] ?? '') }}">
                                            </div>
                                            <div class="col-6">
                                                <input type="text" name="split_banner_large_{{ $s }}_url" class="form-control form-control-sm" placeholder="URL" value="{{ old('split_banner_large_'.$s.'_url', $slot['url'] ?? '') }}">
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <h6 class="small fw-bold text-uppercase text-muted mb-2">@lang('Small banner (right)')</h6>
                            <div class="row g-3 mb-0">
                                <div class="col-lg-4">
                                    @if(empty($splitSmall['image']))
                                        <div class="small text-muted border border-dashed rounded p-2 mb-2 bg-light">
                                            <strong>@lang('Small card')</strong>: <code>~800 × 900</code> @lang('px portrait') · JPG/PNG/WebP · 5MB
                                        </div>
                                    @endif
                                    @if(!empty($splitSmall['image']))
                                        <div class="mb-2">
                                            <img src="{{ \App\Services\BannerService::rowSplitImageUrl($splitSmall['image']) }}" alt="" class="img-fluid rounded border" style="max-height:120px;">
                                            <input type="hidden" name="split_banner_small_keep" value="{{ $splitSmall['image'] }}">
                                        </div>
                                    @endif
                                    <input type="file" name="split_banner_small_image" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp">
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" name="split_banner_small_badge" class="form-control form-control-sm mb-2" placeholder="@lang('Badge (e.g. Summer Offer)')" value="{{ old('split_banner_small_badge', $splitSmall['badge'] ?? '') }}">
                                    <input type="text" name="split_banner_small_heading" class="form-control form-control-sm mb-2" placeholder="@lang('Heading')" value="{{ old('split_banner_small_heading', $splitSmall['heading'] ?? '') }}">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <input type="text" name="split_banner_small_btn" class="form-control form-control-sm" placeholder="@lang('Button')" value="{{ old('split_banner_small_btn', $splitSmall['btn'] ?? '') }}">
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="split_banner_small_url" class="form-control form-control-sm" placeholder="URL" value="{{ old('split_banner_small_url', $splitSmall['url'] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-top d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn--primary btn-lg px-5 fw-semibold">
                                <i
                                    class="las la-check-circle me-1"></i>{{ $isEdit ? __('Update product line') : __('Create product line') }}
                            </button>
                            <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}"
                                class="btn btn-outline-secondary btn-lg">@lang('Cancel')</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('style')
        
{{-- inline style moved to critical-admin.css --}}

    @endpush
    @push('script')
        <script>
            (function () {
                function sync() {
                    var manual = document.getElementById('src_man') && document.getElementById('src_man').checked;
                    document.querySelectorAll('.js-field-category').forEach(function (el) { el.style.display = manual ? 'none' : 'block'; });
                    document.querySelectorAll('.js-field-manual').forEach(function (el) { el.style.display = manual ? 'block' : 'none'; });
                }
                document.querySelectorAll('input[name="source_type"]').forEach(function (r) { r.addEventListener('change', sync); });
                sync();
            })();
        </script>
    @endpush
@endsection