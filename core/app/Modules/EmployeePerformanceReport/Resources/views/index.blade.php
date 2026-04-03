@extends('admin.layouts.master')

@section('content')
    <div class="page-wrapper default-version">
        @include('admin.partials.sidenav')
        <div class="admin-sidebar-overlay" id="adminSidebarOverlay" aria-hidden="true"></div>
        @include('admin.partials.topnav')
        <div class="body-wrapper">
            <div class="bodywrapper__inner">
                @include('admin.partials.breadcrumb')

                <div class="row mb-4">
                    <div class="col-12">
                        <h4 class="mb-1">{{ $pageTitle }}</h4>
                        <p class="text-muted mb-0">@lang('Orders handled and actions by staff in date range.')</p>
                    </div>
                </div>

                <form method="GET" class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">@lang('From')</label>
                                <input type="date" class="form-control" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">@lang('To')</label>
                                <input type="date" class="form-control" name="date_to" value="{{ $dateTo->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn--primary">@lang('Apply')</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table--light style--two">
                                <thead class="table-light">
                                    <tr>
                                        <th>@lang('Staff')</th>
                                        <th>@lang('Username')</th>
                                        <th class="text-center">@lang('Orders Handled')</th>
                                        <th class="text-center">@lang('Actions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($performers as $p)
                                        <tr>
                                            <td>{{ $p->admin->name ?? '—' }}</td>
                                            <td>{{ $p->admin->username ?? '—' }}</td>
                                            <td class="text-center"><strong>{{ $p->orders_handled }}</strong></td>
                                            <td class="text-center">{{ $p->action_count }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">@lang('No activity in this date range.')</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
