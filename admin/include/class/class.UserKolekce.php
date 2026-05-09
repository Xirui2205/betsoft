<?php
/**
 * @link       main
 */


 /**
 * Trida pro praci s uživateliF
 *
 *Menit vysi zustatku, email a menu muze pouze superadmin
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    main
 */

class UserKolekce extends Template{

/**
 * objekt databaze spojeni s databazi game
 * @access private
 * @var PEAR::DB
 */
private  $dbGame;

/**
 * spojeni na databazi gamewarehouse
 * @access private
 * @var DB
 */
private  $dbSession;

/**
 * spojeni na databazi db
 * @access private
 * @var DB
 */
private  $db;

/**
 * spojeni na databazi affiliate
 * @access private
 * @var DB
 */
private  $dbAf;

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

private $messages = Array();
private $errors = Array();

/**
 * vytvori chybovy XML response podle vyhozene vyjimky
 * @param DB_PEAR $db spojeni na databazi
 * @param int $section isd sekce
 * @return void
 */
  public function __construct($section=NULL,$dbGame=null){

   $this->section = $section;

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

   $this->db = DB::connect(DATABASE ."://". MY_USER .":". MY_PASS ."@". MY_HOST ."/". MY_DB);
   if (DB::isError($this->db)) {
     throw new ExHandler($this->db->getMessage(),"admin_ex_db");
   }
   $this->db->setFetchMode(DB_FETCHMODE_ASSOC);
   $sql = "set names 'utf8'";
   $res =& $this->db->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

   $this->dbSession = DB::connect(SESDATABASE ."://". SESMY_USER .":". SESMY_PASS ."@". SESMY_HOST ."/". SESMY_DB);
   if (DB::isError($this->dbSession)) {
      throw new ExHandler($this->dbSession->getMessage(),"admin_ex_db");
   }
   $this->dbSession->setFetchMode(DB_FETCHMODE_ASSOC);
   $sql = "set names 'utf8'";
   $res =& $this->dbSession->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

	$urlParams = array( 'section' => $this->section );
	$this->url = new Url(null, $urlParams);
	
	
  }


  private function _addTemplateMessage($nazev, $telo){
    $telo = Help::slash($telo);
    $nazev = Help::slash($nazev);
    $sql = "INSERT INTO poznamky_template (predmet, telo) VALUES ('$nazev', '$telo')";
    $this->dbGame->query($sql);
  }

  private function _deleteTemplateMessage($id){
    $id = intval($id);
    $sql = "DELETE FROM poznamky_template WHERE id = $id";
    $this->dbGame->query($sql);
  }

	private function _deleteMessage($id){
		$encrypted = It6_Models_Admin::cryptPass($_POST['s_heslo']);

		$admin = It6_Models_Admin::getData(It6_Session_Admin::getUserData('id'));
		if (empty($admin)
			|| 1 != preg_match('/(?:^|;)1001(?:$|;)/', $admin['key'])
			|| $encrypted != $admin['passwd']) {
			$this->errors[] = 'Comment was not deleted';
			return false;
		}

// 	if(count(Zend_Registry::get('zdb_admin')->query($sql)->fetchAll()) < 1){
//// 		$this->vrat .= "<div class=\"okmsg\">Comment was not deleted</div><br />";
//		$this->errors[] = 'Comment was not deleted';
// 		return false;
// 	}
//		$sql = 'DELETE FROM uzivatel_poznamka WHERE nl_id = ' . intval($id);
		Zend_Registry::get('zdb_game')->delete('uzivatel_poznamka', array('nl_id=?' => $id));
	}

  private function _getTemplateMessages(){
        $sql = "SELECT id, predmet, telo FROM poznamky_template";
        return $this->dbGame->query($sql);
  }

 /**
 * Metoda spousti jednitlove metody podle stavu
 * @return void
 */
  public function runAction(){

   #vytvoreni noveho uzivatele#
   if(isset($_POST['create']))
     $this->createUser();

   #download souboru#
   else if(isset($_GET['file'])){

     $this->DownloadFile($_GET['file']);

   }

   #zmena zustatku#
   else if(isset($_POST['zustatek_zmena']) && isset($_REQUEST['user_id'])){

     $this->ChangeBalance($_REQUEST['user_id']);

   }

   #ulozeni poznamka#
   else if(isset($_POST['send_poznamka'])){
     if($this->update)
          $this->CreatePoznamka();
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vytváření poznámek</div>\n";

   }

   #Vymazat akce#
   else if(isset($_POST['zrusit_deposit']) ){
     if($this->update)
          $this->zrusitDeposit($_REQUEST['user_id']);
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo na tuto akci</div>\n";

   }


   #ulozeni poznamka book#
   else if(isset($_POST['send_book_info'])){
     if($this->update)
          $this->CreatePoznamkaBook();
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vytváření poznámek</div>\n";

   }

   #Vymazat akce#
   else if(isset($_POST['play_block_no']) || isset($_POST['play_block'])){
     if($this->update)
          $this->playBlock($_REQUEST['user_id']);
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro  akci</div>\n";

   }
   #smazani souboru#
   else if(isset($_GET['filedelete']) && is_numeric($_GET['filedelete'])){
     if($this->delete)
          $this->DeleteFile(intval($_GET['filedelete']));
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání souboru</div>\n";

   }
   #povoleni/zakazani hromadne#
   else if((isset($_POST['zakazat_all']) || isset($_POST['obnovit_all'])) && isset($_POST['selectUser'])){


    if($this->update){

            if(isset($_POST['zakazat_all'])){

            foreach($_POST['selectUser'] as $k=>$h){
               $_POST['zakazat'] = 1;
               $this->PovolitZakazat($k);
            }

        }else{

            foreach($_POST['selectUser'] as $k=>$h){
              $_POST['povolit'] = 1;
               $this->PovolitZakazat($k);
            }


        }


    }else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci uživatele</div>\n";

   }
   #povoleni/zakazani#
   else if((isset($_POST['povolit']) || isset($_POST['zakazat']) || isset($_POST['otevrit']) || isset($_POST['zrusit'])) && !isset($_POST['user_id'])){
     if($this->update){
        if(isset($_POST['povolit']) || isset($_POST['zakazat']))
         $this->PovolitZakazat((isset($_POST['povolit'])?key($_POST['povolit']):key($_POST['zakazat'])));
        else
         $this->PovolitZakazat((isset($_POST['otevrit'])?key($_POST['otevrit']):key($_POST['zrusit'])));
     }else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci uživatele</div>\n";

   }

	if(!isset($_POST['submit']['edit']) && !isset($_REQUEST['user_id'])) {
		$this->showUsers();
	}
	else if(isset($_POST['submit']['edit']) || isset($_POST['edituser']) || isset($_REQUEST['user_id']) || isset($_POST['upload']) || isset($_POST['delete']) || isset($_POST['povolit']) || isset($_POST['zakazat']) || isset($_POST['validuser']) || isset($_POST['disvaliduser']) || isset($_POST['newpass'])) {
		$this->showDetail((isset($_POST['submit']['edit'])?intval(key($_POST['submit']['edit'])):intval($_REQUEST['user_id'])));
	}

   $this->vrat .= "";

  $this->dbGame->disconnect();
  $this->dbSession->disconnect();

  }



  /**
 * Metoda zakaze/povoly hranni pro uzivatele
 * @param int $user_id id uzivatele
 * @return void
 */
 private function  playBlock($user_id){

 	$block = 0;
 if(isset($_POST['play_block']) ) $block = 1;

         $status = true;


         if($status){


         $sql = "update uzivatel set block_play=".$block." where user_id=".$user_id;
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");


     	 $this->dbGame->query('insert into uzivatel_poznamka (user_id,admin_id,text,datum) values('.$user_id.','.It6_Session_Admin::getUserData('id').',"'.($block ==1?"Zamezení hraní":"Odblokování hraní").'","'. It6_Date::dbNow() .'")');



           $this->vrat .= "<div class=\"okmsg\">Save</div><br />";
          $_SESSION['lastaction'][time()] = "Nastaven block ".$user_id." (block:".(isset($_POST['play_block'])?'nastaven':'zrušen').")" ;

           return;

         }


        $this->vrat .= "<div class=\"errormsg\">Deposit bonus se nepodařilo vymazat 23</div><br />";






 }


  /**
 * Metoda vymaze deposit bonus pokud jej jest eneprotocil
 * @param int $user_id id uzivatele
 * @return void
 */
 private function  zrusitDeposit($user_id){



       if( isset($_POST['kod_bonus'])){

         $status = true;

         $sql = "select * from admin where admin_id=".intval(It6_Session_Admin::getUserData('id'));
         $res =& $this->db->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

         if($row =& $res->fetchRow()){

            if(Help::cryptPass($_POST['kod_bonus']) != $row['heslo']) {$status = false;}
            $row['klic'] = explode(";",$row['klic']);
            if(!in_array(19,$row['klic'])) {$status = false;}

         }
         if($status){


         	   $sql = "select a.bonus,a.deposit_id,a.date from deposit_bonus_user a  inner join deposit_bonus_rule b on a.deposit_id=b.deposit_id where a.accept=1 and a.b_paid=0 and b.deposit_type=2 and a.user_id=".intval($user_id)." order by date";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) {throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber ticket_pohled',"admin_ex_db");}

     $bonus_amount = array();

     while ($row =& $res->fetchRow()){


   	  $bonus_amount[$row['deposit_id']]['date'] = $row['date'];
   	  $bonus_amount[$row['deposit_id']]['bonus'] = $row['bonus'];

     }

     foreach($bonus_amount as $deposit_id=>$h){

           Zend_Registry::get('zdb_game')->query('update uzivatel_im_data set zustatek_bonus=zustatek_bonus-? where user_id=?',array($h['bonus'],$user_id));
           Zend_Registry::get('zdb_game')->query('update deposit_bonus_user set poznamka="Manual delete",b_paid=1,dt_paid_date=now() where deposit_id=? and user_id=?',array($deposit_id,$user_id));
     }

             $data = array();
   	         $data['user_id'] =  $user_id;
   	         $data['admin_id'] =  It6_Session_Admin::getUserData('id');
   	         $data['text'] =  'Vymazani deposit bonusu ';
   	         $data['datum'] =  It6_Date::dbNow();

  	    	 Zend_Registry::get('zdb_game')->insert('uzivatel_poznamka', $data);


//           $this->vrat .= "<div class=\"okmsg\">Bonus byl smazán</div><br />";
			$this->messages[] = 'Bonus byl smazán';
           $_SESSION['lastaction'][time()] = "Smazání bonusu ".$user_id." (od:".$_POST['akce_od'].";do:".$_POST['akce_od'].")" ;

           return;

         }
        $this->vrat .= "<div class=\"errormsg\">Deposit bonus se nepodařilo vymazat 23</div><br />";


       }else $this->vrat .= "<div class=\"errormsg\">Deposit bonus se nepodařilo vymazat 12</div><br />";




 }




  /**
 * Metoda vytvari poznamku k uzivateli
 * @param int $user_id id uzivatele
 * @return void
 */
 private function  ChangeBalance($user_id){



       if(isset($_POST['zustatek']) && isset($_POST['kod'])){

         $status = true;

         $sql = "select * from admin where admin_id=".intval(It6_Session_Admin::getUserData('id'));
         $res =& $this->db->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

         if($row =& $res->fetchRow()){

            if(Help::cryptPass($_POST['kod']) != $row['heslo']) {$status = false;}
            $row['klic'] = explode(";",$row['klic']);
            if(!in_array(2,$row['klic'])) $status = false;

         }


         if($status){

           $sql = "select zustatek,zetony from uzivatel_im_data where user_id=".intval($user_id);
           $res =& $this->dbGame->query($sql);
           if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni noveho uzivatele',"admin_ex_db");
           if($row =& $res->fetchRow()){$old_zustatek = $row['zustatek'];$old_zetony = $row['zetony'];}else return;

           //$sql = "update uzivatel_im_data set zetony=".floatval($_POST['zetony']).",zustatek=".floatval($_POST['zustatek'])." where user_id=".intval($user_id);
		   //FIXME this should be done with help with WS->Transaction->Make
           $sql = "call BalanceSet(".intval($user_id).",".floatval($_POST['zustatek']).",".floatval($_POST['zetony']).",-1)";
           $res =& $this->dbGame->query($sql);
           if(DB::isError($res)) throw new ExHandler($res->getMessage().'<br>Nepodarilo se provest dotaz: vlozeni noveho uzivatele',"admin_ex_db");

//           $this->vrat .= "<div class=\"okmsg\">Zůstatek byl editován</div><br />";
			$this->messages[] = 'Zůstatek byl editován';
           $_SESSION['lastaction'][time()] = "Změna zůstatku uzivatel ".$user_id." z (zetony:".$old_zetony.";zustatek:".$old_zustatek.") na (zetony:".floatval($_POST['zetony']).";zustatek:".floatval($_POST['zustatek']).")" ;

           return;

         }

       }


       $this->vrat .= "<div class=\"errormsg\">Zůstatek se nepodařilo změnit</div><br />";

 }

