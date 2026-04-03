@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold"><i class="las la-ad me-2"></i>{{ $pageTitle }}</h5>
                    <p class="mb-0 small text-muted mt-1">@lang('Popup ads show on public pages at the size you set. Each ad can have its own size and pages.')</p>
                </div>
                <a href="{{ route('admin.popup-ads.create') }}" class="btn btn--primary">
                    <i class="las la-plus"></i> @lang('Add Popup Ad')
                </a>
            </div>
            <div class="card-body p-0">
                @include('partials.notify')
                @if($ads->isEmpty())
                <div class="text-center py-5 px-3">
                    <i class="las la-ad display-4 text-muted opacity-50"></i>
                    <p class="mt-3 mb-2 text-muted">@lang('No popup ads yet.')</p>
                    <p class="small text-muted mb-3">@lang('Add one to show a banner on selected public pages at your chosen size.')</p>
                    <a href="{{ route('admin.popup-ads.create') }}" class="btn btn--primary"><i class="las la-plus"></i> @lang('Add Popup Ad')</a>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>@lang('Banner')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Size')</th>
                                <th>@lang('Position')</th>
                                <th>@lang('Type')</th>
                                <th>@lang('Pages')</th>
                                <th>@lang('Delay')</th>
                                <th>@lang('Status')</th>
                                <th class="text-end">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ads as $ad)
                            <tr>
                                <td>
                                    @if($ad->image)
                                    <img src="{{ getImage(getFilePath('popupAd') . '/' . $ad->image) }}" alt="" class="rounded" style="max-width: 80px; max-height: 50px; object-fit: contain;">
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="fw-medium">{{ __($ad->name) }}</td>
                                <td class="small">{{ $ad->getWidth() }} × {{ $ad->getHeight() }}</td>
                                <td class="small">{{ __(\App\Models\PopupAd::positionOptions()[$ad->getPosition()] ?? $ad->getPosition()) }}</td>
                                <td class="small">
                                    @if($ad->getDisplayType() === 'inline')
                                        <span class="badge bg-info">@lang('Inline')</span>
                                        @if($ad->getInlinePlacement())
                                            <br><small>{{ __(\App\Models\PopupAd::inlinePlacementOptions()[$ad->getInlinePlacement()] ?? $ad->getInlinePlacement()) }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-primary">@lang('Popup')</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @if(!empty($ad->show_on_pages) && in_array('all', $ad->show_on_pages))
                                        @lang('All pages')
                                    @elseif(!empty($ad->show_on_pages))
                                        {{ implode(', ', array_slice($ad->show_on_pages, 0, 3)) }}{{ count($ad->show_on_pages) > 3 ? '…' : '' }}
                                    @else
                                        @lang('All')
                                    @endif
                                </td>
                                <td>{{ $ad->delay_seconds }}s</td>
                                <td>
                                    @if($ad->is_active)
                                    <span class="badge bg-success">@lang('Active')</span>
                                    @else
                                    <span class="badge bg-secondary">@lang('Off')</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.popup-ads.edit', $ad->id) }}" class="btn btn-outline--primary" title="@lang('Edit')"><i class="las la-pen"></i></a>
                                        <form action="{{ route('admin.popup-ads.status', $ad->id) }}" method="post" class="d-inline">@csrf
                                            <button type="submit" class="btn btn-outline--{{ $ad->is_active ? 'warning' : 'success' }}" title="{{ $ad->is_active ? __('Turn off') : __('Turn on') }}"><i class="las la-{{ $ad->is_active ? 'eye-slash' : 'eye' }}"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-outline--danger confirmationBtn" data-action="{{ route('admin.popup-ads.destroy', $ad->id) }}" data-question="@lang('Delete this popup ad?')" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<x-confirmation-modal />
@endsection
