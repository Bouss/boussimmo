import '../scss/property_ad_index.scss';
import '../scss/property_ad.scss';

$('#filter-form').on('submit', function (e) {
    e.preventDefault();

    loadPropertyAds();
});

$('#sort-select').on('change', function () {
    loadPropertyAds();
});

$('.btn-signout').on('click', function () {
    document.location.href = 'https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue=https://www.immoscrap.de';
});

function loadPropertyAds() {
    $.ajax({
        type: 'GET',
        url: Routing.generate('property_ads_list'),
        data: {
            filters: $('#filter-form').serialize(),
            sort: $('#sort-select').val()
        },
        beforeSend: function () {
            $('body').append('<div class="loader"></div>')
        },
        success: function (html) {
            $('.property-ad-container').html(html);
            $('.result__count').html($('.property-ad-container .property-ad').length);
        },
        complete: function () {
            $('body').find('.loader').remove();
        }
    });
}

$(function() {
    loadPropertyAds();
});
