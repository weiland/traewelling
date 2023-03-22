const mix = require("laravel-mix");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
        "resolve": {
            "alias": {
                "react": "preact/compat",
                "react-dom/test-utils": "preact/test-utils",
                "react-dom": "preact/compat",     // Must be below test-utils
                "react/jsx-runtime": "preact/jsx-runtime"
            }
        }
    })
    .preact()
    .browserSync("http://localhost:8081")
    .js("resources/js/app.js", "public/js")
    .js("resources/js/stats.js", "public/js")
    .sass("resources/sass/app.scss", "public/css")
    .js("resources/js/admin.js", "public/js")
    .sass("resources/sass/admin.scss", "public/css")
    .sass("resources/sass/welcome.scss", "public/css")
    .sourceMaps();

if (mix.inProduction()) {
    mix.version();
}
