@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12 {{ $key == 'policy_pages' ? 'mb-3' : 'mb-30' }}">
            <div class="card {{ $key == 'policy_pages' ? 'border-0 shadow-sm' : '' }}">
                <div class="card-body {{ $key == 'policy_pages' ? 'py-3 px-3' : '' }}">
                    <form action="{{ route(getFrontendSectionRoute($key, 'content')) }}" method="POST" enctype="multipart/form-data" class="{{ $key == 'policy_pages' ? 'policy-element-form' : '' }}">
                        @csrf
                        <input type="hidden" name="type" value="element">
                        @if(@$data)
                            <input type="hidden" name="id" value="{{$data->id}}">
                        @endif
                        <div class="row {{ $key == 'policy_pages' ? 'g-2' : '' }}">
                            @php
                                $imgCount = 0;
                            @endphp
                            @foreach($section->element as $k => $content)
                                @if($k == 'images')
                                    @php
                                        $imgCount = collect($content)->count();
                                    @endphp
                                    @foreach($content as $imgKey => $image)
                                            <div class="col-md-4">
                                                <input type="hidden" name="has_image[]" value="1">
                                                <div class="form-group">
                                                    <label>{{ __(keyToTitle($imgKey)) }}</label>
                                                    <div class="image-upload">
                                                        <div class="thumb">
                                                            <div class="avatar-preview">
                                                                <div class="profilePicPreview" style="background-image: url({{getImage('assets/images/frontend/' . $key .'/'. @$data->data_values->$imgKey,@$section->element->images->$imgKey->size) }})">
                                                                    <button type="button" class="remove-image"><i class="fa fa-times"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="avatar-edit">
                                                                @if($key == 'banner')
                                                                    <input type="file" class="profilePicUpload" name="image_input[{{ $imgKey }}]" id="fe_el_img_{{ $key }}_{{ $loop->index }}" accept=".png, .jpg, .jpeg, .webp, .mp4">
                                                                    <label for="fe_el_img_{{ $key }}_{{ $loop->index }}" class="bg--primary">{{ __(keyToTitle($imgKey)) }}</label>
                                                                    <small class="mt-2">@lang('Supported files'): <b>WEBP (Primary), JPG, PNG, MP4</b>.
                                                                        @if(@$section->element->images->$imgKey->size)
                                                                            | @lang('Recommended size'): <b>{{@$section->element->images->$imgKey->size}}</b> @lang('px').
                                                                        @endif
                                                                    </small>
                                                                @elseif($key == 'social_icon')
                                                                    <input type="file" class="profilePicUpload" name="image_input[{{ $imgKey }}]" id="fe_el_img_{{ $key }}_{{ $loop->index }}" accept=".png,.jpg,.jpeg,.webp,.svg">
                                                                    <label for="fe_el_img_{{ $key }}_{{ $loop->index }}" class="bg--primary">{{ __(keyToTitle($imgKey)) }}</label>
                                                                    <small class="mt-2">@lang('Supported files'): <b>JPEG, PNG, WebP, SVG</b>.
                                                                        @if(@$section->element->images->$imgKey->size)
                                                                            | @lang('Suggested'): <b>{{@$section->element->images->$imgKey->size}}</b> @lang('px').
                                                                        @endif
                                                                    </small>
                                                                @else
                                                                    <input type="file" class="profilePicUpload" name="image_input[{{ $imgKey }}]" id="fe_el_img_{{ $key }}_{{ $loop->index }}" accept=".png, .jpg, .jpeg">
                                                                    <label for="fe_el_img_{{ $key }}_{{ $loop->index }}" class="bg--primary">{{ __(keyToTitle($imgKey)) }}</label>
                                                                    <small class="mt-2  ">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png')</b>.
                                                                        @if(@$section->element->images->$imgKey->size)
                                                                            | @lang('Will be resized to'):
                                                                            <b>{{@$section->element->images->$imgKey->size}}</b>
                                                                            @lang('px').
                                                                        @endif
                                                                    </small>
                                                                @endif
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

                                    @elseif($content == 'icon')
                                        @php $feElId = 'fe_el_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                        <div class="form-group">
                                            <label for="{{ $feElId }}">{{keyToTitle($k)}}</label>
                                            <div class="input-group">
                                                <input type="text" id="{{ $feElId }}" class="form-control iconPicker icon" autocomplete="off" name="{{ $k }}" value="{{ @$data->data_values->$k }}" @if($key != 'social_icon') required @endif placeholder="{{ $key == 'social_icon' ? 'fab fa-facebook-f / lab la-instagram' : '' }}">
                                                <span class="input-group-text  input-group-addon" data-icon="{{ $key == 'social_icon' ? 'lab la-share-alt' : 'las la-home' }}" role="iconpicker"></span>
                                            </div>
                                        </div>

                                    @else
                                        @if($content == 'textarea')
                                            @php $feElId = 'fe_el_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k); @endphp
                                            @php $optSvgEl = ($key == 'social_icon' && $k == 'custom_icon_svg'); @endphp
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="{{ $feElId }}">{{ __(keyToTitle($k)) }}</label>
                                                    <textarea id="{{ $feElId }}" rows="{{ $optSvgEl ? 6 : 10 }}" class="form-control {{ $optSvgEl ? 'form-control-sm' : '' }}" name="{{ $k }}" dir="ltr" spellcheck="false" @if(!$optSvgEl) required @endif @if($optSvgEl) placeholder="{{ __('Paste &lt;svg&gt;…&lt;/svg&gt; or data-URI &lt;img&gt;') }}" @endif>{{ @$data->data_values->$k }}</textarea>
                                                    @if($optSvgEl)
                                                        <small class="text-muted">@lang('Optional. Saved markup is sanitized (no scripts).')</small>
                                                    @endif
                                                </div>
                                            </div>

                                        @elseif($content == 'textarea-nic')
                                            @php
                                                $isPolicyDetails = ($key == 'policy_pages' && in_array($k, ['details', 'details_2']));
                                                $feElId = 'fe_el_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k);
                                            @endphp
                                            <div class="col-md-12">
                                                <div class="form-group {{ $key == 'policy_pages' ? 'mb-2' : '' }}">
                                                    <label for="{{ $feElId }}" class="{{ $key == 'policy_pages' ? 'small' : '' }}">{{ __(keyToTitle($k)) }}</label>
                                                    @if($isPolicyDetails)
                                                        <textarea id="{{ $feElId }}" rows="12" class="form-control form-control-sm policy-plain-textarea" name="{{ $k }}" dir="auto" placeholder="@lang('Bengali / English – type freely')">{{ @$data->data_values->$k }}</textarea>
                                                        <small class="text-muted d-block mt-1">@lang('Supports Bengali & English.')</small>
                                                    @else
                                                        <textarea id="{{ $feElId }}" rows="{{ $key == 'policy_pages' ? '12' : '10' }}" class="form-control nicEdit {{ $key == 'policy_pages' ? 'form-control-sm' : '' }}" name="{{$k}}" >{{ @$data->data_values->$k}}</textarea>
                                                    @endif
                                                </div>
                                            </div>

                                        @elseif($k == 'select')
                                            @php
                                                $selectName = $content->name;
                                                $feElId = 'fe_el_' . $key . '_sel_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $selectName);
                                            @endphp
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="{{ $feElId }}">{{__(keyToTitle(@$selectName))}}</label>
                                                        <select id="{{ $feElId }}" class="form-control" name="{{ @$selectName }}" required>
                                                            @foreach($content->options as $selectItemKey => $selectOption)
                                                                <option value="{{ $selectItemKey }}" @if(@$data->data_values->$selectName == $selectItemKey) selected @endif>{{ __($selectOption) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                        @else
                                            @php
                                                $isOptional = ($k == 'display_order' || $k == 'url');
                                                $feElId = 'fe_el_' . $key . '_' . $loop->iteration . '_' . preg_replace('/[^a-z0-9]/', '_', $k);
                                            @endphp
                                            <div class="col-md-12">
                                                <div class="form-group {{ $key == 'policy_pages' ? 'mb-2' : '' }}">
                                                    <label for="{{ $feElId }}" class="{{ $key == 'policy_pages' ? 'small' : '' }}">{{ __(keyToTitle($k)) }}</label>
                                                    <input type="{{ $k == 'display_order' ? 'number' : 'text' }}" 
                                                           id="{{ $feElId }}"
                                                           class="form-control {{ $key == 'policy_pages' ? 'form-control-sm' : '' }}" 
                                                           name="{{$k}}" 
                                                           value="{{ @$data->data_values->$k }}" 
                                                           {{ $isOptional ? '' : 'required' }}
                                                           @if($k == 'display_order') min="1" placeholder="1" @endif/>
                                                    @if($k == 'display_order')
                                                        <small class="text-muted">@lang('Lower numbers appear first. Leave empty for auto-ordering.')</small>
                                                    @endif
                                                </div>
                                            </div>

                                        @endif
                                    @endif
                            @endforeach
                            @stack('divend')
                        </div>

                        <div class="form-group {{ $key == 'policy_pages' ? 'mb-0' : '' }}">
                            <button type="submit" class="btn btn--primary {{ $key == 'policy_pages' ? 'btn-sm' : 'w-100 h-45' }}">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@if(isset($key) && $key == 'policy_pages')
@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush
@endif



@push('script-lib')
    <script src="{{ asset('assets/admin/js/fontawesome-iconpicker.js') }}"></script>
@endpush

@push('script')
    <script>

        (function ($) {
            "use strict";
            $('.iconPicker').iconpicker().on('iconpickerSelected', function (e) {
                var v = e.iconpickerValue || '';
                var $inp = $(this).filter('input').length ? $(this) : $(this).closest('.input-group, .form-group').find('input.iconPicker, input.iconpicker-input').first();
                if ($inp.length) {
                    $inp.val(v);
                }
            });
        })(jQuery);
    </script>
@endpush
