import '../scss/property_ad_index.scss';
import '../scss/property_ad.scss';
import Cookies from './cookies';

let loader = document.createElement('div');
loader.className = 'loader';
let newerThanSelect;
let labelSelect;
let newBuildCheckbox;
let sortSelect;

function propertyAdIndexCallback(html) {
    document.body.classList.remove('homepage-body');
    document.getElementsByClassName('container')[0].innerHTML = html;

    newerThanSelect = document.getElementById('filter_property_ads_newerThan');
    labelSelect = document.getElementById('filter_property_ads_label');
    newBuildCheckbox = document.getElementById('filter_property_ads_newBuild');
    sortSelect = document.getElementById('sort_property_ads_sort');

    initListeners();
    loadPropertyAds(Cookies.get('filters') || getFilters(), Cookies.get('sort') || sortSelect.value);
}

function loadPropertyAds(filters, sort) {
    $.ajax({
        type: 'POST',
        url: Routing.generate('property_ads_list', { filters: filters, sort: sort }),
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

    Cookies.set('filters', getFilters());
    loadPropertyAds(getFilters(), Cookies.get('sort') || sortSelect.value);
}

function handleSortOnChange() {
    Cookies.set('sort', sortSelect.value);
    loadPropertyAds(Cookies.get('filters') || getFilters(), sortSelect.value);
}

function initListeners() {
    document.getElementById('btn-google-signout').onclick = handleSignoutClick;
    document.getElementById('btn-apply-filter').onclick = handleApplyFiltersClick;
    sortSelect.onchange = handleSortOnChange;
}

function getFilters() {
    return {
        'newer_than': newerThanSelect.value,
        'label': labelSelect.value,
        'new_build': newBuildCheckbox.checked
    };
}

export { propertyAdIndexCallback };
