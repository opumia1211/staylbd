@extends($activeTemplate . 'layouts.master')
@section('dashboard_page_title')
    @include($activeTemplate . 'partials.dashboard_page_header', ['title' => __('Transactions History'), 'subtitle' => __('Your payment and order transaction history')])
@endsection
@section('content')
    <div class="row justify-content-center card-wrapper">
        <div class="col-md-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn--base cmn--btn showFilterBtn btn-sm">@include($activeTemplate . 'partials.icon', ['name' => 'filter']) @lang('Filter')</button>
            </div>
            <div class="card custom--card responsive-filter-card mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Transaction Number')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control form--control">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--base cmn--btn w-100">@include($activeTemplate . 'partials.icon', ['name' => 'filter']) @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card custom--card cmn--card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table custom--table cmn--table">
                            <thead>
                                <tr>
                                    <th>@lang('Trx')</th>
                                    <th>@lang('Transacted')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Detail')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trx)
                                    <tr>
                                        <td><strong>{{ $trx->trx }}</strong></td>
                                        <td>
                                            {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                        </td>
                                        <td class="budget">
                                            <span class="fw-bold  text--success">
                                                {{ showAmount($trx->amount) }} {{ __($general->cur_text) }}
                                            </span>
                                        </td>
                                        <td>{{ __($trx->details) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($transactions->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($transactions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
