<?php
/**
 * @package    ciselnik
 */

/**
 * Trida pro praci s cislenikem Zemi
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class Zeme extends Template{

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
 * metoda zavola prislusne dalsi metody podle provedene akce uzivatelem
 * @return void
 */
  public function runAction(){

   if(isset($_POST['zeme'])){


    #Zobrazeni ne skryti prvku#
    if(isset($_POST['zobrazit']) || isset($_POST['skryt'])){

	 if($this->update)
	    $this->ZobrazitSkrytZemi((isset($_POST['zobrazit'])?key($_POST['zobrazit']):key($_POST['skryt'])));
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

    #Vytvoreni zeme#
    else if(isset($_POST['create'])){

	  if($this->update)
	    $this->CreateZeme();
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vkládání dat</div>\n";

	}

	#Editace vsech zemi#
    else if(isset($_POST['editall']) && isset($_POST['zeme'])){

	  if($this->update)
	    $this->EditZeme();
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci dat</div>\n";

	}

    #Vymazani zeme#
    else if(isset($_POST['delete'])){

	  if($this->delete)
	    $this->DeleteZeme(key($_POST['delete']));
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání dat</div>\n";

	}

   }

   $this->ShowZeme();

   $this->vrat .= "";

   $this->dbGame->disconnect();

  }

 /**
 * Editace men
 * @return void
 */
  private function EditZeme(){

  $x = 1;

   foreach($_POST['zeme'] as $k=>$h){

    $status = true;

   if(isset($_POST['zeme'][$k]['nazev'])){

     $preklady = new Preklady($this->section);

     $res = $preklady->selectData("where index_pole='".Help::slash($_POST['zeme'][$k]['nazev'])."'");

     if (!$row =& $res->fetchRow()) {$this->vrat .= "<div class=\"errormsg\"> (<strong>Název:".Help::Html($_POST['zeme'][$k]['nazev'])."</strong>) tento index není platným překladem</div><br />";$status = false;}

   }

    if(!isset($_POST['zeme'][$k]['nazev']) || mb_strlen(trim($_POST['zeme'][$k]['nazev'])) > 45 || mb_strlen(trim($_POST['zeme'][$k]['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\">(".Help::Html($_POST['zeme'][$k]['hidden']).") <strong>Název</strong> musí být uvedeno a mít maximálně 45 znaků minimálně 1</div><br />";$status = false;}
    if(isset($_POST['zeme'][$k]['kod']) && mb_strlen(trim($_POST['zeme'][$k]['kod'])) > 6) {$this->vrat .= "<div class=\"errormsg\"> <strong>Zkratka</strong> musí  mít maximálně 6 znaků</div><br />";$status = false;}

    $sql = "select nazev from zeme where nazev='".Help::slash($_POST['zeme'][$k]['nazev'])."'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani zeme z databaze',"admin_ex_db");

    if($res->numRows() > 0 && $_POST['zeme'][$k]['hidden'] != $_POST['zeme'][$k]['nazev']){ $this->vrat .= "<div class=\"errormsg\">Teto \"Země\"  (".Help::Html($_POST['zeme'][$k]['nazev']).") již existuje</div><br />";$status = false;}

    #vsechno je  vporadku muzeme aktualizovat#
    if($status){

	     $sql = "update zeme set nazev='".Help::slash($_POST['zeme'][$k]['nazev'])."',kod='".Help::slash($_POST['zeme'][$k]['kod'])."' where zeme_id=".intval($k);
         $res =& $this->dbGame->query($sql);
	     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: aktualizace zeme',"admin_ex_db");
	     $this->vrat .= "<div class=\"okmsg\">Země ".Help::Html($_POST['zeme'][$k]['nazev'])." byla úspěšně aktualizována</div><br />";
		 $_SESSION['lastaction'][time()+$x] = "Editace země: ".$_POST['zeme'][$k]['nazev'];

     $x++;
    }

   }

  }


 /**
 * Vytvoreni zeme
 */
  private function CreateZeme(){

   $status = true;

   $pole_preklad = array();


   if(isset($_POST['nazev'])){

     $preklady = new Preklady($this->section);

     $res = $preklady->selectData("where index_pole='".Help::slash($_POST['nazev'])."'");

     if (!$row =& $res->fetchRow()) {$this->vrat .= "<div class=\"errormsg\"> (<strong>Název</strong>) tento index není platným překladem</div><br />";$status = false;}

   }

   if(!isset($_POST['nazev']) || mb_strlen(trim($_POST['nazev'])) > 45 || mb_strlen(trim($_POST['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> musí být uveden a mít maximálně 45 znaků minimálně 1</div><br />";$status = false;}
   if(isset($_POST['kod']) && mb_strlen(trim($_POST['kod'])) > 6) {$this->vrat .= "<div class=\"errormsg\"> <strong>Zkratka</strong> musí  mít maximálně 6 znaků</div><br />";$status = false;}

   $sql = "select nazev from zeme where nazev='".Help::slash($_POST['nazev'])."'";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani zeme z databaze',"admin_ex_db");

   if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tato \"Země\"  (".Help::Html($_POST['nazev']).") již existuje</div><br />";$status = false;}

   #vsechno je  vporadku muzeme aktualizovat#
   if($status){

	    $sql = "insert into zeme (nazev,kod) values ('".Help::slash($_POST['nazev'])."','".Help::slash($_POST['kod'])."')";
        $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni nove zeme',"admin_ex_db");
	    $this->vrat .= "<div class=\"okmsg\">Země ".Help::Html($_POST['nazev'])." byla úspěšně vytvořena</div><br />";

		It6_Log::info(
			"New country was added: '%country%'",
			It6_Log::TAG_ADMIN_OPERATION,
			array('country' => $_POST['nazev'])
		);
   }

  }


 /**
 * Vymazani zeme
 * @param int $zeme_id id zeme
 */
  private function DeleteZeme($zeme_id){

     $sql = "delete from zeme where zeme_id=".$zeme_id;
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vymazani z tabulky zeme',"admin_ex_db");

	 if($this->dbGame->affectedRows()){

	 $this->vrat .= "<div class=\"okmsg\">Země byla úspěšně smazána</div><br />";

		It6_Log::info(
			"Country '%country%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('country' => $_POST['zeme'][$zeme_id]['nazev'])
		);

	 }else
	     $this->vrat .= "<div class=\"errormsg\">Zemi se nepodařilo smazat</div><br />";


  }


 /**
 * Zobrazeni nebo skryti zeme
 * @param int $zeme_id id zeme
 */
  private function ZobrazitSkrytZemi($zeme_id){

	$sql = "update zeme set zobrazeno=".(isset($_POST['zobrazit'])?1:0)." where zeme_id=".$zeme_id;
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: zobrazeni v tabulce zeme',"admin_ex_db");

	$this->vrat .= "<div class=\"okmsg\">Země ".Help::Html($_POST['zeme'][$zeme_id]['nazev'])." byla ůspěšně ".(isset($_POST['zobrazit'])?"zobrazena":"skryta")."</div><br />";

		It6_Log::info(
			"Country '%country%' was '%action%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'country'	=> $_POST['zeme'][$zeme_id]['nazev'],
				'action'	=> (isset($_POST['zobrazit'])?"shown":"hidden")
			)
		);
  }

  /**
 * vyber dat z databaze
 * @return object
 */
  public function selectData($where=""){

     $sql = "select zeme_id,nazev,zobrazeno,kod from zeme ".$where." order by nazev";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

	 return $res;

  }

 /**
 * Zobrazeni formulare
 * @return sting
 */
  private function ShowZeme(){

     $res = $this->selectData();
  	 $preklad = new Preklady();

     /*hlavicka*/
	 $this->vrat .= "<form method=\"post\" action\"?section=".$this->section."\">
	                 <table class=\"table-list\"><thead>
	                 <tr><th class=\"span-1\">&nbsp;</th><th class=\"span-2\">Název</th><th class=\"span-2\">&nbsp;</th><th class=\"span-2\">Zkratka</th><th class=\"span-2\">&nbsp;</th><th class=\"span-2\">&nbsp;</th></thead><tbody>";

	 $x = 1;

	 while ($row =& $res->fetchRow()){

	  $alt = $preklad->FindPreklad($row['nazev']);

	  $this->vrat .= '<tr>
	                  <td>'. $x .'.</td>
	                  <td><input type="text" class="sinput" title="'.Help::Html($alt[1]).'" maxlength="20" name="zeme['.$row['zeme_id'].'][nazev]" id="zeme_'.$row['zeme_id'].'" value="'.Help::Html($row['nazev']).'" /><input type="hidden" class="sinput" maxlength="20" name="zeme['.$row['zeme_id'].'][hidden]" value="'.Help::Html($row['nazev']).'" /></td>
					  <td><a href="javascript:openWin(\'ciselnik.php\',\'zeme_'.$row['zeme_id'].'\',\'preklady\',400,300);void(0);"><img src="/_clip/translate.gif" alt="Překladový slovník" class="img" /></a></td>
			          <td><input type="text" class="sinput" maxlength="6" name="zeme['.$row['zeme_id'].'][kod]" value="'.Help::Html($row['kod']).'" /></td>
					  <td><input type="submit" name="delete['.$row['zeme_id'].']" class="sbutton" onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($row['nazev']).'?\')) return false;" value="Smazat" /></td>
					  <td><input type="submit" name="'.($row['zobrazeno']?'skryt':'zobrazit').'['.$row['zeme_id'].']" class="sbutton" value="'.($row['zobrazeno']?'Skrýt':'Zobrazit').'" /></td>
				  </tr>';
	  $x++;

	 }

	    /*spodek*/
	 $this->vrat .= '<tr><td colspan="7" class=""><br /><input type="submit" style="width:280px;" name="editall" value="Aktualizovat všechny země na stránce" /></td></tr>
					  <tr><td colspan="7" class=""><br /><strong>Nová Země</strong></td></tr>
	                  <tr>
					  <td>&nbsp;</td>
	                  <td><input type="text" class="sinput mandatory" maxlength="20" id="nazev" name="nazev" value="'.(isset($_POST['nazev'])?Help::Html($_POST['nazev']):"").'" /></td>
					  <td><a href="javascript:openWin(\'ciselnik.php\',\'nazev\',\'preklady\',400,300);void(0);"><img src="/_clip/translate.gif" alt="Překladový slovník" class="img" /></a></td>
					  <td><input type="text" class="sinput" maxlength="6" name="kod" value="'.(isset($_POST['kod'])?Help::Html($_POST['kod']):"").'" /></td>
					  <td><input type="submit" name="create" class="sbutton" value="Vytvořit" /></td>
					  <td>&nbsp;</td>
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



}

?>
