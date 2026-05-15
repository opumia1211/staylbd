@extends('admin.layouts.app')

@section('panel')
<div class="container-xxl flex-grow-1 container-p-y p-0">
    <div class="row g-4">
        {{-- ── 1. Tactical Command Center ── --}}
        <div class="col-12">
            <div class="card border-0 shadow-none bg-label-success overflow-hidden">
                <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white p-2 rounded-circle shadow-sm" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            <i class="las la-broom fs-3"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-success">@lang('System Optimization Matrix')</h5>
                            <p class="text-muted small mb-0">@lang('Streamline application performance and clear infrastructure congestion.')</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.system.info') }}" class="btn btn-white btn-sm shadow-sm border px-3">
                            <i class="las la-info-circle me-1 text-success"></i> @lang('System Info')
                        </a>
                        <a href="{{ route('admin.system.server.info') }}" class="btn btn-white btn-sm shadow-sm border px-3">
                            <i class="las la-server me-1 text-success"></i> @lang('Server Details')
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2. Cache Load Indicators ── --}}
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm bg-white p-4 h-100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-label-primary p-3 rounded">
                                <i class="las la-database fs-2"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">@lang('Application Layer Cache')</h6>
                                <h3 class="mb-0 fw-bold text-primary">{{ $cacheSize ?? 'N/A' }}</h3>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 45%"></div>
                        </div>
                        <p class="text-muted tiny mt-2 mb-0">@lang('Stored data objects and internal logic state.')</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm bg-white p-4 h-100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-label-info p-3 rounded">
                                <i class="las la-file-alt fs-2"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">@lang('Compiled Interface Views')</h6>
                                <h3 class="mb-0 fw-bold text-info">{{ $viewSize ?? 'N/A' }}</h3>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 30%"></div>
                        </div>
                        <p class="text-muted tiny mt-2 mb-0">@lang('Pre-rendered Blade templates for rapid delivery.')</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 3. Action Matrix: Safe & Production ── --}}
        <div class="col-xl-8 col-lg-7">
            <div class="row g-4">
                {{-- Recommended Action --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm border-start border-success border-5">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-label-success p-2 rounded">
                                        <i class="las la-shield-alt fs-3"></i>
                                    </div>
                                    <h5 class="mb-0 fw-bold">@lang('Standard Tactical Optimization')</h5>
                                </div>
                                <span class="badge bg-label-success rounded-pill px-3">@lang('Highly Recommended')</span>
                            </div>
                            <p class="text-muted small mb-4">@lang('Performs a safe cleanup of framework and view caches. Re-synchronizes settings to ensure zero disruption to core features like branding and site configuration.')</p>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4"><span class="text-muted tiny fw-bold"><i class="las la-check text-success me-1"></i>@lang('App Logic Clean')</span></div>
                                <div class="col-md-4"><span class="text-muted tiny fw-bold"><i class="las la-check text-success me-1"></i>@lang('View Compiling Reset')</span></div>
                                <div class="col-md-4"><span class="text-muted tiny fw-bold"><i class="las la-check text-success me-1"></i>@lang('Setting Matrix Refresh')</span></div>
                            </div>
                            <a href="{{ route('admin.system.optimize.clear') }}" class="btn btn-success shadow-sm px-4">
                                <i class="las la-broom me-2"></i> @lang('Execute Safe Cleanup')
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Individual Control Panel --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom bg-white py-3 px-4">
                            <h6 class="mb-0 fw-bold"><i class="las la-tasks me-2 text-primary"></i>@lang('Individual Module Controls')</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="p-3 border rounded h-100 bg-lighter text-center">
                                        <h6 class="fw-bold mb-1">@lang('Config Stack')</h6>
                                        <p class="tiny text-muted mb-3">@lang('Internal .env and config file mapping.')</p>
                                        <a href="{{ route('admin.system.optimize.clear.config') }}" class="btn btn-white btn-sm border w-100">@lang('Clear')</a>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded h-100 bg-lighter text-center">
                                        <h6 class="fw-bold mb-1">@lang('Route Map')</h6>
                                        <p class="tiny text-muted mb-3">@lang('URL routing and middleware definitions.')</p>
                                        <a href="{{ route('admin.system.optimize.clear.route') }}" class="btn btn-white btn-sm border w-100">@lang('Clear')</a>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 border rounded h-100 bg-lighter text-center">
                                        <h6 class="fw-bold mb-1">@lang('View Engine')</h6>
                                        <p class="tiny text-muted mb-3">@lang('Pre-compiled HTML interface files.')</p>
                                        <a href="{{ route('admin.system.optimize.clear.view') }}" class="btn btn-white btn-sm border w-100">@lang('Clear')</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Full System Flush --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-label-secondary">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <i class="las la-cog fs-1 opacity-50"></i>
                                <div>
                                    <h5 class="mb-1 fw-bold">@lang('Total Infrastructure Flush')</h5>
                                    <p class="text-muted small mb-0">@lang('Clears all compiled artifacts. Only use during major version deployments.')</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.system.optimize.clear.full') }}" class="btn btn-secondary shadow-sm px-4">
                                <i class="las la-sync-alt me-2"></i> @lang('Full Flush')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 4. Strategic Maintenance ── --}}
        <div class="col-xl-4 col-lg-5">
            <div class="row g-4">
                {{-- Production Boost --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-primary text-white overflow-hidden" style="background: linear-gradient(135deg, #696cff 0%, #3f42b5 100%);">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <i class="las la-rocket fs-1"></i>
                                <h5 class="mb-0 fw-bold text-white">@lang('Production Warp')</h5>
                            </div>
                            <p class="small text-white opacity-75 mb-4">@lang('Compiles and caches config, routes, and views into memory for maximum server responsiveness.')</p>
                            <a href="{{ route('admin.system.optimize.run') }}" class="btn btn-white btn-sm shadow-sm px-4 w-100">
                                <i class="las la-bolt me-1 text-primary"></i> @lang('Optimize Cluster')
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Advanced Cleanup --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm border-top border-warning border-5">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="mb-0 fw-bold"><i class="las la-trash-restore me-2 text-warning"></i>@lang('Data Retention')</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('admin.system.optimize.retention') }}" method="POST" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label class="text-muted tiny fw-bold text-uppercase d-block mb-1">@lang('Auto-Purge Cycles')</label>
                                    <select name="file_retention_days" class="form-select border-warning-subtle bg-light-warning">
                                        <option value="0" {{ $retentionDays === 0 ? 'selected' : '' }}>@lang('Manual Manual Only')</option>
                                        <option value="7" {{ $retentionDays === 7 ? 'selected' : '' }}>7 @lang('Days')</option>
                                        <option value="15" {{ $retentionDays === 15 ? 'selected' : '' }}>15 @lang('Days')</option>
                                        <option value="30" {{ $retentionDays === 30 ? 'selected' : '' }}>30 @lang('Days')</option>
                                        <option value="60" {{ $retentionDays === 60 ? 'selected' : '' }}>60 @lang('Days')</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-warning btn-sm w-100 shadow-sm">
                                    <i class="las la-save me-1"></i> @lang('Lock Parameter')
                                </button>
                            </form>
                            <hr class="my-3 opacity-25">
                            <p class="text-muted tiny mb-3">@lang('Performs deep cleanup of orphaned assets and framework temp files.')</p>
                            <a href="{{ route('admin.system.optimize.cleanup') }}" class="btn btn-label-warning btn-sm w-100">
                                <i class="las la-broom me-1"></i> @lang('Run Deep Cleanup')
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Tactical Note --}}
                <div class="col-12">
                    <div class="alert alert-info border-0 shadow-sm mb-0">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="las la-info-circle fs-4"></i>
                            <h6 class="mb-0 alert-heading fw-bold small">@lang('Cluster Deployment Protocol')</h6>
                        </div>
                        <ol class="small mb-0 ps-3">
                            <li class="mb-1">@lang('Sync .env credentials')</li>
                            <li class="mb-1">@lang('Execute Full Flush once')</li>
                            <li>@lang('Trigger Production Warp')</li>
                        </ol>
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
    .bg-light-warning { background-color: #fffaf0 !important; }
    .tiny { font-size: 0.7rem !important; }
    .progress { background-color: rgba(0,0,0,0.05); border-radius: 10px; }
    .btn-white { background: #fff; color: #444; }
    .btn-label-warning { background: #fff2d6; color: #ffab00; }
    .btn-label-warning:hover { background: #ffab00; color: #fff; }
</style>
@endpush
