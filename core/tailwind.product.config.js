/** @type {import('tailwindcss').Config} */
const base = require("./tailwind.config.js");

module.exports = {
    ...base,
    content: [
        "./resources/views/**/*.blade.php",
        "!./resources/views/admin/**",
        "./app/Modules/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./resources/css/**/*.css",
        "./app/Http/Helpers/helpers.php",
    ],
};
