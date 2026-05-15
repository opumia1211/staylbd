@extends('admin.layouts.app')

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    <div class="row g-4">
        {{-- ── 1. Strategic Control Center ── --}}
        <div class="col-12">
            <div class="card border-0 shadow-none bg-label-info overflow-hidden">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-info text-white p-2 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="las la-server fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-info">@lang('Server Tactical Matrix')</h5>
                            <p class="text-muted small mb-0">@lang('Live environment parameters and hardware resource allocation mapping.')</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.system.info') }}" class="btn btn-white btn-sm shadow-sm border px-3">
                            <i class="las la-info-circle me-1 text-info"></i> @lang('App Context')
                        </a>
                        <a href="{{ route('admin.system.optimize') }}" class="btn btn-white btn-sm shadow-sm border px-3">
                            <i class="las la-broom me-1 text-info"></i> @lang('Optimize')
                        </a>
                        <a href="{{ route('admin.system.info.export') }}" class="btn btn-info btn-sm shadow-sm px-4 text-white" target="_blank">
                            <i class="las la-download me-1"></i> @lang('JSON Export')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. Resource Pulse Meter ── --}}
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-primary rounded-pill">@lang('Disk Free')</span>
                            <i class="las la-hdd fs-4 text-primary opacity-50"></i>
                        </div>
                        <h4 class="mb-0 fw-bold text-primary">{{ $resources['disk_free'] }}</h4>
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-primary" style="width: 70%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-success rounded-pill">@lang('PHP Runtime')</span>
                            <i class="lab la-php fs-4 text-success opacity-50"></i>
                        </div>
                        <h4 class="mb-0 fw-bold text-success">{{ $core['php_version'] }}</h4>
                        <p class="text-muted tiny mb-0 mt-1">@lang('Engine optimized and stable')</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-warning rounded-pill">@lang('Memory Cap')</span>
                            <i class="las la-memory fs-4 text-warning opacity-50"></i>
                        </div>
                        <h4 class="mb-0 fw-bold text-warning">{{ $phpConfig['memory_limit'] }}</h4>
                        <p class="text-muted tiny mb-0 mt-1">@lang('Runtime allocation limit')</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm bg-white p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-info rounded-pill">@lang('System OS')</span>
                            <i class="las la-desktop fs-4 text-info opacity-50"></i>
                        </div>
                        <h4 class="mb-0 fw-bold text-info">{{ $resources['server_os'] }}</h4>
                        <p class="text-muted tiny mb-0 mt-1">{{ $resources['server_time'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 3. Core Software Architecture ── --}}
        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-white py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="las la-microchip me-2 text-info"></i>@lang('Server Core Software')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Software Engine')</span></td>
                                    <td class="text-end pe-4"><code class="small">{{ $core['server_software'] }}</code></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('IP Architecture')</span></td>
                                    <td class="text-end pe-4"><span class="badge bg-label-secondary">{{ $core['server_addr'] }}</span></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Gateway Interface')</span></td>
                                    <td class="text-end pe-4"><code class="small">{{ $core['gateway_interface'] }}</code></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Root Directory')</span></td>
                                    <td class="text-end pe-4"><code class="tiny text-muted">{{ $core['document_root'] }}</code></td>
                                </tr>
                                <tr>
                                    <td class="ps-4 py-3"><span class="text-muted small fw-bold text-uppercase">@lang('Execution Path')</span></td>
                                    <td class="text-end pe-4"><code class="tiny text-muted">{{ $core['script_filename'] }}</code></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 4. PHP Runtime Config Grid ── --}}
        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-white py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="lab la-php me-2 text-info"></i>@lang('PHP Dynamic Configuration')</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 border rounded bg-lighter">
                                <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Max Upload')</label>
                                <span class="fw-bold text-dark">{{ $phpConfig['upload_max_filesize'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-lighter">
                                <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Post Size')</label>
                                <span class="fw-bold text-dark">{{ $phpConfig['post_max_size'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-lighter">
                                <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Execution Timeout')</label>
                                <span class="fw-bold text-dark">{{ $phpConfig['max_execution_time'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 border rounded bg-lighter">
                                <label class="d-block text-muted tiny fw-bold text-uppercase mb-1">@lang('Input Variables')</label>
                                <span class="fw-bold text-dark">{{ $phpConfig['max_input_vars'] }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded bg-label-info">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <label class="d-block text-info tiny fw-bold text-uppercase mb-1">@lang('Timezone Sync')</label>
                                        <span class="fw-bold fs-6">{{ $phpConfig['date_timezone'] }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="badge {{ $phpConfig['opcache_enabled'] === __('Yes') ? 'bg-success' : 'bg-secondary' }}">OPcache: {{ $phpConfig['opcache_enabled'] }}</span>
                                        <span class="badge {{ $phpConfig['display_errors'] === __('On') ? 'bg-warning' : 'bg-success' }}">Errors: {{ $phpConfig['display_errors'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 5. Active Transactional Info ── --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="las la-exchange-alt me-2 text-info"></i>@lang('Live Request Metadata')</h6>
                    <span class="badge bg-label-info px-3">@lang('Method'): {{ $request['request_method'] }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-4 border-end">
                            <div class="mb-3">
                                <label class="text-muted tiny fw-bold text-uppercase d-block">@lang('Source IP Address')</label>
                                <span class="fw-bold fs-5 text-primary">{{ $request['remote_addr'] }}</span>
                            </div>
                            <div>
                                <label class="text-muted tiny fw-bold text-uppercase d-block">@lang('Security Protocol')</label>
                                @if($request['https'] === __('Yes'))
                                    <span class="badge bg-label-success px-3"><i class="las la-lock me-1"></i> @lang('SSL Encrypted')</span>
                                @else
                                    <span class="badge bg-label-danger px-3"><i class="las la-lock-open me-1"></i> @lang('Plain HTTP')</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="bg-lighter p-3 rounded border">
                                <label class="text-muted tiny fw-bold text-uppercase d-block mb-1">@lang('User Agent Fingerprint')</label>
                                <code class="small text-break">{{ $request['http_user_agent'] }}</code>
                            </div>
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted small fw-bold text-uppercase">@lang('Request URI')</span>
                                <code class="small">{{ $request['request_uri'] }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($securityHeaders))
        {{-- ── 6. Header Security Audit ── --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-white py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="las la-shield-alt me-2 text-info"></i>@lang('Security Header Pulse')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless mb-0 align-middle">
                            <tbody>
                                @foreach($securityHeaders as $hKey => $hVal)
                                <tr class="border-bottom">
                                    <td class="ps-4 py-3"><code class="small text-primary fw-bold">{{ str_replace('HTTP_', '', $hKey) }}</code></td>
                                    <td class="pe-4 text-end text-muted tiny">{{ $hVal }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(!empty($loadedModules))
        {{-- ── 7. Module Ecosystem ── --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-white py-3 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold"><i class="las la-puzzle-piece me-2 text-info"></i>@lang('Apache Loaded Module Matrix')</h6>
                    <span class="badge bg-label-info px-3">{{ count($loadedModules) }} @lang('Modules Active')</span>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($loadedModules as $mod)
                            <span class="badge bg-label-secondary border py-2 px-3 small">{{ $mod }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
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
    .progress { background-color: rgba(105, 108, 255, 0.1); border-radius: 10px; }
</style>
@endpush

