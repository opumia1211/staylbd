{{-- Status change / Cancel confirmation --}}
<div id="orderStatusModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="orderStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderStatusModalLabel">@lang('Confirmation')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="modal-detail mb-0"></p>
                    <input type="hidden" name="order_status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Pathao --}}
<div class="modal fade" id="pathaoModal" tabindex="-1" aria-labelledby="pathaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pathaoModalLabel">@lang('Send to Pathao')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
            </div>
            <form action="{{ route('admin.orders.pathao') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">@lang('Store')</label>
                            <select class="form-select" name="pathaostore" id="pathaostore" required>
                                <option value="">@lang('Select Store')</option>
                                @if(isset($pathaostore['data']))
                                    @foreach($pathaostore['data'] as $store)
                                        <option value="{{ $store['store_id'] ?? $store['id'] ?? '' }}">{{ $store['store_name'] ?? $store['name'] ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('City')</label>
                            <select class="form-select" name="pathaocity" id="pathaocity" required>
                                <option value="">@lang('Select City')</option>
                                @if(isset($pathaocities['data']))
                                    @foreach($pathaocities['data'] as $city)
                                        <option value="{{ $city['city_id'] ?? $city['id'] ?? '' }}">{{ $city['city_name'] ?? $city['name'] ?? '' }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Zone')</label>
                            <select class="form-select" name="pathaozone" id="pathaozone" required>
                                <option value="">@lang('Select Zone')</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Area')</label>
                            <input type="text" class="form-control" name="pathaoarea" id="pathaoarea" required placeholder="@lang('Area')">
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Selected Orders')</label>
                            <div id="selectedOrders" class="border rounded p-2 bg-light" style="max-height: 200px; overflow-y: auto;">
                                <p class="text-muted mb-0 small">@lang('No orders selected')</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Send to Pathao')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Steadfast --}}
<div class="modal fade" id="steadfastModal" tabindex="-1" aria-labelledby="steadfastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="steadfastModalLabel">@lang('Send to Steadfast')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
            </div>
            <form action="{{ route('admin.orders.steadfast') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">@lang('Consignment Type')</label>
                            <select class="form-select" name="consignment_type" required>
                                <option value="1">@lang('Document')</option>
                                <option value="2">@lang('Parcel')</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Delivery Type')</label>
                            <select class="form-select" name="delivery_type" required>
                                <option value="1">@lang('Normal')</option>
                                <option value="2">@lang('Express')</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('City')</label>
                            <input type="text" class="form-control" name="city" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">@lang('Area')</label>
                            <input type="text" class="form-control" name="area" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">@lang('Selected Orders')</label>
                            <div id="selectedOrdersSteadfast" class="border rounded p-2 bg-light" style="max-height: 200px; overflow-y: auto;">
                                <p class="text-muted mb-0 small">@lang('No orders selected')</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Send to Steadfast')</button>
                </div>
            </form>
        </div>
    </div>
</div>
