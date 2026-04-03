@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 bg--primary bg-opacity-10 p-3">
                            <i class="las la-link fs-2 text--primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">@lang('Autopay Gateways')</h6>
                            <p class="text-muted small mb-0">@lang('Connect external payment websites (user pays there, confirmation returns here) or connect your own Android/app to send payment SMS here—when real payment is detected, user sees success automatically.')</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- External payment sites --}}
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h6 class="mb-0 fw-bold"><i class="las la-external-link-alt me-2"></i>@lang('External Payment Websites')</h6>
                    <a href="{{ route('admin.gateway.autopay.external.create') }}" class="btn btn--primary btn-sm"><i class="las la-plus me-1"></i>@lang('Add External Gateway')</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Status')</th>
                                    <th class="text-end">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($externalGateways as $g)
                                    <tr>
                                        <td>{{ __($g->name) }}</td>
                                        <td>{{ $g->singleCurrency ? $g->singleCurrency->currency : '—' }}</td>
                                        <td>@php echo $g->statusBadge; @endphp</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.gateway.autopay.external.edit', $g->alias) }}" class="btn btn-outline--primary btn-sm"><i class="las la-pen"></i></a>
                                            @if($g->status == Status::DISABLE)
                                                <button class="btn btn-outline--success btn-sm confirmationBtn" data-question="@lang('Enable this gateway?')" data-action="{{ route('admin.gateway.autopay.status', $g->id) }}"><i class="las la-eye"></i></button>
                                            @else
                                                <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Disable this gateway?')" data-action="{{ route('admin.gateway.autopay.status', $g->id) }}"><i class="las la-ban"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">@lang('No external gateways. Add one to redirect users to another payment website.')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Message / App gateways --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h6 class="mb-0 fw-bold"><i class="las la-mobile-alt me-2"></i>@lang('App / SMS Message Gateways')</h6>
                    <a href="{{ route('admin.gateway.autopay.message.create') }}" class="btn btn--primary btn-sm"><i class="las la-plus me-1"></i>@lang('Add Message Gateway')</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Currency')</th>
                                    <th>@lang('Status')</th>
                                    <th class="text-end">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messageGateways as $g)
                                    <tr>
                                        <td>{{ __($g->name) }}</td>
                                        <td>{{ $g->singleCurrency ? $g->singleCurrency->currency : '—' }}</td>
                                        <td>@php echo $g->statusBadge; @endphp</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.gateway.autopay.message.edit', $g->alias) }}" class="btn btn-outline--primary btn-sm"><i class="las la-pen"></i></a>
                                            @if($g->status == Status::DISABLE)
                                                <button class="btn btn-outline--success btn-sm confirmationBtn" data-question="@lang('Enable this gateway?')" data-action="{{ route('admin.gateway.autopay.status', $g->id) }}"><i class="las la-eye"></i></button>
                                            @else
                                                <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Disable this gateway?')" data-action="{{ route('admin.gateway.autopay.status', $g->id) }}"><i class="las la-ban"></i></button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">@lang('No message gateways. Add one and use the API in your Android/app to send payment confirmations.')</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="{{ route('admin.gateway.automatic.index') }}" class="btn btn-outline--primary btn-sm">@lang('Automatic')</a>
    <a href="{{ route('admin.gateway.manual.index') }}" class="btn btn-outline--primary btn-sm">@lang('Manual')</a>
@endpush
