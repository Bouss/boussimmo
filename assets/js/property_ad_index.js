import '../css/property_ad_index.scss';
import '../css/property_ad.scss';
import { handleSignoutClick } from './gmail_client';
import Cookies from './cookies';

let newerThanSelect;
let labelSelect;
let sortSelect;

function propertyAdIndexCallback(html) {
    document.getElementsByClassName('container')[0].innerHTML = html;

    newerThanSelect = document.getElementById('filter_property_ads_newerThan');
    labelSelect = document.getElementById('filter_property_ads_label');
    sortSelect = document.getElementById('sort_property_ads_sort');

    let newerThan = Cookies.get('newer_than') || newerThanSelect.value;
    let label = Cookies.get('label') || labelSelect.value;
    let sort = Cookies.get('sort') || sortSelect.value;

    initListeners();
    loadPropertyAds(newerThan, label, sort);
}

function loadPropertyAds(newerThan, label, sort) {
    let $loader = $('.loader');
    // TODO: Fix the loader label
    $loader.attr('data-text', 'Chargement de vos annonces');

    $.ajax({
        type: 'POST',
        url: Routing.generate('property_ads_list', { newer_than: newerThan, label: label, sort: sort }),
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        contentType: 'application/octet-stream; charset=utf-8',
        processData: false,
        data: gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token,
        beforeSend: function () {
            $loader.show();
        },
    }).done(function (data) {
        $loader.hide();
        document.getElementById('property-ad-container').innerHTML = data.html;
        document.getElementsByClassName('result__count')[0].innerHTML = data.property_ad_count;
    });
}

function handleApplyFiltersClick(e) {
    e.preventDefault();

    Cookies.set('newer_than', newerThanSelect.value);
    Cookies.set('label', labelSelect.value);
    let sort = Cookies.get('sort') || sortSelect.value;

    loadPropertyAds(newerThanSelect.value, labelSelect.value, sort);
}

function handleSortOnChange() {
    Cookies.set('sort', sortSelect.value);

    let newerThan = Cookies.get('newer_than') || newerThanSelect.value;
    let label = Cookies.get('label') || labelSelect.value;

    loadPropertyAds(newerThan, label, sortSelect.value);
}

function initListeners() {
    document.getElementById('btn-google-signout').onclick = handleSignoutClick;
    document.getElementById('btn-apply-filter').onclick = handleApplyFiltersClick;
    sortSelect.onchange = handleSortOnChange;
}

export { propertyAdIndexCallback };
