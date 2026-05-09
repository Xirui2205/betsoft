var window_top = $(window).scrollTop();
var localTimeDiff = 0;


$(document).ready(function() {
	hookLogin();

	synchronizeServerTime();
	setInterval('setServerClock()', 1000);
	setInterval('synchronizeServerTime()', 1000*60*3+1);

	$(".sport-menu-toggler").click(function() {
		var myid = $(this).attr("id").substring(1);
		if ($('#sport_menu_' + myid).length > 0) {
			$('#sport_menu_' + myid).slideToggle('fast');
			return false;
		}
	});

	$(".alert .toggle-alert").click(function() {
		$(this).closest(".alert").slideUp();
		return false;
	});
	
	$(".tooltip").tooltip();
});


function synchronizeServerTime() {
	$.get(
		'/time.php',
		{},
		function (data) {
			localTimeDiff = (new Date()).getTime() - (new Date(data.year, data.month - 1, data.day, data.hour, data.minute, data.second, 0)).getTime();
			setServerClock();
		},
		'json'
	);
}


function twoDigits(num) {
	if (num >= 0 && num < 10) return '0' + num;
	else return String(num);
}


function setServerClock(){
	var time = new Date( (new Date()).getTime() - localTimeDiff );
	var hours = twoDigits(time.getHours());
	var minutes = twoDigits(time.getMinutes());
	var seconds = twoDigits(time.getSeconds());
	$('#hoursC').html(hours + ':' + minutes + ':' + seconds);
	$('#dayC').text(time.getDate() + '.' + (time.getMonth()+1) + '.' + time.getFullYear());
}


function getOnline(id,url){
	parm = {}
	$.post(url,parm,function(retdata){
		$('#'+id).html(retdata);
	}, "html");
}


function regInfo(ob) {
	$("#"+ob).mouseover(function(){regError(ob+"_er_info",1);}).mouseout(function(){regError(ob+"_er_info",0);}); $("#"+ob+"_img").mouseover(function(){regError(ob+"_er_info",1);}).mouseout(function(){regError(ob+"_er_info",0);});
}


function regError(ob,a) {
	if (a == 1) $("#"+ob).show();
	else $("#"+ob).hide();
}


function hpNameSee(see,not) {
	$("."+not).hide();
	$("#"+not+'li').attr('class','');

	$("."+see).show();
	$("#"+see+'li').attr('class','sel');

	return false;
}

function hpIdSee(see,not) {
	$("#"+not).hide();
	$("#"+not+'li').attr('class','');

	$("#"+see).show();
	$("#"+see+'li').attr('class','sel');

	return false;
}

function hpSee(see, not1, not2, not3){

	$("#"+not1).hide();
	$("#"+not1+'li').attr('class','');
	$("#"+not2).hide();
	$("#"+not2+'li').attr('class','');
	$("#"+not3).hide();
	$("#"+not3+'li').attr('class','');

	$("#"+see).show();
	$("#"+see+'li').attr('class','sel');

	return false;
}


