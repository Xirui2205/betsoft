var method = "POST";
var ajaxurl    = "";
var dataToSend    = "";
var loadingText    = "";
var liveBets = new Array();
var liveLoadTime = 240000; // time live bet general info load
var liveLoadTimeBets = 5000; // time live bet event info
var liveLoadTimeStatus = 180000; // time live bet status and money info
var live_ev = new Object();
var animateCount = new Object();
var animateInterval = new Object();
var importRun = 0;
var vyhernost = new Object();
var callUpdate = 0;
var makeUpdate = 1;
var money = false;
$(document).ready(function(){

 dataToSend = '';
 dataToSend += 'matches='+liveBets.join();
 LiveAction('newMatch');

 setInterval("LiveAction('newMatch')",liveLoadTime);
 setInterval("eventLoad()",liveLoadTimeBets);
 setInterval("LiveAction('importStatus')",(liveLoadTimeStatus));
 
});

/*Hlavni funkce ktera handluje jednotlive akce*/
function LiveAction(act){
	
	if(makeUpdate == 0) return;
	
  $('#lb_message').html('');
  
    if(act == "newMatch"){
      
    	dataToSend  = '';
    	
    	for(vl8 in live_ev){

        	 dataToSend += '&loadBet['+vl8+']=1';
  
        }
   
  
      loadingText = "Načítám data";
      ajaxurl = "work=8&event=1";
      if($('#all_live').get(0).checked) dataToSend += '&all_live=1';
      
    }
    if(act == "sign_live"){
    
      loadingText = "Aktualizuji  data";
      ajaxurl = "work=8&event=2";
      
    }   
    if(act == "show_live"){
       
      makeUpdate=0;
    	
      loadingText = "Načítám data";
      ajaxurl = "work=8&event=3";
      
    }
    if(act == "load_bet"){
    
      loadingText = "Aktualizuji sázky/druhy";
      ajaxurl = "work=8&event=4"+(callUpdate>0?'&noupdate=1':'');
      
      if(callUpdate<=0) makeUpdate=0;
      
      callUpdate++;
    }
    if(act == "take_over"){
    
      loadingText = "Aktualizuji take over";
      ajaxurl = "work=8&event=5";
      
    }
    if(act == "stop_run"){
    
      loadingText = "Aktualizuji stop/run";
      ajaxurl = "work=8&event=6";
      
    }
    if(act == "close_bet"){
    
      loadingText = "Zavírám sázku";
      ajaxurl = "work=8&event=7";
      
    }
    if(act == "save_bet"){
    
      loadingText = "Aktualizuji sázku";
      ajaxurl = "work=8&event=8";
      
    }
    if(act == "fin"){
    
      loadingText = "Ukončuji / Vracím sázku";
      ajaxurl = "work=8&event=9";
      
    }
    if(act == "stop_all"){
    
      loadingText = "Zastavuji vše";
      ajaxurl = "work=8&event=10";
      
    }
    if(act == "run_all"){
    
      loadingText = "Spoustim vše";
      ajaxurl = "work=8&event=15";
      
    }
    if(act == "run_import"){
    
      loadingText = "Aktualizuji process";
      ajaxurl = "work=8&event=11";
      
    }
    if(act == "importStatus"){
    
       dataToSend = '';
       if(money == true){
       	 dataToSend = 'money=1&';
       	money = false;
       }
       for(vl in live_ev){


  	     dataToSend += '&lBet[]='+vl;
  

       }
 
      loadingText = "Aktualizuji info o processu";
      ajaxurl = "work=8&event=12";
      
    }
    if(act == "riskLimit"){
    
      loadingText = "Aktualizuji info o risk limitu";
      ajaxurl = "work=8&event=13";
      
    }
    if(act == "limits"){
    
      loadingText = "Aktualizuji info o limitech";
      ajaxurl = "work=8&event=14";
      
    }
       
    
              
    $.ajax({
     type: method,
     url: "ajax.server.php?"+ajaxurl,
     data: dataToSend,
     dataType: "json",
     beforeSend:function(){
      $('body').css('cursor','wait');
      $('#lb_message').show();
      $('#lb_message').append(loadingText);
     },
     error:function(XMLHttpRequest, textStatus, errorThrown){
     	makeUpdate = 1;
     },
     success: function(msg){

      $('body').css('cursor','auto');

      if(typeof msg['error'] != 'undefined'){
      
         $('#lb_message').text("Nastala chyba: "+msg.error);
      
      }else{
      
        $('#lb_message').hide("slow");
         
        for(var vl in msg){
         if(vl=='js_dynamic')eval(msg[vl]); 
         else
         $('#'+vl).html(msg[vl]);

        }
      
      }
      
      makeUpdate = 1;
      
     }
   });
   
}


