httpRequest =  null;

  function TTCupon(){

   this.position = "static";
   this.top      = 0;
   this.left     = 0;
   this.width    = 400;
   this.words = {};
   this.bet   = {};
   this.betOrder = undefined;
   this.objName = "ob";
   this.simple = 0;
   this.maxBetNumSimple = 0;
   this.maxBetNumKombi = 0;
   this.maxBetNumMaxi = 0;
   this.maxBetNumSystem = 0;
   this.maxGroupCount = 0;
   this.page = 0;
   this.page_help = 1;
   this.betNum = 0;
   this.simpleSum = '0.00';
   this.totalBet = '0.00';
   this.totalBetInPoints = '0.00';
   this.totalWin = '0.00';
   this.totalRate = '0.00';
   this.pointType = null;
   this.rateAdvance = null;
   this.rateAdvanceWithoutAdvance = '0.00';
   this.action = '';
   this.method = 'post';
   this.minSum = 1;
   this.maxSum = 5000;
   this.block = false;
   this.url = '';
   this.lock = true;
   this.bet_code = '';
   this.lockInterval = null;
   this.blockSysDel = false;
   this.objectBanker = '';
   this.XmlData = '';
   this.initializing = false;
   this.groupsCount = 0;
   this.groupCounts = [];
   this.hasGroupT = false;
   this.combinations = false;
   this.combinedBetNum = 0;
   this.preapproved = false;
   this.showGetPoints = false;
   this.getPoints = 0;
   this.preferenceSizes = [];
   this.showRateAdvance = false;
   this.rateAdvanceCost = 0;
   this.showPoints = false;
   this.betsLocked = false;
   this.checkProveInterval = undefined;
   this.mail = 1;
   this.sms = 0;
   this.couponId = 0;
   this.confirming = false;
   this.confirmationRuns = 0;
   this.restartConfirmation = false;
   this.freeCode = [];
   this.savedClick = false;
   this.repeatingClick = false;
   this.givenTotalBet = 0.0;
   this.ticketNumber = 0;

   this.systemAr = {};
   this.systemAr[2] = {}; this.systemAr[2][1] = 2; this.systemAr[2][2] = 1;
   this.systemAr[3] = {}; this.systemAr[3][1] = 3; this.systemAr[3][2] = 3;this.systemAr[3][3] = 1;
   this.systemAr[4] = {}; this.systemAr[4][1] = 4;this.systemAr[4][2] = 6;this.systemAr[4][3] = 4;this.systemAr[4][4] = 1;
   this.systemAr[5] = {}; this.systemAr[5][1] = 5;this.systemAr[5][2] = 10;this.systemAr[5][3] = 10;this.systemAr[5][4] = 5;this.systemAr[5][5] = 1;
   this.systemAr[6] = {}; this.systemAr[6][1] = 6;this.systemAr[6][2] = 15;this.systemAr[6][3] = 20;this.systemAr[6][4] = 15;this.systemAr[6][5] = 6;this.systemAr[6][6] = 1;
   this.systemAr[7] = {}; this.systemAr[7][1] = 7;this.systemAr[7][2] = 21;this.systemAr[7][3] = 35;this.systemAr[7][4] = 35;this.systemAr[7][5] = 21;this.systemAr[7][6] = 7;this.systemAr[7][7] = 1;
   this.systemAr[8] = {}; this.systemAr[8][1] = 8;this.systemAr[8][2] = 28;this.systemAr[8][3] = 56;this.systemAr[8][4] = 70;this.systemAr[8][5] = 56;this.systemAr[8][6] = 28;this.systemAr[8][7] = 8;this.systemAr[8][8] = 1;
   this.systemAr[9] = {}; this.systemAr[9][1] = 9;this.systemAr[9][2] = 36;this.systemAr[9][3] = 84;this.systemAr[9][4] = 126;this.systemAr[9][5] = 126;this.systemAr[9][6] = 84;this.systemAr[9][7] = 36;this.systemAr[9][8] = 9;this.systemAr[9][9] = 1;
   this.systemAr[10] = {}; this.systemAr[10][1] = 10;this.systemAr[10][2] = 45;this.systemAr[10][3] = 120;this.systemAr[10][4] = 210;this.systemAr[10][5] = 252;this.systemAr[10][6] = 210;this.systemAr[10][7] = 120;this.systemAr[10][8] = 45;this.systemAr[10][9] = 10;this.systemAr[10][10] = 1;
  }


  TTCupon.prototype.prtA = function(key,word){


   this.words[key] = word;

  };


  TTCupon.prototype.genFastStakes = function(changeMethodId) {
    var prefix = ''/*'<br>'*/;
    switch(changeMethodId) {
      case 1: // simple/solo
        var changeMethod = this.objName + '.changeBetSimple'; var valuesArr = [10, 20, 50, 100]; break;
      case 2: // ako
        var changeMethod = this.objName + '.changeBetAko'; var valuesArr = [10, 20, 50, 100]; break;
      case 3: // kombi
        var prefix = ''; var changeMethod = this.objName + '.changeBetKombi'; var valuesArr = [1, 2, 3, 5, 10, 15, 20, 50]; break;
      default: ;
    }
	  return prefix + ($.map(valuesArr, function(v,i){return '<a href="javascript:void(0);" onclick="' + changeMethod + '(' + v + ');">' + v + '</a>';})).join(' ');
  };

  TTCupon.prototype.changeBetSimple = function(value) {
	  $('#bet_simple_sum_all').val(value);
	  if(this.SetSimpleSum(value)) this.ServerUpdate();
  };

  TTCupon.prototype.changeBetAko = function(value) {
    $('#total_bet').val(value);
    this.bet_code=''; // neznam vyznam
   // console.log(value);
    if(this.updatedAmountTotalBet('total_bet', value)) this.ServerUpdate();
  };

  TTCupon.prototype.changeBetKombi = function(value) {
    var groups = this.getGroupsCount(false);
    for(var i=1; i<=groups; i++) {
      $('#mcStake' + i).val(value);
      $('#mcUsed' + i).attr('checked', 'checked');
      this.enableCombination(i, true); 
    }
    parseCombinationStakes();    
    this.ServerUpdate();
  };

  TTCupon.prototype.genSpinner = function(tickType) {
    return '<div class="inputSpinnerCont right"><a href="javascript:void(0);" onclick="'+this.objName+'.incInput('+tickType+');">+</a><a href="javascript:void(0);" onclick="'+this.objName+'.decInput('+tickType+');">-</a></div>';
  };

  TTCupon.prototype.incInput = function(tickType) {
    this.changeInput(tickType, 1);
  };
  TTCupon.prototype.decInput = function(tickType) {
    this.changeInput(tickType, -1);
  };
  TTCupon.prototype.changeInput = function(tickType, direction) {
    if(this.betsLocked) return;
    switch(tickType) {
      case 1: 
        var value = this.simpleSum; break;
      case 2:
        var value = this.totalBet; break;
      case 3: 
        var value = this.totalBet; break;
      default: ;
    }
    var newValue = Math.max(10, this.calcProgressiveStep(value, direction));
    switch(tickType) {
      case 1: this.changeBetSimple(newValue); break;
      case 2: this.changeBetAko(newValue); break;
      case 3: 
    	  $('#total_bet').val(newValue);
    	  if(parseTotalBet()) tick.ServerUpdate(); 
    	  break;
      default: ;
    }
  };

  TTCupon.prototype.calcProgressiveStep = function(value, direction) {
    var newValue = 0;
    var steps = [10, 20, 30, 50, 100, 200, 300, 500, 1000, 2000, 3000, 5000];
    if(direction <0 ) steps = steps.reverse();
    $.each(steps, function(i,v) {
      if(direction>0) { if(value < v && !newValue) newValue = v; }
      else { if(value > v && !newValue) newValue = v; }
    });
    return newValue;
  };
TTCupon.prototype.formatCurrencyPrecision = function(value, _currency, showUnits, _precision) {
		try {
			var num = Number(value).toFixed(undefined === _precision ? 2 : _precision);
			if ( undefined === showUnits || !showUnits )
				return num;
			if (undefined === _currency && undefined !== currency)
				_currency = currency;
			
			if (undefined === _currency)
				return num;
			else
				return num + '&nbsp;' + _currency;
	}
	catch (e) {
		return 'NaN';
	}
};

TTCupon.prototype.formatCurrency = function(value, _currency, showUnits) {
	return this.formatCurrencyPrecision(value, _currency, showUnits, precisionStake);
};

TTCupon.prototype.formatPoints = function(value, _pointType, showUnits) {
	return Number(value).toFixed(0);
};

TTCupon.prototype.formatCurrencyWin = function(value, _currency, showUnits) {
	return this.formatCurrencyPrecision(value, _currency, showUnits, precisionWin);
};

TTCupon.prototype.formatRate = function(value) {
	return Number(value).toFixed(2);
};

TTCupon.prototype.Prepare = function() {

   //var item  = '<div id="ghtr1" class="item"><div id="ghtr1_0" class="item_nonactive">'+this.words['type1']+'</div><div id="ghtr1_1" class="item_nonactive">'+this.words['type2']+'</div></div>';
   //var item  = '<ul id="ghtr1"><li id="ghtr1_0"><a class="t1 first"  href="#"><span>'+this.words['type1']+'</span></a></li><li id="ghtr1_1"><a class="t2" href="#"><span>'+this.words['type2']+'</span></a></li><li id="ghtr1_2"><a class="t3 last" href="javascript:void(0);"><span>'+this.words['okf16']+'</span></a></li></ul>';
   var item  = '<div id="ticketButtons" class="tabs" style="display:none"><ul id="ghtr1" class="nav nav-tabs yellow"><li id="ghtr1_0" class="active"><a class="t1 first"  href="#"><span>'+this.words['type1']+'</span></a></li><li id="ghtr1_1"><a class="t2 last" href="#"><span>'+this.words['type2']+'</span></a></li></ul></div>';
   TTCupon.bit1 = item;
   //var item1 = '<div id="ghtr2" class="bet_list betslip-bets"></div>';
   var item1 = '<ul id="ghtr2" class="betslip-bets"></ul>';
   var item2 = '<div id="ghtr3" style="display:none;"><strong>'+this.words['okf1']+'</strong></div>';
   var item7 = '<div id="ghtr8" class="item2"></div>';
   var item3 = '<div id="ghtr4" class="bet_warning"><p class="dashesBot"><strong class="msg_err">'+this.words['okf38']+'</strong></p></div>';
   var item4 = '<div id="ghtr5" class="bet_sum"></div>';
   var item5 = '<div id="ghtr6" class="bet_bottom"></div>';
   var item8 = '<div id="ghtr9"></div>';
   var item6 = '<div id="ghtr7" class="bet_sum"></div>';
   //document.write('<div style="position:'+this.position+';top:'+this.top+'px;left:'+this.left+'px;"  id="ticket_div"><form method="'+this.method+'"  id="bet_ticket_form" action="'+this.action+'&'+document.location.search.substring(1,document.location.search.length)+'"><div class="Rfum" id="sportbookTicker"><div class="dotine"><h2>'+this.words['okf31']+'</h2><a href="" target="_blank" ><img class="helpTick" alt="" src="/img/icons/ico-help2.gif"/></a>'+item+'<div class="ticket"><div class="inTicket">'+item3+item1+item2+item7+item4+item5+item8+item6+'</div></div></div></div><input type="hidden" id="bet_ticket_form_cupon_data" value="" /></form></div>');
   document.write(
'<div id="ticket_div">' +
	'<form method="'+this.method+'" id="bet_ticket_form" action="'+this.action+'&'+document.location.search.substring(1,document.location.search.length)+'">' +
	'<div class="Rfum" id="sportbookTicker"><div class="content" id="ticket_content">'+item+item3+'<div class="text"><div class="inTicket tab-content yellow">'+item1+item2+item7+item4+item5+item8+item6+'</div></div></div></div>' +
	'<input type="hidden" id="bet_ticket_form_cupon_data" value="" />' +
	'</form>' +
'</div>');

};

TTCupon.prototype.okfBoxShow = function(ob,text,l,t) {

    var poz = $(ob).offset();
    var agent=navigator.userAgent.toLowerCase();
    var name=navigator.appName;

     if(((agent.indexOf("msie") != -1) && name=="Microsoft Internet Explorer")) {l = l +41;t = t - 2;}
    //$('#ticket_help_basic').appendTo($(ob)).css('position','absolute');
    $('#ticket_help_basic').appendTo('body');
    //$('#ticket_help_basic .okf_hid2_left').remove();
    $('#ticket_help_basic').css('left',(poz.left+l) + "px").css('top',(poz.top+t) + "px").show();
    $('#ticket_help_basic .okf_hid_body_left').text(text);

};

TTCupon.prototype.okfBoxHide = function(text) {
     $('#ticket_help_basic').hide();
};

