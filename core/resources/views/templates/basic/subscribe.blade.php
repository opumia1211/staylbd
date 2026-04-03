@extends($activeTemplate . 'layouts.frontend')

@section('content')
<section class="subscribe-page-section py-5">
    <div class="container">
        <nav class="mb-3" aria-label="Breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('Home')</a></li>
                <li class="breadcrumb-item active" aria-current="page">@lang('Subscribe to Newsletter')</li>
            </ol>
        </nav>
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h4 mb-2">@lang('Subscribe to our newsletter')</h1>
                        <p class="text-muted small mb-4">@lang('Get updates on new products and offers. Enter your email below.')</p>
                        <form class="newletter-form js-footer-subscribe" action="{{ route('subscribe') }}" method="post" aria-label="@lang('Newsletter subscription')">
                            @csrf
                            <div class="mb-3">
                                <label for="subscribe-email" class="form-label">@lang('Email address')</label>
                                <input type="email" class="form-control form-control-lg subscribe-email" id="subscribe-email" name="email"
                                       placeholder="@lang('Enter Your Email')" required aria-label="@lang('Email address')" autocomplete="email">
                            </div>
                            <button type="submit" class="btn btn--base btn-lg w-100 subscribe-btn" aria-label="@lang('Subscribe')">
                                @include($activeTemplate . 'partials.icon', ['name' => 'paper-plane', 'class' => 'me-1']) @lang('Subscribe')
                            </button>
                            <div class="subscribe-inline-message small mt-2 text-muted" aria-live="polite"></div>
                        </form>
                        <p class="small text-muted mt-3 mb-0">@lang('You can also subscribe from the footer on any page.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
