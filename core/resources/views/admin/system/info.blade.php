@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-12">
        {{-- Quick Actions --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.system.server.info') }}" class="btn btn-outline--primary btn-sm"><i class="las la-server me-1"></i>@lang('Server Details')</a>
                        <a href="{{ route('admin.system.optimize') }}" class="btn btn-outline--primary btn-sm"><i class="las la-broom me-1"></i>@lang('Clear Cache')</a>
                        <a href="{{ route('admin.system.info.export') }}" class="btn btn-outline--primary btn-sm" target="_blank"><i class="las la-download me-1"></i>@lang('Export JSON')</a>
                    </div>
                    <small class="text-muted">@lang('All data is auto-detected. No manual edit needed when deploying.')</small>
                </div>
            </div>
        </div>

        {{-- Deployment Note --}}
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading"><i class="las la-info-circle me-1"></i>@lang('About This Page')</h6>
            <p class="mb-0 small">@lang('This page shows live system information. When you deploy to a server, update your .env file (APP_URL, DB_*, etc.) and this page will automatically show the correct server values. No admin editing or database storage is needed.')</p>
        </div>

        {{-- Application Info --}}
        <div class="card mb-4">
            <div class="card-header bg--primary">
                <h5 class="text-white mb-0"><i class="las la-mobile-alt me-2"></i>@lang('Application Information')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 40%">{{ systemDetails()['name'] }} @lang('Version')</td>
                                <td><span class="badge bg--primary">{{ systemDetails()['version'] }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Build Version')</td>
                                <td>{{ systemDetails()['build_version'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Laravel Version')</td>
                                <td>{{ $laravelVersion }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Environment')</td>
                                <td><span class="badge {{ $env === 'production' ? 'bg-success' : 'bg-warning' }}">{{ $env }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Debug Mode')</td>
                                <td><span class="badge {{ $debug ? 'bg-danger' : 'bg-success' }}">{{ $debug ? __('On') : __('Off') }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('App URL')</td>
                                <td><code>{{ $url }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Timezone')</td>
                                <td>{{ $timeZone }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Base Path')</td>
                                <td><code class="small">{{ $basePath }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('HTTPS')</td>
                                <td>
                                    @if($isHttps)
                                        <span class="badge bg-success"><i class="las la-lock"></i> @lang('Enabled')</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="las la-lock-open"></i> @lang('Disabled')</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Active Template')</td>
                                <td><span class="badge bg-info">{{ $activeTemplate }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Drivers & Config --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-cog me-2"></i>@lang('Drivers & Config')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 40%">@lang('Cache Driver')</td>
                                <td><code>{{ $cacheDriver }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Session Driver')</td>
                                <td><code>{{ $sessionDriver }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Queue Driver')</td>
                                <td><code>{{ $queueDriver }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Mail Driver')</td>
                                <td><code>{{ $mailDriver }}</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PHP & Server --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="lab la-php me-2"></i>@lang('PHP & Server')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 40%">@lang('PHP Version')</td>
                                <td><span class="badge bg-info">{{ $phpVersion }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Server OS')</td>
                                <td><code>{{ $serverOs }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Memory Limit')</td>
                                <td>{{ $memoryLimit }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Upload Max Size')</td>
                                <td>{{ $uploadMax }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Post Max Size')</td>
                                <td>{{ $postMax }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Max Execution Time')</td>
                                <td>{{ $maxExecutionTime }} @lang('seconds')</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Disk Free')</td>
                                <td>{{ $diskFreeFormatted }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Disk Total')</td>
                                <td>{{ $diskTotalFormatted }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Database --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-database me-2"></i>@lang('Database')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 40%">@lang('Driver')</td>
                                <td><span class="badge bg-secondary">{{ $dbDriver }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Database Name')</td>
                                <td><code>{{ $dbName }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Connection')</td>
                                <td>
                                    @if($dbConnected)
                                        <span class="badge bg-success"><i class="las la-check"></i> @lang('Connected')</span>
                                    @else
                                        <span class="badge bg-danger"><i class="las la-times"></i> @lang('Not Connected')</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Server Version')</td>
                                <td><code class="small">{{ $dbVersion ?? 'N/A' }}</code></td>
                            </tr>
                            @if($dbTablesCount !== null)
                            <tr>
                                <td class="fw-bold">@lang('Tables Count')</td>
                                <td>{{ $dbTablesCount }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Permissions & Directories --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-folder-open me-2"></i>@lang('Permissions & Directories')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 40%">@lang('Storage')</td>
                                <td>
                                    @if($storageWritable)
                                        <span class="badge bg-success"><i class="las la-check"></i> @lang('Writable')</span>
                                    @else
                                        <span class="badge bg-danger"><i class="las la-times"></i> @lang('Not Writable')</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Bootstrap Cache')</td>
                                <td>
                                    @if($bootstrapCacheWritable)
                                        <span class="badge bg-success"><i class="las la-check"></i> @lang('Writable')</span>
                                    @else
                                        <span class="badge bg-danger"><i class="las la-times"></i> @lang('Not Writable')</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Storage Path')</td>
                                <td><code class="small">{{ $storagePathReal ?: storage_path() }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Log File Size')</td>
                                <td>{{ $logSize }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PHP Extensions --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="las la-puzzle-piece me-2"></i>@lang('PHP Extensions')</h5>
                @if(count($missingExtensions) > 0)
                    <span class="badge bg-danger">@lang('Missing') {{ count($missingExtensions) }} @lang('required')</span>
                @else
                    <span class="badge bg-success">@lang('All required extensions loaded')</span>
                @endif
            </div>
            <div class="card-body">
                @if(count($missingExtensions) > 0)
                    <div class="alert alert-warning mb-3">
                        <strong>@lang('Missing required extensions'):</strong>
                        <code>{{ implode(', ', $missingExtensions) }}</code>
                    </div>
                @endif
                <div class="d-flex flex-wrap gap-1">
                    @foreach($phpExtensions as $ext)
                        @php $isRequired = in_array($ext, $requiredExtensions); @endphp
                        <span class="badge {{ $isRequired ? 'bg-success' : 'bg-light text-dark' }} mb-1">{{ $ext }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- System Health Summary --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-heartbeat me-2"></i>@lang('System Health')</h5>
            </div>
            <div class="card-body">
                @php
                    $warnings = [];
                    if (!$storageWritable) $warnings[] = __('Storage directory is not writable');
                    if (!$bootstrapCacheWritable) $warnings[] = __('Bootstrap cache is not writable');
                    if (count($missingExtensions) > 0) $warnings[] = __('Some required PHP extensions are missing');
                    if ($debug && $env === 'production') $warnings[] = __('Debug mode is ON in production');
                @endphp
                @if(count($warnings) > 0)
                    <div class="alert alert-warning mb-0">
                        <h6 class="alert-heading"><i class="las la-exclamation-triangle me-1"></i>@lang('Attention Needed')</h6>
                        <ul class="mb-0">
                            @foreach($warnings as $w)
                                <li>{{ $w }}</li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div class="alert alert-success mb-0">
                        <i class="las la-check-circle me-2"></i>@lang('All systems operational. No critical issues detected.')
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
