@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-12">
        {{-- Quick Status Overview --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-1"><i class="las la-link me-2"></i>@lang('App URL')</h6>
                        <code class="small">{{ config('app.url') }}</code>
                        <small class="text-muted d-block mt-1">@lang('Callback URLs use this base. Update APP_URL in .env when deploying.')</small>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @php
                            $googleOk = !empty(env('GOOGLE_CLIENT_ID')) && !empty(env('GOOGLE_CLIENT_SECRET'));
                            $fbOk = !empty(env('FACEBOOK_CLIENT_ID')) && !empty(env('FACEBOOK_CLIENT_SECRET'));
                            $twitterOk = !empty(env('TWITTER_CLIENT_ID')) && !empty(env('TWITTER_CLIENT_SECRET'));
                            $appleOk = !empty(env('APPLE_CLIENT_ID')) && !empty(env('APPLE_CLIENT_SECRET'));
                            $githubOk = !empty(env('GITHUB_CLIENT_ID')) && !empty(env('GITHUB_CLIENT_SECRET'));
                        @endphp
                        <span class="badge {{ $googleOk ? 'bg-success' : 'bg-secondary' }}"><i class="lab la-google me-1"></i>@lang($googleOk ? 'Configured' : 'Not Set')</span>
                        <span class="badge {{ $fbOk ? 'bg-success' : 'bg-secondary' }}"><i class="lab la-facebook-f me-1"></i>@lang($fbOk ? 'Configured' : 'Not Set')</span>
                        <span class="badge {{ $twitterOk ? 'bg-success' : 'bg-secondary' }}"><i class="lab la-twitter me-1"></i>@lang($twitterOk ? 'Configured' : 'Not Set')</span>
                        <span class="badge {{ $appleOk ? 'bg-success' : 'bg-secondary' }}"><i class="lab la-apple me-1"></i>@lang($appleOk ? 'Configured' : 'Not Set')</span>
                        <span class="badge {{ $githubOk ? 'bg-success' : 'bg-secondary' }}"><i class="lab la-github me-1"></i>@lang($githubOk ? 'Configured' : 'Not Set')</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Deployment Note --}}
        <div class="alert alert-info mb-4">
            <h6 class="alert-heading"><i class="las la-info-circle me-1"></i>@lang('When Deploying to Server')</h6>
            <p class="mb-0 small">@lang('Create new OAuth apps (or add production URLs) on Google/Facebook/Twitter/Apple developer consoles for your production domain. Then enter the new credentials here and save. The callback URLs below show what to add in each provider\'s settings.')</p>
        </div>

        @php $googleCallback = $callbackUrls['google'] ?? ''; @endphp
        @if($googleCallback)
        <div class="alert alert-warning mb-4">
            <h6 class="alert-heading"><i class="las la-exclamation-triangle me-1"></i>@lang('Google "redirect_uri_mismatch" fix')</h6>
            <p class="mb-2 small">@lang('If users see "Error 400: redirect_uri_mismatch", add this <strong>exact</strong> URL in Google Cloud Console:')</p>
            <p class="mb-2"><code class="d-inline-block bg-white px-2 py-1 rounded">{{ $googleCallback }}</code> <button type="button" class="btn btn-sm btn-outline-secondary ms-2 copy-callback" data-target="googleCallback"><i class="las la-copy"></i> @lang('Copy')</button></p>
            <p class="mb-0 small">@lang('APIs &amp; Services → Credentials → Your OAuth 2.0 Client ID → Authorized redirect URIs → Add URI → paste (no space, no trailing slash) → Save.')</p>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.setting.social.login') }}">
        @csrf
        <div class="row">
            {{-- Google --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="lab la-google me-2" style="font-size: 1.5rem;"></i>
                        <h6 class="mb-0">Google Login</h6>
                        <span class="badge {{ env('GOOGLE_LOGIN_ENABLED') == '1' ? 'bg-success' : 'bg-secondary' }} ms-auto">@lang(env('GOOGLE_LOGIN_ENABLED') == '1' ? 'Enabled' : 'Disabled')</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" class="form-control" name="GOOGLE_CLIENT_ID" value="{{ env('GOOGLE_CLIENT_ID') }}" placeholder="xxxxx.apps.googleusercontent.com">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Client Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control secret-input" name="GOOGLE_CLIENT_SECRET" value="{{ env('GOOGLE_CLIENT_SECRET') }}" placeholder="GOCSPX-xxxxx" id="google_secret">
                                <button type="button" class="btn btn-outline-secondary secret-toggle" data-target="google_secret" data-show="{{ __("Show") }}" data-hide="{{ __("Hide") }}" title="{{ __("Show") }}"><i class="las la-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Callback URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $callbackUrls['google'] ?? '' }}" readonly id="googleCallback">
                                <button type="button" class="btn btn-outline-secondary copy-callback" data-target="googleCallback"><i class="las la-copy"></i></button>
                            </div>
                            <small class="text-muted d-block mt-1">@lang('In Google Cloud Console go to: APIs &amp; Services → Credentials → your OAuth 2.0 Client → Authorized redirect URIs → add this URL exactly (no trailing slash).')</small>
                        </div>
                        <div class="form-group form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="google-enable" name="GOOGLE_LOGIN_ENABLED" {{ env('GOOGLE_LOGIN_ENABLED') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="google-enable">@lang('Enable Google Login')</label>
                        </div>
                        <a href="https://console.cloud.google.com/apis/credentials" target="_blank" rel="noopener" class="small">@lang('Google Cloud Console') <i class="las la-external-link-alt"></i></a>
                    </div>
                </div>
            </div>

            {{-- Facebook --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="lab la-facebook-f me-2" style="font-size: 1.5rem;"></i>
                        <h6 class="mb-0">Facebook Login</h6>
                        <span class="badge {{ env('FACEBOOK_LOGIN_ENABLED') == '1' ? 'bg-success' : 'bg-secondary' }} ms-auto">@lang(env('FACEBOOK_LOGIN_ENABLED') == '1' ? 'Enabled' : 'Disabled')</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">App ID</label>
                            <input type="text" class="form-control" name="FACEBOOK_CLIENT_ID" value="{{ env('FACEBOOK_CLIENT_ID') }}" placeholder="xxxxxxxxxxxx">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">App Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control secret-input" name="FACEBOOK_CLIENT_SECRET" value="{{ env('FACEBOOK_CLIENT_SECRET') }}" placeholder="xxxxxxxxxxxx" id="facebook_secret">
                                <button type="button" class="btn btn-outline-secondary secret-toggle" data-target="facebook_secret" data-show="{{ __("Show") }}" data-hide="{{ __("Hide") }}" title="{{ __("Show") }}"><i class="las la-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Callback URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $callbackUrls['facebook'] ?? '' }}" readonly id="facebookCallback">
                                <button type="button" class="btn btn-outline-secondary copy-callback" data-target="facebookCallback"><i class="las la-copy"></i></button>
                            </div>
                            <small class="text-muted">@lang('Add in Facebook App → Settings → Valid OAuth Redirect URIs')</small>
                        </div>
                        <div class="form-group form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="facebook-enable" name="FACEBOOK_LOGIN_ENABLED" {{ env('FACEBOOK_LOGIN_ENABLED') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="facebook-enable">@lang('Enable Facebook Login')</label>
                        </div>
                        <a href="https://developers.facebook.com/apps/" target="_blank" rel="noopener" class="small">@lang('Facebook Developers') <i class="las la-external-link-alt"></i></a>
                    </div>
                </div>
            </div>

            {{-- Twitter --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="lab la-twitter me-2" style="font-size: 1.5rem;"></i>
                        <h6 class="mb-0">Twitter (X) Login</h6>
                        <span class="badge {{ env('TWITTER_LOGIN_ENABLED') == '1' ? 'bg-success' : 'bg-secondary' }} ms-auto">@lang(env('TWITTER_LOGIN_ENABLED') == '1' ? 'Enabled' : 'Disabled')</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" class="form-control" name="TWITTER_CLIENT_ID" value="{{ env('TWITTER_CLIENT_ID') }}" placeholder="OAuth 2.0 Client ID">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Client Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control secret-input" name="TWITTER_CLIENT_SECRET" value="{{ env('TWITTER_CLIENT_SECRET') }}" placeholder="OAuth 2.0 Client Secret" id="twitter_secret">
                                <button type="button" class="btn btn-outline-secondary secret-toggle" data-target="twitter_secret" data-show="{{ __("Show") }}" data-hide="{{ __("Hide") }}" title="{{ __("Show") }}"><i class="las la-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Callback URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $callbackUrls['twitter'] ?? '' }}" readonly id="twitterCallback">
                                <button type="button" class="btn btn-outline-secondary copy-callback" data-target="twitterCallback"><i class="las la-copy"></i></button>
                            </div>
                            <small class="text-muted">@lang('Add in Twitter Developer Portal → OAuth 2.0 Redirect URIs')</small>
                        </div>
                        <div class="form-group form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="twitter-enable" name="TWITTER_LOGIN_ENABLED" {{ env('TWITTER_LOGIN_ENABLED') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="twitter-enable">@lang('Enable Twitter Login')</label>
                        </div>
                        <a href="https://developer.twitter.com/en/portal/dashboard" target="_blank" rel="noopener" class="small">@lang('Twitter Developer Portal') <i class="las la-external-link-alt"></i></a>
                    </div>
                </div>
            </div>

            {{-- Apple --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="lab la-apple me-2" style="font-size: 1.5rem;"></i>
                        <h6 class="mb-0">Apple Login</h6>
                        <span class="badge {{ env('APPLE_LOGIN_ENABLED') == '1' ? 'bg-success' : 'bg-secondary' }} ms-auto">@lang(env('APPLE_LOGIN_ENABLED') == '1' ? 'Enabled' : 'Disabled')</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Service ID (Client ID)</label>
                            <input type="text" class="form-control" name="APPLE_CLIENT_ID" value="{{ env('APPLE_CLIENT_ID') }}" placeholder="com.example.service">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Client Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control secret-input" name="APPLE_CLIENT_SECRET" value="{{ env('APPLE_CLIENT_SECRET') }}" placeholder="JWT secret" id="apple_secret">
                                <button type="button" class="btn btn-outline-secondary secret-toggle" data-target="apple_secret" data-show="{{ __("Show") }}" data-hide="{{ __("Hide") }}" title="{{ __("Show") }}"><i class="las la-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Callback URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $callbackUrls['apple'] ?? '' }}" readonly id="appleCallback">
                                <button type="button" class="btn btn-outline-secondary copy-callback" data-target="appleCallback"><i class="las la-copy"></i></button>
                            </div>
                            <small class="text-muted">@lang('Add in Apple Developer → Sign in with Apple')</small>
                        </div>
                        <div class="form-group form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="apple-enable" name="APPLE_LOGIN_ENABLED" {{ env('APPLE_LOGIN_ENABLED') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="apple-enable">@lang('Enable Apple Login')</label>
                        </div>
                        <a href="https://developer.apple.com/account/resources/identifiers/list" target="_blank" rel="noopener" class="small">@lang('Apple Developer') <i class="las la-external-link-alt"></i></a>
                    </div>
                </div>
            </div>

            {{-- GitHub --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center">
                        <i class="lab la-github me-2" style="font-size: 1.5rem;"></i>
                        <h6 class="mb-0">GitHub Login</h6>
                        <span class="badge {{ env('GITHUB_LOGIN_ENABLED') == '1' ? 'bg-success' : 'bg-secondary' }} ms-auto">@lang(env('GITHUB_LOGIN_ENABLED') == '1' ? 'Enabled' : 'Disabled')</span>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" class="form-control" name="GITHUB_CLIENT_ID" value="{{ env('GITHUB_CLIENT_ID') }}" placeholder="Ov23li...">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Client Secret</label>
                            <div class="input-group">
                                <input type="password" class="form-control secret-input" name="GITHUB_CLIENT_SECRET" value="{{ env('GITHUB_CLIENT_SECRET') }}" placeholder="GitHub OAuth secret" id="github_secret">
                                <button type="button" class="btn btn-outline-secondary secret-toggle" data-target="github_secret" data-show="{{ __("Show") }}" data-hide="{{ __("Hide") }}" title="{{ __("Show") }}"><i class="las la-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">@lang('Callback URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $callbackUrls['github'] ?? '' }}" readonly id="githubCallback">
                                <button type="button" class="btn btn-outline-secondary copy-callback" data-target="githubCallback"><i class="las la-copy"></i></button>
                            </div>
                            <small class="text-muted">@lang('Add in GitHub → Settings → Developer settings → OAuth Apps → Authorization callback URL')</small>
                        </div>
                        <div class="form-group form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="github-enable" name="GITHUB_LOGIN_ENABLED" {{ env('GITHUB_LOGIN_ENABLED') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="github-enable">@lang('Enable GitHub Login')</label>
                        </div>
                        <a href="https://github.com/settings/developers" target="_blank" rel="noopener" class="small">@lang('GitHub OAuth Apps') <i class="las la-external-link-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <button type="submit" class="btn btn--primary btn-lg w-100"><i class="las la-save me-2"></i>@lang('Save All Social Login Settings')</button>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script>
(function() {
    // Copy callback URL
    document.querySelectorAll('.copy-callback').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-target');
            var input = document.getElementById(id);
            if (input) {
                input.select();
                input.setSelectionRange(0, 99999);
                navigator.clipboard.writeText(input.value).then(function() {
                    var icon = btn.querySelector('i');
                    if (icon) { icon.className = 'las la-check'; setTimeout(function() { icon.className = 'las la-copy'; }, 1500); }
                });
            }
        });
    });

    // Show/Hide Client Secret
    document.querySelectorAll('.secret-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var input = document.getElementById(targetId);
            var icon = this.querySelector('i');
            var showText = this.getAttribute('data-show') || 'Show';
            var hideText = this.getAttribute('data-hide') || 'Hide';
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'las la-eye-slash';
                this.setAttribute('title', hideText);
            } else {
                input.type = 'password';
                icon.className = 'las la-eye';
                this.setAttribute('title', showText);
            }
        });
    });
})();
</script>
@endpush
