let filterControl = {
    init: function () {
        $('#list-filter-open').click(function () {
            $(window).scrollTop(0);
            $('body').addClass('body-list-filter-opened');
            $('#list-filter').addClass('list-filter-opened');
            return false;
        });
        $('#list-filter-close').click(function () {
            $('body').removeClass('body-list-filter-opened');
            $('#list-filter').removeClass('list-filter-opened');
            return false;
        });
    },
    buildApplied: function () {
        let arrayTags = [];
        let filter = $('#list-filter');
        let list = $('#filter-applied-list');
        let title = filter.find("input[name='s[title]']");
        let dateFrom = filter.find("input[name='s[dateFrom]']");
        let dateTo = filter.find("input[name='s[dateTo]']");
        let priceFrom = filter.find("input[name='s[priceFrom]']");
        let priceTo = filter.find("input[name='s[priceTo]']");
        let checkboxChecked = filter.find("input:checked");
        let filterRoomsDescription = filter.find(".js-filter-rooms-description");

        if (dateFrom.length === 0) {
            dateFrom = filter.find("input[name='s[arrivalDate]']");
            dateTo = filter.find("input[name='s[departureDate]']");
        }

        if (title.val() !== '') {
            arrayTags.push(title.val());
        }
        if (filterRoomsDescription.text() !== '') {
            arrayTags.push(filterRoomsDescription.text());
        }
        arrayTags.push(dateFrom.val() + ' - ' + dateTo.val());
        arrayTags.push('$' + (priceFrom.val() > 0 ? priceFrom.val() : 0) + ' - $' + priceTo.val());
        checkboxChecked.each(function () {
            arrayTags.push($(this).next().text());
        });

        list.html('');
        for (let i = 0; i < arrayTags.length; i++) {
            list.append('<span class="tag">' + arrayTags[i] + '</span>');
        }
    },
}

$(function () {
    filterControl.init();
});