   /**
 * Metoda vytvari poznamku k uzivateli bookmakeru
 * @return void
 */
 private function  CreatePoznamkaBook(){

  $status = true;

  if(isset($_POST['book_info_value']) && (mb_strlen($_POST['book_info_value']) > 2000 || mb_strlen($_POST['book_info_value']) < 1)) {$this->vrat .= "<div class=\"errormsg\"><strong>Text</strong> musí mít  1-2000 znaků</div><br />";$status = false;}


  if($status){

     $sql = "update uzivatel set book_info='".Help::Slash($_POST['book_info_value'])."' where user_id=".intval($_POST['user_id']);
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

//     $this->vrat .
	$this->messages[] = 'Poznámka byla uložena';
     $_SESSION['lastaction'][time()] = "Vytvoření book poznámky pro uživatele:".$_POST['user_id'] ;


  }

 }

   /**
 * Metoda vytvari poznamku k uzivateli
 * @return void
 */
 private function  CreatePoznamka(){

  $status = true;

  if(isset($_POST['poznamka_text']) && (mb_strlen($_POST['poznamka_text']) > 2000 || mb_strlen($_POST['poznamka_text']) < 1)) {$this->vrat .= "<div class=\"errormsg\"><strong>Text</strong> musí mít  1-2000 znaků</div><br />";$status = false;}


  if($status){

     $sql = "insert into uzivatel_poznamka (user_id, text, admin_id, datum) values(".intval($_REQUEST['user_id']).",'".Help::Slash($_POST['poznamka_text'])."',".intval(It6_Session_Admin::getUserData('id')).",'".It6_Date::dbNow()."')";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

//     $this->vrat .= "<div class=\"okmsg\">Poznámka byla uložena</div><br />";
		$this->messages[] = 'Poznámka byla uložena';
     $_SESSION['lastaction'][time()] = "Vytvoření poznámky pro uživatele:".$_POST['user_id'] ;


  }

 }

 /**
 * Metoda povoluje/zakazuje uzivatele pro vybery
 * @param int $user_id id uzivatele
 * @return void
 */
 private function  ValidovatVyber($user_id){

     $sql = "update uzivatel set vyber_status=".(isset($_POST['validuser'])?1:0)." where user_id=".intval($user_id);
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

//     $this->vrat .= "<div class=\"okmsg\">Uživateli byly  (".(isset($_POST['validuser'])?"povoleny":"zakázány").") výbiry</div><br />";

	$this->messages[] = 'Uživateli byly  ('.(isset($_POST['validuser'])?"povoleny":"zakázány").') výběry';

     $_SESSION['lastaction'][time()] = (isset($_POST['validuser'])?"Povolení":"Zakázání")." výbiru uživatele: ".(isset($_POST['nick'])?$_POST['nick']:$_POST['user'][intval($user_id)]['nick']);

 }


 /**
 * Metoda povoluje/zakazuje uzivatele
 * @param int $user_id id uzivatele
 * @return void
 */
 private function PovolitZakazat($user_id){

     if(isset($_POST['povolit']) || isset($_POST['zakazat'])){
       $sql = "update uzivatel set zakazany=".(isset($_POST['povolit'])?0:1).(isset($_POST['povolit'])?'':'')." where user_id=".intval($user_id);
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");
     }else{

       $sql = "update uzivatel set zakazany=".(isset($_POST['otevrit'])?0:2)." where user_id=".intval($user_id);
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

     }

//     $this->vrat .= "<div class=\"okmsg\">Uživatel byl úspěšně (".(isset($_POST['povolit'])?"povolen":"zakázán").")</div><br />";
	$this->messages[] = I18n::tr("Uživatel ID {1} byl ".(isset($_POST['povolit'])?"povolen":"zakázán").".",$user_id);
     $_SESSION['lastaction'][time()] = (isset($_POST['povolit'])?"Povolení":"Zakázání")." uživatele: ".(isset($_POST['nick'])?$_POST['nick']:$_POST['user'][intval($user_id)]['nick']);

 }



  /**
 * Metoda pro diwnload uživatelova souboru
 * @param int $user_id id uzivatele
 * @return void
 */
 private function DownloadFile($file){

   Help::OutputFile(UPLOAD_ROOT.$file,$file);

 }

   /**
 * Metoda pro diwnload uživatelova souboru
 * @param int $doc_id id dokumentu
 * @return void
 */
 private function DeleteFile($doc_id){

   $sql = "select name from uzivatel_dokumenty where doc_id=".intval($doc_id);
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

   if($row =& $res->fetchRow()){

    if(unlink(UPLOAD_ROOT.$doc_id.$row['name'])){

     $sql = "delete from uzivatel_dokumenty where doc_id=".intval($doc_id);
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

//     $this->vrat .= "<div class=\"okmsg\">Soubor byl smazán</div><br />";
	$this->messages[] = 'Soubor byl smazán';
     $_SESSION['lastaction'][time()] = "Smazání souboru (".intval($doc_id).")";


    }else
//       $this->vrat .= "<div class=\"errormsg\">Soubor nebyl smazán</div><br />";
		$this->errors[] = 'Soubor nebyl smazán';
   }else
//       $this->vrat .= "<div class=\"errormsg\">Soubor nebyl smazán</div><br />";
		$this->errors[] = 'Soubor nebyl smazán';
 }

  /**
 * Metoda pro vypsani nahranych dokumentu pro uzivatele
 * @param int $user_id id uzivatele
 * @return void
 */
 private function UserFiles($user_id){

   $vrat = "";


   return $vrat;

 }

    /**
 * Metoda pro nahrani dokumentu na server
 * @return void
 */
 private function Upload($user_id){

   $ob = new Dokumenty();
   $ob->Upload($user_id);

   $this->vrat = $ob->getContent();

 }

  /**
 * Metoda vytvori nove heslo a posle na uzivatelsky mail
 * @param int $user_id id uzivatele
 * @param string $email email uzivatele
 * @param string $lang_id preferovany jazyk uzivatele uzivatele
 * @return void
 */
 private function NoveHeslo($user_id,$email,$lang_id){


     if(isset($_POST['kod_pass']) ){
        $sql = "select * from admin where admin_id=".intval(It6_Session_Admin::getUserData('id'));
         $res =& $this->db->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

         if($row =& $res->fetchRow()){

            if(Help::cryptPass($_POST['kod_pass']) != $row['heslo']) {
//				$this->vrat .= "<div class=\"errormsg\">Nemáte právo měnit heslo</div><br />";
				$this->errors[] = 'Nemáte právo měnit heslo';
				return;
			} else {
            $klic = explode(";",$row['klic']);
            if(!in_array(6,$klic)){

//                $this->vrat .= "<div class=\"errormsg\">Nemáte právo měnit heslo</div><br />";
				$this->errors[] = 'Nemáte právo měnit heslo';
				return;

            }
         }
      } else {
//		$this->vrat .= "<div class=\"errormsg\">Nemáte právo měnit heslo</div><br />";
		$this->errors[] = 'Nemáte právo měnit heslo';
		return;
	}
    }
    if(!isset($_POST['kod_pass'])) {
//		$this->vrat .= "<div class=\"errormsg\">Nemáte právo měnit heslo</div><br />";
		$this->errors[] = 'Nemáte právo měnit heslo';
		return;
	}

  $ob= new ZapomenuteHeslo();
  $ob->NoveHeslo($user_id,$email,$lang_id);
  $this->vrat = $ob->getContent();

 }