TTCupon.prototype.SetSum = function() {

	var ob1 = new getObj('ghtr5');
	var ob2 = new getObj('ghtr6');
	var ob3 = new getObj('ghtr7');
	var ob4 = new getObj('ghtr9');
	var ob5 = new getObj('ticketButtons');

	if (isNaN(this.totalBet)) this.totalBet = '0.00';
	if (isNaN(this.totalBetInPoints)) this.totalBetInPoints = '0.00';
	if (isNaN(this.totalWin)) this.totalWin = '0.00';

	if (this.betNum == 0) {

		ob1.style.display = 'none';
		ob2.style.display = 'none';
		ob3.style.display = 'none';
		ob4.style.display = 'none';
		ob5.style.display = 'none';

	} else if (this.page == 0) {

		//ob1.obj.innerHTML = '<div class="bet_sum_okf"><div class="bet_sum_okf_l">'+this.words['okf3']+'</div><div class="bet_sum_okf_r"><input type="text" value="'+this.simpleSum+'" id="bet_simple_sum_all" onblur="'+this.objName+'.SetSimpleSum(String(this.value));'+this.objName+'.ServerUpdate();"  name="press1"  maxlength="8" /></div></div> <div class="bet_sum_okf"><div class="bet_sum_okf_l">'+this.words['okf4']+'</div><div class="bet_sum_okf_r">'+this.betNum+'</div></div> <div class="bet_sum_okf"><div class="bet_sum_okf_l">'+this.words['okf5']+' </div><div class="bet_sum_okf_r_2">'+this.totalBet+'</div></div> <div class="bet_sum_okf"><div class="bet_sum_okf_l">'+this.words['okf6']+'</div><div class="bet_sum_okf_r_2">'+this.totalWin+'</div></div>';
		
		// vklad na sázku, vklad celkem, výhra - původní
		/*ob1.obj.innerHTML = '<div class="tp"><div class="tp-pole"><label class="width4" for="bet_simple_sum_all">'+this.words['okf3'] + "<span class=\"nastavit-vklad\">" +this.genFastStakes(1) + '</span></label><div class="tp-boxik"><div class="cc"><input type="text" class="txt width1" style="margin-left:-1px; margin-right:2px" value="'+this.simpleSum+'" id="bet_simple_sum_all" onfocus="'+this.objName+'.onInputFocus(this);" onblur="if('+this.objName+'.SetSimpleSum(this.value)) '+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;" name="press1"  maxlength="8" /><input type="text" class="txt width3e" readonly="readonly" value="'+currency+'"/></div>' + this.genSpinner(1) + '</div></div>'+
		'<div class="tp-pole"><label class="va-middle" for="total_bet">'+this.words['total']+'</label><div class="tp-boxik"><div class="cc"><input type="text" class="txt width3b" value="'+this.formatCurrency(this.totalBet)+'" readonly="readonly" id="total_bet" /><input type="text" class="txt width3e" readonly="readonly" value="'+currency+'"/></div></div></div>'+
		'<div class="tp-pole"><label class="va-middle" for="total_win">'+this.words['okf6']+'</label><div class="tp-boxik"><div class="cc"><input type="text" class="txt width3d strong" value="'+this.formatCurrencyWin(this.totalWin)+'" readonly="readonly" id="total_win" /><input type="text" class="txt width3e" readonly="readonly" value="'+currency+'"/></div></div></div></div>';
		*/
	   
		// vklad na sázku, vklad celkem, výhra
		ob1.obj.innerHTML = '<div class="betslip-summary">' +
								'<div class="form-group"><label for="bet_simple_sum_all">' + this.words['okf3'] + '</label> <input type="text" value="'+this.simpleSum+'" id="bet_simple_sum_all" onfocus="'+this.objName+'.onInputFocus(this);" onblur="if('+this.objName+'.SetSimpleSum(this.value)) '+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;" name="press1"  maxlength="8" /> ' /*+ currency*/ +
								'<div class="stake"><span>' + this.words['total'] + '</span> <span class="value">' + this.formatCurrency(this.totalBet) + ' ' + currency + '</span></div>' +
								'<div class="possible"><span>' + this.words['okf6'] + '</span> <span class="value">'+this.formatCurrencyWin(this.totalWin)+' ' + currency + '</span></div>' +
							'</div>';
					
		//ob2.obj.innerHTML = '<input type="button" onclick="'+this.objName+'.SendData(this);return false;" value="'+this.words['okf8']+'" '+(this.betNum>0?'':'disabled="disabled"')+' />';
		
		// přepočítat, pokračovat, zaslat výsledky emailem - původní
		/*ob2.obj.innerHTML = '<div class="tp"><input type="button" class="sbm2" onclick="return false;" id="ticketRecalculate" value="'+this.words['okf39']+'" />'
			+ '<input type="button" class="sbm1" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.SendData(this);return false;" id="ticket_data_sender" value="'+this.words['okf8']+'" '+(this.betNum>0?'':'="disabled"')+' /></div>'
			+ '<div class="tp"><input type="checkbox" id="mail_send" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.sendMailClicked();"'
			+ (this.mail == 0 ? '' : ' checked="checked"') + ' /> <label for="mail_send"><strong>'+this.words['okf30']+'</strong></label></div>';
		*/
	   
		// smaž tiket, pokračovat
		var betSlipButtons = '<div class="betslip-buttons">';
		
		if(this.betNum>0){
			// Place bet button
			betSlipButtons += '<a class="btn pull-right" href="#" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.SendData(this);return false;" id="ticket_data_sender">' + this.words['okf8'] + '</a>';
		}
		// remove all onclick
		betSlipButtons += '<a href="javascript:'+this.objName+'.DeleteAll();void(0);" onclick="if ('+this.objName+'.saveClick(event)) return false;">' + this.words['act3'] + '</a>' +
							'</div>';
		ob2.obj.innerHTML = betSlipButtons;
		
			//+ '<div class="tp"><input type="checkbox" id="sms_send" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.sendSmsClicked();"'
			//+ (this.sms == 0 ? '' : ' checked="checked"') + ' /> <label for="sms_send"><strong>'+this.words['okf40']+'</strong></label></div>';
		ob1.style.display = 'block';
		ob2.style.display = 'block';
		ob4.style.display = 'block';
		ob5.style.display = 'block';

	} else if (this.page == 1) {

		this.totalWin  = parseFloat(this.totalWin).toFixed(precisionWin);
		this.totalRate = parseFloat(this.totalRate).toFixed(2);
		this.totalRateWithoutAdvance = parseFloat(this.totalRateWithoutAdvance).toFixed(2);

		if (this.isMaxicombinator()) {
			var html = '<div class="ticket-padding">';
			var n = this.getGroupsCount(false);
			for (var i = 0; i < this.combinations.length; ++i) {
				var k = i + 1;
				var comb = this.combinations[i];
				var checked = (0 == parseFloat(comb.stake) ? '' : ' checked="checked"');
				html += '<div class="mc-spacing text-center"><label for="mcStake' + k + '">';
				// html += '<input type="checkbox" id="mcUsed' + k + '"' + checked + ' onchange="enableCombination(' + i + ', this.checked); ' + this.objName + '.ServerUpdate(); return true;" onclick="if ('+this.objName+'.saveClick(event)) return false;"/>';
				html += '<input type="checkbox" id="mcUsed' + k + '"' + checked + ' onclick="if ('+this.objName+'.saveClick(event)) return false; else enableCombination(' + i + ', this.checked); ' + this.objName + '.ServerUpdate(); return true;"/>';
				html += k + '/' + n + ' (' + comb.betCount + ')</label>';
				html += '<input type="text" class="txt width3" id="mcStake' + k + '" value="' + this.formatCurrency(parseFloat(comb.stake)) + '" onfocus="'+this.objName+'.onInputFocus(this);" onfocus="'+this.objName+'.onInputFocus(this);" onblur="if(parseCombinationStakes()) ' + this.objName + '.ServerUpdate(); return true;" onclick="if ('+this.objName+'.saveClick(event)) return false;"/>';
				html += '</div>';
			}

			html += '<div class="text-center"><span class=\"nastavit-vklad\">'  + this.genFastStakes(3) + '</span></div>';
			html += '</div><!-- konec .ticket-padding -->'
			+ '<div class="betslip-summary"><div class="form-group">';
			
			var getPoints = '';
			if ( this.showGetPoints ) {
				// body input
				//var getPoints = '<div class="tp-pole"><label class="va-middle" for="get_points">'+this.words['getPoints']+'</label><div class="tp-boxik"><div class="cc"><input type="text" class="txt width3b" value="'+this.getPoints+'" name="press2" id="get_points" maxlength="8" readonly="1"/></div></div></div>';
				// body span + hidden
				getPoints = '<div><label>'+this.words['getPoints']+'</label><span class="value">'+this.getPoints+'</span><input type="hidden" value="'+this.getPoints+'" name="press2" id="get_points" /></div>';
			}
			html += '<div class="tp">'
			//počet sázek: +'<div class="tp-pole"><label class="width4 s28">'+this.words['okf4']+'</label><div class="tp-boxik"><div class="cc ta-right">'+this.combinedBetNum+'</div></div></div>'
			//+'<div class="tp-pole"><label class="width4 s28">'+this.words['okf7']+'</label><div class="tp-boxik"><div class="cc ta-right"><strong>'+this.totalRate+'</strong></div></div></div>'
			+'<div><label for="total_bet">'+this.words['okf5']+'</label><input type="text" value="'+this.formatCurrency(this.totalBet)+'" name="press2" id="total_bet" maxlength="8"  onfocus="'+this.objName+'.onInputFocus(this);" onblur="'+this.objName+'.bet_code=\'\'; if(parseTotalBet()) '+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;"/></div>'/*currency+ this.genSpinner(3)*/
			+ getPoints
			// total_win input:
			//+'<div class="possible"><label class="va-middle" for="multi_win">'+this.words['okf6']+'</label><input type="text" class="txt width3d" value="'+this.formatCurrencyWin(this.totalWin)+'" name="press3" id="total_win" maxlength="8" readonly="readonly" /> '+currency+'</div>'
			// total_win span + total_win hidden:
			+'<div class="possible"><span>'+this.words['okf6']+'</span><span id="total_win-span" class="value">'+this.formatCurrencyWin(this.totalWin)+' '+currency+'</span><input type="hidden" value="'+this.formatCurrencyWin(this.totalWin)+'" name="press3" id="total_win" /></div>'
			+'</div></div></div><!-- konec .betslip-summary -->';

			ob1.obj.innerHTML = html;

		} else {
			//ob1.obj.innerHTML = '<div class="bet_sum_okf"><div class="bet_sum_okf_l">'+this.words['okf4']+'</div><div class="bet_sum_okf_r">'+this.betNum+'</div></div><div class="bet_sum_okf"><div class="bet_sum_okf_l">'+this.words['okf7']+'</div><div class="bet_sum_okf_r">'+this.totalRate+'</div></div> <div class="bet_sum_okf"><div class="bet_sum_okf_l">'+this.words['okf5']+'</div><div class="bet_sum_okf_r_2"> <input type="text" value="'+this.totalBet+'"  onblur="'+this.objName+'.totalBet=String(this.value);'+this.objName+'.SetTotalSum();'+this.objName+'.ServerUpdate();"  name="press2"  maxlength="8" /></div></div> <div class="bet_sum_okf"><div class="bet_sum_okf_l">'+this.words['okf6']+'</div><div class="bet_sum_okf_r_2"> <input type="text" value="'+this.totalWin+'"  onblur="'+this.objName+'.totalWin=String(this.value);'+this.objName+'.SetTotalSum(1);'+this.objName+'.ServerUpdate();"  name="press3"  maxlength="30" /></div></div>';
			if ( this.showPoints ) {
				// select peníze (měna) nebo body
				var pointTypes = '<select id="units1" name="units" onchange="document.getElementById(\'units2\').value = this.options[this.selectedIndex].text; '+this.objName+'.pointType = this.value;'+this.objName+'.SetTotalSum();'+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;" style="width:60px; height:27px">';

				pointTypes += '<option value="0">'+currency+'</option>';
				//TODO add dynamicly generated list of point types
				pointTypes += '<option value="1"' + (this.pointType >= 1 ? 'selected="1"' : '') + '>B</option>';
				pointTypes += '</select>';
			} else {
				// jen peníze (měna)
				var pointTypes = /*'<input id="units2" type="text" value="'+ currency +'" readonly="readonly" style="width:50px"/>'*/'';
			}

			if ((!this.showPoints || !(this.pointType >= 1)) && this.showRateAdvance ) {
				var rateAdvances = '<div><label for="rate_advance">'+this.words['okf36']+'</label>'
				rateAdvances += '<select class="smaller-select" name="rate_advance" id="rate_advance" maxlength="8" onchange="'+this.objName+'.rateAdvance = this.value;'+this.objName+'.SetTotalSum();'+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;">';
				rateAdvances += '<option value="1.00">---</option>';
				for ( size in this.preferenceSizes ) {
					var tmp = (100.0 + parseFloat(size))/100.0;
					rateAdvances += '<option value="'
							+ tmp + '"' 
							+ (this.rateAdvance == tmp ? ' selected="1"' : '') 
							+ (!this.preferenceSizes[size] ? ' disabled="disabled"' : '')
							+ '>' + 
							+ parseFloat(size) + '%</option>';
				}
				rateAdvances += '</select>';
						// solution for stable select#rate_advance position:
						/*+'<strong style="font-size: 1.8em; color: #f00" id="rateAdvanceEqual">';
				if ( this.rateAdvance > 1 ) {
					rateAdvances += '&nbsp;=&nbsp;</strong>';
					rateAdvances += '<div class="tp-boxik"><div class="cc"><strong>'+this.totalRate+'</strong></div></div></div>';
				} else {
					rateAdvances += '</strong></div>';
				}*/
				if (this.rateAdvance > 1 ) {
					rateAdvances += '<div><span>&nbsp;</span><span class="value">... = '+this.totalRate+'</span></div>';
				}
				
			} else {
				var rateAdvances = '';
			}

			if (this.showGetPoints ) {
				var getPoints = '<div style="margin-top:5px"><label for="get_points">'+this.words['getPoints']+'</label><input type="text" value="'+this.getPoints+'" name="press2" id="get_points" maxlength="8" readonly="1"/></div>';
			} else {
				var getPoints = '';
			}
			
			if (this.pointType >= 1 ) {
				// hodnota v měně - na 2 řádky
				var stakeInCurrency = '<div style="margin-top:5px"><label>'+this.words['stakeInCurrency']+':</label></div>';
				stakeInCurrency += '<div style="margin-top:5px">&nbsp;<input type="text" readonly="readonly" value="'+this.formatCurrency(this.totalBet)+'" name="press3" id="total_win" maxlength="8" onfocus="'+this.objName+'.onInputFocus(this);" onblur="if ('+this.objName+'.updatedAmountTotalWin(\'total_win\', this.value)) '+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;"/><input id="units2" type="text" value="'+ currency +'" readonly="readonly" /></div>';
			} else {
				var stakeInCurrency = '';
			}

		/*	ob1.obj.innerHTML = '<div class="betslip-summary">'
				+'<!-- počet sázek <div class="tp-pole"><label class="width4 s28">'+this.words['okf4']+'</label><div class="tp-boxik"><div class="cc ta-right">1</div></div></div>-->'
				+'<div class="tp-pole"><label class="width4 s28">'+this.words['okf7']+'</label><div class="tp-boxik"><div class="cc"><strong>'+this.totalRateWithoutAdvance+'</strong></div></div></div>'
				+ rateAdvances
				+'<div class="tp-pole"><label class="width4" for="multi_bet"><span class="nastavit-vklad">'+this.words['okf5']+ this.genFastStakes(2) + '</span></label><div class="tp-boxik"><div class="cc"><input type="text" class="txt width1 total_bet_2" value="' + (!(this.pointType >= 1) ? this.formatCurrency(this.totalBet) : this.formatPoints(this.totalBetInPoints,this.pointType,false))+'" name="press2" id="total_bet" maxlength="8" onfocus="'+this.objName+'.onInputFocus(this);" onblur="'+this.objName+'.bet_code=\'\'; if ('+this.objName+'.updatedAmountTotalBet(\'total_bet\', this.value)) '+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;"/>'+ pointTypes+'</div>'+this.genSpinner(2) +'</div></div>'
				+ getPoints
				+ stakeInCurrency
				+'<div class="tp-pole"><label class="va-middle" for="multi_win">'+this.words['okf6']+'</label><div class="tp-boxik"><div class="cc"><input type="text" class="txt width3d" value="'+this.formatCurrencyWin(this.totalWin)+'" name="press3" id="total_win" maxlength="8" onfocus="'+this.objName+'.onInputFocus(this);" onblur="if ('+this.objName+'.updatedAmountTotalWin(\'total_win\', this.value)) '+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;"/><input id="units2" type="text" value="'+ currency +'" readonly="readonly" class="txt width3e"/></div></div></div>'
				+'</div>';
		*/
			ob1.obj.innerHTML = '<div class="betslip-summary">'
				// celkový kurz
				+'<div><label>'+this.words['okf7']+'</label><span class="value">'+this.totalRateWithoutAdvance+'</span></div>'
				//
				+ rateAdvances
				
				// vklad
				+ '<div>' + this.words['okf5'] 
					+ '<input type="text" class="txt width1 total_bet_2" value="' + (!(this.pointType >= 1) ? this.formatCurrency(this.totalBet) : this.formatPoints(this.totalBetInPoints,this.pointType,false))+'" name="press2" id="total_bet" maxlength="8" onfocus="'+this.objName+'.onInputFocus(this);" onblur="'+this.objName+'.bet_code=\'\'; if ('+this.objName+'.updatedAmountTotalBet(\'total_bet\', this.value)) '+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;"/>'+ pointTypes
					//+this.genSpinner(2) // ne kvůli měnám
				+ '</div>'
				
				// naklikávání částek vkladu - zatím ne kvůli měnám
				//+ '<div class="center"><span class="nastavit-vklad">'+ this.genFastStakes(2) + '</span></div>'
				//<label for="multi_bet"><span class="nastavit-vklad">'+this.words['okf5']+ this.genFastStakes(2) + '</span></label>
				
				+ getPoints
				+ stakeInCurrency
				+'<div style="margin-top:5px"><strong style="font-size:18px">'+this.words['okf6']+'</strong><span style="float:right"><input type="text" value="'+this.formatCurrencyWin(this.totalWin)+'" name="press3" id="total_win" maxlength="8" onfocus="'+this.objName+'.onInputFocus(this);" onblur="if ('+this.objName+'.updatedAmountTotalWin(\'total_win\', this.value)) '+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;"/><input id="units2" type="text" value="'+ currency +'" readonly="readonly" style="width:50px" /></span></div>'
				+'</div>';
		}

		//ob2.obj.innerHTML = '<input type="button" value="'+this.words['okf8']+' &gt;&gt;" onclick="'+this.objName+'.SendData(this);" '+(this.betNum>0?'':'disabled="disabled"')+' />';
		//ob2.obj.innerHTML = '<p class="center"><input type="button" class="button" onclick="'+this.objName+'.SendData(this);return false;"  id="ticket_data_sender" value="'+this.words['okf8']+'" '+(this.betNum>0?'':'disabled="disabled"')+' /></p>';
		
		//old
		/*ob2.obj.innerHTML = '<div class="tp"><input type="button" class="sbm2" onclick="return false;" id="ticketRecalculate" value="'+this.words['okf39']+'" />'
			+ '<input type="button" class="sbm1" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.saveClick(); '+this.objName+'.SendData(this);return false;" id="ticket_data_sender" value="'+this.words['okf8']+'" '+(this.betNum>0?'':'disabled="disabled"')+' /></div>'
			//+ '<div class="tp"><input type="button" class="sbm2" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.ServerUpdate(false, true);return false;" id="coupon_save" value="'+this.words['okf41']+'" '+(this.betNum>0?'':'disabled="disabled"')+' /></div>'
			+ '<div class="tp"><input type="checkbox" id="mail_send" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.sendMailClicked();"'
			+ (this.mail == 0 ? '' : ' checked="checked"') + ' /> <label for="mail_send"><strong>'+this.words['okf30']+'</strong></label></div>';*/
		
		// smaž tiket, pokračovat
		ob2.obj.innerHTML = '<div class="betslip-buttons">' +
				'<a href="javascript:'+this.objName+'.DeleteAll();void(0);" title="'+this.words['act3']+'" onclick="if ('+this.objName+'.saveClick(event)) return false;">'+this.words['act3']+'</a>' +
				'<input type="button" class="btn pull-right" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.saveClick(); '+this.objName+'.SendData(this);return false;" id="ticket_data_sender" value="'+this.words['okf8']+'" '+(this.betNum>0?'':'disabled="disabled"')+' />' +
				'</div>'
				// zaslat výsledky na e-mail - zatím ne (nebylo v návrhu)
				/*+'<div class="text-center"><input type="checkbox" id="mail_send" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.sendMailClicked();"' +
				(this.mail == 0 ? '' : ' checked="checked"') + ' /> <label for="mail_send">'+this.words['okf30']+'</label></div>'*/;
		
		//+ '<div class="tp"><input type="checkbox" id="sms_send" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.sendSmsClicked();"'
		//+ (this.sms == 0 ? '' : ' checked="checked"') + ' /> <label for="sms_send"><strong>'+this.words['okf40']+'</strong></label></div>';
		
		ob1.style.display = 'block';
		ob2.style.display = 'block';
		ob4.style.display = 'none';
		ob5.style.display = 'block';
	}

	if (this.betNum != 0) {

		var dat = '';
		dat += '<div class="bet_sum_okf"><input type="text" style="display:none" id="bet_code"  name="bet_code"  maxlength="40" />';
		if (this.freeCode.length > 0)
			dat += '<p>';
		for (var cx = 0; cx < this.freeCode.length; cx++) {
			dat += '<a href="javascript:'+this.objName+'.bet_code=\''+this.freeCode[cx]['code']+'\';'+this.objName+'.MailImg('+cx+');'+this.objName+'.ServerUpdate();void(0);">'+this.words['okf28']+' - '+this.freeCode[cx]['amount']+' '+this.words['okf29']+'</a> &nbsp;&nbsp; <img src="/img/icons/ico-ok.gif" name="mailchimg" style="display:none" id="mailchimg_'+cx+'" class="img" alt="'+this.words['okf28']+' - '+this.freeCode[cx]['amount']+' '+this.words['okf29']+'" /><br />';
		}
		/*if(this.freeCode.length > 0) dat += '<p class="dashesTop" ><a class="help" href="#"  onmouseover="'+this.objName+'.okfBoxShow(this,\''+this.words['okf35']+'\',-252,0)" onmouseout="'+this.objName+'.okfBoxHide();">'+this.words['okf32']+'</a></p>';*/

		dat += '</div>';
		ob3.obj.innerHTML = dat;
		ob3.style.display = 'block';
		//ob4.style.display = 'block';

	}
	this.EventCapture();
};

