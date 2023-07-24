//header menu
let menuGeneral = $('#menu-general');

$('#menu-up-control').click(function () {
    if ($(document).width() > 767) {
        menuGeneral.animate({ right: '0' }, 500);
    } else {
        menuGeneral.css('display', 'block');
    }
});

$('#menu-up-control-close').click(function () {
    if ($(document).width() > 767) {
        menuGeneral.animate({ right: '-350px' }, 500);
    } else {
        menuGeneral.css('display', 'none');
    }
});
