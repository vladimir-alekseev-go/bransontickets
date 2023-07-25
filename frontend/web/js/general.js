//sly

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
