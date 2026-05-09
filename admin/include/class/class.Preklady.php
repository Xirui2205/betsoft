<?php


/**
 * Trida pro praci s preklady
 *
 *
 * <code>
 *
 * </code>
 *
 * @package   main
 */

class Preklady{

/**
 * navratova hodnota
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * pravo zmeny v sekci
 * @access private
 * @var int
 */
private  $update = 0;
/**
 * pravo vymazani v sekci
 * @access private
 * @var int
 */
private  $delete = 0;

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
 * pole aktivnich jazyku
 * @access private
 * @var array
 */
private  $activeLangs = array();

/**
 * pole vsech jazyku
 * @access private
 * @var array
 */
private  $allLangs = array();

/**
 * pole prekladu
 * @access private
 * @var array
 */
private  $preklady = array();

/**
 * objekt strankovani
 * @access private
 * @var Page
 */
private  $page;


/**
 * pocet vypisu na stranku
 * @access private
 * @var number
 */
private $page_num = 1;

/**
 * User feedback
 * @var array
 */
public $messages = array();

/**
 * User feedback
 * @var array
 */
public $errors = array();

  public function __construct($section=1,$dbGame=null){

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
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){



    #Vytvoreni noveho prekladu#
    if(isset($_POST['create']) && isset($_POST['newindex']) && isset($_POST['newpreklad']) && is_array($_POST['newpreklad'])){

	 if($this->update)
	    $this->CreatePreklad();
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

	#Editace jednoho prekladu#
	else if(!empty($_POST['updateOne']) && isset($_POST['preklad']) && is_array($_POST['preklad'])){

	 if($this->update)
	    $this->EditPrekladOne();
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

	#Editace vsech prekladu#
    else if(isset($_POST['editall']) && isset($_POST['preklad']) && is_array($_POST['preklad'])){

	 if($this->update)
	    $this->EditPreklad();
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

    #Export#
    else if(isset($_POST['export'])){

	 if($this->update)
	    $this->Export();
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}


    #Import#
    else if(isset($_POST['import'])){

	 if($this->update)
	    $this->Import();
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

	#Editace vsech prekladu#
    else if(isset($_POST['delete']) && isset($_GET['index'])){

	 if($this->delete)
	    $this->DeletePreklad($_GET['index']);
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání údajů</div>\n";

	}

   $this->PolePreklady();
   $this->ShowPreklady();

   $this->vrat .= "";

   $this->dbGame->disconnect();

}


/**
 * Import prekladu z uploadnuteho XML souboru (dle XML schematu bewatrans.xsd)
 * Je moznost importovat vsechny preklady nebo jen chybejici (vzdy se jen vklada/aktualizuje, nic se nemaze).
 * Asociace a reporty chybejicich prekladu jsou vzdy vsechny pridany/aktualizovany.
 * @return void
 */
private function Import(){

	if (empty($_FILES['fileimport']) || mb_strlen($_FILES['fileimport']['name']) < 1) {
		$this->vrat .= UiUtil::printErrors('Nebyl vybrán soubor');
		return;
	}
	$fileName = $_FILES['fileimport']['tmp_name'];
	$doc = null;
	if (!It6_Xml_XmlSchema::isValidBbasTransDocument($fileName, $doc)) {
		$this->vrat .= UiUtil::printErrors('Nahraný soubor není validní dokument pro import překladů');
		return;
	}

	$xpath = new DOMXPath($doc);
	$onlyNew = !empty($_POST['importnew']);
	$db = Zend_Registry::get('zdb_game');

	// TRANSLATION RESOURCES
	$translations = array();
	// XML node name -> key in array
	$map = array('key' => 'key', 'langId' => 'langId', 'text' => 'text');
	foreach ($xpath->query('/data/translations/resource') as $node) {
		$resource = array();
		foreach ($node->childNodes as $child) {
			$name = $child->nodeName;
			if (array_key_exists($name, $map))
				$resource[$map[$name]] = $child->textContent;
		}
		$resource['lkey'] = strtolower($resource['key']);
		$translations[ $resource['lkey'] ][ $resource['langId'] ] = $resource;
	}
	if ($onlyNew) {
		$pks = array();
		foreach ($translations as $lkey => $langs) {
			foreach ($langs as $langId => $resource)
				$pks[] = '(' . $db->quote(array($lkey, $langId)) . ')';
		}
		// already present (key, langId) pairs
		$dbResources = array();
		$res = $db->select()
			->from('preklady', array('key' => 'index_pole', 'langId' => 'lang_id'))
			->where('(index_pole,lang_id) IN (' . implode(',', $pks) .  ')')
			->query();
		while ($row = $res->fetch()) {
			$lkey = strtolower($row['key']);
			$langId = $row['langId'];
			if (array_key_exists($lkey, $dbResources))
				$dbResources[$lkey][$langId] = true;
			else
				$dbResources[$lkey] = array($langId => true);
		}
		foreach (array_keys($translations) as $lkey) {
			if (array_key_exists($lkey, $dbResources)) {
				$langs = &$translations[$lkey];
				foreach (array_keys($langs) as $langId) {
					if (array_key_exists($langId, $dbResources[$lkey]))
						unset($langs[$langId]); 
				};
				if (empty($langs))
					unset($translations[$lkey]);
			}
		}
		unset($langs);
	}
	if (empty($translations))
		$this->vrat .= UiUtil::printMessages('Nenalezeny žádné nové překlady.');
	else {
//		$transId = $db->select()
//			->from('preklady', array('id' => '(MAX(preklad_id))'))
//			->query()->fetchAll();
//		$transId = $transId[0]['id'];
		$sql = array();
		foreach ($translations as $langs) {
			foreach ($langs as $r)
				$sql[] = '(' . $db->quote(array($r['key'], $r['langId'], $r['text'])) . ')';
		}
		$sql = 'REPLACE INTO `preklady`(`index_pole`,`lang_id`,`text`)VALUES' . implode(',', $sql);
		$db->query($sql);
		$this->vrat .= UiUtil::printMessages('Překlady byly naimportovány');
	}

	// RESOURCE ASSOCIATIONS TO PAGES
	$associations = array();
	// XML node name -> key in array
	$map = array('key' => 'key', 'controller' => 'controller', 'action' => 'action');
	foreach ($xpath->query('/data/associations/resource') as $node) {
		$resource = array();
		foreach ($node->childNodes as $child) {
			$name = $child->nodeName;
			if (array_key_exists($name, $map))
				$resource[$map[$name]] = $child->textContent;
		}
		$resource['lkey'] = strtolower($resource['key']);
		$associations[] = $resource;
	}
	if (empty($associations))
		$this->vrat .= UiUtil::printMessages('Nenalezeny žádné asociace překlad-stránka.');
	else {
		$sql = array();
		foreach ($associations as $r)
			$sql[] = '(' . $db->quote(array($r['lkey'], $r['controller'], $r['action'])) . ')';
		$sql = 'REPLACE INTO `translate_pages`(`translate_key`,`controller`,`action`)VALUES' . implode(',', $sql);
		$db->query($sql);
		$this->vrat .= UiUtil::printMessages('Asociace překlad-stránka byly aktualizovány');
	}

	// REPORTED RESOURCES (MISSING)
	$missing = array();
	// XML node name -> key in array
	$map = array('key' => 'key', 'langId' => 'langId', 'controller' => 'controller', 'action' => 'action');
	foreach ($xpath->query('/data/missing/resource') as $node) {
		$resource = array();
		foreach ($node->childNodes as $child) {
			$name = $child->nodeName;
			if (array_key_exists($name, $map))
				$resource[$map[$name]] = $child->textContent;
		}
		$resource['lkey'] = strtolower($resource['key']);
		$missing[] = $resource;
	}
	if (empty($missing))
		$this->vrat .= UiUtil::printMessages('Nenalezeny žádné reporty chybějících překladů.');
	else {
		$sql = array();
		foreach ($missing as $r)
			$sql[] = '(' . $db->quote(array($r['lkey'], $r['langId'], $r['controller'], $r['action'], 'missing')) . ')';
		$sql = 'REPLACE INTO `translate_missing`(`translate_key`,`lang_id`,`controller`,`action`,`status`)VALUES' . implode(',', $sql);
		$db->query($sql);
		$this->vrat .= UiUtil::printMessages('Reporty chybějících překladů byly aktualizovány');
	}

	$this->vrat .= UiUtil::printMessages('(Aby se změny projevily na webu, musí se přegenerovat slovníkové soubory a musí se obnovit cache.)');
}

private static function writeTmpFile($f, $str, &$size) {
	fwrite($f, $str);
	$size += strlen($str);
} 

/**
 * Export prekladu do XML (dle XML schematu bewatrans.xsd)
 * @return void
 */
private function Export(){

	$tmpFile = tmpfile();
	$size = 0;
	self::writeTmpFile($tmpFile, "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<data>\n<translations>\n", $size);
	$db = Zend_Registry::get('zdb_game');
	$res = $db->select()
		->from('preklady', array('key' => 'index_pole', 'langId' => 'lang_id', 'text' => 'text'))
		->order(array('index_pole', 'lang_id'))
		->query();
	while ($row = $res->fetch()) {
		$key = htmlspecialchars($row['key']);
		$text = htmlspecialchars($row['text']);
		self::writeTmpFile($tmpFile, "<resource><key>{$key}</key><langId>{$row['langId']}</langId><text>{$text}</text></resource>\n", $size);
	}
	self::writeTmpFile($tmpFile, "</translations>\n<associations>\n", $size);
	$res = $db->select()
		->from('translate_pages', array('key' => 'translate_key', 'controller' => 'controller', 'action' => 'action'))
		->query();
	while ($row = $res->fetch()) {
		$key = htmlspecialchars($row['key']);
		$controller = htmlspecialchars($row['controller']);
		$action = htmlspecialchars($row['action']);
		self::writeTmpFile($tmpFile, "<resource><key>{$key}</key><controller>{$controller}</controller><action>{$action}</action></resource>\n", $size);
	}
	self::writeTmpFile($tmpFile, "</associations>\n<missing>\n", $size);
	$res = $db->select()
		->from('translate_missing', array('key' => 'translate_key', 'langId' => 'lang_id', 'controller' => 'controller', 'action' => 'action'))
		->query();
	while ($row = $res->fetch()) {
		$key = htmlspecialchars($row['key']);
		$controller = htmlspecialchars($row['controller']);
		$action = htmlspecialchars($row['action']);
		self::writeTmpFile($tmpFile, "<resource><key>{$key}</key><langId>{$row['langId']}</langId><controller>{$controller}</controller><action>{$action}</action></resource>\n", $size);
	}
	self::writeTmpFile($tmpFile, "</missing>\n</data>\n", $size);
	rewind($tmpFile);
	header('Content-Type: application/xml');
	header('Content-Disposition: attachment; filename="preklady-' . strftime('%Y-%m-%d-%H-%M-%S') . '.xml');
	header('Content-Length: ' . $size);
	fpassthru($tmpFile);
	fclose($tmpFile);
	It6_Session_Admin::end();
	exit();
}

  /**
 * Vraceni prekladu
 * @param string $index index prekladu
 * @param int $lang_id index_pole
 * @return array
 */
 public function FindPreklad($index,$lang_id=null){

  $pole = array();


  $res = $this->selectData2(" index_pole='".Help::slash($index)."' ".($lang_id != null?"and lang_id=".intval($lang_id):""));

  while ($row =& $res->fetchRow()) {$pole[$row['lang_id']] = $row['text'];}


  if(count($pole) < 1 && $lang_id != null) $pole[$lang_id] = "Translation not found";
  else if(count($pole) < 1) $pole[1] = "Translation not found";

  return $pole;

 }

   /**
 * Vraceni prekladu
 * @param string $index index prekladu
 * @param int $lang_id index_pole
 * @return string
 */
 public function findTrans($index,$lang_id=null){

  $pole = '';

  if($lang_id == null) return 'Lang not specify';

  $res = $this->selectData2("where index_pole='".Help::slash($index)."' and lang_id=".intval($lang_id));

  if ($row =& $res->fetchRow()) {$pole= $row['text'];}


  if(mb_strlen($pole) < 1) $pole[$lang_id] = "Translation not found";

  return $pole;

 }

 /**
 * Vymazani prekladu
 * @param string $index index_pole
 * @return void
 */
 private function DeletePreklad($index){

     $sql = "delete from preklady where index_pole='".Help::slash($index)."'";
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: smazani prekladu z databáze',"admin_ex_db");

	 if($this->dbGame->affectedRows()){
		$this->messages[] = I18n::tr('The translation {0} was successfuly deleted.', $index);
		
		It6_Log::info(
			"Translation '%translation%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('translation'	=> $index)
		);
	 }else
	 	$this->errors[] = I18n::tr('The translation {0} could not be deleted.', $index);

 }

/**
 * Editace jednoho prekladu na strance
 */
private function EditPrekladOne() {
	$this->EditPreklad();
	die($this->vrat); // because AJAX
}

 /**
 * Editace vsech prekladu na strance
 * @return void
 */
	private function EditPreklad(){

		$x = 0;

//echo "<pre>";print_r($_POST['preklad']);exit;
		foreach($_POST['preklad'] as $k=>$h){
			$x++;
			$this->dbGame->autocommit(false);
			$status = true;

			if(mb_strlen(trim($h['index'])) < 1 || mb_strlen(trim($h['index'])) > 128){
				$this->vrat .= "
					<div class=\"errormsg\">
						<strong>Index</strong> musí mít min. 1 znak max. 128 znaků
					</div>
					<br />";
				$status = false;
			}

			$sql = "select index_pole,preklad_id from preklady where index_pole='".Help::slash($h['index'])."'";
			$res =& $this->dbGame->query($sql);

			if(DB::isError($res)){
				throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani prekladu z databáze',"admin_ex_db");
			}
			if($row =& $res->fetchRow()){
				$p_id = $row['preklad_id'];
			}

			if($res->numRows() > 0 && $h['index'] != $k){
				$this->vrat .= "
					<div class=\"errormsg\">
						Tento \"Index\" (".Help::Html($h['index']).") již existuje
					</div>
					<br />";
				$status = false;
			}
			$this->dbGame->autocommit(false);

			$sql = "select COALESCE((max(preklad_id)+1),1) as maximal from preklady";
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)){
				throw new ExHandler('Nepodarilo se provest dotaz: vybrani maximalni polozky menu',"admin_ex_db");
			}
			if($row =& $res->fetchRow()){
				$max = intval($row['maximal']);
			}

			foreach($h as $k2=>$h2){   //$k2 = lang_id
				if(!isset($h2['shorttext'])){
					$h2['shorttext'] = "";
				}

				if($k2 != "index"){
					$translate = (isset($_POST['translate'][$k][$k2])?1:0);
					$sql = "delete from preklady where lang_id=".$k2." and index_pole='".Help::Slash($k)."'";
					$res =& $this->dbGame->query($sql);
					if(DB::isError($res)){
						$this->dbGame->rollback();
						$this->dbGame->autoCommit(true);
						throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: aktualizace prekladu',"admin_ex_db");
					}

					$h2['text'] = mb_ereg_replace(";",",",$h2['text']);
//$h2['text'] = mb_ereg_replace("^([^\n|\t|\r]*)[\r|\n|\t]+$","\\1",$h2['text']);
					$h2['text'] = mb_ereg_replace("^[\r|\n|\t]+([^\n|\t|\r]*)[\r|\n|\t]+$","\\1",$h2['text']);
					$sql = "insert into preklady(`preklad_id`,`lang_id`,`index_pole`,`short_text`,`text`,`translate`) values(".$max.",".$k2.",'".Help::Slash($h['index'])."','".Help::Slash($h2['shorttext'])."','".Help::Slash($h2['text'])."',".$translate.")";
					$res =& $this->dbGame->query($sql);
					if(DB::isError($res)){
						$this->dbGame->rollback();
						$this->dbGame->autoCommit(true);
						throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: aktualizace prekladu',"admin_ex_db");
					}

				}
			}

			if(isset($_POST['menu']) && isset($_POST['menu'][$k]) && mb_strlen($_POST['menu'][$k]) > 0){
				$menu = explode(';',$_POST['menu'][$k]);
			}
			else{
				$sql = "select DISTINCT c_id from controller_convert";
				$res =& $this->dbGame->query($sql);
				$menu = array();
				while ($row =& $res->fetchRow()){
					$menu[] = $row['c_id'];
				}
			}

			//TODO: use new translation system
			foreach($menu as $c_id){
				if(intval($c_id) == 0) continue;
				$sql = "replace into controller_preklady(preklad_id,c_id) values (".$p_id.",".$c_id.")";
				$res =& $this->dbGame->query($sql);
	      if(DB::isError($res)){
					$this->vrat .= "
						<div class=\"errormsg\">
							Neplatné Menu ID : ".$h['index']."
						</div>
						<br />";
				}
			}


//check for controller id not in the list and remove corresponding entry from DB
			$sql = "select DISTINCT c_id from controller_convert";
			$res =& $this->dbGame->query($sql);
			$allControllerIDs = array();
			while ($row =& $res->fetchRow()){
				$allControllerIDs[] = $row['c_id'];
			}

			$controllerIDsDiff = array_diff($allControllerIDs, $menu);

			foreach($controllerIDsDiff as $controllerID){
					$sql = "DELETE FROM controller_preklady WHERE c_id = '$controllerID' AND preklad_id= '$p_id'";
					$res =& $this->dbGame->query($sql);
					if(DB::isError($res)){
						$this->vrat .= "
							<div class=\"errormsg\">
								Menu ID : ".$controllerID." se nepodařilo odstranit ze seznamu.
							</div>
							<br />";
					}


			}
			$this->messages[] = i18n::tr('The transaltion {0} was successfuly updated.', $h['index']);

			$_SESSION['lastaction'][time()+$x] = "Editace překladu: ".$h['index'];
			$this->dbGame->commit();

		}
	}

   /**
 * Vytvoreni noveho prekladu
 * @return void
 */
 private function CreatePreklad(){

    $status = true;

    if (mb_strlen(trim($_POST['newindex'])) < 1 || mb_strlen(trim($_POST['newindex'])) > 128) {
		$this->vrat .= "<div class=\"errormsg\"><strong>Index</strong> musí mít min. 1 znak max. 128 znaků</div><br />";
		$status = false;
	}

     $sql = "select index_pole from preklady where index_pole='".Help::slash($_POST['newindex'])."'";
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani prekladu z databáze',"admin_ex_db");

	 if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tento \"Index\" již existuje</div><br />";$status = false;}

	 if($status){

	    $this->dbGame->autocommit(false);

	   $sql = "select COALESCE((max(preklad_id)+1),1) as maximal from preklady";
       $res =& $this->dbGame->query($sql);
	   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vybrani maximalni polozky menu',"admin_ex_db");

	   if($row =& $res->fetchRow()) $max = intval($row['maximal']);

	   foreach($_POST['newpreklad'] as $k=>$h){
	     $h['text'] = mb_ereg_replace(";",",",$h['text']);
	     //$h['text'] = mb_ereg_replace("^([^\n|\t|\r]*)[\r|\n|\t]+$","\\1",$h['text']);
	     $h['text'] = mb_ereg_replace("^[\r|\n|\t]+([^\n|\t|\r]*)[\r|\n|\t]+$","\\1",$h['text']);
	     $translate = (isset($_POST['translate'])?1:0);

	     	 if(mb_strlen($_POST['newmenu']) > 0)
	 	      $menu = explode(';',$_POST['newmenu']);

     else{

     	 $sql = "select DISTINCT c_id from controller_convert";

          $res =& $this->dbGame->query($sql);
	    $menu = array();
     	while ($row =& $res->fetchRow()){

     		$menu[] = $row['c_id'];
     	}
     }


	     try{
	     $sql = "insert into preklady(preklad_id,lang_id,index_pole,short_text,text,translate) values (".$max.",".$k.",'".Help::Slash($_POST['newindex'])."','".Help::Slash($h['shorttext'])."','".Help::Slash($h['text'])."',".$translate.")";
         $res = Zend_Registry::get('zdb_game')->query($sql);
	     }catch(Zend_Exception $e){

        		  throw new ExHandler($e->getMessage(). ': '. __FILE__ .': '. __LINE__ );

         }

	     foreach($menu as $c_id){
	     	if(intval($c_id) == 0) continue;
	       $sql = "replace into controller_preklady(preklad_id,c_id) values (".$max.",".$c_id.")";
          $res =& $this->dbGame->query($sql);
	      if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni prekladu',"admin_ex_db");

	     }

       }

       $this->dbGame->commit();
       $this->messages[] = i18n::tr('Translation {0} was successfuly inserted.', $_POST['newindex']);

		It6_Log::info(
			"New translation '%translation%' was added.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('translation'	=> $_POST['newindex'])
		);
	 }

 }


   /**
 * vyber dat z databaze
 * @param string $where podminka dotazu
 * @return object
 */
  public function selectData($where = "1"){
	
  	if(mb_substr($where,0,5) != 'where' && mb_substr($where,0,5) != 'WHERE')
			$where = " where 1 ".(mb_strlen(trim($where)) == 0?'':$where);

     $sql = "
			SELECT index_pole,text,lang_id
			FROM preklady ". (substr_count($where,"where") > 0?'':' ') ."
			".$where."
			GROUP BY index_pole";

     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

	 return $res;

  }

     /**
 * vyber dat z databaze bez group by
 * @param string $where podminka dotazu
 * @return object
 */
  public function selectData2($where = ""){

  	  	if(mb_strlen($where) == 0) $where = "   ";
  	if(mb_substr($where,0,5) != 'where') $where = " where   ".$where;

     $sql = "select index_pole,text,lang_id from preklady ".$where;
     $res = $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

	 return $res;

  }


 /**
 * Naplnuje pole prekladu
 * @return void
 */
 private function PolePreklady() {
	if($this->section == 72)
		$this->page_num = 1;
	else
		$this->page_num = PAGE;

	$jazyky = new Jazyky($this->section, $this->dbGame);
	$res	= $jazyky->selectData();
	while ($row =& $res->fetchRow()) {
		$this->allLangs[$row['lang_id']] = $row;
		if($row['zobrazeno'] == 1)
			$this->activeLangs[$row['lang_id']] = $row;
	}

	if(empty($_REQUEST['languages']))
		$languages = $this->activeLangs;
	else
		$languages = array_intersect_key($this->allLangs, array_flip($_REQUEST['languages']));
	
	$fullt = (!empty($_REQUEST['fulltext']) ? mb_ereg_replace("_+","\_",$_REQUEST['fulltext']) : null);

	$where = array('1');
	if(isset($_REQUEST['ch_index']))
		$where[] = "index_pole LIKE '%".Help::Slash($fullt)."%' OR short_text LIKE '%".Help::Slash($fullt)."%'";
	elseif(isset($_REQUEST['ch_text']))
		$where[] = "text LIKE '%".Help::Slash($fullt)."%'";
	$where = implode(' AND ', $where);
	
	$sql =
		"SELECT COUNT(*) AS total
		FROM (
			SELECT index_pole
			FROM preklady
			WHERE
				lang_id IN ('".implode("','", array_keys($languages))."')
				AND ".$where." 
			GROUP BY index_pole
		) AS t";
	
	$res = $this->dbGame->query($sql);
	$row = $res->fetchRow();

	$langUrlString = '';
	foreach($languages as $langId => $lang ){
		$langUrlString .= "&languages[]=".$langId;
	}
	
	$this->page = new Page(
		$row['total'],	
		$this->page_num,
		"section=".$this->section
		.(isset($_REQUEST['fulltext']) && mb_strlen($_REQUEST['fulltext']) > 0 ? "&fulltext=".$_REQUEST['fulltext'] : "")
		.(isset($_REQUEST['ch_index']) ? "&ch_index=1" : "")
		.(isset($_REQUEST['ch_text']) ? "&ch_text=1" : "")
		.$langUrlString
	);

	
	$sql =
		"SELECT preklad_id, lang_id, index_pole, short_text, text, translate
		FROM preklady
		WHERE
			lang_id IN ('".implode("','", array_keys($languages))."')
			AND ".$where."	
		ORDER BY index_pole
		LIMIT ".($this->page_num * $this->page->page * count($languages)).", ".$this->page_num * count($languages);

	$res =& $this->dbGame->query($sql);
	if(DB::isError($res))
		throw new ExHandler($sql.$res->getMessage(), "admin_ex_db");


	while ($row =& $res->fetchRow()) {
		$this->preklady[$row['index_pole']][$row['lang_id']]['shorttext']	= $row['short_text'];
		$this->preklady[$row['index_pole']][$row['lang_id']]['text']		= $row['text'];
		$this->preklady[$row['index_pole']][$row['lang_id']]['translate']	= $row['translate'];
	}
 }


 /**
 * Zobrazeni vsechny preklady
 * @return sting
 */
 public function ShowPreklady() {
 	if(empty($_REQUEST['languages']))
 		$languages = $this->activeLangs;
 	else
 		$languages = array_intersect_key($this->allLangs, array_flip($_REQUEST['languages']));
 	
	$langList = '';
	if (!empty($languages))
		$langList = '"' . implode('","', array_keys($languages)) . '"';

	
	$this->vrat .= UiUtil::printMessages($this->messages);
	$this->vrat .= UiUtil::printMessages($this->errors);
	$this->vrat .= 
		'<script type="text/javascript">
			function sendUpdateOne(id) {
				var langs = [' . $langList . '];
				var prefix = "preklad[" + id + "]"; 
				var data = { "updateOne": 1 };
				data["menu[" + id + "]"] = $(":input[name=\'menu[" + id + "]\']").val();
				data[prefix + "[index]"] = $(":input[name=\'preklad[" + id + "][index]\']").val();
				for (var i in langs) {
					var lang = langs[i];
					data[prefix + "[" + lang + "][text]"] = $(":input[name=\'preklad[" + id + "][" + lang + "][text]\']").val();
					if ( $(":input[name=\'translate[" + id + "][" + lang + "]\']").is(":checked") )
						data["translate[" + id + "][" + lang + "]"] = ($(":input[name=\'translate[" + id + "][" + lang + "]\']").is(":checked") ? 1 : 0);
				}
				$.post(
					"/?section=' . $this->section . '",
					data,
					function(data) {
						$("#dynMessages").html(data);
					},
					"html"
				);
			}
		</script>
		<div id="dynMessages"></div>
		
		<form method="post"  action="?section='.$this->section	.'">
			<table class="table-filter">
				<caption>'.I18n::tr('Filter').'</caption>
				<tr>
					<td>'.I18n::tr('Fulltext').'</td>
					<td>
						<input
							type="text"
							name="fulltext"
							value="'.(isset($_REQUEST['fulltext']) ? Help::Html($_REQUEST['fulltext']) : '').'"
						/>
					</td>
										<td colspan=2>
						'.I18n::tr('Key').'
						<input
							type="checkbox"
							class="no"
							name="ch_index"
							'.((!isset($_REQUEST['ch_index']) && !isset($_REQUEST['ch_text'])) || isset($_REQUEST['ch_index'])
								? 'checked="checked"'
								: ''
							).'
						/>
						Text:
						<input
							type="checkbox"
							class="no"
							name="ch_text"
							'.(isset($_REQUEST['ch_text']) ? 'checked="checked"' : "").'
						/>
					</td>
				</tr>
				<tr>
					<td>
						'.I18n::tr('Language').'
					</td>
					<td>
						<select multiple="multiple" name="languages[]">';
	foreach($this->allLangs as $langId => $lang) {
		$this->vrat .=
							'<option
								value="'.$langId.'"
								'.(in_array($langId, array_keys($languages)) ? 'selected="selected"' : '').'
							>
								'.$lang['alt_text'].'
								'.($lang['zobrazeno'] == 1 ? '('.I18n::tr('active').')' : '').'
							</option>';
	}
	$this->vrat .=
						'</select>
					</td>
					<td>
						<input type="submit" value="Vyhledat" />
					</td>
				</tr>
			</table>
		
			<div class="actions">
				'.$this->page->getPage().'
				('.($this->page->numRows<count($this->preklady) ? count($this->preklady) : $this->page->numRows).')
			</div>
			
			<table class="table-list">
				<thead>
					<tr>
						<th>Id</th>
						<th>Klíč</th>
						<th colspan="3">&nbsp;</th>
					</tr>
				</thead>
				<tbody>';

	$x = 1;
	if($this->section == 46 || isset($_REQUEST['fulltext'])) {
		foreach($this->preklady as $k=>$h){
			$this->vrat .=
					'<tr>
						<td>'.($x + ($this->page_num * $this->page->page)).'</td>
						<td>
							<input type="text" name="preklad['.Help::Html($k).'][index]" value="'.Help::Html($k).'" />
						</td>
						<td>
							<input
								type="button"
								name=""
								style="width:240px;"
								onclick="var oo = new getObj(\'preklad'.$x.'\');displayObj(oo);this.value=(oo.style.display==\'\'?\'Skrýt ['.Help::Script($k).']\':\'Zobrazit ['.Help::Script($k).']\')"
								value="Zobrazit ['.Help::Html($k).']"
							/>
						</td>
						<td>
							<input
								type="submit"
								value="Smazat"
								onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($k).'?\')) return false;else document.forms[0].action=\'?section='.$this->section.'&index='.urlencode($k).'\'"
								name="delete"
							/>
						</td>
						<td>
							<input type="button" value="Uložit" onclick="sendUpdateOne(\''.$k.'\');" name="updateOne" />
						</td>
					</tr>
					<tr id="preklad'.$x.'" style="display:none">
						<td colspan="4">
							<table>';
			$cc = 0;
			foreach($languages as $langId => $lang){
				$this->vrat .=
								'<tr>
									<td>
										<strong>'.$lang['alt_text'].':</strong>
										'.($cc == 0
											? '<input
												type="checkbox"
												'.($this->preklady[$k][$langId]['translate'] == 1 ? 'checked="checked"':'').'
												name="translate['.Help::Html($k).']['.$langId.']"
												class="no" />'
											: ''
										).'
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<textarea
											class="tinymce"
											cols="80"
											name="preklad['.Help::Html($k).']['.$langId.'][text]"
											rows="24"
										>'
											.(!isset($this->preklady[$k][$langId]['text']) ? '' : $this->preklady[$k][$langId]['text']).
										'</textarea>
									</td>
								</tr>';
				$cc++;
			}
	
			$this->vrat .=
							'</table>
						</td>
					</tr>';
					
			$x++;
		}
	}
	