TTCupon.prototype.MailImg = function(obr) {

	if($("#mailchimg_"+obr).css('display') == 'none') st = false;
	else st = true;

	$("img[name='mailchimg']").hide();

	if (!st) $("#mailchimg_"+obr).show();
	else {
		this.bet_code = '';
		$("#mailchimg_"+obr).hide();
		$('#bet_code').get(0).value= '';
	}
};

TTCupon.prototype.FreeBet = function(code,amount) {
	this.freeCode[this.freeCode.length] = {'code':code,'amount':amount};
};

TTCupon.prototype.showLoginForm = function(t){
	showLoginForm();
};

TTCupon.prototype.SendData = function(t){

	$('#ticket_content :input').attr('disabled', 'disabled');
	this.clearNotifications();

//	if ( !this.userLoggedIn ) {
//		this.showNotification(this.words['must-be-logged-in'], 'error');
//		this.showLoginForm();
//		return false;
//	}

/*	if ( (!this.showPoints || !this.pointType >= 1) && this.balance < this.totalBet ) {
		this.showNotification(this.words['out-of-money'], 'error');
		return false;
	}

	if ( this.showPoints && this.pointType >= 1 && this.pointBalance < this.totalBet ) {
		this.showNotification(this.words['out-of-points'], 'error');
		return false;
	}

	if ( (!this.showPoints || !this.pointType >= 1)
			&& this.rateAdvanceCost > this.pointBalance ) {
		this.showNotification(this.words['rate-advance-out-of-points'], 'error');
		return false;
	}
*/
	var ob3 = new getObj('bet_code');

	if (this.betNum <= 0) {
		$('#ticket_content :input').attr('disabled', '');
		this.showNotification(this.words['okf9'], 'error');
		return false;
	}

	var ob1 = new getObj('ghtr6');
	var ob2 = new getObj('bet_ticket_form');
	// WTF?
	//if (typeof bid != 'undefined') { data_action=ob2.obj.action.toString(); xx = data_action.replace(/e=\d+/,'e='+bid); ob2.obj.action = xx; }

	var zero = false;
	var invalid = false;
	if(this.block){
		ob1.obj.innerHTML = ob1.obj.innerHTML+'<input type="hidden" name="block" value="1" />';
		ob2.obj.submit();
	}
	else if (0 == this.page){
		ob1.obj.innerHTML = ob1.obj.innerHTML+'<input type="hidden" name="type" value="simple" />';
		for(var idBet in this.bet){
			for(var idCol in this.bet[idBet]){
				ob1.obj.innerHTML = ob1.obj.innerHTML+'<input type="hidden" name="bet['+idBet+']['+idCol+']" value="'+this.bet[idBet][idCol]['amount']+'" />';
				var a = parseFloat(this.bet[idBet][idCol]['amount']);
				if (0 == a) // no freebets implemented
					zero = true;
				else if (0 > a)
					invalid = true;
			}
		}
	}
	else if (1 == this.page){
		ob1.obj.innerHTML = ob1.obj.innerHTML+'<input type="hidden" name="type" value="kombi" />';
		ob1.obj.innerHTML = ob1.obj.innerHTML+'<input type="hidden" name="total_sum" value="'+this.totalBet+'" />';
		for (var idBet in this.bet) {
			for (var idCol in this.bet[idBet]) {
				ob1.obj.innerHTML = ob1.obj.innerHTML+'<input type="hidden" name="bet['+idBet+']['+idCol+']" value="1" />';
			}
		}
		if (this.isMaxicombinator()) {
			for (var i = 0; i < this.combinations.length; ++i) {
				if (this.combinations[i].used) {
					var a = parseFloat(this.combinations[i].stake);
					if (0 == a) // no freebets implemented
						zero = true;
					else if (0 > a)
						invalid = true;
				}
			}
		}
		else {
			var a = parseFloat(this.totalBet);
			if (0 == a) // no freebets implemented
				zero = true;
			else if (0 > a)
				invalid = true;
		}
	}

	var error = (zero || invalid);
	if(zero)
		this.showNotification(this.words['okf11'], 'error');
	if (invalid)
		this.showNotification(this.words['okf37'], 'error');
	if (!error) {
		//$('#ticket_data_sender').attr('value', this.words['okf10']); // button value
		$('#ticket_data_sender').text(this.words['okf10']); // změna na <a> podle nové šablony
		confirmTicket(this.getCouponData(true), 1);
	}
	else
		$('#ticket_content :input').attr('disabled', '');

	return false;
};