function getLiveMatch(id,live,url){
	parm = {idParent:id,n:live}
	$.post(url,parm,function(retdata) {
		for (var vl in retdata) {
			if (vl=='js_dynamic') {
				eval(retdata[vl]);
			} else {
				if (vl == "html") {
					$('#'+id).html(retdata[vl]);
				} else if (!isNaN(vl)) {
					if ($("#"+vl+'_livebet').length) {
						$("#"+vl+'_livebet').html(retdata[vl]['html']);
					} else {
						$("<div id='"+vl+"_livebet'></div>").appendTo("#"+id);
						$("#"+vl+'_livebet').html(retdata[vl]['html']);
					}
				} else {
					if (vl == 'lastNote' && retdata[vl] != null) {
						$("#"+vl).html(retdata[vl]['time']+' - '+retdata[vl]['text']+' - '+retdata[vl]['book_text']);
					} else if (vl == 'info' && retdata[vl].length != 0) {
						var oldContent = '';
						oldContent = $("#bookTextDropdown").html();
						var newContent = retdata[vl].pop();
						newContent = newContent['time']+' - '+newContent['text']+' - '+newContent['book_text'];
						if (oldContent.lastIndexOf(newContent) == -1) {
							$("#bookTextDropdown").html(oldContent + newContent+'<br />');
						}
					} else {
						$("#"+vl).html(retdata[vl]);
						if (vl=='service' && retdata[vl]=='home') {
							$('#teamNameAway').removeClass('tenisService');
							$('#teamNameHome').addClass('tenisService');
						} else if(vl=='service' && retdata[vl]=='away') {
							$('#teamNameHome').removeClass('tenisService');
							$('#teamNameAway').addClass('tenisService');
						}
					}

					if(retdata['l_sport_id'] == 1003) {
						var setNumber = retdata['stav'].substring(0,1);
						$(".third").removeClass("third");
						$('#set_'+setNumber+'_home').addClass('third');
						$('#set_'+setNumber+'_away').addClass('third');
					}
				}
			}
		}
	}, "json");
}


//function TicketProve(){
//	tick.confirmationStart();
//}


function TicketNew(){
	tick.confirmationEnd();
	tick.preapproved = false;
	tick.betsLocked = false;
	tick.DeleteAll();
	$('#box_kupon').removeClass('box-kupon-no-tabs');
	$('#ticket_container2').html('');
	$('#ticket_container2').hide();
	$('#ticket_container').show();

}


function TicketBack(rejectPreapproved, byServerCommand){
	var sendData = (true === byServerCommand ? false : true);
	tick.confirmationEnd();
	tick.betsLocked = false;
	if (undefined !== rejectPreapproved && rejectPreapproved) {
		tick.preapproved = false;
		tick.couponId = 0;
	}
	if (tick.preapproved) {
		//tick.preapproved = false;
		confirmTicket2(false);
	}
	else {
		$('#box_kupon').removeClass('box-kupon-no-tabs');
		$('#ticket_container2').html('');
		$('#ticket_container2').hide();
		$('#ticket_container').show();
		if (sendData)
			tick.ServerUpdate();
	}
}


var loginDisabled = false;
function postLogin() {
	if (loginDisabled)
		return false;	
loginDisabled = true;
	$.post(
		location.pathname + location.search, {
			lang_id:lang_id,
			nick: $('#nick').attr('value'),
			pass: $('#pass').attr('value'),
			formId: 'loginForm'
		},
		function(data){
			eval(data);
			window.setTimeout('loginDisabled=false;', 2000);
		}
	);
	return true;
}


function postPageLogin() {
	$.post(
		location.pathname + location.search, {
			lang_id: lang_id ,
			nick: $('#pageNick').attr('value'),
			pass: $('#pagePass').attr('value'),
			redirectTo: $('#redirectTo').attr('value'),
			formId: 'loginPageForm'
		},
		function(data){
			eval(data);
		}
	);
	return true;
}


function hookLogin(){
	$('#loginForm').submit( function () { postLogin(); return false; } );
	$('#loginPageForm').submit( function () { postPageLogin(); return false; } );
}


function showLoginForm() {
	$('#logoutWrapper:visible').hide();
	$('#loginWrapper:hidden').show();
}


function childHide(t) {
	$('#'+t+'>li').each(function(){
		if($(this).css('display') == 'none') $(this).show();else $(this).hide();
	});
}


function childHide2(t) {
	if ($('#'+t).css('display') == 'none')$('#'+t).show();else $('#'+t).hide();
}


function ticketUpdate(bid,cid,tt,dtit,tip,r,p,j,l) {
	if(typeof tick !='undefined') {
		tick.preapproved = false;
		tick.ChangeBet(bid,cid,tt,dtit,tip,r,p,j,1,0,l);
	}
}


