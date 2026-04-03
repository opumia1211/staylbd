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
    <style>
    .custom-site-msg { padding: 10px 16px; background: var(--base-color, #0d9488); color: #fff; text-align: center; font-size: 14px; }
    .custom-site-msg--top { position: sticky; top: 0; z-index: 9999; }
    .custom-site-msg--bottom { position: relative; margin-top: auto; }
    .custom-site-msg__item { margin: 0; }
    .custom-site-msg__text { margin-right: 6px; }
    .custom-site-msg__link { color: #fff; text-decoration: underline; font-weight: 600; }
    .custom-site-msg__link:hover { color: rgba(255,255,255,0.9); }
    .custom-site-msg--banner { position: fixed; left: 50%; top: 50%; transform: translate(-50%, -50%); z-index: 99998; max-width: 90%; width: 420px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); padding: 20px; }
    .custom-site-msg__item--banner { padding: 0; }
    </style>
@endif
