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
 * @package    Sport
 */

class Oblast extends Template{

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

    $this->CreateOblast();

   }

   #Vymazani oblasti#
   else if(isset($_POST['delete'])){

	    $this->DeleteOblast(key($_POST['delete']));

   }


   #Editace oblasti#
   else if(isset($_POST['editAll'])){
		$this->EditOblast();
   }
   else if(isset($_POST['edit'])){

	    $this->EditOblast(intval(key($_POST['edit'])));

   }
   
   #Posuny oblasti#
   else if(isset($_POST['up']) || isset($_POST['down'])){

		$key = (isset($_POST['up'])?key($_POST['up']):key($_POST['down']));
		$pozice = (isset($_POST['up'])?intval(key($_POST['up'][$key])):intval(key($_POST['down'][$key])));

		$this->UpDown($key,$pozice);

   }

   $this->ShowOblast();



    $this->dbGame->disconnect();

  }

   /**
 * Edit oblasti
 * @param int|NULL $oblast_id id oblasti nebo null pro vsechny
 * @return void
 */
private function EditOblast($oblast_id = null){
	
	if (!isset($oblast_id)) {
		$updatingAll = true;
		if (empty($_POST['oblast']) || !is_array($_POST['oblast']))
			return;
		else
			$oblastIds = array_keys($_POST['oblast']);
	}
	else {
		$updatingAll = false;
		$oblastIds = array( $oblast_id );
	}

	$error = null;
	$anyUpdated = false; //TODO: make it array of regions and use them to better cache invalidation
	try {
		foreach ($oblastIds as $oblast_id) {
			$oblast_id = intval($oblast_id);
			if (empty($oblast_id))
				continue;
			$status = true;
		   if(!isset($_POST['oblast'][$oblast_id]['nazev']) || mb_strlen(trim($_POST['oblast'][$oblast_id]['nazev'])) > 20 || mb_strlen(trim($_POST['oblast'][$oblast_id]['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> musí být vybrán z překladů</div><br />";$status = false;}
		
		   if ($status) {
		   	 $regionName = It6_Filter_TranslationKey::filterStatic( $_POST['oblast'][$oblast_id]['nazev'] );
		   	
		     $preklady = new Preklady();
		
		     $res = $preklady->selectData("where index_pole='".Help::slash($regionName)."'");
		
		     if (!$row =& $res->fetchRow()) {$this->vrat .= "<div class=\"errormsg\"> (<strong>$regionName</strong>) tento index není platným překladem</div><br />";$status = false;}
		
		   }
		
		   #vsechno je  vporadku muzeme editovat#
		   if($status){
				$anyUpdated = true;
			    $sql = "update oblast set iso='".Help::Slash($_POST['oblast'][$oblast_id]['iso'])."',betradar_oblast_id=".intval($_POST['oblast'][$oblast_id]['betradar_oblast_id']).",img='".Help::Slash($_POST['oblast'][$oblast_id]['img'])."',nazev='".Help::Slash($regionName)."'";
			    $updatePosition = false;
			    if (!empty($_POST['oblast'][$oblast_id]['pozice'])) {
			    	$pozice = intval($_POST['oblast'][$oblast_id]['pozice']);
			    	if (!empty($pozice)) {
			    		$sql .= ",pozice=$pozice";
			    		$updatePosition = !$updatingAll;
			    	}
			    }
			    $sql .= " where oblast_id=$oblast_id";
		        $res = $this->dbGame->query($sql);
			    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace oblastu',"admin_ex_db");

			    if ($updatePosition) {
			    	$res = $this->dbGame->query("SELECT COUNT(*) AS c FROM oblast WHERE pozice=$pozice AND oblast_id<>$oblast_id");
			    	if (!DB::isError($res)) {
			    		$res = $res->fetchRow();
			    		if (0 < $res['c'])
			    			$res = $this->dbGame->query("UPDATE oblast SET pozice=pozice+1 WHERE pozice>=$pozice AND oblast_id<>$oblast_id");
			    	}
			    }
			    
			    foreach($_POST['oblast'][$oblast_id]['seo'] as $k=>$h){
				 $h = It6_Filter_SeoUrl::filterStatic($h);
			     $sql = "select url from seo_url where url='".Help::Slash($h)."'";
		         $res =& $this->dbGame->query($sql);
			     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace oblastu',"admin_ex_db");
		
			     if(mb_strlen($h) > 0 ){
			      $sql = "replace into seo_url values (".intval($k).",2,'".Help::Slash($h)."',".$oblast_id.")";
		          $res =& $this->dbGame->query($sql);
			      if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: editace oblastu',"admin_ex_db");
			     }
		
			    }
		
		
			    $this->vrat .= "<div class=\"okmsg\">oblast ".Help::Html($regionName)." byl úspěšně editován</div><br />";
		
				It6_Log::info(
					"Region '%region%' was updated.",
					It6_Log::TAG_ADMIN_OPERATION,
					array('region' => $regionName)
				);
		
			}
		}
	}
	catch (Exception $e) {
		$error = $e;
	}
	if ($anyUpdated) {
		It6_GlobalCache_Invalidator::invalidateSportsbook();
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();
	}
	if (isset($error))
		throw error;
}

   /**
 * Posun nahoru dolu
 * @param int $oblast_id id oblastu
 * @param int $pozice id aktualni pozice
 * return void
 */
  public function UpDown($oblast_id,$pozice){

   $this->dbGame->autoCommit(false);

   if(isset($_POST['down']))
    $sql = "SELECT oblast_id,pozice FROM `oblast` WHERE pozice>".$pozice." order by pozice limit 1";
   else
    $sql = "SELECT oblast_id,pozice FROM `oblast` WHERE pozice<".$pozice." order by pozice desc limit 1";

   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky oblast',"admin_ex_db");

   if ($row =& $res->fetchRow()) {$oblast_id_2 = $row['oblast_id'];$pozice2 = $row['pozice'];}else{$this->dbGame->rollback();throw new ExHandler('Chyba nebyla zjistena pozice',"admin_ex_poge");}

   $sql = "update oblast set pozice=".$pozice." where oblast_id=".$oblast_id_2;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky oblast',"admin_ex_db");

   $sql = "update oblast set pozice=".$pozice2." where oblast_id=".$oblast_id;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky oblast',"admin_ex_db");

   $this->dbGame->autoCommit(true);
   $this->dbGame->commit();

   $this->vrat .= "<div class=\"okmsg\">Oblast ".Help::Html($_POST['oblast'][$oblast_id]['nazev'])." byl ůspěšně ".(isset($_POST['up'])?"posunut nahoru":"posunut dolů")."</div><br />";

		It6_Log::info(
			"Region '%region%' was moved '%action%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'region'	=> $_POST['oblast'][$oblast_id]['nazev'],
				'action'	=> (isset($_POST['up'])?"up":"down")
			)
		);
  }



   /**
 * Vymazani oblastu
 * @param int $oblast_id id oblastu
 * return void
 */
  private function Deleteoblast($oblast_id){



     $sql = "delete from oblast where oblast_id=".intval($oblast_id);
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) {$this->dbGame->autoCommit(false); $this->dbGame->rollback();throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vymazani oblastu. oblast je asi navázán na události',"admin_ex_db");}

	 if($this->dbGame->affectedRows()){

	   $this->vrat .= "<div class=\"okmsg\">oblast byl úspěšně smazán</div><br />";

	   	It6_Log::info(
			"Region '%region%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('region'	=> $_POST['oblast'][$oblast_id]['nazev'])
		);

		It6_GlobalCache_Invalidator::invalidateSportsbook();
		It6_GlobalCache_Invalidator::invalidateSportMenuFrame();

	 }else
	     $this->vrat .= "<div class=\"errormsg\">oblast se nepodařilo smazat</div><br />";


  }


 /**
 * Vytvoreni oblastu
 * @return void
 */
  private function Createoblast(){

   $status = true;


   if(!isset($_POST['nazev']) || mb_strlen(trim($_POST['nazev'])) > 20 || mb_strlen(trim($_POST['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> musí být vybrán z překladů</div><br />";$status = false;}

   if ($status) {
     $regionName = It6_Filter_TranslationKey::filterStatic(trim($_POST['nazev']));	
     $preklady = new Preklady($this->section);

     $res = $preklady->selectData("where index_pole='".Help::slash($regionName)."'");

     if (!$row =& $res->fetchRow()) {$this->vrat .= "<div class=\"errormsg\"> (<strong>$regionName</strong>) tento index není platným překladem</div><br />";$status = false;}

   }

   #vsechno je  vporadku muzeme zapisovat#
   if($status){


		$sql = "select max(pozice) AS m from oblast";
        $res =& $this->dbGame->query($sql);

		if (!$row =& $res->fetchRow()) $poz = 1;else $poz = intval($row['m'])+1;


	    $sql = "insert into oblast (nazev,pozice,img) values ('".Help::slash($regionName)."',".$poz.",'".Help::Slash($_POST['img'])."')";
        $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) {$this->dbGame->autoCommit(false); $this->dbGame->rollback();throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho oblastu',"admin_ex_db");}
	    $this->vrat .= "<div class=\"okmsg\">Oblast ".Help::Html($regionName)." byl úspěšně vytvořena</div><br />";

		It6_Log::info(
			"New region '%region%' was created.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('region'	=> $_POST['nazev'])
		);


   }

  }

/**
* metoda vypise vsechny zadane oblasti
* @return void
*/
public function ShowOblast(){
	$jazyky = array();
	$sql = "select alt_text,lang_id from jazyky";
	$res =& $this->dbGame->query($sql);
	
	while ($row =& $res->fetchRow()){
		$jazyky[$row['lang_id']] = $row['alt_text'];
	}

	$sql = "SELECT MAX(pozice) AS m,min(pozice)AS m1 FROM oblast";
	$res =& $this->dbGame->query($sql);

	if (!$row =& $res->fetchRow()) {
		$poz = 1;
		$min = 1;
	}
	else {
		$poz = intval($row['m']);
		$min = intval($row['m1']);
	}

	$res = $this->selectData();

	/*hlavicka*/
	$this->vrat .=
		'<form method="post" action="?superb=1&section='.$this->section.'">
			<table class="table-list">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Název</th>
						<th>&nbsp;</th>
						<th>Obrázek</th>
						<th>BetRadar ID / ISO</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>Pozice</th>
					</tr>
				</thead>
				<tbody>';

	$x = 1;
	$preklady = new Preklady();
	$regions = array();
	$regionsOptions = '<option disabled="disabled" selected="selected">'.I18n::tr('select').'</option>';
	while($row = $res->fetchRow()) {
		$res2 = $preklady->selectData("WHERE lang_id=1 AND index_pole='".Help::slash($row['nazev'])."'");
		if ($row2 =& $res2->fetchRow())
			$row['title'] = $row2['text'];
		else
			$row['title'] = "";
		$regions[] = $row;
		$regionsOptions .= '<option value="'.intval($row['pozice'] + 1).'">'.$row['title'].'</option>';
	}
	

	foreach($regions as $row) {
		$this->vrat .= 
					'<tr>
						<td><strong>'. $x .'.</strong></td>
						<td>
							<input
								type="text"
								class="mandatory"
								maxlength="20"
								title="'.Help::Html($row['title']).'"
								id="oblast['.$row['oblast_id'].'][nazev]"
								name="oblast['.$row['oblast_id'].'][nazev]"
								value="'.Help::Html($row['nazev']).'"
							/>
						</td>
						<td>
							<a href="javascript:openWin(\'ciselnik.php\',\'oblast['.$row['oblast_id'].'][nazev]\',\'preklady\',400,300);void(0);">
								<img src="_clip/translate.gif" alt="Prekladovy slovnik" class="img" />
							</a>
						</td>
						<td>
							<input
								type="text"
								maxlength="60"
								id="oblast['.$row['oblast_id'].'][img]"
								name="oblast['.$row['oblast_id'].'][img]"
								value="'.Help::Html($row['img']).'"
							/>
							<a href="javascript:window.open(\'/galery.php?type=1&node=oblast['.$row['oblast_id'].'][img]\',\'\',\'width=640,height=480,scrollbars=yes\');void(0);">
								<img src="_clip/image_aktivni.gif" alt="" class="img" />
							</a>
						</td>
						<td>
							<input
								type="text"
								maxlength="20"
								style="width:50px"
								title="Bet Radar oblast ID"
								name="oblast['.$row['oblast_id'].'][betradar_oblast_id]"
								value="'.Help::Html($row['betradar_oblast_id']).'"
							/>
							/
							<input
								type="text"
								maxlength="3"
								style="width:40px"
								title="ISO zeme"
								name="oblast['.$row['oblast_id'].'][iso]"
								value="'.Help::Html($row['iso']).'"
							/>
						</td>
						<td>
							<input type="submit" name="edit['.$row['oblast_id'].']" class="sbutton" value="Editovat" />
						</td>
						<td>
							<input
								type="submit"
								name="delete['.$row['oblast_id'].']"
								class="sbutton"
								onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($row['title']).'?\')) return false;"
								value="Smazat"
							/>
						</td>
						<td>
							'.($row['pozice'] == $min
								? '&nbsp;'
								: '<input
									type="submit"
									title="Posun nahoru"
									name="up['.$row['oblast_id'].']['.$row['pozice'].']"
									class="sbutton2"
									value="^"
								/>'
							).'
						</td>
						<td>
							'.($row['pozice'] == $poz
								? '&nbsp;'
								: '<input
									type="submit"
									title="Posun dolů"
									name="down['.$row['oblast_id'].']['.$row['pozice'].']"
									class="sbutton2"
									value="v"
									/>'
							).'
						</td>
						<td>
							<input
								type="text"
								size="5"
								title="Pozice"
								id="region'.$row['oblast_id'].'-order"
								name="oblast['.$row['oblast_id'].'][pozice]"
								style="text-align: right;"
								value="'.intval($row['pozice']).'"
							/>
							<img
								src="/_clip/translate.gif"
								alt="select from list" class="img"
								onclick="$(\'#order-selector-container_'.$row['oblast_id'].'\').show()"
							/>
							<div
								class="popup-box popup-box-oneline"
								id="order-selector-container_'.$row['oblast_id'].'"
							>
								'.I18n::tr('place_after').':
								<select
									title="'.I18n::tr('place_after').'"
									onchange="$(\'#region'.$row['oblast_id'].'-order\').val($(this).val()); $(\'#order-selector-container_'.$row['oblast_id'].'\').hide()"
								>
									'.$regionsOptions.'
								</select>
								<img
									src="/_clip/logout.png"
									alt="close"
									onclick="$(\'#order-selector-container_'.$row['oblast_id'].'\').hide()"
								/>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="10">
							<table style=\"width:800px\">';

		$xx = 1;
		foreach($jazyky as $k=>$h){
			if($xx == 1)
				$this->vrat .= '<tr>';
			$sql = "SELECT url FROM seo_url WHERE type=2 AND event_id=".$row['oblast_id']." AND lang_id=".$k;
			$res3 =& $this->dbGame->query($sql);
			if ($row3 =& $res3->fetchRow());
			else
				$row3['url'] = '';

			$this->vrat .=
								'<td>
									'.$h.':
									<input
										type="text"
										name="oblast['.$row['oblast_id'].'][seo]['.$k.']"
										value="'.Help::Html($row3['url']).'"
									/>
								</td>';

			if($xx == 4) {
				$xx = 0;
				$this->vrat .= '</tr>';
			}
			$xx++;
		}

		$this->vrat .= 
							'</table>
						</td>
					</tr>';
		$x++;
	}

	/*spodek*/
	$this->vrat .=
					'<tr>
						<td colspan="7"></td>
						<td colspan="3"><input type="submit" name="editAll" value="Editovat vše" /></td>
					</tr>
					<tr>
						<td colspan="10" class=""><br /><strong>Nová oblast</strong></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<input
								type="text"
								class="mandatory"
								maxlength="20"
								id="nazev"
								name="nazev"
								value="'.(isset($_POST['nazev'])?Help::Html($_POST['nazev']):"").'"
								
							/>
						</td>
						<td>
							<a href="javascript:openWin(\'ciselnik.php\',\'nazev\',\'preklady\',400,300);void(0);">
								<img src="_clip/translate.gif" alt="Překladový slovník" class="img" />
							</a>
						</td>
						<td>
							<input type="text" maxlength="60" id="img" name="img" value="'.(isset($_POST['img'])?Help::Html($_POST['img']):"").'" />
							<a href="javascript:window.open(\'/galery.php?type=1&node=img\',\'\',\'width=640,height=480,scrollbars=yes\');void(0);">
								<img src="_clip/image_aktivni.gif" alt="" class="img" />
							</a>
						</td>
						<td>&nbsp;</td>
						<td colspan="6"><input type="submit" name="create"  value="Vytvořit" /></td>
					</tr>
				</tbody>
			</table>
		</form>';
}



  /**
 * vraci pole oblastu
 * @return array
 */
  public function GetOblast(){

		$oblast = array();
		$res = $this->selectData();

		while ($row =& $res->fetchRow()){
		  $oblast[$row['oblast_id']]['oblast_id'] = $row['oblast_id'];
			$oblast[$row['oblast_id']]['nazev'] 		= $row['nazev'];
			$oblast[$row['oblast_id']]['pozice'] 		= $row['pozice'];
			//$oblast[$row['oblast_id']]['zvyrazneni'] = $row['zvyrazneni'];
			//$oblast[$row['oblast_id']]['zobrazeno'] = $row['zobrazeno'];
		}

		return $oblast;
  }



 /**
 * vyber dat z databaze
 * @return object
 */
  public function selectData($where=""){

     $sql = "select oblast_id,nazev,pozice,img,betradar_oblast_id,iso from oblast ".$where." order by pozice";
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
