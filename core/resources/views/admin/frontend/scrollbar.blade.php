@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3 px-3">
                {{-- Compact top: title + global toggle + Add --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h5 class="mb-0 small fw-bold"><i class="las la-bolt me-1"></i>@lang('Scroll Bar')</h5>
                        <p class="text-muted small mb-0 mt-1">@lang('Headline tickers by position (header, banner, footer, products). Create, reorder, preview.')</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form method="POST" action="{{ route('admin.frontend.sections.scrollbar.settings') }}" class="d-flex align-items-center gap-2">
                            @csrf
                            <span class="small text-muted">@lang('Site-wide')</span>
                            <select name="scrollbar_enabled" class="form-select form-select-sm" style="width: auto; min-width: 95px;">
                                <option value="1" {{ !isset($scrollbarEnabled) || $scrollbarEnabled ? 'selected' : '' }}>@lang('Enabled')</option>
                                <option value="0" {{ isset($scrollbarEnabled) && !$scrollbarEnabled ? 'selected' : '' }}>@lang('Disabled')</option>
                            </select>
                            <button type="submit" class="btn btn--primary btn-sm">@lang('Save')</button>
                        </form>
                        <a href="{{ route('admin.frontend.sections.scrollbar.new') }}" class="btn btn--primary btn-sm">
                            <i class="las la-plus me-1"></i>@lang('Add Scroll Bar')
                        </a>
                        <a href="{{ route('admin.frontend.sections.scrollbar2.new') }}" class="btn btn--success btn-sm">
                            <i class="las la-plus me-1"></i>@lang('Add Scroll Bar 2')
                        </a>
                    </div>
                </div>

                {{-- Search --}}
                <form method="GET" action="{{ route('admin.frontend.sections.scrollbar') }}" class="d-flex gap-2 flex-wrap mb-2">
                    <input type="text" name="search" class="form-control form-control-sm" style="max-width: 220px;" value="{{ request('search') }}" placeholder="@lang('Search in bar content...')">
                    <button type="submit" class="btn btn-sm btn-outline--primary">@lang('Search')</button>
                    @if(request('search'))
                        <a href="{{ route('admin.frontend.sections.scrollbar') }}" class="btn btn-sm btn-outline-secondary">@lang('Clear')</a>
                    @endif
                </form>

                <div class="table-responsive scrollbar-table-shell">
                    <table class="table table--light style--two table-sm mb-0 scrollbar-admin-table">
                        <thead>
                            <tr>
                                <th style="width: 36px;"></th>
                                <th>@lang('Order')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('Position')</th>
                                <th>@lang('Template')</th>
                                <th>@lang('Publish')</th>
                                <th>@lang('Visibility')</th>
                                <th>@lang('Items')</th>
                                <th class="d-none d-md-table-cell">@lang('Content summary')</th>
                                <th class="d-none d-lg-table-cell">@lang('Preview')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bars as $bar)
                            @php
                                $dv = $bar->data_values ?? (object)[];
                                $previewItems = is_array($dv->items ?? null) ? ($dv->items ?? []) : (array)($dv->items ?? []);
                                $safeDv = [
                                    'title' => $dv->title ?? '',
                                    'position' => $dv->position ?? 'header_below',
                                    'template' => $dv->template ?? 'glass',
                                    'status' => $dv->status ?? 1,
                                    'visibility' => $dv->visibility ?? 'public',
                                    'visibility_users' => $dv->visibility_users ?? 'all',
                                    'visibility_pages' => $dv->visibility_pages ?? 'all',
                                    'schedule_start' => $dv->schedule_start ?? '',
                                    'schedule_end' => $dv->schedule_end ?? '',
                                    'display_order' => $dv->display_order ?? $bar->id,
                                    'scroll_speed' => $dv->scroll_speed ?? 45,
                                    'scroll_direction' => $dv->scroll_direction ?? 'ltr',
                                    'loop_mode' => $dv->loop_mode ?? 'infinite',
                                    'pause_on_hover' => $dv->pause_on_hover ?? 1,
                                    'gap_between_items' => $dv->gap_between_items ?? 8,
                                    'animation_type' => $dv->animation_type ?? 'linear',
                                    'bar_height' => $dv->bar_height ?? 52,
                                    'bar_padding' => $dv->bar_padding ?? '',
                                    'bar_background_type' => $dv->bar_background_type ?? '',
                                    'bar_background_value' => $dv->bar_background_value ?? '',
                                    'bar_border' => $dv->bar_border ?? '',
                                    'bar_shadow' => $dv->bar_shadow ?? '',
                                    'hide_on_mobile' => $dv->hide_on_mobile ?? 0,
                                    'hide_on_desktop' => $dv->hide_on_desktop ?? 0,
                                    'items' => $previewItems,
                                ];
                                $previewSummary = '';
                                if (!empty($previewItems)) {
                                    $parts = [];
                                    foreach ($previewItems as $it) {
                                        $parts[] = \App\Services\ScrollbarService::itemPreviewSummary($it, 80);
                                    }
                                    $previewSummary = implode(' · ', $parts);
                                    if (strlen($previewSummary) > 80) {
                                        $previewSummary = Str::limit($previewSummary, 80);
                                    }
                                }
                                $visibilityInfo = getScrollbarVisibilityReasons($bar);
                            @endphp
                            <tr class="scrollbar-list-row" data-bar-id="{{ $bar->id }}">
                                <td class="align-middle">
                                    <button type="button" class="btn btn-sm btn-outline-secondary scrollbar-expand-row" title="@lang('Expand')" aria-label="@lang('Expand row details')" aria-expanded="false"><i class="las la-chevron-down" aria-hidden="true"></i></button>
                                </td>
                                <td class="align-middle">{{ $safeDv['display_order'] }}</td>
                                <td class="align-middle"><strong class="small">{{ $safeDv['title'] ?: __('Scroll Bar') . ' #' . $bar->id }}</strong></td>
                                <td class="align-middle"><span class="badge bg-secondary small">{{ __(str_replace('_', ' ', $safeDv['position'])) }}</span></td>
                                <td class="align-middle"><span class="badge bg-info small">{{ $safeDv['template'] }}</span></td>
                                <td class="align-middle">
                                    <form method="POST" action="{{ route('admin.frontend.sections.scrollbar.toggle', $bar->id) }}" class="d-inline">
                                        @csrf
                                        @if(!empty($safeDv['status']))
                                            <button type="submit" class="badge bg-success border-0 py-1 px-2 small" title="@lang('Click to set Draft')">@lang('On')</button>
                                        @else
                                            <button type="submit" class="badge bg-warning text-dark border-0 py-1 px-2 small" title="@lang('Click to Publish')">@lang('Draft')</button>
                                        @endif
                                    </form>
                                </td>
                                <td class="align-middle">
                                    <form method="POST" action="{{ route('admin.frontend.sections.scrollbar.toggle.visibility', $bar->id) }}" class="d-inline">
                                        @csrf
                                        @if(($safeDv['visibility'] ?? 'public') === 'private')
                                            <button type="submit" class="badge bg-dark border-0 py-1 px-2 small" title="@lang('Click to make Public')">@lang('Private')</button>
                                        @else
                                            <button type="submit" class="badge bg-primary border-0 py-1 px-2 small" title="@lang('Click to make Private')">@lang('Public')</button>
                                        @endif
                                    </form>
                                </td>
                                <td class="align-middle">{{ count($previewItems) }}</td>
                                <td class="align-middle text-muted small d-none d-md-table-cell" style="max-width:180px;" title="{{ $previewSummary }}">{{ $previewSummary ? Str::limit($previewSummary, 50) : '—' }}</td>
                                <td class="align-middle text-muted small d-none d-lg-table-cell">
                                    @if(!empty($previewItems))
                                        @foreach(array_slice($previewItems, 0, 2) as $it)
                                            @php $it = is_array($it) ? $it : (array)$it; @endphp
                                            @if(($it['type'] ?? '') === 'text') {{ Str::limit($it['content'] ?? '', 12) }}
                                            @elseif(($it['type'] ?? '') === 'emoji') {{ $it['content'] ?? '' }}
                                            @else [IMG] @endif
                                        @endforeach
                                        @if(count($previewItems) > 2) ... @endif
                                    @else — @endif
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex flex-wrap gap-1">
                                        <a href="{{ route('admin.frontend.sections.scrollbar.duplicate', $bar->id) }}" class="btn btn-sm btn-outline--info" title="@lang('Duplicate')"><i class="las la-copy"></i></a>
                                        <a href="{{ route('admin.frontend.sections.scrollbar.edit', ['id' => $bar->id]) }}" class="btn btn-sm btn-outline--primary" title="@lang('Edit')"><i class="las la-edit"></i></a>
                                        <form method="POST" action="{{ route('admin.frontend.sections.scrollbar.delete', $bar->id) }}" class="d-inline scrollbar-delete-form">
                                            @csrf
                                            <input type="hidden" name="scrollbar_mode" value="default">
                                            <button type="submit" class="btn btn-sm btn-outline--danger scrollbar-delete-btn" data-delete-title="{{ $safeDv['title'] ?: ('Scroll Bar #' . $bar->id) }}" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr class="scrollbar-expand-detail d-none" data-expand-for="{{ $bar->id }}">
                                <td colspan="11" class="bg-light small p-2">
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <strong class="text-muted">@lang('All items text')</strong>
                                            <ul class="list-unstyled mb-0 mt-1 small">
                                                @foreach($previewItems as $idx => $it)
                                                    @php $it = is_array($it) ? $it : (array)$it; @endphp
                                                    <li>{{ $idx + 1 }}. @if(!empty($it['segments']) && is_array($it['segments']))
                                                        @foreach($it['segments'] as $s) {{ is_array($s) ? ($s['text'] ?? '') : '' }} @endforeach
                                                    @else
                                                        {{ ((string)($it['type'] ?? 'text')) === 'image' ? '[IMG]' : ($it['content'] ?? $it['content_text'] ?? '') }}
                                                    @endif</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-muted">@lang('Debug')</strong>
                                            <ul class="list-unstyled mb-0 mt-1 small">
                                                <li>@lang('Bar ID'): {{ $bar->id }}</li>
                                                <li>@lang('Position'): {{ $safeDv['position'] ?? '—' }}</li>
                                                <li>@lang('Order'): {{ $safeDv['display_order'] ?? '—' }}</li>
                                                <li>@lang('Visibility'): {{ $visibilityInfo['visible'] ? __('Visible') : __('Hidden') }} — {{ implode('; ', $visibilityInfo['reasons']) }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4 small">
                                    <i class="las la-bolt fa-2x mb-2 d-block opacity-50"></i>
                                    <p class="mb-0">@lang('No scroll bars yet.')</p>
                                    <p class="mb-0">@lang('Click') &quot;@lang('Add Scroll Bar')&quot; @lang('to create your first headline ticker.')</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">@lang('Scroll Bar 2 - Custom Page Setup')</h6>
                    <small class="text-muted">@lang('Use this table for custom-link based scrollbars')</small>
                </div>
                <div class="table-responsive">
                    <table class="table table--light style--two table-sm mb-0 scrollbar-admin-table">
                        <thead>
                            <tr>
                                <th>@lang('ID')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('Position')</th>
                                <th>@lang('Publish')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($customBars ?? collect()) as $bar)
                                @php $dv = $bar->data_values ?? (object)[]; @endphp
                                <tr>
                                    <td>{{ $bar->id }}</td>
                                    <td>{{ $dv->title ?? ('Scroll Bar 2 #' . $bar->id) }}</td>
                                    <td><span class="badge bg-secondary small">{{ __((string)($dv->position ?? 'custom')) }}</span></td>
                                    <td>
                                        @if(!empty($dv->status))
                                            <span class="badge bg-success">@lang('On')</span>
                                        @else
                                            <span class="badge bg-warning text-dark">@lang('Draft')</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('admin.frontend.sections.scrollbar2.edit', ['id' => $bar->id]) }}" class="btn btn-sm btn-outline--primary" title="@lang('Edit')"><i class="las la-edit"></i></a>
                                            <form method="POST" action="{{ route('admin.frontend.sections.scrollbar.delete', $bar->id) }}" class="d-inline scrollbar-delete-form">
                                                @csrf
                                                <input type="hidden" name="scrollbar_mode" value="custom">
                                                <button type="submit" class="btn btn-sm btn-outline--danger scrollbar-delete-btn" data-delete-title="{{ $dv->title ?? ('Scroll Bar 2 #' . $bar->id) }}" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3 small">@lang('No custom-link scroll bars yet. Click "Add Scroll Bar 2".')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('style')
