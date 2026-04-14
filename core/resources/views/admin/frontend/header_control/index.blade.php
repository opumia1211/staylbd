@extends('admin.layouts.app')

@section('panel')
@php
    $d = $draftConfig;
    $live = $liveConfig;
@endphp

<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="mb-1">@lang('Header Control Board')</h5>
                    <p class="text-muted mb-0">@lang('Manage Top, Main, and Menu bars from one compact page with draft + publish flow.')</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge--primary">@lang('Route'): /sajaladminopu/frontend/header</span>
                    <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.frontend.sections.headericons') }}">@lang('Legacy Icons')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <form action="{{ route('admin.frontend.sections.header.saveDraft') }}" method="POST" class="card mb-3">
            @csrf
            <div class="card-header d-flex align-items-center justify-content-between">
                <strong>@lang('Draft Editor')</strong>
                <button class="btn btn--primary btn-sm" type="submit">@lang('Save Draft')</button>
            </div>
            <div class="card-body">
                <div class="accordion" id="headerControlAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingAppearance">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAppearance" aria-expanded="true" aria-controls="collapseAppearance">
                                @lang('Global Appearance')
                            </button>
                        </h2>
                        <div id="collapseAppearance" class="accordion-collapse collapse show" aria-labelledby="headingAppearance" data-bs-parent="#headerControlAccordion">
                            <div class="accordion-body">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('Top bar color')</label>
                                        <input type="color" class="form-control form-control-color w-100" name="appearance[top_bg]" value="{{ $d['appearance']['top_bg'] }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('Main bar color')</label>
                                        <input type="color" class="form-control form-control-color w-100" name="appearance[main_bg]" value="{{ $d['appearance']['main_bg'] }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('Menu bar color')</label>
                                        <input type="color" class="form-control form-control-color w-100" name="appearance[menu_bg]" value="{{ $d['appearance']['menu_bg'] }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('Top height')</label>
                                        <input type="number" class="form-control" name="appearance[top_height]" value="{{ $d['appearance']['top_height'] }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('Main height')</label>
                                        <input type="number" class="form-control" name="appearance[main_height]" value="{{ $d['appearance']['main_height'] }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('Menu height')</label>
                                        <input type="number" class="form-control" name="appearance[menu_height]" value="{{ $d['appearance']['menu_height'] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTop">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTop" aria-expanded="false" aria-controls="collapseTop">
                                @lang('Top Bar Controls')
                            </button>
                        </h2>
                        <div id="collapseTop" class="accordion-collapse collapse" aria-labelledby="headingTop" data-bs-parent="#headerControlAccordion">
                            <div class="accordion-body">
                                <div class="row g-2">
                                    <div class="col-md-3 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="top_bar[enabled]" value="1" @checked(!empty($d['top_bar']['enabled']))>
                                        <label class="form-check-label">@lang('Enable top bar')</label>
                                    </div>
                                    <div class="col-md-3 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="top_bar[show_language]" value="1" @checked(!empty($d['top_bar']['show_language']))>
                                        <label class="form-check-label">@lang('Show language')</label>
                                    </div>
                                    <div class="col-md-3 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="top_bar[show_currency]" value="1" @checked(!empty($d['top_bar']['show_currency']))>
                                        <label class="form-check-label">@lang('Show currency')</label>
                                    </div>
                                    <div class="col-md-3 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="top_bar[show_apps]" value="1" @checked(!empty($d['top_bar']['show_apps']))>
                                        <label class="form-check-label">@lang('Show apps')</label>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Language label mode')</label>
                                        <select class="form-control" name="top_bar[language_mode]">
                                            <option value="code" @selected(($d['top_bar']['language_mode'] ?? 'code') === 'code')>@lang('Show current code')</option>
                                            <option value="name" @selected(($d['top_bar']['language_mode'] ?? '') === 'name')>@lang('Show current language name')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Currency label mode')</label>
                                        <select class="form-control" name="top_bar[currency_mode]">
                                            <option value="code" @selected(($d['top_bar']['currency_mode'] ?? 'code') === 'code')>@lang('Show current code')</option>
                                            <option value="name" @selected(($d['top_bar']['currency_mode'] ?? '') === 'name')>@lang('Show current currency name')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Support label')</label>
                                        <input class="form-control" name="top_bar[support_label]" value="{{ $d['top_bar']['support_label'] ?? '24/7 Support' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Support phone')</label>
                                        <input class="form-control" name="top_bar[support_phone]" value="{{ $d['top_bar']['support_phone'] ?? '' }}">
                                    </div>
                                    <div class="col-md-4 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="top_bar[show_seller_button]" value="1" @checked(!empty($d['top_bar']['show_seller_button']))>
                                        <label class="form-check-label">@lang('Show seller button on top')</label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('Seller text')</label>
                                        <input class="form-control" name="top_bar[seller_text]" value="{{ $d['top_bar']['seller_text'] ?? 'BECOME A SELLER' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">@lang('Seller URL')</label>
                                        <input class="form-control" name="top_bar[seller_url]" value="{{ $d['top_bar']['seller_url'] ?? '/seller/apply' }}">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="form-label mb-0">@lang('Top bar custom buttons (link/dropdown)')</label>
                                            <button type="button" class="btn btn-outline--primary btn-sm" onclick="window.addHeaderButtonRow('top')">@lang('+ Add button')</button>
                                        </div>
                                        <div id="topButtonsWrap">
                                            @foreach((array)($d['top_bar']['custom_buttons'] ?? []) as $idx => $btn)
                                                <div class="border rounded p-2 mb-2 header-btn-row">
                                                    <div class="row g-2">
                                                        <div class="col-md-3"><input class="form-control" name="top_bar[custom_buttons][{{ $idx }}][label]" value="{{ $btn['label'] ?? '' }}" placeholder="Label"></div>
                                                        <div class="col-md-3">
                                                            <select class="form-control" name="top_bar[custom_buttons][{{ $idx }}][type]">
                                                                <option value="link" @selected(($btn['type'] ?? 'link') === 'link')>Link</option>
                                                                <option value="dropdown" @selected(($btn['type'] ?? '') === 'dropdown')>Dropdown</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5"><input class="form-control" name="top_bar[custom_buttons][{{ $idx }}][url]" value="{{ $btn['url'] ?? '#' }}" placeholder="/custom-url"></div>
                                                        <div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline--danger" onclick="this.closest('.header-btn-row').remove()">×</button></div>
                                                        <div class="col-12">
                                                            <textarea class="form-control" rows="2" name="top_bar[custom_buttons][{{ $idx }}][items_text]" placeholder="Dropdown items: Label|URL (one per line)">@foreach((array)($btn['items'] ?? []) as $item){{ ($item['label'] ?? '') }}|{{ ($item['url'] ?? '#') }}