function limits(type,id,val){
	
		dataToSend = '';
        dataToSend += 'type='+type+'&number='+val+'&bet_id='+id;
        LiveAction('limits');
	
}


function Prove(){
	
 var pole = new Array();
  $("input[name='prove_ch']").each(function (i) {
        
  	  if(this.checked){pole[pole.length] = $(this).attr('hod');}   
  	
   });

   var str = '&live=';
   for(var x=0;x<pole.length;x++){str += ''+pole[x]+'X';}
   str += '0';
    window.open('/ajax.server.php?work=3&br=1'+str,'','width=800,height=600');
   
}

function riskLimit(id,val){
	
		dataToSend = '';
        dataToSend += 'number='+val+'&bet_id='+id;
        LiveAction('riskLimit');
	
}

function RunImport(act){
	
	if(act == 1){
		
		dataToSend = '';
        dataToSend += 'run=1';
        LiveAction('run_import');
	
	}
	else if(act == 0){
		
		
		dataToSend = '';
        dataToSend += 'run=0';
        LiveAction('run_import');
	}
	
	
}

function saveBet(event_id,sazka_id){
	
	dataToSend = '';
    dataToSend += 'event_id='+parseInt(event_id);
    dataToSend += '&sazka_id='+parseInt(sazka_id);

 for(vl in live_ev[event_id]){
  for(vl2 in live_ev[event_id][vl]){

  	 dataToSend += '&loadBet['+event_id+']['+vl+']['+vl2+']='+live_ev[event_id][vl][vl2];
  
  }
 }
 
 for(vl in live_ev[event_id][sazka_id]){

 	 dataToSend += '&sloupec['+vl+']='+$("#live_event_bets_"+ event_id +"_"+ sazka_id +"_"+vl).get(0).value;
 	
 }
 
 
 

    LiveAction('save_bet');

}

function closeBet(event_id,sazka_id){
	
	dataToSend = '';
    dataToSend += 'event_id='+parseInt(event_id);
    dataToSend += '&sazka_id='+parseInt(sazka_id);

 for(vl in live_ev[event_id]){
  for(vl2 in live_ev[event_id][vl]){

  	 dataToSend += '&loadBet['+event_id+']['+vl+']['+vl2+']='+live_ev[event_id][vl][vl2];
  
  }
 }

    LiveAction('close_bet');

}

function stopAll(event_id){
	
	dataToSend = '';
    dataToSend += 'event_id='+parseInt(event_id);

    for(vl in live_ev[event_id]){
     for(vl2 in live_ev[event_id][vl]){

  	  dataToSend += '&loadBet['+event_id+']['+vl+']['+vl2+']='+live_ev[event_id][vl][vl2];
   
     }
    }
    

	LiveAction('stop_all');

	

 
	
}

function runAll(event_id){
	
	dataToSend = '';
    dataToSend += 'event_id='+parseInt(event_id);

    for(vl in live_ev[event_id]){
     for(vl2 in live_ev[event_id][vl]){

  	  dataToSend += '&loadBet['+event_id+']['+vl+']['+vl2+']='+live_ev[event_id][vl][vl2];
   
     }
    }
    

	LiveAction('run_all');

	

 
	
}

