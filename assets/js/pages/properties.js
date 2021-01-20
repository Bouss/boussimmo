import 'bootstrap/js/dist/tooltip';
import '../../scss/pages/properties/index.scss';
import '../../scss/pages/properties/property.scss';
import '../../scss/pages/properties/filter_form.scss';

let $body = $('body');
let $filterForm = $('#filter-form');
let $sortSelect = $('#sort-select');
let $container = $('#property-ad-container');
let xhrCount = 0;

$filterForm.on('submit', function (e) {
    e.preventDefault();

    loadProperties();
});

$sortSelect.on('change', function () {
    loadProperties();
});

const loadProperties = () => {
    let xhrId = ++xhrCount;

    $.ajax({
        type: 'GET',
        url: Routing.generate('property_list'),
        data: {
            filters: $filterForm
                .find(':input').filter(function () {
                    return '' !== $(this).val()
                })
                .serialize(),
            sort: $sortSelect.val()
        },
        beforeSend: function () {
            if ($body.find('.loader').length) {
                return;
            }

            $body.append('<div class="loader"></div>');
        },
        success: function (html) {
            if (xhrId !== xhrCount) {
                return;
            }

            $container.html(html);
            $('#result-count').html($container.find('> article').length);
            initTooltips();
        },
        complete: function () {
            if (xhrId !== xhrCount) {
                return;
            }

            $body.find('.loader').remove();
        }
    });
}

const initTooltips = () => {
    $('[data-toggle="tooltip"]').tooltip({animation: true});
}

$(function() {
    initTooltips();
    loadProperties();
});
