@extends('admin.layouts.app')
@section('panel')
<div class="scrollbar-management-wrapper animate__animated animate__fadeIn">
    {{-- 1. Strategic Summary --}}
    <div class="row mb-4 g-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial rounded-circle bg-label-primary shadow-sm"><i class="las la-broadcast-tower fs-3"></i></span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted small fw-bold">@lang('Total Tickers')</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ $bars->count() + ($customBars ?? collect())->count() }}</h4>
                        <div class="tiny text-success mt-1"><i class="las la-arrow-up"></i> @lang('Active Matrix')</div>
                    </div>
                </div>
                <div class="abstract-bg-shape bg-primary opacity-05"></div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial rounded-circle bg-label-success shadow-sm"><i class="las la-check-circle fs-3"></i></span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted small fw-bold">@lang('Live Now')</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ $bars->where('data_values.status', 1)->count() }}</h4>
                        <div class="tiny text-muted mt-1">@lang('Publicly Visible')</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="avatar avatar-lg me-3">
                        <span class="avatar-initial rounded-circle bg-label-warning shadow-sm"><i class="las la-clock fs-3"></i></span>
                    </div>
                    <div>
                        <h6 class="mb-0 text-muted small fw-bold">@lang('Draft/Hidden')</h6>
                        <h4 class="mb-0 fw-bold text-dark">{{ $bars->where('data_values.status', 0)->count() }}</h4>
                        <div class="tiny text-muted mt-1">@lang('Internal Nodes')</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden border-start border-4 border-info">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 text-muted small fw-bold">@lang('Master Protocol')</h6>
                            <div class="mt-2">
                                <span class="badge {{ !isset($scrollbarEnabled) || $scrollbarEnabled ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill px-3 py-2 shadow-sm">
                                    {{ !isset($scrollbarEnabled) || $scrollbarEnabled ? __('SYSTEM ENABLED') : __('SYSTEM DISABLED') }}
                                </span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.frontend.sections.scrollbar.settings') }}">
                            @csrf
                            <input type="hidden" name="scrollbar_enabled" value="{{ (!isset($scrollbarEnabled) || $scrollbarEnabled) ? 0 : 1 }}">
                            <button type="submit" class="btn btn-icon btn-label-{{ !isset($scrollbarEnabled) || $scrollbarEnabled ? 'danger' : 'success' }} btn-md rounded-circle shadow-sm">
                                <i class="las la-power-off fs-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Ticker Management Hub --}}
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center">
                <div class="badge bg-label-primary p-2 me-3 rounded-3 shadow-sm">
                    <i class="las la-newspaper fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">@lang('Headline Tickers')</h5>
                    <p class="text-muted small mb-0">@lang('Configure dynamic horizontal news bars and promotional headlines.')</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <div class="input-group input-group-merge search-box shadow-none border rounded-pill px-3 py-1 me-2" style="max-width: 200px;">
                    <span class="input-group-text border-0 bg-transparent p-0 me-2"><i class="las la-search text-muted"></i></span>
                    <input type="text" id="scrollbarSearch" class="form-control border-0 bg-transparent p-0 small" placeholder="@lang('Filter tickers...')">
                </div>
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm px-4 rounded-pill shadow-md dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="las la-plus me-1"></i> @lang('Initialize Node')
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-2xl rounded-3">
                        <li><a class="dropdown-item py-2" href="{{ route('admin.frontend.sections.scrollbar.new') }}"><i class="las la-bolt me-2 text-primary"></i> @lang('Default Headline Bar')</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('admin.frontend.sections.scrollbar2.new') }}"><i class="las la-link me-2 text-success"></i> @lang('Custom Page Ticker')</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-premium">
                        <tr>
                            <th class="ps-4 tiny fw-bold text-muted text-uppercase ls-1">@lang('Order')</th>
                            <th class="tiny fw-bold text-muted text-uppercase ls-1">@lang('Identity')</th>
                            <th class="tiny fw-bold text-muted text-uppercase ls-1">@lang('Position & Styling')</th>
                            <th class="tiny fw-bold text-muted text-uppercase ls-1">@lang('Visibility')</th>
                            <th class="tiny fw-bold text-muted text-uppercase ls-1">@lang('Status')</th>
                            <th class="text-end pe-4 tiny fw-bold text-muted text-uppercase ls-1">@lang('Tactical Actions')</th>
                        </tr>
                    </thead>
                    <tbody id="scrollbarTableBody">
                        @forelse($bars as $bar)
                        @php
                            $dv = $bar->data_values ?? (object)[];
                            $items = is_array($dv->items ?? null) ? ($dv->items ?? []) : (array)($dv->items ?? []);
                            $searchData = strtolower($dv->title . ' ' . ($dv->position ?? ''));
                        @endphp
                        <tr class="scrollbar-row transition-all" data-search="{{ $searchData }}">
                            <td class="ps-4">
                                <span class="badge bg-label-secondary small rounded-pill fw-bold">#{{ $dv->display_order ?? $bar->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-label-info rounded me-3 d-flex align-items-center justify-content-center shadow-sm">
                                        <i class="las la-paragraph fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-semibold">{{ $dv->title ?: __('Scroll Bar') . ' #' . $bar->id }}</h6>
                                        <div class="d-flex align-items-center tiny text-muted mt-1">
                                            <i class="las la-layer-group me-1"></i> {{ count($items) }} @lang('Elements')
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge bg-label-secondary rounded-pill w-fit tiny px-3 py-1 border border-secondary border-opacity-10">
                                        <i class="las la-map-marker-alt me-1"></i>{{ __(str_replace('_', ' ', $dv->position ?? 'header_below')) }}
                                    </span>
                                    <div class="d-flex gap-1 align-items-center">
                                        <span class="badge bg-label-info tiny text-uppercase">{{ $dv->template ?? 'glass' }}</span>
                                        <span class="text-muted tiny">|</span>
                                        <span class="text-primary tiny fw-bold"><i class="las la-tachometer-alt me-1"></i>{{ $dv->scroll_speed ?? 45 }} spd</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <form method="POST" action="{{ route('admin.frontend.sections.scrollbar.toggle.visibility', $bar->id) }}">
                                        @csrf
                                        @if(($dv->visibility ?? 'public') === 'private')
                                            <button type="submit" class="btn p-0 border-0" title="@lang('Make Public')">
                                                <span class="badge bg-label-dark rounded-pill px-3 py-1"><i class="las la-lock me-1"></i>@lang('Private')</span>
                                            </button>
                                        @else
                                            <button type="submit" class="btn p-0 border-0" title="@lang('Make Private')">
                                                <span class="badge bg-label-primary rounded-pill px-3 py-1"><i class="las la-globe me-1"></i>@lang('Public')</span>
                                            </button>
                                        @endif
                                    </form>
                                    @if(!empty($dv->schedule_start))
                                        <div class="tiny text-warning fw-bold mt-1"><i class="las la-clock me-1"></i>@lang('Scheduled Node')</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.frontend.sections.scrollbar.toggle', $bar->id) }}">
                                    @csrf
                                    <div class="form-check form-switch modern-switch-sm p-0 d-flex align-items-center">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" onchange="this.form.submit()" {{ !empty($dv->status) ? 'checked' : '' }}>
                                        <span class="badge {{ !empty($dv->status) ? 'bg-label-success' : 'bg-label-warning' }} rounded-pill tiny px-2">
                                            {{ !empty($dv->status) ? __('ONLINE') : __('DRAFT') }}
                                        </span>
                                    </div>
                                </form>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group shadow-sm border rounded-pill overflow-hidden">
                                    <a href="{{ route('admin.frontend.sections.scrollbar.duplicate', $bar->id) }}" class="btn btn-white btn-sm px-2 border-0" title="@lang('Clone Architecture')">
                                        <i class="las la-copy text-secondary fs-5"></i>
                                    </a>
                                    <a href="{{ route('admin.frontend.sections.scrollbar.edit', ['id' => $bar->id]) }}" class="btn btn-white btn-sm px-2 border-0 border-start" title="@lang('Reconfigure')">
                                        <i class="las la-edit text-primary fs-5"></i>
                                    </a>
                                    <button type="button" class="btn btn-white btn-sm px-2 border-0 border-start confirmationBtn" 
                                        data-action="{{ route('admin.frontend.sections.scrollbar.delete', $bar->id) }}"
                                        data-question="@lang('Permanently remove this ticker from the system?')"
                                        title="@lang('Decommission')">
                                        <i class="las la-trash-alt text-danger fs-5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state py-5">
                                    <div class="avatar avatar-xl bg-label-secondary mx-auto mb-4">
                                        <span class="avatar-initial rounded-circle shadow-sm"><i class="las la-bolt fs-1 opacity-25"></i></span>
                                    </div>
                                    <h5 class="text-dark fw-bold">@lang('No Tickers Found')</h5>
                                    <p class="text-muted small mb-4">@lang('Your headline news matrix is empty. Initialize your first ticker to engage users.')</p>
                                    <a href="{{ route('admin.frontend.sections.scrollbar.new') }}" class="btn btn-primary btn-sm px-5 rounded-pill shadow-md">
                                        <i class="las la-plus me-1"></i> @lang('Deploy First Ticker')
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- 3. Custom Context Tickers --}}
    @if(($customBars ?? collect())->count() > 0)
    <div class="mt-5 animate__animated animate__fadeInUp">
        <div class="d-flex align-items-center mb-4">
            <div class="badge bg-label-success p-2 me-3 rounded-3 shadow-sm">
                <i class="las la-link fs-4"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold text-dark">@lang('Contextual Page Tickers')</h5>
                <p class="text-muted small mb-0">@lang('Tickers bound to specific URL protocols or category clusters.')</p>
            </div>
        </div>
        <div class="row g-4" id="customTickersGrid">
            @foreach($customBars as $bar)
                @php $dv = $bar->data_values ?? (object)[]; @endphp
                <div class="col-xl-4 col-md-6 custom-ticker-card" data-search="{{ strtolower($dv->title ?? '') }}">
                    <div class="card border-0 shadow-sm h-100 transition-all hover-translate-y rounded-4 overflow-hidden border-top border-4 border-success border-opacity-25">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between mb-3 align-items-start">
                                <div class="badge bg-label-success rounded-pill px-3 py-1 tiny fw-bold">@lang('CUSTOM RULE')</div>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-label-secondary btn-sm rounded-circle" type="button" data-bs-toggle="dropdown"><i class="las la-ellipsis-v"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-2xl border-0 rounded-3">
                                        <li><a class="dropdown-item py-2" href="{{ route('admin.frontend.sections.scrollbar2.edit', ['id' => $bar->id]) }}"><i class="las la-cog me-2 text-primary"></i>@lang('Configure')</a></li>
                                        <li><a class="dropdown-item py-2" href="{{ route('admin.frontend.sections.scrollbar.duplicate', $bar->id) }}"><i class="las la-copy me-2 text-info"></i>@lang('Duplicate')</a></li>
                                        <li><hr class="dropdown-divider opacity-10"></li>
                                        <li>
                                            <button type="button" class="dropdown-item py-2 text-danger confirmationBtn" 
                                                data-action="{{ route('admin.frontend.sections.scrollbar.delete', $bar->id) }}"
                                                data-question="@lang('Decommission this custom ticker?')">
                                                <i class="las la-trash-alt me-2"></i>@lang('Decommission')
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <h6 class="fw-bold text-dark mb-2">{{ $dv->title ?? ('Custom Ticker #' . $bar->id) }}</h6>
                            <div class="d-flex align-items-center text-muted tiny mb-3">
                                <i class="las la-map-pin me-1"></i> @lang('Position'): <span class="text-dark fw-bold ms-1">{{ __((string)($dv->position ?? 'custom')) }}</span>
                            </div>
                            
                            <div class="bg-light-premium p-3 rounded-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="tiny fw-bold text-muted">@lang('Targeting')</span>
                                    <span class="badge bg-label-dark tiny rounded-pill">@lang('Specific URL')</span>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <div class="d-flex align-items-center">
                                    <div class="dot {{ !empty($dv->status) ? 'bg-success' : 'bg-warning' }} me-2" style="width: 8px; height: 8px; border-radius: 50%;"></div>
                                    <span class="tiny fw-bold {{ !empty($dv->status) ? 'text-success' : 'text-warning' }}">{{ !empty($dv->status) ? __('LIVE') : __('DRAFT') }}</span>
                                </div>
                                <a href="{{ route('admin.frontend.sections.scrollbar2.edit', ['id' => $bar->id]) }}" class="btn btn-sm btn-label-primary px-3 rounded-pill fw-bold">@lang('Management Hub')</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<x-confirmation-modal />
