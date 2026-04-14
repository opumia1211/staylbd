@extends('admin.layouts.app')
@section('panel')
@php
    $general = gs();
    $logoEffectsEnabled = $general->logo_effects_enabled ?? 0;
    $logoHoverEffect = $general->logo_hover_effect ?? 'none';
    $logoAnimation = $general->logo_animation ?? 'none';
    $logoAnimationSpeed = $general->logo_animation_speed ?? 'normal';
    $logoOpacity = $general->logo_opacity ?? 1;
    $logoMaxWidth = $general->logo_max_width ?? 200;
    $logoMaxHeight = $general->logo_max_height ?? 60;
    $footerLogoHeight = $general->footer_logo_height ?? 35;
@endphp

<div class="row gy-3 admin-icon-page admin-icon-page--compact">
    {{-- Main Card --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header bg--primary">
                <h5 class="card-title text-white mb-0">
                    <i class="las la-images me-2"></i>@lang('Logo & Favicon Management')
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.frontend.sections.icon.update') }}" method="POST" enctype="multipart/form-data" id="logoForm" class="admin-icon-form">
                    @csrf
                    
                    {{-- Logo Upload Section --}}
                    <div class="row g-3 mb-3">
                        {{-- Main Logo --}}
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <div class="card icon-upload-card h-100 border">
                                <div class="card-header py-2 px-3">
                                    <span class="badge bg--primary">@lang('Main Logo')</span>
                                </div>
                                <div class="card-body text-center p-3">
                                    <div class="logo-preview-box logo-preview-box--light mb-2" id="logoPreviewBox" data-type="logo">
                                        @if(getLogo('logo'))
                                            <img src="{{ getLogo('logo') }}" alt="Logo" id="logoPreviewImg" class="logo-preview-img">
                                            <button type="button" class="btn btn-danger btn-sm logo-remove-btn" data-remove-type="logo" title="@lang('Remove logo')">
                                                <i class="las la-times"></i>
                                            </button>
                                            <div class="logo-format-info" style="display: none;"></div>
                                        @else
                                            <div class="text-muted logo-placeholder-content" id="logoPlaceholder">
                                                <i class="las la-cloud-upload-alt" style="font-size: 42px;"></i>
                                                <p class="mb-1 fw-bold">@lang('Click to upload')</p>
                                                <small class="d-block"><strong>@lang('Formats'):</strong> SVG, PNG, WEBP, JPG</small>
                                                <small class="d-block"><strong>@lang('Size'):</strong> 320×70 – 460×100 px</small>
                                                <small class="d-block"><strong>@lang('Min-Max'):</strong> 320×70 (min) – 460×100 (max)</small>
                                                <small class="d-block"><strong>@lang('Max File'):</strong> 2 MB</small>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="logo" id="logoInput" class="form-control" accept=".svg,.png,.jpg,.jpeg,.webp,.gif" onchange="previewLogo(this, 'logo', 'logoPreviewBox', 'logoPreviewImg', 'logoPlaceholder')">
                                    <input type="hidden" name="remove_logo" id="removeLogo" value="0">
                                    <small class="text-muted d-block mt-1 text-start">@lang('Supported'): 320×70 px – 460×100 px</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Dark Logo --}}
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <div class="card icon-upload-card h-100 border">
                                <div class="card-header py-2 px-3 bg-dark-card">
                                    <span class="badge bg-light text-dark">@lang('Dark Mode Logo')</span>
                                </div>
                                <div class="card-body text-center p-3 bg-dark-card">
                                    <div class="logo-preview-box logo-preview-box--dark mb-2" id="logoDarkPreviewBox" data-type="logo_dark">
                                        @if(getLogo('logo_dark'))
                                            <img src="{{ getLogo('logo_dark') }}" alt="Dark Logo" id="logoDarkPreviewImg" class="logo-preview-img">
                                            <button type="button" class="btn btn-danger btn-sm logo-remove-btn" data-remove-type="logo_dark" title="@lang('Remove logo')">
                                                <i class="las la-times"></i>
                                            </button>
                                            <div class="logo-format-info" style="display: none;"></div>
                                        @else
                                            <div class="text-white-50 logo-placeholder-content" id="logoDarkPlaceholder">
                                                <i class="las la-cloud-upload-alt" style="font-size: 42px;"></i>
                                                <p class="mb-1 fw-bold">@lang('Click to upload')</p>
                                                <small class="d-block"><strong>@lang('Formats'):</strong> SVG, PNG, WEBP, JPG</small>
                                                <small class="d-block"><strong>@lang('Size'):</strong> 320×70 – 460×100 px</small>
                                                <small class="d-block"><strong>@lang('Min-Max'):</strong> 320×70 (min) – 460×100 (max)</small>
                                                <small class="d-block"><strong>@lang('Max File'):</strong> 2 MB</small>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="logo_dark" id="logoDarkInput" class="form-control" accept=".svg,.png,.jpg,.jpeg,.webp,.gif" onchange="previewLogo(this, 'logo_dark', 'logoDarkPreviewBox', 'logoDarkPreviewImg', 'logoDarkPlaceholder')">
                                    <input type="hidden" name="remove_logo_dark" id="removeLogoDark" value="0">
                                    <small class="text-white-50 d-block mt-1 text-start">@lang('Supported'): 320×70 px – 460×100 px</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Favicon --}}
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <div class="card icon-upload-card h-100 border">
                                <div class="card-header py-2 px-3">
                                    <span class="badge bg--success">@lang('Browser Favicon')</span>
                                </div>
                                <div class="card-body text-center p-3">
                                    <div class="logo-preview-box logo-preview-box--favicon mb-2" id="faviconPreviewBox" data-type="favicon">
                                        @if(getLogo('favicon'))
                                            <img src="{{ getLogo('favicon') }}" alt="Favicon" id="faviconPreviewImg" class="logo-preview-img logo-preview-img--favicon">
                                            <button type="button" class="btn btn-danger btn-sm logo-remove-btn" data-remove-type="favicon" title="@lang('Remove favicon')">
                                                <i class="las la-times"></i>
                                            </button>
                                        @else
                                            <div class="text-muted logo-placeholder-content" id="faviconPlaceholder">
                                                <i class="las la-window-maximize" style="font-size: 42px;"></i>
                                                <p class="mb-1 fw-bold">@lang('Click to upload')</p>
                                                <small class="d-block"><strong>@lang('Formats'):</strong> SVG, PNG, ICO, JPG, WEBP</small>
                                                <small class="d-block"><strong>@lang('Size'):</strong> 32×32 px, 64×64 px</small>
                                                <small class="d-block"><strong>@lang('PWA/Mobile Tab'):</strong> 180×180 px</small>
                                                <small class="d-block"><strong>@lang('Max File'):</strong> 512 KB</small>
                                            </div>
                                        @endif
                                    </div>
                                    <input type="file" name="favicon" id="faviconInput" class="form-control mt-2" accept=".svg,.png,.ico,.jpg,.jpeg,.webp" data-preview-type="favicon">
                                    <input type="hidden" name="remove_favicon" id="removeFavicon" value="0">
                                    <button type="submit" class="btn btn--success btn-sm mt-2" id="faviconUploadBtn" style="display:none;">
                                        <i class="las la-upload me-1"></i>@lang('Upload Favicon Now')
                                    </button>
                                    <small class="text-muted d-block mt-1 text-start">@lang('Supported'): 32×32, 64×64, 180×180 px. SVG, PNG, ICO, JPG, WEBP</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Animation & Effects --}}
                    <div class="card mb-3 admin-icon-section-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="las la-magic me-2"></i>@lang('Logo Animation & Effects')</h6>
                            <label class="form-switch mb-0">
                                <input type="checkbox" class="form-check-input" name="logo_effects_enabled" id="logoEffectsEnabled" value="1" {{ $logoEffectsEnabled ? 'checked' : '' }}>
                                <span class="ms-2">@lang('Enable')</span>
                            </label>
                        </div>
                        <div class="card-body p-3 {{ $logoEffectsEnabled ? '' : 'logo-effects-disabled' }}" id="effectsBody">
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label">@lang('Hover Effect')</label>
                                    <select class="form-select" name="logo_hover_effect" id="logoHoverEffect">
                                        <option value="none" {{ $logoHoverEffect == 'none' ? 'selected' : '' }}>@lang('None')</option>
                                        <option value="fade" {{ $logoHoverEffect == 'fade' ? 'selected' : '' }}>@lang('Fade')</option>
                                        <option value="scale" {{ $logoHoverEffect == 'scale' ? 'selected' : '' }}>@lang('Scale (Zoom)')</option>
                                        <option value="rotate" {{ $logoHoverEffect == 'rotate' ? 'selected' : '' }}>@lang('Rotate')</option>
                                        <option value="bounce" {{ $logoHoverEffect == 'bounce' ? 'selected' : '' }}>@lang('Bounce')</option>
                                        <option value="shake" {{ $logoHoverEffect == 'shake' ? 'selected' : '' }}>@lang('Shake')</option>
                                        <option value="glow" {{ $logoHoverEffect == 'glow' ? 'selected' : '' }}>@lang('Glow')</option>
                                        <option value="bright" {{ $logoHoverEffect == 'bright' ? 'selected' : '' }}>@lang('Brighten')</option>
                                        <option value="shadow" {{ $logoHoverEffect == 'shadow' ? 'selected' : '' }}>@lang('Shadow')</option>
                                        <option value="lift" {{ $logoHoverEffect == 'lift' ? 'selected' : '' }}>@lang('Lift Up')</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label">@lang('Continuous Animation')</label>
                                    <select class="form-select" name="logo_animation" id="logoAnimation">
                                        <option value="none" {{ $logoAnimation == 'none' ? 'selected' : '' }}>@lang('None')</option>
                                        <option value="pulse" {{ $logoAnimation == 'pulse' ? 'selected' : '' }}>@lang('Pulse')</option>
                                        <option value="float" {{ $logoAnimation == 'float' ? 'selected' : '' }}>@lang('Float')</option>
                                        <option value="glow" {{ $logoAnimation == 'glow' ? 'selected' : '' }}>@lang('Glow')</option>
                                        <option value="shimmer" {{ $logoAnimation == 'shimmer' ? 'selected' : '' }}>@lang('Shimmer')</option>
                                        <option value="heartbeat" {{ $logoAnimation == 'heartbeat' ? 'selected' : '' }}>@lang('Heartbeat')</option>
                                        <option value="swing" {{ $logoAnimation == 'swing' ? 'selected' : '' }}>@lang('Swing')</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label">@lang('Animation Speed')</label>
                                    <select class="form-select" name="logo_animation_speed" id="logoAnimationSpeed">
                                        <option value="slow" {{ $logoAnimationSpeed == 'slow' ? 'selected' : '' }}>@lang('Slow (3s)')</option>
                                        <option value="normal" {{ $logoAnimationSpeed == 'normal' ? 'selected' : '' }}>@lang('Normal (2s)')</option>
                                        <option value="fast" {{ $logoAnimationSpeed == 'fast' ? 'selected' : '' }}>@lang('Fast (1s)')</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label">@lang('Logo Opacity'): <span id="opacityValue">{{ $logoOpacity * 100 }}%</span></label>
                                    <input type="range" class="form-range" name="logo_opacity" id="logoOpacity" min="0.3" max="1" step="0.05" value="{{ $logoOpacity }}">
                                </div>
                            </div>
                            
                            {{-- Live Preview --}}
                            <div class="mt-3 p-3 bg-light rounded text-center">
                                <label class="form-label d-block mb-2">@lang('Live Preview') - @lang('Hover over logo to test effects')</label>
                                <div id="effectPreview" style="display: inline-block;">
                                    @if(getLogo('logo'))
                                        <img src="{{ getLogo('logo') }}" alt="Preview" id="previewImage" class="{{ getLogoEffectClasses() }}" style="max-height: 80px; {{ getLogoStyle() }}">
                                    @else
                                        <div class="text-muted py-4">
                                            <i class="las la-image" style="font-size: 48px;"></i>
                                            <p>@lang('Upload a logo to preview effects')</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Invoice QR Caption (Customer) - shown above QR on invoice --}}
                    @if(\Schema::hasColumn('general_settings', 'invoice_qr_caption_en'))
                    <div class="card mb-3 admin-icon-section-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="las la-qrcode me-2"></i>@lang('Invoice QR Caption') (@lang('Customer scan'))</h6>
                        </div>
                        <div class="card-body p-3">
                            <p class="small text-muted">@lang('Text above customer QR on invoice. Use') <code>@{{ name }}</code> @lang('for customer name. You can set any language.')</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">@lang('English')</label>
                                    <input type="text" class="form-control" name="invoice_qr_caption_en" value="{{ gs('invoice_qr_caption_en') }}" placeholder="e.g. Customer name, please scan the QR code after receiving product" maxlength="500">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Bengali') / বাংলা</label>
                                    <input type="text" class="form-control" name="invoice_qr_caption_bn" value="{{ gs('invoice_qr_caption_bn') }}" placeholder="উদাহরণ: গ্রাহকের নাম, প্রোডাক্ট পাওয়ার পর QR স্ক্যান করুন" maxlength="500">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Display Settings --}}
                    <div class="card mb-3 admin-icon-section-card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="las la-ruler-combined me-2"></i>@lang('Display Settings')</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Header Logo Max Width')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="logo_max_width" value="{{ $logoMaxWidth }}" min="100" max="400">
                                        <span class="input-group-text">px</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Header Logo Max Height')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="logo_max_height" value="{{ $logoMaxHeight }}" min="30" max="120">
                                        <span class="input-group-text">px</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Footer Logo Height')</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" name="footer_logo_height" value="{{ $footerLogoHeight }}" min="20" max="80">
                                        <span class="input-group-text">px</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex gap-2 flex-wrap admin-icon-action-row">
                        <button type="submit" class="btn btn--primary btn-lg">
                            <i class="las la-save me-2"></i>@lang('Save All Changes')
                        </button>
                        <button type="button" class="btn btn--secondary btn-lg" onclick="location.reload()">
                            <i class="las la-sync me-2"></i>@lang('Refresh Page')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- File Formats Recommended --}}
    <div class="col-12 admin-icon-info-cards">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-file-image me-2"></i>@lang('File Formats (Recommended)')</h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <span class="badge bg-primary">@lang('Primary')</span>
                        <strong class="d-block mt-1">SVG</strong>
                        <small class="text-muted">@lang('Must have - Best quality')</small>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-secondary">@lang('Backup')</span>
                        <strong class="d-block mt-1">PNG</strong>
                        <small class="text-muted">@lang('Widely supported')</small>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-info">@lang('Modern')</span>
                        <strong class="d-block mt-1">WEBP</strong>
                        <small class="text-muted">@lang('Smaller file size')</small>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-light text-dark">@lang('Optional')</span>
                        <strong class="d-block mt-1">JPG</strong>
                        <small class="text-muted">@lang('For photos')</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="col-12 admin-icon-info-cards">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-info-circle me-2"></i>@lang('Logo Usage Locations')</h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <h6 class="text-primary"><i class="las la-image me-1"></i> @lang('Main Logo') (320×70 – 460×100 px)</h6>
                        <ul class="list-unstyled small">
                            <li><i class="las la-check text-success me-1"></i> @lang('User Header - Home Button')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('Admin Panel Sidebar')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('Login & Register Pages')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('Footer Section')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('Invoice & Contact Pages')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('Error Pages (404, 419)')</li>
                            <li><i class="las la-home text-primary me-1"></i> <strong>@lang('Logo = Home Button')</strong></li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h6 class="text-dark"><i class="las la-moon me-1"></i> @lang('Dark Logo')</h6>
                        <ul class="list-unstyled small">
                            <li><i class="las la-check text-success me-1"></i> @lang('Admin Sidebar')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('Dark Footer')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('Admin Login Page')</li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <h6 class="text-success"><i class="las la-window-maximize me-1"></i> @lang('Browser Favicon') (32×32 – 180×180 px)</h6>
                        <ul class="list-unstyled small">
                            <li><i class="las la-check text-success me-1"></i> @lang('Browser Tabs')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('Bookmarks')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('PWA / Mobile Tab - 180×180')</li>
                            <li><i class="las la-check text-success me-1"></i> @lang('All New Tab Links')</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
