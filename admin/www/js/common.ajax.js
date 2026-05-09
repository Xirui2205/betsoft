jQuery(document).ready(function() {
	$("#wrapper").ajaxSend(function(r, s) {
		//$("#wrapper").fadeTo('fast', 0.5);
		//$("#wrapper").fadeTo('fast', 0.5);
		$("#working").show();
	});

	var paramString = window.location.hash.substring(1);
	if(paramString.length != 0) {
		var paramString = decodeURI(paramString);
		params = jQuery.parseJSON(paramString);

		if(params.branchId != undefined && params.branchId.length != 0)
			viewBranchDetail(params.branchId);
		if(params.userId != undefined && params.userId.length != 0)
			viewUserDetail(params.userId);
	}
});

function submitAndUpload(section, section2, formName, destElmId, filterFormName) {
	var form = new FormData($('#'+formName)[0]);
	$.ajax({
		url: '?section='+section,
		type: 'post',
		xhr: function() {
			var myXhr = $.ajaxSettings.xhr();
			if (myXhr.upload) {}
			return myXhr;
		},
		success: function(res) {
			$('#'+destElmId).html(res);
			wrapperRestore();
			if ( filterFormName === undefined ) {
				$('#list').load(
					'?section='+section2 + ' #list',
					{},
					function (data) {
						wrapperRestore();
					}
				);
			} else {
				$.post(
					'?section='+section2,
					$(filterFormName).serialize(),
					function (data) {
						$('#list').html($(data).find("#list"));
						wrapperRestore()
					}
				);
			}
		},
		data: form,
		cache: false,
		contentType: false,
		processData: false
	});
}

function submitGeneric(section, formName, destElmId, params) {
	$.post('?section='+section,
		$('#'+formName).serialize()+params,
		function(data) {
			$('#'+destElmId).html(data);
			wrapperRestore();
			hideTabs(section);
		}
	);
}

function submitAndReloadGeneric(section1, section2, formName, destElmId, params, filterFormName) {
	$.post(
		'?section='+section1,
		$('#'+formName).serialize()+params,
		function(data) {
			$('#'+destElmId).html(data);
			wrapperRestore();
			if ( filterFormName === undefined ) {
				$('#list').load(
					'?section='+section2 + ' #list',
					{},
					function (data) {
						wrapperRestore();
					}
				);
			}
			else {
				$.post(
					'?section='+section2,
					$(filterFormName).serialize(),
					function(data){
						$('#list').html($(data).find("#list"));
						wrapperRestore()
					}
				);
			}
		}
	);
}

function viewGeneric(section,destElmId,params){
	elm = prepareElementForContent(destElmId);
	elm.load('?section='+section,
	params,
	function(data){
		wrapperRestore();
	});
}
function loadGeneric(section,destElmId,params){
	viewGeneric(section,destElmId,params);
}

function ld(section,destElmId,params){
	elm = $('#'+destElmId);
	elm.load('?section='+section,
	params,
	function(data){
		wrapperRestore();
	});
}

function loadTabGeneric(section, destElmId, params) {
	//alert(section+"-"+destElmId+"-"+params)
	if ($("#"+destElmId+"-loaded").val() == 0) {
		loadGeneric(section, destElmId, params);
		$("#"+destElmId+"-loaded").val(1);
	}
}

function insertGeneric(section,destElmId) {
	elm = prepareElementForContent(destElmId);
	elm.load('?section='+section,
	{},
	function(data){
		wrapperRestore();
	});
}


function prepareElementForContent(destElmId) {
	elm = '#'+destElmId;
	$(elm).html('');
	$(elm).show();
	return $(elm);
}

function hideTabs(section) {
	if (section==192) // branch insert
		$(".idTabs").hide();
}

function hideInner() {
	$('#inner').hide();
}

function wrapperRestore() {
	$('#working').hide();
	$("#wrapper").fadeIn('fast');
	$("#wrapper").css( { opacity : 1 });
	addCalendars();
}

function hideDetail(element) {
	$('#'+element).fadeOut();
	$(".idTabs").fadeOut();
}
