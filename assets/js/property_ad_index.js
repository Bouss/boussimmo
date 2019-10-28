import '../css/property_ad_index.scss';
import '../css/property_ad.scss';
import { getLabels, handleSignoutClick } from './gmail_client';

function propertyAdIndexCallback(html) {
    document.getElementsByClassName('container')[0].innerHTML = html;
    document.getElementById('btn-google-signout').onclick = handleSignoutClick;
    document.getElementById('btn-apply-filters').onclick = handleApplyFiltersClick;
    loadPropertyAds();
    getLabels(function (labels) {
        if (labels && labels.length > 0) {
            fillLabelSelect(labels);
        }
    });
}

function loadPropertyAds() {
    let $loader = $('.loader');
    // TODO: Fix the loader label
    $loader.attr('data-text', 'Chargement de vos annonces');

    let label = document.getElementById('label-select').value;

    $.ajax({
        type: 'POST',
        url: Routing.generate('property_ads_list'),
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        data: {
            access_token: gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token,
            newer_than: document.getElementById('newer-than-select').value,
            labels: label ? [label] : []
        },
        beforeSend: function () {
            $loader.show();
        },
    }).done(function (html) {
        $loader.hide();
        document.getElementById('property-ad-container').innerHTML = html;
    });
}

function fillLabelSelect(labels) {
    let labelSelect = document.getElementById('label-select');

    let opt = document.createElement('option');
    opt.value = '';
    opt.text = 'Choisissez un label';
    labelSelect.add(opt);

    labels.forEach(function(label) {
        let opt = document.createElement('option');
        opt.value = label.id;
        opt.text = label.name;
        labelSelect.add(opt);
    });
}

function handleApplyFiltersClick(e) {
    e.preventDefault();
    loadPropertyAds();
}

export { propertyAdIndexCallback };
