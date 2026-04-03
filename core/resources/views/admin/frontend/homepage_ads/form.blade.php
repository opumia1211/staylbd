@extends('admin.layouts.app')
@section('panel')
@php
    $isEdit = $ad->exists;
    $action = $isEdit ? route('admin.frontend.sections.homepageAds.update', $ad->id) : route('admin.frontend.sections.homepageAds.store');
    $hpFrameStyle = old('frame_style', $ad->frame_style ?? 'none');
    if (!in_array($hpFrameStyle, ['none', 'thin', 'card', 'minimal', 'bordered'], true)) {
        $hpFrameStyle = 'none';
    }
@endphp

@push('style')
<style>
    :root {
        --hp-ad-primary: #1e293b;
        --hp-ad-secondary: #64748b;
        --hp-ad-accent: #3b82f6;
        --hp-ad-bg-light: #f8fafc;
        --hp-ad-border: rgba(226, 232, 240, 0.8);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid var(--hp-ad-border);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--hp-ad-secondary);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--hp-ad-border);
    }

    .form-label {
        font-weight: 600;
        color: var(--hp-ad-primary);
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border-color: var(--hp-ad-border);
        padding: 0.6rem 0.75rem;
        font-size: 0.9rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--hp-ad-accent);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .hp-ad-preview-panel {
        position: sticky;
        top: 20px;
    }

    .preview-container {
        min-height: 360px;
        background: #f1f5f9;
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 20px 20px;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        padding: 0.75rem;
    }

    .hp-ad-wrap {
        transition: width 0.2s ease, height 0.2s ease, left 0.2s ease, top 0.2s ease;
        max-width: 100%;
        position: absolute;
        background: transparent;
        left: 12px;
        top: 12px;
        cursor: move;
        user-select: none;
        touch-action: none;
        outline: none;
        border: none;
        box-shadow: none;
        padding: 0;
    }

    .hp-ad-wrap img {
        width: 100%;
        height: auto;
        display: block;
        transition: opacity 0.2s ease;
        pointer-events: none;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        vertical-align: top;
    }
    #previewIframe { pointer-events: none; }
    .hp-ad-resize-handle {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #2563eb;
        border: 2px solid #fff;
        border-radius: 999px;
        z-index: 3;
    }
    .hp-ad-resize-handle--br { right: -6px; bottom: -6px; cursor: nwse-resize; }
    .hp-ad-resize-handle--tr { right: -6px; top: -6px; cursor: nesw-resize; }
    .preview-device {
        position: absolute;
        inset: 0;
        border: none;
        border-radius: 0;
        pointer-events: none;
    }
    .preview-grid-label {
        position: absolute;
        top: 14px;
        right: 14px;
        background: rgba(15, 23, 42, 0.78);
        color: #fff;
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 999px;
        pointer-events: none;
    }

    /* Frame styles — ডিফল্ট কোনো বর্ডার/সাদা ফ্রেম নয়; শুধু "Thick Border" এ চৌকো ফ্রেম */
    .style-none img,
    .style-thin img,
    .style-card img,
    .style-minimal img {
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }
    .style-card {
        padding: 0;
        border-radius: 0;
        box-shadow: none;
        background: transparent;
    }
    .style-bordered {
        border: 2px solid var(--hp-ad-primary);
        padding: 4px;
        border-radius: 6px;
        background: transparent;
    }
    .style-bordered img {
        border-radius: 2px !important;
    }

    .badge-info {
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
    }

    .pos-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 5px;
        width: fit-content;
    }

    .pos-btn {
        width: 30px;
        height: 30px;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 10px;
    }

    .pos-btn.active {
        background: var(--hp-ad-accent);
        color: #fff;
        border-color: var(--hp-ad-accent);
    }
