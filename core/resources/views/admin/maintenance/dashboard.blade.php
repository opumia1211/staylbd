@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        {{-- Quick Actions --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('admin.maintenance.clean.temp.cache') }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Clear all temp and cache?') }}');">
                            @csrf
                            <button type="submit" class="btn btn--success btn-sm">
                                <i class="las la-broom me-1"></i>@lang('Clean Temp & Cache')
                            </button>
                        </form>
                        <a href="{{ route('admin.system.optimize') }}" class="btn btn-outline--primary btn-sm">
                            <i class="las la-tasks me-1"></i>@lang('Cache Settings')
                        </a>
                        <a href="{{ route('admin.system.info') }}" class="btn btn-outline--primary btn-sm">
                            <i class="las la-info-circle me-1"></i>@lang('System Info')
                        </a>
                    </div>
                    <button type="button" class="btn btn--primary btn-sm" data-bs-toggle="tooltip" title="@lang('Use Cursor AI to analyze and optimize based on suggestions')">
                        <i class="las la-robot me-1"></i>@lang('Analyze & Optimize')
                    </button>
                </div>
            </div>
        </div>

        {{-- Widget 1: Disk Usage --}}
        <div class="card mb-4">
            <div class="card-header bg--primary">
                <h5 class="text-white mb-0"><i class="las la-hdd me-2"></i>@lang('Disk Usage')</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded h-100">
                            <i class="las la-folder-open text--primary me-3" style="font-size: 2rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Storage')</span>
                                <h5 class="mb-0">{{ $diskUsage['storage_size'] ?? 'N/A' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded h-100">
                            <i class="las la-globe text--primary me-3" style="font-size: 2rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Public')</span>
                                <h5 class="mb-0">{{ $diskUsage['public_size'] ?? 'N/A' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded h-100">
                            <i class="las la-file-alt text--primary me-3" style="font-size: 2rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Logs')</span>
                                <h5 class="mb-0">{{ $diskUsage['log_size'] ?? 'N/A' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded h-100">
                            <i class="las la-tachometer-alt text--primary me-3" style="font-size: 2rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Disk Free')</span>
                                <h5 class="mb-0">{{ $diskUsage['disk_free'] ?? 'N/A' }}{{ isset($diskUsage['usage_percent']) ? ' (' . $diskUsage['usage_percent'] . '% used)' : '' }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 2: Database Health --}}
        <div class="card mb-4">
            <div class="card-header bg-info">
                <h5 class="text-white mb-0"><i class="las la-database me-2"></i>@lang('Database Health')</h5>
            </div>
            <div class="card-body">
                @if($dbHealth['connected'] ?? false)
                    <p class="text-success mb-3"><i class="las la-check-circle me-1"></i>@lang('Connected') - {{ $dbHealth['name'] ?? 'N/A' }} ({{ $dbHealth['total_size'] ?? 'N/A' }})</p>
                    @if(!empty($dbHealth['tables']))
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>@lang('Table')</th>
                                        <th>@lang('Rows')</th>
                                        <th>@lang('Size')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dbHealth['tables'] as $t)
                                        <tr>
                                            <td><code>{{ $t['name'] }}</code></td>
                                            <td>{{ number_format($t['rows']) }}</td>
                                            <td>{{ $t['size'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <p class="text-muted mb-0">@lang('Not connected'){{ isset($dbHealth['error']) ? ': ' . $dbHealth['error'] : '' }}</p>
                @endif
            </div>
        </div>

        {{-- Widget 3: Cache Status --}}
        <div class="card mb-4">
            <div class="card-header bg-success">
                <h5 class="text-white mb-0"><i class="las la-chart-pie me-2"></i>@lang('Cache Status')</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="las la-database text-success me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Framework Cache')</span>
                                <h5 class="mb-0">{{ $cacheStatus['cache_size'] ?? 'N/A' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="las la-file-alt text-success me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Compiled Views')</span>
                                <h5 class="mb-0">{{ $cacheStatus['view_size'] ?? 'N/A' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="las la-clock text-success me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <span class="text-muted small">@lang('Last Cleared')</span>
                                <h5 class="mb-0 small">{{ $cacheStatus['last_cleared'] ?? __('Never') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widget 4: Media & Uploads --}}
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="text-dark mb-0"><i class="las la-images me-2"></i>@lang('Media & Uploads')</h5>
            </div>
            <div class="card-body">
                <p class="mb-3"><strong>@lang('Total:')</strong> {{ $mediaUploads['total_size'] ?? 'N/A' }}</p>
                @if(!empty($mediaUploads['breakdown']))
                    <div class="row g-2">
                        @foreach($mediaUploads['breakdown'] as $key => $size)
                            <div class="col-6 col-md-4 col-lg-3">
                                <span class="badge bg-light text-dark">{{ $key }}: {{ $size }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="alert alert-info mb-0">
            <h6 class="alert-heading"><i class="las la-info-circle me-1"></i>@lang('Maintenance Dashboard')</h6>
            <p class="mb-0 small">@lang('Use the buttons above to clean temp/cache, or run the scheduled command:') <code>php artisan maintenance:clean-temp-cache</code></p>
        </div>
    </div>
</div>
@endsection