TTCupon.prototype.SetTotalSum = function(pr){
/*
   this.totalRate = 1;

   if(this.page == 0){

    this.totalBet = 0.00;
	this.totalWin = 0.00;

	 for(var vl in this.bet){

	  for(var vl2 in this.bet[vl]){

	   if(this.bet[vl][vl2]['visible'] == 1){
         this.totalBet = parseFloat(this.totalBet)+parseFloat(this.bet[vl][vl2]['amount']);
         this.totalWin = parseFloat(this.totalWin) + (parseFloat(this.bet[vl][vl2]['amount'])*parseFloat(this.bet[vl][vl2]['rate']));
	   }

      }

	 }

	 this.totalBet = this.totalBet.toFixed(2);
     this.totalWin = this.totalWin.toFixed(2);

	}
	else if(this.page == 1){
		var groupRates = new Array();
		for (var vl in this.bet) {
			for (var vl2 in this.bet[vl]) {
				if (this.bet[vl][vl2]['visible'] == 1) {
					var rate = parseFloat(this.bet[vl][vl2]['rate']);
					this.totalRate = parseFloat(this.totalRate) * rate;
					var group = this.bet[vl][vl2]['group'];
					if (undefined === groupRates[group])
						groupRates[group] = rate;
					else
						groupRates[group] *= rate;
				}
			}
		}

		this.totalRate = this.totalRate;
		if (this.betNum == 0) this.totalRate = '0.00';

		this.totalRateWithoutAdvance = parseFloat(this.totalRate).toFixed(2);
		if ( (!this.showPoints || !(this.pointType >= 1)) && this.showRateAdvance && this.rateAdvance > 1 ) {
			this.totalRate *= parseFloat(this.rateAdvance);

		}
 		this.totalBet = parseFloat(this.totalBet);

		if (this.isMaxicombinator()) {
			this.totalBet = this.totalBet.toFixed(2);
			this.totalWin = 0.0;
			var groups = new Array();
			for (var i = 0; i < this.getGroupsCount(false); ++i)
				groups.push(i + 1);
			for (var i = 0; i < this.combinations.length; ++i) {
				//TODO: recalculate stakes from totalBet!
				if (false !== this.combinations[i].used) {
					var cs = this.getCombinations(this.getGroupsCount(false), i + 1, groups);
					for (var c = 0; c < cs.length; ++c) {
						var rate = 1.0;
						for (var j = 0; j < cs[c].length; ++j)
							rate *= groupRates[ cs[c][j] ];
						if (this.hasGroupT)
							rate *= groupRates[0];
						this.totalWin += this.combinations[i].stake * rate;
					}
				}
			}
		}
		else {
			if(pr == 1) {
				this.totalWin = parseFloat(this.totalWin).toFixed(2);
				this.totalBet = (this.totalWin/this.totalRate);
				this.totalBet = this.totalBet.toFixed(2);
				this.totalWin = (this.totalBet*this.totalRate).toFixed(2);
			}
			else {
				this.totalBet = this.totalBet.toFixed(2);
				this.totalWin = (this.totalBet*this.totalRate).toFixed(2);
			}
		}
	}
*/
	//this.totalRate = 4;
	//this.totalWin = 3;
	//this.ServerUpdate();
	//this.SetSum();

	if ( pr == 1 ) {
		this.totalBet = null;
	}

};

TTCupon.prototype.SetSimpleSum = function(val){
	var a = this.updatedAmount('bet_simple_sum_all', val, this.simpleSum);
	if (false !== a) {
		this.simpleSum = a;
		for(var vl in this.bet){
			for(var vl2 in this.bet[vl])
				this.bet[vl][vl2]['amount'] = a;
		}
		this.SetBet();
		this.SetTotalSum();
		return true;
	}
	return false;
};

TTCupon.prototype.PreapprovedTicketUpdate = function(couponId, json){
	var data = null;
	this.confirmationEnd();
	try {
		if (typeof(json) == 'string' || (typeof(json) == 'object' && json.constructor == 'String'))
			data = JSON.parse(json);
		else
			data = json;
	}
	catch (e) {
		//TODO: indicate error don't clear timeout
		return;
	}

	this.couponId = couponId;
	if (0 == this.page) {
		this.simpleSum = 0.0;
		for (var i in data.bets) {
			var b = data.bets[i];
			this.bet[ b.id ][ b.column ]['amount'] = parseFloat(b.stake);
		}
		this.totalBet = parseFloat(data.stake);
		this.totalWin = parseFloat(data.win);
		this.SetBet();
	}
	else if (1 == this.page) {
		if (this.isMaxicombinator()) {
			for (var i = 0; i < this.combinations.length; ++i) {
				if (undefined !== data.combinations[i])
					this.combinations[i].stake = parseFloat(data.combinations[i].stake);
			}
			this.totalBet = parseFloat(data.stake);
			this.totalWin = parseFloat(data.win);
		}
		else {
			this.totalBet = parseFloat(data.stake);
			if ( this.pointType >= 1 ) {
				this.totalBetInPoints = parseFloat(data.stakeInPoints); 
			}
			this.totalWin = parseFloat(data.win);
			this.pointType = data.pointType;
			this.rateAdvance = data.rateAdvance > 1 ? data.rateAdvance : null;
		}
	}
	this.preapproved = true;
	this.SetSum();
};

TTCupon.prototype.confirmationStart = function(couponId, running){
	this.preapproved = false;
	if (undefined !== couponId)
		this.couponId = couponId;
	if (undefined === running)
		running = false;
	if(undefined === this.checkProveInterval) {
		this.confirmationRuns = (running ? 1 : 0);
		this.checkProveInterval = window.setInterval('confirmTicket2()',5000);
		confirmTicket2();
	}
};

TTCupon.prototype.confirmationEnd = function() {
	if(undefined !== this.checkProveInterval) {
		window.clearInterval(this.checkProveInterval);
		this.checkProveInterval = undefined;
	}
	//this.couponId = 0;
};

  TTCupon.Watermark = function(){

   var ne=document.layers;
   var ie=(document.all || document.getElementById);
   var op = (navigator.userAgent.indexOf(" Opera ")>0?true:false);

   var ob = new getObj('box_kupon');

   if(ne || op){
	  CH=window.innerHeight;
	  ST=window.pageYOffset;
	}
	else if(ie){
	 if(document.documentElement && document.documentElement.clientHeight) doc = document.documentElement;else doc = document.body;
	  CH=doc.clientHeight;
	  ST=doc.scrollTop;
	}

     /*if( isNaN(parseInt(ob.style.top)) ) ob.style.top = 0+'px';*/

	/* if((ST%4) == 1) ST = ST+3;
	 else if((ST%4) == 2) ST = ST+2;
	 else if((ST%4) == 3) ST = ST+1;

	 if(parseInt(ob.style.top) < ST && (ST - 4) <= parseInt(ob.style.top)) ST = parseInt(ob.style.top);
	 if(parseInt(ob.style.top) > ST && (ST + 4) >= parseInt(ob.style.top)) ST = parseInt(ob.style.top);

	 if(parseInt(ob.style.top) == ST);
	 else if(parseInt(ob.style.top) > ST) ob.style.top = (parseInt(ob.style.top)-4)+'px';
     else if(parseInt(ob.style.top) < ST) ob.style.top = (parseInt(ob.style.top)+4)+'px';
	 */

	 var nec = (parseInt($("#box_kupon").css('top'))<=0?0:214);

	 ST = ST-270;
	 if(ST<0) ST = 0;

	  $("#box_kupon").show().animate({
                                        top: (ST)+'px'
                                        }, 750 );

	 if(parseInt($("#box_kupon").css('top'))<=0) $("#box_kupon").css('top','0px');

  };

TTCupon.prototype.Lock = function(){

	var img = new getObj('ticket_lock');
	var ob = new getObj('ticket_container');

	$('#box_kupon').css('z-index',2000);

	if(this.lock == true){

	  img.obj.src = '/images/tp-ico-unlock.png';
	  $('#box_kupon').css('position','relative');
	  $('#box_kupon').css('top','0px');


	  this.lock = false;

	  this.lockInterval = setInterval('TTCupon.Watermark()',1000);

	}else{

	  img.obj.src = '/images/tp-ico-lock.png';
	  $('#box_kupon').css('position','static');
	  this.lock = true;

	  clearInterval(this.lockInterval);

	}

};

TTCupon.prototype.alternateCss = function(jObj, cssProperty, altValue, times, interval) {
	var origValue = jObj.css(cssProperty);
	var n = 0;
	var id = window.setInterval(function() {
			if (n++ >= times) {
				window.clearInterval(id);
				return;
			}
			var value = (n % 2 ? altValue : origValue);
			jObj.css(cssProperty, value);
		},
		interval
	);
};

TTCupon.prototype.updatedAmount = function(stakeElemId, amount, prevAmount, type) {
	var a = (new Number(amount)).valueOf();
	if (!isNaN(a)) {
		a = a.toFixed('win' == type ? precisionWin : precisionStake);
		$('#'+stakeElemId).val('win' == type ? this.formatCurrencyWin(a) : this.formatCurrency(a));
		return a;
	}
	else {
		this.alternateCss($('#'+stakeElemId), 'background-color', 'red', 6, 300);
		if (undefined !== prevAmount)
			$('#'+stakeElemId).val('win' == type ? this.formatCurrencyWin(prevAmount) : this.formatCurrency(prevAmount));
		return false;
	}
};

TTCupon.prototype.updatedAmountSimpleBet = function(stakeElemId, winElemId, amount, betId, colId) {
	var a = this.updatedAmount(stakeElemId, amount, this.bet[betId][colId]['amount']);
	if (false !== a) {
		this.bet[betId][colId]['amount'] = a;
		$('#'+winElemId).val( this.formatCurrencyWin(a * this.bet[betId][colId]['rate']) );
		this.SetTotalSum();
		var all = $('#bet_simple_sum_all');
		if (a != new Number(all.val()))
			all.val('');
		return true;
	}
	return false;
};

TTCupon.prototype.updatedWinSimpleBet = function(winElemId, stakeElemId, win, betId, colId) {
	var w = this.updatedAmount(winElemId, win, this.bet[betId][colId]['rate'] * this.bet[betId][colId]['amount']);
	if (false !== w) {
		var a = w / this.bet[betId][colId]['rate'];
		return this.updatedAmountSimpleBet(stakeElemId, winElemId, a, betId, colId);
	}
	return false;
};

TTCupon.prototype.updatedAmountTotalBet = function(stakeElemId, amount) {
	if ( this.pointType >= 1 )
		var a = this.updatedAmount(stakeElemId, amount, this.totalBetInPoints);
	else
		var a = this.updatedAmount(stakeElemId, amount, this.totalBet);
		
	if (false !== a && a != this.totalBet) {
		//$('#'+winElemId).val( this.formatCurrency(a * this.totalRate) );
		//if ( this.pointType >= 1 ) {
			this.totalBetInPoints = a;
			this.totalBet  = a;
		//}
		//else {
		//	this.totalBet = a;
		//}
			
		this.SetTotalSum();
		return true;
	}
	return false;
};

TTCupon.prototype.updatedAmountTotalWin = function(winElemId, amount) {
	var a = this.updatedAmount(winElemId, amount, this.totalWin, 'win');
	if (false !== a && a != this.totalWin) {
		//$('#'+winElemId).val( this.formatCurrencyWin(a * this.totalRate) );
		this.totalWin = a;
		this.SetTotalSum(1);
		return true;
	}
	return false;
};

TTCupon.prototype.rateChanged = function(betId, columnId, newRate) {
	var r = parseFloat(newRate).toFixed(2);
	this.bet[betId][columnId]['rate'] = r;
	var tdId = 'td#b' + betId + 'c' + columnId;
	$(tdId + ' a.kurz, ' + tdId + '_lastminute a.kurz, ' + tdId + '_terno a.kurz, ' + tdId + '_bookmakertip a, ' + tdId + '_supertip a.kurz').text(r);
};

TTCupon.prototype.sendMailClicked = function() {
	this.mail = ($('#mail_send').is(':checked') ? 1 : 0);
	this.ServerUpdate();
};

TTCupon.prototype.sendSmsClicked = function() {
	this.sms = ($('#sms_send').is(':checked') ? 1 : 0);
	this.ServerUpdate();
}

TTCupon.prototype.setSendMail = function(send) {
	if (undefined === send)
		send = 1;
	this.mail = (send ? 1 : 0);
	$('#mail_send').checked = (send ? true : false);
};

TTCupon.prototype.setSendSms = function(send) {
	if (undefined === send)
		send = 1;
	this.sms = (send ? 1 : 0);
	$('#sms_send').checked = (send ? true : false);
};

