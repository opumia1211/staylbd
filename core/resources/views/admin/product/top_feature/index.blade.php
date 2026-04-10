@extends('admin.layouts.app')
@section('panel')
{{-- Stats --}}
<div class="row mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm bg--primary overflow-hidden">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-white-50 mb-0 small text-uppercase">@lang('Total')</p>
                        <h4 class="mb-0 text-white">{{ $stats['total'] }}</h4>
                    </div>
                    <i class="las la-th-large fa-2x text-white opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm bg-success overflow-hidden">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-white-50 mb-0 small text-uppercase">@lang('Active')</p>
                        <h4 class="mb-0 text-white">{{ $stats['active'] }}</h4>
                    </div>
                    <i class="las la-eye fa-2x text-white opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm bg-danger overflow-hidden">
            <div class="card-body py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-white-50 mb-0 small text-uppercase">@lang('Hidden')</p>
                        <h4 class="mb-0 text-white">{{ $stats['hidden'] }}</h4>
                    </div>
                    <i class="las la-eye-slash fa-2x text-white opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center gap-2 py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="las la-th-large me-2"></i>{{ $pageTitle }}</h5>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-outline--info"><i class="las la-external-link-alt"></i> @lang('View Homepage')</a>
                    <button type="button" class="btn btn--primary btn-sm" id="btnAddTopFeature" data-bs-toggle="modal" data-bs-target="#topFeatureModal" data-toggle="modal" data-target="#topFeatureModal">
                        <i class="las la-plus"></i> @lang('Add Feature Box')
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">@lang('These cards appear below the banner on the homepage. Only active items are shown to visitors. Drag the handle to reorder.')</p>
                @include('partials.notify')
                <div id="reorderMessage" class="alert alert-success py-2 small mb-3 d-none" role="alert"></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle admin-top-feature-table">
                        <thead class="table-light">
                            <tr>
                                <th width="44" class="text-center">#</th>
                                <th width="64">@lang('Image')</th>
                                <th>@lang('Title')</th>
                                <th>@lang('Link')</th>
                                <th class="text-end">@lang('Offer')</th>
                                <th width="100">@lang('Status')</th>
                                <th width="150" class="text-end">@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-top-features">
                            @forelse($features as $f)
                            @php
                                $featureJson = json_encode([
                                    'id' => $f->id,
                                    'title' => $f->title,
                                    'background_style' => $f->background_style ?? '',
                                    'product_id' => $f->product_id,
                                    'category_id' => $f->category_id,
                                    'offer_price' => $f->offer_price !== null ? (string) $f->offer_price : '',
                                    'discount_percentage' => $f->discount_percentage !== null ? (string) $f->discount_percentage : '',
                                    'offer_start' => $f->offer_start ? $f->offer_start->format('Y-m-d\TH:i') : '',
                                    'offer_end' => $f->offer_end ? $f->offer_end->format('Y-m-d\TH:i') : '',
                                    'redirect_url' => $f->redirect_url ?? '',
                                    'status' => (int) $f->status,
                                    'image' => $f->icon_image ? $f->imageShow() : '',
                                ]);
                            @endphp
                            <tr data-id="{{ $f->id }}" data-feature-b64="{{ base64_encode($featureJson) }}"
                                data-title="{{ e($f->title) }}" data-background_style="{{ e($f->background_style ?? '') }}"
                                data-product_id="{{ $f->product_id ?? '' }}" data-category_id="{{ $f->category_id ?? '' }}"
                                data-offer_price="{{ $f->offer_price ?? '' }}" data-discount_percentage="{{ $f->discount_percentage ?? '' }}"
                                data-offer_start="{{ $f->offer_start ? $f->offer_start->format('Y-m-d\TH:i') : '' }}" data-offer_end="{{ $f->offer_end ? $f->offer_end->format('Y-m-d\TH:i') : '' }}"
                                data-redirect_url="{{ e($f->redirect_url ?? '') }}" data-status="{{ $f->status }}"
                                data-image="{{ $f->icon_image ? e($f->imageShow()) : '' }}">
                                <td class="text-center align-middle">
                                    <i class="las la-grip-vertical text-muted drag-handle" style="cursor: move;" title="@lang('Drag to reorder')"></i>
                                    <span class="text-muted">{{ $f->sort_order }}</span>
                                </td>
                                <td class="align-middle">
                                    @if($f->icon_image)
                                        <img src="{{ $f->imageShow() }}" alt="" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                                    @else
                                        <span class="d-inline-flex align-items-center justify-content-center rounded border bg-light text-muted" style="width:48px;height:48px;"><i class="las la-image"></i></span>
                                    @endif
                                </td>
                                <td class="align-middle fw-medium">{{ __($f->title) }}</td>
                                <td class="align-middle small">
                                    @if($f->redirect_url)
                                        <span class="text-info text-break">{{ Str::limit($f->redirect_url, 35) }}</span>
                                    @elseif($f->product)
                                        <span class="text-success">{{ Str::limit(__($f->product->name), 25) }}</span>
                                    @elseif($f->category)
                                        <span class="text-primary">{{ Str::limit(__($f->category->name), 25) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="align-middle text-end small">
                                    @if($f->offer_price !== null)
                                        <span class="fw-semibold">{{ $general->cur_sym }}{{ showAmount($f->offer_price) }}</span>
                                    @endif
                                    @if($f->discount_percentage !== null)
                                        <span class="badge bg-success ms-1">{{ $f->discount_percentage }}%</span>
                                    @endif
                                    @if($f->offer_price === null && $f->discount_percentage === null)
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($f->status)
                                        <span class="badge bg-success">@lang('Active')</span>
                                    @else
                                        <span class="badge bg-danger">@lang('Hidden')</span>
                                    @endif
                                </td>
                                <td class="align-middle text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline--primary edit-top-feature" title="@lang('Edit')"><i class="las la-pen"></i></button>
                                        <form action="{{ route('admin.product.topbar.status', $f->id) }}" method="post" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline--{{ $f->status ? 'warning' : 'success' }}" title="{{ $f->status ? __('Disable') : __('Enable') }}"><i class="las la-{{ $f->status ? 'eye-slash' : 'eye' }}"></i></button>
                                        </form>
                                        <button type="button" class="btn btn-outline--danger confirmationBtn" data-action="{{ route('admin.product.topbar.destroy', $f->id) }}" data-question="@lang('Delete this feature box?')" title="@lang('Delete')"><i class="las la-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="las la-th-large fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-2">@lang('No feature boxes yet.')</p>
                                    <button type="button" class="btn btn--primary btn-sm" id="btnAddFirstTopFeature" data-bs-toggle="modal" data-bs-target="#topFeatureModal" data-toggle="modal" data-target="#topFeatureModal">
                                        <i class="las la-plus"></i> @lang('Add your first feature box')
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add / Edit Modal --}}
<div class="modal fade" id="topFeatureModal" tabindex="-1" aria-labelledby="topFeatureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="topFeatureModalLabel">@lang('Add Feature Box')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="topFeatureForm" method="post" enctype="multipart/form-data" action="{{ route('admin.product.topbar.store') }}">
                @csrf
                <div class="modal-body pt-2">
                    <input type="hidden" name="id" id="tf_id" value="">
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item"><button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabBasic">@lang('Basic')</button></li>
                        <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tabLink">@lang('Link & Offer')</button></li>
                        <li class="nav-item"><button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tabSchedule">@lang('Schedule & Status')</button></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tabBasic">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">@lang('Feature Title / Name') <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="tf_title" class="form-control" required maxlength="255" placeholder="e.g. Hot Deals, Flash Sale">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">@lang('Status')</label>
                                    <select name="status" id="tf_status" class="form-select">
                                        <option value="1">@lang('Active')</option>
                                        <option value="0">@lang('Hidden')</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Icon / Image')</label>
                                    <div class="d-flex flex-wrap align-items-start gap-3">
                                        <input type="file" name="icon_image" id="tf_icon_image" class="form-control form-control-sm" accept="image/jpeg,image/jpg,image/png,image/webp,image/gif" style="max-width:220px;">
                                        <div id="tf_image_preview" class="d-flex align-items-center gap-2"></div>
                                    </div>
                                    <div class="form-text">JPG, PNG, WebP, GIF. Recommended 200×200. Leave empty to keep current.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">@lang('Background Style')</label>
                                    <input type="text" name="background_style" id="tf_background_style" class="form-control" maxlength="100" placeholder="#f0f0f0 or rgba(0,0,0,0.05)">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabLink">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">@lang('Redirect Link (URL)')</label>
                                    <input type="text" name="redirect_url" id="tf_redirect_url" class="form-control" maxlength="500" placeholder="https://... or /path or leave blank">
                                    <div class="form-text">@lang('Optional. If empty, link from Product or Category below is used.')</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Product Category')</label>
                                    <select name="category_id" id="tf_category_id" class="form-select select2-topfeature">
                                        <option value="">— @lang('None') —</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->id }}">{{ __($c->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Specific Product')</label>
                                    <select name="product_id" id="tf_product_id" class="form-select select2-topfeature">
                                        <option value="">— @lang('None') —</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}">{{ __($p->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Offer Price')</label>
                                    <input type="number" name="offer_price" id="tf_offer_price" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Discount %')</label>
                                    <input type="number" name="discount_percentage" id="tf_discount_percentage" class="form-control" min="0" max="100" step="0.01" placeholder="0">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tabSchedule">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Offer Start')</label>
                                    <input type="datetime-local" name="offer_start" id="tf_offer_start" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">@lang('Offer End')</label>
                                    <input type="datetime-local" name="offer_end" id="tf_offer_end" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal" data-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn--primary" id="topFeatureSubmitBtn"><i class="las la-save me-1"></i> @lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-confirmation-modal />
