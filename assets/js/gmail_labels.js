window.showSelectGmailLabelsModal = function(labels) {
    let labelInputContainer = document.getElementById('label-input-container');

    for (let i = 0; i < labels.length; i++) {
        let gmailLabel = labels[i];
        let div = document.createElement('div');
        let input = '<input type="checkbox" name="labels" id=' + gmailLabel.id + ' value=' + gmailLabel.id + '>';
        let label = '<label for=' + gmailLabel.id + '>' + gmailLabel.name + '</label>';
        div.innerHTML = input + label;
        labelInputContainer.appendChild(div);
    }

    $('#modal-select-gmail-labels').modal('show');
};

$('#form-gmail-labels').on('submit', function(e) {
    e.preventDefault();

    let labelIds = [];

    $.each($('input[name=labels]:checked'), function() {
        labelIds.push($(this).val());
    });

    setCookie('labels', labelIds, 2*365);

    $('#modal-select-gmail-labels').modal('hide');
});