TTCupon.prototype.SetBet = function(){
	var ob1 = new getObj('ghtr2');
	var ob2 = new getObj('ghtr3');
	var bets = '';

	$('#ghtr3').hide();
	$('#ghtr4').hide();

	if (undefined === this.betOrder)
		this.orderBets(false);

	if(this.betNum == 0){
		$('#ghtr3').show();
		ob1.obj.innerHTML = '';
		ob2.obj.innerHTML = '<p class="msg_warn">'+this.words['okf1']+'</p>';
	}
	else if(this.page == 0) {
		var i = 1;
		for (var j = 0; j < this.betOrder.length; ++j) {
			var vl = this.betOrder[j]['betId'];
			var vl2 = this.betOrder[j]['colId'];
			
				// šablona pro porovnání
				//bets += '<li> <input value="150" type="text"> <a href="#" class="updown betslip-up">+</a> <a href="#" class="updown betslip-down">-</a> <div class="bet-item">Dundee United – St Johnstone <span>8.50</span></div> <div class="bet-info">Match: 1<br>Win: 1255 Eur</div> <button type="button" class="close" data-dismiss="alert" title="Delete"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button> </li>';
				bets += '<li id="bet_item_' + vl + '_' + vl2 + '">';
				
				// šipečky pro vklad +/- zatím neřešit
				// pokud by se to řešilo, tak bylo to dohodnuto takto:
				// pro CZK: 0-99: +-10; 100-999 +-50; 1000-1999 +-200; 2000 a víc +-500
				//bets += '<a href="#" class="updown betslip-up">+</a><a href="#" class="updown betslip-down">-</a>';
				// vklad, výhra                         - style níže dát pryč v případě šipeček pro vklad
				bets += '<input type="text" maxlength="8" style="margin-right:-10px;margin-top:10px;"'
						+ ' onfocus="'+this.objName+'.onInputFocus(this);"'
						+ ' onblur="if('+this.objName+'.updatedAmountSimpleBet(\'textfield'+i+'\',\'betw'+i+'\',this.value,'+vl+','+vl2+')) '
						+ this.objName+'.ServerUpdate();" name="press4" value="'+this.formatCurrency(this.bet[vl][vl2]['amount'])
						+ '" onclick="if ('+this.objName+'.saveClick(event)) return false;" id="textfield'+i+'" name="textfield'+i+'"/>';
				
				// input na nastavení vkladu podle výhry:
				/*bets += '<input onchange="alert(\'changed\')" class="txt width1 txt-label strong" type="text" id="betw'+i
						+ '" onfocus="'+this.objName+'.onInputFocus(this);"'
						+ ' onblur="if('+this.objName+'.updatedWinSimpleBet(\'betw'+i+'\',\'textfield'+i+'\',this.value,'+vl+','+vl2+')) '
						+this.objName+'.ServerUpdate();" onclick="if ('+this.objName+'.saveClick(event)) return false;"'
						+ ' value="'+this.formatRate(this.bet[vl][vl2]['amount']*this.bet[vl][vl2]['rate'])+'" />';*/
				// název + kurz
				bets += '<div class="bet-item">'+ this.bet[vl][vl2]['text']+' '+(this.bet[vl][vl2]['live']==1?'<small>[LIVE!]</small>':'')+' <span>' + this.bet[vl][vl2]['rate'] + '</span></div>';
				// typ sázky, výhra
				bets += '<div class="bet-info">'+this.bet[vl][vl2]['type']+': '+this.bet[vl][vl2]['bet']+'<br>' + this.words['okf06'] + ': <span class="match-win-amount">'+this.formatRate(this.bet[vl][vl2]['amount']*this.bet[vl][vl2]['rate'])+'</span></div>';
				// zrušit zápas v sázce
				bets += '<a class="close" href="javascript: '+this.objName+'.ChangeBet('+vl+','+vl2+');void(0);" onclick="if ('+this.objName+'.saveClick(event)) return false;"><span aria-hidden="true">×</span><span class="sr-only">Close</span></a>';
				bets += '</li>';
				
				++i;
		//	}
		//}
		}

		//var switches = '<div class="tp tp-help"><div class="left"><a href="javascript:'+this.objName+'.DeleteAll();void(0);" title="'+this.words['act3']+'" onclick="if ('+this.objName+'.saveClick(event)) return false;"><img alt="'+this.words['act3']+'" src="/images/tp-ico-delete.png"/></a></div><div class="right"></div></div>';
		ob1.obj.innerHTML = bets/*+switches*/; // v nových šablonách řešeno jinde
		ob2.obj.innerHTML = '';
	}
	else if (this.page == 1) {
		/*
		for(var vl in this.bet) {
			for(var vl2 in this.bet[vl]){
				var banker = '';
				if(this.activeBanker == 1 && this.activeSystem == 1) {
					//if(this.bet[vl][vl2]['banker'] == 1) banker = '<a href="javascript:'+this.objName+'.SetBanker('+vl+','+vl2+',0);'+this.objName+'.ServerUpdate();void(0);" class="floatleft"><img src="_clip/B_active.gif" alt="'+this.words['okf19']+'" class="img"></a>';
					//else banker = '<a href="javascript:'+this.objName+'.SetBanker('+vl+','+vl2+',1);'+this.objName+'.ServerUpdate();void(0);" class="floatleft"><img src="_clip/B_unactive.gif" alt="'+this.words['okf18']+'" class="img"></a>';
					if(this.bet[vl][vl2]['banker'] == 1) banker = '<a href="javascript:'+this.objName+'.SetBanker('+vl+','+vl2+',0);'+this.objName+'.ServerUpdate();void(0);" ><img src="/img/icons/ico-b.gif" alt="'+this.words['okf19']+'" class="img"></a>';
					else banker = '<a href="javascript:'+this.objName+'.SetBanker('+vl+','+vl2+',1);'+this.objName+'.ServerUpdate();void(0);" ><img src="/img/ico-b_negativ.gif" alt="'+this.words['okf18']+'" class="img"></a>';
				}

				//bets += '<div class="bet_okf"><div class="bet_okf_1"><input type="checkbox" class="no" onclick="'+this.objName+'.SetBanker('+vl+','+vl2+',0);'+this.objName+'.bet['+vl+']['+vl2+'][\'visible\']=(this.checked?1:0);'+this.objName+'.UpdateAll();" '+(this.bet[vl][vl2]['visible']==1?'checked="checked"':'')+' /> </div><div class="bet_okf_2"><strong>'+this.bet[vl][vl2]['text']+' '+(this.bet[vl][vl2]['live']==1?'<small>[!LIVE]</small>':'')+'</strong> <br />'+this.bet[vl][vl2]['type']+': '+this.bet[vl][vl2]['bet']+'</div><div class="bet_okf_3">'+banker+' '+this.bet[vl][vl2]['rate']+'  <a href="javascript:'+this.objName+'.SetBanker('+vl+','+vl2+',0);'+this.objName+'.ChangeBet('+vl+','+vl2+');void(0);"><img src="_clip/bdelete.gif" alt="'+this.words['okf14']+'" class="img"></a></div></div>';
				bets += '<tr><td class="first"><input type="checkbox" class="checkbox" onclick="'+this.objName+'.SetBanker('+vl+','+vl2+',0);'+this.objName+'.bet['+vl+']['+vl2+'][\'visible\']=(this.checked?1:0);'+this.objName+'.UpdateAll();" '+(this.bet[vl][vl2]['visible']==1?'checked="checked"':'')+' value="" name="input2"/></td><td><strong style="font-size:0.9em;">'+this.bet[vl][vl2]['text']+' '+(this.bet[vl][vl2]['live']==1?'<small>[!LIVE]</small>':'')+'</strong><br/></td><td rowspan="2"><span class="right">'+banker+'</span></td><td class="right" style="font-size:0.9em;">'+this.bet[vl][vl2]['rate']+'</td><td class="right"><a href="javascript:'+this.objName+'.SetBanker('+vl+','+vl2+',0);'+this.objName+'.ChangeBet('+vl+','+vl2+');void(0);"><img class="pointer" alt=" " src="/img/icons/ico-closeTTCupon.gif"/></a></td></tr><tr><td class="first"></td><td style="font-size:0.9em">'+this.bet[vl][vl2]['type']+': '+this.bet[vl][vl2]['bet']+'</td><td class="right"></td><td class="right"></td></tr><tr><td colspan="5" class="sep">&nbsp;</td></tr>';
			}
		}

		var switches = '<tr><td class="tickActions" colspan="4"><a href="javascript:'+this.objName+'.CheckAll();void(0);" title="'+this.words['act1']+'"><img  src="/img/sportBookTicket-but-1.gif" alt="'+this.words['act1']+'" /></a><a href="javascript:'+this.objName+'.UnCheckAll();void(0);" title="'+this.words['act2']+'"><img alt="'+this.words['act2']+'" src="/img/sportBookTicket-but-2.gif"/></a><a href="javascript:'+this.objName+'.DeleteAll();void(0);" title="'+this.words['act3']+'"><img alt="'+this.words['act3']+'" src="/img/sportBookTicket-but-3.gif"/></a></td> <td class="right tickActions"><a href="javascript:'+this.objName+'.Lock();void(0);" title="'+this.words['act4']+'" style="float:right"><img height="18" width="18" alt="'+this.words['act4']+'" id="ticket_lock" src="/img/sportBookTicket-but-4.gif"/></a></td></tr>';
		ob1.obj.innerHTML = bets+switches+'</tbody></table>';
		ob2.obj.innerHTML = '';
		*/
		var i = 1;
		for (var j = 0; j < this.betOrder.length; ++j) {
			var vl = this.betOrder[j]['betId'];
			var vl2 = this.betOrder[j]['colId'];
				/*
					bets += '<tr><td class="first"><input type="checkbox" class="checkbox" onclick="'+this.objName+'.SetBanker('+vl+','+vl2+',0);'+this.objName+'.bet['+vl+']['+vl2+'][\'visible\']=(this.checked?1:0);'+this.objName+'.UpdateAll();" '+(this.bet[vl][vl2]['visible']==1?'checked="checked"':'')+' value="" name="input2"/></td>'
					+'<td><strong style="font-size:0.9em;">'+this.bet[vl][vl2]['text']+' '+(this.bet[vl][vl2]['live']==1?'<small>[!LIVE]</small>':'')+'</strong><br/></td>'
					+'<td rowspan="2"><span class="right">'+banker+'</span></td>'
					+'<td class="right" style="font-size:0.9em;">'+this.bet[vl][vl2]['rate']+'</td>'
					+'<td class="right"><a href="javascript:'+this.objName+'.SetBanker('+vl+','+vl2+',0);'+this.objName+'.ChangeBet('+vl+','+vl2+');void(0);">'
					+'<img class="pointer" alt=" " src="/img/icons/ico-closeTTCupon.gif"/></a></td></tr>'
					+'<tr><td class="first"></td><td style="font-size:0.9em">'+this.bet[vl][vl2]['type']+': '+this.bet[vl][vl2]['bet']+'</td>'
					+'<td class="right"></td><td class="right"></td></tr><tr><td colspan="5" class="sep">&nbsp;</td></tr>';
					*/
				//bets += '<div class="bet_okf"><div class="bet_okf_1"><input type="checkbox" class="no" onclick="'+this.objName+'.bet['+vl+']['+vl2+'][\'visible\']=(this.checked?1:0);(this.checked?0:('+this.objName+'.bet['+vl+']['+vl2+'][\'banker\']==1?'+this.objName+'.systemNumBanker--:0));'+this.objName+'.UpdateAll();" '+(this.bet[vl][vl2]['visible']==1?'checked="checked"':'')+' /> </div><div class="bet_okf_2"><strong>'+this.bet[vl][vl2]['text']+' '+(this.bet[vl][vl2]['live']==1?'<small>[!LIVE]</small>':'')+'</strong> <br />'+this.bet[vl][vl2]['type']+': '+this.bet[vl][vl2]['bet']+'</div><div class="bet_okf_3">'+this.bet[vl][vl2]['rate']+' <a href="javascript:'+this.objName+'.ChangeBet('+vl+','+vl2+');void(0);"><img src="_clip/bdelete.gif" alt="'+this.words['okf14']+'" class="img"></a> <br /> <input type="text" maxlength="8" class="simple_bet_input" onkeyup="'+this.objName+'.bet['+vl+']['+vl2+'][\'amount\']=String(this.value);'+this.objName+'.SetTotalSum();" onblur="'+this.objName+'.ServerUpdate();" name="press4" value="'+this.bet[vl][vl2]['amount']+'" /> </div></div>';
				//bets += '<tr><td class="first" valign="top"><input type="checkbox" class="checkbox" onclick="'+this.objName+'.bet['+vl+']['+vl2+'][\'visible\']=(this.checked?1:0);(this.checked?0:('+this.objName+'.bet['+vl+']['+vl2+'][\'banker\']==1?'+this.objName+'.systemNumBanker--:0));'+this.objName+'.UpdateAll();" '+(this.bet[vl][vl2]['visible']==1?'checked="checked"':'')+' name="input"/></td><td><strong style="font-size:0.9em;">'+this.bet[vl][vl2]['text']+' '+(this.bet[vl][vl2]['live']==1?'<small>[!LIVE]</small>':'')+'</strong><br/></td><td class="right" style="font-size:0.9em;">'+this.bet[vl][vl2]['rate']+'</td><td class="right"><a href="javascript:'+this.objName+'.ChangeBet('+vl+','+vl2+');void(0);"><img class="pointer" alt="'+this.words['okf14']+'" src="/img/icons/ico-closeTTCupon.gif"/></a></td></tr><tr><td class="first"></td><td colspan="2" style="font-size:0.9em;">'+this.bet[vl][vl2]['type']+': '+this.bet[vl][vl2]['bet']+'</td><td class="right" ><input type="text" maxlength="8" class="field w30"  onkeyup="'+this.objName+'.bet['+vl+']['+vl2+'][\'amount\']=String(this.value);'+this.objName+'.SetTotalSum();" onblur="'+this.objName+'.ServerUpdate();" name="press4" value="'+this.bet[vl][vl2]['amount']+'" id="textfield" name="textfield"/></td></tr><tr><td colspan="4" class="sep">&nbsp;</td></tr>';
				
				bets += '<li id="bet_item_' + vl + '_' + vl2 + '">';
				// název + kurz
				bets += '<div class="bet-item">'+ this.bet[vl][vl2]['text']+' '
						+(this.bet[vl][vl2]['live']==1?'<small>[LIVE!]</small>':'')+' <span>' 
						+ this.bet[vl][vl2]['rate'] + '</span></div>';
				// typ sázky, skupina
				bets += '<div class="bet-info">'+this.bet[vl][vl2]['type']+': '+this.bet[vl][vl2]['bet']+' <span class="right"><select class="smaller-select" id="betgroup' + i + '" name="bgselect" onclick="if ('+this.objName+'.saveClick(event)) return false;" onchange="'+this.objName+'.updateBetGroup(this,'+vl+','+vl2+');'+this.objName+'.UpdateAll();">'+this.getAllGroupOptions(this.bet[vl][vl2]['group'])+'</select></span></div>';
				// zrušit
				bets += '<a class="close" href="javascript: '+this.objName+'.ChangeBet('+vl+','+vl2+');void(0);" onclick="if ('+this.objName+'.saveClick(event)) return false;"><span aria-hidden="true">×</span><span class="sr-only">'+this.words['okf14']+'</span></a>';
				bets += '</li>';
			++i;
		}

		//var switches = '<a href="javascript:'+this.objName+'.Lock();void(0);" title="'+this.words['act4']+'" style="float:right"><img src="_clip/unlock.gif" id="ticket_lock" alt="'+this.words['act4']+'" class="img" /></a><a href="javascript:'+this.objName+'.CheckAll();void(0);" title="'+this.words['act1']+'"><img src="_clip/bcheck_all.gif" alt="'+this.words['act1']+'" class="img" /></a> <a href="javascript:'+this.objName+'.UnCheckAll();void(0);" title="'+this.words['act2']+'"><img src="_clip/buncheck_all.gif" alt="'+this.words['act2']+'" class="img" /></a> <a href="javascript:'+this.objName+'.DeleteAll();void(0);" title="'+this.words['act3']+'"><img src="_clip/bdelete_all.gif" alt="'+this.words['act3']+'" class="img" /></a> ';
		//var switches = '<tr><td class="tickActions" colspan="3"><a href="javascript:'+this.objName+'.CheckAll();void(0);" title="'+this.words['act1']+'"><img  src="/img/sportBookTicket-but-1.gif" alt="'+this.words['act1']+'" /></a><a href="javascript:'+this.objName+'.UnCheckAll();void(0);" title="'+this.words['act2']+'"><img alt="'+this.words['act2']+'" src="/img/sportBookTicket-but-2.gif"/></a><a href="javascript:'+this.objName+'.DeleteAll();void(0);" title="'+this.words['act3']+'"><img alt="'+this.words['act3']+'" src="/img/sportBookTicket-but-3.gif"/></a></td> <td class="right tickActions"><a href="javascript:'+this.objName+'.Lock();void(0);" title="'+this.words['act4']+'" style="float:right"><img height="18" width="18" alt="'+this.words['act4']+'" id="ticket_lock" src="/img/sportBookTicket-but-4.gif"/></a></td></tr>';
		//ob1.obj.innerHTML = bets+switches+'</tbody></table>';
		//ob2.obj.innerHTML = '';
		//var switches = '<div class="tp tp-help"><div class="left"><a href="javascript:'+this.objName+'.DeleteAll();void(0);" title="'+this.words['act3']+'"><img alt="'+this.words['act3']+'" src="/images/tp-ico-delete.png"/></a></div><div class="right"><a href="javascript:'+this.objName+'.Lock();void(0);" title="'+this.words['act4']+'"><img height="18" width="18" alt="'+this.words['act4']+'" id="ticket_lock" src="/images/tp-ico-lock.png"/></a></div></div>';
		
		
		// smaž tiket, pokračovat
		var betSlipButtons = '<div class="betslip-buttons">';
		
		if(this.betNum>0){
			// Place bet button
			betSlipButtons += '<a class="btn pull-right" href="#" onclick="if ('+this.objName+'.saveClick(event)) return false; '+this.objName+'.SendData(this);return false;" id="ticket_data_sender">' + this.words['okf8'] + '</a>';
		}
		// remove all onclick
		betSlipButtons += '<a href="javascript:'+this.objName+'.DeleteAll();void(0);" onclick="if ('+this.objName+'.saveClick(event)) return false;">' + this.words['act3'] + '</a>' +
							'</div>';
		ob2.obj.innerHTML = betSlipButtons;
		
		/*var switches = '<div class="tp tp-help"><div class="left"><a href="javascript:'+this.objName+'.DeleteAll();void(0);" title="'+this.words['act3']+'" onclick="if ('+this.objName+'.saveClick(event)) return false;"><img alt="'+this.words['act3']+'" src="/images/tp-ico-delete.png"/></a></div><div class="right"></div></div>';*/
		//TODO: freebet

		ob1.obj.innerHTML = bets/*+switches*/; // smaž tiket přesunuto níže
		ob2.obj.innerHTML = '';
	}
};