@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('topFeatureModal');
    var form = document.getElementById('topFeatureForm');
    var submitBtn = document.getElementById('topFeatureSubmitBtn');
    if (!form || !modal) return;

    if (modal.parentNode && modal.parentNode !== document.body) {
        document.body.appendChild(modal);
    }

    window.openAddModal = function() {
        document.getElementById('topFeatureModalLabel').textContent = '{{ __("Add Feature Box") }}';
        form.setAttribute('action', '{{ route("admin.product.topbar.store") }}');
        form.setAttribute('method', 'post');
        form.setAttribute('enctype', 'multipart/form-data');
        ['tf_id','tf_title','tf_background_style','tf_category_id','tf_product_id','tf_offer_price','tf_discount_percentage','tf_offer_start','tf_offer_end','tf_redirect_url'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) el.value = id === 'tf_status' ? '1' : '';
        });
        document.getElementById('tf_status').value = '1';
        document.getElementById('tf_icon_image').value = '';
        document.getElementById('tf_image_preview').innerHTML = '';
        if (typeof $ !== 'undefined' && $('.select2-topfeature').length) {
            $('.select2-topfeature').val(null).trigger('change');
        }
    };

    function showTopFeatureModal() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var m = bootstrap.Modal.getOrCreateInstance(modal);
            if (m) m.show();
        } else if (typeof $ !== 'undefined' && $('#topFeatureModal').length) {
            $('#topFeatureModal').modal('show');
        }
    }

    var btnAdd = document.getElementById('btnAddTopFeature');
    if (btnAdd) btnAdd.addEventListener('click', function(e) { e.preventDefault(); e.stopImmediatePropagation(); openAddModal(); showTopFeatureModal(); });

    var btnAddFirst = document.getElementById('btnAddFirstTopFeature');
    if (btnAddFirst) {
        btnAddFirst.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            openAddModal();
            showTopFeatureModal();
        });
    }

    function getEditData(row) {
        var b64 = row.getAttribute('data-feature-b64');
        if (b64) {
            try {
                return JSON.parse(atob(b64));
            } catch (e) {}
        }
        return {
            id: row.getAttribute('data-id') || '',
            title: row.getAttribute('data-title') || '',
            background_style: row.getAttribute('data-background_style') || '',
            product_id: row.getAttribute('data-product_id') || '',
            category_id: row.getAttribute('data-category_id') || '',
            offer_price: row.getAttribute('data-offer_price') || '',
            discount_percentage: row.getAttribute('data-discount_percentage') || '',
            offer_start: row.getAttribute('data-offer_start') || '',
            offer_end: row.getAttribute('data-offer_end') || '',
            redirect_url: row.getAttribute('data-redirect_url') || '',
            status: row.getAttribute('data-status') !== null ? row.getAttribute('data-status') : '1',
            image: row.getAttribute('data-image') || ''
        };
    }

    var sortableTbody = document.getElementById('sortable-top-features');
    if (sortableTbody) {
        sortableTbody.addEventListener('click', function(e) {
            var btn = e.target.closest('.edit-top-feature');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            var row = btn.closest('tr');
            if (!row || !row.getAttribute('data-id')) return;
            var d = getEditData(row);
            document.getElementById('topFeatureModalLabel').textContent = '{{ __("Edit Feature Box") }}';
            form.setAttribute('action', '{{ route("admin.product.topbar.update", ["id" => "__ID__"]) }}'.replace('__ID__', d.id));
            form.setAttribute('method', 'post');
            form.setAttribute('enctype', 'multipart/form-data');
            document.getElementById('tf_id').value = d.id || '';
            document.getElementById('tf_title').value = d.title || '';
            document.getElementById('tf_background_style').value = d.background_style || '';
            document.getElementById('tf_category_id').value = d.category_id || '';
            document.getElementById('tf_product_id').value = d.product_id || '';
            document.getElementById('tf_offer_price').value = d.offer_price || '';
            document.getElementById('tf_discount_percentage').value = d.discount_percentage || '';
            document.getElementById('tf_offer_start').value = d.offer_start || '';
            document.getElementById('tf_offer_end').value = d.offer_end || '';
            document.getElementById('tf_redirect_url').value = d.redirect_url || '';
            document.getElementById('tf_status').value = d.status !== undefined && d.status !== null ? String(d.status) : '1';
            document.getElementById('tf_icon_image').value = '';
            var prev = document.getElementById('tf_image_preview');
            prev.innerHTML = '';
            if (d.image) {
                var img = document.createElement('img');
                img.src = d.image;
                img.alt = '';
                img.className = 'rounded border';
                img.style.maxWidth = '80px';
                img.style.maxHeight = '80px';
                prev.appendChild(img);
            }
            if (typeof $ !== 'undefined' && $('.select2-topfeature').length) {
                $('.select2-topfeature').val(null).trigger('change');
                setTimeout(function() {
                    $('#tf_category_id').val(d.category_id || '').trigger('change');
                    $('#tf_product_id').val(d.product_id || '').trigger('change');
                }, 150);
            }
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(modal).show();
            } else if (typeof $ !== 'undefined' && $('#topFeatureModal').length) {
                $('#topFeatureModal').modal('show');
            }
        });
    }

    var iconInput = document.getElementById('tf_icon_image');
    if (iconInput) {
        iconInput.addEventListener('change', function(e) {
            var f = e.target.files[0];
            var prev = document.getElementById('tf_image_preview');
            prev.innerHTML = '';
            if (!f) return;
            var r = new FileReader();
            r.onload = function() {
                var img = document.createElement('img');
                img.src = r.result;
                img.className = 'rounded border';
                img.style.maxWidth = '100px';
                img.style.maxHeight = '100px';
                prev.appendChild(img);
            };
            r.readAsDataURL(f);
        });
    }

    form.addEventListener('submit', function() {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> {{ __("Saving...") }}';
        }
    });

    var tbody = document.getElementById('sortable-top-features');
    if (tbody && typeof Sortable !== 'undefined') {
        new Sortable(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function() {
                var order = [];
                tbody.querySelectorAll('tr[data-id]').forEach(function(tr) {
                    order.push(parseInt(tr.getAttribute('data-id'), 10));
                });
                var msgEl = document.getElementById('reorderMessage');
                fetch('{{ route("admin.product.topbar.reorder") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ order: order })
                }).then(function(r) {
                    return r.json().then(function(data) {
                        if (!r.ok) throw new Error(data.message || 'Error');
                        return data;
                    });
                }).then(function() {
                    if (msgEl) {
                        msgEl.textContent = '{{ __("Order saved.") }}';
                        msgEl.classList.remove('d-none', 'alert-danger');
                        msgEl.classList.add('alert-success');
                        setTimeout(function() { msgEl.classList.add('d-none'); }, 3000);
                    }
                    tbody.querySelectorAll('tr[data-id]').forEach(function(tr, i) {
                        var span = tr.querySelector('.drag-handle + .text-muted');
                        if (span) span.textContent = i + 1;
                    });
                }).catch(function(err) {
                    if (msgEl) {
                        msgEl.textContent = err.message || '{{ __("Failed to save order.") }}';
                        msgEl.classList.remove('d-none', 'alert-success');
                        msgEl.classList.add('alert-danger');
                    }
                });
            }
        });
    }
});
</script>
@endpush

@push('script-lib')
<script src="{{ asset('assets/admin/js/vendor/sortable.min.js') }}?v={{ $assetVersion ?? config('app.version') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        var $modal = $('#topFeatureModal');
        $('.select2-topfeature').select2({
            width: '100%',
            placeholder: '— {{ __("None") }} —',
            allowClear: true,
            dropdownParent: $modal.length ? $modal : document.body
        });
    }
});
</script>
@endpush
