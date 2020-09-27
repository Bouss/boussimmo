import '../../../scss/components/form/field.scss';

$(function() {
    // Add a space every thousand for number inputs
    $('[data-type="number"]').on('input', function () {
        $(this).val(function (index, value) {
            return value.replace(/[^\dA-Z]/g, '').replace(/(.)(?=(.{3})+$)/g,'$1 ').trim();
        });
    })
});
