/** @type {import('tailwindcss').Config}
 * Admin panel only — separate from storefront tailwind.config.js (content + public bundle).
 * Preflight off: keep existing Bootstrap / app.css layout and components unchanged.
 */
module.exports = {
    content: [
        "./resources/views/admin/**/*.blade.php",
        "./resources/views/partials/**/*.blade.php",
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
