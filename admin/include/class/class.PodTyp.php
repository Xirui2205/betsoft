<?php
/**
 * @package    ciselnik
 */

/**
 * Trida pro praci s cislenikem podtypu
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class POdTyp extends Template{

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
	 * Pokud neni identifikator spojeni predan vytvori se nove spojeni
	 * @param int $section id aktualni sekce
	 * @param PEAR::DB $dbGame objekt spojeni s databazi
	 */
	public function __construct($section=0) {
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
			$this->CreatePodTyp();
		}

		#Vymazani podtypu#
		else if(isset($_POST['delete'])){
			$this->DeletePodtyp(key($_POST['delete']));
		}

		#Vytvoreni kopie#
		else if(isset($_POST['duplicate'])){
			$this->DuplicatePodtyp(key($_POST['duplicate']));
		}

		#Vymazani sloupce#
		else if(isset($_GET['deletesloupec']) && isset($_GET['podtyp_id']) && isset($_GET['sloupec'])){
			$this->DeleteSloupec(intval($_GET['podtyp_id']),intval($_GET['sloupec']));
		}

		#Editace podtypu#
		if(isset($_POST['edit'])){
			$this->EditPodtyp(intval(key($_POST['edit'])));
	   }

		$this->ShowPodTyp();
		$this->dbGame->disconnect();
	}


	/**
	 * Vytvoreni kopie
	 * @param int $podtyp_id id typu
	 * return void
	 */
	private function DuplicatePodtyp($podtyp_id){
		$this->dbGame->autoCommit(false);
		$sql = "SELECT * FROM podtyp WHERE podtyp_id=".$podtyp_id;
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) {
			$this->dbGame->rollback();
			$this->dbGame->autoCommit(true);
			throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu',"admin_ex_db");
		}

		if ($row =& $res->fetchRow()) {
			$sql = 
"INSERT INTO podtyp
  (text,interni_nazev,radek_sloupec,sloupec_pocet_max)
VALUES (
  '".Help::slash($_row['text'])."',
  '".Help::slash($row['interni_nazev'])."_copy',
  ".$row['radek_sloupec'].",
  ".$row['sloupec_pocet_max'].")";
			
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)) {
				$this->dbGame->rollback();
				$this->dbGame->autoCommit(true);
				throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu',"admin_ex_db");
			}
		}
		else {
			$this->dbGame->rollback();
			$this->dbGame->autoCommit(true);
			throw new ExHandler('Nepodarilo se provest dotaz: zjisteni podtypu',"admin_ex_page");
		}

		$sql = "SELECT MAX(podtyp_id) AS maxi FROM podtyp";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) {
			$this->dbGame->rollback();
			$this->dbGame->autoCommit(true);
			throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu',"admin_ex_db");
		}
		
		if ($row =& $res->fetchRow()) {
			$id = $row['maxi'];
		}
		else {
			$this->dbGame->rollback();
			$this->dbGame->autoCommit(true);
			throw new ExHandler('Nepodarilo se provest dotaz: zjisteni max id podtypu',"admin_ex_page");
		}

		$sql = "SELECT * FROM podtyp_sloupce WHERE podtyp_id=".$podtyp_id;
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) {
			$this->dbGame->rollback();
			$this->dbGame->autoCommit(true);
			throw new ExHandler('Nepodarilo se provest dotaz: vyber podtypu sloupce',"admin_ex_db");
		}

		while ($row =& $res->fetchRow()) {
			$sql =
"INSERT INTO podtyp_sloupce (podtyp_id,nazev,poradi)
VALUES (
  ".$id.",
  '".Help::slash($row['nazev'])."',
  ".$row['poradi'].")";
			
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)) {
				$this->dbGame->rollback();
				$this->dbGame->autoCommit(true);
				throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu_sloupce',"admin_ex_db");
			}
		}

		$sql = "SELECT * FROM typ_podtyp WHERE podtyp_id=".$podtyp_id;
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) {
			$this->dbGame->rollback();
			$this->dbGame->autoCommit(true);
			throw new ExHandler('Nepodarilo se provest dotaz: vyber typ_podtyp',"admin_ex_db");
		}

		while ($row =& $res->fetchRow()) {
			$sql =
"INSERT INTO typ_podtyp (podtyp_id,typ_id,sport_id,live_bet)
VALUES (
  ".$id.",
  ".$row['typ_id'].",
  ".$row['sport_id'].",
  ".$row['live_bet'].")";
			
			$res2 =& $this->dbGame->query($sql);
			if(DB::isError($res2)) {
				$this->dbGame->rollback();
				$this->dbGame->autoCommit(true);
				throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho typ_podtyp',"admin_ex_db");
			}
		}

		$this->vrat .= '<div class="okmsg">Duplikace provedena</div><br />';
 		 $this->vrat .= ' 
		 <div id="dialog-message" title="compbet.com">
		 <p>Duplikace provedena</p>
		 </div>';

		It6_Log::info(
			"Duplicate odds type (odds type: '%type%')",
			It6_Log::TAG_ADMIN_OPERATION,
			array('type'=> intval($podtyp_id))
		);

		It6_GlobalCache_Invalidator::invalidateSportsbook();
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

		$this->dbGame->autoCommit(true);
		$this->dbGame->commit();

	}

	/**
	 * Vymazani sloupce u podtypu
	 * @param int $podtyp_id id podtypu
	 * @param int $sloupec id sloupce
	 * @return void
	 */
	private function DeleteSloupec($podtyp_id,$sloupec) {
		$status = true;
		$sql = "SELECT * FROM podtyp_sloupce WHERE podtyp_id=".$podtyp_id ;
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber podtyp_sloupce',"admin_ex_db");

		if($res->numRows() < 3 && $res->numRows() > 0) {
			$this->vrat .= "<div class=\"errormsg\"> Podtyp musí mít minimálně 2 sloupce</div><br />";
            //$_SESSION['ERRMSG'][] = 'Podtyp musí mít minimálně 2 sloupce';
			$status = false;
		}
		if($res->numRows() == 0) {
			$this->vrat .= "<div class=\"errormsg\">Neexistující podtyp</div><br />";
            //$_SESSION['ERRMSG'][] = 'Neexistující podtyp';
			$status = false;
		}

		if($status) {
			$sql = "DELETE FROM podtyp_sloupce WHERE poradi=".$sloupec." AND podtyp_id=".$podtyp_id ;
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber podtyp_sloupce',"admin_ex_db");

			if($this->dbGame->affectedRows()) {
				$this->vrat .= "<div class=\"okmsg\">Záznám byl vymazán</div><br />";
		
		 		 $this->vrat .= ' 
				 <div id="dialog-message" title="compbet.com">
				 <p>Záznám byl vymazán</p>
				 </div>';

				It6_Log::info(
					"Odd deleted (odds type: '%type%', odd: '%odd%')",
					It6_Log::TAG_ADMIN_OPERATION,
					array(
						'type'	=> $podtyp_id,
						'odd'	=> $sloupec
					)
				);
		
				It6_GlobalCache_Invalidator::invalidateSportsbook();
				It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
			}
			else {
				$this->vrat .= "<div class=\"errormsg\">Záznam se nepodařilo smazat</div><br />";
		 		 $this->vrat .= ' 
				 <div id="dialog-message" title="compbet.com">
				 <p>Záznam se nepodařilo smazat</p>
				 </div>';
			}
		}
	}

	/**
	 * Edit podtypu
	 * @param int $podtyp_id id podtypu
	 * @return void
	 */
	private function EditPodtyp($podtyp_id){
		$status = true;

		if(!isset($_POST['podtyp'][$podtyp_id]['interninazev']) || mb_strlen(trim($_POST['podtyp'][$podtyp_id]['interninazev'])) < 1) {
			$this->vrat .= "<div class=\"errormsg\"> <strong>Interní popis</strong> musí být uveden</div><br />";
            //$_SESSION['ERRMSG'][] = 'Interní popis musí být uveden';
			$status = false;
		}
		else if (mb_strlen(trim($_POST['podtyp'][$podtyp_id]['interninazev'])) > 50) {
			$this->vrat .= "<div class=\"errormsg\"> <strong>Interní popis</strong> smí mít max. 50 znaků</div><br />";
            //$_SESSION['ERRMSG'][] = 'Interní popis smí mít max. 50 znaků';
			$status = false;
		}
		if(!isset($_POST['podtyp'][$podtyp_id]['nazev']) || mb_strlen(trim($_POST['podtyp'][$podtyp_id]['nazev'])) > 20) {
			$this->vrat .= "<div class=\"errormsg\"> <strong>Text na stránkách</strong> musí mím maximálně 20 znaků</div><br />";
            //$_SESSION['ERRMSG'][] = '<strong>Text na stránkách</strong> musí mím maximálně 20 znaků';
			$status = false;
		}
		if(!isset($_POST['podtyp'][$podtyp_id]['sloupec']) || !is_array($_POST['podtyp'][$podtyp_id]['sloupec']) || $this->CheckNonValueArray($_POST['podtyp'][$podtyp_id]['sloupec'])){
			$this->vrat .= "<div class=\"errormsg\"> <strong>Sloupce</strong> musí být zvolené minimálně dva a každý musí mít minimálně 1 znak</div><br />";
            //$_SESSION['ERRMSG'][] = '<strong>Sloupce</strong> musí být zvolené minimálně dva a každý musí mít minimálně 1 znak';
			$status = false;
		}
		if(isset($_POST['podtyp'][$podtyp_id]['nazev']) && mb_strlen(trim($_POST['podtyp'][$podtyp_id]['nazev'])) > 0) {
			$preklady = new Preklady($this->section);
			$res = $preklady->selectData("where index_pole='".Help::slash($_POST['podtyp'][$podtyp_id]['nazev'])."'");
			if (!$row =& $res->fetchRow()) {
				$this->vrat .= "<div class=\"errormsg\"> (<strong>Text na stránkách</strong>) tento index není platným překladem</div><br />";
				//$_SESSION['ERRMSG'][] = '(<strong>Text na stránkách</strong>) tento index není platným překladem';
				$status = false;
			}
		}

		#vsechno je  vporadku muzeme zapisovat#
		if($status) {
			$this->dbGame->autoCommit(false);
			$sql =
"UPDATE podtyp
SET
  text='".Help::slash($_POST['podtyp'][$podtyp_id]['nazev'])."',
  interni_nazev='".Help::slash($_POST['podtyp'][$podtyp_id]['interninazev'])."',
  radek_sloupec=".(isset($_POST['podtyp'][$podtyp_id]['radek']) && isset($_POST['podtyp'][$podtyp_id]['radek_num']) && is_numeric($_POST['podtyp'][$podtyp_id]['radek_num'])?1:0).",
  sloupec_pocet_max=".(isset($_POST['podtyp'][$podtyp_id]['radek']) && isset($_POST['podtyp'][$podtyp_id]['radek_num']) && is_numeric($_POST['podtyp'][$podtyp_id]['radek_num'])?intval($_POST['podtyp'][$podtyp_id]['radek_num']):0)."
WHERE podtyp_id=".$podtyp_id;
        
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace podtypu',"admin_ex_db");

			$sql = "DELETE FROM typ_podtyp WHERE podtyp_id=".$podtyp_id ;
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vymazani typu_podtypu',"admin_ex_db");

			foreach($_POST['podtyp'][$podtyp_id]['typ'] as $k=>$h ) {  //sport id
				foreach($h as $k2=>$h2){  //typ id
					$live = (isset($_POST['podtyp'][$podtyp_id]['live'][$k][$k2])?1:0);
					$sql = "INSERT INTO typ_podtyp (typ_id,sport_id,podtyp_id,live_bet) VALUES (".intval($k2).",".intval($k).",".intval($podtyp_id).",".$live.")";
					$res =& $this->dbGame->query($sql);
					if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu',"admin_ex_db");
				}
			}

			foreach($_POST['podtyp'][$podtyp_id]['sloupec'] as $k=>$h) {  //
				$sql = "UPDATE podtyp_sloupce SET nazev='".Help::Slash($h)."' WHERE poradi=".intval($k)." AND podtyp_id=".$podtyp_id;
				$res =& $this->dbGame->query($sql);
				if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu',"admin_ex_db");
			}

			$this->vrat .= "<div class=\"okmsg\">PodTyp (".Help::Html($_POST['podtyp'][$podtyp_id]['interninazev']).") byl úspěšně editován</div><br />";

			$this->vrat .= ' 
			<div id="dialog-message" title="compbet.com">
			<p>PodTyp ('.Help::Html($_POST['podtyp'][$podtyp_id]['interninazev']).') byl úspěšně editován
			</p>
			</div>
			';

			It6_Log::info(
				"Odds type '%type%' was updated.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('type' => intval($podtyp_id))
			);

			It6_GlobalCache_Invalidator::invalidateSportsbook();
			It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

			$this->dbGame->autoCommit(true);
			$this->dbGame->commit();
		}
	}


   /**
 * Vymazani podtyp
 * @param int $podtyp_id id typu
 * return void
 */
  private function DeletePodtyp($podtyp_id){

     $sql = "delete from podtyp where podtyp_id=".intval($podtyp_id);
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vymazani Podtyp. Typ je asi navázán na sázky',"admin_ex_db");

	 if($this->dbGame->affectedRows()){

	   $this->vrat .= "<div class=\"okmsg\">Podtyp byl úspěšně smazán</div><br />";

		$this->vrat .= ' 
		<div id="dialog-message" title="compbet.com">
		<p>Podtyp byl úspěšně smazán</p>
		</div>';

	   It6_Log::info(
			"Odds type '%type%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('type' => $_POST['podtyp'][$podtyp_id]['interninazev'])
		);

		It6_GlobalCache_Invalidator::invalidateSportsbook();
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

	 }else
	     $this->vrat .= "<div class=\"errormsg\">Podtyp se nepodařilo smazat</div><br />";
 		 $this->vrat .= ' 
		 <div id="dialog-message" title="compbet.com">
		 <p>Podtyp se nepodařilo smazat</p>
		 </div>';

  }

   /**
 * Vytvoreni podtypu
 *
 * Vraci true pokud je  v poli polozka ktera je prazdna
 *
 * @param array $ar jednorozmerne pole
 * @return bool
 */
  private function CheckNonValueArray($ar){


    foreach($ar as $h){

	  if(mb_strlen($h) < 1) return true;

	}

    return false;

  }

	/**
	 * Vytvoreni podtypu
	 * @return void
	 */
	private function CreatePodTyp(){
		$status = true;
        //$_SESSION['ERRMSG'] = array();
		if(
			!isset($_POST['interninazev'])
			|| mb_strlen(trim($_POST['interninazev'])) > 20
			|| mb_strlen(trim($_POST['interninazev'])) < 1
		) {
			$this->vrat .= '<div class="errormsg"> <strong>Interní popis</strong> musí být uveden</div><br />';
            //$_SESSION['ERRMSG'][] = 'Interní popis musí být uveden';
			$status = false;
		}
		if(!isset($_POST['nazev']) || mb_strlen(trim($_POST['nazev'])) > 20) {
			$this->vrat .=
				'<div class="errormsg">
					<strong>Text na stránkách</strong> musí mím maximálně 20 znaků
				</div>
				<br />';
            //$_SESSION['ERRMSG'][] = 'Text na stránkách musí mím maximálně 20 znaků';
			$status = false;
		}
		if(
			!isset($_POST['newpodtyp']['sloupec'])
			|| !is_array($_POST['newpodtyp']['sloupec'])
			|| $this->CheckNonValueArray($_POST['newpodtyp']['sloupec'])
		) {
			$this->vrat .=
				'<div class="errormsg">
					<strong>Sloupce</strong> musí být zvolené minimálně dva a každý musí mít minimálně 1 znak
				</div>
  				<br />';
            //$_SESSION['ERRMSG'][] = 'Sloupce musí být zvolené minimálně dva a každý musí mít minimálně 1 znak';
			$status = false;
		}
		if(isset($_POST['nazev']) && mb_strlen(trim($_POST['nazev'])) > 0) {
			$preklady = new Preklady($this->section);
			$res = $preklady->selectData("where index_pole='".Help::slash($_POST['nazev'])."'");
			if (!$row =& $res->fetchRow()) {
				$this->vrat .=
					'<div class="errormsg">
						(<strong>Text na stránkách</strong>) tento index není platným překladem
					</div>
					<br />';
                //$_SESSION['ERRMSG'][] = '(Text na stránkách) tento index není platným překladem';
				$status = false;
			}
		}

		#vsechno je  vporadku muzeme zapisovat#
		if($status) {
			$this->dbGame->autoCommit(false);
			$sql =
"INSERT INTO podtyp (text,interni_nazev,radek_sloupec,sloupec_pocet_max)
VALUES (
  '".Help::slash($_POST['nazev'])."',
  '".Help::slash($_POST['interninazev'])."',
  ".(isset($_POST['radek']) && isset($_POST['radek_num']) && is_numeric($_POST['radek_num']) ? 1 : 0).",
  ".(isset($_POST['radek']) && isset($_POST['radek_num']) && is_numeric($_POST['radek_num']) ? intval($_POST['radek_num']) : 0).")";
			
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) {
				$this->dbGame->rollback();
				$this->dbGame->autoCommit(true);
				throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu',"admin_ex_db");
			}

			$sql = "SELECT MAX(podtyp_id) AS m FROM podtyp";
			$res =& $this->dbGame->query($sql);
			if (!$row =& $res->fetchRow()) {
				$this->dbGame->rollback();
				throw new ExHandler('Nepodarilo se ziskat identifikator typ',"admin_ex_db");
			}
			else {
				$podtyp_id = $row['m'];
			}

			if(isset($_POST['newpodtyp']['typ'])) {
				foreach($_POST['newpodtyp']['typ'] as $k=>$h) {  //sport id
					foreach($h as $k2=>$h2){  //typ id
						$live = (isset($_POST['newpodtyp']['live'][$k][$k2])?1:0);
						$sql =
"INSERT INTO typ_podtyp (typ_id,sport_id,podtyp_id)
VALUES (".intval($k2).",".intval($k).",".intval($podtyp_id).")";
						
						$res =& $this->dbGame->query($sql);
						if(DB::isError($res)) {
							$this->dbGame->rollback();
							$this->dbGame->autoCommit(true);
							throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu',"admin_ex_db");
						}
					}
				}
			}

			foreach($_POST['newpodtyp']['sloupec'] as $k=>$h) {
				$sql =
"INSERT INTO podtyp_sloupce (podtyp_id,nazev,poradi)
VALUES (".intval($podtyp_id).",'".Help::Slash($h)."',".intval($k).") ";
				
				$res =& $this->dbGame->query($sql);
				if(DB::isError($res)) {
					$this->dbGame->rollback();
					$this->dbGame->autoCommit(true);
					throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho podtypu',"admin_ex_db");
				}
			}

			$this->vrat .= "<div class=\"okmsg\">PodTyp (".Help::Html($_POST['interninazev']).") byl úspěšně vytvořen</div><br />";
 		    
 		    $this->vrat .= ' 
		    <div id="dialog-message" title="compbet.com">
		    <p>Podtyp ('.Help::Html($_POST['interninazev']).') byl úspěšně vytvořen</p>
		    </div>';

			It6_Log::info(
				"New odds type '%type%' was created.",
				It6_Log::TAG_ADMIN_OPERATION,
				array('type' => intval($podtyp_id))
			);
			$this->dbGame->autoCommit(true);
			$this->dbGame->commit();

   }

  }

	/**
	 * metoda vypise vsechny zadane podtypy
	 * @return void
	 */
	public function ShowPodTyp(){
		$preklad = new Preklady($this->section);
		$sp = new Sport();
		$sp_ar = $sp->GetSport();

		foreach($sp_ar as $k=>$h) {
			$r = $preklad->selectData("WHERE lang_id=1 and index_pole='".Help::Slash($h['nazev'])."'");
			if (!$row =& $r->fetchRow() || mb_strlen($row['text']) < 0) {
				$row['text'] = "Překlad nenalezen";
			}
			$sp_ar[$k]['preklad'] = $row['text'];
		}

		if(!isset($_REQUEST['podtyp_id'])) {
			$podtyp_id = 23;
		}
		else {
			$podtyp_id = $_REQUEST['podtyp_id'];
		}

		$podtyp = array();
		$sql = 
"SELECT a.podtyp_id,a.radek_sloupec,a.sloupec_pocet_max,a.text,a.interni_nazev,b.nazev,b.poradi
FROM podtyp a
INNER JOIN podtyp_sloupce b ON a.podtyp_id=b.podtyp_id";
		
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

		while ($row =& $res->fetchRow()){
			$podtyp[$row['podtyp_id']]['sloupce'][$row['poradi']] = $row['nazev'];
			$podtyp[$row['podtyp_id']]['text'] = $row['text'];
			$podtyp[$row['podtyp_id']]['interni_nazev'] = $row['interni_nazev'];
			$podtyp[$row['podtyp_id']]['radek'] = $row['radek_sloupec'];
			$podtyp[$row['podtyp_id']]['radek_max'] = $row['sloupec_pocet_max'];
		}

		$sql = "SELECT a.podtyp_id,b.typ_id,b.sport_id FROM podtyp a INNER JOIN typ_podtyp b ON a.podtyp_id=b.podtyp_id";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

		while ($row =& $res->fetchRow()){
			$podtyp[$row['podtyp_id']]['typ_podtyp'][$row['sport_id']][$row['typ_id']]['status'] = 1;
		}

		foreach($podtyp as $pid=>$h) {
			$this->vrat .=
				'<div style="width:130px;height:35px;font-size:11px;text-align:center;float:left;border-bottom:1px solid black;border-right:1px solid black;">
					<a href="/?superb=1&section=139&podtyp_id='. $pid .'">'.Help::Html($h['interni_nazev']).'</a>
				</div> ';
		}

		/*hlavicka*/

        /* Message dialog
			<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
			<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
			<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
        	 <script>
			$(function() {
			$( "#dialog-message" ).dialog({
			modal: true,
			buttons: {
			Ok: function() {
			$( this ).dialog( "close" );
			}
			}
			});
			});
			</script>
        */

		$this->vrat .=
			'
			<form style="clear:both;overflow:hidden;" method="post" action="?superb=1&section='.$this->section.'&podtyp_id='. $podtyp_id .'">
			     <br />
				 <table>
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>Popis</th>
							<th>Text na webu</th>
							<th>&nbsp;</th>
							<th>Klasicky/Do řádků</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
						</thead>
					<tbody>';

		$sql =
"SELECT t.typ_id, t.nazev, u.sport_id
FROM typ t
INNER JOIN typ_udalost tu ON t.typ_id=tu.typ_id
INNER JOIN udalost u ON u.udalost_id=tu.udalost_id
WHERE tu.is_binded = 1";
		
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

		$typ_sport = array();
		while ($row =& $res->fetchRow()){
			$res2 = $preklad->selectData("where lang_id=1 and index_pole='".Help::slash($row['nazev'])."'");
			if ($row2 =& $res2->fetchRow()) {
				$title = $row2['text'];
			}
			else {
				$title = "Překlad nenalezen";
			}
			$typ_sport[$row['sport_id']][$row['typ_id']] = $title;
		}

		$x = 1;
		foreach($podtyp as $k=>$h) {
			if($k != $podtyp_id) {
				continue;
			}

			/*
			if (count($_SESSION['ERRMSG']) > 0) {
   			   $this->vrat .=
				'<tr>
				  <td colspan="5">';


				$errMessage = "";
				$i = 1;
				foreach ($_SESSION['ERRMSG'] as $mes) {
				   $errMessage .= '<p style="color:red;"> ('.$i.'.) '.$mes.'</p>';
				   $i++;
				}

		        $this->vrat .= ' 
		        <div id="dialog-message" title="compbet.com">'.$errMessage.'</div>';

			   $this->vrat .=
				  '</td>
				</tr>';
              
			}
			*/

			$this->vrat .=
				'<tr>
					<td>'.$x.'.</td>
					<td><input type="text" class="mandatory sinput3" maxlength="50" name="podtyp['.$k.'][interninazev]" value="'.Help::Html($h['interni_nazev']).'" /></td>
					<td><input type="text" class="sinput3" maxlength="20" name="podtyp['.$k.'][nazev]" value="'.Help::Html($h['text']).'" /></td>
					<td>
						<a href="javascript:openWin(\'ciselnik.php\',\'podtyp['.$k.'][nazev]\',\'preklady\',400,300);void(0);">
							<img src="_clip/translate.gif" alt="Prekladovy slovnik" class="img" />
						</a>
					</td>
					<td><input type="checkbox" name="podtyp['.$k.'][radek]"  '.($h['radek']?"checked=\"checked\"":"").'class="no" /><input type="text" class="span-1" name="podtyp['.$k.'][radek_num]" value="'.($h['radek']?$h['radek_max']:"").'"/></td>
					<td><input type="submit" class="sinput2" style="width:40px" name="edit['.$k.']" value="Edit" />
						<input type="submit" class="sinput" name="delete['.$k.']" onclick="if(!confirm(\'Opravdu chcete smazat?\')) return false;" value="Vymazat" />
						<input type="submit" class="sinput2"  name="duplicate['.$k.']"  value="Duplikovat" title="Vytvořit kopii" />
					</td>
					<td><input type="button" value="+" class="sinput2" onclick="this.value=(this.value==\'+\'?\'-\':\'+\');displayObj(document.getElementById(\'podtyp_'.$k.'\'))"/></td>
				</tr>
				<tr>
					<td colspan="8">
						<table style="display:none;border-collapse:collapse;" id="podtyp_'.$k.'">';

			$pole = "<h3>Sloupce</h3><br />";
			foreach($h['sloupce'] as $k2=>$h2) {
				$pole .= 
					'<div>
						<input type="text" class="sinput3" name="podtyp['.$k.'][sloupec]['.$k2.']" value="'.Help::Html($h2).'">
						<a href="javascript:openWin(\'ciselnik.php\',\'podtyp['.$k.'][sloupec]['.$k2.']\',\'preklady\',400,300);void(0);">
							<img src="_clip/translate.gif" alt="Překladový slovník" class="img" />
						</a>
						<a href="?section='.$this->section.'&podtyp_id='.$k.'&sloupec='.$k2.'&superb=1&deletesloupec=1" onclick="if(!confirm(\'Opravdu chcete smazat?\')) return false;" >
							Odstranit
						</a>
					</div>';
			}
		
			$y = 1;
			foreach($sp_ar as $k2=>$h2) {
				$this->vrat .=
					'<tr>
						<td valign="top" style="background:#7cb6a4;font-weight:700;padding:4px;border-bottom:1px solid #778899" >
							<strong>'.Help::Html($h2['preklad']).'</strong>
						</td>
						<td valign="top" style="background:#7cb6a4;border-bottom:1px solid #778899;">';

				if(isset($typ_sport[$k2])) {
					$this->vrat .=
						'<table style="border-collapse:collapse;margin:2px;width:100%;">
							<tr>
								<th>&nbsp;</th>
								<th title="Aktivuj pro daný sport a typ">Patří do</th>
							</tr>';

					foreach($typ_sport[$k2] as $k3=>$h3) {
						$this->vrat .=
							'<tr>
								<td valign="top" style="background:#696969;color:white;padding:4px">'.$h3.':</td>
								<td valign="top" style="background:#696969">
									<input type="checkbox" '.(!empty($h['typ_podtyp'][$k2][$k3]['status']) ? 'checked="checked"' : '').' class="no" name="podtyp['.$k.'][typ]['.$k2.']['.$k3.']" />
								</td>
							</tr>';
					}
					$this->vrat .= '</table>';
				}
				else {
					$this->vrat .= '&nbsp;';
				}
				
				$this->vrat .= '</td>'.($y==1?"<td valign=\"top\" style=\"padding-left:40px;vertical-align:top\" rowspan=".(count($sp_ar)*3).">".$pole."</td>":"").'</tr>';
				$y++;
			}

			$this->vrat .= '</td></table></tr>';
			$x++;
		}

		/*spodek*/
		$this->vrat .=
			'<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="6" align="left">
					<br /><strong>Nový PodTyp</strong>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="text" class="mandatory sinput3" maxlength="50" id="interninazev" name="interninazev" value="'.(isset($_POST['interninazev'])?Help::Html($_POST['interninazev']):"").'" /></td>
				<td><input type="text" class="sinput3" maxlength="20" id="nazev" name="nazev" value="'.(isset($_POST['nazev'])?Help::Html($_POST['nazev']):"").'" /></td>
				<td><a href="javascript:openWin(\'ciselnik.php\',\'nazev\',\'preklady\',400,300);void(0);"><img src="_clip/translate.gif" alt="Překladový slovník" class="img" /></a></td>
				<td><input type="checkbox" name="radek" onclick="SloupecRadek(this,\'radek_num\');" class="no" /></td>
				<td><input type="button" class="sinput2" onclick="EditSloupce(1);" value="+" /><input type="button" class="sinput2" onclick="EditSloupce(-1);" value="-" /></td>
				<td colspan="6"><input type="submit" name="create" class="sinput2" value="Vytvořit" /></td>
			</tr>
			<tr>
				<td colspan="6" align="left">
					<table style="border-collapse:collapse;">';

		$pole =
			'<h3>Sloupce</h3><br />
			<div id="sl_1"><input type="text" class="sinput3" name="newpodtyp[sloupec][1]">
				<a href="javascript:openWin(\'ciselnik.php\',\'newpodtyp[sloupec][1]\',\'preklady\',400,300);void(0);">
					<img src="_clip/translate.gif" alt="Překladový slovník" class="img" />
				</a>
			</div>
			<div id="sl_2">
				<input type="text" class="sinput3" name="newpodtyp[sloupec][2]" />
				<a href="javascript:openWin(\'ciselnik.php\',\'newpodtyp[sloupec][2]\',\'preklady\',400,300);void(0);">
					<img src="_clip/translate.gif" alt="Překladový slovník" class="img" />
				</a>
			</div>
			<div id="sl_3">
				<input type="text" class="sinput3" name="newpodtyp[sloupec][3]">
				<a href="javascript:openWin(\'ciselnik.php\',\'newpodtyp[sloupec][3]\',\'preklady\',400,300);void(0);">
					<img src="_clip/translate.gif" alt="Překladový slovník" class="img" />
				</a>
			</div>';

		$x = 1;
		foreach($sp_ar as $k=>$h) {
			$this->vrat .=
				'<tr>
					<td valign="top" style="background:#7cb6a4;font-weight:700;vertical-align:top;padding:4px;border-bottom:1px solid #778899">
						<strong>'.Help::Html($h['preklad']).'</strong>
					</td>
					<td valign="top" style="background:#7cb6a4;border-bottom:1px solid #778899;">';

			if(isset($typ_sport[$k])) {
				$this->vrat .=
					'<table style="border-collapse:collapse;margin:2px;width:100%;">
						<tr>
							<th>&nbsp;</th>
							<th title="Aktivuj pro daný sport a typ">Patří do</th>
						</tr>';
				foreach($typ_sport[$k] as $k2=>$h2) {
					$this->vrat .=
						'<tr>
							<td valign="top" style="background:#696969;color:white;padding:4px">'.$h2.':</td>
							<td valign="top" style="background:#696969">
								<input type="checkbox" class="no" name="newpodtyp[typ]['.$k.']['.$k2.']" />
							</td>
						</tr>';
				}
				$this->vrat .= '</table>';
			}
			else {
				$this->vrat .= '&nbsp;';
			}

			$this->vrat .= '</td>'.($x==1?"<td id=\"rows\" valign=\"top\" style=\"padding-left:40px;vertical-align:top;\" rowspan=".(count($sp_ar)*3).">".$pole."</td>":"").'</tr>';
			$x++;
		}

		$this->vrat .= '</table></td></tr></tbody></table></form>';
	}


	/**
	 * vyber dat z databaze
	 * @return object
	 */
	public function selectData($where="") {
		$sql = "SELECT a.podtyp_id,a.radek,a.sloupec_pocet_max,a.text FROM podtyp a ".$where." order by b.podtyp_id";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

		return $res;
	}


	/**
	 * Nastaveni prav k sekci
	 * @param int $update pravo zapisu
	 * @param int $delete pravo smazani
	 * @return void
	 */
	public function setPrivileges($update,$delete) {

	}

	
	/**
	 * Vraci vystup do tridy main
	 * @return string
	 */
	public function getContent() {
		return $this->vrat;
	}


	public function __destruct() {
		
	}

}
