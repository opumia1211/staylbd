@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.orders.hub') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="las la-arrow-left"></i> @lang('Order Center')</a>
            <h5 class="mb-0 text-dark fw-bold">@lang('Import / Export Orders')</h5>
            <p class="text-muted small mb-0 mt-1">@lang('Move orders between this store and other websites or spreadsheets.')</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-white"><h6 class="mb-0 text-dark fw-semibold"><i class="las la-file-export text-success"></i> @lang('Export')</h6></div>
                <div class="card-body">
                    <p class="text-secondary small">@lang('Download orders as CSV (up to 5000 rows). Choose scope on the list page or use quick export:')</p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(['pending','confirmed','processing','shipped','delivered','cancel'] as $scope)
                            <a href="{{ route('admin.orders.export', ['scope' => $scope]) }}" class="btn btn-sm btn-outline-success">{{ ucfirst($scope) }}</a>
                        @endforeach
                        <a href="{{ route('admin.orders.export') }}" class="btn btn-sm btn-success">@lang('All (pending default)')</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border shadow-sm h-100">
                <div class="card-header bg-white"><h6 class="mb-0 text-dark fw-semibold"><i class="las la-file-import text-primary"></i> @lang('Import CSV')</h6></div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.import') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">@lang('Source label')</label>
                            <input type="text" name="order_source" class="form-control" value="{{ old('order_source', 'csv_import') }}" required maxlength="40" placeholder="woocommerce, shopify, partner_store">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('CSV file')</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv,.txt" required>
                        </div>
                        <p class="text-muted small">@lang('Columns'): <code>external_id, customer, phone, email, address, total</code></p>
                        <button type="submit" class="btn btn-primary"><i class="las la-upload"></i> @lang('Import')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
