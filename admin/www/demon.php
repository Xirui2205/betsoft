<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');
//Header("Content-type: text/plain");
include(ROOT.'common/includes.inc.php');
set_time_limit(0);
error_reporting(E_ALL);


// ze shared
include '../config_local.php';
include "common.php";

try{

  $demon = new Demon();
  $action = $_GET['action'];
  $status = false;

    #Negativni balance nebo spatny zustatek#
  if(is_array($action) && in_array('negative_balance',$action)) {

    if($demon->negativeBalance()) $demon->InsertLog("game","Negative_balance","Negative_balance","Negative_balance ".$demon->logtext);
    $status = true;

  }
  
  #Test#
  if(is_array($action) && in_array('test',$action)) {

    if($demon->test()) $demon->InsertLog("game","Test","test","Test ".$demon->logtext);
    $status = true;

  }


  #Seo URL pro udalosti#
  if(is_array($action) && in_array('seo',$action)) {

    if($demon->SeoUrl()) $demon->InsertLog("game","Seo udalosti","seo","Seo udalosti ".$demon->logtext);
    $status = true;

  }


  #Log zustatku#
  if(is_array($action) && in_array('bal_log',$action)) {

    if($demon->BalLog()) $demon->InsertLog("game","Log balance","bal_log","Log balance ".$demon->logtext);
    $status = true;

  }



  #Vycisteni sazek a tiketu#
  if(is_array($action) && in_array('cleanbet',$action)) {

    if($demon->CleanBet()) $demon->InsertLog("game","Vycisteni sazek a tiketu","bet_clean","Sázky a tiekty byly vyčištěny ".$demon->logtext);
    $status = true;

  }

  #Vycisteni tabulky se systemovymi sazkami#
  if(is_array($action) && in_array('cleansystem',$action)) {

    if($demon->CleanSystem()) $demon->InsertLog("game","Vycisteni systemovych sazek","system_bet_clean","Systémové sázky byly vyčištěny ".$demon->logtext);
    $status = true;

  }

  #Nastaveni vypoctu vyhernosti hracu#
  if(is_array($action) && in_array('uwin',$action)) {

    if($demon->UBetWIN()) $demon->InsertLog("game","Vyhernost hracu","user_win","Výhernost hráčů u sázek byla nastavena  ".$demon->logtext);
    $status = true;

  }

  #Nastaveni risk_limitu#
  if(is_array($action) && in_array('risk',$action)) {

    if($demon->RiskLimit()) $demon->InsertLog("game","Risk limit","bet_risk_limit","Risk limit byl nastaven".$demon->logtext);
    $status = true;

  }



  #Vycisteni dennich limitu#
  if(is_array($action) && in_array('limitden',$action)){
    if($demon->Limit(1)) $demon->InsertLog("game","Denní limit","day_limit","Denní limit byl resetován ".$demon->logtext);
    $status = true;
  }

  #Vycisteni tydennich limitu#
  if(is_array($action) && in_array('limittyden',$action)){
    if($demon->Limit(2)) $demon->InsertLog("game","Týdenní limit","week_limit","Týdenní limit byl resetován ".$demon->logtext);
    $status = true;
  }

  #Vycisteni mesicnich limitu#
  if(is_array($action) && in_array('limitmesic',$action)){
    if($demon->Limit(3)) $demon->InsertLog("game","Měsíční limit","month_limit","Měsíční limit byl resetován ".$demon->logtext);
    $status = true;
  }


  #Ukonceni vsech session ktere byly dlouho neaktivni#
  if(is_array($action) && in_array('vycistisession',$action)) {

    if($demon->VycistiSession()) $demon->InsertLog("game","Vyčištění session","ses_clear","Všechny neaktivní session byly ukončeny ".$demon->logtext);
    $status = true;

  }

   #Ukonceni vsech session ktere byly dlouho neaktivni u adminu#
  if(is_array($action) && in_array('vycistisessionadmin',$action)) {

    if($demon->VycistiSessionAdmin()) $demon->InsertLog("admin","Vyčištění session","ses_clear_adm","Všechny neaktivní session byly ukončeny ".$demon->logtext);
    $status = true;

  }


  #Aktualizace uzivatelu ve skladu#
  if(is_array($action) && in_array('users',$action)) {

    if($demon->Users()) $demon->InsertLog("game","Aktualizace uživatelů","users","Uživatelé byli aktualizováni. ".$demon->logtext);
    $status = true;

  }


  

  if(!$status){
      throw new ExHandler('Not known action in demon.','_fatal');
  }else{

    $demon->EndDemon();

  }

  unset($demon);

 echo '<div class="message">Finished</div>';
}
catch(ExHandler $e){
  $e->produceAllError();
}


