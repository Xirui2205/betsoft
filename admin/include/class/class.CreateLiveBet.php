<?php
/**
 * @package    livebet
 */


 /**
 * Trida pro vytvareni live sazek
 *
 *
 * @package    main
 */

class CreateLiveBet{


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


  	if(isset($_POST['send'])) $this->SaveData();

    $this->ShowData();

    $this->dbGame->disconnect();

  }

    /**
 * Ulozit data
 * @return void
 */
  public  function SaveData(){

  	$status = true;

    if(!isset($_POST['zahajeni']) || !It6_Date::checkFormat($_POST['zahajeni'])){$this->vrat .= "<div class=\"errormsg\"><strong>Datum zahájení</strong> nemá správný formát dd.mm.RRRR HH:mm:ss</div><br />";$status = false;}
    if(!isset($_POST['sport']) || intval($_POST['sport'])==0){$this->vrat .= "<div class=\"errormsg\"><strong>Sport</strong> musí být vybrán</div><br />";$status = false;}
    if(!isset($_POST['udalost']) || intval($_POST['udalost'])==0){$this->vrat .= "<div class=\"errormsg\"><strong>Událost</strong> musí být vybrána</div><br />";$status = false;}
    if(!isset($_POST['domaci']) || !isset($_POST['hoste']) || mb_strlen($_POST['domaci'])==0 || mb_strlen($_POST['hoste'])==0){$this->vrat .= "<div class=\"errormsg\"><strong>Domácí / Hosté</strong> musí být vyplněno</div><br />";$status = false;}

  	if($status){

  	   $this->dbGame->autocommit(false);

  	   $sth = $this->dbGame->prepare("insert into live_event (start_date,stav,l_sport_id,l_udalost_id,home_team,away_team) values (?,?,?,?,?,?)");
       if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");
     // echo  It6_Date::toDb($_POST['zahajeni'])." - ".'live_stav_1'." - ".intval($_POST['sport'])." - ".intval($_POST['udalost'])." - ".$_POST['domaci']." - ".$_POST['hoste'];
       $res2 =& $this->dbGame->execute($sth,array(It6_Date::toDb($_POST['zahajeni']),0,intval($_POST['sport']),intval($_POST['udalost']),$_POST['domaci'],$_POST['hoste']));
       if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");

       $id = mysqli_insert_id($this->dbGame->connection);

       $sth = $this->dbGame->prepare("insert into live_".intval($_POST['sport'])." (event_id) values (?)");
       if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

       $res2 =& $this->dbGame->execute($sth,array($id));
       if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");

  	   if(isset($_POST['kombinace']) && mb_strlen($_POST['kombinace']) > 2){

  	   	$komb = explode(";",$_POST['kombinace']);

  	   	foreach($komb as $h){
  	   	  if(ctype_digit($h)){

  	   	   $sth3 = $this->dbGame->prepare("insert into live_kombinace (event_id,sazka_id) values (?,?)");
           if (PEAR::isError($sth3))  throw new ExHandler($sth3->getMessage(),"admin_ex_db");
  	       $res2 =& $this->dbGame->execute($sth3,array($id,$h));
           if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");

  	   	   $sth5 = $this->dbGame->prepare("select sazka1_id,sazka2_id from sazka_kombinace where kombinace_show=1 and (sazka1_id=? or sazka2_id=?)");
           if (PEAR::isError($sth5))  throw new ExHandler($sth3->getMessage(),"admin_ex_db");
  	       $res5 =& $this->dbGame->execute($sth5,array($h,$h));
           if (PEAR::isError($res5))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");

           while ($row5 =& $res5->fetchRow()){
           	 if($row5['sazka1_id'] == $h) $next_id = $row5['sazka2_id'];else $next_id = $row5['sazka1_id'];
  	         $res2 =& $this->dbGame->execute($sth3,array($id,$next_id));
             if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se vytvorit live sazku',"admin_ex_db");
           }

  	   	  }
  	   	}

  	   }
		$this->dbGame->commit();
		$this->vrat .= "<div class=\"okmsg\">Live sázka byla vytvořena.</div><br />";
		It6_Log::info(
			"Live bet #%id% created.",
			It6_Log::TAG_BOOKMAKER_OPERATION,
			array('id' => $id));
  	}

  }

  /**
 * Zobrazi data k zadani
 * @return void
 */
  public  function ShowData(){

  	$this->vrat .= '
      <script>
        var uid = 0;
      </script>

      <h3>'.I18n::tr('Create a live bet').'</h3>
      <form method="post" class="noprint" action="?superb=1&section='.$this->section.'">
        <table class="table-detail">
          <tr>
            <td>Sport</td>
            <td>
              <select name="sport" id="sport" onchange="jQuery.LoadEventLive();" >
                <option value="0">Zvolte sport</option>
                '.$this->Sport().'
              </select>
            </td>
          </tr>

          <tr>
            <td>Liga </td>
            <td>
              <select name="udalost" id="udalost" onchange="uid = this.options[this.selectedIndex].value">
                <option value="0">Musíte vybrat událost ...</option>
              </select>
            </td>
          </tr>

          <tr>
            <td>Kombinace </td>
            <td>
              <input type="text" class="input" name="kombinace" id="kombinace" />
              <a href="javascript:openWin(\'ciselnik.php\',\'kombinace\',\'kombinace\',600,400,\'&live=1&udalost=\'+uid);void(0);">
                <img src="_clip/comb.gif" alt="Vyber sázky do kombinace" class="img" />
              </a>
            </td>
          </tr>

          <tr>
            <td>Zahájení</td>
            <td>
              <input
                type="text"
                class="input dateTime"
                name="zahajeni"
                id="zahajeni"
                maxlength="19"
                value="'.(isset($_REQUEST['zahajeni'])?Help::Html($_REQUEST['zahajeni']):"").'"
              />
              <img src="_clip/calendar.gif" class="calendar-icon" />
            </td>
          </tr>

          <tr>
            <td>Domácí</td>
            <td>
              <input
                type="text"
                class="input"
                name="domaci"
                id="domaci"
                value="'.(isset($_REQUEST['domaci'])?Help::Html($_REQUEST['domaci']):"").'"
              />
				<a href="javascript:openWin(\'ciselnik.php\',\'domaci\',\'tymy\',800,300);void(0);">
					<img class="img" alt="Týmy" src="https://admin.testbook.cz/_clip/translate.gif">
				</a>
            </td>
          </tr>

          <tr>
            <td>Hosté </td>
            <td>
              <input
                type="text"
                class="input"
                name="hoste"
                id="hoste"
                value="'.(isset($_REQUEST['hoste'])?Help::Html($_REQUEST['hoste']):"").'"
              />
				<a href="javascript:openWin(\'ciselnik.php\',\'hoste\',\'tymy\',800,300);void(0);">
					<img class="img" alt="Týmy" src="https://admin.testbook.cz/_clip/translate.gif">
				</a>
            </td>
          </tr>

          <tr>
            <td colspan="3">
              <input type="submit" name="send"  value="Vytvořit" />
            </td>
          </tr>
        </table>
      </form>';

  }

    /**
 * Vybere sporty ktere jsou pro live sazky
 * @return string
 */
  public  function Sport(){

  	$vrat = '';

    $sth = $this->dbGame->prepare("select nazev,sport_id from sport where live=1");
    if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

    $res2 =& $this->dbGame->execute($sth);
    if (PEAR::isError($res2))  throw new ExHandler($res2->getMessage().'Nepodarilo se nacist sporty',"admin_ex_db");

    while ($row =& $res2->fetchRow()){
		//commented out by Martin on 3.12. - this statement makes no sense to me
    	//$vrat .= '<option '.($_POST['sport']==$row['sport_id']).' value="'.$row['sport_id'].'" >'.$row['nazev'].'</option>';
    	$vrat .= '<option value="'.$row['sport_id'].'" >'.$row['nazev'].'</option>';

    }




  	return $vrat;


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
