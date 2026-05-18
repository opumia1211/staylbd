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
    <div class="row g-3">
        @if($totalBanners < 30)
            <div class="col-12 mb-1">
                <div class="card border-0 shadow-sm border-start border-4 border--primary">
                    <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h5 class="mb-0 fw-bold fs-6">@lang('Hero Banner Manager')</h5>
                            <p class="small text-muted mb-0" style="font-size: 11px;">@lang('Upload and manage homepage slides. Desktop: 1920x400, Mobile: 1024x1024.')</p>
                        </div>
                        <div class="d-flex gap-2">
                             <form action="{{ route('admin.frontend.sections.banner.addNew') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn--primary btn-sm px-3 rounded-pill shadow-sm">
                                    <i class="las la-plus-circle"></i> @lang('Add New Slide')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 1. MAIN BANNER GRID (Priority #1) -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="mb-0 fw-bold small text-uppercase text-muted">@lang('Live Slideshow') <span class="badge bg-light text-dark ms-1">({{ $totalBanners }}/30)</span></h6>
                    </div>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.frontend.sections.banner') }}" method="GET" class="d-inline">
                            <input type="hidden" name="edit" value="{{ request()->get('edit') }}">
                            <div class="input-group input-group-sm" style="width: 180px;">
                                <input type="text" name="search" class="form-control form-control-sm border-0 bg-light rounded-start shadow-none" value="{{ request()->get('search') }}" placeholder="@lang('Quick search...')">
                                <button type="submit" class="btn btn-light btn-sm border-0 rounded-end"><i class="las la-search text-muted"></i></button>
                            </div>
                        </form>
                        @if($totalBanners < 30)
                            <form action="{{ route('admin.frontend.sections.banner.addNew') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn--primary btn-sm rounded-pill px-3">
                                    <i class="las la-plus"></i> @lang('Add Slide')
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body pt-0 px-3 pb-3">
                    @if($totalBanners == 0)
                        <div class="text-center py-5 text-muted bg-light rounded-4">
                            <i class="las la-images opacity-25 mb-2" style="font-size: 3rem;"></i>
                            <p class="mb-0 fw-semibold text-muted">@lang('No active banners.')</p>
                            <small class="d-block">@lang('Click "Add Slide" to start.')</small>
                        </div>
                    @else
                    <div class="banner-upload-grid" id="bannerGrid">
                        @foreach($displayBanners as $i => $banner)
                            @php
                                $bannerId = $banner ? $banner->id : null;
                                $hasImage = false;
                                $imageUrl = null;
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
                             <div class="banner-upload-cell border rounded-4 position-relative overflow-hidden group shadow-none hover-shadow-sm transition-all bg-white" 
                                  data-banner-id="{{ $bannerId }}"
                                  id="bannerCell{{ $bannerId ?? $i }}">
                                  @if($bannerId && $hasImage)
                                     <div class="banner-cell-content uploaded h-100 w-100 position-relative" style="min-height: 162px;">
                                         <!-- Full Bleed Image -->
                                         @if($hasImage)
                                             <img src="{{ $imageUrl }}" alt="Banner {{ $displayOrder }}" class="w-100 h-100 object-fit-cover position-absolute top-0 start-0">
                                         @else
                                             <div class="w-100 h-100 position-absolute top-0 start-0 d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary">
                                                 <i class="las la-image fs-1"></i>
                                             </div>
                                         @endif

                                         <!-- Small Top Badges (Order and Status Dot) -->
                                         <div class="position-absolute top-0 start-0 p-2 d-flex gap-1.5 align-items-center" style="z-index: 4;">
                                             <span class="badge bg-dark bg-opacity-75">#{{ $displayOrder }}</span>
                                             <div class="dot {{ (int)(@$banner->data_values->is_active ?? 1) === 1 ? 'bg-success' : 'bg-secondary' }}" 
                                                  style="width: 10px; height: 10px; border-radius: 50%; border: 1.5px solid #fff;"
                                                  title="{{ (int)(@$banner->data_values->is_active ?? 1) === 1 ? __('Public') : __('Private') }}"></div>
                                         </div>

                                          <!-- Full Card Hover Overlay -->
                                          <div class="banner-cell-overlay d-flex flex-column align-items-center justify-content-center position-absolute top-0 start-0 w-100 h-100" 
                                               style="background: rgba(0,0,0,0.65); backdrop-filter: blur(3px); z-index: 5;">
                                              
                                              <!-- Floating Action Buttons -->
                                              <div class="d-flex gap-2 p-2 flex-wrap justify-content-center mb-1">
                                                  <!-- Edit & Settings Modal Trigger -->
                                                  <button type="button" 
                                                          class="btn btn-sm btn--primary shadow-sm d-flex align-items-center justify-content-center open-edit-modal" 
                                                          style="width: 36px; height: 36px; border-radius: 8px; background-color: #0d9488 !important; border: none !important; color: #fff !important;" 
                                                          data-id="{{ $bannerId }}"
                                                          data-url="{{ @$banner->data_values->url }}"
                                                          data-title="{{ @$banner->data_values->banner_content->title ?? '' }}"
                                                          data-subtitle="{{ @$banner->data_values->banner_content->subtitle ?? '' }}"
                                                          data-desc="{{ @$banner->data_values->banner_content->description ?? '' }}"
                                                          data-order="{{ $displayOrder }}"
                                                          data-animation="{{ @$banner->data_values->animation_type ?? 'none' }}"
                                                          data-active="{{ (int)(@$banner->data_values->is_active ?? 1) }}"
                                                          title="@lang('Edit Settings & Visibility')">
                                                      <i class="las la-pen fs-5"></i>
                                                  </button>
                                                  
                                                  <!-- Replace Image Trigger -->
                                                  <label for="replaceInput{{ $bannerId }}" 
                                                         class="btn btn-sm btn--info shadow-sm d-flex align-items-center justify-content-center mb-0 cursor-pointer" 
                                                         style="width: 36px; height: 36px; border-radius: 8px; background-color: #0284c7 !important; border: none !important; color: #fff !important; cursor: pointer;" 
                                                         title="@lang('Replace Image')">
                                                     <i class="las la-cloud-upload-alt fs-5"></i>
                                                  </label>
                                                  <input type="file" 
                                                         class="banner-replace-input d-none" 
                                                         id="replaceInput{{ $bannerId }}" 
                                                         data-banner-id="{{ $bannerId }}" 
                                                         accept="image/*">
                                                  
                                                  <!-- Delete / Remove Banner -->
                                                  <form method="POST" action="{{ route('admin.frontend.remove', $bannerId) }}" class="d-inline banner-delete-form">
                                                      @csrf
                                                      <button type="submit" 
                                                              class="btn btn-sm btn--danger shadow-sm d-flex align-items-center justify-content-center" 
                                                              style="width: 36px; height: 36px; border-radius: 8px; background-color: #ef4444 !important; border: none !important; color: #fff !important;" 
                                                              title="@lang('Delete / Remove Banner')">
                                                          <i class="las la-trash fs-5"></i>
                                                      </button>
                                                  </form>
                                              </div>

                                              <span class="text-white x-small opacity-75 mt-1 fw-semibold text-uppercase">@lang('SLIDE') {{ $displayOrder }}</span>
                                          </div>
                                     </div>
                                 @else
                                     <div class="banner-cell-content empty h-100 w-100">
                                         <form action="{{ route('admin.frontend.sections.content.banner') }}" method="POST" enctype="multipart/form-data" class="banner-upload-form h-100 w-100">
                                             @csrf
                                             <input type="hidden" name="type" value="element">
                                             @if($bannerId) <input type="hidden" name="id" value="{{ $bannerId }}"> @endif
                                             <input type="hidden" name="display_order" value="{{ $displayOrder }}">
                                             <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-light py-4 px-3 text-center border-dashed border-2 rounded-4 hover-bg-white transition-all cursor-pointer position-relative" 
                                                  style="min-height: 162px;"
                                                  onclick="if(!event.target.closest('.open-edit-modal') && !event.target.closest('.banner-delete-form')) { document.getElementById('upz{{ $bannerId ?? $i }}').click(); }">
                                                 <input type="file" class="banner-file-input d-none" name="image_input[image]" id="upz{{ $bannerId ?? $i }}" accept="image/*">
                                                 
                                                 <!-- Floating slide number badge on empty card if slot exists -->
                                                 <div class="position-absolute top-0 start-0 p-2" style="z-index: 4;">
                                                     <span class="badge bg-secondary bg-opacity-75">#{{ $displayOrder }}</span>
                                                 </div>

                                                 <!-- Floating Action Buttons for Empty Slot -->
                                                 @if($bannerId)
                                                 <div class="position-absolute top-0 end-0 p-2 d-flex gap-1 align-items-center" style="z-index: 10;">
                                                     <!-- Edit Details -->
                                                     <button type="button" 
                                                             class="btn btn-xs btn--primary shadow-sm d-flex align-items-center justify-content-center open-edit-modal" 
                                                             style="width: 28px; height: 28px; border-radius: 6px; padding: 0;" 
                                                             data-id="{{ $bannerId }}"
                                                             data-url="{{ @$banner->data_values->url }}"
                                                             data-title="{{ @$banner->data_values->banner_content->title ?? '' }}"
                                                             data-subtitle="{{ @$banner->data_values->banner_content->subtitle ?? '' }}"
                                                             data-desc="{{ @$banner->data_values->banner_content->description ?? '' }}"
                                                             data-order="{{ $displayOrder }}"
                                                             data-animation="{{ @$banner->data_values->animation_type ?? 'none' }}"
                                                             data-active="{{ (int)(@$banner->data_values->is_active ?? 1) }}"
                                                             title="@lang('Edit Settings')">
                                                         <i class="las la-pen fs-6"></i>
                                                     </button>
                                                     
                                                     <!-- Delete Slot -->
                                                     <form method="POST" action="{{ route('admin.frontend.remove', $bannerId) }}" class="d-inline banner-delete-form">
                                                         @csrf
                                                         <button type="submit" 
                                                                 class="btn btn-xs btn--danger shadow-sm d-flex align-items-center justify-content-center" 
                                                                 style="width: 28px; height: 28px; border-radius: 6px; padding: 0;" 
                                                                 title="@lang('Delete Slot')">
                                                             <i class="las la-trash fs-6"></i>
                                                         </button>
                                                     </form>
                                                 </div>
                                                 @endif

                                                 <div class="mb-2"><i class="las la-cloud-upload-alt fs-2 text-primary"></i></div>
                                                 <span class="d-block small fw-bold text-dark mb-2">@lang('Upload Banner Slide')</span>
                                                 <div class="d-flex flex-column gap-1 align-items-center mt-1 w-100">
                                                     <span class="badge bg-label-info px-2 py-1 w-100 text-truncate" style="font-size: 10px; font-weight: 700; border-radius: 4px;">@lang('Size: 1920 × 400 px')</span>
                                                     <span class="badge bg-label-secondary px-2 py-1 w-100 text-truncate" style="font-size: 10px; font-weight: 700; border-radius: 4px;">@lang('Formats: JPG, JPEG, PNG, WEBP')</span>
                                                 </div>
                                             </div>
                                         </form>
                                     </div>
                                 @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-2 border-top text-center">
                        <a href="{{ route('admin.frontend.sections.homepageCustomRows.create') }}" class="btn btn-warning btn-sm text-dark fw-semibold">
                            <i class="las la-plus"></i> @lang('Add row promo (large + small)')
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. ANALYTICS & SETTINGS (Clean Footer) -->
        <div class="col-12 mt-3 mb-4">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-muted text-uppercase" style="letter-spacing: 0.5px;">@lang('Slider Engine Configuration')</span>
                            <div class="d-flex align-items-center gap-3 border-start ps-3">
                                <div class="text-center"><span class="text-muted d-block mb-0" style="font-size: 8px;">@lang('VIEW')</span><strong class="text--primary small">{{ number_format($bannerStats['impressions']) }}</strong></div>
                                <div class="text-center"><span class="text-muted d-block mb-0" style="font-size: 8px;">@lang('CLICK')</span><strong class="small">{{ number_format($bannerStats['clicks']) }}</strong></div>
                                <div class="text-center"><span class="text-muted d-block mb-0" style="font-size: 8px;">@lang('CTR')</span><strong class="small">{{ $bannerStats['ctr'] }}%</strong></div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form action="{{ route('admin.frontend.sections.content.banner') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="content">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="small fw-bold text-muted mb-1" style="font-size: 10px;">@lang('Interval')</label>
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="slide_interval_seconds" class="form-control border-end-0" value="{{ $slideInterval }}" min="1">
                                            <span class="input-group-text bg-white border-start-0 text-muted">s</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small fw-bold text-muted mb-1" style="font-size: 10px;">@lang('Autoplay')</label>
                                        <select name="autoplay" class="form-select form-select-sm shadow-none border">
                                            <option value="1" {{ $autoplay == 1 ? 'selected' : '' }}>@lang('Enabled')</option>
                                            <option value="0" {{ $autoplay == 0 ? 'selected' : '' }}>@lang('Disabled')</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small fw-bold text-muted mb-1" style="font-size: 10px;">@lang('Width')</label>
                                        <input type="number" name="banner_width" class="form-control form-control-sm" value="{{ $bannerWidth }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="small fw-bold text-muted mb-1" style="font-size: 10px;">@lang('Height')</label>
                                        <input type="number" name="banner_height" class="form-control form-control-sm" value="{{ $bannerHeight }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn--primary btn-sm w-100 rounded-pill shadow-sm"><i class="las la-save me-1"></i>@lang('Save')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-muted text-uppercase" style="letter-spacing: 0.5px;">@lang('Category Sliders')</span>
                            <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}" class="small text-muted text-decoration-none">@lang('All Rows') &rarr;</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 11px;">
                                    <tbody>
                                        @foreach($homepageProductRows->take(3) as $hpRow)
                                            <tr class="border-bottom border-light">
                                                <td class="ps-3 py-2"><strong>{{ $hpRow->title }}</strong></td>
                                                <td class="text-end pe-3"><a href="{{ route('admin.frontend.sections.homepageCustomRows.edit', $hpRow->id) }}" class="text--primary text-decoration-none fw-600">@lang('UPLOAD')</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Slide Modal -->
        <div class="modal fade" id="editSlideModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header border-bottom py-3 px-4 bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <i class="las la-pen-fancy text-primary fs-4"></i>
                            <h5 class="modal-title fw-bold text-dark fs-5">@lang('Edit Slide Settings & Visibility')</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.frontend.sections.content.banner') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <input type="hidden" name="id" id="modalSlideId" value="">
                        
                        <div class="modal-body py-4 px-4">
                            <div class="row g-4">
                                <!-- LEFT: Media Upload -->
                                <div class="col-md-5 border-end pe-md-4">
                                    <h6 class="small fw-bold text-muted text-uppercase mb-3"><i class="las la-image me-1"></i>@lang('Slide Images')</h6>
                                    
                                    <!-- Desktop Image -->
                                    <div class="mb-3">
                                        <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Desktop Image')</label>
                                        <input type="file" class="form-control form-control-sm" name="image_input[image]" accept="image/*">
                                        <small class="text-muted d-block mt-1" style="font-size: 9px;">@lang('Ideal size: 1920 × 400 px')</small>
                                    </div>
                                    
                                    <!-- Mobile Image -->
                                    <div class="mb-0">
                                        <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Mobile Image')</label>
                                        <input type="file" class="form-control form-control-sm" name="image_input[mobile_image]" accept="image/*">
                                        <small class="text-muted d-block mt-1" style="font-size: 9px;">@lang('Ideal size: 1024 × 1024 px')</small>
                                    </div>
                                </div>
                                
                                <!-- RIGHT: Meta / Text Fields -->
                                <div class="col-md-7 ps-md-4">
                                    <div class="row g-3">
                                        <div class="col-md-9">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Target Redirect URL')</label>
                                            <input type="url" name="url" id="modalSlideUrl" class="form-control form-control-sm" placeholder="https://...">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Position')</label>
                                            <input type="number" name="display_order" id="modalSlideOrder" class="form-control form-control-sm" required>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Title Overlay')</label>
                                            <input type="text" name="banner_title" id="modalSlideTitleOverlay" class="form-control form-control-sm">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Subtitle')</label>
                                            <input type="text" name="banner_subtitle" id="modalSlideSubtitle" class="form-control form-control-sm">
                                        </div>
                                        
                                        <div class="col-12">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Short Description')</label>
                                            <textarea name="banner_description" id="modalSlideDesc" class="form-control form-control-sm" rows="2"></textarea>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Entrance Effect')</label>
                                            <select name="animation_type" id="modalSlideAnimation" class="form-select form-select-sm">
                                                <option value="none">@lang('None')</option>
                                                <option value="fadeIn">@lang('Fade In')</option>
                                                <option value="slideInLeft">@lang('Slide from Left')</option>
                                                <option value="slideInRight">@lang('Slide from Right')</option>
                                                <option value="zoomIn">@lang('Zoom In')</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label class="small fw-bold text-muted mb-1" style="font-size: 11px;">@lang('Visibility Status')</label>
                                            <select name="is_active" id="modalSlideActive" class="form-select form-select-sm">
                                                <option value="1">@lang('Live on Site (Public)')</option>
                                                <option value="0">@lang('Hidden Preview (Private)')</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer border-top py-3 px-4 bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">@lang('Cancel')</button>
                            <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm"><i class="las la-check-circle me-1"></i>@lang('Save Changes')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('style')
