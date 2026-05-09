<?php

/**
 * @package    livebet
 */

/**
 * Trida pro praci se live sazkami Basket
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Live bet
 */
 
class LiveSport1006 extends LiveSport{
	
	/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $act jaka akce se ma provest
* @param PEAR::DB $db objekt spojeni s databazi
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($act=NULL,$ed=NULL,$db=null,$dbGame=null){
  	
  	if(!is_object($dbGame) || !is_object($db) || $ed == NULL || $act == NULL || $act == NULL) $this->error('Nepodařilo se inicializovat promenne');
  	
  	$this->db = $db;
  	$this->dbGame = $dbGame;
  	$this->action = $act;
  	$this->event_data = $ed;
  	
  
  	parent::__construct();
  	
  }

  
/**
* Aktualizace vsech info dat
*
*

*/
  protected function UpdateAllInfo(){
  	 
  
  	if(!isset($_POST['score_home']) || !isset($_POST['q_1_h']) || !isset($_POST['q_1_a']) || !isset($_POST['q_2_h']) || !isset($_POST['q_2_a']) || !isset($_POST['q_3_h']) || !isset($_POST['q_3_a']) || !isset($_POST['q_4_h']) || !isset($_POST['q_4_a'])  || !isset($_POST['l_minute']) || !isset($_POST['start_at']) || !isset($_POST['score_away']))
  	$this->error('Nepodařilo se aktualizovat informace code:1000');
  	else{
      
  	  $sth = $this->dbGame->prepare("update  live_event set start_date=?,minute=?,win=?,aktualizace=?,limit_rate=?,limit_bet=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array(It6_Date::toDb($_POST['start_at']),$_POST['l_minute'],$_POST['win'],It6_Date::dbNow(),$_POST['limit_rate'],$_POST['limit_bet'],$this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}
      
  	  $sth = $this->dbGame->prepare("update  live_".$this->event_data['l_sport_id']." set score_home=?,score_away=?,ctvrtina_1_home=?,ctvrtina_1_away=?,ctvrtina_2_home=?,ctvrtina_2_away=?,ctvrtina_3_home=?,ctvrtina_3_away=?,ctvrtina_4_home=?,ctvrtina_4_away=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array($_POST['score_home'],$_POST['score_away'],$_POST['q_1_h'],$_POST['q_1_a'],$_POST['q_2_h'],$_POST['q_2_a'],$_POST['q_3_h'],$_POST['q_3_a'],$_POST['q_4_h'],$_POST['q_4_a'],$this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}
  	  
      $this->ReturnTotalScore();
      
  	}
  	
  }
  
    /**
 * Vraci tlacitka period a stavu pro tento sport
 * @return void
 */
  protected  function ReturnPeriodButton(){
  	
   $this->json['period_b'] = '	
  	 <input type="button" value="1. čtvrtina" '.($this->event_data['stav']==8?'style="color:red"':'').' onclick="SetPeriod(8);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="2. čtvrtina" '.($this->event_data['stav']==9?'style="color:red"':'').' onclick="SetPeriod(9);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="3. čtvrtina" '.($this->event_data['stav']==10?'style="color:red"':'').' onclick="SetPeriod(10);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="4. čtvrtina" '.($this->event_data['stav']==11?'style="color:red"':'').' onclick="SetPeriod(11);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="Prodloužení"  '.($this->event_data['stav']==12?'style="color:red"':'').' onclick="SetPeriod(12);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="Přestávka"  '.($this->event_data['stav']==13?'style="color:red"':'').' onclick="SetPeriod(13);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Ukončeno"  '.($this->event_data['stav']==2?'style="color:red;background:black"':'').' style="background:black" onclick="if(!confirm(\'Opravdu chcete zápas ukončit\'))return;SetPeriod(2);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     &nbsp;<input type="button" value="Nedohráno"  '.($this->event_data['stav']==21?'style="color:red;background:black"':'').' style="background:black" onclick="if(!confirm(\'Opravdu chcete nastavit jako nedohraný\'))return;SetPeriod(21);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
   ';
  
  }
  
   /**
 * Vraci tabulku vysledku za jednotlive periody zapasu
 * @return void
 */
  protected  function ReturnPeriodScore(){

  	$sth = $this->dbGame->prepare("select * from  live_".$this->event_data['l_sport_id']." where event_id=?");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
    $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
    if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}
    
     if ($row =& $res->fetchRow()){
     	
     	$this->json['live_period'] = '<tr><td class="live_stav_scores_th">1. čtvrtina</td><td><input type="text" id="q_1_h" class="score_input" value="'.$row['ctvrtina_1_home'].'" />:
                                             <input type="text" id="q_1_a" class="score_input" value="'.$row['ctvrtina_1_away'].'" /></td></tr>
            <tr><td class="live_stav_scores_th">2. čtvrtina</td><td><input type="text" id="q_2_h" class="score_input" value="'.$row['ctvrtina_2_home'].'" />:
                                             <input type="text" id="q_2_a" class="score_input" value="'.$row['ctvrtina_2_away'].'" /></td></tr>
            <tr><td class="live_stav_scores_th">3. čtvrtina</td><td><input type="text" id="q_3_h" class="score_input" value="'.$row['ctvrtina_3_home'].'" />:
                                             <input type="text" id="q_3_a" class="score_input" value="'.$row['ctvrtina_3_away'].'" /></td></tr>
            <tr><td class="live_stav_scores_th">4. čtvrtina</td><td><input type="text" id="q_4_h" class="score_input" value="'.$row['ctvrtina_4_home'].'" />:
                                             <input type="text" id="q_4_a" class="score_input" value="'.$row['ctvrtina_4_away'].'" /></td></tr>';
     	$this->json['js_dynamic'] .= 'periodAr = new Array("q_1_h","q_1_a","q_2_h","q_2_a","q_3_h","q_3_a","q_4_h","q_4_a");';
     }
    
    
  }
  
}

?>