TTCupon.prototype.updateBetGroup = function(obj, betId, colId) {
	var prevHasGroupT = this.hasGroupT;
	var prevGroupsCount = this.groupsCount;
	this.bet[betId][colId]['group'] = obj.value;
	this.updateGroupCounts();
	this.updateCombinations(prevGroupsCount, prevHasGroupT);
	this.orderBets();
};

TTCupon.prototype.getGroupsCount = function(includeGroupT) {
	if (undefined === includeGroupT)
		includeGroupT = true;
	return this.groupsCount - (this.hasGroupT && !includeGroupT  ? 1 : 0);
};

TTCupon.prototype.isMaxicombinator = function() {
	return (this.page == 1 && this.getGroupsCount(false) > 1);
};

TTCupon.prototype.orderBets = function(updateDom) {
	if (undefined === updateDom)
		updateDom = true;

	var fnSort = function(a, b) {
		if (a['group'] == b['group']) {
			if (a['couponTime'] == b['couponTime'])
				return 0;
			else
				return (a['couponTime'] < b['couponTime'] ? -1 : 1);
		}
		else
			return a['group'] - b['group'];
	} 
	var fnGroup = (
		1 < this.getGroupsCount(true)
		? function(b) { return b['group']; }
		: function(b) { return 1; }
	);
	this.betOrder = new Array();
	for (var betId in this.bet) {
		for (var colId in this.bet[betId]) {
			var bet = this.bet[betId][colId];
			this.betOrder.push(
				{'betId': betId, 'colId': colId, 'group': fnGroup(bet), 'couponTime': bet['couponTime']}
			);
		}
	}
	this.betOrder.sort(fnSort);

	if (updateDom)
		this.SetBet()
};

TTCupon.prototype.updateGroupCounts = function() {
	var groups = new Array();
	groups[0] = 0;
	var ordered = new Array();
	ordered.push(0);
	for (var betId in this.bet) {
		for (var colId in this.bet[betId]) {
			var group = this.bet[betId][colId]['group'];
			if (undefined === groups[group]) {
				groups[group] = group;
				ordered.push(group);
			}
		}
	}
	ordered.sort(function(a,b){return a - b});
	for (var i = 0; i < ordered.length; ++i) {
		var group = ordered[i];
		groups[group] = i;
	}
	this.groupsCount = 0;
	this.groupCounts = new Array();
	this.hasGroupT = false;
	for (var betId in this.bet) {
		for (var colId in this.bet[betId]) {
			var group = groups[ this.bet[betId][colId]['group'] ];
			this.bet[betId][colId]['group'] = group;
			if (undefined === this.groupCounts[group]) {
				this.groupCounts[group] = 1;
				++this.groupsCount;
			}
			else
				++this.groupCounts[group];
		}
	}
	this.hasGroupT = (undefined !== this.groupCounts[0]);
	return this.groupsCount;
};

TTCupon.prototype.updateCombinations = function(prevGroupCount, prevHasGroupT, data, forced) {
	var prevGcCorr = (prevHasGroupT ? 1 : 0);
	var prevGc = prevGroupCount - prevGcCorr;
	var gcCorr = (this.hasGroupT ? 1 : 0);
	var gc = this.groupsCount - gcCorr;
	//if (gc + gcCorr > 1) {
	if (gc > 1) {
		if (false === this.combinations)
			this.combinations = new Array();
		if (gc < prevGc)
			this.combinations = this.combinations.slice(0, gc);
		if (true === forced || gc != prevGc || gc != this.combinations.length) {
			this.combinedBetNum = 0;
			for (var i = 0; i < gc; ++i) {
				if (undefined === this.combinations[i])
					this.combinations[i] = {'used': false};
				var n = this.systemAr[gc][i + 1];
				var used = true;
				if (undefined !== data) {
					if (false === data[i].used || 0 == data[i].stake)
						used = false;
				}
				else if (undefined === this.combinations[i] || false === this.combinations[i].used)
					used = false;
				if (used)
					this.combinedBetNum += n;
				this.combinations[i].betCount = n;
				this.combinations[i].used = used;
			}
			var cdata = (undefined === data ? this.combinations : data);
			this.updateCombinationStakes(cdata);
		}
	}
	else
		this.combinations = false;
};

TTCupon.prototype.enableCombination = function(i, enable) {
	if (true === this.repeatingClick && false === this.givenTotalBet)
		return false;
	if (false !== this.combinations && undefined !== this.combinations[i]) {
		this.combinations[i].used = enable;
		if (!enable)
			this.combinations[i].stake = 0.0;
		if (!enable
			|| (false != this.givenTotalBet && 0 != this.givenTotalBet)
			|| (false === this.givenTotalBet && 0 != this.combinations[i].stake)) {

			this.combinedBetNum = 0;
			for (var i in this.combinations)
				this.combinedBetNum += (false === this.combinations[i].used ? 0 : this.combinations[i].betCount);
			return true;
		}
	}
	return false;
};

TTCupon.prototype.updateCombinationStakes = function(data) {
	var total = (false !== this.givenTotalBet ? this.givenTotalBet : this.totalBet);
	var stake = new Number(this.combinedBetNum > 0 ? total / this.combinedBetNum : 0).toFixed(2);
	for (var i = 0; i < this.combinations.length; ++i) {
		if (undefined === data) {
			if (false !== this.combinations[i].used)
				this.combinations[i].stake = stake;
			else
				this.combinations[i].stake = 0.0;
		}
		else
			this.combinations[i].stake = data[i].stake;
	}
};

TTCupon.prototype.getAllGroupNames = function() {
	var groups = new Array();
	var aCode = 'A'.charCodeAt(0);
	var n = this.getGroupsCount(false);
	if (n < this.betNum)
		++n;
	var n = Math.min(n, this.maxGroupCount);
	for (var i = 0; i < n; ++i)
		groups[i] = { id: i + 1, name: String.fromCharCode(aCode + i) };
	if (n > 0)
		groups[n] = { id: 0, name: 'T' };
	return groups;
};

TTCupon.prototype.getAllGroupOptions = function(selectedGroup) {
	var groups = this.getAllGroupNames();
	var result = '';
	for (var group in groups) {
		var g = groups[group];
		result += '<option value="' + g.id + '"';
		if (g.id == selectedGroup)
			result += ' selected="selected"';
		result += '>' + g.name + '</option>';
	}
	return result;
};

TTCupon.prototype.getCombinations = function(n, k, A) {
	if (k > n)
		return new Array();
	if (0 >= k)
		return new Array();
	if (k == n)
		return new Array(A);
	if (1 == k) {
		var result = new Array();
		for (var i = 0; i < n; ++i)
			result[i] = [ A[i] ];
		return result;
	}
	var headA = A[0];
	var tailA = A.slice(1);
	var c1 = this.getCombinations(n - 1, k - 1, tailA);
	var c2 = this.getCombinations(n - 1, k, tailA);
	var result = new Array();
	for (var i = 0; i < c1.length; ++i)
		result[i] = [ headA ].concat(c1[i]);
	return result.concat(c2);
};

TTCupon.prototype.getMaxBetNum = function() {
	if (0 == this.page)
		return this.maxBetNumSimple
	else if (1 == this.page) {
		if (this.isMaxicombinator())
			return this.maxBetNumMaxi;
		else
			return this.maxBetNumKombi
	}
	else
		return 0;
};

TTCupon.prototype.DeleteAll = function(server){
	if (this.betsLocked)
		return false;
	for(var vl in this.bet){
			for(var vl2 in this.bet[vl]){
				var tdId = '#b' + vl + 'c' + vl2;
				$(tdId+','+tdId+'_terno,'+tdId+'_lastminute,'+tdId+'_supertip,'+tdId+'_bookmakertip').removeClass('active-rate');
			}
			delete this.bet[vl];
	}
	this.totalBet = '0.00';
	this.totalBetInPoints = '0.00';
	this.pointType = null;
	this.totalWin = '0.00';
	this.simpleSum = '0.00';
	this.preapproved = false;
	this.setSendMail(1);
	this.setSendSms(1);
	this.givenTotalBet = false;

	if (undefined === server)
		server = true;
	this.UpdateAll(server);

	$('#ghtr8').hide();
};

