/** @type {import('tailwindcss').Config} */
/* New storefront Blade sections: prefer Tailwind grid/flex + theme max-width utilities;
   avoid introducing Bootstrap .row/.col-* in the same component as dense utility layout. */
module.exports = {
    safelist: require('./tailwind.safelist.storefront.cjs'),
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./app/**/*.php",
        "./resources/css/**/*.css",
    ],
    corePlugins: {
        preflight: false,
    },
    theme: {
        extend: {
            fontFamily: {
                /* Inter only for UI text; generic fallbacks for rare glyph gaps */
                sans: ["Inter", "ui-sans-serif", "system-ui", "sans-serif"],
            },
            maxWidth: {
                storefront: "min(1920px, calc(100vw - 2 * clamp(15px, 2vw, 24px)))", // Advanced adaptive scaling
                "container-wide": "1440px",
            },
            colors: {
                primary: "#3e8804",
                secondary: "#0e9f90",
            },
            screens: {
                'xs': '475px',
                '3xl': '1920px', // Extra large monitors
                '4xl': '2560px', // Ultra wide monitors
            },
            boxShadow: {
                'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                'soft-xl': '0 20px 25px -5px rgba(0, 0, 0, 0.03), 0 10px 10px -5px rgba(0, 0, 0, 0.01)',
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio'),
    ],
};
