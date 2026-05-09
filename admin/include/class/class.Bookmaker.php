<?php
/**
 * @package  book
 */

//TODO:ADMIN cela trida refaktorovat resp. vyjit z AdminKolekce a pouzit ji na vsechny typy uzivatelu


 /**
 * Trida pro praci s bookmakery
 *
 * Vytvaret mazat a muze superadmin, zakazovat muze i superbookmaker
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    main
 */

class Bookmaker extends Template{

/**
 * objekt databaze spojeni s databazi game
 * @access private
 * @var PEAR::DB
 */
private  $dbGame;

/**
 * spojeni na databazi admin
 * @access private
 * @var DB
 */
private  $db;

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

/**
 * vytvori chybovy XML response podle vyhozene vyjimky
 * @param DB_PEAR $db spojeni na databazi
 * @param int $section isd sekce
 * @return void
 */
  public function __construct($section='b1',$dbGame=null){

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


  }

 /**
 * Metoda spousti jednitlove metody podle stavu
 * @return void
 */
  public function runAction(){

   #vytvoreni noveho uzivatele#
   if(isset($_POST['create'])){

    if($this->update)
         $this->createBookmaker();
     else
	     $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vytvoreni bookmakera</div>\n";

   }

   #smazani bookmakera#
   else if(isset($_POST['delete'])){

     if($this->delete)
         $this->deleteBookmaker();
     else
	     $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vymazani bookmakera</div>\n";

   }

   #povoleni/zakazani#
   else if((isset($_POST['povolit']) || isset($_POST['zakazat']))){

	 if($this->update)
          $this->PovolitZakazat((isset($_POST['povolit'])?key($_POST['povolit']):key($_POST['zakazat'])));
     else
	     $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci uživatele</div>\n";

   }


   if(isset($_POST['edit']))
     $this->showBookmakerDetail(intval(key($_POST['edit'])));
   else if(isset($_POST['editbook']))
     $this->showBookmakerDetail(intval(key($_POST['editbook'])));
   else
     $this->showBookmakers();


  	$this->dbGame->disconnect();
	$this->db->disconnect();

  }


