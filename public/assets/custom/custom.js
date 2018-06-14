$('#modalConfirmation').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var path = button.data('path');
    var crf = button.data('crf');
    var delBtn = $('.delete-resource-form');

    delBtn.attr({
        'data-path': path,
        'data-crf': crf
    });
});

$('.delete-resource-form').on('click', function () {

    var $form = $(document.createElement('form'));

    $form.attr({
        'action': $(this).data('path'),
        'method': 'POST'
    });

    $form.append($('<input/>', {
        type: 'hidden',
        name: '_method'
    }).val('DELETE'));

    $form.append($(this).data('crf'));

    $(this).parent().append($form);

    $form.submit();

    return false;
});