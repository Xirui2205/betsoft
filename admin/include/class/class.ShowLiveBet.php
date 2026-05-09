<?php



 /**
 * Trida pro zobrazeni live sazek
 *
 *
 * @package    main
 */

class ShowLiveBet{



/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * id sekce kde jsme
 * @access private
 * @var int
 */
private  $section;


/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
* @param PEAR::DB $db objekt spojeni s databazi
*/
  public function __construct($section, $dbGame = null){

  $this->section =  $section;


   if($dbGame == null){

    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


   }else
        $this->dbGame =  $dbGame;



  }

   /**
 * Metoda spousti jednitlove metody podle stavu
 * @return void
 */
  public  function runAction(){

    if(isset($_POST['delete'])){
      if($this->deleteLive(intval(key($_POST['delete'])))){
        $this->ShowData();
      }
    }
    else {
      $this->ShowData();
    }
    $this->dbGame->disconnect();
  }

       /**
 * Vymaze live sazku
 * @param int $id id live sazky
 * @return void
 */
  private  function deleteLive($id){
//maze z tabulky live_even kde je seznam eventu
      $sth = $this->dbGame->prepare("DELETE FROM live_event WHERE event_id=?");
      if (PEAR::isError($sth)) throw new ExHandler($sth->getMessage(),"admin_ex_db");

//maze z tabulky live_sazka kde je seznam trhu, jinak to hazi constraint violation chybu
      $sth2 = $this->dbGame->prepare("DELETE FROM live_sazka WHERE event_id=?");
      if (PEAR::isError($sth)) throw new ExHandler($sth->getMessage(),"admin_ex_db");



      $res =& $this->dbGame->execute($sth, array($id));
      if (PEAR::isError($res2)) throw new ExHandler($res2->getMessage().'Nepodarilo se smazat live sazku', "admin_ex_db");

      $res2 =& $this->dbGame->execute($sth2, array($id));
      if (PEAR::isError($res2)) throw new ExHandler($res2->getMessage().'Nepodarilo se smazat live sazku', "admin_ex_db");



      $this->vrat .= "<div class=\"okmsg\">Live sázka byla smazána.</div><br />";

		It6_Log::info(
			"Live bet '%bet%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('bet' => $id)
		);
      return(true);
  }


  /**
 * Zobrazi data k zadani
 * @return void
 */
  public  function ShowData(){

  	$this->vrat .= '
      <h1 class="livebet">Live sázky kalendář</h1>
      <form method="post" class="noprint" action="?superb=1&section='.$this->section.'">
        <table class="filtr">
          <tr>
            <td>Datum </td>
            <td>-> </td>
            <td colspan="2">
              <input
                type="text"
                class="input dateTime"
                id="datum"
                name="datum"
                maxlength="19"
                value="'.(isset($_REQUEST['datum'])?Help::Html($_REQUEST['datum']):"").'" />
                <img src="_clip/calendar.gif" class="calendar-icon">
              </a>
            </td>
          </tr>

<!--
  <tr>
    <td>7 dní </td>
    <td>
      <input type="radio" class="no" value="tyden" name="format"  '.(!isset($_REQUEST['format']) || $_REQUEST['format']=="tyden"?"checked=\"checked\"":"").' />
    </td>
    <td align="right"></td>
    <td>
      <input
        type="radio"
        class="no"
        value="mesic"
        name="format"
        '.(isset($_REQUEST['format']) && $_REQUEST['format']=="mesic"?"checked=\"checked\"":"").' />
    </td>
  </tr>
-->

          <tr>
            <td colspan="4">
              <input type="submit" name="send"  value="Zobrazit" />
            </td>
          </tr>
        </table>
      </form>
    ';

    if(!isset($_REQUEST['format']) || $_REQUEST['format']=="tyden") $this->Week();
    else if(isset($_REQUEST['format']) && $_REQUEST['format']=="mesic") $this->Month();

  }

