function removeA(arr) {
    let what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax = arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}

let commonSly = {
    onChange: function (eventName, sly) {
        let duration = eventName !== 'load' ? 400 : 0,
            $prevElms = $(sly.options.prevPage).add(sly.options.prev),
            $nextElms = $(sly.options.nextPage).add(sly.options.next);

        if (sly.pos.dest <= sly.pos.start) {
            $prevElms.fadeOut(duration);
        } else {
            $prevElms.fadeIn(duration);
        }

        if (sly.pos.dest >= sly.pos.end) {
            $nextElms.fadeOut(duration);
        } else {
            $nextElms.fadeIn(duration);
        }
    }
};

let listFilterBody = {
    resize: function (ob, correctHeight) {
        let div = ob.children('div');
        let div2 = ob.children('div > div:first-child');
        if ($(window).width() < 992) {
            div.addClass('scroll-wrapper');
            div.addClass('scrollbar-inner');
            div2.addClass('scroll-content');
            div2.addClass('scrollbar-inner');
            ob.height($(window).height() - correctHeight);
        } else {
            div.removeClass('scroll-wrapper');
            div.removeClass('scrollbar-inner');
            div2.removeClass('scroll-content');
            div2.removeClass('scrollbar-inner');
            div2.attr('style', '');
            ob.attr('style', '');
        }
    }
}

let popupSizer = {
    init: function () {
        // $('body').append('<style>.modal-content .scrollbar-inner {max-height:'+($(window).height()-100)+'px;}</style>');
        // $('body').append('<style>.modal-body-print-schedule .scrollbar-inner {max-height:'+($(window).height()-260)+'px;}</style>');
        $('body').append('<style>.modal-dialog .scrollbar-inner {max-height:' + ($(window).height() - 200) + 'px;}</style>');
        // $('body').append('<style>#modalVacationPackage .scrollbar-inner, #modalVacationPackage .conrainer-images {max-height:'+($(window).height()-200)+'px;}</style>');
        // $('body').append('<style>.vacation-packages-buy .scrollbar-inner {max-height:'+($(window).height()-200)+'px;}</style>');
    },
    // modifyPopup: function () {
    //     let orderModifyInfo = $('#order-modify-info');
    //     if(orderModifyInfo.length === 1) {
    //         $('body').append('<style>.modal-dialog #order-form-left .scrollbar-inner {max-height:' + ($(window).height() + 11200 - orderModifyInfo.height()) + 'px;}</style>');
    //     }
    // }
}

$(function () {
    try {
        $('.scrollbar-inner').scrollbar()
    } catch (e) {
    }

    $(window).resize(function () {
        listFilterBody.resize($('.list-filter-body'), 66);
    });
    listFilterBody.resize($('.list-filter-body'), 66);

    popupSizer.init();
});
