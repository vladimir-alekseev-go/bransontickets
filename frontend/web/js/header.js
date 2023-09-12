//header menu
let menuGeneral = $('#menu-general');

$('#menu-up-control').click(function () {
    $('.wrapper-main').css('opacity', '0.7');

    if ($(document).width() > 767) {
        menuGeneral.animate({ right: '0' }, 500);
    } else {
        menuGeneral.animate({ right: '0' }, 500);
    }
});

$('#menu-up-control-close').click(function () {
    $('.wrapper-main').css('opacity', '1');

    if ($(document).width() > 767) {
        menuGeneral.animate({ right: '-400px' }, 500);
    } else {
        menuGeneral.animate({ right: '-100vw' }, 500);
    }
});
