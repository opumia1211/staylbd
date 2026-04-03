@extends($activeTemplate . 'layouts.frontend')
@section('content')
<section class="policy-page py-3 py-md-4 wow fadeInUp" data-wow-duration="0.35s" data-wow-delay="0.05s">
    <div class="container">
        <div class="row justify-content-center g-2 g-md-3">
            <div class="col-12">
                <div class="policy-card shadow-sm rounded-3 border-0 bg-white overflow-hidden">
                    <div class="policy-header px-3 px-md-4 py-2 border-bottom bg-light">
                        <h1 class="policy-title h6 mb-0 text-dark">{{ __($pageTitle) }}</h1>
                    </div>
                    <div class="policy-body px-3 px-md-4 py-3 py-md-4">
                        @if(!empty($safeDetails) || !empty($safeDetails2))
                            @php $hasBoth = !empty($safeDetails) && !empty($safeDetails2); @endphp
                            <div class="row g-2 g-md-3 policy-bilingual-row">
                                @if(!empty($safeDetails))
                                    <div class="{{ $hasBoth ? 'col-md-6' : 'col-12' }}">
                                        <div class="policy-col">
                                            @if($hasBoth)<h6 class="policy-col-label small text-uppercase text-muted mb-2">@lang('Details') 1</h6>@endif
                                            <div class="policy-content prose">{!! $safeDetails !!}</div>
                                        </div>
                                    </div>
                                @endif
                                @if(!empty($safeDetails2))
                                    <div class="{{ $hasBoth ? 'col-md-6' : 'col-12' }}">
                                        <div class="policy-col">
                                            @if($hasBoth)<h6 class="policy-col-label small text-uppercase text-muted mb-2">@lang('Details') 2</h6>@endif
                                            <div class="policy-content prose">{!! $safeDetails2 !!}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-muted mb-0 small">@lang('No content available for this policy yet.')</p>
                        @endif
                    </div>
                    <div class="policy-footer px-3 px-md-4 py-2 bg-light border-top small text-muted">
                        <a href="{{ url()->previous() }}" class="text-decoration-none">← @lang('Back')</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('style')
<style>
    .policy-page { font-size: 0.9rem; }
    .policy-page .policy-content { font-size: 0.875rem; line-height: 1.6; color: #333; }
    .policy-page .policy-content p { margin-bottom: 0.75rem; font-size: 0.875rem; }
    .policy-page .policy-content h1, .policy-page .policy-content h2, .policy-page .policy-content h3,
    .policy-page .policy-content h4, .policy-page .policy-content h5, .policy-page .policy-content h6 {
        margin-top: 1rem; margin-bottom: 0.5rem; font-weight: 600; font-size: 1rem; color: #1a1a1a;
    }
    .policy-page .policy-content h2 { font-size: 1.1rem; }
    .policy-page .policy-content h3 { font-size: 1rem; }
    .policy-page .policy-content ul, .policy-page .policy-content ol { margin-bottom: 0.75rem; padding-left: 1.25rem; font-size: 0.875rem; }
    .policy-page .policy-content li { margin-bottom: 0.25rem; }
    .policy-page .policy-content a { color: var(--base-color, #0d6efd); text-decoration: underline; }
    .policy-page .policy-content a:hover { text-decoration: none; }
    .policy-page .policy-content blockquote { border-left: 3px solid #dee2e6; padding-left: 0.75rem; margin: 0.75rem 0; font-size: 0.85rem; color: #555; }
    .policy-page .policy-content table { width: 100%; border-collapse: collapse; margin-bottom: 0.75rem; font-size: 0.8rem; }
    .policy-page .policy-content table th, .policy-page .policy-content table td { border: 1px solid #dee2e6; padding: 0.4rem 0.6rem; }
    .policy-page .policy-content table th { background: #f8f9fa; font-weight: 600; }
    .policy-page .policy-content hr { border: 0; border-top: 1px solid #dee2e6; margin: 1rem 0; }
    .policy-col { min-height: 1px; }
    .policy-col-label { font-size: 0.7rem; letter-spacing: 0.05em; }
    @media (max-width: 767px) {
        .policy-page { padding-top: 0.75rem !important; padding-bottom: 1rem !important; }
        .policy-page .policy-body { padding: 0.75rem 1rem !important; }
        .policy-page .policy-content { font-size: 0.8rem; }
        .policy-bilingual-row .col-md-6 { margin-bottom: 1rem; }
        .policy-bilingual-row .col-md-6:last-child { margin-bottom: 0; }
    }
</style>
@endpush
@endsection
