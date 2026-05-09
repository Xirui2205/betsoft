function viewTypeDetail(typeId) {
	resetTabs();

	$('#type-main').load('?section=328',
	{ typeId : typeId },
	function(data){
		$('#type-container div.tab').hide();
		$('#type-main').show();
		$('input#typeId').val(typeId);

		wrapperRestore();

		$('#type-container').show();
		$('.idTabs').show();
	});
}


function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#type-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("type-main");
}


function insertType() {
	resetTabs();
	$('.idTabs').hide();

	$('#type-main').load('?section=332',
	{  },
	function(data){
		$('#type-container div.tab').hide();
		$('#type-main').show();

		wrapperRestore();

		$('#type-container').show();
		$('.idTabs').show();
	});
}


function deleteType(typeId) {
	resetTabs();
	$('.idTabs').hide();

	$('#type-main').load('?section=333',
	{ typeId: typeId },
	function(data){
		$('#type-container div.tab').hide();
		$('#type-main').show();
		$("#list").load(
			'?section=138 #list',
			{  },
			function(data){
				wrapperRestore();
				hideTabs(section);
			});

		$('#type-container').show();
	});
}