function HttpRequest() {
	this.xmlhttp=false;
	this.url = ''
	this.method = 'Post';
	this.data = '';

	if (!this.xmlhttp && typeof XMLHttpRequest!='undefined') {
		try {
			this.xmlhttp = new XMLHttpRequest();
		} catch (e) {
			this.xmlhttp=false;
		}
	}

	if (!this.xmlhttp && window.createRequest) {
		try {
			this.xmlhttp = window.createRequest();
		} catch (e) {
			this.xmlhttp=false;
		}
	}
}


HttpRequest.prototype.Send = function() {
	var req = this.xmlhttp;

	this.xmlhttp.open(this.method, this.url,true);
	this.xmlhttp.onreadystatechange = function() {
		if (req.readyState == 4) {
			//alert(req.responseText)
		}
	}
	this.xmlhttp.setRequestHeader('Accept','message/xml-data')
	this.xmlhttp.send(this.data)
}


function getObj(name) {
	if (document.all) {
		if(!document.all[name]) return false;
		this.obj = document.all[name];
		this.style = document.all[name].style;
	} else if (document.getElementById) {
		if(!document.getElementById(name)) return false;
		this.obj = document.getElementById(name);
		this.style = document.getElementById(name).style;
	} else if (document.layers) {
		this.obj = getObjNN4(document,name);
		this.style = this.obj;
	}
}


function getObjNN4(obj,name) {
	var x = obj.layers;
	var foundLayer;
	for (var i=0;i<x.length;i++) {
		if (x[i].id == name) foundLayer = x[i];
		else if (x[i].layers.length) var tmp = getObjNN4(x[i],name);
		if (tmp) foundLayer = tmp;
	}
	return foundLayer;
}


function orderMarkets(order) {
	$('#matchOrderDirection').val(order);
	$('#matchListForm').submit();
}


function onlyTodayFilter() {
	$('#matchListForm').submit();
}


var lockLastPostFunc = false;
var isLast = false;
var i = 0;
function lastPostFunc() {
	lockLastPostFunc = true;
	var url = false;
	while (nextOddsUrls.length) {
		url = nextOddsUrls.shift();
		if (url.loaded) url = false;
		else break;
	}
	if (nextOddsUrls.length == 0) $('#show-more-odds-button').hide();

	if (!url) {
		lockLastPostFunc = false;
		return;
	}
	$('div#lastPostsLoader').html('<center><img src="/images/bigLoader.gif"></center>');
	
	//var params = qselect + query + type + onlyToday + todayAndTomorow + orderDirection + today + tomorow + dayAfterTomorow + oneHour + threeHours + sixHours + twelveHours + weekend + week + rateMin + rateMax + tenMinutes + twentyMinutes + thirtyMinutes + dateTimeRange + narrower;
	//if (params.length) params = '?' + params;

	var params = '?';

	$.get(url.url + params,
		//OLD: "?ajax=next&page=" + $(".wrdLatest:last").attr("id") + query + type + onlyToday + todayAndTomorow + orderDirection,
		function(data){
			if (data != "")
				$(".wrdLatest:last").after(data);

			$('div#lastPostsLoader').empty();
			lockLastPostFunc = false;
			scrollSportsbook();
		}
	);
};


function scrollSportsbook() {
	// zatím neřešeno, způsobuje chyby
	if (i == 15) {
		//if ( ($(".col1").offset().top + $(".col1").height() < $(document).scrollTop() + $('html')[0].clientHeight)) {
			if ( !lockLastPostFunc  && !isLast )
				lastPostFunc();
			i = 0;
		//}
	} else {
		if ( !lockLastPostFunc  && !isLast )
			lastPostFunc();
		i++;
	}
}


