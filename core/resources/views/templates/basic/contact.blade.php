@php
    $contactContent = $contactContent ?? getContent('contact_us.content', true);
    $contactChannels = collect($contactChannels ?? []);
@endphp
@extends($activeTemplate . 'layouts.frontend')

@push('style')
<style>
/* Contact page – professional, clean, compact (inline so it always applies) */
.contact-section.contact-page-pro { padding: 1.25rem 0 1.5rem; min-height: 0; background: linear-gradient(180deg, #f8fafc 0%, #fff 100%); }
.contact-section.contact-page-pro .contact-page-container { max-width: 720px; margin: 0 auto; padding: 0 1rem; box-sizing: border-box; }
.contact-section.contact-page-pro .contact-page-header { text-align: center; margin-bottom: 1.25rem; }
.contact-section.contact-page-pro .contact-page-brand { display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; color: inherit; }
.contact-section.contact-page-pro .contact-page-logo { height: 36px; width: auto; max-width: 140px; object-fit: contain; display: block; }
.contact-section.contact-page-pro .contact-page-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0; }
.contact-section.contact-page-pro .contact-page-tagline { font-size: 0.875rem; color: #64748b; margin: 0.35rem 0 0; }
.contact-section.contact-page-pro .contact-page-card { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.06); overflow: hidden; }
.contact-section.contact-page-pro .contact-page-actions { padding: 0.75rem 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem 0.75rem; }
.contact-section.contact-page-pro .contact-page-actions-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; }
.contact-section.contact-page-pro .contact-page-btns { display: flex; flex-wrap: wrap; gap: 0.4rem; align-items: center; }
.contact-section.contact-page-pro .contact-page-btn { display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 0.75rem; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; transition: opacity 0.2s, transform 0.15s; color: #fff !important; }
.contact-section.contact-page-pro .contact-page-btn:hover { opacity: 0.92; transform: translateY(-1px); }
.contact-section.contact-page-pro .contact-page-btn--chat { background: #4FC4F7 !important; }
.contact-section.contact-page-pro .contact-page-btn--wa { background: #25D366 !important; }
.contact-section.contact-page-pro .contact-page-btn--tg { background: #0088cc !important; }
.contact-section.contact-page-pro .contact-page-btn--email { background: #64748b !important; }
.contact-section.contact-page-pro .contact-page-body { display: grid; grid-template-columns: 1fr; gap: 1rem; padding: 1rem; }
@media (min-width: 768px) {
  .contact-section.contact-page-pro .contact-page-body { grid-template-columns: 1fr 220px; padding: 1.25rem; }
}
.contact-section.contact-page-pro .contact-form-head { margin-bottom: 0.75rem; }
.contact-section.contact-page-pro .contact-form-title { font-size: 1.0625rem; font-weight: 600; color: #1e293b; margin: 0 0 0.25rem; }
.contact-section.contact-page-pro .contact-form-subtitle { font-size: 0.8125rem; color: #64748b; margin: 0; line-height: 1.45; }
.contact-section.contact-page-pro .contact-main-form .contact-form-row { display: grid; gap: 0.5rem; margin-bottom: 0.5rem; }
.contact-section.contact-page-pro .contact-form-row--half { grid-template-columns: 1fr 1fr; }
.contact-section.contact-page-pro .contact-form-field { margin-bottom: 0.5rem; }
.contact-section.contact-page-pro .contact-form-label { display: block; font-size: 0.8125rem; font-weight: 600; color: #334155; margin-bottom: 0.25rem; }
.contact-section.contact-page-pro .contact-form-meta { font-weight: 400; color: #94a3b8; }
.contact-section.contact-page-pro .contact-form-input,
.contact-section.contact-page-pro .contact-form-select,
.contact-section.contact-page-pro .contact-form-textarea { width: 100%; padding: 0.5rem 0.65rem; font-size: 0.875rem; line-height: 1.45; color: #1e293b; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box; }
.contact-section.contact-page-pro .contact-form-input:focus,
.contact-section.contact-page-pro .contact-form-select:focus,
.contact-section.contact-page-pro .contact-form-textarea:focus { outline: none; border-color: #4FC4F7; box-shadow: 0 0 0 3px rgba(79,196,247,0.2); }
.contact-section.contact-page-pro .contact-form-textarea { min-height: 80px; resize: vertical; }
.contact-section.contact-page-pro .contact-char-counter { font-size: 0.75rem; color: #94a3b8; margin-top: 0.2rem; }
.contact-section.contact-page-pro .contact-char-counter.at-limit .contact-char-count { color: #dc2626; }
.contact-section.contact-page-pro .contact-form-submit { margin-top: 0.75rem; margin-bottom: 0; }
.contact-section.contact-page-pro .contact-form-btn { width: 100%; padding: 0.6rem 1rem; font-size: 0.9375rem; font-weight: 600; color: #fff !important; background: #4FC4F7 !important; border: none !important; border-radius: 8px; cursor: pointer; transition: opacity 0.2s; }
.contact-section.contact-page-pro .contact-form-btn:hover { opacity: 0.95; }
.contact-section.contact-page-pro .contact-main-form .form-control { min-height: 40px; padding: 0.5rem 0.65rem; font-size: 0.875rem; border-radius: 8px; border: 1px solid #e2e8f0; }
.contact-section.contact-page-pro .contact-page-visual { display: flex; align-items: center; justify-content: center; }
.contact-section.contact-page-pro .contact-page-img { width: 100%; max-width: 220px; height: auto; max-height: 180px; object-fit: contain; border-radius: 10px; }
.contact-section.contact-page-pro .contact-page-live { margin-top: 0.75rem; }
.contact-section.contact-page-pro .contact-page-live-inner { display: flex; flex-wrap: wrap; align-items: center; gap: 0.5rem 0.75rem; padding: 0.65rem 1rem; background: #f1f5f9; border-radius: 10px; border: 1px solid #e2e8f0; }
.contact-section.contact-page-pro .contact-page-live-icon { font-size: 1.35rem; color: #4FC4F7; }
.contact-section.contact-page-pro .contact-page-live-text { font-size: 0.875rem; font-weight: 600; color: #334155; }
.contact-section.contact-page-pro .contact-page-live-link { font-size: 0.875rem; color: #4FC4F7; text-decoration: none; font-weight: 500; }
.contact-section.contact-page-pro .contact-page-live-link:hover { text-decoration: underline; }
.contact-section.contact-page-pro .contact-page-live-btn { margin-left: auto; padding: 0.4rem 0.75rem; font-size: 0.8125rem; font-weight: 600; color: #fff !important; background: #4FC4F7 !important; border: none !important; border-radius: 8px; cursor: pointer; }
.contact-section.contact-page-pro .contact-page-live-btn:hover { opacity: 0.9; }
</style>
@endpush

@section('content')
<section class="contact-section contact-page-pro wow fadeInUp" data-wow-duration="0.4s" data-wow-delay="0.1s">
    <div class="contact-page-container">
        {{-- Compact header --}}
        <div class="contact-page-header">
            <a href="{{ route('home') }}" class="contact-page-brand">
                @php $contactLogo = getLogo('logo'); @endphp
                @if($contactLogo)
                    <img src="{{ $contactLogo }}" alt="{{ gs('site_name') }}" class="contact-page-logo">
                @endif
                <span class="contact-page-title">{{ gs('site_name') }}</span>
            </a>
            <p class="contact-page-tagline">@lang('Get in touch')</p>
        </div>

        {{-- Single compact card: Quick contact + Form + Image --}}
        <div class="contact-page-card">
            {{-- Quick actions: one row --}}
            <div class="contact-page-actions">
                <span class="contact-page-actions-label">@lang('Send message via')</span>
                <div class="contact-page-btns">
                    <button type="button" class="contact-page-btn contact-page-btn--chat js-contact-panel-open" onclick="if(window.openContactPanel){window.openContactPanel();} return false;">
                        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'messages_icon', 'fallback' => 'comments', 'width' => 18, 'height' => 18, 'alt' => ''])<span>@lang('Live Chat')</span>
                    </button>
                    <button type="button" class="contact-page-btn contact-page-btn--wa js-contact-panel-open" onclick="if(window.openContactPanel){window.openContactPanel();} return false;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        <span>WhatsApp</span>
                    </button>
                    <button type="button" class="contact-page-btn contact-page-btn--tg js-contact-panel-open" onclick="if(window.openContactPanel){window.openContactPanel();} return false;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                        <span>Telegram</span>
                    </button>
                    <button type="button" class="contact-page-btn contact-page-btn--email js-contact-panel-open" onclick="if(window.openContactPanel){window.openContactPanel();} return false;">
                        @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'mail_icon', 'fallback' => 'envelope', 'width' => 18, 'height' => 18, 'alt' => ''])<span>@lang('Email')</span>
                    </button>
                </div>
            </div>

            <div class="contact-page-body">
                <div class="contact-page-form-wrap">
                    <div class="contact-form-head">
                        <h3 class="contact-form-title">{{ __(@$contactContent->data_values->title ?? 'Contact Us') }}</h3>
                        <p class="contact-form-subtitle">{{ __(@$contactContent->data_values->subtitle) }}</p>
                    </div>
                    <form method="post" action="{{ route('contact') }}" class="contact-main-form verify-gcaptcha">
                        @csrf
                        @if($user)
                            <input type="hidden" name="username" value="{{ optional($user)->username }}">
                        @endif
                        <div class="contact-form-row contact-form-row--half">
                            <div class="contact-form-field">
                                <label class="contact-form-label">@lang('Name')</label>
                                <input name="name" type="text" class="contact-form-input" value="{{ old('name', optional($user)->fullname ?? '') }}" autocomplete="name" @if($user) readonly @endif required>
                            </div>
                            <div class="contact-form-field">
                                <label class="contact-form-label">@lang('Email')</label>
                                <input name="email" type="email" class="contact-form-input" value="{{ old('email', optional($user)->email ?? '') }}" autocomplete="email" @if($user) readonly @endif required>
                            </div>
                        </div>
                        <div class="contact-form-field">
                            <label class="contact-form-label">@lang('Subject')</label>
                            <select name="subject" class="contact-form-input contact-form-select" required>
                                <option value="Live Chat Message" {{ old('subject') == 'Live Chat Message' ? 'selected' : '' }}>@lang('Live Chat Message')</option>
                                <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>@lang('General Inquiry')</option>
                                <option value="Report a Problem" {{ old('subject') == 'Report a Problem' ? 'selected' : '' }}>@lang('Report a Problem')</option>
                                <option value="Order Support" {{ old('subject') == 'Order Support' ? 'selected' : '' }}>@lang('Order Support')</option>
                            </select>
                        </div>
                        <div class="contact-form-field">
                            <label class="contact-form-label">@lang('Message') <small class="contact-form-meta">(@lang('Max 500 characters'))</small></label>
                            <textarea name="message" wrap="off" class="contact-form-input contact-form-textarea contact-message-input" maxlength="500" rows="3" required>{{ old('message') }}</textarea>
                            <div class="contact-char-counter"><span class="contact-char-count">0</span>/500</div>
                        </div>
                        <x-captcha class="form-control-sm form--control-4"/>
                        <div class="contact-form-submit">
                            <button type="submit" class="contact-form-btn">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
                <div class="contact-page-visual">
                    <img src="{{ getImage('assets/images/frontend/contact_us/' . @$contactContent->data_values->image, '480x430') }}" alt="@lang('Contact')" class="contact-page-img" loading="lazy" decoding="async">
                </div>
            </div>
        </div>

        {{-- Compact live chat CTA --}}
        <div class="contact-page-live">
            <div class="contact-page-live-inner">
                @include($activeTemplate . 'partials.header_icon_asset', ['iconKey' => 'messages_icon', 'fallback' => 'comments', 'class' => 'contact-page-live-icon', 'width' => 22, 'height' => 22, 'alt' => ''])
                <span class="contact-page-live-text">@lang('Live Chat Support')</span>
                <a href="{{ route('contact.live') }}" class="contact-page-live-link">@lang('Open Live')</a>
                <button type="button" class="contact-page-live-btn js-contact-panel-open" onclick="if(window.openContactPanel){window.openContactPanel();} return false;">@lang('Open Chat')</button>
            </div>
        </div>
    </div>
</section>

@push('script')
<script>
(function(){
    var MAX = 500;
    document.querySelectorAll('.contact-message-input').forEach(function(ta){
        var counter = ta.closest('.contact-form-field') && ta.closest('.contact-form-field').querySelector('.contact-char-counter span');
        if (!counter) counter = ta.closest('.contact-floating-chat-body') ? ta.closest('.contact-floating-chat-body').querySelector('.contact-float-count') : null;
        if (!counter) return;
        function update(){
            var n = (ta.value || '').length;
            counter.textContent = n;
            var wrap = counter.closest('.contact-char-counter');
            if (wrap) { if (n >= MAX) wrap.classList.add('at-limit'); else wrap.classList.remove('at-limit'); }
        }
        ta.addEventListener('input', update);
        ta.addEventListener('keyup', update);
        update();
    });
})();
</script>
@endpush
@endsection
