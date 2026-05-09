<?php
include_once('class.AbstractSection.php');
include_once('class.Url.php');
include_once('class.DbUtil.php');
include_once(ROOT.'admin/config_local.php');
include_once(ROOT.'common/config.php');
include_once(ROOT.'common/pear/DB.php');
include_once('class.I18n.php');
include_once(ROOT.'common/class/class.Help.php');

class Galerie extends AbstractSection{
const PARAM_NEW = 'new';
const PARAM_EDIT = 'edit';
const PARAM_DELETE = 'delete';

const CONTROLLER = 'static-page';
	/**
	 * spojeni na databazi game
	 * @access private
	 * @var DB
	 */
	private $dbGame;

	/**
	 * chybova hlaska xml rpc nahrani souboru
	 * @access private
	 * @var string
	 */
	private $xml_rpc_log = '';

	/**
	 * detekce zda chceme vlozit obrazek do wysiwigu
	 * @access private
	 * @var boolean
	 */
	private $popup = false;

	/**
	 * prvek do ktereho se ma vratit nazev obrazku
	 * @access private
	 * @var string
	 */
	private $node = false;

	public function __construct($section=null) {

		$this->dbGame = DbUtil::connectWebDb();
		parent::__construct($section);
	}

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){

   #Vytvoreni noveho menu#
   if(isset($_POST['cgm']) && isset($_POST['new_gmenu']) && mb_strlen($_POST['new_gmenu']) > 0 && mb_strlen($_POST['new_gmenu']) <= 100 && $this->update){

	 $this->CreateGMenu();

   }
   #Vymazani noveho menu#
   else if(isset($_GET['gmenud']) && $this->delete){

	 $this->DeleteGMenu();

   }

   #Vlozeni noveho obrazku#
   else if(isset($_POST['insertimage']) && $this->update){

	 $this->InsertImage();

   }

   #Vymazani obrazku#
   else if(isset($_POST['deleteimage']) && $this->delete && is_array($_POST['deleteimage']) && count($_POST['deleteimage']) == 1){

	 $this->DeleteImage(intval(key($_POST['deleteimage'])));

   }

   #Editovani obrazku#
   else if(isset($_POST['editimage']) && $this->update && is_array($_POST['editimage']) && count($_POST['editimage']) == 1){

	 $this->EditImage(intval(key($_POST['editimage'])));

   }

   $this->ShowGalery();

   $this->dbGame->disconnect();

  }

  /**
 * Editace obrazku
 * @param int $im_id id souboru
 * return void
 */
  private function EditImage($im_id){

   $this->dbGame->autoCommit(false);


   if(isset($_POST['popis'][$im_id])){

	  $sql = "update galerie set popis='".Help::Slash($_POST['popis'][$im_id])."' where image_id=".$im_id;
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler($sql.'Nepodarilo se provest dotaz: update galerie',"admin_ex_db");}

   }

   if(isset($_POST['alt'][$im_id]) && is_array($_POST['alt'][$im_id])){

	  foreach($_POST['alt'][$im_id] as $k=>$h){

	     $sql = "replace into galerie_alt (image_id,lang_id,alt) values (".$im_id.",".$k.",'".Help::Slash($h)."')";
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update galerie',"admin_ex_db");}

	  }

   }
   
	$sql = "delete from galerie_prirad_menu where image_id = $im_id";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Neuspech pri smazani menu.',"admin_ex_db");}
		if (!empty($_POST['galery_menu'][$im_id])) foreach ($_POST['galery_menu'][$im_id] as $gmId) {
			$sql = "insert into galerie_prirad_menu set galerie_menu_id = ".intval($gmId).", image_id = $im_id";
			$res =& $this->dbGame->query($sql);
			if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Neuspech pri ulozeni menu.',"admin_ex_db");break;}
	}
	
   $this->vrat .= "<div class=\"okmsg\">".I18n::tr('gallery_edit_ok')."</div><br />";

		It6_Log::info(
			"Galery file '%file%' was updated.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('file' => $im_id)
		);
   $this->dbGame->autoCommit(true);
   $this->dbGame->commit();

  }


   /**
 * Vymazani obrazku
 * @param int $im_id id souboru
 * return void
 */
  private function DeleteImage($im_id){

   $status = true;

   $this->dbGame->autoCommit(false);

   $sql = "select name from galerie where image_id=".$im_id;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky udalost',"admin_ex_db");}

   if ($row =& $res->fetchRow()) $name = $row['name'];else {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: zjisteni jmena obrazku',"admin_ex_page");}

   $sql = "delete from galerie where image_id=".$im_id;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky udalost',"admin_ex_db");}

   $ftp = new It6_FtpSync_Client();
   $res = $ftp->delete("/images/gallery/$name");
   //if($this->XML_RPC_DELETE($name)){
   if ($ftp->allResultsOK($res)) {

     $imgPath = GALLERY_PATH . $name;
     $imgDeleted = (file_exists($imgPath) && unlink($imgPath));
     $thumbPath = GALLERY_THUMB_PATH . $name;
     $thumbDeleted = (file_exists($thumbPath) && unlink($thumbPath));
     if($imgDeleted && $thumbDeleted){

	   $this->vrat .= "<div class=\"okmsg\">".I18n::tr('gallery_delete_ok')."</div><br />";

		It6_Log::info(
			"Galery file and thumbnail '%file%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('file' => $im_id)
		);
	 }else $this->vrat .= "<div class=\"errormsg\">Nepodařilo se vymazat obrázek a náhled</div><br />";

   }else {
		//$this->vrat .= "<div class=\"errormsg\"> Nepodarilo se vymazat obrázek:".Help::Html($this->xml_rpc_log)." </div><br />";
		$this->vrat .= '<div class="errormsg">'.I18n::tr('gallery_delete_error');
		foreach ($res as $host => $result)
			$this->vrat .= "<br />$host : " . ($result ? 'OK' : I18n::tr('error'));
		$this->vrat .= '</div><br />';
   }
   $ftp->disconnect();

   $this->dbGame->autoCommit(true);
   $this->dbGame->commit();

  }

 /**
 * Vlozeni obrazku
 * return void
 */
  private function InsertImage(){

   $status = true;
   //echo $_FILES['new_file']['type'];
   if(!isset($_FILES['new_file']['tmp_name']) || mb_strlen($_FILES['new_file']['name']) < 1) {$this->vrat .= "<div class=\"errormsg\"> ".I18n::tr('gallery_select_file')." </div><br />";$status = false;}
   if(isset($_FILES['new_file']['tmp_name']) && ($_FILES['new_file']['size']/1000000) > 2 ) {$this->vrat .= "<div class=\"errormsg\"> ".I18n::tr('gallery_max_size_info')." 4 MB </div><br />";$status = false;}
   if(!isset($_FILES['new_file']['type']) || (
      substr_count($_FILES['new_file']['type'],'application/x-shockwave-flash') < 1
      && substr_count($_FILES['new_file']['type'],'image/gif') < 1
      && substr_count($_FILES['new_file']['type'],'image/jpeg') < 1
      && substr_count($_FILES['new_file']['type'],'image/png') < 1
      && substr_count($_FILES['new_file']['type'],'image/x-png') < 1
      && substr_count($_FILES['new_file']['type'],'image/pjpeg') < 1
   )) {
      $this->vrat .= "<div class=\"errormsg\"> ".I18n::tr('gallery_allowed_formats')."  jpeg | gif | png |  swf </div><br />";
      $status = false;
   }

   if($status){

	$realName = null;
	if(mb_strlen($_POST['new_name']) < 1 || mb_strlen($_POST['new_name']) > 100)
		$name =  $_FILES['new_file']['name'];
	else {
		$realName = $_POST['new_name'];
		$name = $realName . strstr($_FILES['new_file']['name'],'.');
	}

	$dstPath = GALLERY_PATH . $name;
	if (file_exists($dstPath)) {
		$this->vrat .= "<div class=\"errormsg\">".I18n::tr('gallery_file_exists')."</div><br />";
		return false;
	}
	
//	print_r($_FILES);
	move_uploaded_file($_FILES['new_file']['tmp_name'], ROOT."web/www/images/gallery/" . $_FILES['new_file']['name']);

	$ftp = new It6_FtpSync_Client();
	$res = $ftp->upload(array($_FILES['new_file']['tmp_name'] => "/images/gallery/$name"), false);
	//if($this->XML_RPC($name,$_FILES['new_file']['tmp_name'])){
	if ($ftp->allResultsOK($res, 2)) {
	  $this->dbGame->autoCommit(false);

	  $sql = "insert into galerie (name,popis) values('".Help::Slash($name)."','".Help::Slash($_POST['new_popis'])."')";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky udalost',"admin_ex_db");}

	  $sql = "select MAX(image_id) AS maxi from galerie";
      $res =& $this->dbGame->query($sql);
      if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky udalost',"admin_ex_db");}

	  if ($row =& $res->fetchRow()) $id = $row['maxi'];else{$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky udalost',"admin_ex_db");}

	  if(isset($_POST['galery_menu'][0]) && is_array($_POST['galery_menu'][0]) && !empty($_POST['galery_menu'][0])){

	   foreach($_POST['galery_menu'][0] as $gmId){

		 $sql = "insert into galerie_prirad_menu (image_id,galerie_menu_id) values (".$id.",".$gmId.")";
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky udalost',"admin_ex_db");}

	   }

	  }

	  if(isset($_POST['new_alt']) && is_array($_POST['new_alt'])){

	    foreach($_POST['new_alt'] as $k=>$h){

		 $sql = "insert into galerie_alt (image_id,lang_id,alt) values (".$id.",".$k.",'".Help::Slash($h)."')";
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky udalost',"admin_ex_db");}

	    }

	  }


	  //if(substr_count($_FILES['new_file']['type'],'application/x-shockwave-flash') < 1) $this->SImage($name,$_FILES['new_file']['tmp_name'],(substr_count($_FILES['new_file']['type'],'image/gif') > 0?1:(substr_count($_FILES['new_file']['type'],'image/png')?4:2)));
	 // $ret = $this->saveImageFiles($_FILES['new_file'], $realName);
	  //if (true !== $ret)
//	  	$this->vrat .= "<div class=\"errormsg\">$ret</div><br />";

	  $this->vrat .= "<div class=\"okmsg\">".I18n::tr('gallery_insert_ok')."</div><br />";

		It6_Log::info(
			"File '%file%' was added to galery.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('file' => $id)
		);
	  $this->dbGame->autoCommit(true);
      $this->dbGame->commit();

	}else {
	     //$this->vrat .= "<div class=\"errormsg\"> ".Help::Html($this->xml_rpc_log)." </div><br />";
		$this->vrat .= '<div class="errormsg">'.I18n::tr('gallery_upload_error');
		foreach ($res as $local => $hosts) {
			foreach ($hosts as $host => $result) {
				if (true === $result)
					$msg = 'OK';
				else if (null === $result)
					$msg = I18n::tr('gallery_file_exists');
				else
					$msg = I18n::tr('error');
				$this->vrat .= "<br />$host : $msg";
			}
		}
		$this->vrat .= '</div><br />';
	}

	$ftp->disconnect();
   }

  }

	/**
	 * Saves image thumbnail
	 * @param array $file Same data as entry in $_FILES superglobal array
	 * @param string File base name that can override uploaded file base name
	 * @return boolean|string TRUE or error message
	 */
	private function saveImageFiles($file, $realName = null) {
		if (empty($realName))
			$realName = $file['name'];
		else
			$realName .= strstr($file['name'], '.');
		
		$srcPath = $file['tmp_name'];
		$dstPath = GALLERY_THUMB_PATH . $realName;
		$types = array(
			array(
				'name' => 'gif',
				'mimeTypes' => array('image/gif'),
				'createFn' => 'imagecreatefromgif',
				'saveFn' => 'imagegif',
			),
			array(
				'name' => 'jpg',
				'mimeTypes' => array('image/jpg', 'image/jpeg', 'image/pjpeg'),
				'createFn' => 'imagecreatefromjpeg',
				'saveFn' => 'imagejpeg',
			),
			array(
				'name' => 'png',
				'mimeTypes' => array('image/png', 'image/x-png'),
				'createFn' => 'imagecreatefrompng',
				'saveFn' => 'imagepng',
			),
		);
		$type = null;
		foreach ($types as $_type) {
			foreach ($_type['mimeTypes'] as $substr) {
				if (0 < substr_count($file['type'], $substr)) {
					$type = $_type;
					break;
				}
			}
			if (isset($type))
				break;
		}
		if (!isset($type))
			return "Unknown image type for resizing ({$file['type']})";
		if (!function_exists($type['saveFn']))
			return "Image type not supported by PHP ({$type['name']})";

		$img = call_user_func($type['createFn'], $srcPath);
		$wi = imagesx($img);
		$hi = imagesy($img);
		$wt = 90;
		$ht = 70;
		$thumb = @imagecreatetruecolor($wt, $ht);
		imagecopyresampled($thumb, $img, 0, 0, 0, 0, $wt, $ht, $wi, $hi);
		if (!call_user_func_array($type['saveFn'], array($thumb, $dstPath)))
			return "Image thumbnail not saved: '$dstPath'";

		$dstPath = GALLERY_PATH . $realName;
		if (!move_uploaded_file($srcPath, $dstPath))
			return "Image not saved: '{$dstPath}'";
		return true;
	}

 /**
 * @deprecated
 * Vytvori maly nahled obrazku
 * @param string $file_name jmeno souboru
 * @param string $file_path cesta k souboru
 * @param int $type  1=gif 2 jpeg
 * return boolean
 */
  private function SImage($file_name,$file_path,$type){

  	if($type == 2) $im2 = imagecreatefromjpeg($file_path);
  	if($type == 4) $im2 = imagecreatefrompng($file_path);
 	else $im2 = imagecreatefromgif ($file_path);

	$im = @imagecreatetruecolor(90, 70);
 	$dw=imagesx($im);
 	$dh=imagesy($im);
 	$sw=imagesx($im2);
 	$sh=imagesy($im2);
 	imagecopyresampled ( $im, $im2, 0,0,0,0,$dw,$dh,$sw,$sh);

	if (function_exists("imagejpeg")) {
    	header("Content-type: image/jpeg");
    	imagejpeg($im,$_SERVER["DOCUMENT_ROOT"].'_img/gallery/small/'.$file_name);
	}
	elseif (function_exists("imagegif")) {

    	header("Content-type: image/gif");
    	imagegif($im,$_SERVER["DOCUMENT_ROOT"].'_img/gallery/small/'.$file_name);
	}
	elseif (function_exists("imagepng")) {
    	header("Content-type: image/gif");
    	imagepng($im,$_SERVER["DOCUMENT_ROOT"].'_img/gallery/small/'.$file_name);
	}
	else {
      die("No image support in this PHP server");
    }

  }

  /**
   * @deprecated replaced by It6_FtpSync_Client
 * Odeslani souboru na server
 * @param string $file_name jmeno souboru
 * return boolean
 */
  private function XML_RPC_DELETE($file_name){

   require_once 'XML/RPC.php';

   $fault = "";

   $img_name = $file_name; //jmeno obrazku na serveru

   $params = array(new XML_RPC_Value(XML_RPC_NAME),new XML_RPC_Value(XML_RPC_PASS));
   $msg = new XML_RPC_Message('SetConnect', $params);

   $cli = new XML_RPC_Client(XML_RPC_PATH,XML_RPC_HOST,XML_RPC_PORT);
   $cli->setDebug(0);
   $resp = $cli->send($msg);
   //print_r($cli);

   if (!$resp) {
      $fault .= 'Communication error1: ' . $cli->errstr;
   }

   if (strlen($fault)<1 && !$resp->faultCode()) {

	  $session = XML_rpc_decode($resp->value());

	  $params = array(new XML_RPC_Value($session),new XML_RPC_Value($img_name));
      $msg = new XML_RPC_Message('DeleteIm', $params);

	  $resp = $cli->send($msg);

      if (!$resp) {
         $fault .= 'Communication error2: ' . $cli->errstr;
      }

	  if (strlen($fault)<1 && !$resp->faultCode()) ;
	  else{
          if(strlen($fault)<1) $fault .= $resp->faultString();
	  }

   } else {
      if(strlen($fault)<1) $fault .= $resp->faultString();
   }

   if(isset($session)){

     #Ukonceni na serveru#
     $params = array(new XML_RPC_Value($session));
     $msg = new XML_RPC_Message('Unload',$params);
     $resp = $cli->send($msg);
     if (!$resp) {
        $fault .= 'Communication error3: ' . $cli->errstr;
     }

     if (strlen($fault)<1 && !$resp->faultCode()) ;
     else {
        if(strlen($fault)<1)$fault .= $resp->faultString();
     }

   }

   $this->xml_rpc_log = $fault;

   if(strlen($fault) < 1) return true;
   else return false;

  }


  /**
   * @deprecated replaced by It6_FtpSync_Client
 * Odeslani souboru na server
 * @param string $file_name jmeno souboru
 * @param string $file_path cesta k souboru
 * return boolean
 */
  private function XML_RPC($file_name,$file_path){

   require_once 'XML/RPC.php';

   $fault = "";

   $img_name = $file_name; //jmeno obrazku na serveru
   $obr = $file_path;  //cesta k obrazku

   $params = array(new XML_RPC_Value(XML_RPC_NAME),new XML_RPC_Value(XML_RPC_PASS));
   $msg = new XML_RPC_Message('SetConnect', $params);

   $cli = new XML_RPC_Client(XML_RPC_PATH,XML_RPC_HOST,XML_RPC_PORT);
   $cli->setDebug(0);
   $resp = $cli->send($msg);
   //print_r($cli);

   if (!$resp) {
      $fault .= 'Communication error1: ' . $cli->errstr;
   }

   if (strlen($fault)<1 && !$resp->faultCode()) {

	  $session = XML_rpc_decode($resp->value());

      $fp = fopen($obr,"rb");
	  if(!$fp) throw new ExHandler('Nepodarilo se otevrit soubor xml rpc',"admin_ex_page");
      $data = base64_encode(fread($fp,filesize($obr)));
      fclose($fp);

	  $params = array(new XML_RPC_Value($session),new XML_RPC_Value($img_name),new XML_RPC_Value($data));
      $msg = new XML_RPC_Message('SetImage', $params);

	  $resp = $cli->send($msg);

      if (!$resp) {
         $fault .= 'Communication error2: ' . $cli->errstr;
      }

	  if (strlen($fault)<1 && !$resp->faultCode()) ;
	  else{
          if(strlen($fault)<1) $fault .= $resp->faultString();
	  }

   } else {
      if(strlen($fault)<1) $fault .= $resp->faultString();
   }

   if(isset($session)){

     #Ukonceni na serveru#
     $params = array(new XML_RPC_Value($session));
     $msg = new XML_RPC_Message('Unload',$params);
     $resp = $cli->send($msg);
     if (!$resp) {
        $fault .= 'Communication error3: ' . $cli->errstr;
     }

     if (strlen($fault)<1 && !$resp->faultCode()) ;
     else {
        if(strlen($fault)<1)$fault .= $resp->faultString();
     }

   }

   $this->xml_rpc_log = $fault;

   if(strlen($fault) < 1) return true;
   else return false;

  }

  /**
 * Vymazani kategorie obrazku
 * return void
 */
  private function DeleteGMenu(){

   	$sql = "delete from galerie_menu where galerie_menu_id=".intval($_GET['gmenud']);
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'<br>'.I18n::tr('gallery_category_delete_error'),"admin_ex_db");

	if($this->dbGame->affectedRows()){

	 $this->vrat .= "<div class=\"okmsg\">".I18n::tr('gallery_category_delete_ok')."</div><br />";

		It6_Log::info(
			"Image and object category was deleted: '%menu%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('menu' => intval($_GET['gmenud']))
		);
   }

  }


  /**
 * Vytvoreni kategorie obrazku
 * return void
 */
  private function CreateGMenu(){

   	$sql = "insert into galerie_menu (nazev) values ('".Help::Slash($_POST['new_gmenu'])."')";
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'<br>'.I18n::tr('gallery_category_insert_error'),"admin_ex_db");

	$this->vrat .= "<div class=\"okmsg\">".I18n::tr('gallery_category_insert_ok')."</div><br />";

		It6_Log::info(
			"Image and object menu was created: '%menu%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('menu' => $_POST['new_gmenu'])
		);
  }

 /**
 * Zobrazeni ulozenych obrazku
 * return string
 */
  private function GaleryFiles(){

   $image = $ids = $j = array();
   $vrat = $script_alt = '';
   if($this->popup && !$this->node) $script_alt = 'alt = new Object();';


   $where = "1 and";
   if(isset($_REQUEST['gmenu']) && $_REQUEST['gmenu'] != "all") $where = " e.galerie_menu_id=".intval($_REQUEST['gmenu'])." and";
   if(isset($_REQUEST['search']) && mb_strlen($_REQUEST['search']) > 0) $where = "( e.popis like '%".Help::Slash($_REQUEST['search'])."%' or e.name like '%".Help::Slash($_REQUEST['search'])."%') and";

   $where = substr($where,0,-3);

   $orderDir = 'desc';
   $sql = "select e.image_id from galerie_pohled e where ".$where." group by e.image_id order by e.image_id $orderDir";
   $res2 =& $this->dbGame->query($sql);
   if(DB::isError($res2)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber galerie',"admin_ex_db");

   $page = new Page($res2->numRows(),PAGE,"node=".($this->node)."&search=".(isset($_REQUEST['search']) && mb_strlen($_REQUEST['search']) > 0?$_REQUEST['search']:"")."&section=".$this->section.($this->popup?"&type=1":"").(isset($_REQUEST['gmenu'])?"&gmenu=".intval($_REQUEST['gmenu']):""));

   $sql = "select e.image_id from galerie_pohled e where ".$where." group by e.image_id order by e.image_id $orderDir limit ".(PAGE*$page->page).",".PAGE;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber galerie',"admin_ex_db");

   while ($row =& $res->fetchRow()){
    $ids[] = $row['image_id'];
   }

   if(count($ids) > 0){

     $sql = "select a.image_id,a.name,a.popis,a.galerie_menu_id,a.alt,a.lang_id,c.alt_text from galerie_pohled a inner join jazyky c on a.lang_id=c.lang_id where a.image_id in (".implode(",",$ids).") order by a.image_id $orderDir";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber galerie',"admin_ex_db");

   }

   $vrat .= '<div style="float: left; clear: right; width: 100%;">';
   while ($row =& $res->fetchRow()){

	if(!isset($image[$row['image_id']])){
	  $image[$row['image_id']]['name'] = $row['name'];
	  $image[$row['image_id']]['popis'] = $row['popis'];
      if($this->popup && !$this->node) $script_alt .= 'alt['.$row['image_id'].'] = new Object();';
	}

	if(!isset($j[$row['lang_id']])) $j[$row['lang_id']] = $row['alt_text'];

    if(is_numeric($row['galerie_menu_id'])) $image[$row['image_id']]['menu'][$row['galerie_menu_id']] = 1;
	if(is_numeric($row['lang_id'])) $image[$row['image_id']]['alt'][$row['lang_id']] = $row['alt'];

	if($this->popup && !$this->node) $script_alt .= 'alt['.$row['image_id'].']['.$row['lang_id'].'] = "'.Help::Script($row['alt']).'";';

   }

   $vrat .= $page->getPage().' '.I18n::tr('gallery_count').': '.$page->numRows;
   //$vrat .= '<table>';

   if($this->popup && !$this->node){
    $vrat .= '<br /><strong>'.I18n::tr('gallery_alt').':</strong> ';
	foreach($j as $k=>$h){
     $vrat .= Help::Html($h).': <input type="radio" name="altt" onclick="SetAlt('.$k.')"  />';
	}

   }

   if($this->popup){

    $vrat .= '<script language="Javascript" type="text/javascript">
                 '.$script_alt.'

				 function ReturnImageToNode(node,value){

				  window.opener.document.getElementById(node).value = value;

				  self.close();

				 }
             </script>';

   }
   $vrat .= '</div>';

   foreach($image as $k=>$h){

	$vrat .= '<table class="table-detail gallery-file-table" style="width:180px;">';

	//if(!$this->popup)$vrat .= '<tr><td colspan="2">'.(file_exists(SITE_WEB_ROOT.GALLERY_THUMB_PATH.$h['name'])?'<a href="'.PROTOCOL.XML_RPC_HOST.XML_RPC_IMAGEPATH.$h['name'].'" target="_blank"><img src="'.PROTOCOL.WEBHOST.GALLERY_THUMB_PATH.$h['name'].'" class="img" /></a>':'<a href="'.PROTOCOL.XML_RPC_HOST.XML_RPC_IMAGEPATH.$h['name'].'" target="_blank"><img src="_img/noimage.gif" class="img" /></a>').'</td></tr>';
	//else if($this->node) $vrat .= '<tr><td colspan="2">'.(file_exists(SITE_WEB_ROOT.GALLERY_THUMB_PATH.$h['name'])?'<a href="javascript:ReturnImageToNode(\''.$this->node.'\',\''.$h['name'].'\')"><img src="'.PROTOCOL.WEBHOST.GALLERY_THUMB_PATH.$h['name'].'" class="img" /></a>':'<img src="_img/noimage.gif" class="img" />').'</td></tr>';
	//else $vrat .= '<tr><td colspan="2">'.(file_exists(SITE_WEB_ROOT.GALLERY_THUMB_PATH.$h['name'])?'<a href="javascript:ChooseImage(\''.WEBHOST.XML_RPC_IMAGEPATH.$h['name'].'\','.$k.')"><img src="'.PROTOCOL.WEBHOST.GALLERY_THUMB_PATH.$h['name'].'" class="img" /></a>':'<img src="_img/noimage.gif" class="img" />').'</td></tr>';
	$imgOk = file_exists(GALLERY_PATH . $h['name']);
	$thumbOk = file_exists(GALLERY_THUMB_PATH . $h['name']);
	$vrat .= '<tr><td colspan="2">';
	if (!$this->popup)
		$vrat .= ($imgOk ? '<a href="'.PROTOCOL.ADMINHOST.GALLERY_URL.$h['name'].'" target="_blank"><img src="'.PROTOCOL.ADMINHOST.GALLERY_THUMB_URL.$h['name'].'" class="img" /></a>':'<img src="_img/noimage.gif" class="img" />');
	else if ($this->node)
		$vrat .= ($thumbOk ? '<a href="javascript:ReturnImageToNode(\''.$this->node.'\',\''.$h['name'].'\')"><img src="'.PROTOCOL.ADMINHOST.GALLERY_THUMB_URL.$h['name'].'" class="img" /></a>':'<img src="_img/noimage.gif" class="img" />');
	else
		$vrat .= ($thumbOk ? '<a href="javascript:ChooseImage(\''.ADMINHOST.GALLERY_THUMB_URL.$h['name'].'\','.$k.')"><img src="'.PROTOCOL.ADMINHOST.GALLERY_THUMB_URL.$h['name'].'" class="img" /></a>':'<img src="_img/noimage.gif" class="img" />');
	$vrat .= '</td></tr>';

	if($this->popup)  $vrat .= '<tr><td>'.I18n::tr('name').':</td><td>'.Help::Html($h['name']).'</td></tr>';
	if(!$this->popup) $vrat .= '<tr><td>'.I18n::tr('gallery_file_name').':</td><td><input type="text" readonly="readonly" name="name['.$k.']" value="'.Help::Html($h['name']).'" maxlength="100" /></td></tr>';
	if(!$this->popup) $vrat .= '<tr><td>'.I18n::tr('gallery_file_description').':</td><td><input type="text" name="popis['.$k.']" value="'.Help::Html($h['popis']).'" maxlength="255" /></td></tr>';
	if(!$this->popup) $vrat .= '<tr><td colspan="2">'.I18n::tr('gallery_alt_text').':</td><td>&nbsp;</td></tr>';
	if(!$this->popup) $vrat .= '<tr><td colspan="2">'.$this->Alt((isset($image[$k]['alt'])?$image[$k]['alt']:NULL),$k).'</td></tr>';
	if(!$this->popup) $vrat .= '<tr><td>'.I18n::tr('gallery_category').':</td>
									<td>' . $this->getGaleryMenuOptionsHtml($k) . '</td>
								</tr>';
	if(!$this->popup) $vrat .= '<tr><td><input type="submit" class="sinput" name="editimage['.$k.']" value="'.I18n::tr('gallery_edit').'"></td>'
		.'<td><input type="submit" class="sinput" name="deleteimage['.$k.']" value="'.I18n::tr('gallery_delete').'" onclick="if(!confirm(\''.I18n::tr('gallery_really_delete').'\')) return false;"></td></tr>';
    $vrat .= '</table>';

   }

   return $vrat;

  }

  /**
 * Zobrazeni obrázků a objektů
 * return void
 */
  public function ShowGalery(){

	#popup s wysiwyg editoru#
    if(isset($_GET['type']) && $_GET['type']) $this->popup = true;
    if(isset($_GET['node'])) $this->node = $_GET['node'];

	$gmenuParam = isset($_GET['gmenu']) ? '&gmenu='.$_GET['gmenu'] : '';
	
    $this->vrat .= "<form method=\"post\" enctype=\"multipart/form-data\" action=\"?section=".$this->section.$gmenuParam."\">";
	if($this->popup) $this->vrat .= 'XXXXXXXXX<input type="hidden" name="type" value="1" />';
	if($this->node) $this->vrat .= '<input type="hidden" name="node" value="'.Help::Html($this->node).'" />';

	$this->vrat .= '<table class="table-filter"><tr><td>'.I18n::tr('gallery_search').': <input type="text" name="search" value="'.(isset($_REQUEST['search']) && mb_strlen($_REQUEST['search']) > 0?Help::Html($_REQUEST['search']):"").'" /> <input type="submit" name="search_button" value="'.I18n::tr('gallery_search').'" /></td></tr></table>';

	
	

  if(!$this->popup){


	$this->vrat .= '<table class="table-detail" style="width:180px;float:left;margin-right:1em">';
	$this->vrat .= '<tr><th colspan="2">'.I18n::tr('gallery_image_add').'</th></tr>';
	$this->vrat .= '<tr><td colspan="2"><strong>'.I18n::tr('gallery_allowed_formats').' .jpeg, .gif, .swf .png | Max. 4 MB</strong></td><!--<td rowspan="7" valign="top"><img id="gimage" style="position:absolute;visibility:hidden;" class="img" /></td>--></tr>';
	$this->vrat .= '<tr><td>'.I18n::tr('gallery_file_name').':</td><td><input type="text" name="new_name" maxlength="100" /></td></tr>';
	$this->vrat .= '<tr><td>'.I18n::tr('gallery_file_description').':</td><td><input type="text" name="new_popis" maxlength="255" /></td></tr>';
	$this->vrat .= '<tr><td>'.I18n::tr('gallery_file').':</td><td><input type="file" name="new_file" onchange="document.getElementById(\'gimage\').src = this.value" accept="image/gif,image/jpeg" /></td></tr>';
	$this->vrat .= '<tr><td colspan="2">'.I18n::tr('gallery_alt_text').':</td></tr>';
	$this->vrat .= '<tr><td colspan="2">'.$this->Alt().'</td></tr>';
	$this->vrat .= '<tr><td>'.I18n::tr('gallery_category').':</td>
						<td>'.$this->getGaleryMenuOptionsHtml().'</td>
					</tr>';
	$this->vrat .= '<tr><td><input type="submit" name="insertimage" value="'.I18n::tr('gallery_image_add').'"></td></tr>';
	$this->vrat .= '</table>';

	}
	$this->vrat .= $this->GaleryMenu();
	
	$this->vrat .= '<div style="width: 100%; position: relative; float: left;">';
	$this->vrat .= '<h3>'.I18n::tr('gallery_files').'</h3>';
	$this->vrat .= $this->GaleryFiles();
	$this->vrat .= '</div>';
	

	$this->vrat .= '</form>';

  }

  /**
 * Zobrazeni alt
 * @param array $im_alt pole textu k jazykum
 * @param int $im_id id obrazky
 * return string
 */
  private function Alt($im_alt = NULL,$im_id = NULL){

	$vrat = "";

	$sql = "select lang_id,iso from jazyky";
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: Vyber jazyka',"admin_ex_db");

	if($im_alt == NULL){

	  while ($row =& $res->fetchRow()){
	   $vrat .= '<span class="floatleft" style="width:80px;">'.$row['iso'].': <input type="text" class="sinput2" name="new_alt['.$row['lang_id'].']" maxlength="255" /></span>';
	  }

   }
   else if(is_array($im_alt)){

     while ($row =& $res->fetchRow()){
	   $vrat .= '<span class="floatleft" style="width:80px;">'.$row['iso'].': <input type="text" class="sinput2" name="alt['.$im_id.']['.$row['lang_id'].']" value="'.(isset($im_alt[$row['lang_id']])?Help::Html($im_alt[$row['lang_id']]):'').'" maxlength="255" /></span>';
	 }

   }

   return $vrat;

  }

 /**
 * Zobrazeni galerie
 * return void
 */
  private function GaleryMenu(){

	$vrat = '<table class="table-detail" style="float:left;"><tr><th>'.I18n::tr('gallery_category').'</th></tr>
	<tr><td><a href="?section='.$this->section.'&gmenu=all'.($this->popup?"&type=1":"").($this->node?"&node=".$this->node:"").'">'.(!isset($_REQUEST['gmenu']) || $_REQUEST['gmenu'] == 'all'?'<strong>Ukázat vše</strong>':'Ukázat vše').'</a></td></tr>';

	$sql = "select * from galerie_menu order by nazev";
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: Vyber kategorii obrazku',"admin_ex_db");

	while ($row =& $res->fetchRow()){

	  $vrat .= '<tr><td><a href="?section='.$this->section.'&gmenu='.$row['galerie_menu_id'].($this->popup?"&type=1":"").($this->node?"&node=".$this->node:"").'">'.(isset($_REQUEST['gmenu']) && $_REQUEST['gmenu'] == $row['galerie_menu_id']?'<strong>'.Help::Html($row['nazev']).'</strong>':Help::Html($row['nazev'])).'</a>';
	  if(!$this->popup)$vrat .= '&nbsp;&nbsp;<a href="?section='.$this->section.'&search='.(isset($_REQUEST['search']) && mb_strlen($_REQUEST['search']) > 0?urlencode($_REQUEST['search']):"").'&gmenud='.$row['galerie_menu_id'].'" title="'.I18n::tr('gallery_category_delete').'" onclick="return confirm(\''.I18n::tr('gallery_category_really_delete').'\');">x</a></td></tr>';
	  else $vrat .= '';

	}

	if(!$this->popup) $vrat .= '<tr><td>'.I18n::tr('gallery_category_new').': <input type="text" class="sinput" maxlength="100" name="new_gmenu"/></td></tr><tr><td><input type="submit" value="'.I18n::tr('gallery_category_create').'" class="sbutton" name="cgm" /></td></tr>';

	$vrat .= '</table>';
	return $vrat;

  }


	public function imagePicker() {
//TODO: this aint nice at all, I know
		$menuItems = array();
		$sql = "SELECT * FROM galerie_menu ORDER BY nazev";
		$res =& $this->dbGame->query($sql);
		while ($row =& $res->fetchRow()){
			$menuItems[] = $row;
		}


		$images = array();
		if(isset($_REQUEST['gmenu']))
			$where = "
				JOIN galerie_prirad_menu AS gpm
					ON gpm.image_id = g.image_id
				WHERE gpm.galerie_menu_id = ".mysql_real_escape_string($_REQUEST['gmenu']);
		else
			$where = '';

		if (!empty($_REQUEST['search'])) {
			if (empty($where))
				$where .= 'WHERE ';
			else
				$where .= ' AND ';
			$where .= 'g.name LIKE \'%' . Help::Slash($_REQUEST['search']) . '%\'';
		}
			
		$sql = "
			SELECT g.*, ga.alt, j.iso
			FROM galerie AS g
			JOIN galerie_alt AS ga
				ON g.image_id = ga.image_id
			JOIN jazyky AS j
				ON ga.lang_id = j.lang_id
			$where
			ORDER BY g.image_id DESC
		";
		$res =& $this->dbGame->query($sql);


		while ($row =& $res->fetchRow()){
			$images[$row['image_id']]['imageId']			= $row['image_id'];
			$images[$row['image_id']]['name']				= $row['name'];
			$images[$row['image_id']]['popis']				= $row['popis'];
			$images[$row['image_id']]['path']				= PROTOCOL.ADMINHOST.GALLERY_THUMB_URL.$row['name'];
			$images[$row['image_id']]['pathFullSize']		= PROTOCOL.ADMINHOST.GALLERY_URL.$row['name'];

			$images[$row['image_id']]['alt'][$row['iso']]	= $row['alt'];
		}

		ob_start();
			require 'Template/Galerie/imagePicker.phtml';
			$out = ob_get_contents();
		ob_end_clean();

		$this->vrat = $out;
	}
	
	private function getGaleryMenuOptionsHtml($imageId = null) {
		$imageId = intval($imageId);
		$sql = 'select gm.galerie_menu_id as gmId,
					gm.nazev as gmNazev,
					(select count(*) from galerie_prirad_menu as gpm
						where gpm.image_id = ' . $imageId . '
						and gpm.galerie_menu_id = gmId) AS selected
				from galerie_menu as gm
				order by gmNazev';

		$res =& $this->dbGame->query($sql);
		
		$galeryMenuOptionsHtml = '<select name="galery_menu['.$imageId.'][]" multiple="multiple">';

		while ($row =& $res->fetchRow()) {
			$selectedHtml = (!$row['selected']) ? '' : ' selected="selected"';
			$galeryMenuOptionsHtml .= '<option value="'.$row['gmId'].'"'
					. $selectedHtml . '>' . $row['gmNazev'] . '</option>';
		}

		return $galeryMenuOptionsHtml.'</select>';
	}
}

?>