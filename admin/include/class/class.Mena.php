<?php
/**
 * @package    ciselnik
 */

/**
 * Trida pro praci s Měnou
 *
 *
 * <code>
 *
 * </code>
 *
 * @package    Ciselniky
 */

class Mena extends Template{

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

   if(isset($_POST['mena'])){


    #Zobrazeni ne skryti prvku#
    if(isset($_POST['zobrazit']) || isset($_POST['skryt'])){

	 if($this->update)
	    $this->ZobrazitSkrytMenu((isset($_POST['zobrazit'])?key($_POST['zobrazit']):key($_POST['skryt'])));
	 else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

    #Vytvoreni meny#
    else if(isset($_POST['create'])){

	  if($this->update)
	    $this->CreateMena();
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vkládání dat</div>\n";

	}

	#Editace vsech men#
    else if(isset($_POST['editall']) && isset($_POST['mena'])){

	  if($this->update)
	    $this->EditMeny();
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro editaci dat</div>\n";

	}

    #Vymazani meny#
    else if(isset($_POST['delete'])){

	  if($this->delete)
	    $this->DeleteMena(key($_POST['delete']));
	  else
        $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání dat</div>\n";

	}

   }

   $this->ShowMena();

   $this->dbGame->disconnect();

  }

 /**
 * Editace men
 * @return void
 */
  private function EditMeny(){

  $x = 1;

   foreach($_POST['mena'] as $k=>$h){

    $status = true;

    if(!isset($_POST['mena'][$k]['nazev']) || mb_strlen(trim($_POST['mena'][$k]['nazev'])) > 20 || mb_strlen(trim($_POST['mena'][$k]['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\">(".$_POST['mena'][$k]['hidden'].") <strong>Název</strong> musí být uvedeno a mít maximálně 20 znaků minimálně 1</div><br />";$status = false;}
    if(isset($_POST['mena'][$k]['popis']) && mb_strlen(trim($_POST['mena'][$k]['popis'])) > 20) {$this->vrat .= "<div class=\"errormsg\"> (".Help::Html($_POST['mena'][$k]['hidden']).") <strong>Popis</strong> musí mít maximálně 20 znaků</div><br />";$status = false;}

    $sql = "select mena_text from mena where mena_text='".Help::slash($_POST['nazev'])."'";
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani meny z databaze',"admin_ex_db");

    if($res->numRows() > 0 && $_POST['mena'][$k]['hidden'] != $_POST['mena'][$k]['nazev']){ $this->vrat .= "<div class=\"errormsg\">Teto \"Měna\"  (".Help::Html($_POST['mena'][$k]['nazev']).") již existuje</div><br />";$status = false;}

    #vsechno je  vporadku muzeme aktualizovat#
    if($status){

	     $sql = "update mena set mena_text='".Help::slash($_POST['mena'][$k]['nazev'])."',mena_info='".Help::slash($_POST['mena'][$k]['popis'])."' where mena_id=".intval($k);
         $res =& $this->dbGame->query($sql);
	     if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: aktualizace meny',"admin_ex_db");
	     $this->vrat .= "<div class=\"okmsg\">Měna ".Help::Html($_POST['mena'][$k]['nazev'])." byla úspěšně aktualizována</div><br />";
		 $_SESSION['lastaction'][time()+$x] = "Editace měny: ".$_POST['mena'][$k]['nazev'];

     $x++;
    }

   }

  }


 /**
 * Vytvoreni měny
 */
  private function CreateMena(){

   $status = true;

   if(!isset($_POST['nazev']) || mb_strlen(trim($_POST['nazev'])) > 20 || mb_strlen(trim($_POST['nazev'])) < 1) {$this->vrat .= "<div class=\"errormsg\"> <strong>Název</strong> musí být uveden a mít maximálně 20 znaky minimálně 1</div><br />";$status = false;}
   if(isset($_POST['popis']) && mb_strlen(trim($_POST['popis'])) > 20) {$this->vrat .= "<div class=\"errormsg\"> <strong>Popis</strong> musí mít maximálně 20 znaků</div><br />";$status = false;}

   $sql = "select mena_text from mena where mena_text='".Help::slash($_POST['nazev'])."'";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyhledani meny z databaze',"admin_ex_db");

   if($res->numRows() > 0){ $this->vrat .= "<div class=\"errormsg\">Tato \"Měna\"  (".Help::Html($_POST['nazev']).") již existuje</div><br />";$status = false;}

   #vsechno je  vporadku muzeme aktualizovat#
   if($status){

	    $sql = "insert into mena (mena_text,mena_info) values ('".Help::slash($_POST['nazev'])."','".Help::slash($_POST['popis'])."')";
        $res =& $this->dbGame->query($sql);
	    if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni nove meny',"admin_ex_db");
	    $this->vrat .= "<div class=\"okmsg\">Měna ".Help::Html($_POST['nazev'])." byla úspěšně vytvořena</div><br />";

		It6_Log::info(
			"New currency '%currency%' was added.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('currency' => $_POST['nazev'])
		);
	}

  }


 /**
 * Vymazani meny
 * @param int $lang_id id jazyka
 */
  private function DeleteMena($mena_id){

     $sql = "delete from mena where mena_id=".$mena_id;
     $res =& $this->dbGame->query($sql);
	 if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vymazani z tabulky mena',"admin_ex_db");

	 if($this->dbGame->affectedRows()){

	 $this->vrat .= "<div class=\"okmsg\">Měna byla úspěšně smazána</div><br />";

		It6_Log::info(
			"Currency '%currency%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('currency' => $mena_id)
		);
	 }else
	     $this->vrat .= "<div class=\"errormsg\">Měnu se nepodařilo smazat</div><br />";


  }


 /**
 * Zobrazeni nebo skryti meny
 * @param int $mena_id id meny
 */
  private function ZobrazitSkrytMenu($mena_id){

	$sql = "update mena set zobrazeno=".(isset($_POST['zobrazit'])?1:0)." where mena_id=".$mena_id;
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: zobrazeni v tabulce mena',"admin_ex_db");

	$this->vrat .= "<div class=\"okmsg\">Měna ".Help::Html($_POST['mena'][$mena_id]['nazev'])." byla ůspěšně ".(isset($_POST['zobrazit'])?"zobrazena":"skryta")."</div><br />";
		It6_Log::info(
			"Currency '%currency%' was '%status%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'currency'	=> $_POST['mena'][$mena_id]['nazev'],
				'status'	=> (isset($_POST['zobrazit'])?"hidden":"showed")
			)
		);
  }

 /**
 * vyber dat z databaze
 * @return object
 */
  public function selectData(){

     $sql = "select mena_id,mena_text,mena_info,zobrazeno from mena order by mena_text";
     $res =& $this->dbGame->query($sql);
     if(DB::isError($res)) throw new ExHandler($sql.$res->getMessage(),"admin_ex_db");

	 return $res;

  }

 /**
 * Zobrazeni formulare
 * @return sting
 */
  private function ShowMena(){

     $res = $this->selectData();

     /*hlavicka*/
	 $this->vrat .= "<form method=\"post\" action\"?section=".$this->section."\">
	                 <table class=\"table-list\"><thead>
	                 <tr><th class=\"span-1\">&nbsp;</th><th class=\"span-2\">Název</th><th class=\"span-2\">Popis</th><th  class=\"span-2\">&nbsp;</th><th  class=\"span-2\">&nbsp;</th></thead><tbody>";

	 $x = 1;

	 while ($row =& $res->fetchRow()){

	  $this->vrat .= '<tr>
	                  <td>'. $x .'.</td>
	                  <td><input type="text" class="sinput" maxlength="20" name="mena['.$row['mena_id'].'][nazev]" value="'.Help::Html($row['mena_text']).'" /></td>
	                  <td><input type="text" class="sinput" maxlength="20" name="mena['.$row['mena_id'].'][popis]" value="'.Help::Html($row['mena_info']).'" /><input type="hidden" class="sinput" maxlength="2" name="mena['.$row['mena_id'].'][hidden]" value="'.Help::Html($row['mena_text']).'" /></td>
					  <td><input type="submit" name="delete['.$row['mena_id'].']" class="sbutton" onclick="if(!confirm(\'Opravdu chcete smazat: '.Help::Script($row['mena_text']).'?\')) return false;" value="Smazat" /></td>
					  <td><input type="submit" name="'.($row['zobrazeno']?'skryt':'zobrazit').'['.$row['mena_id'].']" class="sbutton" value="'.($row['zobrazeno']?'Skrýt':'Zobrazit').'" /></td>
				  </tr>';
	  $x++;

	 }

	    /*spodek*/
		$this->vrat .= '</table>';
		$this->vrat .= '<div class="actions"><input type="submit" style="width:280px;" name="editall" value="Aktualizovat všechny měny na stránce" /></div>';
		$this->vrat .= '<table class="table-list"><tr><th colspan="4" class=""><strong>Nová Měna</strong></th></tr>
	                  <tr>
					  <td>&nbsp;</td>
	                  <td><input type="text" class="sinput mandatory" maxlength="20" name="nazev" value="'.(isset($_POST['nazev'])?Help::Html($_POST['nazev']):"").'" /></td>
	                  <td><input type="text" class="sinput" maxlength="20" name="popis" value="'.(isset($_POST['popis'])?Help::Html($_POST['popis']):"").'" /></td>
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

}

?>
