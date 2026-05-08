@extends($activeTemplate . 'layouts.frontend')

@section('content')
<style>
    :root {
        --seller-primary: #10b981;
        --seller-secondary: #6366f1;
        --seller-glass: rgba(255, 255, 255, 0.85);
    }
    .seller-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 100px 0 80px;
        position: relative;
        overflow: hidden;
    }
    .seller-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: url('{{ asset('brain/73de526f-082a-4669-9af2-d7b8f73ac19e/seller_onboarding_hero_1778259288850.png') }}');
        background-size: cover;
        background-position: center;
        opacity: 0.25;
        mix-blend-mode: overlay;
    }
    .seller-form-card {
        background: var(--seller-glass);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        margin-top: -60px;
        position: relative;
        z-index: 10;
    }
    .benefit-icon {
        width: 48px;
        height: 48px;
        background: rgba(16, 185, 129, 0.1);
        color: var(--seller-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    .form-control-elite {
        background: rgba(255, 255, 255, 0.5) !important;
        border: 1px solid rgba(0, 0, 0, 0.1) !important;
        padding: 12px 18px;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .form-control-elite:focus {
        background: #fff !important;
        border-color: var(--seller-primary) !important;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15) !important;
    }
    .btn-seller-submit {
        background: linear-gradient(135deg, var(--seller-primary) 0%, #059669 100%);
        color: white;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-seller-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
        color: white;
    }
</style>

<div class="seller-hero text-center text-white">
    <div class="container">
        <h1 class="display-4 font-weight-bold mb-3">@lang('Start Selling on StayLBD')</h1>
        <p class="lead mb-0 opacity-80 max-w-2xl mx-auto">@lang('Join thousands of successful businesses and reach millions of customers across the nation.')</p>
    </div>
</div>

<section class="seller-registration-section pb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-9">
                <div class="seller-form-card p-4 p-lg-5">
                    <div class="row g-5">
                        <!-- Left Side: Benefits -->
                        <div class="col-lg-5 d-none d-lg-block border-end">
                            <h2 class="h4 mb-4 font-weight-bold">@lang('Why choose us?')</h2>
                            
                            <div class="benefit-item mb-4">
                                <div class="benefit-icon">
                                    <i class="las la-chart-line la-2x"></i>
                                </div>
                                <h3 class="h6 mb-2">@lang('Massive Reach')</h3>
                                <p class="text-muted small">@lang('Connect with a vast audience and grow your business exponentialy.')</p>
                            </div>

                            <div class="benefit-item mb-4">
                                <div class="benefit-icon">
                                    <i class="las la-wallet la-2x"></i>
                                </div>
                                <h3 class="h6 mb-2">@lang('Secure Payments')</h3>
                                <p class="text-muted small">@lang('Get paid on time with our secure and automated payment systems.')</p>
                            </div>

                            <div class="benefit-item">
                                <div class="benefit-icon">
                                    <i class="las la-headset la-2x"></i>
                                </div>
                                <h3 class="h6 mb-2">@lang('Dedicated Support')</h3>
                                <p class="text-muted small">@lang('Our team is here to help you every step of the way, 24/7.')</p>
                            </div>
                        </div>

                        <!-- Right Side: Form -->
                        <div class="col-lg-7">
                            <form action="{{ route('contact') }}" method="POST" class="verify-gcaptcha">
                                @csrf
                                <input type="hidden" name="subject" value="Seller Application">
                                
                                <h2 class="h4 mb-4 font-weight-bold">@lang('Register Your Shop')</h2>
                                
                                <div class="row">
                                    <div class="col-sm-12 mb-3">
                                        <label class="form-label text-muted small font-weight-bold">@lang('Shop Name')</label>
                                        <input type="text" name="name" class="form-control form-control-elite" placeholder="@lang('Enter your shop name')" required>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label text-muted small font-weight-bold">@lang('Owner Name')</label>
                                        <input type="text" name="contact_name" class="form-control form-control-elite" placeholder="@lang('Full name')" required>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label class="form-label text-muted small font-weight-bold">@lang('Email Address')</label>
                                        <input type="email" name="email" class="form-control form-control-elite" placeholder="@lang('Email for contact')" required>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label class="form-label text-muted small font-weight-bold">@lang('Phone Number')</label>
                                        <input type="tel" name="phone" class="form-control form-control-elite" placeholder="@lang('WhatsApp or mobile number')" required>
                                    </div>
                                    <div class="col-sm-12 mb-4">
                                        <label class="form-label text-muted small font-weight-bold">@lang('Business Description')</label>
                                        <textarea name="message" class="form-control form-control-elite" rows="3" placeholder="@lang('Tell us about what you sell...')"></textarea>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <x-captcha class="form-control-elite" />
                                </div>

                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label small text-muted" for="termsCheck">
                                        @lang('I agree to the') <a href="#" class="text-primary">@lang('Seller Terms & Conditions')</a>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-seller-submit w-100">
                                    @lang('Submit Application')
                                </button>
                                
                                <p class="text-center mt-3 small text-muted">
                                    @lang('Already have an account?') <a href="{{ route('user.login') }}" class="text-primary font-weight-bold">@lang('Login here')</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
