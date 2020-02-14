import '../scss/property_ad_index.scss';
import '../scss/property_ad.scss';
import Cookies from './cookies';

$('#filter-form').on('submit', function (e) {
    e.preventDefault();

    loadPropertyAds();
});

$('#sort_property_ads_sort').on('change', function () {
    loadPropertyAds();
});

function loadPropertyAds() {
    $.ajax({
        type: 'GET',
        url: Routing.generate('property_ads_list'),
        data: {
            filters: $('#filter-form').serialize(),
            sort: $('#sort_property_ads_sort').val()
        },
        beforeSend: function () {
            $('body').append('<div class="loader"></div>')
        },
        success: function (data) {
            $('.property-ad-container').html(data.html);
            $('.result__count').html(data.property_ad_count);
        },
        complete: function () {
            $('body').find('.loader').remove();
        }
    });
}

$(function() {
    loadPropertyAds();
});
