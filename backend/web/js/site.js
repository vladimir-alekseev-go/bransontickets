$(function() {
    $('aside.main-sidebar > section > ul > li > a').click(function() {
        if ($(this).next('ul').length) {
            if ($(this).parent().hasClass('active')) {
                $(this).parent().removeClass('active')
            } else {
                $(this).parent().addClass('active')
            }
            return false
        }
    })
})