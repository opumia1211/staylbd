<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@lang('Delivery Confirmed')</title>
    <link rel="stylesheet" href="{{ storefront_compiled_stylesheet_url('critical-storefront') }}" crossorigin="anonymous">

</head>
<body class="st-order-delivery-scanned">
    <div class="box">
        <div class="icon">✅</div>
        @if($justScanned)
            <h1>@lang('Thank you!')</h1>
            <p>@lang('You have confirmed receipt of your order.')</p>
        @else
            <h1>@lang('Already confirmed')</h1>
            <p>@lang('This order was already confirmed earlier.')</p>
        @endif
        <p class="order-no">{{ $order->order_no }}</p>
        <p class="small text-muted">@lang('We have notified the store.')</p>
    </div>
</body>
</html>
