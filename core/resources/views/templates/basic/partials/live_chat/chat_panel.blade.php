{{--
Live Chat Panel - Main Chat Interface
This file contains the unified chat layout with message history and floating input bar
--}}

<div id="contactPanelChatThread" class="contact-panel-message-box contact-panel-unified-chat contact-panel-chat-thread"
    role="region" aria-label="@lang('Chat messages and typing area')">
    {{-- Message history and typing area combined --}}
    <div class="contact-panel-chat-scroll-area" id="contactPanelChatScrollArea">
        <div class="contact-panel-chat-messages" id="contactPanelChatMessages" role="log" aria-live="polite"
            tabindex="0"></div>
        {{-- Typing spacer (space for textarea) --}}
        <div class="contact-panel-typing-spacer"></div>
    </div>

    {{-- Chat composer (typing area, buttons, file preview) --}}
    <div class="contact-panel-floating-input">
        <div class="contact-panel-typing-area-full">
            <label for="contactPanelMessage" class="visually-hidden">@lang('Type a message')</label>
            <textarea name="message" id="contactPanelMessage"
                class="form-control form-control-sm contact-panel-msg contact-panel-msg-floating" maxlength="500"
                rows="3" placeholder="@lang('Type a message')" required aria-label="@lang('Type a message')"></textarea>
            <div class="contact-panel-files-inline d-none" id="contactPanelFilesRow">
                <div class="contact-panel-files-list" id="contactPanelFilesList"></div>
                <div class="contact-panel-file-previews d-flex flex-wrap gap-1" id="contactPanelFilePreviews"></div>
            </div>
            <div class="contact-panel-input-wrap contact-panel-input-wrap--floating contact-panel-buttons-row">
                <button type="button" class="contact-panel-plus-btn contact-panel-btn-floating" id="contactPanelPlusBtn"
                    title="@lang('Add photo/file')" aria-label="@lang('Add attachment')">
                    @include($activeTemplate . 'partials.icon', ['name' => 'plus'])
                </button>
                <button type="button" class="contact-panel-emoji-btn contact-panel-btn-floating" id="contactPanelEmojiBtn"
                    title="@lang('Emoji')" aria-label="@lang('Emoji')">
                    @include($activeTemplate . 'partials.icon', ['name' => 'smile'])
                </button>
                <span class="contact-char-counter contact-char-counter--floating">
                    <span class="contact-panel-char-count" id="contactPanelCharCount">0</span>/500
                </span>
                <span class="contact-panel-actions-right">
                    <button type="button" class="contact-panel-mic-btn contact-panel-btn-floating" id="contactPanelMicBtn"
                        title="@lang('Voice message')" aria-label="@lang('Voice message')">
                        @include($activeTemplate . 'partials.icon', ['name' => 'microphone'])
                    </button>
                    <button type="button" class="contact-panel-send-btn contact-panel-btn-floating" id="contactPanelSendBtn"
                        title="@lang('Send')" aria-label="@lang('Send')">
                        @include($activeTemplate . 'partials.icon', ['name' => 'paper-plane'])
                    </button>
                </span>
                <input type="file" name="attachments[]" id="contactPanelFiles" class="d-none"
                    accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.mp3,.m4a,.mp4,.webm" multiple>
            </div>
        </div>
    </div>
</div>