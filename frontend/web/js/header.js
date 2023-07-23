//header menu
let menuGeneral = $('#menu-general');

$('#menu-up-control').click(function () {
    menuGeneral.animate({ right: '0' }, 500);
});

$('#menu-up-control-close').click(function () {
    menuGeneral.animate({ right: '-350px' }, 500);
});
