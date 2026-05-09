<?php
/**
 * @package    ciselnik
 */

/**
 * Trida pro praci s cislenikem udalosti
 * @package    Ciselniky
 */

class Udalost extends AbstractSection{


/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){

		$this->dbGame = DbUtil::connectWebDb();

    if($this->section == "136"){

			if(isset($_POST['create']))
				$this->CreateUdalost();
			#Vymazani udalosti#
			else if(isset($_POST['delete']))
				$this->DeleteUdalost(key($_POST['delete']));

			#Zobrazeni ne skryti prvku#
			if(isset($_POST['zobrazit']) || isset($_POST['skryt']))
				$this->ZobrazitSkrytUdalost((isset($_POST['zobrazit'])?key($_POST['zobrazit']):key($_POST['skryt'])));

			#Editace udalosti#
			if(isset($_POST['edit']))
				$this->EditUdalost(intval(key($_POST['edit'])));
			#Posuny udalosti#
			else if(isset($_POST['up']) || isset($_POST['down'])){
				$key = (isset($_POST['up'])?key($_POST['up']):key($_POST['down']));
				$this->UpDown($key);
			}
			#Aktualizovat vse#
			else if(isset($_POST['akt_all'])){
				foreach($_POST['udalost'] as $k=>$h){
					$this->EditUdalost($k);
				}
			}

			$this->ShowUdalost();
	}

		else{
			#Editace prav#
			if(isset($_POST['ok']) && isset($_POST['prava']) && is_array($_POST['prava'])){
				$this->EditPrava();
			}

			$this->ShowPrava();
		}
	}



/**
* Edit prav
* @return void
*/
  private function EditPrava(){

		$status = true;
		$book 	= array();
		$data 	= array();
		$b 			= new Bookmaker();
		$res 		= $b->selectData();

		while ($row =& $res->fetchRow()){
			$book[$row['bookmaker_id']] = 1;
		}

		$res = $this->selectData();

		while ($row =& $res->fetchRow()){
			$data[$row['udalost_id']] = 1;
		}

		$sth = $this->dbGame->prepare(
			'REPLACE INTO prava_udalost (bookmaker_id,udalost_id,povoleny) VALUES (?,?,?)'
		);
		DbUtil::testResult($sth);

		$this->dbGame->autoCommit(false);

/*
var_dump($data);
var_dump($book);
*/

	//	foreach($book as $k=>$h){ //$k book id
		foreach($data as $k2=>$h2){ //$k2 udalost id
//$k2 = 1688;
//				var_dump($_SESSION['bookmaker']);
//				var_dump($_POST['prava'][1][$k2]);

				if ($_POST['select_all']) {
					$pravo = 1;
				}
				else {
					if(key_exists($k2,$_POST['prava'][1]))
						if($_POST['prava'][1][$k2]=='on')
							$pravo = 1;
						else $pravo = 0;
					else $pravo = 0;
				}
//			var_dump($pravo);
//			exit;
				$res =& $this->dbGame->execute($sth,array($_SESSION['bookmaker'],$k2,$pravo));
				DbUtil::testResult($res);
		}
	//	}

		$this->vrat .= UiUtil::printMessages('Práva aktualizována');

		It6_Log::info(
			"Bookmaker event privilegies updated.",
			It6_Log::TAG_ADMIN_OPERATION
		);
		$this->dbGame->autoCommit(true);
		$this->dbGame->commit();
	}



