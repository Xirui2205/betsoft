function viewUserDetail(userId, section) {
	if (undefined === section) {
		section = 227;
	}

	resetTabs();

	$('#user-profile').load('?section=' + section,
	{ userId : userId },
	function(data){
		$('#user-container div.tab').hide();
		$('#user-profile').show();
		$('input#userId').val(userId);

		wrapperRestore();

		$('#user-container').show();
		$('.idTabs').show();
	});
}



function insertUser() {

	resetTabs();
	$('.idTabs').hide();

	$('#user-profile').load('?section=249',
	{  },
	function(data){
		$('#user-container div.tab').hide();
		$('#user-profile').show();

		wrapperRestore();

		$('#user-container').show();
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
	$('#user-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("user-profile");
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
