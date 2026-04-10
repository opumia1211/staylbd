@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $data = isset($maintenance) && $maintenance ? ($maintenance->data_values ?? (object)[]) : (object)[];
    $title = $data->title ?? __('We\'ll Be Back Soon!');
    $shortDesc = $data->short_description ?? __('We are upgrading our system for a better experience.');
    $showCountdown = $data->show_countdown ?? 1;
    $countdownDatetime = $data->countdown_datetime ?? '';
    $progress = $data->progress_percentage ?? 50;
    $showProgressBar = $data->show_progress_bar ?? 1;
    $estimatedDuration = $data->estimated_duration ?? '';
    $allowEmailSignup = $data->allow_email_signup ?? 1;
    $emailSignupMsg = $data->email_signup_message ?? __('Get notified when we\'re back!');
@endphp
<section class="py-5 maintenance-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="maintenance-card">
                    <div class="maintenance-icon">
                        @include($activeTemplate . 'partials.icon', ['name' => 'tools'])
                    </div>
                    <h1 class="maintenance-title">{{ __($title) }}</h1>
                    <p class="maintenance-short-desc">{{ __($shortDesc) }}</p>

                    @if($showCountdown && $countdownDatetime)
                    <div class="countdown-wrapper mb-4">
                        <div id="countdown" class="countdown-grid">
                            <div class="countdown-item">
                                <span class="countdown-value" id="days">00</span>
                                <span class="countdown-label">@lang('Days')</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value" id="hours">00</span>
                                <span class="countdown-label">@lang('Hours')</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value" id="minutes">00</span>
                                <span class="countdown-label">@lang('Minutes')</span>
                            </div>
                            <div class="countdown-item">
                                <span class="countdown-value" id="seconds">00</span>
                                <span class="countdown-label">@lang('Seconds')</span>
                            </div>
                        </div>
                        <input type="hidden" id="countdownTarget" value="{{ $countdownDatetime }}">
                    </div>
                    @endif

                    @if($showProgressBar)
                    <div class="progress-wrapper mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="progress-label">@lang('Progress')</span>
                            <span class="progress-percent">{{ $progress }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @if($estimatedDuration)
                        <small class="text-muted mt-1 d-block">{{ __($estimatedDuration) }}</small>
                        @endif
                    </div>
                    @endif

                    <div class="maintenance-description">
                        @php echo $data->description ?? '' @endphp
                    </div>

                    @if($allowEmailSignup)
                    <div class="maintenance-email-form mt-4">
                        <p class="mb-2">{{ __($emailSignupMsg) }}</p>
                        <form class="maintenance-subscribe-form" method="POST" action="{{ route('subscribe') }}">
                            @csrf
                            <div class="input-group">
                                <input type="email" name="email" class="form-control form-control-lg" placeholder="@lang('Enter your email')" required>
                                <button type="submit" class="btn btn--primary btn-lg px-4">@lang('Notify Me')</button>
                            </div>
                            <div class="subscribe-message mt-2"></div>
                        </form>
                    </div>
                    @endif

                    @if(!empty($data->contact_email) || !empty($data->contact_phone) || !empty($data->social_facebook) || !empty($data->social_twitter) || !empty($data->social_instagram) || !empty($data->social_linkedin))
                    <div class="maintenance-contact mt-4 pt-4 border-top">
                        <h6 class="mb-3">@lang('Stay Connected')</h6>
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-center">
                            @if(!empty($data->contact_email))
                            <a href="mailto:{{ $data->contact_email }}" class="contact-link">@include($activeTemplate . 'partials.icon', ['name' => 'envelope', 'class' => 'me-1']){{ $data->contact_email }}</a>
                            @endif
                            @if(!empty($data->contact_phone))
                            <a href="tel:{{ $data->contact_phone }}" class="contact-link">@include($activeTemplate . 'partials.icon', ['name' => 'phone', 'class' => 'me-1']){{ $data->contact_phone }}</a>
                            @endif
                            @if(!empty($data->social_facebook))
                            <a href="{{ $data->social_facebook }}" target="_blank" rel="noopener noreferrer" class="social-link" title="Facebook">@include($activeTemplate . 'partials.icon', ['name' => 'facebook-f'])</a>
                            @endif
                            @if(!empty($data->social_twitter))
                            <a href="{{ $data->social_twitter }}" target="_blank" rel="noopener noreferrer" class="social-link" title="Twitter">@include($activeTemplate . 'partials.icon', ['name' => 'twitter'])</a>
                            @endif
                            @if(!empty($data->social_instagram))
                            <a href="{{ $data->social_instagram }}" target="_blank" rel="noopener noreferrer" class="social-link" title="Instagram">@include($activeTemplate . 'partials.icon', ['name' => 'instagram'])</a>
                            @endif
                            @if(!empty($data->social_linkedin))
                            <a href="{{ $data->social_linkedin }}" target="_blank" rel="noopener noreferrer" class="social-link" title="LinkedIn">@include($activeTemplate . 'partials.icon', ['name' => 'linkedin-in'])</a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush

@if($showCountdown && $countdownDatetime)
@push('script')
<script>
(function() {
    var target = document.getElementById('countdownTarget');
    if (!target || !target.value) return;
    var countTo = new Date(target.value).getTime();
    function update() {
        var now = new Date().getTime();
        var diff = countTo - now;
        if (diff <= 0) {
            document.getElementById('days').textContent = '00';
            document.getElementById('hours').textContent = '00';
            document.getElementById('minutes').textContent = '00';
            document.getElementById('seconds').textContent = '00';
            return;
        }
        var d = Math.floor(diff / (1000 * 60 * 60 * 24));
        var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var s = Math.floor((diff % (1000 * 60)) / 1000);
        document.getElementById('days').textContent = String(d).padStart(2, '0');
        document.getElementById('hours').textContent = String(h).padStart(2, '0');
        document.getElementById('minutes').textContent = String(m).padStart(2, '0');
        document.getElementById('seconds').textContent = String(s).padStart(2, '0');
    }
    update();
    setInterval(update, 1000);
})();
</script>
@endpush
@endif

@if($allowEmailSignup)
@push('script')
<script>
document.querySelector('.maintenance-subscribe-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    var form = this;
    var msgEl = form.querySelector('.subscribe-message');
    var btn = form.querySelector('button[type="submit"]');
    var originalText = btn ? btn.textContent : '';
    if (btn) { btn.disabled = true; btn.textContent = '...'; }
    var formData = new FormData(form);
    fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '' },
        body: formData
    }).then(function(r) { return r.json().catch(function() { return {}; }); })
    .then(function(data) {
        if (msgEl) {
            if (data.success) {
                msgEl.innerHTML = '<span class="text-success">' + (data.success || 'Success!') + '</span>';
                form.reset();
            } else if (data.error) {
                var err = Array.isArray(data.error) ? data.error : (typeof data.error === 'object' ? (data.error.email ? data.error.email[0] : JSON.stringify(data.error)) : data.error);
                msgEl.innerHTML = '<span class="text-danger">' + err + '</span>';
            }
        }
    }).catch(function() { if (msgEl) msgEl.innerHTML = '<span class="text-danger">Something went wrong.</span>'; })
    .finally(function() { if (btn) { btn.disabled = false; btn.textContent = originalText; } });
});
</script>
@endpush
@endif
@endsection
