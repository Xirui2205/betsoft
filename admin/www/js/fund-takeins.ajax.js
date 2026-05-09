setInterval('reloadDataTable()', 30 * 1000);


function loadOddTypes() {
	$('#working').show();
	var typeIds = $('#type-ids-selector').val();
	var eventIds = $('#event-ids-selector').val();
	
	
	$.post(
		'?section=303',
		{
			typeIds: typeIds,
			eventIds: eventIds
		},
		function(oddTypes) {
			var html = '';
			$.each(oddTypes, function(key, type) {
				html = html + '<option value="' + type['oddTypeId'] + '">' + type['name'] + '</option>'
			});
			$('#odd-type-ids-selector').html(html);
			$('#working').hide();
		},
		'json'
	);
}


function loadTypes() {
	$('#working').show();
	var eventIds = $('#event-ids-selector').val();
	var sportId = $('#sport-id-selector').val();
	
	$.post(
		'?section=304',
		{
			eventIds: eventIds,
			sportId: sportId
		},
		function(types) {
			var html = '';
			$.each(types, function(key, type) {
				html = html + '<option value="' + type['betTypeId'] + '">' + type['name'] + '</option>'
			});
			$('#type-ids-selector').html(html);
			$('#working').hide();
			
			loadOddTypes();
		},
		'json'
	);
}


function loadEvents() {
	$('#type-ids-selector').html('');
	$('#odd-type-ids-selector').html('');
	loadEventOptsForSport('#sport-id-selector', '#event-ids-selector', true);
}


function reloadDataTable() {
	var eventIds = $('#event-ids-selector').val();
	var typeIds = $('#type-ids-selector').val();
	var oddTypeIds = $('#odd-type-ids-selector').val();
	var order = $('#order-selector').val();
	var betId = $('#bet-id-input').val();
	var betAlias = $('#bet-alias-input').val();
	var sportId = $('#sport-id-selector').val();
	
	$('#data-table-container').load(
		'?section=302',
		{
			filter: {
				'event-ids': eventIds,
				'type-ids': typeIds,
				'odd-type-ids': oddTypeIds,
				'bet-id': betId,
				'bet-alias': betAlias,
				'sport-id': sportId
			},
			order: {'col-type': order},
			ajax: true
		}
	);
}
