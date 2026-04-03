@extends($activeTemplate . 'layouts.frontend')

@section('content')
<section class="seller-apply-section py-5">
    <div class="container">
        <nav class="mb-3" aria-label="@lang('Breadcrumb')">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">@lang('Home')</a></li>
                <li class="breadcrumb-item active" aria-current="page">@lang('Seller account')</li>
            </ol>
        </nav>
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h4 mb-3">{{ $pageTitle }}</h1>
                        <p class="text-muted mb-0">@lang('Seller registration and product upload will be available here in a future update. Thank you for your interest.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
