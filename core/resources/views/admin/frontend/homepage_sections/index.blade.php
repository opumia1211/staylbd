@extends('admin.layouts.app')
@section('panel')
<div class="hp-hub">
    {{-- Page header --}}
    <div class="card border-0 shadow-sm mb-3 overflow-hidden">
        <div class="hp-hub__hero d-flex flex-wrap align-items-start justify-content-between gap-3 p-3 p-md-4">
            <div>
                <h1 class="hp-hub__title h5 mb-1 fw-semibold">{!! lang_en_bn('Homepage Sections') !!}</h1>
                <p class="text-muted small mb-0 hp-hub__lead">{!! lang_en_bn('Power Zone, trust, promos, sliders — one place.') !!}</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('admin.frontend.sections.homepageCustomRows') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="las la-stream me-1"></i>@lang('Layout & rows')
                </a>
                <a href="{{ route('admin.category.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="las la-border-all me-1"></i>{!! lang_en_bn('Categories') !!}
                </a>
                <button type="button" class="btn btn-sm rounded-pill px-3 hp-help-btn" data-bs-toggle="collapse" data-bs-target="#hpReference" aria-expanded="false">
                    <i class="las la-info-circle me-1 hp-help-btn__icon"></i><span class="hp-help-btn__text">{!! lang_en_bn('How rows work') !!}</span>
                </button>
            </div>
        </div>
        <div class="collapse border-top border-light" id="hpReference">
            <div class="p-3 p-md-4 bg-light bg-opacity-50">
                <p class="small text-muted mb-3 mb-md-2">{!! lang_en_bn('Product rows on home (managed in Products)') !!}</p>
                <div class="row g-2 g-md-3">
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="hp-ref-tile h-100"><strong>Quick Deals</strong><span class="hp-ref-tile__hint">{!! lang_en_bn('Today Deal on product') !!}</span></div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="hp-ref-tile h-100"><strong>Hot Deals</strong><span class="hp-ref-tile__hint">{!! lang_en_bn('Hot Deal flag') !!}</span></div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="hp-ref-tile h-100"><strong>Featured</strong><span class="hp-ref-tile__hint">{!! lang_en_bn('Featured on product') !!}</span></div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="hp-ref-tile h-100"><strong>New Arrivals</strong><span class="hp-ref-tile__hint">{!! lang_en_bn('Auto list') !!}</span></div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="hp-ref-tile h-100"><strong>Trending</strong><span class="hp-ref-tile__hint">{!! lang_en_bn('Trending + sales') !!}</span></div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="hp-ref-tile h-100"><strong>Best / Recommended</strong><span class="hp-ref-tile__hint">{!! lang_en_bn('sale_count from orders') !!}</span></div>
                    </div>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="hp-ref-tile h-100"><strong>Category</strong><span class="hp-ref-tile__hint">{!! lang_en_bn('Active + image') !!}</span></div>
                    </div>
                </div>
                <p class="small text-muted mb-0 mt-3"><i class="las la-clock me-1"></i>{!! lang_en_bn('After changing products, home updates within ~10 minutes (cache) or immediately on next save from admin.') !!}</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm hp-hub__main">
        <div class="hp-hub__tabbar px-2 px-md-3 pt-2 pt-md-3">
            <ul class="nav nav-tabs hp-hub__tabs flex-nowrap overflow-auto border-0" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-settings" role="tab"><i class="las la-sliders-h me-1"></i><span>{!! lang_en_bn('General') !!}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-trust" role="tab"><i class="las la-shield-alt me-1"></i><span>{!! lang_en_bn('Trust') !!}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-quick-service" role="tab"><i class="las la-concierge-bell me-1"></i><span>{!! lang_en_bn('Shortcuts') !!}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-promo" role="tab"><i class="las la-bullhorn me-1"></i><span>{!! lang_en_bn('Promo') !!}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-quick-category" role="tab"><i class="las la-th-large me-1"></i><span>{!! lang_en_bn('Quick boxes') !!}</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-product-slider" role="tab"><i class="las la-arrows-alt-h me-1"></i><span>{!! lang_en_bn('Sliders') !!}</span></a>
                </li>
            </ul>
        </div>

        <div class="card-body p-3 p-md-4 pt-2">
            <div class="tab-content hp-tab-content">
                {{-- General --}}
                <div class="tab-pane fade show active" id="tab-settings" role="tabpanel">
                    <form method="POST" action="{{ route('admin.frontend.sections.homepage.saveSettings') }}" class="homepage-settings-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-xl-6">
                                <div class="hp-card mb-3">
                                    <div class="hp-card__head">{!! lang_en_bn('Power Zone (below hero banner)') !!}</div>
                                    <div class="hp-card__body">
                                        @php
                                            $pz = [
                                                ['power_zone_enabled', lang_en_bn('Enable Power Zone')],
                                                ['show_category_icons', lang_en_bn('Category Icons Slider')],
                                                ['show_flash_deals', lang_en_bn('Flash Deals / Today Deals')],
                                                ['show_trending', lang_en_bn('Trending Section')],
                                                ['show_quick_services', lang_en_bn('Quick Service Shortcuts')],
                                                ['show_promo_blocks', lang_en_bn('Promo / Trust Blocks')],
                                                ['show_quick_category_boxes', lang_en_bn('Quick Category Boxes')],
                                            ];
                                        @endphp
                                        @foreach($pz as [$key, $label])
                                            <div class="hp-switch">
                                                <label class="hp-switch__label" for="{{ $key }}">{!! $label !!}</label>
                                                <div class="form-check form-switch mb-0 flex-shrink-0">
                                                    <input type="hidden" name="{{ $key }}" value="0">
                                                    <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="{{ $key }}" {{ ($data['settings']->{$key} ?? 1) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="hp-card">
                                    <div class="hp-card__head">{!! lang_en_bn('Flash sale') !!}</div>
                                    <div class="hp-card__body">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Flash Sale Title') !!}</label>
                                                <input type="text" name="flash_sale_title" class="form-control form-control-sm" value="{{ $data['settings']->flash_sale_title ?? 'Flash Sale' }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Flash Sale End') !!}</label>
                                                <input type="datetime-local" name="flash_sale_end_date" class="form-control form-control-sm" value="{{ isset($data['settings']->flash_sale_end_date) ? \Carbon\Carbon::parse($data['settings']->flash_sale_end_date)->format('Y-m-d\TH:i') : now()->endOfDay()->format('Y-m-d\TH:i') }}">
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Flash Deals Limit') !!}</label>
                                                <input type="number" name="flash_deals_limit" class="form-control form-control-sm" min="1" max="20" value="{{ $data['settings']->flash_deals_limit ?? 8 }}">
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Trending Limit') !!}</label>
                                                <input type="number" name="trending_limit" class="form-control form-control-sm" min="1" max="20" value="{{ $data['settings']->trending_limit ?? 8 }}">
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Top Rated Limit') !!}</label>
                                                <input type="number" name="top_rated_limit" class="form-control form-control-sm" min="1" max="20" value="{{ $data['settings']->top_rated_limit ?? 8 }}">
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Reviews Slider Limit') !!}</label>
                                                <input type="number" name="reviews_slider_limit" class="form-control form-control-sm" min="1" max="15" value="{{ $data['settings']->reviews_slider_limit ?? 6 }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="hp-card mb-3">
                                    <div class="hp-card__head">{!! lang_en_bn('Trust & Social Proof') !!}</div>
                                    <div class="hp-card__body">
                                        @foreach([
                                            ['trust_section_enabled', lang_en_bn('Trust Section (above footer)')],
                                            ['social_proof_enabled', lang_en_bn('Social Proof Section')],
                                            ['reviews_slider_enabled', lang_en_bn('Customer Reviews Slider')],
                                            ['top_rated_enabled', lang_en_bn('Top Rated Products')],
                                        ] as [$key, $label])
                                            <div class="hp-switch">
                                                <label class="hp-switch__label" for="{{ $key }}">{!! $label !!}</label>
                                                <div class="form-check form-switch mb-0 flex-shrink-0">
                                                    <input type="hidden" name="{{ $key }}" value="0">
                                                    <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="{{ $key }}" {{ ($data['settings']->{$key} ?? 1) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="hp-card mb-3">
                                    <div class="hp-card__head">{!! lang_en_bn('Recommendations & UX') !!}</div>
                                    <div class="hp-card__body">
                                        @foreach([
                                            ['recommendation_enabled', lang_en_bn('Recommended / Similar Products')],
                                            ['recently_viewed_enabled', lang_en_bn('Recently Viewed')],
                                            ['sticky_cart_enabled', lang_en_bn('Sticky Add To Cart')],
                                            ['quick_view_enabled', lang_en_bn('Quick View Product')],
                                            ['wishlist_popup_enabled', lang_en_bn('Wishlist Popup')],
                                            ['compare_enabled', lang_en_bn('Compare Products')],
                                            ['floating_cart_enabled', lang_en_bn('Floating Cart')],
                                        ] as [$key, $label])
                                            <div class="hp-switch">
                                                <label class="hp-switch__label" for="{{ $key }}">{!! $label !!}</label>
                                                <div class="form-check form-switch mb-0 flex-shrink-0">
                                                    <input type="hidden" name="{{ $key }}" value="0">
                                                    <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="{{ $key }}" {{ ($data['settings']->{$key} ?? 1) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="hp-card">
                                    <div class="hp-card__head">{!! lang_en_bn('Conversion Boost') !!}</div>
                                    <div class="hp-card__body">
                                        @foreach([
                                            ['conversion_enabled', lang_en_bn('Show conversion cues')],
                                            ['limited_stock_enabled', lang_en_bn('Limited Stock Warning')],
                                            ['only_x_left_enabled', lang_en_bn('Only X Left')],
                                        ] as [$key, $label])
                                            <div class="hp-switch">
                                                <label class="hp-switch__label" for="{{ $key }}">{!! $label !!}</label>
                                                <div class="form-check form-switch mb-0 flex-shrink-0">
                                                    <input type="hidden" name="{{ $key }}" value="0">
                                                    <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="{{ $key }}" {{ ($data['settings']->{$key} ?? 1) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn--primary px-4"><i class="las la-save me-1"></i>{!! lang_en_bn('Save Settings') !!}</button>
                        </div>
                    </form>
                </div>

                {{-- Trust --}}
                <div class="tab-pane fade" id="tab-trust" role="tabpanel">
                    <p class="text-muted small mb-3">{!! lang_en_bn('Trust items above footer: Secure Payment, Fast Delivery, etc.') !!}</p>
                    <div class="hp-table-wrap mb-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{!! lang_en_bn('Title') !!}</th>
                                    <th class="d-none d-md-table-cell">{!! lang_en_bn('Icon') !!}</th>
                                    <th class="d-none d-lg-table-cell">{!! lang_en_bn('Short detail') !!}</th>
                                    <th class="text-end" style="width:9rem">{!! lang_en_bn('Action') !!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['trust_elements'] as $el)
                                    @php $dv = $el->data_values ?? (object)[]; @endphp
                                    <tr>
                                        <td class="fw-medium">{{ __($dv->title ?? '') }}</td>
                                        <td class="d-none d-md-table-cell"><i class="{{ $dv->icon ?? 'las la-check' }}"></i> <code class="small text-muted">{{ $dv->icon ?? '-' }}</code></td>
                                        <td class="d-none d-lg-table-cell small text-muted">{{ \Illuminate\Support\Str::limit($dv->short_detail ?? '', 48) }}</td>
                                        <td class="text-end text-nowrap">
                                            <button type="button" class="btn btn-sm btn-outline--primary edit-trust" data-id="{{ $el->id }}" data-title="{{ $dv->title ?? '' }}" data-icon="{{ $dv->icon ?? '' }}" data-short_detail="{{ $dv->short_detail ?? '' }}" data-url="{{ $dv->url ?? '#' }}">{!! lang_en_bn('Edit') !!}</button>
                                            <form method="POST" action="{{ route('admin.frontend.sections.homepage.deleteTrust', $el->id) }}" class="d-inline" onsubmit="return confirm('{{ __("Delete this item?") }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline--danger">{!! lang_en_bn('Delete') !!}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">{!! lang_en_bn('No trust items. Add one below.') !!}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="hp-form-card">
                        <div class="hp-form-card__title">{!! lang_en_bn('Add / Edit Trust Item') !!}</div>
                        <form method="POST" action="{{ route('admin.frontend.sections.homepage.saveTrust') }}">
                            @csrf
                            <input type="hidden" name="id" id="trust_id" value="">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Title') !!}</label>
                                    <input type="text" name="title" id="trust_title" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Icon') !!}</label>
                                    <input type="text" name="icon" id="trust_icon" class="form-control form-control-sm" placeholder="las la-lock">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('URL') !!}</label>
                                    <input type="text" name="url" id="trust_url" class="form-control form-control-sm" placeholder="#">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Short detail') !!}</label>
                                    <input type="text" name="short_detail" id="trust_short_detail" class="form-control form-control-sm">
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save me-1"></i>{!! lang_en_bn('Save') !!}</button>
                                    <button type="button" class="btn btn-outline--secondary btn-sm" id="trust-clear">{!! lang_en_bn('Clear') !!}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Quick services --}}
                <div class="tab-pane fade" id="tab-quick-service" role="tabpanel">
                    <p class="text-muted small mb-3">{!! lang_en_bn('Power Zone shortcuts: Track Order, Support, Return, Coupons.') !!}</p>
                    <div class="hp-table-wrap mb-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{!! lang_en_bn('Title') !!}</th>
                                    <th class="d-none d-sm-table-cell">{!! lang_en_bn('Icon') !!}</th>
                                    <th>{!! lang_en_bn('URL') !!}</th>
                                    <th class="text-end" style="width:9rem">{!! lang_en_bn('Action') !!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['quick_service_elements'] as $el)
                                    @php $dv = $el->data_values ?? (object)[]; @endphp
                                    <tr>
                                        <td class="fw-medium">{{ __($dv->title ?? '') }}</td>
                                        <td class="d-none d-sm-table-cell"><i class="{{ $dv->icon ?? 'las la-link' }}"></i></td>
                                        <td class="small"><span class="text-break">{{ \Illuminate\Support\Str::limit($dv->url ?? '', 56) }}</span></td>
                                        <td class="text-end text-nowrap">
                                            <button type="button" class="btn btn-sm btn-outline--primary edit-qservice" data-id="{{ $el->id }}" data-title="{{ $dv->title ?? '' }}" data-icon="{{ $dv->icon ?? '' }}" data-url="{{ $dv->url ?? '' }}">{!! lang_en_bn('Edit') !!}</button>
                                            <form method="POST" action="{{ route('admin.frontend.sections.homepage.deleteQuickService', $el->id) }}" class="d-inline" onsubmit="return confirm('{{ __("Delete?") }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline--danger">{!! lang_en_bn('Delete') !!}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">{!! lang_en_bn('No quick services. Add one below.') !!}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="hp-form-card">
                        <div class="hp-form-card__title">{!! lang_en_bn('Add / Edit Quick Service') !!}</div>
                        <form method="POST" action="{{ route('admin.frontend.sections.homepage.saveQuickService') }}">
                            @csrf
                            <input type="hidden" name="id" id="qservice_id" value="">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Title') !!}</label>
                                    <input type="text" name="title" id="qservice_title" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Icon') !!}</label>
                                    <input type="text" name="icon" id="qservice_icon" class="form-control form-control-sm" placeholder="las la-truck">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('URL') !!}</label>
                                    <input type="text" name="url" id="qservice_url" class="form-control form-control-sm" required placeholder="/track-order">
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save me-1"></i>{!! lang_en_bn('Save') !!}</button>
                                    <button type="button" class="btn btn-outline--secondary btn-sm" id="qservice-clear">{!! lang_en_bn('Clear') !!}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Promo --}}
                <div class="tab-pane fade" id="tab-promo" role="tabpanel">
                    <p class="text-muted small mb-3">{!! lang_en_bn('Promo blocks: Free Shipping, Cash on Delivery, etc.') !!}</p>
                    <div class="hp-table-wrap mb-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{!! lang_en_bn('Title') !!}</th>
                                    <th class="d-none d-md-table-cell">{!! lang_en_bn('Subtitle') !!}</th>
                                    <th class="d-none d-lg-table-cell">{!! lang_en_bn('URL') !!}</th>
                                    <th class="text-end" style="width:9rem">{!! lang_en_bn('Action') !!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['promo_banner_elements'] as $el)
                                    @php $dv = $el->data_values ?? (object)[]; @endphp
                                    <tr>
                                        <td class="fw-medium">{{ __($dv->title ?? '') }}</td>
                                        <td class="d-none d-md-table-cell small text-muted">{{ \Illuminate\Support\Str::limit($dv->subtitle ?? '', 36) }}</td>
                                        <td class="d-none d-lg-table-cell small">{{ \Illuminate\Support\Str::limit($dv->url ?? '', 40) }}</td>
                                        <td class="text-end text-nowrap">
                                            <button type="button" class="btn btn-sm btn-outline--primary edit-promo" data-id="{{ $el->id }}" data-title="{{ $dv->title ?? '' }}" data-subtitle="{{ $dv->subtitle ?? '' }}" data-url="{{ $dv->url ?? '#' }}">{!! lang_en_bn('Edit') !!}</button>
                                            <form method="POST" action="{{ route('admin.frontend.sections.homepage.deletePromoBanner', $el->id) }}" class="d-inline" onsubmit="return confirm('{{ __("Delete?") }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline--danger">{!! lang_en_bn('Delete') !!}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">{!! lang_en_bn('No promo banners. Add one below.') !!}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="hp-form-card">
                        <div class="hp-form-card__title">{!! lang_en_bn('Add / Edit Promo Banner') !!}</div>
                        <form method="POST" action="{{ route('admin.frontend.sections.homepage.savePromoBanner') }}">
                            @csrf
                            <input type="hidden" name="id" id="promo_id" value="">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Title') !!}</label>
                                    <input type="text" name="title" id="promo_title" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Subtitle') !!}</label>
                                    <input type="text" name="subtitle" id="promo_subtitle" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('URL') !!}</label>
                                    <input type="text" name="url" id="promo_url" class="form-control form-control-sm" placeholder="#">
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save me-1"></i>{!! lang_en_bn('Save') !!}</button>
                                    <button type="button" class="btn btn-outline--secondary btn-sm" id="promo-clear">{!! lang_en_bn('Clear') !!}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Quick category --}}
                <div class="tab-pane fade" id="tab-quick-category" role="tabpanel">
                    <p class="text-muted small mb-3">{!! lang_en_bn('Square boxes below banner: Hot Deals, Top Selling, New Arrival, Category or URL.') !!}</p>
                    <div class="hp-table-wrap mb-3">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{!! lang_en_bn('Title') !!}</th>
                                    <th class="d-none d-sm-table-cell">{!! lang_en_bn('Icon') !!}</th>
                                    <th>{!! lang_en_bn('Link Type') !!}</th>
                                    <th class="text-end" style="width:9rem">{!! lang_en_bn('Action') !!}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['quick_category_elements'] ?? [] as $el)
                                    @php $dv = $el->data_values ?? (object)[]; @endphp
                                    <tr>
                                        <td class="fw-medium">{{ __($dv->title ?? '') }}</td>
                                        <td class="d-none d-sm-table-cell"><i class="{{ $dv->icon ?? 'las la-th-large' }}"></i></td>
                                        <td class="small"><span class="badge bg-light text-dark border">{{ $dv->link_type ?? '-' }}</span>@if(($dv->link_type ?? '') === 'category' && !empty($dv->category_id)) <span class="text-muted">#{{ $dv->category_id }}</span> @endif</td>
                                        <td class="text-end text-nowrap">
                                            <button type="button" class="btn btn-sm btn-outline--primary edit-qcat" data-id="{{ $el->id }}" data-title="{{ $dv->title ?? '' }}" data-icon="{{ $dv->icon ?? '' }}" data-link_type="{{ $dv->link_type ?? 'hot_deal' }}" data-category_id="{{ $dv->category_id ?? '' }}" data-custom_url="{{ $dv->custom_url ?? '' }}">{!! lang_en_bn('Edit') !!}</button>
                                            <form method="POST" action="{{ route('admin.frontend.sections.homepage.deleteQuickCategory', $el->id) }}" class="d-inline" onsubmit="return confirm('{{ __("Delete?") }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline--danger">{!! lang_en_bn('Delete') !!}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">{!! lang_en_bn('No quick category boxes. Add below.') !!}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="hp-form-card">
                        <div class="hp-form-card__title">{!! lang_en_bn('Add / Edit Quick Category Box') !!}</div>
                        <form method="POST" action="{{ route('admin.frontend.sections.homepage.saveQuickCategory') }}">
                            @csrf
                            <input type="hidden" name="id" id="qcat_id" value="">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Title') !!}</label>
                                    <input type="text" name="title" id="qcat_title" class="form-control form-control-sm" required placeholder="Hot Deals">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Icon') !!}</label>
                                    <input type="text" name="icon" id="qcat_icon" class="form-control form-control-sm" placeholder="las la-bolt">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Link Type') !!}</label>
                                    <select name="link_type" id="qcat_link_type" class="form-select form-select-sm">
                                        <option value="hot_deal">{!! lang_en_bn('Hot Deals') !!}</option>
                                        <option value="best_selling">{!! lang_en_bn('Top Selling') !!}</option>
                                        <option value="new_arrival">{!! lang_en_bn('New Arrival') !!}</option>
                                        <option value="featured">{!! lang_en_bn('Featured') !!}</option>
                                        <option value="discount">{!! lang_en_bn('Discount & Offers') !!}</option>
                                        <option value="category">{!! lang_en_bn('Specific Category') !!}</option>
                                        <option value="url">{!! lang_en_bn('Custom URL') !!}</option>
                                    </select>
                                </div>
                                <div class="col-md-6 qcat-field qcat-field-category" style="display:none;">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Category') !!}</label>
                                    <select name="category_id" id="qcat_category_id" class="form-select form-select-sm"><option value="">-- {!! lang_en_bn('Select') !!} --</option>@foreach($categories ?? [] as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select>
                                </div>
                                <div class="col-md-6 qcat-field qcat-field-url" style="display:none;">
                                    <label class="form-label small fw-medium">{!! lang_en_bn('Custom URL') !!}</label>
                                    <input type="text" name="custom_url" id="qcat_custom_url" class="form-control form-control-sm" placeholder="https://...">
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save me-1"></i>{!! lang_en_bn('Save') !!}</button>
                                    <button type="button" class="btn btn-outline--secondary btn-sm" id="qcat-clear">{!! lang_en_bn('Clear') !!}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Product slider --}}
                <div class="tab-pane fade" id="tab-product-slider" role="tabpanel">
                    <p class="text-muted small mb-3">{!! lang_en_bn('Control automatic horizontal product scrolling (right → left) on home, category, product detail and dashboard sections.') !!}</p>
                    @php $ps = $data['product_slider_settings'] ?? (object) getProductSliderSettingsDefaults(); @endphp
                    <form method="POST" action="{{ route('admin.frontend.sections.homepage.saveProductSliderSettings') }}" class="product-slider-settings-form">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-lg-6">
                                <div class="hp-card h-100">
                                    <div class="hp-card__head">{!! lang_en_bn('Auto Scroll') !!}</div>
                                    <div class="hp-card__body">
                                        <div class="hp-switch mb-2 pb-2 border-bottom border-light">
                                            <label class="hp-switch__label" for="auto_scroll_enabled">{!! lang_en_bn('Enable Auto Scroll') !!}</label>
                                            <div class="form-check form-switch mb-0 flex-shrink-0">
                                                <input type="hidden" name="auto_scroll_enabled" value="0">
                                                <input class="form-check-input" type="checkbox" name="auto_scroll_enabled" value="1" id="auto_scroll_enabled" {{ ($ps->auto_scroll_enabled ?? 1) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-sm-6">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Scroll Interval (seconds)') !!}</label>
                                                <select name="scroll_interval_seconds" class="form-select form-select-sm">
                                                    <option value="3" {{ (int)($ps->scroll_interval_seconds ?? 5) === 3 ? 'selected' : '' }}>3</option>
                                                    <option value="5" {{ (int)($ps->scroll_interval_seconds ?? 5) === 5 ? 'selected' : '' }}>5</option>
                                                    <option value="7" {{ (int)($ps->scroll_interval_seconds ?? 5) === 7 ? 'selected' : '' }}>7</option>
                                                    <option value="10" {{ (int)($ps->scroll_interval_seconds ?? 5) === 10 ? 'selected' : '' }}>10</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Scroll Animation Speed (ms)') !!}</label>
                                                <input type="number" name="scroll_animation_speed_ms" class="form-control form-control-sm" min="300" max="2000" step="100" value="{{ $ps->scroll_animation_speed_ms ?? 600 }}">
                                                <small class="text-muted">300–2000</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="hp-card h-100">
                                    <div class="hp-card__head">{!! lang_en_bn('Products Per Row') !!}</div>
                                    <div class="hp-card__body">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Desktop') !!}</label>
                                                <input type="number" name="products_per_row_desktop" class="form-control form-control-sm" min="3" max="8" value="{{ $ps->products_per_row_desktop ?? 6 }}">
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Tablet') !!}</label>
                                                <input type="number" name="products_per_row_tablet" class="form-control form-control-sm" min="2" max="6" value="{{ $ps->products_per_row_tablet ?? 4 }}">
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small text-muted mb-1">{!! lang_en_bn('Mobile') !!}</label>
                                                <input type="number" name="products_per_row_mobile" class="form-control form-control-sm" min="1" max="3" value="{{ $ps->products_per_row_mobile ?? 2 }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hp-card mb-3">
                            <div class="hp-card__head">{!! lang_en_bn('Per-section scroll speed (seconds)') !!}</div>
                            <div class="hp-card__body">
                                <p class="small text-muted mb-3">{!! lang_en_bn('Each product row can have its own auto-scroll interval. Default used if empty.') !!}</p>
                                <div class="row g-2 g-md-3">
                                    <div class="col-6 col-sm-4 col-lg-2">
                                        <label class="form-label small text-muted mb-1">{!! lang_en_bn('Hot Deals') !!}</label>
                                        <input type="number" name="hot_deal_interval_seconds" class="form-control form-control-sm" min="2" max="30" value="{{ $ps->hot_deal_interval_seconds ?? 3 }}">
                                    </div>
                                    <div class="col-6 col-sm-4 col-lg-2">
                                        <label class="form-label small text-muted mb-1">{!! lang_en_bn('Featured') !!}</label>
                                        <input type="number" name="featured_interval_seconds" class="form-control form-control-sm" min="2" max="30" value="{{ $ps->featured_interval_seconds ?? 5 }}">
                                    </div>
                                    <div class="col-6 col-sm-4 col-lg-2" id="hp-new-arrivals-interval">
                                        <label class="form-label small text-muted mb-1">{!! lang_en_bn('New Arrivals') !!}</label>
                                        <input type="number" name="new_arrivals_interval_seconds" class="form-control form-control-sm" min="2" max="30" value="{{ $ps->new_arrivals_interval_seconds ?? 4 }}">
                                    </div>
                                    <div class="col-6 col-sm-4 col-lg-2">
                                        <label class="form-label small text-muted mb-1">{!! lang_en_bn('Trending Now') !!}</label>
                                        <input type="number" name="trending_interval_seconds" class="form-control form-control-sm" min="2" max="30" value="{{ $ps->trending_interval_seconds ?? 4 }}">
                                    </div>
                                    <div class="col-6 col-sm-4 col-lg-2">
                                        <label class="form-label small text-muted mb-1">{!! lang_en_bn('Best Selling') !!}</label>
                                        <input type="number" name="best_selling_interval_seconds" class="form-control form-control-sm" min="2" max="30" value="{{ $ps->best_selling_interval_seconds ?? 5 }}">
                                    </div>
                                    <div class="col-6 col-sm-4 col-lg-2" id="hp-recommended-interval">
                                        <label class="form-label small text-muted mb-1">{!! lang_en_bn('Recommended') !!}</label>
                                        <input type="number" name="recommended_interval_seconds" class="form-control form-control-sm" min="2" max="30" value="{{ $ps->recommended_interval_seconds ?? 5 }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn--primary px-4"><i class="las la-save me-1"></i>{!! lang_en_bn('Save Product Slider Settings') !!}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    .hp-hub__hero {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #eef2ff 100%);
        border-bottom: 1px solid rgba(0,0,0,.04);
    }
    .hp-hub__title { letter-spacing: -0.02em; }
    /* Help button: high contrast (readable EN + BN) — not white-on-blue */
    .hp-help-btn {
        background: #fff !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
        font-weight: 500;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
    }
    .hp-help-btn:hover, .hp-help-btn:focus {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #020617 !important;
    }
    .hp-help-btn__icon {
        color: #2563eb !important;
        font-size: 1.1rem;
        vertical-align: -2px;
    }
    .hp-help-btn .text-muted,
    .hp-help-btn__text .text-muted {
        color: #475569 !important;
        opacity: 1;
    }
    .hp-ref-tile {
        padding: 0.65rem 0.75rem;
        background: #fff;
        border: 1px solid #e8ecf1;
        border-radius: 10px;
        font-size: 0.75rem;
    }
    .hp-ref-tile strong { display: block; font-size: 0.8rem; color: #0f172a; margin-bottom: 0.2rem; }
    .hp-ref-tile__hint { color: #64748b; line-height: 1.35; }
    .hp-hub__tabbar {
        background: #fafbfc;
        border-bottom: 1px solid #e8ecf1;
    }
    .hp-hub__tabs {
        gap: 0.15rem;
        flex-wrap: nowrap;
        scrollbar-width: thin;
    }
    .hp-hub__tabs .nav-link {
        border: none;
        border-radius: 8px 8px 0 0;
        padding: 0.55rem 0.85rem;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #64748b;
        white-space: nowrap;
        margin-bottom: -1px;
    }
    .hp-hub__tabs .nav-link:hover { color: #334155; background: rgba(255,255,255,.7); }
    .hp-hub__tabs .nav-link.active {
        color: var(--base, #4634ff);
        background: #fff;
        border: 1px solid #e8ecf1;
        border-bottom-color: #fff;
    }
    .hp-tab-content { min-height: 12rem; }
    .hp-card {
        border: 1px solid #e8ecf1;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .hp-card__head {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        padding: 0.65rem 1rem;
        background: linear-gradient(180deg, #fafbfc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e8ecf1;
    }
    .hp-card__body { padding: 0.35rem 1rem 0.65rem; }
    .hp-switch {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.45rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .hp-switch:last-child { border-bottom: 0; }
    .hp-switch__label {
        margin: 0;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
        flex: 1;
        line-height: 1.35;
    }
    .hp-table-wrap {
        border: 1px solid #e8ecf1;
        border-radius: 12px;
        overflow: hidden;
    }
    .hp-table-wrap .table thead th {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        font-weight: 600;
        color: #64748b;
        border-bottom: 1px solid #e8ecf1 !important;
        padding: 0.7rem 1rem;
    }
    .hp-table-wrap .table tbody td { padding: 0.65rem 1rem; vertical-align: middle; }
    .hp-form-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: linear-gradient(180deg, #fafbfc 0%, #f8fafc 100%);
        padding: 1.1rem 1.25rem;
    }
    .hp-form-card__title {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #0f172a;
        margin-bottom: 1rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid #e2e8f0;
    }
    @media (max-width: 575px) {
        .hp-hub__tabs .nav-link span { display: none; }
        .hp-hub__tabs .nav-link { padding: 0.5rem 0.65rem; }
    }
</style>
@endpush

@push('script')
<script>
(function() {
    function openProductSliderTabAndScroll() {
        var tabLink = document.querySelector('a.nav-link[href="#tab-product-slider"]');
        if (tabLink && typeof bootstrap !== 'undefined' && bootstrap.Tab) {
            try { new bootstrap.Tab(tabLink).show(); } catch (e) {}
        }
        var focus = new URLSearchParams(window.location.search).get('hp_focus');
        setTimeout(function() {
            if (focus === 'recommended') {
                document.getElementById('hp-recommended-interval') && document.getElementById('hp-recommended-interval').scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else if (focus === 'new_arrivals') {
                document.getElementById('hp-new-arrivals-interval') && document.getElementById('hp-new-arrivals-interval').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }, 300);
    }
    var _hpFocus = new URLSearchParams(window.location.search).get('hp_focus');
    if (window.location.hash === '#tab-product-slider' || _hpFocus === 'new_arrivals' || _hpFocus === 'recommended') {
        document.addEventListener('DOMContentLoaded', openProductSliderTabAndScroll);
    }
    document.querySelectorAll('.edit-trust').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('trust_id').value = this.dataset.id || '';
            document.getElementById('trust_title').value = this.dataset.title || '';
            document.getElementById('trust_icon').value = this.dataset.icon || '';
            document.getElementById('trust_short_detail').value = this.dataset.short_detail || '';
            document.getElementById('trust_url').value = this.dataset.url || '#';
        });
    });
    document.getElementById('trust-clear') && document.getElementById('trust-clear').addEventListener('click', function() {
        document.getElementById('trust_id').value = '';
        document.getElementById('trust_title').value = '';
        document.getElementById('trust_icon').value = '';
        document.getElementById('trust_short_detail').value = '';
        document.getElementById('trust_url').value = '#';
    });
    document.querySelectorAll('.edit-qservice').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('qservice_id').value = this.dataset.id || '';
            document.getElementById('qservice_title').value = this.dataset.title || '';
            document.getElementById('qservice_icon').value = this.dataset.icon || '';
            document.getElementById('qservice_url').value = this.dataset.url || '';
        });
    });
    document.getElementById('qservice-clear') && document.getElementById('qservice-clear').addEventListener('click', function() {
        document.getElementById('qservice_id').value = '';
        document.getElementById('qservice_title').value = '';
        document.getElementById('qservice_icon').value = '';
        document.getElementById('qservice_url').value = '';
    });
    document.querySelectorAll('.edit-promo').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('promo_id').value = this.dataset.id || '';
            document.getElementById('promo_title').value = this.dataset.title || '';
            document.getElementById('promo_subtitle').value = this.dataset.subtitle || '';
            document.getElementById('promo_url').value = this.dataset.url || '#';
        });
    });
    document.getElementById('promo-clear') && document.getElementById('promo-clear').addEventListener('click', function() {
        document.getElementById('promo_id').value = '';
        document.getElementById('promo_title').value = '';
        document.getElementById('promo_subtitle').value = '';
        document.getElementById('promo_url').value = '#';
    });
    function toggleQcatFields() {
        var lt = document.getElementById('qcat_link_type').value;
        document.querySelectorAll('.qcat-field').forEach(function(el) { el.style.display = 'none'; });
        if (lt === 'category') document.querySelector('.qcat-field-category').style.display = 'block';
        if (lt === 'url') document.querySelector('.qcat-field-url').style.display = 'block';
    }
    document.getElementById('qcat_link_type') && document.getElementById('qcat_link_type').addEventListener('change', toggleQcatFields);
    toggleQcatFields();
    document.querySelectorAll('.edit-qcat').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('qcat_id').value = this.dataset.id || '';
            document.getElementById('qcat_title').value = this.dataset.title || '';
            document.getElementById('qcat_icon').value = this.dataset.icon || '';
            document.getElementById('qcat_link_type').value = this.dataset.link_type || 'hot_deal';
            document.getElementById('qcat_category_id').value = this.dataset.category_id || '';
            document.getElementById('qcat_custom_url').value = this.dataset.custom_url || '';
            toggleQcatFields();
        });
    });
    document.getElementById('qcat-clear') && document.getElementById('qcat-clear').addEventListener('click', function() {
        document.getElementById('qcat_id').value = '';
        document.getElementById('qcat_title').value = '';
        document.getElementById('qcat_icon').value = 'las la-th-large';
        document.getElementById('qcat_link_type').value = 'hot_deal';
        document.getElementById('qcat_category_id').value = '';
        document.getElementById('qcat_custom_url').value = '';
        toggleQcatFields();
    });
})();
</script>
@endpush
@endsection
