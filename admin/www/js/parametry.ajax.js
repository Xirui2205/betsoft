function viewParamDetail(paramId, section) {
	//alert("id"+paramId);
	resetTabs();
	$('#parameters-settings-main').load(
		'?section=356',
		{ paramId : paramId },
		function(data){
			$('#parameters-settings-container div.tab').hide();
			$('#parameters-settings-main').show();
			$('input#paramId').val(paramId);
			wrapperRestore();
			$('#parameters-settings-container').show();
			$('.idTabs').show();
		});
}

function editParamDetail(paramId){
	$("#parameters-settings-main").load('?section=357',
	{ paramId : paramId },
	function(data){
		wrapperRestore();
	});
}


function hideParamDetail(element) {
	$('#'+element).fadeOut();
}

function insertParam() {
	resetTabs();
	$('.idTabs').hide();

	$('#parameters-settings-main').load('?section=358',
	{  },
	function(data){
		$('#parameters-settings-container div.tab').hide();
		$('#parameters-settings-main').show();
		wrapperRestore();
		$('#parameters-settings-container').show();
		$('.idTabs').show();
	});
}



function exportUsers() {

	resetTabs();
	$('.idTabs').hide();

	$('#user-profile').load('?section=299',
	{  },
	function(data){
		$('#user-container div.tab').hide();
		$('#user-profile').show();

		wrapperRestore();

		$('#user-container').show();
	});
}



function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#parameters-settings-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("parameters-settings");
}

function resetUserNote() {
	$('#userNote').empty();
}

function setUserNoteTemplate(telo) {
	$('#userNote').html(telo);
}


function submitUserProfileForm(section, indexSection){
	submitUser(section,'UserProfileForm','user-profile','&submit=1', indexSection);
	return false;
}

function submitUser(section,formName,destElmId,params, indexSection) {
	if (undefined === indexSection) {
		indexSection = 40;
	}
	$.post('?section='+section,
		$('#'+formName).serialize()+params,
		function(data) {
			$('#'+destElmId).html(data);
			wrapperRestore();
			hideTabs(section);

			$("#list").load('?section=' + indexSection + '#list',
			{  },
			function(data){
				wrapperRestore();
				hideTabs(section);
			});
		}
	);
}
