{{-- Shared auth page layout: center alignment, responsive card, no fixed widths --}}
<style>
/* STEP 2: Same flex-based centering for login & register */
.auth-overlay {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    min-height: 100vh;
    min-height: 100dvh;
    padding: 20px;
    padding: clamp(0.5rem, 2vw, 1.25rem);
    box-sizing: border-box;
    background: transparent !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}
.auth-card {
    width: 100%;
    max-width: 420px;
    background: #ffffff !important;
    border-radius: 12px;
    box-shadow: 0 12px 36px rgba(0,0,0,0.12);
    padding: 30px;
    box-sizing: border-box;
    word-break: break-word;
    position: relative;
    max-height: 92vh;
    overflow-y: auto;
    margin: auto;
}
.auth-header {
    text-align: center;
    margin-bottom: 25px;
}
.auth-header img {
    max-width: 150px;
    height: auto;
    margin-bottom: 10px;
}
.auth-title {
    display: block;
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}
.auth-close {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 24px;
    color: #999;
    text-decoration: none !important;
    line-height: 1;
    transition: color 0.2s;
}
.auth-close:hover {
    color: #333;
}
.auth-panel {
    display: none;
}
.auth-panel-active {
    display: block;
}
/* Apply to existing structure */
.floating-auth-page.account-section {
    min-height: 100dvh;
    padding: clamp(0.5rem, 2vw, 1.25rem);
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: center;
    contain: layout style;
}
.floating-auth-page .floating-auth-wrap--compact,
.floating-auth-page .floating-auth-wrap {
    width: 100%;
    max-width: 420px;
    margin: 0 auto;
    box-sizing: border-box;
}
.floating-auth-page .floating-auth-glass-border { width: 100%; }
.floating-auth-page .floating-auth-glass-inner {
    box-sizing: border-box;
    padding: 20px;
    padding: clamp(1rem, 3vw, 1.5rem);
}
.floating-auth-page .form-control,
.floating-auth-page .form--control {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    transition: all 0.3s;
    background: #f8fafc;
    width: 100%;
    box-sizing: border-box;
    font-size: 16px;
    padding: 10px 12px;
    min-height: 44px;
}
@media (max-width: 480px) {
    .auth-card { padding: 20px; }
    .floating-auth-page .form-control,
    .floating-auth-page .form--control { font-size: 14px; padding: 10px; }
}
.floating-auth-page .form-control:focus,
.floating-auth-page .form--control:focus {
    border-color: #0d9488;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    outline: none;
}
.auth-btn {
    background: #2dd4bf; 
    background: linear-gradient(135deg, #2dd4bf 0%, #0d9488 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 14px 20px;
    width: 100%;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    min-height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.auth-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    filter: brightness(1.05);
}
.auth-btn:active {
    transform: translateY(0);
}
.auth-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}
.password-input-wrap {
    position: relative;
}
.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #64748b;
    padding: 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    z-index: 5;
}
.password-toggle:hover {
    color: #1e293b;
}
.password-toggle svg {
    display: block;
}
.social-login-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-top: 15px;
}
@media (max-width: 400px) {
    .social-login-grid { grid-template-columns: 1fr; }
}
.btn--social-login {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    color: #334155;
    transition: all 0.2s;
}
.btn--social-login:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.btn--social-text {
    font-size: 14px;
}
/* Prevent text overflow */
.floating-auth-page .floating-auth-site-name,
.floating-auth-page .account-header--compact .title,
.floating-auth-page .form--label { word-break: break-word; }
</style>
