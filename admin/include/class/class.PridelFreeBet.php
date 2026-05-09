<?php
/**
 * @package    bonus
 */

/**
 * Trida pro praci s pridelenim free bet bonusu
 *
 *
 * <code>
 *
 * </code>
 *
 * @package   Dokumenty
 */

class PridelFreeBet{

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
 * pocet odeslanych mailu
 * @access private
 * @var int
 */
private  static  $numSend = 0;


/**
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

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

    if(isset($_POST['send']))
  	  $this->pridelBonus();

  	else if(isset($_POST['sendemail']))
  	  $this->pridelBonusEmail();

    $this->showForm();

    $this->dbGame->disconnect();

  }

    /**
 * Metoda pridely bonus
 * @return void
 */
 private function pridelBonusEmail(){



 	if(!isset($_FILES['fileemail']['name']) ||  mb_strlen($_FILES['fileemail']['name'])<1) {$this->vrat .= "<div class=\"errormsg\">Nepodařilo se přidělit bonusy</div><br />";$status = false;}

 	$ar =file($_FILES['fileemail']['tmp_name']);


    $mena = array();
    $sql = "select e.kurz,e.mena_id from game.kurzmena e where e.platny_od<=now() and e.platny_do>=now() ";
    $res =& $this->dbGame->query($sql);
    while ($row =& $res->fetchRow()){
    	$mena[$row['mena_id']] = $row['kurz'];
    }

 	foreach($ar as $h){
    $sql = "select user_id,mena_id from uzivatel  where email='".Help::Slash($h)."'";
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");
 	if($row =& $res->fetchRow()) {

 	   	  if(!$this->pridelBonus($row['user_id'],(intval($_POST['castkaemail'])*$mena[$row['mena_id']])))
 	   	  	break;
 	}
 	}

 	$this->vrat .= "<div class=\"okmsg\">Posláno ".self::$numSend." x</div><br />";

 }

   /**
 * Metoda pridely bonus
 * @return void
 */
 public function pridelBonus($us=null,$castka=null,$message="",$dbmessage=""){




 	$status = true;
 	$uid = false;
 	$email = $mena = "";
 	$lang = 0;

 	if($us != null)    $_POST['uid'] = $us;
 	if($castka != null) $_POST['castka'] = $castka;

 	if(!is_numeric($_POST['castka'])) {$this->vrat .= "<div class=\"errormsg\">Castka neni cislo</div><br />";$status = false;}

 	if(!isset($_POST['uid']) && !isset($_POST['nick'])) {$this->vrat .= "<div class=\"errormsg\">Nick nebo UID mus být uvedeno</div><br />";$status = false;}
 	if(!isset($_POST['castka']) || intval($_POST['castka']) == 0){$this->vrat .= "<div class=\"errormsg\">Částka není platná</div><br />";$status = false;}

 	if(isset($_POST['uid'])){

 	  $sql = "select a.user_id,b.mena_text,a.lang_id,a.email from uzivatel a inner join mena b  on a.mena_id=b.mena_id where a.user_id=".intval($_POST['uid']);
      $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");
 	  if($row =& $res->fetchRow()) {$uid = $row['user_id'];$mena=$row['mena_text'];$email=$row['email'];$lang=$row['lang_id'];}


 	}
 	else if(isset($_POST['nick'])){

 	  $sql = "select a.user_id,b.mena_text,a.lang_id,a.email from uzivatel a inner join mena b  on a.mena_id=b.mena_id where nick='".Help::Slash($_POST['nick'])."'";
      $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");
 	  if($row =& $res->fetchRow()) {$uid = $row['user_id'];$mena=$row['mena_text'];$email=$row['email'];$lang=$row['lang_id'];}

 	}



 	if($status && $uid){
 		self::$numSend++;

 		do{

 		  $kod = "fbb-".substr(md5(time()),0,6);

 		  $sql = "select * from ticket_bonus_uzivatel where kod='".$kod."'";
      $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");

 		}while($res->numRows()>0);

 		$preklad = new Preklady();
 		$predmet = $preklad->FindPreklad('mail_fbb_pred',$lang); $predmet = $predmet[$lang];
    $telo = $preklad->FindPreklad('mail_fbb_free_body',$lang); $telo = $telo[$lang];
    $mail_footer = $preklad->FindPreklad('mail_footer',$lang); $mail_footer = $mail_footer[$lang]; //paticka
    $begin  = $preklad->FindPreklad('mail_fbb_anotace',$lang); $begin  = $begin [$lang];
	  $osloveni  = $preklad->FindPreklad('mail_osloveni',$lang); $osloveni = $osloveni[$lang];
	  $copy  = $preklad->FindPreklad('sys_copyright',$lang); $copy  = $copy [$lang];

    $predmet = str_replace("{max_amount}",($_POST['castka']),$predmet);
    $predmet = str_replace("{mena_text}",$mena,$predmet);

    $telo = mb_ereg_replace("{code}",'<table style="margin:10px 0px;"><tr><td style="padding:10px;background-color:#252525;width:auto;">Freebet ('.round($_POST['castka'],2).' '.$mena.')</td></tr></table>',$telo);
    $telo = str_replace("{info}",$_POST['text'],$telo);


    include_once "shared/template/class.TemplatePower.inc.php";

		$stpl = new TemplatePower("_tpl/mail_fbs.tpl");
    $stpl->prepare();



	  $body = mb_ereg_replace("{URL}","<a href=\"".WEBHOST.$menu[24]."\">".WEBHOST.$menu[24]."</a>",$body);
	  $body = mb_ereg_replace("{CODE}",'<table style="margin:10px 0px;"><tr><td style="padding:10px;background-color:#252525;width:auto;">'.$text.'</td></tr></table>',$body);

	  $stpl->assign("FBSNAME",$osloveni);
//$stpl->assign("BEGIN",$begin );
//$stpl->assign("CODE",'');
    $stpl->assign("TEXT",$telo);
    $stpl->assign("FOOT",$mail_footer);
    $stpl->assign("COPY",$copy);
    $stpl->assign("TEXT2",$message);

    $telo = $stpl->getOutputContent();

 		include('htmlMimeMail/htmlMimeMail.php');
		$mail = new htmlMimeMail();
		$mail->addHTMLImage($mail->getFile($_SERVER["DOCUMENT_ROOT"].'/_clip_mail/logo2.gif'),'logo2.gif','image/jpeg');
		$mail->setTextCharset("UTF-8");
		$mail->setHeadCharset("UTF-8");
		$mail->setHTMLCharset("UTF-8");  $mail->html_charset = "UTF-8";$mail->text_encoding = "UTF-8";
		$mail->setHTML($telo);
		$mail->setFrom(NOREPLY);
		$mail->setReturnPath(NOREPLY);
		$mail->setSubject($predmet);

 		if(strlen($_POST['s_poznamka']) > 1){
	 		$ommes = Help::Slash($_POST['s_poznamka']);
	 		$sql = "INSERT INTO uzivatel_poznamka (user_id, text, admin_id, datum)
	 				VALUES (" . intval($uid) . ", '$ommes', " . intval(It6_Session_Admin::getUserData('id')) . ", NOW())";
			Zend_Registry::get('zdb_game')->query($sql);
	 	}
	 	else{
	 		$this->vrat .= "<div class=\"errormsg\">Free bet nebyl poslán nebyla vyplňen důvod</div><br />";
	 		return false;
	 	}


    $mail->send(array($email));

//added by martin on 5.5. 2010 to solve foreign key constraint violation upon adding new bonus ticket, not sure how this thing works
 		$sql = "INSERT INTO ticket_bonus (nazev) VALUES ('')";
    $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");


 		$sql = "INSERT INTO ticket_bonus_uzivatel (ticket_bonus_id,user_id,kod,bonus_castka,poznamka) VALUES (1,".intval($uid).",'".Help::Slash($kod)."',".round($_POST['castka'],2).",'".$dbmessage."')";
    $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani admina z databáze',"admin_ex_db");


 		$this->vrat .= "<div class=\"okmsg\">Free bet byl poslán</div><br />";

		It6_Log::info(
			"Free bet bonus sent to user '%user%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('user' => intval($uid))
		);
 	}

 	return true;
 }

