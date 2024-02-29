//header menu
let menuGeneral = $('#menu-general');

$('#menu-up-control').click(function () {
    $('body').addClass('menu-up-is-open');

    if ($(document).width() > 767) {
        menuGeneral.animate({ right: '0' }, 500);
    } else {
        menuGeneral.animate({ right: '0' }, 500);
    }
});

$('#menu-up-control-close').click(function () {
    $('body').removeClass('menu-up-is-open');

    if ($(document).width() > 767) {
        menuGeneral.animate({ right: '-400px' }, 500);
    } else {
        menuGeneral.animate({ right: '-100vw' }, 500);
    }
});

$(document).on('click', '.js-menu-general-fon-click', function() {
    console.log(111)
    $('#menu-up-control-close').trigger('click');
});
