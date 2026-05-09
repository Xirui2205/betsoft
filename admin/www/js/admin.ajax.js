/* ADMIN MAIN  */

function createAdmin(){
	$("#admin-detail").load('?section=283',
	{ },
	function(){
		wrapperRestore();
		$(".idTabs").hide();
	});
}

function editAdminDetail(adminId){
	$("#admin-main").load('?section=284',
	{ adminId : adminId },
	function(data){
		wrapperRestore();
	});
}

function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#admin-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("admin-main");
}

function viewAdminDetail(adminId) {
	resetTabs();

	var ajaxManager = $.manageAjax.create('cacheQueue', {
		/*success : function(data) {
			$('#working').hide();
			alert('all calls completed');
		},*/
		queue: true,
		cacheResponse: false
	}
	);

	//and add an ajaxrequest with the returned function
	ajaxManager.add({
		type: "POST",
		success: function(data) {
			$("#admin-main").html(data);

			$('#admin-container div.tab').hide();
			$('#admin-main').show();

			wrapperRestore();

			$('#admin-container').show();
			$('.idTabs').show();

		},
		url: '?section=282',
		data: { adminId: adminId }
	});

}

//jen nacteni view (ostatni zalozky uz jsou nacteny)
function viewAdminDetailOnly(adminId){
	$("#admin-main").load('?section=282',
	{ adminId: adminId },
	function(data){
		wrapperRestore();
	});
}

function insertAdminMain(section){
	$("#admin-main").load('?section='+section,
	{  },
	function(data){
		wrapperRestore();
		$('#admin-container').show();
		$('#admin-main').show();
	});
}

function submitAdminMainForm(section){
	submitAdmin(section,'AdminMainForm','admin-main','&submit=1');
	return false;
}

function submitAdminPrivilegesForm(section){
	submitAdmin(section,'AdminPrivilegesForm','admin-privileges','&submit=1');
	return false;
}

function submitAdmin(section,formName,destElmId,params) {
	$.post('?section='+section,
		$('#'+formName).serialize()+params,
		function(data) {
			$('#'+destElmId).html(data);
			wrapperRestore();
			hideTabs(section);

			$("#list").load('?section=281 #list',
			{  },
			function(data){
				wrapperRestore();
				hideTabs(section);
			});
		}
	);
}

function deleteAdmin(element, adminId	){
	//implement me
}

function closeAdminDetail(element){
	$("#admin-detail").hide();
	$(".idTabs").hide();
}
