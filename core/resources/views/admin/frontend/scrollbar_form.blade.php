@extends('admin.layouts.app')
@section('panel')
@php
    $fd = $formData ?? [];
    $barId = $bar ? $bar->id : null;
    $isCustomMode = (bool)($isCustomMode ?? false);
    $scrollbarMode = $scrollbarMode ?? ($isCustomMode ? 'custom' : 'default');
    $initialRichSegments = [];
    $fdItemsForRich = $fd['items'] ?? [];
    if (!is_array($fdItemsForRich)) { $fdItemsForRich = (array) $fdItemsForRich; }
    foreach ($fdItemsForRich as $it) {
        $it = is_array($it) ? $it : (array) $it;
        if ((string)($it['type'] ?? 'text') === 'image') continue;
        $segments = $it['segments'] ?? [];
        if (is_array($segments) && !empty($segments)) {
            foreach ($segments as $s) {
                $s = is_array($s) ? $s : (array) $s;
                $txt = trim((string) ($s['text'] ?? ''));
                if ($txt === '') continue;
                $initialRichSegments[] = [
                    'text' => $txt,
                    'color' => (string) ($s['color'] ?? ($it['color'] ?? '#333333')),
                    'weight' => (string) ($s['weight'] ?? ($s['font_weight'] ?? ($it['font_weight'] ?? '400'))),
                ];
            }
        } else {
            $txt = trim((string) ($it['content_text'] ?? ($it['content'] ?? '')));
            if ($txt !== '') {
                $initialRichSegments[] = [
                    'text' => $txt,
                    'color' => (string) ($it['color'] ?? '#333333'),
                    'weight' => (string) ($it['font_weight'] ?? '400'),
                ];
            }
        }
    }
    if (empty($initialRichSegments)) { $initialRichSegments = [['text' => '', 'color' => '#333333', 'weight' => '400']]; }
@endphp

