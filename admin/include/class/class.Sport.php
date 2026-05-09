<?php
/**
 * @package    ciselnik
 */

/**
 * Trida pro praci s cislenikem sportu
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class Sport extends Template{

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

    $this->CreateSport();

   }

   #Vymazani sportu#
   else if(isset($_POST['delete'])){

	    $this->DeleteSport(key($_POST['delete']));

   }

   #Zobrazeni ne skryti prvku#
   if(isset($_POST['zobrazit']) || isset($_POST['skryt'])){

	    $this->ZobrazitSkrytSport((isset($_POST['zobrazit'])?key($_POST['zobrazit']):key($_POST['skryt'])));

   }

   #Editace sportu#
   if(isset($_POST['edit'])){

	    $this->EditSport(intval(key($_POST['edit'])));

   }

   #Posuny sportu#
   else if(isset($_POST['up']) || isset($_POST['down'])){

		$key = (isset($_POST['up'])?key($_POST['up']):key($_POST['down']));
		$pozice = (isset($_POST['up'])?intval(key($_POST['up'][$key])):intval(key($_POST['down'][$key])));

		$this->UpDown($key,$pozice);

   }

   $this->ShowSports();



    $this->dbGame->disconnect();

  }

   /**
 * Edit sportu
 * @param int $sport_id id sportu
 * @return void
 */
  private function EditSport($sport_id){

   $status = true;


	if (!isset($_POST['sport'][$sport_id]['nazev']) || mb_strlen(trim($_POST['sport'][$sport_id]['nazev'])) > 20 || mb_strlen(trim($_POST['sport'][$sport_id]['nazev'])) < 1) {
		$this->vrat .= UiUtil::printErrors("<strong>Název</strong> musí být vybrán z překladů");
		$status = false;
	}

   if(isset($_POST['sport'][$sport_id]['nazev']) && $status){

     $preklady = new Preklady();

     $res = $preklady->selectData("where index_pole='".Help::slash($_POST['sport'][$sport_id]['nazev'])."'");

	if (!$row =& $res->fetchRow()) {
		$this->vrat .= UiUtil::printErrors("(<strong>Název</strong>) tento index není platným překladem");
		$status = false;
	}

   }

   #vsechno je  vporadku muzeme editovat#
   if($status){

		$sql = "
			UPDATE sport
			SET betradar_sport_id=".intval($_POST['sport'][$sport_id]['betradar_sport_id']).",
				zvyrazneni=".(isset($_POST['sport'][$sport_id]['zvyrazneno'])?1:0).",
				nazev='".Help::Slash($_POST['sport'][$sport_id]['nazev'])."',
				bet_alias_from=".intval($_POST['sport'][$sport_id]['bet_alias_from']).",
				bet_alias_to=".intval($_POST['sport'][$sport_id]['bet_alias_to']).",
				bet_alias_from_new=".intval($_POST['sport'][$sport_id]['bet_alias_from_new']).",
				bet_alias_to_new=".intval($_POST['sport'][$sport_id]['bet_alias_to_new'])."
			WHERE sport_id=".$sport_id;

		$res =& $this->dbGame->query($sql);

		DbUtil::testResult($res);

		foreach($_POST['sport'][$sport_id]['seo'] as $k=>$h) {
			$sql = "select url from seo_url where url='".Help::Slash($h)."'";
			$res =& $this->dbGame->query($sql);
			DbUtil::testResult($res,'Nepodarilo se provest dotaz: editace oblasti');

			if(mb_strlen($h) > 0 && $res->numRows()==0) {
				$sql = "replace into seo_url values (".intval($k).",1,'".Help::Slash($h)."',".$sport_id.")";
				$res =& $this->dbGame->query($sql);
				DbUtil::testResult($res,'Nepodarilo se provest dotaz: editace oblasti');
			}
		}

		$this->vrat .= UiUtil::printMessages("Sport ".Help::Html($_POST['sport'][$sport_id]['nazev'])." byl úspěšně editován");

		It6_Log::info(
			"Sport '%sport%' was updated.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('sport' => $_POST['sport'][$sport_id]['nazev'])
		);
		
		It6_GlobalCache_Invalidator::invalidateSportsbook();
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

   }

  }

   /**
 * Posun nahoru dolu
 * @param int $sport_id id sportu
 * @param int $pozice id aktualni pozice
 * return void
 */
  public function UpDown($sport_id,$pozice){

   $this->dbGame->autoCommit(false);

   try {
   if(isset($_POST['down']))
    $sql = "SELECT sport_id,pozice FROM `sport` WHERE pozice>".$pozice." order by pozice limit 1";
   else
    $sql = "SELECT sport_id,pozice FROM `sport` WHERE pozice<".$pozice." order by pozice desc limit 1";

   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky sport',"admin_ex_db");

   if ($row =& $res->fetchRow()) {$sport_id_2 = $row['sport_id'];$pozice2 = $row['pozice'];}else{$this->dbGame->rollback();throw new ExHandler('Chyba nebyla zjistena pozice',"admin_ex_poge");}

   $sql = "update sport set pozice=".$pozice." where sport_id=".$sport_id_2;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky sport',"admin_ex_db");

   $sql = "update sport set pozice=".$pozice2." where sport_id=".$sport_id;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky sport',"admin_ex_db");

   $this->dbGame->autoCommit(true);
   $this->dbGame->commit();

   $this->vrat .= "<div class=\"okmsg\">Sport ".Help::Html($_POST['sport'][$sport_id]['nazev'])." byl ůspěšně ".(isset($_POST['up'])?"posunut nahoru":"posunut dolů")."</div><br />";

		It6_Log::info(
			"Sport '%sport%' was moved '%action%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'sport'		=> $_POST['sport'][$sport_id]['nazev'],
				'action'	=> (isset($_POST['up']) ? "up" : "down")
			)
		);
  } catch (Exception $e) {
  	It6_Log::notice(
			"Sport '%sport%' up/down failed: %message%",
			It6_Log::TAG_ADMIN_OPERATION,
			array('sport' => $_POST['sport'][$sport_id]['nazev'],'message' => $res->message)
		);
		$this->vrat .= UiUtil::printErrors('Nepodarilo se provest dotaz: editace sportu');
		return false;
  }
}


   /**
 * Zobrazeni nebo skryti sportu
 * @param int $sport_id id sportu
 * return void
 */
