{{--
Live Chat Panel - Main Chat Interface
This file contains the unified chat layout with message history and floating input bar
--}}

<div id="contactPanelChatThread" class="contact-panel-message-box contact-panel-unified-chat contact-panel-chat-thread"
    role="region" aria-label="@lang('Chat messages and typing area')">
    {{-- Message history and typing area combined --}}
    <div class="contact-panel-chat-scroll-area flex-1 overflow-y-auto p-3 space-y-3 bg-slate-50/50" id="contactPanelChatScrollArea">
        <div class="contact-panel-chat-messages space-y-2.5" id="contactPanelChatMessages" role="log" aria-live="polite"
            tabindex="0">
            {{-- Default Welcome Message (visible when empty) --}}
            <div class="contact-chat-msg contact-chat-msg-admin bg-blue-50/50 p-3 rounded-2xl border border-blue-100/50 mb-4">
                <div class="flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-[10px] font-bold">S</div>
                    <div class="flex-1">
                        <div class="text-[10px] font-bold text-blue-600 uppercase tracking-wider mb-0.5">Support Team</div>
                        <p class="text-xs text-slate-600 leading-relaxed">@lang('Hello! How can we help you today? Feel free to start a chat or choose a channel above.')</p>
                    </div>
                </div>
            </div>
        </div>
        {{-- Typing spacer --}}
        <div class="contact-panel-typing-spacer h-20"></div>
    </div>

    <div class="contact-panel-floating-input absolute bottom-0 left-0 right-0 p-3 bg-white/95 backdrop-blur-md border-t border-slate-100">
        <div class="contact-panel-typing-area-full flex flex-col gap-1.5">
            <label for="contactPanelMessage" class="visually-hidden">@lang('Type a message')</label>
            <textarea name="message" id="contactPanelMessage"
                class="form-control form-control-sm contact-panel-msg contact-panel-msg-floating w-full px-3 py-2 bg-slate-50 border-0 rounded-lg focus:ring-2 focus:ring-teal-500/10 focus:outline-none transition-all duration-300 text-xs text-slate-700 placeholder-slate-400 resize-none" maxlength="500"
                rows="2" placeholder="@lang('Type a message...')" required aria-label="@lang('Type a message')"></textarea>
            <div class="contact-panel-files-inline d-none" id="contactPanelFilesRow">
                <div class="contact-panel-files-list" id="contactPanelFilesList"></div>
                <div class="contact-panel-file-previews d-flex flex-wrap gap-1" id="contactPanelFilePreviews"></div>
            </div>
            <div class="contact-panel-input-wrap contact-panel-input-wrap--floating contact-panel-buttons-row flex items-center justify-between mt-1 px-1">
                <div class="flex items-center gap-2">
                    <button type="button" class="contact-panel-plus-btn contact-panel-btn-floating p-2 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all duration-300" id="contactPanelPlusBtn"
                        title="@lang('Add photo/file')" aria-label="@lang('Add attachment')">
                        @include($activeTemplate . 'partials.icon', ['name' => 'paperclip'])
                    </button>
                    <button type="button" class="contact-panel-emoji-btn contact-panel-btn-floating p-2 text-slate-400 hover:text-yellow-500 hover:bg-yellow-50 rounded-lg transition-all duration-300" id="contactPanelEmojiBtn"
                        title="@lang('Emoji')" aria-label="@lang('Emoji')">
                        @include($activeTemplate . 'partials.icon', ['name' => 'smile'])
                    </button>
                    <span class="contact-char-counter contact-char-counter--floating text-[11px] font-bold text-slate-300 ml-2 tracking-wider">
                        <span class="contact-panel-char-count" id="contactPanelCharCount">0</span>/500
                    </span>
                </div>
                <div class="contact-panel-actions-right flex items-center gap-2">
                    <button type="button" class="contact-panel-mic-btn contact-panel-btn-floating p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300" id="contactPanelMicBtn"
                        title="@lang('Voice message')" aria-label="@lang('Voice message')">
                        @include($activeTemplate . 'partials.icon', ['name' => 'microphone'])
                    </button>
                    <button type="button" class="contact-panel-send-btn p-2 bg-teal-500 text-white rounded-lg shadow-lg shadow-teal-500/20 hover:bg-teal-600 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300" id="contactPanelSendBtn"
                        title="@lang('Send')" aria-label="@lang('Send')">
                        @include($activeTemplate . 'partials.icon', ['name' => 'paper-plane'])
                    </button>
                </div>
                <input type="file" name="attachments[]" id="contactPanelFiles" class="d-none"
                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.mp3,.m4a,.mp4,.webm" multiple>
            </div>
        </div>
    </div>
</div>