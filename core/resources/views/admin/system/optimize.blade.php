@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        {{-- Quick Links --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.system.info') }}" class="btn btn-outline--primary btn-sm"><i class="las la-info-circle me-1"></i>@lang('System Info')</a>
                        <a href="{{ route('admin.system.server.info') }}" class="btn btn-outline--primary btn-sm"><i class="las la-server me-1"></i>@lang('Server Details')</a>
                    </div>
                    <small class="text-muted">@lang('No manual edit needed. All actions run automatically.')</small>
                </div>
            </div>
        </div>

        {{-- Cache Status --}}
        <div class="card mb-4">
            <div class="card-header bg--primary">
                <h5 class="text-white mb-0"><i class="las la-chart-pie me-2"></i>@lang('Cache Status')</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="las la-database text--primary me-3" style="font-size: 2rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Application Cache')</span>
                                <h5 class="mb-0">{{ $cacheSize ?? 'N/A' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="las la-file-alt text--primary me-3" style="font-size: 2rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Compiled Views')</span>
                                <h5 class="mb-0">{{ $viewSize ?? 'N/A' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Safe Clear - Recommended --}}
        <div class="card mb-4 border border-success">
            <div class="card-header bg-success bg-opacity-10">
                <h5 class="mb-0"><i class="las la-check-double me-2 text-success"></i><span class="badge bg-success me-2">@lang('Recommended')</span>@lang('Safe Clear')</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">@lang('Clears application and view cache. Logo, favicon and settings will reload correctly. Safe for daily use.')</p>
                <ul class="list-unstyled small mb-3">
                    <li><i class="las la-check text-success me-2"></i>@lang('Application cache')</li>
                    <li><i class="las la-check text-success me-2"></i>@lang('Compiled views')</li>
                    <li><i class="las la-check text-success me-2"></i>@lang('GeneralSetting cache re-warmed')</li>
                </ul>
                <a href="{{ route('admin.system.optimize.clear') }}" class="btn btn--success">
                    <i class="las la-broom me-2"></i>@lang('Safe Clear Cache')
                </a>
            </div>
        </div>

        {{-- Individual Clear Options --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-tasks me-2"></i>@lang('Individual Clear')</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">@lang('Clear specific cache types when needed.')</p>
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="cache-item-card p-3 border rounded h-100">
                            <h6 class="mb-2">@lang('Config')</h6>
                            <p class="small text-muted mb-2">@lang('Configuration cache')</p>
                            <a href="{{ route('admin.system.optimize.clear.config') }}" class="btn btn-outline-secondary btn-sm w-100">@lang('Clear')</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="cache-item-card p-3 border rounded h-100">
                            <h6 class="mb-2">@lang('Route')</h6>
                            <p class="small text-muted mb-2">@lang('Route definitions')</p>
                            <a href="{{ route('admin.system.optimize.clear.route') }}" class="btn btn-outline-secondary btn-sm w-100">@lang('Clear')</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="cache-item-card p-3 border rounded h-100">
                            <h6 class="mb-2">@lang('View')</h6>
                            <p class="small text-muted mb-2">@lang('Compiled Blade views')</p>
                            <a href="{{ route('admin.system.optimize.clear.view') }}" class="btn btn-outline-secondary btn-sm w-100">@lang('Clear')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Full Clear - Advanced --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-cog me-2"></i>@lang('Full Clear') <small class="text-muted">(@lang('Advanced'))</small></h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">@lang('Clears config, route, view, and compiled files. Use after code or config changes.')</p>
                <ul class="list-unstyled small mb-3">
                    <li><i class="las la-minus me-2"></i>@lang('Config cache')</li>
                    <li><i class="las la-minus me-2"></i>@lang('Route cache')</li>
                    <li><i class="las la-minus me-2"></i>@lang('View cache')</li>
                    <li><i class="las la-minus me-2"></i>@lang('Compiled services & packages')</li>
                </ul>
                <a href="{{ route('admin.system.optimize.clear.full') }}" class="btn btn-outline-secondary">
                    <i class="las la-cog me-2"></i>@lang('Full Clear')
                </a>
            </div>
        </div>

        {{-- Advanced Cleanup: Trash + Temp --}}
        <div class="card mb-4 border border-warning">
            <div class="card-header bg-warning bg-opacity-10">
                <h5 class="mb-0"><i class="las la-trash-restore me-2 text-warning"></i>@lang('Advanced Cleanup') <small class="text-muted">(@lang('Trash + Temp'))</small></h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    @lang('Removes old deleted files from trash (after :days days) and clears temp/cache files. Safe to run when disk usage grows.', ['days' => $retentionDays])
                </p>
                <ul class="list-unstyled small mb-3">
                    <li><i class="las la-check text-warning me-2"></i>@lang('Trashed uploads older than retention are permanently deleted')</li>
                    <li><i class="las la-check text-warning me-2"></i>@lang('Framework cache and compiled views cleaned')</li>
                    <li><i class="las la-check text-warning me-2"></i>@lang('Daily cron also runs this automatically')</li>
                </ul>
                <form action="{{ route('admin.system.optimize.retention') }}" method="POST" class="row g-2 align-items-end mb-3">
                    @csrf
                    <div class="col-md-6 col-lg-4">
                        <label for="file_retention_days" class="form-label mb-1">@lang('Auto delete trashed uploads after')</label>
                        <select name="file_retention_days" id="file_retention_days" class="form-select form-select-sm">
                            <option value="0" {{ $retentionDays === 0 ? 'selected' : '' }}>@lang('Manual only – do not auto delete')</option>
                            <option value="7" {{ $retentionDays === 7 ? 'selected' : '' }}>7 @lang('days')</option>
                            <option value="15" {{ $retentionDays === 15 ? 'selected' : '' }}>15 @lang('days')</option>
                            <option value="30" {{ $retentionDays === 30 ? 'selected' : '' }}>30 @lang('days')</option>
                            <option value="60" {{ $retentionDays === 60 ? 'selected' : '' }}>60 @lang('days')</option>
                        </select>
                        <div class="form-text small">@lang('0 = keep in trash until you run cleanup manually.')</div>
                    </div>
                    <div class="col-md-3 col-lg-2">
                        <button type="submit" class="btn btn-sm btn-warning w-100 mt-2 mt-md-0">
                            <i class="las la-save me-1"></i>@lang('Save Setting')
                        </button>
                    </div>
                </form>
                <a href="{{ route('admin.system.optimize.cleanup') }}" class="btn btn-outline-warning">
                    <i class="las la-trash-restore me-2"></i>@lang('Run Advanced Cleanup')
                </a>
            </div>
        </div>

        {{-- Optimize for Production --}}
        <div class="card mb-4 border border-info">
            <div class="card-header bg-info bg-opacity-10">
                <h5 class="mb-0"><i class="las la-rocket me-2 text-info"></i>@lang('Optimize for Production')</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">@lang('Caches config, routes and views for better performance. Run this after deploying to server.')</p>
                <ul class="list-unstyled small mb-3">
                    <li><i class="las la-check text-info me-2"></i>@lang('Creates config cache')</li>
                    <li><i class="las la-check text-info me-2"></i>@lang('Creates route cache')</li>
                    <li><i class="las la-check text-info me-2"></i>@lang('Compiles views for faster loading')</li>
                </ul>
                <a href="{{ route('admin.system.optimize.run') }}" class="btn btn--primary">
                    <i class="las la-rocket me-2"></i>@lang('Run Optimize')
                </a>
            </div>
        </div>

        {{-- Deployment Note --}}
        <div class="alert alert-info mb-0">
            <h6 class="alert-heading"><i class="las la-info-circle me-1"></i>@lang('When Deploying to Server')</h6>
            <p class="mb-0 small">@lang('1. Update .env with server values. 2. Run "Full Clear" once. 3. Run "Optimize for Production". 4. No manual editing needed - all actions work automatically.')</p>
        </div>
    </div>
</div>
@endsection

@push('style')
<style>
.cache-item-card:hover { background: rgba(0,0,0,.02); }
</style>
@endpush
