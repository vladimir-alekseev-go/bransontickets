$(document).ready(function() {
    $('.popup-gallery').magnificPopup({
        delegate: 'a',
        type: 'image',
    callbacks: {
      elementParse: function(item) {
        if(item.el[0].className === 'video') {
          item.type = 'iframe',
          item.iframe = {
             patterns: {
               youtube: {
                 index: 'youtube.com/',
                 id: 'v=',
                 src: '//www.youtube.com/embed/%id%?autoplay=1'
               }
             }
          }
        } else {
           item.type = 'image',
           item.tLoading = 'Loading image #%curr%...',
           item.mainClass = 'mfp-img-mobile',
           item.image = {
             tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
           }
        }

      }
    },
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1]
        }
    });
});