<div class="scrollbar-config-wrapper">
    {{-- Header Navigation --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h5 class="fw-bold mb-0">{{ $bar ? __('Modify Ticker') : __('Create New Ticker') }}</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.frontend.sections.scrollbar') }}">@lang('Headline Tickers')</a></li>
                    <li class="breadcrumb-item active">{{ $bar ? __('Edit') : __('New') }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.frontend.sections.scrollbar') }}" class="btn btn-outline-secondary btn-sm me-2"><i class="las la-arrow-left"></i> @lang('Back')</a>
            <button type="submit" form="scrollbarFormPage" class="btn btn-primary btn-sm px-4 shadow-sm"><i class="las la-save me-1"></i> @lang('Save Configuration')</button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Configuration Column --}}
        <div class="col-xl-8 col-lg-7">
            {{-- Presets Bar --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3 overflow-auto">
                    <div class="d-flex align-items-center gap-3">
                        <span class="small fw-bold text-muted text-uppercase ls-1">@lang('Quick Presets'):</span>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-primary btn-sm scrollbar-preset-btn" data-preset="breaking_news"><i class="las la-bullhorn me-1"></i>@lang('Breaking News')</button>
                            <button type="button" class="btn btn-outline-success btn-sm scrollbar-preset-btn" data-preset="home_offer"><i class="las la-tag me-1"></i>@lang('Limited Offer')</button>
                            <button type="button" class="btn btn-outline-info btn-sm scrollbar-preset-btn" data-preset="product_highlight"><i class="las la-star me-1"></i>@lang('Highlights')</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm scrollbar-preset-btn" data-preset="minimal_clean"><i class="las la-leaf me-1"></i>@lang('Minimal')</button>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.frontend.sections.scrollbar.save') }}" method="POST" enctype="multipart/form-data" id="scrollbarFormPage">
                @csrf
                @if($barId)<input type="hidden" name="id" value="{{ $barId }}">@endif
                <input type="hidden" name="scrollbar_mode" value="{{ $scrollbarMode }}">

                {{-- Unified Content Editor --}}
                <div class="card border-0 shadow-sm mb-4 border-top-premium-primary">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-label-primary rounded me-3 d-flex align-items-center justify-content-center">
                                <i class="las la-edit fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">@lang('Content Architecture')</h6>
                                <small class="text-muted">@lang('Design your headline with rich text and emojis')</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="editor-toolbar mb-3 p-2 bg-light-premium rounded border d-flex flex-wrap gap-2 align-items-center">
                            <div class="d-flex align-items-center gap-1 border-end pe-2">
                                <input type="color" id="scrollbarRichColor" class="form-control form-control-color p-1 border-0 bg-transparent" value="#333333" style="width:30px;height:30px;">
                                <div class="d-flex gap-1" id="scrollbarRichQuickColors">
                                    <button type="button" class="rich-quick-color-btn bg-danger" data-color="#ef4444"></button>
                                    <button type="button" class="rich-quick-color-btn bg-success" data-color="#22c55e"></button>
                                    <button type="button" class="rich-quick-color-btn bg-primary" data-color="#3b82f6"></button>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-1 border-end pe-2">
                                <button type="button" class="btn btn-sm btn-light border" id="scrollbarRichWeightBold" title="Bold"><i class="las la-bold"></i></button>
                                <button type="button" class="btn btn-sm btn-light border" id="scrollbarRichWeightNormal" title="Normal"><i class="las la-minus"></i></button>
                            </div>

                            <select class="form-select form-select-sm border-0 bg-transparent" id="scrollbarRichFontSize" style="width: 85px;">
                                <option value="">@lang('Size')</option>
                                <option value="12px">12px</option>
                                <option value="14px">14px</option>
                                <option value="16px">16px</option>
                                <option value="18px">18px</option>
                                <option value="20px">20px</option>
                            </select>

                            <button type="button" class="btn btn-sm btn-light border ms-auto" id="scrollbarEmojiPickerToggle"><i class="las la-smile"></i></button>
                            <button type="button" class="btn btn-sm btn-label-warning border-0" id="scrollbarRichClearFormat">@lang('Reset')</button>
                        </div>

                        <div id="scrollbarRichEditor" class="form-control premium-rich-editor" contenteditable="true" placeholder="@lang('Start typing your headlines here...')"></div>
                        
                        <div class="scrollbar-emoji-picker shadow-lg border rounded-3 bg-white d-none" id="scrollbarEmojiPickerPanel">
                            <div class="p-3">
                                <h6 class="small fw-bold mb-2 text-muted">@lang('Quick Emoji')</h6>
                                <div class="emoji-grid-premium" id="scrollbarEmojiGrid"></div>
                            </div>
                        </div>

                        <input type="hidden" name="rich_content" id="scrollbarRichContentInput">
                        <input type="hidden" name="rich_segments_json" id="scrollbarRichSegmentsInput">
                        <div id="scrollbarItemsContainer" class="d-none">
                            @foreach(array_values(old('items', $fd['items'] ?? [['type'=>'text','content'=>'']])) as $idx => $item)
                                @include('admin.frontend.partials.scrollbar_item_row', ['idx' => $idx, 'item' => $item])
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Ticker Properties & Schedule --}}
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 border-bottom-0">
                                <h6 class="mb-0 fw-bold text-dark"><i class="las la-cog me-2 text-primary"></i>@lang('Tactical Configuration')</h6>
                            </div>
                            <div class="card-body pt-0">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Ticker Identity')</label>
                                    <div class="input-group input-group-merge border rounded-3 overflow-hidden">
                                        <span class="input-group-text border-0 bg-light"><i class="las la-tag"></i></span>
                                        <input type="text" class="form-control border-0" name="title" value="{{ old('title', $fd['title'] ?? '') }}" placeholder="@lang('e.g. Flash Sale Ticker')">
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('System Position')</label>
                                        <select class="form-select border-0 bg-light rounded-3" name="position" required>
                                            @foreach(\App\Services\ScrollbarService::POSITIONS as $v => $l)
                                                <option value="{{ $v }}" {{ old('position', $fd['position'] ?? '') == $v ? 'selected' : '' }}>{{ __($l) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Visual Template')</label>
                                        <select class="form-select border-0 bg-light rounded-3" name="template" required>
                                            @foreach(['glass'=>'Glass Architecture','solid'=>'Solid Opaque','minimal'=>'Minimal Clean','dark'=>'Modern Dark','breaking_news'=>'Breaking News','offer'=>'Promo Offer'] as $v => $l)
                                                <option value="{{ $v }}" {{ old('template', $fd['template'] ?? 'glass') == $v ? 'selected' : '' }}>{{ __($l) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Flow Velocity')</label>
                                        <div class="input-group input-group-merge border rounded-3 overflow-hidden">
                                            <input type="number" class="form-control border-0 bg-light" name="scroll_speed" value="{{ old('scroll_speed', $fd['scroll_speed'] ?? 45) }}" min="1" max="100">
                                            <span class="input-group-text border-0 bg-light text-muted tiny">@lang('SPD')</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Visibility Control')</label>
                                        <select class="form-select border-0 bg-light rounded-3" name="visibility">
                                            <option value="public" {{ ($fd['visibility'] ?? 'public') == 'public' ? 'selected' : '' }}>@lang('Universal (Public)')</option>
                                            <option value="private" {{ ($fd['visibility'] ?? '') == 'private' ? 'selected' : '' }}>@lang('Internal (Admin)')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white py-3 border-bottom-0">
                                <h6 class="mb-0 fw-bold text-dark"><i class="las la-clock me-2 text-info"></i>@lang('Deployment Schedule')</h6>
                            </div>
                            <div class="card-body pt-0">
                                <div class="p-3 bg-label-info rounded-4 border border-info border-opacity-10 mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="dot bg-info me-2" style="width: 8px; height: 8px; border-radius: 50%;"></div>
                                        <span class="tiny fw-bold text-info">@lang('TIME SENSITIVE DEPLOYMENT')</span>
                                    </div>
                                    <p class="tiny text-muted mt-1 mb-0">@lang('Optional: Automate ticker activation and decommission.')</p>
                                </div>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Activation Time')</label>
                                        <div class="input-group input-group-merge border rounded-3 overflow-hidden">
                                            <span class="input-group-text border-0 bg-light"><i class="las la-calendar-plus"></i></span>
                                            <input type="datetime-local" class="form-control border-0 bg-light" name="schedule_start" value="{{ !empty($fd['schedule_start']) ? date('Y-m-d\TH:i', strtotime($fd['schedule_start'])) : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Decommission Time')</label>
                                        <div class="input-group input-group-merge border rounded-3 overflow-hidden">
                                            <span class="input-group-text border-0 bg-light"><i class="las la-calendar-minus"></i></span>
                                            <input type="datetime-local" class="form-control border-0 bg-light" name="schedule_end" value="{{ !empty($fd['schedule_end']) ? date('Y-m-d\TH:i', strtotime($fd['schedule_end'])) : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white py-3 border-bottom-0">
                                <h6 class="mb-0 fw-bold text-dark"><i class="las la-palette me-2 text-warning"></i>@lang('Advanced Aesthetics & Logic')</h6>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row g-4 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Background Signature')</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="position-relative">
                                                <input type="color" class="form-control form-control-color border-0 p-0 shadow-none bg-transparent" id="scrollbarBarColorQuick" value="{{ preg_match('/^#[0-9A-Fa-f]{6}$/', $fd['bar_background_value'] ?? '') ? $fd['bar_background_value'] : '#0d6efd' }}" style="width: 40px; height: 40px;">
                                            </div>
                                            <div class="flex-grow-1">
                                                <input type="text" class="form-control border-0 bg-light rounded-3" name="bar_background_value" id="scrollbarBarBackgroundValue" value="{{ old('bar_background_value', $fd['bar_background_value'] ?? '') }}" placeholder="#hex code">
                                            </div>
                                            <select class="form-select border-0 bg-light rounded-3 w-auto" name="bar_background_type" id="scrollbarBarBackgroundType">
                                                <option value="solid" {{ ($fd['bar_background_type'] ?? '') == 'solid' ? 'selected' : '' }}>Solid</option>
                                                <option value="gradient" {{ ($fd['bar_background_type'] ?? '') == 'gradient' ? 'selected' : '' }}>Gradient</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Height') (px)</label>
                                        <input type="number" class="form-control border-0 bg-light rounded-3" name="bar_height" value="{{ old('bar_height', $fd['bar_height'] ?? 52) }}" min="8" max="150">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Typography Weight')</label>
                                        <select class="form-select border-0 bg-light rounded-3" name="default_text_weight">
                                            @foreach(['light'=>'Lightweight','normal'=>'Regular','medium'=>'Medium Tier','semibold'=>'Semi Bold','bold'=>'Bold Emphasis'] as $v => $l)
                                                <option value="{{ $v }}" {{ old('default_text_weight', $fd['default_text_weight'] ?? 'normal') == $v ? 'selected' : '' }}>{{ __($l) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Initial Status')</label>
                                        <select class="form-select border-0 bg-light rounded-3" name="status">
                                            <option value="1" {{ (old('status', $fd['status'] ?? 1) == 1) ? 'selected' : '' }}>@lang('LIVE (Online)')</option>
                                            <option value="0" {{ (old('status', $fd['status'] ?? 1) == 0) ? 'selected' : '' }}>@lang('DRAFT (Internal)')</option>
                                        </select>
                                    </div>
                                </div>

                                @if($isCustomMode)
                                <div class="mt-4 pt-4 border-top">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('Target Page Protocols')</label>
                                            <select class="form-select border-0 bg-light rounded-3" name="visibility_pages" id="scrollbarVisibilityPages">
                                                <option value="all" {{ ($fd['visibility_pages'] ?? 'all') == 'all' ? 'selected' : '' }}>@lang('All Page Clusters')</option>
                                                <option value="home" {{ ($fd['visibility_pages'] ?? '') == 'home' ? 'selected' : '' }}>@lang('Homepage Protocol Only')</option>
                                                <option value="custom_urls" {{ ($fd['visibility_pages'] ?? '') == 'custom_urls' ? 'selected' : '' }}>@lang('Specific URL Matrix')</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 scrollbar-custom-url-wrap {{ ($fd['visibility_pages'] ?? '') === 'custom_urls' ? '' : 'd-none' }}">
                                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">@lang('URL Pattern Matching')</label>
                                            <input type="text" class="form-control border-0 bg-light rounded-3" name="custom_urls" value="{{ old('custom_urls', $fd['custom_urls'] ?? '') }}" placeholder="/offer, /promo/*">
                                            <small class="text-muted tiny mt-1">@lang('Use * for wildcard matching (e.g. /products/*)')</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Preview Column --}}
        <div class="col-xl-4 col-lg-5">
            <div class="sticky-preview-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-bold"><i class="las la-eye me-2 text-primary"></i>@lang('Device Preview')</h6>
                    <div class="btn-group btn-group-sm bg-white shadow-sm rounded-pill p-1">
                        <button type="button" class="btn btn-outline-secondary scrollbar-preview-size-btn border-0 active" data-width="100%"><i class="las la-desktop"></i></button>
                        <button type="button" class="btn btn-outline-secondary scrollbar-preview-size-btn border-0" data-width="390px"><i class="las la-mobile"></i></button>
                    </div>
                </div>

                <div class="phone-mockup shadow-lg mx-auto" id="previewFrameContainer">
                    <div class="phone-frame">
                        <div class="phone-speaker"></div>
                        <div class="phone-screen bg-light">
                            <div class="app-status-bar d-flex justify-content-between px-3 pt-2 small text-muted">
                                <span>12:30</span>
                                <i class="las la-battery-full"></i>
                            </div>
                            
                            <div class="app-content d-flex flex-column h-100">
                                <div class="bg-white p-3 border-bottom d-flex align-items-center gap-2">
                                    <div class="bg-primary rounded-circle" style="width: 25px; height: 25px;"></div>
                                    <div class="bg-light rounded" style="width: 100px; height: 10px;"></div>
                                    <i class="las la-bars ms-auto text-muted"></i>
                                </div>
                                
                                <div class="preview-iframe-wrapper flex-grow-1 position-relative bg-white">
                                    <iframe id="scrollbarLivePreviewIframe" src="about:blank" style="width:100%; height:100%; border:0;"></iframe>
                                    <div class="preview-placeholder text-center p-5 opacity-25">
                                        <i class="las la-sync fa-spin fs-1"></i>
                                    </div>
                                </div>
                                
                                <div class="mt-auto p-4 bg-white border-top">
                                    <div class="bg-light rounded mb-2" style="width: 100%; height: 150px;"></div>
                                    <div class="bg-light rounded" style="width: 60%; height: 20px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-primary border-0 shadow-sm mt-4 p-3 rounded-3 d-flex align-items-start">
                    <i class="las la-info-circle fs-4 me-2 mt-1"></i>
                    <div>
                        <h6 class="alert-heading mb-1 fw-bold small">@lang('Pro Tip')</h6>
                        <p class="mb-0 small text-muted">@lang('Use contrasting colors for the background and text to ensure high readability on all devices.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    /* Premium Architecture */
    :root {
        --premium-primary: #696cff;
        --premium-primary-light: #e7e7ff;
        --premium-gray: #f5f5f9;
        --premium-border: #d9dee3;
        --premium-info: #03c3ec;
    }

    .scrollbar-config-wrapper { font-family: 'Public Sans', sans-serif; }

    .border-top-premium-primary { border-top: 4px solid var(--premium-primary) !important; }
    .bg-light-premium { background-color: var(--premium-gray); }
    .bg-label-primary { background-color: #e7e7ff !important; color: var(--premium-primary) !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: var(--premium-info) !important; }
    .bg-label-warning { background-color: #fff2e2 !important; color: #ffab00 !important; }
    
    .avatar-sm { width: 40px; height: 40px; }
    .ls-1 { letter-spacing: 0.5px; }
    .tiny { font-size: 0.72rem; }

    /* Rich Editor */
    .premium-rich-editor {
        min-height: 150px;
        border-radius: 0.75rem;
        padding: 1.5rem;
        font-size: 1rem;
        line-height: 1.6;
        background: #fff;
        border: 2px solid #f0f2f4;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .premium-rich-editor:focus {
        border-color: var(--premium-primary);
        box-shadow: 0 0 0 0.25rem rgba(105, 108, 255, 0.1);
        outline: none;
    }
    .premium-rich-editor[contenteditable]:empty:before {
        content: attr(placeholder);
        color: #8592a3;
    }

    .rich-quick-color-btn {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #eee;
        padding: 0;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .rich-quick-color-btn:hover { transform: scale(1.2); }

    .emoji-grid-premium {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
        max-height: 200px;
        overflow-y: auto;
    }
    .scrollbar-emoji-btn {
        font-size: 1.4rem;
        background: none;
        border: none;
        padding: 8px;
        border-radius: 10px;
        transition: all 0.2s;
    }
    .scrollbar-emoji-btn:hover { background: #f0f2f4; transform: scale(1.15); }
    
    .scrollbar-emoji-picker {
        position: absolute;
        bottom: 100%;
        right: 0;
        z-index: 1000;
        width: 320px;
        margin-bottom: 15px;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }

    /* Device Preview & Phone */
    .sticky-preview-container { position: sticky; top: 120px; }
    .phone-mockup {
        width: 280px;
        height: 580px;
        background: #1e1e1e;
        border-radius: 45px;
        padding: 12px;
        border: 6px solid #2b2b2b;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .phone-frame { width: 100%; height: 100%; background: #fff; border-radius: 35px; overflow: hidden; position: relative; border: 4px solid #000; }
    .phone-speaker {
        position: absolute; top: 0; left: 50%; transform: translateX(-50%);
        width: 80px; height: 25px; background: #000; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px; z-index: 10;
    }
    
    .preview-iframe-wrapper { border: 1px dashed #eee; margin: 10px; border-radius: 15px; background: #fafafa; overflow: hidden; }

    /* Forms */
    .form-control:focus, .form-select:focus {
        border-color: var(--premium-primary);
        box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1);
    }

    .ls-1 { letter-spacing: 0.5px; }

    @media (max-width: 991px) {
        .sticky-preview-container { position: relative; top: 0; margin-top: 3rem; }
        .phone-mockup { width: 100%; max-width: 320px; }
    }
</style>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    
    var scrollbarPreviewLiveBase = {!! json_encode(url()->route('admin.frontend.sections.scrollbar.preview.live')) !!};
    var initialRichSegments = {!! json_encode($initialRichSegments, JSON_UNESCAPED_UNICODE) !!};
    var previewTimer = null;
    var savedRange = null;

    // 1. Rich Editor Core
    function renderEditor(segs) {
        let html = '';
        segs.forEach(s => {
            if (!s.text) return;
            html += `<span style="color:${s.color}; font-weight:${s.weight};">${s.text}</span>`;
        });
        $('#scrollbarRichEditor').html(html || '<span style="color:#333333;"></span>');
    }

    function getSegments() {
        let out = [];
        let editor = document.getElementById('scrollbarRichEditor');
        let walker = document.createTreeWalker(editor, NodeFilter.SHOW_TEXT, null);
        let node;
        while (node = walker.nextNode()) {
            let txt = node.nodeValue.replace(/\u00A0/g, ' ');
            if (!txt.trim()) continue;
            let cs = window.getComputedStyle(node.parentElement || editor);
            out.push({
                text: txt,
                color: rgbToHex(cs.color),
                weight: cs.fontWeight
            });
        }
        return out;
    }

    function rgbToHex(rgb) {
        if (!rgb || rgb[0] === '#') return rgb || '#333';
        let m = rgb.match(/\d+/g);
        if (!m) return '#333';
        return '#' + m.slice(0,3).map(x => parseInt(x).toString(16).padStart(2,'0')).join('');
    }

    // 2. Toolbar Actions
    $('#scrollbarRichApplyColor').on('click', function() {
        restoreSelection();
        document.execCommand('foreColor', false, $('#scrollbarRichColor').val());
        refreshPreview();
    });

    $('.rich-quick-color-btn').on('click', function() {
        let color = $(this).data('color');
        $('#scrollbarRichColor').val(color);
        restoreSelection();
        document.execCommand('foreColor', false, color);
        refreshPreview();
    });

    $('#scrollbarRichWeightBold').on('click', function() {
        restoreSelection();
        document.execCommand('bold');
        refreshPreview();
    });

    $('#scrollbarRichFontSize').on('change', function() {
        let size = $(this).val();
        if (!size) return;
        restoreSelection();
        let sel = window.getSelection();
        if (!sel.rangeCount) return;
        let range = sel.getRangeAt(0);
        let span = document.createElement('span');
        span.style.fontSize = size;
        span.appendChild(range.extractContents());
        range.insertNode(span);
        refreshPreview();
    });

    $('#scrollbarRichClearFormat').on('click', function() {
        restoreSelection();
        document.execCommand('removeFormat');
        refreshPreview();
    });

    // 3. Emoji System
    const emojis = ['🔥','🎁','⚡','💥','🚀','💎','🛍️','✅','⭐','📢','🎉','🎯','💯','🆕','📣','🎊','✨','🌟','🏷️','💸','💰','📦','🚚','🎈','🔔','📌','🛒','👑'];
    function renderEmojis() {
        let html = '';
        emojis.forEach(e => { html += `<button type="button" class="scrollbar-emoji-btn" data-emoji="${e}">${e}</button>`; });
        $('#scrollbarEmojiGrid').html(html);
    }

    $(document).on('click', '.scrollbar-emoji-btn', function() {
        let emoji = $(this).data('emoji');
        restoreSelection();
        let sel = window.getSelection();
        let range = sel.getRangeAt(0);
        range.deleteContents();
        range.insertNode(document.createTextNode(emoji));
        $('#scrollbarEmojiPickerPanel').addClass('d-none');
        refreshPreview();
    });

    $('#scrollbarEmojiPickerToggle').on('click', function(e) {
        e.stopPropagation();
        $('#scrollbarEmojiPickerPanel').toggleClass('d-none');
    });

    // 4. Live Preview Logic
    function refreshPreview() {
        if (previewTimer) clearTimeout(previewTimer);
        previewTimer = setTimeout(() => {
            let $f = $('#scrollbarFormPage');
            let params = {
                position: $f.find('[name="position"]').val(),
                template: $f.find('[name="template"]').val(),
                bar_height: $f.find('[name="bar_height"]').val(),
                scroll_speed: $f.find('[name="scroll_speed"]').val(),
                bar_background_value: $('#scrollbarBarColorQuick').val(),
                bar_background_type: $f.find('[name="bar_background_type"]').val(),
                default_text_weight: $f.find('[name="default_text_weight"]').val(),
                items: JSON.stringify([{ type: 'text', segments: getSegments(), is_active: 1 }])
            };
            let url = scrollbarPreviewLiveBase + '?' + $.param(params);
            $('#scrollbarLivePreviewIframe').attr('src', url);
            $('.preview-placeholder').fadeOut();
        }, 500);
    }

    // 5. Selection Management
    function saveSelection() {
        let sel = window.getSelection();
        if (sel.rangeCount) savedRange = sel.getRangeAt(0).cloneRange();
    }

    function restoreSelection() {
        if (!savedRange) return;
        let sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(savedRange);
        $('#scrollbarRichEditor').focus();
    }

    $('#scrollbarRichEditor').on('keyup mouseup focus blur', saveSelection);
    $('#scrollbarRichEditor').on('input', refreshPreview);

    // 6. Init
    $(document).ready(function() {
        renderEditor(initialRichSegments);
        renderEmojis();
        refreshPreview();

        // Color sync
        $('#scrollbarBarColorQuick').on('input change', function() {
            $('#scrollbarBarBackgroundValue').val($(this).val());
            refreshPreview();
        });

        // Preset handling
        $('.scrollbar-preset-btn').on('click', function() {
            let preset = $(this).data('preset');
            // Simplified preset logic for demonstration
            if (preset === 'breaking_news') {
                $('[name="template"]').val('breaking_news');
                $('[name="scroll_speed"]').val(60);
                $('#scrollbarBarColorQuick').val('#dc3545');
            } else if (preset === 'home_offer') {
                $('[name="template"]').val('offer');
                $('[name="scroll_speed"]').val(48);
                $('#scrollbarBarColorQuick').val('#ffc107');
            }
            $('#scrollbarBarColorQuick').trigger('change');
        });

        $('.scrollbar-preview-size-btn').on('click', function() {
            let w = $(this).data('width');
            $('#previewFrameContainer').css('width', w === '100%' ? '100%' : '320px');
            $('.scrollbar-preview-size-btn').removeClass('active');
            $(this).addClass('active');
        });

        $('#scrollbarFormPage').on('submit', function() {
            let segs = getSegments();
            $('#scrollbarRichContentInput').val(segs.map(s => s.text).join(' '));
            $('#scrollbarRichSegmentsInput').val(JSON.stringify(segs));
            // Ensure background value is synced
            $('#scrollbarBarBackgroundValue').val($('#scrollbarBarColorQuick').val());
        });
    });

})(jQuery);
</script>
@endpush