     /**
 * Vraci nick bookmakera
 * @param int $id id bookmakera
 * @return string
 */
  public  function BookName($id){

  	  $sth = $this->dbGame->prepare("select nick from bookmaker where bookmaker_id=".$id);
      if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

      $res2 =& $this->dbGame->execute($sth);
      if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");

      if ($row =& $res2->fetchRow()){
      	return $row['nick'];
      }else return '';
  }

 /**
 * Zobrazi mesice
 * @return void
 */
  public  function Month(){

  	  $preklad = new Preklady();
  	  $den = (3600*24);

  	  $udalost = $this->Udalost();
  	  $sport   = $this->Sport();

  	  $match = array();

  	  if(!isset($_REQUEST['datum']) || !It6_Date::checkFormat($_REQUEST['datum'])) $_REQUEST['datum'] = date('d.m.Y 00:00:00');

  	  $sth = $this->dbGame->prepare("select event_id,stav,DATE_FORMAT(start_date,'%H') AS hodina,DATE(start_date) AS den,start_date,l_sport_id,l_udalost_id,home_team,away_team,live_bookmaker_id from live_event where start_date>='".It6_Date::toDb($_REQUEST['datum'])."' and start_date<=date_add('".It6_Date::toDb($_REQUEST['datum'])."',INTERVAL 7 DAY) order by start_date");
      if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

      $res2 =& $this->dbGame->execute($sth);
      if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");


      while ($row =& $res2->fetchRow()){

      	//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
      	$t = It6_Date::fromDbAsTimeStamp($row['start_date']);

      	$klic = count($match[$row['den']][$row['hodina']]);

      	$match[$row['den']][$row['hodina']][$klic]['start'] = It6_Date::fromDb($row['start_date']);
      	$match[$row['den']][$row['hodina']][$klic]['sport'] = $sport[$row['l_sport_id']];
      	$match[$row['den']][$row['hodina']][$klic]['sport_id'] = $row['l_sport_id'];
      	$match[$row['den']][$row['hodina']][$klic]['udalost'] = $udalost[$row['l_udalost_id']];
      	$match[$row['den']][$row['hodina']][$klic]['team'] = $row['home_team'].' - '.$row['away_team'];
      	$match[$row['den']][$row['hodina']][$klic]['book_id'] = intval($row['live_bookmaker_id']);
      	$match[$row['den']][$row['hodina']][$klic]['book_nick'] = $this->BookName(intval($row['live_bookmaker_id']));
      	$match[$row['den']][$row['hodina']][$klic]['stav'] = intval($row['stav']);
      	$match[$row['den']][$row['hodina']][$klic]['event'] = intval($row['event_id']);

      }

      $t = It6_Date::toTimestamp($_REQUEST['datum']);
      $pocet_dnu = date("t",$t);
      $mesic_rok = date("m.Y",$t);
      $prvni_den = date("D",It6_Date::toTimestamp("01.".$mesic_rok." 00:00:00"));
      $t = It6_Date::toTimestamp("1.".$mesic_rok." 00:00:00");
      $dny_ar = array("Mon"=>1,"Tue"=>2,"Wen"=>3,"Thu"=>4,"Fri"=>5,"Sat"=>6,"Sun"=>7);
      $this->vrat .= '<form method="post" action="?'.$_SERVER['QUERY_STRING'].'"><table class="week_live"><tr><td colspan="9">'.$mesic_rok.'</td></tr><tr class="live_head_row"><td class="live_head_row_1"><a href="?superb=1&section='.$this->section.'&format=mesic&datum='.date("01.m.Y 00:00:00",($t-(2*$den))).'">&lt;</a></td>
                      <td>Po</td><td>Út</td><td>St</td><td>Čt</td><td>Pá</td><td>So</td><td>Ne</td><td class="live_head_row_1"><a href="?superb=1&section='.$this->section.'&format=mesic&datum='.date("01.m.Y 00:00:00",($t+(31*$den))).'">&gt;</a></td></tr>';

      $y = $dny_ar[$prvni_den];
      $this->vrat .= '<tr><td class="live_td_hour">&nbsp;</td>';
      for($xx=1;$y>$xx;$xx++)$this->vrat .= '<td>&nbsp;</td>';

      for($x=0;$x<$pocet_dnu;$x++){

      	if($y>7){$y=1; $this->vrat .= '<td class="live_td_hour">&nbsp;</td></tr><tr><td class="live_td_hour">&nbsp;</td>';}
      	$dd = date("Y-m-d",$t);

      	if(is_array($match[$dd])){
      	reset($match[$dd]);
      	$this->vrat .= '<td valign="top" class="'.(intval(date('d')) == ($x+1)?'live_td_hour':'').'">';
      	foreach($match[$dd] as $k=>$h8){

      	   	  foreach($match[$dd][$k] as $h){

      	   	  	if($h['book_id'] != 0) $book = '<div class="live_owner">Bookmaker: '.Help::Html($h['book_nick']).'</div>';
      	   	  	else                   $book = '<div class="live_owner">Bookmaker: Volná</div>';

      	   	  	if($h['stav'] == LIVE_NOT_STARTED)  { $stav='<div class="live_state">Nezahájeno</div>';}
      	   	    else if($h['stav'] == LIVE_BEGIN)     $stav='<div class="live_state"><span>! RUN</span> Zahájeno</div>';
      	   	    else if($h['stav'] == LIVE_END)       $stav='<div class="live_state">Ukončeno</div>';
      	   	    else if($h['stav'] == LIVE_1_HALF)    $stav='<div class="live_state"><span>! RUN</span> První poločas</div>';
      	   	    else if($h['stav'] == LIVE_2_HALF)    $stav='<div class="live_state"><span>! RUN</span> Druhý poločas</div>';
      	   	    else if($h['stav'] == LIVE_1_THIRD)   $stav='<div class="live_state"><span>! RUN</span> První třetina</div>';
      	   	    else if($h['stav'] == LIVE_2_THIRD)   $stav='<div class="live_state"><span>! RUN</span> Druhá třetina</div>';
      	   	    else if($h['stav'] == LIVE_3_THIRD)   $stav='<div class="live_state"><span>! RUN</span> Třettí třetina</div>';
      	   	    else if($h['stav'] == LIVE_1_Q)       $stav='<div class="live_state"><span>! RUN</span> První čtvrtina</div>';
      	   	    else if($h['stav'] == LIVE_2_Q)       $stav='<div class="live_state"><span>! RUN</span> Druhá čtvrtina</div>';
      	   	    else if($h['stav'] == LIVE_3_Q)       $stav='<div class="live_state"><span>! RUN</span> Třetí čtvrtina</div>';
      	   	    else if($h['stav'] == LIVE_4_Q)       $stav='<div class="live_state"><span>! RUN</span> Čtvrtá čtvrtina</div>';
      	   	  	else                                  $stav='<div class="live_state">Nedefinováno</div>';

      	   	  	if($h['book_id']==$_SESSION['bookmaker'] || $h['stav'] == LIVE_NOT_STARTED)$book .= '<input type="button" onclick="jQuery.OpenLive('.$h['event'].')" value="Převzít live událost" /> <input type="submit"  name="delete['.$h['event'].']" onclick="if(!confirm(\'Orpavdu chcete smazat?\')) return false;" value="Smazat live událost" />';
      	   	  	      	   	  	$this->vrat .= '<div class="match_sport">'.$stav.' '.$h['start'].'<div class="match sport_'.$h['sport_id'].'">'.$h['sport'].' -> '.$h['udalost'].'</div> '.($h['stav'] == LIVE_END?'<a href="#">'.Help::Html($h['team']).'</a>':'<span class="match_hover">'.Help::Html($h['team']).'</span>').' '.$book.' </div>';

      	   	  }

      	}
      	$this->vrat .= '</td>';
      	}else $this->vrat .= '<td><div class="match_sport" style="visibility:hidden"></div></td>';

      	$t = $t + $den;
      	$y++;
      }

      for($xx=7;$y<=$xx;$xx--) $this->vrat .= '<td>&nbsp;</td>';
      $this->vrat .= '<td class="live_td_hour">&nbsp;</td></tr>';



      $this->vrat .= '</table></form>';

  }

