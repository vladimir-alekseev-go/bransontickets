let featuredItemsCreate = {
    create: function(id) {
        featuredItems().init(id);
    }
}

let featuredItems = function()
{
    return {
        frame: null,
        wrap: null,
        sly: null,
        init: function (id) {
            let _self = this;
            _self.frame = $('#' + id).find('.frame');
            _self.wrap = _self.frame.parent();
            _self.initSly();
            $(window).resize(function () {
                _self.resize();
            })
            _self.resize();
        },
        resize: function () {
            let _self = this;
            _self.sly.reload();
        },
        initSly: function () {
            let _self = this;
            _self.sly = new Sly(_self.frame, {
                horizontal: 1,
                itemNav: 'basic',
                smart: 1,
                activateOn: 'click',
                mouseDragging: 1,
                touchDragging: 1,
                releaseSwing: 1,
                startAt: 0,
                speed: 3000,
                elasticBounds: 1,
                easing: 'easeOutExpo',
                dragHandle: 1,
                dynamicHandle: 1,
                clickBar: 1,

                // Cycling
                cycleBy: 'items',
                cycleInterval: 3000,
                pauseOnHover: 1,

                // Buttons
                prevPage: _self.wrap.find('.featured-left'),
                nextPage: _self.wrap.find('.featured-right'),
            }).init();
        },
    }
}
