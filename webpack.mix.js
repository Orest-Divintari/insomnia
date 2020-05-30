const mix = require("laravel-mix");

const tailwindcss = require("tailwindcss");

mix.js("resources/js/app.js", "public/js")
    .sass("resources/sass/app.scss", "public/css")
    .options({
        processCssUrls: false,
        postCss: [tailwindcss("./tailwind.config.js")]
    });
mix.copy(
    "node_modules/@fortawesome/fontawesome-free/webfonts",
    "public/webfonts"
);
