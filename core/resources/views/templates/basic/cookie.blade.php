@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card custom--card">
                        <div class="card-header">
                            <h4 class="mb-0">{{ __($pageTitle) }}</h4>
                        </div>
                        <div class="card-body">
                            @php echo ($cookie->data_values->description ?? '') @endphp
                            @php $cookieData = $cookie ?? \App\Models\Frontend::where('data_keys', 'cookie.data')->first(); @endphp
                            @if($cookieData && ($cookieData->data_values->status ?? 0) == \App\Constants\Status::ENABLE && ($cookieData->data_values->show_preferences_link ?? 1) != 0)
                            <div class="mt-4 pt-4 border-top">
                                <a href="{{ route('cookie.revoke') }}" class="btn btn--base">{{ __($cookieData->data_values->preferences_link_text ?? __('Cookie Preferences')) }}</a>
                                <small class="d-block mt-2 text-muted">@lang('Change your cookie consent at any time')</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
