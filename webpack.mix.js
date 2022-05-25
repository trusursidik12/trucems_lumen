const mix = require('laravel-mix');
mix.js("resources/js/app.js", "public/js")
    // .react()
    .postCss("resources/css/app.css", "public/css", [
        require("tailwindcss"),
]);
// mix.copyDirectory('node_modules/kioskboard/dist','public/js/kioskboard')