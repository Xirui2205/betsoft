<?php

/**
 * @package    statistics
 */

/**
 * Trida pro zobrazeni tiketu uzivatelu
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class PovolovaniStat extends Template{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";



/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;


/**
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

/**
 *ma se ukazat filtr formular
 * @access private
 * @var bool
 */
private  $nomenu=false;


/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0){

    $this->section =  $section;


    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame)) {
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
    }
    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "set names 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


  }

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction($nomenu=false){

  	$this->nomenu = $nomenu;

   if($_SESSION['superbookmaker'] != 1) {$this->vrat .= "<div class=\"okmsg\">Nemáte právo pro vstup do této sekce</div><br />";return;}



   $this->ShowTicket();

   $this->dbGame->disconnect();

  }


 /**
 * metoda vypise vsechny zadane tickety podle filtru
 * @return void
 */
  public function ShowTicket(){


   $bookmaker =  $data = array();
   $book = '';

   #Vyber bookmakeru#
   $sql = "select a.jmeno,a.prijmeni,a.nick,a.bookmaker_id from bookmaker a";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bookmakera',"admin_ex_db");

   while ($row =& $res->fetchRow()){

     $bookmaker[$row['bookmaker_id']]['nick'] = $row['nick'];
	 $bookmaker[$row['bookmaker_id']]['jmeno'] = $row['jmeno']." ".$row['prijmeni'];

	 $book .=  "<option  value=\"".$row['bookmaker_id']."\" ".(isset($_POST['book']) && $_POST['book'] == $row['bookmaker_id']?"selected=\"selected\"":"").">".$bookmaker[$row['bookmaker_id']]['nick']."</option>";

   }

   if(isset($_POST['filtr'])){
    $where = '1 and';


    if(isset($_REQUEST['od']) && It6_Date::checkFormat($_REQUEST['od'])) {$od = It6_Date::toDb($_REQUEST['od']);$where .= " a.date>'".$od."' and";}
  	if(isset($_REQUEST['do']) && It6_Date::checkFormat($_REQUEST['do'])) {$do = It6_Date::toDb($_REQUEST['do']);$where .= " a.date<'".$do."' and";}
    if(isset($_REQUEST['book']) && ctype_digit($_REQUEST['book']) && $_REQUEST['book'] != 0) {$where .= " a.bookmaker_id=".$_REQUEST['book']." and";}

  	$where = substr($where,0,-3);

    $sql = "
      SELECT a.*,b.nick,b.nick,b.jmeno,b.prijmeni
      FROM bookmaker_ticket_prove_log a
      INNER JOIN uzivatel b ON a.user_id=b.user_id
      WHERE ".$where." order by a.date desc
    ";

    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bookmakera',"admin_ex_db");

    while ($row =& $res->fetchRow()){

   	  $bet = explode(';',$row['sazky']);

   	  $b = array();
   	  foreach($bet as $h){

   	  	if(ctype_digit($h)){

   	  		$sql = "SELECT a.text FROM sazky a WHERE a.sazka_id=".$h;
            $res2 =& $this->dbGame->query($sql);
            if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber bookmakera',"admin_ex_db");
            if ($row2 =& $res2->fetchRow()) $b[$h] = $row2['text'];

   	  	}

   	  }

   	  $data[] = array("user"=>$row['user_id'].' - '.$row['nick'],"sazky"=>$b,"bookmaker"=>$bookmaker[$row['bookmaker_id']]['nick'],"status"=>$row['status'],"cas"=>It6_Date::fromDb($row['date']),"req"=>$row['req_amount'],"prove"=>$row['prove_amount']);

   }
   }
//var_dump($_POST);
    $this->vrat .= '
      <form method="post" action="?'.($this->section==90?'':'superb=1&').'section='.$this->section.'">
        <table class="filtr">
          <tr>
            <td class="textleft" class="head" colspan="4">Filtr</td>
          </tr>

          <tr>
            <td>
              <select style="font-size:0.8em" name="book" size="4">
                <option value="0">Všichni</option>
                '.$book.'
              </select>
            </td>
          </tr>

          <tr>
            <td>
              <strong>Od:</strong>
              <input
                type="text"
                name="od"
                id="od"
                class="sinput3 dateTime"
                value="'.(isset($_POST['od'])?Help::Html($_POST['od']):"").'"
              />
              <img src="_clip/calendar.gif" class="calendar-icon">
              -
              <input
                type="text"
                name="do"
                id="do"
                class="sinput3 dateTime"
                value="'.(isset($_POST['do'])?Help::Html($_POST['do']):"").'"
              />
              <img src="_clip/calendar.gif" class="calendar-icon">
            </td>
          </tr>

          <tr>
            <td>
              <input type="submit" name="filtr" class="inputs" value="Filtr" />
            </td>
          </tr>

        </table>

        <br />

        <table class="unitable2">
          <thead>
            <tr>
              <th>Čas</th>
              <th>Bookmaker</th>
              <th>Zákazník</th>
              <th>Sázky</th>
              <th>Požadovaná částka</th>
              <th>Povolená částka</th>
              <th>Status</th>
            </tr>
          </thead>';

   if(count($data) == 0)  $this->vrat .= '<tr><td colspan="7">žádný záznam</td></tr>';

   foreach($data as $h){

   	 if($h['status'] == 2) $status = 'zamítnuto';
   	 else if($h['status'] == 4) $status = 'jiná částka';
   	 else    $status = 'povoleno';

   	 $s = '';
   	 foreach($h['sazky'] as $sid=>$text){

   	 	$s .= '<a href="?superb=1&section=b13&filtr=1&filtr_sazka_id='.$sid.'&udalost=0" target="_blank">#'.$sid.'</a> '.$text.' <br />';

   	 }

   	 $this->vrat .= '<tr><td>'.$h['cas'].'</td><td>'.$h['bookmaker'].'</td><td>'.$h['user'].'</td><td>'.$s.'</td><td>'.$h['req'].'</td><td>'.$h['prove'].'</td><td><strong>'.$status.'</strong></td></tr>';

   }

   $this->vrat .= '</table>';

  }



 /**
 * vyber dat z databaze
 * @return object
 */
  public function selectData($where=""){


  }


  /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
  public function setPrivileges($update,$delete){



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
