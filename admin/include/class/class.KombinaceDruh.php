<?php


class KombinaceDruh extends Template{

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
  public function runAction(){


  	if(isset($_POST['create'])){
  	 	 $this->Create();
  	}
   	else if(isset($_GET['u1']) && isset($_GET['u2']) && isset($_GET['t1']) && isset($_GET['t2'])){
  	 	 $this->Delete();
  	}
    $this->ShowData();





   $this->dbGame->disconnect();

  }

      /**
 * vymaze data
 * @return void
 */
  private function Delete(){

  	$sql = "delete from kombinace_druh where udalost_id_1=".intval($_GET['u1'])." and udalost_id_2=".intval($_GET['u2'])." and typ_id_1=".intval($_GET['t1'])." and typ_id_2=".intval($_GET['t2']);
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni nove udalosti',"admin_ex_db");

    $this->vrat .= UiUtil::printNotice('Kombinace byla vymazána'); // "<div class=\"okmsg\">Kombinace byla vymazána</div><br />";

		It6_Log::info(
			"Combination was deleted. '%u1%' -> '%t1%' -> '%u2%' -> '%t2%'",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'u1' => $_GET['u1'],
				't1' => $_GET['t1'],
				'u2' => $_GET['u2'],
				't2' => $_GET['t2'],
			)
		);
  }
    /**
 * vypise data
 * @return void
 */
  private function Create(){

  	$status = true;

     if(!isset($_POST['udalost_1']) || !is_array($_POST['udalost_1']) || !isset($_POST['udalost_2']) || !is_array($_POST['udalost_2']) || !isset($_POST['typ_1']) || !isset($_POST['typ_2'])) {$this->vrat .= "<div class=\"errormsg\"> Pro vytvoření kombinace musíte zvolit události i druhy</div><br />";$status = false;}


      if($status){

      	$viditelnost = (isset($_POST['viditelnost'])?1:0);

      	foreach($_POST['udalost_1'] as $h){

      	  if($h == 0) continue;

      	  foreach($_POST['udalost_2'] as $h2){

      	  	if($h2 == 0) continue;
      	  	else if($h== $h2 && $_POST['typ_1']==$_POST['typ_2'])  continue;

      	  	$sql = "delete from kombinace_druh where (udalost_id_1=".intval($h)." and udalost_id_2=".intval($h2)." and typ_id_1=".intval($_POST['typ_1'])." and typ_id_2=".intval($_POST['typ_2']).") or (udalost_id_2=".intval($h)." and udalost_id_1=".intval($h2)." and typ_id_2=".intval($_POST['typ_1'])." and typ_id_1=".intval($_POST['typ_2']).") ";
      	  	$res =& $this->dbGame->query($sql);
      	  	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni nove udalosti',"admin_ex_db");

      	  	$sql = "replace into kombinace_druh values(".intval($h).",".intval($_POST['typ_1']).",".intval($h2).",".intval($_POST['typ_2']).",".$viditelnost.")";
      	  	$res =& $this->dbGame->query($sql);
      	  	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni nove udalosti',"admin_ex_db");

      	  }

      	}

      	$this->vrat .= UiUtil::printNotice('Kombinace byla vytvořena'); //"<div class=\"okmsg\">Kombinace byla vytvořena</div><br />";

		It6_Log::info(
			"New combination was created.",
			It6_Log::TAG_ADMIN_OPERATION
		);

      }

  }

  /**
 * vypise data
 * @return void
 */
  private function ShowData(){
  	 $this->vrat .=
		'<h3>Nová</h3>
		<form method="post" action="?section=141">
			<table>
				<tr>
					<th>UDÁLOST 1</th>
					<th>UDÁLOST 2</th>
				</tr>
				<tr>
					<td valign="top" style="vertical-align:top">'.$this->RetUdalost(1).'</td>
					<td valign="top" style="vertical-align:top">'.$this->RetUdalost(2).'</td>
				<tr>
				<tr>
					<th>DRUH 1</th>
					<th>DRUH 2</th>
				</tr>
				<tr>
					<td>'.$this->RetDruh(1).'</td>
					<td>'.$this->RetDruh(2).'</td>
				<tr>
				<tr>
					<td>
						Viditelnost
						<input type="checkbox" class="no" name="viditelnost" />
					</td>
					<td><input type="submit" name="create" value="Vytvořit" /></td>
				</tr>
			<table>
		</form>
		
		<h3>Existující</h3>';

  	 $this->vrat .=  $this->showCreate();


  }


  /**
 * Zobrazi vytvorene kombinace
 * @return sting
 */
  private function showCreate(){

  	$typ = $udalost = $sport = $komb = array();
  	$vrat = '';

  	$preklad = new Preklady();

  	$sql = "select typ_id,nazev from typ";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");

    while ($row =& $res->fetchRow()){

      	$s = $preklad->FindPreklad($row['nazev'],1);
      	$typ[$row['typ_id']] = $s[1];

    }

    $sql = "select s.nazev as snazev,u.nazev as unazev,u.udalost_id,s.sport_id from sport s inner join udalost u on s.sport_id=u.sport_id";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");

    while ($row =& $res->fetchRow()){


     	$s = $preklad->FindPreklad($row['snazev'],1);
     	$udalost[$row['udalost_id']]['s'] = $s[1];


     	$s = $preklad->FindPreklad($row['unazev'],1);
     	$udalost[$row['udalost_id']]['u'] = $s[1];

    }

    $sql = "select * from kombinace_druh order by udalost_id_1,typ_id_1,udalost_id_2,typ_id_2";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");

    while ($row =& $res->fetchRow()){

        $klic = count($komb);
    	$komb[$klic]['udalost_id_1'] = $row['udalost_id_1'];
    	$komb[$klic]['udalost_id_2'] = $row['udalost_id_2'];
    	$komb[$klic]['typ_id_1'] = $row['typ_id_1'];
    	$komb[$klic]['typ_id_2'] = $row['typ_id_2'];
    	$komb[$klic]['viditelnost'] = $row['viditelnost'];

    }

    $vrat .= '<table><tr>';$x = 0;
    foreach($komb as $h){
    	//$x++;
    	$vrat .= '<tr><td style="padding:6px;border:1px solid black">'.$udalost[$h['udalost_id_1']]['s'].'->'.$udalost[$h['udalost_id_1']]['u'].'->'.$typ[$h['typ_id_1']].'</td><td style="padding:6px;border:1px solid black">'.$udalost[$h['udalost_id_2']]['s'].'->'.$udalost[$h['udalost_id_2']]['u'].'->'.$typ[$h['typ_id_2']].'</td><td>'.($h['viditelnost']==1?'Viditelné':'Neviditelné').'</td><td style=""><a href="?section=141&u1='.$h['udalost_id_1'].'&u2='.$h['udalost_id_2'].'&t1='.$h['typ_id_1'].'&t2='.$h['typ_id_2'].'" onclick="if(!confirm(\'Opravdu chcete smazat?\'))return false;">Zrušit</a></td></tr>';
       //if($x==3) {$vrat .= '</tr><tr>';$x=0;}
    }
    $vrat .= '</tr></table>';

    return $vrat;

  }

