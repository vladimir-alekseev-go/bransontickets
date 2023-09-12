function number_format(number, decimals, dec_point, thousands_sep) {	// Format a number with grouped thousands
    //
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +	 bugfix by: Michael White (http://crestidg.com)

    var i, j, kw, kd, km;

    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point === undefined) {
        dec_point = ",";
    }
    if (thousands_sep === undefined) {
        thousands_sep = ".";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if ((j = i.length) > 3) {
        j = j % 3;
    } else {
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


    return km + kw + kd;
}

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

function inputFactorControl(ob) {
    let block = ob.closest('.with-input-field');
    let count = ob.val() * 1;
    let max = ob.attr('max');
    let min = ob.attr('min');

    if (min === undefined) {
        min = 0;
    }

    block.find('.js-input-factor').removeClass('in-active');

    if (count <= min) {
        block.find('.js-input-factor.fa-minus').addClass('in-active');
    }
    if (count >= max) {
        block.find('.js-input-factor.fa-plus').addClass('in-active');
    }
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

let DateFormats = {
    monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
    monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    day: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    dayShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
}

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
