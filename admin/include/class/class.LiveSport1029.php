<?php

/**
 * @package    livebet
 */

/**
 * Trida pro praci se live sazkami F 1
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Live bet
 */
 
class LiveSport1029 extends LiveSport{
	
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
  	 
  
  	if(!isset($_POST['lap']))
  	$this->error('Nepodařilo se aktualizovat informace');
  	else{
      
  	  $sth = $this->dbGame->prepare("update  live_event set start_date=?,minute=?,win=?,aktualizace=?,limit_rate=?,limit_bet=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array(It6_Date::toDb($_POST['start_at']),$_POST['l_minute'],$_POST['win'],It6_Date::dbNow(),$_POST['limit_rate'],$_POST['limit_bet'],$this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}
      
   
  	  $sth = $this->dbGame->prepare("update  live_".$this->event_data['l_sport_id']." set lap=?,lap_total=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array($_POST['lap'],$_POST['lap_total'],$this->event_data['event_id']));
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
   <input type="button" value="Probíhá z." '.($this->event_data['stav']==LIVE_RACE_RUNNING?'style="color:red"':'').' onclick="SetPeriod(27);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
  &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Ukončeno"  '.($this->event_data['stav']==2?'style="color:red;background:black"':'').' style="background:black" onclick="if(!confirm(\'Opravdu chcete zápas ukončit\'))return;SetPeriod(2);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     &nbsp;<input type="button" value="Nedohráno"  '.($this->event_data['stav']==21?'style="color:red;background:black"':'').' style="background:black" onclick="if(!confirm(\'Opravdu chcete nastavit jako nedohraný\'))return;SetPeriod(21);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
    ';
  
  }
  
   /**
 * Vraci tabulku vysledku za jednotlive periody zapasu
 * @return void
 */
  protected  function ReturnPeriodScore(){

  	$sth = $this->dbGame->prepare("select lap,lap_total from  live_".$this->event_data['l_sport_id']." where event_id=?");
    if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
    $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
    if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}
    
     if ($row =& $res->fetchRow()){
     	
     	$this->json['live_period'] = '<tr><td class="live_stav_scores_th">Kolo</td><td><input type="text" id="lap" class="score_input" value="'.$row['lap'].'" /></td></tr>
<tr><td class="live_stav_scores_th">Celkem kol</td><td><input type="text" id="lap_total" class="score_input" value="'.$row['lap_total'].'" /></td></tr>
';
     	$this->json['js_dynamic'] .= 'periodAr = new Array("lap","lap_total");';
     }
    
    
  }
  
}

?>