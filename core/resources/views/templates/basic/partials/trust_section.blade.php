@php
    $homeData = getCachedHomeSectionData();
    $settings = $homeData['settings'];
    if (!($settings->trust_section_enabled ?? 1)) return;
    $trustElements = $homeData['trust_elements'];
    if ($trustElements->isEmpty()) return;
@endphp
<section class="trust-section-pro" aria-label="@lang('Trust & Assurance')">
    <div class="container">
        <div class="trust-section-pro__grid">
            @foreach($trustElements as $el)
                @php $dv = $el->data_values ?? (object)[]; $url = $dv->url ?? '#'; @endphp
                @php
                    $detail = $dv->short_detail ?? '';
                    $isPlaceholder = $detail && (stripos($detail, 'aliquam') !== false || stripos($detail, 'lorem ipsum') !== false || stripos($detail, 'elit congue') !== false);
                @endphp
                <a href="{{ $url }}" class="trust-section-pro__item {{ $url === '#' ? 'trust-section-pro__item--no-link' : '' }}">
                    <span class="trust-section-pro__icon">@include($activeTemplate . 'partials.icon', ['name' => 'circle-check', 'class' => 'w-6 h-6'])</span>
                    <span class="trust-section-pro__title">{{ __($dv->title ?? '') }}</span>
                    @if(!empty($detail) && !$isPlaceholder)
                        <span class="trust-section-pro__detail">{{ __($detail) }}</span>
                    @elseif(empty($detail))
                        <span class="trust-section-pro__detail">{{ __('Trusted service') }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush
