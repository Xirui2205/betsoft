/* BRANCH MAIN  */

function createBranch(){
	$("#branch-detail").load('?section=166',
	{ },
	function(){
		wrapperRestore();
		$(".idTabs").hide();
	});
}

function editBranchDetail(branchId){
	$("#branch-main").load('?section=191',
	{ branchId : branchId },
	function(data){
		wrapperRestore();
	});
}

function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#branch-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("branch-main");
}

function viewBranchDetail(branchId) {
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
			$("#branch-main").html(data);

			$('#branch-container div.tab').hide();
			$('#branch-main').show();

			wrapperRestore();

			$('#branch-container').show();
			$('.idTabs').show();

		},
		url: '?section=190',
		data: { branchId: branchId }
	});

	/*
	ajaxManager.add({
		type: "POST",
		success: function(data) {
			$("#branch-parameter").html(data);
			$("#working-feedback").append(', parameters');
		},
		url: '?section=166',
		data: { branchId : branchId }
	});


	ajaxManager.add({
		type: "POST",
		success: function(data) {
			$("#branch-bank").html(data);
			$("#working-feedback").append(', bank accounts');
		},
		url: '?section=164',
		data: { branchId : branchId }
	});


	ajaxManager.add({
		type: "POST",
		success: function(data) {
			$("#branch-contract").html(data);
			$("#working-feedback").append(', contracts');
		},
		url: '?section=165',
		data: { branchId : branchId }
	});


	ajaxManager.add({
		type: "POST",
		success: function(data) {
			$("#branch-employee").html(data);
			$("#working-feedback").append(', employee');
		},
		url: '?section=185',
		data: { branchId : branchId }
	});

	ajaxManager.add({
		type: "POST",
		success: function(data) {
			$("#branch-user").html(data);
			$("#working-feedback").append(', user');
		},
		url: '?section=183',
		data: { branchId : branchId }
	});

	ajaxManager.add({
		type: "POST",
		success: function(data) {
			$("#branch-host").html(data);
			$("#working-feedback").append(', host');
			wrapperRestore();
			$('#branch-container').show();
			$('.idTabs').show();
			$("#working-feedback").html('');
		},
		url: '?section=180',
		data: { branchId : branchId}
	});*/
}

//jen nacteni view (ostatni zalozky uz jsou nacteny)
function viewBranchDetailOnly(branchId){
	$("#branch-main").load('?section=190',
	{ branchId: branchId },
	function(data){
		wrapperRestore();
	});
}

function insertBranchMain(section){
	$("#branch-main").load('?section='+section,
	{  },
	function(data){
		wrapperRestore();
		$('#branch-container').show();
		$('#branch-main').show();
	});
}

function submitBranchMainForm(section){
	submitBranch(section,'BranchMainForm','branch-main','&submit=1');
	return false;
}

function submitBranch(section,formName,destElmId,params) {
	$.post('?section='+section,
		$('#'+formName).serialize()+params,
		function(data) {
			$('#'+destElmId).html(data);
			wrapperRestore();
			hideTabs(section);

			$("#list").load('?section=193 #list',
			{  },
			function(data){
				wrapperRestore();
				hideTabs(section);
			});
		}
	);
}

function deleteBranch(element, branchId	){
//implement me

	/*if(confirm(trConfirmDelete)){
		$.post(
			'?section=165',
			{ branchId:branchId },
			function(data){
				wrapperRestore()
				$('#branch-create').before(data);
				$('tr#branchRow'+branchId).remove();
			},
			function(data){
				wrapperRestore()
				$('#branch-create').before(data);
			}
		);
	}*/
}

function closeBranchDetail(element){
	$("#branch-detail").hide();
	$(".idTabs").hide();
}



/* PARAMETERS */
function editBranchParameter(branchId){
	$("#branch-parameter").load('?section=167',
		{ branchId: branchId },
		function(data){
		wrapperRestore();
	});
}

function viewBranchParameter(branchId){
	$("#branch-parameter").load('?section=166',
		{ branchId: branchId },
		function(data){
			wrapperRestore();
	});
}


/* BANK ACCOUNT */
function editBranchBankAccount(branchId){
	$("#branch-bank").load('?section=169',
		{ branchId: branchId },
		function(data){
		wrapperRestore();
	});
}

function viewBranchBankAccount(branchId){
	$("#branch-bank").load('?section=164',
		{ branchId: branchId },
		function(data){
			wrapperRestore();
	});
}

/* CONTRACT */
function editBranchContract(branchId){
		$("#branch-contract").load('?section=168',
	{ branchId: branchId },
	function(data){
		wrapperRestore();
	});
}

function viewBranchContract(branchId){
	$("#branch-contract").load('?section=165',
	{ branchId: branchId },
	function(){
		wrapperRestore();
	});
}

function loadContractForm(branchId){
	var templateId = $('#new-contract-template-picker').val();
	$('#new-contract-form').load('?section=163',
		{ templateId:templateId, branchId:branchId},
		function(){;
			wrapperRestore();
			$('#contract-form-submit').remove();
		}
	);
}

