/** @type {import('tailwindcss').Config} */
const base = require("./tailwind.config.js");

module.exports = {
    ...base,
    content: [
        "./resources/views/templates/basic/layouts/**/*.blade.php",
        "./resources/views/templates/basic/partials/**/*.blade.php",
        "./resources/views/templates/basic/products/**/*.blade.php",
        "./resources/views/templates/basic/home.blade.php",
        "./resources/views/partials/**/*.blade.php",
        "./app/Modules/Banner/**/*.blade.php",
        "./resources/css/**/*.css",
    ],
};
