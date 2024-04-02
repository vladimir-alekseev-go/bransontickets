let compare = {
    types: [],
    data: {},
    url: null,
    popupCompareModal: null,
    init: function (url, types) {
        let _self = this;
        _self.url = url;
        _self.types = types;
        _self.popupCompareModal = new bootstrap.Modal(document.getElementById('popup-compare'));

        if ($.cookie('compare')) {
            let compare = $.cookie('compare').split(';');
            for (let i = 0; i < compare.length; i++) {
                if (typeof compare[i] == 'string') {
                    let it = compare[i].split(':');
                    if (it[1] !== "" && typeof it[1] == 'string') {
                        _self.data[it[0]] = it[1].split(',');
                    }
                }
            }
        }

        $(".show-list").after(
            '<div class="container-to-compare" id="container-to-compare" style="display:none;">' +
            '<div class="bg">' +
            '<span class="count"></span>' +
            '<a class="btn btn-link js-compare-clear clear">'
            + '<span class="d-none d-sm-block"><i class="fa fa-rotate-left"></i> Clear Selection</span>'
            + '<span class="d-sm-none d-block"><i class="fa fa-rotate-left"></i> Clear</span>'
            + '</a>'
            + '<a class="btn btn-primary" id="link-to-compare">Compare items</a>' +
            '</div>' +
            '</div>');

        $(document).on("change", ".compare-add input", function () {
            _self.changeItem($(this));
            _self.positionContainer();
        });

        $(window).resize(function () {
            _self.positionContainer();
        });

        $(window).scroll(function () {
            _self.positionContainer();
        });

        $("#link-to-compare").click(function () {
            _self.popupCompareModal.show();
            _self.getData();
            return false;
        });

        $(document).on("click", ".js-compare-clear", function () {
            _self.removeInAllTypes();
            _self.popupCompareModal.hide();
            return false;
        });

        $(document).on("click", ".js-compare-remove", function () {
            _self.remove($(this).attr("data-type"), $(this).attr("data-id-external"));
            $(this).closest(".it").remove();
            return false;
        });
        _self.initItems();
    },
    initItems: function () {
        let _self = this;
        for (let i = 0; i < _self.types.length; i++) {
            _self.setItem(_self.types[i]);
        }
    },
    positionContainer: function () {
        let bg = $("#container-to-compare .bg");

        if ($(document).scrollTop() + $(window).height() - 73 > $('.container-to-compare').position().top) {
            bg.attr('style', 'position:relative;');
        } else {
            bg.attr('style', 'position:fixed;');
        }
        bg.width(bg.parent().width() - 30);
    },
    removeInAllTypes: function () {
        let _self = this;
        for (let i = 0; i < _self.types.length; i++) {
            _self.removeAll(_self.types[i]);
        }
    },
    removeAll: function (type) {
        let _self = this;

        $(".compare-add input").prop("checked", false);

        _self.data[type] = [];
        _self.setCookie();
        _self.setItem(type);
    },
    remove: function ($type, $id) {
        let _self = this;
        let tmp = [];
        for (let i = 0, len = _self.data[$type].length; i < len; i++) {
            if (_self.data[$type][i] * 1 !== $id * 1) {
                tmp.push(_self.data[$type][i]);
            }
        }
        _self.data[$type] = tmp;
        _self.setCookie();
        _self.setItem($type);

        if (_self.data[$type].length === 0) {
            _self.popupCompareModal.hide();
        }

        $(".compare-add input[value='" + $id + "']").prop("checked", false);
    },
    getData: function () {
        let _self = this;

        $("#js-data-container-compare").html('<div class="load-progress"></div>');

        $.ajax({
            url: _self.url,
            type: 'get',
            data: {},
            success: function (data) {
                $("#js-data-container-compare").html(data);
            }
        });
    },
    countTotal: function () {
        let _self = this;
        let count = 0;
        for (let i = 0; i < _self.types.length; i++) {
            count += _self.data[_self.types[i]] ? _self.data[_self.types[i]].length : 0
        }
        return count;
    },
    setItem: function (type) {
        let _self = this;

        if (!_self.data[type]) {
            return;
        }
        for (let i = 0; i < _self.data[type].length; i++) {
            if (typeof _self.data[type][i] == 'string') {
                $("#it-" + _self.data[type][i]).prop("checked", true);
            }
        }
        if (_self.countTotal()) {
            $("#container-to-compare").show();
            $(".container-hold-compare").show();
            $("#container-to-compare .count").html(
                '<span class="d-none d-sm-block">' + _self.countTotal() + ' items to compare</span>'
                + '<span class="d-sm-none d-block">' + _self.countTotal() + ' items</span>'
            );
        } else {
            $("#container-to-compare").hide();
            $(".container-hold-compare").hide();
            $("#container-to-compare .count").html('');
        }
    },
    changeItem: function (ob) {
        let _self = this;

        if (!_self.data[ob.data('type')]) {
            _self.data[ob.data('type')] = [];
        }
        if (ob.is(':checked')) {
            if (!_self.data[ob.data('type')].includes(ob.val())) {
                _self.data[ob.data('type')].push(ob.val());
            }
        } else {
            removeA(_self.data[ob.data('type')], ob.val());
        }

        _self.setCookie()

        _self.setItem(ob.data('type'))
    },
    setCookie: function () {
        let _self = this;
        let compare = '';

        for (let type in _self.data) {
            if (compare !== "") {
                compare += ";";
            }
            compare += type + ":" + _self.data[type].join();
        }
        $.cookie('compare', compare, {expires: 1, path: '/'});
    }
}
