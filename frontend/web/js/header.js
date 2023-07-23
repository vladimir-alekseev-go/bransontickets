//header menu
let menuGeneral = $('#menu-general');

$('#menu-up-control').click(function () {
    menuGeneral.css("display", "block");
});

$('#menu-up-control-close').click(function () {
    menuGeneral.css("display", "none");
});