TTCupon.prototype.SetAll = function(data){
	if (this.betsLocked)
		return false;
	
	this.DeleteAll(false);
	
	this.mail = data['mail'];
	this.sms = data['sms'];
	
	this.initializing = true;
	for(var key in data['bet']){
		item = data['bet'][key];
		tick.ChangeBet(
			parseInt(item['id_bet']),
			parseInt(item['id_col']),
			item['text'] !== undefined ? item['text'] : 'Neznámá příležitost',
			item['type'] !== undefined ? item['type'] : 'Neznámý typ',
			item['bet'] !== undefined ? item['bet'] : 'Neznámá sázka',
			item['rate'],
			item['simple'] !== undefined ? item['simple'] : 0, 
			0, // server
			null, // visible
			item['amount'],
			0, // live
			item['group'],
			undefined
		);
	}
	if (data['totalSum'] !== undefined) this.totalBet = data['totalSum'];
	if (data['pointType'] !== undefined) this.pointType = data['pointType'];
	if (data['rateAdvance'] !== undefined) this.rateAdvance = data['rateAdvance'];
	this.rateAdvanceCost = 0;
	this.page_help = this.page = data['type'] == 'kombi' || data['type'] == 'maxikombi' ? 1 : 0;
	
	if (data['combinations'] !== undefined) {
		this.updateGroupCounts();
		var comb = new Array();
		for(var key in data['combinations']) {
			item = data['combinations'][key];
			comb.push(item);
		}
		this.updateCombinations( 0, false, comb );
	}
	this.initializing = false;

	this.UpdateAll(true, false);
};

TTCupon.prototype.SetPage = function() {

	var ob1 = new getObj('ghtr1_0');
	var ob2 = new getObj('ghtr1_1');

	if (this.betNum == 0) {
		$('#ghtr1_1').attr('class','');
		$('#ghtr1_0').attr('class','');

		ob1.obj.innerHTML = '<a class="t1 first" href="#"><span>'+this.words['type1']+'</span></a>';
		ob2.obj.innerHTML = '<a class="t2" href="#"><span>'+this.words['type2']+'</span></a>';
	}
	else if (this.page == 0) {
		$('#ghtr1_0').attr('class','active');
		if (this.simple == 1 || this.betNum == 1)
			$('#ghtr1_1').attr('class','');
		else
			$('#ghtr1_1').attr('class','avail');
		ob1.obj.innerHTML = '<a class="t1 first"  href="#"><span>'+this.words['type1']+'</span></a>';
		if (this.simple == 1 || this.betNum == 1)
			ob2.obj.innerHTML = '<a class="t2" href="#"><span>'+this.words['type2']+'</span></a>';
		else
			ob2.obj.innerHTML = '<a href="javascript:'+this.objName+'.page=1;'+this.objName+'.page_help=1;'+this.objName+'.UpdateAll();" class="t2"><span>'+this.words['type2']+'</span></a>';
	}
	else if (this.page == 1) {
		$('#ghtr1_0').attr('class','avail');
		$('#ghtr1_1').attr('class','active');
		ob1.obj.innerHTML = '<a href="javascript:'+this.objName+'.page=0;'+this.objName+'.page_help=0;'+this.objName+'.UpdateAll();" class="t1 first"><span>'+this.words['type1']+'</span></a>';
		ob2.obj.innerHTML = '<a class="t2"  href="#"><span>'+this.words['type2']+'</span></a>';
	}
};


TTCupon.prototype.UpdateAll = function(server, notInvalidate) {
	this.UpdateData();
	this.SetPage();
	this.updateGroupCounts();
	this.updateCombinations(this.groupsCount, this.hasGroupT);
	this.SetBet();

	this.SetTotalSum();
	this.SetSum();
	if (0 != server && false !== server)
		this.ServerUpdate(notInvalidate);

};

TTCupon.prototype.Warn = function(text) {
	var warn = new getObj('ghtr4');
	warn.obj.innerHTML = '<p class="dashesBot"><strong class="msg_err">'+text+' </strong></p>';
	warn.style.display = 'block';
};

TTCupon.prototype.ActiveCell = function() {
	for (var vll2 in this.bet) {
		for (var vll3 in this.bet[vll2]) {
			$('#'+this.bet[vll2][vll3]['td']).attr('class','active_rate');
		}
	}
};

TTCupon.prototype.ChangeRate = function(field) {
	var o1;
	var o2;
	var r1;
	var x,y;

	for (x = 1, y=0;x<field.length;x++,y++) {
		if (y == 1)
			o1 = field[x];
		else if(y == 2)
			o2 = field[x];
		else if(y == 3)
			r1 = field[x];

		if(y == 3) {
			this.bet[o1][o2]['rate'] = parseFloat(r1).toFixed(2);
			this.bet[o1][o2]['rate2'] = r1.toString();
			y = 0;
		}
	}
	this.ServerUpdate();
};

TTCupon.prototype.fixBetOrder = function() {
	var fixedBetOrder = [];
	for (var i = 0; i < this.betOrder.length; ++i) {
		var betId = this.betOrder[i]['betId'];
		var colId = this.betOrder[i]['colId'];
		if (undefined !== this.bet[betId] && undefined !== this.bet[betId][colId])
			fixedBetOrder.push(this.betOrder[i]);
	}
	this.betOrder = fixedBetOrder;
}

TTCupon.prototype.deleteBet = function(betId, colId, updateTicket, server) {
	var ob = new getObj(this.bet[betId][colId]['td']);

	delete this.bet[betId][colId];
	var prevGroupsCount = this.groupsCount;
	var prevHasGroupT = this.hasGroupT;
	this.updateGroupCounts();
	this.updateCombinations(prevGroupsCount, prevHasGroupT);

	var tdId = '#b' + betId + 'c' + colId;
	$(tdId+','+tdId+'_terno,'+tdId+'_lastminute,'+tdId+'_supertip,'+tdId+'_bookmakertip').removeClass('active-rate');

	var x = 0;
	for (var vl in this.bet[betId]) {
		x++;
		break;
	}

	if (!this.initializing && 1 >= --this.betNum)
		this.page_help = 1;

	if (x == 0)
		delete this.bet[betId];

	this.fixBetOrder();

	if (updateTicket)
		this.UpdateAll(server ? 1 : 0, false);
};

TTCupon.prototype.selectBetRate = function(betId, colId) {
	var tdId = '#b' + betId + 'c' + colId;
	$(tdId+','+tdId+'_terno,'+tdId+'_lastminute,'+tdId+'_supertip,'+tdId+'_bookmakertip').addClass('active-rate');
}

TTCupon.prototype.selectAllBetRates = function() {
	for (var betId in this.bet) {
		for (var colId in this.bet[betId]) {
			this.selectBetRate(betId, colId);
		}
	}
}

TTCupon.prototype.ChangeBet = function(id,id_col,text,type,bet,rate,simple,server,visible,amount,live,group,couponTime){
	if (this.betsLocked)
		return false;

	if (!$('#ticket_container').is(':visible')) {
		$('#ticket_container2').hide();
		$('#box_kupon').removeClass('box-kupon-no-tabs');
		$('#ticket_container').show();
	}

	if (!this.bet[id])
		this.bet[id] = new Object();

	if (!this.bet[id][id_col]) {
		var tdId = '#b' + id + 'c' + id_col;
		var td = $('td'+tdId);
		if (0 == td.length) {
			td = $('td'+tdId+'_terno');
			if (0 == td.length)
				td = $('td'+tdId+'_lastminute');
			if (0 == td.length)
				td = $('td'+tdId+'_supertip');
			if (0 == td.length)
				td = $('td'+tdId+'_bookmakertip');
		}
		if (null === rate)
			rate = parseFloat(td.text());
		//if((this.betNum+this.visible) >= this.maxBet){
		var maxBetNum = this.getMaxBetNum();
		if(this.betNum >= maxBetNum){
			var warn = new getObj('ghtr4');
			//warn.obj.innerHTML = '<div class="notify notify-error">'+this.words['okf2']+' '+maxBetNum+'</div>';
			warn.obj.innerHTML = '<div role="alert" class="alert alert-danger alert-dismissible"><button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button><h2>!</h2>'+this.words['okf2']+' '+maxBetNum+'</div>';
			warn.style.display = 'block';
			return;
		}

		//alert(id+' - '+id_col+' - '+text+' - '+type+' - '+bet+' - '+rate+' - '+simple+' - '+server+' - '+visible+' - '+amount+' - '+live)
		var newBet = new Object();
		newBet['id'] = id;
		newBet['column'] = id_col;
		newBet['td'] = 'bet_'+id+'_'+id_col;
		newBet['text']   = text;
		newBet['type']   = type;
		newBet['bet']   = bet;
		newBet['live']   = live;
		newBet['rate']   = parseFloat(rate).toFixed(2);
		newBet['rate2']   = rate.toString();
		newBet['simple']   = simple;
		newBet['amount']   = (amount?amount:this.simpleSum);
		newBet['couponTime'] = (undefined === couponTime ? String( (new Date()).getTime() ) : couponTime);
		if (undefined === group) {
			var gc = this.getGroupsCount(false);
			newBet['group'] = (gc > 0 ? gc : 1);
		}
		else
			newBet['group'] = group;
		this.bet[id][id_col] = newBet;

		this.selectBetRate(id, id_col);

		var ob = new getObj(this.bet[id][id_col]['td']);
		if (ob.obj) {
		}
	}
	else {
		this.deleteBet(id, id_col);
	}
	this.orderBets();

	if (this.betNum > 0)
		$('#ghtr2').show();

	if (!this.initializing)
		this.UpdateAll(server);
};

TTCupon.prototype.ChangeBet2 = function(id,id_col,rate,server,akt) {
	if (this.betsLocked)
		return false;

  if(akt == 1){

  	  var ob = new getObj(this.bet[id][id_col]['td']);


    if(ob.obj){

       $('#'+this.bet[id][id_col]['td']).attr('class','nonactive_rate');

    }

    delete this.bet[id][id_col];

    var x = 0;

    for(var vl in this.bet[id]){
     x++;break;
    }

    if(x == 0) delete this.bet[id];

  }else{
   this.bet[id][id_col]['rate']   = parseFloat(rate).toFixed(2);
   this.bet[id][id_col]['rate2']   = parseFloat(rate).toFixed(2);
  }


   this.UpdateAll(server);

   this.ServerUpdate();

  };

TTCupon.prototype.EventCapture = function(){

	var ob1 = (document.getElementsByName?document.getElementsByName('press1'):document.all['press1']);
	var ob2 = (document.getElementsByName?document.getElementsByName('press2'):document.all['press2']);
	var ob3 = (document.getElementsByName?document.getElementsByName('press3'):document.all['press3']);
	var ob4 = (document.getElementsByName?document.getElementsByName('press4'):document.all['press4']);

	if(document.attachEvent){
      if(ob1){
	   for(var x=0;x<ob1.length;x++)
	    ob1[x].attachEvent('onkeypress',KeyPressed);
      }
      if(ob2){
	   for(var x=0;x<ob2.length;x++)
	    ob2[x].attachEvent('onkeypress',KeyPressed);
      }
      if(ob3){
	   for(var x=0;x<ob3.length;x++)
	    ob3[x].attachEvent('onkeypress',KeyPressed);
      }
      if(ob4){
	   for(var x=0;x<ob4.length;x++)
	    ob4[x].attachEvent('onkeypress',KeyPressed);
      }

    }

    else if(document.addEventListener){
      if(ob1){
	   for(var x=0;x<ob1.length;x++)
	    ob1[x].addEventListener('keypress', KeyPressed, false);
      }
      if(ob2){
	   for(var x=0;x<ob2.length;x++)
	    ob2[x].addEventListener('keypress', KeyPressed, false);
      }
      if(ob3){
	   for(var x=0;x<ob3.length;x++)
	    ob3[x].addEventListener('keypress', KeyPressed, false);
      }
      if(ob4){
	   for(var x=0;x<ob4.length;x++)
	    ob4[x].addEventListener('keypress', KeyPressed, false);
      }
    //document.getElementById('text1').removeEventListener(udalost, funkce, typ);
    }
    else{

	 if(ob1){
	   for(var x=0;x<ob1.length;x++)
	    ob1[x].onkeypress = KeyPressed;
     }
	 if(ob2){
	   for(var x=0;x<ob2.length;x++)
	    ob2[x].onkeypress = KeyPressed;
     }
	 if(ob3){
	   for(var x=0;x<ob3.length;x++)
	    ob3[x].onkeypress = KeyPressed;
     }
	 if(ob4){
	   for(var x=0;x<ob4.length;x++)
	    ob4[x].onkeypress = KeyPressed;
     }
	}

};

TTCupon.prototype.UpdateData = function(){

	var x = 0;
	var y;
	var numChanged = false;
	this.simple = 0;

	for (var vl in this.bet) {
		y = 0;
		for (var vl2 in this.bet[vl]) {
			x++;
			y++;
			if (this.bet[vl][vl2]['simple'] == 1)
				this.simple = 1;
		}
		if (y > 1)
			this.simple = 1;
	}
	//zjistim jestli doslo ke zmene poctu prilezitosti na tiketu
	if (this.betNum < x && this.betNum != 0 )
		numChanged = true;
	this.betNum = x;
	if (this.simple != 1 && this.betNum > 1){
		this.page=1;
		//doslo li k pridani
		if(numChanged) this.page_help = 1;
	}
	if (this.page_help == 0 || this.simple == 1 || this.betNum == 1)
		this.page=0;

//	if ((this.betNum + this.visible) <= this.maxBet) {
	if (this.betNum <= this.getMaxBetNum()) {
		var warn = new getObj('ghtr4');
		warn.style.display = 'none';
	}
};

