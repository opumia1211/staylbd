@extends('admin.layouts.app')
@php
    $whatsapp = $grouped->get('whatsapp')?->first();
    $telegram = $grouped->get('telegram')?->first();
    $emailChannel = $grouped->get('email')?->first();
@endphp
@section('panel')
    {{-- Page header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h4 class="mb-1">@lang('Contact Channels')</h4>
                    <p class="text-muted mb-0 small">@lang('Connect WhatsApp, Telegram, or Email so customer messages from your website or apps reach you in one place.')</p>
                </div>
                <a href="{{ route('admin.ticket.index') }}" class="btn btn-outline--primary btn-sm">
                    <i class="las la-envelope me-1"></i> @lang('View Messages / Support Tickets')
                </a>
            </div>
        </div>
    </div>

    {{-- Help: Always visible by default (nothing hidden) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info shadow-sm">
                <div class="card-header bg-info bg-opacity-10 py-3 d-flex align-items-center justify-content-between flex-wrap gap-2" data-bs-toggle="collapse" data-bs-target="#contactChannelsHelp" aria-expanded="true" style="cursor: pointer;">
                    <span class="d-flex align-items-center">
                        <i class="las la-question-circle la-2x text-info me-2"></i>
                        <strong class="fs-6">@lang('What are Contact Channels?')</strong>
                    </span>
                    <span class="badge bg-info">@lang('Click to collapse / expand')</span>
                </div>
                <div class="collapse show" id="contactChannelsHelp">
                    <div class="card-body">
                        <p class="mb-2">@lang('Contact Channels let you receive and reply to customer messages from multiple platforms in one place:')</p>
                        <ul class="mb-2">
                            <li><strong>@lang('WhatsApp Business API')</strong> — @lang('Customers message your business number; messages appear in Support Tickets.')</li>
                            <li><strong>@lang('Telegram Bot')</strong> — @lang('Customers chat with your bot; conversations sync to Support Tickets.')</li>
                            <li><strong>@lang('Email Forwarding')</strong> — @lang('Contact form submissions are forwarded to an email address.')</li>
                        </ul>
                        <p class="mb-0 text-muted">@lang('After saving, use "Test connection" to verify. Webhook URLs (Advanced section in each card) must be set in Meta/Telegram dashboards.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Channel status cards --}}
    <div class="row gy-3 mb-4">
        @foreach($channels as $channel)
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h5 class="card-title mb-0">{{ $channel->name }}</h5>
                                <small class="text-muted text-uppercase">{{ $channel->channel }}</small>
                            </div>
                            <span class="badge {{ $channel->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $channel->is_active ? __('Online') : __('Offline') }}
                            </span>
                        </div>
                        <p class="text-muted mb-1 small">
                            <i class="las la-sync-alt me-1"></i>
                            {{ __('Last sync: :time', ['time' => $channel->last_synced_at ? diffForHumans($channel->last_synced_at) : __('Never')]) }}
                        </p>
                        @if($channel->last_error_message)
                            <p class="text-danger small mb-2"><i class="las la-exclamation-triangle me-1"></i>{{ $channel->last_error_message }}</p>
                        @endif
                        <div class="d-flex flex-wrap gap-2">
                            <form method="POST" action="{{ route('admin.contact.channels.toggle', $channel->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-{{ $channel->is_active ? 'danger' : 'success' }}">
                                    {{ $channel->is_active ? __('Disable') : __('Enable') }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.contact.channels.test', $channel->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="las la-vial me-1"></i> @lang('Test connection')
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @if($channels->isEmpty())
            <div class="col-12">
                <div class="alert alert-info mb-0">
                    <i class="las la-info-circle me-1"></i>
                    @lang('No contact channels configured yet. Use the forms below to connect WhatsApp, Telegram, or Email forwarding.')
                </div>
            </div>
        @endif
    </div>

    {{-- Channel setup forms - clearly visible section header --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0">@lang('Channel setup')</h5>
                <span class="badge bg-primary">@lang('WhatsApp · Telegram · Email')</span>
            </div>
            <p class="text-muted mb-0 mt-1">@lang('Configure each channel below. All sections are visible; use the switches to enable or disable.')</p>
        </div>
    </div>
    <div class="row gy-4">
        {{-- WhatsApp --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0"><i class="lab la-whatsapp text-success me-1"></i> @lang('WhatsApp Business API')</h5>
                    <span class="badge bg-light text-dark text-uppercase small">@lang('Official API')</span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">@lang('Get credentials from Meta Business Suite / WhatsApp Developer. Messages from customers will appear in Support Tickets.')</p>
                    <form method="POST" action="{{ route('admin.contact.channels.store') }}">
                        @csrf
                        <input type="hidden" name="channel" value="whatsapp">
                        <div class="mb-3">
                            <label class="form-label">@lang('Display Name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $whatsapp?->name ?? 'WhatsApp Business') }}" placeholder="@lang('e.g. WhatsApp Business')">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Phone Number (with country code)')</label>
                                <input type="text" name="whatsapp_phone_number" class="form-control"
                                       value="{{ old('whatsapp_phone_number', $whatsapp?->getSetting('phone_number') ?? '') }}" required placeholder="8801712345678">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Business Account ID')</label>
                                <input type="text" name="whatsapp_business_id" class="form-control"
                                       value="{{ old('whatsapp_business_id', $whatsapp?->getSetting('business_id') ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Phone Number ID')</label>
                                <input type="text" name="whatsapp_phone_id" class="form-control"
                                       value="{{ old('whatsapp_phone_id', $whatsapp?->getSetting('phone_number_id') ?? '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Verify Token')</label>
                                <input type="text" name="whatsapp_verify_token" class="form-control"
                                       value="{{ old('whatsapp_verify_token', $whatsapp?->getSetting('verify_token') ?? \Illuminate\Support\Str::random(12)) }}" required>
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">@lang('Permanent Access Token')</label>
                            <input type="password" name="whatsapp_access_token" class="form-control" autocomplete="off"
                                   placeholder="{{ $whatsapp?->getSecret('access_token') ? __('••••••••') : __('Enter token') }}">
                            <small class="text-muted">@lang('Leave blank to keep existing token.')</small>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="waActiveSwitch"
                                   {{ old('is_active', $whatsapp?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="waActiveSwitch">@lang('Enable WhatsApp channel')</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="waPrimarySwitch"
                                   {{ old('is_primary', $whatsapp?->is_primary ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="waPrimarySwitch">@lang('Primary channel (default for replies)')</label>
                        </div>
                        <button type="submit" class="btn btn--primary w-100">@lang('Save WhatsApp Settings')</button>
                    </form>
                    @if(isset($webhookUrls['whatsapp']))
                        <div class="mt-4 pt-3 border-top border-2">
                            <label class="form-label fw-bold text-uppercase text-muted">@lang('Advanced — Webhook URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="waWebhookUrl" readonly value="{{ $webhookUrls['whatsapp'] }}">
                                <button type="button" class="btn btn-outline-primary" onclick="copyWebhook('waWebhookUrl');"><i class="las la-copy me-1"></i> @lang('Copy')</button>
                            </div>
                            <p class="text-muted mb-0 mt-1">@lang('Use this URL in Meta App Dashboard → WhatsApp → Configuration.')</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Telegram --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0"><i class="lab la-telegram text-info me-1"></i> @lang('Telegram Bot')</h5>
                    <span class="badge bg-light text-dark text-uppercase small">@lang('Bot API')</span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">@lang('Create a bot with @BotFather, then set the webhook URL below. Chats will sync to Support Tickets.')</p>
                    <form method="POST" action="{{ route('admin.contact.channels.store') }}">
                        @csrf
                        <input type="hidden" name="channel" value="telegram">
                        <div class="mb-3">
                            <label class="form-label">@lang('Display Name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $telegram?->name ?? 'Telegram Bot') }}" placeholder="@lang('e.g. Support Bot')">
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">@lang('Bot Username')</label>
                                <input type="text" name="telegram_bot_name" class="form-control"
                                       value="{{ old('telegram_bot_name', $telegram?->getSetting('bot_name') ?? '') }}" required placeholder="@YourBot">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">@lang('Webhook Secret (optional)')</label>
                                <input type="text" name="telegram_webhook_secret" class="form-control"
                                       value="{{ old('telegram_webhook_secret', $telegram?->getSecret('webhook_secret')) }}" placeholder="@lang('Optional')">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">@lang('Bot Token')</label>
                            <input type="password" name="telegram_bot_token" class="form-control" autocomplete="off"
                                   placeholder="{{ $telegram?->getSecret('bot_token') ? __('••••••••') : __('Enter bot token') }}" required>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="tgActiveSwitch"
                                   {{ old('is_active', $telegram?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tgActiveSwitch">@lang('Enable Telegram channel')</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="tgPrimarySwitch"
                                   {{ old('is_primary', $telegram?->is_primary ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tgPrimarySwitch">@lang('Primary channel (default for replies)')</label>
                        </div>
                        <button type="submit" class="btn btn--primary w-100">@lang('Save Telegram Settings')</button>
                    </form>
                    @if(isset($webhookUrls['telegram']))
                        <div class="mt-4 pt-3 border-top border-2">
                            <label class="form-label fw-bold text-uppercase text-muted">@lang('Advanced — Webhook URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="tgWebhookUrl" readonly value="{{ $webhookUrls['telegram'] }}">
                                <button type="button" class="btn btn-outline-primary" onclick="copyWebhook('tgWebhookUrl');"><i class="las la-copy me-1"></i> @lang('Copy')</button>
                            </div>
                            <p class="text-muted mb-0 mt-1">@lang('Set via: https://api.telegram.org/bot&lt;token&gt;/setWebhook?url=...')</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Email --}}
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0"><i class="las la-envelope text-danger me-1"></i> @lang('Email Forwarding')</h5>
                    <span class="badge bg-light text-dark text-uppercase small">@lang('Forward')</span>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">@lang('Contact form submissions from your site can be forwarded to this email address.')</p>
                    <form method="POST" action="{{ route('admin.contact.channels.store') }}">
                        @csrf
                        <input type="hidden" name="channel" value="email">
                        <div class="mb-3">
                            <label class="form-label">@lang('Display Name')</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $emailChannel?->name ?? 'Email Desk') }}" placeholder="@lang('e.g. Email Desk')">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Forward-to Address')</label>
                            <input type="email" name="email_forward_address" class="form-control"
                                   value="{{ old('email_forward_address', $emailChannel?->getSetting('forward_address') ?? auth()->user()->email ?? '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">@lang('Subject Prefix')</label>
                            <input type="text" name="email_subject_prefix" class="form-control"
                                   value="{{ old('email_subject_prefix', $emailChannel?->getSetting('subject_prefix') ?? '[Contact]') }}" placeholder="[Contact]">
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="emailActiveSwitch"
                                   {{ old('is_active', $emailChannel?->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailActiveSwitch">@lang('Enable Email channel')</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_primary" value="1" id="emailPrimarySwitch"
                                   {{ old('is_primary', $emailChannel?->is_primary ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailPrimarySwitch">@lang('Primary channel (default for replies)')</label>
                        </div>
                        <button type="submit" class="btn btn--primary w-100">@lang('Save Email Settings')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Feature overview (always visible, readable font) --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light border shadow-sm">
                <div class="card-header py-3">
                    <h5 class="mb-0"><i class="las la-book me-2"></i> @lang('Feature overview — What does each part do?')</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-nowrap align-top pe-2"><strong>@lang('Contact Channels')</strong></td>
                            <td>@lang('Unified place to connect WhatsApp, Telegram, and Email. Messages from these channels can be handled from Admin → Messages / Support Tickets.')</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap align-top pe-2"><strong>@lang('WhatsApp Business API')</strong></td>
                            <td>@lang('Official integration with Meta. You need a Business Account, Phone Number ID, and Permanent Access Token from Meta Developer Console. The Webhook URL must be set in WhatsApp → Configuration. When customers message your business number, messages appear as support tickets.')</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap align-top pe-2"><strong>@lang('Telegram Bot')</strong></td>
                            <td>@lang('Create a bot with @BotFather, get the token, and set the Webhook URL so Telegram sends updates to your site. User chats with the bot are mirrored into support tickets so you can reply from the admin panel.')</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap align-top pe-2"><strong>@lang('Email Forwarding')</strong></td>
                            <td>@lang('When someone submits the contact form on your website, the message can be forwarded to the email address you set here. Subject prefix helps you filter these in your inbox.')</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap align-top pe-2"><strong>@lang('Primary channel')</strong></td>
                            <td>@lang('If you have multiple channels, the primary one is used as the default for sending replies (e.g. when replying from Support Tickets).')</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap align-top pe-2"><strong>@lang('Test connection')</strong></td>
                            <td>@lang('Verifies that the token/credentials are valid. For Telegram it calls getMe; for WhatsApp it checks the Phone Number ID. If it fails, Last error is shown on the channel card.')</td>
                        </tr>
                        <tr>
                            <td class="text-nowrap align-top pe-2"><strong>@lang('Webhook URL')</strong></td>
                            <td>@lang('External services (Meta, Telegram) send events to this URL. You must paste this URL in the provider’s dashboard (WhatsApp Configuration or Telegram setWebhook) so messages reach your site.')</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyWebhook(id) {
            var el = document.getElementById(id);
            if (el && (navigator.clipboard && navigator.clipboard.writeText)) {
                navigator.clipboard.writeText(el.value);
            } else if (el) {
                var ta = document.createElement('textarea'); ta.value = el.value; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
            }
            alert('{{ __('Copied!') }}');
        }
    </script>
@endsection
