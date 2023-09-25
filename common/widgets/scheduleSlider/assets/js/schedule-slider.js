
var scheduleSlider = {
	frame: null,
	slidee: null,
	wrap: null,
	sly: null,
	cloneInfoOver: null,
	init: function()
	{
		var _self = this
		_self.frame  = $('.calendar-slider .frame');
		_self.slidee = _self.frame.children('ul').eq(0);
		_self.wrap   = _self.frame.parent();
		
		_self.initSly()
		
		$(window).resize(function(){
			_self.resize()
		})
		_self.resize()
	},
	resize: function(){
		/*var _self = this
		var k = 1
		var w = $(".main-slider .frame").width()/k
		$(".main-slider .frame ul li").width(w)
		_self.sly.reload()
		$(".main-slider .frame ul").width(w*$(".main-slider .frame ul li").length +100)
		*/
	},
	initSly: function()
	{
		var _self = this;
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
			scrollBy:     0,
			
			// Cycling
			//cycleBy: 'pages',
			//cycleInterval: 5000,
			//pauseOnHover: 1,
			//startPaused: 1,
			
			// Buttons
			prevPage: _self.wrap.find('.left'),
			nextPage: _self.wrap.find('.right'),
		});
		_self.sly.one('load', function(eventName) {
			$(".calendar-slider-block").css('height','auto')
			var index = $(".calendar-slider .frame li").index($(".calendar-slider .frame li.act"))
			_self.toCenter(index, true);
		});
		_self.sly.init();
	},
	toCenter: function(index, immediate){
		if(index === null) //for safary
			index = 0;
		if(immediate === null) //for safary
			immediate = false;
		
		var _self = this;
		_self.sly.toCenter(index, immediate);
	},
	initHash: function()
	{
		var _self = this
				
		var hash = document.location.hash.split("#")[1];
		if(hash)
		{
			setTimeout(function(){
				_self.setHash(hash);
			}, 1000)			
		}
		
		_self.frame.find('.act .time').click(function(){
			var hash = $(this).data("hash");
			_self.setHash(hash);
		})
		
		$(window).on("wheel",function(){
			$('html, body').stop();
		})
		
	},
	setHash: function(hash)
	{
		var a = $("a[data-hash='"+hash+"']")
		a.parent().find('.hash-active').removeClass("hash-active")
		a.addClass("hash-active")
		if($("h4[data-hash='"+hash+"']").length > 0)
		{
			$('html, body').animate({
		        scrollTop: $("h4[data-hash='"+hash+"']").offset().top-300
		    }, 1000);
		}
		var h = $("h4[data-hash='"+hash+"']");
		var allotmentDate = $("h4[data-hash='"+hash+"']").next();
		var table = $("h4[data-hash='"+hash+"']").next().next();
		
		$('.add-order .hash-active').removeClass('hash-active');
		table.addClass('hash-active')
		h.addClass('hash-active')
		allotmentDate.addClass('hash-active')
		
	    return false;
	},
	initInfoOver: function()
	{
		var _self = $(this);
		$(".show-over-info").hover(
		  function(event) {
		      if ($(event.target).hasClass('.show-over-info')) {
		          var prices = $(event.target).data('data');
		      } else {
		          var prices = $(event.target).closest('.show-over-info').data('data');
		      }
			  
			  var html = '';
			  for(var i in prices)
			  {
			      if (!prices[i].n) {
			          continue;
			      } 
				  html += '<div class="it">';
				  if (prices[i].s != '') {
				      html += '<div class="saved-block nowrap"><span class="cost">$'+prices[i].p+'</span><span class="saved">$'+(prices[i].p - prices[i].s).formatMoney(2, '.', ',')+' saved</span></div>';
	                  html += '<div class="cost">$'+prices[i].s+'</div>';
				  } else {
				      html += '<div class="cost">$'+prices[i].p+'</div>';
				  }
				  html += '<div class="name">'+decodeURIComponent(prices[i].n.replace(/\+/g,  " "))+'</div>';
				  html += '<div class="descr">'+decodeURIComponent(prices[i].d.replace(/\+/g,  " "))+'</div>';
				  html += '</div>';
			  }
			  
			  $('.info-over').find('.list').html(html);
			  
			  _self.cloneInfoOver = $('.info-over').clone();
			  
			  _self.cloneInfoOver.appendTo("body");
			  
			  var top = event.pageY-event.offsetY+$(this).height()/2 - _self.cloneInfoOver.height() - 45;
			  var left = event.pageX-event.offsetX+$(this).width()/2 - _self.cloneInfoOver.width()/2 - 5;
			  
			  _self.cloneInfoOver.css('top',top).css('left',left).show()
		  }, function() {
		      if (_self.cloneInfoOver != undefined) {
		          _self.cloneInfoOver.remove();
		      }
		  }
		);
		
	}
}
