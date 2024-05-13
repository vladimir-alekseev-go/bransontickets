let cart = {
    init: function () {
        let _self = this;
        $(window).resize(function () {
            _self.positionContainer();
        });

        $(window).scroll(function () {
            _self.positionContainer();
        });

        _self.positionContainer();
    },
    positionContainer: function () {
        try {
            let rp = $(".js-real-position");
            let column = rp.parent();
            let sp = $(".js-static-position");
            let topDelta = 100;
            let top = topDelta;

            if (column.position().top + column.height() - sp.height() < $(document).scrollTop() + topDelta) {
                top = column.position().top + column.height() - sp.height() - $(document).scrollTop();
            }

            if ($(document).scrollTop() + topDelta > rp.position().top) {
                sp.css({top: top + 'px', 'position': 'fixed'});
            } else {
                sp.css({top: '0', 'position': 'static'});
            }

            sp.width(sp.parent().width());
        } catch (e) {

        }
    },
}
