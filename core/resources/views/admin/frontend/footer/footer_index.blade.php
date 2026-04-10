@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3 px-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h5 class="mb-0 small fw-bold"><i class="las la-shoe-prints me-1"></i>{{ $pageTitle }}</h5>
                        <p class="text-muted small mb-0 mt-1">@lang('Choose a section to edit. Each has its own page for easier management.')</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.frontend.sections.social_icon') }}" class="btn btn-sm btn-outline--primary">
                            <i class="las la-share-alt me-1"></i> @lang('Social Icons')
                        </a>
                        <a href="{{ route('admin.frontend.sections.footer.all') }}" class="btn btn--primary btn-sm">
                            <i class="las la-th-large me-1"></i> @lang('Edit all in one page')
                        </a>
                    </div>
                </div>

                <div class="row g-2">
                    @foreach($sections as $slug => $info)
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.frontend.sections.footer.section', $slug) }}" class="text-decoration-none d-block">
                            <div class="card border-0 bg-light hover-shadow h-100 footer-section-card">
                                <div class="card-body py-2 px-2 d-flex align-items-center">
                                    <span class="avatar avatar--md bg--primary bg-opacity-10 text--primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-2">
                                        <i class="{{ $info['icon'] ?? 'las la-cog' }}"></i>
                                    </span>
                                    <div class="flex-grow-1 min-w-0">
                                        <span class="small fw-semibold text-dark d-block text-truncate">{{ __($info['title']) }}</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">@lang('Edit')</span>
                                    </div>
                                    <i class="las la-chevron-right text-muted flex-shrink-0 small"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ route('admin.frontend.sections.social_icon') }}" class="text-decoration-none d-block">
                            <div class="card border-0 bg-light hover-shadow h-100 footer-section-card">
                                <div class="card-body py-2 px-2 d-flex align-items-center">
                                    <span class="avatar avatar--md bg--primary bg-opacity-10 text--primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 me-2">
                                        <i class="las la-share-alt"></i>
                                    </span>
                                    <div class="flex-grow-1 min-w-0">
                                        <span class="small fw-semibold text-dark d-block text-truncate">@lang('Social Icons')</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">@lang('Edit')</span>
                                    </div>
                                    <i class="las la-chevron-right text-muted flex-shrink-0 small"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')

{{-- inline style moved to critical-admin.css --}}

@endpush
@endsection
