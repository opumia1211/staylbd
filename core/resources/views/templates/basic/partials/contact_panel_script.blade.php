{{-- Floating live chat panel script --}}
<script>
    'use strict';
    (function () {
        function initContactPanel() {
            var contactBackdrop = document.getElementById('contactPanelBackdrop');
            var contactPanel = document.getElementById('contactPanelGlass');
            var contactClose = document.getElementById('contactPanelClose');
            var contactFloatBtn = document.getElementById('contactFloatBtn');
            if (!contactFloatBtn) return;
            var contactForm = document.getElementById('contactPanelForm');
            var contactMsg = document.getElementById('contactPanelMessage');
            var contactCharCount = document.getElementById('contactPanelCharCount');
            var selectRow = document.getElementById('contactPanelSelectRow');
            var channelInput = document.getElementById('contactPanelChannelInput');
            var sendBtn = document.getElementById('contactPanelSendBtn');
            var sendBtnOther = document.getElementById('contactPanelSendBtnOther');
            var selectedChannel = 'livechat';
            function getActiveSendBtn() { return selectedChannel === 'livechat' ? sendBtn : sendBtnOther; }
            var contactLiveUrl = contactFloatBtn.getAttribute('data-contact-live-url') || '{{ route("contact.live") }}';
            var channelConfigRaw = contactPanel ? contactPanel.getAttribute('data-channel-config') : '';
            var channelConfig = [];
            if (channelConfigRaw) {
                try {
                    channelConfig = JSON.parse(window.atob(channelConfigRaw));
                } catch (err) {
                    channelConfig = [];
                }
            }

            function isChannelActive(channel) {
                if (!Array.isArray(channelConfig) || !channelConfig.length) return true;
                var slug = (channel || '').toLowerCase();
                var record = channelConfig.find(function (cfg) { return (cfg.channel || '').toLowerCase() === slug; });
                return !record || !!record.is_active;
            }

            function syncChannelBadges() {
                if (!Array.isArray(channelConfig)) return;
                channelConfig.forEach(function(cfg) {
                    var btn = document.querySelector('.contact-panel-select-btn[data-channel="' + cfg.channel + '"]');
                    if (!btn) return;
                    var offline = !cfg.is_active;
                    btn.classList.toggle('contact-panel-select-offline', offline);
                    btn.setAttribute('aria-disabled', offline ? 'true' : 'false');
                    btn.disabled = offline && cfg.channel !== 'livechat';
                });
            }
            syncChannelBadges();

            // Instant load: cache messages so history shows immediately when panel opens
            var CACHE_TTL_MS = 45000;
            var chatMessagesUrl = contactForm ? contactForm.getAttribute('data-chat-messages-url') : '';
            function getChatCache() {
                var c = window.__contactChatCache;
                if (!c || !c.data || (Date.now() - (c.ts || 0)) > CACHE_TTL_MS) return null;
                return c.data;
            }
            function setChatCache(data) {
                window.__contactChatCache = { ts: Date.now(), data: data };
            }
            function prefetchChatMessages() {
                if (!chatMessagesUrl || selectedChannel !== 'livechat') return;
                fetch(chatMessagesUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        setChatCache({ messages: data.messages || [], unread_count: typeof data.unread_count === 'number' ? data.unread_count : 0 });
                    })
                    .catch(function () {});
            }
            if (chatMessagesUrl) {
                if (typeof requestIdleCallback !== 'undefined') {
                    requestIdleCallback(function () { prefetchChatMessages(); }, { timeout: 800 });
                } else {
                    setTimeout(prefetchChatMessages, 100);
                }
                if (window.location.pathname.indexOf('contactlive') !== -1) {
                    prefetchChatMessages();
                }
            }

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

            contactFloatBtn.addEventListener('click', function (e) {
                e.preventDefault();
                openContactPanel();
                // On user dashboard, use replaceState so Back button returns to previous section (e.g. Notifications -> Dashboard)
                var isUserDashboard = typeof window.location !== 'undefined' && window.location.pathname.indexOf('/user/') !== -1;
                try {
                    if (isUserDashboard) {
                        history.replaceState({ contactLive: true }, '', contactLiveUrl);
                    } else {
                        history.pushState({ contactLive: true }, '', contactLiveUrl);
                    }
                } catch (err) { }
                var chatThread = document.getElementById('contactPanelChatThread');
                if (chatThread && selectedChannel === 'livechat') { chatThread.style.display = 'flex'; loadChatMessages(); }
            });
            document.addEventListener('click', function (e) { if (e.target.closest('.js-contact-panel-open')) { e.preventDefault(); openContactPanel(); } });
            window.addEventListener('popstate', function () {
                if (contactPanel && contactPanel.classList.contains('is-open')) closeContactPanel();
            });
            if (window.location.pathname.indexOf('contactlive') !== -1 || (typeof window.location !== 'undefined' && window.location.search.indexOf('open_contact=1') !== -1)) {
                setTimeout(function () {
                    openContactPanel();
                    var chatThread = document.getElementById('contactPanelChatThread');
                    if (chatThread && selectedChannel === 'livechat') { chatThread.style.display = 'flex'; loadChatMessages(); }
                    if (window.history && window.history.replaceState && window.location.search.indexOf('open_contact=1') !== -1) {
                        var url = window.location.pathname + (window.location.hash || '');
                        window.history.replaceState({}, document.title, url);
                    }
                }, 300);
            }
            if (contactClose) contactClose.addEventListener('click', closeContactPanel);
            if (contactBackdrop) contactBackdrop.addEventListener('click', closeContactPanel);
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && contactPanel && contactPanel.classList.contains('is-open')) closeContactPanel(); });

            var lastChatMessageCount = 0;
            var chatLoadSkeletonHtml = '<div class="contact-chat-skeleton" aria-hidden="true"><div class="contact-chat-skeleton-line contact-chat-skeleton-line--short"></div><div class="contact-chat-skeleton-line contact-chat-skeleton-line--mid"></div><div class="contact-chat-skeleton-line contact-chat-skeleton-line--short"></div><div class="contact-chat-skeleton-line contact-chat-skeleton-line--long"></div></div>';
            function loadChatMessages(forceScroll) {
                if (forceScroll === undefined) forceScroll = true;
                var url = contactForm ? contactForm.getAttribute('data-chat-messages-url') : '';
                if (!url || selectedChannel !== 'livechat') return;
                var chatThread = document.getElementById('contactPanelChatThread');
                var chatMessages = document.getElementById('contactPanelChatMessages');
                if (!chatThread || !chatMessages) return;

                chatThread.style.display = 'flex';
                var cached = getChatCache();
                var isFirstLoad = !chatThread.classList.contains('is-loaded');

                if (cached && cached.messages && cached.messages.length >= 0) {
                    renderChatMessages(cached.messages, chatMessages, forceScroll, lastChatMessageCount, cached.unread_count || 0);
                    lastChatMessageCount = (cached.messages || []).length;
                    chatThread.classList.add('is-loaded');
                    if (typeof updateUnreadBadge === 'function') updateUnreadBadge();
                } else if (isFirstLoad) {
                    chatMessages.innerHTML = chatLoadSkeletonHtml;
                }

                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        var list = data.messages || [];
                        var unreadCount = typeof data.unread_count === 'number' ? data.unread_count : 0;
                        setChatCache({ messages: list, unread_count: unreadCount });
                        renderChatMessages(list, chatMessages, forceScroll, lastChatMessageCount, unreadCount);
                        lastChatMessageCount = list.length;
                        if (typeof updateUnreadBadge === 'function') updateUnreadBadge();
                        chatThread.classList.add('is-loaded');
                    })
                    .catch(function () {
                        if (isFirstLoad && (!cached || !cached.messages || cached.messages.length === 0)) {
                            chatMessages.innerHTML = '<div style="text-align:center;padding:16px;color:#8696a0;font-size:0.85rem;"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="font-size:1.5rem;"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v7"></path><circle cx="12" cy="17" r="1" fill="currentColor" stroke="none"></circle></svg><br>Unable to load.</div>';
                        }
                        chatThread.classList.add('is-loaded');
                    });
            }
            function renderChatMessages(messages, container, forceScroll, previousCount, unreadCountForBadge) {
                if (!container) return;
                previousCount = previousCount || 0;
                if (typeof unreadCountForBadge !== 'number') unreadCountForBadge = 0;

                // Find scroll container for new unified layout
                var scrollContainer = document.getElementById('contactPanelChatScrollArea') || container.parentElement;
                var oldScrollTop = 0;
                var oldScrollHeight = 0;
                var wasNearBottom = false;
                if (scrollContainer && scrollContainer.scrollHeight > 0) {
                    var threshold = 100;
                    wasNearBottom = (scrollContainer.scrollHeight - scrollContainer.scrollTop - scrollContainer.clientHeight) <= threshold;
                    oldScrollTop = scrollContainer.scrollTop;
                    oldScrollHeight = scrollContainer.scrollHeight;
                }
                var hasNewMessages = messages.length > previousCount;

                if (messages.length === 0) {
                    container.innerHTML = '<div style="text-align:center;padding:40px 20px;color:#667781;"><svg class="ui-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="font-size:3rem;opacity:0.3;"><path d="M21 12a8 8 0 0 1-8 8H7l-4 2 1.5-4.5A8 8 0 1 1 21 12z"></path><path d="M4 4l16 16"></path></svg><br><span style="font-size:0.9rem;">No messages yet.<br>Send a message to start the conversation.</span></div>';
                    return;
                }

                var html = '';
                var lastDate = '';
                var doubleCheck = '<span class="contact-chat-msg-read" aria-label="@lang("Read")">@include($activeTemplate . 'partials.icon', ['name' => 'check-double'])</span>';
                messages.forEach(function (m) {
                    var datePart = m.date_label || (m.created_at || '').split(',')[0].trim();
                    if (datePart && datePart !== lastDate) {
                        lastDate = datePart;
                        html += '<div class="contact-chat-date-divider"><span>' + datePart + '</span></div>';
                    }
                    var cls = m.is_admin ? 'contact-chat-msg-admin' : 'contact-chat-msg-user';
                    var name = (m.name || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    var msg = (m.message || '').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
                    var channelLabel = (m.channel || '').toString().toUpperCase();
                    html += '<div class="contact-chat-msg ' + cls + '"><div class="contact-chat-msg-name">' + name;
                    if (channelLabel) {
                        html += '<span class="contact-chat-msg-channel" data-channel="' + channelLabel + '">' + channelLabel + '</span>';
                    }
                    html += '</div><div class="contact-chat-msg-text">' + msg + '</div>';
                    if (m.attachments && m.attachments.length) {
                        var att = m.attachments[0];
                        var attUrl = typeof att === 'string' ? att : (att && (att.url || att.download_url || att.file_url));
                        var attLabel = '@lang("Attachment")';
                        if (typeof att === 'object' && att) {
                            attLabel = att.name || att.type || attLabel;
                        }
                        if (attUrl) {
                            html += '<div class="contact-chat-msg-att"><a href="' + attUrl + '" target="_blank">@include($activeTemplate . 'partials.icon', ['name' => 'paperclip']) ' + attLabel + '</a></div>';
                        } else {
                            html += '<div class="contact-chat-msg-att">@include($activeTemplate . 'partials.icon', ['name' => 'paperclip']) ' + attLabel + '</div>';
                        }
                    }
                    var dateTimeStr = (m.date_label ? m.date_label + ' · ' : '') + (m.created_at || '');
                    if (m.is_admin) {
                        html += '<div class="contact-chat-msg-meta"><span class="contact-chat-msg-time">' + dateTimeStr + '</span>' + doubleCheck + '</div>';
                    } else {
                        html += '<div class="contact-chat-msg-time">' + dateTimeStr + '</div>';
                    }
                    html += '</div>';
                });

                requestAnimationFrame(function () {
                    container.innerHTML = html;
                    var badge = document.getElementById('contactFloatBadge');
                    if (badge) {
                        var n = unreadCountForBadge;
                        badge.textContent = n;
                        badge.classList.toggle('d-none', !n);
                    }
                    if (!scrollContainer) return;
                    requestAnimationFrame(function () {
                        var newHeight = scrollContainer.scrollHeight;
                        var clientH = scrollContainer.clientHeight;
                        // Only auto-scroll when user explicitly opened/sent (forceScroll) or was at bottom AND new messages arrived
                        if (forceScroll || (wasNearBottom && hasNewMessages)) {
                            scrollContainer.scrollTop = newHeight;
                        } else {
                            // Preserve scroll position so history does not "run" or jump on poll
                            var targetTop = oldScrollHeight > 0 ? (oldScrollTop / oldScrollHeight) * newHeight : 0;
                            scrollContainer.scrollTop = Math.min(Math.max(0, targetTop), Math.max(0, newHeight - clientH));
                        }
                    });
                });
            }
            if (selectRow) {
                selectRow.addEventListener('click', function (e) {
                    var btn = e.target.closest('.contact-panel-select-btn');
                    if (!btn) return;
                    selectedChannel = btn.getAttribute('data-channel');
                    selectRow.querySelectorAll('.contact-panel-select-btn').forEach(function (b) {
                        b.classList.toggle('contact-panel-select-active', b === btn);
                    });
                    if (channelInput) channelInput.value = selectedChannel;
                    var chatThread = document.getElementById('contactPanelChatThread');
                    if (chatThread) chatThread.style.display = (selectedChannel === 'livechat') ? 'flex' : 'none';
                    if (sendBtnOther) sendBtnOther.classList.toggle('d-none', selectedChannel === 'livechat');
                    if (selectedChannel === 'livechat') loadChatMessages();
                    var glass = document.getElementById('contactPanelGlass');
                    if (!glass) return;
                    var msg = (contactMsg && contactMsg.value) ? contactMsg.value.trim() : '';
                    if (!isChannelActive(selectedChannel) && selectedChannel !== 'livechat') {
                        if (typeof notify === 'function') {
                            notify('error', '{{ __("This channel is temporarily offline. Please choose another channel.") }}');
                        }
                        return;
                    }
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
                contactMsg.addEventListener('input', function () {
                    var n = (contactMsg.value || '').length;
                    contactCharCount.textContent = n;
                    var counterEl = contactCharCount.closest('.contact-char-counter');
                    if (counterEl) counterEl.classList.toggle('at-limit', n >= 500);
                    /* Limit floating textarea height to keep layout steady */
                    if (this.classList.contains('contact-panel-msg-floating')) {
                        this.style.height = 'auto';
                        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                        return;
                    }
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                });
            }
            function getMsg() { return (contactMsg && contactMsg.value) ? contactMsg.value.trim() : ''; }
            var glass = document.getElementById('contactPanelGlass');
            var channelRedirectUrl = glass ? glass.getAttribute('data-channel-redirect-url') : '';

            if (sendBtn && contactForm) {
                sendBtn.addEventListener('click', function () {
                    var msg = getMsg();
                    if (selectedChannel === 'livechat') {
                        if (!msg) { if (typeof notify === 'function') notify('error', '{{ __("Please type a message.") }}'); return; }
                        if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse && grecaptcha.getResponse().length === 0) {
                            var errEl = document.getElementById('g-recaptcha-error');
                            if (errEl) errEl.innerHTML = '<span class="text-danger">{{ __("Captcha field is required.") }}</span>';
                            return;
                        }
                        var btn = getActiveSendBtn();
                        if (btn) btn.disabled = true;
                        var formData = new FormData(contactForm);
                        var fileInput = document.getElementById('contactPanelFiles');
                        if (fileInput && fileInput.files && fileInput.files.length > 0) {
                            for (var fi = 0; fi < fileInput.files.length; fi++) {
                                formData.append('attachments[]', fileInput.files[fi]);
                            }
                        }
                        var csrf = document.querySelector('meta[name="csrf-token"]');
                        fetch(contactForm.getAttribute('action'), { method: 'POST', body: formData, headers: { 'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '', 'Accept': 'application/json' } })
                            .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
                            .then(function (result) {
                                if (result.ok && result.data.success && typeof notify === 'function') {
                                    notify('success', result.data.message);
                                    contactForm.reset();
                                    if (contactCharCount) contactCharCount.textContent = '0';
                                    var fileList = document.getElementById('contactPanelFilesList');
                                    if (fileList) fileList.innerHTML = '';
                                    var filePreviews = document.getElementById('contactPanelFilePreviews');
                                    if (filePreviews) filePreviews.innerHTML = '';
                                    var filesRow = document.getElementById('contactPanelFilesRow');
                                    if (filesRow) { filesRow.classList.add('d-none'); filesRow.classList.remove('d-flex'); }
                                    var fileInput = document.getElementById('contactPanelFiles');
                                    if (fileInput) fileInput.value = '';
                                    if (result.data.messages && result.data.messages.length) {
                                        var chatMessages = document.getElementById('contactPanelChatMessages');
                                        renderChatMessages(result.data.messages, chatMessages, true, lastChatMessageCount, 0);
                                        lastChatMessageCount = result.data.messages.length;
                                    } else {
                                        loadChatMessages(true);
                                    }
                                } else if (typeof notify === 'function') {
                                    var errMsg = (result.data && result.data.message) ? result.data.message : '{{ __("Something went wrong.") }}';
                                    if (result.data && result.data.errors) {
                                        var errs = result.data.errors;
                                        errMsg = Object.keys(errs).map(function (k) { return errs[k][0]; }).join(' ');
                                    }
                                    notify('error', errMsg);
                                }
                            })
                            .catch(function () { if (typeof notify === 'function') notify('error', '{{ __("Something went wrong.") }}'); })
                            .finally(function () { var b = getActiveSendBtn(); if (b) b.disabled = false; });
                        return;
                    }
                    if (!msg) { if (typeof notify === 'function') notify('error', '{{ __("Please type a message.") }}'); return; }
                    if (selectedChannel === 'whatsapp' || selectedChannel === 'telegram' || selectedChannel === 'email') {
                        if (!channelRedirectUrl) { if (typeof notify === 'function') notify('error', '{{ __("Something went wrong.") }}'); return; }
                        var nameVal = contactForm.querySelector('input[name="name"]') ? contactForm.querySelector('input[name="name"]').value : '';
                        var subjVal = contactForm.querySelector('select[name="subject"]') ? contactForm.querySelector('select[name="subject"]').value : '';
                        var emailVal = contactForm.querySelector('input[name="email"]') ? contactForm.querySelector('input[name="email"]').value : '';
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
                            .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, status: r.status, data: data }; }); })
                            .then(function (result) {
                                if (result.ok && result.data.success) {
                                    if (result.data.redirect) {
                                        window.open(result.data.redirect, '_blank');
                                        if (typeof notify === 'function') notify('success', selectedChannel === 'whatsapp' ? '{{ __("Opening WhatsApp...") }}' : '{{ __("Opening Telegram...") }}');
                                    } else {
                                        if (typeof notify === 'function') notify('success', result.data.message || '{{ __("Message sent!") }}');
                                        contactForm.reset();
                                        if (contactCharCount) contactCharCount.textContent = '0';
                                    }
                                } else if (typeof notify === 'function') {
                                    notify('error', (result.data && result.data.message) ? result.data.message : '{{ __("This channel is not configured. Add in Admin → Contact.") }}');
                                }
                            })
                            .catch(function () { if (typeof notify === 'function') notify('error', '{{ __("Something went wrong.") }}'); })
                            .finally(function () { var b = getActiveSendBtn(); if (b) b.disabled = false; });
                        return;
                    }
                    if (typeof notify === 'function') notify('error', '{{ __("Please select a channel.") }}');
                });
            }
            if (sendBtnOther) sendBtnOther.addEventListener('click', function () { if (sendBtn) sendBtn.dispatchEvent(new MouseEvent('click', { bubbles: true })); });

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
                plusBtn.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); fileInput.click(); });
                fileInput.addEventListener('change', updateContactPanelFileList);
            }

            var emojiBtn = document.getElementById('contactPanelEmojiBtn');
            var micBtn = document.getElementById('contactPanelMicBtn');
            if (emojiBtn && contactMsg) {
                var emojiList = ['😀', '😃', '😄', '😁', '😅', '😂', '🤣', '😊', '😇', '🙂', '😉', '😍', '🥰', '😘', '👍', '👋', '❤️', '🙏', '🔥', '✨', '👍', '👌', '😎', '🥳', '😢', '😭', '🤔', '👀'];
                var emojiPop = document.createElement('div');
                emojiPop.id = 'contactPanelEmojiPop';
                emojiPop.className = 'contact-panel-emoji-pop';
                emojiPop.innerHTML = emojiList.map(function (em) { return '<button type="button" class="contact-panel-emoji-item" data-emoji="' + em + '" aria-label="Emoji">' + em + '</button>'; }).join('');
                document.body.appendChild(emojiPop);
                emojiBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var pop = document.getElementById('contactPanelEmojiPop');
                    if (pop.classList.contains('open')) { pop.classList.remove('open'); return; }
                    var rect = emojiBtn.getBoundingClientRect();
                    pop.style.left = rect.left + 'px';
                    pop.style.top = (rect.top - 200) + 'px';
                    pop.classList.add('open');
                });
                document.getElementById('contactPanelEmojiPop').addEventListener('click', function (e) {
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
                document.addEventListener('click', function (e) {
                    if (!e.target.closest('#contactPanelEmojiPop') && !e.target.closest('#contactPanelEmojiBtn'))
                        document.getElementById('contactPanelEmojiPop').classList.remove('open');
                });
            }
            // Voice Input Feature - Enhanced with Bengali Support
            if (micBtn && contactMsg) {
                let isRecording = false; // Track recording state
                let recognition = null; // Store recognition instance
                
                micBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('[Voice] Mic button clicked, isRecording:', isRecording);
                    
                    // If already recording, stop it (abort = immediate off)
                    if (isRecording) {
                        if (recognition) {
                            try {
                                recognition.abort();
                            } catch (e) {
                                try { recognition.stop(); } catch (e2) {}
                            }
                            recognition = null;
                        }
                        micBtn.classList.remove('recording');
                        isRecording = false;
                        return;
                    }
                    
                    // Check browser support
                    var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                    if (!SpeechRecognition) {
                        console.error('[Voice] Speech Recognition not supported');
                        if (typeof notify === 'function') {
                            notify('info', '{{ __("Voice input not supported. Use Chrome or Edge.") }}');
                        }
                        return;
                    }
                    
                    try {
                        // Create new recognition instance
                        recognition = new SpeechRecognition();
                        recognition.continuous = false;
                        recognition.interimResults = false;
                        recognition.maxAlternatives = 1;
                        
                        // Language detection - CRITICAL FIX
                        var htmlLang = document.documentElement.lang || 'en';
                        console.log('[Voice] Document language:', htmlLang);
                        
                        // Set language based on document lang
                        if (htmlLang === 'bn' || htmlLang.startsWith('bn-') || htmlLang === 'bd') {
                            recognition.lang = 'bn-BD'; // Bengali (Bangladesh)
                            console.log('[Voice] Using Bengali (bn-BD)');
                        } else if (htmlLang === 'en' || htmlLang.startsWith('en-')) {
                            recognition.lang = 'en-US'; // English (US)
                            console.log('[Voice] Using English (en-US)');
                        } else {
                            recognition.lang = htmlLang; // Use document language
                            console.log('[Voice] Using document language:', htmlLang);
                        }
                        
                        // Success: Voice recognized
                        recognition.onresult = function (event) {
                            console.log('[Voice] Recognition result received');
                            var transcript = event.results[event.results.length - 1][0].transcript;
                            console.log('[Voice] Transcript:', transcript);
                            
                            // Add to textarea
                            var currentValue = contactMsg.value;
                            var newValue = currentValue + (currentValue ? ' ' : '') + transcript;
                            contactMsg.value = newValue.slice(0, 500);
                            
                            // Update character count
                            if (contactCharCount) {
                                contactCharCount.textContent = contactMsg.value.length;
                            }
                            
                            // Floating bar: up to 5 lines then scroll
                            if (contactMsg.classList.contains('contact-panel-msg-floating')) {
                                contactMsg.style.height = 'auto';
                                contactMsg.style.height = Math.min(contactMsg.scrollHeight, 100) + 'px';
                            } else {
                                contactMsg.style.height = 'auto';
                                contactMsg.style.height = Math.min(contactMsg.scrollHeight, 100) + 'px';
                            }
                            
                            // Trigger input event for any listeners
                            var inputEvent = new Event('input', { bubbles: true });
                            contactMsg.dispatchEvent(inputEvent);
                        };
                        
                        // Error: Failed to recognize or permission denied
                        recognition.onerror = function (event) {
                            console.error('[Voice] Recognition error:', event.error);
                            
                            // CRITICAL: Always turn off mic button
                            micBtn.classList.remove('recording');
                            isRecording = false;
                            recognition = null;
                            
                            // Show appropriate error message
                            if (event.error === 'not-allowed' || event.error === 'permission-denied') {
                                if (typeof notify === 'function') {
                                    notify('error', '{{ __("Microphone permission denied. Please allow microphone access in browser settings.") }}');
                                }
                            } else if (event.error === 'no-speech') {
                                if (typeof notify === 'function') {
                                    notify('info', '{{ __("No speech detected. Please speak clearly and try again.") }}');
                                }
                            } else if (event.error === 'network') {
                                if (typeof notify === 'function') {
                                    notify('error', '{{ __("Network error. Check your internet connection.") }}');
                                }
                            } else if (event.error === 'aborted') {
                                console.log('[Voice] Recognition aborted by user');
                            } else {
                                if (typeof notify === 'function') {
                                    notify('error', '{{ __("Voice input failed. Error: ") }}' + event.error);
                                }
                            }
                        };
                        
                        // Start: Recording started
                        recognition.onstart = function () {
                            console.log('[Voice] Recognition started');
                            micBtn.classList.add('recording');
                            isRecording = true;
                        };
                        
                        // End: Recording stopped (success, timeout, or manual stop)
                        recognition.onend = function () {
                            if (!micBtn) return;
                            micBtn.classList.remove('recording');
                            isRecording = false;
                            recognition = null;
                        };
                        
                        // Start recognition
                        console.log('[Voice] Starting recognition...');
                        recognition.start();
                        
                    } catch (err) {
                        console.error('[Voice] Exception:', err);
                        
                        // CRITICAL: Remove recording state on error
                        micBtn.classList.remove('recording');
                        isRecording = false;
                        recognition = null;
                        
                        if (typeof notify === 'function') {
                            notify('error', '{{ __("Voice not supported in this browser. Use Chrome or Edge.") }}');
                        }
                    }
                });
                
                // Cleanup on page unload
                window.addEventListener('beforeunload', function() {
                    if (recognition && isRecording) {
                        try {
                            recognition.stop();
                        } catch (err) {
                            console.error('[Voice] Error stopping on unload:', err);
                        }
                    }
                });
            }
            var chatPollTimer = null;
            function startChatPoll() {
                if (chatPollTimer) return;
                chatPollTimer = setInterval(function () {
                    if (!contactPanel || !contactPanel.classList.contains('is-open') || selectedChannel !== 'livechat') return;
                    loadChatMessages(false);
                }, 12000);
            }
            function stopChatPoll() { if (chatPollTimer) { clearInterval(chatPollTimer); chatPollTimer = null; } }
            if (contactPanel) {
                var obs = new MutationObserver(function () {
                    if (contactPanel.classList.contains('is-open') && selectedChannel === 'livechat') startChatPoll();
                    else stopChatPoll();
                });
                obs.observe(contactPanel, { attributes: true, attributeFilter: ['class'] });
            }
            function updateUnreadBadge() {
                var unreadUrl = contactForm ? contactForm.getAttribute('data-chat-unread-url') : '';
                if (!unreadUrl) return;
                fetch(unreadUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        var badge = document.getElementById('contactFloatBadge');
                        if (!badge) return;
                        var n = typeof data.unread_count === 'number' ? data.unread_count : 0;
                        badge.textContent = n;
                        badge.classList.toggle('d-none', !n);
                    })
                    .catch(function () {});
            }
            var unreadPollTimer = null;
            function startUnreadPoll() {
                if (unreadPollTimer) return;
                unreadPollTimer = setInterval(function () {
                    if (contactPanel && contactPanel.classList.contains('is-open')) return;
                    updateUnreadBadge();
                }, 30000);
            }
            function stopUnreadPoll() { if (unreadPollTimer) { clearInterval(unreadPollTimer); unreadPollTimer = null; } }
            if (contactForm && contactForm.getAttribute('data-chat-unread-url')) {
                updateUnreadBadge();
                startUnreadPoll();
            }
            if (contactPanel) {
                contactPanel.addEventListener('transitionend', function () {
                    if (!contactPanel.classList.contains('is-open')) updateUnreadBadge();
                });
            }

            var icons = contactFloatBtn.querySelectorAll('.contact-float-btn-icon[data-icon]');
            if (icons.length > 1) {
                var idx = 0;
                setInterval(function () {
                    icons.forEach(function (el) { el.classList.remove('contact-float-btn-icon--active'); el.style.position = 'absolute'; });
                    var active = icons[idx];
                    if (active) { active.classList.add('contact-float-btn-icon--active'); active.style.position = 'relative'; }
                    idx = (idx + 1) % icons.length;
                }, 2500);
            }

        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initContactPanel);
        } else {
            initContactPanel();
        }
    })();
</script>