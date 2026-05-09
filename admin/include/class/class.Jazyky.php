<?php
/**
 * @package    ciselnik
 */

/**
 * Trida pro praci s Jazyky
 *
 *
 * <code>
 *
 * </code>
 *
 * @package   Ciselniky
 */

class Jazyky extends Template{

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

   if(isset($_POST['jazyk'])){


    #Zobrazeni ne skryti prvku#
    if(isset($_POST['zobrazit']) || isset($_POST['skryt'])){

	 if($this->update)
	    $this->ZobrazitSkrytJazyk((isset($_POST['zobrazit'])?key($_POST['zobrazit']):key($_POST['skryt'])));
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

	#Posuny jazyka#
    else if(isset($_POST['up']) || isset($_POST['down'])){

	  if($this->update)
	    $this->UpDown((isset($_POST['up'])?key($_POST['up']):key($_POST['down'])));
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

    #Vytvoreni jazyka#
    else if(isset($_POST['create'])){

	  if($this->update)
	    $this->CreateJazyk();
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vkládání dat</div>\n";

	}

	#Editace vsech jazyku#
    else if(isset($_POST['editall']) && isset($_POST['jazyk'])){

	  if($this->update)
	    $this->EditJazyky();
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci dat</div>\n";

	}

    #Vymazani jazyka#
    else if(isset($_POST['delete'])){

	  if($this->delete)
	    $this->DeleteJazyk(key($_POST['delete']));
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání dat</div>\n";

	}

   }

   $this->ShowLang();

   $this->vrat .= "";

    $this->dbGame->disconnect();

  }

 /**
 * Editace jazyku
 * @return void
 */
  private function EditJazyky(){

  $x = 1;

   foreach($_POST['jazyk'] as $k=>$h){

    $status = true;

    if(!isset($_POST['jazyk'][$k]['iso']) || mb_strlen(trim($_POST['jazyk'][$k]['iso'])) > 2 || mb_strlen(trim($_POST['jazyk'][$k]['iso'])) < 1) {$this->vrat .= "<div class=\"errormsg\">(".Help::Html($_POST['jazyk'][$k]['hidden']).") <strong>ISO</strong> musí být uvedeno a mít maximálně 2 znaky minimálně 1</div><br />";$status = false;}
    if(!isset($_POST['jazyk'][$k]['nazev']) || mb_strlen(trim($_POST['jazyk'][$k]['nazev'])) > 45 || mb_strlen(trim($_POST['jazyk'][$k]['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> (".Help::Html($_POST['jazyk'][$k]['hidden']).") <strong>Název</strong> musí být uveden a mít maximálně 45 znaků minimálně 1</div><br />";$status = false;}

    $sql = "select iso from jazyky where iso='".Help::slash($_POST['jazyk'][$k]['iso'])."'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani jazyka z databaze',"admin_ex_db");

    if($res->numRows() > 0 && $_POST['jazyk'][$k]['hidden'] != $_POST['jazyk'][$k]['iso']){ $this->vrat .= "<div class=\"errormsg\">Tento \"Jazyk\"  (".Help::Html($_POST['jazyk'][$k]['iso']).") již existuje</div><br />";$status = false;}

    #vsechno je  vporadku muzeme aktualizovat#
    if($status){

	     $sql = "update jazyky set iso='".Help::slash($_POST['jazyk'][$k]['iso'])."',alt_text='".Help::slash($_POST['jazyk'][$k]['nazev'])."' where lang_id=".intval($k);
         $res =& $this->dbGame->query($sql);
	     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: aktualizace jazyka',"admin_ex_db");
	     $this->vrat .= "<div class=\"okmsg\">Jazyk ".Help::Html($_POST['jazyk'][$k]['iso'])." byl úspěšně aktualizován</div><br />";
		 $_SESSION['lastaction'][time()+$x] = "Editace jazyka: ".$_POST['jazyk'][$k]['iso'];

     $x++;
    }

   }

  }


 /**
 * Vytvoreni azyka
 */
  private function CreateJazyk(){

   $status = true;

   if(!isset($_POST['iso']) || mb_strlen(trim($_POST['iso'])) > 2 || mb_strlen(trim($_POST['iso'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>ISO</strong> musí být uvedeno a mít maximálně 2 znaky minimálně 1</div><br />";$status = false;}
   if(!isset($_POST['alt']) || mb_strlen(trim($_POST['alt'])) > 45 || mb_strlen(trim($_POST['alt'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> musí být uveden a mít maximálně 45 znaků minimálně 1</div><br />";$status = false;}

   $sql = "select iso from jazyky where iso='".Help::slash($_POST['iso'])."'";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani jazyka z databaze',"admin_ex_db");

   if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tento \"Jazyk\"  (".Help::Html($_POST['iso']).") již existuje</div><br />";$status = false;}

   #vsechno je  vporadku muzeme aktualizovat#
   if($status){

	    $sql = "select max(pozice) AS poz from jazyky";
        $res =& $this->dbGame->query($sql);
        if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani jazyka z databaze',"admin_ex_db");

		if($row =& $res->fetchRow()) $pozice = $row['poz']+1;

	    $sql = "insert into jazyky (iso,pozice,alt_text,zobrazeno) values ('".Help::slash($_POST['iso'])."',".$pozice.",'".Help::slash($_POST['alt'])."',0)";
        $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho administratora',"admin_ex_db");
	    $this->vrat .= "<div class=\"okmsg\">Jazyk ".Help::Html($_POST['iso'])." byl úspěšně vytvoren</div><br />";

		It6_Log::info(
			"New language '%lang%' was added.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('lang' => $_POST['iso'])
		);
   }

  }


 /**
 * Vymazani jazyka
 * @param int $lang_id id jazyka
 */
  private function DeleteJazyk($lang_id){

     $sql = "delete from jazyky where lang_id=".$lang_id;
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z pohledu admin_prava_sekce',"admin_ex_db");

	 if($this->dbGame->affectedRows()){

	   $this->vrat .= "<div class=\"okmsg\">Jazyk byl úspěšně smazán</div><br />";

		It6_Log::info(
			"Language '%lang%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('lang' => $_POST['jazyk'][$lang_id]['nazev'])
		);
	 }else
	     $this->vrat .= "<div class=\"errormsg\">Jazyk se nepodařilo smazat</div><br />";

  }

 /**
 * Posun nahoru dolu
 * @param int $lang_id id jazyka
 */
  public function UpDown($lang_id){

   $pozice = (isset($_POST['up'])?key($_POST['up'][$lang_id]):key($_POST['down'][$lang_id]));

   $novaPozice = (isset($_POST['up'])?key($_POST['up'][$lang_id][$pozice]):key($_POST['down'][$lang_id][$pozice]));

   $this->dbGame->autoCommit(false);

   $sql = "update jazyky set pozice=".$pozice." where pozice=".$novaPozice;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky jazyky',"admin_ex_db");

   $sql = "update jazyky set pozice=".$novaPozice." where lang_id=".$lang_id;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: update pozice tabulky jazyky',"admin_ex_db");

   $this->dbGame->autoCommit(true);
   $this->dbGame->commit();

   $this->vrat .= "<div class=\"okmsg\">Jazyk ".Help::Html($_POST['jazyk'][$lang_id]['nazev'])." byl ůspěšně ".(isset($_POST['up'])?"posunut nahoru":"posunut dolů")."</div><br />";

		It6_Log::info(
			"Language '%lang%' was moved '%action%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'lang'		=> $_POST['iso'],
				'action'	=> (isset($_POST['up'])?"up":"down")
			)
		);
  }

 /**
 * Zobrazeni nebo skryti jazyka
 * @param int $lang_id id jazyka
 */
  public function ZobrazitSkrytJazyk($lang_id){

	$sql = "update jazyky set zobrazeno=".(isset($_POST['zobrazit'])?1:0)." where lang_id=".$lang_id;
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni noveho administratora',"admin_ex_db");

	$this->vrat .= "<div class=\"okmsg\">Jazyk ".Help::Html($_POST['jazyk'][$lang_id]['nazev'])." byl ůspěšně ".(isset($_POST['zobrazit'])?"zobrazen":"skryt")."</div><br />";

		It6_Log::info(
			"Language '%lang%' was '%action%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'lang'		=> $_POST['iso'],
				'action'	=> (isset($_POST['zobrazit'])?"shown":"hidden")
			)
		);
  }



  /**
 * vyber dat z databaze
 * @return object
 */
  public function selectData($where=""){

	$sql = "SELECT lang_id,iso,pozice,alt_text,zobrazeno from jazyky ".$where." ORDER BY pozice";
	$res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler($res->getMessage(),"admin_ex_db");

	return $res;
  }



 /**
 * Zobrazeni formulare
 * @return sting
 */
  public function ShowLang(){

     $res = $this->selectData();

     /*hlavicka*/
	 $this->vrat .= "<form method=\"post\" action\"?section=".$this->section."\">
	                 <table class=\"table-list\"><thead>
	                 <tr><th class=\"span-1\">&nbsp;</th><th class=\"span-2\">Název</th><th class=\"span-2\">ISO</th><th class=\"span-2\" >&nbsp;</th><th class=\"span-2\" >&nbsp;</th><th class=\"span-1\" >&nbsp;</th><th class=\"span-1\" >&nbsp;</th></thead><tbody>";

	 $x = 1;
	 $pocetRadku = $res->numRows();

	 $maxpozice = null;
	 $minpozice = null;
	 $pole = array();

     while ($row =& $res->fetchRow()){

	  $maxpozice = ($maxpozice == null || $maxpozice < $row['pozice']?$row['pozice']:$maxpozice);
	  $minpozice = ($minpozice == null || $minpozice > $row['pozice']?$row['pozice']:$minpozice);

	  $pole[$row['lang_id']]['alt_text'] = $row['alt_text'];
	  $pole[$row['lang_id']]['iso'] = $row['iso'];
	  $pole[$row['lang_id']]['zobrazeno'] = $row['zobrazeno'];
	  $pole[$row['lang_id']]['pozice'] = $row['pozice'];

	 }

	 $p = $pole;

	 foreach($pole as $k=>$h){

	  if($minpozice!=$h['pozice']){$up = prev($p);$up = $up['pozice'];next($p);}
	  if($maxpozice!=$h['pozice']){$down = next($p);$down = $down['pozice'];prev($p);}
	  next($p);

	  $this->vrat .= '<tr>
	                  <td>'. $x .'.</td>
	                  <td><input type="text" class="sinput" maxlength="50" name="jazyk['.$k.'][nazev]" value="'.Help::Html($h['alt_text']).'" /></td>
	                  <td><input type="text" class="sinput" maxlength="2" name="jazyk['.$k.'][iso]" value="'.Help::Html($h['iso']).'" /><input type="hidden" class="sinput" maxlength="2" name="jazyk['.$k.'][hidden]" value="'.Help::Html($h['iso']).'" /></td>
					  <td><input type="submit" name="delete['.$k.']" class="sbutton" onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($h['alt_text']).'?\')) return false;" value="Smazat" /></td>
					  <td><input type="submit" name="'.($h['zobrazeno']?'skryt':'zobrazit').'['.$k.']" class="sbutton" value="'.($h['zobrazeno']?'Skrýt':'Zobrazit').'" /></td>
					  <td>'.($minpozice==$h['pozice']?"&nbsp;":'<input type="submit" name="up['.$k.']['.$h['pozice'].']['.$up.']" class="sbutton2" value="^" />').'</td>
					  <td>'.($maxpozice==$h['pozice']?"&nbsp;":'<input type="submit" name="down['.$k.']['.$h['pozice'].']['.$down.']" class="sbutton2" value="v" />').'</td>
					  </tr>';
	  $x++;

	 }

	    /*spodek*/
	 $this->vrat .= '</table>';
	 $this->vrat .= '<div class="actions"><input type="submit" style="width:280px;" name="editall" value="Aktualizovat všechny jazyky na stránce" /></div>';
	 $this->vrat .= '<table class="table-list"><tr><th colspan="4"><strong>Nový Jazyk</strong></th></tr>
	                  <tr><td>&nbsp;</td>
	                  <td><input type="text" class="sinput mandatory" maxlength="45" name="alt" value="'.(isset($_POST['alt'])?Help::Html($_POST['alt']):"").'" /></td>
	                  <td><input type="text" class="sinput mandatory" maxlength="2" name="iso" value="'.(isset($_POST['iso'])?Help::Html($_POST['iso']):"").'" /></td>
					  <td><input type="submit" name="create" class="sbutton" value="Vytvořit" /></td>
					  </tr>
					  </tbody></table></form>';
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



 /**
 * Vraci seznam zobrazenych(aktivnich) jazyku
 * @return array
 */
	public function GetJazyky(){

		$jazyky = array();
		$res = $this->selectData();

		while ($row =& $res->fetchRow()){
			if($row['zobrazeno'] == 1){
				$jazyky[$row['lang_id']]['lang_id'] 	= $row['lang_id'];
				$jazyky[$row['lang_id']]['iso'] 			= $row['iso'];
				$jazyky[$row['lang_id']]['pozice'] 		= $row['pozice'];
				$jazyky[$row['lang_id']]['alt_text'] 	= $row['alt_text'];
			}
		}

		return $jazyky;
  }



}

