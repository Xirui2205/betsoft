function viewBetStatDetail(betId) {
	resetTabs();
	$('#betting-statistics-main').load(
		'?section=346',
		{ betId : betId },
		function(data){
			$('#betting-statistics-container div.tab').hide();
			$('#betting-statistics-main').show();
			$('input#betId').val(betId);
			wrapperRestore();
			$('#betting-statistics-container').show();
			$('.idTabs').show();
		}
	);
}

function viewBetCurrentStatDetail(betId) {
	resetTabs();
	$('#betting-statistics-main').load(
		'?section=347',
		{ betId : betId },
		function(data){
			$('#betting-statistics-container div.tab').hide();
			$('#betting-statistics-main').show();
			$('input#betId').val(betId);
			wrapperRestore();
			$('#betting-statistics-container').show();
			$('.idTabs').show();
		}
	);
}

function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#betting-statistics-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("betting_statistics");
}

function loadEvents() {
	$('#type-ids-selector').html('');
	$('#odd-type-ids-selector').html('');
	loadEventOptsForSport('#sport-id-selector', '#event-ids-selector', true);
}

function toggleBetStatDetail(betId, sectionId, trBetId) {
	var container = $('#'+trBetId);

	if (container.is(":visible")) {
		container.hide();
		return false;
	} else {
		loadGeneric(sectionId, trBetId, {betId:betId});
		return false;
	}
}

$(document).ready(function() {
	$("#tab1").delegate("tr", "click", function() {
		$(this).addClass("selected").siblings().removeClass("selected"); 
	});

	$('.tip').tooltip({
		position: { 
			my: "left+5 center", 
			at: "right center", 
			using: function( position, feedback ) {
				$( this ).css( position );
				$('.ui-tooltip-content').click(function() {
					$('.tip').tooltip("close");
				});
			}
		},
		content:function(callback) {
			if ($('.ui-tooltip').length != 0) return;
			$.get('?section=348', {
				betId: $(this).attr('name')
			}, function(data) {
				callback(data);
				wrapperRestore();
			});
		},
	});
});