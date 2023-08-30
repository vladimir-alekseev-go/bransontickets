let pageNav = {
    selector: '.view-more-block a.ajax',
    init: function () {
        let _self = this

        $(document).on("click", _self.selector, function () {
            if ($(this).data("url") !== undefined && $(this).data("url") !== "")
                _self.loadmorebtn($(this).data("container"), $(this).data("url"))
            return false;
        })
        $(window).scroll(function () {
            _self.scroll()
        })
        _self.scroll()
    },
    scroll: function () {
        let _self = this;
        let top = $(window).scrollTop() + $(window).height()
        let obA = $(_self.selector)
        let offset = obA.offset();

        if (offset && top > offset.top)
            obA.trigger("click")
    },
    loadmorebtn: function (container, url) {
        let _self = this

        $(container).append('<div class="load-progress load-progress-page"></div>')

        $('.view-more-block').remove()
        $('.loadindicator-holder').show()

        $.get(url, function (result) {
            let html = result;

            try {
                let data = JSON.parse(result);
                html = data.listHtml;
            } catch (e) {
            }

            try {
                html = html.listHtml ? html.listHtml : html;
            } catch (e) {
            }

            $(container).append(html)
            $(container).find(".load-progress").remove();
            $('.loadindicator-holder').hide();
            if ($('.show-list .line-title').eq(1)) {
                $('.show-list .line-title').eq(1).closest('.it').removeClass('display-list-title');
                $('.show-list .line-title').eq(1).remove();
            }

            if (result.itemCount) {
                dataList.setItemCount(result.itemCount);
            }
            compare.initItems();
        })
    },
    loadNextPage: function () {
        let _self = this;

        $(_self.selector).trigger("click");
    }
}
pageNav.init();