TTCupon.prototype.executeCommands = function(commands, pinPoint) {
	if (!commands)
		return;
	if (undefined === pinPoint)
		pinPoint = 'default';
	for (i in commands) {
		var cmd = commands[i];
		var cmdPinPoint = (undefined === cmd.pinPoint ? 'default' : cmd.pinPoint);
		if (pinPoint != cmdPinPoint)
			continue;
		if ('displayMessage' == cmd.name) {
			var type = (undefined === cmd.type ? 'error' : cmd.type);
			if (undefined === cmd.bet || undefined === cmd.column)
				this.showNotification(cmd.param, type);
			else
				this.showBetNotification(cmd.bet, cmd.column, cmd.param, type);
		}
		else if ('showLoginForm' == cmd.name && cmd.param)
			this.showLoginForm();
		else if ('stopConfirmation' == cmd.name) {
			if (!cmd.noBack)
				TicketBack(true, true);
			else {
				this.confirmationEnd();
				this.betsLocked = false;
			}
		}
		else if ('deleteBet' == cmd.name) {
			this.deleteBet(cmd.bet, cmd.column, true, false);
		}
		else if ('setPreapproved' == cmd.name) {
			this.preapproved = (cmd.param ? true : false);
		}
	}
};

TTCupon.prototype.updateFromServer = function(data, resetCouponId, sendBack, notInvalidate) {
	if (undefined !== data.commands) {
		this.executeCommands(data.commands);
	}
	if (undefined !== data.ticket) {
		this.userLoggedIn = data.ticket.userLoggedIn;
		this.totalRate = parseFloat(data.ticket.rate);
		this.totalRateWithoutAdvance = parseFloat(data.ticket.rateWithoutAdvance);
		this.totalWin = parseFloat(data.ticket.win);
		this.totalBet = parseFloat(data.ticket.stake);
		this.showGetPoints = data.showGetPoints;
		this.getPoints = parseInt(data.getPoints);
		this.preferenceSizes = data.preferenceSizes;
		if ( this.showRateAdvance != data.showRateAdvance ) {
			this.showRateAdvance = data.showRateAdvance;
			//this.SetTotalSum();
			//TODO: PAVEL: tohle se zda bejt hovadina
			//      jednak je to zacykleni (proc startujes AJAX z AJAX response handleru, ktery ma do tiketu nasypat data ze serveru?)
			//      jednak to nema mit vliv na simple a maxi tikety
			//if (sendBack)
			//	this.ServerUpdate(notInvalidate);
		}
		if ( this.showPoints != data.showPoints ) {
			this.showPoints = data.showPoints;
			//this.SetTotalSum();
			//TODO: PAVEL: tohle se zda bejt hovadina
			//      dtto
			//if (sendBack)
			//	this.ServerUpdate();
		}
		this.rateAdvance = parseFloat(data.ticket.rateAdvance);
		this.rateAdvanceCost = parseFloat(data.rateAdvanceCost);
		this.setSendMail(data.ticket.mail);
		this.setSendSms(data.ticket.sms);
		this.givenTotalBet = (undefined === data.ticket.givenStake ? false : data.ticket.givenStake);
		if (resetCouponId)
			this.couponId = 0;
		var amountAll = null;
		this.betNum = 0;
		var amountSum = 0;
		for (var idBet in this.bet) {
			for (var idCol in this.bet[idBet]) {
				var amount = parseFloat(this.bet[idBet][idCol]['amount']);
				amountSum += amount;
				++this.betNum;
				if (null === amountAll)
					amountAll = amount;
				else if (amountAll != amount)
					amountAll = false;
			}
		}
		for (var i = 0; i < data.ticket.bets.length; ++i) {
			var bet = data.ticket.bets[i];
			var idBet = bet['id'];
			var idCol = bet['column'];
			if (undefined !== this.bet[idBet]
				&& undefined !== this.bet[idBet][idCol]
				&& undefined === this.bet[idBet][idCol]['couponTime']) {
				this.bet[idBet][idCol]['couponTime'] = bet['couponTime'];
			}
		}
		amountSum = Number(amountSum).toFixed(precisionStake);
		if (amountSum != this.totalBet) {
			amountAll = Number(this.totalBet / this.betNum).toFixed(precisionStake);
			this.simpleSum = Number(amountAll * this.betNum).toFixed(precisionStake);
			for (var idBet in this.bet) {
				for (var idCol in this.bet[idBet]) {
					this.bet[idBet][idCol]['amount'] = amountAll;
				}
			}
		}
		else
			this.simpleSum = (null === amountAll || false === amountAll ? 0.0 : amountAll);
		if (undefined !== data.ticket.combinations) {
			var prevGc = this.getGroupsCount(true);
			var prevHasT = this.hasGroupT;
			this.updateCombinations(prevGc, prevHasT, data.ticket.combinations.slice(1), true);
		}
		this.page = ('simple' == data.ticket.type ? 0 : 1);
		this.orderBets(false);
	}
	this.SetPage();
	this.SetBet();
	this.SetSum();
	if (undefined !== data.commands) {
		this.executeCommands(data.commands, 'after-update');
	}
	this.scrollToNotification();
	if (true === data.refresh)
		window.setTimeout(function() { tick.ServerUpdate(0); }, 250);
	this.selectAllBetRates();
};

TTCupon.prototype.ServerUpdateResponse = function(data, notInvalidate){
	try {
		if(undefined !== data.error && 0 != data.error){
			for(var x = 2; x<10; x++) {
				$('#ghtr'+x).hide();
			}

			$('#ghtr1').html(TTCupon.bit1);
			$('#ghtr3').show().html('<p class="msg_warn">'+TTCupon.bproveText+'</p>');
		}
		else {
			this.updateFromServer(data, true, true, notInvalidate);
		}
	}
	catch (e) {
		alert(this.words['okf34']); // server communication error
	}
	$('#ticket_content').removeClass('busy');
	//$('#ticket_content :input').attr('disabled', '');
	//$('#ticket_content :input[type="checkbox"]').attr('disabled', '');
	this.betsLocked = false;
	this.repeatSavedClick();
};

TTCupon.prototype.getCouponData = function(asString) {
	if (undefined === asString)
		asString = false;

	var data = new Object();
	data.couponId = this.couponId;

	if (this.page == 0)
		data.type = 'simple';
	else { // if (this.page == 1) {
		if (this.isMaxicombinator()) {
			data.type = 'maxikombi';
			data.totalSum = this.totalBet;
			//TODO: add bet group combination stakes if any
		}
		else {
			data.type = 'kombi';
			if ( this.pointType >= 1 ) {
				data.totalSumInPoints = this.totalBetInPoints;
			}
			else {
				data.totalSum = this.totalBet;
			}
			data.win = this.totalWin;
			data.pointType = this.showPoints ? this.pointType : null;
			data.rateAdvance = (!this.showPoints || !(this.pointType >= 1)) && this.showRateAdvance && this.rateAdvance > 1
				? this.rateAdvance : null;
		}
	}

	data.mail = this.mail;
	data.sms = this.sms;
	data.givenStake = this.givenTotalBet;
	data.bet_code = this.bet_code;

	var g = 0;
	var group;
	data.bet = new Array();
	var order = 0;
	for(var vl in this.bet) {
		for(var vl2 in this.bet[vl]) {
			++order;
			group = this.bet[vl][vl2]['group'];
			if(isNaN(this.bet[vl][vl2]['amount']) || this.bet[vl][vl2]['amount'].length==0)
				this.bet[vl][vl2]['amount'] = 0;
			var bet = new Object();
			bet['id_bet'] = vl;
			bet['id_col'] = vl2;
			bet['rate'] = this.bet[vl][vl2]['rate'];
			bet['amount'] = this.bet[vl][vl2]['amount'];
			bet['group'] = group;
			bet['couponTime'] = this.bet[vl][vl2]['couponTime'];
			bet['order'] = order;
			data.bet.push(bet);
		}
	}

	data.combinations = new Array();
	for (var i = 0; i < this.combinations.length; ++i) {
		var comb = new Object();
		comb['stake'] = this.combinations[i].stake;
		comb['used'] = (this.combinations[i].used ? true : false);
		data.combinations[i + 1] = comb;
	}

	return (asString ? JSON.stringify(data) : data);
};

TTCupon.prototype.ServerUpdate = function(notInvalidate, saveCoupon){
	if (saveCoupon == undefined || saveCoupon !== true) saveCoupon = false;
	
	this.clearNotifications();
	var json = this.getCouponData(true);
	
	$('#bet_ticket_form_cupon_data').attr('value', json);
	form = document.getElementById('ticket_content');
	this.betsLocked = true;
	this.prepareSaveClick();
	$('#ticket_content').addClass('busy');
	//$('#ticket_content :input').attr('disabled', 'disabled');
	//$('#ticket_content :input[type="checkbox"]').attr('disabled', 'disabled');
	var url = this.url + (notInvalidate == 1 ?  '?notInvalidate' : '') + (saveCoupon ?  (notInvalidate == 1 ? '&' : '?') + 'saveCoupon' : '');
	jQuery.post(url,json, function(data) { tick.ServerUpdateResponse(data, notInvalidate); }, 'json');
};

TTCupon.prototype.getNotificationContainer = function() {
	var container = $('#ticket_container #ticket_content');
	if (container.is(':visible'))
		return container;
	container = $('#ticket_container2 .content');
	if (container.is(':visible'))
		return container;
	return false;
};

// type := ('success'|'info'|'warning'|'error'|'alert')
TTCupon.prototype.showNotification = function(msg, type) {
	var container = this.getNotificationContainer();
	if (false === container)
		return;
	//$('.text:first', container).before('<div class="notify notify-' + type + '">' + msg + '</div>');
	
	// to-do type: class + h2
	
	// to-do alert-dismissible
	// http://stackoverflow.com/questions/10082330/dynamically-create-bootstrap-alerts-box-through-javascript
	$('.inTicket.tab-content.yellow:first', container)
			.before('<div class="inTicket notify tab-content yellow no-margin"><div role="alert" class="alert alert-danger">' + // class alert-dismissible
						'<!--<button data-dismiss="alert" class="close" type="button"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>-->' +
						'<!--<h2> </h2>-->' + msg +
					'</div></div>');
	
	//this.scrollToNotification(container);
};

TTCupon.prototype.scrollToNotification = function(container) {
  return;
	if (undefined === container)
		container = this.getNotificationContainer();
	if (false === container)
		return;
	var minTop = false;
	$('div.notify-success, div.notify-info, div.notify-warning, div.notify-error, div.notify-alert, .alert-danger, .alert-info, .alert-success, .alert-warning', container).each(
		function (i, e) {
			var top = $(e).offset().top;
			if (false === minTop || minTop > top)
				minTop = top;
		}
	);
	if (false !== minTop)
		$('html, body').animate({ scrollTop: (minTop > 50 ? minTop - 50 : 50) }, 1000);
};

TTCupon.prototype.getBetNotificationContainer = function(betId, colId) {
	var container = $('#ticket_container #ghtr2 #bet_item_' + betId + '_' + colId);
	if (container.is(':visible'))
		return container;
	container = $('#ticket_container2 .content #bet_item2_' + betId + '_' + colId);
	if (container.is(':visible'))
		return container;
	return false;
};

//type := same as in showNotification()
TTCupon.prototype.showBetNotification = function(betId, colId, msg, type) {
	var container = this.getBetNotificationContainer(betId, colId);
	if (false === container)
		return;
	$('> div:last', container).after('<div class="alert alert-info notify-' + type + '">' + msg + '</div>');
};

TTCupon.prototype.clearNotifications = function() {
	var container = this.getNotificationContainer();
	if (false === container)
		return;
	$('.notify', container).remove();
};

TTCupon.prototype.onInputFocus = function(elem) {
	$(elem).select();
};

TTCupon.prototype.prepareSaveClick = function() {
	this.savedClick = true;
};

TTCupon.prototype.saveClick = function(event) {
	if (false !== this.savedClick) {
		var je = $.Event(event);
		this.savedClick = {targetId: event.currentTarget.id};
		je.preventDefault();
		je.stopImmediatePropagation();
		return true;
	}
	return false;
};

TTCupon.prototype.repeatSavedClick = function() {
	var targetId = this.savedClick.targetId;
	this.clearSavedClick();
	this.repeatingClick = true;
	if (undefined !== targetId)
		$('#'+targetId).click();
	this.repeatingClick = false;
};

TTCupon.prototype.clearSavedClick = function() {
	this.savedClick = false;
};

function KeyPressed(e){
/* TODO: reexamine this... this shouldn't be necessary (and it has ugly side-effects like suppressed cursor moving by arrow keys)
 if(!e) e = window.event;

 var k=(e.which)?e.which:e.keyCode;

 var srcEl = (e.srcElement?e.srcElement:e.currentTarget);

 if(k == 46){

   var vzor = new RegExp("\\.","g");

   if(vzor.test(srcEl.value) || srcEl.value.length < 1){

     if(e.preventDefault)
	   e.preventDefault();
	 else
	 return false;

   }else
     return true;

 }
 else if((k >= 48 && k <= 57) || k == 8)
  return true;
 else{

     if(e.preventDefault)
	   e.preventDefault();
	 else
	   return false;

 }
*/
	return true;
}