@php
    $footerData   = getCachedFooterData();
    $companyInfo    = $footerData['footer_company_info'] ?? null;
    $socialElement  = $footerData['social_element'] ?? collect();
    $policyPages    = $footerData['policy_pages'] ?? collect();
    $quickLinks     = $footerData['footer_quick_links'] ?? collect();
    $footerContent  = $footerData['footer_content'] ?? null;
    $categories     = \App\Models\Category::active()->limit(6)->get();
@endphp

<footer class="bg-light border-top mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <a href="{{ route('home') }}" class="d-inline-block mb-3">
                    <img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="Logo" style="height: 40px;">
                </a>
                <p class="text-muted mb-3">
                    {{ __($companyInfo->data_values->about_text ?? 'Premium multipurpose e-commerce platform.') }}
                </p>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($socialElement as $social)
                        @php
                            $dv = $social->data_values ?? (object)[];
                            if (is_array($dv)) $dv = (object)$dv;
                            $socialUrl = trim((string)($dv->url ?? '')) ?: '#';
                            $iconHtml = trim((string)($dv->icon ?? ''));
                            $socialLabel = trim((string)($dv->title ?? '')) ?: __('Social link');
                        @endphp
                        <a href="{{ $socialUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabel }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            @if($iconHtml !== '')
                                {!! $iconHtml !!}
                            @else
                                @include($activeTemplate . 'partials.icon', ['name' => 'globe', 'sizePx' => 16, 'class' => 'text-current'])
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="fw-semibold mb-3">{{ __('Categories') }}</h6>
                <ul class="list-unstyled mb-0">
                    @foreach ($categories as $cat)
                        <li class="mb-2">
                            <a href="{{ route('category.products', [slug($cat->name), $cat->id]) }}" class="text-decoration-none text-muted">
                                {{ __($cat->name) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="fw-semibold mb-3">{{ __('Company') }}</h6>
                <ul class="list-unstyled mb-0">
                    @foreach ($policyPages as $policy)
                        <li class="mb-2">
                            <a href="{{ route('policy.pages.short', $policy->id) }}" class="text-decoration-none text-muted">
                                {{ __($policy->data_values->title) }}
                            </a>
                        </li>
                    @endforeach
                    <li class="mb-2">
                        <a href="{{ route('contact') }}" class="text-decoration-none text-muted">{{ __('Contact Us') }}</a>
                    </li>
                </ul>
            </div>

            <div class="col-12 col-lg-4">
                <h6 class="fw-semibold mb-3">{{ __('Newsletter') }}</h6>
                <p class="text-muted mb-3">{{ __('Get updates on new products and special offers.') }}</p>
                <form action="{{ route('subscribe') }}" method="POST" class="input-group">
                    @csrf
                    <input type="email" name="email" class="form-control" placeholder="{{ __('Your email address') }}" required>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center justify-content-center" aria-label="{{ __('Subscribe') }}">
                        @include($activeTemplate . 'partials.icon', ['name' => 'paper-plane', 'sizePx' => 16, 'class' => 'text-current'])
                    </button>
                </form>
                @if(!empty($companyInfo->data_values->contact_phone))
                    <p class="text-muted mb-0 mt-3">
                        <span class="fw-semibold">{{ __('Questions? Call us') }}:</span>
                        {{ $companyInfo->data_values->contact_phone }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="border-top">
        <div class="container py-3 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
            <p class="text-muted small mb-0">
                @php
                    $copyrightText = $footerContent && trim($footerContent->data_values->copyright_text ?? '') !== ''
                        ? $footerContent->data_values->copyright_text
                        : __('Copyright') . ' &copy; ' . date('Y') . ' ' . gs('site_name') . '. ' . __('All Right Reserved.');
                    echo str_replace('{year}', date('Y'), $copyrightText);
                @endphp
            </p>
            <div class="d-flex align-items-center gap-3">
                 <img src="https://zenis-laravel.preoit.com/assets/images/footer_payment_icon_1.jpg" style="height: 24px;" alt="Payments">
                 <img src="https://zenis-laravel.preoit.com/assets/images/footer_payment_icon_2.jpg" style="height: 24px;" alt="Payments">
                 <img src="https://zenis-laravel.preoit.com/assets/images/footer_payment_icon_3.jpg" style="height: 24px;" alt="Payments">
            </div>
        </div>
    </div>
</footer>
