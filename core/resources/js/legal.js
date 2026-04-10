/**
 * Staylbd Legal & Compliance: Cookie consent and privacy automation.
 */
class StaylbdLegal {
    constructor() {
        this.init();
    }

    init() {
        if (!localStorage.getItem('staylbd_cookies_accepted')) {
            this.showConsent();
        }
    }

    showConsent() {
        const banner = document.createElement('div');
        banner.className = 'fixed bottom-6 left-6 right-6 md:left-auto md:w-96 z-[10000] bg-white border border-slate-100 shadow-2xl rounded-2xl p-6 transform transition-all translate-y-0';
        banner.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="bg-indigo-50 p-2 rounded-lg"><i class="las la-cookie-bite text-indigo-600 fs-4"></i></div>
                <div>
                    <h6 class="text-slate-900 font-bold mb-1">Cookie Preferences</h6>
                    <p class="text-slate-500 text-sm mb-4">We use cookies to enhance your experience, serve personalized ads, and analyze our traffic.</p>
                    <div class="flex gap-3">
                        <button id="cookie-accept" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-bold flex-grow hover:bg-slate-800 transition-colors">Accept All</button>
                        <button id="cookie-settings" class="bg-slate-50 text-slate-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-100 transition-colors">Settings</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(banner);

        document.getElementById('cookie-accept').onclick = () => {
            localStorage.setItem('staylbd_cookies_accepted', 'true');
            banner.remove();
        };
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.StaylbdLegal = new StaylbdLegal();
});
