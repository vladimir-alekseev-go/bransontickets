let order = {
    all: 0,
    availableTotalAllotment: 0,
    totalPrice: 0,
    totalCount: 0,
    tax: 0,
    modifyCurrentCost: 0,
    modifyAmount: 0,
    modifyCurrentDate: '',
    modifyNewDate: '',
    modifyHashData: null,
    allotmentHash: null,
    init: function()
    {
        let _self = this;

        $(document).on("click", ".js-input-factor", function () {
            let flexTable = $(this).closest('.flex-table');
            if (flexTable.hasClass('fp-in-active')) {
                return;
            }
            let block = $(this).closest('.with-input-field');
            let input = block.find('input[type="number"]');
            if (input.attr('disabled') === 'disabled') {
                let modalId = input.closest('.js-order-fields-is-non-refundable').length === 1
                    ? 'modal-unselect-refundable-rate' : 'modal-unselect-non-refundable-rate';
                let modal = new bootstrap.Modal(document.getElementById(modalId))
                modal.show();
                return;
            }
            let factor = $(this).data('factor');
            let max = input.attr('max');
            let count = _self.getNumberObjectValue(input);
            count += factor;

            if (count <= 0) {
                count = 0;
            }
            if (count >= max) {
                count = max;
            }
            input.val(count);
            input.trigger('change');
            inputFactorControl(input);
        });

        $(document).on("wheel", "#list-tickets input.count", function () {
            $(this).blur();
        });

        $(document).on("submit", "form#list-tickets", function () {
            $.post($(this).attr('action'), $(this).serialize(), function(data) {
                $('#order-form-container').html(data);
                $('input.count').eq(0).trigger('change');
            }).always(function(jqXHR) {
                if (jqXHR.status === 302) {
                    window.location.href = jqXHR.responseText;
                }
            });
            return false;
        });

        $(document).on("change keyup","#list-tickets input.count",function(){
            _self.allotmentHash = $(this).attr('allotment-hash');
            _self.availableTotalAllotment = $(this).data('max');

            _self.recount();
            _self.resetSumm();
            _self.resetZero();
            _self.countControl($(this));

            _self.recount();
            _self.resetSumm();
            _self.resetZero();
            _self.controlRefundable();

            $('input.count, input.count-family-pass').each(function () {
                inputFactorControl($(this));
            });
        });
    },
    getCountNotEmptyFields: function () {
        let countNotEmptyFields = 0;
        let _self = this;
        $("input.count").each(function () {
            if (_self.getNumberObjectValue($(this)) !== 0) {
                countNotEmptyFields++;
            }
        });
        return countNotEmptyFields;
    },
    isNonRefundableBlockActive: function () {
        let _self = this;
        if (_self.getCountNotEmptyFields() === 0) {
            return null;
        }
        let isNonRefundableBlockActive = false;
        $("input.count").each(function () {
            if (_self.getNumberObjectValue($(this)) !== 0) {
                isNonRefundableBlockActive = $(this).hasClass('js-non-refundable');
            }
        });
        return isNonRefundableBlockActive;
    },
    controlRefundable: function () {
        let _self = this;
        $('.count[name*="OrderForm[price_id_"]').attr('disabled', false);

        let inputs = $("input.count");
        inputs.each(function () {
            let id = $(this).data('ticketId');
            let alternativeRate = $('.js-alternative-rate[name*="OrderForm[price_id_' + id + '"]');
            let defaultRate = $('.js-default-rate[name*="OrderForm[price_id_' + id + '"]');
            alternativeRate.closest(".flex-table").removeClass("fp-in-active");
            defaultRate.closest(".flex-table").removeClass("fp-in-active");
        });

        inputs.each(function () {
            let id = $(this).data('ticketId');
            let alternativeRate = $('.js-alternative-rate[name*="OrderForm[price_id_' + id + '"]');
            let defaultRate = $('.js-default-rate[name*="OrderForm[price_id_' + id + '"]');

            if (_self.getNumberObjectValue($(this)) !== 0) {
                if ($(this).hasClass('js-default-rate')) {
                    alternativeRate.attr('disabled', true);
                    alternativeRate.closest(".flex-table").addClass("fp-in-active");
                }
                if ($(this).hasClass('js-alternative-rate')) {
                    defaultRate.attr('disabled', true);
                    defaultRate.closest(".flex-table").addClass("fp-in-active");
                }
            }
        });
    },
    countControl: function(field)
    {
        let _self = this;
        if (field.val() != "" || field.data("min") > 0) {
            let max = field.data("max");
            let min = field.data("min");
            let val = field.val();
            let reg = /^\d+$/;
            val = reg.test(val) ? Math.abs(val*1) : 0;
            field.val(val);
            if (val > max) {
                val = max;
            }
            if (val < min) {
                val = min;
            }
            field.val(val);
            if (_self.getTotalCountAllotment() > _self.availableTotalAllotment) {
                field.val(_self.availableTotalAllotment - (_self.getTotalCountAllotment() - val));
            }
            if (field.val()*1 < 0) {
                field.val(0);
            }
            field.val(field.val()*1);
        }
    },
    resetZero: function()
    {
        let _self = this;
        $("input.count").each(function(){
            if (_self.getNumberObjectValue($(this)) === 0) {
                $(this).closest(".flex-table").addClass("zero");
            } else {
                $(this).closest(".flex-table").removeClass("zero");
            }
        })

        if (_self.totalPrice > 0) {
            $("#total-price").removeClass('zero');
            if (_self.tax > 0) {
                $("#total-tax").removeClass('zero');
            } else {
                $("#total-tax").addClass('zero');
            }
        } else {
            $("#total-price").addClass('zero');
            $("#total-tax").addClass('zero');
        }
    },
    showMessage: function()
    {
        let ob = $("#alert-recount");
        ob.hide();
        ob.removeClass("hide");
        ob.hide().css("width","100% !important");
        ob.stop().show();
    },
    hideMessage: function() {
        let ob = $("#alert-recount");
        ob.hide();
    },
    recount: function() {
        let _self = this;
        let adult, child, family_pass, family_pass_4, family_pass_8, family_pass_seat, family_pass_4_seat, family_pass_8_seat;
        let adultAR, adultDR, childAR, childDR, familyPassAR, familyPassDR, familyPassAR4, familyPassDR4;
        let familyPassAR8, familyPassSeatAR4, familyPassSeatDR4, familyPassDR8, familyPassSeatAR8, familyPassSeatDR8;

        adultAR = $("#list-tickets input.count.js-alternative-rate[data-ticket-name='ADULT'][data-recount='yes']");
        adultDR = $("#list-tickets input.count.js-default-rate[data-ticket-name='ADULT'][data-recount='yes']");
        adult = _self.getNumberObjectValue(adultAR) > 0 ? adultAR : adultDR;

        childAR = $("#list-tickets input.count.js-alternative-rate[data-ticket-name='CHILD'][data-recount='yes']");
        childDR = $("#list-tickets input.count.js-default-rate[data-ticket-name='CHILD'][data-recount='yes']");
        child = _self.getNumberObjectValue(childAR) > 0 ? childAR : childDR;

        familyPassAR = $("#list-tickets input.count.js-alternative-rate[data-ticket-name='FAMILY PASS'][data-recount='yes']");
        familyPassDR = $("#list-tickets input.count.js-default-rate[data-ticket-name='FAMILY PASS'][data-recount='yes']");
        family_pass = _self.getNumberObjectValue(familyPassAR) > 0 ? familyPassAR : familyPassDR;

        familyPassAR4 = $("#list-tickets input.count.js-alternative-rate[data-ticket-name='FAMILY PASS 4 PACK'][data-recount='yes']");
        familyPassDR4 = $("#list-tickets input.count.js-default-rate[data-ticket-name='FAMILY PASS 4 PACK'][data-recount='yes']");
        familyPassSeatAR4 = $("input.js-alternative-rate.js-family-pass-4-seat");
        familyPassSeatDR4 = $("input.js-default-rate.js-family-pass-4-seat");
        if (_self.getNumberObjectValue(familyPassAR4) > 0) {
            family_pass_4 = familyPassAR4;
            family_pass_4_seat = familyPassSeatAR4;
        } else {
            family_pass_4 = familyPassDR4;
            family_pass_4_seat = familyPassSeatDR4;
        }

        familyPassAR8 = $("#list-tickets input.count.js-alternative-rate[data-ticket-name='FAMILY PASS 8 PACK'][data-recount='yes']");
        familyPassDR8 = $("#list-tickets input.count.js-default-rate[data-ticket-name='FAMILY PASS 8 PACK'][data-recount='yes']");
        familyPassSeatAR8 = $("input.js-alternative-rate.js-family-pass-8-seat");
        familyPassSeatDR8 = $("input.js-default-rate.js-family-pass-8-seat");
        if (_self.getNumberObjectValue(familyPassAR8) > 0) {
            family_pass_8 = familyPassAR8;
            family_pass_8_seat = familyPassSeatAR8;
        } else {
            family_pass_8 = familyPassDR8;
            family_pass_8_seat = familyPassSeatDR8;
        }

        let _family_pass_4 = _self.getNumberObjectValue(family_pass_4);
        let _family_pass_8 = _self.getNumberObjectValue(family_pass_8);
        if (family_pass.length > 0) {
            for (let i=0; i<family_pass.length; i++) {
                if ($(family_pass[i]).val() > 0) {
                    $(family_pass[i]).closest('.flex-table').next(".flex-table").removeClass("hide");
                    $(family_pass[i]).closest('.flex-table').next(".flex-table").find("input[name*='_family_pass_open']").val(1);
                    family_pass_seat = $(family_pass[i]).closest('.flex-table').next(".flex-table").find("[name*='family_pass_seat]']");
                    if (_self.getNumberObjectValue(family_pass_seat) === 0) {
                        family_pass_seat.val(1)
                    }
                } else {
                    family_pass_seat = $(family_pass[i]).closest('.flex-table').next(".flex-table").find("[name*='family_pass_seat]']");
                    family_pass_seat.val(0)
                    $(family_pass[i]).closest('.flex-table').next(".flex-table").find("input[name*='_family_pass_open']").val('');
                    $(family_pass[i]).closest('.flex-table').next(".flex-table").addClass("hide");
                }
            }
        }

        if (_self.getNumberObjectValue(adult) >= 2) {
            if (
                family_pass_4.length === 1 && _self.getNumberObjectValue(family_pass_4) === 0
                && _self.getNumberObjectValue(family_pass_4_seat) < 4 && _self.getNumberObjectValue(child) > 0) {

                adult.val(_self.getNumberObjectValue(adult) - 2);

                family_pass_4.val(1);
                family_pass_8.val(0);

                family_pass_4_seat.val(2);
                family_pass_8_seat.val(0);

                if (_self.getNumberObjectValue(child) > 0) {
                    while (_self.getNumberObjectValue(child) !== 0 && _self.getNumberObjectValue(family_pass_4_seat) < 4) {
                        _self.showMessage();
                        child.val(_self.getNumberObjectValue(child) - 1);
                        family_pass_4_seat.val(_self.getNumberObjectValue(family_pass_4_seat) + 1);
                    }
                }

            } else if(_self.getNumberObjectValue(family_pass_4) === 1
                && family_pass_8.length === 1 && _self.getNumberObjectValue(family_pass_8) === 0
                && (_self.getNumberObjectValue(child) > 0 || _self.getNumberObjectValue(family_pass_4_seat) > 0
                    || _self.getNumberObjectValue(family_pass_8_seat) > 0)){

                adult.val(_self.getNumberObjectValue(adult) - 2);

                family_pass_4.val(0);
                family_pass_8.val(1);

                family_pass_8_seat.val(_self.getNumberObjectValue(family_pass_4_seat) + 2);
                family_pass_4_seat.val(0);

                if (_self.getNumberObjectValue(child) > 0){
                    while (_self.getNumberObjectValue(child) !== 0 && _self.getNumberObjectValue(family_pass_8_seat) < 8) {
                        _self.showMessage();
                        child.val(_self.getNumberObjectValue(child) - 1);
                        family_pass_8_seat.val(_self.getNumberObjectValue(family_pass_8_seat) + 1);
                    }
                }
            }
        }

        if (_self.getNumberObjectValue(child) > 0) {
            if (_self.getNumberObjectValue(family_pass_4) === 1) {
                while (_self.getNumberObjectValue(child) !== 0 && _self.getNumberObjectValue(family_pass_4_seat) < 4) {
                    _self.showMessage();
                    child.val(_self.getNumberObjectValue(child) - 1);
                    family_pass_4_seat.val(_self.getNumberObjectValue(family_pass_4_seat) + 1);
                }
            }
            if (this.getNumberObjectValue(family_pass_8) === 1) {
                while (_self.getNumberObjectValue(child) !== 0 && _self.getNumberObjectValue(family_pass_8_seat) < 8) {
                    _self.showMessage();
                    child.val(_self.getNumberObjectValue(child) - 1);
                    family_pass_8_seat.val(_self.getNumberObjectValue(family_pass_8_seat) + 1);
                }
            }
        }

        if (_self.getNumberObjectValue(family_pass_4) === 1 && _self.getNumberObjectValue(family_pass_8) === 0) {
            family_pass_4.attr("max",1).attr("disabled",false);
            family_pass_4.closest(".flex-table").removeClass("fp-in-active").addClass("fp-active");
            family_pass_4_seat.attr("disabled", false).closest(".flex-table").removeClass("hide");

            familyPassDR8.attr("max", 0).attr("disabled", true);
            familyPassAR8.attr("max", 0).attr("disabled", true);
            familyPassDR8.closest(".flex-table").addClass("fp-in-active").removeClass("fp-active");
            familyPassAR8.closest(".flex-table").addClass("fp-in-active").removeClass("fp-active");
            familyPassSeatDR8.closest(".flex-table").addClass("hide");
            familyPassSeatAR8.closest(".flex-table").addClass("hide");
            familyPassSeatDR8.attr("disabled", true).val(0);
            familyPassSeatAR8.attr("disabled", true).val(0);

            if (_self.getNumberObjectValue(family_pass_4_seat) < 3) {
                family_pass_4_seat.val(3);
            }

            if (_family_pass_4 !== _self.getNumberObjectValue(family_pass_4)) {
                _self.showMessage();
            }
        } else if (_self.getNumberObjectValue(family_pass_4) === 0 && _self.getNumberObjectValue(family_pass_8) === 1) {

            familyPassDR4.attr("max", 0).attr("disabled", true);
            familyPassAR4.attr("max", 0).attr("disabled", true);
            familyPassDR4.closest(".flex-table").addClass("fp-in-active").removeClass("fp-active");
            familyPassAR4.closest(".flex-table").addClass("fp-in-active").removeClass("fp-active");
            familyPassSeatDR4.closest(".flex-table").addClass("hide");
            familyPassSeatAR4.closest(".flex-table").addClass("hide");
            familyPassSeatDR4.attr("disabled", true).val(0);
            familyPassSeatAR4.attr("disabled", true).val(0);

            family_pass_8.attr("max",1).attr("disabled",false);
            family_pass_8.closest(".flex-table").removeClass("fp-in-active").addClass("fp-active");
            family_pass_8_seat.attr("disabled", false).closest(".flex-table").removeClass("hide");

            if (_self.getNumberObjectValue(family_pass_8_seat) < 5) {
                family_pass_8_seat.val(5);
            }

            if (_family_pass_8 !== _self.getNumberObjectValue(family_pass_8)) {
                _self.showMessage();
            }
        } else {
            familyPassDR4.attr("max", 1).attr("disabled", false);
            familyPassAR4.attr("max", 1).attr("disabled", false);
            familyPassDR4.closest(".flex-table").removeClass("fp-in-active").removeClass("fp-active");
            familyPassAR4.closest(".flex-table").removeClass("fp-in-active").removeClass("fp-active");
            familyPassSeatDR4.closest(".flex-table").addClass("hide");
            familyPassSeatAR4.closest(".flex-table").addClass("hide");
            familyPassSeatDR4.attr("disabled", true).val(0);
            familyPassSeatAR4.attr("disabled", true).val(0);

            familyPassDR8.attr("max", 1).attr("disabled", false);
            familyPassAR8.attr("max", 1).attr("disabled", false);
            familyPassDR8.closest(".flex-table").removeClass("fp-in-active").removeClass("fp-active");
            familyPassAR8.closest(".flex-table").removeClass("fp-in-active").removeClass("fp-active");
            familyPassSeatDR8.closest(".flex-table").addClass("hide");
            familyPassSeatAR8.closest(".flex-table").addClass("hide");
            familyPassSeatDR8.attr("disabled", true).val(0);
            familyPassSeatAR8.attr("disabled", true).val(0);

            _self.hideMessage();
        }
        _self.controlOneTicket();
        _self.stickPosition();
    },
    stickPosition: function () {
        $('.stick-order-family-pass-seats').removeClass('stick-order-family-pass-seats');
        let fpActive = $('.fp-active');
        let bottom = $('.stick-order-bottom-alert').height() + 15;
        fpActive.next().addClass('stick-order-family-pass-seats');

        fpActive.next().css("bottom", bottom);
        bottom += fpActive.next().height();
        fpActive.css("bottom", bottom);
    },
    controlOneTicket: function () {
        let _self = this;
        let oneTickets = $("#list-tickets input.count[data-only-one-ticket='true']");
        let oneTicketSelected = false;
        if (oneTickets.length === 0) {
            return false;
        }
        oneTickets.each(function () {
            if (_self.getNumberObjectValue($(this)) > 0) {
                oneTicketSelected = true;
            }
        });
        if (oneTicketSelected) {
            oneTickets.each(function () {
                if (_self.getNumberObjectValue($(this)) > 0) {
                    $(this).attr("disabled", false);
                    $(this).closest("tr").css("opacity", 1);
                    $('#only-one-ticket-in-one-time').hide().removeClass("hide").css("width","100% !important")
                        .stop().show('slow');
                } else {
                    $(this).attr("disabled", true);
                    $(this).closest("tr").css("opacity", 0.5);
                }
            });
        } else {
            oneTickets.attr("disabled", false);
            oneTickets.closest("tr").css("opacity", 1);
        }
    },
    getNumberObjectValue: function(ob) {
        if (ob.length === 1) {
            if (ob.val() === '') {
                return 0;
            }
            return parseInt(ob.val(),10);
        }
        return 0;
    },
    getTotalCountAllotment: function()
    {
        let _self = this;
        _self.all = 0;
        $("input.count[allotment-hash="+_self.allotmentHash+"]").each(function(){
            let k = 1;
            // if($(this).attr("data-ticket-name") === "FAMILY PASS 4 PACK" && $(this).attr("data-recount") === "yes")
            //     k = $('[name="OrderForm[family_pass_4_seat]"]').val();
            // if($(this).attr("data-ticket-name") === "FAMILY PASS 8 PACK" && $(this).attr("data-recount") === "yes")
            //     k = $('[name="OrderForm[family_pass_8_seat]"]').val();
            _self.all += $(this).val()*k;
        })
        return _self.all;
    },
    resetSumm: function()
    {
        let _self = this;
        let totalTaxOb = $("#total-tax");

        _self.totalPrice = 0;
        _self.totalCount = 0;

        $("#list-tickets input.count").each(function(){
            let price = $(this).data("price");
            let val = Math.abs($(this).val()*1);
            $(this).closest('.order-container-row').find(".js-subtotal-cost").html(
                "$ "+(price*val).toFixed(2)
            );
            _self.totalPrice += (price*val).toFixed(2)*1;
            _self.totalCount += val;
        })

        let tax = totalTaxOb.attr('data-tax') ? totalTaxOb.attr('data-tax') : 0;

        _self.tax = (tax*_self.totalPrice/100).toFixed(2);


        $("#total-price").html("$ "+(_self.totalPrice + +_self.tax).toFixed(2)).css("position","relative");
        $("#total-count").html(_self.totalCount);
        totalTaxOb.html("$ "+_self.tax);
        $("#orderform-count").val(_self.totalCount);
        setTimeout(function(){$("#total-price").show()},1000);

    },
    btnProceedEnable: function () {
        let btnProceed = $('#btn-proceed');
        btnProceed.removeClass('btn-loading');
        btnProceed.prop("disabled", false);
    },
    btnProceedDisable: function () {
        let btnProceed = $('#btn-proceed');
        btnProceed.addClass('btn-loading');
        btnProceed.prop("disabled", true);
    },
    countCheck: -1,
    controlScrollBar: function () {
        try {
            if ($(window).width() > 787) {
                $('.js-scrollbar-inner-full-screen').addClass('scrollbar-inner');
                $('.js-scrollbar-inner-small-screen').scrollbar('destroy');
                $('.js-scrollbar-inner-small-screen').removeClass('scrollbar-inner');
                $('.js-scrollbar-inner-small-screen.scroll-wrapper').remove();
                $('.scrollbar-inner').scrollbar();
            } else {
                $('.js-scrollbar-inner-small-screen').addClass('scrollbar-inner');
                $('.js-scrollbar-inner-full-screen').scrollbar('destroy');
                $('.js-scrollbar-inner-full-screen').removeClass('scrollbar-inner');
                $('.js-scrollbar-inner-full-screen.scroll-wrapper').remove();
                $('.scrollbar-inner').scrollbar();
            }
            popupSizer.init();
        } catch ($e) {
        }
    },
    initModify: function() {
        let _self = this;

        $(document).on("submit", "#payment", function(){
            modification.payment();
            return false;
        });
        $(document).on("submit", "#payment-add-card", function(){
            modification.paymentAddCard();
            return false;
        });
        $(window).resize(function(){
            _self.controlScrollBar();
            _self.controlScrollBar();
        })

        //$(document).on("change keyup","#list-tickets input.count",function(){
        $(document).on("change keyup","input.count, input[name='OrderForm[coupon_code]']",function(){
            _self.countCheck++

            if (_self.countCheck <= 0) {
                return;
            }

            _self.modifyCurrentCost = $('#modified-total').attr('data-cost')*1;
            _self.modifyNewDate = $('[name="OrderForm[date_format]"]').val();
            _self.modifyCurrentDate = $('[name="OrderForm[package_date_format]"]').val();
            if (!_self.modifyCurrentHashData) {
                _self.modifyCurrentHashData = $('[name="OrderForm[hashData]"]').val();
            }

            _self.btnProceedDisable();

            modification.check(function(data){

                let messWarningOb = $('#mess-warning');
                let messAmountUpOb = $('#mess-amound-up');
                let messAmountDownOb = $('#mess-amound-down');
                let messDateChangeOb = $('#mess-date-change');

                if (!(data.statusText && data.statusText === 'abort')) {
                    _self.btnProceedEnable();
                }

                messWarningOb.removeClass("hide");
                messAmountUpOb.addClass("hide");
                messAmountDownOb.addClass("hide");
                messDateChangeOb.addClass("hide");

                if (data['globalErrors'] && data['globalErrors'][0]) {
                    $("#popup-errors-modify").html('<div class="alert alert-danger alert-dismissible">'+data['globalErrors'][0]+'</div>');
                    // $('#modified-amound-block').hide();
                    $('#btn-proceed').prop("disabled",true);
                    messWarningOb.addClass("hide");
                    $('#modified-total').html('$ '+number_format(_self.modifyCurrentCost, 2, ".", ""));

                } else {
                    $("#qty").html(data['ticketsQtyModifyPackageNew'] + " ticket" + (data['ticketsQtyModifyPackageNew'] > 1 ? "s" : ""));
                    $("#date-packepge").html(data.datePackepgeFormat);
                    $("#order-form-container").html(data.orderForm);
                    $("#package-total").html("$ "+number_format(data['totalPrice'], 2, ".", ""));
                    $("#fullTotalOrderNew").html("$ "+number_format(data['fullTotalOrderNew'], 2, ".", ""));
                    $("#popup-errors-modify").html('');

                    _self.modifyAmount = data['modifyAmount'] ? (data['modifyAmount']).toFixed(2) : 0;
                    _self.modifyNewHashData = $('[name="OrderForm[hashData]"]').val();

                    if (_self.modifyCurrentHashData !== _self.modifyNewHashData
                        || _self.modifyAmount*1 !== 0
                        || _self.modifyNewDate !== _self.modifyCurrentDate) {

                        if (_self.modifyAmount > 0) {
                            messAmountDownOb.removeClass("hide");
                        } else if(_self.modifyAmount < 0) {
                            messAmountUpOb.removeClass("hide");
                        } else {
                            messDateChangeOb.removeClass("hide");
                        }
                        // $('#modified-amound-block').show().removeClass('hide');
                        $('.cancel-detail .alert').show();
                        $('#btn-proceed').prop("disabled",false);
                    } else {
                        _self.lockPanel();
                    }

                    $('#modified-total').html('$ '+number_format(data['modifiedTotal'], 2, ".", ""));
                    $('#modified-amound').html('$ '+number_format(Math.abs(_self.modifyAmount), 2, ".", ""));
                    if (data.coupon && data.coupon.discount && data.coupon.code) {
                        $('#modification-coupon-code').val(data.coupon.code);
                        $('#orderform-coupon_code').val(data.coupon.code);
                        $('#modified-discount').html('$ '+number_format(data.coupon.discount, 2, ".", ""));
                    } else {
                        $('#modification-coupon-code').val('');
                        $('#orderform-coupon_code').val('');
                        $('#modified-discount').html('$ '+number_format(0, 2, ".", ""));
                    }
                }

                _self.recount();
                _self.resetSumm();
                _self.resetZero();
                _self.controlRefundable();

                $('input.count, input.count-family-pass').each(function () {
                    inputFactorControl($(this));
                });

            });

        })

    },
    lockPanel: function()
    {
        // $('#modified-amound-block').hide();
        $('.cancel-detail .alert').hide();
        $('#btn-proceed').prop("disabled",true);
    }
}

