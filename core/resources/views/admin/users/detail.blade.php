@extends('admin.layouts.app')
@section('panel')
@php
    $kyc = (array)($user->kyc_data ?? []);
    $addr = is_object($user->address ?? null) ? (array)$user->address : (array)($user->address ?? []);
@endphp
<div class="user-detail-page user-detail-page--compact">
    {{-- ========== BASIC: Profile + Quick Actions (single compact bar) ========== --}}
    <div class="card border-0 shadow-sm rounded-2 mb-2 overflow-hidden admin-detail-card">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center g-2">
                <div class="col-auto">
                    <div class="user-detail-avatar rounded-circle overflow-hidden bg-light d-flex align-items-center justify-content-center">
                        <img src="{{ getImage(getFilePath('userProfile') . '/' . @$user->image, getFileSize('userProfile')) }}" alt="{{ $user->fullname }}" class="w-100 h-100 object-fit-cover">
                    </div>
                </div>
                <div class="col min-w-0">
                    <div class="d-flex flex-wrap align-items-center gap-1 mb-0">
                        <h5 class="mb-0 fw-bold" style="font-size:1rem">{{ $user->fullname }}</h5>
                        @if($user->status == Status::USER_ACTIVE)
                            <span class="badge bg-success" style="font-size:0.65rem">@lang('Active')</span>
                        @else
                            <span class="badge bg-danger" style="font-size:0.65rem">@lang('Banned')</span>
                        @endif
                    </div>
                    <p class="text-muted mb-0 small"><span class="text-dark">@</span>{{ $user->username }}</p>
                    <div class="d-flex flex-wrap gap-2 mt-1 small text-muted" style="font-size:0.75rem">
                        @if($user->email)<span><i class="las la-envelope me-1"></i>{{ $user->email }}</span>@endif
                        @if($user->mobile)<span><i class="las la-phone me-1"></i>{{ $user->mobile }}</span>@endif
                        <span><i class="las la-calendar me-1"></i>{{ showDateTime($user->created_at, 'd M Y') }}</span>
                    </div>
                </div>
                <div class="col-auto d-none d-md-block">
                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                        <a href="{{ route('admin.report.login.history') }}?search={{ $user->username }}" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.7rem"><i class="las la-list-alt me-1"></i>@lang('Logins')</a>
                        <a href="{{ route('admin.users.notification.log', $user->id) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:0.7rem"><i class="las la-bell me-1"></i>@lang('Notifications')</a>
                        <a href="{{ route('admin.users.login', $user->id) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.7rem"><i class="las la-sign-in-alt me-1"></i>@lang('Login as customer')</a>
                        <a href="{{ url('/user/profile-setting') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm py-0 px-2" style="font-size:0.7rem"><i class="las la-external-link-alt me-1"></i>@lang('Profile')</a>
                        @if($user->status == Status::USER_ACTIVE)
                            <button type="button" class="btn btn-outline-warning btn-sm py-0 px-2" style="font-size:0.7rem" data-bs-toggle="modal" data-bs-target="#userStatusModal"><i class="las la-ban me-1"></i>@lang('Ban')</button>
                        @else
                            <button type="button" class="btn btn-outline-success btn-sm py-0 px-2" style="font-size:0.7rem" data-bs-toggle="modal" data-bs-target="#userStatusModal"><i class="las la-undo me-1"></i>@lang('Unban')</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-md-none mt-2 pt-2 border-top">
                <div class="d-flex flex-wrap gap-1">
                    <a href="{{ route('admin.report.login.history') }}?search={{ $user->username }}" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.7rem"><i class="las la-list-alt me-1"></i>@lang('Logins')</a>
                    <a href="{{ route('admin.users.notification.log', $user->id) }}" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:0.7rem"><i class="las la-bell me-1"></i>@lang('Notifications')</a>
                    <a href="{{ route('admin.users.login', $user->id) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.7rem"><i class="las la-sign-in-alt me-1"></i>@lang('Login')</a>
                    <a href="{{ url('/user/profile-setting') }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success btn-sm py-0 px-2" style="font-size:0.7rem"><i class="las la-external-link-alt me-1"></i>@lang('Profile')</a>
                    @if($user->status == Status::USER_ACTIVE)
                        <button type="button" class="btn btn-outline-warning btn-sm py-0 px-2" style="font-size:0.7rem" data-bs-toggle="modal" data-bs-target="#userStatusModal"><i class="las la-ban me-1"></i>@lang('Ban')</button>
                    @else
                        <button type="button" class="btn btn-outline-success btn-sm py-0 px-2" style="font-size:0.7rem" data-bs-toggle="modal" data-bs-target="#userStatusModal"><i class="las la-undo me-1"></i>@lang('Unban')</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========== BASIC: Stats (single compact row) ========== --}}
    <div class="row g-1 mb-2">
        <div class="col-4 col-md-2">
            <a href="{{ route('admin.deposit.list') }}?search={{ $user->username }}" class="card border-0 shadow-sm rounded-2 h-100 text-decoration-none text-dark user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-primary bg-opacity-10 text-primary rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-wallet"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $general->cur_sym }}{{ showAmount($totalDeposit) }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Payments')</div></div>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="{{ route('admin.report.transaction') }}?search={{ $user->username }}" class="card border-0 shadow-sm rounded-2 h-100 text-decoration-none text-dark user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-info bg-opacity-10 text-info rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-exchange-alt"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $totalTransaction }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Transactions')</div></div>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="{{ route('admin.orders.index') }}?search={{ $user->username }}" class="card border-0 shadow-sm rounded-2 h-100 text-decoration-none text-dark user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-primary bg-opacity-10 text-primary rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-list-alt"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $order['total'] }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Orders')</div></div>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="{{ route('admin.orders.pending') }}?search={{ $user->username }}" class="card border-0 shadow-sm rounded-2 h-100 text-decoration-none text-dark user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-warning bg-opacity-10 text-warning rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-spinner"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $order['pending'] }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Pending')</div></div>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="{{ route('admin.orders.confirmed') }}?search={{ $user->username }}" class="card border-0 shadow-sm rounded-2 h-100 text-decoration-none text-dark user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-success bg-opacity-10 text-success rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-check-double"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $order['confirmed'] }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Confirmed')</div></div>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="{{ route('admin.orders.shipped') }}?search={{ $user->username }}" class="card border-0 shadow-sm rounded-2 h-100 text-decoration-none text-dark user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-secondary bg-opacity-10 text-secondary rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-truck"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $order['shipped'] }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Shipping')</div></div>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="{{ route('admin.orders.delivered') }}?search={{ $user->username }}" class="card border-0 shadow-sm rounded-2 h-100 text-decoration-none text-dark user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-info bg-opacity-10 text-info rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-check-circle"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $order['delivered'] }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Delivered')</div></div>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <a href="{{ route('admin.orders.cancel') }}?search={{ $user->username }}" class="card border-0 shadow-sm rounded-2 h-100 text-decoration-none text-dark user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-danger bg-opacity-10 text-danger rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-times-circle"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $order['canceled'] }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Rejected')</div></div>
                </div>
            </a>
        </div>
        <div class="col-4 col-md-2">
            <div class="card border-0 shadow-sm rounded-2 h-100 user-stat-card admin-detail-card">
                <div class="card-body py-1 px-2 d-flex align-items-center gap-1">
                    <span class="user-stat-icon bg-secondary bg-opacity-10 text-secondary rounded-1" style="width:26px;height:26px;font-size:0.75rem;display:inline-flex;align-items:center;justify-content:center"><i class="las la-ticket-alt"></i></span>
                    <div class="min-w-0 flex-grow-1"><div class="fw-bold" style="font-size:0.75rem">{{ $order['ticket'] }}</div><div class="text-muted" style="font-size:0.6rem">@lang('Tickets')</div></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== ADVANCED: Two columns – Basic Info + Address ========== --}}
    <div class="row g-2 mb-2">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-2 h-100 admin-detail-card">
                <div class="card-header bg-transparent border-bottom py-1 px-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 small fw-bold"><i class="las la-user-circle me-1"></i>@lang('Basic Information')</h6>
                    <a href="{{ url('/user/profile-setting') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size:0.65rem">@lang('Profile')</a>
                </div>
                <div class="card-body py-1 px-2">
                    <div class="row g-1 small" style="font-size:0.75rem">
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Username')</span><span class="fw-semibold">{{ $user->username }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Age')</span><span class="fw-semibold">{{ $user->age ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Gender')</span><span class="fw-semibold">{{ $user->gender ? __(ucfirst($user->gender)) : '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Date of birth')</span><span class="fw-semibold">{{ $kyc['date_of_birth'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Occupation')</span><span class="fw-semibold">{{ $kyc['occupation'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Company name')</span><span class="fw-semibold">{{ $kyc['company_name'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('NID / Passport')</span><span class="fw-semibold">{{ $kyc['nid_number'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Alternate phone')</span><span class="fw-semibold">{{ $kyc['alternate_phone'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('WhatsApp')</span><span class="fw-semibold">{{ $user->whatsapp_identity ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Telegram')</span><span class="fw-semibold">{{ $user->telegram_username ? '@' . $user->telegram_username : '—' }}</span></div>
                        @if(!empty($kyc['website']))
                        <div class="col-12"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Website')</span><a href="{{ $kyc['website'] }}" target="_blank" rel="noopener" class="text-break small">{{ $kyc['website'] }}</a></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-2 h-100 admin-detail-card">
                <div class="card-header bg-transparent border-bottom py-1 px-2">
                    <h6 class="mb-0 small fw-bold"><i class="las la-map-marker-alt me-1"></i>@lang('Address')</h6>
                </div>
                <div class="card-body py-1 px-2">
                    <div class="row g-1 small" style="font-size:0.75rem">
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Country')</span><span class="fw-semibold">{{ $addr['country'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Division')</span><span class="fw-semibold">{{ $addr['division'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('District')</span><span class="fw-semibold">{{ $addr['city'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Thana')</span><span class="fw-semibold">{{ $addr['thana'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('State')</span><span class="fw-semibold">{{ $addr['state'] ?? '—' }}</span></div>
                        <div class="col-6"><span class="text-muted d-block" style="font-size:0.65rem">@lang('ZIP')</span><span class="fw-semibold">{{ $addr['zip'] ?? '—' }}</span></div>
                        <div class="col-12"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Full address')</span><span class="fw-semibold">{{ $addr['address'] ?? '—' }}{{ !empty($addr['address_2']) ? ', ' . $addr['address_2'] : '' }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== ADVANCED: More profile (collapsible) ========== --}}
    <div class="card border-0 shadow-sm rounded-2 mb-2 admin-detail-card">
        <div class="card-header bg-transparent border-bottom py-1 px-2 d-flex justify-content-between align-items-center collapsed" data-bs-toggle="collapse" data-bs-target="#advancedProfileCollapse" role="button" aria-expanded="false" aria-controls="advancedProfileCollapse">
            <h6 class="mb-0 small fw-bold"><i class="las la-chevron-down me-1 collapse-chevron"></i>@lang('Advanced profile') (Tax, Language, Contact opt-in)</h6>
        </div>
        <div class="collapse" id="advancedProfileCollapse">
            <div class="card-body py-1 px-2">
                <div class="row g-1 small" style="font-size:0.75rem">
                    <div class="col-6 col-md-4"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Tax ID / VAT')</span><span class="fw-semibold">{{ $kyc['tax_id'] ?? '—' }}</span></div>
                    <div class="col-6 col-md-4"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Preferred language')</span><span class="fw-semibold">{{ !empty($kyc['preferred_language']) ? (strtoupper($kyc['preferred_language']) === 'BN' ? 'বাংলা' : 'English') : '—' }}</span></div>
                    <div class="col-6 col-md-4"><span class="text-muted d-block" style="font-size:0.65rem">@lang('Contact opt-in')</span><span class="fw-semibold">{{ ($user->contact_channel_opt_in ?? true) ? __('Yes') : __('No') }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== ADVANCED: Cart & Wishlist (side by side, compact) ========== --}}
    <div class="row g-2 mb-2">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-2 h-100 admin-detail-card">
                <div class="card-header bg-transparent border-bottom py-1 px-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 small fw-bold"><i class="las la-shopping-cart me-1"></i>@lang('Cart')</h6>
                    <span class="badge bg-primary" style="font-size:0.65rem">{{ $userCarts->count() }}</span>
                </div>
                <div class="card-body p-0" style="max-height:140px;overflow-y:auto">
                    @if($userCarts->isEmpty())
                        <p class="p-2 mb-0 text-muted small text-center" style="font-size:0.7rem">@lang('No items in cart')</p>
                    @else
                        <ul class="list-group list-group-flush user-detail-list">
                            @foreach($userCarts as $cart)
                                <li class="list-group-item border-0 border-bottom py-1 px-2 d-flex justify-content-between align-items-center small" style="font-size:0.75rem">
                                    @if($cart->product)
                                        <a href="{{ route('admin.product.edit', $cart->product->id) }}" target="_blank" class="text-decoration-none text-dark text-truncate me-1">{{ $cart->product->name ?? '#' . $cart->product_id }}</a>
                                        <span class="badge bg-primary rounded-pill" style="font-size:0.6rem">{{ $cart->quantity }}</span>
                                    @else
                                        <span class="text-muted">#{{ $cart->product_id }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-2 h-100 admin-detail-card">
                <div class="card-header bg-transparent border-bottom py-1 px-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 small fw-bold"><i class="las la-heart me-1"></i>@lang('Wishlist')</h6>
                    <span class="badge bg-danger" style="font-size:0.65rem">{{ $userWishlist->count() }}</span>
                </div>
                <div class="card-body p-0" style="max-height:140px;overflow-y:auto">
                    @if($userWishlist->isEmpty())
                        <p class="p-2 mb-0 text-muted small text-center" style="font-size:0.7rem">@lang('No items in wishlist')</p>
                    @else
                        <ul class="list-group list-group-flush user-detail-list">
                            @foreach($userWishlist as $wish)
                                <li class="list-group-item border-0 border-bottom py-1 px-2 small" style="font-size:0.75rem">
                                    @if($wish->product)
                                        <a href="{{ route('admin.product.edit', $wish->product->id) }}" target="_blank" class="text-decoration-none text-dark text-truncate d-block">{{ $wish->product->name ?? '#' . $wish->product_id }}</a>
                                    @else
                                        <span class="text-muted">#{{ $wish->product_id }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ========== ADVANCED: Edit user form (compact) ========== --}}
    <div class="card border-0 shadow-sm rounded-2 admin-detail-card">
        <div class="card-header bg-transparent border-bottom py-1 px-2">
            <h6 class="mb-0 small fw-bold"><i class="las la-user-edit me-1"></i>@lang('Information of') {{ $user->fullname }}</h6>
        </div>
        <div class="card-body py-2 px-2">
            <form action="{{ route('admin.users.update', [$user->id]) }}" method="POST" enctype="multipart/form-data" class="user-detail-form">
                @csrf
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('First Name')</label>
                        <input class="form-control form-control-sm" type="text" name="firstname" required value="{{ $user->firstname }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('Last Name')</label>
                        <input class="form-control form-control-sm" type="text" name="lastname" required value="{{ $user->lastname }}">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('Email')</label>
                        <input class="form-control form-control-sm" type="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('Mobile Number')</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text mobile-code" style="font-size:0.7rem"></span>
                            <input type="number" name="mobile" value="{{ old('mobile') }}" id="mobile" class="form-control checkUser" required style="font-size:0.8rem">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('Address')</label>
                        <input class="form-control form-control-sm" type="text" name="address" value="{{ @$user->address->address }}" style="font-size:0.8rem">
                    </div>
                    <div class="col-4 col-md-2">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('City')</label>
                        <input class="form-control form-control-sm" type="text" name="city" value="{{ @$user->address->city }}" style="font-size:0.8rem">
                    </div>
                    <div class="col-4 col-md-2">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('State')</label>
                        <input class="form-control form-control-sm" type="text" name="state" value="{{ @$user->address->state }}" style="font-size:0.8rem">
                    </div>
                    <div class="col-4 col-md-2">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('Zip/Postal')</label>
                        <input class="form-control form-control-sm" type="text" name="zip" value="{{ @$user->address->zip }}" style="font-size:0.8rem">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small mb-0" style="font-size:0.7rem">@lang('Country')</label>
                        <select name="country" class="form-select form-select-sm" style="font-size:0.8rem">
                            @foreach($countries as $key => $country)
                                <option data-mobile_code="{{ $country->dial_code }}" value="{{ $key }}">{{ __($country->country) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 pt-1">
                        <hr class="my-1">
                        <p class="small text-muted mb-1" style="font-size:0.65rem">@lang('Verification')</p>
                        <div class="d-flex flex-wrap gap-3">
                            <div><label class="form-label small mb-0" style="font-size:0.65rem">@lang('Email')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="ev" @if($user->ev) checked @endif></div>
                            <div><label class="form-label small mb-0" style="font-size:0.65rem">@lang('Mobile')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-bs-toggle="toggle" data-on="@lang('Verified')" data-off="@lang('Unverified')" name="sv" @if($user->sv) checked @endif></div>
                            <div><label class="form-label small mb-0" style="font-size:0.65rem">@lang('2FA')</label>
                                <input type="checkbox" data-width="100%" data-onstyle="success" data-offstyle="danger" data-bs-toggle="toggle" data-on="@lang('Enable')" data-off="@lang('Disable')" name="ts" @if($user->ts) checked @endif></div>
                        </div>
                    </div>
                    <div class="col-12 pt-1">
                        <button type="submit" class="btn btn--primary btn-sm">@lang('Save changes')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Ban / Unban modal --}}
<div id="userStatusModal" class="modal fade" tabindex="-1" aria-labelledby="userStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-3">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title" id="userStatusModalLabel">
                    @if($user->status == Status::USER_ACTIVE)
                        @lang('Ban User')
                    @else
                        @lang('Unban User')
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.status', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body py-4">
                    @if($user->status == Status::USER_ACTIVE)
                        <p class="text-muted small mb-3">@lang('If you ban this user he/she won\'t able to access his/her dashboard.')</p>
                        <label class="form-label">@lang('Reason')</label>
                        <textarea class="form-control" name="reason" rows="4" required placeholder="@lang('Reason for ban')"></textarea>
                    @else
                        <p class="small mb-2">@lang('Ban reason was'):</p>
                        <p class="bg-light rounded p-3 small mb-0">{{ $user->ban_reason }}</p>
                        <p class="text-center mt-4 mb-0">@lang('Are you sure to unban this user?')</p>
                    @endif
                </div>
                <div class="modal-footer border-top py-3">
                    @if($user->status == Status::USER_ACTIVE)
                        <button type="submit" class="btn btn--primary w-100">@lang('Submit')</button>
                    @else
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">@lang('No')</button>
                        <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.users.all') }}" />
@endpush

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
(function($) {
    "use strict";
    $('.bal-btn').on('click', function() {
        var act = $(this).data('act');
        $('#addSubModal').find('input[name=act]').val(act);
        $('#addSubModal').find('.type').text(act == 'add' ? 'Add' : 'Subtract');
    });
    var mobileElement = $('.mobile-code');
    $('select[name=country]').on('change', function() {
        mobileElement.text('+' + $(this).find(':selected').data('mobile_code'));
    });
    $('select[name=country]').val('{{ @$user->country_code }}');
    var dialCode = $('select[name=country] :selected').data('mobile_code');
    var mobileNumber = '{{ $user->mobile }}'.replace(dialCode, '');
    $('input[name=mobile]').val(mobileNumber);
    mobileElement.text('+' + (dialCode || ''));
})(jQuery);
</script>
@endpush
