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
