@extends('admin.layouts.app')

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    <div class="row g-4">
        {{-- ── 1. Strategic Control Center ── --}}
        <div class="col-12">
            <div class="card border-0 shadow-none bg-label-primary overflow-hidden">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary text-white p-2 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="las la-tools fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary">@lang('Maintenance Strategic Hub')</h5>
                            <p class="text-muted small mb-0">@lang('Unified telemetry and resource cleanup for your application infrastructure.')</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.maintenance.dashboard', ['refresh' => 1]) }}" class="btn btn-outline-primary btn-sm shadow-sm border px-3">
                            <i class="las la-sync-alt me-1 text-primary"></i> @lang('Refresh Matrix')
                        </a>
                        <form action="{{ route('admin.maintenance.clean.temp.cache') }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to trigger a full resource flush?') }}');">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm px-4">
                                <i class="las la-broom me-1 text-white"></i> @lang('Tactical Clean')
                            </button>
                        </form>
                        <a href="{{ route('admin.system.optimize') }}" class="btn btn-white btn-sm shadow-sm border px-3">
                            <i class="las la-tasks me-1 text-primary"></i> @lang('Cache Logic')
                        </a>
                        <a href="{{ route('admin.system.info') }}" class="btn btn-white btn-sm shadow-sm border px-3">
                            <i class="las la-info-circle me-1 text-primary"></i> @lang('Matrix Info')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. Hardware Telemetry Widgets ── --}}
        <div class="col-12">
            <div class="row g-4">
                {{-- Disk Usage --}}
                <div class="col-xl-6 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold"><i class="las la-hdd me-2 text-primary"></i>@lang('Disk Telemetry')</h6>
                            @if(isset($diskUsage['usage_percent']))
                                <span class="badge bg-label-{{ $diskUsage['usage_percent'] > 85 ? 'danger' : 'primary' }} px-3">@lang('Load'): {{ $diskUsage['usage_percent'] }}%</span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <div class="p-3 border rounded bg-lighter">
                                        <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Total Capacity')</label>
                                        <span class="fw-bold text-dark fs-5">{{ $diskUsage['disk_total'] }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 border rounded bg-lighter">
                                        <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Available Space')</label>
                                        <span class="fw-bold text-primary fs-5">{{ $diskUsage['disk_free'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-3 col-6">
                                    <div class="text-center">
                                        <span class="d-block text-muted tiny fw-bold">@lang('STORAGE')</span>
                                        <span class="fw-bold small">{{ $diskUsage['storage_size'] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 border-start">
                                    <div class="text-center">
                                        <span class="d-block text-muted tiny fw-bold">@lang('PUBLIC')</span>
                                        <span class="fw-bold small">{{ $diskUsage['public_size'] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 border-start">
                                    <div class="text-center">
                                        <span class="d-block text-muted tiny fw-bold">@lang('LOGS')</span>
                                        <span class="fw-bold small">{{ $diskUsage['log_size'] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6 border-start">
                                    <div class="text-center">
                                        <span class="d-block text-muted tiny fw-bold">@lang('TEMP')</span>
                                        <span class="fw-bold small">{{ $diskUsage['temp_size'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Database & Cache Analytics --}}
                <div class="col-xl-6 col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold"><i class="las la-database me-2 text-info"></i>@lang('Logic Engine Health')</h6>
                            <span class="badge {{ $dbHealth['connected'] ? 'bg-label-success' : 'bg-label-danger' }} px-3">
                                {{ $dbHealth['connected'] ? __('Synchronized') : __('Offline') }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-label-info p-3 rounded">
                                        <i class="las la-chart-pie fs-2"></i>
                                    </div>
                                    <div>
                                        <h3 class="mb-0 fw-bold text-info">{{ $dbHealth['total_size'] }}</h3>
                                        <span class="text-muted small">@lang('Total Relational Footprint')</span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted tiny fw-bold d-block">@lang('CACHE REFRESH')</span>
                                    <span class="fw-bold small text-success">{{ $cacheStatus['last_cleared'] }}</span>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <td><span class="text-muted small fw-bold">@lang('Relational Engine')</span></td>
                                            <td class="text-end fw-bold small text-uppercase text-primary">{{ $dbHealth['driver'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="text-muted small fw-bold">@lang('Framework Cache Load')</span></td>
                                            <td class="text-end fw-bold small text-primary">{{ $cacheStatus['cache_size'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="text-muted small fw-bold">@lang('View Compiling Load')</span></td>
                                            <td class="text-end fw-bold small text-primary">{{ $cacheStatus['view_size'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 3. Media Cluster Breakdown ── --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="las la-images me-2 text-warning"></i>@lang('Distributed Media Cluster Breakdown')</h6>
                    <span class="badge bg-label-warning px-3">@lang('Total Volume'): {{ $mediaUploads['total_size'] }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        @foreach($mediaUploads['breakdown'] as $key => $size)
                            <div class="col-md-3 col-sm-6">
                                <div class="p-3 border rounded h-100 bg-lighter d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="d-block text-muted tiny fw-bold text-uppercase mb-1">{{ str_replace('product', 'PRD ', $key) }}</span>
                                        <span class="fw-bold text-dark">{{ $size }}</span>
                                    </div>
                                    <i class="las la-file fs-3 opacity-25"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 4. Strategic Maintenance Note ── --}}
        <div class="col-12">
            <div class="card border-0 shadow-none bg-label-info">
                <div class="card-body p-4 d-flex align-items-center gap-3">
                    <i class="las la-info-circle fs-1 opacity-75"></i>
                    <div>
                        <h6 class="mb-1 fw-bold text-info">@lang('Maintenance Automation Protocol')</h6>
                        <p class="mb-0 text-muted small">@lang('All telemetry data is live. You can automate the resource flush by scheduling the command:') <code>php artisan maintenance:clean-temp-cache</code> @lang('in your server cron matrix.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
    .bg-label-primary { background-color: #e7e7ff !important; color: #696cff !important; }
    .bg-label-success { background-color: #e2f6ed !important; color: #28c76f !important; }
    .bg-label-warning { background-color: #fff2d6 !important; color: #ffab00 !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
    .bg-lighter { background-color: #f8fafc !important; }
    .tiny { font-size: 0.7rem !important; }
    .btn-white { background: #fff; color: #444; }
</style>
@endpush
