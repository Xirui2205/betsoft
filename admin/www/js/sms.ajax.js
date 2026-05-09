/* SMS  */

function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#sms-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("sms-view");
}

function viewSmsDetail(smsId) {
	resetTabs();

	var ajaxManager = $.manageAjax.create('cacheQueue', {
		/*success : function(data) {
			$('#working').hide();
			alert('all calls completed');
		},*/
		queue: true,
		cacheResponse: false
	}
	);

	//and add an ajaxrequest with the returned function
	ajaxManager.add({
		type: "POST",
		success: function(data) {
			$("#sms-view").html(data);

			$('#sms-container div.tab').hide();
			$('#sms-view').show();

			wrapperRestore();

			$('#sms-container').show();
			$('.idTabs').show();

		},
		url: '?section=354',
		data: { smsId: smsId }
	});

}

function closeSmsDetail(element){
	$("#sms-view").hide();
	$(".idTabs").hide();
}
