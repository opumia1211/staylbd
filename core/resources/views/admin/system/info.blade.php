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
                            <i class="las la-info-circle fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-primary">@lang('System Infrastructure Matrix')</h5>
                            <p class="text-muted small mb-0">@lang('Real-time diagnostics and environmental parameters for your application cluster.')</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.system.server.info') }}" class="btn btn-white btn-sm shadow-sm border px-3">
                            <i class="las la-server me-1 text-primary"></i> @lang('Server Details')
                        </a>
                        <a href="{{ route('admin.system.optimize') }}" class="btn btn-white btn-sm shadow-sm border px-3">
                            <i class="las la-broom me-1 text-primary"></i> @lang('Optimize Cluster')
                        </a>
                        <a href="{{ route('admin.system.info.export') }}" class="btn btn-primary btn-sm shadow-sm px-4" target="_blank">
                            <i class="las la-download me-1"></i> @lang('Export Matrix')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. Tactical Health Overview ── --}}
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-success rounded-pill px-3">@lang('Operational')</span>
                            <i class="las la-microchip fs-4 text-primary opacity-50"></i>
                        </div>
                        <h6 class="mb-1 fw-bold">@lang('Laravel Engine')</h6>
                        <h4 class="mb-0 text-primary fw-bold">{{ $laravelVersion }}</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge {{ $env === 'production' ? 'bg-label-success' : 'bg-label-warning' }} rounded-pill px-3">{{ $env }}</span>
                            <i class="las la-globe fs-4 text-primary opacity-50"></i>
                        </div>
                        <h6 class="mb-1 fw-bold">@lang('Deployment Mode')</h6>
                        <h4 class="mb-0 text-primary fw-bold">@lang('Active')</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge {{ $debug ? 'bg-label-danger' : 'bg-label-success' }} rounded-pill px-3">{{ $debug ? __('ON') : __('OFF') }}</span>
                            <i class="las la-bug fs-4 text-primary opacity-50"></i>
                        </div>
                        <h6 class="mb-1 fw-bold">@lang('Diagnostic Debug')</h6>
                        <h4 class="mb-0 text-primary fw-bold">@lang('Status')</h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-info rounded-pill px-3">{{ $activeTemplate }}</span>
                            <i class="las la-palette fs-4 text-primary opacity-50"></i>
                        </div>
                        <h6 class="mb-1 fw-bold">@lang('Active Interface')</h6>
                        <h4 class="mb-0 text-primary fw-bold">@lang('Template')</h4>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 3. Application Core Architecture ── --}}
        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="las la-layer-group me-2 text-primary"></i>@lang('Core Environment Data')</h6>
                    <span class="badge bg-label-primary rounded-pill small">@lang('System Detected')</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Framework Name')</span></td>
                                    <td class="text-end pe-4"><span class="fw-bold">{{ systemDetails()['name'] }}</span></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Build Signature')</span></td>
                                    <td class="text-end pe-4"><code class="small">{{ systemDetails()['build_version'] }}</code></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Application Vector')</span></td>
                                    <td class="text-end pe-4"><code class="small">{{ $url }}</code></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Temporal Zone')</span></td>
                                    <td class="text-end pe-4"><span class="badge bg-label-secondary">{{ $timeZone }}</span></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('HTTPS Protocol')</span></td>
                                    <td class="text-end pe-4">
                                        @if($isHttps)
                                            <span class="text-success small fw-bold"><i class="las la-lock me-1"></i> @lang('Encrypted')</span>
                                        @else
                                            <span class="text-danger small fw-bold"><i class="las la-lock-open me-1"></i> @lang('Insecure')</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Storage Path')</span></td>
                                    <td class="text-end pe-4"><code class="tiny text-muted">{{ $storagePathReal ?: storage_path() }}</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 4. Driver & Logic Stack ── --}}
        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-white py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="las la-cog me-2 text-primary"></i>@lang('Infrastructure Driver Stack')</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 border rounded bg-lighter">
                                <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Cache Driver')</label>
                                <span class="fw-bold text-dark">{{ $cacheDriver }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-lighter">
                                <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Session Driver')</label>
                                <span class="fw-bold text-dark">{{ $sessionDriver }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-lighter">
                                <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Queue Driver')</label>
                                <span class="fw-bold text-dark">{{ $queueDriver }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-lighter">
                                <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Mail Engine')</label>
                                <span class="fw-bold text-dark">{{ $mailDriver }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded bg-label-primary">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <label class="d-block text-primary tiny fw-bold text-uppercase mb-1">@lang('Log Integrity')</label>
                                        <span class="fw-bold fs-5">{{ $logSize }}</span>
                                    </div>
                                    <i class="las la-file-alt fs-2 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 5. Resources & Logic Components ── --}}
        <div class="col-xl-8 col-lg-7">
            <div class="row g-4">
                {{-- Database Insight --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold"><i class="las la-database me-2 text-primary"></i>@lang('Database Logic Cluster')</h6>
                            <span class="badge {{ $dbConnected ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill small">
                                <i class="las la-circle me-1"></i> {{ $dbConnected ? __('Connected') : __('Disconnected') }}
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4 align-items-center">
                                <div class="col-md-4 text-center border-end">
                                    <h2 class="mb-0 fw-bold text-primary">{{ $dbTablesCount ?? 'N/A' }}</h2>
                                    <span class="text-muted small">@lang('Total Tables')</span>
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">@lang('Relational Engine')</span>
                                            <span class="badge bg-label-info">{{ $dbDriver }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">@lang('Cluster Name')</span>
                                            <span class="fw-bold">{{ $dbName }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted small">@lang('Server Version')</span>
                                            <code class="small">{{ $dbVersion ?? 'N/A' }}</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PHP Extension Grid --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-bold"><i class="las la-puzzle-piece me-2 text-primary"></i>@lang('PHP Extension Ecosystem')</h6>
                            @if(count($missingExtensions) > 0)
                                <span class="badge bg-danger rounded-pill">@lang('Missing') {{ count($missingExtensions) }}</span>
                            @else
                                <span class="badge bg-success rounded-pill">@lang('Fully Synchronized')</span>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($phpExtensions as $ext)
                                    @php $isRequired = in_array($ext, $requiredExtensions); @endphp
                                    <span class="badge {{ $isRequired ? 'bg-primary' : 'bg-label-secondary border' }} py-2 px-3">{{ $ext }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 6. Permissions & Strategic Alerts ── --}}
        <div class="col-xl-4 col-lg-5">
            <div class="row g-4">
                {{-- Permissions --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom bg-white py-3 px-4">
                            <h6 class="mb-0 fw-bold"><i class="las la-folder-open me-2 text-primary"></i>@lang('Write Permissions')</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-label-{{ $storageWritable ? 'success' : 'danger' }} p-2 rounded">
                                        <i class="las la-hdd fs-4"></i>
                                    </div>
                                    <span class="fw-bold small">@lang('Storage Layer')</span>
                                </div>
                                <span class="badge bg-{{ $storageWritable ? 'success' : 'danger' }}">{{ $storageWritable ? __('Writable') : __('Protected') }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-label-{{ $bootstrapCacheWritable ? 'success' : 'danger' }} p-2 rounded">
                                        <i class="las la-bolt fs-4"></i>
                                    </div>
                                    <span class="fw-bold small">@lang('Bootstrap Engine')</span>
                                </div>
                                <span class="badge bg-{{ $bootstrapCacheWritable ? 'success' : 'danger' }}">{{ $bootstrapCacheWritable ? __('Writable') : __('Protected') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- System Health Pulse --}}
                <div class="col-12">
                    @php
                        $warnings = [];
                        if (!$storageWritable) $warnings[] = __('Storage layer is locked');
                        if (!$bootstrapCacheWritable) $warnings[] = __('Bootstrap acceleration is disabled');
                        if (count($missingExtensions) > 0) $warnings[] = __('Incomplete extension cluster');
                        if ($debug && $env === 'production') $warnings[] = __('Security Risk: Debug ON in Production');
                    @endphp
                    <div class="card border-0 shadow-sm {{ count($warnings) > 0 ? 'bg-label-warning' : 'bg-label-success' }}">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <i class="las {{ count($warnings) > 0 ? 'la-exclamation-triangle' : 'la-check-circle' }} fs-1"></i>
                                <h5 class="mb-0 fw-bold">@lang('Health Integrity')</h5>
                            </div>
                            @if(count($warnings) > 0)
                                <ul class="mb-0 ps-3 small fw-bold">
                                    @foreach($warnings as $w)
                                        <li class="mb-1">{{ $w }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mb-0 fw-bold small">@lang('System integrity verified. All architectural components are performing optimally.')</p>
                            @endif
                        </div>
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
    .bg-label-danger { background-color: #ff3e1d1a !important; color: #ff3e1d !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: #03c3ec !important; }
    .bg-label-secondary { background-color: #ebeef0 !important; color: #8592a3 !important; }
    .bg-lighter { background-color: #f8fafc !important; }
    .tiny { font-size: 0.7rem !important; }
    .ls-1 { letter-spacing: 1px; }
</style>
@endpush
