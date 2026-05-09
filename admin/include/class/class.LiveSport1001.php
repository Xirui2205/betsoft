<?php

/**
 * @package    livebet
 */

/**
 * Trida pro praci se live sazkami Fotbal
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Live bet
 */

class LiveSport1001 extends LiveSport{

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


  	if(!isset($_POST['score_home']) || !isset($_POST['f_h_a']) || !isset($_POST['f_h_h']) || !isset($_POST['s_h_a']) || !isset($_POST['s_h_h']) || !isset($_POST['l_minute']) || !isset($_POST['start_at']) || !isset($_POST['score_away']))
  	$this->error('Nepodařilo se aktualizovat informace');
  	else{

  	  $sth = $this->dbGame->prepare("update  live_event set start_date=?,minute=?,win=?,aktualizace=?,limit_rate=?,limit_bet=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array(It6_Date::toDb($_POST['start_at']),$_POST['l_minute'],$_POST['win'],It6_Date::dbNow(),$_POST['limit_rate'],$_POST['limit_bet'],$this->event_data['event_id']));
      if (PEAR::isError($res))  {$this->error('Nepodařilo se získat score');}

  	  $sth = $this->dbGame->prepare("update  live_".$this->event_data['l_sport_id']." set score_home=?,score_away=?,first_half_home=?,first_half_away=?,second_half_home=?,second_half_away=?,yellow_card_home=?,yellow_card_away=?,red_card_home=?,red_card_away=? where event_id=?");
      if (PEAR::isError($sth))  {$this->error('Nepodařilo se získat score');}
      $res =& $this->dbGame->execute($sth,array($_POST['score_home'],$_POST['score_away'],$_POST['f_h_h'],$_POST['f_h_a'],$_POST['s_h_h'],$_POST['s_h_a'],$_POST['y_c_h'],$_POST['y_c_a'],$_POST['r_c_h'],$_POST['r_c_a'],$this->event_data['event_id']));
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
      <input
        type="button"
        value="1. poločas"
        '.($this->event_data['stav']==3?'style="color:red"':'').'
        onclick="SetPeriod(3);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state"
      />

      <input
        type="button"
        value="2. poločas"
        '.($this->event_data['stav']==4?'style="color:red"':'').'
        onclick="SetPeriod(4);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state"
      />

      <input
        type="button"
        value="Přestávka"
        '.($this->event_data['stav']==13?'style="color:red"':'').'
        onclick="SetPeriod(13);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state"
      />

      <input
        type="button"
        value="Prodloužení"
        '.($this->event_data['stav']==12?'style="color:red"':'').'
        onclick="SetPeriod(12);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';" class="b_state"
      />

      <br />

      <input
        type="button"
        value="Ukončeno"
        '.($this->event_data['stav']==2?'style="color:red;background:black"':'').'
        style="background:black"
        onclick="if(!confirm(\'Opravdu chcete zápas ukončit\'))return;SetPeriod(2);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';"
        class="b_state"
      />

      &nbsp;

      <input
        type="button"
        value="Nedohráno"
        '.($this->event_data['stav']==21?'style="color:red;background:black"':'').'
        style="background:black"
        onclick="if(!confirm(\'Opravdu chcete nastavit jako nedohraný\'))return;SetPeriod(21);$(\'.b_state\').css(\'color\',\'white\');this.style.color=\'red\';"
        class="b_state"
      />
    ';
  }

   /**
 * Vraci tabulku vysledku za jednotlive periody zapasu
 * @return void
 */
  protected  function ReturnPeriodScore(){

  	$sth = $this->dbGame->prepare("
      SELECT
        first_half_home,
        first_half_away,
        second_half_home,
        second_half_away,
        yellow_card_home,
        yellow_card_away,
        red_card_home,
        red_card_away,
        note
      FROM live_".$this->event_data['l_sport_id']."
      WHERE event_id=?
    ");
    $res =& $this->dbGame->execute($sth,array($this->event_data['event_id']));
    DbUtil::testResult($res, 'Unable to obtain score.');

    if ($row =& $res->fetchRow()){

      $this->json['live_period'] = '
        <tr>
          <td class="live_stav_scores_th">1. polocas</td>
          <td>
            <input type="text" id="f_h_h" class="score_input" value="'.$row['first_half_home'].'" />
            :
            <input type="text" id="f_h_a" class="score_input" value="'.$row['first_half_away'].'" />
          </td>
        </tr>

        <tr>
          <td class="live_stav_scores_th">2. polocas</td>
          <td>
            <input type="text" id="s_h_h" class="score_input" value="'.$row['second_half_home'].'" />
            :
            <input type="text" id="s_h_a" class="score_input" value="'.$row['second_half_away'].'" />
          </td>
        </tr>

        <tr>
          <td class="live_stav_scores_th">Žluté karty</td>
          <td>
            <input type="text" id="y_c_h" class="score_input" value="'.$row['yellow_card_home'].'" />
            :
            <input type="text" id="y_c_a" class="score_input" value="'.$row['yellow_card_away'].'" />
          </td>
        </tr>

        <tr>
          <td class="live_stav_scores_th">Červené karty</td>
          <td>
            <input type="text" id="r_c_h" class="score_input" value="'.$row['red_card_home'].'" />
            :
            <input type="text" id="r_c_a" class="score_input" value="'.$row['red_card_away'].'" />
          </td>
        </tr>
      ';

      $this->json['js_dynamic'] .= '
        periodAr = new Array("f_h_h","f_h_a","s_h_h","s_h_a","y_c_h","y_c_a","r_c_h","r_c_a");
      ';
    }
  }


}

?>
