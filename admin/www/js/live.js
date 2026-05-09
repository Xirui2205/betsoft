var method = "POST";
var ajaxurl    = "";
var dataToSend    = "";
var loadingText    = "";
var minuteLimit = null;
var betminuteLimit = null;
var actMinute = 1;
var stopMinute = false;
var period = 0;
var deactive_action = false;
var bet_act_time = 120000; //pocet sekund pro aktualizaci
var bet_act_time_act = 120;
var stop_reload_bet_info = false;
var bet_sloupec = new Object();
var shdb = 0;
var court = 'tennis_field_screen';
var counter_type = 1;  //typ pocitadla 1 body;2 sety;
var rate_def_act = new Object();
var rate_def_ob = new Object();
var rate_def_sl = new Object();
var tiebreak = 0;


/*Hlavni funkce ktera handluje jednotlive akce*/
function LiveAction(act){

   $('#live_stav_action').html('');


    if(act == "all"){
      loadingText = "Inicializují se data";
      ajaxurl = "work=1&event="+event_id;
    }

    else if(act == "minute"){
      loadingText = "Aktualizace minut na serveru";
      ajaxurl = "work=2&minute="+parseInt(actMinute)+"&event="+event_id;
    }

    else if(act == "begin"){
      stop_reload_bet_info = false;
      loadingText = "Startuji zápas";
      ajaxurl = "work=3&event="+event_id;
      $('#stopbutton').css('color','white');
    }

    else if(act == "stop"){
      stop_reload_bet_info = true;
      loadingText = "Zastavuji live sázku";
      ajaxurl = "work=4&event="+event_id;
      $('#stopbutton').css('color','red');
    }

    else if(act == "period"){
      loadingText = "Nastavuji stav zápasu";
      ajaxurl = "work=5&period="+period+"&event="+event_id;
    }

    else if(act == "updatei"){
      loadingText = "Aktualizuji ...";
      ajaxurl = "work=6&event="+event_id;
    }

    else if(act == "act_bet"){
      loadingText = "Aktualizuji sázku";
      ajaxurl = "work=7&event="+event_id;
    }

    else if(act == "stop_bet"){
      loadingText = "Zastavuji sázku";
      ajaxurl = "work=8&event="+event_id;
    }

    else if(act == "close_bet"){
      loadingText = "Uzavírám sázku";
      ajaxurl = "work=11&event="+event_id;
    }

    else if(act == "act_bet_info"){
      loadingText = "Stahuji informace k sázkám";
      ajaxurl = "work=9&event="+event_id;
    }

    else if(act == "info_select"){
      loadingText = "Nahrávám informace k zápasu";
      ajaxurl = "work=10&event="+event_id;
   }

    else if(act == "delete_info"){
      loadingText = "Mažu info";
      ajaxurl = "work=12&event="+event_id;
    }

    else if(act == "delete_temp"){
      loadingText = "Mažu template";
      ajaxurl = "work=13&event="+event_id;
    }

    else if(act == "set_court"){
      loadingText = "Nastavuji kurt";
      ajaxurl = "work=14&event="+event_id+"&court="+court;
    }

    else if(act == "first_service"){
      loadingText = "Nastavuji první podání";
      ajaxurl = "work=15&event="+event_id;
    }

    else if(act == "set_num"){
      loadingText = "Nastavuji počet setů";
      ajaxurl = "work=16&event="+event_id;
    }

    else if(act == "runAllBet"){
      loadingText = "Spouštim sázky";
      ajaxurl = "work=17&event="+event_id;
      $('#stopbutton').css('color','white');
    }

    else if(act == "tiebreak"){
      loadingText = "Aktualizuji...";
      ajaxurl = "work=18&event="+event_id;
    }

    else if(act == "permanent_info"){
      loadingText = "Aktualizuji...";
      ajaxurl = "work=19&event="+event_id;
    }

    $.ajax({
     type: method,
     url: "live.php?"+ajaxurl,
     data: dataToSend,
     dataType: "json",
     beforeSend:function(){
      $('body').css('cursor','wait');
      $('#live_stav_action').show();
      $('#live_stav_action').append(loadingText);
     },
     success: function(msg){

      $('body').css('cursor','auto');

      if(typeof msg['error'] != 'undefined'){

         $('#live_stav_action').text("Nastala chyba: "+msg.error);

      }else{

        $('#live_stav_action').hide("slow");

        for(var vl in msg){
         if(vl=='js_dynamic')eval(msg[vl]);
         else
         $('#'+vl).html(msg[vl]);

        }

      }

     }
   });

}


