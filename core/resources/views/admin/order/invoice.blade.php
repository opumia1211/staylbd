<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@lang('Invoice')</title>
    @php $invoiceFavicon = getLogo('favicon'); @endphp
    @if($invoiceFavicon)
    <link rel="icon" type="image/x-icon" href="{{ $invoiceFavicon }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ $invoiceFavicon }}">
    @endif
</head>
<style>
    @page {
        size: 8.27in 11.7in;
        margin: .5in;
    }

    body {
        font-family: "Arial", sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: #023047;
    }

    /* Typography */
    .strong {
        font-weight: 700;
    }

    .fw-md {
        font-weight: 500;
    }

    .primary-text {
        color: #219ebc;
    }

    h1,
    .h1 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 67px;
        line-height: 1.2;
        font-weight: 500;
    }

    h2,
    .h2 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 50px;
        line-height: 1.2;
        font-weight: 500;
    }

    h3,
    .h3 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 38px;
        line-height: 1.2;
        font-weight: 500;
    }

    h4,
    .h4 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 500;
    }

    h5,
    .h5 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 500;
    }

    h6,
    .h6 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 16px;
        line-height: 1.2;
        font-weight: 500;
    }

    .text-uppercase {
        text-transform: uppercase;
    }

    .text-end {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    /* List Style */
    ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    /* Utilities */
    .d-block {
        display: block;
    }

    .mt-0 {
        margin-top: 0;
    }

    .m-0 {
        margin: 0;
    }

    .mt-3 {
        margin-top: 16px;
    }

    .mt-4 {
        margin-top: 24px;
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    /* Title */
    .title {
        display: inline-block;
        letter-spacing: 0.05em;
    }

    /* Table Style */
    table {
        width: 7.27in;
        caption-side: bottom;
        border-collapse: collapse;
        border: 1px solid #ffffff;
        color: #000000;
        vertical-align: top;
    }

    table td {
        padding: 5px 15px;
    }

    table th {
        padding: 5px 15px;
    }

    table,
    td,
    th {
        border: 1px solid #ddd;
    }

    table th:last-child {
        text-align: right !important;
    }

    .table> :not(caption)>*>* {
        padding: 12px 24px;
        background-color: #ffffff;
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px #ffffff;
    }

    .table>tbody {
        vertical-align: inherit;
        border: 1px solid #eafbff;
    }

    .table>thead {
        vertical-align: bottom;
        background: #219ebc;
        color: #000;
    }

    .table>thead th {
        font-family: "Arial", sans-serif;
        text-align: left;
        font-size: 16px;
        letter-spacing: 0.03em;
        font-weight: 500;
    }

    .table td:last-child {
        text-align: right;
    }

    .table th:last-child {
        text-align: right;
    }

    .table> :not(:first-child) {
        border-top: 0;
    }

    .table-sm> :not(caption)>*>* {
        padding: 5px;
    }

    .table-bordered> :not(caption)>* {
        border-width: 1px 0;
    }

    .table-bordered> :not(caption)>*>* {
        border-width: 0 1px;
    }

    .table-borderless> :not(caption)>*>* {
        border-bottom-width: 0;
    }

    .table-borderless> :not(:first-child) {
        border-top-width: 0;
    }

    .table-striped>tbody>tr:nth-of-type(even)>* {
        background: #eafbff;
    }


    /* Logo */
    .logo {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 200px;
        height: 50px;
        font-size: 24px;
        text-transform: capitalize;
    }

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .info {
        justify-content: space-between;
        padding-top: 15px;
        padding-bottom: 15px;
        border-top: 1px solid #023047;
        border-bottom: 1px solid #023047;
    }

    .address {
        padding-top: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #023047;
    }

    header {
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .body {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    footer {
        padding-bottom: 15px;
    }

    .badge {
        display: inline-block;
        padding: 2px 15px;
        font-size: 10px;
        line-height: 1;
        border-radius: 15px;
    }

    .badge--success {
        color: white;
        background: #02c39a;
    }

    .badge--warning {
        color: white;
        background: #ffb703;
    }

    .align-items-center {
        align-items: center;
    }

    .footer-link {
        text-decoration: none;
        color: #219ebc;
    }

    .footer-link:hover {
        text-decoration: none;
        color: #219ebc;
    }

    .list--row {
        overflow: auto
    }

    .list--row::after {
        content: '';
        display: block;
        clear: both;
    }

    .float-left {
        float: left;
    }

    .float-right {
        float: right;
    }

    .d-block {
        display: block;
    }

    .d-inline-block {
        display: inline-block;
    }

    .invoice-signature-block {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #023047;
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 2rem;
        align-items: flex-end;
    }
    .invoice-signature-box {
        text-align: center;
        min-width: 140px;
    }
    .invoice-signature-box img {
        max-width: 120px;
        max-height: 50px;
        object-fit: contain;
        display: block;
        margin: 0 auto 0.5rem;
    }
    .invoice-signature-box .auth-name {
        font-weight: 600;
        font-size: 14px;
        color: #023047;
    }
    .invoice-signature-box .auth-label {
        font-size: 12px;
        color: #666;
        margin-top: 0.25rem;
    }
    .btn-print-invoice {
        padding: 0.4rem 1rem;
        font-size: 14px;
        background: #219ebc;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-print-invoice:hover { opacity: 0.9; }
    .invoice-address-list { list-style: none; margin: 0; padding: 0; }
    .invoice-address-list li { margin-bottom: 0.35rem; }
    .invoice-address-block { margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(2, 48, 71, 0.12); }
    .invoice-address-text { font-size: 18px; line-height: 1.6; color: #023047; font-weight: 500; }
    .invoice-address-text .d-block { margin-bottom: 0.25rem; }
    .invoice-qr-block { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #023047; text-align: center; }
    .invoice-qr-block img { display: block; margin: 0 auto 0.5rem; }
    .invoice-qr-caption { font-size: 12px; color: #666; }
    .invoice-driver-qr { margin-top: 0.5rem; }
    .invoice-qr-img { border: 0; vertical-align: middle; }
    @media print {
        .no-print { display: none !important; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .invoice-qr-img { max-width: none !important; }
    }
</style>

<body onload="window.print()">
    <header>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="list--row">
                        <div class="logo float-left">
                            @php $invoiceLogo = getLogo('invoice_logo') ?: getLogo('logo'); @endphp
                            @if($invoiceLogo)
                                <img src="{{ $invoiceLogo }}" alt="{{ gs('site_name') }}" class="logo-img" />
                            @else
                                <span>{{ gs('site_name') }}</span>
                            @endif
                        </div>
                        <div class="float-right text-end">
                            <h4 class="m-0">@lang('Invoice')</h4>
                            <div class="no-print mt-2 d-flex gap-2 justify-content-end flex-wrap">
                                <a href="{{ route('admin.orders.detail', $order->id) }}" class="btn-print-invoice" style="background: #6c757d; text-decoration: none;">@lang('Back to Order')</a>
                                <button type="button" class="btn-print-invoice" onclick="window.print();">@lang('Print')</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="info list--row">
                        <div class="info-left float-left">
                            <div class="list list--row">
                                <span class="strong">@lang('Order Date'):</span>
                                <span> {{ showDateTime($order->created_at, 'd/m/Y') }} </span>
                            </div>
                        </div>
                        <div class="info-right float-right">
                            <div class="list list--row text-right">
                                <span class="strong">@lang('Order No'):</span>
                                <span> {{ $order->order_no}} </span>
                            </div>
                        </div>
                    </div>
                    <div class="address list--row">
                        <div class="address-to float-left">
                            <h5 class="text-uppercase">@lang('Invoice To')</h5>
                            @php
                                $address = is_string($order->address) ? json_decode($order->address) : $order->address;
                            @endphp
                            <ul class="list invoice-address-list">
                                <li>
                                    <span class="strong">@lang('Name'):</span>
                                    <span>{{ $order->isGuest() ? ($order->guest_name ?? '—') : ($order->user->fullname ?? '—') }}</span>
                                </li>
                                <li>
                                    <span class="strong">@lang('Phone'):</span>
                                    <span>{{ $order->isGuest() ? ($order->guest_phone ?? '—') : ($order->user->mobile ?? '—') }}</span>
                                </li>
                                @if($address && is_object($address))
                                <li class="invoice-address-block">
                                    <div class="d-flex flex-wrap gap-3 align-items-start">
                                        <div class="invoice-address-text flex-grow-1">
                                            <span class="strong d-block mb-1">@lang('Delivery Address'):</span>
                                            @if(!empty($address->address))
                                            <span class="d-block">{{ __($address->address) }}</span>
                                            @endif
                                            @if(!empty($address->address_2))
                                            <span class="d-block">{{ __($address->address_2) }}</span>
                                            @endif
                                            <span class="d-block">
                                                @if(!empty($address->division)){{ __($address->division) }}, @endif
                                                @if(!empty($address->city)){{ __($address->city) }}, @endif
                                                @if(!empty($address->thana)){{ __($address->thana) }}, @endif
                                                @if(!empty($address->state)){{ __($address->state) }}, @endif
                                                @if(!empty($address->country)){{ __($address->country) }}@endif
                                                @if(!empty($address->zip)) {{ __($address->zip) }}@endif
                                            </span>
                                        </div>
                                    </div>
                                </li>
                                @endif
                                {{-- Delivery man QR: beside address / in front of client (only 1 of 2 QRs on invoice) --}}
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'delivery_driver_scan_token') && trim((string)($order->delivery_driver_scan_token ?? '')) !== '')
                                @php
                                    $driverScanUrl = route('order.delivery.driver.scanned', $order->delivery_driver_scan_token);
                                    $driverQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&format=png&color=000000&bgcolor=FFFFFF&data=' . rawurlencode($driverScanUrl);
                                @endphp
                                <li class="invoice-address-block">
                                    <div class="invoice-driver-qr text-center">
                                        <img src="{{ $driverQrUrl }}" width="100" height="100" alt="Delivery: Scan for Google Maps" class="invoice-qr-img" style="display:block;margin:0 auto;min-width:100px;min-height:100px;">
                                        <p class="invoice-qr-caption m-0 mt-1" style="font-size: 11px;">@lang('Delivery: Scan for Google Maps')</p>
                                        <p class="invoice-qr-caption m-0" style="font-size: 11px;">ডেলিভারি: গুগল ম্যাপের জন্য স্ক্যান করুন</p>
                                    </div>
                                </li>
                                @endif
                            </ul>
                        </div>
                        <div class="address-form float-right">
                            <ul class="text-end">
                                <li>
                                    <h5 class="text-uppercase">@lang('Order Summary')</h5>
                                </li>
                                <li>
                                    <span class="d-inline-block strong">@lang('Total Amount') :</span>
                                    <span class="d-inline-block">{{ showAmount($order->total) }} {{ __($general->cur_text) }}</span>
                                </li>
                                <li>
                                    <span class="d-inline-block strong">@lang('Payment Type') :</span>
                                    <span class="d-inline-block">
                                        @if ($order->payment_type == Status::PAYMENT_ONLINE)
                                            <span class="d-inline-block">@lang('Online payment gateway')</span>
                                        @else
                                            <span class="d-inline-block">@lang('Cash on delivery')</span>
                                        @endif
                                    </span>
                                </li>
                                <li>
                                    <span class="d-inline-block strong">@lang('Shipping Area')</span>
                                    <span class="d-inline-block">{{ __(@$order->shipping->name) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="body">
                        <div class="text-center mt-4 mb-3">
                            <div class="title-inset">
                                <h6 class="title m-0 text-uppercase">@lang('Order Details')</h6>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>@lang('Product Name')</th>
                                    <th>@lang('Quantity')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Subtotal')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($order->orderDetail as $detail)
                                    <tr>
                                        <td>
                                            <span>{{ __(@$detail->product->name) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $detail->quantity }}</span>
                                        </td>

                                        <td>
                                            <span>{{ showAmount($detail->price) }} {{ __($general->cur_text) }}</span>
                                        </td>

                                        <td>
                                            <span>{{ showAmount($detail->price * $detail->quantity) }} {{ __($general->cur_text) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                                <tr>
                                    <td colspan="3" class="text-end">@lang('Subtotal')</td>
                                    <td>{{ showAmount($order->subtotal) }} {{ __($general->cur_text) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">@lang('Shipping Charge')</td>
                                    <td>{{ showAmount($order->shipping_charge) }} {{ __($general->cur_text) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">@lang('Discount')</td>
                                    <td>{{ showAmount($order->discount) }} {{ __($general->cur_text) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">@lang('Total Amount')</td>
                                    <td><span>{{ showAmount($order->total) }} {{ __($general->cur_text) }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                        @php
                            $invoiceSignature = getLogo('invoice_signature');
                            $invoiceAuthorizedName = gs('invoice_authorized_name');
                        @endphp
                        @if($invoiceSignature || $invoiceAuthorizedName)
                        <div class="invoice-signature-block">
                            @if($invoiceSignature)
                            <div class="invoice-signature-box">
                                <img src="{{ $invoiceSignature }}" alt="@lang('Signature')" />
                                @if($invoiceAuthorizedName)
                                <div class="auth-name">{{ $invoiceAuthorizedName }}</div>
                                <div class="auth-label">@lang('Authorized by')</div>
                                @endif
                            </div>
                            @elseif($invoiceAuthorizedName)
                            <div class="invoice-signature-box">
                                <div class="auth-name">{{ $invoiceAuthorizedName }}</div>
                                <div class="auth-label">@lang('Authorized by')</div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    {{-- Client QR only: below product/price (2nd of 2 QRs on invoice). Caption: name + "প্রোডাক্ট বুঝে পেয়ে থাকেন তাহলে QR স্ক্যান করুন" --}}
                    @if(\Illuminate\Support\Facades\Schema::hasColumn('orders', 'delivery_scan_token') && trim((string)($order->delivery_scan_token ?? '')) !== '')
                    @php
                        $deliveryScanUrl = route('order.delivery.scanned', $order->delivery_scan_token);
                        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&format=png&color=000000&bgcolor=FFFFFF&data=' . rawurlencode($deliveryScanUrl);
                        $customerName = $order->isGuest() ? ($order->guest_name ?? '') : ($order->user->fullname ?? $order->user->username ?? '');
                        $captionEn = gs('invoice_qr_caption_en') ?: ($customerName ? $customerName . ', ' : '') . __('If you have received the product, please scan the QR code');
                        $captionBn = gs('invoice_qr_caption_bn') ?: ($customerName ? $customerName . ', ' : '') . 'আপনি যদি প্রোডাক্ট বুঝে পেয়ে থাকেন তবে QR কোড স্ক্যান করুন';
                        $captionEn = str_replace(['{{name}}', '{{ name }}'], $customerName, $captionEn);
                        $captionBn = str_replace(['{{name}}', '{{ name }}'], $customerName, $captionBn);
                    @endphp
                    <div class="invoice-qr-block">
                        <p class="invoice-qr-caption mb-2" style="font-size: 14px; font-weight: 600;">{{ $captionEn }}</p>
                        <p class="invoice-qr-caption mb-2" style="font-size: 14px; font-weight: 600;">{{ $captionBn }}</p>
                        <img src="{{ $qrImageUrl }}" width="140" height="140" alt="QR Code" class="invoice-qr-img" style="display:block;margin:0 auto;min-width:140px;min-height:140px;">
                        <p class="invoice-qr-caption m-0 mt-1">@lang('Product received? Scan to confirm')</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <span class="d-block text-center">
                        @lang('Copyright') &copy; @php date('Y') @endphp @lang('All Right Reserved By')
                        <a href="#" class="footer-link">{{ $general->site_name }}</a>
                    </span>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
