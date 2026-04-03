@extends('admin.layouts.app')

@section('panel')
    {{-- Header: Provider + Quick stats --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card b-radius--10 border-0 shadow-sm">
                <div class="card-body py-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="widget-icon bg--primary rounded p-3">
                                <i class="{{ $driver->getIcon() }} text-white fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">@lang('Bulk Shipments'): {{ $currentProvider->name ?: ucfirst($currentProvider->type) }}</h5>
                                <p class="text-muted small mb-0">@lang('Country') {{ strtoupper($currentProvider->country_code ?: 'INTL') }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <div class="d-flex gap-3 me-2">
                                <span class="badge badge--primary px-3 py-2">@lang('Orders') {{ $orders->total() ?? 0 }}</span>
                                <span class="badge badge--success px-3 py-2">@lang('Linked') {{ count($sentOrderIds ?? []) }}</span>
                            </div>
                            @foreach($activeProviders ?? [] as $p)
                                <a href="{{ route('admin.orders.bulk.courier', $p->type) }}"
                                    class="btn btn-sm {{ $currentProvider->type === $p->type ? 'btn--primary' : 'btn-outline--secondary' }} px-3">
                                    {{ $p->name ?: ucfirst($p->type) }}
                                </a>
                            @endforeach
                            <a href="{{ route('admin.api.courier.manage') }}" class="btn btn-sm btn--dark px-3"><i class="las la-cog"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Actions --}}
    <div class="card b-radius--10 border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-6">
                    <form method="get" action="{{ request()->url() }}" class="d-flex gap-2 flex-wrap">
                        <input type="text" name="search" class="form-control form-control-sm" style="max-width: 220px;" value="{{ request('search') }}" placeholder="@lang('Order # or customer...')">
                        <button type="submit" class="btn btn--primary btn-sm">@lang('Search')</button>
                        @if(request()->anyFilled(['search', 'date_from', 'date_to']))
                            <a href="{{ request()->url() }}" class="btn btn--dark btn-sm">@lang('Clear')</a>
                        @endif
                    </form>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="small text-muted d-none" id="selectionCounterBox"><span id="selectionCount">0</span> @lang('selected')</span>
                    <button type="button" class="btn btn-sm btn--success d-none ms-2" id="bulkSendBtn" data-toggle="modal" data-target="#unifiedCourierModal">
                        <i class="las la-paper-plane me-1"></i> @lang('Send to Courier')
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="card b-radius--10 border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <div class="form-check ms-2">
                                    <input class="form-check-input" type="checkbox" id="selectAll">
                                </div>
                            </th>
                            <th>@lang('Order Info')</th>
                            <th>@lang('Customer')</th>
                            <th>@lang('Total Amount')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('API Status')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="{{ in_array($order->id, $sentOrderIds ?? []) ? 'opacity-75' : '' }}">
                                <td>
                                    <div class="form-check ms-2">
                                        <input class="form-check-input order-checkbox" type="checkbox" value="{{ $order->id }}"
                                            data-no="{{ $order->order_no }}" data-user="{{ $order->isGuest() ? ($order->guest_name ?? 'N/A') : ($order->user->username ?? 'N/A') }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">#{{ $order->order_no }}</div>
                                    <div class="small text-muted">{{ $order->created_at->format('M d, Y H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $order->isGuest() ? ($order->guest_name ?? 'N/A') : ($order->user->fullname ?? 'N/A') }}</div>
                                    <div class="small text-muted">{{ $order->isGuest() ? ($order->guest_phone ?? 'N/A') : ($order->user->mobile ?? 'N/A') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">
                                        {{ $general->cur_sym }}{{ number_format($order->total, 2) }}</div>
                                    <div class="small text-muted text-uppercase">
                                        {{ $order->payment_type == 1 ? __('Online') : __('COD') }}</div>
                                </td>
                                <td>
                                    @php echo $order->ordersBadge; @endphp
                                </td>
                                <td>
                                    @if(in_array($order->id, $sentOrderIds ?? []))
                                        <span class="badge badge--success">@lang('Linked')</span>
                                    @else
                                        <span class="badge badge--warning">@lang('Pending')</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.detail', $order->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-eye"></i> @lang('Details')</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center py-5" colspan="7">
                                    <div class="mb-2"><i class="las la-inbox fs-1 opacity-25"></i></div>
                                    @lang('No confirmed orders waiting for shipment')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-transparent border-0 py-4">
                {{ paginateLinks($orders) }}
            </div>
        @endif
    </div>

    {{-- Unified Courier Modal (Bootstrap 4) --}}
    <div class="modal fade" id="unifiedCourierModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content b-radius--10 overflow-hidden">
                <form action="{{ route('admin.orders.bulk.courier.send') }}" method="POST" id="courierSendForm">
                    @csrf
                    <input type="hidden" name="courier_type" value="{{ $currentProvider->type }}">

                    <div class="modal-header bg--success py-3 border-0">
                        <div class="d-flex align-items-center gap-2">
                            <i class="{{ $driver->getIcon() }} text-white fs-4"></i>
                            <h5 class="modal-title text-white mb-0">@lang('Submit to') {{ $currentProvider->name ?: ucfirst($currentProvider->type) }}</h5>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 mb-4">
                            <i class="las la-info-circle"></i>
                            @lang('You are sending') <strong id="modalOrderCount">0</strong> @lang('orders to the courier API.')
                        </div>

                        <div class="row g-4">
                            {{-- Specific fields for Pathao --}}
                            @if($currentProvider->type === 'pathao')
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">@lang('Store')</label>
                                    <select class="form-select bg-light border-0" name="pathaostore" required>
                                        <option value="">@lang('Select Store')</option>
                                        @foreach($driverOptions['stores'] ?? [] as $store)
                                            <option value="{{ $store['store_id'] }}">{{ $store['store_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">@lang('City')</label>
                                    <select class="form-select bg-light border-0" name="pathaocity" id="pathaocity" required>
                                        <option value="">@lang('Select City')</option>
                                        @foreach($driverOptions['cities'] ?? [] as $city)
                                            <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">@lang('Zone')</label>
                                    <select class="form-select bg-light border-0" name="pathaozone" id="pathaozone" required>
                                        <option value="">@lang('Select City First')</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">@lang('Area')</label>
                                    <input type="text" class="form-control bg-light border-0" name="pathaoarea" required
                                        placeholder="@lang('Specific delivery area')">
                                </div>
                            @endif

                            {{-- Generic/Shared fields --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold">@lang('Consignment/Delivery Type')</label>
                                <select class="form-select bg-light border-0" name="delivery_type">
                                    <option value="1">@lang('Regular / Express')</option>
                                    <option value="2">@lang('Standard / Economy')</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">@lang('Standard Weight (Kg)')</label>
                                <input type="number" step="0.1" class="form-control bg-light border-0" name="weight"
                                    value="0.5">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">@lang('Global Shipping Notes')</label>
                                <textarea class="form-control bg-light border-0" name="notes" rows="2"
                                    placeholder="@lang('Instructions for the courier...')"></textarea>
                            </div>
                        </div>

                        <div id="selectedOrderInputs"></div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--success">@lang('Confirm & Send')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline--primary"><i class="las la-arrow-left"></i> @lang('Back to Orders')</a>
@endpush

@push('script')
    <script>
        (function () {
            "use strict";

            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.order-checkbox');
            const bulkSendBtn = document.getElementById('bulkSendBtn');
            const counterBox = document.getElementById('selectionCounterBox');
            const counterSpan = document.getElementById('selectionCount');
            const modalCounter = document.getElementById('modalOrderCount');
            const hiddenInputs = document.getElementById('selectedOrderInputs');

            function updateUI() {
                const checked = document.querySelectorAll('.order-checkbox:checked');
                const count = checked.length;

                counterSpan.textContent = count;
                modalCounter.textContent = count;

                if (count > 0) {
                    bulkSendBtn.classList.remove('d-none');
                    counterBox.classList.remove('d-none');
                } else {
                    bulkSendBtn.classList.add('d-none');
                    counterBox.classList.add('d-none');
                }

                // Sync hidden inputs for form
                hiddenInputs.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'order_ids[]';
                    input.value = cb.value;
                    hiddenInputs.appendChild(input);
                });
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    updateUI();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    updateUI();
                    if (!cb.checked) selectAll.checked = false;
                    else if (document.querySelectorAll('.order-checkbox:checked').length === checkboxes.length) selectAll.checked = true;
                });
            });

            // Pathao dynamic zones
            const citySelect = document.getElementById('pathaocity');
            if (citySelect) {
                citySelect.addEventListener('change', function () {
                    const cityId = this.value;
                    const zoneSelect = document.getElementById('pathaozone');
                    if (!cityId) return;

                    zoneSelect.innerHTML = '<option value="">@lang("Loading...")</option>';
                    fetch('{{ route("admin.orders.pathao.zone") }}?city_id=' + cityId)
                        .then(r => r.json())
                        .then(res => {
                            zoneSelect.innerHTML = '<option value="">@lang("Select Zone")</option>';
                            if (res.data) {
                                res.data.forEach(z => {
                                    const opt = document.createElement('option');
                                    opt.value = z.zone_id;
                                    opt.textContent = z.zone_name;
                                    zoneSelect.appendChild(opt);
                                });
                            }
                        });
                });
            }

        })();
    </script>
@endpush