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

let arPackageSliders = [];

let VacationPackageBuy =
    {
        dataSelecting: [],
        arPackageSliders: [],
        conditions: null,
        prices: null,
        urlSelectedInfo: null,
        path: [],
        init: function (urlSelectedInfo) {
            let _self = this;
            _self.urlSelectedInfo = urlSelectedInfo;
            $(document).on('click', '.js-vp-item-remove', function () {
                $('#' + $(this).data('id')).find('.js-open-close.act').trigger('click');
            });
            $(document).on('click', '.js-open-close.act', function () {
                let id = $(this).closest('.calendar-slider-block').attr('id');
                _self.dataSelecting = $.grep(_self.dataSelecting, function (e) {
                    return e.id !== id;
                });
                $(this).removeClass('act');
                $(this).closest('.calendar-slider-block').find('.calendar-slider').show();
                $(this).closest('.calendar-slider-block').removeClass('active');
                $(this).closest('.calendar-slider-block').find('.active').removeClass('active');
                $(this).closest('.calendar-slider-block').find('.js-selected-date').text('');
                $(this).closest('.calendar-slider-block').find('.js-family-pass').show();
                _self.validateSelecting();
                _self.updateSelectingInfo();
                return false;
            });
            $(document).on('change keyup', '#count', function () {
                _self.validateSelecting();
            });
            $('.js-family-pass').on('keyup', 'input', function () {
                $(this).trigger('change');
            });
            $(document).on('click', '.js-package-item-time', function () {
                if ($(this).closest('.calendar-slider-block').find('.js-family-pass').find('.has-error').length > 0) {
                    return false;
                }
                let id = $(this).closest('.calendar-slider-block').attr('id');
                if (!_self.hasIdInDataSelecting(id)) {
                    _self.dataSelecting.push($.extend({
                        id: id
                    }, $(this).data()));
                }
                $(this).closest('.calendar-slider-block').find('.js-add-more-tickets').attr('href', $(this).attr('href'));
                $(this).closest('.calendar-slider-block').find('.js-open-close').addClass('act');
                $(this).closest('.calendar-slider-block').find('.calendar-slider').hide();
                $(this).closest('.calendar-slider-block').addClass('active');
                $(this).closest('.calendar-slider-block').find('.active').removeClass('active');
                $(this).closest('.calendar-slider-block').find('.js-selected-date').html(
                    '<span>Selected date:</span>' + $(this).data('dateFormatting')
                );
                $(this).closest('.calendar-slider-block').find('.js-family-pass').hide();
                $(this).addClass('active');
                _self.validateSelecting();
                _self.updateSelectingInfo();
                return false;
            });
            $(document).on('submit', '#packages-form', function () {
                let validate = _self.validatePart(_self.countByTypeCurrent(), _self.conditions);
                if (!validate) {
                    let modal = new bootstrap.Modal(document.getElementById('selecting-package-rules'));
                    modal.show();
                    return false;
                }
                $('#btn-buy-package').addClass("btn-loading-need").addClass("btn-loading");

                let form = $(this);
                $.ajax({
                    method: 'post',
                    dataType: 'json',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function (data) {
                        if (data && data.redirectUrl) {
                            document.location.href = data.redirectUrl;
                        } else if (data && data.errorsHtml) {
                            $('#error-description').html(data.errorsHtml);
                        }
                        form.find('.btn-loading-need').removeClass("btn-loading");
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR, textStatus, errorThrown);
                        form.find('.btn-loading-need').removeClass("btn-loading");
                    }
                });

                return false;
            });
            $(document).on("click", ".js-input-factor", function () {
                let block = $(this).closest('.with-input-field');
                let input = block.find('input');
                let factor = $(this).data('factor');
                let max = input.attr('max');
                let min = input.attr('min');
                let count = input.val() * 1;
                count += factor;

                if (count <= min) {
                    count = min;
                }
                if (count >= max) {
                    count = max;
                }
                input.val(count);
                input.trigger('change');
                inputFactorControl(input);
            });
            $('.js-vp-tickets .calendar-slider-block').each(function () {
                _self.arPackageSliders.push(new packageSlider($(this).attr('id')).init());
            });
            _self.onload();
        },
        hasIdInDataSelecting: function (id) {
            for (let i = 0; i < this.dataSelecting.length; i++) {
                if (this.dataSelecting[i] === id) {
                    return true;
                }
            }
            return false;
        },
        onload: function () {
            let vacationPackagesBuy = $('#vacationPackagesBuy');
            this.conditions = vacationPackagesBuy.data('conditions');
            this.prices = vacationPackagesBuy.data('prices');
            this.validateSelecting();
            this.updateSelectingInfo();
        },
        clickByElement: function (itemType, itemId, date) {
            setTimeout(function () {
                $('.js-package-item-time[data-item-type-real="' + itemType + '"][data-item-id="' + itemId + '"][data-date="' + date + '"]').trigger('click');
            }, 100);
        },
        reset: function () {
            this.dataSelecting = [];
            this.arPackageSliders = [];
            this.path = [];
        },
        countByTypeCurrent: function () {
            let arCurrentCount = {};
            for (let i = 0; i < this.dataSelecting.length; i++) {
                if (this.dataSelecting[i].itemType) {
                    if (arCurrentCount[this.dataSelecting[i].itemType] === undefined) {
                        arCurrentCount[this.dataSelecting[i].itemType] = 1;
                    } else {
                        arCurrentCount[this.dataSelecting[i].itemType]++;
                    }
                }
            }
            return arCurrentCount;
        },
        validateSelecting: function () {
            let validate = this.validatePart(this.countByTypeCurrent(), this.conditions);
            let _self = this;
            let priceItem = $('.js-price-item');
            priceItem.removeClass('item-active');
            priceItem.each(function () {
                if (validate && parseInt($(this).data('count')) === _self.dataSelecting.length) {
                    $(this).addClass('item-active');
                }
            })

            let price = _self.prices ? $.grep(_self.prices, function (e) {
                return e.count === _self.dataSelecting.length;
            }) : [];

            let count = parseInt($('#count').val());
            let p = price && price[0] ? price[0].price * 1 : 0;
            $('#total-price').html('$ ' + number_format((p * count), 2, '.', ','));

            $('.js-container').text(_self.dataSelecting.length);

            $('#packages-form').find('input[name="selectedData"]').val(JSON.stringify(_self.dataSelecting));

            return validate;
        },
        updateSelectingInfo: function () {
            let _self = this;
            if (_self.urlSelectedInfo) {
                let form = $('#packages-form');
                $.ajax({
                    method: 'post',
                    dataType: 'json',
                    url: _self.urlSelectedInfo,
                    data: form.serialize(),
                    success: function (data) {
                        $('.js-selected-info').html(data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR, textStatus, errorThrown);
                    }
                });
            }
        },
        checkSum: function (obj) {
            let sum = 0;
            for (let el in obj) {
                if (obj.hasOwnProperty(el)) {
                    sum += parseFloat(obj[el]);
                }
            }
            if (this.prices) {
                for (let i = 0; i < this.prices.length; i++) {
                    if (this.prices[i].count === sum) {
                        return true;
                    }
                }
            }
            return false;
        },
        validatePart: function (arCount, data) {
            let count = 0;
            if (!this.checkSum(arCount)) {
                return false;
            }

            if (data === null) {
                return true;
            }

            for (let i in arCount) {
                count += arCount[i];
            }

            if (data.category) {
                count = arCount[data.category] ? arCount[data.category] : 0;
            }

            let result1 = null;
            let result2 = null;

            if (count && count >= data.min && count <= data.max) {
                if (data.and) {
                    result1 = this.validatePart(arCount, data.and);
                } else {
                    result1 = true;
                }
            } else {
                result1 = false;
            }

            let countOr = 0;

            if (data.or) {
                countOr = arCount[data.or.category] ? arCount[data.or.category] : 0;
                result2 = this.validatePart(arCount, data.or);
            } else {
                result2 = false;
            }
            //console.log('1r: ', result1)
            //console.log('2r: ', result2)
            //console.log('count: ', count);
            //console.log('countOr: ', countOr);
            return (result1 === true && result2 === false && countOr == 0 || result1 === false && result2 === true && count == 0);
        },
    }


let VPDetail = {
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
        let rp = $(".js-real-position");
        let column = $('.js-column-to-scroll-block');
        let columnAdd = $('.js-column-to-scroll-block-additional');
        let sp = $(".js-static-position");
        let topDelta = 20;
        let top = topDelta;

        if (column.position().top + column.height() + columnAdd.height() - sp.height() < $(document).scrollTop() + topDelta) {
            top = column.position().top + column.height() + columnAdd.height() - sp.height() - $(document).scrollTop();
        }

        if ($(document).scrollTop() + topDelta > rp.position().top) {
            sp.css({top: top + 'px', 'position': 'fixed'});
        } else {
            sp.css({top: '0', 'position': 'static'});
        }

        sp.width(sp.parent().width());
    },
}

$(function (){
    VPDetail.init();
})