function submitNewContractForm(formType, tabToShow){

	if(tabToShow === undefined){
		tabToShow = formType;
	}

	$.post(
		'?section=167',
		$('#'+formType).serialize()+'&save='+formType,
		function(data){
			wrapperRestore();
		}
	);
}

function cancelContract(branchId){

	$("#branch-contract").load('?section=203',
		{ branchId: branchId },
		function(data){
			wrapperRestore();
		}
	);
}

/* BRANCH EMPLOYEE */
function viewBranchEmployee(branchId, employeeId){
	$("#branch-employee").load('?section=185',
		{ branchId : branchId, employeeId : employeeId },
		function(data){
			wrapperRestore();
		}
	);
}

function showEmployeeTimesheet(employeeId) {
	$.post('?section=186',
		$('#employeeTimesheetForm').serialize()+"&employeeId="+employeeId,
		function(data) {
		  $('#timesheet').html(data);
		wrapperRestore();
	});
}

/* BRANCH USER  */
function viewBranchUser(branchId, userId){
	$("#branch-user").load('?section=183',
		{ branchId: branchId, userId : userId },
		function(data){
			wrapperRestore();
		}
	);
}

function assignUsersToBranch(){
	$.post('?section=184',
		$('#usersToAssignForm').serialize(),
		function(data) {
			$('#branch-user').html(data);
			wrapperRestore();
		}
	);
}

/* BRANCH HOST */
function createBranchHost(branchId){
	$("#branch-host").load('?section=182',
	{ branchId: branchId },
	function(data){
		wrapperRestore();
	});
}

function viewBranchHost(branchId, hostId){
	$("#branch-host").load('?section=180',
	{ branchId: branchId, hostId : hostId },
	function(data){
		wrapperRestore();
	});
}

function editBranchHost(branchId, hostId){
	$("#branch-host").load('?section=181',
	{ branchId: branchId, hostId : hostId },
	function(data){
		wrapperRestore();
	});
}

function submitBranchHostForm(section){
	submitGeneric(section,'BranchHostForm','branch-host','&submit=1');
	return false;
}

function editBranchHost(branchId, hostId){
	$("#branch-host").load('?section=181',
	{ branchId: branchId, hostId : hostId },
	function(data){
		wrapperRestore();
	});
}

function submitBranchHostForm(section){
	submitGeneric(section,'BranchHostForm','branch-host','&submit=1');
	return false;
}


function editBranchOpeningHours(branchId){
	$("#branch-opening-hours").load('?section=287',
	{ branchId: branchId },
	function(data){
		wrapperRestore();
	});
}

function viewBranchOpeningHours(branchId){
	$("#branch-opening-hours").load('?section=286',
	{ branchId: branchId },
	function(data){
		wrapperRestore();
	});
}

function submitBranchOpeningHoursForm(section){
	submitGeneric(section,'BranchOpeningHoursForm','branch-opening-hours','&submit=1');
	return false;
}

function newParam(branchId, name, value, description) {
	$.post(
		'?section=363',
		{ branchId: branchId, name : name, value : value, description : description },
		function(data){
			//$(".idTabs").idTabs("branch-parameter");
			wrapperRestore();
			viewBranchParameter(branchId);
		}
	);
}

function infoParameterBranch(paramId, branchId){
	$.post('?section=364',
	{paramId : paramId, branchId : branchId},
		function(data) {
				wrapperRestore();
				if ( $('#info').length ) {
					$("#info").remove();
				}
				var $infoDiv = '<div class="info" id="info">'+
				'<table>'+
				'<tr>'+
				'<td colspan="4">Parameter: <a class="div-a-close">' + data['paramName'] + '</a></td>'+
				'<td style="text-align: right;"><a onclick="closeInfo();" class="div-a-close">X</a></td>'+
				'</tr>'+
				'<tr>'+
				'<td>Time</td>'+
				'<td>Admin</td>'+
				'<td>Old value</td>'+
				'<td>New value</td>'+
				'<td>Message</td>'+
				'</tr>';
				for (var i in data['paramData']) {
					$infoDiv = $infoDiv + "<tr>"+
					                        "<td>" + data['paramData'][i]['time'] + "</td>"+
					                        "<td>" + data['paramData'][i]['first_name'] + " " + data['paramData'][i]['surname'] + "</td>"+
					                        "<td>" + data['paramData'][i]['old_value'] + "</td>"+
					                        "<td <a class='info-new-param-text'>" + data['paramData'][i]['new_value'] + "</a></td>"+
					                        "<td>" + data['paramData'][i]['message'] + "</td>"+
					                      "</tr>\n";
				}

				$infoDiv = $infoDiv + "</table></div>\n";
				$('#branch-parameter').append($infoDiv);
		},
		'json'
	);
}

function deleteParam(branchId, paramId) {
	$.post(
		'?section=372',
		{ branchId: branchId, paramId : paramId },
		function(data){
			wrapperRestore();
			$(".idTabs").idTabs("branch-parameter");
			viewBranchParameter(branchId);
		}
	);
}