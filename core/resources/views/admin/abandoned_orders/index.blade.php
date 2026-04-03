@php
    $stats = $stats ?? ['total' => 0, 'potential_value' => 0, 'with_contact' => 0];
    $emptyMessage = $emptyMessage ?? __('No abandoned carts found.');
@endphp
@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 rounded-3 bg-warning bg-opacity-10 p-2">
                            <i class="las la-shopping-cart text-warning fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted small mb-0">@lang('Abandoned Carts')</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['total']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 rounded-3 bg--primary bg-opacity-10 p-2">
                            <i class="las la-coins text--primary fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted small mb-0">@lang('Potential Value')</p>
                            <h5 class="mb-0 fw-bold">{{ $general->cur_sym ?? '৳' }}{{ number_format($stats['potential_value'], 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm bg-white rounded-3 h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 rounded-3 bg-success bg-opacity-10 p-2">
                            <i class="las la-envelope text-success fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted small mb-0">@lang('With Contact Info')</p>
                            <h5 class="mb-0 fw-bold">{{ number_format($stats['with_contact']) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ request()->url() }}" class="row g-3 align-items-end flex-wrap">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small mb-0">@lang('Search')</label>
                    <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="@lang('Email / Phone / Session')">
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small mb-0">@lang('Status')</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">@lang('All')</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                        <option value="abandoned" {{ request('status') === 'abandoned' ? 'selected' : '' }}>@lang('Abandoned')</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small mb-0">@lang('Date from')</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small mb-0">@lang('Date to')</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn btn--primary btn-sm w-100">@lang('Apply')</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 b-radius--10">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>@lang('User / Session')</th>
                            <th>@lang('Products')</th>
                            <th>@lang('Cart Value')</th>
                            <th>@lang('Last Activity')</th>
                            <th>@lang('Contact')</th>
                            <th>@lang('Reminder')</th>
                            <th class="text-end">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($abandonedCarts as $ac)
                            <tr>
                                <td>
                                    @if($ac->user_id && $ac->user)
                                        <a href="{{ route('admin.users.detail', $ac->user_id) }}" class="text--primary fw-medium">{{ $ac->user->username ?? $ac->email ?? '#' . $ac->id }}</a>
                                        <br><small class="text-muted">ID: {{ $ac->user_id }}</small>
                                    @else
                                        <span class="text-muted">Guest</span>
                                        <br><small class="text-muted">{{ Str::limit($ac->session_id, 12) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($ac->cart_snapshot))
                                        @foreach(array_slice($ac->cart_snapshot, 0, 2) as $item)
                                            <div>{{ $item['product_name'] ?? 'Product' }} × {{ $item['quantity'] ?? 0 }}</div>
                                        @endforeach
                                        @if(count($ac->cart_snapshot) > 2)
                                            <small class="text-muted">+{{ count($ac->cart_snapshot) - 2 }} more</small>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><strong>{{ $general->cur_sym ?? '৳' }}{{ number_format($ac->cart_value ?? 0, 2) }}</strong></td>
                                <td><span class="text-muted">{{ $ac->last_activity_at ? $ac->last_activity_at->format('M d, Y H:i') : '—' }}</span></td>
                                <td>
                                    @if($ac->user_id && $ac->user)
                                        <div>{{ $ac->user->email ?? '—' }}</div>
                                        @if($ac->user->mobile ?? null)<div>{{ $ac->user->mobile }}</div>@endif
                                    @else
                                        <div>{{ $ac->email ?? '—' }}</div>
                                        @if($ac->mobile)<div>{{ $ac->mobile }}</div>@endif
                                    @endif
                                </td>
                                <td>
                                    @if($ac->reminder_sent_at)
                                        <small class="text-muted">{{ $ac->reminder_sent_at->format('M d H:i') }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        @if($ac->recovery_token)
                                            <a href="{{ $ac->recovery_url }}" target="_blank" class="btn btn-outline--primary" title="@lang('Open recovery link')"><i class="las la-external-link-alt"></i></a>
                                            <button type="button" class="btn btn-outline--info copy-recovery-link" data-url="{{ $ac->recovery_url }}" title="@lang('Copy recovery link')"><i class="las la-copy"></i></button>
                                        @endif
                                        @if(($ac->email || ($ac->user && $ac->user->email)) || ($ac->mobile || ($ac->user && $ac->user->mobile)))
                                            <form action="{{ route('admin.abandoned-orders.send-reminder', $ac->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline--success" title="@lang('Send reminder')"><i class="las la-bell"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-muted py-5" colspan="7">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($abandonedCarts->hasPages())
            <div class="card-footer py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">@lang('Showing') {{ $abandonedCarts->firstItem() }} - {{ $abandonedCarts->lastItem() }} @lang('of') {{ $abandonedCarts->total() }}</small>
                {{ $abandonedCarts->links() }}
            </div>
        @endif
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline--primary me-2"><i class="las la-arrow-left"></i> @lang('Back')</a>
    <a href="{{ route('admin.abandoned-orders.settings') }}" class="btn btn-sm btn-outline--primary"><i class="las la-cog"></i> @lang('Settings')</a>
@endpush

@push('script')
<script>
(function () {
    document.querySelectorAll('.copy-recovery-link').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-url');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    var t = btn.innerHTML; btn.innerHTML = '<i class="las la-check"></i>'; setTimeout(function() { btn.innerHTML = t; }, 1500);
                });
            } else {
                var inp = document.createElement('input'); inp.value = url; document.body.appendChild(inp); inp.select(); document.execCommand('copy'); document.body.removeChild(inp);
                var t = btn.innerHTML; btn.innerHTML = '<i class="las la-check"></i>'; setTimeout(function() { btn.innerHTML = t; }, 1500);
            }
        });
    });
})();
</script>
@endpush
