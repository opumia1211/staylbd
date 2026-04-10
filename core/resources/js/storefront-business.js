/**
 * Staylbd Business Engine: User behavior tracking, exit intent, and growth automation.
 */
class StaylbdBusinessEngine {
    constructor() {
        this.sessionId = this.getOrCreateSessionId();
        this.startTime = Date.now();
        this.maxScroll = 0;
        this.init();
    }

    init() {
        this.trackScroll();
        this.trackExitIntent();
        this.trackClicks();
        this.trackTimeOnPage();
    }

    getOrCreateSessionId() {
        let sid = localStorage.getItem('staylbd_sid');
        if (!sid) {
            sid = 'sid_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
            localStorage.setItem('staylbd_sid', sid);
        }
        return sid;
    }

    async logEvent(type, data = {}) {
        try {
            await fetch('/api/v1/track/event', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    type: type,
                    session_id: this.sessionId,
                    url: window.location.href,
                    data: data
                })
            });
        } catch (e) {
            /* silent fail to not interrupt UX */
        }
    }

    trackScroll() {
        window.addEventListener('scroll', () => {
            const h = document.documentElement, 
                  b = document.body,
                  st = 'scrollTop',
                  sh = 'scrollHeight';
            const percent = (h[st]||b[st]) / ((h[sh]||b[sh]) - h.clientHeight) * 100;
            if (percent > this.maxScroll) {
                this.maxScroll = Math.round(percent);
            }
        });

        // Log max scroll when leaving
        window.addEventListener('beforeunload', () => {
            this.logEvent('page_scroll_final', { max_percent: this.maxScroll });
        });
    }

    trackExitIntent() {
        document.addEventListener('mouseleave', (e) => {
            if (e.clientY < 0) {
                this.showExitIntentPopup();
            }
        });
    }

    showExitIntentPopup() {
        if (sessionStorage.getItem('staylbd_exit_shown')) return;
        
        sessionStorage.setItem('staylbd_exit_shown', 'true');
        this.logEvent('exit_intent_trigger');

        // Professional layout for exit intent (Coupon focus)
        const modal = document.createElement('div');
        modal.className = 'staylbd-business-modal fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4';
        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all scale-100 p-8 text-center">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2z"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-2">Wait! Don't Miss Out</h2>
                <p class="text-slate-600 mb-6">Finish your order now and get an extra 5% OFF on your entire cart!</p>
                <div class="bg-slate-50 rounded-lg p-3 border-2 border-dashed border-slate-200 font-mono text-xl font-bold text-emerald-600 mb-6 tracking-wide uppercase">
                    COMEBACK5
                </div>
                <button id="staylbd-exit-claim" class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20 mb-4">
                    Claim My Discount
                </button>
                <button id="staylbd-exit-close" class="text-sm text-slate-400 font-medium hover:text-slate-600">No thanks, I'll pay full price</button>
            </div>
        `;
        document.body.appendChild(modal);

        document.getElementById('staylbd-exit-claim').onclick = () => {
            this.logEvent('exit_intent_claim');
            location.href = '/cart-list';
        };
        document.getElementById('staylbd-exit-close').onclick = () => {
            modal.remove();
        };
    }

    trackClicks() {
        document.addEventListener('click', (e) => {
            const el = e.target.closest('button, a, .clickable');
            if (el) {
                this.logEvent('user_click', {
                    tag: el.tagName,
                    text: el.innerText.trim().substring(0, 30),
                    classes: el.className
                });
            }
        });
    }

    trackTimeOnPage() {
        window.addEventListener('beforeunload', () => {
            const duration = Math.round((Date.now() - this.startTime) / 1000);
            this.logEvent('time_on_page', { duration_seconds: duration });
        });
    }
}

// Global initialization
window.StaylbdBusiness = new StaylbdBusinessEngine();
