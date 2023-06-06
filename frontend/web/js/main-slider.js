$(function () {
    mainSly.init();
});

let mainSly = {
    frame: null,
    wrap: null,
    sly: null,
    init: function () {
        let _self = this;
        _self.frame = $('.main-slider .frame');
        _self.wrap = _self.frame.parent();

        _self.initSly();

        _self.resize();
        $(window).resize(function () {
            _self.resize();
        });
        setTimeout(function () {
            _self.resize();
        }, 3000);
    },
    resize: function () {
        let _self = this;
        let k = 1;
        let w = $(".main-slider .frame").width() / k;
        let li = $(".main-slider .frame ul li");
        li.width(w);
        _self.sly.reload();
        $(".main-slider .frame ul").width(w * li.length);
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
            scrollBar: null,
            pagesBar: null,
            activatePageOn: 'click',
            speed: 300,
            elasticBounds: 1,
            easing: 'easeOutExpo',
            dragHandle: 1,
            dynamicHandle: 1,
            clickBar: 1,
            scrollBy: 0,

            // Cycling
            cycleBy: 'pages',
            cycleInterval: 5000,
            pauseOnHover: 1,
            //startPaused: 1,

            // Buttons
            prevPage: _self.wrap.find('.left'),
            nextPage: _self.wrap.find('.right'),
        }).init();
        //
        _self.sly.on('load change', function (eventName) {
            commonSly.onChange(eventName, this);
        });

        _self.sly.one('load', function () {
            $(".main-slider").css("opacity", 1)
        });
    },
}