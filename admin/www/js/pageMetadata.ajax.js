function viewPageMetadataDetail(controllerId, sectionId, langId) {
	resetTabs();
	$('#page-metadata-main').load(
		'?section=' + sectionId,
		{ controllerId : controllerId, langId : langId },
		function (data) {
			$('#page-metadata-container div.tab').hide();
			$('#page-metadata-main').show();
			$('input#controllerId').val(controllerId);
			wrapperRestore();
			$('#page-metadata-container').show();
			$('.idTabs').show();
		}
	);
}

function editPageMetadataDetail(controllerId, langId) {
	resetTabs();
	$('#page-metadata-main').load(
		'?section=312',
		{ controllerId : controllerId, langId : langId },
		function (data) {
			$('#page-metadata-container div.tab').hide();
			$('#page-metadata-main').show();
			$('input#controllerId').val(controllerId);
			wrapperRestore();
			$('#page-metadata-container').show();
			$('.idTabs').show();
		}
	);
}

function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#page-metadata-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("page-metadata-main");
}