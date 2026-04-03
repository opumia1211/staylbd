<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@lang('Delivery Confirmed')</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; margin: 0; padding: 2rem; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f5f5f5; }
        .box { max-width: 400px; text-align: center; padding: 2rem; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .box h1 { font-size: 1.5rem; color: #023047; margin-bottom: 0.5rem; }
        .box p { color: #666; margin: 0.5rem 0; }
        .box .order-no { font-weight: 600; color: #219ebc; }
        .icon { font-size: 3rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
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
