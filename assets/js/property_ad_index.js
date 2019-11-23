import '../css/property_ad_index.scss';
import '../css/property_ad.scss';
import { handleSignoutClick } from './gmail_client';
import Cookies from './cookies';

let loader = document.createElement('div');
loader.className = 'loader';
let newerThanSelect;
let labelSelect;
let sortSelect;

function propertyAdIndexCallback(html) {
    document.body.classList.remove('homepage-body');
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
    $.ajax({
        type: 'POST',
        url: Routing.generate('property_ads_list', { newer_than: newerThan, label: label, sort: sort }),
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        contentType: 'application/octet-stream; charset=utf-8',
        processData: false,
        data: gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token,
        beforeSend: function () {
            document.getElementsByTagName('body')[0].append(loader);
        },
    }).done(function (data) {
        loader.remove();
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
