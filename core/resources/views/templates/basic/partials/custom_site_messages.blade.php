@php
    $customMessages = getCustomSiteMessages();
    $byPosition = $customMessages->groupBy(function ($m) { return $m->data_values->position ?? 'banner_center'; });
@endphp
@if($customMessages->isNotEmpty())
    @foreach($byPosition as $position => $items)
        @if($position === 'top_bar')
            <div class="custom-site-msg custom-site-msg--top" role="region" aria-label="@lang('Site message')">
                @foreach($items as $row)
                    @php $d = $row->data_values; @endphp
                    <div class="custom-site-msg__item">
                        <span class="custom-site-msg__text">{{ $d->message ?? '' }}</span>
                        @if(!empty($d->link_url))
                            <a href="{{ $d->link_url }}" target="_blank" rel="noopener" class="custom-site-msg__link">{{ $d->link_text ?? __('Read more') }}</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @elseif($position === 'bottom_bar')
            {{-- ফুটার নিচে বার দেখানো বন্ধ – রিমুভ করা হয়েছে --}}
        @else
            {{-- banner_center: fixed center banner --}}
            <div class="custom-site-msg custom-site-msg--banner" role="region" aria-label="@lang('Site message')">
                @foreach($items as $row)
                    @php $d = $row->data_values; @endphp
                    <div class="custom-site-msg__item custom-site-msg__item--banner">
                        <span class="custom-site-msg__text">{{ $d->message ?? '' }}</span>
                        @if(!empty($d->link_url))
                            <a href="{{ $d->link_url }}" target="_blank" rel="noopener" class="custom-site-msg__link">{{ $d->link_text ?? __('Read more') }}</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
    
{{-- inline style moved to critical-storefront.css --}}

@endif
