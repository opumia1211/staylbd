@extends('admin.layouts.app')

@push('style')
<style>
    .autoai-rules-table tbody td { background-color: #fff !important; }
    .autoai-rules-table tbody td .auto-reply-text { color: #212529 !important; }
</style>
@endpush

@section('panel')
    {{-- Stats --}}
    <div class="row gy-3 mb-4">
        <div class="col-xxl-3 col-sm-6">
            <div class="card b-radius--10 overflow-hidden">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="widget-icon bg--primary rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="las la-robot f-size--24 text-white"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block">@lang('Total Rules')</span>
                        <h4 class="mb-0">{{ $totalRules }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card b-radius--10 overflow-hidden">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="widget-icon bg--success rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="las la-check-circle f-size--24 text-white"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block">@lang('Active Rules')</span>
                        <h4 class="mb-0">{{ $activeRules }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card b-radius--10 overflow-hidden">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="widget-icon bg--info rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px;">
                        <i class="las la-key f-size--24 text-white"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block">@lang('Total Keywords')</span>
                        <h4 class="mb-0">{{ $totalKeywords }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <div class="card b-radius--10 overflow-hidden">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="widget-icon rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background-color: #0d6efd;">
                        <i class="las la-globe f-size--24 text-white"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block">@lang('Public (sent to users)')</span>
                        <h4 class="mb-0">{{ $publicRules ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6">
            <a href="{{ route('admin.autoai.create') }}" class="text-decoration-none">
                <div class="card b-radius--10 overflow-hidden border border--primary h-100">
                    <div class="card-body d-flex align-items-center justify-content-center gap-2">
                        <i class="las la-plus-circle f-size--32 text--primary"></i>
                        <span class="fw-bold text--primary">@lang('Add New Rule')</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
                        <h5 class="mb-0"><i class="las la-list me-2 text--primary"></i>@lang('Auto AI Reply Rules')</h5>
                        <a href="{{ route('admin.autoai.create') }}" class="btn btn--primary btn-sm"><i class="las la-plus"></i> @lang('Add Auto AI Reply')</a>
                    </div>
                    <div class="alert alert--info d-flex align-items-start gap-2 mb-4">
                        <i class="las la-info-circle f-size--18 mt-1"></i>
                        <div>
                            <strong>@lang('How it works')</strong>: @lang('Public') = @lang('When user message matches the keyword(s), this reply is sent automatically.'). @lang('Private') = @lang('Stored for your reference only; not sent to users.'). @lang('Keywords are matched case-insensitively in any language.')
                        </div>
                    </div>
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two autoai-rules-table">
                            <thead>
                                <tr>
                                    <th style="width: 140px;">@lang('Rule / Keywords')</th>
                                    <th>@lang('Auto Reply')</th>
                                    <th style="width: 90px;">@lang('Visibility')</th>
                                    <th style="width: 90px;">@lang('Status')</th>
                                    <th style="width: 160px;">@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td class="align-middle" style="background-color: #fff;">
                                            @if($item->name)
                                                <span class="fw-semibold d-block mb-1">{{ $item->name }}</span>
                                            @endif
                                            @php $kws = $item->getKeywordsList(); @endphp
                                            @if(count($kws) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($kws as $kw)
                                                        <span class="badge px-2 py-1" style="background-color: #e9ecef; color: #212529; font-weight: 500;">{{ $kw }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="align-middle" style="background-color: #fff;">
                                            <span class="small d-block auto-reply-text" style="color: #212529 !important; background: transparent !important;" title="{{ $item->message }}">{{ strLimit($item->message, 70) }}</span>
                                        </td>
                                        <td class="align-middle" style="background-color: #fff;">
                                            <form action="{{ route('admin.autoai.toggle.visibility', $item->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="badge border-0 {{ ($item->is_public ?? true) ? 'badge--success' : 'bg-secondary' }}" style="cursor: pointer;" title="{{ ($item->is_public ?? true) ? __('Click to set Private') : __('Click to set Public') }}">
                                                    @if($item->is_public ?? true)
                                                        <i class="las la-globe me-1"></i>@lang('Public')
                                                    @else
                                                        <i class="las la-lock me-1"></i>@lang('Private')
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                        <td class="align-middle" style="background-color: #fff;">
                                            <form action="{{ route('admin.autoai.toggle.active', $item->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="badge border-0 {{ $item->is_active ? 'badge--success' : 'badge--danger' }}" style="cursor: pointer;" title="{{ $item->is_active ? __('Click to deactivate') : __('Click to activate') }}">
                                                    {{ $item->is_active ? __('Active') : __('Inactive') }}
                                                </button>
                                            </form>
                                        </td>
                                        <td class="align-middle" style="background-color: #fff;">
                                            <a href="{{ route('admin.autoai.edit', $item->id) }}" class="btn btn-sm btn-outline--primary" title="@lang('Edit')"><i class="las la-pen"></i></a>
                                            <form action="{{ route('admin.autoai.destroy', $item->id) }}" method="post" class="d-inline confirmation-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline--danger" onclick="return confirm('{{ __('Are you sure to delete this auto-response?') }}');" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center py-5" colspan="5">
                                            <i class="las la-robot text-muted f-size--48 mb-2 d-block"></i>
                                            <p class="text-muted mb-2">@lang('No auto-response rules yet.')</p>
                                            <a href="{{ route('admin.autoai.create') }}" class="btn btn--primary btn-sm">@lang('Add one')</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($items->hasPages())
                        <div class="card-footer py-3">
                            {{ paginateLinks($items) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