function SetChange(){

      $('#score_home').attr('value',0);
      $('#score_away').attr('value',0);

      UpdateAllInfo();


}

function SetNum(n){

 dataToSend = '';
 dataToSend += 'set_num='+parseInt(n);

 LiveAction('set_num');


}

function First_service(f){

 dataToSend = '';
 dataToSend += 'first_service='+f;

 LiveAction('first_service');

}

function SetCourt(c){

  court = c;

  LiveAction('set_court');

}

function DeleteFromTemplate(typ_id,podtyp_id){

   dataToSend = '';

   dataToSend += 'typ_id='+typ_id+'&podtyp_id='+podtyp_id;

   LiveAction('delete_temp');

}

function DeleteInfo(id){

  dataToSend = '';

  dataToSend += 'id='+id+'&';

  LiveAction('delete_info');

}

function EditInfo(id){

  dataToSend = '';

  dataToSend += 'eifno=1&id='+id+'&';
  dataToSend += 'info_select_text='+$("#special_info_text_edit_"+id).get(0).value+'&';

  LiveAction('info_select');

}

function PlusScore(obj,amt){

  if(counter_type == 1) $("#"+obj).get(0).value = parseInt($("#"+obj).get(0).value)+amt;

}

function SpecialInfo(){

  dataToSend = '';
  dataToSend += 'special_info='+$("#special_info").get(0).options[$("#special_info").get(0).selectedIndex].value+'&';
  dataToSend += 'info_select_min='+$("#special_info_min").get(0).value+'&';
  dataToSend += 'info_select_text='+$("#special_info_text").get(0).value;

  $("#special_info_min").get(0).value = '';
  $("#special_info_text").get(0).value = '';

  LiveAction('info_select');
}

function PermanentInfo(){

  dataToSend = 'permanent_info='+$("#permanent_info").get(0).value;
  LiveAction('permanent_info');
}

function StopBet(sazka_id,typ_id,podtyp_id){

  dataToSend = '';

  if(sazka_id != 0){

   dataToSend += 'sazka_id='+sazka_id+'&';

   LiveAction('stop_bet');

  }

}

function CloseBet(sazka_id,typ_id,podtyp_id){

  dataToSend = '';

  if(sazka_id != 0){

   dataToSend += 'sazka_id='+sazka_id+'&typ_id='+typ_id+'&podtyp_id='+podtyp_id;

   LiveAction('close_bet');

  }

}

function RunBet(sazka_id,typ_id,podtyp_id,sloupce){

  if(deactive_action == true) return;

  dataToSend = "";

  if(sazka_id != 0) dataToSend += 'sazka_id='+sazka_id+'&';
  if($('#bet_'+sazka_id+'_'+typ_id+'_'+podtyp_id+'_jednoducha').get(0).checked) dataToSend += 'simple=1&';
  if($('#bet_'+sazka_id+'_'+typ_id+'_'+podtyp_id+'_comb').get(0).checked) dataToSend += 'comb=1&';
  dataToSend += 'risk='+$('#bet_'+sazka_id+'_'+typ_id+'_'+podtyp_id+'_risk').get(0).value+'&';
  dataToSend += 'typ_id='+typ_id+'&';
  dataToSend += 'podtyp_id='+podtyp_id+'&';
  dataToSend += 'text='+$('#xbet_text_'+sazka_id+'_'+typ_id+'_'+podtyp_id).get(0).value+'&';

  for(var x=0;x<sloupce.length;x++){

    dataToSend += 'sloupec['+sloupce[x]+']='+$('#bet_'+sazka_id+'_'+typ_id+'_'+podtyp_id+'_sloupec_'+sloupce[x]).get(0).value+'&';

  }

  deactive_action =  true;

  LiveAction('act_bet');

  deactive_action =  false;

}