 /**
 * Metoda povoluje/zakazuje uzivatele
 * @param int $book_id id uzivatele
 * @return void
 */
 private function PovolitZakazat($book_id){

     $sql = "update bookmaker set zakazany=".(isset($_POST['povolit'])?0:1)." where bookmaker_id=".intval($book_id);
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: update bookmakera',"admin_ex_db");

	 $this->vrat .= "<div class=\"okmsg\">Bookmaker byl úspěšně (".(isset($_POST['povolit'])?"povolen":"zakázán").")</div><br />";

	$nick = (isset($_POST['nick']) && mb_strlen($_POST['nick']) > 0?$_POST['nick']:$_POST['book'][intval($book_id)]['nick']);
	if ( $_POST['povolit'] )
		It6_Log::info(
			"Bookamker '%nick%' allowed.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('nick' => $nick));
	else
		It6_Log::info(
			"Bookamker '%nick%' banned.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('nick' => $nick));

 }


  /**
 * Metoda maze bookmakere
 *
 *zaznamy v ostatnich tabulkach tykajici se vymazaneho bookmakera zustavaji
 *
 * @param int $book_id id uzivatele
 * @return void
 */
 private function deleteBookmaker() {

	$id = intval(key($_POST['delete']));

	$sql = "delete from bookmaker where bookmaker_id=".$id;
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: delete bookmakera',"admin_ex_db");

	$this->vrat .= "<div class=\"okmsg\">Bookmaker byl úspěšně smazán</div><br />";
	It6_Log::info(
		"Bookmaker '%nick%' deleted.",
		It6_Log::TAG_ADMIN_OPERATION,
		array('nick' => (isset($_POST['nick']) && mb_strlen($_POST['nick']) > 0?$_POST['nick']:$_POST['book'][intval($id)]['nick'])));

 }

 /**
 * Metoda vytvari noveho bookmakera
 * @return void
 */
 final private function createBookmaker(){


  if($this->update){

   $status = true;

	  if(!isset($_POST['heslo']) || !Help::checkPass($_POST['heslo'])) {$this->vrat .= "<div class=\"errormsg\"><strong>Heslo</strong> má špatný formát (6-16 znaků, nesmí začínat číslem, minimálně 2 čísla)</div><br />";$status = false;}
	  if($_POST['heslo'] != $_POST['heslo2']) {$this->vrat .= "<div class=\"errormsg\">Potvrzení hesla nesouhlasí</div><br />";$status = false;}
	  if(!isset($_POST['nick']) || !Help::checkNick($_POST['nick']))  {$this->vrat .= "<div class=\"errormsg\"><strong>Uživatelské jméno</strong> musí být uvedeno a mít správný formát (4-20 znaků, nesmí začínat číslem)</div><br />";$status = false;}
	  if(!isset($_POST['email']) || !Help::valideMail($_POST['email']) || mb_strlen(trim($_POST['email'])) > 80 || mb_strlen(trim($_POST['email'])) < 6)  {$this->vrat .= "<div class=\"errormsg\"><strong>Mail</strong> má špatný formát (min. 6 znaků)</div><br />";$status = false;}
	  if(!isset($_POST['jmeno']) || mb_strlen(trim($_POST['jmeno']),'utf-8') > 40 || mb_strlen(trim($_POST['jmeno']),'utf-8') < 1)  {$this->vrat .= "<div class=\"errormsg\"><strong>Jméno</strong> musí být uvedeno (max 40 znaků)</div><br />";$status = false;}
	  if(!isset($_POST['prijmeni']) || mb_strlen(trim($_POST['prijmeni']),'utf-8') > 60 || mb_strlen(trim($_POST['prijmeni']),'utf-8') < 1)  {$this->vrat .= "<div class=\"errormsg\"><strong>Přijmení</strong> musí být uvedeno (max 60 znaků)</div><br />";$status = false;}
	  if(isset($_POST['nick']) &&  mb_strlen(trim($_POST['nick'])) > 0){

	    $sql = "select nick from bookmaker where nick='".Help::slash($_POST['nick'])."'";
        $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani bookmaker z databáze',"admin_ex_db");

		if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tento \" <strong>Nick</strong> již existuje</div><br />";$status = false;}

	  }

	if ( $status ) {

		$sql = "insert into bookmaker(jmeno,prijmeni,nick,heslo,email,super)
		 values('".Help::slash($_POST['jmeno'])."','".Help::slash($_POST['prijmeni'])."','".Help::slash($_POST['nick'])."',
		 '".Help::cryptPass($_POST['heslo'])."','".Help::slash($_POST['email'])."',".(isset($_POST['super'])?1:0).")";
		$res =& $this->dbGame->query($sql);
		if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni noveho bookmakera',"admin_ex_db");

		$this->vrat .= "<div class=\"okmsg\">Bookmaker byl úspěšně vložen</div><br />";

		It6_Log::info(
			"New bookmaker '%nick%' inserted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('nick' => $_POST['nick']));

	}

   }else
       $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vytvoření bookmaker</div>\n";



 }

  /**
 * Metoda edituje existujícího bookmakera
 * @param int $bookmaker_id id bookmakera
 * @return void
 */
 final private function EditBookmaker($bookmaker_id){

    if($this->update){

      $status = true;

	  if(mb_strlen($_POST['heslo']) > 0 && (!isset($_POST['heslo']) || !Help::checkPass($_POST['heslo']))) {$this->vrat .= "<div class=\"errormsg\"><strong>Heslo</strong> má špatný formát (6-16 znaků, nesmí začínat číslem, minimálně 2 čísla)</div><br />";$status = false;}
	  if($_POST['heslo'] != $_POST['heslo2']) {$this->vrat .= "<div class=\"errormsg\">Potvrzení hesla nesouhlasí</div><br />";$status = false;}
	  if(!isset($_POST['nick']) || !Help::checkNick($_POST['nick']))  {$this->vrat .= "<div class=\"errormsg\"><strong>Uživatelské jméno</strong> musí být uvedeno a mít správný formát (4-20 znaků, nesmí začínat číslem)</div><br />";$status = false;}
	  if(!isset($_POST['email']) || !Help::valideMail($_POST['email']) || mb_strlen(trim($_POST['email'])) > 80 || mb_strlen(trim($_POST['email'])) < 6)  {$this->vrat .= "<div class=\"errormsg\"><strong>Mail</strong> má špatný formát (min. 6 znaků)</div><br />";$status = false;}
	  if(!isset($_POST['jmeno']) || mb_strlen(trim($_POST['jmeno']),'utf-8') > 40 || mb_strlen(trim($_POST['jmeno']),'utf-8') < 1)  {$this->vrat .= "<div class=\"errormsg\"><strong>Jméno</strong> musí být uvedeno (max 40 znaků)</div><br />";$status = false;}
	  if(!isset($_POST['prijmeni']) || mb_strlen(trim($_POST['prijmeni']),'utf-8') > 60 || mb_strlen(trim($_POST['prijmeni']),'utf-8') < 1)  {$this->vrat .= "<div class=\"errormsg\"><strong>Přijmení</strong> musí být uvedeno (max 60 znaků)</div><br />";$status = false;}
	  if(isset($_POST['nick'])){

	    $sql = "select nick from bookmaker where bookmaker_id<>".$bookmaker_id." and nick='".Help::slash($_POST['nick'])."'";
        $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani bookmaker z databáze',"admin_ex_db");

		if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tento \" <strong>Nick</strong> již existuje</div><br />";$status = false;}

	  }

	   if($status){


        $sql = "update bookmaker set jmeno='".Help::slash($_POST['jmeno'])."',prijmeni='".Help::slash($_POST['prijmeni'])."',
		nick='".Help::slash($_POST['nick'])."',heslo=".(mb_strlen($_POST['heslo']) > 0?"'".Help::cryptPass($_POST['heslo'])."'":"heslo").",
		email='".Help::slash($_POST['email'])."',super=".(isset($_POST['super'])?1:0)." where bookmaker_id=".$bookmaker_id;
        $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vlozeni noveho bookmakera',"admin_ex_db");


	    $this->vrat .= "<div class=\"okmsg\">Bookmaker byl úspěšně aktualizován</div><br />";

		It6_Log::info(
			"Bookmaker '%bookmaker%' was updated.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('bookmaker' => $_POST['nick'])
		);
	  }

   }else
       $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci bookmaker</div>\n";


 }

     /**
 * vraci udaje o bookmakerech
 * @return object
 */
  public function SelectData($where = ""){

     $sql = "select bookmaker_id,nick, zakazany,jmeno,prijmeni,email,super from bookmaker ".$where;
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

	 return $res;

  }



   /**
 * Metoda resi detail bookmakera
 * @param int $bookmaker_id  id bookmakera
 * @return void
 */
 final private function showBookmakerDetail($bookmaker_id){

	if(isset($_POST['editbook'])){
	 if($this->update)
          $this->EditBookmaker($bookmaker_id);
     else
	     $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci bookmakera</div>\n";
	}


    $sql = "select * from uzivatel where bookmaker_id=".$bookmaker_id;
    $res =& $this->selectData("where bookmaker_id=".$bookmaker_id);

	if($res->numRows() < 1) throw new ExHandler('Neexistující bookmaker',"admin_ex_user");

	$row =& $res->fetchRow();



   	$this->vrat .= "<div class=\"floatright rightMenuParent\">
	                <a href=\"javascript:show('profil',['history','profil']);void(0);\" class=\"rightMenu\">Profil</a>
					<a href=\"javascript:show('history',['history','profil']);void(0);\" class=\"rightMenu\">Historie</a>
	                </div>";


	$this->vrat .= "<form method=\"post\" action=\"?section=".$this->section."\">";

	$this->vrat .= "<table style=\"display:none\" id=\"history\"><col /><col class=\"licha\" /><col /><col class=\"licha\" />".$this->BookAkce($bookmaker_id)."</table>";

    $this->vrat .= ' <div id="profil"><table><tr><td colspan="9" class=""><h3>Profil uživatel '.($this->OnlineBookmaker($bookmaker_id)?"<span class=\"blue floatright\">ONLINE</span>":"<span class=\"red floatright\" >OFFLINE</span>").'</h3></td></tr>

					  <tr><td colspan="9">Bookmaker '.($row['zakazany']?"<span class=\"red\">je zakázaný</span>":"<span class=\"blue\">povolený</span>").'</td></tr>
					  <tr><td>&nbsp;</td></tr>
					   <tr><th colspan="2">Jméno</th> <td colspan="7"><input type="text" class="classic mandatory" maxlength="40" name="jmeno" value="'.Help::Html($row['jmeno']).'" /></td></tr>
	                  <tr><th colspan="2">Přijmení</th><td colspan="7"><input type="text" class="classic mandatory" maxlength="60" name="prijmeni" value="'.Help::Html($row['prijmeni']).'" /></td></tr>
					  <tr><th colspan="2">Uživatelské jméno</th><td colspan="7"><input type="text" class="classic mandatory" maxlength="20" name="nick" value="'.Help::Html($row['nick']).'" /></td></tr>
					  <tr><th colspan="2">Heslo</th><td colspan="7"><input type="password" class="classic mandatory" maxlength="16" name="heslo" value="" /></td></tr>
					  <tr><th colspan="2">Heslo znovu</th><td colspan="7"><input type="password" class="classic mandatory" maxlength="16" name="heslo2" value="" /></td></tr>
					  <tr><th colspan="2">Email</th><td colspan="7"><input type="text" class="classic mandatory" maxlength="80" name="email" value="'.Help::Html($row['email']).'" /></td></tr>
					  <tr><th colspan="2">Admin bookmaker</th><td  colspan="7"><input type="checkbox" class="no" value="" '.($row['super'] == 1?"checked=\"checked\"":"").' name="super" /></td></tr>


					  </td></tr></table></div>

					  <table>
					  <tr><td >&nbsp;</td><td class="suda" colspan="8"><input type="submit" name="editbook['.$bookmaker_id.']" class="sbutton" value="Uložit změny" />
					   &nbsp;'.($row['zakazany']?'<input type="submit" name="povolit['.$bookmaker_id.']" class="blue" value="Povolit" />':'<input type="submit" name="zakazat['.$bookmaker_id.']" class="red" value="Zakázat" />').'
					  </td></tr>
					  </tbody></table><input type="hidden" name="edit['.$bookmaker_id.']" value="1" /></form>';

 }

   /**
    *vyjede vsechny akce daneho bookmakera
	*
	* @param int $book_id id bookmaker
    * @return string
  */
  private function BookAkce($book_id){

   $sql = "select data,time,start,status,zprava from session where bookmaker_id=".$book_id." order by start desc";
   $res =& $this->db->query($sql);
   if(DB::isError($res)) throw new ExHandler('<br>Nepodarilo se provest dotaz: vyber z tabulky session',"admin_ex_db");

   $vrat = "<br /><br />";

   $vrat .= "<ol style=\"list-style-type:decimal;display:none\" style=\"\" id=\"history\">\n";

   while ($row =& $res->fetchRow()){

     $row['data'] = Help::session_real_decode($row['data']);

	 switch($row['status']){
	  case 2: $status =  "Aktivní";break;
	  case 3: $status =  "Právě přihlášen";break;
	  case 1: $status =  "Vypršela session";break;
	  case 4: $status =  "Vyhozený (".$row['zprava'].")";break;
	  default: $status =  "Nepřihlášený";
	 }

     $vrat .= "<li onclick=\"displayObj(this.childNodes[1]);\" >".$row['start']." [".$status."] ";

	 $vrat .= "<ul style=\"display:none\">";

	 if(isset($row['data']['lastaction'])){
	   ksort($row['data']['lastaction'],"Help::Kcmp");
	   $row['data']['lastaction'] = array_reverse($row['data']['lastaction'],true);

	   foreach($row['data']['lastaction'] as $k=>$h){
	     $vrat .= "<li class=\"historyli2\">".date("H:i:s Y.m.d",$k)." ".$h."</li>";
	   }

	 }else $vrat.= "<li>Žádné akce</li>";

	 $vrat .= "</ul></li>";

   }

   $vrat .= "</ol>";

   return $vrat;

  }

 /**
 * Metoda vypise vsechny bookmakery
 * @return void
 */
 private function showBookmakers(){

	 $where = (isset($_REQUEST['fulltext']) && mb_strlen($_REQUEST['fulltext']) > 0 ?"jmeno like '%".Help::Slash($_REQUEST['fulltext'])."%' or prijmeni like '%".Help::Slash($_REQUEST['fulltext'])."%' or nick like '%".Help::Slash($_REQUEST['fulltext'])."%' or email like '%".Help::Slash($_REQUEST['fulltext'])."%'":"1");

     $sql = "select * from bookmaker a where ".$where;
     $res2 =& $this->dbGame->query($sql);
	 if(DB::isError($res2)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky bookmaker',"admin_ex_db");

	 $page = new Page($res2->numRows(),PAGE,"section=".$this->section.(isset($_REQUEST['fulltext']) && mb_strlen($_REQUEST['fulltext']) > 0?"&fulltext=".$_REQUEST['fulltext']:""));

     $sql = "select a.bookmaker_id,a.jmeno,a.prijmeni,a.nick,a.email,a.super,a.zakazany from bookmaker a where ".$where." order by ".(isset($_GET['order']) && isset($_GET['desc'])?$_GET['order']." ".$_GET['desc']:"bookmaker_id desc")."  limit ".(PAGE*$page->page).",".PAGE;
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky bookmaker',"admin_ex_db");



	 $this->vrat .= "<form method=\"post\" action\"?section=".$this->section."\">";

	 #hlavicka#
	 $this->vrat .= "
	                <table class=\"table-filter\"><thead>
					 <tr><td colspan=\"9\">Vyhledávání ve jménu, přijmení, nicku a emailu</td></tr>
					 <tr><td colspan=\"9\">Fulltext: <input type=\"text\" id=\"fulltext\" name=\"fulltext\" value=\"".(isset($_REQUEST['fulltext'])?Help::Html($_REQUEST['fulltext']):"")."\" \>&nbsp;<input type=\"submit\" value=\"Vyhledat\" /></td></tr>
	                 <tr><td colspan=\"9\">&nbsp;</td></tr>
					 <tr><td colspan=\"3\">Počet záznamů: <strong>".$res2->numRows()."</strong></td><td colspan=\"6\" class=\"suda textright\">".$page->getPage()."</td></tr>
					 <tr><td colspan=\"9\">&nbsp;</td></tr>
					 </thead></table>";

	 $x = 1;

	 $this->vrat .= "<table class=\"table-default table-bookmaker\">";
	 $this->vrat .= "<tr><th>&nbsp;</th>
					 <th><a href=\"?section=".$this->section."&order=jmeno".(isset($_REQUEST['fulltext'])?"&fulltext=".urlencode($_REQUEST['fulltext']):"")."&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."\">Jméno</a></th>
					 <th><a href=\"?section=".$this->section."&order=prijmeni".(isset($_REQUEST['fulltext'])?"&fulltext=".urlencode($_REQUEST['fulltext']):"")."&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."\">Přijmení</a></th>
					 <th><a href=\"?section=".$this->section."&order=nick".(isset($_REQUEST['fulltext'])?"&fulltext=".urlencode($_REQUEST['fulltext']):"")."&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."\">Nick</a></th>
					 <th><a href=\"?section=".$this->section."&order=email".(isset($_REQUEST['fulltext'])?"&fulltext=".urlencode($_REQUEST['fulltext']):"")."&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."\">Email</a></th>
					 <th><a href=\"?section=".$this->section."&order=super".(isset($_REQUEST['fulltext'])?"&fulltext=".urlencode($_REQUEST['fulltext']):"")."&desc=".(isset($_GET['desc']) && $_GET['desc'] == "asc"?"desc":"asc")."\">Admin bookmaker</a></th>
					 <th>&nbsp;</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead><tbody>";
	 /*telo*/
	 while ($row =& $res->fetchRow()){

	 $this->vrat .= '<tr '.($row['zakazany']?"style=\"background:#CC0000\"":($this->OnlineBookmaker($row['bookmaker_id'])?"style=\"background:#3366cc\"":"")).'>
	                  <td>'.((PAGE*$page->page)+$x).'.</td>
	                  <td><input type="text" class="sinput" maxlength="50" name="book['.$row['bookmaker_id'].'][jmeno]" value="'.Help::Html($row['jmeno']).'" /></td>
	                  <td><input type="text" class="sinput" maxlength="60" name="book['.$row['bookmaker_id'].'][prijmeni]" value="'.Help::Html($row['prijmeni']).'" /></td>
					  <td><input type="text" class="sinput" maxlength="16" name="book['.$row['bookmaker_id'].'][nick]" value="'.Help::Html($row['nick']).'" /></td>
					  <td><input type="text" class="sinput" maxlength="16" name="book['.$row['bookmaker_id'].'][email]" value="'.Help::Html($row['email']).'" /></td>
					  <td class="textcenter '.($row['super']?"blue":"red").'">'.($row['super']?"ANO":"NE").'</td>
					  <td><input type="submit" name="'.($row['zakazany']?"povolit":"zakazat").'['.$row['bookmaker_id'].']" class="sbutton" value="'.($row['zakazany']?"Povolit":"Zakázat").'" /></td>
					  <td><input type="submit" name="edit['.$row['bookmaker_id'].']" class="sbutton" value="Detail" /></td>
					  <td><input type="submit" name="delete['.$row['bookmaker_id'].']" onclick="if(!confirm(\'Opravdu chcete smazat bookmakera?\')) return false;" class="sbutton" value="Smazat" /></td>
					  </tr>';
	  $x++;
	 }
	  /*spodek*/
	  $this->vrat .= '</tbody></table>';
	  $this->vrat .= '<table class="table-default form-insert">';
		$this->vrat .=  '<tr><th colspan="3">Nový Bookmaker</th></tr>
					  <tr><th colspan="2">Jméno</th> <td ><input type="text" class="classic mandatory" maxlength="40" name="jmeno" value="'.(isset($_POST['jmeno'])?Help::Html($_POST['jmeno']):"").'" /></td></tr>
						<tr><th colspan="2">Přijmení</th><td><input type="text" class="classic mandatory" maxlength="60" name="prijmeni" value="'.(isset($_POST['prijmeni'])?Help::Html($_POST['prijmeni']):"").'" /></td></tr>
					  <tr><th colspan="2">Uživatelské jméno</th><td ><input type="text" class="classic mandatory" maxlength="20" name="nick" value="'.(isset($_POST['nick'])?Help::Html($_POST['nick']):"").'" /></td></tr>
					  <tr><th colspan="2">Heslo</th><td><input type="password" class="classic mandatory" maxlength="16" name="heslo" value="" /></td></tr>
					  <tr><th colspan="2">Heslo znovu</th><td><input type="password" class="classic mandatory" maxlength="16" name="heslo2" value="" /></td></tr>
					  <tr><th colspan="2">Email</th><td><input type="text" class="classic mandatory" maxlength="80" name="email" value="'.(isset($_POST['email'])?Help::Html($_POST['email']):"").'" /></td></tr>
					  <tr><th colspan="2">Admin bookmaker</th><td ><input type="checkbox" class="no" value="" '.(isset($_POST['super'])?"checked=\"checked\"":"").' name="super" /></td></tr>
					  <tr><td >&nbsp;</td><td class="suda" colspan="2"><input type="submit" name="create" class="sbutton" value="Vytvořit" /></td></tr>
					  </table></form>';

 }

 /**
 * Metoda zjistuje zda je bookmaker online
 * @param int $uid
 * @return void
 */
  private function OnlineBookmaker($uid = false){

   if($uid && is_numeric($uid)){

    $sql = "select bookmaker_id from session where (status=2 or status=3) and bookmaker_id=".intval($uid);
    $res =& $this->db->query($sql);
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