@endforeach</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingMainMenu">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMainMenu" aria-expanded="false" aria-controls="collapseMainMenu">
                                @lang('Main + Menu Bar Controls')
                            </button>
                        </h2>
                        <div id="collapseMainMenu" class="accordion-collapse collapse" aria-labelledby="headingMainMenu" data-bs-parent="#headerControlAccordion">
                            <div class="accordion-body">
                                <div class="row g-2">
                                    <div class="col-md-3 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="main_bar[enabled]" value="1" @checked(!empty($d['main_bar']['enabled']))>
                                        <label class="form-check-label">@lang('Enable main bar')</label>
                                    </div>
                                    <div class="col-md-3 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="menu_bar[enabled]" value="1" @checked(!empty($d['menu_bar']['enabled']))>
                                        <label class="form-check-label">@lang('Enable menu bar')</label>
                                    </div>
                                    <div class="col-md-3 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="main_bar[show_language_icon]" value="1" @checked(!empty($d['main_bar']['show_language_icon']))>
                                        <label class="form-check-label">@lang('Show main language icon')</label>
                                    </div>
                                    <div class="col-md-3 form-check mt-4">
                                        <input class="form-check-input" type="checkbox" name="menu_bar[show_seller_button]" value="1" @checked(!empty($d['menu_bar']['show_seller_button']))>
                                        <label class="form-check-label">@lang('Show seller on menu bar')</label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Logo max height')</label>
                                        <input class="form-control" type="number" name="main_bar[logo_max_height]" value="{{ $d['main_bar']['logo_max_height'] ?? 48 }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">@lang('Main icon size')</label>
                                        <input class="form-control" type="number" name="main_bar[icon_size]" value="{{ $d['main_bar']['icon_size'] ?? 48 }}">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="form-label mb-0">@lang('Menu bar custom buttons')</label>
                                            <button type="button" class="btn btn-outline--primary btn-sm" onclick="window.addHeaderButtonRow('menu')">@lang('+ Add button')</button>
                                        </div>
                                        <div id="menuButtonsWrap">
                                            @foreach((array)($d['menu_bar']['custom_buttons'] ?? []) as $idx => $btn)
                                                <div class="border rounded p-2 mb-2 header-btn-row">
                                                    <div class="row g-2">
                                                        <div class="col-md-3"><input class="form-control" name="menu_bar[custom_buttons][{{ $idx }}][label]" value="{{ $btn['label'] ?? '' }}" placeholder="Label"></div>
                                                        <div class="col-md-3">
                                                            <select class="form-control" name="menu_bar[custom_buttons][{{ $idx }}][type]">
                                                                <option value="link" @selected(($btn['type'] ?? 'link') === 'link')>Link</option>
                                                                <option value="dropdown" @selected(($btn['type'] ?? '') === 'dropdown')>Dropdown</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-5"><input class="form-control" name="menu_bar[custom_buttons][{{ $idx }}][url]" value="{{ $btn['url'] ?? '#' }}" placeholder="/custom-url"></div>
                                                        <div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline--danger" onclick="this.closest('.header-btn-row').remove()">×</button></div>
                                                        <div class="col-12">
                                                            <textarea class="form-control" rows="2" name="menu_bar[custom_buttons][{{ $idx }}][items_text]" placeholder="Dropdown items: Label|URL (one per line)">@foreach((array)($btn['items'] ?? []) as $item){{ ($item['label'] ?? '') }}|{{ ($item['url'] ?? '#') }}
