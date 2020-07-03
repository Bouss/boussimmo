import '../scss/homepage/index.scss';
import '../scss/homepage/btn_signin.scss';
import '../scss/homepage/generate_urls_form.scss';

let $form = $('#generate-urls-form');
let $resultContainer = $('#result-container');

$form.on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        type: 'POST',
        url: Routing.generate('provider_result_urls'),
        data: $form.serialize(),
        success: function (data) {
            if (!$resultContainer.length) {
                $form.parent().after('<div id="result-container"></div>');
                $resultContainer = $('#result-container');
            } else {
                $resultContainer.empty();
            }

            $.each(data, function (i, item) {
                $resultContainer.append(`
                    <a class="result link" href="${item.url}" target="_blank" rel="noopener noreferrer">
                        <div class="result-logo-wrapper">
                            <img src="build/providers/${item.logo}" alt="${item.provider}"/>
                        </div>
                        <span>${item.url}</span>
                    </a>
                `);
            })
        }
    });
});
