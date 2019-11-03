import '../css/property_ad_index.scss';
import '../css/property_ad.scss';
import { handleSignoutClick } from './gmail_client';
import CookieManager from './cookie_manager';

function propertyAdIndexCallback(html) {
    document.getElementsByClassName('container')[0].innerHTML = html;

    let newerThan = CookieManager.getCookie('newer_than') ? CookieManager.getCookie('newer_than') : 7;
    let label = CookieManager.getCookie('label') ? CookieManager.getCookie('label') : [];

    initListeners();
    loadPropertyAds(newerThan, label);
}

function loadPropertyAds(newerThan, label) {
    let $loader = $('.loader');
    // TODO: Fix the loader label
    $loader.attr('data-text', 'Chargement de vos annonces');

    $.ajax({
        type: 'POST',
        url: Routing.generate('property_ads_list'),
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        data: {
            access_token: gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token,
            newer_than: newerThan,
            label: label
        },
        beforeSend: function () {
            $loader.show();
        },
    }).done(function (html) {
        $loader.hide();
        document.getElementById('property-ad-container').innerHTML = html;
    });
}

function handleApplyFiltersClick(e) {
    e.preventDefault();
    let newerThan = document.getElementById('newer-than-select').value;
    let label = document.getElementById('label-select').value;

    CookieManager.setCookie('newer_than', newerThan);
    CookieManager.setCookie('label', label);
    loadPropertyAds(newerThan, label);
}

function initListeners() {
    document.getElementById('btn-google-signout').onclick = handleSignoutClick;
    document.getElementById('btn-apply-filters').onclick = handleApplyFiltersClick;
}

export { propertyAdIndexCallback };