<style>
    .banner-upload-grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 1.25rem !important;
    }
    @media (max-width: 1199px) {
        .banner-upload-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 767px) {
        .banner-upload-grid {
            grid-template-columns: 1fr !important;
        }
    }
    .banner-upload-cell {
        min-height: 162px !important;
        border-radius: 12px !important;
    }
    .bg-label-info {
        background-color: #e0f2fe !important;
        color: #0369a1 !important;
    }
    .bg-label-secondary {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
    }
    .hover-bg-white:hover {
        background-color: #ffffff !important;
        border-color: #0e9f90 !important;
    }
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
        $(document).on('click', '.banner-toggle-status', function(e) {
            e.preventDefault();
            var btn = $(this);
            var bannerId = btn.data('banner-id');
            var current = parseInt(btn.data('current'), 10);
            var newVal = current === 1 ? 0 : 1;
            
            if (!bannerId) return;
            
            btn.prop('disabled', true).html('<i class="las la-spinner la-spin fs-5"></i>');
            
            $.ajax({
                url: '{{ route("admin.frontend.sections.banner.updateField") }}',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}', id: bannerId, field: 'is_active', value: newVal },
                success: function(response) {
                    if (response.success) {
                        notify('success', newVal === 1 ? 'Banner is now PUBLIC' : 'Banner is now PRIVATE');
                        setTimeout(function() { location.reload(); }, 800);
                    } else {
                        notify('error', response.message || 'Failed');
                        btn.prop('disabled', false).html('<i class="las ' + (current === 1 ? 'la-eye-slash' : 'la-eye') + ' fs-5"></i>');
                    }
                },
                error: function() { 
                    notify('error', 'Failed to update status.'); 
                    btn.prop('disabled', false).html('<i class="las ' + (current === 1 ? 'la-eye-slash' : 'la-eye') + ' fs-5"></i>');
                }
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

        // Populate and open edit modal when pencil is clicked
        $(document).on('click', '.open-edit-modal', function(e) {
            e.preventDefault();
            const btn = $(this);
            const id = btn.data('id');
            const url = btn.data('url');
            const title = btn.data('title');
            const subtitle = btn.data('subtitle');
            const desc = btn.data('desc');
            const order = btn.data('order');
            const animation = btn.data('animation');
            const active = btn.data('active');
            
            $('#modalSlideId').val(id);
            $('#modalSlideUrl').val(url);
            $('#modalSlideOrder').val(order);
            $('#modalSlideTitleOverlay').val(title);
            $('#modalSlideSubtitle').val(subtitle);
            $('#modalSlideDesc').val(desc);
            $('#modalSlideAnimation').val(animation).change();
            $('#modalSlideActive').val(active).change();
            
            const myModal = new bootstrap.Modal(document.getElementById('editSlideModal'));
            myModal.show();
        });
    })(jQuery);
    
    function addNewBannerSlot() {
        window.location.href = '{{ route("admin.frontend.sections.banner.addNew") }}';
    }
</script>

<style>
    .banner-upload-cell .banner-cell-overlay {
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease-in-out;
        backdrop-filter: blur(2px);
    }
    .banner-upload-cell:hover .banner-cell-overlay {
        opacity: 1;
        visibility: visible;
    }
    .banner-cell-overlay .btn {
        transform: scale(0.8);
        transition: all 0.2s ease;
    }
    .banner-upload-cell:hover .banner-cell-overlay .btn {
        transform: scale(1);
    }
    .banner-cell-overlay .btn:hover {
        transform: scale(1.1) !important;
    }
</style>
@endpush
