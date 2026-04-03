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
                    <span class="trust-section-pro__icon">@include($activeTemplate . 'partials.icon', ['name' => 'check-circle'', 'class' => '{{ $dv->icon ?? \'las }}'])</span>
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
<style>
.trust-section-pro { padding: 1.75rem 0; background: rgba(255,255,255,0.7); border-top: 1px solid rgba(0,0,0,.05); }
.trust-section-pro__grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
@media (min-width: 768px) { .trust-section-pro__grid { grid-template-columns: repeat(4, 1fr); } }
@media (min-width: 992px) { .trust-section-pro__grid { grid-template-columns: repeat(5, 1fr); } }
.trust-section-pro__item { display: flex; flex-direction: column; align-items: center; text-align: center; padding: 1.25rem 1rem; border-radius: 12px; text-decoration: none; color: #334155; background: #fff; border: 1px solid rgba(0,0,0,.04); transition: border-color .2s, box-shadow .2s; }
.trust-section-pro__item:hover { border-color: rgba(99,102,241,.15); box-shadow: 0 4px 12px rgba(0,0,0,.05); color: #1a1a2e; }
.trust-section-pro__item--no-link { pointer-events: none; cursor: default; }
.trust-section-pro__icon { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, var(--base, #6366f1), rgba(99,102,241,.8)); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 0.5rem; }
.trust-section-pro__title { font-size: 0.9rem; font-weight: 700; margin-bottom: 0.25rem; }
.trust-section-pro__detail { font-size: 0.75rem; color: #64748b; line-height: 1.3; }
</style>
@endpush
