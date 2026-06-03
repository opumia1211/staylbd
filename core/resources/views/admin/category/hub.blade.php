@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h5 class="mb-0 text-dark fw-bold">@lang('Category Center')</h5>
                <p class="text-muted small mb-0 mt-1">@lang('Organize catalog structure — categories, brands, attributes and coupons.')</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.category.index') }}" class="btn btn-sm btn-primary"><i class="las la-align-left"></i> @lang('Categories')</a>
                <a href="{{ route('admin.brand.index') }}" class="btn btn-sm btn-success"><i class="las la-tags"></i> @lang('Brands')</a>
                <a href="{{ route('admin.coupon.index') }}" class="btn btn-sm btn-danger"><i class="las la-bullhorn"></i> @lang('Coupons')</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Categories')</span>
                    <span class="fw-bold fs-4 text-primary">{{ $stats['categories'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Subcategories')</span>
                    <span class="fw-bold fs-4 text-info">{{ $stats['subcategories'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Brands')</span>
                    <span class="fw-bold fs-4 text-success">{{ $stats['brands'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Coupons')</span>
                    <span class="fw-bold fs-4 text-danger">{{ $stats['coupons'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach($modules as $module)
            @php $color = $module['color'] ?? 'primary'; @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ route($module['route'], $module['route_params'] ?? []) }}" class="text-decoration-none d-block h-100">
                    <div class="card border shadow-sm h-100 bg-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-3 p-3 bg-{{ $color }} @if(in_array($color, ['warning','info'])) text-dark @else text-white @endif">
                                    <i class="las la-{{ $module['icon'] }} fs-2"></i>
                                </div>
                                @if(isset($module['count']))
                                    <span class="badge bg-{{ $color }}">{{ $module['count'] }}</span>
                                @endif
                            </div>
                            <h6 class="card-title mb-2 text-dark fw-semibold">{{ $module['title'] }}</h6>
                            <p class="text-secondary small mb-0 flex-grow-1">{{ $module['description'] }}</p>
                            <span class="mt-3 small fw-semibold text-{{ $color }}">@lang('Open') <i class="las la-arrow-right ms-1"></i></span>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