function UpdateAllInfo(){

  dataToSend = "";

  dataToSend += "score_home="+$("#score_home").attr('value')+"&";
  dataToSend += "score_away="+$("#score_away").attr('value')+"&";
  dataToSend += "start_at="+$("#start_at").attr('value')+"&";
  dataToSend += "l_minute="+parseInt($("#l_minute").attr('value'))+"&";
  dataToSend += "win="+$("#win").attr('value')+"&";
  dataToSend += "limit_rate="+$("#prove_rate").attr('value')+"&";
  dataToSend += "limit_bet="+$("#prove_bet").attr('value')+"&";

  actMinute = parseInt($("#l_minute").attr('value'));
  $('#live_minute').html(actMinute+' min.');

  for(var x=0;x<periodAr.length;x++){

    dataToSend += periodAr[x]+"="+parseInt($('#'+periodAr[x]).attr('value'))+"&";

  }

  LiveAction('updatei');

}
function UpdateTiebreak(){
  dataToSend = "tiebreak="+tiebreak;
  LiveAction('tiebreak');
}


function SetPeriod(p){

  if(p==13 || p==2){

    stopMinute=true;

  }else{

    stopMinute=false;

  }

   period = p;
   LiveAction('period');

}

function SecondClock(){

  /*if(stop_reload_bet_info == true) return;

  $("#bet_sec").text(bet_act_time_act);

  bet_act_time_act--;

  if(bet_act_time_act<0) {bet_act_time_act = 120; LiveAction('act_bet_info');}
  */


}
function RunBetLimit(){

  /*if(betminuteLimit == null) {

   betminuteLimit = setInterval("RunBetLimit()",bet_act_time);
   setInterval("SecondClock()",1000);
  }else{

    if(stop_reload_bet_info == true) return;

    bet_act_time_act = 120;

    LiveAction('act_bet_info');

  }*/

   LiveAction('act_bet_info');
}

function SRMinute(t){

  if(stopMinute==false){
    t.value='Run minute';
    stopMinute=true;
  }else{
   t.value='Stop minute';
   stopMinute=false;
  }

}

function ClearRunMinute(){

  clearInterval(minuteLimit);

  actMinute = $("#l_minute").attr('value');
  minuteLimit = null;
  $('#live_minute').html(actMinute+' min.');
  $("#special_info_min").attr("value",actMinute);
   LiveAction('minute');
  RunMinute();

}

function RunMinute(){


   if(minuteLimit == null) {minuteLimit = setInterval("RunMinute()",60000);}
   else{

     if(stopMinute==false){
      actMinute++;
      $("#l_minute").attr('value',actMinute);

      $('#live_minute').html(actMinute+' min.');
      $("#special_info_min").attr("value",actMinute);
      LiveAction('minute');
     }

   }



}


function SHDB(n){

  if(n == 3) n = shdb;

  if(n==0){
     $("div[name='shdb']").show();
  }else
    $("div[name='shdb']").hide();


  shdb = n;

}

function RateDef(i,r,typ,podtyp,sazka_id,ik){



   if(i == 0) delete rate_def_act[sazka_id][typ][podtyp];
   else{

      if(typeof rate_def_act[sazka_id] == 'undefined'){
        rate_def_act[sazka_id] = new Object();

      }

      if(typeof rate_def_act[sazka_id][typ] == 'undefined'){
        rate_def_act[sazka_id][typ] = new Object();

      }

      if(typeof rate_def_act[sazka_id][typ][podtyp] == 'undefined'){
        rate_def_act[sazka_id][typ][podtyp] = new Object();
        rate_def_act[sazka_id][typ][podtyp][r] = ik;
      }

   }
}