@endforeach</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end">
                <button class="btn btn--primary btn-sm" type="submit">@lang('Save Draft')</button>
            </div>
        </form>

        <form action="{{ route('admin.frontend.sections.header.publish') }}" method="POST" class="card">
            @csrf
            <div class="card-body d-flex align-items-center justify-content-end">
                <button type="submit" class="btn btn--success">@lang('Publish Saved Draft')</button>
            </div>
        </form>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><strong>@lang('Live Preview Snapshot')</strong></div>
            <div class="card-body">
                <div class="p-2 rounded mb-2" style="background: {{ $d['appearance']['top_bg'] }}; color: #fff; min-height: 34px;">
                    {{ $d['top_bar']['support_label'] ?? '24/7 Support' }} {{ $d['top_bar']['support_phone'] ?? '' }}
                </div>
                <div class="p-2 rounded mb-2" style="background: {{ $d['appearance']['main_bg'] }}; color: #0f172a; min-height: 48px;">
                    @lang('Main bar') · @lang('Logo'): {{ $d['main_bar']['logo_max_height'] ?? 48 }}px · @lang('Icons'): {{ $d['main_bar']['icon_size'] ?? 48 }}px
                </div>
                <div class="p-2 rounded" style="background: {{ $d['appearance']['menu_bg'] }}; color: #0f172a; min-height: 34px;">
                    @lang('Menu bar') · @lang('Seller on menu'): {{ !empty($d['menu_bar']['show_seller_button']) ? __('Yes') : __('No') }}
                </div>
                <hr>
                <small class="text-muted">
                    @lang('Live currently uses top color') <strong>{{ $live['appearance']['top_bg'] }}</strong>,
                    @lang('main color') <strong>{{ $live['appearance']['main_bg'] }}</strong>,
                    @lang('menu color') <strong>{{ $live['appearance']['menu_bg'] }}</strong>.
                </small>
                <div class="alert alert-info mt-3 mb-0 py-2">
                    @lang('If your old header buttons were added from legacy panel, they are auto-imported here as link buttons.')
                </div>
                <hr>
                <div class="ratio ratio-16x9">
                    <iframe src="{{ route('admin.frontend.sections.header.preview') }}" loading="lazy" style="border:1px solid #e2e8f0;border-radius:8px;"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function () {
        function addRow(kind) {
            var wrap = document.getElementById(kind === 'top' ? 'topButtonsWrap' : 'menuButtonsWrap');
            if (!wrap) return;
            var idx = wrap.querySelectorAll('.header-btn-row').length;
            var prefix = kind === 'top' ? 'top_bar' : 'menu_bar';
            var html = ''
                + '<div class="border rounded p-2 mb-2 header-btn-row"><div class="row g-2">'
                + '<div class="col-md-3"><input class="form-control" name="' + prefix + '[custom_buttons][' + idx + '][label]" placeholder="Label"></div>'
                + '<div class="col-md-3"><select class="form-control" name="' + prefix + '[custom_buttons][' + idx + '][type]"><option value="link">Link</option><option value="dropdown">Dropdown</option></select></div>'
                + '<div class="col-md-5"><input class="form-control" name="' + prefix + '[custom_buttons][' + idx + '][url]" value="#" placeholder="/custom-url"></div>'
                + '<div class="col-md-1 d-flex align-items-center"><button type="button" class="btn btn-sm btn-outline--danger" onclick="this.closest(\\\'.header-btn-row\\\').remove()">×</button></div>'
                + '<div class="col-12"><textarea class="form-control" rows="2" name="' + prefix + '[custom_buttons][' + idx + '][items_text]" placeholder="Dropdown items: Label|URL (one per line)"></textarea></div>'
                + '</div></div>';
            wrap.insertAdjacentHTML('beforeend', html);
        }
        window.addHeaderButtonRow = addRow;
    })();
</script>
@endpush

