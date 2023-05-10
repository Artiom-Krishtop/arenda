/**
 * helpers
 */
;(function () {
	//swiper helper function
	{
		// swiper param pagination: { renderBullet: swiperRenderBullets }
		window.swiperRenderBullets = function (index, className) {
			var maxWidth = (100/this.slides.length).toFixed(6),
				style = 'max-width: calc( '+maxWidth+'% - 8px );';
			return '<span style="'+ style +'" class="' + className + '"></span>';
		};
		
		window.resizeSliderContainer = function () {
			if (!this.slidesSizesGrid.length) return;
			
			var sum = 0;
			for (var i = 0; i < this.slidesSizesGrid.length; i++) {
				sum += this.slidesSizesGrid[i];
			}
			sum += +this.params.spaceBetween * (this.slidesSizesGrid.length - 1);
			
			this.$wrapperEl.css('width', sum+'px');
		};
	}
	
	
	window.smoothScroll = function ($target, params) {
		$target = typeof $target === 'string' ? $($target) : $target;
		params = $.extend({
			offsetTop: 0,
			duration: 400
		}, params || {});
		if($target.length) $("html, body").animate({ scrollTop: $target.offset().top - params.offsetTop }, params.duration );
	};
	
	window.clickOff = function ( $el, callback ) {
		if( !$el ) return false;
		$(document).on('click', function (e) {
			if ($el.has(e.target).length === 0 && !$el.is(e.target)){
				callback($el);
			}
		});
	};
	
	// split in rows by bottom position and equal height
	window.equalHeightBot = function ($items) {
		var calc = function ($items) {
			var rows = {};
			$items.each(function () {
				var offsetBot = $(this).offset().top+$(this).height();
				if (!rows[offsetBot]) rows[offsetBot] = $([]);
				rows[offsetBot] = rows[offsetBot].add($(this));
			});
			
			for (var rowKey in rows) {
			    if (!rows.hasOwnProperty(rowKey)) continue;
				var $rows = rows[rowKey];
				$rows.css('min-height','0');
				
				if ($rows.length < 2) return;
				
				var maxHeight = 0;
				$rows.each(function () {
					if ($(this).height() > maxHeight) maxHeight = $(this).height();
				});
				$rows.css('min-height', maxHeight+'px');
			}
		};
		calc($items);
		
		$(window).resize(function () {
			calc($items);
		});
	};
}());

var cui = {
	clickOff: function ($el, callback) {
		if( !$el ) return false;
		$(document).on('click', function (e) {
			if ($el.has(e.target).length === 0 && !$el.is(e.target)){
				callback($el);
			}
		});
	},
	
	ifdefined: function ( v ) {
		return typeof v !== 'undefined' && v !== null;
	}
};

$(function () {

	var $overlay = $('.main-overlay'),
		$mobileSidebar = $('.mobile-sidebar'),
		$humburger = $('.js-open-menu');
	$humburger.on('click', function(event) {
		event.preventDefault();
		$mobileSidebar.addClass('_active');
		$overlay.addClass('_active');
		$('body').addClass('_overflow');
	});
	$overlay.on('click', function(event) {
		event.preventDefault();
		$mobileSidebar.removeClass('_active');
		$overlay.removeClass('_active');
		$('body').removeClass('_overflow');
	});

});
