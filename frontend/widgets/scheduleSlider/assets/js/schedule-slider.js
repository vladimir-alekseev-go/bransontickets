let scheduleSlider = {
    calendarSlider: null,
    frame: null,
    wrap: null,
    sly: null,
    cloneInfoOver: null,
    orderFormContainer: null,
    init: function () {
        let _self = this;
        _self.calendarSlider = $('.calendar-slider');
        _self.frame = _self.calendarSlider.find('.frame');
        _self.orderFormContainer = $('#order-form-container');
        _self.wrap = _self.frame.parent();

        _self.initSly();

        _self.frame.find('a.js-tag').click(function () {
            _self.loadPage($(this).data('href'), $(this));
            document.getElementById('order-form-container').scrollIntoView();
            return false;
        });

        $(document).on('click', '.js-allotment', function(){
            _self.frame.find("a.js-tag[data-allotment-id='" + $(this).data('allotmentId') + "']").eq(0).trigger('click');
            let index = $(".calendar-slider .frame li").index($(".calendar-slider .frame li .js-tag.active").closest('li'));
            _self.toCenter(index, true);
            $('.js-allotment').removeClass('active');
            $(this).addClass('active');
            return false;
        });
        let urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('tickets-on-date') !== null && urlParams.get('allotmentId') !== null) {
            _self.frame.find("a.js-tag[data-date='" + urlParams.get('tickets-on-date') + "'][data-allotment-id='" + urlParams.get('allotmentId') + "']").trigger('click');
            let index = $(".calendar-slider .frame li").index($(".calendar-slider .frame li .js-tag.active").closest('li'));
            _self.toCenter(index, true);
        } else if (urlParams.get('tickets-on-date') !== null) {
            _self.frame.find("a.js-tag[data-date='" + urlParams.get('tickets-on-date') + "']").trigger('click');
            let index = $(".calendar-slider .frame li").index($(".calendar-slider .frame li .js-tag.active").closest('li'));
            _self.toCenter(index, true);
        } else if (urlParams.get('allotmentId') !== null) {
            _self.frame.find("a.js-tag[data-allotment-id='" + urlParams.get('allotmentId') + "']").eq(0).trigger('click');
            let index = $(".calendar-slider .frame li").index($(".calendar-slider .frame li .js-tag.active").closest('li'));
            _self.toCenter(index, true);
        } else if (urlParams.get('on-date') !== null) {
            let index = $("li[data-date='" + urlParams.get('on-date') + "']");
            _self.toCenter(index, true);
        } else if (_self.calendarSlider.hasClass('d-none')) {
            _self.calendarSlider.find('a.js-tag').eq(0).trigger('click');
        }
    },
    loadPage: function (url, ob) {

        if (modification) {
            modification.dataHash = null;
        }

        window.location.hash = 'availability';
        let _self = this;
        _self.orderFormContainer.addClass('load-progress');
        _self.frame.find('.js-tag').removeClass('active');
        ob.addClass('active');
        $.get(url, function (result) {
            $('#order-form-container').html(result);
            _self.orderFormContainer.removeClass('load-progress');

            order.resetSumm();
            order.resetZero();

            $("input.count").trigger("change");
        });
    },
    initSly: function () {
        let _self = this;
        if (_self.frame.length === 0) {
            return;
        }
        _self.sly = new Sly(_self.frame, {
            horizontal: 1,
            itemNav: 'basic',
            smart: 1,
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

            // Buttons
            prevPage: _self.wrap.find('.left'),
            nextPage: _self.wrap.find('.right'),
        });
        _self.sly.one('load', function (eventName) {
            $(".calendar-slider-block").css('height', 'auto');
            let index = $(".calendar-slider .frame li").index($(".calendar-slider .frame li .js-tag.active").closest('li'));
            _self.toCenter(index, true);
        });
        _self.sly.init();
    },
    toCenter: function (index, immediate) {
        if (index === null) { //for safary
            index = 0;
        }
        if (immediate === null) { //for safary
            immediate = false;
        }

        let _self = this;
        _self.sly.toCenter(index, immediate);
    },
    initHash: function () {

    },
    setHash: function (hash) {

    },
    initInfoOver: function () {

    }
}
