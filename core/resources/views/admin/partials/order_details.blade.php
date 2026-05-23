
{{-- inline style moved to critical-admin.css --}}

<div class="card border-0 shadow-sm rounded-3 order-detail-card">
    <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2 py-2">
        <h6 class="card-title mb-0 fw-semibold"><i class="las la-receipt me-1"></i>@lang('Order detail of') {{ $order->order_no }}</h6>
    </div>
    <div class="card-body">
        <div class="row mb-2 g-2 g-md-3">
            <div class="col-md-6">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Order No')
                        <span class="fw-bold">{{ $order->order_no }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Total Price')
                        <span class="fw-bold">{{ showAmount($order->total) }} {{ __($general->cur_text) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payment')
                        @if ($order->payment_type == Status::PAYMENT_ONLINE)
                            <span class="fw-bold">{{ __(@$order->deposit->gateway->name) }} @lang('payment gateway')</span>
                        @else
                            <span class="fw-bold"><span class="badge bg-warning text-dark">COD</span> @lang('Cash on delivery')</span>
                        @endif
                    </li>
                    @if ($order->payment_type == Status::PAYMENT_OFFLINE && isset($order->cod_charge) && $order->cod_charge > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('COD Charge')
                        <span class="fw-bold">{{ $general->cur_sym }}{{ showAmount($order->cod_charge) }}</span>
                    </li>
                    @endif
                    @if (@$order->deposit->trx)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Payment Trx')
                            <span class="fw-bold">{{ @$order->deposit->trx }}</span>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Order Date')
                        <span class="fw-bold">{{ showDateTime($order->created_at) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Order Status')
                        @php
                            echo $order->ordersBadge;
                        @endphp
                    </li>
                </ul>
            </div>
            <div class="col-md-6">
                @if($order->isGuest())
                <ul class="list-group list-group-flush mb-2">
                    <li class="list-group-item bg-light">
                        <span class="badge bg-secondary">@lang('Customer Type')</span>
                        <span class="fw-bold ms-1">@lang('Guest')</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Name')
                        <span class="fw-bold">{{ $order->guest_name ?? '—' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Phone')
                        <span class="fw-bold">{{ $order->guest_phone ?? '—' }}</span>
                    </li>
                    @if($order->guest_email)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Email')
                        <span class="fw-bold">{{ $order->guest_email }}</span>
                    </li>
                    @endif
                    <li class="list-group-item">
                        <span class="fw-bold text-uppercase small text-dark">@lang('Delivery Address')</span>
                        <div class="mt-1">{{ $order->guest_address ?? '—' }}</div>
                        @if($order->guest_location)<div class="text-muted small mt-1">{{ $order->guest_location }}</div>@endif
                        @if($order->guest_delivery_note)<div class="small mt-1">@lang('Note'): {{ $order->guest_delivery_note }}</div>@endif
                        @if($order->guest_preferred_delivery_time)<div class="small">@lang('Preferred time'): {{ $order->guest_preferred_delivery_time }}</div>@endif
                    </li>
                </ul>
                @endif
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Shipping Area')
                        <span class="fw-bold">{{ __(@$order->shipping->name) }}</span>
                    </li>
                    @if ($order->discount != 0)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            @lang('Coupon')
                            <span class="fw-bold">{{ __(@$order->coupon->name) }}</span>
                        </li>
                    @endif
                    @php
                        $address = is_string($order->address) ? json_decode($order->address) : $order->address;
                        $address = is_object($address) ? $address : (object)['address' => '', 'address_2' => '', 'country' => '', 'state' => '', 'city' => '', 'zip' => '', 'division' => '', 'thana' => ''];
                    @endphp
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                            <span class="fw-bold text-uppercase small text-dark">@lang('Delivery Address')</span>
                            <button type="button" class="btn btn-sm btn-outline--primary" data-bs-toggle="collapse" data-bs-target="#orderAddressEditForm"><i class="las la-edit me-1"></i>@lang('Correct / Edit')</button>
                        </div>
                        <div class="order-delivery-address-block">
                            <div class="order-address-line">{{ __(data_get($address, 'address', '—')) }}</div>
                            @if(data_get($address, 'address_2'))
                            <div class="order-address-line">{{ __(data_get($address, 'address_2')) }}</div>
                            @endif
                            <div class="order-address-line">
                                @if(data_get($address, 'division')){{ __(data_get($address, 'division')) }}, @endif
                                @if(data_get($address, 'city')){{ __(data_get($address, 'city')) }}, @endif
                                @if(data_get($address, 'thana')){{ __(data_get($address, 'thana')) }}, @endif
                                @if(data_get($address, 'state')){{ __(data_get($address, 'state')) }}, @endif
                                @if(data_get($address, 'country')){{ __(data_get($address, 'country')) }}@endif
                                @if(data_get($address, 'zip')) {{ __(data_get($address, 'zip')) }}@endif
                            </div>
                        </div>
                        <div class="collapse mt-3" id="orderAddressEditForm">
                            <form action="{{ route('admin.orders.address.update', $order->id) }}" method="POST" class="order-address-edit-form border rounded p-3 bg-light">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label order-address-edit-label">@lang('Address') / @lang('Road, House No') <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address" value="{{ data_get($address, 'address') }}" required maxlength="500" placeholder="@lang('Street, building, floor')">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label order-address-edit-label">@lang('Address 2')</label>
                                        <input type="text" class="form-control" name="address_2" value="{{ data_get($address, 'address_2') }}" maxlength="500" placeholder="@lang('Optional')">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label order-address-edit-label">@lang('Division')</label>
                                        <input type="text" class="form-control" name="division" value="{{ data_get($address, 'division') }}" maxlength="100">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label order-address-edit-label">@lang('City') / @lang('District')</label>
                                        <input type="text" class="form-control" name="city" value="{{ data_get($address, 'city') }}" maxlength="100">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label order-address-edit-label">@lang('Thana')</label>
                                        <input type="text" class="form-control" name="thana" value="{{ data_get($address, 'thana') }}" maxlength="100">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label order-address-edit-label">@lang('State')</label>
                                        <input type="text" class="form-control" name="state" value="{{ data_get($address, 'state') }}" maxlength="100">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label order-address-edit-label">@lang('Country')</label>
                                        <input type="text" class="form-control" name="country" value="{{ data_get($address, 'country') }}" maxlength="100">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label order-address-edit-label">@lang('ZIP') / @lang('Postal Code')</label>
                                        <input type="text" class="form-control" name="zip" value="{{ data_get($address, 'zip') }}" maxlength="20">
                                    </div>
                                    <div class="col-12 pt-1">
                                        <button type="submit" class="btn btn--primary"><i class="las la-save me-1"></i>@lang('Save address')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <p class="small text-muted mt-2 mb-0">
                            <i class="las la-qrcode me-1"></i>@lang('QR scan alerts'): <a href="{{ route('admin.notifications.delivery.scan') }}" class="text--primary">@lang('Delivery Scan Notifications')</a>
                        </p>
                    </li>
                    @if (optional($order)->ip_address || (optional($order)->device_lat !== null && optional($order)->device_lng !== null) || optional($order)->location_risk_score !== null)
                    <li class="list-group-item">
                        <span class="small fw-semibold text-muted d-block mb-1">@lang('Location & Risk')</span>
                        @if ($order->ip_address)
                            <span class="d-block small">@lang('IP'): <code>{{ $order->ip_address }}</code></span>
                        @endif
                        @if ($order->device_lat !== null && $order->device_lng !== null)
                            <span class="d-block small">@lang('Device'): {{ $order->device_lat }}, {{ $order->device_lng }}</span>
                        @endif
                        @if ($order->location_risk_score !== null)
                            <span class="d-inline-block mt-1 badge {{ $order->location_risk_score >= 50 ? 'bg-warning' : ($order->location_risk_score > 0 ? 'bg-info' : 'bg-secondary') }}">@lang('Risk'): {{ $order->location_risk_score }}</span>
                        @endif
                    </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        @lang('Payment Status')
                        @php
                            echo $order->paymentBadge;
                        @endphp
                    </li>
                </ul>
            </div>
        </div>

        @includeIf('modules.OrderEnhancements::order_section', ['order' => $order, 'general' => $general])

        <h6 class="mb-2 mt-3 fw-semibold"><i class="las la-shopping-bag me-1"></i>@lang('Ordered Products')</h6>
        <div class="table-responsive">
            <table class="table table--light style--two table-align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">@lang('Image')</th>
                        <th>@lang('Product')</th>
                        <th class="order-detail-size-col">@lang('Size / Variant')</th>
                        <th class="text-center order-detail-qty-col">@lang('Qty')</th>
                        <th class="text-end">@lang('Unit Price')</th>
                        <th class="text-end">@lang('Subtotal')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->orderDetail as $detail)
                        @php
                            $product = $detail->product;
                            $vDetail = null;
                            if (!empty($detail->variant_details)) {
                                $vDetail = is_string($detail->variant_details) ? json_decode($detail->variant_details, true) : $detail->variant_details;
                            }
                            $sizeText = null;
                            if (is_array($vDetail)) {
                                if (!empty($vDetail['custom_size'])) {
                                    $sizeText = __('Custom Size') . ': ' . $vDetail['custom_size'];
                                } else {
                                    $sizeText = $vDetail['size'] ?? $vDetail['name'] ?? $vDetail['variant'] ?? implode(', ', array_map(function ($k, $v) { return $k . ': ' . $v; }, array_keys($vDetail), $vDetail));
                                }
                            } elseif ($vDetail !== null) {
                                $sizeText = $detail->variant_details;
                            }
                        @endphp
                        <tr>
                            <td class="align-middle">
                                @if($product)
                                    <a href="{{ route('admin.product.edit', $product->id) }}" class="d-block text-center">
                                        @if($product->image)
                                            <img src="{{ getImage(getFilePath('product') . '/' . $product->image, getFileSize('product')) }}" alt="{{ __($product->name) }}" class="rounded border object-fit-cover" style="width: 56px; height: 56px; object-fit: contain;">
                                        @else
                                            <span class="d-inline-flex align-items-center justify-content-center rounded border bg-light" style="width: 56px; height: 56px;"><i class="las la-image text-muted"></i></span>
                                        @endif
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($product)
                                    <a href="{{ route('admin.product.edit', $product->id) }}" class="text--primary fw-medium">{{ __($product->name) }}</a>
                                    @if($product->product_sku)
                                        <br><small class="text-muted">@lang('SKU'): {{ $product->product_sku }}</small>
                                    @endif
                                    @if($product->warehouse_location)
                                        <br><small class="text-info"><i class="las la-warehouse"></i> @lang('Whouse'): {{ $product->warehouse_location }}</small>
                                    @endif
                                    @if($product->source_url)
                                        <br><small><a href="{{ $product->source_url }}" target="_blank" class="text-secondary text-decoration-underline"><i class="las la-link"></i> @lang('Source URL')</a></small>
                                    @endif
                                    @if(in_array($product->product_type, ['digital', 'service']))
                                        <br><span class="badge bg-indigo text-white mt-1" style="font-size: 0.65rem;"><i class="las la-download"></i> {{ ucfirst($product->product_type) }}</span>
                                    @endif
                                    @if($product->file)
                                        <br><a href="{{ route('download', [$product->id, $product->file]) }}" class="small text--primary mt-1 d-inline-block"><i class="las la-download"></i> @lang('Download')</a>
                                    @elseif($product->link)
                                        <br><a href="{{ $product->link }}" target="_blank" rel="noopener" class="small text--primary mt-1 d-inline-block"><i class="las la-external-link-alt"></i> @lang('Link')</a>
                                    @endif
                                @else
                                    <span class="text-muted">@lang('Product removed')</span>
                                @endif
                            </td>
                            <td class="align-middle order-detail-size-col">
                                @if($sizeText !== null)
                                    <span class="badge bg-primary">{{ __($sizeText) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center align-middle order-detail-qty-col"><strong class="order-detail-qty-value">{{ $detail->quantity }}</strong></td>
                            <td class="text-end align-middle">{{ showAmount($detail->price) }} {{ __($general->cur_text) }}</td>
                            <td class="text-end align-middle"><strong>{{ showAmount($detail->price * $detail->quantity) }} {{ __($general->cur_text) }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-muted text-center py-4" colspan="6">{{ __($emptyMessage ?? 'No items') }}</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end">@lang('Subtotal')</td>
                        <td class="text-end fw-bold">{{ showAmount($order->subtotal) }} {{ __($general->cur_text) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end">@lang('Shipping')</td>
                        <td class="text-end fw-bold">{{ showAmount($order->shipping_charge) }} {{ __($general->cur_text) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end">@lang('Discount')</td>
                        <td class="text-end fw-bold">− {{ showAmount($order->discount) }} {{ __($general->cur_text) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-end">@lang('Total')</td>
                        <td class="text-end fw-bold fs-6">{{ showAmount($order->total) }} {{ __($general->cur_text) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Location & Shipment Tracking – professional, map, timeline --}}
        <div class="mt-3 order-tracking-section">
            <div class="order-tracking-section__header d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <h6 class="mb-0 fw-semibold"><i class="las la-map-marker-alt me-1"></i>@lang('Location & Shipment Tracking')</h6>
            </div>
            @if(\Illuminate\Support\Facades\Schema::hasTable('order_shipment_trackings'))
            @php
                $trackings = $order->shipmentTrackings ?? collect();
                $trackingsChrono = $trackings->sortBy('created_at')->values();
                $latestWithCoords = $trackings->filter(fn($t) => $t->latitude && $t->longitude)->sortByDesc('created_at')->first();
                $address = is_string($order->address) ? json_decode($order->address, true) : (array) $order->address;
                $deliveryAddressLine = trim(implode(', ', array_filter([ data_get($address, 'address'), data_get($address, 'city'), data_get($address, 'division'), data_get($address, 'country') ]))) ?: '—';
                $lastTracking = $trackings->sortByDesc('created_at')->first();
                $pointsWithCoords = $trackingsChrono->filter(fn($t) => $t->latitude && $t->longitude);
                $googleMapsDirUrl = $pointsWithCoords->count() >= 2
                    ? 'https://www.google.com/maps/dir/' . $pointsWithCoords->map(fn($t) => $t->latitude . ',' . $t->longitude)->implode('/')
                    : ($latestWithCoords ? $latestWithCoords->map_url : null);
            @endphp

            {{-- Summary: Customer | Delivery address | Courier – manage from here --}}
            <div class="row g-2 mb-3 order-tracking-summary">
                <div class="col-md-4">
                    <div class="card order-tracking-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="order-tracking-card__icon bg-primary bg-opacity-10 text-primary rounded-2 p-2"><i class="las la-user"></i></span>
                                <span class="fw-semibold small text-uppercase text-muted">@lang('Customer')</span>
                            </div>
                            @if($order->isGuest())
                                <p class="mb-1 fw-medium">{{ $order->guest_name ?? '—' }}</p>
                                <p class="small text-muted mb-1">{{ $order->guest_email ?? '—' }}</p>
                                @if($order->guest_phone)
                                <p class="small mb-2">
                                    <a href="tel:{{ $order->guest_phone }}" class="text-decoration-none">{{ $order->guest_phone }}</a>
                                    @php
                                        $waNumber = preg_replace('/[^0-9]/', '', $order->guest_phone ?? '');
                                        if (substr($waNumber, 0, 1) === '0') $waNumber = '88' . substr($waNumber, 1);
                                        elseif (strlen($waNumber) === 11 && substr($waNumber, 0, 2) === '01') $waNumber = '88' . $waNumber;
                                        elseif (strlen($waNumber) === 10) $waNumber = '88' . $waNumber;
                                    @endphp
                                    @if($waNumber)
                                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="ms-2 btn btn-sm btn-success" title="@lang('WhatsApp')"><i class="lab la-whatsapp"></i></a>
                                    @endif
                                </p>
                                @endif
                            @else
                                <p class="mb-1 fw-medium">{{ $order->user->username ?? '—' }}</p>
                                <p class="small text-muted mb-1">{{ $order->user->email ?? '—' }}</p>
                                @if(class_exists(\App\Modules\FraudGuard\FraudGuardService::class))
                                    @php $orderCount = app(\App\Modules\FraudGuard\FraudGuardService::class)->orderCountForUser($order->user_id ?? null); @endphp
                                    @if($orderCount > 0)
                                        <p class="small mb-1"><span class="badge bg-info">@lang('Repeat customer') — {{ $orderCount }} @lang('orders')</span></p>
                                    @endif
                                @endif
                                @if($order->user && ($order->user->mobile ?? null))
                                    <p class="small mb-2">
                                        <a href="tel:{{ $order->user->mobile }}" class="text-decoration-none">{{ $order->user->mobile }}</a>
                                        @php
                                            $waNumber = preg_replace('/[^0-9]/', '', $order->user->mobile ?? '');
                                            if (substr($waNumber, 0, 1) === '0') $waNumber = '88' . substr($waNumber, 1);
                                            elseif (strlen($waNumber) === 11 && substr($waNumber, 0, 2) === '01') $waNumber = '88' . $waNumber;
                                            elseif (strlen($waNumber) === 10) $waNumber = '88' . $waNumber;
                                        @endphp
                                        @if($waNumber)
                                            <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener" class="ms-2 btn btn-sm btn-success" title="@lang('WhatsApp')"><i class="lab la-whatsapp"></i></a>
                                        @endif
                                    </p>
                                @endif
                                @if($order->user)
                                <a href="{{ route('admin.users.notification.single', $order->user->id) }}" class="btn btn-sm btn-outline-primary">@lang('Notify Customer')</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card order-tracking-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="order-tracking-card__icon bg-success bg-opacity-10 text-success rounded-2 p-2"><i class="las la-map-marker-alt"></i></span>
                                <span class="fw-semibold small text-uppercase text-muted">@lang('Delivery Address')</span>
                            </div>
                            <p class="small mb-2 order-tracking-address-text">{{ $deliveryAddressLine }}</p>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($deliveryAddressLine) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success"><i class="las la-external-link-alt"></i> @lang('Open in Google Maps')</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card order-tracking-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="order-tracking-card__icon bg-info bg-opacity-10 text-info rounded-2 p-2"><i class="las la-truck"></i></span>
                                <span class="fw-semibold small text-uppercase text-muted">@lang('Courier / Shipment')</span>
                            </div>
                            @if($lastTracking)
                                @if($lastTracking->courier_name)<p class="small fw-medium mb-1">{{ $lastTracking->courier_name }}</p>@endif
                                @if($lastTracking->tracking_number)
                                    <p class="small mb-2"><i class="las la-barcode"></i> {{ $lastTracking->tracking_number }}</p>
                                    @php
                                        $courierTrackUrl = !empty($lastTracking->tracking_link) ? $lastTracking->tracking_link : null;
                                        if (!$courierTrackUrl) {
                                            $courierLower = strtolower($lastTracking->courier_name ?? '');
                                            if (str_contains($courierLower, 'pathao')) $courierTrackUrl = 'https://pathao.com/courier/tracking?consignment_id=' . urlencode($lastTracking->tracking_number);
                                            elseif (str_contains($courierLower, 'steadfast')) $courierTrackUrl = 'https://steadfast.com.bd/tracking/' . urlencode($lastTracking->tracking_number);
                                        }
                                    @endphp
                                    @if($courierTrackUrl)
                                        <a href="{{ $courierTrackUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info">@lang('Track on Courier')</a>
                                    @endif
                                @else
                                    <p class="small text-muted mb-0">{{ \App\Models\OrderShipmentTracking::statusLabels()[$lastTracking->status] ?? $lastTracking->status }}</p>
                                @endif
                            @else
                                <p class="small text-muted mb-0">@lang('No tracking yet')</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                {{-- Add tracking update form – with Notify customer --}}
                <div class="col-lg-4">
                    <div class="card order-tracking-card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header order-tracking-card__header bg-transparent border-bottom">
                            <span class="fw-semibold"><i class="las la-plus-circle me-1"></i>@lang('Add tracking update')</span>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.orders.tracking.store', $order->id) }}" method="POST" class="order-tracking-form">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">@lang('Status') <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select form-select-sm" required>
                                        @foreach(\App\Models\OrderShipmentTracking::statusLabels() as $k => $v)
                                            <option value="{{ $k }}">{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">@lang('Location Name')</label>
                                    <input type="text" class="form-control form-control-sm" name="location_name" placeholder="@lang('e.g. Dhaka Hub')" maxlength="200">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">@lang('Location Address')</label>
                                    <input type="text" class="form-control form-control-sm" name="location_address" placeholder="@lang('Full address')" maxlength="500">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">@lang('Latitude')</label>
                                        <input type="text" class="form-control form-control-sm" name="latitude" placeholder="23.8103" step="any">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">@lang('Longitude')</label>
                                        <input type="text" class="form-control form-control-sm" name="longitude" placeholder="90.4125" step="any">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">@lang('Notes')</label>
                                    <input type="text" class="form-control form-control-sm" name="notes" placeholder="@lang('e.g. Package picked up')" maxlength="500">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">@lang('Tracking Number')</label>
                                        <input type="text" class="form-control form-control-sm" name="tracking_number" placeholder="e.g. STD123456789" maxlength="100">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">@lang('Courier Name')</label>
                                        <input type="text" class="form-control form-control-sm" name="courier_name" placeholder="@lang('e.g. Steadfast, Pathao')" maxlength="100">
                                    </div>
                                </div>
                                @if(\Illuminate\Support\Facades\Schema::hasColumn('order_shipment_trackings', 'tracking_link'))
                                <div class="mb-3">
                                    <label class="form-label">@lang('Tracking Link')</label>
                                    <input type="url" class="form-control form-control-sm" name="tracking_link" placeholder="https://courier.com/track/..." maxlength="500">
                                </div>
                                @endif
                                <div class="form-check mb-3">
                                    <input type="hidden" name="notify_user" value="0">
                                    <input class="form-check-input" type="checkbox" name="notify_user" value="1" id="trackingNotifyUser">
                                    <label class="form-check-label small" for="trackingNotifyUser">@lang('Notify customer about this update')</label>
                                </div>
                                <button type="submit" class="btn btn--primary btn-sm w-100"><i class="las la-plus me-1"></i>@lang('Add Tracking Update')</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Google Map + Timeline --}}
                <div class="col-lg-8">
                    {{-- Google Map: latest location or placeholder --}}
                    <div class="card order-tracking-card border-0 shadow-sm rounded-3 mb-2">
                        <div class="card-header order-tracking-card__header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="fw-semibold"><i class="las la-map me-1"></i>@lang('Location on Map')</span>
                            @if($googleMapsDirUrl)
                                <a href="{{ $googleMapsDirUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">@lang('Open full route in Google Maps')</a>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            @if($latestWithCoords)
                                <div class="order-tracking-map-wrap ratio ratio-16x9">
                                    <iframe src="https://www.google.com/maps?q={{ $latestWithCoords->latitude }},{{ $latestWithCoords->longitude }}&z=14&output=embed" class="order-tracking-map-iframe" allowfullscreen loading="lazy" title="@lang('Map')"></iframe>
                                </div>
                                <div class="p-2 small text-muted bg-light">{{ $latestWithCoords->location_name ?? __('Last known location') }} — {{ showDateTime($latestWithCoords->created_at) }}</div>
                            @else
                                <div class="order-tracking-map-placeholder d-flex align-items-center justify-content-center flex-column py-5 px-3">
                                    <i class="las la-map-marked-alt fa-3x text-muted mb-2"></i>
                                    <p class="small text-muted mb-2">@lang('Add a tracking update with latitude & longitude to see the map.')</p>
                                    <a href="https://www.google.com/maps" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary">@lang('Open Google Maps')</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Journey timeline (oldest first) --}}
                    <div class="card order-tracking-card border-0 shadow-sm rounded-3">
                        <div class="card-header order-tracking-card__header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                            <span class="fw-semibold"><i class="las la-route me-1"></i>@lang('Tracking history')</span>
                            @if($trackings->isNotEmpty())
                                <span class="badge bg-primary">{{ $trackings->count() }} @lang('updates')</span>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($trackings->isEmpty())
                                <p class="text-muted small mb-0 py-3">@lang('No tracking updates yet. Add one from the form.')</p>
                            @else
                                <div class="tracking-timeline-vertical">
                                    @foreach($trackingsChrono as $t)
                                        @php
                                            $statusLabel = \App\Models\OrderShipmentTracking::statusLabels()[$t->status] ?? $t->status;
                                            $icon = $t->status === \App\Models\OrderShipmentTracking::STATUS_DELIVERED ? 'las la-check-circle' : ($t->status === \App\Models\OrderShipmentTracking::STATUS_PICKED ? 'las la-box' : ($t->status === \App\Models\OrderShipmentTracking::STATUS_IN_TRANSIT ? 'las la-truck' : ($t->status === \App\Models\OrderShipmentTracking::STATUS_OUT_FOR_DELIVERY ? 'las la-shipping-fast' : 'las la-cog')));
                                        @endphp
                                        <div class="tracking-timeline-item">
                                            <div class="tracking-timeline-icon"><i class="{{ $icon }}"></i></div>
                                            <div class="tracking-timeline-body flex-grow-1 min-w-0">
                                                <span class="badge bg-primary mb-1">{{ __($statusLabel) }}</span>
                                                @if($t->location_name)<div class="small fw-medium">{{ $t->location_name }}</div>@endif
                                                @if($t->location_address)<div class="small text-muted">{{ $t->location_address }}</div>@endif
                                                @if($t->latitude && $t->longitude)
                                                    <a href="{{ $t->map_url }}" target="_blank" rel="noopener" class="small text-primary"><i class="las la-external-link-alt"></i> @lang('View on Google Maps')</a>
                                                @endif
                                                @if($t->notes)<div class="small mt-1">{{ $t->notes }}</div>@endif
                                                @if($t->tracking_number)
                                                    <div class="small text-info mt-1"><i class="las la-barcode"></i> {{ $t->tracking_number }}@if($t->courier_name) ({{ $t->courier_name }})@endif</div>
                                                @endif
                                                @if(!empty($t->tracking_link))
                                                    <a href="{{ $t->tracking_link }}" target="_blank" rel="noopener" class="small text-primary d-inline-block mt-1"><i class="las la-external-link-alt"></i> @lang('Track on courier')</a>
                                                @endif
                                                <div class="small text-muted mt-1">{{ showDateTime($t->created_at) }}</div>
                                            </div>
                                            <div class="tracking-timeline-actions flex-shrink-0">
                                                <form action="{{ route('admin.orders.tracking.destroy', [$order->id, $t->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __("Remove this update?") }}');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="@lang('Remove')"><i class="las la-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @else
            <p class="text-muted small">@lang('Run migration to enable tracking:') <code>php artisan migrate</code></p>
            @endif
        </div>
    </div>
</div>
