function loadDetail(targetId, sectionId, transactionTypeId) {
	$('#' + targetId).load(
		'?section=' + sectionId,
		{ transactionTypeId:transactionTypeId },
		function(data){
			wrapperRestore();
		}
	);
}

function submitTransactionType(formName, destElmId, sectionSave, sectionLoad, reloadEl, params) {
	$.post('?section='+sectionSave,
		$('#'+formName).serialize() + params,
		function(data) {
			$('#'+destElmId).html(data);
			$('#' + reloadEl).load('?section=' + sectionLoad + ' #' + reloadEl,
			{  },
			function(){
				wrapperRestore();
			});
		}
	);
}
