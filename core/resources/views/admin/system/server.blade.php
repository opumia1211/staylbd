@extends('admin.layouts.app')
@section('panel')
<div class="row mb-none-30">
    <div class="col-12">
        {{-- Quick Actions --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.system.info') }}" class="btn btn-outline--primary btn-sm"><i class="las la-info-circle me-1"></i>@lang('Application Info')</a>
                        <a href="{{ route('admin.system.optimize') }}" class="btn btn-outline--primary btn-sm"><i class="las la-broom me-1"></i>@lang('Clear Cache')</a>
                        <a href="{{ route('admin.system.info.export') }}" class="btn btn-outline--primary btn-sm" target="_blank"><i class="las la-download me-1"></i>@lang('Export JSON')</a>
                    </div>
                    <small class="text-muted"><i class="las la-sync me-1"></i>@lang('Auto-detected. No manual edit needed when deploying.')</small>
                </div>
            </div>
        </div>

        {{-- Deployment Note --}}
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading"><i class="las la-info-circle me-1"></i>@lang('About This Page')</h6>
            <p class="mb-0 small">@lang('Server details are read live from the environment. When you deploy to a server, update your .env file (APP_URL, DB_*, etc.) and this page will automatically reflect the correct values. No admin editing or database storage required.')</p>
        </div>

        {{-- Application Context --}}
        <div class="card mb-4">
            <div class="card-header bg--primary">
                <h5 class="text-white mb-0"><i class="las la-mobile-alt me-2"></i>@lang('Application Context')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 38%">@lang('App URL')</td>
                                <td><code>{{ $appContext['app_url'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Environment')</td>
                                <td><span class="badge {{ $appContext['app_env'] === 'production' ? 'bg-success' : 'bg-warning' }}">{{ $appContext['app_env'] }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Debug Mode')</td>
                                <td><span class="badge {{ $appContext['app_debug'] === __('On') ? 'bg-danger' : 'bg-success' }}">{{ $appContext['app_debug'] }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Laravel Version')</td>
                                <td><span class="badge bg-info">{{ $appContext['laravel_version'] }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Core Server Info --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-server me-2"></i>@lang('Core Server Information')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 38%">@lang('PHP Version')</td>
                                <td><span class="badge bg-info">{{ $core['php_version'] }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Server Software')</td>
                                <td><code>{{ $core['server_software'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Server IP Address')</td>
                                <td><code>{{ $core['server_addr'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Server Port')</td>
                                <td>{{ $core['server_port'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Server Protocol')</td>
                                <td>{{ $core['server_protocol'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('HTTP Host')</td>
                                <td><code>{{ $core['http_host'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Server Name')</td>
                                <td>{{ $core['server_name'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Document Root')</td>
                                <td><code class="small">{{ $core['document_root'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Gateway Interface')</td>
                                <td><code>{{ $core['gateway_interface'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Script Filename')</td>
                                <td><code class="small">{{ $core['script_filename'] }}</code></td>
                            </tr>
                            @if($core['server_admin'] !== 'N/A')
                            <tr>
                                <td class="fw-bold">@lang('Server Admin')</td>
                                <td><code>{{ $core['server_admin'] }}</code></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Current Request Info --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-exchange-alt me-2"></i>@lang('Current Request Information')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 38%">@lang('Request Method')</td>
                                <td><span class="badge bg-secondary">{{ $request['request_method'] }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Request URI')</td>
                                <td><code class="small">{{ $request['request_uri'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Query String')</td>
                                <td><code class="small">{{ $request['query_string'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Remote Address')</td>
                                <td><code>{{ $request['remote_addr'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Remote Port')</td>
                                <td>{{ $request['remote_port'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('HTTPS')</td>
                                <td>
                                    @if($request['https'] === __('Yes'))
                                        <span class="badge bg-success"><i class="las la-lock"></i> @lang('Yes')</span>
                                    @else
                                        <span class="badge bg-secondary"><i class="las la-lock-open"></i> @lang('No')</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('User Agent')</td>
                                <td><code class="small text-break">{{ $request['http_user_agent'] }}</code></td>
                            </tr>
                            @if($request['http_referer'] !== '-')
                            <tr>
                                <td class="fw-bold">@lang('Referer')</td>
                                <td><code class="small">{{ $request['http_referer'] }}</code></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PHP Runtime Configuration --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="lab la-php me-2"></i>@lang('PHP Runtime Configuration')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 38%">@lang('Memory Limit')</td>
                                <td>{{ $phpConfig['memory_limit'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Upload Max Size')</td>
                                <td>{{ $phpConfig['upload_max_filesize'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Post Max Size')</td>
                                <td>{{ $phpConfig['post_max_size'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Max Execution Time')</td>
                                <td>{{ $phpConfig['max_execution_time'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Max Input Time')</td>
                                <td>{{ $phpConfig['max_input_time'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Max Input Vars')</td>
                                <td>{{ $phpConfig['max_input_vars'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Default Socket Timeout')</td>
                                <td>{{ $phpConfig['default_socket_timeout'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Timezone')</td>
                                <td><code>{{ $phpConfig['date_timezone'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Display Errors')</td>
                                <td><span class="badge {{ $phpConfig['display_errors'] === __('On') ? 'bg-warning' : 'bg-success' }}">{{ $phpConfig['display_errors'] }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('OPcache')</td>
                                <td><span class="badge {{ $phpConfig['opcache_enabled'] === __('Yes') ? 'bg-success' : 'bg-secondary' }}">{{ $phpConfig['opcache_enabled'] }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Resources & System --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-memory me-2"></i>@lang('Resources & System')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold" style="width: 38%">@lang('Server OS')</td>
                                <td><code>{{ $resources['server_os'] }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Server Time')</td>
                                <td>{{ $resources['server_time'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Disk Free')</td>
                                <td>{{ $resources['disk_free'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Disk Used')</td>
                                <td>{{ $resources['disk_used'] }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">@lang('Disk Total')</td>
                                <td>{{ $resources['disk_total'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(!empty($securityHeaders))
        {{-- Security Headers --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="las la-shield-alt me-2"></i>@lang('Security Headers (Request)')</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <tbody>
                            @foreach($securityHeaders as $hKey => $hVal)
                            <tr>
                                <td class="fw-bold" style="width: 38%"><code>{{ $hKey }}</code></td>
                                <td><code class="small">{{ $hVal }}</code></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if(!empty($loadedModules))
        {{-- Apache Loaded Modules --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0"><i class="las la-puzzle-piece me-2"></i>@lang('Apache Loaded Modules')</h5>
                <span class="badge bg-info">{{ count($loadedModules) }} @lang('modules')</span>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-1">
                    @foreach($loadedModules as $mod)
                        <span class="badge bg-light text-dark mb-1">{{ $mod }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush
