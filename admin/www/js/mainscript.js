function loadParameters(userId) {
	$("#parameters").load('?section=223',
		{ userId: userId },
		function(data){
	});
}
function disableField(fieldId){
	if($('#'+fieldId).attr('disabled'))
		$('#'+fieldId).removeAttr('disabled');
	else
		$('#'+fieldId).attr('disabled','disabled');
}
function submitGeneric(section,formName,destElmId,params) {
	$.post('?section='+section,
		$('#'+formName).serialize()+params,
		function(data) {
			$('#'+destElmId).html(data);
			wrapperRestore();
		});
}



var imageFile = "_clip/";  //cesta k obrazkum
var fileimg =  ["excel_neaktivni.gif","pdf_neaktivni.gif","powerpoint_neaktivni.gif","txt_neaktivni.gif","visio_neaktivni.gif","word_neaktivni.gif","image_neaktivni.gif","other_neaktivni.gif"];

var minSloupec = 1;//minimalni pocet sloupcu u podtypu
var maxSloupec = 100;//maximalni pocet sloupcu u podtypu
var aktSloupec = 3; //aktualnipocet zobrazenych sloupcu
var altlang = false; //id jazyka u alt
var comb_ch_ar = new Array();  //pole kombinaci sazek
var submitProve = true;
var zindex = 1;

jQuery(document).ready(function() {

   document.onkeypress = function(){
      submitProve = false;
   }

  //window.onerror = JSError;
	if ($('#panel-left').is(':hidden'))
		$('#wrapper').attr({ 'style' : 'margin-left:0'});
	else
		$('#wrapper').attr({ 'style' : 'margin-left:210px'});
  addCalendars();
  //$('#panel-left').dcFloater({
            //width: 200,
            //location: 'top',
            //align: 'right',
            //offsetLocation: 0,
            //offsetAlign: 0,
            //speedFloat: 100,
            //speedContent: 200,
            //tabText: 'Menu',
            //autoClose: false,
            //easing: 'easeOutQuint',
          ////  event: 'hover'
    //});

});

function toggleFeedbackWrapper() {
		$("#feedback-wrapper").slideToggle();
}

function toggleMenu() {
	$('#panel-left').toggle();
	$('#menu-toggler').toggle();
	if ($('#panel-left').is(':hidden'))
		$('#wrapper').attr({ 'style' : 'margin-left:0'});
	else
		$('#wrapper').attr({ 'style' : 'margin-left:210px'});
}

function toggleGeneric(buttonEl, elmIdPrefix, id) {
	if ($(buttonEl).html()=='+')
		$(buttonEl).html('-');
	else
		$(buttonEl).html('+');
	
	 $('#' + elmIdPrefix + '_' + id).toggle();
}

function addCalendars(){
  jQuery(document).ready(function() {
    jQuery("input.dateTime").dynDateTime({
      align: "CR",
      showsTime: true,
      ifFormat: "%d.%m.%Y %H:%M:00",
      daFormat: "%d.%m.%Y %H:%M:00"
    });
  });

  jQuery(document).ready(function() {
    jQuery("input.dateOnly").dynDateTime({
      align: "CR",
      ifFormat: "%d.%m.%Y",
      daFormat: "%d.%m.%Y"
    });
  });
}

jQuery.extend({BInfo:function(act,info,id){

    if(act == 1){

      var offset =  $('#bi_'+id).offset();
      $('#binfomes').html(info);
      $('#binfomes').css('left',offset.left);
      $('#binfomes').css('top',offset.top);
      $('#binfomes').show();

    }else{

      $('#binfomes').hide();

    }

   }
 });

function OnlineProcessing(){

    $.ajax({
     type: 'GET',
     url: "ajax.server.php?work=100&read",
     dataType: "json",
     success: function(msg){

        for(var vl in msg){
         if(vl=='js_dynamic')eval(msg[vl]);
         else
         $('#'+vl).prepend(msg[vl]);
        }

     }
   });

}


jQuery.extend({OpenLive:function(ev){

      window.open("/live.php?event="+ev,"","scrollbars=yes,resizable=yes,width=1070,height=800,top=0,left=0");

   }
 });

 jQuery.fn.extend({
   check: function() {
     return this.each(function() { if(this.checked ==  false)this.checked = true;else this.checked = false;});
   }
 });

jQuery.extend({SetCombClick:function(n){

    if(n==1){

       $("input[name*='[kombinace]']").check();
    }
    else if(n==2){

      $("input[name*='[kombinace_show]']").check();
    }


  }
 });

function InsertDv(v,t){

 if(v.indexOf(":")==-1 && t.value.length>=1 && t.value.length<2){

  t.value =  v+':';
if(t.setSelectionRange) {
t.focus();
t.setSelectionRange((t.value.length-1),(t.value.length-1));
}
else {
if(t.createTextRange) {
range=t.createTextRange();
range.collapse(true);
range.moveEnd('character',(t.value.length-1));
range.moveStart('character',(t.value.length-1));
range.select();
}
}



 //var inputText = t.createTextRange();
 // var range = t.createRange();
//range.setStart(startNode, t.length);
//range.setEnd(endNode, 1);
  //inputText.moveStart("character", 1);
  //inputText.moveEnd("character",2 );
 // inputText.move("character",(t.length-1));
  //inputText.select();

 }else{

     var text = '';
     var znak = true;
     for(var x=0;x<t.value.length;x++){

        if(t.value.charAt(x) == ":" && znak) {text += ':';znak =  false;}
        else if(t.value.charAt(x) == ":" && !znak);
        else text += t.value.charAt(x);

     }
     t.value  = text;
  }

}

jQuery.extend({LoadEventLive:function(){

   var selectSport = $('#sport').get(0).options[$('#sport').get(0).selectedIndex].value;

   if(selectSport != 0){

      $('#udalost').get(0).length = 0;
      $('#udalost').append('<option value="0">Nahravam...</option>');

      $.get("ajax.server.php",{ work: "4", sport: selectSport  }, function(data){
           $('#udalost').get(0).length = 0;
           $('#udalost').append('<option value="0">Zvolte udalost</option>'+data);
      });


   }else{

    $('#udalost').get(0).length = 0;
    $('#udalost').append('<option value="0">Musite vybrat sport ...</option>')


   }

  }
 });


/*Vymeni carku za tecku*/
function RemoveCarka(v){

  return v.replace(/,+/g,".")

}

function fixDecimalComa(elem) {
	var jElem = $(elem);
	jElem.val(jElem.val().replace(/,+/g, '.'));
}

/*Vyhernosti u sazek*/
function fillInLiability(betId, complementColumns){
	$("#vyhernost_"+betId).val(calcLiability(betId, complementColumns));
}

var columnsToCompute = {};
/* returns column id to edit or false if there is no such column */
function saveRateToEdit(betId, complementColumns) {
	var colToEdit = null;
	for (var colId in vyhernost[betId]) {
		if (complementColumns[colId])
			continue;
		var input = $('#sazka\\['+betId+'\\]\\[sloupec\\]\\['+colId+'\\]');
		var rate = String(input.val()).replace(/\s+/g, '').replace(',', '.');
		input.val(rate);
		if ('' == rate) {
			if (colToEdit) {
				columnsToCompute[betId] = undefined;
				return false;
			}
			else
				colToEdit = colId;
		}
	}
	if (colToEdit) {
		columnsToCompute[betId] = colToEdit;
		return colToEdit;
	}
}

function getColumnToComputeRate(betId) {
	return (columnsToCompute[betId] ? columnsToCompute[betId] : false);
}

function SetVyhernost(sazka, complementColumns){
	for(var vl in vyhernost[sazka]) {
		var ob = new getObj('sazka['+sazka+'][sloupec]['+vl+']');
		if (ob.obj.value.match(/,+/g)!=null) {
			ob.obj.value = ob.obj.value.replace(/,+/g,".")
		}
	}
	$("#vyhernost_"+sazka).val(calcLiability(sazka, complementColumns));
/*	var status = true;
	var vyh = 0;
	var tmp = 0;
	
	for(var vl in vyhernost[sazka]) {
		
		var ob = new getObj('sazka['+sazka+'][sloupec]['+vl+']');

		if (ob.obj.value.match(/,+/g)!=null) {
			ob.obj.value = ob.obj.value.replace(/,+/g,".")
		}
		
		if (isNaN(parseFloat(ob.obj.value)) || parseFloat(ob.obj.value) == 0) {
			status = false;
		} else {
			vyh += (1/parseFloat(ob.obj.value));
		}
		
	}

	for (var vl in vyhernost[sazka]) {

		var ob2 = new getObj('sazka['+sazka+'][sloupec]['+vl+']');
		tmp = ( 1/(parseFloat(ob2.obj.value)*vyh) ).toFixed(2);
	}

	//var ob = new getObj('vyhernost_'+sazka);
	$("#vyhernost_"+sazka).val(vyh.toFixed(2));
	//ob.obj.value = vyh.toFixed(2);*/

}

function SetLastRateByLiabilty(sazka, complementColumns, eInput, editing, columnIds, colNumber) {
	if (undefined == editing)
		editing = false;

	var editColId = getColumnToComputeRate(sazka);
	var eInputColId = (eInput ? $(eInput).attr('id').replace(/^.*\[(\d+)\]$/i, '$1') : 0);
	var desiredLiab = $('#vyhernost_ctrl_'+sazka).val();
	var liab = $('#vyhernost_ctrl_'+sazka).val();
	var liab2 = liab;
	var rateToUpdate = $('#rateToUpdate_'+sazka).val();
	var complementColsCount = 0;
	for (var colId in complementColumns)
		++complementColsCount;
	var colsCount = $('#colsCount_'+sazka).val() - complementColsCount;
	var freeCols = colsCount;
	var mainRates = [];

	var emptyCols = 0;
	var allRatesSame = true;
	var rate = false;
	for (var vl in vyhernost[sazka]) {
		if (complementColumns[vl])
			continue;
		var input = $('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+vl+'\\]');
		var colRate = String(input.val()).replace(/(^\s+|\s+$)/g, '').replace(',', '.');
		input.val(colRate);
		mainRates[vl] = colRate;
		if (colRate) {
			if (false === rate)
				rate = colRate;
		}
		else
			++emptyCols;
		if (rate !== false && parseFloat(rate) != parseFloat(colRate))
			allRatesSame = false;
	}
	if (colsCount > 1) {
		/*
		var lastMainRate = null;
		var lastZeroMainRate = null;
		var liabRates = [];
		for(var vl in vyhernost[sazka]) {
			if (complementColumns[vl])
				continue;
			var rate = $('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+vl+'\\]').val();
			mainRates[vl] = rate;
			lastMainRate = vl;
			if (0 == rate)
				lastZeroMainRate = vl;
		}
		rateToUpdate = (lastZeroMainRate ? lastZeroMainRate : lastMainRate);
		*/
		rateToUpdate = editColId;
		$('#rateToUpdate_' + sazka).val(rateToUpdate);
		var x = x2 = 0;
		for (var vl in mainRates) {
			var rate = mainRates[vl];
			if (rate != 0 && vl != rateToUpdate) {
				x = (1/rate);
				x2 = (Math.floor( (x) *100))/100;
				liab2 = liab2 - x2;
				liab = liab - x;
				freeCols--;
			}
		}
		if (colsCount - emptyCols < 2)
			allRatesSame = false;
		$('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+rateToUpdate+'\\]').css('background','white');
		if (!editColId || allRatesSame || (eInputColId == editColId && editing)) {
			var newLiab = calcLiability(sazka, complementColumns);
			$("#vyhernost_"+sazka).val(newLiab);
		}
		else {
			if (freeCols == 1) {
				if (liab && liab2) {
					var calcRate1 = 1/liab2;
					var calcRate2 = 1/liab;

					var calcRate = (parseFloat(calcRate2) - ((parseFloat(calcRate2) - parseFloat(calcRate1))/colsCount).toFixed(2)).toFixed(2);
					if (calcRate < 0) {
						alternateCss($('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+rateToUpdate+'\\]'), 'background-color', 'red', 4, 300);
					} else {
					
						$('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+rateToUpdate+'\\]').val(calcRate);
						var newLiab = calcLiability(sazka, complementColumns);
						var start = (new Date()).getTime();
						while (newLiab == desiredLiab) {
							calcRate = parseFloat(calcRate) - parseFloat(0.01);
							calcRate = calcRate.toFixed(2);
							$('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+rateToUpdate+'\\]').val(calcRate);
							newLiab = calcLiability(sazka, complementColumns);
							if (2000 < (new Date()).getTime() - start) {
								// convergention timeout
								calcRate = false;
								break;
							}
						}
						if (calcRate) {
							calcRate = parseFloat(calcRate) + parseFloat(0.01);
							calcRate = calcRate.toFixed(2);
							$('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+rateToUpdate+'\\]').val(calcRate);
							mainRates[rateToUpdate] = calcRate;
							$("#vyhernost_"+sazka).val(calcLiability(sazka, complementColumns));
						}
						else {
							alternateCss($('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+rateToUpdate+'\\]'), 'background-color', 'red', 4, 300);							
						}
					}
				}
				for (var colId in complementColumns) {
					var sum = 0;
					for(var i in complementColumns[colId]) {
						var complColId = complementColumns[colId][i];
						sum += 1 / mainRates[complColId];
					}
					calcRate = (sum > 0 ? 1 / sum : 1);
					if (calcRate < 1.0)
						calcRate = 1.0;
					$('#sazka\\['+sazka+'\\]\\[sloupec\\]\\['+colId+'\\]').val(calcRate.toFixed(2));
				}
			}
		}
	}

	roundRates(sazka, columnIds, colNumber, complementColumns);
}

function roundRates (betId, columnIds, colNumber, complementColumns){
	if (columnIds.length == 2) {
		var ratio = $("#vyhernost_ctrl_" + betId).val();

		var rate = $("#sazka\\[" + betId + "\\]\\[sloupec\\]\\[" + columnIds[(colNumber-1)] + "\\]").val();
	 
		var rateNumber ;

		if (colNumber == 1){
			rateNumber = 2;
		}
		else {
			rateNumber = 1;
		}

		$.get("/?section=344", {ratio: ratio, rate: rate, rateNumber: rateNumber}, function(data) {
			if (data != "") {
				$("#sazka\\[" + betId + "\\]\\[sloupec\\]\\[" + columnIds[(rateNumber-1)] + "\\]").val(data);
				SetVyhernost(betId, complementColumns);
			}
			}
		);
}
}

function alternateCss (jObj, cssProperty, altValue, times, interval) {
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
}

function calcLiability(betId, complementColumns) {
	var liab = 0;
	for(var vl in vyhernost[betId]) {
		if (complementColumns[vl])
			continue;
		rate = $('#sazka\\['+betId+'\\]\\[sloupec\\]\\['+vl+'\\]').val();
		if (rate != 0 && !isNaN(rate)) {
			x = (1/rate);
			liab = liab + x;
		}
	}
	return liab.toFixed(2);
}

/*Vyhernosti u live betradaru*/
function SetVyhernost2(sazka,event_id){

      var status = true;
      var vyh = 0;

      for(var vl in vyhernost[sazka]){;

         var ob = new getObj('live_event_bets_'+ event_id +'_'+ sazka +'_'+vl);
         if(ob.obj.value.match(/,+/g)!=null)ob.obj.value = ob.obj.value.replace(/,+/g,".")
         if(isNaN(parseFloat(ob.obj.value)) || parseFloat(ob.obj.value) == 0) status = false;
         else vyh += (1/parseFloat(ob.obj.value));


      }


    /*  for(var vl in vyhernost[sazka]){

        var ob = new getObj('pravdepodobnost_'+sazka+'_'+vl);
        var ob2 = new getObj('live_event_bets_'+ event_id +'_'+ sazka +'_'+vl);

        ob.obj.value = ( 1/(parseFloat(ob2.obj.value)*vyh) ).toFixed(4);

      }*/

      var ob = new getObj('vyhernost_'+sazka);
      ob.obj.value = vyh.toFixed(2);


    }

/*Kombinace*/

function ReturnComb(ob,v,sazka,platna_od,platna_do,text,risk){

        window.opener.document.getElementById(ob).value = v;

        if(window.opener.document.getElementById('sazka['+sazka+'][platna_od]'))
          window.opener.document.getElementById('sazka['+sazka+'][platna_od]').value = platna_od;
        if(window.opener.document.getElementById('sazka['+sazka+'][platna_do]'))
          window.opener.document.getElementById('sazka['+sazka+'][platna_do]').value = platna_do;
        if(window.opener.document.getElementById('sazka['+sazka+'][text]'))
          window.opener.document.getElementById('sazka['+sazka+'][text]').value = text;
        if(window.opener.document.getElementById('sazka['+sazka+'][risk]'))
          window.opener.document.getElementById('sazka['+sazka+'][risk]').value = risk;

        self.close();
}

function ReturnComb2(ob,v,multiple,sazka){


        if(multiple !=1){

         var ret = '';
         for(var x=0;x<comb_ch_ar.length;x++){

          if(comb_ch_ar[x] == 0) continue;

          ret += comb_ch_ar[x]+';';

        }
        window.opener.document.getElementById(ob).value += ret;

       /* if(sazka != 0){
         window.opener.document.getElementById('sazka['+sazka+'][text]').value = comb_ch_ar_text[0];
         window.opener.document.getElementById('sazka['+sazka+'][platna_od]').value = comb_ch_ar_od[0];
         window.opener.document.getElementById('sazka['+sazka+'][platna_do]').value = comb_ch_ar_do[0];
         window.opener.document.getElementById('sazka['+sazka+'][risk]').value = comb_ch_ar_risk[0];
        }*/

        }else{

          window.opener.$("input[name*='[kombinace_jine]']").each(function(xx) {

                   if(comb_ch_ar[xx] != 0 && typeof comb_ch_ar[xx] !='undefined') this.value = this.value +";"+ comb_ch_ar[xx];

           });
            /*window.opener.$("input[name*='[platna_od]']").each(function(xx) {

                   if(comb_ch_ar_od[xx] != 0 && typeof comb_ch_ar_od[xx] !='undefined') this.value=comb_ch_ar_od[xx];

           });
            window.opener.$("input[name*='[platna_do]']").each(function(xx) {

                   if(comb_ch_ar_do[xx] != 0 && typeof comb_ch_ar_do[xx] !='undefined') this.value=comb_ch_ar_do[xx];

           });
            window.opener.$("input[name*='[text]']").each(function(xx) {

                   if(comb_ch_ar_text[xx] != 0 && typeof comb_ch_ar_text[xx] !='undefined') this.value=comb_ch_ar_text[xx];

           });
           window.opener.$("input[name*='[risk]']").each(function(xx) {

                   if(comb_ch_ar_risk[xx] != 0 && typeof comb_ch_ar_risk[xx] !='undefined') this.value=comb_ch_ar_risk[xx];

           });*/

        }
        self.close();
}


  /*function SetComb(t,id){

     if(t.checked) comb_ch_ar[comb_ch_ar.length] = id
     else{

          for(var x=0;x<comb_ch_ar.length;x++){
            if(comb_ch_ar[x] == id) comb_ch_ar[x] = 0;
          }

     }


  }*/

   /*#######Zobrazuje a skryva pole pro zadani poctu sloupcu u podtypu#########*/
function SloupecRadek(t,sib){

       var par = t.parentNode;

       if(t.checked){

        var el = document.createElement('input');
        el.type = 'text';
        el.id = sib;
        el.setAttribute('name',sib);
        el.value = 1;
        el.setAttribute('className','sinput2');
        par.appendChild(el);

       }else{

        par.removeChild(document.getElementById(sib))

       }

}

/*#######Zobrazuje a skryva sloupce pro sazky#########*/
function EditSloupce(akce){

       var radek = new getObj('rows');

       if(akce==1 && (aktSloupec+1) <= maxSloupec){

         aktSloupec++;

         var el = document.createElement('div');
         el.id = 'sl_'+aktSloupec;
         el.innerHTML = '';
         el.innerHTML = "<input type='text' class='sinput3' name='newpodtyp[sloupec]["+aktSloupec+"]'> <a href='javascript:openWin(\"ciselnik.php\",\"newpodtyp[sloupec]["+aktSloupec+"]\",\"preklady\",400,300);void(0);'><img src='/_clip/translate.gif' alt='Překladový slovník' class='img' /></a>";
         radek.obj.appendChild(el);


       }
       else if(akce==-1 && (aktSloupec-1) >= minSloupec){

        var ob = new getObj('sl_'+aktSloupec);

        radek.obj.removeChild(ob.obj);

        aktSloupec--;

       }

}


/*function JSError(text,url,radek){

 if(document.getElementsByTagName || !document.createElement){

       var tit = document.getElementsByTagName("head");

        if(o = document.getElementById('old')) tit[0].removeChild(o);

        var sc = document.createElement('script');
        //document.write("https://"+host+"/jserror.php?errortext="+escape(text)+"&errorurl="+escape(url)+"&errorradek="+radek);return;
        sc.src = "https://"+host+"/jserror.php?errortext="+escape(text)+"&errorurl="+escape(url)+"&errorradek="+radek;
        sc.id = "old";
        sc.defer = "true";
        sc.type = "text/javascript";
        tit[0].appendChild(sc);

 }


 return false;

}*/

function Loading(){
 var ob = new getObj('load');
 ob.style.display = "none";
}

/*#######Objekty podle podpory browseru name nebo id#########*/
function getObj(name)
{

  if (document.getElementById)
  {
    if(!document.getElementById(name)) return false;
    this.obj = document.getElementById(name);
    this.style = document.getElementById(name).style;
  }
  else if (document.all)
  {
    if(!document.all[name]) return false;
    this.obj = document.all[name];
    this.style = document.all[name].style;
  }

  else if (document.layers)
  {
    this.obj = getObjNN4(document,name);
    this.style = this.obj;
  }
}

function getObjNN4(obj,name)
{
    var x = obj.layers;
    var foundLayer;
    for (var i=0;i<x.length;i++)
    {
        if (x[i].id == name)
            foundLayer = x[i];
        else if (x[i].layers.length)
            var tmp = getObjNN4(x[i],name);
        if (tmp) foundLayer = tmp;
    }
    return foundLayer;
}

/*detekce browseru*/
function Browser()
{
    var agent=navigator.userAgent.toLowerCase();
    var name=navigator.appName;
    this.version=parseInt(navigator.appVersion.substring(0,1));

    this.gecko = (agent.indexOf('gecko') != -1);
    /*Netscape*/
    this.ne  = (agent.indexOf('mozilla')!=-1 && name=="Netscape");
    this.ne2 = (this.ne && (this.version == 2));
    this.ne3 = (this.ne && (this.version == 3));
    this.ne4 = (this.ne && (this.version == 4));
    this.ne4up = (this.ne && (this.version >= 4));
    this.ne6 = (this.ne && (this.version == 5));
    this.ne6up = (this.ne && (this.version>= 5));
    /*Explorer*/
    this.ie=((agent.indexOf("msie") != -1) && name=="Microsoft Internet Explorer");
    this.ie3=(this.ie && (this.version<4));
    this.ie4=(this.ie && (this.version == 4) && (agent.indexOf("msie 4")!=-1));
    this.ie4up=(this.ie && ((this.version>4) || (agent.indexOf("msie 5")!=-1) || (agent.indexOf("msie 6")!=-1)));
    this.ie5_0=(this.ie && (this.version == 4) && (agent.indexOf("msie 5.0")!=-1));
    this.ie5_5=(this.ie && (this.version == 4) && (agent.indexOf("msie 5.5")!=-1));
    this.ie5_0up=(this.ie && !this.ie4 && !this.ie3);
    this.ie5_5up=(this.ie && !this.ie5_0 && !this.ie4 && !this.ie3);
    this.ie6=(this.ie && (this.version == 4) && (agent.indexOf("msie 6.0")!=-1));
    this.ie6up=(this.ie && !this.ie5_5 && !this.ie5_0 && !this.ie4 && !this.ie3);
    /*Opera*/
    this.op=(agent.indexOf("opera") != -1);
    this.op2 =(agent.indexOf("opera 2") != -1 || agent.indexOf("opera/2") != -1);
    this.op3= (agent.indexOf("opera 3") != -1 || agent.indexOf("opera/3") != -1);
    this.op4= (agent.indexOf("opera 4") != -1 || agent.indexOf("opera/4") != -1);
    this.op5 = (agent.indexOf("opera 5") != -1 || agent.indexOf("opera/5") != -1);
    this.op5up = (this.op && !this.op3 && !this.op4 && !this.op5);
    this.op7= (name.indexOf('opera')!=-1 && document.getElementById && document.childNodes);
    /*Firefox*/
    this.ffox=(agent.indexOf("firefox") != -1);

}

/*odeslani nicku a hesla na kontrolu*/
function AdminLogin(){
/*
      if(document.getElementsByTagName || !document.createElement){

       var tit = document.getElementsByTagName("head");

       if(o = document.getElementById('old')) tit[0].removeChild(o)

        superb = (document.getElementById('superb').checked == true?"&superb=1":"");

        var sc = document.createElement('script');
        sc.src = protocol+host+"/index.php?nick="+escape(document.getElementById('nick').value)+"&pass="+escape(document.getElementById('password').value)+superb;
        sc.id = "old";
        sc.defer = "true"
        sc.type = "text/javascript"
        tit[0].appendChild(sc);

      }else{

        alert("Your browser is not supported.");

      }
*/

	$.post(protocol + host + '/index.php', { 'nick' : $('#nick').val(), 'pass' : $('#password').val()},
		function(data) {
			$('#password').val('');
			if ('OK' == data.status)
				document.location.href = data.location;
			else
				alert(data.message);
		},
		'json'
	);

}



/*Otevreni nove sekce*/
function newSection(s){

 var obj = new getObj('newsection');
 var obj2 = new getObj('sec_id');

 obj2.obj.value = parseInt(s);

 obj.style.display = "";

}

/*Pokud se zvoli superadmin vsechny pole se zaskrtnou*/
function setPravo(checked){

  for(var x=0;x<pravo.length;x++){
   if(checked) bool = true; else bool = false;
   if(checked)  document.forms[0].elements['pravo['+pravo[x]+'][read]'].checked = bool;
    document.forms[0].elements['pravo['+pravo[x]+'][read]'].readonly = bool;
   if(checked)document.forms[0].elements['pravo['+pravo[x]+'][update]'].checked = bool;
    document.forms[0].elements['pravo['+pravo[x]+'][update]'].readonly = bool;
   if(checked)document.forms[0].elements['pravo['+pravo[x]+'][delete]'].checked = bool;
    document.forms[0].elements['pravo['+pravo[x]+'][delete]'].readonly = bool;
  }

}


/*Zobrazi nebo skryje objekt*/
function displayObj(obj){

 if(obj.style.display == 'none')
   obj.style.display = '';
 else
   obj.style.display = 'none';

}

/*Vypnuti a zapnuti wysiwyg editoru pro jednitlive jazyky v Novinkach*/
function PRLang(ids,id){

    var ob = new getObj('data_b_'+id);

    var data = new Array();

    for(var x=0;x<ids.length;x++){

      data[x] = 'data_'+ids[x];

      var obb = new getObj('data_b_'+ids[x]);
      obb.style.fontWeight = 'normal';
      obb.style.border = '1px solid  #515B73';
    }

    ob.style.fontWeight = 'bold';
    ob.style.border = '2px solid black';

    show('data_'+id,data);

}

/*Vypnuti a zapnuti wysiwyg editoru pro jednitlive jazyky v Novinkach*/
function NewsLang(ids,id){


    var ob = new getObj('data_b_'+id);

    var data = new Array();

    for(var x=0;x<ids.length;x++){

      data[x] = 'data_'+ids[x];

      var obb = new getObj('data_b_'+ids[x]);
      obb.style.color = '#515B73';
      obb.style.fontWeight = 'normal';
      obb.style.border = '1px solid  #515B73';

    }

    ob.style.color = 'black';
    ob.style.fontWeight = 'bold';
    ob.style.border = '2px solid black';

    show('data_'+id,data);

}

/*Zobrazi ob a zakryje pole objektu poleObj*/
function show(ob,poleObj){

 var ob1 = new getObj(ob);


 for(var x=0;x<poleObj.length;x++){
  var obj2 = new getObj(poleObj[x]);

  obj2.style.display = 'none';
 }

 ob1.style.display = 'block';

}


/*Prehazovaci menu*/
function PrehodSelect(odkud,kam)
{
var od_text = new Array();
var doo = new Array();
var od = new Array();
var doo_text = new Array();

for(x=0;x<kam.obj.length;x++){
 doo[x]=kam.obj.options[x].value;
 doo_text[x]=kam.obj.options[x].text;
}

for(i=0;i<odkud.obj.length;i++){

 if(odkud.obj.options[i].selected){
   doo[doo.length]=odkud.obj.options[i].value;
   doo_text[doo_text.length]=odkud.obj.options[i].text;
 }else{
   od[od.length]=odkud.obj.options[i].value;
   od_text[od_text.length]=odkud.obj.options[i].text;
 }

}

kam.obj.length=0;
odkud.obj.length=0;

od.sort();
doo.sort();

for(var y=0;y<od.length;y++){

     o = new Option();
     o.value=od[y];
     o.text=od_text[y];
     odkud.obj.options[y]=o;

}

for(var p=0;p<doo.length;p++){

     o=new Option();
     o.value=doo[p];
     o.text=doo_text[p];
     kam.obj.options[p]=o;

}

}

/*vybrani vsech polozek selectu pokud je checkbox zaskrtbuty*/
function SelectAll(ob,t){
                       if(!t || t.checked){
                        for(var x=0;x<ob.obj.length;x++){
                         ob.obj.options[x].selected = true;
                        }
                       }else{
                        for(var x=0;x<ob.obj.length;x++){
                         ob.obj.options[x].selected = false;
                        }
                       }
}

function openWin(page,obj,cis_id,h,w,url){

  if(!url) url = "";

  var okno = window.open(page+'?obj='+obj+'&width='+parseInt(w)+'&cis_id='+cis_id+url,'Číselník','scrollbars=yes,width='+parseInt(w)+',height='+parseInt(h)+',top=0,left='+(parseInt(screen.availWidth)-300));

}

function openCatalog(page,obj,w,h,url,obj2,optPar,eventId){

  if(!url) url = "";

  var okno = window.open(page+'&opener='+obj+'&opener2='+obj2+'&width='+parseInt(w)+'&cis_id='+url+'&opt_par='+optPar+'&event_id='+eventId,'Číselník','scrollbars=yes,width='+parseInt(w)+',height='+parseInt(h)+',top=0,left='+(parseInt(screen.availWidth)-300));

}

/**/
function NavigatorAlert(){

  /*var bw = new Browser();

  if(!bw.ie6up && !bw.ffox) alert("[INFO] \nPage is optimized for Internet Explorer v 6 and higher and Firefox. \n You could have problems with your work \n We recommend to use IE v 6 or higher or Firefox");
	*/
}


/**Fce pripravi promenne pro wysiwig
*
*param array node_id  id elementu ktery bude wisiwig
*/
function SetWysiwig(node_id){

 node_id = node_id.join(",");

     tinyMCE.init({
        mode : "exact",
        elements : node_id,
        theme : "advanced",
        plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable",
        theme_advanced_buttons1_add_before : "save,newdocument,separator",
        theme_advanced_buttons1_add : "fontselect,fontsizeselect",
        theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
        theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
        theme_advanced_buttons3_add_before : "tablecontrols,separator",
        theme_advanced_buttons3_add : "emotions,iespell,flash,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_path_location : "bottom",
        content_css : "example_full.css",
        force_p_newlines : true,
        plugin_insertdate_dateFormat : "%Y-%m-%d",
        plugin_insertdate_timeFormat : "%H:%M:%S",
        extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
        external_link_list_url : "example_link_list.js",
        external_image_list_url : "example_image_list.js",
        flash_external_list_url : "example_flash_list.js",
        theme_advanced_resize_horizontal : false,
        theme_advanced_resizing : true,
        convert_urls : false
    });

}

/**Fce pripravi promenne pro wysiwig
*
*param array node_id  id elementu ktery bude wisiwig
*/
function SetWysiwig2(node_id){

 node_id = node_id.join(",");

     tinyMCE.init({
        mode : "exact",
        elements : node_id,
        theme : "advanced",
        width: "680px",
        height: "400px",
        plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,jimg,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable",
        theme_advanced_buttons1_add_before : "save,newdocument,separator",
        theme_advanced_buttons1_add : "fontselect,fontsizeselect",
        theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
        theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
        theme_advanced_buttons3_add_before : "tablecontrols,separator",
        theme_advanced_buttons3_add : "emotions,iespell,flash,jimg,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_path_location : "bottom",
        content_css : "example_full.css",
        force_p_newlines : true,
        plugin_insertdate_dateFormat : "%Y-%m-%d",
        plugin_insertdate_timeFormat : "%H:%M:%S",
        extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
        external_link_list_url : "example_link_list.js",
        external_image_list_url : "example_image_list.js",
        flash_external_list_url : "example_flash_list.js",
        theme_advanced_resize_horizontal : false,
        theme_advanced_resizing : true,
        convert_urls : false
    });

}

/**Fce pripravi promenne pro wysiwig
*
*nastavi vsechny textareas na strance jako wysiwig
*/
function SetWysiwigTiny(){


    tinyMCE.init({
        theme : "advanced",
        mode : "textareas",
        content_css : "example_advanced.css",
        plugins : "table",
        extended_valid_elements : "a[href|target|name]",
        theme_advanced_toolbar_location : "top",
        theme_advanced_buttons3_add_before : "tablecontrols",
        width: "500px",
        height: "420px",
        entity_encoding : "raw",
        theme_advanced_disable : "strikethrough,help,removeformat,sup,sub",
        debug : false,
        convert_urls : false
    });

}

/*Aktualizace stranky u online sledovani*/
  function ReloadPage(section){

     ReloadPage.sec = 40;
     ReloadPage.section = section;
     this.limitObj = null;
     this.second;
  }

  ReloadPage.prototype.GetRestTime = function(){

    this.limitObj = setInterval('ReloadPage.Run()',1000);

  }

  ReloadPage.Run = function(){

    ReloadPage.sec--;
    var ob = new getObj('second');
    ob.obj.innerHTML = ReloadPage.sec;
    if(ReloadPage.sec <= 0 ){

     ReloadPage.sec = ReloadPage.second
     RemoteJavascript(ReloadPage.section);

    }

  }

  ReloadPage.prototype.SetSec = function(sec){

   ReloadPage.second = parseInt(sec);

  }

/*odeslani nicku a hesla na kontrolu*/
function RemoteJavascript(section){

      if(document.getElementsByTagName || !document.createElement){
       var tit = document.getElementsByTagName("head")
       if(o = document.getElementById('old')) tit[0].removeChild(o)

        var sc = document.createElement('script');
        sc.src = "remote.php?section="+section;
        sc.id = "old";
        sc.defer = "true"
        sc.type = "text/javascript"
        tit[0].appendChild(sc);
      }else{

        alert("Your browser is not supported.");

      }

}


/*zmena souboru pri vkladani dokumentu k uzivateli*/
function ChangeFile(image,name,n){

 for(var x=0;x<8;x++){

  var ob = new getObj("obr"+x);
  ob.obj.src = imageFile+fileimg[x];

 }

 var ob = new getObj("obr"+n);

 if(image.indexOf(name+"_neaktivni.gif") != -1)
   ob.obj.src = imageFile+name+"_aktivni.gif";
 else
   ob.obj.src = imageFile+name+"_neaktivni.gif";

 document.forms['filesend'].elements['file'].value = name;

}

/*Urci jake checkboxy maji byt disabled*/
function Checkbox(tr,ar){


     for(var x=0;x<ar.length;x++){

        if(tr){
          document.forms[0].elements[ar[x]].disabled = false;
        }else{
          document.forms[0].elements[ar[x]].disabled = true;
          document.forms[0].elements[ar[x]].checked = false;
        }

     }



   }

   /*Prace s wysiwigem a obrazky*/
     function SetAlt(n){
       altlang = n;
     }

     function ChooseImage(img,im_id){
       var alttext = '';
       if(altlang && alt[im_id][altlang]) alttext = alt[im_id][altlang];
       var img = "<img src=\""+img+"\" alt=\""+alttext+"\" class=\"img\" />";

       IMGHtml = img;

       insertSomething();

     }





  /*Stanoveni vysledku*/
    var resart = new Object();
    function SetRes(sazka_id,sloupec_id,t){

     sazka_id =  sazka_id.toString();
     //sloupec_id =  sloupec_id.toString();

     var del = false;

     if(!resart[sazka_id]) {resart[sazka_id] = new Array();}


       for(var x=0;x<resart[sazka_id].length;x++){

         if(resart[sazka_id][x] == sloupec_id) {del = true;delete resart[sazka_id][x];}

       }

        if(del == false){

          resart[sazka_id][resart[sazka_id].length] = sloupec_id;



          /*document.getElementById("td_"+sazka_id+"_"+sloupec_id).setAttribute("className","kurz chbet");*/
          $("#td_"+sazka_id+"_"+sloupec_id).attr("class","kurz chbet");

        }else {

          /*document.getElementById("td_"+sazka_id+"_"+sloupec_id).setAttribute("className","kurz");*/
          $("#td_"+sazka_id+"_"+sloupec_id).attr("class","kurz");

      }
    }


    function SetResSubmit(){

    var vrat = "";

       for(vl in resart){

         for(var x=0;x<resart[vl].length;x++){

           vrat += vl+":"+resart[vl][x]+";";

         }

      }

      document.getElementById('result_set_hid').value = vrat;


    }




    /*Konec stanoveni vysledku*/

    function FindUd(sid){

if(parseInt(sid) != 0 && !isNaN(sid)){

    $.get('index.php', { superb:1,section:'b6',sportid:sid },
       function(data){

         $('#udalost_container').html(data);

  });

}

}


function loadext(t,f) {
        var s=document.createElement((t=='js')?'script':'style');
        s.setAttribute('src',f);
        document.getElementsByTagName('body')[0].appendChild(s);
}




function orderUsersBy(field, direction){

	$('#formUsersFilter input[type=hidden][name=orderBy]').attr('value', field);
	$('#formUsersFilter').submit();
}

function checkAll() {
}

function CheckAll(elm, checkboxSelector){
	if(checkboxSelector === undefined) {
		checkboxSelector = ".checkboxBets"; 
	}
	$(checkboxSelector).attr( { checked : elm.checked } );
	//var allcheckbox = $("input[@name='sazka[all]']");
	//for(var x=0;x<allcheckbox.length;x++) {
		//if(allcheckbox[x].getAttribute("type") == "checkbox" && allcheckbox[x].getAttribute("name").indexOf("sazka[all]")!=-1) {
			//if(allcheckbox[x].checked)
				//allcheckbox[x].checked=false;
			//else
				//allcheckbox[x].checked = true;
		//}
	//}
}
$(document).ready(function() {
	$(".bet-toggler").click(function() {
		if ($(this).html()=='+')
			$(this).html('-');
		else $(this).html('+');
		 var myid = $(this).attr("id").substring(1);
		 $('#bet-more' + myid).toggle();
	});
	$(".all-bet-toggler").click(function() {
		$('.bet-more').toggle('fast');
	});
});

function sameAsBranchBankBalanceForm() {
	$('#BranchBankForm_balance #bankName').val($('#BranchBankForm_provision #bankName').val());
	$('#BranchBankForm_balance #bankBranch').val($('#BranchBankForm_provision #bankBranch').val());
	$('#BranchBankForm_balance #accountPrefix').val($('#BranchBankForm_provision #accountPrefix').val());
	$('#BranchBankForm_balance #accountNumber').val($('#BranchBankForm_provision #accountNumber').val());
	$('#BranchBankForm_balance #bankCode').val($('#BranchBankForm_provision #bankCode').val());
	$('#BranchBankForm_balance #currency').val($('#BranchBankForm_provision #currency').val());
	$('#BranchBankForm_balance #note').val($('#BranchBankForm_provision #note').val());
}

function selectOptgroupBySport(elemSelectSport, elemSelectEvents, elemSelectRegions) {
	var s = (undefined === elemSelectSport ? $('#sport_opt') : $(elemSelectSport));
	var e = (undefined === elemSelectEvents ? $('#udalost') : $(elemSelectEvents));
	var o = (undefined === elemSelectRegions ? $('#oblast') : $(elemSelectRegions));

	e.children().remove();
	$('#working').show();
	e.load(
		'/?section=140',
		{ sportId: s.val(), oblastId: o.val() },
		function() { $('#working').hide(); }
	);
}

function loadEventOptsForSport(elemSport, elemEvents, addAllEventsOpt, eventValueCol) {
	var s = (undefined === elemSport ? $('#sport_opt') : $(elemSport));
	var e = (undefined === elemEvents ? $('#udalost') : $(elemEvents));
	if (undefined === addAllEventsOpt)
		addAllEventsOpt = true;
		
	var data = {
		'ajax' : 'sportEvents',
		'sportId' : s.val(),
	};
	if(addAllEventsOpt !== undefined)
		data['addAllEventsOpt'] = addAllEventsOpt;
	if(eventValueCol !== undefined)
		data['eventValueCol'] = eventValueCol;
	
	e.children().remove();
	if ($('#formSazkyFiltr').length && $('.bet-types').length) { 
		loadTypeOpts([]);
		e.css('visibility', 'hidden');
		e.before('<img src="_img/ajax-loader.gif" alt="working" style="position:absolute;">');
	} else {
		$('#working').show();
	}
	e.load(
		'/?section=146',
		data,
		function() { 
			if ($('#formSazkyFiltr').length && $('.bet-types').length) {
				e.prev('img').remove();
				e.css('visibility', 'visible');
			} else 
				$('#working').hide(); 
		}
	);
}

var loadingTypeOpts = null;
var lastSelectedTypes = null;
function loadTypeOpts(selectedTypes) {
	elemFilter = $('#formSazkyFiltr');
	elemTypes = $('.bet-types');
	if (undefined === selectedTypes) {
		if (loadingTypeOpts !== null) {
			selectedTypes = lastSelectedTypes;
		} else {
			selectedTypes = [];
			$.each(elemTypes.find('input'), function(key, element) {
				if (element.checked) {
					if (element.value == 0) {
						selectedTypes = [];
						return;
					}
					
					selectedTypes.push(parseInt(element.name.substring(4, element.name.length - 1)));
				}
			});
		}
	}
	lastSelectedTypes = undefined === selectedTypes ? [] : selectedTypes;
	selectedTypes = ($.inArray(0, lastSelectedTypes) != -1 ? [] : lastSelectedTypes);
	
	if (loadingTypeOpts !== null) {
		loadingTypeOpts.abort();
		loadingTypeOpts = null;
	} else {
		elemTypes.css('height', elemTypes.height() + 4);
		elemTypes.children().remove();
		elemTypes.css('visibility', 'hidden');
		elemTypes.before('<img src="_img/ajax-loader.gif" alt="working" style="position:absolute;">');
	}
	var data = {
		'ajax' : 'types',
		'form' : elemFilter.serialize(),
		'selected' : selectedTypes,
	};
	
	loadingTypeOpts = $.ajax({
		type: "POST",
		url: '/?section=146',
        data: data,
        success: function(data) {
        	loadingTypeOpts = null;
        	elemTypes.html(data);
        	$(function() {
				$('#chAll').click (function () {
					var checkedStatus = this.checked;
					$('.betTypeChck').each(function () {
						$(this).prop('checked', checkedStatus);
					});
				});
				$('.betTypeChck').click(function () {
					setChckAll(false);
					checkAllIfNothingChecked();
				});
				setChckAll(true);
			});
			elemTypes.prev('img').remove();
			elemTypes.css('height', 'auto');
			elemTypes.css('visibility', 'visible');
        }
     });
}

function SetResultColumn(betId,columnId) {

	var resultsAdd = new Array();
	var resultsDel = new Array();

	var currVal = $("#resultColumns\\["+betId+"\\]").val();
	
	var current = new Array;

	if (currVal) {
		current = currVal.split(';');
		current.sort();

		for(var i in current) {
			if (current[i]!=columnId)
				resultsAdd.push(current[i]);
			else
				resultsDel.push(current[i]);
		}
		
		if (!inArray(columnId,resultsDel))
			resultsAdd.push(columnId);
		
		resultsAdd.sort();
		resultsDel.sort();

	}
	else
		resultsAdd.push(columnId);

	$("#resultColumns\\["+betId+"\\]").val(resultsAdd.join(';'));
	
	for(var i in resultsAdd) {
		$("#sazka\\["+betId+"\\]\\[sloupec\\]\\["+resultsAdd[i]+"\\]").css({ 'background-color':'green', 'color' : '#FFF'});
	}
	for(var i in resultsDel) {
		$("#sazka\\["+betId+"\\]\\[sloupec\\]\\["+resultsDel[i]+"\\]").css({ 'background-color':'#FFF', 'color' : '#000'});
	}
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(typeof haystack[i] == 'object') {
            if(arrayCompare(haystack[i], needle)) return true;
        } else {
            if(haystack[i] == needle) return true;
        }
    }
    return false;
}

