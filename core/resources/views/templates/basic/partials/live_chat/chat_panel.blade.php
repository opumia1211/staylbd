{{--
Live Chat Panel - Main Chat Interface
Unified chat layout with all buttons integrated into the typing area for a super-clean WhatsApp-like UI.
--}}

<div id="contactPanelChatThread" class="contact-panel-message-box contact-panel-unified-chat contact-panel-chat-thread position-relative" style="min-height: 480px; display: none; flex-direction: column;"
    role="region" aria-label="@lang('Chat messages and typing area')">
    {{-- Message history --}}
    <div class="contact-panel-chat-scroll-area flex-1 overflow-y-auto p-2" id="contactPanelChatScrollArea" style="background-color: #f0f2f5; background-image: radial-gradient(#d1d5db 1px, transparent 0); background-size: 20px 20px;">
        <div class="contact-panel-chat-messages" id="contactPanelChatMessages" role="log" aria-live="polite" tabindex="0">
            {{-- MESSAGES WILL BE RENDERED HERE DYNAMICALLY VIA JS --}}
        </div>
    </div>

    {{-- Professional Integrated Input Bar --}}
    <div class="contact-panel-floating-input position-absolute bottom-0 start-0 end-0 w-100 p-2 bg-white" style="z-index: 50; box-shadow: 0 -2px 10px rgba(0,0,0,0.05);">
        <div class="contact-panel-typing-area-full">
            <div class="d-flex align-items-center gap-1 bg-slate-100/80 border border-slate-200/60 p-1.5 rounded-full px-2" id="contactPanelInputContainer">
                {{-- Left Actions: Plus/Attach (INSIDE) --}}
                <button type="button" class="btn btn-link btn-sm p-1 d-flex items-center justify-center hover:bg-slate-200 rounded-full transition-colors" id="contactPanelPlusBtn" title="@lang('Attach')" style="color: #64748b !important; border:none; background:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                </button>

                {{-- Input Center with Hidden Scrollbar --}}
                <div class="flex-grow-1 position-relative">
                    <textarea name="message" id="contactPanelMessage"
                        class="form-control border-0 shadow-none text-xs w-100 bg-transparent" 
                        rows="1" placeholder="@lang('Type a message...')" required 
                        style="resize: none; min-height: 42px !important; height: 42px !important; max-height: 140px; padding: 10px 36px 10px 6px; font-size: 13px; line-height: 1.4; color: #1e293b; overflow-y: auto; outline: none !important; -webkit-appearance: none;"></textarea>
                    
                    {{-- Emoji Button: Inside right of textarea --}}
                    <button type="button" class="btn btn-link btn-sm p-0 position-absolute end-0 top-50 translate-middle-y me-1 hover:scale-110 transition-transform" id="contactPanelEmojiBtn" title="@lang('Emoji')" style="color: #64748b !important; z-index: 5; border: none; background: transparent;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/></svg>
                    </button>
                </div>

                {{-- Right Actions: Mic & Send --}}
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-link btn-sm p-1 d-flex items-center justify-center hover:bg-slate-200 rounded-full transition-colors" id="contactPanelMicBtn" title="@lang('Voice')" style="color: #64748b !important; border:none; background:none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                    </button>
                    <button type="button" class="btn btn-success btn-sm rounded-circle d-flex align-items-center justify-content-center shadow-md p-0 hover:scale-105 transition-transform" 
                        id="contactPanelSendBtn" title="@lang('Send')" style="width: 32px; height: 32px; min-width: 32px; background-color: #10b981; border: none !important; margin-left: 2px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: white;"><path d="m3 3 3 9-3 9 19-9Z"/><path d="M6 12h16"/></svg>
                    </button>
                </div>
            </div>
            
            {{-- Hidden File Input --}}
            <input type="file" name="attachments[]" id="contactPanelFiles" class="d-none" multiple>
        </div>
    </div>
</div>