/**
 * Vyber udalosti
 * @return sting
 */
private function RetDruh($n){

	$vrat = '';
	$preklad = new Preklady();

	$sql = "select typ_id,nazev from typ";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");
/*
      $vrat .= '<table border="1">';
      while ($row =& $res->fetchRow()){

      	$s = $preklad->FindPreklad($row['nazev'],1);
      	$vrat .= '<tr><td style="font-size:11b px;">'.$s[1].'</td><td> <input type="radio" class="no" name="typ_'.$n.'" value="'.$row['typ_id'].'" /></td></tr>';

      }
      $vrat .= '</table>';
*/
	$vrat .= "<select name=\"typ_$n\">\n";
	while ($row = $res->fetchRow()){
		$s = $preklad->FindPreklad($row['nazev'], 1);
		$vrat .= "<option value=\"{$row['typ_id']}\">{$s[1]}</option>\n";
	}
	$vrat .= "</select>\n";

	return $vrat;

}

	/**
	* Vyber udalosti
	* @return sting
	*/
	private function RetUdalost($n) {
		$_sports = It6_SportEventsFilter::getSportEvents(empty($_REQUEST['sport_opt_'.$n]) ? null : $_REQUEST['sport_opt_'.$n]);
		list($htmlSports, $htmlEvents) = It6_SportEventsFilter::getSportFilterHtml(
			$_sports,
			(empty($_REQUEST['sport_opt_'.$n]) ? null : $_REQUEST['sport_opt_'.$n]),
			(empty($_REQUEST['udalost_'.$n]) ? null : $_REQUEST['udalost_'.$n]),
			true,
			true
		);
		
		$ret = 
			'<select name="sport_opt_'.$n.'" onClick="loadEventOptsForSport(this, \'[name=\\\'udalost_'.$n.'[]\\\']\')">
				'.$htmlSports.'
			</select>
			<br>
			<select name="udalost_'.$n.'[]" multiple="multiple" size="15">
				'.$htmlEvents.'
			</select>';
		
		return $ret;
	}


 /**
 * Vytvoreni kombinace s ostatnimi sazkami ktere jsou v kombinaci druh
 * @param int $u_id udalost_id
 * @param int $t_id typ id
 * @param int $sazka_id id sazky
 * @return sting
 */
