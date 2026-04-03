@php
    use App\Constants\Status;
    $steps = [
        ['key' => Status::ORDER_PENDING,   'label' => __('Pending'),   'icon' => 'clock',        'where' => __('Order received. We are processing it.')],
        ['key' => Status::ORDER_CONFIRMED, 'label' => __('Confirmed'), 'icon' => 'check-circle', 'where' => __('Order confirmed. Preparing for shipment.')],
        ['key' => Status::ORDER_SHIPPED,   'label' => __('Shipped'),   'icon' => 'truck',        'where' => __('Your order is on the way to you.')],
        ['key' => Status::ORDER_DELIVERED, 'label' => __('Delivered'), 'icon' => 'box',          'where' => __('Delivered. Thank you for your order!')],
    ];
    $currentStep = (int) $order->order_status;
    $isCancelled = $order->order_status == Status::ORDER_CANCEL;
@endphp

@if($isCancelled)
    <div class="track-result-card card border-0 shadow-sm">
        <div class="card-body text-center py-4">
            <span class="text-danger" style="font-size: 2.25rem;">@include($activeTemplate . 'partials.icon', ['name' => 'times-circle'])</span>
            <h5 class="mt-2 mb-1 text--danger" style="font-size: 1.1rem;">{{ __($emptyMessage) }}</h5>
            <p class="text-muted mb-0 small">@lang('Order No:') <strong>{{ $order->order_no }}</strong></p>
        </div>
    </div>
@else
    <div class="track-result-card card border-0 shadow-sm mb-0 track-result-card--pro">
        <div class="card-header track-result-header">
            <div class="track-result-header__top">
                <div class="track-result-header__info">
                    <span class="track-result-header__label">@lang('Order')</span>
                    <strong class="track-result-header__no">{{ $order->order_no }}</strong>
                    <span class="track-result-header__date">@lang('Placed') {{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="track-result-header__badges">
                    <span class="track-result-status-badge track-result-status-badge--{{ $currentStep }}">
                        @foreach($steps as $s) @if($s['key'] == $currentStep) {{ $s['label'] }} @endif @endforeach
                    </span>
                    <span class="track-result-header__total">{{ $general->cur_sym }}{{ showAmount($order->total) }}</span>
                </div>
            </div>
        </div>
        <div class="card-body track-result-body">
            {{-- Where is your product – prominent --}}
            <div class="track-where-box">
                <div class="track-where-box__icon">@include($activeTemplate . 'partials.icon', ['name' => 'map-marker-alt'])</div>
                <div class="track-where-box__content">
                    <h3 class="track-where-box__title">@lang('Where is your order?')</h3>
                    @foreach($steps as $s)
                        @if($s['key'] == $currentStep)
                            <p class="track-where-box__text">{{ $s['where'] }}</p>
                            @break
                        @endif
                    @endforeach
                </div>
            </div>

            <h4 class="track-section-title">@lang('Order progress')</h4>
            <div class="track-timeline">
                @foreach($steps as $index => $step)
                    @php $done = $currentStep >= $step['key']; $active = $currentStep == $step['key']; @endphp
                    <div class="track-timeline__item {{ $done ? 'done' : '' }} {{ $active ? 'active' : '' }}">
                        <div class="track-timeline__marker">
                            @if($done && !$active)
                                @include($activeTemplate . 'partials.icon', ['name' => 'check'])
                            @else
                                @include($activeTemplate . 'partials.icon', ['name' => $step['icon']])
                            @endif
                        </div>
                        <div class="track-timeline__content">
                            <h6 class="mb-0">{{ $step['label'] }}</h6>
                            @if($active)
                                <small class="text-muted">{{ $step['where'] }}</small>
                            @endif
                        </div>
                        @if(!$loop->last)
                            <div class="track-timeline__line"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if(\Illuminate\Support\Facades\Schema::hasTable('order_shipment_trackings') && ($order->shipmentTrackings ?? collect())->isNotEmpty())
            <div class="mt-3 pt-2 border-top">
                <h6 class="mb-2 small">@include($activeTemplate . 'partials.icon', ['name' => 'map-marker-alt']) @lang('Location & Shipment Tracking')</h6>
                <div class="tracking-timeline">
                    @foreach($order->shipmentTrackings->sortByDesc('created_at') as $t)
                        <div class="d-flex mb-2 pb-2 {{ !$loop->last ? 'border-bottom' : '' }} gap-2">
                            <div class="flex-shrink-0">
                                <span class="badge badge--primary" style="font-size: 0.7rem;">{{ __(\App\Models\OrderShipmentTracking::statusLabels()[$t->status] ?? $t->status) }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                @if($t->location_name)<strong class="small">{{ $t->location_name }}</strong><br>@endif
                                @if($t->location_address)<small class="text-muted">{{ $t->location_address }}</small><br>@endif
                                @if($t->latitude && $t->longitude)<a href="{{ $t->map_url }}" target="_blank" class="text-primary small">@include($activeTemplate . 'partials.icon', ['name' => 'map-marked-alt']) @lang('View on Map')</a><br>@endif
                                @if($t->notes)<small>{{ $t->notes }}</small><br>@endif
                                @if($t->tracking_number)<small class="text-info">@lang('Tracking #'): {{ $t->tracking_number }}</small><br>@endif
                                <small class="text-muted">{{ showDateTime($t->created_at) }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if(isset($isOwner) && $isOwner && !empty($order->address))
                @php $addr = is_string($order->address) ? json_decode($order->address) : $order->address; @endphp
                @if($addr)
                    <div class="track-delivery-address mt-4 pt-3 border-top">
                        <h4 class="track-section-title">@include($activeTemplate . 'partials.icon', ['name' => 'map-pin']) @lang('Delivery address')</h4>
                        <p class="track-delivery-address__text">
                            {{ __($addr->address ?? '') }}
                            @if(!empty($addr->city))<br>{{ __($addr->city ?? '') }}@endif
                            @if(!empty($addr->state)) {{ __($addr->state ?? '') }}@endif
                            @if(!empty($addr->zip)) {{ $addr->zip ?? '' }}@endif
                            @if(!empty($addr->country))<br>{{ __($addr->country ?? '') }}@endif
                        </p>
                    </div>
                @endif
            @endif

            @if($order->relationLoaded('orderDetail') && $order->orderDetail->isNotEmpty())
                <div class="mt-3 pt-2 border-top">
                    <h6 class="mb-1 small">@lang('Products in this order')</h6>
                    <ul class="list-group list-group-flush">
                        @foreach($order->orderDetail as $detail)
                            <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center small">
                                <span class="text-truncate">{{ $detail->product ? __($detail->product->name) : '#' . $detail->product_id }}</span>
                                <span class="badge bg-light text-dark flex-shrink-0">x{{ $detail->quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endif
