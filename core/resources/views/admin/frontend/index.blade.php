@extends('admin.layouts.app')
@section('panel')
<div class="row g-4">
    <!-- Frontend Intelligence: Primary Controls -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3 py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class="las la-broadcast-tower"></i></span>
                    </div>
                    <div>
                        <h5 class="mb-0 text-capitalize">{{ __(keyToTitle($key)) }} Intelligence</h5>
                        <small class="text-muted">@lang('Manage public display parameters and dynamic elements')</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($key == 'contact_us')
                        <a href="{{ route('admin.ticket.index') }}" class="btn btn-label-success btn-sm"><i class="las la-inbox me-1"></i> @lang('Go to Inbox')</a>
                    @endif
                    <div class="badge bg-label-info rounded-pill px-3 py-2 d-flex align-items-center">
                        <i class="las la-database me-1"></i> {{ count($elements ?? []) }} @lang('Elements')
                    </div>
                </div>
            </div>
        </div>

        @if($key == 'contact_us' || $key == 'policy_pages')
            <div class="alert bg-label-secondary border-0 mb-4 d-flex align-items-center p-3">
                <i class="las la-info-circle fs-4 me-2 text-primary"></i>
                <div class="small">
                    @if($key == 'contact_us')
                        @lang('Channel settings (WhatsApp, Telegram, Email) – set below, used in background for various widgets.')
                    @else
                        @lang('Policy Pages — Privacy, Terms, Shipping, etc. Public links available at') <code class="small text-primary">/policy/{id}</code>
                    @endif
                </div>
            </div>
        @endif

        {{-- Section Content (Single Items) --}}
        @if(@$section->content)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-bottom py-3 d-flex align-items-center">
                <i class="las la-edit me-2 fs-4 text-primary"></i>
                <h6 class="mb-0">@lang('Configuration Matrix')</h6>
            </div>
            <div class="card-body">
                <form action="{{ route(getFrontendSectionRoute($key, 'content'), $key) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="content">
                    <div class="row g-3">
                        @foreach($section->content as $k => $item)
                            @if($k == 'images')
                                @foreach($item as $imgKey => $image)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold small text-muted">{{__(keyToTitle(@$imgKey))}}</label>
                                        <div class="fe-image-upload-wrapper border rounded p-3 text-center bg-light-soft position-relative">
                                            <input type="hidden" name="has_image" value="1">
                                            <div class="preview-container mb-3 mx-auto" style="width: 150px; height: 100px; overflow: hidden; border-radius: 8px; border: 1px dashed #d9dee3;">
                                                <img src="{{getImage('assets/images/frontend/' . $key .'/'. @$content->data_values->$imgKey,@$section->content->images->$imgKey->size) }}" class="w-100 h-100 object-fit-cover preview-img-target">
                                            </div>
                                            <div class="upload-controls">
                                                <input type="file" class="form-control form-control-sm d-none image-upload-input" name="image_input[{{ @$imgKey }}]" id="fe_c_img_{{ $key }}_{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                <label for="fe_c_img_{{ $key }}_{{ $loop->index }}" class="btn btn-outline-primary btn-sm px-4">
                                                    <i class="las la-cloud-upload-alt me-1"></i> @lang('Select File')
                                                </label>
                                            </div>
                                            <small class="text-muted d-block mt-2 tiny">
                                                @if(@$section->content->images->$imgKey->size) <b>{{@$section->content->images->$imgKey->size}}px</b> | @endif @lang('JPG, PNG, JPEG supported')
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @if($k != 'images')
                                    <div class="col-md-12">
                                        <label class="form-label fw-bold small text-muted">{{__(keyToTitle($k))}}</label>
                                        @if($item == 'icon')
                                            <div class="input-group input-group-merge">
                                                <input type="text" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" value="{{ @$content->data_values->$k }}" required>
                                                <span class="input-group-text border-start-0" data-icon="las la-home" role="iconpicker"></span>
                                            </div>
                                        @elseif($item == 'textarea')
                                            <textarea rows="4" class="form-control" name="{{$k}}" required>{{ @$content->data_values->$k}}</textarea>
                                        @elseif($item == 'textarea-nic')
                                            <textarea rows="8" class="form-control nicEdit" name="{{$k}}">{{ @$content->data_values->$k}}</textarea>
                                        @elseif($k == 'select')
                                            <select class="form-select" name="{{ @$item->name }}">
                                                @foreach($item->options as $selectItemKey => $selectOption)
                                                    <option value="{{ $selectItemKey }}" @if(@$content->data_values->{$item->name} == $selectItemKey) selected @endif>{{ $selectOption }}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" class="form-control" name="{{$k}}" value="{{@$content->data_values->$k }}" required/>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-5 shadow-sm">
                            <i class="las la-save me-1"></i> @lang('Deploy Changes')
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Section Elements (Table items) --}}
        @if(@$section->element)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center">
                    <i class="las la-layer-group me-2 fs-4 text-success"></i>
                    <h6 class="mb-0">@lang('Dynamic Element Hub')</h6>
                </div>
                <div class="d-flex gap-2 w-auto">
                    <div class="input-group input-group-merge input-group-sm w-px-200">
                        <span class="input-group-text"><i class="las la-search"></i></span>
                        <input type="text" id="elementSearch" class="form-control" placeholder="@lang('Quick Filter...')">
                    </div>
                    @if($section->element->modal)
                        <button type="button" class="btn btn-primary btn-sm addBtn shadow-sm px-3"><i class="las la-plus me-1"></i> @lang('Add New')</button>
                    @else
                        <a href="{{ route(getFrontendSectionRoute($key, 'element')) }}" class="btn btn-primary btn-sm shadow-sm px-3"><i class="las la-plus me-1"></i> @lang('Add New')</a>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-lighter">
                        <tr>
                            <th class="ps-4 py-2 small fw-bold">@lang('SL')</th>
                            @if(@$section->element->images)
                                <th class="py-2 small fw-bold">@lang('Visual')</th>
                            @endif
                            @foreach($section->element as $k => $type)
                                @if($k !='modal')
                                    @if($type=='text' || $type=='icon' || $type == 'textarea' || $k == 'select')
                                        <th class="py-2 small fw-bold">{{ __(keyToTitle($k == 'select' ? @$section->element->$k->name : $k)) }}</th>
                                    @endif
                                @endif
                            @endforeach
                            <th class="text-end pe-4 py-2 small fw-bold">@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody class="list" id="elementTableBody">
                        @forelse($elements as $data)
                        <tr class="element-row" data-search="{{ strtolower(json_encode($data->data_values)) }}">
                            <td class="ps-4"><span class="text-muted small">#{{$loop->iteration}}</span></td>
                            @if(@$section->element->images)
                                @php $firstKey = collect($section->element->images)->keys()[0]; @endphp
                                <td>
                                    <img src="{{ getImage('assets/images/frontend/' . $key .'/'. @$data->data_values->$firstKey,@$section->element->images->$firstKey->size) }}" class="rounded shadow-xs border" width="45" height="30" style="object-fit: cover;">
                                </td>
                            @endif
                            @foreach($section->element as $k => $type)
                                @if($k !='modal')
                                    @if($type == 'text' || $type == 'icon')
                                        <td class="small fw-semibold">
                                            @if($type == 'icon')
                                                <i class="{{ @$data->data_values->$k }} fs-5 text-primary me-1"></i>
                                                <span class="text-muted tiny d-block d-md-inline">{{ \Illuminate\Support\Str::limit(strip_tags((string)(@$data->data_values->$k ?? '')), 15) }}</span>
                                            @else
                                                {{__(@$data->data_values->$k)}}
                                            @endif
                                        </td>
                                    @elseif($type == 'textarea')
                                        <td class="small text-muted tiny">{{ \Illuminate\Support\Str::limit(strip_tags((string)(@$data->data_values->$k ?? '')), 25) }}</td>
                                    @elseif($k == 'select')
                                        @php $dataVal = @$section->element->$k->name; $selRaw = @$data->data_values->$dataVal; @endphp
                                        <td>
                                            @if($key == 'social_icon' && $dataVal === 'show_on_public')
                                                @php $pub = ($selRaw === null || $selRaw === '' || (int) $selRaw === 1); @endphp
                                                <span class="badge {{ $pub ? 'bg-label-success' : 'bg-label-danger' }} tiny">
                                                    {{ $pub ? __('Public') : __('Private') }}
                                                </span>
                                            @else
                                                <span class="text-muted small">{{ $selRaw }}</span>
                                            @endif
                                        </td>
                                    @endif
                                @endif
                            @endforeach
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    @if($section->element->modal)
                                        @php
                                            $images = [];
                                            if(@$section->element->images){
                                                foreach($section->element->images as $imgKey => $imgs){
                                                    $imgName = @$data->data_values->$imgKey ?: '';
                                                    $images[] = getImage('assets/images/frontend/' . $key . '/' . $imgName, @$section->element->images->$imgKey->size);
                                                }
                                            }
                                            $dvJson = json_encode($data->data_values ?? new \stdClass());
                                            $dvB64 = base64_encode($dvJson);
                                            $imgB64 = !empty($images) ? base64_encode(json_encode($images)) : '';
                                        @endphp
                                        <button type="button" class="btn btn-sm btn-outline-primary updateBtn" data-id="{{$data->id}}" data-fe-dv="{{ $dvB64 }}" @if($imgB64) data-fe-images="{{ $imgB64 }}" @endif>
                                            <i class="la la-pencil"></i>
                                        </button>
                                    @else
                                        <a href="{{ route(getFrontendSectionRoute($key, 'element'), $data->id) }}" class="btn btn-sm btn-outline-primary"><i class="la la-pencil"></i></a>
                                    @endif
                                    <button class="btn btn-sm btn-outline-danger confirmationBtn" data-action="{{ route('admin.frontend.remove',$data->id) }}" data-question="@lang('Remove this element from public view?')">
                                        <i class="la la-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center py-5" colspan="100%">
                                <i class="las la-folder-open fs-1 text-muted opacity-25"></i>
                                <div class="text-muted small mt-2">@lang('No items found in this section matrix.')</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Tactical Visual Intelligence Sidebar -->
    <div class="col-xl-4 col-lg-5">
        <div class="sticky-top" style="top: 130px; z-index: 1 !important;">
            <div class="card border-0 shadow-sm mb-4 bg-dark text-white overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <i class="las la-eye fs-4 text-success me-2"></i>
                        <h6 class="mb-0 text-white ls-1">@lang('Tactical Preview')</h6>
                    </div>
                    <div class="preview-mockup rounded bg-lighter p-3 text-center border-dashed border-secondary mb-3">
                        @if(in_array($key, ['banner', 'middle_banner', 'bottom_banner']))
                            <div class="banner-mockup rounded bg-secondary position-relative overflow-hidden" style="height: 120px;">
                                @php $firstImg = @$section->content->images ? collect(@$section->content->images)->keys()->first() : null; @endphp
                                @if($firstImg)
                                    <img src="{{ getImage('assets/images/frontend/' . $key .'/'. @$content->data_values->$firstImg) }}" class="w-100 h-100 object-fit-cover opacity-50">
                                @endif
                                <div class="position-absolute top-50 start-50 translate-middle w-75">
                                    <div class="bg-white p-1 rounded-pill w-100 mb-1"></div>
                                    <div class="bg-white p-1 rounded-pill w-50 mx-auto"></div>
                                </div>
                            </div>
                        @elseif($key == 'service')
                            <div class="row g-2">
                                @for($i=0; $i<4; $i++)
                                <div class="col-6">
                                    <div class="bg-white rounded p-2 text-start border shadow-xs">
                                        <div class="avatar avatar-xs bg-label-primary mb-1"></div>
                                        <div class="bg-light p-1 rounded w-75"></div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        @else
                            <div class="text-muted py-5 px-3">
                                <i class="las la-image fs-1 opacity-25"></i>
                                <p class="small mb-0">@lang('Dynamic visualization for') <b>{{ $key }}</b></p>
                            </div>
                        @endif
                    </div>
                    <div class="small text-white-50">
                        <i class="las la-info-circle me-1"></i> @lang('Public rendering is optimized for all devices.')
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3">
                    <h6 class="mb-0">@lang('Intelligence Summary')</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush border-0">
                        <div class="list-group-item d-flex align-items-center justify-content-between py-3 border-0">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded bg-label-success"><i class="las la-check"></i></span>
                                </div>
                                <span class="small fw-semibold">@lang('Status')</span>
                            </div>
                            <span class="badge bg-label-success px-3">@lang('Operational')</span>
                        </div>
                        <div class="list-group-item d-flex align-items-center justify-content-between py-3 border-0">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded bg-label-primary"><i class="las la-clock"></i></span>
                                </div>
                                <span class="small fw-semibold">@lang('Last Updated')</span>
                            </div>
                            <span class="text-muted tiny">{{ now()->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-3 border-top-0 rounded-bottom">
                    <button class="btn btn-outline-secondary btn-sm w-100" type="button" onclick="window.location.reload()">
                        <i class="las la-sync-alt me-1"></i> @lang('Refresh Matrix')
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Standard Modals --}}
<div id="addModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title h6">@lang('Add New Matrix Item')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route(getFrontendSectionRoute($key, 'content'), $key) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="element">
                <div class="modal-body">
                    <div class="row g-3">
                        @foreach($section->element ?? [] as $k => $type)
                            @if($k != 'modal')
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">{{__(keyToTitle($k))}}</label>
                                    @if($type == 'icon')
                                        <div class="input-group input-group-merge input-group-sm">
                                            <input type="text" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" required>
                                            <span class="input-group-text" data-icon="las la-home" role="iconpicker"></span>
                                        </div>
                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                            <div class="border rounded p-2 mb-2 bg-light-soft">
                                                <input type="file" class="form-control form-control-sm" name="image_input[{{ $imgKey }}]" accept=".png,.jpg,.jpeg,.webp">
                                                <small class="tiny text-muted mt-1 d-block">{{ @$image->size }}px @lang('suggested')</small>
                                            </div>
                                        @endforeach
                                    @elseif($type == 'textarea' || $type == 'textarea-nic')
                                        <textarea rows="3" class="form-control form-control-sm {{ $type == 'textarea-nic' ? 'nicEdit' : '' }}" name="{{ $k }}"></textarea>
                                    @elseif($k == 'select')
                                        <select class="form-select form-select-sm" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control form-control-sm" name="{{ $k }}" required/>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">@lang('Initialize')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="updateBtn" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title h6">@lang('Update Element Data')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.frontend.sections.content', $key) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="element">
                <input type="hidden" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        @foreach($section->element ?? [] as $k => $type)
                            @if($k != 'modal')
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-muted">{{__(keyToTitle($k))}}</label>
                                    @if($type == 'icon')
                                        <div class="input-group input-group-merge input-group-sm">
                                            <input type="text" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" required>
                                            <span class="input-group-text" data-icon="las la-home" role="iconpicker"></span>
                                        </div>
                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                            <div class="border rounded p-2 mb-2 bg-light-soft d-flex align-items-center gap-2">
                                                <div class="imageModalUpdate{{ $loop->index }} rounded border" style="width: 50px; height: 40px; background-size: cover; background-position: center;"></div>
                                                <input type="file" class="form-control form-control-sm" name="image_input[{{ $imgKey }}]" accept=".png,.jpg,.jpeg,.webp">
                                            </div>
                                        @endforeach
                                    @elseif($type == 'textarea' || $type == 'textarea-nic')
                                        <textarea rows="4" class="form-control form-control-sm {{ $type == 'textarea-nic' ? 'nicEdit' : '' }}" name="{{ $k }}"></textarea>
                                    @elseif($k == 'select')
                                        <select class="form-select form-select-sm" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control form-control-sm" name="{{ $k }}" required/>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">@lang('Update Matrix')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<x-confirmation-modal />