function updateFeeAndTotal(currencyE, amountE, feeE, totalE) {
	var feeStr = '';
	var totalStr = '';

	var fee = 0.0;
	if ( currenciesCount > 1 )
		var currency = currencies[ $(currencyE).val() ];
	else
		for ( tmp in currencies ) {
			var currency = currencies[tmp];
			break;
		}
	var amount = $(amountE).val();
	if (undefined === amount)
		amount = '';
	amount = parseFloat(amount.replace(",", "."));
	if ('NaN' == amount)
		amount = 0.0;
	if (undefined !== currency) {
		if (0 < currency['feeFix'])
			fee += currency['feeFix'];
		if (0 < currency['feeRel'])
			fee += currency['feeRel'] * amount;
		//TODO: localize float strings
		feeStr = fee.toFixed(2) + ' ' + currency['name'];
		totalStr = Number(amount + fee).toFixed(2) + ' ' + currency['name'];
	}
	else {
		feeStr = fee.toFixed(2);
		totalStr = Number(amount + fee).toFixed(2);
	}
	$(feeE).val(feeStr);
	$(totalE).val(totalStr);
}


function getTicketDetail(url, bet_id) {
	if ( $("#bet_detail_" + bet_id).is(':visible')) {
		 $("#bet_detail_" + bet_id).hide().slideUp('slow');
	} else {
		$.get(url ,{"ajax": 1}, function(data) {
			$("#bet_detail_" + bet_id).show();
			$("#bet_detail_" + bet_id + " div").html(data).slideDown('slow');
		});
	}
}


function comboQuickFilter(url, timeFilter){
	window.location.href = url + "?" + timeFilter + "=1";
}


function getBetDetails(url, bet_id) {
	if($("#bet_details_tr_" + bet_id).is(":visible")){
		$("#bet_details_tr_" + bet_id).hide();
		$("#plusMinus" + bet_id).text('+');
		//$("tr#" + bet_id + " td.alias").css('border-bottom-color', '#212121');
	}
	else {
		$("#bet_details_tr_" + bet_id).show();
		$("#bet_details_" + bet_id).html('<center><img src="/images/bigLoader.gif"></center>');
		$("#bet_details_" + bet_id).load(url, {"ajax":1});
		$("#plusMinus" + bet_id).text('-');
		//$("tr#" + bet_id + " td.alias").css('border-bottom-color', '#1a1a1a');
	}
}


/**
 * Toggle default value
 * @param string elementId - id of input type text
 * @param string defaultValue - something like label in input type text
 */
function toggleDefaultValue(elementId, defaultValue) {
	if ($('#'+elementId).val() === '') {
		$('#'+elementId).val(defaultValue);
	}
	$('#'+elementId).focus(function() {
		// remove default value on focus
		if ($('#'+elementId).val() == defaultValue) {
			$('#'+elementId).val('');
		}
	}).blur(function() {
		// set default value on blur (if empty)
		if ($('#'+elementId).val() === '') {
			$('#'+elementId).val(defaultValue);
		}
	});
}

// custom validation rule
$.validator.addMethod("emailstrict", 
    function(value, element) {
        return /^\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(value);
    }
);

// timepicker
var timepickerControl = {
	create: function(tp_inst, obj, unit, val, min, max, step){
		$('<input class="ui-timepicker-input" value="'+val+'" style="width:50%">')
			.appendTo(obj)
			.spinner({
				min: min,
				max: max,
				step: step,
				change: function(e,ui){
						if(e.originalEvent !== undefined)
							tp_inst._onTimeChange();
						tp_inst._onSelectHandler();
					},
				spin: function(e,ui){
						tp_inst.control.value(tp_inst, obj, unit, ui.value);
						tp_inst._onTimeChange();
						tp_inst._onSelectHandler();
					}
			});
		return obj;
	},
	options: function(tp_inst, obj, unit, opts, val){
		if(typeof(opts) == 'string' && val !== undefined)
			return obj.find('.ui-timepicker-input').spinner(opts, val);
		return obj.find('.ui-timepicker-input').spinner(opts);
	},
	value: function(tp_inst, obj, unit, val){
		if(val !== undefined)
			return obj.find('.ui-timepicker-input').spinner('value', val);
		return obj.find('.ui-timepicker-input').spinner('value');
	}
};