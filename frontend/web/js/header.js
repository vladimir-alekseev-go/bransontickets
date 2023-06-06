$('#menu-up-control').click(function () {
    let menuGeneral = $('#menu-general');
    if ($(this).hasClass('menu-up-control-is-close')) {
        $(this).removeClass('menu-up-control-is-close');
        $(this).addClass('menu-up-control-is-open');
        menuGeneral.removeClass('menu-general-control-is-close');
        menuGeneral.addClass('menu-general-control-is-open');
        $('.header').addClass('header-menu-is-open');
    } else {
        $(this).removeClass('menu-up-control-is-open');
        $(this).addClass('menu-up-control-is-close');
        menuGeneral.removeClass('menu-general-control-is-open');
        menuGeneral.addClass('menu-general-control-is-close');
        $('.header').removeClass('header-menu-is-open');
    }
});
