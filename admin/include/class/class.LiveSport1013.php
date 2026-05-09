<?php

/**
 * @package    livebet
 */

/**
 * Trida pro praci se live sazkami COlejbal
 *
 * 
 * <code>
 * 
 * </code>
 *
 * @package    Live bet
 */
 
class LiveSport1013 extends LiveSport{
	
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
* Vymaze kdyz je prestavka score
*
*

*/
 protected function clearScore(){
 	
 
    	
 	  $sth = $this->dbGame->prepare("update  live_".$this->event_data['l_sport_id']." set score_home=0,score_away=0 where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}

 	  $this->json['live_score'] = '0:0';
 	  $this->json['js_dynamic'] .= '$("#score_home").attr("value",0);';
 	  $this->json['js_dynamic'] .= '$("#score_away").attr("value",0);';
 	
 }
 
   /**
* Nastavuje prvni podani
*
*

*/
 protected function FirstService(){
 	
    if(isset($_POST['first_service'])){
 	  
      $_SESSION['service'] = 	$_POST['first_service'];
    	
 	  $sth = $this->dbGame->prepare("update  live_".$this->event_data['l_sport_id']." set service=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array(($_POST['first_service'] == 1?'home':'away'),$this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}
 		
 		
 	}
 	
 	
 }
  
  
  
/**
* Aktualizace vsech info dat
*
*

*/
  protected function UpdateAllInfo(){
  	 
    if(!isset($_SESSION['score_home'])) $_SESSION['score_home'] = 0;
  	if(!isset($_SESSION['score_away'])) $_SESSION['score_away'] = 0;
  	
  	if(!isset($_POST['score_home']) || !isset($_POST['set_1_h']) || !isset($_POST['set_1_a']) || !isset($_POST['set_2_h']) || !isset($_POST['set_2_a']) || !isset($_POST['set_3_h']) || !isset($_POST['set_3_a']) || !isset($_POST['set_4_h']) || !isset($_POST['set_4_a']) || !isset($_POST['set_5_h']) || !isset($_POST['set_5_a']) || !isset($_POST['l_minute']) || !isset($_POST['start_at']) || !isset($_POST['score_away']))
  	$this->error('Nepodařilo se aktualizovat informace');
  	else{
      
  	  $service = 'service';
  	  if($_SESSION['score_home'] != $_POST['score_home'])      $service = 'home';
  	  else if($_SESSION['score_away'] != $_POST['score_away']) $service = 'away';

  	 if( $service != 'service' )$this->json['js_dynamic'] .= '$("#live_o_info").html( \'<tr><td>&nbsp; </td><td><input type="radio" '.($service == 'home'?'checked="checked"':'').' name="first_service" onclick="First_service(1)" /></td><td style="color:white"> Domácí</td><td>  <input type="radio" '.($service == 'away'?'checked="checked"':'').' name="first_service" onclick="First_service(2)" /> </td><td style="color:white">Host</td> </tr>\'); ';

  	  $_SESSION['score_home'] = $_POST['score_home'];
  	  $_SESSION['score_away'] = $_POST['score_away'];
  	  
  	  if($this->event_data['stav'] == LIVE_BEGIN || $this->event_data['stav'] == LIVE_1_SET)    {$_POST['set_1_h'] = $_POST['score_home'];$_POST['set_1_a'] = $_POST['score_away'];}
  	  else if($this->event_data['stav'] == LIVE_2_SET)    {$_POST['set_2_h'] = $_POST['score_home'];$_POST['set_2_a'] = $_POST['score_away'];}
      else if($this->event_data['stav'] == LIVE_3_SET)    {$_POST['set_3_h'] = $_POST['score_home'];$_POST['set_3_a'] = $_POST['score_away'];}
      else if($this->event_data['stav'] == LIVE_4_SET)    {$_POST['set_4_h'] = $_POST['score_home'];$_POST['set_4_a'] = $_POST['score_away'];}
      else if($this->event_data['stav'] == LIVE_5_SET)   {$_POST['set_5_h'] = $_POST['score_home'];$_POST['set_5_a'] = $_POST['score_away'];}
  	  
  	  $sth = $this->dbGame->prepare("update  live_event set start_date=?,minute=?,win=?,aktualizace=?,limit_rate=?,limit_bet=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array(It6_Date::toDb($_POST['start_at']),$_POST['l_minute'],$_POST['win'],It6_Date::dbNow(),$_POST['limit_rate'],$_POST['limit_bet'],$this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}
      
  	  $sth = $this->dbGame->prepare("update  live_".$this->event_data['l_sport_id']." set service=?,score_home=?,score_away=?,set_1_home=?,set_1_away=?,set_2_home=?,set_2_away=?,set_3_home=?,set_3_away=?,set_4_home=?,set_4_away=?,set_5_home=?,set_5_away=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array($service,$_POST['score_home'],$_POST['score_away'],$_POST['set_1_h'],$_POST['set_1_a'],$_POST['set_2_h'],$_POST['set_2_a'],$_POST['set_3_h'],$_POST['set_3_a'],$_POST['set_4_h'],$_POST['set_4_a'],$_POST['set_5_h'],$_POST['set_5_a'],$this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}
  	  
      $this->ReturnTotalScore();
      $this->ReturnPeriodScore();
      
  	}
  	
  }
  
    /**
 * Vraci tlacitka period a stavu pro tento sport
 * @return void
 */
  protected  function ReturnPeriodButton(){

   $this->json['period_b'] = '	
  	 <input type="button" value="1. set" '.($this->event_data['stav']==14?'style="color:red"':'').' onclick="SetPeriod(14);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="2. set" '.($this->event_data['stav']==15?'style="color:red"':'').' onclick="SetPeriod(15);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="3. set" '.($this->event_data['stav']==16?'style="color:red"':'').' onclick="SetPeriod(16);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="4. set" '.($this->event_data['stav']==17?'style="color:red"':'').' onclick="SetPeriod(17);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
	 <input type="button" value="5. set" '.($this->event_data['stav']==18?'style="color:red"':'').' onclick="SetPeriod(18);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="Přestávka"  '.($this->event_data['stav']==13?'style="color:red"':'').' onclick="SetPeriod(13);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
     <input type="button" value="Rozehra"  '.($this->event_data['stav']==20?'style="color:red"':'').' onclick="SetPeriod(20);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state" />
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
     	
     	$this->json['live_period'] = '<tr><td class="live_stav_scores_th">1. set</td><td><input type="text" id="set_1_h" onkeyup="SetChange()" class="score_input" value="'.$row['set_1_home'].'" />
          </td><td> <a href="#" onclick="ChValue(\'plus\',\'set_1_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_1_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td><td>
                                          <input type="text" id="set_1_a" onkeyup="SetChange()" class="score_input" value="'.$row['set_1_away'].'" /></td>
<td> <a href="#" onclick="ChValue(\'plus\',\'set_1_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_1_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td></tr>
            <tr><td class="live_stav_scores_th">2. set</td><td><input type="text" onkeyup="SetChange()" id="set_2_h" class="score_input" value="'.$row['set_2_home'].'" />
</td><td> <a href="#" onclick="ChValue(\'plus\',\'set_2_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_2_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td><td>
                                             <input type="text" id="set_2_a" onkeyup="SetChange()" class="score_input" value="'.$row['set_2_away'].'" /></td>
<td> <a href="#" onclick="ChValue(\'plus\',\'set_2_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_2_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td></tr>
            <tr><td class="live_stav_scores_th">3. set</td><td><input type="text" onkeyup="SetChange()" id="set_3_h" class="score_input" value="'.$row['set_3_home'].'" />
</td><td> <a href="#" onclick="ChValue(\'plus\',\'set_3_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_3_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td><td>
                                             <input type="text" id="set_3_a" onkeyup="SetChange()" class="score_input" value="'.$row['set_3_away'].'" /></td>
<td> <a href="#" onclick="ChValue(\'plus\',\'set_3_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_3_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td>
</tr>
            <tr><td class="live_stav_scores_th">4. set</td><td><input type="text" onkeyup="SetChange()" id="set_4_h" class="score_input" value="'.$row['set_4_home'].'" />
</td><td> <a href="#" onclick="ChValue(\'plus\',\'set_4_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_4_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td><td>
                                             <input type="text" id="set_4_a" onkeyup="SetChange()" class="score_input" value="'.$row['set_4_away'].'" /></td>
<td> <a href="#" onclick="ChValue(\'plus\',\'set_4_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_4_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td>
</tr>
            <tr><td class="live_stav_scores_th">5. set</td><td><input type="text" onkeyup="SetChange()" id="set_5_h" class="score_input" value="'.$row['set_5_home'].'" />
</td><td> <a href="#" onclick="ChValue(\'plus\',\'set_5_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_5_h\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td><td>

                                             <input type="text" id="set_5_a" onkeyup="SetChange()" class="score_input" value="'.$row['set_5_away'].'" /></td>
<td> <a href="#" onclick="ChValue(\'plus\',\'set_5_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_up.gif" class="img" alt="+1" /></a>
	       <a href="#" onclick="ChValue(\'minus\',\'set_5_a\',0,0);SetChange();" class="arrow"><img src="_clip/arrow_down.gif" class="img" alt="-1" /></a></td>
</tr>';

     	$this->json['js_dynamic'] .= 'periodAr = new Array("set_1_h","set_1_a","set_2_h","set_2_a","set_3_h","set_3_a","set_4_h","set_4_a","set_5_h","set_5_a");';
     }
    
    
  }
  
}

?>