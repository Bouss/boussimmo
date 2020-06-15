import '../scss/homepage/index.scss';
import '../scss/components/btn_signin.scss';
import '../scss/homepage/search_form.scss';

let $searchForm = $('#search-form');
let $urlContainer = $('#url-container');

$searchForm.on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        type: 'POST',
        url: Routing.generate('provider_search_urls'),
        data: $searchForm.serialize(),
        success: function (data) {
            if (!$urlContainer.length) {
                $searchForm.after('<div id="url-container" style="display: grid; row-gap: 1em;"></div>');
                $urlContainer = $('#url-container');
            } else {
                $urlContainer.empty();
            }

            $.each(data, function (i, item) {
                $urlContainer.append(`
                    <div style="display: flex; align-items: center;">
                        <img src="build/providers/${item.logo}" alt="${item.provider}"/>
                        <a href="${item.url}" target="_blank" rel="noopener noreferrer">${item.url}</a>
                    </div>
                `);
            })
        }
    });
});