let ajaxRequests = {
    array: [],
    addRequest: function(ajaxData)
    {
        let _self = this;

        _self.kill(ajaxData.url);

        let xhr = $.ajax(ajaxData);

        _self.array.push({key:ajaxData.url,xhr:xhr});
    },
    kill: function(key)
    {
        let _self = this;
        for (let i=0; i<_self.array.length; i++) {
            if (_self.array[i].key === key) {
                _self.array[i].xhr.abort();
            }
        }
    },
    isAllRequestFinished: function()
    {
        let result = true;
        let _self = this;
        for (let i=0; i<_self.array.length; i++) {
            if (_self.array[i].xhr.readyState === 1) {
                result = false;
            }
        }
        return result;
    }
}

let modification = {
    order: null,
    packageNumber: null,
    date: null,
    btn: null,
    couponCode: null,
    open: function (orderNumber, packageNumber, date) {
        order.countCheck = -1;

        let _self = this;

        _self.order = orderNumber;
        _self.packageNumber = packageNumber;
        _self.date = date;

        let modal = new bootstrap.Modal(document.getElementById('popup-modification'))
        modal.show();

        $("#js-data-container-modification").html('<div class="load-progress"></div>')

        let url = "/order/" + _self.order + "/modification/" + _self.packageNumber + "/";

        $.ajax({
            url: url,
            type: 'get',
            data: {},
            success: function (data) {
                $("#js-data-container-modification").html(data)
                // try {
                //     $('.scrollbar-inner').scrollbar();
                // } catch (e) {
                // }

                order.controlScrollBar();

                setTimeout(function () {
                    $('a[data-date="' + date + '"]').eq(0).trigger('click');
                }, 1000)
            }
        });

        return false;
    },
    orderModification: function () {
        let _self = this;

        _self.btn.addClass("btn-loading");

        let url = "/order/" + _self.order + "/modify/" + _self.packageNumber + "/";

        $.ajax({
            url: url,
            type: 'post',
            data: {},
            dataType: "json",
            success: function (data) {
                if (data.error) {
                    _self.btn.removeClass("btn-loading");
                    $("#popup-errors-modify").html('<div class="alert alert-danger">' + data.error.message + '</div>');
                }
            }
        });
    },
    dataHash: null,
    check: function (callback) {
        let _self = this;

        _self.couponCode = $("#modification-coupon-code").val();
        $("#list-tickets input[name='OrderForm[coupon_code]']").val(_self.couponCode);
        let url = "/order/" + _self.order + "/modify/" + _self.packageNumber + "/proceed/0/";
        let serialize = $("#list-tickets").serialize();

        if (_self.dataHash === serialize) {
            _self.dataHash = serialize;
            order.btnProceedEnable();
            return false;
        }

        _self.dataHash = serialize;

        ajaxRequests.addRequest({
            url: url,
            type: 'post',
            data: serialize,
            dataType: "json",
            success: function (data) {
                callback(data);
            },
            error: function (data) {
                callback(data);
            }
        });

        return false;
    },
    proceed: function () {
        let _self = this;

        _self.btn.addClass('btn-loading');

        let url = "/order/" + _self.order + "/modify/" + _self.packageNumber + "/proceed/1/";
        $.ajax({
            url: url,
            type: 'post',
            data: $("#list-tickets").serialize(),
            dataType: "json",
            success: function (data) {
                $("#order-form-left").hide();
                $("#order-form-left-confirm").removeClass("hide").html(data.html);
                $("#order-modify-info").html(data.order_modify_info);
            }
        });
        return false;
    },
    confirm: function () {
        let _self = this;

        _self.btn.addClass('btn-loading');

        let url = "/order/" + _self.order + "/modify/" + _self.packageNumber + "/proceed/2/";
        $.ajax({
            url: url,
            type: 'post',
            data: $("#list-tickets").serialize(),
            dataType: "json",
            success: function (data) {
                if (data.error) {
                    _self.btn.removeClass("btn-loading");
                    $("#popup-errors-modify").html('<div class="alert alert-danger">' + data.error + '</div>');
                } else {
                    document.location.reload();
                }
            }
        });
        return false;
    },
    pay: function () {
        let payment = $("#payment");
        let paymentAddCard = $("#payment-add-card");

        if (payment.parent().hasClass("active")) {
            payment.submit();
        }
        if (paymentAddCard.parent().hasClass("active")) {
            paymentAddCard.submit();
        }

        return false;
    },
    payment: function () {
        let _self = this;

        _self.btn.addClass('btn-loading');

        $("#popup-errors-modify").html('');
        let url = "/order/" + _self.order + "/modify/" + _self.packageNumber + "/modify-payment/";
        $.ajax({
            url: url,
            type: 'post',
            data: $("#payment").serialize(),
            dataType: "json",
            success: function (data) {
                if (data.error) {
                    _self.btn.removeClass("btn-loading");
                    $("#popup-errors-modify").html('<div class="alert alert-danger">' + data.error + '</div>');
                } else {
                    document.location.reload();
                }
            }
        });
    },
    paymentAddCard: function () {
        let _self = this;

        _self.btn.addClass('btn-loading');

        $("#popup-errors-modify").html('');
        let url = "/order/" + _self.order + "/modify/" + _self.packageNumber + "/modify-payment/";
        $.ajax({
            url: url,
            type: 'post',
            data: $("#payment-add-card").serialize(),
            dataType: "json",
            success: function (data) {
                if (data.error) {
                    _self.btn.removeClass("btn-loading");
                    $("#popup-errors-modify").html('<div class="alert alert-danger">' + data.error + '</div>');
                } else {
                    document.location.reload();
                }
            }
        });
    }
}

let cancellation = {
    btn: null,
    open: function (url) {
        let dataContainer = $("#js-data-container-cancel");
        dataContainer.html('<div class="load-progress"></div>');
        let modal = new bootstrap.Modal(document.getElementById('popup-cancel'))
        modal.show();
        dataContainer.load(url, function () {
            try {
                $('.scrollbar-inner').scrollbar();
            } catch (e) {
            }
        });
        return false;
    },
    orderCancel: function (url) {
        let _self = this;

        _self.btn.addClass("btn-loading");

        $.ajax({
            url: url,
            type: 'post',
            data: {},
            dataType: "json",
            success: function () {
                document.location.reload();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                _self.btn.removeClass("btn-loading").prop("disabled", true);
                if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    $("#popup-errors").html('<div class="alert alert-danger">' + jqXHR.responseJSON.message + '</div>');
                }
            }
        });
    }
}