@endsection

@push('style')
<style>
    .ls-1 { letter-spacing: 0.5px; }
    .tiny { font-size: 0.72rem; }
    .bg-light-soft { background-color: rgba(var(--bs-light-rgb), 0.5); }
    .bg-lighter { background-color: #f8f9fa; }
    .preview-mockup { min-height: 150px; display: flex; align-items: center; justify-content: center; }
    .shadow-xs { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
    .element-row:hover { background-color: rgba(var(--bs-primary-rgb), 0.02) !important; }
    .w-px-200 { width: 200px !important; }
</style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script')
<script>
    (function ($) {
        "use strict";

        // Image Preview Sync
        $('.image-upload-input').on('change', function() {
            const input = this;
            const preview = $(this).closest('.fe-image-upload-wrapper').find('.preview-img-target');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });

        // Search Filter
        $('#elementSearch').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('.element-row').each(function() {
                const text = $(this).attr('data-search');
                $(this).toggle(text.includes(query));
            });
        });

        // Modal Helpers
        function adminFeShowModal($modal) {
            bootstrap.Modal.getOrCreateInstance($modal[0]).show();
        }

        function feDecodeB64(b64) {
            try { return JSON.parse(new TextDecoder().decode(Uint8Array.from(atob(b64), c => c.charCodeAt(0)))); } catch(e) { return null; }
        }

        $('.addBtn').on('click', function () {
            $('#addModal').find('form')[0].reset();
            adminFeShowModal($('#addModal'));
        });

        $(document).on('click', '.updateBtn', function () {
            const $btn = $(this);
            const modal = $('#updateBtn');
            modal.find('input[name=id]').val($btn.data('id'));
            
            const obj = feDecodeB64($btn.attr('data-fe-dv'));
            if (obj) {
                $.each(obj, function (index, value) {
                    var $el = $(this);
                    if ($el.data('iconpicker')) {
                        try { $el.iconpicker('destroy'); } catch (err) {}
                    }
                });
                $root.find('.iconPicker').iconpicker();
                $root.find('.iconPicker').off('iconpickerSelected.stayl').on('iconpickerSelected.stayl', function (e) {
                    var v = e.iconpickerValue || '';
                    var $inp = $(this).filter('input').length ? $(this) : $(this).closest('.input-group, .form-group').find('input.iconPicker, input.iconpicker-input').first();
                    if ($inp.length) {
                        $inp.val(v);
                    }
                });
            }
            initFrontendIconPickers($(document));
            $('#addModal, #updateBtn').on('shown.bs.modal', function () {
                var $m = $(this);
                initFrontendIconPickers($m);
                if (this.id === 'updateBtn') {
                    var payload = $m.data('feEditDv');
                    if (payload) {
                        applyFeEditPayload($m, payload);
                        feSyncIconPickerAddons($m);
                    }
                }
            });
            @if(isset($key) && $key == 'social_icon')
            (function () {
                var socToggleUrl = @json(route('admin.frontend.sections.social_icon.toggle_public'));
                var socLblPublic = @json(__('Public'));
                var socLblPrivate = @json(__('Private'));
                var socErr = @json(__('Could not save. Try again.'));
                $(document).on('change', '.social-icon-pub-toggle', function () {
                    var $cb = $(this);
                    var id = $cb.data('id');
                    var on = $cb.is(':checked') ? 1 : 0;
                    var $cell = $cb.closest('.social-icon-pub-cell');
                    var $label = $cell.find('label');
                    $cb.prop('disabled', true);
                    $.ajax({
                        url: socToggleUrl,
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: id,
                            show_on_public: on
                        },
                        success: function (res) {
                            if (res && res.success) {
                                $label.text(on ? socLblPublic : socLblPrivate);
                                if (typeof notify === 'function' && res.message) {
                                    notify('success', res.message);
                                }
                            } else {
                                $cb.prop('checked', !on);
                                if (typeof notify === 'function') {
                                    notify('error', (res && res.message) ? res.message : socErr);
                                }
                            }
                        },
                        error: function () {
                            $cb.prop('checked', !on);
                            if (typeof notify === 'function') {
                                notify('error', socErr);
                            }
                        },
                        complete: function () {
                            $cb.prop('disabled', false);
                        }
                    });
                });
            })();
            @endif
        })(jQuery);
    </script>

@endpush
