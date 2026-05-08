{{-- Floating live chat (available for all users) --}}
@php
    $chatLogo = getLogo('logo');
    $siteName = gs('site_name');
    $isStyleBd = (strtoupper($siteName) === 'STYLE BD' || strtoupper($siteName) === 'STYLEBD');
    $contactContent = getContent('contact_us.content', true);
    $waNum = $contactContent ? preg_replace('/[^0-9]/', '', (string)($contactContent->data_values->whatsapp_number ?? '')) : '';
    $tgUser = $contactContent ? trim(ltrim((string)($contactContent->data_values->telegram_username ?? ''), '@')) : '';
    $contactEmail = $contactContent ? trim((string)($contactContent->data_values->contact_email ?? '')) : '';
    $whatsappUrl = $waNum ? 'https://wa.me/' . $waNum : '';
    $telegramUrl = $tgUser ? 'https://t.me/' . $tgUser : '';
    $emailUrl = $contactEmail ? 'mailto:' . e(__($contactEmail)) : '';
    $channelService = app(\App\Services\ContactChannelService::class);
    $contactIntegrations = $channelService->getActiveIntegrations();
    $channelConfigPayload = $contactIntegrations->map(fn($channel) => [
        'id' => $channel->id,
        'channel' => $channel->channel,
        'name' => $channel->name,
        'is_active' => (bool) $channel->is_active,
        'last_synced_at' => optional($channel->last_synced_at)->toIso8601String(),
    ])->values()->toArray();
    $channelConfigEncoded = base64_encode(json_encode($channelConfigPayload));
@endphp

<div class="contact-panel-backdrop" id="contactPanelBackdrop" aria-hidden="true"></div>

<div class="contact-panel-glass contact-live-chat-module shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-2xl border border-white/40 backdrop-blur-md" id="contactPanelGlass" role="dialog" aria-labelledby="contactPanelTitle" aria-modal="true" aria-hidden="true"
     data-channel-redirect-url="{{ route('contact.channel.redirect') }}"
     data-whatsapp-url="{{ $whatsappUrl }}"
     data-telegram-url="{{ $telegramUrl }}"
     data-email-url="{{ $emailUrl }}"
     data-channel-config="{{ e($channelConfigEncoded) }}">
    <div class="contact-panel-glass-inner">
        <div class="contact-panel-glow"></div>
        <div class="contact-panel-header contact-panel-header--branded contact-panel-header--centered py-3 px-4 border-b border-gray-100">
            <div class="contact-panel-header-center">
                @if($chatLogo)
                    <img src="{{ $chatLogo }}" alt="{{ $siteName }}" class="contact-panel-logo stayl-contact-logo h-8" style="--contact-logo-max-w: 120px; --contact-logo-max-h: 32px; {{ getLogoStyle() }}">
                @else
                    <div class="contact-panel-logo contact-panel-logo-placeholder w-10 h-10">
                        @include($activeTemplate . 'partials.icon', ['name' => 'comments'])
                    </div>
                @endif
                <div class="contact-panel-header-text d-flex align-items-center justify-center gap-2 mt-1">
                    <div class="relative flex h-2 w-2">
                        <span class="animate-pulse-green absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 {{ (gs('admin_online_status') ?? 0) ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    </div>
                    <div class="flex flex-col items-center">
                        <h5 class="contact-panel-title contact-panel-brand-name mb-0 text-sm font-extrabold text-slate-900 tracking-tight" id="contactPanelTitle">
                            @if($isStyleBd)
                                <span class="brand-style">STYLE</span> <span class="brand-b">B</span><span class="brand-d">D</span>
                            @else
                                {{ $siteName }}
                            @endif
                        </h5>
                        <span class="text-[9px] font-bold uppercase tracking-[0.1em] text-slate-400 leading-none">@lang('Live Support')</span>
                    </div>
                </div>
            </div>
            <button type="button" class="contact-panel-close hover:bg-gray-100 p-1.5 rounded-lg transition-colors" id="contactPanelClose" aria-label="@lang('Close')">
                @include($activeTemplate . 'partials.icon', ['name' => 'times'])
            </button>
        </div>

        <div class="contact-panel-body p-3 pb-6 overflow-y-auto">
            {{-- MODULAR COMPONENT: Channel Selection --}}
            @include('templates.basic.partials.live_chat.channel_selection')

            <div class="contact-panel-form-title mt-4 mb-3 font-bold text-slate-800 text-[10px] uppercase tracking-wider px-1">@lang('Get in Touch')</div>
            <form id="contactPanelForm" class="contact-panel-form verify-gcaptcha" method="post" action="{{ route('contact.panel.submit') }}" enctype="multipart/form-data"
                data-chat-messages-url="{{ route('contact.chat.messages') }}" data-chat-unread-url="{{ route('contact.chat.unread') }}"
                @auth data-user-name="{{ e(optional(auth()->user())->fullname ?? '') }}" data-user-email="{{ e(optional(auth()->user())->email ?? '') }}" @endauth>
                @csrf
                <input type="hidden" name="channel" id="contactPanelChannelInput" value="livechat">
                @auth
                    <input type="hidden" name="username" value="{{ auth()->user()->username }}">
                @endauth
                
                {{-- MODULAR COMPONENT: Form Fields (Subject, Name, Email) --}}
                @include('templates.basic.partials.live_chat.form_fields')
                
                {{-- MODULAR COMPONENT: Chat Panel (Message History + Typing Area with files & buttons inside) --}}
                @include('templates.basic.partials.live_chat.chat_panel')
                <x-captcha class="form-control-sm"/>
                {{-- Send button for other channels (for live chat, button is inside message box) --}}
                <button type="button" class="btn contact-panel-submit w-100 d-none" id="contactPanelSendBtnOther">@lang('Send Message')</button>
            </form>
        </div>
    </div>
</div>

<button type="button" class="contact-float-btn contact-float-btn--effect" id="contactFloatBtn" aria-label="@lang('Open Contact')" title="@lang('Contact / Live Chat')" data-contact-live-url="{{ route('contact.live') }}">
    <span class="contact-float-btn-icon contact-float-btn-icon--active" data-icon="chat">@include($activeTemplate . 'partials.icon', ['name' => 'comments'])</span>
    <span class="contact-float-btn-icon" data-icon="whatsapp"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></span>
    <span class="contact-float-btn-icon" data-icon="telegram"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></span>
    <span class="contact-float-btn-icon" data-icon="email"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg></span>
    <span class="contact-float-btn-badge d-none" id="contactFloatBadge">0</span>
</button>