@endsection

@push('style')
<style>
    :root {
        --premium-primary: #696cff;
        --premium-success: #71dd37;
        --premium-info: #03c3ec;
    }

    .scrollbar-management-wrapper { font-family: 'Public Sans', sans-serif; }
    
    .bg-label-primary { background-color: #e7e7ff !important; color: var(--premium-primary) !important; }
    .bg-label-success { background-color: #e8fadf !important; color: var(--premium-success) !important; }
    .bg-label-warning { background-color: #fff2e2 !important; color: #ffab00 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: var(--premium-info) !important; }
    .bg-label-secondary { background-color: #f5f5f9 !important; color: #8592a3 !important; }
    .bg-label-dark { background-color: #e1e2e3 !important; color: #435971 !important; }
    .bg-light-premium { background-color: #fcfdfe; }

    .avatar-lg { width: 48px; height: 48px; }
    .avatar-sm { width: 32px; height: 32px; }
    .avatar-xs { width: 26px; height: 26px; }

    .ls-1 { letter-spacing: 0.5px; }
    .tiny { font-size: 0.72rem; }
    .w-fit { width: fit-content; }
    .opacity-05 { opacity: 0.05; }

    .abstract-bg-shape {
        position: absolute;
        right: -20px;
        top: -20px;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        z-index: 0;
    }

    /* Modern Switch */
    .modern-switch-sm .form-check-input { width: 2.2rem; height: 1.1rem; cursor: pointer; }
    .modern-switch-sm .form-check-input:checked { background-color: var(--premium-success); border-color: var(--premium-success); }

    /* Row Hover */
    .scrollbar-row:hover { background-color: #f8f9ff !important; transform: scale(1.001); }
    
    .hover-translate-y:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(105, 108, 255, 0.1) !important; }

    .search-box { transition: all 0.3s; }
    .search-box:focus-within { border-color: var(--premium-primary) !important; box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1) !important; }

    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .shadow-md { box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    
    @media (max-width: 767px) {
        .card-header .d-flex { flex-direction: column; align-items: flex-start !important; }
        .search-box { max-width: 100% !important; width: 100%; margin-bottom: 10px; }
    }
</style>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    $(document).ready(function() {
        // Real-time Filtering
        $('#scrollbarSearch').on('input', function() {
            const query = $(this).val().toLowerCase();
            
            // Filter Main Table
            $('.scrollbar-row').each(function() {
                const text = $(this).attr('data-search');
                $(this).toggle(text.includes(query));
            });

            // Filter Custom Grid
            $('.custom-ticker-card').each(function() {
                const text = $(this).attr('data-search');
                $(this).closest('.col-xl-4').toggle(text.includes(query));
            });
        });
    });
})(jQuery);
</script>
@endpush