	$this->vrat .=
					'<tr>
						<td colspan="2" class="">
							<br />
							<input type="submit" style="width:280px;" name="editall" value="Uložit změny" />
						</td>
						<td colspan="2" class="">&nbsp;</td>
					</tr>
				</tbody>
			</table>
		
			<div class="actions">
				'.$this->page->getPage().'
				('.($this->page->numRows<count($this->preklady) ? count($this->preklady) : $this->page->numRows).')
			</div>
		
			<table class="table-list">
				<tr>
					<th colspan="4"><strong>Nový Překlad</strong></th>
				</tr>
				<tr>
					<td>'.I18n::tr('Key').'</td>
					<td>
						<input
							type="text"
							class="mandatory"
							maxlength="128"
							name="newindex"
							value="'.(isset($_POST['newindex']) ? Help::Html($_POST['newindex']) : '').'"
						/>
					</td>
					<td colspan="2">
						<input type="submit" name="create" class="sbutton" value="Vytvořit" />
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<table>';
			
	$cc = 0;
	foreach($this->activeLangs as $langId => $lang) {
		$this->vrat .=
							'<tr>
								<td>&nbsp;</td>
								<td>
									'.$lang['alt_text'].':
									'.($cc == 0 ? '<input type="checkbox"  name="translate" class="no" />' : '').'
								</td>
								<td	>
									<textarea cols="40" name="newpreklad['.$langId.'][text]" rows="6"></textarea>
								</td>
							</tr>';
		$cc++;
	 }

	$this->vrat .=
						'</table>
					</td>
				</tr>
			</table>
		</form>
		<form method="post" enctype="multipart/form-data" action="">
			<div class="actions">
				Import:
				  <input type="file" style="width:280px;" name="fileimport" />
				  <input type="submit" style="" name="import" value="Import" />
				  Import pouze neexistujicich indexu:
				  <input type="checkbox" name="importnew" class="no" />
				  <br /><br />
			</div>
			<div class="actions">
				<input type="submit" style="" name="export" value="Export" />
			</div>
		</form>';
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
