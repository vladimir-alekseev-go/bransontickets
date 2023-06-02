let packageSlider = function (id) {
    return {
        frame: null,
        wrap: null,
        sly: null,
        id: id,
        init: function () {
            let _self = this;
            _self.frame = $('#' + _self.id).find('.calendar-slider .frame');
            _self.wrap = _self.frame.parent();

            _self.initSly();

            $(window).resize(function () {
                _self.resize();
            })
            _self.resize();
            return this;
        },
        resize: function () {
        },
        initSly: function () {
            let _self = this;
            _self.sly = new Sly(_self.frame, {
                horizontal: 1,
                itemNav: 'basic',
                smart: 1,
                //activateOn: 'click',
                mouseDragging: 1,
                touchDragging: 1,
                releaseSwing: 1,
                startAt: 0,
                scrollBar: null,
                scrollBy: 1,
                pagesBar: null,
                activatePageOn: 'click',
                speed: 300,
                elasticBounds: 1,
                easing: 'easeOutExpo',
                dragHandle: 1,
                dynamicHandle: 1,
                clickBar: 1,

                // Cycling
                //cycleBy: 'pages',
                //cycleInterval: 5000,
                //pauseOnHover: 1,
                //startPaused: 1,

                // Buttons
                prevPage: _self.wrap.find('.left'),
                nextPage: _self.wrap.find('.right'),
            });
            _self.sly.one('load', function () {
                let index = _self.frame.find("li").index(_self.frame.find("li.act"))
                _self.toStart(index, true);
            });
            _self.sly.init();
        },
        toCenter: function (index, immediate) {
            if (index === null) //for safary
                index = 0;
            if (immediate === null) //for safary
                immediate = false;

            let _self = this;
            _self.sly.toCenter(index, immediate);
        },
        toStart: function (index, immediate) {
            if (index === null) //for safari
                index = 0;
            if (immediate === null) //for safari
                immediate = false;
            let _self = this;
            _self.sly.toStart(index, immediate);
        }
    }
}