/**
 * Edit udalost
 * @param int $ud_id id sportu
 * @return void
 */
  private function EditUdalost($ud_id){

		$errorMsg = array();

		if(
			!isset($_POST['udalost'][$ud_id]['nazev'])
			|| mb_strlen(trim($_POST['udalost'][$ud_id]['nazev'])) > 20
			|| mb_strlen(trim($_POST['udalost'][$ud_id]['nazev'])) < 1
		)
			$errorMsg[] = I18n::tr('"Title" must be between 1 ad 20 characters long and it must be set.');

		if(I18n::tr($_POST['udalost'][$ud_id]['nazev']) == false)
			$errorMsg[] = I18n::tr('The chosen title does not have a translation set. Please set translation first and then set the title.');

		if(It6_Date::toTimestamp($_POST['udalost'][$ud_id]['platne_od']) >= It6_Date::toTimestamp($_POST['udalost'][$ud_id]['platne_do']))
			$errorMsg[] = I18n::tr('The date in "Valid from" can not be greater or equal to "Valid until".');

		if(!It6_Date::checkFormat($_POST['udalost'][$ud_id]['platne_od']))
			$errorMsg[] = I18n::tr('"Valid from" is not well fromatted (dd.mm.yyyy hh:mm:ss)');

		if(!It6_Date::checkFormat($_POST['udalost'][$ud_id]['platne_do']))
			$errorMsg[] = I18n::tr('"Valid until" is not well fromatted (dd.mm.yyyy hh:mm:ss)');

		if(!isset($_POST['udalost'][$ud_id]['sport']) || $_POST['udalost'][$ud_id]['sport'] == 0)
			$errorMsg[] = I18n::tr('"Sport" must be selected');

		if(!isset($_POST['udalost'][$ud_id]['oblast']) || $_POST['udalost'][$ud_id]['oblast'] == 0)
			$errorMsg[] = I18n::tr('"Region" must be selected');

		$this->vrat .= UiUtil::printErrors($errorMsg);

		#vsechno je  vporadku muzeme editovat#
		if(empty($errorMsg)){

			$valid_from 	= It6_Date::toDb($_POST['udalost'][$ud_id]['platne_od']);
			$valid_until 	= It6_Date::toDb($_POST['udalost'][$ud_id]['platne_do']);
			$title				= $_POST['udalost'][$ud_id]['nazev'];
			$sport				= $_POST['udalost'][$ud_id]['sport'];
			$region				= $_POST['udalost'][$ud_id]['oblast'];
			$betradar_id	= $_POST['udalost'][$ud_id]['betradar_udalost_id'];
			$marked				= $_POST['udalost'][$ud_id]['zvyrazneni'];
			$separate			= $_POST['udalost'][$ud_id]['oddeleni'];
			$order				= $_POST['udalost'][$ud_id]['pozice'];


//var_dump($_POST['udalost'][$ud_id]['platne_od']);
//var_dump($ud_id);
			$this->dbGame->autocommit(false);

			$sql = "
				SELECT udalost_id FROM udalost WHERE udalost_id=".intval($ud_id)." AND pozice=".intval($order)
			;
			$res =& $this->dbGame->query($sql);
			DbUtil::testResult($res,'Unable to perform requested edit.');

			if($res->numRows()==0 && intval($order)!=0){

				$sql = "UPDATE udalost SET pozice=pozice+1 WHERE pozice>=".intval($order);
				$res =& $this->dbGame->query($sql);
				DbUtil::testResult($res,'Nepodarilo se provest dotaz: editace udalosti');

				$sql = "UPDATE udalost set pozice=".intval($order)." where udalost_id=".$ud_id;
				$res =& $this->dbGame->query($sql);
				DbUtil::testResult($res,'Unable to perform requested edit.');
			 }



			$sql = "
				UPDATE udalost
				SET
					nazev				='".Help::slash($title)."',
					zvyrazneni	=".(isset($marked)?1:0).",
					oblast_id		=".(isset($region) && ctype_digit($region)?intval($region):'0').",
					pozice			=".intval($order).",
					oddeleni		=".(isset($separate)?1:0).",
					platne_od		='".$valid_from."',
					platne_do		='".$valid_until."',
					sport_id		=".intval($sport).",
					bet_alias_from		=".intval($_POST['udalost'][$ud_id]['bet_alias_from']).",
					bet_alias_to		=".intval($_POST['udalost'][$ud_id]['bet_alias_to'])."
				WHERE udalost_id =".$ud_id
			;
			//var_dump($sql);
			$res =& $this->dbGame->query($sql);
			DbUtil::testResult($res,'Unable to perform requested edit.');

			$brid = explode(";",$_POST['udalost'][$ud_id]['betradar_udalost_id']);

			$sql = "DELETE FROM udalost_betradar WHERE udalost_id=".$ud_id;
			$res =& $this->dbGame->query($sql);
			DbUtil::testResult($res,'Unable to perform requested edit.');


			foreach($brid as $h9){
				if(intval($h9) != 0){
					$sql = "REPLACE INTO udalost_betradar VALUES (".$ud_id.",".intval($h9).")";
					$res =& $this->dbGame->query($sql);
					DbUtil::testResult($res,'Unable to perform requested edit.');
				}
			}

			foreach($_POST['udalost'][$ud_id]['seo'] as $k=>$h){
				if(mb_strlen($h) > 0){
					$sql = "REPLACE INTO seo_url VALUES (".intval($k).",3,'".Help::Slash($h)."',".$ud_id.")";
					$res =& $this->dbGame->query($sql);
					DbUtil::testResult($res,'Unable to perform requested edit.');
				}
			}


			$this->vrat .= UiUtil::printMessages(Help::Html($title).' '.I18n::tr('was succesfully edited'));

			It6_Log::info(
				"Event '%event%' was updated.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('event' => $title)
			);

			It6_GlobalCache_Invalidator::invalidateSportsbook();
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

			$this->dbGame->commit();
			$this->dbGame->autocommit(true);
		}
  }



