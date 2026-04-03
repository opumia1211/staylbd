@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card b-radius--10 mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h5 class="mb-1">@lang('Stock Alerts')</h5>
                        <p class="text-muted small mb-0">@lang('Products at or below low stock threshold. Update quantity or set Low Stock Alert in product edit.')</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.product.index') }}" class="btn btn-outline--primary btn-sm">@lang('All Products')</a>
                        <a href="{{ route('admin.product.create2') }}" class="btn btn--primary btn-sm">@lang('Add Product')</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>@lang('Product')</th>
                                <th>@lang('SKU')</th>
                                <th>@lang('Current Stock')</th>
                                <th>@lang('Alert Level')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $alertLevel = $product->low_stock_alert ?? $defaultThreshold;
                                    $isOut = (int) $product->quantity <= 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ __($product->name) }}</strong>
                                        @if($product->category)
                                            <br><small class="text-muted">{{ __($product->category->name) }}</small>
                                        @endif
                                    </td>
                                    <td><code>{{ $product->product_sku }}</code></td>
                                    <td>
                                        @if($isOut)
                                            <span class="badge bg-danger">0</span>
                                        @else
                                            <span class="badge {{ $product->quantity <= $alertLevel ? 'bg-warning text-dark' : 'bg-secondary' }}">{{ $product->quantity }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $alertLevel }}</td>
                                    <td>
                                        @if($isOut)
                                            <span class="badge bg-danger">@lang('Out Of Stock')</span>
                                        @else
                                            <span class="badge bg-warning text-dark">@lang('Low Stock')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-sm btn-outline--primary" title="@lang('Quick Edit')">
                                            <i class="las la-edit"></i> @lang('Edit')
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">@lang('No products below stock threshold.')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($products->hasPages())
                    <div class="mt-3">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
