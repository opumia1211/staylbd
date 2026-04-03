@extends('admin.layouts.app')
@section('panel')
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title mb-0">{{ $pageTitle }}</h5>
            </div>
            <div class="card-body">
                @include('partials.notify')
                <form action="{{ $ad ? route('admin.popup-ads.update', $ad->id) : route('admin.popup-ads.store') }}" method="POST" enctype="multipart/form-data" id="popupAdForm">
                    @csrf
                    @if($ad) @method('PUT') @endif

                    <div class="mb-4">
                        <h6 class="text-muted border-bottom pb-2 mb-3">@lang('Banner & link')</h6>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Name') <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', isset($ad) ? $ad->name : '') }}" required maxlength="255" placeholder="@lang('e.g. Homepage offer')">
                            <small class="text-muted">@lang('Internal name for admin only.')</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Banner image')</label>
                            <input type="file" class="form-control" name="image" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" id="popupImageInput">
                            @if($ad && $ad->image)
                            <div class="mt-2">
                                <img src="{{ getImage(getFilePath('popupAd') . '/' . $ad->image) }}" alt="" class="rounded border" style="max-width: 200px; height: auto;" id="popupImagePreview">
                                <small class="text-muted d-block mt-1">@lang('Current image. Upload new to replace.')</small>
                            </div>
                            @endif
                            <small class="text-muted">@lang('Any size, any format (PNG, JPG, WebP, GIF, SVG). Display size is set below.')</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Link URL')</label>
                            <input type="url" class="form-control" name="link_url" value="{{ old('link_url', isset($ad) ? $ad->link_url : '') }}" placeholder="https://">
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted border-bottom pb-2 mb-3">@lang('Display size on public page')</h6>
                        <p class="small text-muted mb-3">@lang('This size will be used on the frontend. Image will fit inside this box.')</p>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label">@lang('Quick preset')</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm popup-size-preset" data-w="300px" data-h="250px">@lang('Small') 300×250</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm popup-size-preset" data-w="400px" data-h="300px">@lang('Medium') 400×300</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm popup-size-preset" data-w="600px" data-h="400px">@lang('Large') 600×400</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm popup-size-preset" data-w="90%" data-h="80vh">@lang('Full') 90%×80vh</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Width')</label>
                                <input type="text" class="form-control" name="width" id="popupWidth" value="{{ old('width', isset($ad) && $ad->width ? $ad->width : '400px') }}" placeholder="400px, 90%, 80vw">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Height')</label>
                                <input type="text" class="form-control" name="height" id="popupHeight" value="{{ old('height', isset($ad) && $ad->height ? $ad->height : '300px') }}" placeholder="300px, 80vh, auto">
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Position on screen')</label>
                                <select class="form-select" name="position" id="popupPosition" style="max-width: 280px;">
                                    @foreach(\App\Models\PopupAd::positionOptions() as $val => $label)
                                    <option value="{{ $val }}" {{ old('position', isset($ad) ? $ad->getPosition() : 'center') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">@lang('Where the ad box appears: center (recommended) or a corner.')</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Display type')</label>
                                <select class="form-select" name="display_type" id="displayType" style="max-width: 280px;">
                                    @foreach(\App\Models\PopupAd::displayTypeOptions() as $val => $label)
                                    <option value="{{ $val }}" {{ old('display_type', isset($ad) ? $ad->getDisplayType() : 'popup') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">@lang('Popup: modal with close button. Inline: stays on page (e.g. sidebar on payment/dashboard).')</small>
                            </div>
                            <div class="col-12 js-inline-placement-wrap" style="display: {{ old('display_type', isset($ad) ? $ad->getDisplayType() : 'popup') === 'inline' ? 'block' : 'none' }};">
                                <label class="form-label">@lang('Inline placement')</label>
                                <select class="form-select" name="inline_placement" id="inlinePlacement" style="max-width: 320px;">
                                    @foreach(\App\Models\PopupAd::inlinePlacementOptions() as $val => $label)
                                    <option value="{{ $val }}" {{ old('inline_placement', isset($ad) ? $ad->getInlinePlacement() : '') == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">@lang('Where the ad box appears on the page. Sidebar right = user dashboard / payment page side.')</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted border-bottom pb-2 mb-3">@lang('When to show')</h6>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Show after (seconds)') <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="delay_seconds" value="{{ old('delay_seconds', isset($ad) ? $ad->delay_seconds : 3) }}" min="1" max="60" required style="max-width: 100px;">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Show on pages') <span class="text-danger">*</span></label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(\App\Models\PopupAd::pageOptions() as $val => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="show_on_pages[]" value="{{ $val }}" id="page_{{ $val }}"
                                        {{ in_array($val, old('show_on_pages', isset($ad) ? $ad->show_on_pages : ['all'])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="page_{{ $val }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted border-bottom pb-2 mb-3">@lang('Schedule & status')</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Start at')</label>
                                <input type="datetime-local" class="form-control" name="start_at" value="{{ old('start_at', isset($ad) && $ad->start_at ? $ad->start_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('End at')</label>
                                <input type="datetime-local" class="form-control" name="end_at" value="{{ old('end_at', isset($ad) && $ad->end_at ? $ad->end_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">@lang('Status')</label>
                                <select class="form-select" name="is_active" style="max-width: 120px;">
                                    <option value="1" {{ old('is_active', isset($ad) ? $ad->is_active : 1) == 1 ? 'selected' : '' }}>@lang('Active')</option>
                                    <option value="0" {{ old('is_active', isset($ad) ? $ad->is_active : 1) == 0 ? 'selected' : '' }}>@lang('Inactive')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn--primary">@lang('Save')</button>
                        <a href="{{ route('admin.popup-ads.index') }}" class="btn btn-outline--secondary">@lang('Cancel')</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
(function() {
    document.querySelectorAll('.popup-size-preset').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var w = this.getAttribute('data-w');
            var h = this.getAttribute('data-h');
            if (w) document.getElementById('popupWidth').value = w;
            if (h) document.getElementById('popupHeight').value = h;
        });
    });
    var displayType = document.getElementById('displayType');
    var inlineWrap = document.querySelector('.js-inline-placement-wrap');
    if (displayType && inlineWrap) {
        function toggleInline() { inlineWrap.style.display = (displayType.value === 'inline') ? 'block' : 'none'; }
        displayType.addEventListener('change', toggleInline);
        toggleInline();
    }
    var imgInput = document.getElementById('popupImageInput');
    if (imgInput) {
        imgInput.addEventListener('change', function() {
            var preview = document.getElementById('popupImagePreview');
            if (!preview && this.files && this.files[0]) {
                var div = document.createElement('div');
                div.className = 'mt-2';
                div.innerHTML = '<img id="popupImagePreview" class="rounded border" style="max-width:200px;height:auto" alt=""><small class="text-muted d-block mt-1">@lang("New image selected.")</small>';
                imgInput.parentNode.appendChild(div);
                preview = document.getElementById('popupImagePreview');
            }
            if (preview && this.files && this.files[0]) {
                var r = new FileReader();
                r.onload = function(e) { preview.src = e.target.result; };
                r.readAsDataURL(this.files[0]);
            }
        });
    }
})();
</script>
@endpush
@endsection