function ChValue(action,id,precision,help,typ,podtyp,sloupec,sazka_id){


  if(counter_type == 1 || help != 1){
    if( typeof rate_def_act[sazka_id] != 'undefined' && typeof rate_def_act[sazka_id][typ] != 'undefined' && typeof rate_def_act[sazka_id][typ][podtyp] != 'undefined'){

      for(var vl in rate_def_act[sazka_id][typ][podtyp]){break;}

      var ob_length = 0;
      for(vl_id in rate_def_ob[vl]) ob_length++;

      if(action == 'plus') {

        rate_def_act[sazka_id][typ][podtyp][vl]++;

       if(ob_length == rate_def_act[sazka_id][typ][podtyp][vl] ) rate_def_act[sazka_id][typ][podtyp][vl] = 0;

      }
      else if(action == 'minus') {

       rate_def_act[sazka_id][typ][podtyp][vl]--;

       if(rate_def_act[sazka_id][typ][podtyp][vl] < 0 ) rate_def_act[sazka_id][typ][podtyp][vl] = (ob_length-1);

      }



      if(typeof rate_def_ob[vl][rate_def_act[sazka_id][typ][podtyp][vl]] == 'undefined' && action == 'plus') {rate_def_act[sazka_id][typ][podtyp][vl]--;return;}
      else if(typeof rate_def_ob[vl][rate_def_act[sazka_id][typ][podtyp][vl]] == 'undefined' && action == 'minus') {rate_def_act[sazka_id][typ][podtyp][vl]++;return;}

      for(var v = 0;v<rate_def_sl[sazka_id+'_'+typ+'_'+podtyp].length;v++){

      	 if(sazka_id == 0 && $("#bet_"+sazka_id+'_'+typ+'_'+podtyp+'_sloupec_'+rate_def_sl[sazka_id+'_'+typ+'_'+podtyp][v]).get(0).value == '') rate_def_act[sazka_id][typ][podtyp][vl] = 0;
         $("#bet_"+sazka_id+'_'+typ+'_'+podtyp+'_sloupec_'+rate_def_sl[sazka_id+'_'+typ+'_'+podtyp][v]).get(0).value = rate_def_ob[vl][rate_def_act[sazka_id][typ][podtyp][vl]][v];

      }

    }else{

     var x = 1 ;

     for(var y=0;y<precision;y++) x = x/10;

     if(precision != 0) x *= 5;

    if(isNaN((parseFloat($("#"+id).get(0).value) + x).toFixed(precision))){
      $("#"+id).get(0).value = 1;
    }
    if(action == 'plus') $("#"+id).get(0).value = (parseFloat($("#"+id).get(0).value) + x).toFixed(precision);
    else if(action == 'minus') $("#"+id).get(0).value = (parseFloat($("#"+id).get(0).value) - x).toFixed(precision);

    if($("#"+id).get(0).value <= 0){
      $("#"+id).get(0).value = 0;
    }

    }

  }
  else if(counter_type == 2 && help == 1){

    var vpole = new Array('0','15','30','40','A');
    var v = $("#"+id).get(0).value;
    var new_v = null;

    for(var i=0;i<vpole.length;i++){

      if     (vpole[i] == v && action == 'plus') {new_v = (i+1);break;}
      else if(vpole[i] == v && action == 'minus'){new_v = (i-1);break;}


    }

    if(new_v != null && new_v > -1 && new_v < 5){

       $("#"+id).get(0).value = vpole[new_v];

    }

  }


}

/*Vyhernosti u sazek*/
function SetVyhernostLive(sazka_id,typ_id,podtyp_id){

	  var status = true;
	  var vyh = 0;



	  for(var vl in bet_sloupec[sazka_id+'_'+typ_id+'_'+podtyp_id]){

		 var ob  = $('#'+bet_sloupec[sazka_id+'_'+typ_id+'_'+podtyp_id][vl]).get(0);
		 if(ob.value.match(/,+/g)!=null)ob.value = ob.value.replace(/,+/g,".")
		 if(isNaN(parseFloat(ob.value)) || parseFloat(ob.value) == 0) status = false;
		 else vyh += (1/parseFloat(ob.value));


	  }


	  /*for(var vl in vyhernost[sazka]){

	    var ob = new getObj('pravdepodobnost_'+sazka+'_'+vl);
	    var ob2 = new getObj('sazka['+sazka+'][sloupec]['+vl+']');

		ob.obj.value = ( 1/(parseFloat(ob2.obj.value)*vyh) ).toFixed(4);

	  }*/


	  $('#vyhernost_'+sazka_id+'_'+typ_id+'_'+podtyp_id).get(0).value = vyh.toFixed(2);


	}


	jQuery.extend({ShowType:function(ev,idb){

    if(ev == 1) $('#bettype').show();
    else        $('#bettype').hide();

    if(typeof idb != 'undefined'){
    idb.toString();

     if(idb.length > 0) $('#bet_row_'+idb).show();
               }
   }
 });


function IntervalMinute(){

  var datum = new Date();

  var date = datum.getDate();
  var month = datum.getMonth() + 1;
  var year = datum.getFullYear();

  var hours = datum.getHours();
  hours = hours+'';
  if(hours.length == 1){
    hours = '0'+hours;
  }

  var minutes = datum.getMinutes();
  minutes = minutes+'';
  if(minutes.length == 1){
    minutes = '0'+minutes;
  }

  var seconds = datum.getSeconds();
  seconds = seconds+'';
  if(seconds.length == 1){
    seconds = '0'+seconds;
  }

  $('#live_act_time').text(date+'.'+month+'.'+year+' '+hours+':'+minutes+':'+seconds);

}