 /**
 * Metoda vytvari noveho uzivatele
 * @return void
 */
 final private function createUser(){


  if($this->update){

   $status = true;

      if(!isset($_POST['heslo']) || !Help::checkPass($_POST['heslo'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Heslo</strong> má špatný formát (6-16 znaku, nesmí začínat číslem, minimálně 2 čísla)</div><br />";$status = false;}
      if($_POST['heslo'] != $_POST['heslo2']) {$this->vrat .= "<div class=\"errormsg\">Potvrzení hesla nesouhlasí</div><br />";$status = false;}
      if(!isset($_POST['mena']) || !is_numeric($_POST['mena'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Měna</strong> musí být uvedena</div><br />";$status = false;}
      if(!isset($_POST['zeme']) || !is_numeric($_POST['zeme'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Země</strong> musí být uvedena</div><br />";$status = false;}
      if(!isset($_POST['misto']) || mb_strlen(trim($_POST['misto'])) > 250 || mb_strlen(trim($_POST['misto'])) < 1) {$this->vrat .= "<div class=\"errormsg\"><strong>Místo</strong> musí být uvedeno a mít maximálně 250 znaku</div><br />";$status = false;}
      if(!isset($_POST['ulice']) || mb_strlen(trim($_POST['ulice'])) > 100 || mb_strlen(trim($_POST['ulice'])) < 1) {$this->vrat .= "<div class=\"errormsg\"><strong>Ulice</strong> musí být uvedena a mít maximálně 100 znaku</div><br />";$status = false;}
      if(!isset($_POST['psc'])) {$this->vrat .= "<div class=\"errormsg\"><strong>PSČ/Zip Code</strong> musí být uvedeno a mít maximálně 250 znaku</div><br />";$status = false;}
      if(!isset($_POST['pohlavi']) || ($_POST['pohlavi'] != 'f' && $_POST['pohlavi'] != 'm')) {$this->vrat .= "<div class=\"errormsg\"><strong>Pohlaví</strong> je povinná položka</div><br />";$status = false;}
      if(!isset($_POST['day']) || !is_numeric($_POST['day']) || $_POST['day'] > 31 || $_POST['day'] < 1) {$this->vrat .= "<div class=\"errormsg\"><strong>Den</strong> má nesprávný formát</div><br />";$status = false;}
      if(!isset($_POST['month']) || !is_numeric($_POST['month']) || $_POST['month'] > 12 || $_POST['month'] < 1) {$this->vrat .= "<div class=\"errormsg\"><strong>Měsíc</strong> má nesprávný formát</div><br />";$status = false;}
      if(!isset($_POST['rok']) || !is_numeric($_POST['rok'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Rok</strong> má nesprávný formát</div><br />";$status = false;}
      if(!Help::CheckDatum($_POST['day'].".".$_POST['month'].".".$_POST['rok'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Datum</strong> nesprávný formát</div><br />";$status = false;}
      if(!isset($_POST['nick']) || !Help::checkNick($_POST['nick']))  {$this->vrat .= "<div class=\"errormsg\"><strong>Uživatelské jméno</strong> musí být uvedeno a mít správný formát (4-20 znaku, nesmí začínat číslem)</div><br />";$status = false;}
      if(!isset($_POST['email']) || !Help::valideMail($_POST['email']) || mb_strlen(trim($_POST['email'])) > 80 || mb_strlen(trim($_POST['email'])) < 6)  {$this->vrat .= "<div class=\"errormsg\"><strong>Mail</strong> má špatný formát (min. 6 znaku)</div><br />";$status = false;}
      if($_POST['email'] != $_POST['email2']) {$this->vrat .= "<div class=\"errormsg\">Potvrzení emailu nesouhlasí</div><br />";$status = false;}
      if(!isset($_POST['jmeno']) || mb_strlen(trim($_POST['jmeno']),'utf-8') > 50 || mb_strlen(trim($_POST['jmeno']),'utf-8') < 1)  {$this->vrat .= "<div class=\"errormsg\"><strong>Jméno</strong> musí být uvedeno (max 50 znaku)</div><br />";$status = false;}
      if(!isset($_POST['prijmeni']) || mb_strlen(trim($_POST['prijmeni']),'utf-8') > 60 || mb_strlen(trim($_POST['prijmeni']),'utf-8') < 1)  {$this->vrat .= "<div class=\"errormsg\"><strong>Přijmení</strong> musí být uvedeno (max 60 znaku)</div><br />";$status = false;}

      if(isset($_POST['nick']) && isset($_POST['email']) && mb_strlen(trim($_POST['email'])) > 0 && mb_strlen(trim($_POST['nick'])) > 0){

        $sql = "select nick from uzivatel where nick='".Help::slash($_POST['nick'])."' or email='".Help::slash($_POST['email'])."'";
        $res =& $this->dbGame->query($sql);
        if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");

        if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tento \"".($_POST['nick']==$row['nick']?"<strong>Nick</strong>":"<strong>Email</strong>")."\" již existuje</div><br />";$status = false;}

      }

       if($status){

       $this->dbGame->autocommit(false);

       $sql = "insert into uzivatel(jmeno,prijmeni,nick,heslo,email,mena_id,zeme_id,misto,pohlavi,datum_narozeni,ulice,psc,mobil,newsletter,datum_registrace,lang_id)
         values('".Help::slash($_POST['jmeno'])."','".Help::slash($_POST['prijmeni'])."','".Help::slash($_POST['nick'])."',
         '".Help::cryptPass($_POST['heslo'])."','".Help::slash($_POST['email'])."',".intval($_POST['mena']).",".intval($_POST['zeme']).",
         '".Help::slash($_POST['misto'])."','".Help::slash($_POST['pohlavi'])."','".Help::slash($_POST['rok'])."-".Help::slash($_POST['month'])."-".Help::slash($_POST['day'])."',
         '".Help::slash($_POST['ulice'])."',".intval($_POST['psc']).",".Help::slash($_POST['mobilp']).Help::slash($_POST['mobil'])."',
         ".(isset($_POST['newsletter'])?1:0).",'".It6_Date::dbNow()."',".intval($_POST['lang_id']).")";
        $res =& $this->dbGame->query($sql);
        if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni noveho uzivatele',"admin_ex_db");

       $sql = "select max(user_id) AS maxi from uzivatel";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: ziskani id posledniho vlozeneho uzivatele',"admin_ex_db");

       if($row =& $res->fetchRow()) $id = $row['maxi'];
       else throw new ExHandler('Nepodařilo se získat poslední vložené id',"admin_ex_page");

       $sql = "insert into uzivatel_im_data (user_id,zustatek,zetony) values(".$id.",0,0)";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni noveho uzivatele',"admin_ex_db");

       $this->dbGame->commit();

//        $this->vrat .= "<div class=\"okmsg\">Uživatel byl úspěšně vložen</div><br />";
		$this->messages[] = 'Uživatel byl úspěšně vložen';
        $_SESSION['lastaction'][time()] = "Vložení nového uživatele: ".$_POST['nick'];

      }

   }else
//       $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vytvoření uživatele</div>\n";
		$this->errors[] = 'Nemáte dostatečné právo pro vytvoření uživatele';
 }

  /**
 * Metoda edituje existujícího uživatele
 * @param int $user_id id uzivatele
 * @return void
 */
 final private function EditUser($user_id){

    $status = true;

     #block#
     if(isset($_POST['block_kod'])  && mb_strlen($_POST['block_kod'])>3){
        $sql = "select * from admin where admin_id=".intval(It6_Session_Admin::getUserData('id'));
         $res =& $this->db->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

         if($row =& $res->fetchRow()){

            if(Help::cryptPass($_POST['block_kod']) != $row['heslo']);else{
            $klic = explode(";",$row['klic']);
            if(in_array(3,$klic)){

                $sql = "update uzivatel set block=".intval($_POST['block'])." where user_id=".intval($user_id);
                $res =& $this->dbGame->query($sql);
                if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");
//                $this->vrat .= "<div class=\"okmsg\">Block se podarilo zmenit</div><br />";
				$this->messages[] = 'Block se podařilo změnit';
                $_SESSION['lastaction'][time()] = "Editace uživatele block: ".$_POST['nick'];

            }
         }
      }
    }
    #End block#

     #Narozeni#
     if(isset($_POST['kod_narozeni'])  && mb_strlen($_POST['kod_narozeni'])>3){
        $sql = "select * from admin where admin_id=".intval(It6_Session_Admin::getUserData('id'));
         $res =& $this->db->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

         if($row =& $res->fetchRow()){

            if(Help::cryptPass($_POST['kod_narozeni']) != $row['heslo']);else{
            $klic = explode(";",$row['klic']);
            if(in_array(4,$klic)){

                $sql = "update uzivatel set datum_narozeni='".Help::slash($_POST['rok'])."-".Help::slash($_POST['month'])."-".Help::slash($_POST['day'])."' where user_id=".intval($user_id);
                $res =& $this->dbGame->query($sql);
                if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");
//                $this->vrat .= "<div class=\"okmsg\">Datum narozeni se podarilo zmenit</div><br />";
				$this->messages[] = 'Datum narozeni se podarilo zmenit';
                $_SESSION['lastaction'][time()] = "Editace uživatele datum natozeni: ".$_POST['nick'];
            }
         }
      }
    }
    #End Narozeni#
     #Adresa#
     if(isset($_POST['kod_adress'])  && mb_strlen($_POST['kod_adress'])>3){
        $sql = "select * from admin where admin_id=".intval(It6_Session_Admin::getUserData('id'));
         $res =& $this->db->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

         if($row =& $res->fetchRow()){

            if(Help::cryptPass($_POST['kod_adress']) != $row['heslo']);else{
            $klic = explode(";",$row['klic']);

            if(in_array(7,$klic)){

                $sql = "update uzivatel set zeme_id=".intval($_POST['zeme']).",psc='".$_POST['psc']."',ulice='".Help::slash($_POST['ulice'])."',misto='".Help::slash($_POST['misto'])."' where user_id=".intval($user_id);
                $res =& $this->dbGame->query($sql);
                if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");
//                $this->vrat .= "<div class=\"okmsg\">Adresu se podarilo zmenit</div><br />";
				$this->messages[] = 'Adresu se podarilo zmenit';
                $_SESSION['lastaction'][time()] = "Editace uživatele datum natozeni: ".$_POST['nick'];
            }
         }
      }
    }
    #End Adresa#


    #All#
     if(isset($_POST['kod_all'])){
        $sql = "select * from admin where admin_id=".intval(It6_Session_Admin::getUserData('id'));
         $res =& $this->db->query($sql);
         if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

         if($row =& $res->fetchRow()){

            if(Help::cryptPass($_POST['kod_all']) != $row['heslo']) {
//				$this->vrat .= "<div class=\"errormsg\">Nemáte právo provést změny</div><br />";
				$this->errors[] = 'Nemáte právo provést změny';
				$status = false;
			} else {
				$klic = explode(";",$row['klic']);
            if(!in_array(5,$klic)){

//              $this->vrat .= "<div class=\"errormsg\">Nemáte právo provést změny</div><br />";
				$this->errors[] = 'Nemáte právo provést změny';
				$status = false;

            }
         }
      } else {
//		$this->vrat .= "<div class=\"errormsg\">Nemáte právo provést změny</div><br />";
			$this->errors[] = 'Nemáte právo provést změny';
		$status = false;
	}
    }
    if(!isset($_POST['kod_all'])) {
//		$this->vrat .= "<div class=\"errormsg\">Nemáte právo provést změny</div><br />";
		$this->errors[] = 'Nemáte právo provést změny';
		$status = false;
	}
    #End All#



      //if(!isset($_POST['heslo']) || !Help::checkPass($_POST['heslo'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Heslo</strong> má špatný formát (6-16 znaku, nesmí začínat číslem, minimálně 2 čísla)</div><br />";$status = false;}
      //if($_POST['heslo'] != $_POST['heslo2']) {$this->vrat .= "<div class=\"errormsg\">Potvrzení hesla nesouhlasí</div><br />";$status = false;}
      //if(!isset($_POST['mena']) || !is_numeric($_POST['mena'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Měna</strong> musí být uvedena</div><br />";$status = false;}
      if(!isset($_POST['zeme']) || !is_numeric($_POST['zeme'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Země</strong> musí být uvedena</div><br />";$status = false;}
      if(!isset($_POST['misto']) || mb_strlen(trim($_POST['misto'])) > 250 || mb_strlen(trim($_POST['misto'])) < 1) {$this->vrat .= "<div class=\"errormsg\"><strong>Místo</strong> musí být uvedeno a mít maximálně 250 znaku</div><br />";$status = false;}
      if(!isset($_POST['ulice']) || mb_strlen(trim($_POST['ulice'])) > 100 || mb_strlen(trim($_POST['ulice'])) < 1) {$this->vrat .= "<div class=\"errormsg\"><strong>Ulice</strong> musí být uvedena a mít maximálně 100 znaku</div><br />";$status = false;}
      if(!isset($_POST['psc'])) {$this->vrat .= "<div class=\"errormsg\"><strong>PSČ/Zip Code</strong> musí být uvedeno a mít maximálně 250 znaku</div><br />";$status = false;}
      if(!isset($_POST['pohlavi']) || ($_POST['pohlavi'] != 'f' && $_POST['pohlavi'] != 'm')) {$this->vrat .= "<div class=\"errormsg\"><strong>Pohlaví</strong> je povinná položka</div><br />";$status = false;}
      if(!isset($_POST['day']) || !is_numeric($_POST['day']) || $_POST['day'] > 31 || $_POST['day'] < 1) {$this->vrat .= "<div class=\"errormsg\"><strong>Den</strong> má nesprávný formát</div><br />";$status = false;}
      if(!isset($_POST['month']) || !is_numeric($_POST['month']) || $_POST['month'] > 12 || $_POST['month'] < 1) {$this->vrat .= "<div class=\"errormsg\"><strong>Měsíc</strong> má nesprávný formát</div><br />";$status = false;}
      if(!isset($_POST['rok']) || !is_numeric($_POST['rok'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Rok</strong> má nesprávný formát</div><br />";$status = false;}
      if(!Help::CheckDatum($_POST['day'].".".$_POST['month'].".".$_POST['rok'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Datum</strong> nesprávný formát</div><br />";$status = false;}
      if(!isset($_POST['nick']) || !Help::checkNick($_POST['nick']))  {$this->vrat .= "<div class=\"errormsg\"><strong>Uživatelské jméno</strong> musí být uvedeno a mít správný formát (4-20 znaku, nesmí začínat číslem)</div><br />";$status = false;}
      if($_SESSION['superadmin'] && !isset($_POST['email']) || !Help::valideMail($_POST['email']) || mb_strlen(trim($_POST['email'])) > 80 || mb_strlen(trim($_POST['email'])) < 6)  {$this->vrat .= "<div class=\"errormsg\"><strong>Mail</strong> má špatný formát (min. 6 znaku)</div><br />";$status = false;}
      //if($_SESSION['superadmin'] && $_POST['email'] != $_POST['email2']) {$this->vrat .= "<div class=\"errormsg\">Potvrzení emailu nesouhlasí</div><br />";$status = false;}
      if(!isset($_POST['jmeno']) || mb_strlen(trim($_POST['jmeno']),'utf-8') > 50 || mb_strlen(trim($_POST['jmeno']),'utf-8') < 1)  {$this->vrat .= "<div class=\"errormsg\"><strong>Jméno</strong> musí být uvedeno (max 50 znaku)</div><br />";$status = false;}
      if(!isset($_POST['prijmeni']) || mb_strlen(trim($_POST['prijmeni']),'utf-8') > 60 || mb_strlen(trim($_POST['prijmeni']),'utf-8') < 1)  {$this->vrat .= "<div class=\"errormsg\"><strong>Přijmení</strong> musí být uvedeno (max 60 znaku)</div><br />";$status = false;}
     if($_SESSION['superadmin'] && isset($_POST['nick']) && isset($_POST['email']) && mb_strlen(trim($_POST['email'])) > 0 && mb_strlen(trim($_POST['nick'])) > 0){

        $sql = "select nick from uzivatel where (nick='".Help::slash($_POST['nick'])."' or email='".Help::slash($_POST['email'])."') and user_id<>".intval($user_id);
        $res =& $this->dbGame->query($sql);
        if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");

        if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tento \"".($_POST['nick']==$row['nick']?"<strong>Nick</strong>":"<strong>Email</strong>")."\" již existuje</div><br />";$status = false;}

      }

       if($status){
       		$old_max_bet = Zend_Registry::get('zdb_game')->select()
       						->from("uzivatel",array("max_bet"))
       						->where("user_id = ?", $user_id)
       						->limit(1)
       					->query()->fetchColumn();

       $this->dbGame->autocommit(false);

       $sql = "update uzivatel set jmeno='".Help::slash($_POST['jmeno'])."',prijmeni='".Help::slash($_POST['prijmeni'])."',
       nick=".($_SESSION['superadmin']?"'".Help::slash($_POST['nick'])."'":"nick").",email=".($_SESSION['superadmin'] || $status?"'".Help::slash($_POST['email'])."'":"email").",
       zeme_id=".intval($_POST['zeme']).",misto='".Help::slash($_POST['misto'])."',
       pohlavi='".Help::slash($_POST['pohlavi'])."',datum_narozeni='".Help::slash($_POST['rok'])."-".Help::slash($_POST['month'])."-".Help::slash($_POST['day'])."',
       ulice='".Help::slash($_POST['ulice'])."',psc='".Help::Slash($_POST['psc'])."',
       mobil='".Help::slash($_POST['mobil'])."',
       newsletter=".(isset($_POST['newsletter'])?1:0).",finance_rating=".intval($_POST['finance_rating']).",max_bet=".intval($_POST['max_bet']).",lang_id=".intval($_POST['lang_id']).",ucet_status=".intval($_POST['ucet_status']).((isset($_POST['e_testovaci']) && ($_POST['e_testovaci'] != ''))?(",e_testovaci='".$_POST['e_testovaci']."'"):'')." where user_id=".intval($user_id);
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni noveho uzivatele',"admin_ex_db");

		if(intval($_POST['max_bet']) != $old_max_bet) {
			Zend_Registry::get('zdb_game')
				->query("replace into uzivatel_supply (user_id,s_data_name,s_data_value,dt_updated) values (".intval($user_id).",'max_bet_by','".$this->getAdminName()."',NOW())");
		}

       $this->dbGame->commit();

        $this->vrat .= "<div class=\"okmsg\">Uživatel byl úspěšně editován</div><br />";
        $_SESSION['lastaction'][time()] = "Editace uživatele: ".$_POST['nick'];

      }

 }

     /**
 * vraci udaje o uzivatelych
 * @return object
 */
  public function SelectData($where = ""){

    $sql = "
      SELECT user_id,mena_id,nick,email,zeme_id,lang_id,posledni_prihlaseni,zakazany,vyber_status
      FROM uzivatel ".$where;

     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

     return $res;
  }


  /**
 * vraci udaje o uzivatelych rozdelenych podle pohlavi
 * @return object
 */
  public function SelectPohlavi($where = ""){

     $sql = "select count(pohlavi) AS pohlaviPocet, pohlavi,max(datum_registrace) AS registrace from uzivatel ".$where." group by pohlavi";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

     return $res;

  }

   /**
 * Metoda vypise vsechny uzivatele
 * @param int $user_id  id uzivatele
 * @return void
 */
  private function SaldoUser($user_id){

    $vklad = $vyber = 0;
    /*
    $sql = "select a.* from finacni_transakce a  where a.user_id=".$user_id;
    $res2 =& $this->dbGame->query($sql);
    if(DB::isError($res2)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel',"admin_ex_db");
    while($row =& $res2->fetchRow()){

        if($row['typ_platby'] == 1)     $vklad += $row['castka'];
        else if($row['typ_platby'] == 2) $vyber += $row['castka'];

    }
	*/

    return array("vklad"=>$vklad,"vyber"=>$vyber);

  }


   /**
 * Metoda vypise vsechny uzivatele
 * @param int $user_id  id uzivatele
 * @return void
 */
 final private function showDetail($user_id){

    if (isset($_POST['btnAddTemplate']))
        $this->_addTemplateMessage($_POST['s_nazev'], $_POST['s_telo']);

    $tInt = intval($_GET['templateMessage']);
    if ($tInt > 0)
        $this->_deleteTemplateMessage($tInt);

    $tInt = intval($_POST['nl_id_poznamka']);
    if ($tInt > 0)
        $this->_deleteMessage($tInt);

    if(isset($_POST['povolit']) || isset($_POST['zakazat'])) {
     if($this->update)
          $this->PovolitZakazat($user_id);
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci uživatele</div>\n";
    }
    else if(isset($_POST['validuser']) || isset($_POST['disvaliduser'])){
     if($this->update)
          $this->ValidovatVyber($user_id);
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci uživatele</div>\n";
    }
    else if(isset($_POST['edituser']) || ($_POST['edituser'] == 'test')){
     if($this->update)
          $this->EditUser($user_id);
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci uživatele</div>\n";
    }
    else if(isset($_POST['upload'])){
     if($this->update)
          $this->Upload($user_id);
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci uživatele</div>\n";
    }

    $this->dbGame->autocommit(false);

    $book_info = '';

    $saldo = $this->SaldoUser($user_id);
    $vklad = $saldo['vklad'];
    $vyber = $saldo['vyber'];



    $sql = "select * from uzivatel a left join ticket_bonus_uzivatel b on a.user_id=b.user_id where a.user_id=".$user_id;
    $res2 =& $this->dbGame->query($sql);
    if(DB::isError($res2)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel',"admin_ex_db");
    if($res2->numRows() < 1) throw new ExHandler('Neexistující uživatel',"admin_ex_user");

    $sql = "select zustatek,dluh from uzivatel_im_data where user_id=".$user_id;
    $res9 =& $this->dbGame->query($sql);
    if(DB::isError($res9)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel_im_data',"admin_ex_db");

    if($row =& $res9->fetchRow()){

     $uzivatel = array("zustatek"=>$row['zustatek'],"dluh"=>$row['dluh'],"body"=>0);

    } else throw new ExHandler('Neexistující uživatel',"admin_ex_user");


	// points by user query
	$sql = "select balance from point_account where user_id=".$user_id;
	$res_points =& $this->dbGame->query($sql);
	if(DB::isError($res_points)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel_im_data',"admin_ex_db");

	if($row =& $res_points->fetchRow())
		$uzivatel['body'] = $row['balance'];
	else throw new ExHandler('Neexistující uživatel',"admin_ex_user");

    if($row8 =& $res2->fetchRow()){

    $book_info = $row8['book_info'];

    if(isset($_POST['newpass'])){
     if($this->update)
          $this->NoveHeslo($user_id,$row8['email'],$row8['lang_id']);
     else
         $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci uživatele</div>\n";
    }

     $preklad = new Preklady();

     $mena = new Mena(0);
     $res3 = $mena->selectData();

     $mena = $mena_usr = "";
     $mena_array = array();
     while ($row =& $res3->fetchRow()){

       $mena_array[$row['mena_id']] = $row['mena_text'];
       $mena .= "<option value=\"".$row['mena_id']."\" ".($row8['mena_id'] == $row['mena_id']?"selected=\"selected\"":"").">".Help::Html($row['mena_text'])."</option>";
       if($row8['mena_id'] == $row['mena_id']) $mena_usr = Help::Html($row['mena_text']);
     }

     $zeme = new Zeme(0);
     $res4 = $zeme->selectData();

     $zeme = "";

     while ($row =& $res4->fetchRow()){

       if($row['zobrazeno'] || $row8['zeme_id'] == $row['zeme_id']){

       $r = $preklad->selectData("where lang_id=1 and index_pole='".$row['nazev']."'");
       if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = "Překlad nenalezen";
       $zeme .= "<option value=\"".$row['zeme_id']."\" ".($row8['zeme_id'] == $row['zeme_id']?"selected=\"selected\"":"").">".Help::Html($row2['text'])."</option>";

      }

     }

     $lang = new Jazyky();
     $res6 = $lang->selectData();



     $jazyky = "";

     while ($row =& $res6->fetchRow()){

       if($row['zobrazeno'] || $row8['lang_id']==$row['lang_id'])
       $jazyky .= '<option value="'.$row['lang_id'].'" '.($row8['lang_id'] == $row['lang_id']?"selected=\"selected\"":"").'>'.Help::Html($row['alt_text']).'</option>';

     }

     list($y,$m,$d) = explode("-",$row8['datum_narozeni']);
     $this->dbGame->commit();

     $sql = "select * from uzivatel a inner join bankovni_ucty b on a.user_id=b.user_id where a.user_id=".$user_id;
     $res10 =& $this->dbGame->query($sql);
     if(DB::isError($res10)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky bankovni_ucty',"admin_ex_db");



     $ucty = $karty = "<ol type=\"1\">";
     while ($row10 =& $res10->fetchRow()){

       $ucty .= '<li><br />
                 <strong>Název banky:</strong> '.Help::Html($row10['jmeno_banky']).'<br />
                 <strong>Číslo účtu:</strong> '.Help::Html($row10['cislo_uctu']).'<br />
                 <strong>Kod banky:</strong> '.Help::Html($row10['kod_banky']).'<br />


                 </li>';

     }



     $ucty .= "</ol>";



     if($res10->numRows() == 0) $ucty = "Žádné registrované úety";



     $admin_ar = array();

     $sql = "select * from admin";
     $res11 =& $this->db->query($sql);
     if(DB::isError($res11)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel_poznamka',"admin_ex_db");

     while ($row11 =& $res11->fetchRow()){

        $admin_ar[$row11['admin_id']] = $row11['jmeno'].' '.$row11['prijmeni'].' ('.$row11['nick'].')';

     }

     $poznamka = '<table style="width:600px">';

     $sql = "select * from uzivatel_poznamka  where user_id=".$user_id." order by datum desc";
     $res11 =& $this->dbGame->query($sql);
     if(DB::isError($res11)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel_poznamka',"admin_ex_db");

     while ($row11 =& $res11->fetchRow()){

         $poznamka .= '<tr><th class="span-2">'.It6_Date::fromDb($row11['datum']).'</th><th> '.Help::Html($admin_ar[$row11['admin_id']]).'</th></tr>';
         $poznamka .= '<tr><td colspan="2">'.nl2br(Help::Html($row11['text'])).'</td></tr>';
         $poznamka .= '<tr><td colspan="2"><form method="post" action="?section=40&user_id=' . $_GET['user_id'] . '&pageView=1" ><input type="password" name="s_heslo"><input type="hidden" name="nl_id_poznamka" value="' . $row11['nl_id'] . '"><input type="submit" name="btn" value="Delete"></form></td></tr>';

      }

     $sql = "select kod,vybral,bonus_castka,ticket_bonus_id,poznamka,rel, dt_in from ticket_bonus_uzivatel  where user_id=".$user_id." and (ticket_bonus_id=2 or  ticket_bonus_id=1) order by id";
     $res11 =& $this->dbGame->query($sql);
     if(DB::isError($res11)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky uzivatel_poznamka',"admin_ex_db");

     $fbs = array();
     while ($row11 =& $res11->fetchRow()){

         $fbs[] = array("dt_in"=>$row11['dt_in'],"id"=>$row11['ticket_bonus_id'],"rel"=>$row11['rel'],"kod"=>$row11['kod'],"vybral"=>$row11['vybral'],"bonus_castka"=>$row11['bonus_castka'],"notice"=>$row11['poznamka']);

      }

      $fbbPo[] = 1;
     $sqlO = 'SELECT
					b.`user_id`
				FROM friendship_bonus a
				JOIN uzivatel b ON (b.`email` = a.`email`)
				WHERE a.`user_id` = ' . intval($user_id) . ' AND a.`checked` = 1';
      $resa11 =& $this->dbGame->query($sqlO);
      /*while ($rowA11 =& $resa11->fetchRow()){
      	$fbbPo[] = $rowA11['user_id'];
      }*/




      $poznamka .= '</table>';

 /*     $this->vrat .= "<div class=\"floatright rightMenuParent\">
      <a href=\"javascript:show('userprofil',['userprofil','ucet','pn','doklady','ucty','poznamky','shoda','book_info']);void(0);\" class=\"rightMenu\">Profil</a>
      <a href=\"javascript:show('ucet',['userprofil','ucet','pn','doklady','ucty','poznamky','shoda','book_info']);void(0);\" class=\"rightMenu\">Zůstatek</a>
      <a href=\"javascript:show('pn',['userprofil','ucet','pn','doklady','ucty','poznamky','shoda','book_info']);void(0);\" class=\"rightMenu\">Heslo</a>
       <a href=\"javascript:show('poznamky',['userprofil','ucet','pn','doklady','ucty','poznamky','shoda','book_info']);void(0);\" class=\"rightMenu\" >Poznámky</a>
      <a href=\"javascript:show('book_info',['userprofil','ucet','pn','doklady','ucty','poznamky','shoda','book_info']);void(0);\" class=\"rightMenu\">Poznámky bookmaker</a>
      <a href=\"javascript:show('shoda',['userprofil','ucet','pn','doklady','ucty','poznamky','shoda','book_info']);void(0);\" class=\"rightMenu\">Kontrola shody</a>
      ";

    $this->vrat .= '
                    <a href="?section=76&user='.$user_id.(isset($_GET['superb'])?"&superb=1":"").'">Sázky statistika</a>
                    <a href="?section=88&user='.$user_id.(isset($_GET['superb'])?"&superb=1":"").'">Saldo</a></div>';
*/

	$this->vrat .= '
		<h3>'.i18n::tr('Users').'</h3>
		'.UiUtil::printMessages($this->messages).'
		'.UiUtil::printErrors($this->errors).'
		<ul class="idTabs" style="display:block">
			<li><a href="#userprofil">Profil</a></li>
			<!--<li><a href="#ucet">Zůstatek</a></li>-->
			<li><a href="#pn">Heslo</a></li>
			<li><a href="#poznamky">Poznámky</a></li>
			<li><a href="#book_info">Poznámky bookamker</a></li>
			<li><a href="#shoda">Kontrola shody</a></li>
			<li><a href="?section=76&user='.$user_id.'">Sázky statistika</a></li>
			<li><a href="?section=88&user='.$user_id.'">Saldo</a></li>
			<li><a href="#parameters" onClick="loadParameters('.$user_id.')">Parametry</a></li>
		</ul>

		<div id="tabs-container">
<!--
	<div style="color:red;font-size:16px;padding:6px;width:150px;"> UID'.$_REQUEST['user_id'].'</div>
-->
		<form method="post" name="filesend" enctype="multipart/form-data" action="?section='.$this->section.'">

			<div id="doklady" style="display:none"><table>
				<tr>
					<td><h3>Doklady</h3></td>
				</tr>
				https://'.ADMINHOST.'/?section=40
				<tr>
					<td>Nový dokument</td>
				</tr>
				<tr>
					<td>
						<img src="_clip/excel_neaktivni.gif" id="obr0" alt="Microsoft Excel" width="16" height="16" border="0" onclick="ChangeFile(this.src,\'excel\',0);" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="_clip/pdf_neaktivni.gif" id="obr1" alt="PDF" width="16" height="16" border="0" onclick="ChangeFile(this.src,\'pdf\',1);" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="_clip/powerpoint_neaktivni.gif" id="obr2" alt="Microsoft PowerPoint" width="16" height="16" border="0" onclick="ChangeFile(this.src,\'powerpoint\',2);" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="_clip/txt_neaktivni.gif" id="obr3" alt="TXT" width="16" height="16" border="0" onclick="ChangeFile(this.src,\'txt\',3);" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="_clip/visio_neaktivni.gif" id="obr4" alt="Microsoft Visio" width="16" height="16" border="0" onclick="ChangeFile(this.src,\'visio\',4);" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="_clip/word_neaktivni.gif" id="obr5" alt="MIcrosoft Word" width="16" height="16" border="0" onclick="ChangeFile(this.src,\'word\',5);" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="_clip/image_neaktivni.gif" id="obr6" alt="Obrázek" width="16" height="16" border="0" onclick="ChangeFile(this.src,\'image\',6);" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="_clip/other_aktivni.gif" id="obr7" alt="Jiný soubor" width="16" height="16" border="0" onclick="ChangeFile(this.src,\'other\',7);" />
					</td>
				</tr>
				<tr>
					<td>Důležitý dokument: <input type="checkbox" class="no"  name="important"  /></td>
				</tr>
				<tr>
					<td><input type="text" class="classic" maxlength="240" name="text"  /></td>
				</tr>
				<tr>
					<td><input type="file" class="classic" name="newfile"  /></td>
				</tr>
				<tr>
					<td><input type="submit" class="classic" name="upload" value="Nahrát soubor" /></td>
				</tr>
                '.$this->UserFiles($user_id).'
			</table>
		</div>
		<input type="hidden" name="user_id" value="'.$user_id.'" />
		<input type="hidden" name="nick" value="'.Help::Html($row8['nick']).'" />
		<input type="hidden" name="file" value="other" />
	</form>';

	$message = $this->_getTemplateMessages();
	$myOut = '';
	while ($row =& $message->fetchRow()){
		$telo = str_replace("\n", "\\n", $row['telo']);
		$telo = str_replace("\r", "\\r", $telo);
		$myOut .= '
			<tr>
				<td>
					<input type="button" onclick="setTemplate(\''.$telo .' \');" value="'.$row['predmet'].'">
					 -
					 <a href="?section='.$_GET['section'].'&pageView=1&user_id='.$_REQUEST['user_id'].'&templateMessage='.$row['id'].'" onclick="if(!confirm(\'Opravdu chcete smazat?\'))return false;">
						smazat
					</a>
				</td>
			</tr>';
	}

	$this->vrat .= '
		<script>
			function setTemplate(telo) {
				$(\'#picus\').append(telo);
			}
			function resetTemplate() {
				$(\'#picus\').text(\'\');
			}
		</script>

		<div id="poznamky" style="display:none">

			<form method="post" name="userForm" id="userFormse" action="?section='.$this->section.'&user_id='.$_GET['user_id'].' \">
				<table>
					<tr>
						<td><h3>Template Poznámky</h3></td>
					</tr>
					<tr>
						<td><input type="text" class="classic" name="s_nazev"  /></td>
					</tr>
					<tr>
						<td><textarea name="s_telo" cols="60" rows="10"></textarea></td>
					</tr>
					<tr>
						<td><input type="submit" value="Přidat" name="btnAddTemplate" /></td>
					</tr>
				</table>
			</form>

			<form method="post" id="userForma" action="?section='.$this->section.'&user_id='.$_GET['user_id'].'">
				<table>
					<tr>
						<td><h3>Poznámky</h3></td>
					</tr>
					'.$myOut.'
					<tr>
						<td><textarea id="picus" name="poznamka_text" cols="60" rows="10"></textarea></td>
					</tr>
					<tr>
						<td>
							<input type="submit" value="Uložit" onclick="this.form.action=this.form.action+\'&pageView=1\';" name="send_poznamka" />
							<input type="button" value="Vyčistit text" onclick="resetTemplate();" />
						</td>
					</tr>
				</table>

				<br />
				<br />
				'.$poznamka.'
			</form>

			<form method="post" name="userForm" id="userFormse" action="?section='.$this->section.'&user_id='.$_GET['user_id'].'">
				<div id="book_info" style="display:none">
					<table>
						<tr>
							<td><h3>Poznámky bookmakeři</h3></td>
						</tr>
						<tr>
							<td><textarea name="book_info_value" cols="80" rows="40" >'.$book_info.'</textarea></td>
						</tr>
						<tr>
							<td><input type="submit" value="Uložit" name="send_book_info" /></td>
						</tr>
					</table>
				</div>
			</div>

			<div id="ucty" style="display:none">
				<table>
					<tr>
						<td><h3>Účty</h3></td>
					</tr>
					<tr>
						<td>'.$ucty.'</td>
					</tr>
					<tr>
						<td><h3>Karty</h3></td>
					</tr>
					<tr>
						<td>'.$karty.'</td>
					</tr>
				</table>
			</div>

<!--			<div id="ucet" style="display:none">
				<table>
					<tr>
						<td colspan="2"><h3>Zůstatek</h3></td>
					</tr>
					<tr>
						<td colspan="2"><em></em></td>
					</tr>
					<tr>
						<td>Zůstatek:</td>
						<td><input type="text" name="zustatek" value="'.$uzivatel['zustatek'].'" /> </td>
					</tr>
					<tr>
						<td>Kód:</td>
						<td><input type="password" name="kod" /></td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" name="zustatek_zmena" value="Změnit zůstatek" onclick="if(!confirm(\'Opravdu chcete změnit zůstatek?\'))return false;" />
						</td>
					</tr>
				</table>
			</div>-->

			<div id="shoda" style="display:none">
				'.$this->shoda($user_id).'
			</div>

			<div id="parameters" style="display:none">
				'.$this->parameters($user_id).'
			</div>

			<div id="pn" style="display:none">
				<table>
					<tr>
						<td><h3>Heslo</h3></td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td><input type="submit" name="newpass" style="width:150px" value="Poslat nové heslo"></td>
					</tr>
					<tr>
						<td>Kód <input type="password" name="kod_pass" /></td>
					</tr>
				</table>
			</div>

				<div id="userprofil">

					<table class="table-detail" >
						<tr>
								<th>'.I18n::tr('Balance').'</th>
								<td class="border-left"><font style="font-size:1.4em">'.$uzivatel['zustatek']. ' ' .$mena_array[$row8['mena_id']].'</font></td>
								<td class="border-left"><a href="?section=245&amount=&currencyId=8&userId='.$_REQUEST['user_id'].'&insert=Insert-transaction">'.I18n::tr('Change user balance').'</a><br/>
								<a href="?section=224&userId='.$_REQUEST['user_id'].'&typeId=&value=&fromDate=&toDate=&filter=Filter">'.I18n::tr('User transaction history').'</a><br/>
								<a href="?section=225&userId='.$_REQUEST['user_id'].'&typeId=&value=&fromDate=&toDate=&filter=Filter">'.I18n::tr('User withdrawal requests').'</a>
								
								</td>
						</tr>
						<tr>
								<th>- '.I18n::tr('Debt').'</th>
								<td class="border-left">'.$uzivatel['dluh'].' '.$mena_array[$row8['mena_id']] .'</td>
								<td class="border-left"></td>

						</tr>
						<tr>
							<th>'.I18n::tr('Points').'</th>
							<td class="border-left"><font style="font-size:1.4em">'.round($uzivatel['body'],0).' '.I18n::tr('Pts').'</font></td>
							<td class="border-left"><a href="?section=246&amount=&userId='.$_REQUEST['user_id'].'&insert=Insert-transaction">'.I18n::tr('Change user points balance').'</a></td>
							</tr>
					</table>
					
					<table class="table-detail">
						<tr >
							<td colspan="5" style="border-bottom:1px solid #1b3281">ID: '.$user_id.'</td>
							<td colspan="4" style="border-bottom:1px solid #1b3281">
								Stav účtu:
								<input type="text" name="ucet_status" style="width:14px;" value="'.$row8['ucet_status'].'" />
							</td>
						</tr>
						<tr>
							<td colspan="9" style="border-bottom:1px solid #1b3281">
								Zůstatek na účtu <strong>'.$uzivatel['zustatek'].'</strong>
								'.$mena_array[$row8['mena_id']].' </strong> - Dluh: <strong>'.$uzivatel['dluh'].'
								'.$mena_array[$row8['mena_id']] .'</strong>
							</td>
						</tr>
						<tr>
							<td colspan="5" style="border-bottom:1px solid #1b3281">
								Tento uživatel
								'.($row8['vyber_status']?'<span class="blue">je ověřený</span>':'<span class="red">není ověřený</span>').'
								pro výběry
							</td>
							<td colspan="4" style="border-bottom:1px solid #1b3281">
								Tento uživatel
								'.($row8['zakazany']==2?'<span style="color:orange">má zrušený účet</span>':($row8['zakazany']==1?'<span class="red">je zakázaný</span>':'<span class="blue">je povolený</span>')).'
								pro vstup na stránky
							</td>
						</tr>
						<tr>
							<td colspan="5" style="border-bottom:1px solid #1b3281">
								Tento uživatel
								'.($row8['e_testovaci'] == 'ne'?'<span class="blue">není testovací</span>. Změnit? <input type="checkbox" name="e_testovaci" class="no" value="test" />':'<span class="red">je testovací</span> ('.$row8['e_testovaci'].')').'
							</td>
							<td colspan="4" style="border-bottom:1px solid #1b3281">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="5" style="border-bottom:1px solid #1b3281">
								Datum registrace:
								'.It6_Date::fromDb($row8['datum_registrace']).'
							</td>
							<td colspan="4" style="border-bottom:1px solid #1b3281">
								Sázky výhernost: '.$row8['vyhernost'].' %
							</td>
						</tr>
						<tr>
							<td colspan="1" style="border-bottom:1px solid #1b3281">
								<strong>Vklad</strong>
							</td>
							<td style="border-bottom:1px solid #1b3281">'.$vklad.'</td>
							<td colspan="1" style="border-bottom:1px solid #1b3281">
								<strong>Výběr</strong>
							</td>
							<td style="border-bottom:1px solid #1b3281">
								'.$vyber.'
							</td>
							<td colspan="1" style="border-bottom:1px solid #1b3281">
								<strong>Rozdíl</strong>
							</td>
							<td colspan="3" style="border-bottom:1px solid #1b3281">
								<span style="color:red">
									'.($vklad-$vyber).'
									'.$mena_array[$row8['mena_id']].'
								</span>
							</td>
						</tr>
					</table>

					<table class="table-detail">
						<tr>
							<th>'.I18n::tr('User ID').'</th>
							<td>'.$_REQUEST['user_id'].'</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Profile').'</th>
							<td>'.($this->OnlineUser($user_id)?'<span class="blue floatright">ON</span>':'<span class="red floatright" >OFF</span>').'</td>
							'.(It6_Date::fromDbAsTimestamp($row8['self_excluded_until'], false) > time()?'<tr style="background:Scrollbar"><td colspan="5">Uživatel se zablokoval do</td><td colspan="4">'. It6_Date::fromDb($row8['self_excluded_until']) .'</td></tr>':'').'
						</tr>';



	$a = 0;
	foreach($fbs as $fbs_h){
		$this->vrat .= '
						<tr>
							<td colspan="2">'.($fbs_h['id']==1?'FreeBet:':'FSB Kod:') .'<em></em></td>
							<td colspan="2">Vybral: '.($fbs_h['vybral']==1?'ANO':'NE').'</td>
							<td colspan="2">
								Částka: '.$fbs_h['bonus_castka'].'  '.$mena_array[$row8['mena_id']].'  ('.$fbs_h['notice'].' '.$fbs_h['rel'].')
							</td>
							<td>Obtained:<em>'.$fbs_h['dt_in'].'</em></td>
							<td>User ID:<em>' . ($fbs_h['id']==2? '<a href="?section=40&user_id=56109">' . $fbbPo[$a] . '</a>' : 'Nobody') . '</em></td>
						</tr>';

		if ($fbs_h['id']==2)
			$a++;
	}
	$max_bet_setup = $this->getUSupplyValue($user_id,"max_bet_by");

	$this->vrat .='
<!--
	<tr>
		<td colspan="4">Finance rating </td>
		<td colspan="4">
			1<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==1?'checked="checked"':'').' value="1" />
			2<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==2?'checked="checked"':'').' value="2" />
			3<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==3?'checked="checked"':'').' value="3" />
			4<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==4?'checked="checked"':'').' value="4" />
			5<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==5?'checked="checked"':'').' value="5" />
			6<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==6?'checked="checked"':'').' value="6" />
			7<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==7?'checked="checked"':'').' value="7" />
			8<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==8?'checked="checked"':'').' value="8" />
			9<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==9?'checked="checked"':'').' value="9" />
			10<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==10?'checked="checked"':'').' value="10" />
			11<input type="radio" class="no" name="finance_rating" '.($row8['finance_rating']==11?'checked="checked"':'').' value="11" />
		</td>
	</tr>
-->
						<tr>
							<th>'.I18n::tr('Individual bet limit').'</th>
							<td>
								<input type="text" class="mandatory" maxlength="11" name="max_bet" value="'.Help::Html($row8['max_bet']).'" />
								by '.($max_bet_setup?$max_bet_setup["value"].' at '.$max_bet_setup["ts"]:'registration').'
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Block').'</th>
							<td>
								<input type="text" class="classic mandatory" maxlength="50" name="block" value="'.Help::Html($row8['block']).'" />
								'.I18n::tr('Code').'
								<input type="password" class="span-2"  name="block_kod"  />
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Name').'</th>
							<td>
								<input type="text" class="classic mandatory" maxlength="50" name="jmeno" value="'.Help::Html($row8['jmeno']).'" />
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Surname').'</th>
							<td>
								<input type="text" class="classic mandatory" maxlength="60" name="prijmeni" value="'.Help::Html($row8['prijmeni']).'" />
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Username').'</th>
							<td>
								<input type="text" class="classic mandatory" maxlength="20" name="nick" value="'.Help::Html($row8['nick']).'" />
							</td>
						</tr>
						<!--<tr>
							<th>'.I18n::tr('Password').'</th>
							<td>
								<input type="password" class="classic mandatory" maxlength="16" name="heslo" value="" />
							</td>
						</tr>
						<tr>
							<th>Heslo znovu</th>
							<td>
								<input type="password" class="classic mandatory" maxlength="16" name="heslo2" value="" />
							</td>
						</tr>-->
						<tr>
							<th>Email</th>
							<td>
								<input type="text" class="classic mandatory" maxlength="80" name="email" value="'.Help::Html($row8['email']).'" />
							</td>
						</tr>
<!--
	<tr>
		<th colspan="2">Email potvrzení</th>
		<td colspan="7">
			<input type="text" class="classic mandatory" maxlength="80" name="email2" value="'.Help::Html($row8['email']).'" />
		</td>
	</tr>
-->
						<tr>
							<th>'.I18n::tr('Currency').'</th>
							<td class="mandatory" colspan="7">'.$mena_usr.'<!--<select  name="mena" style="width:auto">'.$mena.'</select>-->
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Country').'</th>
							<td class="mandatory">
								<select name="zeme" style="width:200px;">'.$zeme.'</select>
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('City').'</th>
							<td class="">
								<input type="password2" class="classic mandatory" maxlength="250" name="misto" value="'.Help::Html($row8['misto']).'" />
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Sex').'</th>
							<td class="mandatory">
								'.I18n::tr('Male').'
								<input type="radio" class="no" name="pohlavi" '.($row8['pohlavi'] == "m" ?"checked=\"checked\"":"").' value="m">
								'.I18n::tr('Female').'
								<input type="radio" class="no" name="pohlavi" '.($row8['pohlavi'] == "f" ?"checked=\"checked\"":"").' value="f">
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Birth date').'</th>
							<td class="mandatory">
							'.I18n::tr('Day').'
							<select name="day">
								<option value="-">-</option>
								'.Utils::printSelectOptions(1,31,$d).'
							</select>

							'.I18n::tr('Month').'
							<select name="month">
								<option value="-">-</option>
								'.Utils::printSelectOptions(1,12,$m).'
							</select>

							'.I18n::tr('Year').'
							<select name="rok">
								<option value="-">-</option>
								'.Utils::printSelectOptions(1900,strftime('%Y'),$y).'
							</select>

							'.I18n::tr('Code').'
							<input type="password" class=""  name="kod_narozeni"  />
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Street').'</th>
							<td>
								<input type="text" class="classic mandatory" maxlength="80" name="ulice" value="'.Help::Html($row8['ulice']).'" />
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Zip code').'</th>
							<td>
								<input type="text" class="classic mandatory" maxlength="80" name="psc" value="'.Help::Html($row8['psc']).'" />
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Newsletter').'</th>
							<td>
								<input type="checkbox" name="newsletter" class="no" '.($row8['newsletter'] == 1?"checked=\"checked\"":"").' value="" />
							</td>
						</tr>
						<tr>
							<th>'.I18n::tr('Language').'</th>
							<td>
								<select name="lang_id">'.$jazyky.'</select>
							</td>
						</tr>
						<tr>
							<th>Telefon</th>
							<td>
								<input type="text" class="classic" maxlength="80" name="mobil" value="'.Help::Html($row8['telefon']).'" />
							</td>
						</tr>
					</table>
				</div>
			</div>

		<!-- tabs-container-->

			<div class="actions-filter">
				<input type="submit" name="edituser" class="sbutton" value="Uložit změny" />
				<input type="submit" name="delete" class="sbutton" value="Odstranit" />
				'.($row8['zakazany']?'<input type="submit" name="povolit" class="blue" value="Povolit" />':'<input type="submit" name="zakazat" class="red" value="Zakázat" />').'
				'.($row8['vyber_status']?'<input type="submit" name="disvaliduser" class="red" style="width:150px" value="Zakázat  výběry" />':'<input type="submit" name="validuser" style="width:150px" class="blue" value="Povolit  výběry" />').'
			</div>

			<table>
				<tr>
					<th colspan="9" style="text-align:right;">
						Bezpečnostní kód vše
						<input type="password" class=""  name="kod_all" />
						<br />
						Bezpečnostní kód adresa
						<input type="password" class=""  name="kod_adress"  />
					</th>
				</tr>
			</table>
			<input type="hidden" name="user_id" value="'.$user_id.'">

			<script>
				'.(isset($_GET['pageView']) && $_GET['pageView']==1?"show('poznamky',['userprofil','ucet','pn','doklady','ucty','poznamky','shoda','book_info','vymazatakce']);":'').'
			</script>
		</form>';
	}
}




  /**
  * Metoda vypise vsechny uzivatele
  * @return void
  */
  private function showUsers(){

    $preklad = new Preklady();

    $fulltext             = $_POST['fulltext'];
    $country              = $_POST['country'];
    $currency             = $_POST['currency'];
    $sex                  = $_POST['sex'];
    $lang_id              = $_POST['lang_id'];
    $birthDateFrom        = $_POST['birthDateFrom'];
    $birthDateTo          = $_POST['birthDateTo'];
    $registrationDateFrom = $_POST['registrationDateFrom'];
    $registrationDateTo   = $_POST['registrationDateTo'];
    $name                 = $_POST['name'];
    $lastName             = $_POST['lastName'];
    $username             = $_POST['username'];
    $email                = $_POST['email'];
    $testUser             = $_POST['testUser'];
    $branchUser           = $_POST['branchUser'];
    $balanceLT            = $_POST['balanceLT'];
    $balanceGT            = $_POST['balanceGT'];


    $this->dbGame->autocommit(false);

    if(isset($_GET['narozen'])){
      $_REQUEST['od2']=$_GET['narozen'];
      $_REQUEST['do2']=$_GET['narozen'];
    }

    $where  = '';

    if(mb_strlen($fulltext) > 0){
      $where .= "
        jmeno LIKE '%".Help::Slash($_REQUEST['fulltext'])."%'
        OR v.user_id=".intval($_REQUEST['fulltext'])."
        OR prijmeni like '%".Help::Slash($_REQUEST['fulltext'])."%'
        OR nick like '%".Help::Slash($_REQUEST['fulltext'])."%'
        OR v.email like '%".Help::Slash($_REQUEST['fulltext'])."%'
		AND
      ";
    }
    if(ctype_digit($country) && $country != 0)
      $where .= ' v.zeme_id='.intval($country).' AND';

    if(ctype_digit($currency) && $currency != 0)
      $where .= ' v.mena_id='.intval($currency).' AND';

    if($sex == "m" || $sex == "f")
      $where .= ' v.pohlavi="'.$sex.'" AND';

    if(ctype_digit($lang_id) && $lang_id != 0)
      $where .= ' v.lang_id='.intval($lang_id).' AND';

    if(Help::CheckDatum($birthDateFrom))
      $where .= ' v.datum_narozeni>="'.It6_Date::toDbAsDate($birthDateFrom).'" AND';

    if(Help::CheckDatum($birthDateTo))
      $where .= ' v.datum_narozeni<="'.It6_Date::toDbAsDate($birthDateTo).'" AND';

    if(Help::CheckDatum($registrationDateFrom))
      $where .= ' v.datum_registrace>="'.It6_Date::toDbAsDate($registrationDateFrom).'" AND';

    if(Help::CheckDatum($registrationDateTo))
      $where .= ' v.datum_registrace<="'.It6_Date::toDbAsDate($registrationDateTo).'" AND';

    if(mb_strlen($name) > 0)
      $where .= ' v.jmeno like "'.Help::Slash($name).'%" AND';

    if(mb_strlen($lastName) > 0)
      $where .= ' v.prijmeni like "'.Help::Slash($lastName).'%" AND';

    if(mb_strlen($username ) > 0)
      $where .= ' v.nick like "'.Help::Slash($username ).'%" AND';

    if(mb_strlen($email) > 0)
      $where .= ' v.email like "'.Help::Slash($email).'%" AND';



    if($testUser == 1)
      $where .= ' v.e_testovaci != \'ne\' AND';
    else
      $where .= ' v.e_testovaci = \'ne\' AND';


    if($branchUser == 1)
      $where .= ' v.anonymous=1 AND';


    if(is_numeric($balanceGT) && mb_strlen($balanceGT) > 0)
      $where .= '
        ((b.zustatek/(
          SELECT g.kurz
          FROM kurz f
          INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz
          WHERE f.platny_od <= now( )
            AND f.platny_do > now( )
            AND g.id_mena=v.mena_id
        ))+b.zetony)>'.intval($balanceGT).' AND';
    if(is_numeric($balanceLT) && mb_strlen($balanceLT) > 0)
      $where .= '
        ((b.zustatek/(
          SELECT g.kurz
          FROM kurz f
          INNER JOIN kurz_mena g ON f.id_kurz = g.id_kurz
          WHERE f.platny_od <= now( )
            AND f.platny_do > now( )
            AND g.id_mena=v.mena_id
        ))+b.zetony)<'.intval($balanceLT).' AND';

    if(!empty($_POST['orderBy']))
      $orderBy = $_POST['orderBy'];
    else
      $orderBy = 'v.user_id';

    if(!empty($_POST['orderDirection']))
      $orderDirection = $_POST['orderDirection'];
    else
      $orderDirection = 'ASC';


    $where = substr($where,0,-3);

    $sql = "
      SELECT
        v.user_id,
        v.finance_rating,
        v.jmeno,
        v.prijmeni,
        v.vyhernost,
        v.vyhernost_game,
        v.datum_registrace,
        v.nick,v.email,
        v.zakazany,
        v.datum_narozeni,
        v.vyber_status,
        v.mena_id,
        b.zustatek,
        c.mena_text,
        v.self_excluded_until,
		v.anonymous,
		br.id as branch_id,
		br.name as branch_name
      FROM uzivatel v
      LEFT JOIN uzivatel_im_data b ON v.user_id=b.user_id
      INNER JOIN mena c ON v.mena_id=c.mena_id
		LEFT JOIN vic_admin.branch br ON br.id = v.branch_id
      ".($this->section == 44?"WHERE v.zakazany<>0 AND (".$where.")":"WHERE ".$where)."
      ORDER BY ".$orderBy." ".$orderDirection;



    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky admin',"admin_ex_db");

    $mena = new Mena(0);
    $res3 = $mena->selectData();

    $zeme = new Zeme(0);
    $res4 = $zeme->selectData('');

    $lang = new Jazyky();
    $res6 = $lang->selectData('');



    $jazyky = $jazyky2 = "";

    while ($row =& $res6->fetchRow()){
      $jazyky .= '
        <option value="'.$row['lang_id'].'" '.(isset($_POST['lang_id']) && $_POST['lang_id'] == $row['lang_id']?"selected=\"selected\"":"").'>
          '.Help::Html($row['alt_text']).'
        </option>
      ';
      $jazyky2 .= '
        <option value="'.$row['lang_id'].'" '.(isset($_REQUEST['lang_id2']) && $_REQUEST['lang_id2'] == $row['lang_id']?"selected=\"selected\"":"").'>
          '.Help::Html($row['alt_text']).'
        </option>
      ';
    }

    $zeme = "";
    $zeme2 = "";
    while ($row =& $res4->fetchRow()){
      $r = $preklad->selectData("WHERE lang_id=1 AND index_pole='".$row['nazev']."'");

      if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0)
        $row2['text'] = "Překlad nenalezen";

      $zeme .= "
        <option value=\"".$row['zeme_id']."\" ".(isset($_POST['zeme']) && $_POST['zeme'] == $row['zeme_id']?"selected=\"selected\"":"").">
          ".Help::Html($row2['text'])."
        </option>
      ";
      $zeme2 .= "
        <option value=\"".$row['zeme_id']."\" ".(isset($_POST['zeme2']) && $_POST['zeme2'] == $row['zeme_id']?"selected=\"selected\"":"").">
          ".Help::Html($row2['text'])."
        </option>
      ";
    }

    $mena = $mena2 = "";
    $mena_array = array();

    while ($row =& $res3->fetchRow()){
      $mena_array[$row['mena_id']] = $row['mena_text'];
      $mena .= "
        <option value=\"".$row['mena_id']."\" ".(isset($_POST['mena']) && $_POST['mena'] == $row['mena_id']?"selected=\"selected\"":"").">
          ".Help::Html($row['mena_text'])."
        </option>
      ";
      $mena2 .= "
        <option value=\"".$row['mena_id']."\" ".(isset($_POST['mena2']) && $_POST['mena2'] == $row['mena_id']?"selected=\"selected\"":"").">
          ".Help::Html($row['mena_text'])."
        </option>
      ";
    }

    $this->dbGame->commit();

    $nausers = "";
     /*$nonactive = $this->selectData("where date_add(posledni_prihlaseni,INTERVAL 6 MONTH)< now() and zakazany=0 and user_id not in(".implode(",",$GLOBALS['']).")");
     while ($nona =& $nonactive->fetchRow()){
      if(!$nona['zakazany']) $tl = '<input type="submit" name="zakazat['.$nona['user_id'].']" class="sbutton" value="Zakázat" />'; else $tl = "";
      $nausers .= "<div><strong><a href=\"javascript:var fulltext = new getObj('fulltext');fulltext.obj.value='".Help::Script($nona['nick'])."';void(0);\">".Help::HTML($nona['nick'])."</a></strong> ".$nona['posledni_prihlaseni']." ".$tl."</div>";
     }*/

    $this->vrat .= "
      <!--
        <p>
          <div style=\"height:10px;width:60px;background:#CC0000\"></div>
          Zakázaný uživatel
          <div style=\"height:10px;width:60px;background:#3366cc\"></div>
          On-line uživatel
        </p>
      -->
    ";
    $usersRows = array();
    while($row =& $res->fetchRow()){
      $usersRows[] = $row;
    }


	$this->vrat .= '<h3>'.i18n::tr('Users').'</h3>';

    $this->vrat .= "<form method=\"post\" action=\"".$this->url->getUrl(true)."\" id=\"formUsersFilter\">";

	$this->vrat .= UiUtil::printMessages($this->messages);
	$this->vrat .= UiUtil::printErrors($this->errors);

    #hlavicka#
	$this->vrat .= '<div class="actions">';
	$this->vrat .= '<div class="row">';
	$this->vrat .= '<button onClick="$(\'.table-filter\').toggle();return false;">'.I18n::tr('Filter').'</button>';
	$this->vrat .= '<input type="submit" class="input"  name="zakazat_all" value="Zakázat zvolené" />';
	$this->vrat .= '<input type="submit" class="input"  name="obnovit_all" value="Obnovit zvolené" />';
	$this->vrat .= '</div>';
    $this->vrat .= '
      <table class="table-filter">
        <tr>
          <td>Fulltext:</td>
          <td colspan="3">
            <input
              type="text"
              id="fulltext"
              name="fulltext"
              value="'.$fulltext.'" \>
          </td>
        </tr>

        <tr>
          <td>Jméno:</td>
          <td>
            <input
              type="text"
              id="name"
              name="name"
              value="'.$name.'" \>
          </td>
          <td>Přijmení:</td>
          <td>
            <input
              type="text"
              id="lastName"
              name="lastName"
              value="'.$lastName.'" \>
          </td>
        </tr>

        <tr>
          <td>Uživatelské jméno:</td>
          <td>
            <input
              type="text"
              id="username"
              name="username"
              value="'.$username.'" \>
          </td>
          <td>Email:</td>
          <td>
            <input
              type="text"
              id="email"
              name="email"
              value="'.$email.'" \>
          </td>
        </tr>

        <tr>
          <td>Pohlaví:</td>
          <td>
            <select name="sex">
              <option value="0">Vše</otpion>
              <option
                value="m"
                '.($_POST['sex'] == "m"?"selected=\"selected\"":"").'>
                Muž
              </option>
              <option
                value="f"
                '.($_POST['sex'] == "f"?"selected=\"selected\"":"").'>
                Žena
              </option>
            </select>
          </td>
          <td>Jazyk:</td>
          <td>
            <select name="lang_id">
              <option value="0">Vše</otpion>
              '.$jazyky.'
            </select>
          </td>
        </tr>

        <tr>
          <td>Testovací:</td>
          <td>
            <input
              type="checkbox"
              class="no"
              name="testUser"
              value="1"
              '.($testUser==1? ' checked':'').'/>
          </td>
          <td>Pobočka:</td>
          <td>
            <input
              type="checkbox"
              class="no"
              name="branchUser"
              value="1"
              '.($branchUser==1?' checked':'').'/>
          </td>
        </tr>

        <tr>
          <td>Narozen od:</td>
          <td>
            <input
              type="text"
              class="sinput dateOnly"
              name="birthDateFrom"
              maxlength="10"
              value="'.$birthDateFrom.'" />
              <img src="_clip/calendar.gif" class="calendar-icon">
          </td>
          <td>Narozen do:</td>
          <td>
            <input type="text"
              class="sinput dateOnly"
              name="birthDateTo"
              maxlength="10"
              value="'.$birthDateTo.'" />
              <img src="_clip/calendar.gif" class="calendar-icon">
          </td>
        </tr>

        <tr>
          <td>Datum registrace od:</td>
          <td>
            <input
              type="text"
              class="sinput dateOnly"
              name="registrationDateFrom"
              maxlength="10"
              value="'.$registrationDateFrom.'" />
              <img src="_clip/calendar.gif" class="calendar-icon">
          </td>
          <td>Datum registrace do:</td>
          <td>
            <input
              type="text"
              class="sinput dateOnly"
              name="registrationDateTo"
              maxlength="10"
              value="'.$registrationDateTo.'" />
              <img src="_clip/calendar.gif" class="calendar-icon">
          </td>
        </tr>

        <tr>
          <td>Zůstatek &gt; </td>
          <td>
            <input
              type="text"
              class="sinput"
              name="balanceGT"
              value="'.(isset($_REQUEST['zustatek2'])?Help::Html($_REQUEST['zustatek2']):"").'" />
          </td>
          <td>Zůstatek  &lt; </td>
          <td>
            <input
              type="text"
              class="sinput"
              name="balanceLT"
              value="'.(isset($_REQUEST['zustatek3'])?Help::Html($_REQUEST['zustatek3']):"").'" />
          </td>
        </tr>

        <tr>
          <td>
            <input type="submit" class="inputs" value="Vyhledat" />
          </td>
        </tr>
      </table>
    ';
	$this->vrat .= '</div>';

    $url = "&".$url;

 /*   $this->vrat .= '
		<div class="actions">
			<input type="submit" class="input"  name="zakazat_all" value="Zakázat zvolené" />
			<input type="submit" class="input"  name="obnovit_all" value="Obnovit zvolené" />
		</div>
    ';*/


    $count = sizeof($usersRows);
    $lister = new Lister('uz', 30, $count, 0, 15);
    $lister->setPost(true);
    $lister->updateFromParams($_POST);
    if (!empty($_POST['filtr']))
      $lister->setFrom(0);

//	$this->vrat .= "<div id='inner'></div>";



	if ($count > 0) {

	    $this->vrat .= $lister->getOutput(null, 0, array('formId' => 'formUsersFilter'));
	    $this->vrat .= '
	      <!--
	        <em>Uživatelé, kteří nebyli aktivní déle než pul roku a nejsou zakázaní.</em>
	        <br /><br />
	        <div style="width:400px;height:80px;padding:4px;overflow:auto;border:1px solid black;">
	          '.$nausers.'
	        </div>
	        <br /><br />
	      -->
	    ';

	    $this->vrat .= "
	      <table class='table-list width-100'>
	        <thead>
	          <tr>
	            <th></th>
				<th></th>
				<th></th>
				<th></th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"ID\" onClick=\"orderUsersBy(".$this->setOrder('v.user_id').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Jméno\" onClick=\"orderUsersBy(".$this->setOrder('v.jmeno').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Přijmení\" onClick=\"orderUsersBy(".$this->setOrder('v.prijmeni').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Login\" onClick=\"orderUsersBy(".$this->setOrder('v.nick').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Email\" onClick=\"orderUsersBy(".$this->setOrder('v.email').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Zůstatek\" onClick=\"orderUsersBy(".$this->setOrder('b.zustatek').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Měna\" onClick=\"orderUsersBy(".$this->setOrder('c.mena_text').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Narození\" onClick=\"orderUsersBy(".$this->setOrder('v.datum_narozeni').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Registrace\" onClick=\"orderUsersBy(".$this->setOrder('v.datum_registrace').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Sázky\" onClick=\"orderUsersBy(".$this->setOrder('v.vyhernost').")\" />
	            </th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Rating\" onClick=\"orderUsersBy(".$this->setOrder('v.finance_rating').")\" />
	            </th>
	            <th colspan=\"1\">Rozdíl</th>
	            <th>
	              <input type=\"submit\" class=\"setOrder\" value=\"Branch\" onClick=\"orderUsersBy(".$this->setOrder('branch_name').")\" />
	            </th>
	          </tr>
	        </thead>
	        <tbody>
	    ";

	    $shoda = new UserTrack();

	    /*telo*/
	    foreach($usersRows as $row) {

		      $is = $lister->getIterationStatus();
		      if (Lister::ITER_BREAK == $is)
		        break;
		      else if (Lister::ITER_SKIP == $is)
		        continue;

		      $saldo = $this->SaldoUser($row['user_id']);
		      //TODO Ma to byt v gm nebo ne? Ted je. Jinak by se muselo dat , false
		      $blok = It6_Date::fromDbAsTimestamp($row['self_excluded_until']);

			if ( 1 == $row['anonymous'] ) {
				  $this->vrat .= '<tr class="flag-special">';
			} else  $this->vrat .= '<tr>';

		      $this->vrat .= '
		          <td>
		            <input type="checkbox" class="no" name="selectUser['.$row['user_id'].']" />
		          </td>
				<td>
		            <input
		              type="submit"
		              name="'.($row['zakazany']==1?"povolit":"zakazat").'['.$row['user_id'].']"
		              value="'.($row['zakazany']?"Povolit":"Zakázat").'"' .
		             ( $row['zakazany']!=1 ? 'onclick="return confirm(\''.i18n::tr('Do you want to ban user?').'\');"' : '' ) . '/> 
				</td>
				<td>
		            <input
		              type="submit"
		              name="'.($row['zakazany']==2?"otevrit":"zrusit").'['.$row['user_id'].']"
		              class="sbutton"
		              value="'.($row['zakazany']?"Otevřít":"Zrušit").'" />
		          </td>
		          <td>
			<!--	<button onClick="loadGeneric(\'40&user_id='.intval($row['user_id']).'\',\'inner\',{});return false;">'.i18n::tr('View').'</button>-->
		            <a href="?section=40&user_id='.intval($row['user_id']).'">
		              DETAIL
		            </a>
		          </td>
		          <td class="textcenter" '.($shoda->isShodaPC($row['user_id']) || $shoda->isShodaIP($row['user_id'])?'style="background:yellow"':'').'>
		            '.$row['user_id'].'
		          </td>
		          <td>'.Help::Html($row['jmeno']).'</td>
		          <td>'.Help::Html($row['prijmeni']).'</td>
		          <td>'.Help::Html($row['nick']).'</td>
		          <td>'.Help::Html($row['email']).'</td>
		          <td class="text-right">'.Help::Html(round($row['zustatek'],2)).'</td>
		          <td>'.$mena_array[$row['mena_id']].'</td>
		          <td>'.It6_Date::fromDbAsDate($row['datum_narozeni']).'</td>
		          <td class="textcenter" >
		            '.It6_Date::fromDb($row['datum_registrace']).'
		          </td>
		          <td class="text-right"'.($row['vyhernost']>100?"background:red;color:white":"").'" nowrap="nowrap" >
		            '.$row['vyhernost'].' %
		          </td>
		          <td class="text-right">'.Help::Html($row['finance_rating']).' </td>
		          <td class="text-right">'.($saldo['vklad']-$saldo['vyber']).'</td>
					<td>'.$row['branch_name'].'</td>
		        </tr>
		      ';
	    }

		/*spodek*/
		$this->vrat .= '
			  </tbody>
			</table>
			<input type="hidden" name="orderDirection" value="" />
			<input type="hidden" name="orderBy" value="" />
			'.$lister->getOutput(null, 1, array('formId' => 'formUsersFilter')).'
		  </form>
		';

	} else {
		$this->vrat .= UiUtil::printWarnings(array('No users. Change the filter criteria.'));
	}
  }

 /**
 * Shoda
 * @param int $uid user_id
 * @return void
 */
  private function shoda($uid = 0){
    global $ipd;

          $vrat =  '';
          $user = $ipv = array();
          $status = false;


          $sth3 = $this->dbGame->prepare("select user_id,nick,xforward,persistent_id,ts from user_tracking where user_id=? ");
          if (PEAR::isError($sth3))  {throw new ExHandler($res->getMessage().'Nepodarilo se nacist preklady menu',"admin_ex_db");}
          $res3 =& $this->dbGame->execute($sth3,array($uid));
          if (PEAR::isError($res3))  {throw new ExHandler($res->getMessage().'Nepodarilo se  nacist preklady menu',"admin_ex_db");}


          while ($row =& $res3->fetchRow()){

            if(mb_strlen(trim($row['persistent_id'])) == 0 ) continue;

            $status = true;
            if(!isset($user['user_id'])) {$user['user_id'] = $row['user_id'];}

            $user['ip'][] = $row['xforward'];
            $user['per'][] = "'".Help::Slash($row['persistent_id'])."'";
            $user['time'][] = $row['ts'];

              $sql = "select  a.user_id,a.nick,a.email from uzivatel a inner join user_tracking b on a.user_id=b.user_id where

                a.user_id<>".$row['user_id']."  and (b.xforward not in('',".implode(",",$ipd).") and b.xforward = '".$row['xforward']."')";
             $res =& $this->dbGame->query($sql);//if($row['xforward'] == '62.77.88.215')echo $sql."<br>";
             if(DB::isError($res)) throw new ExHandler($sql.'Nepodarilo se provest dotaz: vyber uzivatele',"admin_ex_db");
             while ($row2 =& $res->fetchRow()){

                $ipv[$row2['user_id']]['id'] = '<p><a href="?section=40&user_id='.$row2['user_id'].'" target="_blank"> '.$row2['user_id'].'  </a></p>';
                $ipv[$row2['user_id']]['nick'] = $row2['nick'];
                $ipv[$row2['user_id']]['email'] = $row2['email'];

                $ipv[$row2['user_id']]['IP'] = 1;
             }
          }

          $user['per'][] = "'-'";

          $sth = $this->dbGame->prepare("select a.user_id,a.nick,b.email from uzivatel b inner join user_tracking a on b.user_id=a.user_id where a.persistent_id in (".implode(',',$user['per']).") and a.user_id<>? and a.xforward not in('',".implode(",",$ipd).")  group by a.user_id");
          if (PEAR::isError($sth))  {throw new ExHandler($res->getMessage().'Nepodarilo se nacist preklady menu',"admin_ex_db");}
          $res3 =& $this->dbGame->execute($sth,array($uid));
          if (PEAR::isError($res3))  {throw new ExHandler($res3->getMessage().'Nepodarilo se  nacist user_tracking',"admin_ex_db");}

         // echo "select a.user_id,a.nick,b.email from uzivatel b inner join user_tracking a on b.user_id=a.user_id where a.persistent_id in (".implode(',',$user['per']).") and a.user_id<>? and a.xforward not in('',".implode(",",$ipd).")  group by a.user_id";

         // print_r($user);

          while ($row =& $res3->fetchRow()){

            if(!isset($ipv[$row['user_id']])){
                $ipv[$row['user_id']]['id'] = '<p><a href="?section=40&user_id='.$row['user_id'].'" target="_blank"> '.$row['user_id'].'  </a></p>';
                $ipv[$row['user_id']]['nick'] = $row['nick'];
                $ipv[$row['user_id']]['email'] = $row['email'];
            }
            $ipv[$row['user_id']]['PC'] = 1;
            //$vrat .= '<p><a href="?section=40&user_id='.$row['user_id'].(isset($_GET['superb'])?'&superb=1':'').'" target="_blank"> #'.$row['user_id'].' '.$row['nick'].' </a></p>';

          }

         // echo "<pre>";print_r($ipv);
         ksort($ipv);
         if($status) $vrat .= '<strong>Počet shod: </strong>'. count($ipv);
          $vrat .= '<table cellpadding="4" border="1"><thead><tr><th>ID</th><th>Uživatelské jméno</th><th>EMAIL</th><th>PC</th><th>IP</th><th>Výhernost</th><th>Zůstatek</th></thead></tr>';
          foreach($ipv as $k=>$h){

             $win = $roz = 0;
             $sth3 = $this->dbGame->prepare("select fn_currency_user2central(a.user_id, b.zustatek) AS zustatek, a.vyhernost,round(((((select COALESCE(Sum(t.acctrans_amount),0) from accTrans t  where t.acctrans_sign=1 and t.acctrans_USR=?)-(select COALESCE(Sum(l.acctrans_amount),0) from accTrans l  where l.acctrans_sign=2 and l.acctrans_USR=?)-b.zustatek)/ (select e.kurz from game.kurzmena e where e.mena_id = a.mena_id and e.platny_od <= now() and e.platny_do >= now()))-b.zetony),2) AS rozdil from uzivatel a inner join uzivatel_im_data b on a.user_id=b.user_id where a.user_id=? ");
             if (PEAR::isError($sth3))  {throw new ExHandler($res->getMessage().'Nepodarilo se nacist preklady menu',"admin_ex_db");}
             $res3 =& $this->dbGame->execute($sth3,array($k,$k,$k));
             if (PEAR::isError($res3))  {throw new ExHandler($res3->getMessage().'Nepodarilo se  nacist preklady menu',"admin_ex_db");}
             if ($row =& $res3->fetchRow()){$win = $row['vyhernost'];$roz = $row['rozdil']; $zus = $row['zustatek']; }

             $vrat .= "<tr ><td>".$h['id']."</td><td>".$h['nick']."</td><td>".$h['email']."</td><td>".(isset($h['PC'])?'ANO':'NE')."</td><td>".(isset($h['IP'])?'ANO':'NE')."</td><td>". $win ." %</td><td>".$zus." </td></tr>";


          }
          $vrat .= '</table>';
          if($status) $vrat .= '<hr /><p><strong>Návštěvy</strong></p>';
          foreach($user['time'] as $k=>$h){

            $vrat .= '<p>'.It6_Date::fromDb($h).' - '.$user['ip'][$k].' ( '.$user['per'][$k].' )</p>';

          }

          if(!$status) $vrat .= 'Žádné shody <br /> <br />';





     return $vrat;

  }



 /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
	private function OnlineUser($uid = false) {

		if($uid && is_numeric($uid)) {
			$sql = "
				SELECT user_id
				FROM session
				WHERE (status=2 or status=3)AND user_id=".intval($uid)." and user_id not in(".implode(",",$GLOBALS['EXCLUDEUSER']).")";
			$res =& $this->dbSession->query($sql);
			if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

			if($res->NumRows() > 0) return true;
		}

	return false;
	}



 /**
 * Nastaveni prav k sekci
 * @param int $update pravo zapisu
 * @param int $delete pravo smazani
 * @return void
 */
	public function setPrivileges($update,$delete) {
		$this->update = $update;
		$this->delete = $delete;
	}



 /**
 * Vraci vystup do tridy main
 * @return string
 */
	public function getContent() {
		return $this->vrat;
	}



	private function getAdminName() {
		return It6_Session_Admin::getUserData('username');
	}



	private function getUSupplyValue($user_id,$name) {
		if(count($row = Zend_Registry::get('zdb_game')->select()
				->from("uzivatel_supply",array("s_data_value","dt_updated"))
				->where("user_id = ?", $user_id)
				->where("s_data_name = ?", $name)
				->limit(1)
			->query()->fetchAll())) {
			return array("value"=>$row[0]["s_data_value"], "ts"=>$row[0]["dt_updated"]);
		}
		return false;
	}



	public function __destruct() {
	}



	private function setOrder($orderBy) {
		if($_POST['orderDirection'] == 'ASC' && $orderBy == $_POST['orderBy'])
			return "'".$orderBy."','DESC'";
		else
			return "'".$orderBy."','ASC'";
	}


	private function parameters($iserId) {

	}
}
