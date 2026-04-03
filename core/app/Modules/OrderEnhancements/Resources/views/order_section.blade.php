@php
    $hasAdvance = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'advance_payment');
    $hasStaffNotes = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'staff_notes');
    $order = $order ?? null;
    $general = $general ?? gs();
@endphp
@if($order && ($hasAdvance || $hasStaffNotes))
<div class="card border-0 shadow-sm rounded-3 order-detail-card mt-3">
    <div class="card-header bg-transparent border-bottom py-2">
        <h6 class="card-title mb-0 fw-semibold"><i class="las la-wallet me-1"></i>@lang('Advance Payment & Staff Notes')</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.orders.enhancements.update', $order->id) }}" method="POST">
            @csrf
            <div class="row g-3">
                @if($hasAdvance)
                <div class="col-md-4">
                    <label class="form-label fw-semibold">@lang('Advance Payment')</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="advance_payment" value="{{ $order->advance_payment ?? 0 }}" min="0" step="0.01" placeholder="0">
                        <span class="input-group-text">{{ __($general->cur_sym ?? '৳') }}</span>
                    </div>
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
@endif
