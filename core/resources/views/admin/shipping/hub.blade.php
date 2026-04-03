@extends('admin.layouts.app')

@section('panel')
@php
	$hasZones = $hasZones ?? \Illuminate\Support\Facades\Schema::hasTable('shipping_zones');
	$hasRules = $hasRules ?? \Illuminate\Support\Facades\Schema::hasTable('shipping_rules');
	$methodsCount = $methodsCount ?? \App\Models\ShippingMethod::count();
	$zonesCount = $zonesCount ?? ($hasZones ? \App\Models\ShippingZone::count() : 0);
@endphp

@if(!$hasZones || !$hasRules)
<div class="alert alert-info mb-4 border-0 shadow-sm d-flex align-items-center gap-3" role="alert">
	<i class="las la-info-circle fs-4 text-info"></i>
	<div class="flex-grow-1 text-dark">
		<strong>@lang('One-time setup')</strong>: <code class="bg-white px-2 py-1 rounded border">php artisan migrate</code>
	</div>
</div>
@endif

<div class="row mb-3">
	<div class="col-12">
		<h5 class="mb-0 text-dark fw-bold">@lang('Shipping Hub')</h5>
		<p class="text-muted small mb-0 mt-1">@lang('Zones · Methods · Rules')</p>
	</div>
</div>

<div class="row g-4">
	<div class="col-md-4">
		<a href="{{ route('admin.shipping.zones.index') }}" class="text-decoration-none d-block h-100">
			<div class="card border shadow-sm h-100 shipping-hub-card bg-white">
				<div class="card-body p-4 d-flex flex-column">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="rounded-3 p-3 bg-primary text-white"><i class="las la-map-marked-alt fs-2"></i></div>
						@if($hasZones)<span class="badge bg-primary text-white">{{ $zonesCount }}</span>@else<span class="badge bg-secondary text-white">Setup</span>@endif
					</div>
					<h6 class="card-title mb-2 text-dark fw-semibold">@lang('Zones')</h6>
					<p class="text-secondary small mb-0 flex-grow-1">@lang('Countries & areas, per-region charge')</p>
					<span class="mt-3 text-primary small fw-semibold">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
				</div>
			</div>
		</a>
	</div>
	<div class="col-md-4">
		<a href="{{ route('admin.shipping.methods.index') }}" class="text-decoration-none d-block h-100">
			<div class="card border shadow-sm h-100 shipping-hub-card bg-white">
				<div class="card-body p-4 d-flex flex-column">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="rounded-3 p-3 bg-success text-white"><i class="las la-shipping-fast fs-2"></i></div>
						<span class="badge bg-success text-white">{{ $methodsCount }}</span>
					</div>
					<h6 class="card-title mb-2 text-dark fw-semibold">@lang('Methods')</h6>
					<p class="text-secondary small mb-0 flex-grow-1">@lang('Shipping options, price & ETA')</p>
					<span class="mt-3 small fw-semibold text-success">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
				</div>
			</div>
		</a>
	</div>
	<div class="col-md-4">
		<a href="{{ route('admin.shipping.rules.index') }}" class="text-decoration-none d-block h-100">
			<div class="card border shadow-sm h-100 shipping-hub-card bg-white">
				<div class="card-body p-4 d-flex flex-column">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="rounded-3 p-3 bg-info text-dark"><i class="las la-cog fs-2"></i></div>
						@if($hasRules)<span class="badge bg-info text-dark">OK</span>@else<span class="badge bg-secondary text-white">Setup</span>@endif
					</div>
					<h6 class="card-title mb-2 text-dark fw-semibold">@lang('Rules')</h6>
					<p class="text-secondary small mb-0 flex-grow-1">@lang('Free shipping min, COD & express')</p>
					<span class="mt-3 small fw-semibold text-info">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
				</div>
			</div>
		</a>
	</div>
	@php $hasCod = \Illuminate\Support\Facades\Schema::hasTable('cod_settings'); @endphp
	<div class="col-md-4">
		<a href="{{ route('admin.shipping.cod.index') }}" class="text-decoration-none d-block h-100">
			<div class="card border shadow-sm h-100 shipping-hub-card bg-white">
				<div class="card-body p-4 d-flex flex-column">
					<div class="d-flex align-items-center justify-content-between mb-3">
						<div class="rounded-3 p-3 bg-warning text-dark"><i class="las la-money-bill-wave fs-2"></i></div>
						@if($hasCod)<span class="badge bg-warning text-dark">@lang('COD')</span>@else<span class="badge bg-secondary text-white">Setup</span>@endif
					</div>
					<h6 class="card-title mb-2 text-dark fw-semibold">@lang('COD Settings')</h6>
					<p class="text-secondary small mb-0 flex-grow-1">@lang('Eligibility, charge, OTP & fraud control')</p>
					<span class="mt-3 small fw-semibold text-warning">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
				</div>
			</div>
		</a>
	</div>
</div>

<div class="row mt-4">
	<div class="col-12">
		<div class="card border bg-light">
			<div class="card-body py-3 d-flex flex-wrap align-items-center gap-2">
				<a href="{{ route('admin.shipping.zones.index') }}" class="btn btn-sm btn-outline-primary"><i class="las la-map-marked-alt"></i> Zones</a>
				<a href="{{ route('admin.shipping.methods.index') }}" class="btn btn-sm btn-outline-success"><i class="las la-shipping-fast"></i> Methods</a>
				<a href="{{ route('admin.shipping.rules.index') }}" class="btn btn-sm btn-outline-info text-dark"><i class="las la-cog"></i> Rules</a>
				<a href="{{ route('admin.shipping.cod.index') }}" class="btn btn-sm btn-outline-warning text-dark"><i class="las la-money-bill-wave"></i> COD</a>
			</div>
		</div>
	</div>
</div>

<div class="row mt-4">
	<div class="col-12">
		<div class="card border-0 bg-white shadow-sm">
			<div class="card-body py-3">
				<h6 class="text-dark fw-semibold mb-2">@lang('What you can do')</h6>
				<ul class="mb-0 ps-3 text-secondary small" style="line-height:1.6;">
					<li><strong class="text-dark">Zones:</strong> Set charge per country (international) or per area/city (BD). Optional free shipping per zone/area for campaigns.</li>
					<li><strong class="text-dark">Methods:</strong> Add delivery options, link to zone, set price & ETA.</li>
					<li><strong class="text-dark">Rules:</strong> Free shipping above amount, COD/express extra.</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection

@push('style')
<style>.shipping-hub-card{transition:transform .2s,box-shadow .2s}.shipping-hub-card:hover{transform:translateY(-4px);box-shadow:0 .5rem 1.5rem rgba(0,0,0,.12)!important}</style>
@endpush
