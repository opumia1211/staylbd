@extends($activeTemplate . 'layouts.master')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">@lang('bKash Payment')</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h4>@lang('Payment Amount'): {{ showAmount($data->amount) }} {{ $data->currency }}</h4>
                        <p class="text-muted">@lang('Order ID'): {{ $data->order_id }}</p>
                    </div>

                    <form method="POST" action="{{ $data->url }}">
                        @csrf
                        <input type="hidden" name="track" value="{{ $data->track }}">

                        <div class="form-group mb-3">
                            <label for="paymentID" class="form-label">@lang('bKash Payment ID')</label>
                            <input type="text" class="form-control" id="paymentID" name="paymentID"
                                   placeholder="@lang('Enter bKash Payment ID')" required>
                            <div class="form-text">@lang('Please enter the bKash Payment ID you received after payment')</div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                @lang('Verify Payment')
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6>@lang('How to pay with bKash:')</h6>
                            <ol>
                                <li>@lang('Open bKash app on your mobile')</li>
                                <li>@lang('Go to Send Money')</li>
                                <li>@lang('Enter Merchant Number: 01700000000')</li>
                                <li>@lang('Enter Amount:') {{ showAmount($data->amount) }} @lang('BDT')</li>
                                <li>@lang('Enter Reference:') {{ $data->order_id }}</li>
                                <li>@lang('Complete the payment')</li>
                                <li>@lang('Copy the Payment ID and paste it above')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
