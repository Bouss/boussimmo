import '../scss/app.scss';

import 'jquery'
import Routing from '../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import routes from '../../public/js/fos_js_routes.json';

const loadAssets = async () => {
    global.assets = await fetch('build/manifest.json').then(res => res.json());
}

Routing.setRoutingData(routes);
global.Routing = Routing;

loadAssets();
