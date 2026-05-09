function viewEventDetail(eventId) {
	resetTabs();

	$('#event-main').load('?section=288',
	{ eventId : eventId },
	function(data){
		$('#event-container div.tab').hide();
		$('#event-main').show();
		$('input#eventId').val(eventId);

		wrapperRestore();

		$('#event-container').show();
		$('.idTabs').show();
	});
}



function insertEvent() {

	resetTabs();
	$('.idTabs').hide();

	$('#event-main').load('?section=291',
	{  },
	function(data){
		$('#event-container div.tab').hide();
		$('#event-main').show();

		wrapperRestore();

		$('#event-container').show();
		$('.idTabs').show();
	});
}



function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#event-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("event-main");
}




function submitEventMainForm(section, eventId){
	submitEvent(section,'EventMainForm','event-main','&submit=1&eventId='+eventId);
	return false;
}

function submitEvent(section,formName,destElmId,params) {
	$.post('?section='+section,
		$('#'+formName).serialize()+params,
		function(data) {
			var filter = new Array();
			//filter['sport'] = $('#filter-sport').val();


			$('#'+destElmId).html(data);
			wrapperRestore();
			hideTabs(section);

			$("#list").load('?section=290 #list',
			{
				'filter[sport]':$('#filter-sport').val(),
				'filter[keyword]':$('#filter-keyword').val(),
				'filter[region]':$('#filter-region').val(),
				'paginator[recsPerPage]':$('#paginator_recsPerPage').val(),
				'paginator[pageNum]':$('#paginator_pageNumCur').val(),
				'paginator[pageNumPrev]':$('#paginator_pageNumPrev').val(),
				'paginator[pageNumNext]':$('#paginator_pageNumNext').val(),
				'paginator[pageNumFirst]':$('#paginator_pageNumFirst').val(),
				'paginator[pageNumLast]':$('#paginator_pageNumLast').val()
			},
			function(data){
				wrapperRestore();
				hideTabs(section);
			});
		}
	);
}

function submitEventPositions(sportId, regionId, eventId, position, positionOffegen) {
	if (undefined === position)
		position = $('#change_navigationOrder_' + eventId).val();
	if (undefined === positionOffegen)
		positionOffergen = $('#change_navigationOrderOffergen_' + eventId).val();
	$.post('?section=305',
		{ 'events' : [ {
			'sportId' : sportId,
			'regionId' : regionId,
			'eventId' : eventId,
			'position' : position,
			'positionOffergen': positionOffergen
		} ] },
		function(data) {
			if (undefined !== data['errors']) {
				for (var i in data['errors'])
					$('#feedbackContainer').append('<div class="feedback error">' + data['errors'][i] + "</div>\n");
			}
			if (undefined !== data['infos']) {
				for (var i in data['infos'])
					$('#feedbackContainer').append('<div class="feedback message">' + data['infos'][i] + "</div>\n");
			}
			$('#working').hide();
		},
		'json'
	);
}