let dataList =
    {
        list: null,
        panel: null,
        filter: null,
        tId: null,
        lastUrlRequest: null,
        init: function(list, panel, filter, data)
        {
            let _self = this;
            _self.list = list;
            _self.panel = panel;
            _self.filter = filter;
            let displayControl = $("#display-control");
            let panelSorting = $("#panel-sorting");
            let listFilterUp = $("#list-filter-up");

            _self.filter.submit(function () {
                filterControl.buildApplied();
                _self.sendFormAjax();
                return false;
            })

            _self.filter.find("[type='checkbox'], [type='radio'], [type='text']").change(function(){
                _self.filter.submit();
            });
            _self.filter.on('change','select', function(){
                _self.filter.submit();
            })

            listFilterUp.on("keyup input", "input[name='s[title]']", function() {
                _self.filter.find("input[name='s[title]']").val($(this).val());
                _self.filter.find("input[name='s[title]']").trigger('keyup');
            });
            listFilterUp.on("keypress", "input[name='s[title]']", function (e) {
                if (e.which === 13) {
                    _self.filter.submit();
                    return false;
                }
            });
            _self.filter.on("keyup input", "input[name='s[title]']", function() {
                window.clearTimeout(_self.tId);
                _self.tId = window.setTimeout(function() {
                    _self.filter.submit();
                }, 500);
            });

            _self.filter.find(".field .icon").click(function(){
                $(this).prev().focus()
            })

            displayControl.find("a").click(function () {
                let display = $(this).attr("data-display")
                _self.filter.find("[name='s[display]']").val(display)

                displayControl.find("a").removeClass("act")

                $(this).addClass("act")

                if (display === "grid") {
                    _self.list.addClass("show-list-grid");
                    _self.list.removeClass("show-list-map");
                    _self.list.removeClass("show-list-list");
                    panelSorting.addClass("list-grid");
                    panelSorting.removeClass("list-map");
                    panelSorting.removeClass("list-list");
                    $('#items-list').show();
                    $('#google-map').hide();
                } else if (display === "map") {
                    _self.list.addClass("show-list-map");
                    _self.list.removeClass("show-list-grid");
                    _self.list.removeClass("show-list-list");
                    panelSorting.addClass("list-map");
                    panelSorting.removeClass("list-grid");
                    panelSorting.removeClass("list-list");
                    googleMapList.load($(this).data('url'));
                    $('#items-list').hide();
                    $('#google-map').show();
                } else {
                    _self.list.addClass("show-list-list");
                    _self.list.removeClass("show-list-grid");
                    _self.list.removeClass("show-list-map");
                    panelSorting.addClass("list-list");
                    panelSorting.removeClass("list-grid");
                    panelSorting.removeClass("list-map");
                    $('#items-list').show();
                    $('#google-map').hide();
                }
                return false;
            });

            panelSorting.find("select").change(function() {
                let option = $(this).find('option:selected');
                panelSorting.find(".js-selected").html(option.html());
                _self.filter.find("[name='s[fieldSort]']").val($(this).val());
                _self.filter.submit();
                return false;
            });

            _self.filter.on("click", ".show-more-filter", function() {
                let $this = $(this),
                    $wrap = $this.closest(".it"),
                    $elements = $wrap.find(".more-elem-filter"),
                    $txtFilter = $wrap.find(".txt-filter");
                if ($elements.css("display") === "none") {
                    $elements.slideDown();
                    $txtFilter.filter(".more").hide();
                    $txtFilter.filter(".less").show();
                } else {
                    $elements.slideUp();
                    $txtFilter.filter(".less").hide();
                    $txtFilter.filter(".more").show();
                }
                return false;
            });

            if($("#slider-range").length)
            {
                var sr = $("#slider-range"),
                    min = sr.data("min")*1,
                    max = sr.data("max")*1,
                    from = sr.data("valueFrom")*1,
                    to = sr.data("valueTo")*1;

                sr.slider({
                    range: true,
                    min: min,
                    max: max,
                    values: [ from, to],
                    slide: function( event, ui ) {
                        _self.setValue(ui.values[0], ui.values[1]);
                    },
                    stop: function( event, ui ) {
                        _self.filter.submit();
                    }
                });
                _self.setValue(from, to)
            }

            if($("#time-range").length)
            {
                var sr = $("#time-range"),
                    min = sr.data("min")*1,
                    max = sr.data("max")*1,
                    from = sr.data("valueFrom")*1,
                    to = sr.data("valueTo")*1;

                sr.slider({
                    range: true,
                    min: min,
                    max: max,
                    values: [ from, to],
                    slide: function( event, ui ) {
                        _self.setValueTime(ui.values[0], ui.values[1]);
                    },
                    stop: function( event, ui ) {
                        _self.filter.submit();
                    }
                });
                _self.setValueTime(from, to)
            }

            filterControl.buildApplied();

        },
        setValue: function(i,j)
        {
            $("#range-from").text(i)
            $("#range-to").text(j)
            $("input[name='s[priceFrom]']").val(i)
            $("input[name='s[priceTo]']").val(j)
        },
        setValueTime: function(i,j)
        {
            $("input[name='s[timeFrom]']").val(i)
            $("input[name='s[timeTo]']").val(j)

            i = this.getTime(i);
            j = this.getTime(j);

            $("#time-from").text(i)
            $("#time-to").text(j)

        },
        getTime: function(i){
            if(i < 13)
                return i+":00 AM";
            else
                return (i-12)+":00 PM";
        },
        sendFormAjax: function()
        {
            var _self = this

            if (_self.filter.find("#s-arrivaldate").length != 0 && _self.filter.find("#s-arrivaldate").val() != '') {
                if (_self.filter.find("#s-departuredate").val() == '') {
                    return false;
                }
            }

            if(_self.obHotelFilter && ! _self.obHotelFilter.checkForm())
            {
                _self.obHotelFilter.openPopup();
                return false;
            }
            $('.view-more-block').remove()
            _self.filter.find("[name=_csrf]").remove()

            let url = document.location.pathname + "?" + _self.filter.serialize();
            if (_self.lastUrlRequest === url) {
                return false;
            }

            _self.lastUrlRequest = url;

            try{
                history.pushState(null, null, url);
            }catch (e) {}

            _self.list.addClass('load-progress');

            $.ajax({
                url: url,
                success: function(result){
                    let html = result;

                    try{
                        let data = JSON.parse(result);
                        html = data.listHtml;
                    }catch(e){}

                    if (result.sliderPriceRange) {
                        let sliderRange = $( "#slider-range" );
                        let valuesCurrent = sliderRange.slider( "values" );
                        // var valueCurent = sliderRange.slider( "value" );
                        let option = { max: Math.ceil(result.sliderPriceRange.max/30)*30 };

                        if (option.max < valuesCurrent[0]) {
                            valuesCurrent[0] = option.max;
                        }
                        if (option.max < valuesCurrent[1]) {
                            valuesCurrent[1] = option.max;
                        }
                        if (option.max < sliderRange.slider( "value" )) {
                            sliderRange.slider( "value", option.max);
                        }
                        _self.setValue(valuesCurrent[0], valuesCurrent[1]);

                        sliderRange.slider( "option",  option);
                        $("#slider-price-range-grid > div").eq(1).html("$ "+option.max/3);
                        $("#slider-price-range-grid > div").eq(2).html("$ "+option.max/3*2);
                        $("#slider-price-range-grid > div").eq(3).html("$ "+option.max);
                    }

                    try{
                        html = html.listHtml ? html.listHtml : html;
                    }catch(e){}

                    _self.list.html(html);

                    if (result.itemCount) {
                        _self.setItemCount(result.itemCount);
                    }
                    try{
                        compare.initItems();
                    }catch(e){}
                }
            }).done(function() {
                _self.list.removeClass('load-progress');
            });
        },
        setItemCount: function(itemCount) {
            $('#items-count').text(itemCount + ' item' + (itemCount === 1 ? '' : 's'));
        },
    }