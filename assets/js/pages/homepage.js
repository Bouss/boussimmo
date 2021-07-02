import '../../scss/pages/homepage/index.scss';
import '../../scss/pages/homepage/btn_signin.scss';
import '../../scss/pages/homepage/generate_urls_form.scss';

import '../components/form/field';

let $form = $('#generate-urls-form');
let $resultContainer = $('#result-container');

$form.on('submit', function (e) {
    e.preventDefault();

    // Remove thousand separators before sending
    let data = $form
        .find(':input').filter(function () {
            return '' !== $(this).val();
        })
        .serializeArray()
        .map((input) => {
            input.value = input.value.replace(/\s+/, '');

            return input;
        });

    $.ajax({
        type: 'GET',
        url: Routing.generate('provider_result_urls'),
        data: data,
        success: function (urls) {
            if (!$resultContainer.length) {
                $form.parent().after('<div id="result-container"></div>');
                $resultContainer = $('#result-container');
            } else {
                $resultContainer.empty();
            }

            $.each(urls, function (i, url) {
                $resultContainer.append(`
                    <a class="result link" href="${url.value}" target="_blank" rel="noopener noreferrer">
                        <div class="result-logo-wrapper">
                            <img src="${assets['build/images/providers/' + url.logo]}" alt="${url.website}"/>
                        </div>
                        <span>${url.value}</span>
                    </a>
                `);
            })
        }
    });
});
