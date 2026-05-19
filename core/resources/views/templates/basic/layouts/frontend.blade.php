@extends($activeTemplate . 'layouts.app')
@php
    $__offerPage = get_offer_timer_page_from_route();
    $__contentTopTimers = get_offer_timers_for_display($__offerPage, 'content_top');
    $__contentBottomTimers = get_offer_timers_for_display($__offerPage, 'content_bottom');
    $__inlineTop = get_inline_ads_for_display($__offerPage, 'content_top');
    $__popupAds = get_popup_ads_for_display($__offerPage);
@endphp
@section('app')
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'header_above'])
    @include($activeTemplate . 'partials.header')
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'header_below'])
    {{-- storefront-main: padding zeroed to allow body padding-top (line 100 in app.blade.php) to manage the header-banner gap --}}
    <main class="storefront-main" style="padding-top: 0 !important;">
        @include($activeTemplate . 'partials.inline_public_ads')
        @include($activeTemplate . 'partials.scrollbar', ['position' => 'content_top'])
        @if($__contentTopTimers->isNotEmpty())
            <div class="main-container py-2">
                @foreach($__contentTopTimers as $__t)
                    @include('partials.offer_timer_bar', ['timer' => $__t])
                @endforeach
            </div>
        @endif
        @if($__inlineTop->isNotEmpty())
            <div class="main-container py-2">
                @include($activeTemplate . 'partials.inline_ad', ['inlineAds' => $__inlineTop, 'placement' => 'content_top'])
            </div>
        @endif
        <div class="main-container">
            @yield('content')
        </div>
        @if($__contentBottomTimers->isNotEmpty())
            <div class="main-container py-2">
                @foreach($__contentBottomTimers as $__t)
                    @include('partials.offer_timer_bar', ['timer' => $__t])
                @endforeach
            </div>
        @endif
        @include($activeTemplate . 'partials.scrollbar', ['position' => 'content_bottom'])
    </main>
    @include($activeTemplate . 'partials.global_positioned_ads')
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'footer_above'])
    @include($activeTemplate . 'partials.footer')
    @include($activeTemplate . 'partials.scrollbar', ['position' => 'footer_below'])

    {{-- Floating WhatsApp Integration --}}
    @if($general->whatsapp_number)
    <a href="https://wa.me/{{ $general->whatsapp_number }}?text=Hi, I need assistance with my order." 
       target="_blank" 
       class="fixed bottom-24 right-6 z-[9990] bg-[#25d366] text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-300">
        <i class="lab la-whatsapp fs-1"></i>
    </a>
    @endif

    @include($activeTemplate . 'partials.contact_panel')
    @if($__popupAds->isNotEmpty())
    @include($activeTemplate . 'partials.popup_ad', ['popupAds' => $__popupAds])
    @endif

    {{-- Quick Order modal (hidden on full /user/order page – that view includes the form once) --}}
    @guest
        @if(!request()->routeIs('user.guest.order'))
            @include($activeTemplate . 'partials.guest_checkout_modal')
        @endif
    @endguest

    <div class="modal fade" id="quickView" tabindex="-1" aria-labelledby="quickViewLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content quick-view-modal-content">
                <span data-bs-dismiss="modal" class="modal-close-btn" aria-label="@lang('Close')">@include($activeTemplate . 'partials.icon', ['name' => 'times'])</span>
                <div class="modal-body py-3 px-3" id="productmodalView"></div>
            </div>
        </div>
    </div>

    {{-- Sticky Cart Floating Trigger --}}
    <div id="stickyCartTrigger" class="fixed right-0 top-1/2 -translate-y-1/2 z-[9995] bg-white dark:bg-slate-900 shadow-[-5px_0_20px_rgba(0,0,0,0.1)] rounded-l-2xl border border-r-0 border-slate-200 dark:border-slate-800 p-3 flex flex-col items-center gap-2 cursor-pointer hover:pl-5 transition-all duration-300 group hidden md:flex">
        <div class="relative">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-sky-600 dark:text-sky-400 group-hover:scale-110 transition-transform">
                <circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <span class="show-cart-count absolute -top-2 -right-2 bg-sky-600 text-white text-[10px] font-bold h-5 w-5 rounded-full flex items-center justify-center ring-2 ring-white">0</span>
        </div>
        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">@lang('Cart')</span>
        <div class="text-sky-600 font-bold text-sm mt-1 staylbd-rt-price" data-base-price="0">0.00</div>
    </div>

    <script>
        window.StaylGlobal = {
            locale: '{{ app()->getLocale() }}',
            currency: {
                code: '{{ session('stayl_display_currency_code') ?: $general->cur_text }}',
                symbol: '{{ session('stayl_display_currency_symbol') ?: $general->cur_sym }}',
                rate: {{ session('stayl_display_currency_rate') ?: 1 }},
                position: '{{ $general->currency_position ?? 'left' }}'
            },
            formatPrice: function(amount) {
                let val = amount * this.currency.rate;
                let formatted = val.toLocaleString(this.locale === 'bn' ? 'bn-BD' : 'en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                return this.currency.position === 'left' 
                    ? this.currency.symbol + formatted 
                    : formatted + this.currency.symbol;
            }
        };

        // Sticky Cart Toggle Logic
        document.addEventListener('DOMContentLoaded', function() {
            const trigger = document.getElementById('stickyCartTrigger');
            if (trigger) {
                trigger.addEventListener('click', function() {
                    window.location.href = "{{ route('user.cart') }}";
                });
                
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        trigger.classList.remove('hidden');
                    } else {
                        trigger.classList.add('hidden');
                    }
                });
            }
        });
    </script>
@endsection

@push('style')

{{-- inline style moved to critical-storefront.css --}}

@endpush

