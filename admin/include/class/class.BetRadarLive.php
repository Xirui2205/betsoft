<?php
/**

 * @package    Live Betradar
 */


class BetRadarLive{


/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * pravo zmeny v sekci
 * @access private
 * @var int
 */
private  $update = 0;

/**
 * pravo vymazani v sekci
 * @access private
 * @var int
 */
private  $delete = 0;

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = '';

/**
 * navratova hodnota JSON
 * @access private
 * @var array
 */
private  $json = array();


/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct(){

    $this->dbGame = Zend_Registry::get('zdb_game');



  }

  /**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){

    $this->template();

  }

 /**
 * Tato metoda se vola pri nejakych vsazenych penezich
 * @param int $uid id uzivatele POVINNA
 * return bool
 */
  public function template(){

   $this->vrat = '<script type="text/javascript" src="/js/liveBetradar.js?r=' . RELEASE_REV . '"></script>

                 <div id="lb_message"></div>

                 <input type="checkbox" id="all_live" style="display:none" class="chbox"/> 
                 <input type="checkbox" id="sh_live" checked="checked" class="chbox" onclick="shLive(this.checked)" /> <strong>Schovat panel - Zobrazit panel</strong>
                 <input type="checkbox" id="logc"   style="display:none" class="chbox" onclick="showLog(this.checked)" /> 
                 <div id="log" style="display:none;border:1px solid black;height:400px;overflow:auto;padding:10px;"></div>

                 <div id="liveB">
                 <input type="button" class="bx" value="Start" onclick="RunImport(1);" /> <input type="button" class="bx" onclick="RunImport(0);" value="Stop" />
                 <input type="button" class="bx" onclick="Prove();" value="Povolování" /><div id="importStatus" style="float:right;"></div>
                   <h3>LIVE</h3>
                   <table id="liveBHead" class="unitable">
                   </table>
                 </div>
                 <div id="liveBody">

                 </div>
                 ';

  }

   /**
 * Tato metoda naleza spravnou akci
 * return bool
 */
  public function findAction(){

    if(isset($_GET['event']) && $_GET['event'] == 1){

        $this->newMatch();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 2){

        $this->signLive();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 3){

        $this->showLive();

    }

    else if(isset($_GET['event']) && $_GET['event'] == 4){

        $this->loadBets();

    }

    else if(isset($_GET['event']) && $_GET['event'] == 5){

        $this->takeOver();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 6){

        $this->stopRun();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 7){

        $this->closeBet();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 8){

        $this->saveBet();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 9){

        $this->fin();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 10){

        $this->stopAll();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 15){

        $this->runAll();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 11){

        $this->runImport();

    }
     else if(isset($_GET['event']) && $_GET['event'] == 12){

        $this->importInfo();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 13){

        $this->riskLimit();

    }
    else if(isset($_GET['event']) && $_GET['event'] == 14){

        $this->limits();

    }
    $this->PrintJson();

  }

  /**
 * Aktualizuje limity
 * @return void
 */
  public function limits(){



    $bet_id = intval($_POST['bet_id']);
    $number = round($_POST['number'],2);
    $type =   intval($_POST['type']);


      try{

         $data = array();
         if($type == 1)$data['limit_rate'] = $number;
         if($type == 2)$data['limit_bet']  = $number;

         $this->dbGame->update('live_event', $data,"event_id=".$bet_id);

      }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

      }