/**
 * Posun nahoru dolu
 * @param int $ud_id id udalosti
 * return void
 */
  public function UpDown($ud_id){

		$this->dbGame->autoCommit(false);

		$sql = "SELECT sport_id,pozice FROM udalost WHERE udalost_id=".$ud_id;
		$res =& $this->dbGame->query($sql);
		DbUtil::testResult($res,'Nepodarilo se provest dotaz: vyber z tabulky udalost');

		if ($row =& $res->fetchRow()) {
			$sport_id = $row['sport_id'];
			$pozice = $row['pozice'];
		}
		else {
			$this->dbGame->rollback();
			throw new ExHandler('Chyba nebyl zjisten sport',"admin_ex_poge");
		}


		if(isset($_POST['down'])){
			$sql = "
				SELECT udalost_id,pozice
				FROM `udalost`
				WHERE pozice>".$pozice."
					AND sport_id=".$sport_id."
				ORDER BY pozice limit 1
			";
		}
		else{
			$sql = "
				SELECT udalost_id,pozice
				FROM `udalost`
				WHERE pozice<".$pozice."
					AND sport_id=".$sport_id."
				ORDER BY pozice desc limit 1
			";
		}

		$res =& $this->dbGame->query($sql);
		DbUtil::testResult($res,'Nepodarilo se provest dotaz: vyber z tabulky udalost');

		if ($row =& $res->fetchRow()) {
			$ud_id_2 = $row['udalost_id'];
			$pozice2 = $row['pozice'];
		}
		else{
			$this->dbGame->rollback();
			throw new ExHandler('Chyba nebyla zjistena pozice',"admin_ex_poge");
		}

		$sql = "UPDATE udalost SET pozice=".$pozice." WHERE udalost_id=".$ud_id_2;
		$res =& $this->dbGame->query($sql);
		DbUtil::testResult($res,'Nepodarilo se provest dotaz: vyber z tabulky udalost');

		$sql = "UPDATE udalost SET pozice=".$pozice2." WHERE udalost_id=".$ud_id;
		$res =& $this->dbGame->query($sql);
		DbUtil::testResult($res,'Nepodarilo se provest dotaz: vyber z tabulky udalost');

		$this->dbGame->autoCommit(true);
		$this->dbGame->commit();

		$this->vrat .= "
			<div class=\"okmsg\">
				Událost ".Help::Html($_POST['udalost'][$ud_id]['nazev'])." byl ůspěšně ".(isset($_POST['up'])?"posunuta nahoru":"posunuta dolů")."
			</div>
			<br />
		";

		It6_Log::info(
			"Event '%event%' was '%action%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'event'		=> $_POST['udalost'][$ud_id]['nazev'],
				'action'	=> (isset($_POST['up'])?"moved up":"moved down")
			)
		);
	}



   /**
 * Zobrazeni nebo skryti udalost
 * @param int $ud_id id sportu
 * return void
 */
  public function ZobrazitSkrytUdalost($ud_id){

		$sql = "
			UPDATE udalost SET zobrazeno=".(isset($_POST['zobrazit'])?1:0)." WHERE udalost_id=".intval($ud_id)
		;
    $res =& $this->dbGame->query($sql);
		DbUtil::testResult($res,'Nepodarilo se provest dotaz: vyber z tabulky udalost');

		if($this->dbGame->affectedRows()){
			$this->vrat .= "
				<div class=\"okmsg\">
					Událost ".Help::Html($_POST['udalost'][$ud_id]['nazev'])." byla ůspěšně ".(isset($_POST['zobrazit'])?"zobrazena":"skryta")."
				</div>
				<br />
			";

			It6_Log::info(
				"Event '%event%' was '%action%'.",
				It6_Log::TAG_ADMIN_OPERATION,
				array(
					'event'		=> $_POST['udalost'][$ud_id]['nazev'],
					'action'	=> (isset($_POST['zobrazit'])?"shown":"hidden")
				)
			);
		}
		else
			$this->vrat .= "<div class=\"errormsg\">Událost se nepodařilo editovat</div><br />";
  }



   /**
 * Vymazani udalosti
 * @param int $ud_id id udalosti
 * return void
 */
  private function DeleteUdalost($ud_id){

		$sql = "DELETE FROM udalost WHERE udalost_id=".intval($ud_id);
		$res =& $this->dbGame->query($sql);
		DbUtil::testResult($res,'Nepodarilo se provest dotaz: vymazani udalosti. Udalost je asi provazana s nejakou sazkou');

		if($this->dbGame->affectedRows()){
			$this->vrat .= "
				<div class=\"okmsg\">
					Událost byla úspěšně smazána
				</div>
				<br />
			";

			It6_Log::info(
				"Event '%event%' was deleted.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('event' => $_POST['udalost'][$ud_id]['nazev'])
			);

			It6_GlobalCache_Invalidator::invalidateSportsbook();
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
		}
		else
			$this->vrat .= "<div class=\"errormsg\">Událost se nepodařilo smazat</div><br />";
  }



 /**
 * Vytvoreni udalost
 * @return void
 */
  private function CreateUdalost(){

		$status = true;

		if(
			!isset($_POST['nazev'])
			|| mb_strlen(trim($_POST['nazev'])) > 20
			|| mb_strlen(trim($_POST['nazev'])) < 1
		){
			$this->vrat .= "
				<div class=\"errormsg\">
					<strong>Název</strong> musí být vybrán z překladů
				</div>
				<br />
			";
			$status = false;
		}

		if(isset($_POST['nazev']) && $status){
			$preklady = new Preklady($this->section);
			$res = $preklady->selectData("where index_pole='".Help::slash($_POST['nazev'])."'");

			if (!$row =& $res->fetchRow()) {
				$this->vrat .= "
					<div class=\"errormsg\">
						(<strong>Název</strong>) tento index není platným překladem
					</div>
					<br />
				";
				$status = false;
			}
		}

		if(It6_Date::toTimestamp($_POST['platnost_od']) >= It6_Date::toTimestamp($_POST['platnost_do'])){
			$this->vrat .= "
				<div class=\"errormsg\">
					Dotum od nemůže být větší nebo roven datumu do
				</div>
				<br />
			";
			$status = false;
		}

		if(!It6_Date::checkFormat($_POST['platnost_od'])){
			$this->vrat .= "
				<div class=\"errormsg\">
					(<strong>Platnost od</strong>) nemá správný formát YYYY-mm-dd HH:mm:ss
				</div>
				<br />
			";
			$status = false;
		}

		if(!It6_Date::checkFormat($_POST['platnost_do'])){
			$this->vrat .= "
				<div class=\"errormsg\">
					(<strong>Platnost do</strong>) nemá správný formát YYYY-mm-dd HH:mm:ss
				</div>
				<br />
			";
			$status = false;
		}

		if(!isset($_POST['sport']) || $_POST['sport'] == 0){
			$this->vrat .= "
				<div class=\"errormsg\">
					(<strong>Sport</strong>) musí být vybrán
				</div>
				<br />
			";
			$status = false;
		}


		#vsechno je  vporadku muzeme zapisovat#
		if($status){

			$this->dbGame->autoCommit(false);
			$sql = "SELECT max(pozice) AS m FROM udalost";
			$res =& $this->dbGame->query($sql);

			if (!$row =& $res->fetchRow())
				$poz = 1;else $poz = intval($row['m'])+1;

			$platnost_do = split("[. ]",$_POST['platnost_do'],4);
			$platnost_do = $platnost_do[2]."-".$platnost_do[1]."-".$platnost_do[0]." ".$platnost_do[3];
			$platnost_od = split("[. ]",$_POST['platnost_od'],4);
			$platnost_od = $platnost_od[2]."-".$platnost_od[1]."-".$platnost_od[0]." ".$platnost_od[3];

	    $sql = "
				INSERT INTO udalost (nazev,pozice,zvyrazneni,oddeleni,platne_od,platne_do,sport_id,zobrazeno)
				VALUES (
					'".Help::slash($_POST['nazev'])."',
					".$poz.",
					".(isset($_POST['zvyrazneno'])?1:0).",
					".(isset($_POST['oddeleno'])?1:0).",
					'".$platnost_od."',
					'".$platnost_do."',
					".intval($_POST['sport']).",
					1
				)
			";
			$res =& $this->dbGame->query($sql);
			DbUtil::testResult($res,'Nepodarilo se provest dotaz: vlozeni nove udalosti');

	    $this->vrat .= "
				<div class=\"okmsg\">
					Událost ".Help::Html($_POST['nazev'])." byla úspěšně vytvořena
				</div>
				<br />
			";

			It6_Log::info(
				"Event '%event%' was created.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('event' => $_POST['nazev'])
			);
	    $this->dbGame->autoCommit(true);
      $this->dbGame->commit();
		}
  }



 /**
 * metoda vypise vsechny zadane udalosti
 * @return void
 */
  public function ShowUdalost(){
		$tr					= new It6_Models_Translator();
		$jazykyObj			= new Jazyky;
		$jazyky				= $jazykyObj->GetJazyky();
		$sportObj 			= new Sport();
		$sports 			= $sportObj->GetSport();
		$oblastObj			= new Oblast();
		$oblasti			= $oblastObj->GetOblast();

		$sports_filter	= '';
		$sports_new			= '';

		$paramSport		 	= (isset($_GET['paramSport']) ? $_GET['paramSport'] : null);
		$paramFulltext	=	(isset($_GET['paramFulltext']) ? $_GET['paramFulltext'] : null);

		foreach ($sports as $sport){
			$sports_filter .= '
				<option
					'.(isset($paramSport) && intval($paramSport) == $sport['sport_id']?'selected="selected"':'').'
					value="'.$sport['sport_id'].'"
				>
					'.I18n::tr($sport['nazev']).'
				</option>
			';
	  }


		foreach($sports as $sport_id=>$sport){
			$sports_new .= '
				<option
					value="'.$sport_id.'"
					'.(isset($_GET['paramSport']) && $_GET['paramSport'] == $sport_id?'selected="selected"':'').'
				>
					'.I18n::tr($sport['nazev']).'
				</option>
			';
		}

		$where = '';
		if(!empty($paramFulltext))
			$where .= "
				LEFT JOIN preklady b ON b.index_pole = a.nazev
				LEFT JOIN preklady_search c
					ON b.preklad_id = c.preklad_id
					AND b.lang_id = c.lang_id
				WHERE c.text LIKE('%".$paramFulltext."%')
				AND
			";
		else
			$where .= "WHERE ";

		if(isset($paramSport) && !empty($paramSport))
			$where .= "sport_id=".intval($paramSport)." AND ";

		$where .= "1";
		$res = $this->selectData($where,"sport_id,pozice");
		$min = $max = array();
		$count 	= 0;

		while ($row =& $res->fetchRow()) {
			if (empty($data[$row['sport_id']][$row['udalost_id']]))
				++$count;

			$data[$row['sport_id']][$row['udalost_id']] = array(
				'nazev' 						=> $row['nazev'],
				'udalost_id'					=> $row['udalost_id'],
				'pozice' 						=> $row['pozice'],
				'zobrazeno' 					=> $row['zobrazeno'],
				'platne_od' 					=> $row['platne_od'],
				'platne_do' 					=> $row['platne_do'],
				'zvyrazneni' 					=> $row['zvyrazneni'],
				'oddeleni' 						=> $row['oddeleni'],
				'sport_id' 						=> $row['sport_id'],
				'oblast_id' 					=> $row['oblast_id'],
				'betradar_udalost_id' => $row['betradar_udalost_id'],
				'bet_alias_from' => $row['bet_alias_from'],
				'bet_alias_to' => $row['bet_alias_to']
			);

			$min[$row['sport_id']] = $row['pozice'];
			$max[$row['sport_id']] = $row['pozice'];
			//commented out by Martin, the two lines above should allways replace
			//the two lines bellow and are more human reaidbale
			//$min[$row['sport_id']] = (!isset($min[$row['sport_id']])?$row['pozice']:($min[$row['sport_id']]>$row['pozice']?$row['pozice']:$min[$row['sport_id']]));
			//$max[$row['sport_id']] = (!isset($max[$row['sport_id']])?$row['pozice']:($max[$row['sport_id']]<$row['pozice']?$row['pozice']:$max[$row['sport_id']]));


//TODO: rozhodnout z jake tabulky se taha betradar_udalost_is a neco tady smazat
			$sql = "
				SELECT betradar_udalost_id
				FROM udalost_betradar
				WHERE udalost_id=".$row['udalost_id']
			;
			$res8 =& $this->dbGame->query($sql);
			DbUtil::testResult($res8);


			while ($row8 =& $res8->fetchRow()) {
				if (intval($row8['betradar_udalost_id']) != 0) {
					if (!isset($data[$row['sport_id']][$row['udalost_id']]['betradar_udalost_id']))
						$data[$row['sport_id']][$row['udalost_id']]['betradar_udalost_id'] = $row8['betradar_udalost_id'];
					else
						$data[$row['sport_id']][$row['udalost_id']]['betradar_udalost_id'] .= ';'.$row8['betradar_udalost_id'];
				}
			}
		}




		$this->vrat .= '
			<div class="actions">
				<div class="row">
					<button onClick="$(\'.sport-filter\').toggle();return false;">'.I18n::tr('Filter').'</button>
				</div>
				<form method="get" action="">
					<table class="table-filter">
					<tr>
						<td>
						<input type="hidden" name="section" value="'.$this->section.'"/>
						'.I18n::tr('Sport').'
						</td>
						<td>
						<select name="paramSport">
							<option value="">'.I18n::tr('Select sport').' ...</option>
							'.$sports_filter.'
						</select>
						</td>
						<td>
						'.I18n::tr('Keyword').'
						</td>
						<td>
						<input type="text" name="paramFulltext" value="'.$paramFulltext.'"/>
						</td>
						<td>
						<input type="submit" value="'.I18n::tr('Search').'"/>
						</td>
					</tr>
					</table>
				</form>
			</div>';

		$this->ws = Zend_Registry::get('ws');
		$listerCount = $this->ws->Parameter->getAdminParameter('pagination.Event.count',$_SESSION['bookmaker']);

		$lister = new Lister('ud', $listerCount, $count, 0, 15);
		$lister->updateFromParams($_GET);
		$url = new Url( null, array(
			'section' 			=> $this->section,
			'paramSport' 		=> $paramSport,
			'paramFulltext' => $paramFulltext
		) );
		$this->vrat .= $lister->getOutput($url, 0);

//var_dump($lister->getUrlParams());
		if (!empty($data)) {
			$this->vrat .= '<form method="post" action="' . $url->getUrl(true, $lister->getUrlParams()) . '">
					<div class="actions"><input type="submit" value="Aktualizovat vše" name="akt_all" /></div>
					<table class="table-list"><thead>
						<tr>
							<th>&nbsp;</th>
							<th>'.I18n::tr('Title').'</th>
							<th>&nbsp;</th>
							<th>'.I18n::tr('Betradar Id').'</th>
							<th>'.I18n::tr('Alias from - to').'</th>
							<th>'.I18n::tr('Marked').'</th>
							<th>'.I18n::tr('Separate').'</th>
							<th>'.I18n::tr('Valid from').'</th>
							<th>'.I18n::tr('Valid until').'</th>
							<th>'.I18n::tr('Sport').'</th>
							<th colspan="3">&nbsp;</th>
							<th>'.I18n::tr('Order').'</th>
							<th colspan="2">&nbsp;</th>
						</thead>
					<tbody>';

			foreach($data as $sport_id => $sport) {

				$sport_name_show = 0;

				foreach($sport as $udalost) {

					$is = $lister->getIterationStatus();
					if (Lister::ITER_BREAK == $is)
						break;
					else if (Lister::ITER_SKIP == $is)
						continue;


					if($sport_name_show == 0){
						$this->vrat .= '
							<tr>
								<th colspan="16">'.I18n::tr($sports[$sport_id]['nazev']).'</td>
							</tr>';
					}
					$sport_name_show++;


					$sports_ud = '';
					$oblast_ud = '';

					foreach($oblasti as $oblast_id=>$oblast){
						$oblast_ud .= '
							<option
								value="'.$oblast_id.'"
								'.($udalost['oblast_id']==$oblast_id?'selected="selected"':'').'
							>
								'.I18n::tr($oblast['nazev']).'
							</option>
						';
					}

					foreach ($sports as $sport_id=>$sport){
						$sports_ud .= '
							<option
								'.($udalost['sport_id']==$sport_id?'selected="selected"':'').'
								value="'.$sport_id.'"
							>
								'.I18n::tr($sport['nazev']).'
							</option>
						';
					}
	//				var_dump($udalost['udalost_id']);
	//				var_dump($min);

				$title = $tr::translate($udalost['nazev'], Zend_Registry::get('langId'));
					$this->vrat .= '
						<tr>
							<td colspan="15" class="textleft">
								<span class="added">'.$title.'</span>
							</td>
						</tr>

						<tr '.(It6_Date::fromDbAsTimestamp($udalost['platne_do'], false) < time()?'class="warn"':'').'>
							<td>#'. $udalost['udalost_id'] .'.</td>
							<td>
								<input
									type="text"
									style="width:150px"
									title="'.$title.'"
									maxlength="20"
									id="udalost['.$udalost['udalost_id'].'][nazev]"
									name="udalost['.$udalost['udalost_id'].'][nazev]"
									value="'.Help::Html($udalost['nazev']).'"
								/>
							</td>
							<td>
								<a href="javascript:openWin(\'ciselnik.php\',\'udalost['.$udalost.'][nazev]\',\'preklady\',400,300);void(0);">
									<img src="https://'.ADMINHOST.'/_clip/translate.gif" alt="Slovník" class="img" />
								</a>
							</td>
							<td>
								<input
									type="text"
									style="width:70px"
									title="BetRadar Událost ID"
									name="udalost['.$udalost['udalost_id'].'][betradar_udalost_id]"
									value="'.Help::Html($udalost['betradar_udalost_id']).'"
								/>
							</td>
							<td>
								<input type="text" maxlength="20" style="width:70px" title="Bet alias from" name="udalost['.$udalost['udalost_id'].'][bet_alias_from]" value="'.Help::Html($udalost['bet_alias_from']).'" />
								-
								<input type="text" maxlength="20" style="width:70px" title="Bet alias to" name="udalost['.$udalost['udalost_id'].'][bet_alias_to]" value="'.Help::Html($udalost['bet_alias_to']).'" />
							</td>
							<td class="textcenter">
								<input
									type="checkbox"
									class="no"
									name="udalost['.$udalost['udalost_id'].'][zvyrazneni]"
									'.($udalost['zvyrazneni']?'checked="checked"':'').'
								/>
							</td>
							<td class="textcenter">
								<input
									type="checkbox"
									class="no"
									name="udalost['.$udalost['udalost_id'].'][oddeleni]"
									'.($udalost['oddeleni']?'checked="checked"':'').'
								/>
							</td>
							<td>
								<input
									type="text"
									class="mandatory sinput3 dateTime"
									id="udalost['.$udalost['udalost_id'].'][platne_od]"
									name="udalost['.$udalost['udalost_id'].'][platne_od]"
									value="'.It6_Date::fromDb($udalost['platne_od']).'"
								/>
								<img src="images/ico/calendar.gif" class="calendar-icon" />
							</td>
							<td>
								<input
									type="text"
									class="mandatory sinput3 dateTime"
									id="udalost['.$udalost['udalost_id'].'][platne_do]"
									name="udalost['.$udalost['udalost_id'].'][platne_do]"
									value="'.It6_Date::fromDb($udalost['platne_do']).'"
								/>
								<img src="images/ico/calendar.gif" class="calendar-icon" />
							</td>
							<td class="mandatory">
								<select style="font-size:0.8em;" name="udalost['.$udalost['udalost_id'].'][sport]">
									<option value="0">'.I18n::tr('Select sport').'</option>
									'.$sports_ud.'
								</select>
							</td>
							<td>
								<input
									type="submit"
									name="edit['.$udalost['udalost_id'].']"
									value="'.I18n::tr('edit').'" />
							</td>
							<td>
								<input
									type="submit"
									name="'.($udalost['zobrazeno']?'skryt':'zobrazit').'['.$udalost['udalost_id'].']"
									value="'.($udalost['zobrazeno']?I18n::tr('hide'):I18n::tr('show')).'"
								/>
							</td>
							<td>
								<input
									type="submit"
									name="delete['.$udalost['udalost_id'].']"
									onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($title).'?\')) return false;"
									value="'.I18n::tr('delete').'"
								/>
							</td>
							<td>
								<input
									type="text"
									style="width:28px;font-size:9px;"
									name="udalost['.$udalost['udalost_id'].'][pozice]"
									value="'.intval($udalost['pozice']).'"
								/>
								<input
									type="hidden"
									name="udalost['.$udalost['udalost_id'].'][old_pozice]"
									value="'.intval($udalost['pozice']).'"
								/>
								<input
									type="hidden"
									name="udalost['.$udalost['udalost_id'].'][sport]"
									value="'.intval($udalost['sport_id']).'"
								/>
							</td>
							<td>
								'.($udalost['pozice'] == $min[$sport_id]?'&nbsp;':'<input type="submit" title="Posun nahoru" name="up['.$udalost['udalost_id'].']" style="width:20px" class="sbutton2" value="^" />').'
							</td>
							<td>
								'.($udalost['pozice'] == $max[$sport_id]?'&nbsp;':'<input type="submit" title="Posun dolů" name="down['.$udalost['udalost_id'].']" style="width:20px" class="sbutton2" value="v" />').'
							</td>
						</tr>

						<tr>
							<td>&nbsp;</td>
							<td colspan="7">
								<a href="javascript:$(\'#seo_'.$udalost['udalost_id'].'\').show();void(0);">SEO</a>
							</td>
							<td colspan="7">
								<select name="udalost['.$udalost['udalost_id'].'][oblast]">
									<option>--'.$tr::translate('select_region', Zend_Registry::get('langId')).'--</option>
									'.$oblast_ud.'
								</select>
							</td>
						</tr>

						<tr style="display:none;" id="seo_'.$udalost['udalost_id'].'">
							<td>&nbsp;</td>
							<td colspan="13">
								<table style=\"width:780px\">
					';

					$xx = 1;
					foreach($jazyky as $jazyk_id=>$jazyk){
						if($xx == 1)
							$this->vrat .= '<tr>';

						$sql = "
							SELECT url
							FROM seo_url
							WHERE type='3'
								AND event_id='".$udalost['udalost_id']."'
								AND lang_id='".$jazyk_id."'
						";
						$res =& $this->dbGame->query($sql);
						DbUtil::testResult($res);
						if( !($row =& $res->fetchRow()) )
							$row['url'] = '';

						$this->vrat .= '
							<td>'.$jazyk['alt_text'].':</td>
							<td>
								<input
									type="text"
									style="width:110px;"
									name="udalost['.$udalost['udalost_id'].'][seo]['.$jazyk_id.']"
									value="'.Help::Html($row['url']).'"
								/>
							</td>
						';

						if($xx == 4) {
							$xx = 0;
							$this->vrat .= '</tr>';
						}
						$xx++;
					}
					$this->vrat .= '</table></td></tr>';

				}
			}
		} else $this->vrat .= UiUtil::printWarnings('no_data');

// second Dave Lister
		$this->vrat .= '</table>';
		$this->vrat .=  $lister->getOutput($url, 1);

		/*spodek*/
		$this->vrat .= '
					<table class="table-list">
						<tr>
							<td colspan="15">
								&nbsp;
								'.(isset($paramSportOpt)?'<input type="hidden" name="sport_opt" value="'.intval($paramSportOpt).'" />':"").'
							</td>
						</tr>

						<tr>
							<td colspan="15" class="">
								<br />
								<strong>Nová událost</strong>
							</td>
						</tr>

						<tr>
							<td>&nbsp;</td>
							<td>
								<input
									type="text"
									style="width:70px"
									class="mandatory sinput3"
									maxlength="20"
									name="nazev"
									id="nazev"
									value="'.(isset($_POST['nazev'])?Help::Html($_POST['nazev']):"").'"
								/>
							</td>
							<td>
								<a href="javascript:openWin(\'ciselnik.php\',\'nazev\',\'preklady\',400,300);void(0);">
									<img src="https://'.ADMINHOST.'/_clip/translate.gif" alt="Překladový slovník" class="img" />
								</a>
							</td>
							<td>&nbsp;</td>
							<td class="textcenter">
								<input
									type="checkbox"
									class="no"
									name="zvyrazneno"
									'.(isset($_POST['zvyrazneno'])?'checked="checked"':'').'
								/>
							</td>
							<td class="textcenter">
								<input
									type="checkbox"
									class="no"
									name="oddeleno"
									'.(isset($_POST['oddeleno'])?'checked="checked"':'').'
								/>
							</td>
							<td>
								<input
									type="text"
									class="mandatory sinput3 dateTime"
									name="platnost_od"
									id="platnost_od"
									value="'.(isset($_POST['platnost_od'])?Help::Html($_POST['platnost_od']):"").'"
								/>
								<img src="images/ico/calendar.gif" class="calendar-icon" />
							</td>
							<td>
								<input
									type="text"
									class="mandatory sinput3 dateTime"
									name="platnost_do"
									id="platnost_do"
									value="'.(isset($_POST['platnost_do'])?Help::Html($_POST['platnost_do']):"").'"
								/>
								<img src="images/ico/calendar.gif" class="calendar-icon" />
							</td>
							<td class="mandatory">
								<select name="sport" style="font-size:0.8em;">
									<option value="0">'.I18n::tr('Select sport').' ...</option>
									'.$sports_new.'
								</select>
							</td>
							<td colspan="6">
								<input type="submit" name="create" value="'.I18n::tr('Create').'" />
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		';

  }



