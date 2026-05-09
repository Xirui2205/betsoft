function viewAffiliateBannerDetail(affiliateBannerId) {
	resetTabs();
	$('#affiliate-banner-main').load(
		'?section=381',
		{ affiliateBannerId : affiliateBannerId },
		function(data){
			$('#affiliate-banner-container div.tab').hide();
			$('#affiliate-banner-main').show();
			$('input#affiliateBannerId').val(affiliateBannerId);
			wrapperRestore();
			$('#affiliate-banner-container').show();
			$('.idTabs').show();
		}
	);
}

function editAffiliateBannerDetail(affiliateBannerId) {
	resetTabs();
	$('#affiliate-banner-main').load(
		'?section=383',
		{ affiliateBannerId : affiliateBannerId },
		function(data){
			$('#affiliate-banner-container div.tab').hide();
			$('#affiliate-banner-main').show();
			$('input#affiliateBannerId').val(affiliateBannerId);
			wrapperRestore();
			$('#affiliate-banner-container').show();
			$('.idTabs').show();
		}
	);
}

function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#affiliate-banner-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("affiliate-banner-main");
}

function insertAffiliateBanner() {
	$('#affiliate-banner-main').load(
		'?section=382',
		{},
		function(data) {
			wrapperRestore();
			$('.idTabs').hide();
			$('#affiliate-banner-container div.tab').hide();
			$('#affiliate-banner-main').show();
			$('#affiliate-banner-container').show();
		}
	);
}