@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="col-xxl-12 col-lg-12">
        <div class="dashboard-wrapper">
            <div class="row g-3 mb-5">
                <h6>@lang('Order Details')</h6>
                <div class="col-md-6">
                    <div class="deposit-preview">
                        <div class="deposit-content w-100">
                            <ul>
                                <li>
                                    @lang('Order No')
                                    <span>{{$order->order_no}}</span>
                                </li>
                                <li>
                                    @lang('Total Price')
                                    <span>{{ showAmount($order->total) }} {{ __($general->cur_text) }}</span>
                                </li>
                                <li>
                                    @lang('Payment Type')
                                    @if ($order->payment_type == Status::PAYMENT_ONLINE)
                                        <span>{{ __(@$order->deposit->gateway->name) }} @lang('payment gateway')</span>
                                    @else
                                        <span>@lang('Cash on delivery')</span>
                                        @if(isset($order->cod_charge) && $order->cod_charge > 0)
                                            <small class="d-block text-muted">@lang('COD charge'): {{ $general->cur_sym }}{{ showAmount($order->cod_charge) }}</small>
                                        @endif
                                    @endif
                                </li>
                                @if (@$order->deposit->trx)
                                    <li>
                                        @lang('Payment Trx')
                                        <span>{{ @$order->deposit->trx }}</span>
                                    </li>
                                @endif
                                <li>
                                    @lang('Order Date')
                                    <span>{{ showDateTime($order->created_at) }}</span>
                                </li>
                                <li>
                                    @lang('Order Status')
                                    @php
                                        echo $order->ordersBadge;
                                    @endphp
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="deposit-preview ">
                        <div class="deposit-content w-100">
                            <ul>
                                <li>
                                    @lang('Shipping Area')
                                    <span>{{ __(@$order->shipping->name) }}</span>
                                </li>
                                @if ($order->discount * 1)
                                    <li>
                                        @lang('Coupon')
                                        <span>{{ __(@$order->coupon->name) }}</span>
                                    </li>
                                @endif
                                @php
                                    $address = json_decode($order->address);
                                @endphp
                                <li>
                                    @lang('Delivery Address')
                                    <span>
                                        {{ __($address->address) }}
                                    </span>
                                </li>
                                <li>
                                    @lang('Country & State')
                                    <span>
                                        {{ __($address->country) }} @lang('&') {{ __($address->state) }}
                                    </span>
                                </li>
                                <li>
                                    @lang('City & Zip')
                                    <span>
                                        {{ __($address->city) }} @lang('&') {{ __($address->zip) }}
                                    </span>
                                </li>
                                <li>
                                    @lang('Payment Status')
                                    @php
                                        echo $order->paymentBadge;
                                    @endphp
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            @if ($order->payment_type == Status::PAYMENT_OFFLINE)
            <div class="alert alert-info border-0 mb-4 d-flex align-items-center gap-2" role="alert">
                @include($activeTemplate . 'partials.icon', ['name' => 'money-bill-wave', 'class' => 'fs-4'])
                <div>
                    <strong>@lang('Cash on Delivery')</strong><br>
                    <span class="small">@lang('Please keep exact amount ready. Our delivery agent will collect cash at doorstep.')</span>
                </div>
            </div>
            @endif

            @if ($order->payment_type == Status::PAYMENT_ONLINE && $order->payment_status == Status::ORDER_PAYMENT_PENDING)
            <div class="alert alert-warning border-0 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2" role="alert">
                <div class="d-flex align-items-center gap-2">
                    @include($activeTemplate . 'partials.icon', ['name' => 'credit-card', 'class' => 'fs-4'])
                    <div>
                        <strong>@lang('Payment Pending')</strong><br>
                        <span class="small">@lang('Complete your payment to confirm this order.')</span>
                    </div>
                </div>
                <a href="{{ route('user.deposit.index', $order->id) }}" class="btn btn--base btn-sm">
                    @include($activeTemplate . 'partials.icon', ['name' => 'redo-alt', 'class' => 'me-1'])@lang('Retry Payment')
                </a>
            </div>
            @endif

            <table class="table cmn--table">
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
                                <a href="{{ product_detail_url($detail->product) }}" class="text--base">
                                    {{ __(strLimit(@$detail->product->name, 15)) }}
                                </a>

                                @if ($order->order_status == Status::ORDER_DELIVERED)
                                    @if ($detail->product->file)
                                        (<span>
                                            <a href="{{ route('download', [$detail->product->id, $detail->product->file]) }}" class="mr-3 text--primary">
                                                @include($activeTemplate . 'partials.icon', ['name' => 'download']) @lang('Download File')
                                            </a>
                                        </span>)
                                    @elseif ($detail->product->link)
                                        (<span>
                                            <a href="{{ $detail->product->link }}" target="_blank" class="mr-3 text--primary">
                                                @include($activeTemplate . 'partials.icon', ['name' => 'external-link-alt']) @lang('Visit URL')
                                            </a>
                                        </span>)
                                    @endif
                                @endif
                            </td>
                            <td>
                                <strong>{{ $detail->quantity }}</strong>
                            </td>
                            <td class="text--base">
                                <strong>{{ showAmount($detail->price) }} {{ __($general->cur_text) }}</strong>
                            </td>
                            <td>
                                <strong>{{ showAmount($detail->price * $detail->quantity) }} {{ __($general->cur_text) }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="text--danger text-center">{{ __($emptyMessage) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if(\Illuminate\Support\Facades\Schema::hasTable('order_shipment_trackings') && ($order->shipmentTrackings ?? collect())->isNotEmpty())
            <div class="card b-radius--10 mt-4">
                <div class="card-header">
                    <h6 class="mb-0">@include($activeTemplate . 'partials.icon', ['name' => 'map-marker-alt']) @lang('Location & Order Tracking')</h6>
                </div>
                <div class="card-body">
                    <div class="tracking-timeline">
                        @foreach($order->shipmentTrackings->sortByDesc('created_at') as $t)
                            <div class="d-flex mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="flex-shrink-0 me-3">
                                    <span class="badge badge--primary">{{ __(\App\Models\OrderShipmentTracking::statusLabels()[$t->status] ?? $t->status) }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    @if($t->location_name)
                                        <strong>{{ $t->location_name }}</strong><br>
                                    @endif
                                    @if($t->location_address)
                                        <small class="text-muted">{{ $t->location_address }}</small><br>
                                    @endif
                                    @if($t->latitude && $t->longitude)
                                        <a href="{{ $t->map_url }}" target="_blank" class="text-primary small">@include($activeTemplate . 'partials.icon', ['name' => 'map-marked-alt']) @lang('View on Map')</a><br>
                                    @endif
                                    @if($t->notes)
                                        <small>{{ $t->notes }}</small><br>
                                    @endif
                                    @if($t->tracking_number)
                                        <small class="text-info">@lang('Tracking #'): {{ $t->tracking_number }}</small><br>
                                    @endif
                                    <small class="text-muted">{{ showDateTime($t->created_at) }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="total-wrapper">
                <div class="d-flex flex-wrap justify-content-between">
                    <strong>@lang('Subtotal :')</strong><strong> {{ showAmount($order->subtotal) }} {{ __($general->cur_text) }}</strong></strong>
                </div>
                <div class="d-flex flex-wrap justify-content-between">
                    <strong>@lang('Shipping Charge :')</strong><strong> {{ showAmount($order->shipping_charge) }} {{ __($general->cur_text) }}</strong>
                </div>
                @if ($order->discount * 1)
                    <div class="d-flex flex-wrap justify-content-between">
                        <strong>@lang('Discount :')</strong><strong> {{ showAmount($order->discount) }} {{ __($general->cur_text) }}</strong>
                    </div>
                @endif
                <div class="d-flex flex-wrap justify-content-between border-0">
                    <strong>@lang('Total :')</strong><strong> {{ showAmount($order->total) }} {{ __($general->cur_text) }}</strong>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .total-wrapper {
            max-width: 300px;
            margin-left: auto;
            margin-top: 15px;
            font-size: 14px;
            margin-right: 20px;
        }

        @media (max-width:575px) {
            .total-wrapper {
                margin-right: 0;
            }
        }

        .total-wrapper>div {
            padding: 6px 0;
            border-bottom: 1px dashed #ddd;
        }
    </style>
@endpush
