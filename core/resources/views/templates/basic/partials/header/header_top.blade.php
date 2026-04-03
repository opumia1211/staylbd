@php
    $headerContent = getContent('contact_us.content', true);
@endphp

<div class="header-top bg--base">
    <div class="container">
        <div class="header__top__wrapper">
            <ul>
                <li>
                    <span class="name text--white">@lang('Email: ')</span>
                    <a href="mailto:{{ __(@$headerContent->data_values->contact_email) }}">{{ __(@$headerContent->data_values->contact_email) }}</a>
                </li>
                <li>
                    <span class="name text--white">@lang('Phone: ')</span>
                    <a href="tel:{{ __(@$headerContent->data_values->contact_number) }}">{{ __(@$headerContent->data_values->contact_number) }}</a>
                </li>
            </ul>

            @if($general->multi_language)
                @php
                    $language = App\Models\Language::all();
                @endphp

                <div class="change-language">
                    <select class="language langSel">
                        @foreach ($language as $item)
                            <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>
                                {{ __($item->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>
</div>