"use strict";

// Session keep-alive runs globally from admin layout (every 90s)

// Preview Logo - type: 'logo' | 'logo_dark' | 'favicon'
function previewLogo(input, type, boxId, imgId, placeholderId) {
    var box = document.getElementById(boxId);
    var placeholder = document.getElementById(placeholderId);
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            // Hide placeholder (format info)
            if (placeholder) placeholder.style.display = 'none';
            
            var img = document.getElementById(imgId);
            if (!img) {
                img = document.createElement('img');
                img.id = imgId;
                img.className = type === 'favicon' ? 'logo-preview-img logo-preview-img--favicon' : 'logo-preview-img';
                img.style.pointerEvents = 'none';
                box.innerHTML = '';
                box.appendChild(img);
                
                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm logo-remove-btn';
                removeBtn.setAttribute('data-remove-type', type);
                removeBtn.innerHTML = '<i class="las la-times"></i>';
                removeBtn.title = 'Remove';
                box.appendChild(removeBtn);
            }
            
            img.src = e.target.result;
            
            // Reset remove flag when new file selected
            var removeIds = {logo: 'removeLogo', logo_dark: 'removeLogoDark', favicon: 'removeFavicon'};
            document.getElementById(removeIds[type]).value = '0';
            
            if (type === 'logo') {
                var previewImg = document.getElementById('previewImage');
                if (previewImg) {
                    previewImg.src = e.target.result;
                } else {
                    var effectPreview = document.getElementById('effectPreview');
                    effectPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" id="previewImage" style="max-height: 80px;">';
                }
                if (typeof updatePreviewEffects === 'function') updatePreviewEffects();
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove Logo - submits form to actually remove from server
function removeLogo(type, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    var boxId, inputId, removeInputId;
    if (type === 'logo') {
        boxId = 'logoPreviewBox';
        inputId = 'logoInput';
        removeInputId = 'removeLogo';
    } else if (type === 'logo_dark') {
        boxId = 'logoDarkPreviewBox';
        inputId = 'logoDarkInput';
        removeInputId = 'removeLogoDark';
    } else {
        boxId = 'faviconPreviewBox';
        inputId = 'faviconInput';
        removeInputId = 'removeFavicon';
    }
    
    document.getElementById(inputId).value = '';
    document.getElementById(removeInputId).value = '1';
    
    var box = document.getElementById(boxId);
    var placeholderHtml = type === 'logo_dark' 
        ? '<div class="text-white-50 logo-placeholder-content" id="logoDarkPlaceholder"><i class="las la-cloud-upload-alt" style="font-size: 42px;"></i><p class="mb-1 fw-bold">Click to upload</p><small class="d-block"><strong>Formats:</strong> SVG, PNG, WEBP, JPG</small><small class="d-block"><strong>Size:</strong> 320×70 – 460×100 px</small><small class="d-block"><strong>Min-Max:</strong> 320×70 (min) – 460×100 (max)</small><small class="d-block"><strong>Max File:</strong> 2 MB</small></div>'
        : type === 'favicon'
        ? '<div class="text-muted logo-placeholder-content" id="faviconPlaceholder"><i class="las la-window-maximize" style="font-size: 42px;"></i><p class="mb-1 fw-bold">Click to upload</p><small class="d-block"><strong>Formats:</strong> SVG, PNG, ICO, JPG, WEBP</small><small class="d-block"><strong>Size:</strong> 32×32, 64×64, 180×180 px</small><small class="d-block"><strong>PWA/Mobile:</strong> 180×180 px</small><small class="d-block"><strong>Max File:</strong> 512 KB</small></div>'
        : '<div class="text-muted logo-placeholder-content" id="logoPlaceholder"><i class="las la-cloud-upload-alt" style="font-size: 42px;"></i><p class="mb-1 fw-bold">Click to upload</p><small class="d-block"><strong>Formats:</strong> SVG, PNG, WEBP, JPG</small><small class="d-block"><strong>Size:</strong> 320×70 – 460×100 px</small><small class="d-block"><strong>Min-Max:</strong> 320×70 (min) – 460×100 (max)</small><small class="d-block"><strong>Max File:</strong> 2 MB</small></div>';
    
    box.innerHTML = placeholderHtml;
    
    if (type === 'logo') {
        var ep = document.getElementById('effectPreview');
        if (ep) ep.innerHTML = '<div class="text-muted py-4"><i class="las la-image" style="font-size: 48px;"></i><p>Upload a logo to preview effects</p></div>';
    }
    
    document.getElementById('logoForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        var removeBtn = e.target.closest('.logo-remove-btn[data-remove-type]');
        if (removeBtn) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            removeLogo(removeBtn.getAttribute('data-remove-type'));
            return false;
        }
    }, true);

    document.querySelectorAll('.logo-preview-box').forEach(function(box) {
        box.addEventListener('click', function(e) {
            if (e.target.closest('.logo-remove-btn')) return;
            var input = this.closest('.card-body').querySelector('input[type="file"]');
            if (input) input.click();
        });
    });

    var faviconInput = document.getElementById('faviconInput');
    if (faviconInput) {
        faviconInput.addEventListener('change', function() {
            previewLogo(this, 'favicon', 'faviconPreviewBox', 'faviconPreviewImg', 'faviconPlaceholder');
            var btn = document.getElementById('faviconUploadBtn');
            if (btn) btn.style.display = this.files.length ? 'inline-block' : 'none';
        });
    }

    var effectsEnabled = document.getElementById('logoEffectsEnabled');
    if (effectsEnabled) {
        effectsEnabled.addEventListener('change', function() {
            var body = document.getElementById('effectsBody');
            if (body) {
                body.classList.toggle('logo-effects-disabled', !this.checked);
            }
            if (typeof updatePreviewEffects === 'function') updatePreviewEffects();
        });
    }

    ['logoHoverEffect', 'logoAnimation', 'logoAnimationSpeed'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('change', updatePreviewEffects);
    });

    var logoOpacityEl = document.getElementById('logoOpacity');
    if (logoOpacityEl) {
        logoOpacityEl.addEventListener('input', function() {
            var val = document.getElementById('opacityValue');
            if (val) val.textContent = Math.round(this.value * 100) + '%';
            if (typeof updatePreviewEffects === 'function') updatePreviewEffects();
        });
    }

    if (typeof updatePreviewEffects === 'function') updatePreviewEffects();
});

// Update preview effects
function updatePreviewEffects() {
    var previewImg = document.getElementById('previewImage');
    if (!previewImg) return;
    
    var enabled = document.getElementById('logoEffectsEnabled').checked;
    var hover = document.getElementById('logoHoverEffect').value;
    var animation = document.getElementById('logoAnimation').value;
    var speed = document.getElementById('logoAnimationSpeed').value;
    var opacity = document.getElementById('logoOpacity').value;
    
    // Remove existing classes
    previewImg.className = previewImg.className.replace(/logo-(hover|animate|speed)-\S+/g, '').trim();
    
    // Apply new classes if enabled
    if (enabled) {
        if (hover && hover !== 'none') {
            previewImg.classList.add('logo-hover-' + hover);
        }
        if (animation && animation !== 'none') {
            previewImg.classList.add('logo-animate-' + animation);
        }
        if (speed && speed !== 'normal') {
            previewImg.classList.add('logo-speed-' + speed);
        }
    }
    
    // Apply opacity
    previewImg.style.opacity = opacity;
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updatePreviewEffects();
});
</script>
@endpush
