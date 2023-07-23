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

//nav tabs slide

$(document).ready(function() {
    function active_tab_highlight_pos(init = false, resize = false) {
  
      let nav_tabs = $('.nav-tabs');
  
      let active_tab = $(nav_tabs).find('.active');
  
      let nav_tabs_pos = {
        x: $(nav_tabs).offset().left,
        y: $(nav_tabs).offset().top
      }
  
      let active_tab_pos = {
        x: $(active_tab).offset().left - nav_tabs_pos.x,
        y: $(active_tab).offset().top - nav_tabs_pos.y,
        w: $(active_tab).outerWidth(),
        h: $(active_tab).outerHeight()
      }
  
      if (init) {
        $(nav_tabs).prepend('<li class="active-highlight"></li>');
      }
      
      let active_highlight = $(nav_tabs).find('.active-highlight');
      
      if (resize) {
          $(active_highlight).css({
            'transition': 'none'
        });
      } else {
          $(active_highlight).css({
            'transition': 'all .5s ease'
        });
      }
  
      $(active_highlight).css({
        'width': active_tab_pos.w + 'px',
        'height': active_tab_pos.h + 'px',
        'left': active_tab_pos.x + 'px',
        'top': active_tab_pos.y + 'px'
      });
    }

    active_tab_highlight_pos(true);
    
    $(window).on('resize', function() {
      active_tab_highlight_pos(false, true);
    });
  
    $('[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
      active_tab_highlight_pos();
    });
});