function stopRun(act,event_id,sazka_id){
	
	dataToSend = '';
    dataToSend += 'event_id='+parseInt(event_id);
    dataToSend += '&sazka_id='+parseInt(sazka_id);

 for(vl in live_ev[event_id]){
  for(vl2 in live_ev[event_id][vl]){

  	 dataToSend += '&loadBet['+event_id+']['+vl+']['+vl2+']='+live_ev[event_id][vl][vl2];
  
  }
 }
    
	if(act == 1){
		
		dataToSend += '&to='+1;
		LiveAction('stop_run');
		
	}
	else if(act == 2){
		
		dataToSend += '&to='+2;
		LiveAction('stop_run');
		
	}
	

 
	
}


function takeOver(act,event_id,sazka_id){
	
	dataToSend = '';
    dataToSend += 'event_id='+parseInt(event_id);
    dataToSend += '&sazka_id='+parseInt(sazka_id);

 for(vl in live_ev[event_id]){
  for(vl2 in live_ev[event_id][vl]){

  	 dataToSend += '&loadBet['+event_id+']['+vl+']['+vl2+']='+live_ev[event_id][vl][vl2];
  
  }
 }
    
	if(act == 1){
		
		dataToSend += '&to='+1;
		LiveAction('take_over');
		
	}
	else if(act == 2){
		
		dataToSend += '&to='+2;
		LiveAction('take_over');
		
	}
	

 
	
}

function eventLoad(){
	
 
 
 for(vl8 in live_ev){
   dataToSend = '';  
   for(vl in live_ev[vl8]){
     for(vl2 in live_ev[vl8][vl]){

  	 dataToSend += '&loadBet['+vl8+']['+vl+']['+vl2+']='+live_ev[vl8][vl][vl2];
  
      }
    }
  
 	 dataToSend += '&event_id='+parseInt(vl8);
     LiveAction('load_bet');
 
 }
 	
	
}


function shLive(t){
	
	if(t) $('#liveB').show();
	else  $('#liveB').hide();
	
}

function showLog(t){
	
	if(t) $('#log').show();
	else  $('#log').hide();
	
	
}

function LoadBets(event_id,sazka_id){
	
 dataToSend = '';
 dataToSend += 'event_id='+parseInt(event_id);
 
 if(sazka_id != 0)  dataToSend += 'sazka_id='+parseInt(sazka_id);
 
 for(vl in live_ev[event_id]){
  for(vl2 in live_ev[event_id][vl]){

  	 dataToSend += '&loadBet['+event_id+']['+vl+']['+vl2+']='+live_ev[event_id][vl][vl2];
  
  }
 }
 
 LiveAction('load_bet');
	
	
}

function Money(event_id){
	
	
 money = true;	
 LiveAction('importStatus');
	
	
}


function ShowLive(act,event_id){
	
 dataToSend = '';
 dataToSend += 'event_id='+parseInt(event_id);
 dataToSend += '&sign='+parseInt(act);
  
  for(vl in live_ev[event_id]){
  for(vl2 in live_ev[event_id][vl]){

  	 dataToSend += '&loadBet['+event_id+']['+vl+']['+vl2+']='+live_ev[event_id][vl][vl2];
  
  }
 }
 
 LiveAction('show_live');
	
	
}

function SignLive(act,event_id){

	
 dataToSend = '';
 dataToSend += 'event_id='+parseInt(event_id);
 dataToSend += '&sign='+parseInt(act);
  
 LiveAction('sign_live');
 

}

function fin(act,event_id){

	
 dataToSend = '';
 dataToSend += 'event_id='+parseInt(event_id);
 dataToSend += '&sign='+parseInt(act);
  
 if($('#all_live').get(0).checked) dataToSend += '&all_live=1';
 
 LiveAction('fin');
 

}

function animate(id){
	
	if(typeof animateCount[id] == 'undefined') animateCount[id] = 0;
	
	if(animateCount[id] < 10 && animateCount[id]%2 == 0) $("#"+id).animate({opacity: 0.1,color:'red'}, 400 );
	else if(animateCount[id] < 10 && animateCount[id]%2 != 0) $("#"+id).animate({opacity: 1,color:'black'}, 400 );
	else {animateCount[id] = 0;clearInterval(animateInterval[id]);}

	animateCount[id]++;
} 