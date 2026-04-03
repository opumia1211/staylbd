@extends('admin.layouts.app')
@section('panel')
    @php
        // Banner elements loaded from controller (DB-compatible, no raw JSON)
        $bannerElements = $bannerElements ?? collect();
        $totalBanners = $bannerElements->count();
        $displayBanners = $bannerElements;
        
        $bannerSliderSettings = \App\Models\Frontend::where('data_keys', 'banner.content')->orderBy('id', 'desc')->first();
        $slideInterval = 5;
        $autoplay = 1;
        $bannerWidth = 2560;
        $bannerHeight = 400;
        if ($bannerSliderSettings && isset($bannerSliderSettings->data_values)) {
            $dv = $bannerSliderSettings->data_values;
            $slideInterval = (int)($dv->slide_interval_seconds ?? 5);
            $autoplay = (int)($dv->autoplay ?? 1);
            $bannerWidth = (int)($dv->banner_width ?? 2560);
            $bannerHeight = (int)($dv->banner_height ?? 400);
        }
        if ($bannerWidth < 100) $bannerWidth = 2560;
        if ($bannerHeight < 50) $bannerHeight = 400;
        if ($slideInterval < 1 || $slideInterval > 60) $slideInterval = 5;
    @endphp

    @php $bannerStats = $bannerStats ?? ['impressions' => 0, 'clicks' => 0, 'ctr' => 0]; @endphp
    <div class="row">
        <!-- Compact: Analytics + Guidelines in one bar -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm banner-top-bar">
                <div class="card-body py-2 px-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex flex-wrap align-items-center gap-4">
                            <div class="banner-stat-item">
                                <span class="text-muted small text-uppercase d-block" style="font-size: 10px;">@lang('Impressions')</span>
                                <strong class="text--primary">{{ number_format($bannerStats['impressions']) }}</strong>
                            </div>
                            <div class="banner-stat-item">
                                <span class="text-muted small text-uppercase d-block" style="font-size: 10px;">@lang('Clicks')</span>
                                <strong>{{ number_format($bannerStats['clicks']) }}</strong>
                            </div>
                            <div class="banner-stat-item">
                                <span class="text-muted small text-uppercase d-block" style="font-size: 10px;">@lang('CTR')</span>
                                <strong>{{ $bannerStats['ctr'] }}%</strong>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                            <span><i class="las la-file-image text--primary"></i> JPG, PNG, WEBP, MP4</span>
                            <span><i class="las la-compress-arrows-alt"></i> 5MB max</span>
                            <span><i class="las la-desktop"></i> 2560×400 (@lang('Recommended'))</span>
                            <span><i class="las la-mobile-alt"></i> @lang('Use same ratio (6.4:1)')</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact: Slider Settings single row – পাবলিক পেজে কত সেকেন্ড পর পর ব্যানার স্লাইড হবে -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <p class="small text-muted mb-2">@lang('These settings control the public homepage banner slider. Save and refresh the public page to see changes.')</p>
                    <form action="{{ route('admin.frontend.sections.content.banner') }}" method="POST" class="banner-slider-form">
                        @csrf
                        <input type="hidden" name="type" value="content">
                        <div class="d-flex flex-wrap align-items-end gap-3">
                            <div class="form-group mb-0">
                                <label class="form-label small mb-0">@lang('Slide interval (sec)')</label>
                                <input type="number" name="slide_interval_seconds" class="form-control form-control-sm" style="width: 70px;" value="{{ $slideInterval }}" min="1" max="60" title="@lang('Seconds between each banner on public page (1-60)')">
                                <small class="text-muted d-block" style="font-size: 10px;">@lang('Public page: change banner every') … @lang('sec')</small>
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label small mb-0">@lang('Autoplay')</label>
                                <select name="autoplay" class="form-select form-select-sm" style="width: 80px;">
                                    <option value="1" {{ $autoplay == 1 ? 'selected' : '' }}>@lang('On')</option>
                                    <option value="0" {{ $autoplay == 0 ? 'selected' : '' }}>@lang('Off')</option>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label small mb-0">@lang('Width')</label>
                                <input type="number" name="banner_width" class="form-control form-control-sm" style="width: 80px;" value="{{ $bannerWidth }}" min="100" max="4000">
                            </div>
                            <div class="form-group mb-0">
                                <label class="form-label small mb-0">@lang('Height')</label>
                                <input type="number" name="banner_height" class="form-control form-control-sm" style="width: 80px;" value="{{ $bannerHeight }}" min="50" max="2000">
                            </div>
                            <button type="submit" class="btn btn--primary btn-sm">
                                <i class="las la-save"></i> @lang('Save')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Banner Upload Grid -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-600">@lang('Banner Management') <span class="text-muted fw-normal small">({{ $totalBanners }}/30)</span></h6>
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('admin.frontend.sections.banner') }}" method="GET" class="d-inline">
                            <input type="hidden" name="edit" value="{{ request()->get('edit') }}">
                            <div class="input-group input-group-sm" style="width: 180px;">
                                <input type="text" name="search" class="form-control form-control-sm" value="{{ request()->get('search') }}" placeholder="@lang('Search...')">
                                <button type="submit" class="btn btn--primary btn-sm"><i class="las la-search"></i></button>
                            </div>
                        </form>
                        @if($totalBanners < 30)
                            <form action="{{ route('admin.frontend.sections.banner.addNew') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn--primary btn-sm">
                                    <i class="las la-plus"></i> @lang('Add Banner')
                                </button>
                            </form>
                        @else
                            <span class="badge bg-warning text-dark small">@lang('Max 30')</span>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-0 px-3 pb-3">
                    @if($totalBanners == 0)
                        <div class="text-center py-4 text-muted">
                            <i class="las la-images opacity-50" style="font-size: 2.5rem;"></i>
                            <p class="mt-2 mb-0 small">@lang('No banners yet. Click "Add Banner" to create your first.')</p>
                        </div>
                    @else
                    <div class="banner-upload-grid" id="bannerGrid">
                        @foreach($displayBanners as $i => $banner)
                            @php
                                $bannerId = $banner ? $banner->id : null;
                                $hasImage = false;
                                $imageUrl = null;
                                $imageFile = null;
                                $displayOrder = @$banner->data_values->display_order ?? ($i + 1);
                                
                                if ($banner && isset($banner->data_values->image) && !empty($banner->data_values->image)) {
                                    $imageFile = $banner->data_values->image;
                                    $imageUrl = \App\Services\BannerService::bannerImageUrl($imageFile);
                                    $checkPaths = [
                                        base_path('../assets/images/frontend/banner/desktop/' . $imageFile),
                                        base_path('../assets/images/frontend/banner/' . $imageFile),
                                    ];
                                    foreach ($checkPaths as $p) {
                                        if (file_exists($p) && is_file($p)) {
                                            $hasImage = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <div class="banner-upload-cell" 
                                 data-cell-index="{{ $i + 1 }}" 
                                 data-banner-id="{{ $bannerId }}"
                                 id="bannerCell{{ $bannerId ?? $i }}">
                                @if($hasImage && $imageUrl && $bannerId)
                                    <!-- Banner Uploaded - Show Image -->
                                    <div class="banner-cell-content uploaded">
                                        <div class="banner-cell-image-container">
                                            <img src="{{ $imageUrl }}" alt="Banner {{ $displayOrder }}" class="banner-cell-img" onerror="this.style.display='none'; var err=this.nextElementSibling; if(err) err.classList.remove('d-none');">
                                            <div class="banner-cell-image-error text-center p-4 d-none" style="position:absolute;top:0;left:0;right:0;bottom:0;background:#f8f9fa;display:flex;align-items:center;justify-content:center;flex-direction:column;z-index:1;">
                                                <i class="las la-image" style="font-size:48px;color:#ccc;"></i>
                                                <small class="mt-2">Image not found</small>
                                            </div>
                                            <div class="banner-cell-overlay" style="z-index:2;">
                                                <div class="banner-cell-order-badge">
                                                    <strong>#{{ $displayOrder }}</strong>
                                                </div>
                                                <div class="banner-cell-actions">
                                                    <button type="button" class="btn btn-sm {{ (int)(@$banner->data_values->is_active ?? 1) === 1 ? 'btn--warning' : 'btn--success' }} banner-toggle-status" data-banner-id="{{ $bannerId }}" data-current="{{ (int)(@$banner->data_values->is_active ?? 1) }}" title="@lang('Enable/Disable')">
                                                        <i class="las {{ (int)(@$banner->data_values->is_active ?? 1) === 1 ? 'la-eye-slash' : 'la-eye' }}"></i>
                                                    </button>
                                                    <label class="btn btn-sm btn--success mb-0 banner-replace-upload" title="@lang('Upload / Replace')">
                                                        <i class="las la-cloud-upload-alt"></i>
                                                        <input type="file" class="d-none banner-replace-input" 
                                                               data-banner-id="{{ $bannerId }}"
                                                               accept=".png, .jpg, .jpeg, .webp">
                                                    </label>
                                                    <a href="{{ route('admin.frontend.sections.banner') }}?edit={{ $bannerId }}{{ request()->get('search') ? '&search='.urlencode(request()->get('search')) : '' }}" 
                                                       class="btn btn-sm btn--primary" 
                                                       title="@lang('Edit')">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.frontend.sections.banner.preview', $bannerId) }}" target="_blank" class="btn btn-sm btn--dark" title="@lang('Live Preview')">
                                                        <i class="las la-external-link-alt"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.frontend.sections.banner.duplicate', $bannerId) }}" class="d-inline banner-duplicate-form" data-confirm="{{ __('Duplicate this banner?') }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn--info" title="@lang('Duplicate')">
                                                            <i class="las la-copy"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.frontend.remove', $bannerId) }}" class="d-inline banner-delete-form" data-confirm="{{ __('Are you sure to remove this banner?') }}">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn--danger" title="@lang('Remove')">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="banner-cell-footer">
                                            <div class="banner-cell-footer-inner">
                                                <div class="banner-order-control">
                                                    <label class="banner-order-label">@lang('Position')</label>
                                                    <input type="number"
                                                           class="banner-order-input form-control form-control-sm"
                                                           value="{{ $displayOrder }}"
                                                           min="1" max="30"
                                                           data-banner-id="{{ $bannerId }}"
                                                           title="@lang('1 = first on public page, 30 = last')">
                                                    <button type="button" class="btn btn-sm btn--success save-order-btn" data-banner-id="{{ $bannerId }}" style="display: none;"><i class="las la-check"></i></button>
                                                </div>
                                                <div class="banner-effect-control">
                                                    <label class="banner-effect-label">@lang('Effect'):</label>
                                                    <select class="form-control form-control-sm banner-effect-select" data-banner-id="{{ $bannerId }}" title="@lang('Transition effect on homepage')">
                                                        <option value="none" {{ (@$banner->data_values->animation_type ?? 'none') == 'none' ? 'selected' : '' }}>@lang('None')</option>
                                                        <option value="fadeIn" {{ (@$banner->data_values->animation_type ?? '') == 'fadeIn' ? 'selected' : '' }}>@lang('Fade In')</option>
                                                        <option value="slideInLeft" {{ (@$banner->data_values->animation_type ?? '') == 'slideInLeft' ? 'selected' : '' }}>@lang('Slide L')</option>
                                                        <option value="slideInRight" {{ (@$banner->data_values->animation_type ?? '') == 'slideInRight' ? 'selected' : '' }}>@lang('Slide R')</option>
                                                        <option value="zoomIn" {{ (@$banner->data_values->animation_type ?? '') == 'zoomIn' ? 'selected' : '' }}>@lang('Zoom')</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Banner Not Uploaded - Show Upload Form (or re-upload when image file missing) -->
                                    <div class="banner-cell-content empty">
                                        <form action="{{ route('admin.frontend.sections.content.banner') }}" 
                                              method="POST" 
                                              enctype="multipart/form-data" 
                                              class="banner-upload-form"
                                              data-cell-index="{{ $i }}">
                                            @csrf
                                            <input type="hidden" name="type" value="element">
                                            @if($bannerId)
                                            <input type="hidden" name="id" value="{{ $bannerId }}">
                                            @if($hasImage)
                                            <input type="hidden" name="has_image" value="1">
                                            @endif
                                            @endif
                                            <input type="hidden" name="display_order" value="{{ $displayOrder }}">
                                            <input type="hidden" name="animation_type" value="{{ $banner && isset($banner->data_values->animation_type) ? $banner->data_values->animation_type : 'none' }}">
                                            
                                            <div class="banner-upload-placeholder">
                                                <div class="upload-icon"><i class="las la-cloud-upload-alt"></i></div>
                                                <strong class="upload-title">@lang('Banner') {{ $displayOrder }}</strong>
                                                <div class="upload-specs">
                                                    <span><strong>@lang('Desktop'):</strong> 2560 × 400 px</span>
                                                    <span><strong>@lang('Mobile'):</strong> 1080 × 900 px</span>
                                                    <span><strong>@lang('Format'):</strong> JPG, JPEG, PNG, WEBP, MP4</span>
                                                    <span class="text-muted">@lang('Max'): 5MB · @lang('Thumbnail auto-generated')</span>
                                                </div>
                                                <div class="upload-input-wrapper">
                                                    <input type="file" class="banner-file-input" name="image_input[image]"
                                                           id="bannerUpload{{ $bannerId ?? $i }}" accept=".png, .jpg, .jpeg, .webp" required>
                                                    <label for="bannerUpload{{ $bannerId ?? $i }}" class="btn btn-sm btn--primary">
                                                        <i class="las la-upload"></i> @lang('Upload Banner')
                                                    </label>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    @if($totalBanners < 30)
                    <div class="mt-3 pt-2 border-top text-center">
                        <form action="{{ route('admin.frontend.sections.banner.addNew') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn--primary btn-sm"><i class="las la-plus"></i> @lang('Add New Banner')</button>
                        </form>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Edit Banner Form (only shown when editing) -->
        @if(@$data && request()->get('edit'))
            <div class="col-12 mb-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-600">@lang('Edit Banner') #{{ @$data->data_values->display_order ?? $data->id }}</h6>
                        <div class="d-flex gap-1">
                            @if(!empty($data->data_values->image ?? null))
                            <a href="{{ route('admin.frontend.sections.banner.preview', $data->id) }}" target="_blank" class="btn btn-sm btn--dark"><i class="las la-external-link-alt"></i></a>
                            @endif
                            <a href="{{ route('admin.frontend.sections.banner') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-times"></i></a>
                        </div>
                    </div>
                    <div class="card-body pt-0 px-3 pb-3">
                        <form action="{{ route('admin.frontend.sections.content.banner') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="element">
                            <input type="hidden" name="id" value="{{ $data->id }}">
                            
                            <div class="row g-2">
                                <!-- Banner Image -->
                                <div class="col-12">
                                    <input type="hidden" name="has_image" value="1">
                                    <div class="image-upload d-flex flex-wrap align-items-start gap-3">
                                        @php
                                            $previewImg = \App\Services\BannerService::thumbImageUrl(@$data->data_values->image ?? '');
                                            if ($previewImg === '') {
                                                $previewImg = \App\Services\BannerService::bannerImageUrl(@$data->data_values->image ?? '');
                                            }
                                        @endphp
                                        <div class="profilePicPreview banner-upload-preview flex-shrink-0 position-relative" style="width: 200px; height: 80px; background-size: contain; background-repeat: no-repeat; background-position: center; background-image: url({{ $previewImg ?: asset('assets/images/default.png') }}); border: 1px solid #dee2e6; border-radius: 6px;">
                                            <button type="button" class="remove-image btn btn-sm btn-danger position-absolute" style="top: 4px; right: 4px; padding: 0 6px;"><i class="fa fa-times"></i></button>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="form-label small mb-1">@lang('Banner Image')</label>
                                            <input type="file" class="profilePicUpload form-control form-control-sm" name="image_input[image]" id="profilePicUploadEdit" accept=".png, .jpg, .jpeg, .webp">
                                            <small class="text-muted">JPG, PNG, WEBP · 5MB · 2560×400 (@lang('recommended'))</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Schedule & Visibility -->
                                <div class="col-12"><hr class="my-2"><strong class="text-muted small">@lang('Schedule & Visibility')</strong></div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small mb-0">@lang('Start date')</label>
                                    <input type="date" class="form-control form-control-sm" name="start_date" value="{{ @$data->data_values->start_date }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small mb-0">@lang('End date')</label>
                                    <input type="date" class="form-control form-control-sm" name="end_date" value="{{ @$data->data_values->end_date }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small mb-0">@lang('Visibility')</label>
                                    <select class="form-select form-select-sm" name="visibility">
                                        <option value="public" {{ (@$data->data_values->visibility ?? 'public') == 'public' ? 'selected' : '' }}>@lang('Public')</option>
                                        <option value="logged_in_only" {{ (@$data->data_values->visibility ?? '') == 'logged_in_only' ? 'selected' : '' }}>@lang('Logged in only')</option>
                                        <option value="guest_only" {{ (@$data->data_values->visibility ?? '') == 'guest_only' ? 'selected' : '' }}>@lang('Guest only')</option>
                                        <option value="campaign_only" {{ (@$data->data_values->visibility ?? '') == 'campaign_only' ? 'selected' : '' }}>@lang('Campaign only')</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label small mb-0">@lang('Status')</label>
                                    <select class="form-select form-select-sm" name="is_active">
                                        <option value="1" {{ (int)(@$data->data_values->is_active ?? 1) === 1 ? 'selected' : '' }}>@lang('Enabled')</option>
                                        <option value="0" {{ (int)(@$data->data_values->is_active ?? 1) === 0 ? 'selected' : '' }}>@lang('Disabled')</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">@lang('Layout')</label>
                                    <select class="form-select form-select-sm" name="layout_type">
                                        <option value="hero_full_width" {{ (@$data->data_values->layout_type ?? 'hero_full_width') == 'hero_full_width' ? 'selected' : '' }}>@lang('Hero Full Width')</option>
                                        <option value="centered_content" {{ (@$data->data_values->layout_type ?? '') == 'centered_content' ? 'selected' : '' }}>@lang('Centered Content')</option>
                                        <option value="left_content" {{ (@$data->data_values->layout_type ?? '') == 'left_content' ? 'selected' : '' }}>@lang('Left Content')</option>
                                        <option value="right_content" {{ (@$data->data_values->layout_type ?? '') == 'right_content' ? 'selected' : '' }}>@lang('Right Content')</option>
                                        <option value="split_banner" {{ (@$data->data_values->layout_type ?? '') == 'split_banner' ? 'selected' : '' }}>@lang('Split Banner')</option>
                                        <option value="image_only" {{ (@$data->data_values->layout_type ?? '') == 'image_only' ? 'selected' : '' }}>@lang('Image Only')</option>
                                        <option value="video_banner" {{ (@$data->data_values->layout_type ?? '') == 'video_banner' ? 'selected' : '' }}>@lang('Video Banner')</option>
                                    </select>
                                </div>

                                <!-- Text Overlay (Content Builder) -->
                                <div class="col-12"><hr><strong class="text-muted small">@lang('Text Overlay')</strong></div>
                                @php $bc = @$data->data_values->banner_content ?? (object)[]; @endphp
                                <div class="col-md-6">
                                    <label class="form-label small">@lang('Title')</label>
                                    <input type="text" class="form-control form-control-sm" name="banner_title" value="{{ $bc->title ?? '' }}" placeholder="@lang('Headline')">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">@lang('Subtitle')</label>
                                    <input type="text" class="form-control form-control-sm" name="banner_subtitle" value="{{ $bc->subtitle ?? '' }}" placeholder="@lang('Subheadline')">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small">@lang('Description')</label>
                                    <textarea class="form-control form-control-sm" name="banner_description" rows="2" placeholder="@lang('Short description')">{{ $bc->description ?? '' }}</textarea>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">@lang('Badge')</label>
                                    <input type="text" class="form-control form-control-sm" name="banner_badge" value="{{ $bc->badge ?? '' }}" placeholder="@lang('e.g. NEW')">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">@lang('Button text')</label>
                                    <input type="text" class="form-control form-control-sm" name="banner_button_text" value="{{ $bc->button_text ?? '' }}" placeholder="@lang('Shop Now')">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small">@lang('Button URL')</label>
                                    <input type="url" class="form-control form-control-sm" name="banner_button_url" value="{{ $bc->button_url ?? '' }}" placeholder="https://">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">@lang('Overlay color')</label>
                                    <input type="text" class="form-control form-control-sm" name="banner_overlay_color" value="{{ $bc->overlay_color ?? 'rgba(0,0,0,0.3)' }}" placeholder="rgba(0,0,0,0.3)">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">@lang('Text color')</label>
                                    <input type="text" class="form-control form-control-sm" name="banner_text_color" value="{{ $bc->text_color ?? '#ffffff' }}" placeholder="#ffffff">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">@lang('Title align')</label>
                                    <select class="form-select form-select-sm" name="banner_title_align">
                                        <option value="left" {{ (@$bc->title_align ?? 'center') == 'left' ? 'selected' : '' }}>@lang('Left')</option>
                                        <option value="center" {{ (@$bc->title_align ?? 'center') == 'center' ? 'selected' : '' }}>@lang('Center')</option>
                                        <option value="right" {{ (@$bc->title_align ?? '') == 'right' ? 'selected' : '' }}>@lang('Right')</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">@lang('Title font size')</label>
                                    <input type="text" class="form-control form-control-sm" name="banner_title_font_size" value="{{ $bc->title_font_size ?? '' }}" placeholder="e.g. 2rem, 32px">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">@lang('Title font weight')</label>
                                    <select class="form-select form-select-sm" name="banner_title_font_weight">
                                        <option value="400" {{ (@$bc->title_font_weight ?? '700') == '400' ? 'selected' : '' }}>400</option>
                                        <option value="600" {{ (@$bc->title_font_weight ?? '') == '600' ? 'selected' : '' }}>600</option>
                                        <option value="700" {{ (@$bc->title_font_weight ?? '700') == '700' ? 'selected' : '' }}>700</option>
                                        <option value="800" {{ (@$bc->title_font_weight ?? '') == '800' ? 'selected' : '' }}>800</option>
                                    </select>
                                </div>

                                <!-- Banner Size (Width x Height) -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Banner Width') <small class="text-muted">(px)</small></label>
                                        <input type="number" class="form-control" name="banner_width" value="{{ @$data->data_values->banner_width ?? '2560' }}" min="100" max="4000" placeholder="2560">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Banner Height') <small class="text-muted">(px)</small></label>
                                        <input type="number" class="form-control" name="banner_height" value="{{ @$data->data_values->banner_height ?? '400' }}" min="50" max="2000" placeholder="400">
                                    </div>
                                </div>

                                <!-- URL Field -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Banner URL') <small class="text-muted">(@lang('Optional'))</small></label>
                                        <input type="url" class="form-control" name="url" value="{{ @$data->data_values->url }}" placeholder="https://example.com">
                                    </div>
                                </div>

                                <!-- Display Order Field -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Display Order')</label>
                                        <input type="number" class="form-control" name="display_order" value="{{ @$data->data_values->display_order ?? '' }}" min="1" max="30">
                                    </div>
                                </div>

                                <!-- Animation Type -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>@lang('Animation Type') <span class="text-danger">*</span></label>
                                        <select class="form-control" name="animation_type" required>
                                            <option value="none" {{ (@$data->data_values->animation_type ?? 'none') == 'none' ? 'selected' : '' }}>@lang('None')</option>
                                            <option value="fadeIn" {{ (@$data->data_values->animation_type ?? '') == 'fadeIn' ? 'selected' : '' }}>@lang('Fade In')</option>
                                            <option value="slideInLeft" {{ (@$data->data_values->animation_type ?? '') == 'slideInLeft' ? 'selected' : '' }}>@lang('Slide In Left')</option>
                                            <option value="slideInRight" {{ (@$data->data_values->animation_type ?? '') == 'slideInRight' ? 'selected' : '' }}>@lang('Slide In Right')</option>
                                            <option value="slideInUp" {{ (@$data->data_values->animation_type ?? '') == 'slideInUp' ? 'selected' : '' }}>@lang('Slide In Up')</option>
                                            <option value="slideInDown" {{ (@$data->data_values->animation_type ?? '') == 'slideInDown' ? 'selected' : '' }}>@lang('Slide In Down')</option>
                                            <option value="zoomIn" {{ (@$data->data_values->animation_type ?? '') == 'zoomIn' ? 'selected' : '' }}>@lang('Zoom In')</option>
                                            <option value="rotateIn" {{ (@$data->data_values->animation_type ?? '') == 'rotateIn' ? 'selected' : '' }}>@lang('Rotate In')</option>
                                            <option value="bounceIn" {{ (@$data->data_values->animation_type ?? '') == 'bounceIn' ? 'selected' : '' }}>@lang('Bounce In')</option>
                                            <option value="flipInX" {{ (@$data->data_values->animation_type ?? '') == 'flipInX' ? 'selected' : '' }}>@lang('Flip In X')</option>
                                            <option value="flipInY" {{ (@$data->data_values->animation_type ?? '') == 'flipInY' ? 'selected' : '' }}>@lang('Flip In Y')</option>
                                            <option value="lightSpeedIn" {{ (@$data->data_values->animation_type ?? '') == 'lightSpeedIn' ? 'selected' : '' }}>@lang('Light Speed In')</option>
                                            <option value="rollIn" {{ (@$data->data_values->animation_type ?? '') == 'rollIn' ? 'selected' : '' }}>@lang('Roll In')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn--primary btn-sm">
                                    <i class="las la-save"></i> @lang('Update Banner')
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <x-confirmation-modal />
@endsection

@push('style')
<style>
    /* Compact top bar */
    .banner-top-bar .card-body { min-height: auto; }
    .banner-stat-item { line-height: 1.2; }
    .banner-slider-form .form-label { font-size: 11px; }

    /* Grid: compact 3 columns */
    .banner-upload-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        max-width: 100%;
        width: 100%;
    }

    .banner-upload-cell {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .banner-upload-cell:hover {
        border-color: var(--primary, #6366f1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .banner-upload-cell.uploading { pointer-events: none; opacity: 0.85; }

    .banner-upload-loading-overlay {
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.95);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 8px;
    }

    .banner-cell-content.uploaded { display: flex; flex-direction: column; height: 100%; }

    .banner-cell-image-container {
        position: relative;
        width: 100%;
        aspect-ratio: 2560 / 400;
        overflow: hidden;
        background: #f8f9fa;
    }

    .banner-cell-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
    }

    .banner-cell-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 6px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .banner-upload-cell:hover .banner-cell-overlay { opacity: 1; }

    .banner-cell-order-badge {
        background: rgba(255,255,255,0.95);
        color: #333;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
        align-self: flex-start;
    }

    .banner-cell-actions {
        display: flex;
        gap: 3px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .banner-cell-actions .btn { padding: 0.2rem 0.4rem; font-size: 12px; }
    .banner-cell-actions .banner-replace-upload { cursor: pointer; margin: 0; }

    .banner-cell-footer {
        padding: 5px 6px;
        background: #f8f9fa;
        border-top: 1px solid #eee;
        flex-shrink: 0;
    }

    .banner-cell-footer-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        flex-wrap: wrap;
    }

    .banner-order-control, .banner-effect-control { display: flex; align-items: center; gap: 4px; }
    .banner-order-label, .banner-effect-label { margin: 0; font-size: 10px; font-weight: 600; color: #555; white-space: nowrap; }
    .banner-order-input { width: 38px !important; padding: 2px 4px; font-size: 10px; text-align: center; }
    .banner-effect-select { min-width: 72px; padding: 2px 4px; font-size: 10px; }

    .banner-cell-content.empty {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        min-height: 120px;
    }

    .banner-upload-form { width: 100%; height: 100%; }
    .banner-upload-placeholder { text-align: center; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px; }
    .upload-icon { font-size: 22px; color: var(--primary, #6366f1); }
    .upload-title { font-size: 12px; color: #333; }
    .upload-specs { font-size: 9px; color: #666; line-height: 1.35; }
    .upload-specs span { display: block; }
    .upload-input-wrapper { margin-top: 4px; }
    .banner-file-input { display: none; }

    @media (max-width: 992px) {
        .banner-upload-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    }
    @media (max-width: 576px) {
        .banner-upload-grid { grid-template-columns: 1fr; }
        .banner-top-bar .d-flex.gap-4 { gap: 1rem !important; }
    }

    #confirmationModal .modal-dialog { z-index: 1060 !important; }
</style>
@endpush

@push('script')
<script>
    (function ($) {
        "use strict";
        
        // Scroll to banner grid when returning from Add New Banner
        @if(session('scroll_to_banner'))
        $(function() {
            var $grid = $('#bannerGrid');
            if ($grid.length) {
                $('html, body').animate({ scrollTop: $grid.offset().top - 80 }, 400);
            }
        });
        @endif
        
        // Auto-submit form when file is selected
        $(document).on('change', '.banner-file-input', function() {
            const form = $(this).closest('.banner-upload-form');
            const cell = $(this).closest('.banner-upload-cell');
            const cellIndex = form.data('cell-index');
            
            if (this.files && this.files[0]) {
                // Validate file size (5MB max)
                const fileSize = this.files[0].size / 1024 / 1024; // Size in MB
                if (fileSize > 5) {
                    notify('error', 'File size must be less than 5MB');
                    $(this).val('');
                    return;
                }
                
                // Validate file type
                const fileName = this.files[0].name.toLowerCase();
                const validExtensions = ['.jpg', '.jpeg', '.png', '.webp'];
                const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
                
                if (!hasValidExtension) {
                    notify('error', 'Invalid file type. Please upload JPG, PNG, or WEBP files only.');
                    $(this).val('');
                    return;
                }
                
                // Show loading overlay (do NOT replace cell content - form must stay in DOM to submit)
                const loadingOverlay = $('<div class="banner-upload-loading-overlay"><div class="text-center p-4"><i class="las la-spinner la-spin" style="font-size: 32px; color: #4FC4F7;"></i><br><small class="mt-2 d-block">Uploading banner...</small><button type="button" class="btn btn-sm btn--danger mt-2 cancel-upload-btn"><i class="las la-times"></i> Cancel</button></div></div>');
                loadingOverlay.find('.cancel-upload-btn').on('click', function() { location.reload(); });
                cell.addClass('uploading').css('position', 'relative').append(loadingOverlay);
                
                // Submit form (form must still be in DOM)
                form[0].submit();
            }
        });
        
        // Update display order via AJAX when input changes
        $(document).on('input', '.banner-order-input', function() {
            const saveBtn = $(this).siblings('.save-order-btn');
            saveBtn.show();
        });
        
        $(document).on('click', '.save-order-btn', function() {
            const bannerId = $(this).data('banner-id');
            const input = $(this).siblings('.banner-order-input');
            const newOrder = input.val();
            
            if (!bannerId || !newOrder || newOrder < 1 || newOrder > 30) {
                notify('error', 'Please enter a valid order number (1–30). Position 1 = first on public page.');
                return;
            }
            
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i>');
            
            $.ajax({
                url: '{{ route("admin.frontend.banner.updateOrder") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: bannerId,
                    display_order: parseInt(newOrder)
                },
                success: function(response) {
                    if (response.success) {
                        notify('success', 'Banner order updated successfully');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        notify('error', response.message || 'Failed to update order');
                        btn.prop('disabled', false).html('<i class="las la-check"></i>');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to update display order';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    notify('error', errorMsg);
                    btn.prop('disabled', false).html('<i class="las la-check"></i>');
                }
            });
        });
        
        // Image preview for edit form
        $('.profilePicUpload').on('change', function() {
            const input = this;
            const preview = $(this).closest('.image-upload').find('.profilePicPreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.css('background-image', 'url(' + e.target.result + ')');
                    preview.css('background-size', 'contain');
                    preview.css('background-repeat', 'no-repeat');
                    preview.css('background-position', 'center');
                };
                reader.readAsDataURL(input.files[0]);
            }
        });
        
        // Remove image
        $('.remove-image').on('click', function() {
            const preview = $(this).closest('.profilePicPreview');
            const input = preview.closest('.image-upload').find('.profilePicUpload');
            preview.css('background-image', 'none');
            input.val('');
        });
        
        // Delete banner: confirm before submit
        $(document).on('submit', '.banner-delete-form', function(e) {
            var msg = $(this).data('confirm') || 'Are you sure to remove this banner?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
        // Duplicate banner: confirm before submit
        $(document).on('submit', '.banner-duplicate-form', function(e) {
            var msg = $(this).data('confirm') || 'Duplicate this banner?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
        // Quick toggle Enable/Disable (is_active)
        $(document).on('click', '.banner-toggle-status', function() {
            var btn = $(this);
            var bannerId = btn.data('banner-id');
            var current = parseInt(btn.data('current'), 10);
            var newVal = current === 1 ? 0 : 1;
            if (!bannerId) return;
            btn.prop('disabled', true);
            $.ajax({
                url: '{{ route("admin.frontend.sections.banner.updateField") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', id: bannerId, field: 'is_active', value: newVal },
                success: function(response) {
                    if (response.success) {
                        btn.data('current', newVal);
                        btn.removeClass('btn--warning btn--success').addClass(newVal === 1 ? 'btn--warning' : 'btn--success');
                        btn.find('i').removeClass('la-eye la-eye-slash').addClass(newVal === 1 ? 'la-eye-slash' : 'la-eye');
                        notify('success', newVal === 1 ? 'Banner enabled.' : 'Banner disabled.');
                    } else {
                        notify('error', response.message || 'Failed');
                    }
                },
                error: function() { notify('error', 'Failed to update status.'); },
                complete: function() { btn.prop('disabled', false); }
            });
        });
        
        // Effect dropdown: update animation_type via AJAX
        $(document).on('change', '.banner-effect-select', function() {
            var bannerId = $(this).data('banner-id');
            var value = $(this).val();
            if (!bannerId || !value) return;
            var $sel = $(this);
            $sel.prop('disabled', true);
            $.ajax({
                url: '{{ route("admin.frontend.sections.banner.updateField") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', id: bannerId, field: 'animation_type', value: value },
                success: function(response) {
                    if (response.success) {
                        notify('success', 'Effect updated.');
                    } else {
                        notify('error', response.message || 'Failed');
                        $sel.prop('disabled', false);
                    }
                },
                error: function() {
                    notify('error', 'Failed to update effect.');
                    $sel.prop('disabled', false);
                },
                complete: function() { $sel.prop('disabled', false); }
            });
        });

        // Replace banner image (upload on card that already has image)
        $(document).on('change', '.banner-replace-input', function() {
            const input = this;
            const bannerId = $(this).data('banner-id');
            if (!this.files || !this.files[0] || !bannerId) return;
            const file = this.files[0];
            if (file.size / 1024 / 1024 > 5) {
                notify('error', 'File size must be less than 5MB');
                $(input).val('');
                return;
            }
            const ext = (file.name.split('.').pop() || '').toLowerCase();
            if (!['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
                notify('error', 'Invalid file type. Use JPG, PNG or WEBP.');
                $(input).val('');
                return;
            }
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('type', 'element');
            formData.append('id', bannerId);
            formData.append('has_image', '1');
            formData.append('image_input[image]', file);
            $.ajax({
                url: '{{ route("admin.frontend.sections.content.banner") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    notify('success', 'Banner image updated.');
                    setTimeout(function() { location.reload(); }, 800);
                },
                error: function() {
                    notify('error', 'Failed to update banner image.');
                    $(input).val('');
                }
            });
        });
    })(jQuery);
    
    function addNewBannerSlot() {
        window.location.href = '{{ route("admin.frontend.sections.banner.addNew") }}';
    }
</script>
@endpush
