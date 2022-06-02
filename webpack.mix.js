const mix = require('laravel-mix');
mix.js("resources/js/app.js", "public/js")
    // .react()
    .postCss("resources/css/app.css", "public/css", [
        require("tailwindcss"),
]);
// mix.copyDirectory('node_modules/kioskboard/dist','public/js/kioskboard')
// mix.copyDirectory('node_modules/chart.js/dist','public/js/chart.js')
// mix.copyDirectory('node_modules/chartjs-plugin-annotation/dist','public/js/chart.js')
// mix.copyDirectory('node_modules/chartjs-plugin-datalabels/dist','public/js/chart.js')
// mix.copyDirectory('node_modules/sweetalert2/dist','public/sweetalert2')