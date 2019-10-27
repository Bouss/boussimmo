import '../css/property_ad_index.scss';
import '../css/property_ad.scss';

function fillPropertyAdContainer() {
    let propertyAdContainer = document.getElementById('property-ad-container');
    let labels = getCookie('labels');
    let $loader = $('.loader');
    // TODO: Fix the loader label
    // $loader.attr('data-text', 'Chargement de vos annonces');

    $.ajax({
        type: 'POST',
        url: Routing.generate('property_ads_list'),
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        data: {
            access_token: gapi.auth2.getAuthInstance().currentUser.get().getAuthResponse().access_token,
            labels: getCookie('labels') ? getCookie('labels') : []
        },
        beforeSend: function () {
            $loader.show();
        },
    }).done(function (data) {
        $loader.hide();
        propertyAdContainer.innerHTML = data;
    });
}

export { fillPropertyAdContainer };
