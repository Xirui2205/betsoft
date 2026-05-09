<?php
/**
 * @package    livebet
 */

/**
 * Trida pro praci s Live sazkami
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    AJAX
 */

class Live{

/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
  private  $dbGame;

 /**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
  private  $db;

  /**
 * kurzy meny
 * @access private
 * @var array
 */
  private  $mena=array();

 /**
 * spojeni na databazi Session
 * @access private
 * @var DB
 */
  private  $dbSession;

   /**
 * pole info ke sportup
 * @access private
 * @var array
 */
  private $sportInfo = array(
                  1001=>array('live_vykdom','live_vykhos','live_goldom','live_golhos','live_pendom','live_penhos','live_pen_ne','live_zkdom','live_zkhos','live_ckdom','live_ckhos','live_rohdom','live_rohhos','live_nast_1pol','live_nast_2pol','live_striddom','live_stridhos','live_I'),
                  1003=>array('live_pr10_1set','live_pr10_2set','live_pr10_3set','live_MT','live_RD','live_R','live_I','live_D','live_MC'),
                  1006=>array('live_pr10_1ctvrt','live_pr10_2ctvrt','live_pr10_3ctvrt','live_pr10_4ctvrt','live_pr10_1pol','live_pr20_1pol','live_pr10_2pol','live_pr20_2pol','live_togo','live_I'),
                  1011=>array('live_goldom_hockey','live_golhos_hockey','live_PPH','live_PPA','live_PP2H','live_PP2A','live_FS_dom','live_ES','live_bezbr_dom','live_bezbr_hos','live_PSH','live_PSA','live_FS_hos','live_togo','live_I'),
                  1013=>array('live_pr10_1set','live_pr10_2set','live_pr10_3set','live_pr10_4set','live_pr10_5set','live_pr20_1set','live_pr20_2set','live_pr20_3set','live_pr20_4set','live_timeout','live_I','live_MC'),
                  1020=>array('live_zkdom','live_zkhos','live_ckdom','live_ckhos','live_PPH','live_PPA','live_PP2H','live_PP2A','live_FS_dom','live_FS_hos','live_ES','live_7mdom','live_7mhos','live_7mne','live_togo','live_timeout','live_I','live_MC'),
                  1029=>array('live_zahkolo','live_zavprob','live_zavprer','live_zavpokr','live_safcar','live_jezout','live_nejrkolo','live_nejlkolo','live_vitzav')
                  );
/**
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $db objekt spojeni s databazi
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0,$dbGme=null){

		$this->section =  $section;


    $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
    if (DB::isError($this->dbGame))
      throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");

    $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
    $sql = "SET NAMES 'utf8'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res))
			throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");


		$this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
		if (DB::isError($this->db))
			throw new ExHandler($this->db->getMessage(),"admin_ex_db");

		$this->db->setFetchMode(DB_FETCHMODE_ASSOC);
		$sql = "SET NAMES 'utf8'";
		$res =& $this->db->query($sql);
		if(DB::isError($res))
			throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

/*
		session_set_save_handler (
			array('It6_Session_Admin', 'open'),
			array('It6_Session_Admin', 'close'),
			array('It6_Session_Admin', 'read'),
			array('It6_Session_Admin', 'write'),
			array('It6_Session_Admin', 'destroy'),
			array('It6_Session_Admin', 'gc')
		);

		//if(!SesClass::open($this->db))
		if (!It6_Session_Admin::start($this->db))
			throw new ExHandler('Nepodarilo se inicializovat Session',"admin_ex_db");
*/

		//if(SesClass::getUserStatus() != 2){
		if (!It6_Session_Admin::isAuthenticated()) {
			echo "SESSION ERROR";
			return;
		}


		$this->FindAction();

