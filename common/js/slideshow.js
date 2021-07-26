// JavaScript Document

		function slideSwitch() {
			var $active = $('#slideshow img.active');
			if ( $active.length == 0 ) $active = $('#slideshow img:last');
			var $next =  $active.next().length ? $active.next() : $('#slideshow img:first');
			$active.addClass('last-active');
			$next.css({opacity: 0.0})
			.addClass('active')
			.animate({opacity: 1.0}, 1000, function() {
				$active.removeClass('active last-active');
			});
		}
		$(function() {
			setInterval( "slideSwitch()", 5000 );
		});
		
		function slideSwitch2() {
			var $active = $('#slideshow2 img.active');
			if ( $active.length == 0 ) $active = $('#slideshow2 img:last');
			var $next =  $active.next().length ? $active.next() : $('#slideshow2 img:first');
			$active.addClass('last-active');
			$next.css({opacity: 0.0})
			.addClass('active')
			.animate({opacity: 1.0}, 1000, function() {
				$active.removeClass('active last-active');
			});
		}
		$(function() {
			setInterval( "slideSwitch2()", 5000 );
		});
		