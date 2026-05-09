function submitEventNavigationOrder() {
		var newEventId = $('#event-selector').val();
		$('#change_'+eventValueCol+'_'+eventId, opener.window.document).val(newEventId);
		if(submitButton != '')
			$('#'+submitButton, opener.window.document).click();

		window.close();
	}
