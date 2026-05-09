<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="cs" xml:lang="cs">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <meta http-equiv="Content-Language" content="cs" />
 <meta name="keywords" content="" />
 <meta name="description" content="" />
 <meta name="abstract" content="" />
 <meta name="robots" content='index,follow' />
 <meta name="googlebot" content='index,follow,snippet,archive' />
 <meta name="Author" content="" />
 <!-- CACHE - MSIE /-->
 <meta http-equiv='Cache-Control' content='must-revalidate, post-check=0, pre-check=0' />
 <meta http-equiv='Pragma' content='public' />
 <!-- CACHE - MSIE end /-->
 <!-- CACHE - other browsers /-->
 <meta http-equiv='Cache-Control' content='no-cache' />
 <meta http-equiv='Pragma' content='no-cache' />
 <meta http-equiv='Expires' content='-1' />

 <meta name='autosize' content='off' />
 <!-- BROWSER SPECIFIC FEATURES = end /-->
	<title>Vic.cz | {TITLE} </title>

 <link rel="stylesheet" href="css/live.css" media="screen" type="text/css" />
 <link rel="stylesheet" href="css/print.css" media="print" type="text/css" />
 <script src="js/jquery.js" type="text/javascript" language="javascript"></script>
 <script src="js/live.js" type="text/javascript" language="javascript"></script>
 <script type="text/javascript" language="javascript">

   var event_id = {EVENT_ID};

   var rate_def_ob = new Object();

   $(document).ready(function(){
     LiveAction('all');
     RunBetLimit();

   });

 </script>


</head>

<body>
<a name="top"></a>
<div id="live_bet" >


</div>

<div id="live_info">
 <div class="live_stav_info" id="live_stav"></div>
 <div class="live_stav_sport">{SU}</div>
 <div class="live_stav_udalost">{TEAM}</div>
  <div class="live_stav_sport" id="live_minute"></div>
 <div class="live_stav_score" id="live_score">0:0</div>


 <div id="live_stav_action" style="position:fixed;top:5px;left:5px;">
    <img src="_clip/loading.gif" class="img" alt="Nahravam" /> <br />
 </div>
 <table class="live_stav_scores"  id="live_period">

 </table>

 <table style="float:left" id="live_o_info">
 {KURT}
 {SET}
 {FIRST_SERVICE}
  </table>
 <div class="clear">&nbsp;</div>

