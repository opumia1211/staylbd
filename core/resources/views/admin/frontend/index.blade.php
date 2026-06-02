@extends('admin.layouts.app')
@section('panel')
<div class="frontend-section-wrapper animate__animated animate__fadeIn">
    {{-- 1. Top Intelligence Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="avatar avatar-md me-3">
                    <span class="avatar-initial rounded bg-label-primary shadow-sm"><i class="las la-broadcast-tower fs-3"></i></span>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">{{ __(keyToTitle($key)) }} @lang('Intelligence Hub')</h5>
                    <p class="text-muted small mb-0">@lang('Manage public visibility, dynamic content nodes, and visual parameters.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex gap-2 align-items-center">
                @if($key == 'contact_us')
                    <a href="{{ route('admin.ticket.index') }}" class="btn btn-label-success btn-sm px-3 rounded-pill">
                        <i class="las la-inbox me-1"></i> @lang('Go to Inbox')
                    </a>
                @endif
                <div class="badge bg-label-info rounded-pill px-3 py-2 shadow-sm border border-info border-opacity-10">
                    <i class="las la-database me-1"></i> {{ count($elements ?? []) }} @lang('Active Elements')
                </div>
            </div>
        </div>
    </div>

    @if($key == 'contact_us' || $key == 'policy_pages')
        <div class="alert alert-custom-primary border-0 shadow-sm mb-4 d-flex align-items-center p-3 rounded-4 overflow-hidden position-relative">
            <div class="alert-icon-box me-3">
                <i class="las la-info-circle fs-3 text-primary"></i>
            </div>
            <div class="position-relative z-index-1">
                <h6 class="mb-1 fw-bold text-primary small">@lang('Strategic Context')</h6>
                <p class="mb-0 small text-dark opacity-75">
                    @if($key == 'contact_us')
                        @lang('Channel settings (WhatsApp, Telegram, Email) – these nodes govern global support widgets across the platform.')
                    @else
                        @lang('Policy Pages logic — Privacy, Terms, Shipping. Access via') <code class="small text-primary fw-bold">/policy/{id}</code>
                    @endif
                </p>
            </div>
            <div class="alert-abstract-shape"></div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Primary Management Area --}}
        <div class="col-xl-8 col-lg-7">
            
            {{-- A. Configuration Matrix (Single content) --}}
            @if(@$section->content)
            <div class="card border-0 shadow-sm mb-4 overflow-hidden border-start border-4 border-primary">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-primary p-2 me-2 rounded-3">
                            <i class="las la-sliders-h fs-4"></i>
                        </div>
                        <h6 class="mb-0 fw-bold">@lang('Configuration Matrix')</h6>
                    </div>
                    <span class="badge bg-label-secondary small rounded-pill">@lang('Static Properties')</span>
                </div>
                <div class="card-body pt-4">
                    <form action="{{ route(getFrontendSectionRoute($key, 'content'), $key) }}" method="POST" enctype="multipart/form-data" class="modern-config-form">
                        @csrf
                        <input type="hidden" name="type" value="content">
                        <div class="row g-4">
                            @foreach($section->content as $k => $item)
                                @if($k == 'images')
                                    @foreach($item as $imgKey => $image)
                                        @if($key === 'contact_us' && in_array($imgKey, ['contact_phone_icon', 'contact_email_icon'], true))
                                            @continue
                                        @endif
                                        @php
                                            $isContactIcon = ($key === 'contact_us' && in_array($imgKey, ['contact_phone_icon', 'contact_email_icon'], true));
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="image-node-card p-3 rounded border bg-light-soft h-100">
                                                <label class="form-label fw-bold text-dark mb-3">{{__(keyToTitle(@$imgKey))}}</label>
                                                <div class="fe-image-upload-wrapper text-center">
                                                    <input type="hidden" name="has_image" value="1">
                                                    <div class="preview-container mb-3 mx-auto shadow-sm rounded-4 border overflow-hidden position-relative bg-white" style="width: 100%; max-width: 200px; aspect-ratio: 16/9;">
                                                        <img src="{{getImage('assets/images/frontend/' . $key .'/'. @$content->data_values->$imgKey,@$section->content->images->$imgKey->size) }}" class="w-100 h-100 object-fit-contain preview-img-target">
                                                        <div class="preview-overlay">
                                                            <i class="las la-search-plus fs-1 text-white"></i>
                                                        </div>
                                                    </div>
                                                    <div class="upload-controls">
                                                        <input type="file" class="form-control d-none image-upload-input" name="image_input[{{ @$imgKey }}]" id="fe_c_img_{{ $key }}_{{ $loop->index }}" accept="{{ $isContactIcon ? '.png,.jpg,.jpeg,.webp,.svg' : '.png,.jpg,.jpeg,.webp' }}">
                                                        <label for="fe_c_img_{{ $key }}_{{ $loop->index }}" class="btn btn-primary btn-sm px-4 rounded-pill shadow-sm">
                                                            <i class="las la-upload me-1"></i> {{ $isContactIcon ? __('Upload Icon') : __('Update Identity') }}
                                                        </label>
                                                        @if($isContactIcon)
                                                            <div class="form-check d-inline-flex align-items-center mt-2">
                                                                <input class="form-check-input me-2" type="checkbox" name="remove_image[{{ $imgKey }}]" value="1" id="remove_{{ $imgKey }}">
                                                                <label class="form-check-label tiny text-muted" for="remove_{{ $imgKey }}">@lang('Remove current icon')</label>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mt-2 d-flex justify-content-center gap-2">
                                                        @if(@$section->content->images->$imgKey->size) 
                                                            <span class="badge bg-label-secondary tiny rounded-pill">{{@$section->content->images->$imgKey->size}}px</span>
                                                        @endif
                                                        <span class="badge bg-label-secondary tiny rounded-pill">{{ $isContactIcon ? 'SVG/WebP/PNG/JPG' : 'WebP/PNG/JPG' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @if($k != 'images')
                                        @if($key === 'contact_us' && in_array($k, ['contact_number', 'contact_email'], true))
                                            @continue
                                        @endif
                                        <div class="{{ $item == 'textarea' || $item == 'textarea-nic' ? 'col-12' : 'col-md-6' }}">
                                            <div class="form-group mb-0">
                                                <label class="form-label fw-bold text-dark mb-2">{{__(keyToTitle($k))}}</label>
                                                @if($item == 'icon')
                                                    <div class="input-group input-group-merge shadow-none border rounded-3 overflow-hidden">
                                                        <span class="input-group-text border-0 bg-light"><i class="{{ @$content->data_values->$k }} fs-5"></i></span>
                                                        <input type="text" class="form-control border-0 iconPicker icon ps-1" autocomplete="off" name="{{ $k }}" value="{{ @$content->data_values->$k }}" required>
                                                        <span class="input-group-text border-0 bg-white" data-icon="las la-home" role="iconpicker" style="cursor: pointer;">@lang('Change')</span>
                                                    </div>
                                                @elseif($item == 'textarea')
                                                    <textarea rows="4" class="form-control rounded-3 border" name="{{$k}}" required placeholder="@lang('Enter ' . keyToTitle($k))">{{ @$content->data_values->$k}}</textarea>
                                                @elseif($item == 'textarea-nic')
                                                    <div class="nic-editor-wrapper">
                                                        <textarea rows="8" class="form-control nicEdit" name="{{$k}}">{{ @$content->data_values->$k}}</textarea>
                                                    </div>
                                                @elseif($k == 'select')
                                                    <select class="form-select rounded-3 border" name="{{ @$item->name }}">
                                                        @foreach($item->options as $selectItemKey => $selectOption)
                                                            <option value="{{ $selectItemKey }}" @if(@$content->data_values->{$item->name} == $selectItemKey) selected @endif>{{ $selectOption }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="text" class="form-control rounded-3 border" name="{{$k}}" value="{{@$content->data_values->$k }}" required placeholder="@lang('Enter ' . keyToTitle($k))"/>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach

                            @if($key === 'contact_us')
                                @php
                                    $showTopPhone = (int) (@$content->data_values->show_top_phone ?? 1) === 1;
                                    $showTopEmail = (int) (@$content->data_values->show_top_email ?? 1) === 1;
                                    $contactPhone = trim((string) (@$content->data_values->contact_number ?? ''));
                                    $contactEmail = trim((string) (@$content->data_values->contact_email ?? ''));
                                    $contactPhoneIcon = trim((string) (@$content->data_values->contact_phone_icon ?? ''));
                                    $contactEmailIcon = trim((string) (@$content->data_values->contact_email_icon ?? ''));
                                    $phoneIconRel = ($contactPhoneIcon !== '' && preg_match('#^[a-zA-Z0-9._-]+$#', $contactPhoneIcon)) ? 'assets/images/frontend/contact_us/' . $contactPhoneIcon : '';
                                    $emailIconRel = ($contactEmailIcon !== '' && preg_match('#^[a-zA-Z0-9._-]+$#', $contactEmailIcon)) ? 'assets/images/frontend/contact_us/' . $contactEmailIcon : '';
                                    $phoneIconAbs = $phoneIconRel !== '' ? public_path($phoneIconRel) : '';
                                    $emailIconAbs = $emailIconRel !== '' ? public_path($emailIconRel) : '';
                                    $hasPhoneIcon = $phoneIconAbs !== '' && is_file($phoneIconAbs);
                                    $hasEmailIcon = $emailIconAbs !== '' && is_file($emailIconAbs);
                                    $phoneIconUrl = $hasPhoneIcon ? getImage($phoneIconRel, '96x96') : '';
                                    $emailIconUrl = $hasEmailIcon ? getImage($emailIconRel, '96x96') : '';
                                @endphp
                                <div class="col-12">
                                    <div class="border rounded-3 p-3 bg-light-soft">
                                        <h6 class="mb-2 fw-bold">@lang('Top Header Contact Quick Control')</h6>
                                        <p class="mb-3 tiny text-muted">@lang('One-line edit: icon upload, contact value, remove, and public/private visibility.')</p>

                                        <div class="row g-2">
                                            <div class="col-12">
                                                <div class="d-flex flex-wrap align-items-center gap-2 p-1 border rounded-2 bg-white contact-quick-line">
                                                    <div class="text-center contact-quick-line__label">
                                                        <div class="mx-auto rounded-circle border bg-light d-flex align-items-center justify-content-center overflow-hidden contact-quick-line__preview">
                                                            @if($hasPhoneIcon)
                                                                <img src="{{ $phoneIconUrl }}" alt="" class="w-100 h-100 object-fit-contain">
                                                            @else
                                                                <i class="las la-phone fs-5 text-muted"></i>
                                                            @endif
                                                        </div>
                                                        <div class="tiny text-muted mt-1">@lang('Phone')</div>
                                                    </div>
                                                    <div>
                                                        <input type="file" class="form-control form-control-sm contact-quick-line__file" name="image_input[contact_phone_icon]" accept=".png,.jpg,.jpeg,.webp,.svg">
                                                    </div>
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input" type="checkbox" name="remove_image[contact_phone_icon]" value="1" id="remove_contact_phone_icon">
                                                        <label class="form-check-label tiny" for="remove_contact_phone_icon">@lang('Remove')</label>
                                                    </div>
                                                    <div class="flex-grow-1 contact-quick-line__value">
                                                        <input type="text" class="form-control form-control-sm" name="contact_number" value="{{ $contactPhone }}" placeholder="@lang('Contact Number')">
                                                    </div>
                                                    <div class="form-check form-switch mb-0">
                                                        <input type="hidden" name="show_top_phone" value="0">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="show_top_phone" name="show_top_phone" value="1" {{ $showTopPhone ? 'checked' : '' }}>
                                                        <label class="form-check-label small fw-semibold" for="show_top_phone">@lang('Public')</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="d-flex flex-wrap align-items-center gap-2 p-1 border rounded-2 bg-white contact-quick-line">
                                                    <div class="text-center contact-quick-line__label">
                                                        <div class="mx-auto rounded-circle border bg-light d-flex align-items-center justify-content-center overflow-hidden contact-quick-line__preview">
                                                            @if($hasEmailIcon)
                                                                <img src="{{ $emailIconUrl }}" alt="" class="w-100 h-100 object-fit-contain">
                                                            @else
                                                                <i class="las la-envelope fs-5 text-muted"></i>
                                                            @endif
                                                        </div>
                                                        <div class="tiny text-muted mt-1">@lang('Email')</div>
                                                    </div>
                                                    <div>
                                                        <input type="file" class="form-control form-control-sm contact-quick-line__file" name="image_input[contact_email_icon]" accept=".png,.jpg,.jpeg,.webp,.svg">
                                                    </div>
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input" type="checkbox" name="remove_image[contact_email_icon]" value="1" id="remove_contact_email_icon">
                                                        <label class="form-check-label tiny" for="remove_contact_email_icon">@lang('Remove')</label>
                                                    </div>
                                                    <div class="flex-grow-1 contact-quick-line__value">
                                                        <input type="email" class="form-control form-control-sm" name="contact_email" value="{{ $contactEmail }}" placeholder="@lang('Contact Email')">
                                                    </div>
                                                    <div class="form-check form-switch mb-0">
                                                        <input type="hidden" name="show_top_email" value="0">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="show_top_email" name="show_top_email" value="1" {{ $showTopEmail ? 'checked' : '' }}>
                                                        <label class="form-check-label small fw-semibold" for="show_top_email">@lang('Public')</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <small class="tiny text-muted d-block">@lang('Supported: SVG, WebP, PNG, JPG | Recommended: 96x96 px | Max: 2MB')</small>
                                                <small class="tiny text-muted d-block">@lang('Email icon is shared: one upload will show in both Header Top Bar and Footer email icon.')</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="{{ $key === 'contact_us' ? 'mt-3 text-end border-top pt-3 contact-save-bar sticky-bottom bg-white rounded-3 px-2 pb-2' : 'mt-5 text-end border-top pt-4' }}">
                            <button type="submit" class="btn btn-primary px-5 shadow-md rounded-pill">
                                <i class="las la-save me-1"></i> @lang('Deploy Matrix')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- B. Dynamic Element Hub (Table elements) --}}
            @if(@$section->element)
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-success p-2 me-2 rounded-3">
                            <i class="las la-layer-group fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">@lang('Dynamic Node Architecture')</h6>
                            <small class="text-muted">@lang('Manage multiple items for this section')</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 w-auto">
                        <div class="input-group input-group-merge search-box shadow-none border rounded-pill px-3 py-1">
                            <span class="input-group-text border-0 bg-transparent p-0 me-2"><i class="las la-search text-muted"></i></span>
                            <input type="text" id="elementSearch" class="form-control border-0 bg-transparent p-0 small" style="width: 150px;" placeholder="@lang('Filter nodes...')">
                        </div>
                        @if($key === 'social_icon')
                            <button type="button" class="btn btn-primary btn-sm shadow-sm px-3 rounded-pill socialQuickAdd">
                                <i class="las la-plus me-1"></i> @lang('Add New')
                            </button>
                        @elseif($section->element->modal)
                            <button type="button" class="btn btn-primary btn-sm addBtn shadow-sm px-3 rounded-pill"><i class="las la-plus me-1"></i> @lang('Add New')</button>
                        @else
                            <a href="{{ route(getFrontendSectionRoute($key, 'element')) }}" class="btn btn-primary btn-sm shadow-sm px-3 rounded-pill"><i class="las la-plus me-1"></i> @lang('Add New')</a>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light-premium">
                            <tr>
                                <th class="ps-4 py-3 tiny fw-bold text-muted text-uppercase ls-1">@lang('ID')</th>
                                @if(@$section->element->images)
                                    <th class="py-3 tiny fw-bold text-muted text-uppercase ls-1">@lang('VISUAL')</th>
                                @endif
                                @foreach($section->element as $k => $type)
                                    @if($k !='modal')
                                        @if($type=='text' || $type=='icon' || $type == 'textarea' || $k == 'select')
                                            <th class="py-3 tiny fw-bold text-muted text-uppercase ls-1">{{ __(keyToTitle($k == 'select' ? @$section->element->$k->name : $k)) }}</th>
                                        @endif
                                    @endif
                                @endforeach
                                <th class="text-end pe-4 py-3 tiny fw-bold text-muted text-uppercase ls-1">@lang('ACTION')</th>
                            </tr>
                        </thead>
                        <tbody id="elementTableBody">
                            @forelse($elements as $data)
                            <tr class="element-row transition-all" data-search="{{ strtolower(json_encode($data->data_values)) }}">
                                <td class="ps-4">
                                    <span class="badge bg-label-secondary small rounded-pill">#{{$loop->iteration}}</span>
                                </td>
                                @if(@$section->element->images)
                                    @php $firstKey = collect($section->element->images)->keys()[0]; @endphp
                                    <td>
                                        <div class="avatar avatar-sm rounded-3 border overflow-hidden bg-white shadow-sm">
                                            @if($key === 'social_icon' && !empty(@$data->data_values->$firstKey))
                                                <img src="{{ getImage('assets/images/frontend/' . $key .'/'. @$data->data_values->$firstKey,@$section->element->images->$firstKey->size) }}" class="w-100 h-100 object-fit-contain p-1" alt="@lang('Social icon image')">
                                            @else
                                                <img src="{{ getImage('assets/images/frontend/' . $key .'/'. @$data->data_values->$firstKey,@$section->element->images->$firstKey->size) }}" class="w-100 h-100 object-fit-cover" alt="@lang('Visual')">
                                            @endif
                                        </div>
                                    </td>
                                @endif
                                @foreach($section->element as $k => $type)
                                    @if($k !='modal')
                                        @if($type == 'text' || $type == 'icon')
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($type == 'icon')
                                                        <div class="badge bg-label-primary p-2 me-2 rounded-circle shadow-premium">
                                                            <i class="{{ @$data->data_values->$k }} fs-5"></i>
                                                        </div>
                                                        <span class="fw-semibold text-dark small">{{ \Illuminate\Support\Str::limit(strip_tags((string)(@$data->data_values->$k ?? '')), 15) }}</span>
                                                    @else
                                                        <span class="fw-semibold text-dark small">{{__(@$data->data_values->$k)}}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        @elseif($type == 'textarea')
                                            <td>
                                                <p class="mb-0 text-muted tiny lh-sm" style="max-width: 200px;">{{ \Illuminate\Support\Str::limit(strip_tags((string)(@$data->data_values->$k ?? '')), 50) }}</p>
                                            </td>
                                        @elseif($k == 'select')
                                            @php $dataVal = @$section->element->$k->name; $selRaw = @$data->data_values->$dataVal; @endphp
                                            <td>
                                                @if($key == 'social_icon' && $dataVal === 'show_on_public')
                                                    @php $pub = ($selRaw === null || $selRaw === '' || (int) $selRaw === 1); @endphp
                                                    <span class="badge {{ $pub ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill px-3 tiny js-public-badge" data-public="{{ $pub ? 1 : 0 }}">
                                                        <i class="las la-{{ $pub ? 'eye' : 'eye-slash' }} me-1"></i> {{ $pub ? __('Public') : __('Private') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary px-3 rounded-pill tiny text-capitalize">{{ $selRaw }}</span>
                                                @endif
                                            </td>
                                        @endif
                                    @endif
                                @endforeach
                                <td class="text-end pe-4">
                                    @if($key === 'social_icon')
                                        @php
                                            $dv = (array) ($data->data_values ?? []);
                                            $pubState = (!isset($dv['show_on_public']) || (int) $dv['show_on_public'] === 1) ? 1 : 0;
                                        @endphp
                                        <div class="d-inline-flex align-items-center gap-1 social-action-wrap" style="font-family: Inter, sans-serif;">
                                            <button type="button"
                                                class="btn btn-white border socialQuickEditBtn"
                                                data-id="{{ $data->id }}"
                                                data-title="{{ e((string) ($dv['title'] ?? '')) }}"
                                                data-icon="{{ e((string) ($dv['icon'] ?? '')) }}"
                                                data-url="{{ e((string) ($dv['url'] ?? '')) }}"
                                                data-custom-icon-svg="{{ e((string) ($dv['custom_icon_svg'] ?? '')) }}"
                                                data-show-on-public="{{ $pubState }}"
                                                title="@lang('Edit details')">
                                                <i class="las la-pen text-primary"></i>
                                            </button>

                                            <form action="{{ route(getFrontendSectionRoute($key, 'content'), $key) }}" method="POST" enctype="multipart/form-data" class="d-inline-flex mb-0 socialUploadForm">
                                                @csrf
                                                <input type="hidden" name="type" value="element">
                                                <input type="hidden" name="id" value="{{ $data->id }}">
                                                <input type="file" name="image_input[custom_icon]" class="d-none socialUploadInput" accept=".jpeg,.jpg,.png,.webp,.svg">
                                                <button type="button" class="btn btn-white border socialUploadTrigger" title="@lang('Upload/Change icon image')">
                                                    <i class="las la-image text-success"></i>
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-white border socialPublicToggleBtn" data-id="{{ $data->id }}" data-public="{{ $pubState }}" title="@lang('Toggle public/private')">
                                                <i class="las la-{{ $pubState ? 'toggle-on text-success' : 'toggle-off text-danger' }}"></i>
                                            </button>

                                            <button class="btn btn-white border confirmationBtn" data-action="{{ route('admin.frontend.remove',$data->id) }}" data-question="@lang('Permanently remove this node from the architecture?')" title="@lang('Remove Node')">
                                                <i class="las la-trash-alt text-danger"></i>
                                            </button>
                                        </div>
                                    @else
                                    <div class="btn-group btn-group-sm shadow-sm rounded-pill overflow-hidden border">
                                        @if($section->element->modal && $key !== 'social_icon')
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
                                            <button type="button" class="btn btn-white updateBtn border-0" data-id="{{$data->id}}" data-fe-dv="{{ $dvB64 }}" @if($imgB64) data-fe-images="{{ $imgB64 }}" @endif title="@lang('Edit Node')">
                                                <i class="las la-edit text-primary fs-5"></i>
                                            </button>
                                        @else
                                            <a href="{{ route(getFrontendSectionRoute($key, 'element'), $data->id) }}" class="btn btn-white border-0 {{ $key === 'social_icon' ? 'px-3' : '' }}" title="@lang('Edit Node')">
                                                @if($key === 'social_icon')
                                                    <i class="las la-edit text-primary me-1"></i><span class="small fw-semibold text-primary">@lang('Edit & Upload')</span>
                                                @else
                                                    <i class="las la-edit text-primary fs-5"></i>
                                                @endif
                                            </a>
                                        @endif
                                        <button class="btn btn-white confirmationBtn border-0 border-start" data-action="{{ route('admin.frontend.remove',$data->id) }}" data-question="@lang('Permanently remove this node from the architecture?')" title="@lang('Remove Node')">
                                            <i class="las la-trash-alt text-danger fs-5"></i>
                                        </button>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center py-5" colspan="100%">
                                    <div class="empty-state">
                                        <div class="avatar avatar-lg bg-label-secondary mx-auto mb-3">
                                            <span class="avatar-initial rounded-circle"><i class="las la-folder-open fs-1"></i></span>
                                        </div>
                                        <h6 class="text-muted fw-bold">@lang('Architecture is Empty')</h6>
                                        <p class="small text-muted opacity-50 mb-3">@lang('Initialize this section by adding dynamic elements or configuring the matrix.')</p>
                                        @if($section->element->modal)
                                            <button type="button" class="btn btn-primary btn-sm addBtn rounded-pill px-4">@lang('Add First Element')</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($elements, 'hasPages') && $elements->hasPages())
                <div class="card-footer bg-white py-3">
                    {{ paginateLinks($elements) }}
                </div>
                @endif
            </div>
            @endif
        </div>

        {{-- Intelligence Sidebar --}}
        <div class="col-xl-4 col-lg-5">
            <div class="sticky-top" style="top: 100px; z-index: 10;">
                
                {{-- A. Tactical Preview --}}
                <div class="card border-0 shadow-sm mb-4 bg-dark text-white overflow-hidden rounded-4">
                    <div class="card-body p-4 position-relative">
                        <div class="d-flex align-items-center mb-4">
                            <div class="badge bg-label-success p-2 me-3 rounded-circle">
                                <i class="las la-eye fs-4 text-white"></i>
                            </div>
                            <h6 class="mb-0 text-white fw-bold tracking-wider">@lang('TACTICAL RENDER')</h6>
                        </div>
                        
                        <div class="preview-mockup-area rounded-4 bg-white bg-opacity-10 p-3 text-center border-dashed border-secondary mb-4 position-relative overflow-hidden">
                            @if(in_array($key, ['banner', 'middle_banner', 'bottom_banner']))
                                <div class="banner-preview-skeleton position-relative rounded-3 overflow-hidden" style="height: 140px; background: #2b2c40;">
                                    @php $firstImg = @$section->content->images ? collect(@$section->content->images)->keys()->first() : null; @endphp
                                    @if($firstImg)
                                        <img src="{{ getImage('assets/images/frontend/' . $key .'/'. @$content->data_values->$firstImg) }}" class="w-100 h-100 object-fit-cover opacity-75">
                                    @endif
                                    <div class="position-absolute top-50 start-50 translate-middle w-75 text-center">
                                        <div class="bg-white bg-opacity-20 p-2 rounded-pill w-100 mb-2 animate-pulse"></div>
                                        <div class="bg-white bg-opacity-20 p-1 rounded-pill w-50 mx-auto animate-pulse"></div>
                                    </div>
                                </div>
                            @elseif($key == 'service' || $key == 'feature')
                                <div class="row g-2">
                                    @for($i=0; $i<4; $i++)
                                    <div class="col-6">
                                        <div class="bg-white bg-opacity-10 rounded-3 p-2 text-start border border-secondary border-opacity-20">
                                            <div class="avatar avatar-xs bg-label-primary mb-2 rounded-circle"></div>
                                            <div class="bg-white bg-opacity-20 p-1 rounded-pill w-75"></div>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            @else
                                <div class="py-5 px-3">
                                    <i class="las la-cube fs-1 text-white opacity-25"></i>
                                    <p class="small text-white-50 mt-2 mb-0">@lang('Architecture visualization for')<br><span class="text-white fw-bold">{{ keyToTitle($key) }}</span></p>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top border-secondary border-opacity-20">
                            <div class="small text-white-50">
                                <i class="las la-info-circle me-1"></i> @lang('Public display optimized')
                            </div>
                            <a href="{{ url('/') }}" target="_blank" class="btn btn-sm btn-link text-white p-0 text-decoration-none small">@lang('Live View') <i class="las la-external-link-alt ms-1"></i></a>
                        </div>
                        <div class="sidebar-abstract-shape"></div>
                    </div>
                </div>

                {{-- B. Intelligence Summary --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="las la-chart-pie me-2 text-primary"></i>@lang('Intelligence Summary')</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush border-0">
                            <div class="list-group-item d-flex align-items-center justify-content-between py-3 px-4 bg-transparent">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-3">
                                        <span class="avatar-initial rounded-circle bg-label-success"><i class="las la-check"></i></span>
                                    </div>
                                    <span class="small fw-semibold text-dark">@lang('System Status')</span>
                                </div>
                                <span class="badge bg-label-success px-3 rounded-pill">@lang('Operational')</span>
                            </div>
                            <div class="list-group-item d-flex align-items-center justify-content-between py-3 px-4 bg-transparent">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-3">
                                        <span class="avatar-initial rounded-circle bg-label-primary"><i class="las la-clock"></i></span>
                                    </div>
                                    <span class="small fw-semibold text-dark">@lang('Last Deploy')</span>
                                </div>
                                <span class="text-muted tiny fw-bold">{{ now()->format('d M, h:i A') }}</span>
                            </div>
                            <div class="list-group-item d-flex align-items-center justify-content-between py-3 px-4 bg-transparent border-0">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-3">
                                        <span class="avatar-initial rounded-circle bg-label-info"><i class="las la-code-branch"></i></span>
                                    </div>
                                    <span class="small fw-semibold text-dark">@lang('Architecture')</span>
                                </div>
                                <span class="badge bg-label-info px-3 rounded-pill">v2.0 Beta</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light py-3 border-0">
                        <button class="btn btn-outline-primary btn-sm w-100 rounded-pill" type="button" onclick="window.location.reload()">
                            <i class="las la-sync-alt me-1"></i> @lang('Refresh Real-time Matrix')
                        </button>
                    </div>
                </div>

                {{-- C. Strategic Insight --}}
                <div class="card border-0 bg-label-primary p-4 rounded-4 shadow-sm position-relative overflow-hidden">
                    <div class="d-flex align-items-start position-relative z-index-1">
                        <div class="avatar avatar-sm bg-primary rounded-circle me-3 shadow-sm">
                            <span class="avatar-initial text-white"><i class="las la-lightbulb"></i></span>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1 text-primary">@lang('Tactical Tip')</h6>
                            <p class="mb-0 small text-dark opacity-75">@lang('Use high-resolution WebP images to maintain visual fidelity while optimizing for mobile network performance.')</p>
                        </div>
                    </div>
                    <div class="insight-abstract-shape"></div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modernized Modals --}}
@if($key !== 'social_icon')
<div id="addModal" class="modal fade animate__animated animate__fadeIn" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-2xl rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="badge bg-label-primary p-2 me-3 rounded-circle">
                        <i class="las la-plus fs-4"></i>
                    </div>
                    <h5 class="modal-title h6 fw-bold mb-0">@lang('Initialize New Node')</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route(getFrontendSectionRoute($key, 'content'), $key) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="element">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        @foreach($section->element ?? [] as $k => $type)
                            @if($k != 'modal')
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark mb-2">{{__(keyToTitle($k))}}</label>
                                    @if($type == 'icon')
                                        <div class="input-group input-group-merge shadow-none border rounded-3 overflow-hidden">
                                            <span class="input-group-text border-0 bg-light"><i class="las la-home fs-5"></i></span>
                                            <input type="text" class="form-control border-0 iconPicker icon ps-1" autocomplete="off" name="{{ $k }}" required>
                                            <span class="input-group-text border-0 bg-white" data-icon="las la-home" role="iconpicker">@lang('Pick')</span>
                                        </div>
                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                            <div class="image-upload-node p-3 border rounded-3 bg-light-soft mb-2">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="tiny fw-bold text-muted">{{__(keyToTitle($imgKey))}}</span>
                                                    <span class="badge bg-label-secondary tiny">{{ @$image->size }}px</span>
                                                </div>
                                                <input type="file" class="form-control form-control-sm border-0 shadow-sm" name="image_input[{{ $imgKey }}]" accept=".png,.jpg,.jpeg,.webp">
                                            </div>
                                        @endforeach
                                    @elseif($type == 'textarea' || $type == 'textarea-nic')
                                        <textarea rows="3" class="form-control border rounded-3 {{ $type == 'textarea-nic' ? 'nicEdit' : '' }}" name="{{ $k }}" placeholder="@lang('Enter ' . keyToTitle($k))"></textarea>
                                    @elseif($k == 'select')
                                        <select class="form-select border rounded-3" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control border rounded-3" name="{{ $k }}" required placeholder="@lang('Enter ' . keyToTitle($k))"/>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-label-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary btn-sm px-5 rounded-pill shadow-md">@lang('Initialize')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="updateBtn" class="modal fade animate__animated animate__fadeIn" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-2xl rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="badge bg-label-primary p-2 me-3 rounded-circle">
                        <i class="las la-edit fs-4"></i>
                    </div>
                    <h5 class="modal-title h6 fw-bold mb-0">@lang('Reconfigure Node Data')</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.frontend.sections.content', $key) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="element">
                <input type="hidden" name="id">
                <div class="modal-body p-4">
                    <div class="row g-4">
                        @foreach($section->element ?? [] as $k => $type)
                            @if($k != 'modal')
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark mb-2">{{__(keyToTitle($k))}}</label>
                                    @if($type == 'icon')
                                        <div class="input-group input-group-merge shadow-none border rounded-3 overflow-hidden">
                                            <span class="input-group-text border-0 bg-light"><i class="las la-home fs-5"></i></span>
                                            <input type="text" class="form-control border-0 iconPicker icon ps-1" autocomplete="off" name="{{ $k }}" required>
                                            <span class="input-group-text border-0 bg-white" data-icon="las la-home" role="iconpicker">@lang('Change')</span>
                                        </div>
                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                            <div class="image-update-node p-3 border rounded-3 bg-light-soft mb-2 d-flex align-items-center gap-3">
                                                <div class="imageModalUpdate{{ $loop->index }} rounded-3 border shadow-sm" style="width: 60px; height: 50px; background-size: cover; background-position: center; flex-shrink: 0;"></div>
                                                <div class="flex-grow-1">
                                                    <input type="file" class="form-control form-control-sm border-0 shadow-sm" name="image_input[{{ $imgKey }}]" accept=".png,.jpg,.jpeg,.webp">
                                                    <small class="tiny text-muted">{{ @$image->size }}px</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif($type == 'textarea' || $type == 'textarea-nic')
                                        <textarea rows="4" class="form-control border rounded-3 {{ $type == 'textarea-nic' ? 'nicEdit' : '' }}" name="{{ $k }}"></textarea>
                                    @elseif($k == 'select')
                                        <select class="form-select border rounded-3" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control border rounded-3" name="{{ $k }}" required/>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-label-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary btn-sm px-5 rounded-pill shadow-md">@lang('Deploy Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($key === 'social_icon')
<div id="socialQuickModal" class="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered social-quick-modal-dialog" role="document">
        <div class="modal-content border-0 shadow-2xl rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title h6 fw-bold mb-0">@lang('Social Icon Manager')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route(getFrontendSectionRoute($key, 'content'), $key) }}" method="POST" enctype="multipart/form-data" id="socialQuickForm">
                @csrf
                <input type="hidden" name="type" value="element">
                <input type="hidden" name="id" value="">
                <div class="modal-body px-3 py-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">@lang('Title')</label>
                            <input type="text" class="form-control rounded-3" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">@lang('Profile URL')</label>
                            <input type="text" class="form-control rounded-3" name="url" placeholder="https://">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">@lang('Icon Class')</label>
                            <div class="input-group input-group-merge shadow-none border rounded-3 overflow-hidden">
                                <span class="input-group-text border-0 bg-light social-icon-preview"><i class="las la-icons fs-5"></i></span>
                                <input type="text" class="form-control border-0 social-icon-input ps-1" autocomplete="off" name="icon" placeholder="fab fa-facebook-f / lab la-instagram">
                            </div>
                            <small class="text-muted tiny d-block mt-2">@lang('Use any icon class (Font Awesome / Line Awesome), or upload image / SVG below.')</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">@lang('Visibility')</label>
                            <select class="form-select rounded-3" name="show_on_public">
                                <option value="1">@lang('Public (show in footer)')</option>
                                <option value="0">@lang('Private (hidden)')</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">@lang('Upload Social Photo')</label>
                            <input type="file" class="form-control form-control-sm rounded-3" name="image_input[custom_icon]" accept=".jpeg,.jpg,.png,.webp,.svg">
                            <small class="text-muted tiny d-block">@lang('Supported formats: JPEG, JPG, PNG, WebP, SVG')</small>
                            <small class="text-muted tiny d-block">@lang('Recommended size: 96x96 px (square), max file size: 2MB')</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">@lang('Photo Action')</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="remove_image[custom_icon]" value="1" id="socialQuickRemoveImage">
                                <label class="form-check-label tiny" for="socialQuickRemoveImage">@lang('Remove existing icon photo')</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">@lang('Custom SVG Markup (optional)')</label>
                            <textarea class="form-control form-control-sm rounded-3" rows="2" name="custom_icon_svg" placeholder="<svg>...</svg>"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-3 pb-3 pt-2">
                    <button type="button" class="btn btn-label-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">@lang('Cancel')</button>
                    <button type="submit" class="btn btn-primary btn-sm px-5 rounded-pill shadow-md">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<x-confirmation-modal />
@endsection

@push('style')
<style>
    :root {
        --premium-primary: #696cff;
        --premium-success: #71dd37;
        --premium-info: #03c3ec;
    }

    .frontend-section-wrapper { font-family: 'Public Sans', sans-serif; }
    
    .bg-label-primary { background-color: #e7e7ff !important; color: var(--premium-primary) !important; }
    .bg-label-success { background-color: #e8fadf !important; color: var(--premium-success) !important; }
    .bg-label-info { background-color: #d7f5fc !important; color: var(--premium-info) !important; }
    .bg-label-secondary { background-color: #f5f5f9 !important; color: #8592a3 !important; }
    .bg-light-premium { background-color: #f8f9fa; }

    .avatar-md { width: 48px; height: 48px; }
    .avatar-sm { width: 34px; height: 34px; }
    .avatar-xs { width: 28px; height: 28px; }

    .ls-1 { letter-spacing: 0.5px; }
    .tiny { font-size: 0.72rem; }
    .bg-light-soft { background-color: rgba(105, 108, 255, 0.02); }
    
    /* Image Nodes */
    .image-node-card { transition: all 0.3s; }
    .image-node-card:hover { border-color: var(--premium-primary) !important; background-color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    
    .preview-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(105, 108, 255, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .preview-container:hover .preview-overlay { opacity: 1; }

    /* Table Hover */
    .element-row:hover { background-color: #fcfdfe !important; transform: scale(1.002); }
    
    /* Custom Alerts */
    .alert-custom-primary { background: linear-gradient(135deg, #e7e7ff 0%, #f0f0ff 100%); }
    .alert-abstract-shape {
        position: absolute;
        right: -30px;
        top: -30px;
        width: 120px;
        height: 120px;
        background: var(--premium-primary);
        opacity: 0.05;
        border-radius: 50%;
    }

    /* Sidebar Visuals */
    .preview-mockup-area { min-height: 200px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(5px); }
    .animate-pulse { animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { opacity: 0.4; }
        50% { opacity: 0.7; }
        100% { opacity: 0.4; }
    }

    .sidebar-abstract-shape {
        position: absolute;
        bottom: -20px;
        right: -20px;
        width: 100px;
        height: 100px;
        background: var(--premium-success);
        opacity: 0.1;
        border-radius: 50%;
    }

    .insight-abstract-shape {
        position: absolute;
        bottom: -30px;
        right: -30px;
        width: 150px;
        height: 150px;
        background: #fff;
        opacity: 0.1;
        border-radius: 50%;
    }

    .shadow-md { box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }

    .search-box { transition: all 0.3s; }
    .search-box:focus-within { border-color: var(--premium-primary) !important; box-shadow: 0 0 0 0.2rem rgba(105, 108, 255, 0.1) !important; }

    /* NicEditor Customization */
    .nic-editor-wrapper .nicEdit-main { 
        background-color: #fff !important; 
        border: 1px solid #d9dee3 !important; 
        border-radius: 0.5rem !important;
        padding: 10px !important;
        min-height: 200px !important;
    }

    .social-action-wrap .btn {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .social-action-wrap .confirmationBtn {
        border-color: #f0d2d2 !important;
    }

    .social-quick-modal-dialog {
        max-width: 540px;
    }

    #socialQuickModal .modal-content {
        max-height: min(76vh, 620px);
        overflow: hidden;
    }

    #socialQuickModal .modal-body {
        overflow-y: auto;
    }

    .contact-quick-line {
        min-height: 56px;
        border-color: #e5e7eb !important;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .contact-quick-line__label {
        width: 60px;
        flex: 0 0 60px;
    }

    .contact-quick-line__preview {
        width: 34px;
        height: 34px;
        border-color: #dbe4ff !important;
        background: #f8faff !important;
    }

    .contact-quick-line__file {
        width: 180px;
    }

    .contact-quick-line__value {
        min-width: 220px;
    }

    .contact-save-bar {
        position: sticky;
        bottom: 0;
        z-index: 20;
        box-shadow: 0 -2px 12px rgba(15, 23, 42, 0.06);
        border-color: #e5e7eb !important;
    }


    @media (max-width: 991px) {
        .sticky-top { position: relative !important; top: 0 !important; margin-top: 2rem; }
        .contact-quick-line__file,
        .contact-quick-line__value {
            width: 100%;
            min-width: 0;
        }
        .contact-quick-line__label {
            width: 50px;
            flex-basis: 50px;
        }
        .contact-save-bar {
            border-radius: 0.5rem;
        }
    }
</style>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script')
<script>
    (function ($) {
        "use strict";

        // 1. Enhanced Image Preview
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

        // 2. Real-time Element Filter
        $('#elementSearch').on('input', function() {
            const query = $(this).val().toLowerCase();
            $('.element-row').each(function() {
                const text = $(this).attr('data-search');
                $(this).toggle(text.includes(query));
            });
        });

        // 3. Modal Architecture
        function adminFeShowModal($modal) {
            if (!$modal.length) return;
            const modalEl = $modal[0];
            $('.modal.show').not($modal).each(function () {
                bootstrap.Modal.getOrCreateInstance(this).hide();
            });
            $('.modal-backdrop').not(':last').remove();
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        function feDecodeB64(b64) {
            try { return JSON.parse(new TextDecoder().decode(Uint8Array.from(atob(b64), c => c.charCodeAt(0)))); } catch(e) { return null; }
        }

        $('.addBtn').on('click', function () {
            const addModalEl = $('#addModal');
            if (addModalEl.length) {
                addModalEl.find('form')[0].reset();
                adminFeShowModal(addModalEl);
            }
        });

        $(document).on('click', '.updateBtn', function () {
            const $btn = $(this);
            const modal = $('#updateBtn');
            if (!modal.length) return;
            const form = modal.find('form');
            form[0].reset();
            form.find('input[name=id]').val($btn.data('id'));
            
            const obj = feDecodeB64($btn.attr('data-fe-dv'));
            if (obj) {
                $.each(obj, function (index, value) {
                    const $inp = form.find(`[name="${index}"]`);
                    if ($inp.length) {
                        if ($inp.hasClass('nicEdit')) {
                            const nicInstance = nicEditors.findEditor(index);
                            if (nicInstance) nicInstance.setContent(value);
                            else $inp.val(value);
                        } else {
                            $inp.val(value);
                        }
                    }
                });
            }

            const images = feDecodeB64($btn.attr('data-fe-images'));
            if (images) {
                $.each(images, function (index, value) {
                    modal.find(`.imageModalUpdate${index}`).css('background-image', `url(${value})`);
                });
            }

            adminFeShowModal(modal);
        });

        @if($key === 'social_icon')
        const socialContentUrl = @json(route(getFrontendSectionRoute($key, 'content'), $key));
        const socialToggleUrl = @json(route('admin.frontend.sections.social_icon.toggle_public'));
        const normalizeIconClass = function (rawValue) {
            const raw = (rawValue || '').toString().trim();
            if (!raw) return '';
            if (raw.includes('<i') || raw.includes('&lt;i')) {
                const decoded = $('<textarea/>').html(raw).text();
                const classMatch = decoded.match(/class\s*=\s*["']([^"']+)["']/i);
                if (classMatch && classMatch[1]) {
                    return classMatch[1].trim();
                }
                return decoded.replace(/<[^>]*>/g, '').trim();
            }
            return raw.replace(/<[^>]*>/g, '').trim();
        };

        function updateSocialBadge($row, isPublic) {
            const $badge = $row.find('.js-public-badge');
            $badge.attr('data-public', isPublic ? 1 : 0);
            $badge.removeClass('bg-label-success bg-label-danger');
            $badge.addClass(isPublic ? 'bg-label-success' : 'bg-label-danger');
            $badge.html(`<i class="las la-${isPublic ? 'eye' : 'eye-slash'} me-1"></i> ${isPublic ? 'Public' : 'Private'}`);
        }

        function updateSocialToggleButton($btn, isPublic) {
            $btn.attr('data-public', isPublic ? 1 : 0);
            $btn.html(`<i class="las la-${isPublic ? 'toggle-on text-success' : 'toggle-off text-danger'}"></i>`);
        }

        $(document).on('click', '.socialUploadTrigger', function () {
            $(this).closest('form').find('.socialUploadInput').trigger('click');
        });

        $(document).on('change', '.socialUploadInput', function () {
            if (this.files && this.files.length > 0) {
                this.form.submit();
            }
        });

        $(document).on('click', '.socialPublicToggleBtn', function () {
            const $btn = $(this);
            const rowId = $btn.data('id');
            const current = parseInt($btn.attr('data-public'), 10) === 1 ? 1 : 0;
            const next = current === 1 ? 0 : 1;

            $.post(socialToggleUrl, {
                _token: '{{ csrf_token() }}',
                id: rowId,
                show_on_public: next
            }).done(function (res) {
                if (res && res.success) {
                    const $row = $btn.closest('tr');
                    updateSocialBadge($row, next === 1);
                    updateSocialToggleButton($btn, next === 1);
                }
            });
        });

        $(document).on('click', '.socialQuickAdd', function () {
            const $modal = $('#socialQuickModal');
            const $form = $modal.find('form');
            $form[0].reset();
            $form.attr('action', socialContentUrl);
            $form.find('input[name=id]').val('');
            $form.find('input[name=icon]').val('');
            $form.find('select[name=show_on_public]').val('1');
            $form.find('.social-icon-preview i').attr('class', 'las la-icons fs-5');
            adminFeShowModal($modal);
        });

        $(document).on('click', '.socialQuickEditBtn', function () {
            const $btn = $(this);
            const $modal = $('#socialQuickModal');
            const $form = $modal.find('form');
            $form[0].reset();
            $form.attr('action', socialContentUrl);
            const normalizedIcon = normalizeIconClass($btn.data('icon') || '');
            $form.find('input[name=id]').val($btn.data('id'));
            $form.find('input[name=title]').val($btn.data('title') || '');
            $form.find('input[name=icon]').val(normalizedIcon);
            $form.find('.social-icon-preview i').attr('class', (normalizedIcon || 'las la-icons') + ' fs-5');
            $form.find('input[name=url]').val($btn.data('url') || '');
            $form.find('textarea[name=custom_icon_svg]').val($btn.data('customIconSvg') || '');
            $form.find('select[name=show_on_public]').val(($btn.data('showOnPublic') ?? 1).toString());
            adminFeShowModal($modal);
        });

        $('#socialQuickModal').on('hidden.bs.modal', function () {
            $('.modal-backdrop').not(':last').remove();
            if (!$('.modal.show').length) {
                $('body').removeClass('modal-open');
            }
        });

        $(document).on('input', '#socialQuickModal input[name=icon]', function () {
            const iconClass = ($(this).val() || '').trim();
            $('#socialQuickModal .social-icon-preview i').attr('class', (iconClass !== '' ? iconClass : 'las la-icons') + ' fs-5');
        });
        @endif

        // 4. Icon Picker Integration
        $('.iconPicker').iconpicker({
            align: 'center',
            arrowClass: 'btn-danger',
            arrowPrevIconClass: 'fas fa-angle-left',
            arrowNextIconClass: 'fas fa-angle-right',
            cols: 10,
            footer: true,
            header: true,
            icon: 'fas fa-bomb',
            iconset: 'fontawesome5',
            labelHeader: '{0} of {1} pages',
            labelFooter: '{0} icons',
            placement: 'bottom',
            rows: 5,
            search: true,
            searchText: 'Search icon...',
            selectedClass: 'btn-success',
            unselectedClass: ''
        }).on('change', function(e) {
            $(this).parent().find('.input-group-text i').attr('class', e.icon);
        });

    })(jQuery);
</script>
@endpush
