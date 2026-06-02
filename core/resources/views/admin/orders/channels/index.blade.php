@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-4">
        <div class="col-12 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <a href="{{ route('admin.orders.hub') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="las la-arrow-left"></i> @lang('Order Center')</a>
                <h5 class="mb-0 text-dark fw-bold">@lang('Order Channels')</h5>
                <p class="text-muted small mb-0 mt-1">@lang('Sync orders with WooCommerce, Shopify, Facebook Shop, or custom API.')</p>
            </div>
            <a href="{{ route('admin.orders.channels.create') }}" class="btn btn-primary btn-sm"><i class="las la-plus"></i> @lang('Add Channel')</a>
        </div>
    </div>

    <div class="card border shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('Platform')</th>
                        <th>@lang('Direction')</th>
                        <th>@lang('Imported')</th>
                        <th>@lang('Last sync')</th>
                        <th>@lang('Status')</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($channels as $ch)
                    <tr>
                        <td class="fw-semibold">{{ $ch->name }}</td>
                        <td><span class="badge bg-label-primary">{{ \App\Models\OrderChannel::platforms()[$ch->platform] ?? $ch->platform }}</span></td>
                        <td class="small text-capitalize">{{ $ch->direction }}</td>
                        <td>{{ $ch->imported_count }}</td>
                        <td class="small">{{ $ch->last_sync_at ? $ch->last_sync_at->diffForHumans() : '—' }}</td>
                        <td>
                            @if($ch->is_active)
                                <span class="badge bg-success">@lang('Active')</span>
                            @else
                                <span class="badge bg-secondary">@lang('Off')</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.channels.edit', $ch->id) }}" class="btn btn-sm btn-outline-primary">@lang('Edit')</a>
                            <form action="{{ route('admin.orders.channels.status', $ch->id) }}" method="post" class="d-inline">@csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning text-dark">@lang('Toggle')</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">@lang('No channels yet. Add one to import orders from other websites.')</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($channels->hasPages())
        <div class="card-footer">{{ paginateLinks($channels) }}</div>
        @endif
    </div>
@endsection
