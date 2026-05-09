$(document).ready(function(){
	
	"use strict";
	
	$('.scrollup').hide();
	
	$('[data-toggle="tooltip"]').tooltip();
	
	$(window).scroll(function(){
		if ($(this).scrollTop() > 100) {
			$('.scrollup').fadeIn("slow", function() {
				$('.scrollup').blur()
				// Animation complete
			});
		} else {
			$('.scrollup').fadeOut("slow", function() {
				$('.scrollup').blur()
				// Animation complete
			});
		}
	});
	
	$('.scrollup').click(function(){
		$("html, body").animate({ scrollTop: 0 }, 500);
		return false;
	});
	
});