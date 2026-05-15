<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@lang('Invoice') - {{ $order->order_no }}</title>
    @php $invoiceFavicon = getLogo('favicon'); @endphp
    @if($invoiceFavicon)
    <link rel="icon" type="image/x-icon" href="{{ $invoiceFavicon }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #696cff;
            --secondary-color: #8592a3;
            --success-color: #71dd37;
            --info-color: #03c3ec;
            --warning-color: #ffab00;
            --danger-color: #ff3e1d;
            --dark-color: #233446;
            --text-main: #566a7f;
            --border-color: #d9dee3;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; font-size: 14px; line-height: 1.5; color: var(--text-main); background: #fff; padding-bottom: 50px; }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        header { border-bottom: 2px solid var(--primary-color); padding-bottom: 20px; margin-bottom: 30px; }
        .row { display: flex; flex-wrap: wrap; margin: 0 -15px; }
        .col-6 { width: 50%; padding: 0 15px; }
        .col-12 { width: 100%; padding: 0 15px; }
        .logo-img { max-height: 60px; }
        .text-end { text-align: right; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 20px; }
        .mt-4 { margin-top: 20px; }
        h1, h2, h3, h4, h5, h6 { color: var(--dark-color); font-weight: 700; margin-bottom: 5px; }
        .invoice-title { font-size: 32px; color: var(--primary-color); text-transform: uppercase; letter-spacing: 1px; }
        .info-box { margin-bottom: 30px; }
        .info-label { font-weight: 600; color: var(--dark-color); margin-right: 5px; min-width: 100px; display: inline-block; }
        .address-box { background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); height: 100%; }
        .address-box h5 { border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 10px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { padding: 12px 15px; border-bottom: 1px solid var(--border-color); text-align: left; }
        .table th { background: #fcfcfd; font-weight: 600; color: var(--dark-color); text-transform: uppercase; font-size: 12px; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: rgba(0, 0, 0, .02); }
        .text-right { text-align: right; }
        .totals-row td { border-bottom: none; padding: 5px 15px; }
        .grand-total { font-size: 18px; color: var(--primary-color); font-weight: 700; border-top: 2px solid var(--primary-color) !important; padding-top: 15px !important; }
        .no-print { margin-bottom: 20px; display: flex; gap: 10px; justify-content: flex-end; }
        .btn-action { padding: 8px 20px; border-radius: 6px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; display: inline-block; font-size: 13px; transition: all 0.2s; }
        .btn-print { background: var(--primary-color); color: #fff; }
        .btn-back { background: var(--secondary-color); color: #fff; }
        .qr-section { margin-top: 40px; text-align: center; border-top: 1px dashed var(--border-color); padding-top: 30px; }
        .qr-box { display: inline-block; text-align: center; margin: 0 20px; padding: 15px; border: 1px solid var(--border-color); border-radius: 12px; background: #fff; }
        .qr-img { margin: 10px 0; display: block; }
        .qr-caption { font-size: 12px; color: var(--text-main); font-weight: 600; }
        .signature-section { margin-top: 50px; text-align: right; }
        .signature-box { display: inline-block; width: 200px; text-align: center; }
        .signature-img { max-width: 150px; border-bottom: 1px solid var(--dark-color); margin-bottom: 10px; }
        .auth-name { font-weight: 700; color: var(--dark-color); }
        .auth-label { font-size: 12px; color: var(--secondary-color); }
        footer { margin-top: 50px; text-align: center; font-size: 12px; color: var(--secondary-color); padding-top: 20px; border-top: 1px solid var(--border-color); }
        @media print {
            .no-print { display: none !important; }
            body { padding-bottom: 0; }
            .container { max-width: 100%; padding: 0; }
            header { margin-bottom: 20px; }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="container">
        <div class="no-print">
            <a href="{{ route('admin.orders.detail', $order->id) }}" class="btn-action btn-back">@lang('Back to Order')</a>
            <button type="button" class="btn-action btn-print" onclick="window.print();">@lang('Print Invoice')</button>
        </div>

        <header>
            <div class="row">
                <div class="col-6">
                    @php $invoiceLogo = getLogo('invoice_logo') ?: getLogo('logo'); @endphp
                    @if($invoiceLogo)
                        <img src="{{ $invoiceLogo }}" alt="{{ gs('site_name') }}" class="logo-img" />
                    @else
                        <h2>{{ gs('site_name') }}</h2>
                    @endif
                </div>
                <div class="col-6 text-end">
                    <h1 class="invoice-title">@lang('Invoice')</h1>
                    <div class="mt-2">
                        <p class="mb-1"><span class="info-label">@lang('Order No'):</span> {{ $order->order_no }}</p>
                        <p><span class="info-label">@lang('Date'):</span> {{ showDateTime($order->created_at, 'd M, Y') }}</p>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <div class="row mb-4">
                <div class="col-6">
                    <div class="address-box">
                        <h5 class="text-uppercase">@lang('Invoice To')</h5>
                        <p class="mb-1"><strong>{{ $order->isGuest() ? ($order->guest_name ?? '—') : ($order->user->fullname ?? '—') }}</strong></p>
                        <p class="mb-2"><i class="las la-phone"></i> {{ $order->isGuest() ? ($order->guest_phone ?? '—') : ($order->user->mobile ?? '—') }}</p>
                        
                        @php
                            $address = is_string($order->address) ? json_decode($order->address) : $order->address;
                        @endphp
                        @if($address && is_object($address))
                        <div class="mt-2" style="font-size: 13px;">
                            <span class="info-label d-block mb-1">@lang('Delivery Address'):</span>
                            <p class="m-0">
                                @if(!empty($address->address)) {{ __($address->address) }}<br> @endif
                                @if(!empty($address->address_2)) {{ __($address->address_2) }}<br> @endif
                                {{ __($address->city ?? '') }}@if(!empty($address->city) && !empty($address->thana)), @endif
                                {{ __($address->thana ?? '') }}<br>
                                {{ __($address->division ?? '') }}@if(!empty($address->division) && !empty($address->zip)) - @endif{{ $address->zip ?? '' }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-6">
                    <div class="address-box">
                        <h5 class="text-uppercase">@lang('Order Summary')</h5>
                        <div class="mt-2">
                            <p class="mb-1"><span class="info-label">@lang('Payment'):</span> 
                                @if ($order->payment_type == Status::PAYMENT_ONLINE)
                                    <span style="color: var(--success-color); font-weight: 600;">@lang('Online Gateway')</span>
                                @else
                                    <span style="color: var(--warning-color); font-weight: 600;">@lang('Cash on Delivery')</span>
                                @endif
                            </p>
                            <p class="mb-1"><span class="info-label">@lang('Shipping'):</span> {{ __(@$order->shipping->name) }}</p>
                            <p class="mb-1"><span class="info-label">@lang('Status'):</span> 
                                @if($order->status == Status::ORDER_COMPLETED) @lang('Delivered')
                                @elseif($order->status == Status::ORDER_CANCELED) @lang('Canceled')
                                @else @lang('Processing')
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="body mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('Description')</th>
                            <th class="text-right">@lang('Price')</th>
                            <th class="text-right">@lang('Qty')</th>
                            <th class="text-right">@lang('Total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->orderDetail as $detail)
                            <tr>
                                <td>
                                    <p class="mb-0" style="color: var(--dark-color); font-weight: 600;">{{ __(@$detail->product->name) }}</p>
                                    @if($detail->size) <small class="text-muted">@lang('Size'): {{ $detail->size }}</small> @endif
                                </td>
                                <td class="text-right">{{ showAmount($detail->price) }} {{ __($general->cur_text) }}</td>
                                <td class="text-right">{{ $detail->quantity }}</td>
                                <td class="text-right">{{ showAmount($detail->price * $detail->quantity) }} {{ __($general->cur_text) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="4">@lang('No items found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="row justify-content-end">
                    <div class="col-6 text-end">
                        <table class="table totals-row">
                            <tr>
                                <td class="text-right">@lang('Subtotal')</td>
                                <td class="text-right" style="width: 150px;">{{ showAmount($order->subtotal) }} {{ __($general->cur_text) }}</td>
                            </tr>
                            <tr>
                                <td class="text-right">@lang('Shipping')</td>
                                <td class="text-right">{{ showAmount($order->shipping_charge) }} {{ __($general->cur_text) }}</td>
                            </tr>
                            @if($order->discount > 0)
                            <tr>
                                <td class="text-right">@lang('Discount')</td>
                                <td class="text-right" style="color: var(--danger-color);">-{{ showAmount($order->discount) }} {{ __($general->cur_text) }}</td>
                            </tr>
                            @endif
                            <tr class="grand-total">
                                <td class="text-right">@lang('Grand Total')</td>
                                <td class="text-right">{{ showAmount($order->total) }} {{ __($general->cur_text) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="qr-section">
                <div class="row">
                    <div class="col-12">
                        {{-- Delivery Driver QR --}}
                        @if(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'delivery_driver_scan_token') && trim((string)($order->delivery_driver_scan_token ?? '')) !== '')
                        @php
                            $driverScanUrl = route('order.delivery.driver.scanned', $order->delivery_driver_scan_token);
                            $driverQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&format=png&color=000000&bgcolor=FFFFFF&data=' . rawurlencode($driverScanUrl);
                        @endphp
                        <div class="qr-box">
                            <p class="qr-caption">@lang('Driver: Scan for Maps')</p>
                            <img src="{{ $driverQrUrl }}" width="100" height="100" alt="QR" class="qr-img">
                            <p class="qr-caption" style="font-size: 10px; font-weight: normal;">@lang('Only for internal logistics')</p>
                        </div>
                        @endif

                        {{-- Client Receipt QR --}}
                        @if(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'delivery_scan_token') && trim((string)($order->delivery_scan_token ?? '')) !== '')
                        @php
                            $deliveryScanUrl = route('order.delivery.scanned', $order->delivery_scan_token);
                            $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&format=png&color=696cff&bgcolor=FFFFFF&data=' . rawurlencode($deliveryScanUrl);
                            $customerName = $order->isGuest() ? ($order->guest_name ?? '') : ($order->user->fullname ?? $order->user->username ?? '');
                        @endphp
                        <div class="qr-box" style="border-color: var(--primary-color);">
                            <p class="qr-caption" style="color: var(--primary-color);">@lang('Customer: Scan to Confirm')</p>
                            <img src="{{ $qrImageUrl }}" width="120" height="120" alt="QR" class="qr-img">
                            <p class="qr-caption" style="font-size: 10px; font-weight: normal;">@lang('Scan once you receive the items')</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            @php
                $invoiceSignature = getLogo('invoice_signature');
                $invoiceAuthorizedName = gs('invoice_authorized_name');
            @endphp
            @if($invoiceSignature || $invoiceAuthorizedName)
            <div class="signature-section">
                <div class="signature-box">
                    @if($invoiceSignature)
                        <img src="{{ $invoiceSignature }}" alt="@lang('Signature')" class="signature-img" />
                    @else
                        <div style="height: 60px;"></div>
                        <div style="border-top: 1px solid var(--dark-color); margin-bottom: 5px;"></div>
                    @endif
                    
                    @if($invoiceAuthorizedName)
                        <div class="auth-name">{{ $invoiceAuthorizedName }}</div>
                        <div class="auth-label">@lang('Authorized Signatory')</div>
                    @endif
                </div>
            </div>
            @endif
        </main>

        <footer>
            <p>@lang('Thank you for shopping with us!')</p>
            <p>&copy; {{ date('Y') }} {{ gs('site_name') }}. @lang('All rights reserved.')</p>
        </footer>
    </div>
</body>

</html>
