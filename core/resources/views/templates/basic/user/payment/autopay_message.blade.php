@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card custom--card">
                <div class="card-header card-header-bg">
                    <h5 class="card-title">{{ __($pageTitle) }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-center mb-3">@lang('You have requested') <strong class="text--success">{{ showAmount($deposit->amount) }} {{ __($general->cur_text) }}</strong>. @lang('Please pay')
                        <strong class="text-success">{{ showAmount($deposit->final_amo) . ' ' . $deposit->method_currency }}</strong> @lang('for successful payment').
                    </p>
                    <div class="alert alert-light border mb-4">
                        <p class="mb-0">{{ $instructions }}</p>
                    </div>
                    <p class="text-center text-muted small mb-0">
                        @lang('When we receive your payment (e.g. via SMS on our system), this page will update automatically and your order will be confirmed.')
                    </p>
                    <div class="text-center mt-4">
                        <a href="{{ route('user.transactions') }}" class="btn btn--base btn-sm">@lang('View Transactions')</a>
                        <a href="{{ route('user.order.index') }}" class="btn btn-outline--primary btn-sm ms-2">@lang('My Orders')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
