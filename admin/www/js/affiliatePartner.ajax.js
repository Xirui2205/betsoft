function viewAffiliatePartnerDetail(affiliatePartnerId, sectionId) {
	resetTabs();
	$('#affiliate-partner-main').load(
		'?section='+sectionId,
		{ affiliatePartnerId : affiliatePartnerId },
		function(data){
			$('#affiliate-partner-container div.tab').hide();
			$('#affiliate-partner-main').show();
			$('input#affiliatePartnerId').val(affiliatePartnerId);
			wrapperRestore();
			$('#affiliate-partner-container').show();
			$('.idTabs').show();
		}
	);
}

function editAffiliatePartnerDetail(affiliatePartnerId) {
	resetTabs();
	$('#affiliate-partner-main').load(
		'?section=377',
		{ affiliatePartnerId : affiliatePartnerId },
		function(data){
			$('#affiliate-partner-container div.tab').hide();
			$('#affiliate-partner-main').show();
			$('input#affiliatePartnerId').val(affiliatePartnerId);
			wrapperRestore();
			$('#affiliate-partner-container').show();
			$('.idTabs').show();
		}
	);
}

function assignAffiliateBanner(affiliatePartnerId) {
	$('#affiliate-partner-banner').load(
		'?section=379',
		{ affiliatePartnerId : affiliatePartnerId },
		function(data) {
			$('#affiliate-partner-container div.tab').hide();
			$('#affiliate-partner-banner').show();
			$('input#affiliatePartnerId').val(affiliatePartnerId);
			wrapperRestore();
			$('#affiliate-partner-container').show();
			$('.idTabs').show();
		}
	);
}

function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#affiliate-partner-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("affiliate-partner-main");
}

function insertAffiliatePartner() {
	$('#affiliate-partner-main').load(
		'?section=376',
		{},
		function(data){
			wrapperRestore();
			$('.idTabs').hide();
			$('#affiliate-partner-container div.tab').hide();
			$('#affiliate-partner-main').show();
			$('#affiliate-partner-container').show();
		}
	);
}

function toggleCodeDetail(partnerId, bannerId, sectionId, trBannerId) {
	var container = $('#'+trBannerId);

	if (container.is(":visible")) {
		container.hide();
		return false;
	} else {
		loadGeneric(sectionId, trBannerId, {affiliatePartnerId : partnerId, affiliateBannerId: bannerId});
		return false;
	}
}