 /**
 * Metoda zobrazi formular pro vlozeni
 * @return void
 */
 private function showForm(){

 	$this->vrat .= '
		<script>
			function FindCurrency(n){
				var inv = (n==1?"uid":"nick");
        var datas = (n==1?$("#uid").attr("value"):$("#nick").attr("value"));

				if(datas !== ""){
					if((inv == "uid" && !isNaN(datas)) || inv == "nick"){
						$.get("ajax.server.php", { work:5,info: inv,dat:datas.toString() },function(data){
							$("#currency").text(data);
						} );
					}
					else{
						alert(\'Indetifikátor uživatele musí být celé číslo.\')
					}
				}
      }
    </script>
	';

 	$this->vrat .= '
		<h2>Nový freebet</h2>
		<form name="filesend" method="post"  enctype="multipart/form-data" action="?section='.$this->section.'">
      <table id="tabnewdoc">
	      <tr>
					<td>Indetifikátor uživatele</td>
					<td>Uživatelské jméno</td>
					<td>Měna</td>
					<td>Částka bonusu</td>
				</tr>
				<tr>
					<td>
						<input type="text" class="sinput" onblur="FindCurrency(1);" id="uid" name="uid" />
					</td>
          <td>
						<input type="text" class="sinput" onblur="FindCurrency(2);" id="nick" name="nick" />
					</td>
          <td id="currency"></td>
          <td>
						<input type="text" class="sinput" id="castka" name="castka" />
					</td>
					<td>
						<input type="submit" name="send" value="Poslat bonus"  />
					</td>
        </tr>
				<tr>
					<td valign="top">Mail text</td>
          <td colspan="4">
						<textarea id="text" name="text" cols="30" rows="4"></textarea>
					</td>
				</tr>
        <tr>
					<td valign="top">Důvod</td>
          <td colspan="4">
						<textarea id="text" name="s_poznamka" cols="30" rows="4"></textarea>
					</td>
				</tr>
		  </table>
		</form>
		<hr />
	';
 }


 /**
 * Vraci vystup do tridy main
 * @return string
 */
  public function getContent(){

    return $this->vrat;

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

  public function __destruct(){




  }

}

?>
