/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import '../scss/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;

const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);

global.routes = routes;
global.Routing = Routing;

$(function() {
    // Add a space every thousand for number inputs
    $('.field--number').on('input', function () {
        $(this).val(function (index, value) {
            return value.replace(/[^\dA-Z]/g, '').replace(/(.)(?=(.{3})+$)/g,'$1 ').trim();
        });
    })
});
