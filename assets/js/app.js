import '../scss/app.scss';

const loadAssets = async () => {
    global.assets = await fetch('build/manifest.json').then(res => res.json());
}
const $ = require('jquery');
const routes = require('../../public/js/fos_js_routes.json');
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';

Routing.setRoutingData(routes);
global.$ = global.jQuery = $;
global.Routing = Routing;

loadAssets();

$(function() {
    // Add a space every thousand for number inputs
    $('[data-type-number]').on('input', function () {
        $(this).val(function (index, value) {
            return value.replace(/[^\dA-Z]/g, '').replace(/(.)(?=(.{3})+$)/g,'$1 ').trim();
        });
    })
});