    $this->loadBets();

  }

        /**
 * Aktualizuje risk limit
 * @return void
 */
  public function riskLimit(){



    $bet_id = intval($_POST['bet_id']);
    $number = intval($_POST['number']);



      try{

         $data = array();
         $data['risk_limit'] = $number;

         $this->dbGame->update('sazky', $data,"sazka_id=".$bet_id);

      }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

      }



    $this->loadBets();

  }

 /**
 * Vraci info o procesu
 * @return void
 */
  public function importInfo(){



    exec('livebetradar status' ,$op,$ret);

    if(intval($ret) == 1) $this->json['importStatus'] = '<span style="color:red">Služba offline</span>';
    else if(intval($ret) <> 1) $this->json['importStatus'] = '<span style="color:blue">Služba online</span>';

    foreach($_POST['lBet'] as $hh){
    $select = $this->dbGame->select()->from(array("a"=>'live_sazka'),array('a.sazka_id','d.sloupec_id'))
                                           ->join(array('d'=>'sazka_kurz'),'a.sazka_id=d.sazka_id')
                                           ->where("a.event_id=?",intval($hh))
                                           ->where("a.close=?",0);

    $stm  = $select->query();
    $rowD = $stm->fetchAll();



    foreach($rowD as $h){

        $this->json['js_dynamic'] .= '$("#live_event_money_'. intval($hh) .'_'.$h['sazka_id'].'_'.$h['sloupec_id'].'").html("'. $this->liveMoney($h['sazka_id'],$h['sloupec_id']) .' ");';

    }

    }

  }

        /**
 * Zastavuje a sposti process
 * @return void
 */
  public function runImport(){

    if(isset($_POST['run']) && $_POST['run'] == 1){

        exec('livebetradar start' ,$op,$ret);
        //print_r($op);echo $ret;
        if(intval($ret) == 1) $this->json['js_dynamic'] = 'alert("'.$op[0].'")';
    }
    else if(isset($_POST['run']) && $_POST['run'] == 0){

        exec('livebetradar stop' ,$op,$ret);
        if(intval($ret) == 1) $this->json['js_dynamic'] = 'alert("'.$op[0].'")';
    }

    $this->importInfo();

  }


      /**
 * Zastavuje sazky
 * @return void
 */
  public function stopAll(){


    $datum = It6_Date::dbNow();

    $select = $this->dbGame->select()->from(array("a"=>'live_sazka'),array('a.sazka_id'))
                                           ->join(array('t'=>'sazky'),'t.sazka_id=a.sazka_id')
                                           ->where("a.event_id=?",intval($_POST['event_id']))
                                           ->where("t.platna_do>?",$datum)
                                           ->where("a.close=?",0);

    $stm  = $select->query();
    $rowD = $stm->fetchAll();

    if(count($rowD) == 0) return;



    foreach($rowD as $h){


      try{

         $data = array();
         $data['status'] = 2;

         $this->dbGame->update('sazky', $data,"sazka_id=".$h['sazka_id']);

      }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

      }


     try{

         $this->dbGame->update('live_sazka', array("aktualizace_sazka"=>$datum,"aktualizace"=>$datum,"no_update"=>1),"sazka_id=".$h['sazka_id']);

     }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

     }

    }

    $this->loadBets();

  }


  
      /**
 * Spoustim sazky
 * @return void
 */
  public function runAll(){


    $datum = It6_Date::dbNow();

    $select = $this->dbGame->select()->from(array("a"=>'live_sazka'),array('a.sazka_id'))
                                           ->join(array('t'=>'sazky'),'t.sazka_id=a.sazka_id')
                                           ->where("a.event_id=?",intval($_POST['event_id']))
                                           ->where("t.platna_do>?",$datum)
                                           ->where("a.close=?",0);

    $stm  = $select->query();
    $rowD = $stm->fetchAll();

    if(count($rowD) == 0) return;



    foreach($rowD as $h){


      try{

         $data = array();
         $data['status'] = 0;

         $this->dbGame->update('sazky', $data,"sazka_id=".$h['sazka_id']);

      }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

      }


     try{

         $this->dbGame->update('live_sazka', array("aktualizace_sazka"=>$datum,"aktualizace"=>$datum,"no_update"=>0),"sazka_id=".$h['sazka_id']);

     }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

     }

    }

    $this->loadBets();

  }
  
  
    /**
 * Aktualizace sazky
 * @return void
 */
  public function saveBet(){

    if(!isset($_POST['sloupec']))    return;

    $datum = It6_Date::dbNow();

    $select = $this->dbGame->select()->from(array('a'=>'sazka_kurz'),array("max"=>'MAX(poradi)','a.kurz','a.sloupec_id','t.podtyp_id','t.typ_id','t.sazka_id','t.udalost_id'))
                                           ->join(array('t'=>'sazky'),'t.sazka_id=a.sazka_id')
                                           ->where("a.sazka_id=?",intval($_POST['sazka_id']))
                                           ->where("t.platna_do>?",$datum)
                                           ->where("t.status=0 or t.status=2")
                                           ->group("a.sloupec_id");

    $stm  = $select->query();
    $rowD = $stm->fetchAll();

    if(count($rowD) == 0) return;

    $select = $this->dbGame->select()->from(array('a'=>'bet_settings'),array('kurz_min','kurz_max','vyhernost_min','vyhernost_max'))
                                     ->where("a.udalost_id=?",$rowD[0]['udalost_id'])
                                     ->where("a.typ_id=?",$rowD[0]['typ_id'])
                                     ->where("a.podtyp_id=?",$rowD[0]['podtyp_id']);

    $stm2  = $select->query();
    $rowD2 = $stm2->fetchAll();

    if (count($rowD2) != 0){
             $min = $rowD2[0]['kurz_min'];$max = $rowD2[0]['kurz_max'];$vyh_min = $rowD2[0]['vyhernost_min'];$vyh_max = $rowD2[0]['vyhernost_max'];
    }else {$this->json['error'] = "Neni definovana výhernost \n";return;}

    $max = $rowD[0]['max'];
    $old = array();
    $vyhernost_num = 0;
    foreach($rowD as $h){$old[$h['sloupec_id']] = $h['kurz'];}

    $this->dbGame->beginTransaction();
    $status = true;

    foreach($_POST['sloupec'] as $k=>$h){$vyhernost_num += (1/$h);}$vyhernost_num = round($vyhernost_num,2);

    foreach($_POST['sloupec'] as $k=>$h){

        //if($h>$max || $h<$min) {$status = false;$this->json['error'] = ('Kurz '.$h.' překročil hranici limitu výše kurzu');}
        if($vyhernost_num<$vyh_min || $vyhernost_num>$vyh_max)  {$status = false;$this->json['error'] =('Výhernost '.round($vyhernost_num,2).' je mimo povolenou hranici ('.$vyh_min.' - '.$vyh_max.')');}

        if(!$status) {$this->dbGame->rollBack();break;}
      try{

         $data = array();
         $data['sazka_id'] = intval($_POST['sazka_id']);
         $data['sloupec_id'] = intval($k);
         $data['poradi'] = ($max+1);
         $data['kurz'] = $h;
         $data['platny_od'] = $datum;
         $data['kurz_zmena'] = ($h<$old[$k]?-1: ($h>$old[$k]?1:0) );

         $this->dbGame->insert('sazka_kurz', $data);

      }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

      }

    }

    $this->dbGame->commit();

    try{

         $this->dbGame->update('live_sazka', array("aktualizace_sazka"=>It6_Date::dbNow(),"aktualizace"=>$datum),"sazka_id=".intval($_POST['sazka_id']));

    }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

    }

    $this->loadBets();

  }



  /**
 * Ukončuji / Vracím sázku
 * @return void
 */
  public function fin(){

    if(!isset($_POST['sign']) || ($_POST['sign'] != 0 && $_POST['sign'] != 1) ) return;

    $this->dbGame->beginTransaction();

    try{

        $this->dbGame->update('live_event', array("start_date"=>"0000-00-00 00:00:00","aktualizace"=>It6_Date::dbNow(),"stav"=>($_POST['sign'] ==0?LIVE_END:0)),"event_id=".intval($_POST['event_id']));
        $this->dbGame->update('live_betradar_event', array("no_update"=>1),"event_id=".intval($_POST['event_id']));
    }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";
          $this->dbGame->rollBack();   return;
    }

    $select = $this->dbGame->select()->from(array('a'=>'live_sazka'),array('a.sazka_id'))
                                          ->join(array('l' => 'sazky'),'a.sazka_id = l.sazka_id')
                                           ->where("l.platna_do>?",It6_Date::dbNow())
                                           ->where("a.event_id=?",intval($_POST['event_id']))
                                           ->where("l.status in(0,2)");
    $stm  = $select->query();
    $row = $stm->fetchAll();

    foreach($row as $h){
    $data = array();
    $data['platna_do'] = It6_Date::dbNow();

    try{

        $this->dbGame->update('sazky', $data,'sazka_id='.$h['sazka_id']);
        $this->dbGame->update('live_sazka', array("aktualizace"=>It6_Date::dbNow(),'close'=>1),'sazka_id='.$h['sazka_id']);

    }catch (Exception $e) {

       $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";
       $this->dbGame->rollBack();
        return false;

    }

    }

    $this->dbGame->commit();

    $this->newMatch();

  }

  /**
 * Close sazky
 * @return void
 */
  public function closeBet(){



    try{

         $this->dbGame->update('sazky', array("status"=>2,"platna_do"=>It6_Date::dbNow()),"sazka_id=".intval($_POST['sazka_id']));
         $this->dbGame->update('live_sazka', array("aktualizace"=>It6_Date::dbNow(),"close"=>1),"sazka_id=".intval($_POST['sazka_id']));

    }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

    }

    $this->loadBets();

  }


   /**
 * Prevzeti / vraceni live sazky
 * @return void
 */
  public function stopRun(){

    if(!isset($_POST['to']) || ($_POST['to'] != 1 && $_POST['to'] != 2) ) return;


    try{

         $this->dbGame->update('sazky', array("status"=>(intval($_POST['to'])==1?2:0)),"status in (0,2) and sazka_id=".intval($_POST['sazka_id']));
         $this->dbGame->update('live_sazka', array("aktualizace"=>It6_Date::dbNow()),"sazka_id=".intval($_POST['sazka_id']));

    }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

    }

    $this->loadBets();

  }

 /**
 * Prevzeti / vraceni live sazky
 * @return void
 */
  public function takeOver(){

    if(!isset($_POST['to']) || ($_POST['to'] != 1 && $_POST['to'] != 2) ) continue;


    try{

         $this->dbGame->update('live_sazka', array("no_update"=>(intval($_POST['to'])==1?0:1)),"event_id=".intval($_POST['event_id'])." and sazka_id=".intval($_POST['sazka_id']));

    }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

    }

    $this->loadBets();

  }


       /**
 * Odhlaseni / Prihlaseni live sazky
 * @return void
 */
  public function loadBets(){

        $template = $poradi = array();
        $preklady = new Preklady();
        $this->json['js_dynamic'] = '';

        $select = $this->dbGame->select()->from(array('a'=>'live'),array('a.*','snazev'=>'a.nazev','unazev'=>'t.nazev'))
                                     ->join(array('t'=>'typ'),'t.typ_id=a.typ_id')
                                     ->where("a.event_id=?",intval($_POST['event_id']))
                                     ->order(array('a.poradi','a.poradi_sloupec'))
                                     ;

        if(isset($_POST['sazka_id'])) $select = $select->where("a.sazka_id=?",intval($_POST['sazka_id']));

        $stm  = $select->query();
        $rowD = $stm->fetchAll();

        foreach ($rowD as $row){

         if(!isset($template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']])){

         $row['unazev'] = $preklady->findPreklad($row['unazev'],1);
         $row['unazev'] = $row['unazev'][1];
         $template[$row['sazka_id']]['tnazev'] = $row['unazev'];



         $template[$row['sazka_id']]['risk_limit'] =  $row['risk_limit'];
         $template[$row['sazka_id']]['risk_limit_balance'] =  $row['risk_limit_balance'];
         $template[$row['sazka_id']]['status'] =  $row['status'];
         $template[$row['sazka_id']]['sazka_id'] =  $row['sazka_id'];
         //TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
         $template[$row['sazka_id']]['platna_do'] =  It6_Date::fromDbAsTimestamp($row['platna_do']);
         $template[$row['sazka_id']]['close'] =  $row['close'];
         $template[$row['sazka_id']]['text'] =  $row['text'];
         $template[$row['sazka_id']]['udalost_id'] =  $row['udalost_id'];
         $template[$row['sazka_id']]['typ_id'] =  $row['typ_id'];
         $template[$row['sazka_id']]['podtyp_id'] =  $row['podtyp_id'];
         $template[$row['sazka_id']]['risk_limit'] =  $row['risk_limit'];
         $template[$row['sazka_id']]['risk_limit_balance'] =  $row['risk_limit_balance'];
         $template[$row['sazka_id']]['no_update'] =  $row['no_update'];
         $template[$row['sazka_id']]['vypnuto'] =  $row['vypnuto'];
         $template[$row['sazka_id']]['stav'] =  $row['stav'];

        }

      if(!isset($template[$row['sazka_id']]['sloupec_name'][$row['sloupec_id']]))$template[$row['sazka_id']]['sloupec_name'][$row['sloupec_id']] = $row['snazev'];

      if(!isset($template[$row['sazka_id']]['sloupec']) || $poradi[$row['sazka_id']] == $row['poradi']){
        $poradi[$row['sazka_id']] = $row['poradi'];
        $template[$row['sazka_id']]['sloupec'][$row['sloupec_id']] = $row['kurz'];
      }

    }

    foreach($template as $h){

       #MAX a MIN kurzu a vyhernosti#
       $min_kurz = $max_kurz = $min_vyhernost = $max_vyhernost = 0;
       $sql = "select kurz_min,kurz_max,vyhernost_min,vyhernost_max from bet_settings where udalost_id=".$h['udalost_id']." and typ_id=".$h['typ_id']." and  podtyp_id=".$h['podtyp_id'];
       $row22 = $this->dbGame->fetchAll($sql);

       if(count($row22) > 0){$min_kurz = $row22[0]['kurz_min'];$max_kurz = $row22[0]['kurz_max'];$min_vyhernost = $row22[0]['vyhernost_min'];$max_vyhernost = $row22[0]['vyhernost_max'];}

        if($h['no_update'] == 1 && $_GET['event'] == 4 && isset($_GET['noupdate']) ) {
         continue;
        }

           if(( ($h['status'] != 0 && $h['status'] != 2) || $h['platna_do'] <= time() ) && isset($_POST['loadBet'][intval($_POST['event_id'])][$h['sazka_id']])){
             $this->json['js_dynamic'] .= 'delete live_ev["'. intval($_POST['event_id']) .'"]["'. $h['sazka_id'] .'"];$("#live_event_bet_'. intval($_POST['event_id']) .'_'. $h['sazka_id'] .'").remove();';

           }

          if(isset($_POST['event_id'])) $this->json['js_dynamic'] .= '$("#live_event_stav_'. intval($_POST['event_id']) .'").html(\''.$this->retStav(intval($_POST['event_id']),array('vypnuto'=>$h['vypnuto'],'stav'=>$h['stav'])).'\');';

        $buttons = '';

        if($h['no_update'] == 1) $buttons .= '<input type="button" class="betsTabInput" onclick="takeOver(1,'.intval($_POST['event_id']).','.$h['sazka_id'].')" value="Betradar" /> <br />';
        else                     $buttons .= '<input type="button" class="betsTabInput" onclick="takeOver(2,'.intval($_POST['event_id']).','.$h['sazka_id'].')" value="Převzít" /> <br />';

        if($h['status'] == 0)    $buttons .= '<input type="button" class="betsTabInputR" onclick="stopRun(1,'.intval($_POST['event_id']).','.$h['sazka_id'].')" value="Stop" /> <br />';
        else                     $buttons .= '<input type="button" class="betsTabInputB" onclick="stopRun(2,'.intval($_POST['event_id']).','.$h['sazka_id'].')" value="Run" /> <br />';

        $buttons .= '<input type="button" class="betsTabInput" onclick="if(confirm(\\\'Opravdu chcete sázku zavřít?\\\'))closeBet('.intval($_POST['event_id']).','.$h['sazka_id'].')" value="END" /> <br />';
        if($h['no_update'] == 1) $buttons .= '<input type="button" class="betsTabInputS" onclick="saveBet('.intval($_POST['event_id']).','.$h['sazka_id'].')" value="Save" /> <br />';

        if( ( ($h['status'] != 0 && $h['status'] != 2) || $h['platna_do'] <= time() ) && !isset($_POST['loadBet'][intval($_POST['event_id'])][$h['sazka_id']]) );
        else if(( ($h['status'] != 0 && $h['status'] != 2) || $h['platna_do'] <= time() ) && isset($_POST['loadBet'][intval($_POST['event_id'])][$h['sazka_id']]))
             $this->json['js_dynamic'] = 'delete live_ev["'. intval($_POST['event_id']) .'"]["'. $h['sazka_id'] .'"];$("#live_event_bet_'. intval($_POST['event_id']) .'_'. $h['sazka_id'] .'").remove();';
        else if(!isset($_POST['loadBet'][intval($_POST['event_id'])][$h['sazka_id']])){


            $vrat = '<table class="betsTab" id="live_event_bet_'. intval($_POST['event_id']) .'_'. $h['sazka_id'] .'">';
            $vrat .= '<tr><td><a href="/?superb=1&section=b13&filtr_sazka_id='.$h['sazka_id'].'" target="_blank">'.$h['sazka_id'].'</a></td><td colspan="'.count($h['sloupec']).'" class="itext">'.$h['tnazev'].' '.$h['text'].'</td><td class="itext2" id="live_event_btext_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'" colspan="'.(count($h['sloupec'])+2).'">'. ($h['no_update'] == 1?'BOOKMAKER':'BETRADAR') .'</td><td class="itext2"  id="live_event_risk_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'" colspan="'.(count($h['sloupec'])+2).'"><input type="text" style="width:60px" onblur="riskLimit('.$h['sazka_id'].',this.value)" value="'. $h['risk_limit'] .'" > / <span id="live_event_risk2_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'">'. $h['risk_limit_balance'] .'</span></td><td rowspan="4" valign="top" style="vertical-align:top" class="betsTabRow" id="live_event_buttons_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'">'.$buttons.'</td></tr>';


            $vrat1 = $vrat2 = '<tr><td>&nbsp;</td>';
            $vrat3 = '<tr><td><input type="text"  class="betsTabInputT" readonly="readonly" id="vyhernost_'. $h['sazka_id'] .'"/></td>';
            $vrat4 = '<tr><td>'. $min_vyhernost .' - '. $max_vyhernost .'</td>';
            $this->json['js_dynamic'] .= 'live_ev["'. intval($_POST['event_id']) .'"]["'. $h['sazka_id'] .'"] = new Object();';

            $this->json['js_dynamic'] .=  'vyhernost['.$h['sazka_id'].'] = new Object();';

            foreach($h['sloupec'] as $k4=>$h4){

                $this->json['js_dynamic'] .= 'vyhernost['.$h['sazka_id'].']['.$k4.'] = 1;';

                $this->json['js_dynamic'] .= 'live_ev["'. intval($_POST['event_id']) .'"]["'. $h['sazka_id'] .'"]["'. $k4 .'"] = '. $h4 .';';

                $vrat1 .= '<td><strong>'.$h['sloupec_name'][$k4].'</strong></td>';
                $vrat2 .= '<td id="live_event_betsText_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'">'.$h4.'</td>';

                $vrat3 .= '<td><input class="betsTabInputT" onkeyup="SetVyhernost2('. $h['sazka_id'] .','. intval($_POST['event_id']) .')" type="text" value="'.$h4.'" id="live_event_bets_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'"  /></td>';
                $vrat4 .= '<td id="live_event_money_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'"  >'.$this->liveMoney($h['sazka_id'],$k4).' </td>';

            }
            $vrat1 .= '</tr>';
            $vrat2 .= '</tr>';
            $vrat3 .= '</tr>';
            $vrat4 .= '</tr>';

            $vrat .= $vrat1 . $vrat2 . $vrat3. $vrat4;
            //$vrat .= '<tr><td class="itext2" id="live_event_btext_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'" colspan="'.(count($h['sloupec'])+2).'">'. ($h['no_update'] == 1?'BOOKMAKER':'BETRADAR') .'</td></tr>';
           // $vrat .= '<tr><td class="itext2"  id="live_event_risk_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'" colspan="'.(count($h['sloupec'])+2).'"><input type="text" style="width:60px" onblur="riskLimit('.$h['sazka_id'].',this.value)" value="'. $h['risk_limit'] .'" > / <span id="live_event_risk2_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'">'. $h['risk_limit_balance'] .'</span></td></tr>';

            $vrat .= '</table>';

            $vrat = mb_ereg_replace("[\n\r\t]+","",$vrat);

            $this->json['js_dynamic'] .= '$("#live_event_bets_'. intval($_POST['event_id']) .'").append(\''.$vrat.'\');';

        }else{

            $buttons = mb_ereg_replace("[\n\r\t]+","",$buttons);
            $this->json['js_dynamic'] .= '$("#live_event_buttons_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'").html(\''.$buttons.'\');';
            $this->json['js_dynamic'] .= '$("#live_event_btext_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'").html("'. ($h['no_update'] == 1?'BOOKMAKER':'BETRADAR') .'");';
            $this->json['js_dynamic'] .= '$("#live_event_risk_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'").attr("value",'. ($h['risk_limit']) .');';
            $this->json['js_dynamic'] .= '$("#live_event_risk2_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'").html("'. ($h['risk_limit_balance']) .'");';

            $xx = 0;
            foreach($h['sloupec'] as $k4=>$h4){

                if(isset($_POST['loadBet'][intval($_POST['event_id'])][$h['sazka_id']])){

                    $change = true;
                    if($_POST['loadBet'][intval($_POST['event_id'])][$h['sazka_id']][$k4] < $h4 )
                      $this->json['js_dynamic'] .= '$("#live_event_betsText_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'").attr("class","rate_up");';
                    else if($_POST['loadBet'][intval($_POST['event_id'])][$h['sazka_id']][$k4] > $h4 )
                      $this->json['js_dynamic'] .= '$("#live_event_betsText_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'").attr("class","rate_down");';
                    else $change = false;

                    if($change)
                              $this->json['js_dynamic'] .= 'animateInterval["live_event_betsText_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'"] = setInterval(\'animate("live_event_betsText_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'")\',600);';

                }

                $this->json['js_dynamic'] .= '$("#live_event_betsText_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'").html('. $h4 .');';
                $this->json['js_dynamic'] .= '$("#live_event_bets_'. intval($_POST['event_id']) .'_'.$h['sazka_id'].'_'.$k4.'").attr("value",'. $h4 .');';

                $this->json['js_dynamic'] .= 'live_ev["'. intval($_POST['event_id']) .'"]["'. $h['sazka_id'] .'"]["'. $k4 .'"] = '. $h4 .';';

                $xx++;
            }

        }

    }

  }

 /**
 * Vsazeno na dany sloupec
 * @param int $sazka_id
 * @param int $sloupec_id
 * @return int
 */
  public function liveMoney($sazka_id,$sloupec_id){
 if(!isset($_POST['money'])) return 0;



        $castka = 0;



          $date = '1980-01-01 00:00:00';

          $sql = "(select t.ticket_id from ticket t inner join ticket_kurz b on t.ticket_id=b.ticket_id  where b.sloupec_id=". intval($sloupec_id) ." and b.sazka_id=".intval($sazka_id).")";
          $row = $this->dbGame->fetchAll($sql);
          $ticket=array();$ticket[]=0;
          foreach($row as $h){$ticket[] =  $h['ticket_id'];}

          $sql = "select (fn_currency_user2central(a.user_id,t.castka) / count(t.ticket_id)) AS castka
          from  ticket t inner join ticket_kurz b on t.ticket_id=b.ticket_id inner join uzivatel a on a.user_id=t.user_id where t.ticket_id in(".implode(",",$ticket).") and  t.zalozen>'". $date ."'  group by t.ticket_id";


        try{

          $row = $this->dbGame->fetchAll($sql);

        }catch (Exception $e) {

            echo $sql.$e->getMessage();exit;
        }


        foreach ($row as $h){

            $castka += intval($h['castka']);


        }


  
        return $castka;

  }




     /**
 * Odhlaseni / Prihlaseni live sazky
 * @return void
 */
  public function showLive(){

    if(!isset($_POST['event_id']))  $this->json['error'] = 'ID event nebylo definovano';

    if(!isset($_POST['sign']) || $_POST['sign'] == 0){


        $this->json['js_dynamic'] = 'delete live_ev["'. intval($_POST['event_id']) .'"];$("#live_event_'. intval($_POST['event_id']) .'").remove();';

    }else{

        $preklady = new Preklady();

        $select = $this->dbGame->select()->from(array('a'=>'live_event'),array('limit_bet','limit_rate','home_team','away_team','b.no_update','a.vypnuto','snazev'=>'s.nazev','unazev'=>'u.nazev','a.event_id','a.start_date','a.stav','a.l_sport_id','a.l_udalost_id'))
                                     ->join(array('b'=>'live_betradar_event'),'a.event_id=b.event_id')
                                     ->join(array('s'=>'sport'),'s.sport_id=a.l_sport_id')
                                     ->join(array('u'=>'udalost'),'u.udalost_id=a.l_udalost_id')
                                     ->where("a.event_id=?",intval($_POST['event_id']));


        $stm  = $select->query();
        $row = $stm->fetchAll();

        foreach ($row as $h){

        $sport   = $preklady->findPreklad($h['snazev'],1);
        $udalost = $preklady->findPreklad($h['unazev'],1);




        $bet = '<div class="liveEvent" id="live_event_'. intval($_POST['event_id']) .'">
                     <div class="live_event_head">'.$h['event_id'].' '.$stext.' '.$sport[1].'  - '.$udalost[1].' <strong>'.$h['home_team'].' - '.$h['away_team'].'</strong> <span id="live_event_stav_'. intval($_POST['event_id']) .'" class="added">'.$this->retStav($h['event_id'],array('vypnuto'=>$h['vypnuto'],'stav'=>$h['stav'])).'</span>
                     <a href="javascript:delete live_ev[\''. intval($_POST['event_id']) .'\'];$(\'#live_event_'. intval($_POST['event_id']) .'\').remove();void(0);"><span class="ora">Zavřít okno</span></a> /
                     <a href="javascript:stopAll('.$h['event_id'].');void(0);"><span class="ora">Zastavit vše</span></a> /
                     <a href="javascript:runAll('.$h['event_id'].');void(0);"><span class="ora">Spustit vše</span></a> /
                     <a href="javascript:LoadBets('.$h['event_id'].',0);void(0);"><span class="ora">Seřadit</span></a> /
                     <a href="javascript:Money('.$h['event_id'].');void(0);"><span class="ora">Nasázeno</span></a>
                     Kurz povolování:  <input type="text"  style="width:60px" onblur="limits(1,'. intval($_POST['event_id']) .',this.value)" id="live_event_rate_'. intval($_POST['event_id']) .'" value="'. $h['limit_rate'] .'" />
                     Částka povolování: <input type="text" style="width:60px" onblur="limits(2,'. intval($_POST['event_id']) .',this.value)" id="live_event_amount_'. intval($_POST['event_id']) .'" value="'. $h['limit_bet'] .'" /> 
                     </div>

                     <div id="live_event_bets_'. intval($_POST['event_id']) .'">

                     <div style="clear:both;"></div>
                     </div>

                </div>';

        }

        $bet = mb_ereg_replace("[\n\r\t]+","",$bet);

        $this->json['js_dynamic'] = '$("#liveBody").append("'. addslashes($bet) .'");live_ev["'. intval($_POST['event_id']) .'"]= new Object();LoadBets('. intval($_POST['event_id']) .',0);';

    }

  }



   /**
 * Vraci stav live sazky
 * @param int $delete pravo smazani
 * @param array $h pole dat
 * @return void
 */
  private function retStav($event_id,array $h){

        if($h['vypnuto'] == 1)               $stav = "STOP";
        else if($h['stav'] == LIVE_NOT_STARTED)   $stav = "Nezahájeno";
        else if($h['stav'] == LIVE_BEGIN)    $stav = "Právě začalo";
        else if($h['stav'] == LIVE_END)      $stav = "Ukončeno";
        else if($h['stav'] == LIVE_1_HALF)   $stav = "1. poločas";
        else if($h['stav'] == LIVE_2_HALF)   $stav = "2. poločas";
        else if($h['stav'] == LIVE_1_THIRD)  $stav = "1. třetina";
        else if($h['stav'] == LIVE_2_THIRD)  $stav = "2. třetina";
        else if($h['stav'] == LIVE_3_THIRD)  $stav = "3. třetina";
        else if($h['stav'] == LIVE_1_Q)      $stav = "1. čtvrtina";
        else if($h['stav'] == LIVE_2_Q)      $stav = "2. čtvrtina";
        else if($h['stav'] == LIVE_3_Q)      $stav = "3. čtvrtina";
        else if($h['stav'] == LIVE_4_Q)      $stav = "4. čtvrtina";
        else if($h['stav'] == OVERTIME)      $stav = "Prodloužení";
        else if($h['stav'] == PAUSE)         $stav = "Přestávka";
        else if($h['stav'] == LIVE_1_SET)    $stav = "1. set";
        else if($h['stav'] == LIVE_2_SET)    $stav = "2. set";
        else if($h['stav'] == LIVE_3_SET)    $stav = "3. set";
        else if($h['stav'] == LIVE_4_SET)    $stav = "4. set";
        else if($h['stav'] == LIVE_5_SET)    $stav = "5. set";
        else if($h['stav'] == LIVE_WARMUP)    $stav = "Rozehra";
        else if($h['stav'] == LIVE_UNFINISHED)    $stav = "Neukončeno";
        else if($h['stav'] == LIVE_PENALTY)    $stav = "Penalty";
        else if($h['stav'] == LIVE_CANCELED)    $stav = "Zrušený zápas";
        else if($h['stav'] == LIVE_RACE_RUNNING)    $stav = "Probíhá závod";

        if($h['stav'] != LIVE_UNFINISHED && $h['stav'] != LIVE_END && $h['stav'] != LIVE_NOT_STARTED) $stav .= " / <span class=\"run\">RUN</span>";

        return $stav;

  }

   /**
 * Odhlaseni / Prihlaseni live sazky
 * @return void
 */
  public function signLive(){

    if(!isset($_POST['sign']) || !isset($_POST['event_id'])) $this->json['error'] = 'Nepodarilo se aktualizovat Live sázku code:101';


    try{

        $this->dbGame->update('live_betradar_event', array("no_update"=>(intval($_POST['sign'])==0?1:0)),"event_id=".intval($_POST['event_id']));
        $this->dbGame->update('live_event', array("vypnuto"=>(intval($_POST['sign'])==0?1:0)),"event_id=".intval($_POST['event_id']));

    }catch(Zend_Exception $e){

          $this->json['error'] = "Database error: " . $e->getMessage()  . __LINE__. "\n";

    }

    $this->newMatch();

  }


 /**
 * Nalezeni vsech betradar aktualich zapasu
 * @return void
 */
  public function newMatch(){

    $this->json['liveBHead'] = '';
    $preklady = new Preklady();

    $select = $this->dbGame->select()->from(array('a'=>'live_event'),array('home_team','away_team','b.no_update','a.vypnuto','snazev'=>'s.nazev','unazev'=>'u.nazev','a.event_id','a.start_date','a.stav','a.l_sport_id','a.l_udalost_id'))
                                     ->join(array('b'=>'live_betradar_event'),'a.event_id=b.event_id')
                                     ->join(array('s'=>'sport'),'s.sport_id=a.l_sport_id')
                                     ->join(array('u'=>'udalost'),'u.udalost_id=a.l_udalost_id');

    if(!isset($_POST['all_live'])){
      $select = $select->where("a.stav<>?",LIVE_END);
      $select = $select->where("a.stav<>?",LIVE_UNFINISHED);
    }

    $select = $select->order(array('a.start_date'));

    $stm  = $select->query();
    $row = $stm->fetchAll();

    $this->json['js_dynamic'] = '';
    $this->json['liveBHead'] .= '<thead><tr><th>&nbsp;</th><th>Identifikátor</th><th>Start</th><th>Sport</th><th>Trh</th><th>Soupeř</th><th>Stav</th><th>&nbsp;</th></tr></thead>';

    foreach ($row as $h){

        $sport   = $preklady->findPreklad($h['snazev'],1);
        $udalost = $preklady->findPreklad($h['unazev'],1);

       $this->json['js_dynamic'] .= '$("#live_event_stav_'. intval($h['event_id']) .'").html(\''.$this->retStav($h['event_id'],array('vypnuto'=>$h['vypnuto'],'stav'=>$h['stav'])).'\');';

        $this->json['liveBHead'] .= '<tr><td><input type="checkbox" '. (isset($_POST['loadBet'][$h['event_id']])?'checked="checked"':'' ) .'  class="chbox" onclick="if(this.checked) {ShowLive(1,'.$h['event_id'].');callUpdate=0;}else ShowLive(0,'.$h['event_id'].');" />
        <input type="checkbox" name="prove_ch" hod="'. $h['event_id'] .'"  class="chbox"  /></td>
                                     <td>#'.$h['event_id'].' </td>
                                     <td>'.It6_Date::fromDb($h['start_date']).'</td>
                                     <td>'.$sport[1].'</td>
                                     <td>'.$udalost[1].'</td>
                                     <td><strong>'.$h['home_team'].' - '.$h['away_team'].'</strong></td>
                                     <td>'.$this->retStav($h['event_id'],array('vypnuto'=>$h['vypnuto'],'stav'=>$h['stav'])).'</td>
                                     <td><a href="javascript:SignLive('.($h['no_update'] == 0?0:1).','.$h['event_id'].');void(0);">'.($h['no_update'] == 0?'Odhlásit':'<span class="ora">Přihlásit</span>').'</a> / <a href="javascript:fin('.($h['stav'] != LIVE_UNFINISHED && $h['stav'] != LIVE_END?0:1).','.$h['event_id'].');void(0);">'.($h['stav'] != LIVE_UNFINISHED && $h['stav'] != LIVE_END?'Ukončit':'<span class="ora">Obnovit</span>').'</a></td></tr>';


    }




    $select = $this->dbGame->select()->from(array("a"=>'live_betradar_log'),array('a.*'))->order("a.log_id desc")->limit(50);

    $stm  = $select->query();
    $rowD = $stm->fetchAll();

    if(count($rowD) == 0) return;


    $log = '';
    foreach($rowD as $h){

        $log .= $h['date']. ':  ' .nl2br(Help::Html($h['text']))."<br /><br />";
    }

    $this->json['log'] = $log;

  }

  /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){

   $this->update = $update;
   $this->delete = $delete;

  }


  /**
 * Vypise JSON
 * @return void
 */
  public  function PrintJson(){


     echo json_encode($this->json);
  }

 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){

    return $this->vrat;

  }

  public function __destruct(){




  }

}

?>
