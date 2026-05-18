@php
    $stats = $stats ?? ['total' => 0, 'potential_value' => 0, 'with_contact' => 0];
    $emptyMessage = $emptyMessage ?? __('No abandoned carts found.');
@endphp

@extends('admin.layouts.app')

@section('panel')
<div class="abandoned-orders-wrapper animate__animated animate__fadeIn">
    {{-- 1. Top Intelligence Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-warning shadow-sm"><i class="las la-shopping-cart fs-3"></i></span>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">@lang('Abandoned Carts & Incomplete Orders')</h5>
                    <p class="text-muted small mb-0">@lang('Strategic tracking of customer drop-offs and potential revenue recovery opportunities.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex gap-2">
                <div class="badge bg-label-primary rounded-pill px-3 py-2 shadow-sm border border-primary border-opacity-10">
                    <i class="las la-clock me-1"></i> {{ now()->format('h:i A') }} @lang('Real-time sync')
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Performance Matrix --}}
    <div class="row mb-4 g-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 transition-all hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md flex-shrink-0 me-3">
                            <span class="avatar-initial rounded-3 bg-label-warning"><i class="las la-shopping-basket fs-3"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['total']) }}</h4>
                            <small class="text-muted text-uppercase fw-bold tracking-wider tiny">@lang('Active Carts')</small>
                        </div>
                    </div>
                    <div class="progress rounded-pill mb-2" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mb-0 small"><span class="text-warning fw-bold">+12%</span> <span class="text-muted">@lang('vs last 24h')</span></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 transition-all hover-lift border-start border-4 border-success">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md flex-shrink-0 me-3">
                            <span class="avatar-initial rounded-3 bg-label-success"><i class="las la-money-bill-wave fs-3"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $general->cur_sym }}{{ number_format($stats['potential_value'], 2) }}</h4>
                            <small class="text-muted text-uppercase fw-bold tracking-wider tiny">@lang('Recoverable Revenue')</small>
                        </div>
                    </div>
                    <div class="progress rounded-pill mb-2" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mb-0 small text-muted">@lang('Based on pending & abandoned states')</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 transition-all hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md flex-shrink-0 me-3">
                            <span class="avatar-initial rounded-3 bg-label-info"><i class="las la-id-card fs-3"></i></span>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ number_format($stats['with_contact']) }}</h4>
                            <small class="text-muted text-uppercase fw-bold tracking-wider tiny">@lang('Lead Conversions')</small>
                        </div>
                    </div>
                    <div class="progress rounded-pill mb-2" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 45%" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="mb-0 small"><span class="text-info fw-bold">{{ $stats['total'] > 0 ? round(($stats['with_contact']/$stats['total'])*100) : 0 }}%</span> <span class="text-muted">@lang('contact details captured')</span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Search & Filter Command Center --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ request()->url() }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold text-dark small">@lang('Intelligence Search')</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text bg-light border-end-0"><i class="las la-search text-muted"></i></span>
                        <input type="text" class="form-control rounded-start-0" name="search" value="{{ request('search') }}" placeholder="@lang('Email, Phone or Session ID...')">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-dark small">@lang('Cart Status')</label>
                    <select class="form-select select2-basic" name="status">
                        <option value="">@lang('All Nodes')</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>@lang('Pending')</option>
                        <option value="abandoned" {{ request('status') === 'abandoned' ? 'selected' : '' }}>@lang('Abandoned')</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-dark small">@lang('Chronology From')</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fw-bold text-dark small">@lang('Chronology To')</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-6 col-md-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm py-2">
                        <i class="las la-filter me-1"></i> @lang('Filter Data')
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. Abandoned Carts Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold d-flex align-items-center">
                <i class="las la-list text-primary me-2 fs-4"></i>
                @lang('Data Feed')
            </h6>
            <div class="dropdown">
                <button class="btn btn-label-secondary btn-sm dropdown-toggle hide-arrow" type="button" data-bs-toggle="dropdown">
                    <i class="las la-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}"><i class="las la-file-export me-2"></i>@lang('Export CSV')</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="window.print()"><i class="las la-print me-2"></i>@lang('Print Report')</a></li>
                </ul>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">@lang('User Node')</th>
                        <th>@lang('Inventory Items')</th>
                        <th>@lang('Potential Value')</th>
                        <th>@lang('Chronometry')</th>
                        <th>@lang('Status Map')</th>
                        <th class="text-end pe-4">@lang('Tactical Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($abandonedCarts as $cart)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-secondary"><i class="las la-user"></i></span>
                                </div>
                                <div class="d-flex flex-column">
                                    @if($cart->user_id)
                                        <a href="{{ route('admin.users.detail', $cart->user_id) }}" class="fw-bold text-dark small">{{ @$cart->user->fullname ?? 'Auth User' }}</a>
                                        <small class="text-muted tiny">{{ @$cart->user->email }}</small>
                                    @else
                                        <span class="fw-bold text-dark small">@lang('Guest Session')</span>
                                        <code class="text-muted tiny">{{ substr($cart->session_id, 0, 8) }}...</code>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @php $items = json_decode($cart->cart_data, true) ?? []; @endphp
                            <div class="d-flex flex-column">
                                <span class="badge bg-label-primary rounded-pill w-fit tiny mb-1">{{ count($items) }} @lang('Items')</span>
                                <div class="small text-muted text-truncate" style="max-width: 180px;">
                                    @foreach($items as $item)
                                        {{ @$item['product_name'] }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">{{ $general->cur_sym }}{{ number_format($cart->cart_value, 2) }}</span>
                                <small class="text-muted tiny">@lang('Gross Estimate')</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="small text-dark">{{ showDateTime($cart->updated_at, 'd M, Y') }}</span>
                                <small class="text-muted tiny">{{ $cart->updated_at->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td>
                            @if($cart->status == 'pending')
                                <span class="badge bg-label-warning rounded-pill px-3">@lang('Pending')</span>
                            @elseif($cart->status == 'abandoned')
                                <span class="badge bg-label-danger rounded-pill px-3">@lang('Abandoned')</span>
                            @else
                                <span class="badge bg-label-success rounded-pill px-3">@lang('Recovered')</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-inline-flex gap-2">
                                @if($cart->user_id || $cart->email || $cart->mobile)
                                <form action="{{ route('admin.abandoned-orders.reminder', $cart->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-label-success btn-sm rounded-3 shadow-none" data-bs-toggle="tooltip" title="@lang('Send Recovery Notification')">
                                        <i class="las la-bell me-1"></i> @lang('Remind')
                                    </button>
                                </form>
                                @endif
                                <button type="button" class="btn btn-label-secondary btn-sm rounded-3 shadow-none copy-recovery-link" data-url="{{ url('cart/recover/' . $cart->session_id) }}" data-bs-toggle="tooltip" title="@lang('Copy Recovery Link')">
                                    <i class="las la-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="empty-state">
                                <i class="las la-folder-open fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted">{{ __($emptyMessage) }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($abandonedCarts->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <small class="text-muted">@lang('Showing') {{ $abandonedCarts->firstItem() }} - {{ $abandonedCarts->lastItem() }} @lang('of') {{ $abandonedCarts->total() }}</small>
            {{ paginateLinks($abandonedCarts) }}
        </div>
        @endif

    </div>
</div>

<style>
    .hover-lift:hover { transform: translateY(-3px); }
    .transition-all { transition: all 0.3s ease; }
    .tiny { font-size: 10px; }
    .w-fit { width: fit-content; }
    .tracking-wider { letter-spacing: 0.5px; }
    .avatar-initial { font-weight: 700; }
    .bg-label-primary { background-color: rgba(105, 108, 255, 0.1) !important; color: #696cff !important; }
    .bg-label-success { background-color: rgba(113, 221, 55, 0.1) !important; color: #71dd37 !important; }
    .bg-label-warning { background-color: rgba(255, 171, 0, 0.1) !important; color: #ffab00 !important; }
    .bg-label-info { background-color: rgba(3, 195, 236, 0.1) !important; color: #03c3ec !important; }
    .bg-label-danger { background-color: rgba(255, 62, 29, 0.1) !important; color: #ff3e1d !important; }
    .bg-label-secondary { background-color: rgba(133, 146, 163, 0.1) !important; color: #8592a3 !important; }
    .btn-label-primary { background: rgba(105, 108, 255, 0.1); color: #696cff; border: none; }
    .btn-label-primary:hover { background: #696cff; color: #fff; }
    .btn-label-success { background: rgba(113, 221, 55, 0.1); color: #71dd37; border: none; }
    .btn-label-success:hover { background: #71dd37; color: #fff; }
    .btn-label-secondary { background: rgba(133, 146, 163, 0.1); color: #8592a3; border: none; }
    .btn-label-secondary:hover { background: #8592a3; color: #fff; }
</style>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.dashboard') }}" class="btn btn-label-secondary btn-sm me-2 shadow-sm"><i class="las la-arrow-left"></i> @lang('Back')</a>
    <a href="{{ route('admin.abandoned-orders.settings') }}" class="btn btn-label-primary btn-sm shadow-sm"><i class="las la-cog"></i> @lang('Tactical Settings')</a>
@endpush
