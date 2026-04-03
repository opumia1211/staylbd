@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0">@lang('Cached') ({{ config('activity.cache_ttl', 600) }}s TTL)</h5>
            <a href="{{ route('admin.report.activity.all') }}" class="btn btn--primary btn-sm">@lang('Full Timeline')</a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card b-radius--10 border--primary h-100">
                <div class="card-body">
                    <span class="text-muted small">@lang('Today Active Users')</span>
                    <h3 class="mb-0 mt-1">{{ $widgets['today_active_users'] ?? 0 }}</h3>
                    <span class="small">@lang('Unique sessions today')</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card b-radius--10 border--info h-100">
                <div class="card-body">
                    <span class="text-muted small">@lang('Real-time Visitors')</span>
                    <h3 class="mb-0 mt-1">{{ $widgets['realtime_visitors'] ?? 0 }}</h3>
                    <span class="small">@lang('Last 5 minutes')</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card b-radius--10 border--success h-100">
                <div class="card-body">
                    <span class="text-muted small">@lang('Conversion Rate')</span>
                    <h3 class="mb-0 mt-1">{{ $widgets['conversion_rate'] ?? 0 }}%</h3>
                    <span class="small">@lang('Orders / Product views today')</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card b-radius--10 border--warning h-100">
                <div class="card-body">
                    <span class="text-muted small">@lang('Today Orders')</span>
                    <h3 class="mb-0 mt-1">{{ $widgets['today_orders'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card b-radius--10 border--secondary h-100">
                <div class="card-body">
                    <span class="text-muted small">@lang('Abandoned Cart (Est.)')</span>
                    <h3 class="mb-0 mt-1">{{ $widgets['abandoned_cart_count'] ?? 0 }}</h3>
                    <span class="small">@lang('Cart adds - Orders today')</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card b-radius--10 {{ ($widgets['failed_payment_rate'] ?? 0) > 10 ? 'border-danger' : 'border--primary' }} h-100">
                <div class="card-body">
                    <span class="text-muted small">@lang('Failed Payment Rate')</span>
                    <h3 class="mb-0 mt-1">{{ $widgets['failed_payment_rate'] ?? 0 }}%</h3>
                    <span class="small">@lang('Today')</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card b-radius--10 border--danger h-100">
                <div class="card-body">
                    <span class="text-muted small">@lang('Login Failures Today')</span>
                    <h3 class="mb-0 mt-1">{{ $widgets['login_failures_today'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card b-radius--10 border--dark h-100">
                <div class="card-body">
                    <a href="{{ route('admin.report.activity.live') }}" class="text-dark text-decoration-none">
                        <span class="text-muted small">@lang('Live Monitor')</span>
                        <h3 class="mb-0 mt-1">@lang('Last 50') <i class="las la-external-link-alt"></i></h3>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card b-radius--10 h-100">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Zero-Result Searches Today')</h5>
                </div>
                <div class="card-body">
                    <h3 class="mb-0">{{ $widgets['zero_result_searches'] ?? 0 }}</h3>
                    <a href="{{ route('admin.report.search.analytics') }}" class="small">@lang('View Search Reports')</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card b-radius--10 h-100">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Top Search Keywords') (Last 7 Days)</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($widgets['top_search_keywords'] ?? [] as $row)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $row->query ?: '—' }}</span>
                            <span class="badge badge--primary">{{ $row->cnt }}</span>
                        </li>
                        @empty
                        <li class="list-group-item text-muted">@lang('No data')</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card b-radius--10">
                <div class="card-header">
                    <h5 class="mb-0">@lang('Country Wise Activity') (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Country')</th>
                                    <th class="text-end">@lang('Activities')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($widgets['country_wise'] ?? [] as $row)
                                <tr>
                                    <td>{{ $row->country ?: __('Unknown') }}</td>
                                    <td class="text-end">{{ $row->total }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-muted text-center">@lang('No data')</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
