<?php


/**
 * @package    livebet
 */

/**
 * Trida pro praci se live sazkami abstraktni
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Live bet
 */

abstract class LiveSport{

	/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
  protected  $dbGame;

 /**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
  protected   $db;

   /**
 * navratove pole
 * @access private
 * @var array
 */
  protected   $json = array();

  /**
 * jaka akce se ma proves
 * @access private
 * @var int
 */
  protected  $action;

    /**
 * zakadni data live sazky
 * @access private
 * @var array
 */
  protected   $event_data;

 /**
 * Sazka typ
 * @access protected
 * @var string
 */
  protected   $sbet_type;


  /**
 * Preddefinovane kurzy pro dva kurzy
 * @access protected
 * @var array
 */
   protected  $rate_def = array(
                            "1.08"=>array(
                                      array(1.01,11),
									  array(1.02,10),
									  array(1.03,9),
                                      array(1.05,8),
                                      array(1.07,7),
                                      array(1.08,6.5),
									  array(1.1,6),
									  array(1.12,5.25),
									  array(1.15,4.75),
									  array(1.18,4.35),
									  array(1.2,4),
									  array(1.23,3.8),
									  array(1.25,3.6),
									  array(1.28,3.4),
									  array(1.3,3.2),
									  array(1.35,2.95),
									  array(1.4,2.75),
									  array(1.45,2.55),
									  array(1.5,2.4),
									  array(1.55,2.3),
									  array(1.6,2.2),
									  array(1.65,2.1),
									  array(1.7,2.05),
									  array(1.75,1.95),
									  array(1.8,1.9),
									  array(1.85,1.85),
									  array(1.9,1.8),
                                      array(1.95,1.75),
                                      array(2.05,1.7),
                                      array(2.1,1.65),
                                      array(2.2,1.6),
                                      array(2.3,1.55),
                                      array(2.4,1.5),
                                      array(2.55,1.45),
                                      array(2.75,1.4),
                                      array(2.95,1.35),
                                      array(3.2,1.3),
                                      array(3.4,1.28),
                                      array(3.6,1.25),
                                      array(3.8,1.23),
                                      array(4,1.2),
                                      array(4.35,1.18),
                                      array(4.75,1.15),
                                      array(5.25,1.12),
                                      array(6,1.1),
                                      array(6.5,1.08),
                                      array(7,1.07),
                                      array(8,1.05),
                                      array(9,1.03),
                                      array(10,1.02),
                                      array(11,1.01)
                                    )
                               );

  /**
 * cachovani dat k sazkam
 * @access private
 * @var string
 */
  private   $cache = "<?php \n \$event=array();";


  	/**
* Konstruktor

*/
  public function __construct(){

  	$this->json['js_dynamic'] = '';

    if($this->action == 1){

  		$this->ReturnStav();
  		$this->ReturnTotalScore();
  		$this->ReturnMinute();
  		$this->ReturnPeriodScore();
  		$this->ReturnPeriodButton();
  		$this->ReturnBets();
  		$this->ReturnTextHistory();

  	}
  	elseif($this->action == 2){
  		$this->ActMinute();
  	}
    elseif($this->action == 3){
  		$this->RunMatch();
  	}
    elseif($this->action == 4){
  		$this->StopMatch();
  	}
    elseif($this->action == 5){
  		$this->SetPeriod();
  	}
    elseif($this->action == 6){
  		$this->UpdateAllInfo();
  	}
    elseif($this->action == 7){
  		$this->CreateBet();
  	}
    elseif($this->action == 8){
  		$this->StopBet();
  	}
    elseif($this->action == 9){
  		$this->BetTicketInfo();
  	}
    elseif($this->action == 10){
  		$this->SelectInfo();
  	}
    elseif($this->action == 11){
  		$this->CloseBet();
  	}
    elseif($this->action == 12){
  		$this->DeleteInfo();
  	}
    elseif($this->action == 13){
  		$this->DeleteFromTemplate();
  	}
  	elseif($this->action == 14){
  		$this->SetCourt();
  	}
    elseif($this->action == 15){
  		$this->FirstService();
  	}
    elseif($this->action == 16){
  		$this->SetNum();
  	}
    elseif($this->action == 17){
  		$this->RunStopBet();
  	}
    elseif($this->action == 18){
  		$this->SetTiebrek();
  	}
    elseif($this->action == 19){
  		$this->SetPermanentInfo();
  	}

  }

    /**
 * Spousti zastavene sazky
 * @return void
 */
  protected  function RunStopBet(){


    $sth = $this->dbGame->prepare("update sazky set status=0 where sazka_id in (select a.sazka_id from live_sazka a where a.event_id=? and close=0)");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se nastavit periodu');}
    $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
    if (PEAR::isError($res))  {$this->dbGame->rollback();$this->error('Nepodařilo se ukončit live sázku');}

    $sth3 = $this->dbGame->prepare("update live_sazka set aktualizace_sazka=? where event_id=?");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:5');}

    $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$this->event_data['event_id']));
    if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:6');}

    $this->ReturnBets();


  }


     /**
 * Vymaze z templatu
 * @return void
 */
  protected  function DeleteFromTemplate(){

  	if(isset($_POST['typ_id']) && isset($_POST['podtyp_id'])){

  	   $sth3 = $this->dbGame->prepare("select event_id from live where event_id=? and typ_id=? and podtyp_id=?");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id'],$_POST['typ_id'],$_POST['podtyp_id']));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

       if($res3->numRows() == 0){
  	    $sth3 = $this->dbGame->prepare("replace into live_template values (?,?,?,?)");
        if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

        $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id'],$_POST['typ_id'],$_POST['podtyp_id'],1));
        if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}
       }

       $this->ReturnBets();

  	}

  }

   /**
 * Maze info
 * @return void
 */
  protected  function DeleteInfo(){

  	 if(isset($_POST['id']) && is_numeric($_POST['id'])){

  	   $sth3 = $this->dbGame->prepare("delete from live_info where id=?");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array($_POST['id']));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

  	   $sth3 = $this->dbGame->prepare("update live_event set aktualizace=? where event_id=?");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$this->event_data['event_id']));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}


       $this->ReturnTextHistory();

  	 }else  $this->error('Info není definováno');

  }

 /**
 * Vraci textovou historii sazek
 * @return void
 */
  protected  function ReturnTextHistory(){

  	   $sth3 = $this->dbGame->prepare("select id,time,text,book_text from live_info where event_id=? order by time desc");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id']));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

       $his = '';
       $preklady = new Preklady();

       while ($row3 =& $res3->fetchRow()){

       	   $text = $preklady->findPreklad($row3['text'],1);

       	   $his .= '
            <div>
              <span>'.$row3['time'].' min.</span>
              '.$text[1].'
              <br />
              <input
                type="text"
                style="width:70px;font-size:10px;"
                id="special_info_text_edit_'.$row3['id'].'"
                value="'.(mb_strlen($row3['book_text'])>0?' '.$row3['book_text']:'').'"
              />
              <a href="javascript:EditInfo('.$row3['id'].');void(0)">
                <img src="_clip/new.png" />
              </a>
              <a href="javascript:DeleteInfo('.$row3['id'].');void(0)">
                <img src="_clip/x.gif" />
              </a>
            </div>
          ';

       }


  	   $this->json['special_info_history'] = $his;

  }


     /**
 * Close sazky
 * @return void
 */
  protected  function CloseBet(){

  	 if(isset($_POST['sazka_id']) && isset($_POST['typ_id']) && isset($_POST['podtyp_id'])){

  	   $this->dbGame->autoCommit(false);
  	   $status =  true;

  	   $sth3 = $this->dbGame->prepare("update sazky set status=2,platna_do=? where sazka_id=?");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$_POST['sazka_id']));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

  	   $sth3 = $this->dbGame->prepare("update live_sazka set aktualizace_sazka=?,close=1 where sazka_id=?");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$_POST['sazka_id']));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

  	   /*$sth3 = $this->dbGame->prepare("replace into live_template values (?,?,?,?)");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id'],$_POST['typ_id'],$_POST['podtyp_id'],1));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}*/

       if($status==true)
         $this->dbGame->commit();
       else
         $this->dbGame->rollback();

       $this->ReturnBets();

  	 }else  $this->error('Sázka nebyla zavřena');

  }

