$(document).ready(function() {
    $('.featured-items').slick({
        autoplay: true,
        slidesToShow: 3,
        prevArrow: $('.featured-left'),
        nextArrow: $('.featured-right'),
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1
                }
            }
          ]
    });
});
