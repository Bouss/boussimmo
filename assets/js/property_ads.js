import '../scss/property_ad/index.scss';
import '../scss/property_ad/property_ad.scss';

let $filterForm = $('#filter-form');
let $sortSelect = $('#sort-select');
let $container = $('#property-ad-container');

$filterForm.on('submit', function (e) {
    e.preventDefault();

    loadPropertyAds();
});

$sortSelect.on('change', function () {
    loadPropertyAds();
});

function loadPropertyAds() {
    $.ajax({
        type: 'GET',
        url: Routing.generate('property_ads_list'),
        data: {
            filters: $filterForm.find(':input').filter(function () {
                return '' !== $(this).val()
            }).serialize(),
            sort: $sortSelect.val()
        },
        beforeSend: function () {
            $('body').append('<div class="loader"></div>');
        },
        success: function (html) {
            $container.html(html);
            $('#result-count').html($container.find('.property-ad').length);
        },
        complete: function () {
            $('body').find('.loader').remove();
        }
    });
}

$(function() {
    loadPropertyAds();
});
