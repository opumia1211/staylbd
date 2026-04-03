@php
    $appItems = $appPromotionItems ?? collect();
    $editing = $editingAppItem ?? null;
    $editId = $editing ? (int) $editing->id : null;
    $editV = (object)[];
    if ($editing && $editing->data_values) {
        $editV = is_array($editing->data_values) ? (object) $editing->data_values : $editing->data_values;
    }
    $editPlatform = old('platform', $editV->platform ?? $editV->title ?? '');
    $editName = old('name', $editV->name ?? $editV->title ?? '');
    $editLink = old('link', $editV->link ?? $editV->android_url ?? $editV->ios_url ?? '');
    $editImage = $editV->image ?? $editV->qr_image ?? null;
    $editAppFile = $editV->app_file ?? null;
    $editOrder = old('display_order', $editing ? (int)($editV->display_order ?? 0) : 0);
    $hiddenId = old('id', $editId ? (string) $editId : '');
    $apData = optional($appPromotion)->data_values;
    $apEnabled = (int) (is_object($apData) ? ($apData->enabled ?? 0) : 0);
@endphp

@if(session('notify'))
    @foreach(session('notify') as $n)
        <div class="alert alert-{{ $n[0] === 'error' ? 'danger' : 'success' }} alert-dismissible fade show py-2 px-3 small mb-3" role="alert">
            {{ __($n[1]) }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endforeach
@endif
@if($errors->any())
    <div class="alert alert-danger py-2 px-3 small mb-3">
        <ul class="mb-0 list-unstyled small">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
    </div>
@endif

<div class="app-promotion-page">
    {{-- Top bar: Show in footer only --}}
    <div class="ap-top-bar mb-4">
        <form method="POST" action="{{ route('admin.frontend.sections.footer.saveSection') }}" class="ap-visibility-form d-flex flex-wrap align-items-center gap-3">
            @csrf
            <input type="hidden" name="section" value="app_promotion">
            <label class="ap-top-bar__label mb-0">@lang('Show App Promotion in Footer')</label>
            <select name="enabled" class="form-select form-select-sm ap-top-bar__select">
                <option value="0" {{ !$apEnabled ? 'selected' : '' }}>@lang('No')</option>
                <option value="1" {{ $apEnabled ? 'selected' : '' }}>@lang('Yes')</option>
            </select>
            <button type="submit" class="btn btn--primary btn-sm">@lang('Save')</button>
        </form>
    </div>

    {{-- Main: App / Software Links --}}
    <section id="app-items" class="ap-main">
        <div class="ap-main__header d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h6 class="ap-main__title mb-1">@lang('App / Software Links')</h6>
                <p class="ap-main__subtitle text-muted small mb-0">@lang('Add items with platform, photo, link and/or direct app file. Users can download from footer.')</p>
            </div>
            @if($editing)
                <a href="{{ route('admin.frontend.sections.footer.section', 'app-promotion') }}#app-items" class="btn btn-sm btn-outline-secondary">@lang('Cancel edit')</a>
            @endif
        </div>

        <div class="ap-form-card card mb-4">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.frontend.sections.footer.saveAppPromotionItem') }}" enctype="multipart/form-data" class="ap-item-form">
                    @csrf
                    <input type="hidden" name="id" value="{{ $hiddenId }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label ap-label">@lang('Platform')</label>
                            <input type="text" name="platform" id="ap-field-platform" class="form-control form-control-sm" placeholder="e.g. Android, iOS" value="{{ old('platform', $editPlatform) }}" maxlength="120">
                            <div class="ap-chips mt-1">
                                <button type="button" class="ap-chip" data-platform="Android">Android</button>
                                <button type="button" class="ap-chip" data-platform="iOS">iOS</button>
                                <button type="button" class="ap-chip" data-platform="Desktop">Desktop</button>
                                <button type="button" class="ap-chip" data-platform="Windows">Windows</button>
                                <button type="button" class="ap-chip" data-platform="Mac">Mac</button>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label ap-label">@lang('Name')</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="@lang('App name')" value="{{ old('name', $editName) }}" maxlength="200">
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label ap-label">@lang('Photo')</label>
                            <input type="file" name="image" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.webp,.gif">
                            @if($editImage)
                                <small class="text-muted d-block mt-1"><img src="{{ getImage('assets/images/frontend/footer/' . $editImage, '50x50') }}" alt="" class="rounded" style="width:24px;height:24px;object-fit:contain;vertical-align:middle"> @lang('Current')</small>
                            @endif
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label ap-label">@lang('Link')</label>
                            <input type="text" name="link" class="form-control form-control-sm" placeholder="https://..." value="{{ old('link', $editLink) }}" maxlength="500">
                            <small class="text-muted">@lang('Store or page URL')</small>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-2">
                            <label class="form-label ap-label">@lang('App file (direct download)')</label>
                            <input type="file" name="app_file" class="form-control form-control-sm" accept=".apk,.exe,.dmg,.zip,.ipa">
                            @if($editAppFile)
                                <small class="text-muted d-block mt-1"><i class="las la-file-download"></i> @lang('Current file attached')</small>
                            @else
                                <small class="text-muted">APK, EXE, DMG, ZIP, IPA — max 150MB</small>
                            @endif
                        </div>
                        <div class="col-6 col-sm-4 col-lg-1">
                            <label class="form-label ap-label">#</label>
                            <input type="number" name="display_order" class="form-control form-control-sm text-center" value="{{ old('display_order', $editOrder) }}" min="0">
                        </div>
                        <div class="col-6 col-sm-4 col-lg-2 text-end">
                            <button type="submit" class="btn btn--primary btn-sm w-100">{{ $editing ? __('Update') : __('Add') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="ap-table-wrap rounded-2 overflow-hidden border">
            <table class="table table-hover ap-table mb-0">
                <thead>
                    <tr>
                        <th class="ap-table__th ap-table__th--img">@lang('Photo')</th>
                        <th class="ap-table__th">@lang('Platform')</th>
                        <th class="ap-table__th">@lang('Name')</th>
                        <th class="ap-table__th">@lang('Link')</th>
                        <th class="ap-table__th">@lang('App file')</th>
                        <th class="ap-table__th ap-table__th--order">#</th>
                        <th class="ap-table__th ap-table__th--action">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appItems as $el)
                        @php
                            $dv = $el->data_values ?? (object)[];
                            if (is_array($dv)) $dv = (object)$dv;
                            $platform = $dv->platform ?? $dv->title ?? '';
                            $name = $dv->name ?? $dv->title ?? '';
                            $link = $dv->link ?? $dv->android_url ?? $dv->ios_url ?? '';
                            $appFile = $dv->app_file ?? null;
                            $img = $dv->image ?? $dv->qr_image ?? null;
                            $order = (int)($dv->display_order ?? 0);
                        @endphp
                        <tr class="{{ $editId && $el->id == $editId ? 'table-active' : '' }}">
                            <td class="align-middle text-center">
                                @if($img)
                                    <img src="{{ getImage('assets/images/frontend/footer/' . $img, '50x50') }}" alt="" class="ap-table__img rounded" loading="lazy">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="align-middle"><span class="fw-medium">{{ $platform ?: '—' }}</span></td>
                            <td class="align-middle">{{ $name ?: '—' }}</td>
                            <td class="align-middle">
                                @if($link)
                                    <a href="{{ $link }}" target="_blank" rel="noopener" class="ap-link">{{ Str::limit($link, 35) }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($appFile)
                                    <span class="badge bg-success"><i class="las la-download"></i> @lang('Yes')</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">{{ $order }}</td>
                            <td class="align-middle text-end">
                                <a href="{{ route('admin.frontend.sections.footer.section', ['section' => 'app-promotion', 'edit' => $el->id]) }}#app-items" class="btn btn-sm btn-outline-primary">@lang('Edit')</a>
                                <form action="{{ route('admin.frontend.sections.footer.deleteAppPromotionItem', $el->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __("Remove this app link?") }}');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">@lang('Delete')</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="las la-mobile-alt ap-empty-icon d-block mb-2"></i>
                                @lang('No app links yet. Add one above.')
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@push('style')
<style>
.app-promotion-page { font-size: 0.875rem; }
.ap-top-bar { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 1rem; }
.ap-top-bar__label { font-weight: 600; color: #334155; font-size: 0.875rem; }
.ap-top-bar__select { width: auto; min-width: 100px; }
.ap-main__title { font-weight: 600; color: #1e293b; font-size: 1rem; }
.ap-main__subtitle { font-size: 0.8rem; line-height: 1.35; }
.ap-form-card { border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
.ap-label { font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 0.25rem; }
.ap-chips { display: flex; flex-wrap: wrap; gap: 0.35rem; }
.ap-chip { font-size: 0.7rem; padding: 0.2rem 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #475569; cursor: pointer; transition: all 0.15s; }
.ap-chip:hover { border-color: var(--bs-primary); color: var(--bs-primary); background: rgba(var(--bs-primary-rgb), 0.06); }
.ap-table-wrap { border-color: #e2e8f0 !important; }
.ap-table { font-size: 0.8125rem; }
.ap-table__th { font-size: 0.75rem; font-weight: 600; color: #64748b; padding: 0.6rem 0.75rem; background: #f8fafc; }
.ap-table__th--img { width: 56px; }
.ap-table__th--order { width: 48px; }
.ap-table__th--action { width: 140px; }
.ap-table td { padding: 0.5rem 0.75rem; }
.ap-table__img { width: 40px; height: 40px; object-fit: contain; }
.ap-link { font-size: 0.8rem; color: var(--bs-primary); text-decoration: none; max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.ap-link:hover { text-decoration: underline; }
.ap-empty-icon { font-size: 2rem; opacity: 0.5; }
</style>
@endpush

@push('script')
<script>
(function(){
    if (window.location.search.indexOf('edit=') !== -1 || window.location.hash === '#app-items') {
        var el = document.getElementById('app-items');
        if (el) setTimeout(function(){ el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 120);
    }
    document.querySelectorAll('.ap-chip').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var field = document.getElementById('ap-field-platform');
            if (field) field.value = btn.getAttribute('data-platform');
        });
    });
})();
</script>
@endpush
