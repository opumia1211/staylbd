<style>
    /* Experience Enhancement Styles */
    :root {
        --stayl-speed-fast: 0.15s;
        --stayl-speed-normal: 0.3s;
        --stayl-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    /* Button Loading States */
    .btn-loading {
        position: relative;
        color: transparent !important;
        pointer-events: none;
        cursor: not-allowed;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: calc(50% - 8px);
        left: calc(50% - 8px);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: stayl-spin 0.6s linear infinite;
    }

    @keyframes stayl-spin {
        to { transform: rotate(360deg); }
    }

    /* Real-Time Update Animation */
    .stayl-rt-updated {
        animation: stayl-pulse-update 1s var(--stayl-bounce);
    }

    @keyframes stayl-pulse-update {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); color: var(--stayl-active-blue, #10b981); }
        100% { transform: scale(1); }
    }

    /* Success Checkmark Animation */
    .stayl-success-check {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        background: var(--stayl-active-blue, #10b981);
        color: #fff;
        border-radius: 50%;
        font-size: 10px;
        margin-left: 5px;
        animation: stayl-check-in 0.4s var(--stayl-bounce);
    }

    @keyframes stayl-check-in {
        from { transform: scale(0) rotate(-45deg); opacity: 0; }
        to { transform: scale(1) rotate(0); opacity: 1; }
    }

    /* Header Icon Bounce */
    .stayl-header-bounce {
        animation: stayl-header-bounce 0.6s var(--stayl-bounce);
    }

    @keyframes stayl-header-bounce {
        0%, 100% { transform: scale(1); }
        30% { transform: scale(1.3); }
        60% { transform: scale(0.9); }
    }

    /* Smooth Hover Transitions for Product Cards */
    .product-card {
        transition: transform var(--stayl-speed-normal), box-shadow var(--stayl-speed-normal);
    }
    
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
    }

    /* Mobile Optimization */
    @media (max-width: 768px) {
        .stayl-btn-mobile-touch {
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
</style>
