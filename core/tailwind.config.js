/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    corePlugins: {
        preflight: false,
    },
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "system-ui", "sans-serif"],
            },
            maxWidth: {
                /* Mirror layout shell used across public pages */
                storefront: "min(1920px, calc(100vw - 2 * clamp(10px, 1.2vw, 20px)))",
            },
            colors: {
                primary: "#3e8804",
                secondary: "#0e9f90",
            },
        },
    },
    plugins: [],
};
