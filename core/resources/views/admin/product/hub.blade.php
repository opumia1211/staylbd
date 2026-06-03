@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h5 class="mb-0 text-dark fw-bold">@lang('Product Center')</h5>
                <p class="text-muted small mb-0 mt-1">@lang('Upload, edit and manage products — fast workflow for daily catalog work.')</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.product.create') }}" class="btn btn-sm btn-success"><i class="las la-plus-circle"></i> @lang('Add New Product')</a>
                <a href="{{ route('admin.product.general.create') }}" class="btn btn-sm btn-info"><i class="las la-cloud-upload-alt"></i> @lang('Quick Upload')</a>
                <a href="{{ route('admin.product.index') }}" class="btn btn-sm btn-primary"><i class="las la-boxes"></i> @lang('All Products')</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Total Products')</span>
                    <span class="fw-bold fs-4 text-primary">{{ $stats['total'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Active')</span>
                    <span class="fw-bold fs-4 text-success">{{ $stats['active'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Low Stock')</span>
                    <span class="fw-bold fs-4 text-warning">{{ $stats['low_stock'] ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <span class="text-muted small d-block">@lang('Pending Reviews')</span>
                    <span class="fw-bold fs-4 text-danger">{{ $stats['pending_reviews'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @foreach($modules as $module)
            @php
                $color = $module['color'] ?? 'primary';
                $url = route($module['route'], $module['route_params'] ?? []);
                if (!empty($module['route_query'])) {
                    $url .= '?' . http_build_query($module['route_query']);
                }
            @endphp
            <div class="col-md-6 col-lg-4">
                <a href="{{ $url }}" class="text-decoration-none d-block h-100">
                    <div class="card border shadow-sm h-100 bg-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="rounded-3 p-3 bg-{{ $color }} @if(in_array($color, ['warning','info'])) text-dark @else text-white @endif">
                                    <i class="las la-{{ $module['icon'] }} fs-2"></i>
                                </div>
                                @if(isset($module['count']))
                                    <span class="badge bg-{{ $color }}">{{ $module['count'] }}</span>
                                @elseif(!empty($module['badge']))
                                    <span class="badge bg-label-secondary">{{ $module['badge'] }}</span>
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
