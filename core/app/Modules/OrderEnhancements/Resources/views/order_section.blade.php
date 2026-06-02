@php
    $hasAdvance = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'advance_payment');
    $hasStaffNotes = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'staff_notes');
    $order = $order ?? null;
    $general = $general ?? gs();
    $orderTotal = (float) ($order->total ?? 0);
    $advancePaid = $hasAdvance ? (float) ($order->advance_payment ?? 0) : 0;
    $balanceDue = max(0, $orderTotal - $advancePaid);
@endphp
@if($order && ($hasAdvance || $hasStaffNotes))
<div class="card border-0 shadow-sm rounded-3 order-detail-card mt-3" id="order-advance-panel">
    <div class="card-header bg-transparent border-bottom py-2 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h6 class="card-title mb-0 fw-semibold"><i class="las la-wallet me-1"></i>@lang('Advance Payment & Staff Notes')</h6>
        @if($hasAdvance)
            <div class="d-flex flex-wrap gap-2 small">
                <span class="badge bg-label-primary">@lang('Order'): {{ showAmount($orderTotal) }}</span>
                <span class="badge bg-label-success">@lang('Advance'): {{ showAmount($advancePaid) }}</span>
                <span class="badge bg-label-warning text-dark" id="order-balance-due-badge">@lang('Due'): {{ showAmount($balanceDue) }}</span>
            </div>
        @endif
    </div>
    <div class="card-body">
        <form action="{{ route('admin.orders.enhancements.update', $order->id) }}" method="POST" id="order-advance-form">
            @csrf
            <div class="row g-3">
                @if($hasAdvance)
                <div class="col-md-4">
                    <label class="form-label fw-semibold">@lang('Advance Payment')</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="advance_payment" id="advance_payment_input"
                            value="{{ $advancePaid }}" min="0" max="{{ $orderTotal }}" step="0.01" placeholder="0"
                            data-order-total="{{ $orderTotal }}">
                        <span class="input-group-text">{{ __($general->cur_sym ?? '৳') }}</span>
                    </div>
                    <div class="form-text">@lang('Remaining due') : <strong id="advance_balance_preview">{{ showAmount($balanceDue) }}</strong></div>
                </div>
                @endif
                @if($hasStaffNotes)
                <div class="col-md-8">
                    <label class="form-label fw-semibold">@lang('Staff Notes')</label>
                    <textarea class="form-control" name="staff_notes" rows="2" placeholder="@lang('Internal notes (not visible to customer)')" maxlength="5000">{{ $order->staff_notes ?? '' }}</textarea>
                </div>
                @endif
                <div class="col-12">
                    <button type="submit" class="btn btn--primary btn-sm"><i class="las la-save me-1"></i>@lang('Save')</button>
                </div>
            </div>
        </form>
    </div>
</div>
@if($hasAdvance)
@push('script')
<script>
(function () {
    var input = document.getElementById('advance_payment_input');
    var preview = document.getElementById('advance_balance_preview');
    var badge = document.getElementById('order-balance-due-badge');
    if (!input || !preview) return;
    var total = parseFloat(input.getAttribute('data-order-total')) || 0;
    var sym = @json($general->cur_sym ?? '৳');
    function formatAmount(n) {
        return sym + ' ' + Number(n).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function refresh() {
        var adv = Math.max(0, parseFloat(input.value) || 0);
        if (adv > total) {
            adv = total;
            input.value = total.toFixed(2);
        }
        var due = Math.max(0, total - adv);
        preview.textContent = formatAmount(due);
        if (badge) badge.textContent = @json(__('Due')) + ': ' + formatAmount(due);
    }
    input.addEventListener('input', refresh);
    input.addEventListener('change', refresh);
})();
</script>
@endpush
@endif
@endif
