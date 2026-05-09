<?php
/**
 * @package    service
 */

/**
 * Trida pro praci s odeslanym heslem
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class ZapomenuteHeslo{

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
* Konstruktor
*
*Pokud neni identifikator spojeni predan vytvori se nove spojeni
*
* @param int $section id aktualni sekce
* @param PEAR::DB $dbGame objekt spojeni s databazi
*/
  public function __construct($section=0,$dbGame=null){

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
 * Metoda vytvori nove heslo a posle na uzivatelsky mail
 * @param int $user_id id uzivatele
 * @param string $email email uzivatele
 * @param string $lang_id preferovany jazyk uzivatele uzivatele
 * @return void
 */
 public function NoveHeslo($user_id,$email,$lang_id){

  if($heslo = Help::PassGenerate()){

   $hash = Help::cryptPass($heslo);

   $this->dbGame->autocommit(false);

   $preklad = new Preklady();
   $res = $preklad->selectData("where lang_id=".$lang_id." and ( index_pole='mail_new_pass' or index_pole='mail_new_pass_sub')");


   $telo = $predmet = $mail_footer = "";

   while($row =& $res->fetchRow()){

	if($row['index_pole'] == 'mail_new_pass') $telo = $row['text'];
	else if($row['index_pole'] == 'mail_new_pass_sub') $predmet = $row['text'];


   }

   $telo = mb_ereg_replace("{new_pwd}",'<strong>'.$heslo.'</strong>',$telo);



	     $sql = "select iso from jazyky where lang_id=".$lang_id;
         $res =& $this->dbGame->query($sql);
         if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber jazyku',"admin_ex_db");

		 if ($row =& $res->fetchRow()) $iso = $row['iso'];


	     $osloveni  = $preklad->FindPreklad('mail_osloveni',$$lang_id); $osloveni = $osloveni[$lang_id];

	     $mail_footer = $preklad->FindPreklad('mail_footer',$lang_id); $mail_footer = $mail_footer[$lang_id]; //paticka
         $copy  = $preklad->FindPreklad('sys_copyright',$lang_id); $copy  = $copy [$lang_id];

		 include_once "shared/template/class.TemplatePower.inc.php";
		 $stpl = new TemplatePower("_tpl/mail_fbs.tpl");
         $stpl->prepare();
 		 $stpl->assign("FBSNAME",$osloveni);
 		 $stpl->assign("TEXT",$telo);
	     $stpl->assign("FOOT",$mail_footer);
	     $stpl->assign("COPY",$copy);


 		 $telo = $stpl->getOutputContent();


   include('shared/htmlMimeMail/htmlMimeMail.php');
   $mail = new htmlMimeMail();

   $mail->setTextCharset("UTF-8");
   $mail->setHeadCharset("UTF-8");
   $mail->setHTMLCharset("UTF-8");  $mail->html_charset = "UTF-8";$mail->text_encoding = "UTF-8";
   $mail->setHTML($telo);
   $mail->setFrom(INFOMAIL);
   $mail->setReturnPath(INFOMAIL);
   $mail->setSubject($predmet);


   $sql = "update uzivatel set heslo='".Help::Slash($hash)."', ucet_status=2 where user_id=".intval($user_id);
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update uzivatele',"admin_ex_db");

   if($mail->send(array($email))){
     $this->dbGame->commit();
     $this->vrat .= "<div class=\"okmsg\">Heslo bylo zminino a posláno na email uživatele</div><br />";
     	It6_Log::info(
			"New password generated for user '%user%' was added.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('user' => $user_id)
		);
   }else{
     $this->dbGame->rollback();
     $this->vrat .= "<div class=\"errormsg\">Nepodařilo se zminit heslo</div><br />";
   }


  }

 }


/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){



   $this->dbGame->disconnect();

  }


 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){

    return $this->vrat;

  }



}

?>