public function ZobrazitSkrytSport($sport_id){

	$sql = "update sport set zobrazeno=".(isset($_POST['zobrazit'])?1:0)." where sport_id=".intval($sport_id);
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) {
		It6_Log::notice(
			"Sport '%sport%' visibility change failed: %message%",
			It6_Log::TAG_ADMIN_OPERATION,
			array('sport' => $_POST['sport'][$sport_id]['nazev'],'message' => $res->message)
		);
		$this->vrat .= UiUtil::printErrors('Nepodarilo se provest dotaz: editace sportu');
		return false;
	}

	 if($this->dbGame->affectedRows()){

	  $this->vrat .= "<div class=\"okmsg\">Sport ".Help::Html($_POST['sport'][$sport_id]['nazev'])." byl ůspěšně ".(isset($_POST['zobrazit'])?"zobrazen":"skryt")."</div><br />";

		It6_Log::info(
			"Sport '%sport%' was '%action%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'sport'		=> intval($podtyp_id),
				'action'	=> (isset($_POST['zobrazit'])?"shown":"hidden")
			)
		);
	 } else {
		$this->vrat .= UiUtil::printErrors( "Sport se nepodařilo editovat");
		It6_Log::notice(
			"Sport '%sport%' visibility change failed: No affected rows",
			It6_Log::TAG_ADMIN_OPERATION,
			array('sport' => $_POST['sport'][$sport_id]['nazev'])
		);
	 }
}


   /**
 * Vymazani sportu
 * @param int $sport_id id sportu
 * return void
 */
  private function DeleteSport($sport_id){

	 $this->dbGame->autoCommit(false);

     $sql = "delete from sport where sport_id=".intval($sport_id);
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) {
	 	$this->dbGame->autoCommit(false);
	 	$this->dbGame->rollback();
	 	It6_Log::notice(
			"Sport '%sport%' deletetion failed: %message%",
			It6_Log::TAG_ADMIN_OPERATION,
			array('sport' => $_POST['sport'][$sport_id]['nazev'],'message' => $res->message)
		);
		$this->vrat .= UiUtil::printErrors('Nepodarilo se provest dotaz: vymazani sportu. Sport je asi navázán na události');
	 	return false;
	 }

	 if($this->dbGame->affectedRows()){


	   $this->vrat .= "<div class=\"okmsg\">Sport byl úspěšně smazán</div><br />";

		It6_Log::info(
			"Sport '%sport%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('sport' => $_POST['sport'][$sport_id]['nazev'])
		);

		It6_GlobalCache_Invalidator::invalidateSportsbook();
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

	 }else
	 	$this->vrat .= UiUtil::printErrors('Sport se nepodařilo smazat.');

	 	It6_Log::notice(
			"Sport '%sport%' deletetion failed: No affected rows",
			It6_Log::TAG_ADMIN_OPERATION,
			array('sport' => $_POST['sport'][$sport_id]['nazev'])
		);

		$this->dbGame->autoCommit(true);
		$this->dbGame->commit();

  }


 /**
 * Vytvoreni sportu
 * @return void
 */
  private function CreateSport(){

   $status = true;


   if(!isset($_POST['nazev']) || mb_strlen(trim($_POST['nazev'])) > 20 || mb_strlen(trim($_POST['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> musí být vybrán z překladů</div><br />";$status = false;}

   if(isset($_POST['nazev']) && $status){

     $preklady = new Preklady($this->section);

     $res = $preklady->selectData("where index_pole='".Help::slash($_POST['nazev'])."'");

     if (!$row =& $res->fetchRow()) {$this->vrat .= "<div class=\"errormsg\"> (<strong>Název</strong>) tento index není platným překladem</div><br />";$status = false;}

   }

   #vsechno je  vporadku muzeme zapisovat#
   if($status){

		$this->dbGame->autoCommit(false);

		$sql = "select max(pozice) AS m from sport";
        $res =& $this->dbGame->query($sql);

		if (!$row =& $res->fetchRow()) $poz = 1;else $poz = intval($row['m'])+1;

		$sql = "select count(*) AS c from sport where nazev = '".Help::slash($_POST['nazev'])."'";
        $res =& $this->dbGame->query($sql);
        $row =& $res->fetchRow();

		if ( intval($row['c']) > 0 ) {
			It6_Log::notice(
				"New sport '%sport%' creation failed: Duplicate name.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('sport' => $_POST['nazev'])
			);
			$this->vrat .= UiUtil::printErrors('Nepodarilo se vlozit novy sport: Duplikatni nazev.');
			return false;
		}
		

	    $sql = "insert into sport (nazev,pozice,zvyrazneni) values ('".Help::slash($_POST['nazev'])."',".$poz.",".(isset($_POST['zvyrazneno'])?1:0).")";
        $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) {$this->dbGame->autoCommit(false); $this->dbGame->rollback();throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho sportu',"admin_ex_db");}
	    $this->vrat .= "<div class=\"okmsg\">Sport ".Help::Html($_POST['nazev'])." byl úspěšně vytvořen</div><br />";

		It6_Log::info(
			"New sport '%sport%' was created.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('sport' => $_POST['nazev'])
		);


	    $this->dbGame->autoCommit(true);
        $this->dbGame->commit();

   }

  }

 /**
 * metoda vypise vsechny zadane sporty
 * @return void
 */
  public function ShowSports(){

   $jazyky = array();
   $sql = "select alt_text,lang_id from jazyky";
   $res =& $this->dbGame->query($sql);
   while ($row =& $res->fetchRow()){
   	  $jazyky[$row['lang_id']] = $row['alt_text'];
   }



   $sql = "select max(pozice) AS m,min(pozice)AS m1 from sport";
   $res =& $this->dbGame->query($sql);

   if (!$row =& $res->fetchRow()) {$poz = 1;$min = 1;}else {$poz = intval($row['m']);$min = intval($row['m1']);}

   $res = $this->selectData();

   /*hlavicka*/
	$this->vrat .= "
		<form method=\"post\" action=\"?superb=1&section=".$this->section."\">
			<table class=\"table-list\">
				<thead>
				<tr>
					<th>&nbsp;</th>
					<th class='border-left'>".I18n::tr('Name')."</th>
					<th>&nbsp;</th>
					<th class='border-left'>".I18n::tr('Betradar Id')."</th>
					<th class='border-left'>".I18n::tr('Alias from - to')."</th>
					<th class='border-left'>".I18n::tr('New alias from - to')."</th>
					<th class='border-left'>&nbsp;</th>
					<th>".I18n::tr('Special')."</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>";
   $x = 1;

   $preklady = new Preklady();

   while ($row =& $res->fetchRow()){

    $res2 = $preklady->selectData("where lang_id=1 and index_pole='".Help::slash($row['nazev'])."'");

    if ($row2 =& $res2->fetchRow()) $title = $row2['text'];else $title = "";

	$this->vrat .= '
		<tr>
			<td><strong>'. $x .'.</strong></td>
			<td class="border-left">
				<input
					type="text"
					class="mandatory"
					maxlength="20"
					title="'.Help::Html($title).'"
					name="sport['.$row['sport_id'].'][nazev]"'.'"
					id="sport['.$row['sport_id'].'][nazev]"'.'
					value="'.Help::Html($row['nazev']).'" />
			</td>
			<td>
				<a href="javascript:openWin(\'ciselnik.php\',\'sport['.$row['sport_id'].'][nazev]\',\'preklady\',400,300);void(0);">
					<img src="_clip/translate.gif" alt="Překladový slovník" class="img" />
				</a>
			</td>
			<td class="border-left">
				<input type="text"   maxlength="20" style="width:70px" title="Bet Radar Sport ID" name="sport['.$row['sport_id'].'][betradar_sport_id]" value="'.Help::Html($row['betradar_sport_id']).'" />
			</td>
			<td class="border-left">
				<input type="text"   maxlength="20" style="width:70px" title="Bet alias from" name="sport['.$row['sport_id'].'][bet_alias_from]" value="'.Help::Html($row['bet_alias_from']).'" />
				-
				<input type="text"   maxlength="20" style="width:70px" title="Bet alias to" name="sport['.$row['sport_id'].'][bet_alias_to]" value="'.Help::Html($row['bet_alias_to']).'" />
			</td>
			<td class="border-left">
				<input type="text"   maxlength="20" style="width:70px" title="Bet alias from new" name="sport['.$row['sport_id'].'][bet_alias_from_new]" value="'.Help::Html($row['bet_alias_from_new']).'" />
				-
				<input type="text"   maxlength="20" style="width:70px" title="Bet alias to new" name="sport['.$row['sport_id'].'][bet_alias_to_new]" value="'.Help::Html($row['bet_alias_to_new']).'" />
			</td>
			<td class="border-left">
				<input type="submit" name="edit['.$row['sport_id'].']" class="sbutton" value="Editovat" />
			</td>
			<td class="textcenter">
				<input type="checkbox" class="no"  name="sport['.$row['sport_id'].'][zvyrazneno]" '.(($row['zvyrazneni']==1)?"checked=\"checked\"":"").' />
			</td>
			<td>
				<input type="submit" name="'.($row['zobrazeno']?'skryt':'zobrazit').'['.$row['sport_id'].']" class="sbutton" value="'.($row['zobrazeno']?'Skrýt':'Zobrazit').'" />
			</td>
			<td>
				<input type="submit" name="delete['.$row['sport_id'].']" class="sbutton" onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($title).'?\')) return false;" value="Smazat" />
			</td>
			<td>'.($row['pozice'] == $min?"&nbsp;":'<input type="submit" title="Posun nahoru" name="up['.$row['sport_id'].']['.$row['pozice'].']" class="sbutton2" value="^" />').'
			</td>
			<td>'.($row['pozice'] == $poz?"&nbsp;":'<input type="submit" title="Posun dolů" name="down['.$row['sport_id'].']['.$row['pozice'].']" class="sbutton2" value="v" />').'
			</td>
		</tr>';
	$x++;

	$this->vrat .= '<tr><td>&nbsp;</td><td colspan="11"><table style=\"width:800px\">';

    $xx = 1;
    foreach($jazyky as $k=>$h){

        if($xx == 1){$this->vrat .= '<tr>';}
        $sql = "select url from seo_url where type=1 and  event_id=".$row['sport_id']." and lang_id=".$k;
        $res3 =& $this->dbGame->query($sql);
        if ($row3 =& $res3->fetchRow());else  $row3['url'] = '';

    	$this->vrat .= '<td>'.$h.':</td><td> <input type="text"  style="width:110px;" name="sport['.$row['sport_id'].'][seo]['.$k.']" value="'.Help::Html($row3['url']).'" /></td>';

    	if($xx == 4){$xx = 0;$this->vrat .= '</tr>';}

    	$xx++;
    }

    $this->vrat .= '</table></td></tr>';

   }

	/*spodek*/
	$this->vrat .= '
		<tr><td colspan="12" class=""><br /><strong>Nový Sport</strong></td></tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="text" class="mandatory" maxlength="20" id="nazev" name="nazev" value="'.(isset($_POST['nazev'])?Help::Html($_POST['nazev']):"").'" /></td>
			<td><a href="javascript:openWin(\'ciselnik.php\',\'nazev\',\'preklady\',400,300);void(0);"><img src="_clip/translate.gif" alt="Překladový slovník" class="img" /></a></td>
			<td class="textcenter"><input type="checkbox" class="no" name="zvyrazneno" '.(isset($_POST['zvyrazneno'])?"checked=\"checked\"":"").' /></td>
			<td colspan="8"><input type="submit" name="create" value="Vytvořit" /></td>
		</tr>
		</tbody></table></form>';
}

/**
 * vraci pole sportu
 * @return array
 */
	public function GetSport(){

		$sport = array();
		$res = $this->selectData();

		while ($row =& $res->fetchRow()) {
			$sport[$row['sport_id']] = array(
				'sport_id' 		=> $row['sport_id'],
				'nazev' 			=> $row['nazev'],
				'pozice' 			=> $row['pozice'],
				'zvyrazneni' 	=> $row['zvyrazneni'],
				'zobrazeno'	 	=> $row['zobrazeno']
			);
		}

		return $sport;
	}



/**
 * vyber dat z databaze
 * @return object
 */
public function selectData($where="") {
	$sql = "SELECT sport_id, nazev, pozice, zvyrazneni, zobrazeno, betradar_sport_id, bet_alias_from, bet_alias_to, 
			bet_alias_from_new, bet_alias_to_new FROM sport ".$where." ORDER BY pozice";

	$res =& $this->dbGame->query($sql);
	DbUtil::testResult($res);
	return $res;
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
