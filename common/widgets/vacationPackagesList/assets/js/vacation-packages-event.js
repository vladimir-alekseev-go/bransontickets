$(document).on('click', '.showModalButton', function () {
    VacationPackageBuy.reset();
    $('#modalVacationPackage').find('#modalContent').html('<div class="load-progress"></div><br/><br/>');
    $('#modalVacationPackage').modal('show').find('#modalContent').load($(this).data('url'), function() {
        $('.scrollbar-inner').scrollbar();
    });
    $('#modalHeaderTitle').html($(this).data('title'));
    return false;
});

$(document).on('click', '.item-list-images', function () {
    $('.item-list-images').removeClass('active');
    $(this).addClass('active');
    $('#conrainerImages').css('backgroundImage','url('+$(this).attr('href')+')');
    return false;
});