<style>
    .scrollbar-table-shell { max-height: 70vh; overflow: auto; border: 1px solid #e2e8f0; border-radius: 10px; }
    .scrollbar-admin-table thead th {
        font-size: 0.76rem;
        padding: 0.34rem 0.42rem;
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fafc;
        border-bottom: 1px solid #dbe4ee;
        white-space: nowrap;
    }
    .scrollbar-admin-table td { padding: 0.32rem 0.42rem; vertical-align: middle !important; font-size: 0.77rem; }
    .scrollbar-admin-table .badge { font-size: 0.66rem; letter-spacing: .2px; }
    .scrollbar-admin-table .btn.btn-sm { padding: 0.18rem 0.36rem; min-height: 24px; line-height: 1.05; }
    .scrollbar-admin-table .scrollbar-list-row:nth-child(odd) { background: #fcfdff; }
    .scrollbar-admin-table .scrollbar-list-row:hover { background: #f5f9ff; }
</style>
@endpush

@push('script')
<script>
(function() {
    "use strict";
    if (typeof jQuery === 'undefined') return;
    var $ = jQuery;
    $(document).ready(function() {
        $(document).on('click', '.scrollbar-expand-row', function() {
            var $btn = $(this);
            var $row = $btn.closest('tr.scrollbar-list-row');
            var barId = $row.data('bar-id');
            var $detail = $('tr.scrollbar-expand-detail[data-expand-for="' + barId + '"]');
            var expanded = !$detail.hasClass('d-none');
            $detail.toggleClass('d-none');
            $btn.find('i').toggleClass('las la-chevron-down las la-chevron-up');
            $btn.attr('aria-expanded', expanded ? 'false' : 'true');
        });

        $(document).on('click', '.scrollbar-delete-btn', function(e) {
            var title = String($(this).data('delete-title') || '').trim();
            var msg = title
                ? ('এই Scroll Bar টি ডিলিট করবেন?\n\n' + title)
                : 'এই Scroll Bar টি ডিলিট করবেন?';
            if (!window.confirm(msg)) {
                e.preventDefault();
                return false;
            }
        });
    });
})();
</script>
@endpush
