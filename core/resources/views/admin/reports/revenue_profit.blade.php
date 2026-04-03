@extends('admin.layouts.app')

@section('panel')
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-1">{{ $pageTitle }}</h4>
        <p class="text-muted mb-0">@lang('Revenue, cost, expense, returns and profit by date range.')</p>
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

<div class="row g-3">
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">@lang('Total Revenue')</h6>
                <p class="mb-0 fs-4 fw-bold text-success">{{ $general->cur_sym ?? '৳' }}{{ number_format($revenue, 2) }}</p>
                <small class="text-muted">@lang('Delivered orders')</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">@lang('Product Cost')</h6>
                <p class="mb-0 fs-4 fw-bold">{{ $general->cur_sym ?? '৳' }}{{ number_format($productCost, 2) }}</p>
                @if(!\Schema::hasColumn('products', 'buying_price'))
                    <small class="text-muted">@lang('Add buying_price to products for cost')</small>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">@lang('Expense')</h6>
                <p class="mb-0 fs-4 fw-bold text-warning">{{ $general->cur_sym ?? '৳' }}{{ number_format($expenseTotal, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">@lang('Returns')</h6>
                <p class="mb-0 fs-4 fw-bold text-info">{{ $general->cur_sym ?? '৳' }}{{ number_format($returnAmount, 2) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100 border-primary">
            <div class="card-body">
                <h6 class="text-muted text-uppercase small mb-2">@lang('Profit')</h6>
                <p class="mb-0 fs-4 fw-bold text-primary">{{ $general->cur_sym ?? '৳' }}{{ number_format($profit, 2) }}</p>
                <small class="text-muted">Revenue − Cost − Expense − Returns</small>
            </div>
        </div>
    </div>
</div>
@endsection