</style>
@endpush

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h4 class="fw-bold mb-1">{{ $pageTitle }}</h4>
                <p class="text-muted small mb-0">Customize and preview your advertisement slots with advanced controls</p>
            </div>
            <a href="{{ route('admin.frontend.sections.homepageAds') }}" class="btn btn-outline-secondary btn-sm rounded-8">
                <i class="las la-arrow-left"></i> @lang('Back to List')
            </a>
        </div>
    </div>

    <div class="col-lg-7 col-xl-8">
        <form action="{{ $action }}" method="post" enctype="multipart/form-data" id="adForm">
            @csrf
            
            <div class="glass-card p-4 mb-4">
                <h6 class="section-title">General Information</h6>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">@lang('Admin Title') <span class="text-danger">*</span></label>
                        <input type="text" name="admin_title" class="form-control" value="{{ old('admin_title', $ad->admin_title) }}" required placeholder="Internal name for this ad">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">@lang('Advertiser')</label>
                        <input type="text" name="advertiser_name" class="form-control" value="{{ old('advertiser_name', $ad->advertiser_name) }}" placeholder="e.g. Brand Name">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">@lang('Sort Order')</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $ad->sort_order) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">@lang('Click URL')</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="las la-link"></i></span>
                            <input type="url" name="link_url" class="form-control border-start-0" value="{{ old('link_url', $ad->link_url) }}" placeholder="https://example.com/promo">
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="open_new_tab" value="1" id="open_new_tab" {{ old('open_new_tab', $ad->open_new_tab) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="open_new_tab">@lang('Open in new tab')</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card p-4 mb-4">
                <h6 class="section-title">Source & Content</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">@lang('Source Type')</label>
                        <select name="source_type" class="form-select" id="sourceType">
                            <option value="upload" {{ old('source_type', $ad->source_type ?? 'upload') === 'upload' ? 'selected' : '' }}>Upload Image</option>
                            <option value="image_url" {{ old('source_type', $ad->source_type) === 'image_url' ? 'selected' : '' }}>External Image URL</option>
                            <option value="embed_url" {{ old('source_type', $ad->source_type) === 'embed_url' ? 'selected' : '' }}>Embed URL / Iframe</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">@lang('Image Upload') <span class="text-muted small">(@lang('Supports JPG, PNG, WEBP, GIF'))</span></label>
                            <input type="file" name="image" class="form-control" id="imageInput" accept="image/*">
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <hr class="flex-grow-1">
                            <span class="text-muted small fw-bold">OR USE EXTERNAL URL</span>
                            <hr class="flex-grow-1">
                        </div>
                        <label class="form-label">@lang('External / Embed URL')</label>
                        <input type="url" name="external_url" class="form-control" id="externalUrl" value="{{ old('external_url', $ad->external_url) }}" placeholder="https://example.com/ad.jpg or https://example.com/embed">
                    </div>
                </div>
            </div>

            <div class="glass-card p-4 mb-4">
                <h6 class="section-title">Layout & Position</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">@lang('Width Preset')</label>
                        <select name="width_mode" class="form-select" id="widthMode">
                            <option value="full" {{ $ad->width_mode == 'full' ? 'selected' : '' }}>Full Width</option>
                            <option value="wide" {{ $ad->width_mode == 'wide' ? 'selected' : '' }}>Wide</option>
                            <option value="half" {{ $ad->width_mode == 'half' ? 'selected' : '' }}>50% Width</option>
                            <option value="third" {{ $ad->width_mode == 'third' ? 'selected' : '' }}>33% Width</option>
                            <option value="quarter" {{ $ad->width_mode == 'quarter' ? 'selected' : '' }}>25% Width</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('Frame Style')</label>
                        <select name="frame_style" class="form-select" id="frameStyle">
                            <option value="none" {{ $hpFrameStyle === 'none' ? 'selected' : '' }}>@lang('None — image only (no border)')</option>
                            <option value="thin" {{ $hpFrameStyle === 'thin' ? 'selected' : '' }}>@lang('Thin Border (legacy)')</option>
                            <option value="card" {{ $hpFrameStyle === 'card' ? 'selected' : '' }}>@lang('Card Shadow (legacy)')</option>
                            <option value="minimal" {{ $hpFrameStyle === 'minimal' ? 'selected' : '' }}>@lang('Minimal (legacy)')</option>
                            <option value="bordered" {{ $hpFrameStyle === 'bordered' ? 'selected' : '' }}>@lang('Thick Border')</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('Placement Mode')</label>
                        <select name="position" class="form-select" id="posMode">
                            <option value="inline" {{ $ad->position == 'inline' ? 'selected' : '' }}>Inline (Flow)</option>
                            <option value="custom" {{ $ad->position == 'custom' ? 'selected' : '' }}>In-Section (Default)</option>
                            <option value="fixed" {{ $ad->position == 'fixed' ? 'selected' : '' }}>Fixed Position</option>
                            <option value="floating" {{ $ad->position == 'floating' ? 'selected' : '' }}>Sticky/Floating</option>
                        </select>
                    </div>

                    <div class="col-12" id="advancedPos" style="display: none;">
                        <div class="row g-3 p-3 bg-light rounded-8 border">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Anchor Side')</label>
                                <select name="side" class="form-select" id="anchorSide">
                            <option value="top" {{ old('side', $ad->side ?? 'bottom') === 'top' ? 'selected' : '' }}>Top</option>
                            <option value="bottom" {{ old('side', $ad->side ?? 'bottom') === 'bottom' ? 'selected' : '' }}>Bottom</option>
                            <option value="left" {{ old('side', $ad->side ?? 'bottom') === 'left' ? 'selected' : '' }}>Left Side</option>
                            <option value="right" {{ old('side', $ad->side ?? 'bottom') === 'right' ? 'selected' : '' }}>Right Side</option>
                            <option value="center" {{ old('side', $ad->side ?? 'bottom') === 'center' ? 'selected' : '' }}>Center</option>
                            <option value="top-left" {{ old('side', $ad->side ?? 'bottom') === 'top-left' ? 'selected' : '' }}>Top Left</option>
                            <option value="top-right" {{ old('side', $ad->side ?? 'bottom') === 'top-right' ? 'selected' : '' }}>Top Right</option>
                            <option value="bottom-left" {{ old('side', $ad->side ?? 'bottom') === 'bottom-left' ? 'selected' : '' }}>Bottom Left</option>
                            <option value="bottom-right" {{ old('side', $ad->side ?? 'bottom') === 'bottom-right' ? 'selected' : '' }}>Bottom Right</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Precise Offsets (px)')</label>
                                <div class="row g-2">
                                    <div class="col-3">
                                        <input type="number" id="offsetTop" name="top" class="form-control form-control-sm" placeholder="Top" value="{{ $ad->top }}">
                                    </div>
                                    <div class="col-3">
                                        <input type="number" id="offsetBottom" name="bottom" class="form-control form-control-sm" placeholder="Btm" value="{{ $ad->bottom }}">
                                    </div>
                                    <div class="col-3">
                                        <input type="number" id="offsetLeft" name="left" class="form-control form-control-sm" placeholder="Lft" value="{{ $ad->left }}">
                                    </div>
                                    <div class="col-3">
                                        <input type="number" id="offsetRight" name="right" class="form-control form-control-sm" placeholder="Rgt" value="{{ $ad->right }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">@lang('Show On')</label>
                        <select name="display_pages" id="displayPages" class="form-select">
                            <option value="all" {{ old('display_pages', $ad->display_pages ?? 'all') === 'all' ? 'selected' : '' }}>All Pages</option>
                            <option value="homepage" {{ old('display_pages', $ad->display_pages) === 'homepage' ? 'selected' : '' }}>Homepage Only</option>
                            <option value="non_home" {{ old('display_pages', $ad->display_pages) === 'non_home' ? 'selected' : '' }}>All Except Homepage</option>
                            <option value="custom_path" {{ old('display_pages', $ad->display_pages) === 'custom_path' ? 'selected' : '' }}>Custom Path</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">@lang('Custom Path')</label>
                        <input type="text" name="custom_path" class="form-control" value="{{ old('custom_path', $ad->custom_path) }}" placeholder="/products/* or /category/fashion">
                    </div>
                </div>
            </div>

            <div class="glass-card p-4 mb-4">
                <h6 class="section-title">Sizing Dynamics</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">@lang('Size Type')</label>
                        <select name="size_type" class="form-select" id="sizeType">
                            <option value="auto" {{ $ad->size_type == 'auto' ? 'selected' : '' }}>Aspect Ratio (Auto)</option>
                            <option value="custom" {{ $ad->size_type == 'custom' ? 'selected' : '' }}>Custom Dimensions</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('Max Height Limit (px)')</label>
                        <input type="number" name="max_height_px" id="maxHeight" class="form-control" value="{{ $ad->max_height_px ?? 400 }}" placeholder="e.g. 250">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">@lang('Z-index')</label>
                        <input type="number" name="z_index" class="form-control" value="{{ old('z_index', $ad->z_index ?? 1100) }}" min="1" max="99999">
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch pt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="ad_active" {{ $ad->is_active ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="ad_active">@lang('Status: Active')</label>
                        </div>
                    </div>
                    
                    <div class="col-12" id="customSizeRow" style="display: none;">
                        <div class="row g-3 p-3 bg-light rounded-8 border">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Custom Width') <span class="text-muted small">(px, %, vh)</span></label>
                                <input type="text" name="custom_width" id="cWidth" class="form-control" value="{{ $ad->custom_width }}" placeholder="e.g. 300px">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Custom Height') <span class="text-muted small">(px, %, vh)</span></label>
                                <input type="text" name="custom_height" id="cHeight" class="form-control" value="{{ $ad->custom_height }}" placeholder="e.g. 150px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg flex-grow-1 rounded-8">
                    <i class="las la-cloud-upload-alt"></i> {{ $isEdit ? __('Update Advertisement') : __('Publish Advertisement') }}
                </button>
            </div>
        </form>
    </div>

    <div class="col-lg-5 col-xl-4">
        <div class="hp-ad-preview-panel">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="section-title mb-0">Live Preview</h6>
                    <span class="badge-info" id="previewModeTag">IN-SECTION</span>
                </div>
                <div class="mb-2 d-flex gap-2">
                    <select id="previewPageType" class="form-select form-select-sm">
                        <option value="home">Homepage Canvas</option>
                        <option value="product">Product Page Canvas</option>
                        <option value="category">Category Page Canvas</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="snapCenterBtn">Center</button>
                </div>
                <div class="mb-2 d-flex gap-1 flex-wrap">
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="top-left">TL</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="top">T</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="top-right">TR</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="left">L</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="center">C</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="right">R</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="bottom-left">BL</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="bottom">B</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm js-align-btn" data-align="bottom-right">BR</button>
                </div>
                
                <div class="preview-container mb-3" id="previewArea">
                    <div class="preview-device"></div>
                    <div class="preview-grid-label" id="previewHint">Drag + Resize</div>
                    <div id="adWrapper" class="hp-ad-wrap style-{{ $hpFrameStyle }}">
                        <img id="previewImg" src="{{ $ad->imageUrl() ?: $ad->external_url }}" alt="Preview" style="{{ !$ad->imageUrl() && !$ad->external_url ? 'display:none;' : '' }}">
                        <iframe id="previewIframe" src="" style="display:none;width:100%;height:180px;border:0;"></iframe>
                        <div id="placeholderText" class="text-center text-muted" style="{{ $ad->imageUrl() || $ad->external_url ? 'display:none;' : '' }}">
                            <i class="las la-image d-block fs-1 opacity-25"></i>
                            <span class="small">No content selected</span>
                        </div>
                        <span class="hp-ad-resize-handle hp-ad-resize-handle--tr" data-resize="tr"></span>
                        <span class="hp-ad-resize-handle hp-ad-resize-handle--br" data-resize="br"></span>
                    </div>
                </div>

                <div class="bg-light p-3 rounded-8">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Width Content:</span>
                        <span class="fw-bold small" id="metaWidth">Full</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Max Height:</span>
                        <span class="fw-bold small" id="metaHeight">None</span>
                    </div>
                    <div class="d-flex justify-content-between mb-0">
                        <span class="text-muted small">Style Type:</span>
                        <span class="fw-bold small text-capitalize" id="metaStyle">Thin</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted small">Live Box:</span>
                        <span class="fw-bold small" id="liveBoxMeta">X:0 Y:0 W:0 H:0</span>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-3 mt-4 bg-primary text-white border-0">
                <div class="d-flex gap-3 align-items-start">
                    <i class="las la-info-circle fs-4"></i>
                    <div>
                        <p class="small mb-0"><strong>Pro Tip:</strong> Use Fixed position to show ads on sides like floating banners. Use "33% Width" to fit 3 ads in one line on large screens.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
(function() {
    const inputs = {
        sourceType: document.getElementById('sourceType'),
        image: document.getElementById('imageInput'),
        external: document.getElementById('externalUrl'),
        widthMode: document.getElementById('widthMode'),
        frameStyle: document.getElementById('frameStyle'),
        posMode: document.getElementById('posMode'),
        anchorSide: document.getElementById('anchorSide'),
        displayPages: document.getElementById('displayPages'),
        top: document.getElementById('offsetTop'),
        bottom: document.getElementById('offsetBottom'),
        left: document.getElementById('offsetLeft'),
        right: document.getElementById('offsetRight'),
        sizeType: document.getElementById('sizeType'),
        maxHeight: document.getElementById('maxHeight'),
        cWidth: document.getElementById('cWidth'),
        cHeight: document.getElementById('cHeight'),
        previewPageType: document.getElementById('previewPageType'),
        snapCenterBtn: document.getElementById('snapCenterBtn')
    };

    const preview = {
        img: document.getElementById('previewImg'),
        iframe: document.getElementById('previewIframe'),
        wrap: document.getElementById('adWrapper'),
        placeholder: document.getElementById('placeholderText'),
        metaWidth: document.getElementById('metaWidth'),
        metaHeight: document.getElementById('metaHeight'),
        metaStyle: document.getElementById('metaStyle'),
        liveBoxMeta: document.getElementById('liveBoxMeta'),
        modeTag: document.getElementById('previewModeTag'),
        area: document.getElementById('previewArea'),
        hint: document.getElementById('previewHint')
    };

    const pageCanvas = {
        home: { w: 390, h: 330, label: 'Homepage' },
        product: { w: 360, h: 300, label: 'Product Page' },
        category: { w: 375, h: 315, label: 'Category Page' }
    };

    let drag = null;
    let resize = null;

    function pxInt(v, fallback = 0) {
        const n = parseInt(v, 10);
        return Number.isFinite(n) ? n : fallback;
    }

    function clamp(n, min, max) {
        return Math.max(min, Math.min(max, n));
    }

    function applyPageCanvas() {
        const key = inputs.previewPageType.value || 'home';
        const c = pageCanvas[key] || pageCanvas.home;
        preview.area.style.height = c.h + 'px';
        preview.hint.textContent = c.label + ' - Drag + Resize';
    }

    function syncOffsetInputsFromPosition() {
        const areaRect = preview.area.getBoundingClientRect();
        const wrapRect = preview.wrap.getBoundingClientRect();
        const top = Math.round(wrapRect.top - areaRect.top);
        const left = Math.round(wrapRect.left - areaRect.left);
        const bottom = Math.round(areaRect.bottom - wrapRect.bottom);
        const right = Math.round(areaRect.right - wrapRect.right);
        inputs.top.value = top;
        inputs.left.value = left;
        inputs.bottom.value = bottom;
        inputs.right.value = right;
        preview.liveBoxMeta.textContent = 'X:' + left + ' Y:' + top + ' W:' + Math.round(wrapRect.width) + ' H:' + Math.round(wrapRect.height);
    }

    function applyPositionFromInputs() {
        const areaRect = preview.area.getBoundingClientRect();
        const wrapRect = preview.wrap.getBoundingClientRect();
        const width = wrapRect.width || 260;
        const height = wrapRect.height || 120;
        const side = inputs.anchorSide.value || 'bottom-right';
        let x = pxInt(inputs.left.value, 12);
        let y = pxInt(inputs.top.value, 12);

        if (side === 'top') { x = Math.round((areaRect.width - width) / 2); y = pxInt(inputs.top.value, 12); }
        if (side === 'bottom') { x = Math.round((areaRect.width - width) / 2); y = areaRect.height - height - pxInt(inputs.bottom.value, 12); }
        if (side === 'left') { x = pxInt(inputs.left.value, 12); y = Math.round((areaRect.height - height) / 2); }
        if (side === 'right') { x = areaRect.width - width - pxInt(inputs.right.value, 12); y = Math.round((areaRect.height - height) / 2); }
        if (side === 'center') { x = Math.round((areaRect.width - width) / 2); y = Math.round((areaRect.height - height) / 2); }
        if (side === 'top-left') { x = pxInt(inputs.left.value, 12); y = pxInt(inputs.top.value, 12); }
        if (side === 'top-right') { x = areaRect.width - width - pxInt(inputs.right.value, 12); y = pxInt(inputs.top.value, 12); }
        if (side === 'bottom-left') { x = pxInt(inputs.left.value, 12); y = areaRect.height - height - pxInt(inputs.bottom.value, 12); }
        if (side === 'bottom-right') { x = areaRect.width - width - pxInt(inputs.right.value, 12); y = areaRect.height - height - pxInt(inputs.bottom.value, 12); }

        x = clamp(x, 0, Math.max(0, areaRect.width - width));
        y = clamp(y, 0, Math.max(0, areaRect.height - height));
        preview.wrap.style.left = x + 'px';
        preview.wrap.style.top = y + 'px';
        syncOffsetInputsFromPosition();
    }

    function enableDragAndResize() {
        preview.wrap.addEventListener('pointerdown', function(e) {
            if (e.target && e.target.dataset && e.target.dataset.resize) {
                const dir = e.target.dataset.resize;
                const rect = preview.wrap.getBoundingClientRect();
                resize = {
                    dir,
                    startX: e.clientX,
                    startY: e.clientY,
                    startW: rect.width,
                    startH: rect.height,
                    startL: pxInt(preview.wrap.style.left, 0),
                    startT: pxInt(preview.wrap.style.top, 0)
                };
                preview.wrap.setPointerCapture(e.pointerId);
                return;
            }
            drag = {
                startX: e.clientX,
                startY: e.clientY,
                startL: pxInt(preview.wrap.style.left, 0),
                startT: pxInt(preview.wrap.style.top, 0)
            };
            preview.wrap.setPointerCapture(e.pointerId);
        });

        preview.wrap.addEventListener('pointermove', function(e) {
            const areaRect = preview.area.getBoundingClientRect();
            if (drag) {
                const dx = e.clientX - drag.startX;
                const dy = e.clientY - drag.startY;
                const w = preview.wrap.offsetWidth;
                const h = preview.wrap.offsetHeight;
                const x = clamp(drag.startL + dx, 0, Math.max(0, areaRect.width - w));
                const y = clamp(drag.startT + dy, 0, Math.max(0, areaRect.height - h));
                preview.wrap.style.left = x + 'px';
                preview.wrap.style.top = y + 'px';
                syncOffsetInputsFromPosition();
            }
            if (resize) {
                const dx = e.clientX - resize.startX;
                const dy = e.clientY - resize.startY;
                let w = Math.max(80, resize.startW + dx);
                let h = Math.max(50, resize.startH + (resize.dir === 'tr' ? -dy : dy));
                if (resize.dir === 'tr') {
                    const top = clamp(resize.startT + dy, 0, areaRect.height - 50);
                    preview.wrap.style.top = top + 'px';
                }
                w = Math.min(w, areaRect.width - pxInt(preview.wrap.style.left, 0));
                h = Math.min(h, areaRect.height - pxInt(preview.wrap.style.top, 0));
                preview.wrap.style.width = w + 'px';
                preview.wrap.style.height = h + 'px';
                if (preview.img) preview.img.style.height = '100%';
                inputs.sizeType.value = 'custom';
                inputs.cWidth.value = Math.round(w) + 'px';
                inputs.cHeight.value = Math.round(h) + 'px';
            }
        });

        preview.wrap.addEventListener('pointerup', function(e) {
            drag = null;
            resize = null;
            preview.wrap.releasePointerCapture(e.pointerId);
            syncOffsetInputsFromPosition();
        });
    }

    function updatePreview() {
        // Image logic
        if (inputs.sourceType.value === 'embed_url' && inputs.external.value) {
            preview.img.style.display = 'none';
            preview.iframe.style.display = 'block';
            preview.iframe.src = inputs.external.value;
            preview.placeholder.style.display = 'none';
        } else if (inputs.external.value && inputs.sourceType.value !== 'upload') {
            preview.iframe.style.display = 'none';
            preview.iframe.src = '';
            preview.img.src = inputs.external.value;
            preview.img.style.display = 'block';
            preview.placeholder.style.display = 'none';
        } else if (inputs.image.files && inputs.image.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.iframe.style.display = 'none';
                preview.iframe.src = '';
                preview.img.src = e.target.result;
                preview.img.style.display = 'block';
                preview.placeholder.style.display = 'none';
            };
            reader.readAsDataURL(inputs.image.files[0]);
        } else if (!preview.img.getAttribute('src')) {
            preview.iframe.style.display = 'none';
            preview.iframe.src = '';
            preview.img.style.display = 'none';
            preview.placeholder.style.display = 'block';
        }

        // Style logic
        preview.wrap.className = 'hp-ad-wrap style-' + inputs.frameStyle.value;
        preview.metaStyle.textContent = inputs.frameStyle.value;

        // Width logic
        let w = inputs.widthMode.value;
        const widthMap = { 'full': '100%', 'wide': '90%', 'half': '50%', 'third': '33%', 'quarter': '25%' };
        preview.wrap.style.width = widthMap[w] || '100%';
        preview.wrap.style.maxWidth = '420px';
        preview.metaWidth.textContent = w.charAt(0).toUpperCase() + w.slice(1);

        // Position logic
        const p = inputs.posMode.value;
        preview.modeTag.textContent = p.toUpperCase();
        document.getElementById('advancedPos').style.display = (p !== 'custom') ? 'block' : 'none';

        // Size logic
        const sType = inputs.sizeType.value;
        document.getElementById('customSizeRow').style.display = (sType === 'custom') ? 'flex' : 'none';
        
        if (sType === 'custom') {
            preview.wrap.style.width = inputs.cWidth.value || preview.wrap.style.width;
            preview.wrap.style.height = inputs.cHeight.value || 'auto';
            preview.img.style.height = inputs.cHeight.value || 'auto';
        } else {
            preview.wrap.style.height = 'auto';
            preview.img.style.height = 'auto';
        }

        // Height limit
        if (inputs.maxHeight.value) {
            preview.img.style.maxHeight = inputs.maxHeight.value + 'px';
            preview.metaHeight.textContent = inputs.maxHeight.value + 'px';
        } else {
            preview.img.style.maxHeight = 'none';
            preview.metaHeight.textContent = 'Auto';
        }
        if (inputs.displayPages.value === 'homepage') {
            inputs.previewPageType.value = 'home';
        } else if (inputs.displayPages.value === 'custom_path' && /product/i.test((document.querySelector('[name="custom_path"]').value || ''))) {
            inputs.previewPageType.value = 'product';
        }
        applyPageCanvas();
        applyPositionFromInputs();
    }

    // Listeners
    ['sourceType', 'image', 'external', 'widthMode', 'frameStyle', 'posMode', 'anchorSide', 'displayPages', 'top', 'bottom', 'left', 'right', 'sizeType', 'maxHeight', 'cWidth', 'cHeight', 'previewPageType'].forEach(k => {
        inputs[k].addEventListener('change', updatePreview);
        inputs[k].addEventListener('input', updatePreview);
    });

    inputs.snapCenterBtn.addEventListener('click', function() {
        inputs.anchorSide.value = 'center';
        updatePreview();
    });
    document.querySelectorAll('.js-align-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            inputs.anchorSide.value = this.getAttribute('data-align') || 'center';
            updatePreview();
        });
    });

    // Initial run
    enableDragAndResize();
    updatePreview();
    
    // Position Mode Toggle Details
    document.getElementById('posMode').addEventListener('change', function() {
        document.getElementById('advancedPos').style.display = (this.value !== 'custom') ? 'block' : 'none';
    });
})();
</script>
@endpush