@if (!$general->maintenance_mode)
    @push('script')
        <script>
            (function() {
                "use strict";
                if (window.StaylModal) return;

                function staylResolveModal(input) {
                    if (!input) return null;
                    if (typeof input === 'string') {
                        return document.getElementById(String(input).replace(/^#/, ''));
                    }
                    return input;
                }

                function show(modalEl) {
                    modalEl = staylResolveModal(modalEl);
                    if (!modalEl) return;
                    modalEl.classList.add('is-open', 'show');
                    modalEl.removeAttribute('aria-hidden');
                    document.body.classList.add('modal-open');
                    try { modalEl.dispatchEvent(new CustomEvent('stayl:modal:shown')); } catch (e) {}
                }

                function hide(modalEl) {
                    modalEl = staylResolveModal(modalEl);
                    if (!modalEl) return;
                    modalEl.classList.remove('is-open', 'show');
                    modalEl.setAttribute('aria-hidden', 'true');
                    if (!document.querySelector('.modal.is-open')) {
                        document.body.classList.remove('modal-open');
                    }
                    try { modalEl.dispatchEvent(new CustomEvent('stayl:modal:hidden')); } catch (e) {}
                }

                document.addEventListener('click', function(e) {
                    var dismissBtn = e.target.closest('[data-bs-dismiss="modal"], .modal-close-btn');
                    if (dismissBtn) {
                        var m = dismissBtn.closest('.modal');
                        if (m) hide(m);
                        return;
                    }
                    var modal = e.target.classList && e.target.classList.contains('modal') ? e.target : null;
                    if (modal && modal.classList.contains('is-open')) hide(modal);
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key !== 'Escape') return;
                    var topOpen = document.querySelector('.modal.is-open');
                    if (topOpen) hide(topOpen);
                });

                window.StaylModal = { show: show, hide: hide };
            })();
        </script>
        @include($activeTemplate . 'partials.auth_iframe_overlay_script')
        <script>
            'use strict';
            // Contact panel: button group + single form that routes to chosen channel
            document.addEventListener('DOMContentLoaded', function() {
                var contactBackdrop = document.getElementById('contactPanelBackdrop');
                var contactPanel = document.getElementById('contactPanelGlass');
                var contactClose = document.getElementById('contactPanelClose');
                var contactFloatBtn = document.getElementById('contactFloatBtn');
                var contactForm = document.getElementById('contactPanelForm');
                var contactMsg = document.getElementById('contactPanelMessage');
                var contactCharCount = document.getElementById('contactPanelCharCount');
                var selectRow = document.getElementById('contactPanelSelectRow');
                var channelInput = document.getElementById('contactPanelChannelInput');
                var sendBtn = document.getElementById('contactPanelSendBtn');
                var sendBtnOther = document.getElementById('contactPanelSendBtnOther');
                var selectedChannel = 'livechat';
                function getActiveSendBtn() { return selectedChannel === 'livechat' ? sendBtn : sendBtnOther; }

                var contactLiveUrl = (contactFloatBtn && contactFloatBtn.getAttribute('data-contact-live-url')) ? contactFloatBtn.getAttribute('data-contact-live-url') : '{{ route("contact.live") }}';
                function openContactPanel() {
                    if (contactBackdrop) contactBackdrop.classList.add('is-open');
                    if (contactPanel) { contactPanel.classList.add('is-open'); contactPanel.setAttribute('aria-hidden', 'false'); }
                    document.body.classList.add('contact-panel-open');
                }
                window.openContactPanel = openContactPanel;
                function closeContactPanel() {
                    if (contactBackdrop) contactBackdrop.classList.remove('is-open');
                    if (contactPanel) { contactPanel.classList.remove('is-open'); contactPanel.setAttribute('aria-hidden', 'true'); }
                    document.body.classList.remove('contact-panel-open');
                }
                window.closeContactPanel = closeContactPanel;
                if (contactFloatBtn) {
                    contactFloatBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        openContactPanel();
                        try { history.pushState({ contactLive: true }, '', contactLiveUrl); } catch (err) {}
                    });
                }
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.js-contact-panel-open')) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (typeof openContactPanel === 'function') {
                            openContactPanel();
                        } else if (contactFloatBtn) {
                            contactFloatBtn.click();
                        } else {
                            var href = e.target.closest('.js-contact-panel-open').getAttribute('data-contact-live-url') || e.target.closest('.js-contact-panel-open').getAttribute('href');
                            if (href) window.location.href = href;
                        }
                    }
                }, true);
                window.addEventListener('popstate', function() {
                    if (contactPanel && contactPanel.classList.contains('is-open')) closeContactPanel();
                });
                if (window.location.pathname.indexOf('contactlive') !== -1 || window.location.search.indexOf('open_contact=1') !== -1) {
                    setTimeout(function() {
                        openContactPanel();
                        var chatThread = document.getElementById('contactPanelChatThread');
                        if (chatThread && selectedChannel === 'livechat') {
                            chatThread.classList.add('is-visible');
                            var subjEl = document.getElementById('contactPanelSubject');
                            if (subjEl && !subjEl.value) subjEl.value = 'Live Chat Message';
                            loadChatMessages();
                        }
                        if (window.history && window.history.replaceState && window.location.search.indexOf('open_contact=1') !== -1) {
                            var url = window.location.pathname + (window.location.hash || '');
                            window.history.replaceState({}, document.title, url);
                        }
                    }, 300);
                }
                if (contactClose) contactClose.addEventListener('click', closeContactPanel);
                if (contactBackdrop) contactBackdrop.addEventListener('click', closeContactPanel);
                document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && contactPanel && contactPanel.classList.contains('is-open')) closeContactPanel(); });

                var lastMessageCount = 0;
                var lastMessageId = 0;
                function loadChatMessages() {
                    var url = contactForm ? contactForm.getAttribute('data-chat-messages-url') : '';
                    if (!url || selectedChannel !== 'livechat') return;
                    var chatThread = document.getElementById('contactPanelChatThread');
                    var chatMessages = document.getElementById('contactPanelChatMessages');
                    if (!chatThread || !chatMessages) return;
                    chatThread.classList.add('is-visible');
                    var forceScroll = arguments[0] !== false;
                    var isFirstLoad = !chatMessages.querySelector('.contact-chat-msg') && !chatMessages.querySelector('.text-muted');
                    if (isFirstLoad) chatMessages.innerHTML = '<span class="text-muted small">{{ __("Loading...") }}</span>';
                    fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            var list = data.messages || [];
                            var lastId = list.length ? (list[list.length - 1].id || 0) : 0;
                            var changed = (list.length !== lastMessageCount) || (lastId !== lastMessageId);
                            if (changed || forceScroll) {
                                lastMessageCount = list.length;
                                lastMessageId = lastId;
                                if (typeof renderChatMessages === 'function') renderChatMessages(list, chatMessages, forceScroll);
                            }
                        })
                        .catch(function() { chatMessages.innerHTML = '<span class="text-muted small">{{ __("Unable to load messages.") }}</span>'; });
                }
                function renderChatMessages(messages, container, forceScroll) {
                    if (!container) return;
                    var scrollContainer = document.getElementById('contactPanelChatScrollArea') || container.parentElement;
                    var wasNearBottom = false;
                    if (scrollContainer && scrollContainer.scrollHeight > 0) {
                        var threshold = 80;
                        wasNearBottom = (scrollContainer.scrollHeight - scrollContainer.scrollTop - scrollContainer.clientHeight) <= threshold;
                    }
                    if (messages.length === 0) {
                        container.innerHTML = '<span class="text-muted small">{{ __("No messages yet. Send a message to start.") }}</span>';
                        if (scrollContainer) scrollContainer.scrollTop = 0;
                        return;
                    }
                    var html = '', lastDate = '';
                    var doubleCheck = `<span class="contact-chat-msg-read" aria-label="{{ __("Read") }}">@include($activeTemplate . 'partials.icon', ['name' => 'check-double'])</span>`;
                    messages.forEach(function(m) {
                        var datePart = m.date_label || (m.created_at || '').split(',')[0].trim();
                        if (datePart && datePart !== lastDate) {
                            lastDate = datePart;
                            html += '<div class="contact-chat-date-divider"><span>' + datePart + '</span></div>';
                        }
                        var cls = m.is_admin ? 'contact-chat-msg-admin' : 'contact-chat-msg-user';
                        var name = (m.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        var msg = (m.message || '').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
                        html += '<div class="contact-chat-msg ' + cls + '"><div class="contact-chat-msg-name">' + name + '</div><div class="contact-chat-msg-text">' + msg + '</div>';
                        if (m.attachments && m.attachments.length) {
                            html += `<div class="contact-chat-msg-att"><a href="${m.attachments[0] || '#'}" target="_blank">@include($activeTemplate . 'partials.icon', ['name' => 'paperclip']) {{ __("Attachment") }}</a></div>`;
                        }
                        var dateTimeStr = (m.date_label ? m.date_label + ' · ' : '') + (m.created_at || '');
                        if (m.is_admin) {
                            html += '<div class="contact-chat-msg-meta"><span class="contact-chat-msg-time">' + dateTimeStr + '</span>' + doubleCheck + '</div>';
                        } else {
                            html += '<div class="contact-chat-msg-time">' + dateTimeStr + '</div>';
                        }
                        html += '</div>';
                    });
                    container.innerHTML = html;
                    if (scrollContainer && (forceScroll || wasNearBottom)) {
                        scrollContainer.scrollTop = scrollContainer.scrollHeight;
                    }
                }
                if (selectRow) {
                    selectRow.addEventListener('click', function(e) {
                        var btn = e.target.closest('.contact-panel-select-btn');
                        if (!btn) return;
                        selectedChannel = btn.getAttribute('data-channel');
                        selectRow.querySelectorAll('.contact-panel-select-btn').forEach(function(b) {
                            b.classList.toggle('contact-panel-select-active', b === btn);
                        });
                        if (channelInput) channelInput.value = selectedChannel;
                        if (chatThread) {
                            if (selectedChannel === 'livechat') chatThread.classList.add('is-visible');
                            else chatThread.classList.remove('is-visible');
                        }
                        if (sendBtnOther) sendBtnOther.classList.toggle('d-none', selectedChannel === 'livechat');
                        if (selectedChannel === 'livechat') {
                            var subjEl = document.getElementById('contactPanelSubject');
                            if (subjEl && !subjEl.value) subjEl.value = 'Live Chat Message';
                            loadChatMessages();
                        }
                        var glass = document.getElementById('contactPanelGlass');
                        if (!glass) return;
                        var msg = (contactMsg && contactMsg.value) ? contactMsg.value.trim() : '';
                        if (selectedChannel === 'whatsapp') {
                            var waUrl = glass.getAttribute('data-whatsapp-url');
                            if (waUrl) {
                                var url = waUrl + (msg ? '?text=' + encodeURIComponent(msg) : '');
                                window.open(url, '_blank');
                                if (typeof notify === 'function') notify('success', '{{ __("Opening WhatsApp...") }}');
                            } else if (typeof notify === 'function') notify('error', '{{ __("WhatsApp is not configured. Add number in Admin → Contact.") }}');
                        } else if (selectedChannel === 'telegram') {
                            var tgUrl = glass.getAttribute('data-telegram-url');
                            if (tgUrl) {
                                var url = tgUrl + (msg ? '?text=' + encodeURIComponent(msg) : '');
                                window.open(url, '_blank');
                                if (typeof notify === 'function') notify('success', '{{ __("Opening Telegram...") }}');
                            } else if (typeof notify === 'function') notify('error', '{{ __("Telegram is not configured. Add username in Admin → Contact.") }}');
                        } else if (selectedChannel === 'email') {
                            var mailUrl = glass.getAttribute('data-email-url');
                            if (mailUrl) {
                                var subj = contactForm && contactForm.querySelector('select[name="subject"]') ? (contactForm.querySelector('select[name="subject"]').value || '') : '';
                                var body = msg || '';
                                var mailto = mailUrl;
                                var sep = mailUrl.indexOf('?') !== -1 ? '&' : '?';
                                if (subj) mailto += sep + 'subject=' + encodeURIComponent(subj);
                                if (body) mailto += (mailto.indexOf('?') !== -1 ? '&' : '?') + 'body=' + encodeURIComponent(body);
                                window.location.href = mailto;
                                if (typeof notify === 'function') notify('success', '{{ __("Opening email...") }}');
                            } else if (typeof notify === 'function') notify('error', '{{ __("Email is not configured. Add email in Admin → Contact.") }}');
                        }
                    });
                }

                if (contactMsg && contactCharCount) {
                    contactMsg.addEventListener('input', function() {
                        var n = (contactMsg.value || '').length;
                        contactCharCount.textContent = n;
                        var counterEl = contactCharCount.closest('.contact-char-counter');
                        if (counterEl) counterEl.classList.toggle('at-limit', n >= 500);
                    });
                }


                function getMsg() { return (contactMsg && contactMsg.value) ? contactMsg.value.trim() : ''; }
                var glass = document.getElementById('contactPanelGlass');
                var channelRedirectUrl = glass ? glass.getAttribute('data-channel-redirect-url') : '';

                if (sendBtn) {
                    sendBtn.addEventListener('click', function() {
                        var msg = getMsg();
                        if (selectedChannel === 'livechat') {
                            if (!contactForm) return;
                            var subjEl = contactForm.querySelector('select[name="subject"]');
                            if (subjEl && !subjEl.value) subjEl.value = 'Live Chat Message';
                            if (!subjEl || !subjEl.value) { if (typeof notify === 'function') notify('error', '{{ __("Please select a subject.") }}'); return; }
                            if (!msg) { if (typeof notify === 'function') notify('error', '{{ __("Please type a message.") }}'); return; }
                            if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse && grecaptcha.getResponse().length === 0) {
                                var errEl = document.getElementById('g-recaptcha-error');
                                if (errEl) errEl.innerHTML = '<span class="text-danger">{{ __("Captcha field is required.") }}</span>';
                                return;
                            }
                            var btn = getActiveSendBtn();
                            if (btn) btn.disabled = true;
                            var formData = new FormData(contactForm);
                            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                            var csrfInput = contactForm.querySelector('input[name="_token"]');
                            var csrfToken = (csrfMeta && csrfMeta.getAttribute('content')) || (csrfInput && csrfInput.value) || '';
                            if (csrfInput && csrfToken) formData.set('_token', csrfToken);
                            fetch(contactForm.getAttribute('action'), { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                                .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, data: data }; }); })
                                .then(function(result) {
                                    if (result.ok && result.data.success && typeof notify === 'function') {
                                        notify('success', result.data.message);
                                        contactForm.reset();
                                        if (contactCharCount) contactCharCount.textContent = '0';
                                        var fileList = document.getElementById('contactPanelFilesList');
                                        if (fileList) fileList.innerHTML = '';
                                        var filePreviews = document.getElementById('contactPanelFilePreviews');
                                        if (filePreviews) filePreviews.innerHTML = '';
                                        var fileInput = document.getElementById('contactPanelFiles');
                                        if (fileInput) fileInput.value = '';
                                        var filesRow = document.getElementById('contactPanelFilesRow');
                                        if (filesRow) { filesRow.classList.add('d-none'); filesRow.classList.remove('d-flex'); }
                                        var subjEl = document.getElementById('contactPanelSubject');
                                        if (subjEl) subjEl.value = 'Live Chat Message';
                                        var userName = contactForm.getAttribute('data-user-name');
                                        var userEmail = contactForm.getAttribute('data-user-email');
                                        if (userName) { var n = document.getElementById('contactPanelName'); if (n) n.value = userName; }
                                        if (userEmail) { var e = document.getElementById('contactPanelEmail'); if (e) e.value = userEmail; }
                                        var chatMessages = document.getElementById('contactPanelChatMessages');
                                        if (chatMessages && typeof renderChatMessages === 'function') {
                                            if (result.data.messages && result.data.messages.length) {
                                                renderChatMessages(result.data.messages, chatMessages, true);
                                            } else if (typeof loadChatMessages === 'function') {
                                                loadChatMessages(true);
                                            }
                                        }
                                    } else if (typeof notify === 'function') {
                                        var errMsg = (result.data && result.data.message) ? result.data.message : '{{ __("Something went wrong.") }}';
                                        if (result.data && result.data.errors) {
                                            var errs = result.data.errors;
                                            errMsg = Object.keys(errs).map(function(k) { return errs[k][0]; }).join(' ');
                                        }
                                        notify('error', errMsg);
                                    }
                                })
                                .catch(function() { if (typeof notify === 'function') notify('error', '{{ __("Something went wrong.") }}'); })
                                .finally(function() { var b = getActiveSendBtn(); if (b) b.disabled = false; });
                            return;
                        }
                        if (!msg) { if (typeof notify === 'function') notify('error', '{{ __("Please type a message.") }}'); return; }
                        if (selectedChannel === 'whatsapp' || selectedChannel === 'telegram' || selectedChannel === 'email') {
                            if (!channelRedirectUrl) { if (typeof notify === 'function') notify('error', '{{ __("Something went wrong.") }}'); return; }
                            var nameVal = contactForm && contactForm.querySelector('input[name="name"]') ? contactForm.querySelector('input[name="name"]').value : '';
                            var subjVal = contactForm && contactForm.querySelector('select[name="subject"]') ? contactForm.querySelector('select[name="subject"]').value : '';
                            var emailVal = contactForm && contactForm.querySelector('input[name="email"]') ? contactForm.querySelector('input[name="email"]').value : '';
                            var body = new FormData();
                            body.append('channel', selectedChannel);
                            body.append('message', msg);
                            body.append('name', nameVal || '');
                            body.append('subject', subjVal || '');
                            body.append('email', emailVal || '');
                            var csrf = document.querySelector('meta[name="csrf-token"]');
                            if (csrf) body.append('_token', csrf.getAttribute('content'));
                            var btn = getActiveSendBtn();
                            if (btn) btn.disabled = true;
                            fetch(channelRedirectUrl, { method: 'POST', body: body, headers: { 'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                                .then(function(r) { return r.json().then(function(data) { return { ok: r.ok, status: r.status, data: data }; }); })
                                .then(function(result) {
                                    if (result.ok && result.data.success) {
                                        if (result.data.redirect) {
                                            window.open(result.data.redirect, '_blank');
                                            if (typeof notify === 'function') notify('success', selectedChannel === 'whatsapp' ? '{{ __("Opening WhatsApp...") }}' : '{{ __("Opening Telegram...") }}');
                                        } else {
                                            if (typeof notify === 'function') notify('success', result.data.message || '{{ __("Message sent!") }}');
                                            contactForm.reset();
                                            if (contactCharCount) contactCharCount.textContent = '0';
                                            closeContactPanel();
                                        }
                                    } else if (typeof notify === 'function') {
                                        notify('error', (result.data && result.data.message) ? result.data.message : '{{ __("This channel is not configured. Add in Admin → Contact.") }}');
                                    }
                                })
                                .catch(function() { if (typeof notify === 'function') notify('error', '{{ __("Something went wrong.") }}'); })
                                .finally(function() { var b = getActiveSendBtn(); if (b) b.disabled = false; });
                            return;
                        }
                        if (typeof notify === 'function') notify('error', '{{ __("Please select a channel.") }}');
                    });
                }
                if (sendBtnOther) sendBtnOther.addEventListener('click', function() { if (sendBtn) sendBtn.dispatchEvent(new MouseEvent('click', { bubbles: true })); });

                var dropzone = document.getElementById('contactPanelDropzone');
                var plusBtn = document.getElementById('contactPanelPlusBtn');
                var fileInput = document.getElementById('contactPanelFiles');
                var fileListEl = document.getElementById('contactPanelFilesList');
                var filePreviewsEl = document.getElementById('contactPanelFilePreviews');
                var filesRow = document.getElementById('contactPanelFilesRow');
                var imageTypes = /^image\/(jpeg|jpg|png|gif|webp)$/i;
                function updateContactPanelFileList() {
                    if (!fileListEl || !fileInput) return;
                    fileListEl.innerHTML = '';
                    if (filePreviewsEl) filePreviewsEl.innerHTML = '';
                    for (var i = 0; i < fileInput.files.length; i++) {
                        var f = fileInput.files[i];
                        var span = document.createElement('span');
                        span.className = 'contact-panel-file-tag me-1 mb-1';
                        span.innerHTML = '<svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"></path><path d="M14 2v5h5"></path></svg> ' + f.name + ' <small>(' + (f.size / 1024).toFixed(1) + ' KB)</small>';
                        fileListEl.appendChild(span);
                        if (filePreviewsEl && f.type && imageTypes.test(f.type)) {
                            var wrap = document.createElement('div');
                            wrap.className = 'contact-panel-file-preview-item';
                            var img = document.createElement('img');
                            img.src = URL.createObjectURL(f);
                            img.alt = f.name;
                            var cap = document.createElement('span');
                            cap.className = 'contact-panel-file-preview-name';
                            cap.textContent = f.name;
                            wrap.appendChild(img);
                            wrap.appendChild(cap);
                            filePreviewsEl.appendChild(wrap);
                        }
                    }
                    if (filesRow) {
                        if (fileInput.files.length > 0) {
                            filesRow.classList.remove('d-none');
                            filesRow.classList.add('d-flex', 'flex-wrap', 'gap-2');
                        } else {
                            filesRow.classList.add('d-none');
                            filesRow.classList.remove('d-flex', 'flex-wrap', 'gap-2');
                        }
                    }
                }
                if (plusBtn && fileInput) {
                    plusBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        fileInput.click();
                    });
                    fileInput.addEventListener('change', updateContactPanelFileList);
                }
                var emojiBtn = document.getElementById('contactPanelEmojiBtn');
                var micBtn = document.getElementById('contactPanelMicBtn');
                if (contactMsg) {
                    contactMsg.addEventListener('input', function() {
                        if (this.classList.contains('contact-panel-msg-floating')) {
                            this.style.height = 'auto';
                            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                            return;
                        }
                        this.style.height = 'auto';
                        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                    });
                }
                if (emojiBtn && contactMsg) {
                    var emojiList = ['😀','😃','😄','😁','😅','😂','🤣','😊','😇','🙂','😉','😍','🥰','😘','👍','👋','❤️','🙏','🔥','✨','👍','👌','😎','🥳','😢','😭','🤔','👀'];
                    var emojiPop = document.createElement('div');
                    emojiPop.id = 'contactPanelEmojiPop';
                    emojiPop.className = 'contact-panel-emoji-pop';
                    emojiPop.innerHTML = emojiList.map(function(em){ return '<button type="button" class="contact-panel-emoji-item" data-emoji="'+em+'" aria-label="Emoji">'+em+'</button>'; }).join('');
                    document.body.appendChild(emojiPop);
                    emojiBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var pop = document.getElementById('contactPanelEmojiPop');
                        if (pop.classList.contains('open')) { pop.classList.remove('open'); return; }
                        var rect = emojiBtn.getBoundingClientRect();
                        pop.style.left = rect.left + 'px';
                        pop.style.top = (rect.top - 200) + 'px';
                        pop.classList.add('open');
                    });
                    document.getElementById('contactPanelEmojiPop').addEventListener('click', function(e) {
                        var btn = e.target.closest('.contact-panel-emoji-item');
                        if (!btn || !contactMsg) return;
                        var em = btn.getAttribute('data-emoji');
                        var start = contactMsg.selectionStart, end = contactMsg.selectionEnd, val = contactMsg.value;
                        contactMsg.value = val.slice(0, start) + em + val.slice(end);
                        contactMsg.selectionStart = contactMsg.selectionEnd = start + em.length;
                        contactMsg.focus();
                        if (contactCharCount) contactCharCount.textContent = contactMsg.value.length;
                        document.getElementById('contactPanelEmojiPop').classList.remove('open');
                    });
                    document.addEventListener('click', function(e) {
                        if (!e.target.closest('#contactPanelEmojiPop') && !e.target.closest('#contactPanelEmojiBtn'))
                            document.getElementById('contactPanelEmojiPop').classList.remove('open');
                    });
                }
                if (micBtn && contactMsg) {
                    var voiceRecognition = null;
                    var voiceRecording = false;
                    micBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (voiceRecording && voiceRecognition) {
                            try { voiceRecognition.abort(); } catch (err) {}
                            micBtn.classList.remove('recording');
                            voiceRecording = false;
                            voiceRecognition = null;
                            return;
                        }
                        var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                        if (!SpeechRecognition) {
                            if (typeof notify === 'function') notify('info', '{{ __("Voice input not supported. Use Chrome or Edge.") }}');
                            return;
                        }
                        try {
                            voiceRecognition = new SpeechRecognition();
                            voiceRecognition.continuous = false;
                            voiceRecognition.interimResults = false;
                            var lang = (document.documentElement.lang || '').toLowerCase();
                            voiceRecognition.lang = (lang === 'bn' || lang.indexOf('bn') === 0) ? 'bn-BD' : (lang || 'en-US');
                            voiceRecognition.onresult = function(event) {
                                var t = event.results[event.results.length - 1][0].transcript;
                                contactMsg.value = (contactMsg.value + (contactMsg.value ? ' ' : '') + t).slice(0, 500);
                                if (contactCharCount) contactCharCount.textContent = contactMsg.value.length;
                            };
                            voiceRecognition.onerror = function() {
                                micBtn.classList.remove('recording');
                                voiceRecording = false;
                                voiceRecognition = null;
                                if (typeof notify === 'function') notify('error', '{{ __("Voice input failed. Try again.") }}');
                            };
                            voiceRecognition.onend = function() {
                                micBtn.classList.remove('recording');
                                voiceRecording = false;
                                voiceRecognition = null;
                            };
                            voiceRecognition.start();
                            micBtn.classList.add('recording');
                            voiceRecording = true;
                        } catch (err) {
                            micBtn.classList.remove('recording');
                            voiceRecording = false;
                            voiceRecognition = null;
                            if (typeof notify === 'function') notify('error', '{{ __("Voice not supported in this browser.") }}');
                        }
                    });
                }
                var chatPollTimer = null;
                function startChatPoll() {
                    if (chatPollTimer) return;
                    chatPollTimer = setInterval(function() {
                        if (!contactPanel || !contactPanel.classList.contains('is-open') || selectedChannel !== 'livechat') return;
                        loadChatMessages(false);
                    }, 10000);
                }
                function stopChatPoll() { if (chatPollTimer) { clearInterval(chatPollTimer); chatPollTimer = null; } }
                if (contactPanel) {
                    var obs = new MutationObserver(function() {
                        if (contactPanel.classList.contains('is-open') && selectedChannel === 'livechat') startChatPoll();
                        else stopChatPoll();
                    });
                    obs.observe(contactPanel, { attributes: true, attributeFilter: ['class'] });
                }
                if (dropzone && fileInput) {
                    dropzone.addEventListener('dragover', function(e) { e.preventDefault(); this.classList.add('contact-panel-dropzone-active'); });
                    dropzone.addEventListener('dragleave', function(e) { e.preventDefault(); this.classList.remove('contact-panel-dropzone-active'); });
                    dropzone.addEventListener('drop', function(e) { e.preventDefault(); this.classList.remove('contact-panel-dropzone-active'); if (e.dataTransfer.files.length) fileInput.files = e.dataTransfer.files; updateContactPanelFileList(); });
                }

                /* Rotate floating contact icons (chat/WhatsApp/Telegram/email) */
                var floatBtn = document.getElementById('contactFloatBtn');
                if (floatBtn) {
                    var icons = floatBtn.querySelectorAll('.contact-float-btn-icon[data-icon]');
                    if (icons.length > 1) {
                        var idx = 0;
                        setInterval(function() {
                            icons.forEach(function(el) { el.classList.remove('contact-float-btn-icon--active'); el.style.position = 'absolute'; });
                            var active = icons[idx];
                            if (active) { active.classList.add('contact-float-btn-icon--active'); active.style.position = 'relative'; }
                            idx = (idx + 1) % icons.length;
                        }, 2500);
                    }
                }

                var loggedOut = (function() { var m = window.location.search.match(/[?&]logged_out=1/); return m ? true : false; })();
                if (loggedOut && typeof notify === 'function') {
                    notify('success', '{{ __("You have been logged out.") }}');
                }
                document.addEventListener('click', function(e) {
                    var btn = e.target.closest('.password-toggle-btn');
                    if (!btn) return;
                    e.preventDefault();
                    var id = btn.getAttribute('data-target');
                    var input = id ? document.getElementById(id) : null;
                    var icon = btn.querySelector('.password-toggle-icon');
                    if (!input || !icon) return;
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.add('is-password-visible');
                        btn.setAttribute('aria-label', '{{ __("Hide password") }}');
                        btn.setAttribute('title', '{{ __("Hide password") }}');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('is-password-visible');
                        btn.setAttribute('aria-label', '{{ __("Show password") }}');
                        btn.setAttribute('title', '{{ __("Show password") }}');
                    }
                });
            });
        </script>
        <script>
            (function() {
                'use strict';
                if (window.__staylVanillaCartWishlistInit) return;
                window.__staylVanillaCartWishlistInit = true;

                var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
                var GUEST_CART_KEY = 'staylbd_guest_cart';
                var GUEST_WISHLIST_KEY = 'staylbd_guest_wishlist';

                function notifySafe(type, msg) {
                    if (typeof window.notify === 'function') window.notify(type, msg);
                }
                function setTextAll(selector, value) {
                    document.querySelectorAll(selector).forEach(function(el) { el.textContent = String(value); });
                }
                function fetchJson(url, options) {
                    return fetch(url, options).then(function(r) { return r.json(); });
                }
                function postForm(url, dataObj) {
                    var body = new URLSearchParams();
                    Object.keys(dataObj || {}).forEach(function(k) { body.append(k, dataObj[k]); });
                    return fetchJson(url, {
                        method: 'POST',
                        body: body,
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        }
                    });
                }

                function pulseHeaderTarget(type) {
                    var target = window.getHeaderTarget && window.getHeaderTarget(type);
                    if (target && window.bounceHeaderIcon) window.bounceHeaderIcon(target);
                }

                /** Immediate tap feedback — does not wait for AJAX (fixes “animation after 2s” feeling). */
                function flashPress(el) {
                    if (!el) return;
                    el.classList.add('stayl-action-pressed');
                    requestAnimationFrame(function() {
                        requestAnimationFrame(function() {
                            el.classList.remove('stayl-action-pressed');
                        });
                    });
                }

                window.getCartCount = function() {
                    return fetchJson("{{ route('cart.list.count') }}", { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                        .then(function(response) {
                            var count = (response && response.count != null) ? response.count : ((typeof response === 'number') ? response : 0);
                            setTextAll('.show-cart-count', count);
                            try {
                                if (response && Array.isArray(response.items) && response.items.length) localStorage.setItem(GUEST_CART_KEY, JSON.stringify(response.items));
                                else localStorage.removeItem(GUEST_CART_KEY);
                            } catch (e) {}
                            var items = (response && Array.isArray(response.items)) ? response.items : [];
                            var pidInCart = {};
                            items.forEach(function(it) {
                                var p = it && (it.product_id != null) ? parseInt(it.product_id, 10) : 0;
                                if (p > 0) pidInCart[p] = true;
                            });
                            document.querySelectorAll('.cart-btn, .add-to-cart').forEach(function(btn) {
                                var pid = parseInt(btn.getAttribute('data-product_id') || btn.dataset.product_id || btn.dataset.productId || '0', 10);
                                btn.classList.toggle('in-cart', pid > 0 && !!pidInCart[pid]);
                            });
                            return response;
                        }).catch(function() {
                            setTextAll('.show-cart-count', 0);
                            document.querySelectorAll('.cart-btn, .add-to-cart').forEach(function(btn) { btn.classList.remove('in-cart'); });
                        });
                };

                window.getWishlistCount = function() {
                    return fetchJson("{{ route('wish.list.count') }}", { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                        .then(function(response) {
                            var items = Array.isArray(response) ? response : (response && response.items ? response.items : []);
                            setTextAll('.show-wishlist-count', items.length || 0);
                            document.querySelectorAll('.add-wishlist').forEach(function(btn) { btn.classList.remove('active', 'added'); });
                            items.forEach(function(value) {
                                var pid = value && (value.product_id != null) ? value.product_id : value;
                                if (pid == null) return;
                                document.querySelectorAll('[data-product_id="' + pid + '"]').forEach(function(el) {
                                    if (el.classList.contains('add-wishlist') || el.classList.contains('btn-wishlist')) el.classList.add('active', 'added');
                                });
                            });
                            try {
                                if (items.length > 0) localStorage.setItem(GUEST_WISHLIST_KEY, JSON.stringify(items));
                                else localStorage.removeItem(GUEST_WISHLIST_KEY);
                            } catch (e) {}
                            return response;
                        }).catch(function() { setTextAll('.show-wishlist-count', 0); });
                };

                window.getCompareCount = function() {
                    return fetchJson("{{ route('compare.count') }}", { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                        .then(function(response) {
                            var count = Math.min(Math.max(0, parseInt((response && response.count != null) ? response.count : 0, 10) || 0), 200);
                            setTextAll('.show-compare-count', count);
                            var ids = (response && Array.isArray(response.product_ids)) ? response.product_ids : [];
                            document.querySelectorAll('.add-to-compare, .btn-compare').forEach(function(btn) {
                                var pid = parseInt(btn.getAttribute('data-product_id') || btn.dataset.productId || btn.dataset.product_id, 10);
                                btn.classList.toggle('in-compare', !!pid && ids.indexOf(pid) >= 0);
                            });
                            return response;
                        }).catch(function() { setTextAll('.show-compare-count', 0); });
                };

                document.addEventListener('submit', function(e) {
                    var form = e.target.closest('.newletter-form, .js-footer-subscribe');
                    if (!form) return;
                    e.preventDefault();
                    var btn = form.querySelector('.subscribe-btn');
                    var emailInput = form.querySelector('.subscribe-email');
                    var msgBox = form.querySelector('.subscribe-inline-message');
                    var email = ((emailInput && emailInput.value) || '').trim();
                    if (!email || (btn && btn.disabled)) return;
                    if (msgBox) { msgBox.textContent = ''; msgBox.classList.remove('text-success', 'text-danger'); }
                    if (btn) { btn.disabled = true; btn.classList.add('loading'); }
                    postForm("{{ route('subscribe') }}", { email: email, _token: csrf })
                        .then(function(response) {
                            if (response && response.success) {
                                if (emailInput) emailInput.value = '';
                                if (msgBox) { msgBox.textContent = response.success; msgBox.classList.add('text-success'); }
                                notifySafe('success', response.success);
                            } else {
                                var err = response && response.error ? (response.error.email || response.error) : '{{ __("Something went wrong.") }}';
                                var msg = Array.isArray(err) ? err[0] : err;
                                if (msgBox) { msgBox.textContent = msg; msgBox.classList.add('text-danger'); }
                                notifySafe('error', msg);
                            }
                        }).catch(function() {
                            var msg = '{{ __("Could not subscribe. Try again.") }}';
                            if (msgBox) { msgBox.textContent = msg; msgBox.classList.add('text-danger'); }
                            notifySafe('error', msg);
                        }).finally(function() {
                            if (btn) { btn.disabled = false; btn.classList.remove('loading'); }
                        });
                }, true);

                document.addEventListener('click', function(e) {
                    var qvBtn = e.target.closest('.quickView');
                    if (qvBtn) {
                        e.preventDefault();
                        var qvPid = parseInt(qvBtn.getAttribute('data-product_id') || qvBtn.dataset.product_id || qvBtn.dataset.productId, 10);
                        if (!qvPid) return;
                        var qvModalBody = document.getElementById('productmodalView');
                        if (!qvModalBody) return;
                        qvModalBody.innerHTML = '<div class="text-center py-4 text-muted">{{ __("Loading...") }}</div>';
                        fetch("{{ route('product.quickView') }}?product_id=" + encodeURIComponent(qvPid), {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html'
                            }
                        }).then(function(r) { return r.text(); })
                          .then(function(html) {
                              qvModalBody.innerHTML = html || '';
                              if (typeof window.refreshStaylLucide === 'function') window.refreshStaylLucide(qvModalBody);
                              if (window.StaylModal && typeof window.StaylModal.show === 'function') {
                                  window.StaylModal.show('quickView');
                              } else {
                                  var qvModal = document.getElementById('quickView');
                                  if (qvModal) {
                                      qvModal.classList.add('is-open', 'show');
                                      qvModal.style.display = 'block';
                                  }
                              }
                          }).catch(function() {
                              qvModalBody.innerHTML = '<div class="text-center py-4 text-danger">{{ __("Could not load quick view.") }}</div>';
                              notifySafe('error', '{{ __("Could not load quick view.") }}');
                          });
                        return;
                    }

                    var wlBtn = e.target.closest('.add-wishlist, .btn-wishlist');
                    if (wlBtn) {
                        e.preventDefault();
                        var productId = parseInt(wlBtn.getAttribute('data-product_id') || wlBtn.dataset.product_id || wlBtn.dataset.productId, 10);
                        if (!productId) return;
                        var inWishlist = wlBtn.classList.contains('active') || wlBtn.classList.contains('added');
                        if (inWishlist) {
                            flashPress(wlBtn);
                            fetchJson("{{ route('wish.list.remove') }}?product_id=" + encodeURIComponent(productId), { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                                .then(function(r) {
                                    if (r && r.success) {
                                        wlBtn.classList.remove('active', 'added');
                                        notifySafe('success', r.success);
                                        if (window.getWishlistCount) window.getWishlistCount();
                                    } else notifySafe('error', (r && r.error) || '{{ __("Could not remove from wishlist.") }}');
                                }).catch(function() { notifySafe('error', '{{ __("Could not remove from wishlist.") }}'); });
                        } else {
                            flashPress(wlBtn);
                            var wlImg = window.getProductImageFromButton ? window.getProductImageFromButton(wlBtn) : null;
                            var wlTarget = window.getHeaderTarget ? window.getHeaderTarget('wishlist') : null;
                            if (window.flyToHeader && wlImg && wlTarget) {
                                window.flyToHeader(wlImg, wlTarget, function() {
                                    if (window.getWishlistCount) window.getWishlistCount();
                                    pulseHeaderTarget('wishlist');
                                });
                            } else {
                                if (window.getWishlistCount) window.getWishlistCount();
                                pulseHeaderTarget('wishlist');
                            }
                            postForm("{{ route('wish.list.add') }}", { product_id: productId, _token: csrf }).then(function(r) {
                                if (r && r.success) {
                                    wlBtn.classList.add('active', 'added');
                                    notifySafe('success', r.success);
                                    if (window.getWishlistCount) window.getWishlistCount();
                                } else {
                                    notifySafe('error', (r && (r.error || r.message)) || '{{ __("Could not add to wishlist.") }}');
                                    if (window.getWishlistCount) window.getWishlistCount();
                                }
                            }).catch(function() {
                                notifySafe('error', '{{ __("Could not add to wishlist.") }}');
                                if (window.getWishlistCount) window.getWishlistCount();
                            });
                        }
                        return;
                    }

                    var cartBtn = e.target.closest('.add-to-cart, .btn-cart');
                    if (cartBtn) {
                        e.preventDefault();
                        var pid = parseInt(cartBtn.getAttribute('data-product_id') || cartBtn.dataset.product_id || cartBtn.dataset.productId, 10);
                        if (!pid) return;
                        if (cartBtn.classList.contains('in-cart')) {
                            flashPress(cartBtn);
                            postForm("{{ route('cart.list.remove') }}", { product_id: pid, all: '1', _token: csrf }).then(function(r) {
                                if (r && r.success) {
                                    notifySafe('success', r.success);
                                    if (window.getCartCount) window.getCartCount();
                                } else {
                                    notifySafe('error', (r && (r.error || r.message)) || '{{ __("Could not update cart.") }}');
                                    if (window.getCartCount) window.getCartCount();
                                }
                            }).catch(function() {
                                notifySafe('error', '{{ __("Could not update cart.") }}');
                                if (window.getCartCount) window.getCartCount();
                            });
                            return;
                        }
                        flashPress(cartBtn);
                        var cartSrcImg = window.getProductImageFromButton ? window.getProductImageFromButton(cartBtn) : null;
                        var cartTargetIcon = window.getHeaderTarget ? window.getHeaderTarget('cart') : null;
                        if (window.flyToHeader && cartSrcImg && cartTargetIcon) {
                            window.flyToHeader(cartSrcImg, cartTargetIcon, function() {
                                if (window.getCartCount) window.getCartCount();
                                pulseHeaderTarget('cart');
                            });
                        } else {
                            if (window.getCartCount) window.getCartCount();
                            pulseHeaderTarget('cart');
                        }
                        postForm("{{ route('cart.list.add') }}", { product_id: pid, quantity: 1, _token: csrf }).then(function(r) {
                            if (r && r.success) {
                                notifySafe('success', r.success);
                                cartBtn.classList.add('in-cart');
                                if (window.getCartCount) window.getCartCount();
                            } else {
                                notifySafe('error', (r && (r.error || r.message)) || '{{ __("Could not add to cart.") }}');
                                if (window.getCartCount) window.getCartCount();
                            }
                        }).catch(function() {
                            notifySafe('error', '{{ __("Could not add to cart.") }}');
                            if (window.getCartCount) window.getCartCount();
                        });
                        return;
                    }

                    var cmpBtn = e.target.closest('.add-to-compare, .btn-compare');
                    if (cmpBtn) {
                        e.preventDefault();
                        var cpid = parseInt(cmpBtn.getAttribute('data-product_id') || cmpBtn.dataset.product_id || cmpBtn.dataset.productId, 10);
                        if (!cpid) return;
                        var isIn = cmpBtn.classList.contains('in-compare');
                        if (!isIn) {
                            flashPress(cmpBtn);
                            var cmpSrcImg = window.getProductImageFromButton ? window.getProductImageFromButton(cmpBtn) : null;
                            var cmpTargetIcon = window.getHeaderTarget ? window.getHeaderTarget('compare') : null;
                            if (window.flyToHeader && cmpSrcImg && cmpTargetIcon) {
                                window.flyToHeader(cmpSrcImg, cmpTargetIcon, function() {
                                    if (window.getCompareCount) window.getCompareCount();
                                    pulseHeaderTarget('compare');
                                });
                            } else {
                                if (window.getCompareCount) window.getCompareCount();
                                pulseHeaderTarget('compare');
                            }
                        }
                        postForm(isIn ? "{{ route('compare.remove') }}" : "{{ route('compare.add') }}", { product_id: cpid, _token: csrf }).then(function(r) {
                            if (r && r.success) {
                                if (window.getCompareCount) window.getCompareCount();
                                notifySafe('success', r.message || (isIn ? '{{ __("Removed from comparison.") }}' : '{{ __("Added to comparison.") }}'));
                            } else {
                                notifySafe('error', (r && r.message) || '{{ __("Could not update comparison.") }}');
                                if (window.getCompareCount) window.getCompareCount();
                            }
                        }).catch(function() {
                            notifySafe('error', '{{ __("Could not update comparison.") }}');
                            if (window.getCompareCount) window.getCompareCount();
                        });
                    }
                }, true);

                function runInitialCounts() {
                    if (window.getWishlistCount) window.getWishlistCount();
                    if (window.getCartCount) window.getCartCount();
                    if (window.getCompareCount) window.getCompareCount();
                }
                if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', runInitialCounts);
                else runInitialCounts();
            })();
        </script>
        <script>
            (function(){
                function updateOfferTimerBars() {
                    document.querySelectorAll('.offer-timer-bar[data-end-ts]').forEach(function(bar) {
                        var endTs = parseInt(bar.getAttribute('data-end-ts'), 10), d = endTs - Date.now();
                        var wrap = bar.querySelector('.offer-timer-bar__countdown');
                        if (!wrap) return;
                        var hEl = wrap.querySelector('.countdown-hours'), mEl = wrap.querySelector('.countdown-mins'), sEl = wrap.querySelector('.countdown-secs');
                        if (d <= 0) { if (hEl) hEl.textContent = '00'; if (mEl) mEl.textContent = '00'; if (sEl) sEl.textContent = '00'; return; }
                        var h = Math.floor(d/3600000), m = Math.floor((d%3600000)/60000), s = Math.floor((d%60000)/1000);
                        if (hEl) hEl.textContent = ('0'+h).slice(-2); if (mEl) mEl.textContent = ('0'+m).slice(-2); if (sEl) sEl.textContent = ('0'+s).slice(-2);
                    });
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function() { updateOfferTimerBars(); setInterval(updateOfferTimerBars, 1000); });
                } else {
                    updateOfferTimerBars();
                    setInterval(updateOfferTimerBars, 1000);
                }
            })();
        </script>
        {{-- Guest Quick Order modal: open on click, load location data and submit via AJAX --}}
        <script>
            (function() {
                var guestModal = document.getElementById('guestCheckoutModal');
                var guestForm = document.getElementById('guestCheckoutForm');
                var guestCheckoutLocationLoaded = false;
                function byId(id) { return document.getElementById(id); }
                function openGuestCheckoutModal() {
                    if (guestModal && window.StaylModal && typeof window.StaylModal.show === 'function') {
                        window.StaylModal.show(guestModal);
                    }
                }
                function closeGuestCheckoutModal() {
                    if (guestModal && window.StaylModal && typeof window.StaylModal.hide === 'function') {
                        window.StaylModal.hide(guestModal);
                    }
                }
                function refreshCartCountBadge() {
                    fetch("{{ route('cart.list.count') }}", {
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).then(function(r){ return r.json(); }).then(function(response){
                        var count = (response && response.count != null) ? response.count : ((typeof response === 'number') ? response : 0);
                        document.querySelectorAll('.show-cart-count').forEach(function(el){ el.textContent = String(count); });
                    }).catch(function(){});
                }

                document.addEventListener('click', function(e) {
                    var t = e.target.closest('a[data-bs-target="#guestCheckoutModal"], a#openGuestCheckoutBtn, a#openGuestCheckoutBtnInline, button[data-bs-target="#guestCheckoutModal"], button#openGuestCheckoutBtn, button#openGuestCheckoutBtnInline');
                    if (t) {
                        var href = t.getAttribute('href') || t.href || '';
                        if (href && (href.indexOf('open_guest_checkout=1') !== -1 || href.indexOf('cart/quickorder') !== -1) && typeof history !== 'undefined' && history.pushState) {
                            history.pushState({}, '', href);
                        }
                        e.preventDefault();
                        e.stopPropagation();
                        openGuestCheckoutModal();
                        return false;
                    }
                }, true);

                window.addEventListener('popstate', function() {
                    closeGuestCheckoutModal();
                });

                function loadGuestCheckoutLocations() {
                    if (guestCheckoutLocationLoaded) return;
                    if (!guestForm) return;
                    fetch('{{ route("guest.checkout.location.data") }}', {
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).then(function(r){ return r.json(); }).then(function(res) {
                        if (!res || !res.success) return;
                        guestCheckoutLocationLoaded = true;
                        var divisions = res.divisions || [];
                        var districtsByDiv = res.districts_by_division || {};
                        var thanasByDistrict = res.thanas_by_district || {};
                        var divEl = byId('guest_division'), distEl = byId('guest_district'), thanaEl = byId('guest_thana');
                        if (!divEl || !distEl || !thanaEl) return;
                        Array.prototype.slice.call(divEl.querySelectorAll('option:not(:first-child)')).forEach(function(o){ o.remove(); });
                        divisions.forEach(function(d) {
                            var id = d.id || d[0];
                            var name = (d.name_en || d[1] || d).toString();
                            var opt = document.createElement('option');
                            opt.value = name;
                            opt.textContent = name;
                            opt.setAttribute('data-id', String(id));
                            divEl.appendChild(opt);
                        });

                        divEl.addEventListener('change', function() {
                            var divName = divEl.value;
                            Array.prototype.slice.call(distEl.querySelectorAll('option:not(:first-child)')).forEach(function(o){ o.remove(); });
                            Array.prototype.slice.call(thanaEl.querySelectorAll('option:not(:first-child)')).forEach(function(o){ o.remove(); });
                            if (!divName) return;
                            var divId = divisions.find(function(x) { return (x.name_en || x[1]) === divName; });
                            divId = divId ? (divId.id || divId[0]) : divName;
                            var dists = districtsByDiv[divId] || [];
                            dists.forEach(function(d) {
                                var en = (d.en || d.name_en || d).toString();
                                var opt = document.createElement('option');
                                opt.value = en;
                                opt.textContent = en;
                                distEl.appendChild(opt);
                            });
                        });

                        distEl.addEventListener('change', function() {
                            var distName = distEl.value;
                            Array.prototype.slice.call(thanaEl.querySelectorAll('option:not(:first-child)')).forEach(function(o){ o.remove(); });
                            if (!distName) return;
                            var thanas = thanasByDistrict[distName] || [];
                            thanas.forEach(function(t) {
                                var en = (t.en || t.name_en || (typeof t === 'object' ? t.en : t)).toString();
                                var opt = document.createElement('option');
                                opt.value = en;
                                opt.textContent = en;
                                thanaEl.appendChild(opt);
                            });
                        });

                        if (res.countries && Array.isArray(res.countries)) {
                            var countryEl = byId('guest_country');
                            if (!countryEl) return;
                            Array.prototype.slice.call(countryEl.querySelectorAll('option:not(:first-child)')).forEach(function(o){ o.remove(); });
                            res.countries.forEach(function(c) {
                                var name = (c.name || c.country || c).toString();
                                var opt = document.createElement('option');
                                opt.value = name;
                                opt.textContent = name;
                                countryEl.appendChild(opt);
                            });
                            if (countryEl.querySelector('option[value="Bangladesh"]')) countryEl.value = 'Bangladesh';
                        }
                    }).catch(function(){});
                }

                if (guestModal) {
                    guestModal.addEventListener('stayl:modal:shown', function() {
                        loadGuestCheckoutLocations();
                    });
                }

                if (document.getElementById('guestOrderPage')) {
                    loadGuestCheckoutLocations();
                }

                document.addEventListener('submit', function(e) {
                    if (!e.target || e.target.id !== 'guestCheckoutForm') return;
                    e.preventDefault();
                    var form = e.target;
                    var btn = byId('guestCheckoutSubmitBtn');
                    var successEl = byId('guestCheckoutSuccess');
                    var errorEl = byId('guestCheckoutError');
                    if (successEl) successEl.classList.add('d-none');
                    if (errorEl) errorEl.classList.add('d-none');
                    Array.prototype.slice.call(form.querySelectorAll('.is-invalid')).forEach(function(el){ el.classList.remove('is-invalid'); });
                    var areaCity = ((byId('guest_area_city') || {}).value || '').trim();
                    if (byId('guest_district_hidden')) byId('guest_district_hidden').value = areaCity;
                    if (byId('guest_city')) byId('guest_city').value = areaCity;
                    if (btn) {
                        btn.disabled = true;
                        var t = btn.querySelector('.btn-text');
                        var sp = btn.querySelector('.spinner-border');
                        if (t) t.classList.add('d-none');
                        if (sp) sp.classList.remove('d-none');
                    }

                    var formData = new FormData(form);
                    var payload = new URLSearchParams();
                    formData.forEach(function(v, k){ payload.append(k, v); });
                    fetch('{{ route("guest.checkout.order") }}', {
                        method: 'POST',
                        body: payload,
                        headers: {
                            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') || {}).content || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                        }
                    }).then(function(r){
                        return r.json().then(function(data){ return { ok: r.ok, data: data }; });
                    }).then(function(resp) {
                        var data = resp.data || {};
                        if (data.success) {
                            form.classList.add('d-none');
                            if (byId('guestCheckoutSuccessMessage')) byId('guestCheckoutSuccessMessage').textContent = data.message || '{{ __("Your order has been successfully placed. Our team will contact you shortly.") }}';
                            if (successEl) successEl.classList.remove('d-none');
                            if (typeof getCartCount === 'function') getCartCount();
                            refreshCartCountBadge();
                            setTimeout(function() {
                                closeGuestCheckoutModal();
                                window.location.href = '{{ route("user.cart") }}';
                            }, 3000);
                        } else {
                            if (byId('guestCheckoutErrorMessage')) byId('guestCheckoutErrorMessage').textContent = data.message || '{{ __("Something went wrong.") }}';
                            if (errorEl) errorEl.classList.remove('d-none');
                        }
                    }).catch(function(xhr) {
                        var msg = '{{ __("Something went wrong. Please try again.") }}';
                        var payload = xhr && xhr.data ? xhr.data : null;
                        if (payload && payload.message) msg = payload.message;
                        else if (payload && payload.errors) {
                            var first = Object.values(payload.errors)[0];
                            msg = Array.isArray(first) ? first[0] : first;
                        }
                        if (byId('guestCheckoutErrorMessage')) byId('guestCheckoutErrorMessage').textContent = msg;
                        if (errorEl) errorEl.classList.remove('d-none');
                        if (payload && payload.errors) {
                            Object.keys(payload.errors).forEach(function(field) {
                                var el = form.querySelector('[name="' + field + '"]');
                                if (!el) return;
                                el.classList.add('is-invalid');
                                var next = el.nextElementSibling;
                                if (next && next.classList.contains('invalid-feedback')) {
                                    var m = payload.errors[field];
                                    next.textContent = Array.isArray(m) ? m[0] : m;
                                }
                            });
                        }
                    }).finally(function() {
                        if (btn) {
                            btn.disabled = false;
                            var t2 = btn.querySelector('.btn-text');
                            var sp2 = btn.querySelector('.spinner-border');
                            if (t2) t2.classList.remove('d-none');
                            if (sp2) sp2.classList.add('d-none');
                        }
                    });
                    return false;
                });

                if (guestModal) {
                    guestModal.addEventListener('stayl:modal:hidden', function() {
                        if (guestForm) { guestForm.classList.remove('d-none'); guestForm.reset(); }
                        if (byId('guestCheckoutSuccess')) byId('guestCheckoutSuccess').classList.add('d-none');
                        if (byId('guestCheckoutError')) byId('guestCheckoutError').classList.add('d-none');
                    });
                }

                if (typeof URLSearchParams !== 'undefined' && window.location.search.indexOf('open_guest_checkout=1') !== -1) {
                    if (document.getElementById('guestOrderPage')) {
                        if (history.replaceState) {
                            history.replaceState({}, '', window.location.pathname + window.location.search.replace(/[?&]open_guest_checkout=1/, '').replace(/^\?$/, '') + window.location.hash);
                        }
                    } else if (guestModal) {
                        openGuestCheckoutModal();
                        if (history.replaceState) history.replaceState({}, '', window.location.pathname + window.location.hash);
                    }
                }
            })();
        </script>
        {{-- Optional template scripts: include only when files exist (prevents 404 noise in console). --}}
        @php
            $templateJsBase = 'assets/templates/' . activeTemplateName() . '/js/';
            $businessJsRel = $templateJsBase . 'storefront-business.js';
            $legalJsRel = $templateJsBase . 'legal.js';
        @endphp
        @if(is_file(public_path($businessJsRel)))
            <script src="{{ asset($businessJsRel) }}" defer></script>
        @endif
        @if(is_file(public_path($legalJsRel)))
            <script src="{{ asset($legalJsRel) }}" defer></script>
        @endif
        @include('partials.marketing_analytics')
    @endpush
@endif