		//SesClass::close($this->db);        //uzavreni session nelze uz pridavat dalsi session
		It6_Session_Admin::end($this->db);
		$this->db->disconnect();  //uzavreni spojeni s db
		$this->dbGame->disconnect();  //uzavreni spojeni s dbGame

  }

   /**
   *Vybere akci
   *
  */
  public function FindAction(){

  	if(!isset($_GET['event'])){
			echo 'array("error"=>"Nebyla nalezena Live sázka")';
			return;
		}

  	$sth = self::$this->dbGame->prepare("SELECT * FROM live_event WHERE event_id=?");
    if(PEAR::isError($sth))
			throw new ExHandler($sth->getMessage(),"admin_ex_db");

    $res =& self::$this->dbGame->execute($sth,array($_GET['event']));
    if(PEAR::isError($res))
			throw new ExHandler($res->getMessage().'Nepodarilo se vlozit baner',"admin_ex_db");

    if($row =& $res->fetchRow());


    $class = 'LiveSport'.$row['l_sport_id'];

  	if(isset($_GET['work'])){

  		if(isset($_GET['event']) && ctype_digit($_GET['event']) && $res->numRows()>0){
  			$ob = new $class(intval($_GET['work']),$row,$this->db,$this->dbGame);
  			$ob->PrintJson();
  		}
			else
  		 echo json_encode('array("error"=>"Nebyla nalezena Live sázka")');

  	}
  	else if(isset($_GET['event']) && ctype_digit($_GET['event'])){

  		$this->PrepareTemplate();

  	}

  }


 /**
 * Vybere udalosti k danemu sportu
 * @return array
 */
  private  function PrepareTemplate(){

		$sql = $this->dbGame->prepare("
			UPDATE live_event SET live_bookmaker_id=? WHERE event_id = ?
		");
		$res = $this->dbGame->execute($sql, array($_SESSION['bookmaker'],$_GET['event']));
		DbUtil::testResult($res);

		$sql = $this->dbGame->prepare("
			SELECT a.*,u.nazev AS unazev,s.nazev
			FROM live_event a
			INNER JOIN udalost u ON a.l_udalost_id=u.udalost_id
			INNER JOIN sport s ON u.sport_id=s.sport_id
			WHERE a.event_id=?
		");
		$res =& $this->dbGame->execute($sql, array($_GET['event']));
		DbUtil::testResult($res);

		if ($row = $res->fetchRow()){

			if($row['l_sport_id'] == 1003)
				$sth = $this->dbGame->prepare("
					SELECT score_home,score_away,first_service,set_num,court,note
					FROM live_".$row['l_sport_id']."
					WHERE event_id=?
				");
			else if($row['l_sport_id'] == 1013)
				$sth = $this->dbGame->prepare("
					SELECT score_home,score_away,service,note
					FROM live_".$row['l_sport_id']."
					WHERE event_id=?
				");
			else if($row['l_sport_id'] == 1029)
				$sth = $this->dbGame->prepare("
					SELECT lap,note
					FROM live_".$row['l_sport_id']."
					WHERE event_id=?");
			else
				$sth = $this->dbGame->prepare("
					SELECT score_home,score_away,note
					FROM live_".$row['l_sport_id']."
					WHERE event_id=?
				");

			DbUtil::testResult($sth);
			$res =& $this->dbGame->execute($sth,array($_GET['event']));
			DbUtil::testResult($res);
			$row2 =& $res->fetchRow();


			$preklad = new Preklady();
			$row['unazev'] = $preklad->findPreklad($row['unazev'],1);

			$this->tpl = new TemplatePower( TPLLIVE, T_BYFILE );
			$this->tpl->prepare();
			$this->tpl->assign("TITLE","Žívé sázení");
			$this->tpl->assign("TEAM",$row['home_team'].' - '.$row['away_team']);
			$this->tpl->assign("SU",$row['nazev'].': '.$row['unazev'][1]);
			$this->tpl->assign("EVENT_ID",$_GET['event']);
			$this->tpl->assign("MINUTE",$row['minute']);
			$this->tpl->assign("WIN",$row['win']);
			$this->tpl->assign("PROVE_AMOUNT",$row['limit_bet']);
			$this->tpl->assign("PROVE_RATE",$row['limit_rate']);
			$this->tpl->assign("SPECIAL_INFO",$this->specialInfo($row['l_sport_id']));
			$this->tpl->assign("SCORE_HOME",$row2['score_home']);
			$this->tpl->assign("SCORE_AWAY",$row2['score_away']);
			$this->tpl->assign("BEGIN_AT",It6_Date::fromDb($row['start_date']));
			$this->tpl->assign("PERMANENT_NOTE",$row2['note']);

			if($row['l_sport_id'] == 1003){

				$kurt = '
					<tr>
						<td>&nbsp;</td>
						<td>
							<input
								type="radio"
								name="court"
								'.($row2['court'] == 'hardcourt'?'checked="checked"':'').'
								onclick="SetCourt(\'hardcourt\')"
							/>
						</td>
						<td>Rychlý</td>
						<td>
							<input
								type="radio"
								name="court"
								'.($row2['court'] == 'grass'?'checked="checked"':'').'
								onclick="SetCourt(\'grass\')"
							/>
						</td>
						<td>Tráva</td>
						<td>
							<input
								type="radio"
								name="court"
								'.($row2['court'] == 'clay'?'checked="checked"':'').'
								onclick="SetCourt(\'clay\')"
							/>
						</td>
						<td>Antuka</td>
					</tr>
				';
				$this->tpl->assign("KURT",$kurt);

				$set = '
					<tr>
						<td>&nbsp;</td>
						<td>
							<input
								type="radio"
								name="set_num"
								'.($row2['set_num'] == 5?'checked="checked"':'').'
								onclick="SetNum(5)"
							/>
						</td>
						<td>5 setů</td>
						<td>
							<input
								type="radio"
								name="set_num"
								'.($row2['set_num'] == 3?'checked="checked"':'').'
								onclick="SetNum(3)"
							/>
						</td>
						<td>3 sety</td>
					</tr>
				';
				$this->tpl->assign("SET",$set);
			}

			else if($row['l_sport_id'] == 1013 || $row['l_sport_id'] == 1003){

				$first_service = '
					<tr>
						<td>&nbsp; </td>
						<td>
							<input
								type="radio"
								'.($row2['first_service'] == 'home'?'checked="checked"':'').'
								name="first_service"
								onclick="First_service(1)"
							/>
						</td>
						<td> Domácí</td>
						<td>
							<input
								type="radio"
								'.($row2['first_service'] == 'away'?'checked="checked"':'').'
								name="first_service"
								onclick="First_service(2)"
							/>
						</td>
						<td>Host</td>
					</tr>
				';
				$this->tpl->assign("FIRST_SERVICE",$first_service);
			}

			$this->tpl->printToScreen();

		}
		else
			echo "Template error";
	}


 /**
 * Vraci option pro spec. info ke kazdemu live sportu
 * @param int $sport_id id sportu
 * @return string
 */
  public  function specialInfo($sport_id){

  	 $vrat = '';
  	 $p = new Preklady();

  	 foreach($this->sportInfo[$sport_id] as $h){

  	 	 $pp = $p->findPreklad($h,1);
  	 	 $vrat .= '<option value="'.Help::Slash($h).'">'.$pp[1].'</option>';

  	 }

  	 return $vrat;

  }


      /**
 * Vybere udalosti k danemu sportu
 * @return array
 */
  public  function UdalostLiveSelect(){


  	$preklad = new Preklady();


    $sql = "select udalost_id,nazev from udalost where sport_id=".intval($_GET['sport']);
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz:  udalosti',"admin_ex_db");

    $udalost = array();
    while($row =& $res->fetchRow()){

    	$r = $preklad->FindPreklad($row['nazev'],1);
    	$udalost[$row['udalost_id']] = $r[1];


    }


    setlocale(LC_COLLATE, "cs_CZ.utf8");
	uasort($udalost,'strcoll');

	foreach($udalost as $k=>$h){

		echo '<option value="'.$k.'">'.$h.'</option>';

	}

  }

}

?>
