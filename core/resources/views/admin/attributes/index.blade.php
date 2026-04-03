@extends('admin.layouts.app')

@section('panel')
<div class="attributes-panel">
    {{-- ব্যাখ্যা: এই পেজে কী করা যায় --}}
    <div class="card b-radius--10 mb-4 border-primary">
        <div class="card-header bg--primary text-white py-2">
            <h6 class="mb-0"><i class="las la-info-circle"></i> @lang('Product Attributes') – কী এবং কীভাবে ব্যবহার করবেন</h6>
        </div>
        <div class="card-body small">
            <p class="mb-2"><strong>এই পেজের কাজ:</strong> এখানে আপনি সেই সব <strong>অ্যাট্রিবিউট</strong> তৈরি ও ম্যানেজ করবেন যেগুলো প্রোডাক্টে ব্যবহার হয় – যেমন <strong>Size</strong> (S, M, L), <strong>Color</strong> (Red, Blue), <strong>Storage</strong> (64GB, 128GB), <strong>RAM</strong> ইত্যাদি।</p>
            <ul class="mb-2">
                <li><strong>Name:</strong> অ্যাট্রিবিউটের নাম (যেমন: Size, Color)।</li>
                <li><strong>Slug:</strong> ইউনিক আইডি (ইংরেজি ছোট অক্ষর, যেমন: size, color)।</li>
                <li><strong>Type:</strong> Select = ড্রপডাউন, Color = রঙ পিকার, Text/Number = টেক্সট বা সংখ্যা।</li>
                <li><strong>Values:</strong> কমা দিয়ে অপশন লিখুন (যেমন: S, M, L, XL বা Red, Blue, Green)। Type Select/Color এর জন্য।</li>
            </ul>
            <p class="mb-0"><strong>পরবর্তী ধাপ:</strong> অ্যাট্রিবিউট তৈরি করার পর <a href="{{ route('admin.category.attributes.index') }}" class="text--base fw-bold">Category Attributes</a> পেজে গিয়ে প্রতিটি ক্যাটাগরির জন্য কোন কোন অ্যাট্রিবিউট ব্যবহার হবে সেটা সিলেক্ট করুন।</p>
        </div>
    </div>

    {{-- Stats --}}
    @if(isset($stats))
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="card b-radius--10 border-0 shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="attr-stat-icon attr-stat-icon--primary rounded-3"><i class="las la-list"></i></div>
                    <div>
                        <span class="d-block small text-muted">@lang('Total')</span>
                        <span class="fw-bold fs-5">{{ $stats['total'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="card b-radius--10 border-0 shadow-sm h-100">
                <div class="card-body py-3 d-flex align-items-center gap-3">
                    <div class="attr-stat-icon attr-stat-icon--success rounded-3"><i class="las la-check-circle"></i></div>
                    <div>
                        <span class="d-block small text-muted">@lang('Active')</span>
                        <span class="fw-bold fs-5">{{ $stats['active'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        @if(!empty($stats['by_type']))
        @foreach($stats['by_type'] as $t => $c)
        <div class="col-sm-6 col-md-2">
            <div class="card b-radius--10 border-0 shadow-sm h-100">
                <div class="card-body py-2 text-center">
                    <span class="badge badge--info mb-1">{{ ucfirst($t) }}</span>
                    <span class="d-block fw-bold">{{ $c }}</span>
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
    @endif

    {{-- Filters & toolbar --}}
    <form method="get" action="{{ route('admin.attributes.index') }}" id="attrFilterForm" class="card b-radius--10 mb-4">
        <div class="card-body py-2 px-3">
            <div class="row g-2 align-items-end flex-wrap">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                <div class="col-auto">
                    <label class="form-label small mb-0">@lang('Type')</label>
                    <select name="type" class="form-select form-select-sm" style="width:auto;">
                        <option value="">@lang('All')</option>
                        <option value="select" {{ request('type') === 'select' ? 'selected' : '' }}>Select</option>
                        <option value="color" {{ request('type') === 'color' ? 'selected' : '' }}>Color</option>
                        <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>Text</option>
                        <option value="number" {{ request('type') === 'number' ? 'selected' : '' }}>Number</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">@lang('Status')</label>
                    <select name="status" class="form-select form-select-sm" style="width:auto;">
                        <option value="">@lang('All')</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>@lang('Active')</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>@lang('Inactive')</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">@lang('Sort')</label>
                    <select name="sort_by" class="form-select form-select-sm" style="width:auto;">
                        <option value="sort_order" {{ ($sortBy ?? '') === 'sort_order' ? 'selected' : '' }}>@lang('Order')</option>
                        <option value="name" {{ ($sortBy ?? '') === 'name' ? 'selected' : '' }}>@lang('Name')</option>
                        <option value="slug" {{ ($sortBy ?? '') === 'slug' ? 'selected' : '' }}>Slug</option>
                        <option value="type" {{ ($sortBy ?? '') === 'type' ? 'selected' : '' }}>@lang('Type')</option>
                        <option value="categories_count" {{ ($sortBy ?? '') === 'categories_count' ? 'selected' : '' }}>@lang('Categories')</option>
                        <option value="created_at" {{ ($sortBy ?? '') === 'created_at' ? 'selected' : '' }}>@lang('Date')</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="sort_dir" class="form-select form-select-sm" style="width:auto;">
                        <option value="asc" {{ ($sortDir ?? 'asc') === 'asc' ? 'selected' : '' }}>@lang('Asc')</option>
                        <option value="desc" {{ ($sortDir ?? '') === 'desc' ? 'selected' : '' }}>@lang('Desc')</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">@lang('Per page')</label>
                    <select name="per_page" class="form-select form-select-sm" style="width:auto;">
                        @foreach([10, 20, 50, 100] as $n)
                            <option value="{{ $n }}" {{ (int)request('per_page', getPaginate()) === $n ? 'selected' : '' }}>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn--primary"><i class="las la-filter"></i> @lang('Apply')</button>
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-sm btn-outline--dark">@lang('Reset')</a>
                </div>
            </div>
        </div>
    </form>

    {{-- Bulk bar --}}
    @if($attributes->total() > 0)
    <div id="attrBulkBar" class="card b-radius--10 mb-3 border-primary d-none">
        <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center gap-2">
            <span class="small text-muted"><span id="attrBulkCount">0</span> @lang('selected')</span>
            <button type="button" class="btn btn-sm btn-outline--dark" id="attrBulkDeselect">@lang('Deselect')</button>
            <form action="{{ route('admin.attributes.bulk.status') }}" method="post" class="d-inline" id="attrBulkForm">
                @csrf
                <input type="hidden" name="status" value="1">
                <input type="hidden" value="" id="attrBulkIds">
                <button type="submit" class="btn btn-sm btn-outline--primary">@lang('Enable')</button>
            </form>
            <form action="{{ route('admin.attributes.bulk.status') }}" method="post" class="d-inline" id="attrBulkFormDisable">
                @csrf
                <input type="hidden" name="status" value="0">
                <input type="hidden" value="" id="attrBulkIdsDisable">
                <button type="submit" class="btn btn-sm btn-outline--danger">@lang('Disable')</button>
            </form>
        </div>
    </div>
    @endif

    {{-- Table --}}
    <div class="card b-radius--10 overflow-hidden">
        <div class="card-header bg-light py-2 px-3">
            <span class="fw-semibold">@lang('All Attributes')</span>
            <span class="text-muted small ms-2">({{ $attributes->total() }} @lang('total'))</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table--light style--two table-hover mb-0">
                    <thead>
                        <tr>
                            @if($attributes->total() > 0)
                            <th style="width:36px"><input type="checkbox" class="form-check-input" id="attrSelectAll" title="@lang('Select all')"></th>
                            @endif
                            <th>@lang('Name')</th>
                            <th>@lang('Slug')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Values')</th>
                            <th class="text-center">@lang('Order')</th>
                            <th class="text-center">@lang('Categories')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attributes as $attr)
                        <tr>
                            @if($attributes->total() > 0)
                            <td><input type="checkbox" class="form-check-input attr-row-check" value="{{ $attr->id }}"></td>
                            @endif
                            <td><strong>{{ $attr->name }}</strong></td>
                            <td><code class="small">{{ $attr->slug }}</code></td>
                            <td>
                                @php $types = ['select' => 'Select', 'color' => 'Color', 'text' => 'Text', 'number' => 'Number']; @endphp
                                <span class="badge badge--info">{{ $types[$attr->type] ?? $attr->type }}</span>
                            </td>
                            <td>
                                @if(!empty($attr->values))
                                    <span class="small text-muted">{{ implode(', ', array_slice($attr->values, 0, 5)) }}{{ count($attr->values) > 5 ? '…' : '' }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $attr->sort_order }}</td>
                            <td class="text-center">{{ $attr->categories_count ?? 0 }}</td>
                            <td>
                                @if($attr->status)
                                    <span class="badge badge--success">@lang('Active')</span>
                                @else
                                    <span class="badge badge--danger">@lang('Inactive')</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline--primary dropdown-toggle" type="button" data-bs-toggle="dropdown">@lang('Action')</button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('admin.attributes.edit', $attr->id) }}"><i class="las la-pen me-1"></i> @lang('Edit')</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.attributes.duplicate', $attr->id) }}"><i class="las la-copy me-1"></i> @lang('Duplicate')</a></li>
                                        <li>
                                            <form action="{{ route('admin.attributes.status', $attr->id) }}" method="post" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item">{{ $attr->status ? __('Disable') : __('Enable') }}</button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button type="button" class="dropdown-item text-danger confirmationBtn" data-action="{{ route('admin.attributes.destroy', $attr->id) }}" data-question="@lang('Delete this attribute? It will be removed from all categories.')"><i class="las la-trash me-1"></i> @lang('Delete')</button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center text-muted py-5" colspan="{{ $attributes->total() > 0 ? 9 : 8 }}">
                                @lang('No attributes yet.') <a href="{{ route('admin.attributes.create') }}">@lang('Add first attribute')</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($attributes->hasPages())
            <div class="card-footer py-3">{{ paginateLinks($attributes) }}</div>
            @endif
        </div>
    </div>
</div>
<x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
<x-search-form placeholder="@lang('Search attribute...')" />
<a href="{{ route('admin.attributes.create') }}" class="btn btn--primary btn-sm">
    <i class="las la-plus"></i> @lang('Add Attribute')
</a>
<a href="{{ route('admin.category.attributes.index') }}" class="btn btn--outline-primary btn-sm">
    <i class="las la-link"></i> @lang('Category Attributes')
</a>
@endpush

@push('style')
<style>
.attr-stat-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.attr-stat-icon--primary { background: rgba(13, 110, 253, 0.12); color: #0d6efd; }
.attr-stat-icon--success { background: rgba(25, 135, 84, 0.12); color: #198754; }
</style>
@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var bulkBar = document.getElementById('attrBulkBar');
    var bulkCount = document.getElementById('attrBulkCount');
    var selectAll = document.getElementById('attrSelectAll');
    var rowChecks = document.querySelectorAll('.attr-row-check');
    var bulkDeselect = document.getElementById('attrBulkDeselect');
    var bulkForm = document.getElementById('attrBulkForm');
    var bulkIds = document.getElementById('attrBulkIds');
    var bulkIdsDisable = document.getElementById('attrBulkIdsDisable');

    function updateBulk() {
        var checked = document.querySelectorAll('.attr-row-check:checked');
        var ids = Array.from(checked).map(function(c) { return c.value; });
        if (bulkBar) bulkBar.classList.toggle('d-none', ids.length === 0);
        if (bulkCount) bulkCount.textContent = ids.length;
        if (bulkIds) bulkIds.value = ids.join(',');
        if (bulkIdsDisable) bulkIdsDisable.value = ids.join(',');
        if (selectAll) selectAll.checked = rowChecks.length > 0 && checked.length === rowChecks.length;
    }

    if (selectAll) selectAll.addEventListener('change', function() { rowChecks.forEach(function(c) { c.checked = selectAll.checked; }); updateBulk(); });
    rowChecks.forEach(function(c) { c.addEventListener('change', updateBulk); });
    if (bulkDeselect) bulkDeselect.addEventListener('click', function() { rowChecks.forEach(function(c) { c.checked = false; }); if (selectAll) selectAll.checked = false; updateBulk(); });

    if (bulkForm) {
        bulkForm.addEventListener('submit', function() {
            var ids = (document.getElementById('attrBulkIds') || {}).value;
            if (!ids) { event.preventDefault(); alert('@lang("Please select at least one attribute.")'); return; }
            var inputs = bulkForm.querySelectorAll('input[name="ids"]');
            if (inputs.length) inputs.forEach(function(i) { i.remove(); });
            ids.split(',').forEach(function(id) {
                var inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
                bulkForm.appendChild(inp);
            });
        });
    }
    var disableForm = document.getElementById('attrBulkFormDisable');
    if (disableForm) {
        disableForm.addEventListener('submit', function(e) {
            var ids = (document.getElementById('attrBulkIdsDisable') || {}).value;
            if (!ids) { e.preventDefault(); alert('@lang("Please select at least one attribute.")'); return; }
            disableForm.querySelectorAll('input[name="ids[]"]').forEach(function(i) { i.remove(); });
            ids.split(',').forEach(function(id) {
                var inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
                disableForm.appendChild(inp);
            });
        });
    }
});
</script>
@endpush