/**
 * Uklada stale info k live zapasu
 * @return void
 */
  protected function SetPermanentInfo(){

    $permanent_note = mysql_real_escape_string($_POST['permanent_info']);
    //$permanent_note = str_replace('!','\!',$permanent_note);
    //$permanent_note = str_replace('?','\?',$permanent_note);

    $sth = $this->dbGame->prepare("
      UPDATE live_".$this->event_data['l_sport_id']."
      SET note=?
      WHERE event_id=?
    ");
    DbUtil::testResult($sth, 'Unable to update bets.');

    $res =& $this->dbGame->execute($sth,array($permanent_note, $this->event_data['event_id']));
    DbUtil::testResult($res, 'Unable to update bets.');

    $this->ReturnPeriodScore();
  }





/**
 * Uklada info k sazkam
 * @return void
 */
  protected function SelectInfo(){

  	if(isset($_POST['eifno'])){

  	  $sth = $this->dbGame->prepare("
        UPDATE live_info
        SET book_text=?
        WHERE event_id=?
        AND id=?
      ");
      DbUtil::testResult($sth, 'Unable to save data');

      $res =& $this->dbGame->execute(
        $sth,
        array(
          $_POST['info_select_text'],
          $this->event_data['event_id'],
          $_POST['id']
        )
      );
      DbUtil::testResult($res, 'Unable to save data');

       $this->ReturnTextHistory();

  	}

    else{
      if(isset($_POST['special_info']) && $_POST['special_info'] != 'no'){

        if(
          ctype_digit($_POST['info_select_min'])
          && isset($_POST['info_select_min'])
          && isset($_POST['info_select_text'])
        ){
          $sth = $this->dbGame->prepare("
            INSERT INTO live_info (event_id,time,text,book_text)
            VALUES (?,?,?,?)
          ");
          DbUtil::testResult($sth, 'Unable to save data');

          $res =& $this->dbGame->execute(
            $sth,
            array(
              $this->event_data['event_id'],
              $_POST['info_select_min'],
              $_POST['special_info'],
              $_POST['info_select_text']
            )
          );
          DbUtil::testResult($res, 'Unable to save data');



          if(
            trim($_POST['special_info']) == 'live_zkdom'
            && (
              $this->event_data['l_sport_id'] == 1001
              || $this->event_data['l_sport_id'] == 1020
            )
          ){

            $sth = $this->dbGame->prepare("
              UPDATE live_".$this->event_data['l_sport_id']."
              SET yellow_card_home=yellow_card_home+1
              WHERE event_id=?
            ");
            DbUtil::testResult($sth, 'Unable to update bets.');

            $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
            DbUtil::testResult($res, 'Unable to update bets.');

            $this->ReturnPeriodScore();
          }



          else if(
            trim($_POST['special_info']) == 'live_zkhos'
            && (
              $this->event_data['l_sport_id'] == 1001
              || $this->event_data['l_sport_id'] == 1020
            )
          ){
            $sth = $this->dbGame->prepare("
              UPDATE live_".$this->event_data['l_sport_id']."
              SET yellow_card_away=yellow_card_away+1
              WHERE event_id=?
            ");
            DbUtil::testResult($sth, 'Unable to update bets.');

            $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
            DbUtil::testResult($res, 'Unable to update bets.');

            $this->ReturnPeriodScore();
          }



          else if(
            trim($_POST['special_info']) == 'live_ckdom'
            && (
              $this->event_data['l_sport_id'] == 1001
              || $this->event_data['l_sport_id'] == 1020
            )
          ){
            $sth = $this->dbGame->prepare("
              UPDATE live_".$this->event_data['l_sport_id']."
              SET red_card_home=red_card_home+1
              WHERE event_id=?
            ");
            DbUtil::testResult($sth, 'Unable to update bets.');

            $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
            DbUtil::testResult($res, 'Unable to update bets.');

            $this->ReturnPeriodScore();
          }



          else if(
            trim($_POST['special_info']) == 'live_ckhos'
            && (
              $this->event_data['l_sport_id'] == 1001
              || $this->event_data['l_sport_id'] == 1020
            )
          ){
            $sth = $this->dbGame->prepare("
              UPDATE live_".$this->event_data['l_sport_id']."
              SET red_card_away=red_card_away+1
              WHERE event_id=?
            ");
            DbUtil::testResult($sth, 'Unable to update bets.');

            $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
            DbUtil::testResult($res, 'Unable to update bets.');

            $this->ReturnPeriodScore();
          }



          else if(
            trim($_POST['special_info']) == 'live_goldom'
            && (
              $this->event_data['l_sport_id'] == 1001
              || $this->event_data['l_sport_id'] == 1020
            )
          ){
            if(($this->event_data['stav'] == LIVE_1_HALF))
              $where = ',first_half_home=first_half_home+1';
            else if($this->event_data['stav'] == LIVE_2_HALF)
              $where = ',second_half_home=second_half_home+1';
            else
              $where = '';

            if(mb_strlen($where) > 0){
              $sth = $this->dbGame->prepare("
                UPDATE live_".$this->event_data['l_sport_id']."
                SET score_home=score_home+1 ".$where."
                WHERE event_id=?
              ");
              DbUtil::testResult($sth, 'Unable to update bets.');

              $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
              DbUtil::testResult($res, 'Unable to update bets.');

              $sth = self::$this->dbGame->prepare("
                SELECT score_home
                FROM live_".$this->event_data['l_sport_id']."
                WHERE event_id=?
              ");
              DbUtil::testResult($sth, 'Unable to get data.');

              $res =& self::$this->dbGame->execute($sth,array($this->event_data['event_id']));
              DbUtil::testResult($res, 'Unable to get data.');

              $row =& $res->fetchRow();

              $this->json['js_dynamic'] .= '$("#score_home").attr("value",'.$row['score_home'].');';
              $this->ReturnPeriodScore();
              $this->ReturnTotalScore();
            }
          }



          else if(
            trim($_POST['special_info']) == 'live_golhos'
            && (
              $this->event_data['l_sport_id'] == 1001
              || $this->event_data['l_sport_id'] == 1020
            )
          ){
            if(($this->event_data['stav'] == LIVE_1_HALF))
              $where = ',first_half_away=first_half_away+1';
            else if($this->event_data['stav'] == LIVE_2_HALF)
              $where = ',second_half_away=second_half_away+1';
            else
              $where = '';

            if(mb_strlen($where) > 0){
              $sth = $this->dbGame->prepare("
                UPDATE live_".$this->event_data['l_sport_id']."
                SET score_away=score_away+1 ".$where."
                WHERE event_id=?
              ");
              DbUtil::testResult($sth, 'Unable to update bets.');

              $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
              DbUtil::testResult($res, 'Unable to update bets.');

              $sth = self::$this->dbGame->prepare("
                SELECT score_away
                FROM live_".$this->event_data['l_sport_id']."
                WHERE event_id=?
              ");
              DbUtil::testResult($sth, 'Unable to get data.');

              $res =& self::$this->dbGame->execute($sth,array($this->event_data['event_id']));
              DbUtil::testResult($res, 'Unable to get data.');

              $row =& $res->fetchRow();

              $this->json['js_dynamic'] .= '$("#score_away").attr("value",'.$row['score_away'].');';
              $this->ReturnPeriodScore();
              $this->ReturnTotalScore();
            }
          }



          else if(
            trim($_POST['special_info']) == 'live_goldom_hockey'
            && $this->event_data['l_sport_id'] == 1011
          ){
            if(($this->event_data['stav'] == LIVE_1_THIRD))
              $where = ',tretina_1_home=tretina_1_home+1';
            else if(($this->event_data['stav'] == LIVE_2_THIRD))
              $where = ',tretina_2_home=tretina_2_home+1';
            else if(($this->event_data['stav'] == LIVE_3_THIRD))
              $where = ',tretina_3_home=tretina_3_home+1';
            else
              $where = '';

            if(mb_strlen($where) > 0){
              $sth = $this->dbGame->prepare("
                UPDATE live_".$this->event_data['l_sport_id']."
                SET score_home=score_home+1 ".$where."
                WHERE event_id=?
              ");
              DbUtil::testResult($sth, 'Unable to update bets.');

              $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
              DbUtil::testResult($res, 'Unable to update bets.');

              $sth = self::$this->dbGame->prepare("
                SELECT score_home
                FROM live_".$this->event_data['l_sport_id']."
                WHERE event_id=?
              ");
              DbUtil::testResult($sth, 'Unable to get data.');

              $res =& self::$this->dbGame->execute($sth,array($this->event_data['event_id']));
              DbUtil::testResult($res, 'Unable to get data.');

              $row =& $res->fetchRow();

              $this->json['js_dynamic'] .= '$("#score_home").attr("value",'.$row['score_home'].');';
              $this->ReturnPeriodScore();
              $this->ReturnTotalScore();
            }
          }



          else if(
            trim($_POST['special_info']) == 'live_golhos_hockey'
            && $this->event_data['l_sport_id'] == 1011
          ){
            if(($this->event_data['stav'] == LIVE_1_THIRD))
              $where = ',tretina_1_away=tretina_1_away+1';
            else if(($this->event_data['stav'] == LIVE_2_THIRD))
              $where = ',tretina_2_away=tretina_2_away+1';
            else if(($this->event_data['stav'] == LIVE_3_THIRD))
              $where = ',tretina_3_away=tretina_3_away+1';
            else
              $where = '';

            if(mb_strlen($where) > 0){
              $sth = $this->dbGame->prepare("
                UPDATE live_".$this->event_data['l_sport_id']."
                SET score_away=score_away+1 ".$where."
                WHERE event_id=?
              ");
              DbUtil::testResult($sth, 'Unable to update bets.');

              $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
              DbUtil::testResult($res, 'Unable to update bets.');

              $sth = self::$this->dbGame->prepare("
                SELECT score_away
                FROM live_".$this->event_data['l_sport_id']."
                WHERE event_id=?
              ");
              DbUtil::testResult($sth, 'Unable to get data.');

              $res =& self::$this->dbGame->execute($sth,array($this->event_data['event_id']));
              DbUtil::testResult($res, 'Unable to get data.');

              $row =& $res->fetchRow();

              $this->json['js_dynamic'] .= '$("#score_away").attr("value",'.$row['score_away'].');';
              $this->ReturnPeriodScore();
              $this->ReturnTotalScore();
            }
          }

          $sth = $this->dbGame->prepare("
            UPDATE live_event
            SET aktualizace=?
            WHERE event_id=?
          ");
          DbUtil::testResult($sth, 'Unable to update bets.');

          $res =& $this->dbGame->execute($sth,array(It6_Date::dbNow(),$this->event_data['event_id']));
          DbUtil::testResult($res, 'Unable to update bets.');

          $this->ReturnTextHistory();

        }
        else
          $this->error('Informace se nepodařilo uložit - 1');

      }
      else
        $this->error('Informace se nepodařilo uložit - 2');

   }

  }

  /**
 * Vraci info k jednotlivym sloupcum kolik vsazeno na dany kurz kolik celkem vsazeno na dany sloupec
 * @return void
 */
  protected  function BetTicketInfo(){

  	$sth3 = $this->dbGame->prepare("select * from live_sazka where event_id=?");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:31');}

    $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id']));
    if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:12');}

    while ($row3 =& $res3->fetchRow()){

    	$i = $this->BetTicket($row3['sazka_id']);

    	foreach($i as $k=>$h){

    		foreach($h['poradi'] as $h2){
    			foreach($h2 as $h3) {
					if(!isset($this->json['info_'.$row3['sazka_id'].'_'.$k]))
						$this->json['info_'.$row3['sazka_id'].'_'.$k] = array();

    				$this->json['info_'.$row3['sazka_id'].'_'.$k] .= ''.$h3['num'].' x - '.round(floatval($h3['amount']),2).' EUR';
    			}
    			break;
    		}
    		$this->json['info_'.$row3['sazka_id'].'_'.$k] .= '<br /><span style="font-size:10px;">'.$h['num'].' x - '.round(floatval($h['amount']),2).' EUR</span>';
    		$this->cache .= '$event['.$row3['sazka_id'].']['.$k.']=\'' .$this->json['info_'.$row3['sazka_id'].'_'.$k]."';\n";
    	}

    }

    $fp = fopen($_SERVER["DOCUMENT_ROOT"].'/../../tmp/live_event_'.$this->event_data['event_id'].'.php','w');
    fwrite($fp,$this->cache);
    fclose($fp);

  }

     /**
 * Zastavi sazky
 * @return void
 */
  protected  function StopBet(){

  	 if(isset($_POST['sazka_id'])){

  	   $sth3 = $this->dbGame->prepare("update sazky set status=2 where sazka_id=?");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array($_POST['sazka_id']));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

  	   $sth3 = $this->dbGame->prepare("update live_sazka set aktualizace_sazka=? where sazka_id=?");
       if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

       $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$_POST['sazka_id']));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

       $this->ReturnBets();

  	 }else  $this->error('Sázka není definována');

  }

   /**
 * Vytvori sazky
 * @return void
 */
  protected  function CreateBet(){


  	$this->dbGame->autoCommit(false);

    $status = true;
    $vyhernost_num = 0;
    $date = It6_Date::dbNow();

   	$sql = "select kurz_min,kurz_max,vyhernost_min,vyhernost_max from bet_settings where udalost_id=".intval($this->event_data['l_udalost_id'])." and typ_id=".intval($_POST['typ_id'])." and podtyp_id=".intval($_POST['podtyp_id']);
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bet_settings',"admin_ex_db");

    if ($row =& $res->fetchRow()){
		$min		= $row['kurz_min'];
		$max		= $row['kurz_max'];
		$vyh_min	= $row['vyhernost_min'];
		$vyh_max	= $row['vyhernost_max'];
    }

    if($this->event_data['l_udalost_id'] == LIVE_END)  {$status = false;$this->error('Sázka už je ukončena není možné vytvářet kurzy');}

  	#vlozeni nebo akt sazky#

  	if(isset($_POST['comb'])) $comb = 1;else $comb = 0;
  	if(isset($_POST['simple'])) $simple = 1;else $simple = 0;
  	if(isset($_POST['risk']) && ctype_digit($_POST['risk'])) $risk = $_POST['risk'];else $risk = 300000;

  	if(isset($_POST['sazka_id'])){

  	 $sth3 = $this->dbGame->prepare("update sazky set jednoducha=?,risk_limit=?,text=?,status=0 where sazka_id=?");
     if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

     $res3 =& $this->dbGame->execute($sth3,array($simple,$risk,$_POST['text'],$_POST['sazka_id']));
     if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

  	 $sth3 = $this->dbGame->prepare("update live_sazka set aktualizace_sazka=? where sazka_id=?");
     if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

     $res3 =& $this->dbGame->execute($sth3,array($date,$_POST['sazka_id']));
     if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

  	}else{




  	 $sth3 = $this->dbGame->prepare("insert into sazky (platna_od,platna_do,live,bookmaker_id,udalost_id,typ_id,podtyp_id,jednoducha,risk_limit,text) values(?,?,?,?,?,?,?,?,?,?)");
     if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:3');}

     $res3 =& $this->dbGame->execute($sth3,array($date,'2020-02-02',1,$_SESSION['bookmaker'],$this->event_data['l_udalost_id'],$_POST['typ_id'],$_POST['podtyp_id'],$simple,$risk,$_POST['text']));
     if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:4');}

     $_POST['sazka_id'] = mysqli_insert_id($this->dbGame->connection);


  	 $sth3 = $this->dbGame->prepare("select sazka_id from live_kombinace where event_id=?");
     if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:17');}

     $sth4 = $this->dbGame->prepare("replace into sazka_kombinace (sazka1_id,sazka2_id,kombinace_show) values (?,?,0)");
     if (PEAR::isError($sth4))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:18');}

  	 $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id']));
     if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:17');}

     while ($row3 =& $res3->fetchRow()){
      $res4 =& $this->dbGame->execute($sth4,array($_POST['sazka_id'],$row3['sazka_id']));
      if (PEAR::isError($res4))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:18');}
     }

  	 $sth3 = $this->dbGame->prepare("insert into live_sazka  values (?,?,?,?,?,?,?,?)");
     if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:5');}

     $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id'],$_POST['sazka_id'],$comb,$date,$date,0,'betradar_sazka_id',0));
     if (PEAR::isError($res3))  {$status = false;$this->error($res3->getMessage().'Nepodařilo aktualizovat sazky code:11; '.$this->event_data['event_id'].' - '.$_POST['sazka_id'].' - '.$comb.' - '.$date.' - '.$date.' - ');}

     if($comb == 0){
  	  $sth3 = $this->dbGame->prepare("select sazka_id from live_sazka where no_comb=1 and event_id=?");
      if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:17');}
  	  $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id']));
      if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:17');}

      while ($row3 =& $res3->fetchRow()){

        $sth4 = $this->dbGame->prepare("replace into sazka_kombinace (sazka1_id,sazka2_id,kombinace_show) values (?,?,0)");
        if (PEAR::isError($sth4))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:13');}
        $res4 =& $this->dbGame->execute($sth4,array($_POST['sazka_id'],$row3['sazka_id']));
        if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:14');}

      }
     }

  	}

  	$sth3 = $this->dbGame->prepare("select sloupec_id,kurz,poradi from sazka_kurz where sazka_id=? order by platny_od desc");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:7');}

    $res3 =& $this->dbGame->execute($sth3,array($_POST['sazka_id']));
    if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:8');}

    $insert_new_rate = false;$max_poradi=1;
    $last_rate = array();
    while ($row3 =& $res3->fetchRow()){
         	if($max_poradi==1) $max_poradi = $row3['poradi'];
         	$last_rate[$row3['sloupec_id']] = $row3['kurz'];
         	if($max_poradi != $row3['poradi']) break;
         	if(!isset($_POST['sloupec'][$row3['sloupec_id']])){$status=false;break;}

         	if($row3['kurz'] != $_POST['sloupec'][$row3['sloupec_id']]) $insert_new_rate = true;


    }
    if($res3->numRows() == 0) $insert_new_rate = true;

    if($insert_new_rate){



      $sth3 = $this->dbGame->prepare("update live_sazka set aktualizace=? where event_id=?");
      if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:5');}

      $res3 =& $this->dbGame->execute($sth3,array($date,$this->event_data['event_id']));
      if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:15');}

      $sth3 = $this->dbGame->prepare("insert into sazka_kurz values(?,?,?,?,?,?)");
      if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:9');}

      foreach($_POST['sloupec'] as $sl_id=>$h){

       if($h>$max || $h<$min) {$status = false;$this->error('Kurz '.$h.' překročil hranici limitu výše kurzu');}
       $vyhernost_num += (1/$h);
       if(isset($last_rate[$sl_id]) && $h<$last_rate[$sl_id]) $kurz_zmena = -1;
       else if(isset($last_rate[$sl_id]) && $h>$last_rate[$sl_id]) $kurz_zmena = 1;
       else $kurz_zmena = 0;
       $res3 =& $this->dbGame->execute($sth3,array($_POST['sazka_id'],$sl_id,($max_poradi+1),$h,$date,$kurz_zmena));
       if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:10');}
      }

      if ($status) {
		Zend_Registry::get('ws')->Alert->assert('RateChange',
				array('betId' => intval($_POST['sazka_id']), 'bookmaker' => $_SESSION['bookmaker']));
     }

      $vyhernost_num = round($vyhernost_num,2);
      if($vyhernost_num<$vyh_min || $vyhernost_num>$vyh_max)  {$status = false;$this->error('Výhernost '.round($vyhernost_num,2).' je mimo povolenou hranici');}

    }else{

     $sth3 = $this->dbGame->prepare("update live_sazka set aktualizace_sazka=? where sazka_id=?");
     if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:1');}

     $res3 =& $this->dbGame->execute($sth3,array($date,$_POST['sazka_id']));
     if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:2');}

    }
    #END#

  	#Vyber sazek ktere jsou prirazeny k dane event_id#
  	$sazky_kombinace = array();

    $sth3 = $this->dbGame->prepare("select * from live_sazka where event_id=?");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:29');}

    $res3 =& $this->dbGame->execute($sth3,array($this->event_data['event_id']));
    if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:12');}

    while ($row3 =& $res3->fetchRow()){
         	$sazky_kombinace[] = $row3['sazka_id'];
    }

    $sth3 = $this->dbGame->prepare("update live_sazka set no_comb=? where sazka_id=?");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:17');}

    if($comb == 1){

        $res3 =& $this->dbGame->execute($sth3,array(1,$_POST['sazka_id']));
        if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:17');}

    	$sth3 = $this->dbGame->prepare("replace into sazka_kombinace (sazka1_id,sazka2_id,kombinace_show) values (?,?,0)");
        if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:13');}

        foreach($sazky_kombinace as $h){
         if($_POST['sazka_id'] != $h){//echo $_POST['sazka_id'].'- '.$h;
          $res3 =& $this->dbGame->execute($sth3,array($_POST['sazka_id'],$h));
          if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:14');}
         }
        }

    }else{

        $res3 =& $this->dbGame->execute($sth3,array(0,$_POST['sazka_id']));
        if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:17');}

    	$sth3 = $this->dbGame->prepare("delete from sazka_kombinace  where (sazka_kombinace.sazka1_id=? or sazka_kombinace.sazka2_id=?) and not exists (select b.sazka_id from live_kombinace b where b.event_id=? and (sazka_kombinace.sazka1_id=b.sazka_id or sazka_kombinace.sazka2_id=b.sazka_id) )");
        if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:15');}

        $res3 =& $this->dbGame->execute($sth3,array($_POST['sazka_id'],$_POST['sazka_id'],$this->event_data['event_id']));
        if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:16');}

    }

    #END#


  	if(!$status){$this->dbGame->rollback();}else {$this->dbGame->commit();$this->ReturnBets(); }



  }

  	/**
* Historie kurzu sazky
*
* @param int $sazka_id id sazky
* @var array
*/
  public function BetHistory($sazka_id=NULL){


  	if($sazka_id == NULL) return array();

  	$bet = array();

    $sth3 = $this->dbGame->prepare("select * from sazka_kurz where sazka_id=? order by platny_od desc");
    if (PEAR::isError($sth3))  {$this->error('Nepodařilo se najit sazky');}

    $res3 =& $this->dbGame->execute($sth3,array($sazka_id));
    if (PEAR::isError($res3))  {$this->error('Nepodařilo se najit histroii sazky');}

    while ($row3 =& $res3->fetchRow()){
         	if(!isset($bet[$row3['poradi']]))
         	$bet[$row3['poradi']]['platny_od'] = It6_Date::fromDb($row3['platny_od']);
         	$bet[$row3['poradi']]['sloupec'][$row3['sloupec_id']] = $row3['kurz'];
    }

    return $bet;

  }

 /**
* Historie tiketu na danou sazku
*
* @param int $sazka_id id sazky
* @var array
*/
  public function BetTicket($sazka_id=NULL){


  	if($sazka_id ==  NULL) return array();

  	$ticket = $sloupec = array();

  	$sth3 = $this->dbGame->prepare("select f.sloupec_id,f.platny_od,f.poradi from sazka_kurz f where f.sazka_id=? order by f.platny_od desc");
    if (PEAR::isError($sth3))  {$this->error('Nepodařilo se najit sazky');}

    $res3 =& $this->dbGame->execute($sth3,array($sazka_id));
    if (PEAR::isError($res3))  {$this->error('Nepodařilo se najit histroii sazky');}

    while ($row3 =& $res3->fetchRow()){
    	$sloupec[$row3['sloupec_id']]['num'] = 0;
    	$sloupec[$row3['sloupec_id']]['amount'] = 0;
    	//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
    	$platny_od = It6_Date::fromDbAsTimestamp($row3['platny_od']);
    	$sloupec[$row3['sloupec_id']]['poradi'][$row3['poradi']][$platny_od]['num'] = 0;
    	$sloupec[$row3['sloupec_id']]['poradi'][$row3['poradi']][$platny_od]['amount'] = 0;
    }

    $sth3 = $this->dbGame->prepare("select f.ticket_id from ticket_kurz f where f.sazka_id=? ");
    if (PEAR::isError($sth3))  {$this->error('Nepodařilo se najit sazky');}

    $res3 =& $this->dbGame->execute($sth3,array($sazka_id));
    if (PEAR::isError($res3))  {$this->error('Nepodařilo se najit histroii sazky');}

    $tid = "0";
    while ($row3 =& $res3->fetchRow()){
    	 $tid .= ','. $row3['ticket_id'];
    }

    $sth3 = $this->dbGame->prepare("select p.ticket_id,p.sazka_id,p.zalozen,p.sloupec_id,(castka/(select e.kurz from kurzmena e where e.platny_od<=now() and e.platny_do>=now() and e.mena_id=u.mena_id)) AS castka from ticket_pohled p inner join uzivatel u on p.user_id=u.user_id where ticket_id in(".$tid.") ");
    if (PEAR::isError($sth3))  {$this->error('Nepodařilo se najit sazky');}

    $res3 =& $this->dbGame->execute($sth3);
    if (PEAR::isError($res3))  {$this->error('Nepodařilo se najit histroii sazky');}

    while ($row3 =& $res3->fetchRow()){

         	if(!isset($ticket[$row3['ticket_id']])){

         		$ticket[$row3['ticket_id']]['num'] = 0;
         		//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
         		$ticket[$row3['ticket_id']]['zalozen'] =  It6_Date::fromDbAsTimestamp($row3['zalozen']);
         		$ticket[$row3['ticket_id']]['castka'] = $row3['castka'];

         	}
         	if(isset($sloupec[$row3['sloupec_id']]) && $row3['sazka_id'] == $sazka_id) {

         		$ticket[$row3['ticket_id']]['sloupec']=$row3['sloupec_id'];$sloupec[$row3['sloupec_id']]['num']++;

         		foreach($sloupec[$row3['sloupec_id']]['poradi'] as $poradi_id=>$h){

         			foreach($h as $platny_od=>$h2){


         				if($ticket[$row3['ticket_id']]['zalozen'] >= $platny_od) {$sloupec[$row3['sloupec_id']]['poradi'][$poradi_id][$platny_od]['num']++;break 2;}

         		    }

         		}

         	}

         	$ticket[$row3['ticket_id']]['num']++;
    }

    foreach($ticket as $h){

      	$pomerna_castka = ($h['castka']/$h['num']);

    	$sloupec[$h['sloupec']]['amount']  += $pomerna_castka;

       foreach($sloupec[$h['sloupec']]['poradi'] as $poradi_id=>$h2){

        	foreach($h2 as $platny_od=>$h3){


         				if($h['zalozen'] >= $platny_od) {$sloupec[$h['sloupec']]['poradi'][$poradi_id][$platny_od]['amount']+=$pomerna_castka;break 2;}

         	}

       	}

    }


    return $sloupec;

  }

 /**
 * Vraci sazky
 * @return void
 */
  protected  function ReturnBets(){


    if(file_exists($_SERVER["DOCUMENT_ROOT"].'/./../tmp/live_event_'.$this->event_data['event_id'].'.php')){
      require_once $_SERVER["DOCUMENT_ROOT"].'/./../tmp/live_event_'.$this->event_data['event_id'].'.php';
    }

    $sl_help = array();
    $bet = array();
    $template = array();
    $sloupec_name = array();
    $poradi = array();

    $preklady = new Preklady();

    $sth4 = $this->dbGame->prepare("
      SELECT sazka1_id FROM sazka_kombinace WHERE sazka1_id=? OR sazka2_id=?
    ");
    if (PEAR::isError($sth4))  {$this->error('Nepodařilo se najit sazky');}

    $sth2 = $this->dbGame->prepare("
      SELECT a.*,t.nazev AS unazev FROM live a
      INNER JOIN typ t ON t.typ_id=a.typ_id
      WHERE a.event_id=?
      ORDER BY a.poradi,a.podtyp_id,a.sazka_id,a.poradi_sloupec
    ");
    if (PEAR::isError($sth2))  {$this->error('Nepodařilo se najit sazky');}

    $res2 =& $this->dbGame->execute($sth2,array($this->event_data['event_id']));
    if (PEAR::isError($res2))  {$this->error('Nepodařilo se najit sazky');}

    $this->sbet_type = '
      <a href="#top" class="typhash" onclick="jQuery.ShowType(0,\'\');" style="width:600px">TOP</a>
    ';


    while ($row =& $res2->fetchRow()){

      if(!isset($template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']])){

        $row['unazev'] = $preklady->findPreklad($row['unazev'],1);
        $row['unazev'] = $row['unazev'][1];

        $res4 =& $this->dbGame->execute($sth4,array($row['sazka_id'],$row['sazka_id']));
        if (PEAR::isError($res4))  {$this->error('Nepodařilo se najit sazky');}

        $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['tnazev'] = $row['unazev'];
        $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['risk_limit'] =  $row['risk_limit'];
      	$template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['risk_limit_balance'] =  $row['risk_limit_balance'];
      	$template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['vypnuto'] =  ($row['status']==2?1:0);
      	$template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['sazka_id'] =  $row['sazka_id'];
      	$template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['close'] =  $row['close'];
      	$template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['text'] =  $row['text'];

        if($row['jednoducha']==1){
          $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['jednoducha'] =  1;
        }

        $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['comb'] =  $row['no_comb'];
        $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['history'] = $this->BetHistory($row['sazka_id']);
//        $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['ticket']  = $event[$row['sazka_id']];

	$preklady = new Preklady();
	$text = $preklady->findPreklad($row['text'],1);
        if($row['close'] == 0){
          $this->sbet_type .= '
            <a href="#hash_'.$row['sazka_id'].'_'.$row['typ_id'].'_'.$row['podtyp_id'].'" style="color:orange" class="typhash" onclick="jQuery.ShowType(0,\''.$row['sazka_id'].'_'.$row['typ_id'].'_'.$row['podtyp_id'].'\');">
              '.$row['unazev'].' '.($text[1] != 'Translation not found'?$text[1]:'').' '.Help::Html($row['text']).'
            </a>
          ';
        }

      }

      if(!isset($poradi[$row['sazka_id']])){
        $poradi[$row['sazka_id']] = $row['poradi'];
      }

      if($poradi[$row['sazka_id']] < $row['poradi']){
        unset($sl_help[$row['sazka_id']]);
      }

      $poradi[$row['sazka_id']]  = $row['poradi'];

      $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['sloupec'][$row['sloupec_id']] = $row['kurz'];
      $sl_help[$row['sazka_id']][] = $row['kurz'];

      if(!isset($template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['js'])){
        $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['js']=$row['sloupec_id'];
      }
      else{
        $template[$row['sazka_id']][$row['typ_id']][$row['podtyp_id']]['js'] .= ','.$row['sloupec_id'];
      }

    }

    $sth = $this->dbGame->prepare("
      SELECT s.nazev AS snazev,s.sloupec_id FROM podtyp_sloupce s ORDER BY poradi
    ");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se najit sazky');}

    $res =& $this->dbGame->execute($sth);
    if (PEAR::isError($res))  {$this->error('Nepodařilo se najit sazky');}


    while ($row =& $res->fetchRow()){
    	$text = $preklady->findPreklad($row['snazev'],1);
    	if($text[1] == 'Translation not found'){
        $text[1] = $row['snazev'];
      }
    	$sloupec_name[$row['sloupec_id']] = $text[1];
    }


  	$sth = $this->dbGame->prepare("
      SELECT a.typ_id,a.podtyp_id,t.nazev
      AS unazev,s.sloupec_id,s.nazev
      AS snazev,p.text FROM typ_podtyp a
      INNER JOIN podtyp p on a.podtyp_id=p.podtyp_id
      INNER JOIN podtyp_sloupce s ON a.podtyp_id=s.podtyp_id
      INNER JOIN typ t ON t.typ_id=a.typ_id
      WHERE EXISTS (
        SELECT v.typ_id
        FROM typ_podtyp v
        WHERE v.live_bet=1
        AND v.sport_id=".$this->event_data['l_sport_id']."
        AND v.typ_id=a.typ_id
        AND v.podtyp_id=a.podtyp_id
      )
      AND NOT EXISTS (
        SELECT h.event_id
        FROM live_template h
        WHERE h.event_id=".intval($this->event_data['event_id'])."
        AND h.typ_id=a.typ_id
        AND h.podtyp_id=a.podtyp_id
      )
      AND a.live_bet=1
      AND a.sport_id=?
      ORDER by t.poradi,p.podtyp_id,s.poradi
    ");

    if (PEAR::isError($sth))  {$this->error('Nepodařilo se najit sazky');}
    $res =& $this->dbGame->execute($sth,array($this->event_data['l_sport_id']));
    if (PEAR::isError($res))  {$this->error('Nepodařilo se najit sazky');}



    while ($row =& $res->fetchRow()){

      if(!isset($template[0][$row['typ_id']][$row['podtyp_id']])){

        if($row['text'] != ""){
          $text = $preklady->findPreklad($row['text'],1);
          $template[0][$row['typ_id']][$row['podtyp_id']]['text'] = $text[1];
        }
        else{
          $template[0][$row['typ_id']][$row['podtyp_id']]['text'] = "";
        }

        $row['unazev'] = $preklady->findPreklad($row['unazev'],1);
        $row['unazev'] = $row['unazev'][1];
        $template[0][$row['typ_id']][$row['podtyp_id']]['tnazev'] = $row['unazev'];

        $template[0][$row['typ_id']][$row['podtyp_id']]['jednoducha'] = 1;
        $this->sbet_type .= '
          <a href="#hash_0_'.$row['typ_id'].'_'.$row['podtyp_id'].'" class="typhash" onclick="jQuery.ShowType(0,\'0_'.$row['typ_id'].'_'.$row['podtyp_id'].'\');" >
            '.$row['unazev'].' '.($text[1] != 'Translation not found'?$text[1]:'').'
          </a>
        ';
      }


    	$template[0][$row['typ_id']][$row['podtyp_id']]['sloupec'][$row['sloupec_id']] = '';

      if(!isset($template[0][$row['typ_id']][$row['podtyp_id']]['js'])){
        $template[0][$row['typ_id']][$row['podtyp_id']]['js']=$row['sloupec_id'];
      }
      else{
        $template[0][$row['typ_id']][$row['podtyp_id']]['js'] .= ','.$row['sloupec_id'];
      }

    }


    foreach($this->rate_def as $rk=>$rh){
      $this->json['js_dynamic'] .= 'rate_def_ob["'. $rk .'"] = new Object();';
      foreach($rh as $rk2=>$rh2){
        $this->json['js_dynamic'] .= 'rate_def_ob["'. $rk .'"]["'. $rk2 .'"] = new Array();';
        $this->json['js_dynamic'] .= 'rate_def_ob["'. $rk .'"]["'. $rk2 .'"][0]='. $rh2[0] .';';
        $this->json['js_dynamic'] .= 'rate_def_ob["'. $rk .'"]["'. $rk2 .'"][1]='. $rh2[1] .';';
      }
    }

    $this->json['live_bet'] = '';

    $this->json['bettype'] = $this->sbet_type;

    foreach($template as $s_id=>$h4){
      foreach($h4 as $typ_id=>$h){
        foreach($h as $podtyp_id=>$h2){
          if(isset($h2['close']) && $h2['close'] == 1) continue;
//die(var_dump(intval($this->event_data['l_udalost_id'])).' '.var_dump(intval($typ_id)).' '.var_dump(intval($podtyp_id)));
          $sql = "
            SELECT kurz_min,kurz_max,vyhernost_min,vyhernost_max
            FROM bet_settings
            WHERE udalost_id=".intval($this->event_data['l_udalost_id'])."
            AND typ_id=".intval($typ_id)."
            AND podtyp_id=".intval($podtyp_id)
          ;
          $res =& $this->dbGame->query($sql);
          if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bet_settings',"admin_ex_db");

          if ($row =& $res->fetchRow()){
             $min = $row['kurz_min'];$max = $row['kurz_max'];
             $vyh_min = $row['vyhernost_min'];
             $vyh_max = $row['vyhernost_max'];
          }
          else{
            $min = 0;$max = 0;$vyh_min = 0;$vyh_max = 0;
          }
          $this->json['live_bet'] .= '
<!-- Live bet-->
            <div class="live_bet_row" id="bet_row_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'" '.($s_id == 0?'name="shdb" ':'').'>
              <a name="hash_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'"></a>

              <div class="live_bet_row_head" '.($s_id ==0?'style="background-color:#bfbd82"':'').' >
                <div style="float:left">

                '.($s_id == 0?'
                  <a href="javascript:if(confirm(\'Opravdu chcete odstranit?\'))DeleteFromTemplate('.$typ_id.','.$podtyp_id.');void(0);">
                    <img src="_clip/x.gif" class="img" alt="Vymazat z templatu" />
                  </a>
                  <strong>Nebyla vypsána</strong>'
                :($s_id !=0 && $template[$s_id][$typ_id][$podtyp_id]['close']==1?'
                  <strong>CLOSED</strong>'
                :'')).'

                <strong style="font-size:13px;color:">'.$h2['tnazev'].'</strong>:
                <span style="font-size:12px;font-weight:bold;">'.Help::Html($h2['text']).'</span>
                <input
                  type="text"
                  id="xbet_text_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'"
                  style="width:220px;font-size:11px"
                  value="'.($s_id!=0?Help::Html($template[$s_id][$typ_id][$podtyp_id]['text']):'').'" />
              </div>

              <div style="float:right">
                <a href="javascript:void(0);"
                  '.($s_id != 0 && $template[$s_id][$typ_id][$podtyp_id]['close']==1?'
                    style="display:none"':'').' onclick="RunBet('.$s_id.','.$typ_id.','.$podtyp_id.',['.$h2['js'].'])">
                    <img src="_clip/check_yes.gif" class="img" alt="Spustit" />
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                <a href="javascript:void(0);"
                  '.($s_id == 0 || $template[$s_id][$typ_id][$podtyp_id]['close']==1?'
                    style="display:none"':'').' onclick="StopBet('.$s_id.','.$typ_id.','.$podtyp_id.')">
                    <img src="_clip/check_no.gif" class="img" alt="Zastavit" />
                </a>
                &nbsp;&nbsp;&nbsp;&nbsp;

                <a href="javascript:void(0);"
                  '.($s_id == 0 || $template[$s_id][$typ_id][$podtyp_id]['close']==1?'
                    style="display:none"':'').' onclick="if(confirm(\'Opravdu chcete sázku zavřít?\'))CloseBet('.$s_id.','.$typ_id.','.$podtyp_id.')">
                    <strong style="color:red">CLOSE</strong>
                </a>

              </div>
            </div>

            <div class="live_bet_row_left '.($s_id != 0 && $template[$s_id][$typ_id][$podtyp_id]['vypnuto']==1?'nonactive':'').'">
              <script>bet_sloupec["'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'"] = new Object();</script>
          ';


          $y = 0;
          $this->json['js_dynamic'] .= 'rate_def_sl["'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'"] = new Array();';


          foreach($h2['sloupec'] as $sloupec_id=>$h3){

            $this->json['live_bet'] .= '
  <!-- Jeden box-->
              <div class="one_rate">
                <div class="one_rate_head">'.Help::Html($sloupec_name[$sloupec_id]).'</div>
                <div class="one_rate_r">
                  <input
                    type="text"
                    id="bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_sloupec_'.$sloupec_id.'"
                    onkeyup="SetVyhernostLive('.$s_id.','.$typ_id.','.$podtyp_id.')"  value="'.(isset($template[$s_id][$typ_id][$podtyp_id]['sloupec'][$sloupec_id])?$template[$s_id][$typ_id][$podtyp_id]['sloupec'][$sloupec_id]:2).'"
                    class="one_rate_input" />

                  <div class="arrow_move">
                    <a
                      href="javascript:void(0);"
                      onclick="ChValue(\'plus\',\'bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_sloupec_'.$sloupec_id.'\',2,0,'.$typ_id.','.$podtyp_id.','.$y.','.$s_id.');SetVyhernostLive('.$s_id.','.$typ_id.','.$podtyp_id.');"
                      class="arrow">
                      <img src="_clip/arrow_up.gif" class="img" alt="+1" />
                    </a>
                    <a
                      href="javascript:void(0);"
                      onclick="ChValue(\'minus\',\'bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_sloupec_'.$sloupec_id.'\',2,0,'.$typ_id.','.$podtyp_id.','.$y.','.$s_id.');SetVyhernostLive('.$s_id.','.$typ_id.','.$podtyp_id.');"
                      class="arrow">
                      <img src="_clip/arrow_down.gif" class="img" alt="-1" />
                    </a>
                  </div>

                </div>
                <div class="one_rate_text" '.($s_id != 0?'id="info_'.$s_id.'_'.$sloupec_id:'').'">
            ';

            if(isset($template[$s_id][$typ_id][$podtyp_id]['ticket'])){

/*
  foreach($bet[$typ_id][$podtyp_id]['ticket'][$sloupec_id]['poradi'] as $poradi){
    foreach($poradi as $platne_od){
      $this->json['live_bet'] .= ''.$platne_od['num'].' x - '.round(floatval($platne_od['amount']),2).' EUR';
    }
    break;
  }

  $this->json['live_bet'] .=	'<br /><span style="font-size:10px;">'.$bet[$typ_id][$podtyp_id]['ticket'][$sloupec_id]['num'].' x - '.round(floatval($bet[$typ_id][$podtyp_id]['ticket'][$sloupec_id]['amount']),2).' EUR</span>';
*/

              $this->json['live_bet'] .= $template[$s_id][$typ_id][$podtyp_id]['ticket'][$sloupec_id];
            }
            else{
              $this->json['live_bet'] .='<span style="font-size: 10px;"></span>';
            }


            $this->json['live_bet'] .= '
              </div>
              </div>
<!--Konec  Jeden box-->
              <script>
                bet_sloupec["'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'"]['.$sloupec_id.'] = "bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_sloupec_'.$sloupec_id.'";
              </script>
            ';


            $y++;
            $this->json['js_dynamic'] .= 'rate_def_sl["'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'"][rate_def_sl["'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'"].length] = '.$sloupec_id.'; ';
          }


          if(count($h2['sloupec']) == 2){
            $this->json['live_bet'] .= '<div class="one_rate">';
            $xxx = 0;
            foreach($this->rate_def as $rk=>$rh){

              $xy = 0;
              foreach($rh as $rh2){
// echo $rh2[0] . ' - ' .  $rh2[1] ."\n";
//echo $sl_help[0]  . ' - ' . $sl_help[1]."\n";

                if($s_id != 0 && $rh2[0] == $sl_help[$s_id][0]  && $rh2[1] == $sl_help[$s_id][1]) $this->json['js_dynamic'] .= 'RateDef(1,'. $rk .','.$typ_id.','.$podtyp_id.','.$s_id.','.$xy.'); ';
                $xy++;
              }

              $this->json['live_bet'] .= '
                <span style="color:white">'. $rk .'</span>
                <input
                  type="checkbox"
                  '.($xxx == 0?'checked="checked"':'').'
                  onclick="if(this.checked) RateDef(1,'. $rk .','.$typ_id.','.$podtyp_id.','.$s_id.',0);else RateDef(0,'. $rk .','.$typ_id.','.$podtyp_id.','.$s_id.',0); " />';
                if($xxx == 0)$this->json['js_dynamic'] .= 'RateDef(1,'. $rk .','.$typ_id.','.$podtyp_id.','.$s_id.',0);
              ';
              $xxx++;
            }

            $this->json['live_bet'] .= '</div>';
          }



          $this->json['live_bet'] .= '
            <br clear="all"/>
            <input
              type="text"
              id="vyhernost_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'"
              value="0"
              style="height:11px;font-size:11px;width:50px;" />
            <small style="color:white">
              Min - Max kurz: '.$min.' - '.$max.'   Max - Min výhernost:  '.$vyh_min.' - '.$vyh_max.'
            </small>
          </div>

          <div class="live_bet_row_right">
<!--
	<a
		href="javascript:void(0);"
		'.(isset($template[$s_id][$typ_id][$podtyp_id]['close']) && $template[$s_id][$typ_id][$podtyp_id]['close']==1?'style="display:none"':'').'
		onclick="RunBet('.$s_id.','.$typ_id.','.$podtyp_id.',['.$h2['js'].'],'.(isset($template[$s_id][$typ_id][$podtyp_id]['sazka_id'])?$template[$s_id][$typ_id][$podtyp_id]['sazka_id']:0).')"
	>
		<img src="_clip/check_yes.gif" class="img" alt="Spustit" />
	</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<a
		href="javascript:void(0);"
		'.(!isset($template[$s_id][$typ_id][$podtyp_id]['close']) || $template[$s_id][$typ_id][$podtyp_id]['close']==1?'style="display:none"':'').'
		onclick="StopBet('.$s_id.','.$typ_id.','.$podtyp_id.','.(isset($template[$s_id][$typ_id][$podtyp_id]['sazka_id'])?$template[$s_id][$typ_id][$podtyp_id]['sazka_id']:0).')"
	>
		<img src="_clip/check_no.gif" class="img" alt="Zastavit" />
	</a>
	&nbsp;&nbsp;&nbsp;&nbsp;
	<a
		href="javascript:void(0);"
		'.(!isset($template[$s_id][$typ_id][$podtyp_id]['close']) || $template[$s_id][$typ_id][$podtyp_id]['close']==1?'style="display:none"':'').'
		onclick="if(confirm(\'Opravdu chcete sázku zavřít?\'))CloseBet('.$typ_id.','.$podtyp_id.','.(isset($template[$s_id][$typ_id][$podtyp_id]['sazka_id'])?$template[$s_id][$typ_id][$podtyp_id]['sazka_id']:0).')"
	>
		<strong style="color:red">CLOSE</strong>
	</a>
	<br />
-->
          <input
            type="text"
            class="w100"
            id="bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_risk"
            value="'.(isset($template[$s_id][$typ_id][$podtyp_id]['risk_limit'])?$template[$s_id][$typ_id][$podtyp_id]['risk_limit']:300000).'"  />

          <span class="blackWeight">
            / '.(isset($template[$s_id][$typ_id][$podtyp_id]['risk_limit_balance'])?$template[$s_id][$typ_id][$podtyp_id]['risk_limit_balance']:'-').'
          </span>

          <br />Podpůrnost ne:
          <input
            type="checkbox"
            id="bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_comb"
            class=""
            '.(isset($template[$s_id][$typ_id][$podtyp_id]['comb']) && $template[$s_id][$typ_id][$podtyp_id]['comb']==0 ?'':'checked="checked"').'  />

          <br />Pouze jednoduchá:
          <input
            type="checkbox"
            id="bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_jednoducha"
            class=""
            '.(isset($template[$s_id][$typ_id][$podtyp_id]['jednoducha'])?'checked="checked"':'').' />

          <a href="javascript:void(0);">
            <img src="_clip/check_clock.gif" onclick="($(\'#bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_history\').css(\'display\')==\'none\'?$(\'#bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_history\').show():$(\'#bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_history\').hide())" class="img" alt="Historie kurzu" />
          </a>

        </div>
        <div class="live_bet_row_bottom" id="bet_'.$s_id.'_'.$typ_id.'_'.$podtyp_id.'_history" style="display:none">
          <table class="ultable">
            <tr class="ul_row tr_th">
              <td class="ul_cell ol_th">&nbsp;</td>
          ';

          foreach($sloupec_name as $sloupec_id=>$h){
            if(isset($template[$s_id][$typ_id][$podtyp_id]['history'][1]['sloupec'][$sloupec_id]) || isset($template[$s_id][$typ_id][$podtyp_id]['history'][2]['sloupec'][$sloupec_id])){
              $this->json['live_bet'] .= '<td class="ul_cell ol_th">'.Help::Html($h).'</td>';
            }
          }

          if(isset($template[$s_id][$typ_id][$podtyp_id]['history'])){

            foreach($template[$s_id][$typ_id][$podtyp_id]['history'] as $poradi=>$h){
              $this->json['live_bet'] .= '
                </tr>
                <tr class="ul_row">
                  <td class="ul_cell">'.Help::Html($h['platny_od']).'</td>
              ';

              foreach($sloupec_name as $sloupec_id => $h2){
                if(isset($template[$s_id][$typ_id][$podtyp_id]['history'][($poradi-1)]['sloupec'][$sloupec_id]) && $h['sloupec'][$sloupec_id]>$template[$s_id][$typ_id][$podtyp_id]['history'][($poradi-1)]['sloupec'][$sloupec_id]){
                  $class = 'rate_up';
                }
                elseif(isset($template[$s_id][$typ_id][$podtyp_id]['history'][($poradi-1)]['sloupec'][$sloupec_id]) && $h['sloupec'][$sloupec_id]<$template[$s_id][$typ_id][$podtyp_id]['history'][($poradi-1)]['sloupec'][$sloupec_id]){
                  $class = 'rate_down';
                }
                else{
                  $class = '';
                }

                if(isset($h['sloupec'][$sloupec_id])){
                  $this->json['live_bet'] .= '
                    <td class="ul_cell '.$class.'">'.$h['sloupec'][$sloupec_id].'</td>
                  ';
                }
              }

              $this->json['live_bet'] .= ' </tr>';
            }
          }

          $this->json['live_bet'] .= '
            </table>
            </div>
            </div>
<!-- End Live bet-->
          ';
        }
      }
    }

    $this->json['live_bet']  .= '<script>SHDB(3);</script>';

  }


   /**
 * Poslani upozorneni tem co si to prali
 * @return void
 */
  protected  function sendNotice(){



     $sth = $this->dbGame->prepare("select b.email,b.lang_id from live_notice a inner join uzivatel b on a.user_id=b.user_id where a.event_id=?");
     if (PEAR::isError($sth))  {$this->error('Nepodařilo se nastavit periodu');}
     $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
     if (PEAR::isError($res))  {$this->error('Nepodařilo se nastavit periodu');}

     //include('shared/htmlMimeMail/htmlMimeMail.php');

     $preklad = new Preklady();


	 $sub = $body = $l = $sport = $udalost = array();

     while ($row =& $res->fetchRow()){

      if(!isset($sub[$row['lang_id']])) {$sub[$row['lang_id']]  = $preklad->FindPreklad('mail_live_notice_sub',$row['lang_id']); $sub[$row['lang_id']]  = $sub [$row['lang_id']][$row['lang_id']];}
      if(!isset($body[$row['lang_id']])) {$body[$row['lang_id']]  = $preklad->FindPreklad('mail_live_notice_b',$row['lang_id']); $body[$row['lang_id']]  = $body [$row['lang_id']][$row['lang_id']];}

      $l[$row['lang_id']][] = $row['email'];


     }

     $sth = $this->dbGame->prepare("select a.event_id,a.home_team,a.away_team,a.start_date,b.nazev as unazev,c.nazev as snazev from live_event a inner join udalost b on a.l_udalost_id=b.udalost_id inner join sport c on a.l_sport_id=c.sport_id  where a.event_id=?");
     if (PEAR::isError($sth))  {$this->error('Nepodařilo se nastavit periodu');}
     $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
     if (PEAR::isError($res))  {$this->error('Nepodařilo se nastavit periodu');}

     if ($row =& $res->fetchRow()){

     	$team = '<a href="https://'.WEBHOST.'/live-betting/?e='.$row['event_id'].'">'.$row['home_team'].' - '.$row['away_team'].'</a>';
     	$team2 = $row['home_team'].' - '.$row['away_team'];
     	$date = It6_Date::fromDb($row['start_date']);
     	$sp =  $row['snazev'];
     	$ud =  $row['unazev'];

     }else {$this->dbGame->rollback();$this->error('Nepodařilo se poslat upozorneni');}


     foreach($l as $k=>$h){

       $email = array();

       $sp  = $preklad->FindPreklad($sp,$k); $sp  = $sp[$k];
       $ud  = $preklad->FindPreklad($ud,$k); $ud  = $ud[$k];

       $mail = new htmlMimeMail();

       $s = str_replace('{TEAM}',$team2,$sub[$k]);

       $b = str_replace('{TEAM}',$team,$body[$k]);
       $b = str_replace('{SPORT}',$sp,$b);
       $b = str_replace('{UDALOST}',$ud,$b);
       $b = str_replace('{DATE}',$date,$b);

       $mail->setTextCharset("UTF-8");
	   $mail->setHeadCharset("UTF-8");
	   $mail->setHTMLCharset("UTF-8");  $mail->html_charset = "UTF-8";$mail->text_encoding = "UTF-8";
	   $mail->setHTMLEncoding("base64");
	   $mail->setFrom(NOREPLY);
	   $mail->setReturnPath(NOREPLY);
       $mail->setHtml($b);
	   $mail->setSubject($s);

	   foreach($h as $h2){

	   	$email[] = $h2;

	   }

	   $mail->send($email);

     }

     $sth = $this->dbGame->prepare("delete from live_notice where event_id=?");
     if (PEAR::isError($sth))  {$this->error('Nepodařilo se vymazat notice');}
     $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
     if (PEAR::isError($res))  {$this->error('Nepodařilo se vymazat notice');}

  }


 /**
 * Nastaveni periody
 * @return void
 */
  protected  function SetPeriod(){

  	if(isset($_GET['period'])){

  	 $this->dbGame->autoCommit(false);

  	 $sth = $this->dbGame->prepare("update live_event set stav=? where event_id=?");
     if (PEAR::isError($sth))  {$this->error('Nepodařilo se nastavit periodu');}
     $res =& $this->dbGame->execute($sth,array($_GET['period'],$this->event_data['event_id']));
     if (PEAR::isError($res))  {$this->error('Nepodařilo se nastavit periodu');}

     if($_GET['period'] == LIVE_BEGIN) $this->sendNotice();

     if($_GET['period'] == LIVE_END || $_GET['period'] == LIVE_UNFINISHED){

       $sth = $this->dbGame->prepare("update sazky set platna_do=? where sazka_id in (select a.sazka_id from live_sazka a where a.event_id=? and close=0)");
       if (PEAR::isError($sth))  {$this->error('Nepodařilo se nastavit periodu');}
       $res =& $this->dbGame->execute($sth,array(It6_Date::dbNow(),$this->event_data['event_id']));
       if (PEAR::isError($res))  {$this->dbGame->rollback();$this->error('Nepodařilo se ukončit live sázku');}

       $sth = $this->dbGame->prepare("update live_sazka set close=1 where event_id=?");
       if (PEAR::isError($sth))  {$this->error('Nepodařilo se nastavit periodu');}
       $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
       if (PEAR::isError($res))  {$this->dbGame->rollback();$this->error('Nepodařilo se ukončit live sázku');}

     }

    if($this->event_data['l_sport_id'] == 1013 && $_GET['period'] == PAUSE) $this->clearScore();

    if($this->event_data['l_sport_id'] == 1013 && $_GET['period'] == LIVE_BEGIN) $this->clearScore();


  	$sth3 = $this->dbGame->prepare("update live_event set aktualizace=? where event_id=?");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:5');}

    $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$this->event_data['event_id']));
    if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:16');}

     $this->dbGame->commit();

  	}else $this->error('Nepodařilo se nastavit periodu');

    if($this->event_data['stav'] == LIVE_NOT_STARTED) $this->ReturnStav(1,1);else $this->ReturnStav(1,0);

  }



    /**
 * Stop live sazky
 * @return void
 */
  protected  function StopMatch(){


  	/*$sth = $this->dbGame->prepare("update live_event set vypnuto=1 where event_id=?");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se aktualizovat minuty');}
    $res =& $this->dbGame->execute($sth,$this->event_data['event_id']);
    if (PEAR::isError($res))  {$this->error('Nepodařilo se aktualizovat minuty');}*/

    $sth = $this->dbGame->prepare("update sazky set status=2 where sazka_id in (select a.sazka_id from live_sazka a where a.event_id=? and a.close=0)");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se nastavit periodu');}
    $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
    if (PEAR::isError($res))  {$this->dbGame->rollback();$this->error('Nepodařilo se ukončit live sázku');}

    $sth3 = $this->dbGame->prepare("update live_sazka set aktualizace_sazka=? where event_id=?");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:5');}

    $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$this->event_data['event_id']));
    if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:19');}


    $this->ReturnBets();


    $this->ReturnStav(1,0);

  }

    /**
 * Spousti zapas/pokud byl stopnuty znovu spousti v zastavenem miste
 * @return void
 */
  protected  function RunMatch(){




  	$sth = $this->dbGame->prepare("update live_event set stav=?,vypnuto=0,aktualizace=? where event_id=?");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se aktualizovat minuty');}
    $res =& $this->dbGame->execute($sth,array(($this->event_data['stav']==0?1:$this->event_data['stav']),It6_Date::dbNow(),$this->event_data['event_id']));
    if (PEAR::isError($res))  {$this->error('Nepodařilo se aktualizovat minuty');}

    if($this->event_data['stav'] == LIVE_BEGIN || $this->event_data['stav'] == 0) $this->sendNotice();

    if($this->event_data['l_sport_id'] == 1029 )  {


  	  $sth = $this->dbGame->prepare("update  live_".$this->event_data['l_sport_id']." set lap=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array(1,$this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}

      $this->ReturnPeriodScore();

    }

    /*$sth = $this->dbGame->prepare("update sazky set status=0 where sazka_id in (select a.sazka_id from live_sazka a where a.event_id=? and close=0)");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se nastavit periodu');}
    $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
    if (PEAR::isError($res))  {$this->dbGame->rollback();$this->error('Nepodařilo se ukončit live sázku');}*/

    $sth3 = $this->dbGame->prepare("update live_sazka set aktualizace_sazka=? where event_id=?");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:5');}

    $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$this->event_data['event_id']));
    if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:21');}

    $this->ReturnBets();

    	if($this->event_data['stav'] == LIVE_NOT_STARTED) $this->ReturnStav(1,1);else  $this->ReturnStav(1,0);

  }

    /**
 * Aktualizace minut
 * @return void
 */
  protected  function ActMinute(){

  	$sth = $this->dbGame->prepare("update live_event set minute=? where event_id=?");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se aktualizovat minuty');}
    $res =& $this->dbGame->execute($sth,array($_GET['minute'],$this->event_data['event_id']));
    if (PEAR::isError($res))  {$this->error('Nepodařilo se aktualizovat minuty');}

    $sth3 = $this->dbGame->prepare("update live_event set aktualizace=? where event_id=?");
    if (PEAR::isError($sth3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:5');}

    $res3 =& $this->dbGame->execute($sth3,array(It6_Date::dbNow(),$this->event_data['event_id']));
    if (PEAR::isError($res3))  {$status = false;$this->error('Nepodařilo aktualizovat sazky code:22');}


  }

    /**
 * Vraci minutu
 * @return void
 */
  protected  function ReturnMinute(){


  	 $this->json['live_minute'] = $this->event_data['minute'].' min.';
     $this->json['js_dynamic'] .= 'actMinute='.$this->event_data['minute'].';RunMinute();';

  }

    /**
 * Vraci score
 * @return void
 */
  protected  function ReturnTotalScore(){

  	$sth = $this->dbGame->prepare("SELECT * from live_".$this->event_data['l_sport_id']."  where event_id=?");
    if (PEAR::isError($sth))  {throw new ExHandler($sth->getMessage(),"admin_ex_db");}
    $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
    if (PEAR::isError($res))  {throw new ExHandler($res->getMessage().'Nepodarilo se vlozit baner',"admin_ex_db");}

    if ($row =& $res->fetchRow()){

  	 $this->json['live_score'] = $row['score_home'].':'.$row['score_away'];

    }else  $this->error('Nepodařilo se načíst score');

  }

  /**
 * Vraci stav sazky
* @param int $new jestli se ma znovu vyhledat z db
* * @param int $new2 jestli se ma zahajit minute pocitani
 * @return void
 */
  protected  function ReturnStav($new=0,$new2=0){

  	if($new==1){

  	   $sth = $this->dbGame->prepare("SELECT stav,vypnuto from live_event  where event_id=?");
       if (PEAR::isError($sth))  {throw new ExHandler($sth->getMessage(),"admin_ex_db");}
       $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
       if (PEAR::isError($res))  {throw new ExHandler($res->getMessage().'Nepodarilo se vlozit baner',"admin_ex_db");}

       if ($row =& $res->fetchRow()){
    	 $this->event_data['stav'] =  $row['stav'];
    	 $this->event_data['vypnuto'] = $row['vypnuto'];
       }else{
       	 $this->error('Nepodařilo se načíst stav');
       }

   }

   if($this->event_data['vypnuto'] == 1)         $this->json['live_stav'] = "STOP";
   else if($this->event_data['stav'] == LIVE_NOT_STARTED)   $this->json['live_stav'] = "Nezahájeno";
   else if($this->event_data['stav'] == LIVE_BEGIN)    $this->json['live_stav'] = "Právě začalo";
   else if($this->event_data['stav'] == LIVE_END)      $this->json['live_stav'] = "Ukončeno";
   else if($this->event_data['stav'] == LIVE_1_HALF)   $this->json['live_stav'] = "1. poločas";
   else if($this->event_data['stav'] == LIVE_2_HALF)   $this->json['live_stav'] = "2. poločas";
   else if($this->event_data['stav'] == LIVE_1_THIRD)  $this->json['live_stav'] = "1. třetina";
   else if($this->event_data['stav'] == LIVE_2_THIRD)  $this->json['live_stav'] = "2. třetina";
   else if($this->event_data['stav'] == LIVE_3_THIRD)  $this->json['live_stav'] = "3. třetina";
   else if($this->event_data['stav'] == LIVE_1_Q)      $this->json['live_stav'] = "1. čtvrtina";
   else if($this->event_data['stav'] == LIVE_2_Q)      $this->json['live_stav'] = "2. čtvrtina";
   else if($this->event_data['stav'] == LIVE_3_Q)      $this->json['live_stav'] = "3. čtvrtina";
   else if($this->event_data['stav'] == LIVE_4_Q)      $this->json['live_stav'] = "4. čtvrtina";
   else if($this->event_data['stav'] == OVERTIME)      $this->json['live_stav'] = "Prodloužení";
   else if($this->event_data['stav'] == PAUSE)         $this->json['live_stav'] = "Přestávka";
   else if($this->event_data['stav'] == LIVE_1_SET)    $this->json['live_stav'] = "1. set";
   else if($this->event_data['stav'] == LIVE_2_SET)    $this->json['live_stav'] = "2. set";
   else if($this->event_data['stav'] == LIVE_3_SET)    $this->json['live_stav'] = "3. set";
   else if($this->event_data['stav'] == LIVE_4_SET)    $this->json['live_stav'] = "4. set";
   else if($this->event_data['stav'] == LIVE_5_SET)    $this->json['live_stav'] = "5. set";
   else if($this->event_data['stav'] == LIVE_WARMUP)    $this->json['live_stav'] = "Rozehra";
   else if($this->event_data['stav'] == LIVE_UNFINISHED)    $this->json['live_stav'] = "Neukončeno";
   else if($this->event_data['stav'] == LIVE_PENALTY)    $this->json['live_stav'] = "Penalty";
   else if($this->event_data['stav'] == LIVE_CANCELED)    $this->json['live_stav'] = "Zrušený zápas";
   else if($this->event_data['stav'] == LIVE_RACE_RUNNING)    $this->json['live_stav'] = "Probíhá závod";


   if($this->event_data['stav'] == LIVE_NOT_STARTED || $this->event_data['stav'] == PAUSE || $this->event_data['stav'] == LIVE_END || $this->event_data['stav'] == LIVE_UNFINISHED ){

   	  $this->json['js_dynamic'] .= "stopMinute=true;";
   	  $this->json['timesec'] = "<input type=\"button\" value=\"Run minute\" onclick=\"SRMinute(this)\" />";

   }else  { $this->json['timesec'] = "<input type=\"button\" value=\"Stop minute\" onclick=\"SRMinute(this)\" />";}


   if($new2==1)$this->json['js_dynamic'] = "RunMinute()";

  }


  /**
 * Nastavuje chybu
 * @return void
 */
  public  function PrintJson(){


  	 echo json_encode($this->json);
  }

  /**
 * Nastavuje chybu
 * @return void
 */
  protected  function error($str){

  	 if(!isset($this->json['error'])) $this->json['error'] = "";
  	 $this->json['error'] .= $str;
  }

}
?>