public static function makeKomb($u_id,$t_id,$sazka_id,$dbGame){

$sazky = array();
$sql = "select * from kombinace_druh where (udalost_id_1=".$u_id." and typ_id_1=".$t_id.") or (udalost_id_2=".$u_id." and typ_id_2=".$t_id.")";
$res =& $dbGame->query($sql);

if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber kombinace druh',"admin_ex_db");

while ($row =& $res->fetchRow()) {
	if($row['udalost_id_1'] == $u_id && $row['typ_id_1'] == $t_id && $row['udalost_id_2'] == $u_id && $row['typ_id_2'] == $t_id)
		continue;
	if($row['udalost_id_1'] == $u_id && $row['typ_id_1'] == $t_id) {
		$udalost = $row['udalost_id_2'];
		$typ = $row['typ_id_2'];
	} else {
		$udalost = $row['udalost_id_1'];$typ = $row['typ_id_1'];
	}

	$sql = "select sazka_id from sazky where udalost_id=".$udalost." and typ_id=".$typ." and overena=0 and proplacena=0";
	$res2 =& $dbGame->query($sql);
	if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber sazek',"admin_ex_db");

	while ($row2 =& $res2->fetchRow()){
		$sazky[$row2['sazka_id']] = $row['viditelnost'];
	}
}

foreach($sazky as $k=>$h) {
	$sql = "delete from sazka_kombinace where (sazka1_id=".$sazka_id." and sazka2_id=".$k.") or (sazka1_id=".$k." and sazka2_id=".$sazka_id.")";
	$res2 =& $dbGame->query($sql);
	if(DB::isError($res2)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber sazek',"admin_ex_db");

	$sql = "insert into sazka_kombinace (sazka1_id,sazka2_id,kombinace_show) values (".$sazka_id.",".$k.",".$h.")";
	$res2 =& $dbGame->query($sql);
	if (DB::isError($res2)) {
		It6_Log::warn(
			"Error creating Betradar combinations %betId% - %betId2%: replace sazka_kombinace error.",
			It6_Log::TAG_BETRADAR_OPERATION,
			array(
				'betId' => $k,
				'betId2' => $sazka_id)
		);
		throw new ExHandler($sql.'Nepodarilo se provest dotaz: vlozeni kombinace',"admin_ex_db");
	} else {
		It6_Log::info(
			"Combination between bets %betId% - %betId2% created.",
			It6_Log::TAG_BETRADAR_OPERATION,
			array(
				'betId' => $sazka_id,
				'betId2' => $k
			)
		);
	}
}

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
