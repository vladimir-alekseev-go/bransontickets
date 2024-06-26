let $datepickerEls = $('.datepicker'),
    $datepickerField = $datepickerEls.closest(".field");

$('.input-daterange').datepicker({startDate: '+0d', orientation: 'bottom'}).on("changeDate", function (e) {
    $('.datepicker-dropdown').hide();
    $(e.target).closest(".js-it").next().find("input").triggerHandler("focus")
    let d = new Date($(e.target).val());
    let departureDate = new Date();
    departureDate.setTime(d.getTime() + 3600 * 24 * 1000);
    let dateTo = new Date();
    dateTo.setTime(d.getTime() + 3600 * 24 * 1000 * 7);

    $(e.target).closest('.js-it').find('.js-date-view')
        .text(DateFormats.dayShort[d.getDay()] + ' ' + DateFormats.monthNamesShort[d.getMonth()] + ' ' + ('0' + d.getDate()).slice(-2));

    if ($(e.target).attr('id') === 's-arrivaldate') {
        let sDepartureDate = $("#s-departuredate");
        sDepartureDate.datepicker('setDate', departureDate);
        sDepartureDate.triggerHandler("focus")
    }

    if ($(e.target).attr('id') === 's-datefrom') {
        let sDateTo = $("#s-dateto");
        sDateTo.datepicker('setDate', dateTo);
        sDateTo.triggerHandler("focus")
    }
});

$('.input-group.date').datepicker({startDate: '+0d', orientation: 'bottom', todayHighlight: true, autoclose: true});

$('.js-search-on-main .datepicker').datepicker();
