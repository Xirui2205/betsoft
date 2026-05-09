$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
			numFiles = input.get(0).files ? input.get(0).files.length : 1,
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label]);
});

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
	
	$('.thumb .btns').hide();
	$('.thumb').hover(function(){
		$(this).find('.btns').fadeIn(300);
	},
	function(){
		$(this).find('.btns').hide();
	});
	
	$('.btn-file :file').on('fileselect', function(event, numFiles, label) {
		var input = $(this).parents('.input-group').find(':text'),
				log = numFiles > 1 ? numFiles + ' files selected' : label;
		if( input.length ) {
			input.val(log);
		} else {
			if( log ) alert(log);
		}
	});
	
	 // ADD SLIDEDOWN ANIMATION TO DROPDOWN //
  $('.dropdown').on('show.bs.dropdown', function(e){
    $(this).find('.dropdown-menu').first().stop(true, true).slideDown();
  });

  // ADD SLIDEUP ANIMATION TO DROPDOWN //
  $('.dropdown').on('hide.bs.dropdown', function(e){
    $(this).find('.dropdown-menu').first().stop(true, true).slideUp();
  });

});