   /**
 * Zobrazi 7 dnu
 * @return void
 */
  public  function Week(){

  	  $preklad = new Preklady();
  	  $den = (3600*24);

  	  $udalost = $this->Udalost();
  	  $sport   = $this->Sport();

  	  $match = array();

  	  if(!isset($_REQUEST['datum']) || !It6_Date::checkFormat($_REQUEST['datum'])) $_REQUEST['datum'] = date('d.m.Y 00:00:00');

  	  $sth = $this->dbGame->prepare("select event_id,stav,DATE_FORMAT(start_date,'%H') AS hodina,DATE(start_date) AS den,start_date,l_sport_id,l_udalost_id,home_team,away_team,live_bookmaker_id from live_event where start_date>='".It6_Date::toDb($_REQUEST['datum'])."' and start_date<=date_add('".It6_Date::toDb($_REQUEST['datum'])."',INTERVAL 7 DAY) order by start_date");
      if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

      $res2 =& $this->dbGame->execute($sth);
      if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");


      while ($row =& $res2->fetchRow()){

      	//TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
      	$t = It6_Date::fromDbAsTimestamp($row['start_date']);

		$row['hodina'] = intval($row['hodina']);
      	$klic = count($match[$row['den']][$row['hodina']]);
      	$match[$row['den']][$row['hodina']][$klic]['start'] = It6_Date::fromDb($row['start_date']);
      	$match[$row['den']][$row['hodina']][$klic]['sport'] = $sport[$row['l_sport_id']];
      	$match[$row['den']][$row['hodina']][$klic]['sport_id'] = $row['l_sport_id'];
      	$match[$row['den']][$row['hodina']][$klic]['udalost'] = $udalost[$row['l_udalost_id']];
      	$match[$row['den']][$row['hodina']][$klic]['team'] = $row['home_team'].' - '.$row['away_team'];
      	$match[$row['den']][$row['hodina']][$klic]['book_id'] = intval($row['live_bookmaker_id']);
      	$match[$row['den']][$row['hodina']][$klic]['book_nick'] = $this->BookName(intval($row['live_bookmaker_id']));
      	$match[$row['den']][$row['hodina']][$klic]['stav'] = intval($row['stav']);
      	$match[$row['den']][$row['hodina']][$klic]['event'] = intval($row['event_id']);
      }

      $t = It6_Date::toTimestamp($_REQUEST['datum']);

      $this->vrat .= '<form method="post" action="?'.$_SERVER['QUERY_STRING'].'"><table class="live_tab"><tr class="live_head_row"><td class="live_head_row_1"><a href="?superb=1&section='.$this->section.'&datum='.date("d.m.Y 00:00:00",($t-(7*$den))).'">&lt;</a></td>';

      $row = array();


      for($x=0;$x<7;$x++){

      	$dd = date("Y-m-d",$t);
      	$this->vrat .= '<td>'.date("d.m.Y",$t).'</td>';


      	for($y=0;$y<24;$y++){

      	   if(!isset($row[$y])) $row[$y] = '';
      	   $obsah = '';

      	   if(isset($match[$dd][$y])){

      	   	  foreach($match[$dd][$y] as $h){

      	   	  	if($h['book_id'] != 0) $book = '<div class="live_owner">Bookmaker: '.Help::Html($h['book_nick']).'</div>';
      	   	  	else                   $book = '<div class="live_owner">Bookmaker: Volná</div>';

      	   	  	if($h['stav'] == LIVE_NOT_STARTED)  { $stav='<div class="live_state">Nezahájeno</div>';}
      	   	    else if($h['stav'] == LIVE_BEGIN)     $stav='<div class="live_state"><span>! RUN</span> Zahájeno</div>';
      	   	    else if($h['stav'] == LIVE_END)       $stav='<div class="live_state">Ukončeno</div>';
      	   	    else if($h['stav'] == LIVE_1_HALF)    $stav='<div class="live_state"><span>! RUN</span> První poločas</div>';
      	   	    else if($h['stav'] == LIVE_2_HALF)    $stav='<div class="live_state"><span>! RUN</span> Druhý poločas</div>';
      	   	    else if($h['stav'] == LIVE_1_THIRD)   $stav='<div class="live_state"><span>! RUN</span> První třetina</div>';
      	   	    else if($h['stav'] == LIVE_2_THIRD)   $stav='<div class="live_state"><span>! RUN</span> Druhá třetina</div>';
      	   	    else if($h['stav'] == LIVE_3_THIRD)   $stav='<div class="live_state"><span>! RUN</span> Třettí třetina</div>';
      	   	    else if($h['stav'] == LIVE_1_Q)       $stav='<div class="live_state"><span>! RUN</span> První čtvrtina</div>';
      	   	    else if($h['stav'] == LIVE_2_Q)       $stav='<div class="live_state"><span>! RUN</span> Druhá čtvrtina</div>';
      	   	    else if($h['stav'] == LIVE_3_Q)       $stav='<div class="live_state"><span>! RUN</span> Třetí čtvrtina</div>';
      	   	    else if($h['stav'] == LIVE_4_Q)       $stav='<div class="live_state"><span>! RUN</span> Čtvrtá čtvrtina</div>';
      	   	  	else                                  $stav='<div class="live_state">Nedefinováno</div>';

      	   	  	if($h['book_id']==$_SESSION['bookmaker'] || $h['stav'] == LIVE_NOT_STARTED){
                  $book .= '
                    <input
                      type="button"
                      onclick="jQuery.OpenLive('.$h['event'].')"
                      value="Převzít live událost" />
                    <input
                      type="submit"
                      name="delete['.$h['event'].']"
                      onclick="if(!confirm(\'Orpavdu chcete smazat?\'))
                      return false;"
                      value="Smazat live událost" />
                  ';
                }

                $obsah .= '
                  <div class="match_sport">
                    '.$stav.' '.$h['start'].'
                    <div class="match sport_'.$h['sport_id'].'">
                      '.$h['sport'].' -> '.$h['udalost'].'
                    </div>
                    '.($h['stav'] == LIVE_END?'
                    <a href="#">'.Help::Html($h['team']).'</a>':'
                    <span class="match_hover">'.Help::Html($h['team']).'</span>').' '.$book.'
                  </div>
                ';
      	   	  }
      	   }

      	   if(mb_strlen($obsah) == 0) $obsah = '&nbsp;';

      	   $row[$y] .= '<td valign="top" >'.$obsah.'</td>';



      	}

      	$t = $t + $den;

      }

      $this->vrat .= '<td class="live_head_row_1"><a href="?superb=1&section='.$this->section.'&datum='.date("d.m.Y 00:00:00",($t)).'">&gt;</a></td></tr>';


      for($y=0;$y<24;$y++){

      	$this->vrat .= '<tr class="'.(intval(date('H')) == $y?'live_active_point':'').'">';
      	$this->vrat .= '<td class="live_td_hour">'.$y.'</td>'.$row[$y];
       	$this->vrat .= '<td class="live_td_hour">&nbsp;</td></tr>';


      }


      $this->vrat .= '</table></form>';

  }



   /**
 * Vraci Sport
 * @return void
 */
  public  function Sport(){

  	  $sport =  array();
      $preklad = new Preklady();

  	  $sql = "select sport_id,nazev from sport";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz:  udalosti',"admin_ex_db");

      while($row =& $res->fetchRow()){

    	$r = $preklad->FindPreklad($row['nazev'],1);
    	$sport[$row['sport_id']] = $r[1];


      }


      return $sport;

  }

   /**
 * Vraci udalost
 * @return void
 */
  public  function Udalost(){

  	 $udalost =  array();
     $preklad = new Preklady();

  	  $sql = "select udalost_id,nazev from udalost";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz:  udalosti',"admin_ex_db");

      while($row =& $res->fetchRow()){

    	$r = $preklad->FindPreklad($row['nazev'],1);
    	$udalost[$row['udalost_id']] = $r[1];


      }


      return $udalost;

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
