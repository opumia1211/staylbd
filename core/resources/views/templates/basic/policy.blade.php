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

{{-- inline style moved to critical-storefront.css --}}

@endpush
@endsection
