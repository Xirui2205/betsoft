/* PARAMETER BRANCH */
function editParameterBranch(){
	$("#parameter-branch").load('?section=209',
	{  },
	function(data){
		wrapperRestore();
	});
}

function viewParameterBranch(){
	$("#parameter-branch").load('?section=206',
	{  },
		function(data){
			wrapperRestore();
	});
}

function infoParameterBranch($paramId){
	$.post('?section=362',
	{paramId : $paramId},
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
				$('#parameter-branch').append($infoDiv);
		},
		'json'

	);
}


/* PARAMETER USER */
function editParameterUser(){
	$("#parameter-user").load('?section=210',
	{  },
		function(data){
			wrapperRestore();
	});
}

function viewParameterUser(){
	alert("xx");
	$("#parameter-user").load('?section=207',
	{  },
		function(data){
			wrapperRestore();
	});
}



/* PARAMETER SYSTEM */
function editParameterSystem(){
	$("#parameter-system").load('?section=251',
	{  },
		function(data){
			wrapperRestore();
	});
}

function viewParameterSystem(){
	$("#parameter-system").load('?section=250',
	{  },
		function(data){
			wrapperRestore();
	});
}

/* GENERIC */
function resetTabs() {
	$('.tabsLoaded').each(function(index) {
		$(this).val(0);
	});
	$('#parameter-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("parameter-branch");
}

function deleteParam($paramId) {
	if ($paramId) {
		$("#parameter-branch").load('?section=361',
		{paramId : $paramId},
			function(data){
				wrapperRestore();
		});
    }

    //resetTabs();
	$('#parameter-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("parameter-branch");
}

function newParam($name, $value, $description) {
	$("#parameter-branch").load('?section=360',
	{name : $name, value : $value, description : $description},
		function(data){
			wrapperRestore();
	});

	$('#parameter-container div').html('');
	$('.idTabs li a').each(function(index) {
		$(this).removeClass('selected');
	});
	$(".idTabs").idTabs("parameter-branch");
}