/**
 * metoda vypise vsechny zadane udalosti a prava bookmakeru
 * @return void
 */
  public function ShowPrava(){

		$preklad = new Preklady();

		$sp = new Sport();
		$sp_ar = $sp->GetSport();

		$res = $this->selectData("","sport_id,pozice");
		//$data = $prava = $book = array();
		$data = $min = $max = $sort = array();
		while ($row =& $res->fetchRow()){
			$r = $preklad->selectData("WHERE lang_id=1 AND index_pole='".Help::Slash($row['nazev'])."'");

			if (
				(!$row2 =& $r->fetchRow())
				|| (isset($row['text']) && mb_strlen($row['text']) < 0)
			);
			else
				$row['nazev'] = $row2['text'];

			$data[$row['sport_id']][$row['udalost_id']] = $row['nazev'];
			//$sort[$row['sport_id']][] = $row['udalost_id'];
			$min[$row['sport_id']] = (!isset($min[$row['sport_id']])?$row['pozice']:($min[$row['sport_id']]>$row['pozice']?$row['pozice']:$min[$row['sport_id']]));
			$max[$row['sport_id']] = (!isset($max[$row['sport_id']])?$row['pozice']:($max[$row['sport_id']]<$row['pozice']?$row['pozice']:$max[$row['sport_id']]));
		}

		$sql = "select * from prava_bookmaker where bookmaker_id=".$_SESSION['bookmaker'];
		$res =& $this->dbGame->query($sql);
		DbUtil::testResult($res, 'Nepodarilo se provest dotaz: editace udalosti');

		while ($row =& $res->fetchRow()){
			$prava[1][$row['udalost_id']] = $row['povoleny'];
		}


		$b = new Bookmaker();
		$res = $b->selectData("where super=0");

		while ($row =& $res->fetchRow()){
			$book[$row['bookmaker_id']] = $row['nick'];
		}


		/*hlavicka*/
		$this->vrat .= '
			<form method="post" action="?section='.$this->section.'">
				<p><em></em></p>
				<table class="prava">
					<tr>
						<td>&nbsp;</td>
		';

		reset($book);
		foreach($book as $k=>$h){
			$this->vrat .= '
				<th>
					<a href="javascript:C('.$k.');void(0);">'.$h.'</a>

				</th>
			';
		}
		$this->vrat .= '
			</tr>
			<script>
				var ch_prava = new Array();
				function C(id){
					ch_prava[id] = (ch_prava[id]?false:true);
					book = document.forms[0];

					for(var x=0;x<book.length;x++){
						if(book.elements[x].name.indexOf(\'prava[\'+id+\']\')!=-1){
							book.elements[x].checked = (ch_prava[id]==false?false:true);
						}
					}
				}
			</script>
		';

		foreach($data as $k=>$h){ //$k sport_id
			$r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($sp_ar[$k]['nazev'])."'");

			if (!$row =& $r->fetchRow() || mb_strlen($row['text']) < 0)
				$row['text'] = "Překlad nenalezen";

			$sp_ar[$k]['preklad'] = $row['text'];

			$this->vrat .= '
				<tr>
					<td colspan="'.(count($book)+1).'">
						<h3>'.Help::Html($sp_ar[$k]['preklad']).'</h3>
					</td>
				</tr>
			';

			foreach($h as $k2=>$h2){ //$k2 udalost_id
				$this->vrat .= '<tr><td>'.$h2.'</td>';
				reset($book);
				//foreach($book as $book_id=>$book_h){
					$book_id=1;
					$this->vrat .= '
						<td class="textcenter">
							'.(!isset($prava[$book_id][$k2]) || $prava[$book_id][$k2] == 0?'<input type="checkbox" class="no" name="prava['.$book_id.']['.$k2.']" />':'<input type="checkbox" name="prava['.$book_id.']['.$k2.']" class="no" checked="checked" />').'
						</td>
					';
				//}

				$this->vrat .= '</tr>';
			}
		}

		/*spodek*/
		$this->vrat .= '<tr><td colspan="'.(count($book)+1).'">
							<input type="submit" name="ok" value="Potvrdit" class="sinput" />
							<input name="select_all" type="checkbox" value="1" /> Select all
						</td></tr>';
		$this->vrat .= '</tbody></table></form>';


  }

/**
 * vyber dat z databaze
 * @return object
 */
  public function selectData($where="",$order="pozice"){

		$sql = "
			SELECT
				a.sport_id,
				a.udalost_id,
				a.oblast_id,
				a.nazev,
				a.pozice,
				a.zvyrazneni,
				a.platne_od,
				a.platne_do,
				a.oddeleni,
				a.zobrazeno,
				a.betradar_udalost_id,
				a.bet_alias_from,
				a.bet_alias_to
			FROM udalost a
			".$where."
			ORDER BY ".$order
		;

		$res =& $this->dbGame->query($sql);
		DbUtil::testResult($res);

		return $res;
  }



}
