@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-8 col-xl-9">
            <div class="card b-radius--10">
                <div class="card-body">
                    <h5 class="mb-4"><i class="las la-puzzle-piece me-2 text--primary"></i>@lang('Add New Extension')</h5>
                    <p class="text-muted small mb-4">@lang('Add a new script/plugin (e.g. TikTok Pixel, LinkedIn Insight, or any custom HTML/JS) without editing code. Use') <code>@{{ key }}</code> @lang('in the script for placeholders.')</p>
                    <form action="{{ route('admin.extensions.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">@lang('Extension Name') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form--control" name="name" value="{{ old('name') }}" required placeholder="@lang('e.g. TikTok Pixel')">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label">@lang('Unique Key (act)') <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form--control" name="act" value="{{ old('act') }}" required placeholder="e.g. tiktok-pixel" pattern="[a-z0-9\-]+" maxlength="60">
                                <small class="text-muted">@lang('Only small letters, numbers and hyphen. Must be unique.')</small>
                                @error('act')
                                    <span class="text-danger small d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Description') <span class="text-muted">(@lang('optional'))</span></label>
                            <textarea class="form-control form--control" name="description" rows="2" placeholder="@lang('Short description for admin list')">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Script (HTML/JS)') <span class="text-danger">*</span></label>
                            <textarea class="form-control form--control font-monospace" name="script" rows="10" required placeholder="<script>
  // Use @{{ key_name }} for values from shortcode
  console.log('ID: @{{ pixel_id }}');
</script>">{{ old('script') }}</textarea>
                            <small class="text-muted">@lang('Use') <code>@{{ key_name }}</code> @lang('for each shortcode key. Example:') <code>@{{ pixel_id }}</code></small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Shortcode keys') <span class="text-muted">(@lang('one per line'))</span></label>
                            <textarea class="form-control form--control" name="shortcode_keys" rows="5" placeholder="pixel_id|Pixel ID&#10;api_key|API Key">{{ old('shortcode_keys') }}</textarea>
                            <small class="text-muted">@lang('Format:') <code>key|Label</code>. @lang('These keys will appear in Configure modal. Use same keys in script as') <code>@{{ key }}</code></small>
                        </div>
                        <div class="form-group mb-4">
                            <label class="form-label">@lang('Image filename') <span class="text-muted">(@lang('optional'))</span></label>
                            <input type="text" class="form-control form--control" name="image" value="{{ old('image', $defaultImage) }}" placeholder="{{ $defaultImage }}">
                            <small class="text-muted">@lang('File in') assets/images/extensions/ (e.g. {{ $defaultImage }})</small>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn--primary">@lang('Add Extension')</button>
                        <a href="{{ route('admin.extensions.index') }}" class="btn btn--dark">@lang('Cancel')</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-xl-3">
            <div class="card b-radius--10">
                <div class="card-body">
                    <h6 class="mb-3"><i class="las la-lightbulb text--warning me-2"></i>@lang('Tips')</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2">• @lang('Unique key (act) cannot be changed later; choose carefully.')</li>
                        <li class="mb-2">• @lang('In script, use') <code>@{{ key }}</code> @lang('for each shortcode key.')</li>
                        <li class="mb-2">• @lang('After adding, enable the extension and use Configure to set values.')</li>
                        <li class="mb-2">• @lang('New extension will appear under category "General" until you enable it.')</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