</div>
<div id="live_manipulation">
 <div class="floatleft">
  <input type="button" value="START" class="b_begin" onclick="LiveAction('begin');stopMinute=false;" /> <br />
  <input type="button" value="STOP" class="b_begin"  id="stopbutton" onclick="LiveAction('stop');" /><br />
  <input type="button" value="GO" class="b_begin" onclick="LiveAction('runAllBet');" />
 </div>
 <div class="period_but" id="period_b">

 </div>
 <div id="live_time">
  <table>
    <tr>
      <td>
        <span class="floatleft">Počty:</span>
      </td>
      <td>
        <input
          type="button"
          value="Tiebreak"
          style="color:red"
          onclick="counter_type = 1;$('#t_b_1').css('color','red');$('#t_b_2').css('color','black');tiebreak=1;UpdateTiebreak()"
          id="t_b_1" />
      </td>
      <td>
        <input
          type="button"
          value="Body"
          id="t_b_2"
          onclick="counter_type = 2;$('#t_b_2').css('color','red');$('#t_b_1').css('color','black');tiebreak=0;UpdateTiebreak()" />
      </td>
      <input type="hidden" name="tiebreak" value="0" />
    </tr>
    <tr>
      <td>
        <span class="floatleft">Stav:</span>
        </td>
        <td>
          <input type="text" value="{SCORE_HOME}" id="score_home" class="b_score" />
          <div class="arrow_move">
            <a href="#" onclick="ChValue('plus','score_home',0,1,0,0,0);UpdateAllInfo();" class="arrow">
              <img src="_clip/arrow_up.gif" class="img" alt="+1" />
            </a>
            <a href="#" onclick="ChValue('minus','score_home',0,1,0,0,0);UpdateAllInfo();" class="arrow">
              <img src="_clip/arrow_down.gif" class="img" alt="-1" />
            </a>
          </div>
          <input type="text" value="{SCORE_AWAY}" id="score_away" class="b_score" />
          <div class="arrow_move">
            <a href="#" onclick="ChValue('plus','score_away',0,1,0,0,0);UpdateAllInfo();" class="arrow">
              <img src="_clip/arrow_up.gif" class="img" alt="+1" />
            </a>
            <a href="#" onclick="ChValue('minus','score_away',0,1,0,0,0);UpdateAllInfo();" class="arrow">
              <img src="_clip/arrow_down.gif" class="img" alt="-1" />
            </a>
          </div>
          <input type="button" value="+2" onclick="PlusScore('score_home',2);"  />
          <input type="button" onclick="PlusScore('score_home',3);"  value="+3" />
          <input type="button" value="+2" onclick="PlusScore('score_away',2);"  />
          <input type="button" onclick="PlusScore('score_away',3);"  value="+3" />
        </td>
      <td rowspan="2">
        <input type="button" style="" value="Obnovit" onclick="UpdateAllInfo()" class="b_input" />
        <br />
        <br />
        <div >
          <input type="button" value="Refresh minute"  onclick="ClearRunMinute();" />
        </div>
        <br />
        <a  href="javascript:void(0);" onclick="window.open('/ajax.server.php?work=6&live={EVENT_ID}&team={TEAM}','','width=720,height=800,top=0,left=400');" >
          Povolování sázek
        </a>
      </td>
    </tr>
    <tr>
      <td>
        <span class="clear">Začátek:</span>
      </td>
      <td>
        <input type="text" value="{BEGIN_AT}" id="start_at" class="b_input" />
        <a href="javascript:window.open(\'calendar.php?cas=zahajeni\',\'\',\'width=140,height=247\');void(0);">
          <img src="_clip/calendar.gif" alt="Kalendar" class="img" />
        </a>
      </td>
    </tr>
    <tr>
      <td>
        <span class="clear">Minuta:</span>
      </td>
      <td>
        <input type="text" value="{MINUTE}" id="l_minute" class="b_input" />
        <div id="timesec"></div>
      </td>
    </tr>

<!--
  <tr><td><span class="clear">Max. výhra EUR:</span> </td><td>
  <input type="text" value="{WIN}" id="win" style="font-size:10px;" class="b_input" /> </td></tr>
-->

    <tr>
      <td>
        <span class="clear">Sázka / kurz povolování</span>
      </td>
      <td>
        <input type="text" value="{PROVE_AMOUNT}" id="prove_bet" class="b_input" style="font-size:10px;" />
        /
        <input type="text" style="font-size:10px;" value="{PROVE_RATE}" id="prove_rate" class="b_input" />
      </td>
    </tr>
    <tr>
      <td>Aktualni cas: </td>
      <td>
        <span class="live_act_time" id="live_act_time">
          <script>window.setInterval('IntervalMinute()',1000);</script>
        </span>
      </td>
    </tr>
  </table>
 </div>


  <div id="live_action">
    <input type="text" id="special_info_min" style="width:60px;" value=""  />
    <select id="special_info" name="special_info">
      <option value="no">Vyberte informaci</option>
      {SPECIAL_INFO}
    </select>
    <input type="text" id="special_info_text" value=""  />
    <input type="button" onclick="SpecialInfo()" value="Uložit" />
    <a href="javascript:$('#special_info_history').css('display') == 'none'?$('#special_info_history').css('display','block'):$('#special_info_history').css('display','none');void(0);">
      <img style="position:relative;top:8px;" src="_clip/info_buble.gif" />
    </a>

    <div id="special_info_history">
      {BETHISTORY}
    </div>

    <div>
      Stálá hláška:<br />
      <input type="text" id="permanent_info" value="{PERMANENT_NOTE}"  />
      <input type="button" onclick="PermanentInfo()" value="Uložit" />
    </div>

  </div>

</div>

<div id="bettype" class="menu" onclick="jQuery.ShowType(0);">

</div>

<p class="download_ticket_info">
<a href="#" onmouseover="jQuery.ShowType(1);" onclick="jQuery.ShowType(0);">MENU</a>
&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:RunBetLimit();void(0);">AKTUALIZOVAT</a>
&nbsp;&nbsp;&nbsp;&nbsp;
[ <a href="javascript:SHDB(0);void(0);">Zobrazit</a> / <a href="javascript:SHDB(1);void(0);">Skrýt</a>  ]
</p>


</body>
</html>
