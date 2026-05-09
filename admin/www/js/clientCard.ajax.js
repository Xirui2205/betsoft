function viewClientCardDetail(clientCardId) {
	resetTabs();
	$('#client-card-main').load(
		'?section=340',
		{ clientCardId : clientCardId },
		function(data){
			$('#client-card-container div.tab').hide();
			$('#client-card-main').show();
			$('input#clientCardId').val(clientCardId);
			wrapperRestore();
			$('#client-card-container').show();
			$('.idTabs').show();
		});
}


function editClientCardDetail(clientCardId) {
	resetTabs();
	$('#client-card-main').load(
		'?section=335',
		{ clientCardId : clientCardId },
		function(data){
			$('#client-card-container div.tab').hide();
			$('#client-card-main').show();
			$('input#clientCardId').val(clientCardId);
			wrapperRestore();
			$('#client-card-container').show();
			$('.idTabs').show();
		});
}


function blockClientCard(clientCardId, filterFormName) {
	resetTabs();
	$('#client-card-main').load(
		'?section=337',
		{ clientCardId : clientCardId },
		function(data) {
			if ( filterFormName === undefined ) {
				$('#list').load(
					'?section=334 #list',
					{},
					function (data) {
						wrapperRestore();
					}
				);
			} else {
				$.post(
					'?section=334',
					$(filterFormName).serialize(),
					function(data){
						$('#list').html($(data).find("#list"));
						wrapperRestore();
					}
				);
			}
	});
}


function unblockClientCard(clientCardId, filterFormName) {
	resetTabs();
	$('#client-card-main').load(
		'?section=341',
		{ clientCardId : clientCardId },
		function(data) {
			if ( filterFormName === undefined ) {
				$('#list').load(
					'?section=334 #list',
					{},
					function (data) {
						wrapperRestore();
					}
				);
			} else {
				$.post(
					'?section=334',
					$(filterFormName).serialize(),
					function(data){
						$('#list').html($(data).find("#list"));
						wrapperRestore();
					}
				);
			}
	});
}


function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#client-card-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("client_cards");
}


function insertClientCard() {
	resetTabs();
	$('.idTabs').hide();

	$('#client-card-main').load(
		'?section=336',
		{  },
		function(data){
			$('#client-card-container div.tab').hide();
			$('#client-card-main').show();
			wrapperRestore();
			$('#client-card-container').show();
			$('.idTabs').show();
		});
}


function cardsToBranch(filterFormName) {
	var selectedItems = new Array();
	var cciForm = $('#client-card-index-form').serializeArray();
	for (var i = 0; i < cciForm.length; i++) {
		if (cciForm[i].name == 'selectedItems[]') selectedItems.push(cciForm[i].value);
		if (cciForm[i].name == 'cardsToBranchId') var cardsToBranchId = cciForm[i].value;
	}

	$('#client-card-main').load(
		'/?section=338',
		{
			selectedItems: selectedItems,
			cardsToBranchId: cardsToBranchId
		},
		function(data) {
			$('#client-card-container').show();
			$('#client-card-main').show();
			if ( filterFormName === undefined ) {
				$('#list').load(
					'?section=334 #list',
					{},
					function (data) {
						wrapperRestore();
					}
				);
			} else {
				$.post(
					'?section=334',
					$(filterFormName).serialize(),
					function(data){
						$('#list').html($(data).find("#list"));
						wrapperRestore();
					}
				);
			}
		}
	);
}


function blockMoreCards(filterFormName) {
	var selectedItems = new Array();
	var cciForm = $('#client-card-index-form').serializeArray();
	for (var i = 0; i < cciForm.length; i++) {
		if (cciForm[i].name == 'selectedItems[]') selectedItems.push(cciForm[i].value);
	}

	$('#client-card-main').load(
		'/?section=342',
		{
			selectedItems: selectedItems,
		},
		function(data) {
			$('#client-card-container').show();
			$('#client-card-main').show();
			if ( filterFormName === undefined ) {
				$('#list').load(
					'?section=334 #list',
					{},
					function (data) {
						wrapperRestore();
					}
				);
			} else {
				$.post(
					'?section=334',
					$(filterFormName).serialize(),
					function(data){
						$('#list').html($(data).find("#list"));
						wrapperRestore();
					}
				);
			}
		}
	);
}


function unblockMoreCards(filterFormName) {
	var selectedItems = new Array();
	var cciForm = $('#client-card-index-form').serializeArray();
	for (var i = 0; i < cciForm.length; i++) {
		if (cciForm[i].name == 'selectedItems[]') selectedItems.push(cciForm[i].value);
	}

	$('#client-card-main').load(
		'/?section=343',
		{
			selectedItems: selectedItems,
		},
		function(data) {
			$('#client-card-container').show();
			$('#client-card-main').show();
			if ( filterFormName === undefined ) {
				$('#list').load(
					'?section=334 #list',
					{},
					function (data) {
						wrapperRestore();
					}
				);
			} else {
				$.post(
					'?section=334',
					$(filterFormName).serialize(),
					function(data){
						$('#list').html($(data).find("#list"));
						wrapperRestore();
					}
				);
			}
		}
	);
}