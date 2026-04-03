@php
    $general = $general ?? gs();
    $emptyMessage = $emptyMessage ?? __('Data not found');
    $gateways = $gateways ?? [];
    $currentRoute = request()->route()->getName();
    $exportUrl = route('admin.deposit.export') . '?' . http_build_query(array_merge(request()->only(['search', 'date', 'method']), ['scope' => $currentRoute === 'admin.deposit.list' ? 'all' : str_replace('admin.deposit.', '', $currentRoute)]));
    $count_pending = $count_pending ?? 0;
    $count_approved = $count_approved ?? 0;
    $count_successful = $count_successful ?? 0;
    $count_rejected = $count_rejected ?? 0;
    $count_initiated = $count_initiated ?? 0;
@endphp
@extends('admin.layouts.app')

@section('panel')
    <div class="row justify-content-center">
        @if(isset($successful) || isset($pending) || isset($rejected) || isset($initiated))
            <div class="col-12 mb-4">
                <div class="row g-3">
                    <div class="col-xxl-3 col-sm-6">
                        <a href="{{ route('admin.deposit.successful') }}" class="text-decoration-none">
                            <div class="widget-two box--shadow2 b-radius--5 bg--success h-100">
                                <div class="widget-two__content p-4">
                                    <h2 class="text-white mb-0">{{ $general->cur_sym ?? '' }}{{ showAmount($successful ?? 0) }}</h2>
                                    <p class="text-white opacity-90 mb-0 mt-1 small">@lang('Successful Payment')</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <a href="{{ route('admin.deposit.pending') }}" class="text-decoration-none">
                            <div class="widget-two box--shadow2 b-radius--5 bg--6 h-100">
                                <div class="widget-two__content p-4">
                                    <h2 class="text-white mb-0">{{ $general->cur_sym ?? '' }}{{ showAmount($pending ?? 0) }}</h2>
                                    <p class="text-white opacity-90 mb-0 mt-1 small">@lang('Pending Payment')</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <a href="{{ route('admin.deposit.rejected') }}" class="text-decoration-none">
                            <div class="widget-two box--shadow2 b-radius--5 bg--pink h-100">
                                <div class="widget-two__content p-4">
                                    <h2 class="text-white mb-0">{{ $general->cur_sym ?? '' }}{{ showAmount($rejected ?? 0) }}</h2>
                                    <p class="text-white opacity-90 mb-0 mt-1 small">@lang('Rejected Payment')</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-xxl-3 col-sm-6">
                        <a href="{{ route('admin.deposit.initiated') }}" class="text-decoration-none">
                            <div class="widget-two box--shadow2 b-radius--5 bg--dark h-100">
                                <div class="widget-two__content p-4">
                                    <h2 class="text-white mb-0">{{ $general->cur_sym ?? '' }}{{ showAmount($initiated ?? 0) }}</h2>
                                    <p class="text-white opacity-90 mb-0 mt-1 small">@lang('Initiated Payment')</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12">
            <div class="card b-radius--10 overflow-hidden">
                <div class="card-header bg--light d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h6 class="mb-0">@lang('Payment List')</h6>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <a href="{{ route('admin.deposit.list') }}" class="btn btn-sm {{ $currentRoute === 'admin.deposit.list' ? 'btn--primary' : 'btn-outline--primary' }}">@lang('All')</a>
                        <a href="{{ route('admin.deposit.pending') }}" class="btn btn-sm {{ $currentRoute === 'admin.deposit.pending' ? 'btn--warning' : 'btn-outline--warning' }}">@lang('Pending') @if($count_pending > 0)<span class="badge bg-white text-dark ms-1">{{ $count_pending }}</span>@endif</a>
                        <a href="{{ route('admin.deposit.approved') }}" class="btn btn-sm {{ $currentRoute === 'admin.deposit.approved' ? 'btn--info' : 'btn-outline--info' }}">@lang('Approved') @if($count_approved > 0)<span class="badge bg-white text-dark ms-1">{{ $count_approved }}</span>@endif</a>
                        <a href="{{ route('admin.deposit.successful') }}" class="btn btn-sm {{ $currentRoute === 'admin.deposit.successful' ? 'btn--success' : 'btn-outline--success' }}">@lang('Successful') @if($count_successful > 0)<span class="badge bg-white text-dark ms-1">{{ $count_successful }}</span>@endif</a>
                        <a href="{{ route('admin.deposit.rejected') }}" class="btn btn-sm {{ $currentRoute === 'admin.deposit.rejected' ? 'btn--danger' : 'btn-outline--danger' }}">@lang('Rejected') @if($count_rejected > 0)<span class="badge bg-white text-dark ms-1">{{ $count_rejected }}</span>@endif</a>
                        <a href="{{ route('admin.deposit.initiated') }}" class="btn btn-sm {{ $currentRoute === 'admin.deposit.initiated' ? 'btn--dark' : 'btn-outline--dark' }}">@lang('Initiated') @if($count_initiated > 0)<span class="badge bg-white text-dark ms-1">{{ $count_initiated }}</span>@endif</a>
                        @if(Route::has('admin.deposit.export'))
                        <a href="{{ $exportUrl }}" class="btn btn-sm btn-outline--primary ms-2" target="_blank" rel="noopener">
                            <i class="las la-download"></i> @lang('Export CSV')
                        </a>
                        @endif
                    </div>
                </div>
                @if(count($gateways) > 0)
                <div class="card-body border-bottom bg--light py-2">
                    <form method="GET" action="{{ request()->url() }}" class="row g-2 align-items-center">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <input type="hidden" name="date" value="{{ request('date') }}">
                        <label class="col-auto col-form-label small mb-0">@lang('Gateway'):</label>
                        <div class="col-auto">
                            <select name="method" class="form-control form-control-sm" style="min-width: 160px;">
                                <option value="">@lang('All Methods')</option>
                                @foreach($gateways as $gw)
                                    <option value="{{ $gw['alias'] ?? '' }}" {{ request('method') == ($gw['alias'] ?? '') ? 'selected' : '' }}>{{ __($gw['name'] ?? '') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn--primary">@lang('Filter')</button>
                        </div>
                    </form>
                </div>
                @endif
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>@lang('Gateway | Transaction')</th>
                                    <th>@lang('Initiated')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Conversion')</th>
                                    <th>@lang('Status')</th>
                                    <th class="text-end">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">
                                                <a href="{{ appendQuery('method', @$deposit->gateway->alias) }}" class="text--base">{{ __(@$deposit->gateway->name) }}</a>
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $deposit->trx }}</small>
                                            <button type="button" class="btn btn-sm btn-outline--secondary py-0 px-1 ms-1 copy-trx" data-trx="{{ $deposit->trx }}" title="@lang('Copy TRX')"><i class="las la-copy"></i></button>
                                        </td>
                                        <td>
                                            <span>{{ showDateTime($deposit->created_at) }}</span>
                                            <br>
                                            <small class="text-muted">{{ diffForHumans($deposit->created_at) }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $deposit->user ? ($deposit->user->fullname ?? $deposit->user->username) : 'N/A' }}</span>
                                            <br>
                                            <a href="{{ appendQuery('search', @$deposit->user->username) }}" class="small">@{{ $deposit->user->username ?? 'N/A' }}</a>
                                        </td>
                                        <td>
                                            {{ $general->cur_sym ?? '' }}{{ showAmount($deposit->amount) }}
                                            <span class="text-danger small" title="@lang('charge')">+{{ showAmount($deposit->charge) }}</span>
                                            <br>
                                            <strong>{{ showAmount($deposit->amount + $deposit->charge) }} {{ __($general->cur_text ?? '') }}</strong>
                                        </td>
                                        <td>
                                            1 {{ __($general->cur_text ?? '') }} = {{ showAmount($deposit->rate) }} {{ __($deposit->method_currency) }}
                                            <br>
                                            <strong>{{ showAmount($deposit->final_amo) }} {{ __($deposit->method_currency) }}</strong>
                                        </td>
                                        <td>@php echo $deposit->statusBadge; @endphp</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.deposit.details', $deposit->id) }}" class="btn btn-sm btn-outline--primary">
                                                <i class="la la-desktop"></i> @lang('Details')
                                            </a>
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
                @if ($deposits->hasPages())
                    <div class="card-footer py-3">
                        {{ paginateLinks($deposits) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    @if(!request()->routeIs('admin.users.deposits') && !request()->routeIs('admin.users.deposits.method'))
        <x-search-form dateSearch="yes" />
    @endif
@endpush

@push('script')
<script>
(function () {
    document.querySelectorAll('.copy-trx').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var trx = this.getAttribute('data-trx');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(trx).then(function() {
                    var icon = btn.querySelector('i');
                    if (icon) { icon.className = 'las la-check text-success'; }
                    setTimeout(function() { if (icon) { icon.className = 'las la-copy'; } }, 2000);
                });
            } else {
                var ta = document.createElement('textarea'); ta.value = trx; document.body.appendChild(ta); ta.select();
                document.execCommand('copy'); document.body.removeChild(ta);
                var icon = btn.querySelector('i');
                if (icon) { icon.className = 'las la-check text-success'; }
                setTimeout(function() { if (icon) { icon.className = 'las la-copy'; } }, 2000);
            }
        });
    });
})();
</script>
@endpush
