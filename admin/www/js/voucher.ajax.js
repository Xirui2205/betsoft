function viewVoucherDetail(voucherId) {
	resetTabs();

	$('#voucher-main').load('?section=306',
	{ voucherId : voucherId },
	function(data){
		$('#voucher-container div.tab').hide();
		$('#voucher-main').show();
		$('input#voucherId').val(voucherId);

		wrapperRestore();

		$('#voucher-container').show();
		$('.idTabs').show();
	});
}



function insertVoucher() {

	resetTabs();
	$('.idTabs').hide();

	$('#voucher-main').load('?section=308',
	{  },
	function(data){
		$('#voucher-container div.tab').hide();
		$('#voucher-main').show();

		wrapperRestore();

		$('#voucher-container').show();
		$('.idTabs').show();
	});
}



function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#voucher-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("voucher-main");
}




function submitVoucherMainForm(section, voucherId){
	submitVoucher(section,'VoucherMainForm','voucher-main','&submit=1&voucherId='+voucherId);
	return false;
}

function submitVoucher(section,formName,destElmId,params) {
	$.post('?section='+section,
		$('#'+formName).serialize()+params,
		function(data) {
			var filter = new Array();
			//filter['sport'] = $('#filter-sport').val();


			$('#'+destElmId).html(data);
			wrapperRestore();
			hideTabs(section);

			$("#list").load('?section=307 #list',
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
