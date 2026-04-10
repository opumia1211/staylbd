/** @type {import('tailwindcss').Config}
 * Admin panel only — separate from storefront tailwind.config.js (content + public bundle).
 * Preflight off: keep existing Bootstrap / app.css layout and components unchanged.
 * New Blade: prefer Tailwind grid/flex; avoid new .row/.col next to heavy utilities; use .st-btn* not .btn+.st-btn.
 */
module.exports = {
    content: [
        "./resources/views/admin/**/*.blade.php",
        "./resources/views/partials/**/*.blade.php",
        "./app/**/*.php",
    ],
    corePlugins: {
        preflight: false,
    },
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "ui-sans-serif", "system-ui", "sans-serif"],
            },
        },
    },
    plugins: [],
};
