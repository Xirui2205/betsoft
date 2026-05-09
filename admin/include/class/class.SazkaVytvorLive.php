<?php
/**
 * @package    book
 */

/**
 * Trida pro vytvoreni live sazek
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class SazkaVytvorLive extends Template{

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
 * zadavani multisazky
 * @access private
 * @var bool
 */
private  $multisazka = false;

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

   if($this->section == "b12") $this->multisazka = true;

  }

/**
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){

   if(isset($_POST['create'])){

    $this->CreateBet();

   }

   $this->ShowBet();

   $this->vrat .= "<img src=\"".HOST."_clip/mandatory.gif\" class=\"img\" /> <small>Takto označený údaj je povinný</small>";

   $this->dbGame->disconnect();

  }


 /**
 * Vytvoreni sazky
 * @return void
 */
  private function CreateBet(){

   $status = true;
   $this->dbGame->autoCommit(false);

   if(!isset($_POST['sazka']) || !is_array($_POST['sazka'])) {$this->vrat .= "<div class=\"errormsg\"> Nebyla zvolena žádná sázka</div><br />";$status = false;}
   if(!isset($_POST['udalost']) || $_POST['udalost'] == 0) {$this->vrat .= "<div class=\"errormsg\"> <strong>Turnaj/Událost</strong> musí být zvolena</div><br />";$status = false;}
   if(isset($_POST['udalost']) && $_POST['udalost'] != 0){  //kontrola zda udalost nevyprsela a uzivatel ma pravo ji editovat

	$sql = "select * from udalost where platne_do>'".It6_Date::dbNow()."' and udalost_id=".intval($_POST['udalost']);
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: editace typu',"admin_ex_db");}
    if (!$row =& $res->fetchRow()){$this->vrat .= "<div class=\"errormsg\"> <strong>Turnaj/Událost</strong> už je ukončena nelze podávat sázky</div><br />";$status = false;}

	if($_SESSION['superbookmaker'] != 1){

	 $sql = "select * from prava_udalost where  bookmaker_id=".intval($_SESSION['bookmaker'])." and povoleny=1 and udalost_id=".intval($_POST['udalost']);
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: editace typu',"admin_ex_db");}
     if (!$row =& $res->fetchRow()){$this->vrat .= "<div class=\"errormsg\"> Nemáte právo vytvážet sázky v tomto turnaji/události</div><br />";$status = false;}

	}

   }

   foreach($_POST['sazka'] as $k=>$h){

    if(!isset($h['typ']) || $h['typ'] == 0) {$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> <strong>Typ</strong> musí být zvolen</div><br />";$status = false;}
    if(!isset($h['podtyp']) || $h['podtyp'] == 0) {$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> <strong>Podtyp</strong> musí být zvolen</div><br />";$status = false;}
    if(isset($h['typ']) && $h['typ'] != 0 && isset($h['podtyp']) && $h['podtyp'] != 0){  //kontrola zda typ a podtyp  jsou navzajem propojene

	 $sql = "select * from typ_podtyp where sport_id=".intval($_POST['sport'])." and typ_id=".intval($h['typ'])." and podtyp_id=".intval($h['podtyp']);
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: editace typu',"admin_ex_db");}
     if (!$row =& $res->fetchRow()){$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> ".$k." -> Spojení tohoto typu a podtypu není povoleno</div><br />";$status = false;}

    }

    if((!isset($h['radek']) || $h['radek'] == 0) && mb_strlen($h['text']) < 1){$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> <strong>Text</strong> musí být u tothoto typu sázky uveden</div><br />";$status = false;}
    if(!It6_Date::checkFormat($h['platna_od'])){$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> <strong>Platné od</strong> nemá správný formát RRRR-mm-dd HH:mm:ss</div><br />";$status = false;}
    if(!It6_Date::checkFormat($h['platna_do'])){$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> <strong>Platné do</strong> nemá správný formát RRRR-mm-dd HH:mm:ss</div><br />";$status = false;}
	if(It6_Date::toTimestamp($h['platna_od']) >= It6_Date::toTimestamp($h['platna_do'])){$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> Datum od nemůže být větší nebo roven datumu do</div><br />";$status = false;}
	if(!isset($h['sloupec']) || !is_array($h['sloupec'])){$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> Nejsou uvedeny kurzy</div><br />";$status = false;}

	#MAX a MIN kurzu u podtypu#
	$sql = "select max_kurz,min_kurz from podtyp where podtyp_id=".intval($h['podtyp']);
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vyber podtypu',"admin_ex_db");}
    if($row =& $res->fetchRow()){$min_kurz = $row['min_kurz'];$max_kurz = $row['max_kurz'];}else {$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> Nebyl nalezen min. a max kurz pro daný podtyp</div><br />";$status = false;}


	#kontrola kurzu#
	$sql = "select sloupec_id from podtyp_sloupce where podtyp_id=".intval($h['podtyp']);
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: editace typu',"admin_ex_db");}
    if($res->NumRows() < 1) {$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> Nebyly nalezeny žádné sloupce pro daný podtyp</div><br />";$status = false;}

	$low = 10000000;$min_max_status = true;
    while ($row =& $res->fetchRow()){

	  if(!isset($h['sloupec'][$row['sloupec_id']]) || mb_strlen($h['sloupec'][$row['sloupec_id']]) < 1 || !is_numeric($h['sloupec'][$row['sloupec_id']])){$low = false;$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> Kurz (".$h['sloupectext'][$row['sloupec_id']].") nemá správný formát</div><br />";$status = false;}
	  else if($low) $low = ($low > $h['sloupec'][$row['sloupec_id']]?$h['sloupec'][$row['sloupec_id']]:$low);

	  if($h['sloupec'][$row['sloupec_id']] < $min_kurz || $h['sloupec'][$row['sloupec_id']] > $max_kurz) $min_max_status = false;

	}

	if($low && $low >= count($h['sloupec'])){$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> Kurzy jsou vypsány chybně, při vsazení na všechny možnosti dojde k výhře.</div><br />";$status = false;}
    if(!$min_max_status){$this->vrat .= "<div class=\"errormsg\"> <a href=\"#sazka".$k."\">Sázka</a> <strong>".$k."</strong> -> Kurzy jsou vyšší nebo nižší nežli povolené; MIN: ".$min_kurz." MAX: ".$max_kurz."</div><br />";$status = false;}

   }

   if($status){

	$komb = array();

    foreach($_POST['sazka'] as $k=>$h){

	 $platna_od = Help::slash(It6_Date::toDb($h['platna_od']));

	 $sql = "insert into sazky (platna_od,platna_do,bookmaker_id,udalost_id,typ_id,podtyp_id,live,text,jednoducha)
	         values('".$platna_od ."','".Help::slash(It6_Date::toDb($h['platna_do']))."',".$_SESSION['bookmaker'].",
			 ".intval($_POST['udalost']).",".intval($h['typ']).",".intval($h['podtyp']).",".(isset($h['live'])?1:0).",
			 '".Help::Slash($h['text'])."',".(isset($h['jednoducha'])?1:0).")";
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vlozeni sazky',"admin_ex_db");}

	 $sql = "select max(sazka_id) AS m from sazky";
     $res =& $this->dbGame->query($sql);

     if (!$row =& $res->fetchRow()) {$this->dbGame->rollback();throw new ExHandler('Nepodarilo se ziskat identifikator typ',"admin_ex_db");}else $sazka_id = $row['m'];

	 $sql = "select max(poradi) AS m from sazka_kurz where sazka_id=".$sazka_id." group by sazka_id";
     $res =& $this->dbGame->query($sql);

     if (!$row =& $res->fetchRow()) $poradi = 1;else $poradi = $row['m'];

     foreach($h['sloupec'] as $k=>$h){

	  $sql = "insert into sazka_kurz (sazka_id,sloupec_id,poradi,kurz,platny_od)
	         values(".$sazka_id.",".intval($k).",".$poradi.",".floatval($h).",'".$platna_od ."')";
      $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vlozeni sazky',"admin_ex_db");}

	 }
	 
	 Zend_Registry::get('ws')->Alert->assert('RateChange',
				array('betId' => $sazka_id, 'bookmaker' => $_SESSION['bookmaker']));

	 if(isset($h['kombinace'])) $komb[] = $sazka_id;

	}

	reset($komb);$y = 0;
	foreach($komb as $k=>$h){

	 for($z=$y;$z<count($komb);$z++){

	  if($k != $z){

	   $sql = "insert into sazka_kombinace (sazka1_id,sazka2_id)
	         values(".$h.",".$komb[$z].")";
       $res =& $this->dbGame->query($sql);
	   if(DB::isError($res)) {$this->dbGame->rollback();$this->dbGame->autoCommit(true);throw new ExHandler('Nepodarilo se provest dotaz: vlozeni kombinace sazky',"admin_ex_db");}

	  }

	 }

	 $y++;
	}

	$this->vrat .= "<div class=\"okmsg\">Sázky byly úspěšně vytvořeny</div><br />";

		It6_Log::info(
			"'%bets%' new bets created.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('bets' => count($_POST['sazka']))
		);
   }


   $this->dbGame->autoCommit(true);
   $this->dbGame->commit();

  }

 /**
 * metoda vypise vsechny zadane typy
 * @return void
 */
  public function ShowBet(){

   $preklad = new Preklady();
   $udalost = $help = "";
   $prava_udalost = $sport_udalost = array();

   if($_SESSION['superbookmaker'] != 1){

	 $sql = "select udalost_id from prava_udalost where  bookmaker_id=".intval($_SESSION['bookmaker'])." and povoleny=1";
     $res2 =& $this->dbGame->query($sql);
	 if(DB::isError($res2)) throw new ExHandler('Nepodarilo se provest dotaz: vyber z tabulky prava_udalost',"admin_ex_db");
     while ($row2 =& $res2->fetchRow()) $prava_udalost[$row2['udalost_id']] = $row2['udalost_id'];


   }

   $sql = "select a.sport_id,a.nazev,b.nazev AS udalost_nazev,b.udalost_id from sport a inner join udalost b on a.sport_id=b.sport_id where platne_do>'".It6_Date::dbNow()."' ".($_SESSION['superbookmaker'] != 1?"and b.udalost_id in (".implode(",",$prava_udalost).")":"")." order by a.sport_id,a.pozice,b.pozice";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");


   while ($row =& $res->fetchRow()){

	  $sport_udalost[$row['udalost_id']] = $row['sport_id'];

	  if($help == "" || $help != $row['sport_id']){
	   $help = $row['sport_id'];
	   $r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['nazev'])."'");
	   if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = "Překlad nenalezen";
	   $udalost .= "<optgroup label=\"".Help::Html($row2['text'])."\">";
	  }

	  $r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['udalost_nazev'])."'");
	  if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = "Překlad nenalezen";

      $udalost .= "<option  value=\"".$row['udalost_id']."\" ".(isset($_POST['udalost']) && $_POST['udalost'] == $row['udalost_id']?"selected=\"selected\"":"").">".Help::Html($row2['text'])."</option>";

	  if($help != $row['sport_id']){
	   $udalost .= "</optgroup>";
	  }

   }

   if(!isset($_POST['pocet'])) $_POST['pocet'] = 1;
   else if(isset($_POST['plus']) && is_numeric($_POST['pocet'])) $_POST['pocet']++;
   else if(isset($_POST['minus']) && is_numeric($_POST['pocet'])) $_POST['pocet']--;

   #multi-sazka#
   if($this->multisazka && isset($_POST['sazka'])){

    $_POST['pocet'] = intval($_POST['multipocet']);
    if($_POST['pocet'] == 0) $_POST['pocet'] = 1;

	for($x = 1;$x <= $_POST['pocet'];$x++){

	 $_POST['sazka'][$x]['typ']    =  $_POST['sazka'][1]['typ'];
	 $_POST['sazka'][$x]['podtyp'] =  $_POST['sazka'][1]['podtyp'];

	}

   }

   $this->vrat .= "<form method=\"post\" action=\"?superb=1&section=".$this->section."\">";
   $this->vrat .= "<a href=\"#bo\">Dolů</a><br /><br />";
   $this->vrat .= "<h3>Krok 1 výběr Sport -> Událost </h3><br />";
   $this->vrat .= "<select name=\"udalost\" style=\"font-size:0.9em\" onchange=\"this.form.submit()\"><option  value=\"0\">Vyberte Turnaj/Událost</option>".$udalost."</select>&nbsp;&nbsp; <input type=\"checkbox\" name=\"template\" ".(isset($_POST['template'])?'checked="checked"':'')." class=\"no\" /> Použít template<br />";
   $this->vrat .= $this->BetForm((isset($sport_udalost[$_POST['udalost']])?$sport_udalost[$_POST['udalost']]:null),(isset($_POST['udalost'])?$_POST['udalost']:null),(isset($_POST['sazka'])?$_POST['sazka']:array()),$_POST['pocet']);
   $this->vrat .= '<input type="hidden" name="sport" value="'.(isset($sport_udalost[$_POST['udalost']])?$sport_udalost[$_POST['udalost']]:0).'" />
				   <input type="hidden" name="pocet" value="'.$_POST['pocet'].'" />
				   </form><a name="bo"></a>';

  }

 /**
 * Vypis formulare
 * @param int $sport_id sport id
 * @param int $udalost_id udalost id
 * @param array $sazky pole obsahuje typ a podtyp + kurzy pokud jsou zadany
 * @param int $pocet_sazek pocet sazek na strance
 * @return string
 */
 private function BetForm($sport_id=null,$udalost_id=null,array $sazka,$pocet_sazek = 1){

  $preklad = new Preklady();
  $vrat = "";
  $ArrayTyp = $ArrayPodTyp = array();
  $submit = true;

  if($sport_id != null && $udalost_id != null){

	$vrat .= "<br /><h3>Krok 2 výběr Typ -> Podtyp</h3>";

    $sql = "select  c.typ_id,c.nazev from sport a inner join (typ_sport b inner join typ c on b.typ_id=c.typ_id) on a.sport_id=b.sport_id where a.sport_id=".$sport_id;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber sportu a udalosti',"admin_ex_db");

	while ($row =& $res->fetchRow()){

	  $r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['nazev'])."'");
	  if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = "Překlad nenalezen";
	  $ArrayTyp[$row['typ_id']] .= $row2['text'];

	}

	for($x=1;$x<=$pocet_sazek;$x++){

	  $typ = $podtyp = "";
	  $ArrayPodTyp = array();

	  foreach($ArrayTyp as $k=>$h){
	   $typ .= "<option  value=\"".$k."\" ".(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] == $k?"selected=\"selected\"":"").">".Help::Html($h)."</option>";
	  }

	  if(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] != 0){

       $sql = "select a.podtyp_id,a.interni_nazev,a.radek_sloupec,a.sloupec_pocet_max from podtyp a inner join typ_podtyp b on a.podtyp_id=b.podtyp_id where b.typ_id=".intval($sazka[$x]['typ'])." and b.sport_id=".intval($sport_id);
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber podtypu',"admin_ex_db");

	   while ($row =& $res->fetchRow()){

	    $podtyp .= "<option  value=\"".$row['podtyp_id']."\" ".(isset($sazka[$x]['podtyp']) && $sazka[$x]['podtyp'] == $row['podtyp_id']?"selected=\"selected\"":"").">".Help::Html($row['interni_nazev'])." ".($row['radek_sloupec']?"(vertikál)":"(horizontál)")."</option>";

	   }

	  }

	   $vrat .=  "<a name=\"sazka".$x."\"></a>";

	  if(!$this->multisazka || ($this->multisazka && $x == 1)){

	   $vrat .=  "<p><select name=\"sazka[".$x."][typ]\" style=\"font-size:0.9em\"  onchange=\"this.form.action=this.form.action+'#sazka'+".($x).";this.form.submit()\"><option  value=\"0\">Vyberte Typ</option>".$typ."</select>";
       if(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] != 0 && mb_strlen($podtyp) > 0)
	   $vrat .=  "<select name=\"sazka[".$x."][podtyp]\" style=\"font-size:0.9em\" onchange=\"this.form.action=this.form.action+'#sazka'+".($x).";this.form.submit()\"><option  value=\"0\">Vyberte PodTyp</option>".$podtyp."</select></p>";
       if(isset($sazka[$x]['typ']) && $sazka[$x]['typ'] != 0 && mb_strlen($podtyp) < 1) $vrat .= "&nbsp;&nbsp;<span class=\"errormsg\">Litujeme, pro tento typ není definován žádný <ins>podtyp</ins></span>";
	   if($this->multisazka && isset($sazka[$x]['podtyp'])) $vrat .= "Počet:<input type=\"text\" class=\"sinput3\" value=\"".(isset($_POST['pocet'])?Help::Html($_POST['pocet']):"")."\" name=\"multipocet\" /><input type=\"submit\" class=\"sinput2\" value=\"Potvrdit\" name=\"multi\" />";

	  }

	  if($this->multisazka && $x > 1) $vrat .= '<input type="hidden" name="sazka['.$x.'][typ]" value="'.(isset($sazka[$x]['typ'])?$sazka[$x]['typ']:0).'" /><input type="hidden" name="sazka['.$x.'][podtyp]" value="'.(isset($sazka[$x]['podtyp'])?$sazka[$x]['podtyp']:0).'" />';

	  if(!$this->multisazka || ($this->multisazka && (isset($_POST['multi']) || isset($_POST['create'])))){

	  if(isset($sazka[$x]['podtyp']) && $sazka[$x]['podtyp'] != 0 && mb_strlen($podtyp) > 1){

	   $sql = "select a.text,a.radek_sloupec,a.sloupec_pocet_max,b.sloupec_id,b.nazev from podtyp a inner join podtyp_sloupce b on a.podtyp_id=b.podtyp_id where b.podtyp_id=".intval($sazka[$x]['podtyp'])." order by b.poradi";
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vyber podtypu',"admin_ex_db");

	   while ($row =& $res->fetchRow()){

	    if(!isset($ArrayPodTyp['text'])){

	     $r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['text'])."'");
	     if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = "";
	     $ArrayPodTyp['text'] = $row2['text'];
		 $ArrayPodTyp['radek'] = $row['radek_sloupec'];
		 $ArrayPodTyp['radek_max'] = $row['sloupec_pocet_max'];

		}

		$r = $preklad->selectData("where lang_id=1 and index_pole='".Help::Slash($row['nazev'])."'");
	    if (!$row2 =& $r->fetchRow() || mb_strlen($row2['text']) < 0) $row2['text'] = $row['nazev'];
		$ArrayPodTyp['sloupec'][$row['sloupec_id']] = $row2['text'];

	   }

	  }else $submit = false;

	  }

	  #vypis sazecich formularu#
	  if(count($ArrayPodTyp) > 0):

	  $vrat .= '<table class="bettab">
	          <tr><td colspan="3" class="bettabhead2">Sázka <span class="red">'.$x.'</span></td></tr>'.
			  (mb_strlen($ArrayPodTyp['text'])>0?'<tr><td colspan="3" class="bettabhead">'.Help::Html($ArrayPodTyp['text']).'</td></tr>':"").'
	          <tr><td>Text:</td><td><input type="text" name="sazka['.$x.'][text]" class="sinput3 '.($ArrayPodTyp['radek'] == 0?"mandatory":"").'" name="" value="'.(isset($sazka[$x]['text'])?Help::Html($sazka[$x]['text']):"").'" /></td><td valign="top" rowspan="6">
			   <table class="'.($ArrayPodTyp['radek'] == 1?"sloupec":"radek").'">';

	  if($ArrayPodTyp['radek'] == 1){//sloupcovy vypis

	   $tr = array();$y = 0;

	   foreach($ArrayPodTyp['sloupec'] as $k => $h){

	    if(!isset($tr[$y])) $tr[$y] = "";
	    $tr[$y] .= '<th valign="top">'.$h.'<input type="hidden" name="sazka['.$x.'][sloupectext]['.$k.']" value="'.$h.'" /></th><td valign="top"><input type="text" name="sazka['.$x.'][sloupec]['.$k.']" class="sinput2" value="'.(isset($sazka[$x]['sloupec'][$k])?Help::Html($sazka[$x]['sloupec'][$k]):"").'" /></td>';
        $y++;
		if($y == $ArrayPodTyp['radek_max']) $y = 0;

	   }

	   foreach($tr as $h){

	    $vrat .= '<tr>'.$h.'</tr>';

	   }

	  }
	  else{ //radkovy vypis

	    $vrat .= '<tr>';

		foreach($ArrayPodTyp['sloupec'] as $k=>$h)
		 $vrat .= '<th>'.$h.'<input type="hidden" name="sazka['.$x.'][sloupectext]['.$k.']" value="'.$h.'" /></th>';

		$vrat .= '</tr>';
		$vrat .= '<tr>';

	    foreach($ArrayPodTyp['sloupec'] as $k => $h)
		 $vrat .= '<td><input type="text" name="sazka['.$x.'][sloupec]['.$k.']" class="sinput2"  value="'.(isset($sazka[$x]['sloupec'][$k])?Help::Html($sazka[$x]['sloupec'][$k]):"").'" /></td>';

		$vrat .= '</tr>';

	  }

	  $vrat .=' </table>
			  </td></tr>
			  <tr><td>Platná od:</td><td><input type="text" name="sazka['.$x.'][platna_od]" class="mandatory sinput3" onclick="ok1 = window.open(\'calendar.php?cas=sazka['.$x.'][platna_od]\',\'\',\'width=140,height=247\');" name="" value="'.(isset($sazka[$x]['platna_od'])?Help::Html($sazka[$x]['platna_od']):"").'" /></td></tr>
			  <tr><td>Platná do:</td><td><input type="text" name="sazka['.$x.'][platna_do]" class="mandatory sinput3" onclick="ok1 = window.open(\'calendar.php?cas=sazka['.$x.'][platna_do]\',\'\',\'width=140,height=247\');" name="" value="'.(isset($sazka[$x]['platna_do'])?Help::Html($sazka[$x]['platna_do']):"").'" /></td></tr>
			  <tr><td>Jednoduchá:</td><td><input type="checkbox" name="sazka['.$x.'][jednoducha]" '.(isset($sazka[$x]['jednoducha'])?"checked=\"checked\"":"").' class="no"  /></td></tr>
			  <tr><td>Live:</td><td><input type="checkbox" name="sazka['.$x.'][live]" class="no" '.(isset($sazka[$x]['live'])?"checked=\"checked\"":"").'  /></td></tr>
			  <tr><td>Kombinace:</td><td><input type="checkbox" name="sazka['.$x.'][kombinace]" '.(isset($sazka[$x]['kombinace'])?"checked=\"checked\"":"").' class="no"  /></td></tr>
	          </table>';

	 endif;

	 $vrat .='<input type="hidden" name="sazka['.$x.'][radek]" value="'.$ArrayPodTyp['radek'].'" />';

	}

	if(!$this->multisazka){

	$vrat .= '<p><input type="submit" onclick="this.form.action=this.form.action+\'#sazka\'+'.($pocet_sazek+1).'" name="plus" title="Přidat sázku do kombinace" class="sinput3" value="+" />&nbsp;&nbsp;';
	if($pocet_sazek > 1)$vrat .= '<input type="submit" title="Odebrat sázku do kombinace" onclick="this.form.action=this.form.action+\'#sazka\'+'.($pocet_sazek-1).'" class="sinput3" name="minus" value="-" /></p>';

	}

	if($submit) $vrat .= '<br /><br /><input type="submit" name="create" value="Vytvořit" />';

  }

   return $vrat;

 }


 /**
 * vyber dat z databaze
 * @return object
 */
  public function selectData($where=""){

     $sql = "SELECT a.typ_id,b.sport_id,a.nazev,a.zobrazeno,b.vychozi,b.poradi FROM `typ` a left join typ_sport b  on a.typ_id=b.typ_id ".$where." order by b.typ_id,b.sport_id";
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
