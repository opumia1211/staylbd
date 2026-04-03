@extends('admin.layouts.app')
@section('panel')
    @if($key == 'contact_us')
        {{-- Compact: Channel info + Message Center in one bar --}}
        <div class="card border-0 shadow-sm mb-3 contact-top-bar">
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap align-items-center gap-3 small text-muted">
                        <span><i class="las la-shield-alt text-success"></i> @lang('Channel settings') (WhatsApp, Telegram, Email) – @lang('set below, used in background')</span>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-1">
                        <a href="{{ route('admin.ticket.index') }}" class="btn btn--primary btn-sm py-1 px-2"><i class="las la-inbox"></i> @lang('Inbox')</a>
                        <a href="{{ route('admin.ticket.pending') }}" class="btn btn--warning btn-sm py-1 px-2"><i class="las la-clock"></i> @lang('Pending')</a>
                        <a href="{{ route('admin.ticket.answered') }}" class="btn btn--success btn-sm py-1 px-2"><i class="las la-check-circle"></i> @lang('Answered')</a>
                        <a href="{{ route('admin.ticket.closed') }}" class="btn btn-outline--dark btn-sm py-1 px-2">@lang('Closed')</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if($key == 'policy_pages')
        {{-- Compact: Policy pages hint + short user URLs --}}
        <div class="card border-0 shadow-sm mb-3 policy-top-bar" role="region" aria-label="@lang('Policy pages info')">
            <div class="card-body py-2 px-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="small text-muted"><i class="las la-file-contract text--primary" aria-hidden="true"></i> @lang('Policy Pages') — @lang('Privacy, Terms, Shipping, etc. User links') <code class="small">/policy/{id}</code> @lang('(short) or') <code class="small">/policy/slug/id</code></span>
                </div>
            </div>
        </div>
    @endif
    @if(@$section->content)
        <div class="row">
            <div class="col-12 {{ $key == 'contact_us' ? 'mb-3' : 'mb-30' }}">
                <div class="card {{ $key == 'contact_us' ? 'border-0 shadow-sm' : '' }}">
                    <div class="card-body {{ $key == 'contact_us' ? 'py-3 px-3' : '' }}">
                        <form action="{{ route(getFrontendSectionRoute($key, 'content')) }}" method="POST" enctype="multipart/form-data" class="{{ $key == 'contact_us' ? 'contact-content-form' : '' }}" aria-label="@lang('Content settings form')">
                            @csrf
                            <input type="hidden" name="type" value="content">
                            <div class="row {{ $key == 'contact_us' ? 'g-2' : '' }}">
                                @php
                                    $imgCount = 0;
                                @endphp
                                @foreach($section->content as $k => $item)
                                    @if($k == 'images')
                                        @php
                                            $imgCount = collect($item)->count();
                                        @endphp
                                        @foreach($item as $imgKey => $image)
                                            <div class="col-md-4">
                                                <input type="hidden" name="has_image" value="1">
                                                <div class="form-group">
                                                    <label>{{__(keyToTitle(@$imgKey))}}</label>
                                                    <div class="image-upload">
                                                        <div class="thumb">
                                                            <div class="avatar-preview">
                                                                <div class="profilePicPreview" style="background-image: url({{getImage('assets/images/frontend/' . $key .'/'. @$content->data_values->$imgKey,@$section->content->images->$imgKey->size) }})">
                                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="avatar-edit">
                                                                <input type="file" class="profilePicUpload" name="image_input[{{ @$imgKey }}]" id="fe_c_img_{{ $key }}_{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                                <label for="fe_c_img_{{ $key }}_{{ $loop->index }}"
                                                                       class="bg--primary">{{__(keyToTitle(@$imgKey))}}</label>
                                                                <small class="mt-2  ">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                                    @if(@$section->content->images->$imgKey->size)
                                                                        | @lang('Will be resized to'):
                                                                        <b>{{@$section->content->images->$imgKey->size}}</b>
                                                                        @lang('px').
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        <div class="@if($imgCount > 1) col-md-12 @else col-md-8 @endif">
                                            @push('divend')
                                        </div>
                                        @endpush
                                    @else
                                        @if($k != 'images')
                                            @if($item == 'icon')
                                                @php $feId = 'fe_c_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                                <div class="col-md-12">
                                                    <div class="form-group ">
                                                        <label for="{{ $feId }}">{{__(keyToTitle($k))}}</label>
                                                        <div class="input-group">
                                                            <input type="text" id="{{ $feId }}" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" value="{{ @$content->data_values->$k }}" required>
                                                            <span class="input-group-text  input-group-addon" data-icon="las la-home" role="iconpicker"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif($item == 'textarea')
                                                @php $feId = 'fe_c_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="{{ $feId }}">{{__(keyToTitle($k))}}</label>
                                                        <textarea id="{{ $feId }}" rows="10" class="form-control" name="{{$k}}" required>{{ @$content->data_values->$k}}</textarea>
                                                    </div>
                                                </div>

                                            @elseif($item == 'textarea-nic')
                                                @php $feId = 'fe_c_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="{{ $feId }}">{{__(keyToTitle($k))}}</label>
                                                        <textarea id="{{ $feId }}" rows="10" class="form-control nicEdit" name="{{$k}}" >{{ @$content->data_values->$k}}</textarea>
                                                    </div>
                                                </div>
                                            @elseif($k == 'select')
                                                @php
                                                    $selectName = $item->name;
                                                    $feId = 'fe_c_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $selectName);
                                                @endphp
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="{{ $feId }}">{{__(keyToTitle(@$selectName))}}</label>
                                                        <select id="{{ $feId }}" class="form-control" name="{{ @$selectName }}">
                                                            @foreach($item->options as $selectItemKey => $selectOption)
                                                                <option value="{{ $selectItemKey }}" @if(@$content->data_values->$selectName == $selectItemKey) selected @endif>{{ $selectOption }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @else
                                                @php $feId = 'fe_c_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="{{ $feId }}">{{__(keyToTitle($k))}}</label>
                                                        <input type="text" id="{{ $feId }}" class="form-control" name="{{$k}}" value="{{@$content->data_values->$k }}" required/>
                                                    </div>
                                                </div>

                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                                @stack('divend')
                            </div>

                            <div class="form-group {{ $key == 'contact_us' ? 'mb-0 mt-2' : '' }}">
                                <button type="submit" class="btn btn--primary {{ $key == 'contact_us' ? 'btn-sm' : 'w-100 h-45' }}">@lang('Submit')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


    @if(@$section->element)
        @if($key == 'social_icon')
        <div class="card border-0 shadow-sm mb-3 font-sans">
            <div class="card-body py-2 px-3 text-sm text-slate-600">
                <p class="mb-1 fw-semibold text-slate-800">@lang('Social icon options')</p>
                <ul class="mb-0 ps-3 small">
                    <li>@lang('Pick a demo icon from the library (click the square next to the field), or type classes e.g.') <code class="small">fab fa-facebook-f</code> / <code class="small">lab la-instagram</code>.</li>
                    <li>@lang('Optional: upload a custom logo (PNG, JPG, WebP, SVG). If uploaded, it is shown in the footer instead of the library icon.')</li>
                    <li>@lang('Visibility: use the switch in the table for one-click Public / Private, or set it in Add / Edit.')</li>
                </ul>
            </div>
        </div>
        @endif

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
            @if($key == 'contact_us')
            <span class="small text-muted">@lang('Contact methods shown on site (e.g. WhatsApp, Phone, Email).')</span>
            @endif
            @if($key == 'policy_pages')
            <span class="small text-muted">@lang('Each row is one policy page. Edit opens full editor.')</span>
            @endif
            <div class="input-group input-group-sm" style="{{ ($key == 'contact_us' || $key == 'policy_pages') ? 'width: 180px;' : '' }}">
                <label for="fe_search_table_{{ $key }}" class="sr-only">@lang('Search table')</label>
                <input type="text" id="fe_search_table_{{ $key }}" name="search_table" class="form-control form-control-sm bg--white" placeholder="@lang('Search')..." aria-label="@lang('Search')">
                <button type="button" class="btn btn--primary btn-sm input-group-text" aria-label="@lang('Search')"><i class="fa fa-search" aria-hidden="true"></i></button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card {{ ($key == 'contact_us' || $key == 'policy_pages') ? 'border-0 shadow-sm' : '' }}">
                    <div class="card-body {{ ($key == 'contact_us' || $key == 'policy_pages') ? 'p-2' : 'p-0' }}">
                        <div class="table-responsive--sm table-responsive">
                            <table class="table table--light style--two custom-data-table {{ ($key == 'contact_us' || $key == 'policy_pages') ? 'table-sm mb-0' : '' }}">
                                <thead>
                                <tr>
                                    <th>@lang('SL')</th>
                                    @if(@$section->element->images)
                                        <th>@lang('Image')</th>
                                    @endif
                                    @foreach($section->element as $k => $type)
                                        @if($k !='modal')
                                            @if($type=='text' || $type=='icon')
                                                <th>{{ __(keyToTitle($k)) }}</th>
                                            @elseif($type == 'textarea')
                                                <th>{{ __(keyToTitle($k)) }}</th>
                                            @elseif($k == 'select')
                                                <th>{{keyToTitle(@$section->element->$k->name)}}</th>
                                            @endif
                                        @endif
                                    @endforeach
                                    <th>@lang('Action')</th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                @forelse($elements as $data)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        @if(@$section->element->images)
                                        @php $firstKey = collect($section->element->images)->keys()[0]; @endphp
                                            <td>
                                                <div class="customer-details d-block">
                                                    <a href="javascript:void(0)" class="thumb">
                                                        <img src="{{ getImage('assets/images/frontend/' . $key .'/'. @$data->data_values->$firstKey,@$section->element->images->$firstKey->size) }}" alt="@lang('image')">
                                                    </a>
                                                </div>
                                            </td>
                                        @endif
                                        @foreach($section->element as $k => $type)
                                            @if($k !='modal')
                                                @if($type == 'text' || $type == 'icon')
                                                    @if($type == 'icon')
                                                        <td class="small">@if($key == 'social_icon'){{ \Illuminate\Support\Str::limit(strip_tags((string)(@$data->data_values->$k ?? '')), 42) }}@else @php echo @$data->data_values->$k; @endphp @endif</td>
                                                    @else
                                                        <td>{{__(@$data->data_values->$k)}}</td>
                                                    @endif
                                                @elseif($type == 'textarea')
                                                    <td class="small text-muted">{{ \Illuminate\Support\Str::limit(strip_tags((string)(@$data->data_values->$k ?? '')), 32) }}</td>
                                                @elseif($k == 'select')
                                                    @php
                                                        $dataVal = @$section->element->$k->name;
                                                        $selRaw = @$data->data_values->$dataVal;
                                                    @endphp
                                                    <td class="align-middle">
                                                        @if($key == 'social_icon' && $dataVal === 'show_on_public')
                                                            @php $pub = ($selRaw === null || $selRaw === '' || (int) $selRaw === 1); @endphp
                                                            <div class="form-check form-switch m-0 d-inline-flex align-items-center gap-2 social-icon-pub-cell">
                                                                <input type="checkbox" class="form-check-input social-icon-pub-toggle" role="switch" id="soc_pub_{{ $data->id }}" data-id="{{ $data->id }}" @if($pub) checked @endif title="{{ __('Toggle: show or hide in site footer') }}" aria-label="{{ __('Show in footer') }}">
                                                                <label class="form-check-label small mb-0 text-nowrap" for="soc_pub_{{ $data->id }}">{{ $pub ? __('Public') : __('Private') }}</label>
                                                            </div>
                                                        @else
                                                            {{ $selRaw }}
                                                        @endif
                                                    </td>
                                                @endif
                                            @endif
                                        @endforeach
                                        <td>
                                            <div class="button--group">
                                                @if($section->element->modal)
                                                @php
                                                    $images = [];
                                                    if(@$section->element->images){
                                                        foreach($section->element->images as $imgKey => $imgs){
                                                            $imgName = isset($data->data_values->$imgKey) ? $data->data_values->$imgKey : '';
                                                            $imgName = is_scalar($imgName) ? (string) $imgName : '';
                                                            $relPath = 'assets/images/frontend/' . $key . '/' . $imgName;
                                                            $images[] = getImage($relPath, @$section->element->images->$imgKey->size);
                                                        }
                                                    }
                                                    $__feRowDvJson = json_encode($data->data_values ?? new \stdClass(), JSON_UNESCAPED_UNICODE);
                                                    if ($__feRowDvJson === false) {
                                                        $__feRowDvJson = '{}';
                                                    }
                                                    $__feRowDvB64 = base64_encode($__feRowDvJson);
                                                    $__feRowImgB64 = ! empty($images) ? base64_encode(json_encode($images, JSON_UNESCAPED_UNICODE)) : '';
                                                @endphp
                                                    <button type="button" class="btn btn-sm btn-outline--primary updateBtn"
                                                        data-id="{{$data->id}}"
                                                        data-fe-dv="{{ $__feRowDvB64 }}"
                                                        @if($__feRowImgB64 !== '')
                                                            data-fe-images="{{ $__feRowImgB64 }}"
                                                        @endif>
                                                        <i class="la la-pencil-alt"></i> @lang('Edit')
                                                    </button>
                                                @else
                                                    <a href="{{ route(getFrontendSectionRoute($key, 'element'), $data->id) }}" class="btn btn-sm btn-outline--primary"><i class="la la-pencil-alt"></i> @lang('Edit')</a>
                                                @endif
                                                <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                data-action="{{ route('admin.frontend.remove',$data->id) }}"
                                                data-question="@lang('Are you sure to remove this item?')"><i class="la la-trash"></i> @lang('Remove')</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add METHOD MODAL --}}
        <div id="addModal" class="modal fade {{ $key == 'contact_us' ? 'contact-element-modal' : '' }} {{ $key == 'social_icon' ? 'social-icon-fe-modal' : '' }}" tabindex="-1" role="dialog">
            <div class="modal-dialog {{ $key == 'contact_us' ? 'modal-dialog-centered' : '' }} {{ $key == 'social_icon' ? 'modal-dialog-centered' : '' }}" role="document">
                <div class="modal-content {{ $key == 'social_icon' ? 'font-sans' : '' }}">
                    <div class="modal-header {{ $key == 'contact_us' ? 'py-2' : '' }} {{ $key == 'social_icon' ? 'py-2 px-3 border-bottom' : '' }}">
                        <h5 class="modal-title {{ $key == 'contact_us' ? 'small' : '' }} {{ $key == 'social_icon' ? 'h6 mb-0 font-weight-bold' : '' }}"> @lang('Add New') {{__(keyToTitle($key))}} @lang('Item')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route(getFrontendSectionRoute($key, 'content')) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <div class="modal-body {{ $key == 'contact_us' ? 'py-2' : '' }} {{ $key == 'social_icon' ? 'py-2 px-3' : '' }}">
                            @foreach($section->element as $k => $type)
                                @if($k != 'modal')
                                    @if($k == 'title')
                                        <div class="row g-2 mb-2">
                                            <div class="col-sm-7">
                                                @php $feAddId = 'fe_add_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                                <div class="form-group mb-0">
                                                    <label for="{{ $feAddId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">{{__(keyToTitle($k))}}</label>
                                                    <input type="text" id="{{ $feAddId }}" class="form-control form-control-sm" name="{{ $k }}" required/>
                                                </div>
                                            </div>
                                            @php $iconK = array_search('icon', (array)$section->element); @endphp
                                            @if($iconK)
                                            <div class="col-sm-5">
                                                @php $feAddIconId = 'fe_add_' . $key . '_icon'; @endphp
                                                <div class="form-group mb-0">
                                                    <label for="{{ $feAddIconId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">@lang('Icon Name')</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" id="{{ $feAddIconId }}" class="form-control iconPicker icon form-control-sm" autocomplete="off" name="icon" value="lab la-share-alt" placeholder="fa fa-facebook" required>
                                                        <span class="input-group-text input-group-addon py-0 px-2" data-icon="lab la-share-alt" role="iconpicker"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    @elseif($k == 'icon')
                                        {{-- Handled in title row above for better layout --}}
                                    @elseif($type == 'icon')
                                        @php $feAddId = 'fe_add_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group mb-2">
                                            <label for="{{ $feAddId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">{{__(keyToTitle($k))}}</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" id="{{ $feAddId }}" class="form-control iconPicker icon form-control-sm" autocomplete="off" name="{{ $k }}" value="las la-home" required>
                                                <span class="input-group-text input-group-addon py-0 px-2" data-icon="las la-home" role="iconpicker"></span>
                                            </div>
                                        </div>
                                    @elseif($k == 'custom_icon_svg')
                                        @php $feAddId = 'fe_add_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group mb-2">
                                            <label for="{{ $feAddId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">@lang('Custom Icon (SVG/HTML)')</label>
                                            <textarea id="{{ $feAddId }}" rows="3" class="form-control form-control-sm font-monospace" style="font-size: 11px;" name="{{ $k }}" dir="ltr" spellcheck="false" placeholder="<svg>...</svg>"></textarea>
                                            <small class="text-muted d-block mt-1" style="font-size: 10px;">@lang('Overrides library icon if provided.')</small>
                                        </div>
                                    @elseif($k == 'select')
                                        @php $feAddSelId = 'fe_add_' . $key . '_sel_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $section->element->$k->name ?? $k); @endphp
                                    <div class="form-group {{ $key == 'social_icon' ? 'mb-2' : '' }}">
                                        <label for="{{ $feAddSelId }}" class="{{ $key == 'social_icon' ? 'small mb-1 font-weight-bold text-uppercase text-muted' : '' }}" @if($key == 'social_icon') style="letter-spacing: 0.5px; font-size: 0.65rem;" @endif>{{keyToTitle(@$section->element->$k->name)}}</label>
                                        <select id="{{ $feAddSelId }}" class="form-control {{ $key == 'social_icon' ? 'form-control-sm' : '' }}" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                        <input type="hidden" name="has_image" value="1">
                                        @if($key == 'social_icon')
                                        <div class="form-group mb-2 border rounded p-2 bg-light-soft">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="small font-weight-bold text-uppercase text-muted mb-0" style="letter-spacing: 0.5px; font-size: 0.65rem;">@lang('Or Upload Image File')</label>
                                                @if(@$section->element->images->$imgKey->size)
                                                    <span class="text-muted" style="font-size: 10px;">{{ @$section->element->images->$imgKey->size }}px</span>
                                                @endif
                                            </div>
                                            <div class="image-upload social-fe-social-upload">
                                                <div class="thumb d-flex align-items-center gap-3">
                                                    <div class="avatar-preview flex-shrink-0 mb-0 position-relative" style="width: 80px; height: 80px; cursor: pointer;" onclick="document.getElementById('fe_add_img_{{ $key }}_{{ $loop->index }}').click()" title="@lang('Click here to select an image')">
                                                        <div class="profilePicPreview rounded border border-dashed border-secondary bg-white d-flex align-items-center justify-content-center" style="width:80px;height:80px;min-width:80px;min-height:80px;max-width:80px;max-height:80px;background-size:contain;background-position:center;background-repeat:no-repeat;background-image:url({{ getImage('/', @$section->element->images->$imgKey->size) }});box-shadow:none; border-width: 1px !important;">
                                                            @if(!getImage('/', @$section->element->images->$imgKey->size))
                                                                <i class="las la-plus text-muted" style="font-size: 1.5rem; opacity: 0.4;"></i>
                                                            @endif
                                                            <button type="button" class="remove-image position-absolute bg-danger text-white border-0 rounded-circle" style="top:-6px; right:-6px; width:20px; height:20px; font-size:10px; line-height:20px; padding:0; z-index: 10;" onclick="event.stopPropagation();"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="avatar-edit flex-grow-1 pt-0">
                                                        <input type="file" class="profilePicUpload d-none hidden" name="image_input[{{ $imgKey }}]" id="fe_add_img_{{ $key }}_{{ $loop->index }}" accept=".png,.jpg,.jpeg,.webp,.svg">
                                                        <label for="fe_add_img_{{ $key }}_{{ $loop->index }}" class="btn btn-sm btn-outline-secondary py-1 px-3 border-dashed" style="font-size: 11px; cursor:pointer;">@lang('Click to Choose File')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="form-group">
                                            <label>{{__(keyToTitle($k)) }}</label>
                                            <div class="image-upload">
                                                <div class="thumb">
                                                    <div class="avatar-preview">
                                                        <div class="profilePicPreview" style="background-image: url({{ getImage('/',@$section->element->images->$imgKey->size) }})">
                                                            <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="avatar-edit">
                                                        <input type="file" class="profilePicUpload" name="image_input[{{ $imgKey }}]" id="fe_add_img_{{ $key }}_{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                        <label for="fe_add_img_{{ $key }}_{{ $loop->index }}" class="bg--success">{{ __(keyToTitle($imgKey)) }}</label>
                                                        <small class="mt-2  ">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                            @if(@$section->element->images->$imgKey->size)
                                                                | @lang('Will be resized to'): <b>{{@$section->element->images->$imgKey->size}}</b> @lang('px').
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    @elseif($type == 'textarea')
                                        @php $feAddId = 'fe_add_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        @php $optSocialSvg = ($key == 'social_icon' && $k == 'custom_icon_svg'); @endphp
                                        <div class="form-group {{ $optSocialSvg ? 'mb-3' : '' }}">
                                            <label for="{{ $feAddId }}" class="{{ $optSocialSvg ? 'small font-weight-bold mb-1' : '' }}">{{__(keyToTitle($k))}}</label>
                                            <textarea id="{{ $feAddId }}" rows="{{ $optSocialSvg ? 3 : 4 }}" class="form-control {{ $optSocialSvg ? 'form-control-sm' : '' }}" name="{{ $k }}" dir="ltr" spellcheck="false" @if(!$optSocialSvg) required @endif @if($optSocialSvg) placeholder="{{ __('Paste full &lt;svg&gt;…&lt;/svg&gt; or &lt;img src=&quot;data:image/webp;base64,...&quot; /&gt;') }}" @endif></textarea>
                                            @if($optSocialSvg)
                                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">@lang('No file upload needed if you paste code here. Scripts and external URLs are removed for security.')</small>
                                            @endif
                                        </div>

                                    @elseif($type == 'textarea-nic')
                                        @php $feAddId = 'fe_add_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group">
                                            <label for="{{ $feAddId }}">{{__(keyToTitle($k))}}</label>
                                            <textarea id="{{ $feAddId }}" rows="4" class="form-control nicEdit" name="{{$k}}"></textarea>
                                        </div>

                                    @else
                                        @php $feAddId = 'fe_add_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group mb-2">
                                            <label for="{{ $feAddId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">{{__(keyToTitle($k))}}</label>
                                            <div class="{{ $k == 'url' ? 'input-group input-group-sm' : '' }}">
                                                @if($k == 'url') <span class="input-group-text bg-light border-end-0"><i class="las la-link"></i></span> @endif
                                                <input type="text" id="{{ $feAddId }}" class="form-control form-control-sm {{ $k == 'url' ? 'border-start-0' : '' }}" name="{{ $k }}" @if(!($key == 'social_icon' && $k == 'url')) required @endif placeholder="{{ ($key == 'social_icon' && $k == 'url') ? 'https://' : '' }}"/>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                        <div class="modal-footer {{ $key == 'contact_us' ? 'py-2' : '' }} {{ $key == 'social_icon' ? 'py-2 px-3' : '' }}">
                            <button type="submit" class="btn btn--primary {{ $key == 'contact_us' || $key == 'social_icon' ? 'btn-sm' : 'w-100 h-45' }}">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Update METHOD MODAL --}}
        <div id="updateBtn" class="modal fade {{ $key == 'contact_us' ? 'contact-element-modal' : '' }} {{ $key == 'social_icon' ? 'social-icon-fe-modal' : '' }}" tabindex="-1" role="dialog">
            <div class="modal-dialog {{ $key == 'contact_us' ? 'modal-dialog-centered' : '' }} {{ $key == 'social_icon' ? 'modal-dialog-centered' : '' }}" role="document">
                <div class="modal-content {{ $key == 'social_icon' ? 'font-sans' : '' }}">
                    <div class="modal-header {{ $key == 'contact_us' ? 'py-2' : '' }} {{ $key == 'social_icon' ? 'py-2 px-3 border-bottom' : '' }}">
                        <h5 class="modal-title {{ $key == 'contact_us' ? 'small' : '' }} {{ $key == 'social_icon' ? 'h6 mb-0 font-weight-bold' : '' }}"> @lang('Update')  {{__(keyToTitle($key))}} @lang('Item')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form action="{{ route('admin.frontend.sections.content', $key) }}" class="edit-route" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        <input type="hidden" name="id">
                        <div class="modal-body {{ $key == 'contact_us' ? 'py-2' : '' }} {{ $key == 'social_icon' ? 'py-2 px-3' : '' }}">
                            @foreach($section->element as $k => $type)
                                @if($k != 'modal')
                                    @if($k == 'title')
                                        <div class="row g-2 mb-2">
                                            <div class="col-sm-7">
                                                @php $feUpdId = 'fe_upd_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                                <div class="form-group mb-0">
                                                    <label for="{{ $feUpdId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">{{__(keyToTitle($k))}}</label>
                                                    <input type="text" id="{{ $feUpdId }}" class="form-control form-control-sm" name="{{ $k }}" required/>
                                                </div>
                                            </div>
                                            @php $iconKUpd = array_search('icon', (array)$section->element); @endphp
                                            @if($iconKUpd)
                                            <div class="col-sm-5">
                                                @php $feUpdIconId = 'fe_upd_' . $key . '_icon'; @endphp
                                                <div class="form-group mb-0">
                                                    <label for="{{ $feUpdIconId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">@lang('Icon Name')</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" id="{{ $feUpdIconId }}" class="form-control iconPicker icon form-control-sm" autocomplete="off" name="icon" placeholder="fab fa-facebook-f" @if($key != 'social_icon') required @endif>
                                                        <span class="input-group-text input-group-addon py-0 px-2" data-icon="lab la-share-alt" role="iconpicker"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    @elseif($k == 'icon')
                                        {{-- Handled above --}}
                                    @elseif($type == 'icon')
                                        @php $feUpdId = 'fe_upd_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group mb-2">
                                            <label for="{{ $feUpdId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">{{keyToTitle($k)}}</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" id="{{ $feUpdId }}" class="form-control iconPicker icon form-control-sm" autocomplete="off" name="{{ $k }}" required>
                                                <span class="input-group-text input-group-addon py-0 px-2" data-icon="las la-home" role="iconpicker"></span>
                                            </div>
                                        </div>
                                    @elseif($k == 'custom_icon_svg')
                                        @php $feUpdId = 'fe_upd_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group mb-2">
                                            <label for="{{ $feUpdId }}" class="small mb-1 font-weight-bold text-uppercase text-muted" style="letter-spacing: 0.5px; font-size: 0.65rem;">@lang('Custom Icon (SVG/HTML)')</label>
                                            <textarea id="{{ $feUpdId }}" rows="3" class="form-control form-control-sm font-monospace" style="font-size: 11px;" name="{{ $k }}" dir="ltr" spellcheck="false"></textarea>
                                        </div>

                                    @elseif($k == 'select')
                                        @php $feUpdSelId = 'fe_upd_' . $key . '_sel_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $section->element->$k->name ?? $k); @endphp
                                    <div class="form-group {{ $key == 'social_icon' ? 'mb-2' : '' }}">
                                        <label for="{{ $feUpdSelId }}" class="{{ $key == 'social_icon' ? 'small mb-1 font-weight-bold text-uppercase text-muted' : '' }}" @if($key == 'social_icon') style="letter-spacing: 0.5px; font-size: 0.65rem;" @endif>{{keyToTitle(@$section->element->$k->name)}}</label>
                                        <select id="{{ $feUpdSelId }}" class="form-control {{ $key == 'social_icon' ? 'form-control-sm' : '' }}" name="{{ @$section->element->$k->name }}">
                                            @foreach($section->element->$k->options as $selectKey => $options)
                                                <option value="{{ $selectKey }}">{{ $options }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @elseif($k == 'images')
                                        @foreach($type as $imgKey => $image)
                                        <input type="hidden" name="has_image" value="1">
                                        @if($key == 'social_icon')
                                        <div class="form-group mb-2 border rounded p-2 bg-light-soft">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="small font-weight-bold text-uppercase text-muted mb-0" style="letter-spacing: 0.5px; font-size: 0.65rem;">@lang('Or Upload Image File')</label>
                                                @if(@$section->element->images->$imgKey->size)
                                                    <span class="text-muted" style="font-size: 10px;">{{ @$section->element->images->$imgKey->size }}px</span>
                                                @endif
                                            </div>
                                            <div class="image-upload social-fe-social-upload">
                                                <div class="thumb d-flex align-items-center gap-3">
                                                    <div class="avatar-preview flex-shrink-0 mb-0 position-relative" style="width: 80px; height: 80px; cursor: pointer;" onclick="document.getElementById('fe_upd_img_{{ $key }}_{{ $loop->index }}').click()" title="@lang('Click here to select an image')">
                                                        <div class="profilePicPreview imageModalUpdate{{ $loop->index }} rounded border border-dashed border-secondary bg-white d-flex align-items-center justify-content-center" style="width:80px;height:80px;min-width:80px;min-height:80px;max-width:80px;max-height:80px;background-size:contain;background-position:center;background-repeat:no-repeat;box-shadow:none; border-width: 1px !important;">
                                                            <button type="button" class="remove-image position-absolute bg-danger text-white border-0 rounded-circle" style="top:-6px; right:-6px; width:20px; height:20px; font-size:10px; line-height:20px; padding:0; z-index: 10;" onclick="event.stopPropagation();"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="avatar-edit flex-grow-1 pt-0">
                                                        <input type="file" class="profilePicUpload d-none hidden" name="image_input[{{ $imgKey }}]" id="fe_upd_img_{{ $key }}_{{ $loop->index }}" accept=".png,.jpg,.jpeg,.webp,.svg">
                                                        <label for="fe_upd_img_{{ $key }}_{{ $loop->index }}" class="btn btn-sm btn-outline-secondary py-1 px-3 border-dashed" style="font-size: 11px; cursor:pointer;">@lang('Click to Choose File')</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <div class="form-group">
                                            <label>{{__(keyToTitle($k))}}</label>
                                            <div class="image-upload">
                                                <div class="thumb">
                                                    <div class="avatar-preview">
                                                        <div class="profilePicPreview imageModalUpdate{{ $loop->index }}">
                                                            <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="avatar-edit">
                                                        <input type="file" class="profilePicUpload" name="image_input[{{ $imgKey }}]" id="fe_upd_img_{{ $key }}_{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                        <label for="fe_upd_img_{{ $key }}_{{ $loop->index }}" class="bg--success">{{ __(keyToTitle($imgKey)) }}</label>
                                                        <small class="mt-2  ">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                            @if(@$section->element->images->$imgKey->size)
                                                                | @lang('Will be resized to'): <b>{{@$section->element->images->$imgKey->size}}</b> @lang('px').
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                        @endforeach
                                    @elseif($type == 'textarea')
                                        @php $feUpdId = 'fe_upd_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        @php $optSocialSvgUpd = ($key == 'social_icon' && $k == 'custom_icon_svg'); @endphp
                                        <div class="form-group {{ $optSocialSvgUpd ? 'mb-3' : '' }}">
                                            <label for="{{ $feUpdId }}" class="{{ $optSocialSvgUpd ? 'small font-weight-bold mb-1' : '' }}">{{keyToTitle($k)}}</label>
                                            <textarea id="{{ $feUpdId }}" rows="{{ $optSocialSvgUpd ? 3 : 4 }}" class="form-control {{ $optSocialSvgUpd ? 'form-control-sm' : '' }}" name="{{ $k }}" dir="ltr" spellcheck="false" @if(!$optSocialSvgUpd) required @endif @if($optSocialSvgUpd) placeholder="{{ __('Paste full &lt;svg&gt;…&lt;/svg&gt; or &lt;img src=&quot;data:image/webp;base64,...&quot; /&gt;') }}" @endif></textarea>
                                            @if($optSocialSvgUpd)
                                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">@lang('No file upload needed if you paste code here. Scripts and external URLs are removed for security.')</small>
                                            @endif
                                        </div>

                                    @elseif($type == 'textarea-nic')
                                        @php $feUpdId = 'fe_upd_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group">
                                            <label for="{{ $feUpdId }}">{{keyToTitle($k)}}</label>
                                            <textarea id="{{ $feUpdId }}" rows="4" class="form-control nicEdit" name="{{$k}}"></textarea>
                                        </div>

                                    @else
                                        @php $feUpdId = 'fe_upd_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group {{ $key == 'social_icon' ? 'mb-2' : '' }}">
                                            <label for="{{ $feUpdId }}" class="{{ $key == 'social_icon' ? 'small mb-1' : '' }}">{{keyToTitle($k)}}</label>
                                            <input type="text" id="{{ $feUpdId }}" class="form-control {{ $key == 'social_icon' ? 'form-control-sm' : '' }}" name="{{ $k }}" @if(!($key == 'social_icon' && $k == 'url')) required @endif placeholder="{{ ($key == 'social_icon' && $k == 'url') ? 'https://' : '' }}"/>
                                            @if($key == 'social_icon' && $k == 'url')
                                                <small class="text-muted font-sans d-block" style="font-size: 0.72rem;">@lang('Optional; use # if not a web link.')</small>
                                            @endif
                                        </div>

                                    @endif
                                @endif
                            @endforeach
                        </div>

                        <div class="modal-footer {{ $key == 'contact_us' ? 'py-2' : '' }} {{ $key == 'social_icon' ? 'py-2 px-3' : '' }}">
                            <button type="submit" class="btn btn--primary {{ $key == 'contact_us' || $key == 'social_icon' ? 'btn-sm' : 'w-100 h-45' }}">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('breadcrumb-plugins')
            @if($section->element->modal)
                <a href="javascript:void(0)" class="btn btn-sm btn-outline--primary addBtn"><i class="las la-plus"></i>@lang('Add New')</a>
            @else
                <a href="{{ route(getFrontendSectionRoute($key, 'element')) }}" class="btn btn-sm btn-outline--primary"><i class="las la-plus"></i>@lang('Add New')</a>
            @endif
        @endpush
    @endif
    {{-- if section element end --}}

    <x-confirmation-modal />

@endsection

@if(isset($key) && $key == 'contact_us')
@push('style')
<style>
    .contact-top-bar .card-body { min-height: auto; }
    .contact-content-form .form-group { margin-bottom: 0.5rem; }
    .contact-content-form .form-group label { font-size: 0.8rem; margin-bottom: 0.2rem; }
    .contact-content-form .form-control { font-size: 0.875rem; padding: 0.35rem 0.5rem; }
    .contact-content-form .input-group-text { font-size: 0.875rem; padding: 0.35rem 0.5rem; }
    .contact-content-form .avatar-preview .profilePicPreview { min-height: 80px; }
    .contact-content-form .avatar-edit label { padding: 0.35rem 0.6rem; font-size: 0.8rem; }
    .contact-content-form small { font-size: 0.75rem; }
    .contact-element-modal .modal-body .form-group { margin-bottom: 0.5rem; }
    .contact-element-modal .modal-body label { font-size: 0.8rem; }
    .contact-element-modal .modal-body .form-control,
    .contact-element-modal .modal-body select.form-control,
    .contact-element-modal .modal-body textarea.form-control { font-size: 0.875rem; padding: 0.35rem 0.5rem; }
    .contact-element-modal .modal-body textarea { min-height: 70px; }
    .contact-element-modal .input-group-text { font-size: 0.875rem; padding: 0.35rem 0.5rem; }
</style>
@endpush
@endif

@push('style-lib')
<link href="{{ asset('assets/admin/css/fontawesome-iconpicker.min.css') }}" rel="stylesheet">
@endpush

@push('style')
<style>
    /* Fixed admin navbar uses z-index 9999; confirmation modal forces backdrop to 10599 — keep Add/Update dialogs above both */
    #addModal.modal,
    #updateBtn.modal {
        z-index: 10610 !important;
    }
    #addModal .modal-dialog,
    #updateBtn .modal-dialog {
        z-index: 10611 !important;
        position: relative;
    }
    #addModal .modal-content,
    #updateBtn .modal-content {
        position: relative;
        z-index: 1;
        pointer-events: auto;
    }
    /* Icon picker popover defaults to z-index: 9 — stays under modal/backdrop otherwise */
    body.modal-open .iconpicker-popover.popover {
        z-index: 10620 !important;
    }
    /* Social Icons: Add/Update modals — override global .profilePicPreview { height:310px } from app.css */
    .social-icon-fe-modal .modal-dialog {
        max-width: min(640px, calc(100vw - 1.5rem));
        width: 100%;
        margin: 1rem auto;
    }
    .social-icon-fe-modal .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }
    .social-icon-fe-modal .modal-body {
        overflow: visible;
        max-height: none;
    }
    .social-icon-fe-modal .social-fe-social-upload .thumb .profilePicPreview {
        width: 80px !important;
        height: 80px !important;
        min-width: 80px !important;
        min-height: 80px !important;
        max-width: 80px !important;
        max-height: 80px !important;
        display: block !important;
        background-size: contain !important;
        background-position: center !important;
        box-sizing: border-box !important;
    }
    .social-icon-fe-modal .social-fe-social-upload .avatar-edit label.btn {
        width: auto !important;
        max-width: 100%;
    }
    .social-icon-fe-modal .social-fe-social-upload .avatar-edit label.bg--success {
        width: auto !important;
        display: inline-block !important;
        line-height: 1.35 !important;
        padding: 0.35rem 0.75rem !important;
    }
    .social-icon-pub-cell .form-check-input {
        width: 2.35em;
        min-height: 1.15em;
        cursor: pointer;
        margin-top: 0;
    }
    .social-icon-pub-cell label {
        cursor: pointer;
        user-select: none;
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
            function adminFeShowModal($modal) {
                if (!$modal || !$modal.length) return;
                var node = $modal[0];
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(node).show();
                } else if (typeof $modal.modal === 'function') {
                    $modal.modal('show');
                }
            }
            function feDecodeB64Utf8Json(b64) {
                if (!b64 || typeof b64 !== 'string') {
                    return null;
                }
                try {
                    var bin = atob(b64);
                    var bytes = new Uint8Array(bin.length);
                    for (var i = 0; i < bin.length; i++) {
                        bytes[i] = bin.charCodeAt(i);
                    }
                    var json = new TextDecoder('utf-8').decode(bytes);
                    return JSON.parse(json);
                } catch (err) {
                    return null;
                }
            }
            function applyFeEditPayload($modal, obj) {
                if (!$modal || !$modal.length || !obj || typeof obj !== 'object') {
                    return;
                }
                $.each(obj, function (index, value) {
                    if (value === null || value === undefined) {
                        value = '';
                    }
                    if (typeof value === 'object') {
                        return;
                    }
                    var name = String(index);
                    if (name === 'show_on_public') {
                        value = String(parseInt(value, 10) === 0 ? 0 : 1);
                    }
                    $modal.find('input[name], select[name], textarea[name]').filter(function () {
                        if (this.type === 'file') {
                            return false;
                        }
                        return this.name === name;
                    }).val(value);
                });
            }
            function feSyncIconPickerAddons($modal) {
                $modal.find('input.iconPicker').each(function () {
                    var v = ($(this).val() || '').trim();
                    var $addon = $(this).closest('.input-group').find('.input-group-text, .input-group-addon').first();
                    if (!$addon.length) {
                        return;
                    }
                    var $ico = $addon.find('i').first();
                    if ($ico.length) {
                        $ico.attr('class', v || 'las la-icons');
                    } else if (v) {
                        $addon.prepend($('<i></i>').attr('class', v));
                    }
                });
            }
            function moveFrontendElementModalsToBody() {
                ['addModal', 'updateBtn'].forEach(function (id) {
                    var el = document.getElementById(id);
                    if (el && el.parentNode && el.parentNode !== document.body) {
                        document.body.appendChild(el);
                    }
                });
            }
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', moveFrontendElementModalsToBody);
            } else {
                moveFrontendElementModalsToBody();
            }
            $('.addBtn').on('click', function () {
                var modal = $('#addModal');
                modal.find('form')[0] && modal.find('form')[0].reset();
                adminFeShowModal(modal);
            });
            $(document).on('click', '.updateBtn', function () {
                var $btn = $(this);
                var modal = $('#updateBtn');
                modal.find('input[name=id]').val($btn.data('id'));
                var obj = feDecodeB64Utf8Json($btn.attr('data-fe-dv'));
                if (!obj) {
                    obj = $btn.data('all');
                    var legacy = $btn.attr('data-all');
                    if ((typeof obj !== 'object' || obj === null) && legacy) {
                        try {
                            obj = JSON.parse(legacy);
                        } catch (e1) {
                            obj = null;
                        }
                    }
                }
                if (!obj || typeof obj !== 'object') {
                    obj = {};
                }
                modal.data('feEditDv', obj);
                var images = feDecodeB64Utf8Json($btn.attr('data-fe-images'));
                if (!images) {
                    images = $btn.data('images');
                    if (images && typeof images === 'string') {
                        try {
                            images = JSON.parse(images);
                        } catch (e2) {
                            images = null;
                        }
                    }
                }
                if (images && images.length) {
                    for (var i = 0; i < images.length; i++) {
                        var imgloc = images[i];
                        if (!imgloc) {
                            continue;
                        }
                        var u = String(imgloc).replace(/\\/g, '/');
                        modal.find('.imageModalUpdate' + i).css('background-image', 'url("' + u.replace(/"/g, '\\"') + '")');
                    }
                }
                applyFeEditPayload(modal, obj);
                feSyncIconPickerAddons(modal);
                adminFeShowModal(modal);
            });
            $('#updateBtn').on('shown.bs.modal', function (e) {
                $(document).off('focusin.modal');
            });
            $('#addModal').on('shown.bs.modal', function (e) {
                $(document).off('focusin.modal');
            });
            function initFrontendIconPickers($root) {
                $root = $root && $root.length ? $root : $(document);
                $root.find('.iconPicker').each(function () {
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
