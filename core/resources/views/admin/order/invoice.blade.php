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

{{-- inline style moved to critical-admin.css --}}


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
