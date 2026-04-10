@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        {{-- Header & Info --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <h5 class="mb-1">@lang('Frontend Templates')</h5>
                        <p class="text-muted mb-0 small">@lang('Select the active template for your site. Changes apply immediately.')</p>
                    </div>
                    <div class="badge bg--primary fs-6 px-3 py-2">
                        @lang('Active'): <strong>{{ ucfirst($general->active_template ?? 'basic') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deployment Note --}}
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading"><i class="las la-info-circle me-1"></i>@lang('When Deploying to Server')</h6>
            <p class="mb-0 small">@lang('Template selection is stored in database. When you deploy, the same template will be used. No manual edit needed.')</p>
            <p class="mb-0 small mt-1">@lang('If you use custom preview images, deploy the folder') <code>assets/images/template-previews/</code> @lang('to your server.')</p>
        </div>

        {{-- Preview Upload Info --}}
        <div class="alert alert-light border mb-4">
            <h6 class="alert-heading"><i class="las la-image me-1"></i>@lang('Custom Preview Images')</h6>
            <p class="mb-0 small">@lang('Hover over a template card and use "Change Preview" to upload a new preview image (JPG/PNG, max 2MB). Use "Reset" to restore the default.')</p>
        </div>

        {{-- Available Templates --}}
        <div class="row g-4">
            @foreach($templates as $temp)
            <div class="col-xl-4 col-md-6">
                <div class="card h-100 {{ ($general->active_template ?? '') == $temp['name'] ? 'border-success border-2' : '' }}">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0"><i class="las la-palette me-2"></i>{{ __(keyToTitle($temp['name'])) }}</h5>
                        @if(($general->active_template ?? '') == $temp['name'])
                        <span class="badge bg-success"><i class="las la-check me-1"></i>@lang('Active')</span>
                        @else
                        <form action="{{ route('admin.frontend.templates.active') }}" method="post" class="d-inline">
                            @csrf
                            <input type="hidden" name="name" value="{{ $temp['name'] }}">
                            <button type="submit" class="btn btn--success btn-sm">
                                <i class="las la-check-double me-1"></i>@lang('Activate')
                            </button>
                        </form>
                        @endif
                    </div>
                    <div class="card-body p-0 position-relative">
                        <div class="template-preview-wrapper position-relative">
                            <img src="{{ $temp['image'] }}" alt="{{ $temp['name'] }}" class="w-100" style="min-height: 200px; object-fit: cover;">
                            <div class="template-preview-actions position-absolute bottom-0 start-0 end-0 p-2 bg-dark bg-opacity-50 d-flex gap-1 justify-content-center flex-wrap">
                                <form action="{{ route('admin.frontend.template.preview.upload') }}" method="post" enctype="multipart/form-data" class="d-inline template-preview-form">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $temp['name'] }}">
                                    <input type="file" name="preview" accept=".jpg,.jpeg,.png" class="d-none template-preview-input">
                                    <button type="button" class="btn btn-sm btn-light template-preview-trigger">
                                        <i class="las la-image me-1"></i>@lang('Change Preview')
                                    </button>
                                </form>
                                @if(!empty($temp['hasCustomPreview']))
                                <form action="{{ route('admin.frontend.template.preview.reset') }}" method="post" class="d-inline" onsubmit="return confirm('@lang('Reset to default preview image?')');">
                                    @csrf
                                    <input type="hidden" name="name" value="{{ $temp['name'] }}">
                                    <button type="submit" class="btn btn-sm btn-outline-light">@lang('Reset')</button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @if(($general->active_template ?? '') == $temp['name'])
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">@lang('Current')</span>
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><code>{{ $temp['name'] }}</code></small>
                            @if(($general->active_template ?? '') == $temp['name'])
                            <a href="{{ url('/') }}" target="_blank" class="btn btn-outline--primary btn-sm">@lang('View Site') <i class="las la-external-link-alt"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if(!empty($extraTemplates))
        <div class="mt-5">
            <h5 class="mb-3">@lang('Additional Templates')</h5>
            <div class="row g-4">
                @foreach($extraTemplates as $temp)
                <div class="col-lg-3 col-md-4 col-6">
                    <div class="card h-100">
                        <div class="card-header bg-secondary">
                            <h6 class="text-white mb-0">{{ __(keyToTitle($temp['name'] ?? 'Template')) }}</h6>
                        </div>
                        <div class="card-body p-0">
                            <img src="{{ $temp['image'] ?? '' }}" alt="@lang('Template')" class="w-100">
                        </div>
                        <div class="card-footer">
                            <a href="{{ $temp['url'] ?? '#' }}" target="_blank" rel="noopener" class="btn btn--primary btn-sm w-100">@lang('Get This') <i class="las la-external-link-alt"></i></a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush

@push('script')
<script>
document.querySelectorAll('.template-preview-trigger').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var form = this.closest('.template-preview-form');
        var input = form.querySelector('.template-preview-input');
        if (input) input.click();
    });
});
document.querySelectorAll('.template-preview-input').forEach(function(input) {
    input.addEventListener('change', function() {
        var form = this.closest('form');
        if (form && this.files && this.files.length) form.submit();
    });
});
</script>
@endpush
