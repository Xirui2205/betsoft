<?php
/**
 * @package  help


*/

//TODO: IT6 refaktorovat

/**
 * Trida pro editaci menu
 *
 *
 * <code>
 *    $this->obj =  new MenuDef($this->db,$this->section);
 *	  $this->obj->setPrivileges($this->menu->getPriv(3,'update'),$this->menu->getPriv(3,'delete'));
 *	  $this->obj->runAction();
 *	  $this->obsah = $this->obj->getContent();
 * </code>
 *
 * @package    Ciselniky
 */

class MenuDef{

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * pole s menu id a parent id
 * @access private
 * @var array
 */
private  $menu = array();

/**
 * navratova hodnota z tridy
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * aktualni sekce
 * @access private
 * @var int
 */
private  $section;

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
 * pole rodicu a potomku
 * @access private
 * @var array
 */
private  $menuArray =  array();

/**
 * pole zobrazeno/nezobrazeno
 * @access private
 * @var array
 */
private  $zobrazeno =  array();

/**
* Konstruktor
*
*
* @param PEAR::DB $dbGame objekt spojeni s databazi
* @param int $section id aktualni sekce
*/
  public function __construct($db,$section_id){

    $this->db = $db;
    $this->section = $section_id;

    $this->vrat .= "<div id=\"newsection\" style=\"position:fixed;_position:absolute;top:100px;left:380px;display:none\">
	         <form action=\"\" method=\"get\">
			 <strong>Nová položka</strong>
              <input type=\"text\" name=\"newtext\">
			  <input type=\"hidden\" id=\"sec_id\"  name=\"sec_id\" value=\"\" />
			  <input type=\"hidden\" name=\"section\" value=\"".$this->section."\" />
			  <input type=\"submit\" name=\"newsec\" value=\"Vytvořit\" />
              </form></div>";

  }

  /**
  *Metoda provede ruzne akce pro praci  s menu
  *@param array $m  pole menu z tridy Menu
  *@param array $z pole zobrazeno z tridy Menu
  * @return void
  */
  public function runAction($m,$z){

  #V sekci muze pacovat pouze superadmin#
  if(!$_SESSION['superadmin']){

   $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro vstup do této sekce</div>\n";
   return;

  }


  $this->menuArray =  $m;
  $this->zobrazeno =  $z;
    #chceme zmenit udaje#
	if(isset($_GET['submit'])){

	  if($this->update){

	    $sth = $this->db->prepare('update sekce set nazev=? where sekce_id=?');
        if (PEAR::isError($sth)) {
           throw new ExHandler($sth->getMessage(),"admin_ex_db");
        }

		if(isset($_GET['menu']) && is_array($_GET['menu'])){

		  foreach($_GET['menu'] as $k=>$h){

            if(mb_strlen($h) <= 80){

		      $res =& $this->db->execute($sth,array($h,$k));
              if (PEAR::isError($res))
                 throw new ExHandler($res->getMessage(),"admin_ex_db");

		    }

		  }

		It6_Log::info(
			"Update of menu items.",
			It6_Log::TAG_ADMIN_OPERATION
		);
		}

	  }else
	       $this->vrat .= "<div class=\"errormsg\">Nemáte právo měnit údaje</div>\n";

	}

	#chceme vymazat urcitou sekci#
	else if(isset($_GET['act']) && $_GET['act'] == "delete"){

	 if($this->delete && intval($_GET['sec_id']) != 1){

	   $sql = "delete from sekce where sekce_id=".intval($_GET['sec_id']);
       $res =& $this->db->query($sql);
       if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vymazani položky menu',"admin_ex_db");

	   $this->deleteChild($_GET['sec_id']);

		It6_Log::info(
			"Menu item '%item%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('item' => intval($_GET['sec_id']))
		);
	   $this->vrat .= "<div class=\"okmsg\">Položka byla úspěšně vymazána</div>\n";


	 }else
	       $this->vrat .= "<div class=\"errormsg\">Nemáte právo mazat údaje</div>\n";

	}

	#chceme zobrazit/skryt prvek#
	else if(isset($_GET['act']) && ($_GET['act'] == "show" || $_GET['act'] == "hide")){

	  if($this->update && intval($_GET['sec_id']) != 1){

	    $sql = "update sekce set zobrazeno=".($_GET['act'] == "show"?1:0)." where sekce_id=".intval($_GET['sec_id']);
        $res =& $this->db->query($sql);
        if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: zobrazit/skryt prvek menu',"admin_ex_db");

		It6_Log::info(
			"Menu item '%item%' was '%action%'.",
			It6_Log::TAG_ADMIN_OPERATION,
			array(
				'item'		=> intval($_GET['sec_id']),
				'action'	=> ($_GET['act'] == "show"?"shown":"hidden")
			)
		);

		$this->vrat .= "<div class=\"okmsg\">Položka byla nastavena na ".($_GET['act'] == "show"?"zobrazeno":"nezobrazeno")."</div>\n";
		($_GET['act'] == "show"?$this->zobrazeno[$_GET['sec_id']]=1:$this->zobrazeno[$_GET['sec_id']] = 0);

	  }else
	       $this->vrat .= "<div class=\"errormsg\">Nemáte právo měnit údaje</div>\n";

	}

	#chceme pridat novy prvek prvek#
	else if(isset($_GET['sec_id']) && isset($_GET['newtext']) && mb_strlen(trim($_GET['newtext'])) > 0){

	  if($this->update){

	   if(mb_strlen(trim($_GET['newtext'])) <= 80){

	    $this->db->autoCommit(false);

	    $sql = "insert into sekce (parent_id,nazev,zobrazeno) values(".intval($_GET['sec_id']).",'".Help::slash($_GET['newtext'])."',0)";
        $res =& $this->db->query($sql);
        if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vytvorit prvek menu',"admin_ex_db");

		It6_Log::info(
			"Menu item '%item%' was added.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('item' => $_GET['newtext'])
		);

		$this->vrat .= "<div class=\"okmsg\">Položka byla vytvorena </div>\n";


	    /*zpristupneni sekce vsem superadminum*/

		$id = mysqli_insert_id($this->db->connection);

		$sth = $this->db->prepare('replace INTO prava(sekce_id,admin_id,readx,updatex,deletex) VALUES (?,?,?,?,?)');
        if (PEAR::isError($sth)) {
		   $this->db->rollback();
           throw new ExHandler($sth->getMessage(),"admin_ex_db");
        }

		$sql = "select admin_id from admin where superadmin=1";
        $res =& $this->db->query($sql);
        if(DB::isError($res)) {$this->db->rollback();throw new ExHandler('Nepodarilo se provest dotaz: vybrat vsechny superadminy',"admin_ex_db");}

		while($row =& $res->fetchRow()){

          $res2 =& $this->db->execute($sth,array($id,$row['admin_id'],1,1,1));
          if (PEAR::isError($res2)) {
		    $this->db->rollback();
            throw new ExHandler($res2->getMessage(),"admin_ex_db");
          }

		}

		$this->db->commit();

	   }else
	       $this->vrat .= "<div class=\"errormsg\">Název musí mít max. 80 znaků</div>\n";

	  }else
	       $this->vrat .= "<div class=\"errormsg\">Nemáte právo měnit údaje</div>\n";

	}

	$this->findMenu(); //nalezeni menu

	$this->vrat .= "<form action=\"\" method=\"get\">
	                <a href=\"?section=1\">HOME</a>
	                <a href=\"javascript:newSection(1);void(0);\">+</a>
				    <a href=\"".HOST."?section=".$this->section."&sec_id=1&act=delete\">-</a><br />\n";

	$this->builtMenu(1,'&nbsp;&nbsp;&nbsp;');

	$this->vrat .= "<br /><input type=\"submit\" name=\"submit\" value=\"Potvrdit změny\" /><input type=\"hidden\" name=\"section\" value=\"".$this->section."\" /></form>\n";

  }

  /**
  *Vymaze potomky daneho prvku
  *@param int $sec_id id sekce
  * @return void
  */
  private function deleteChild($sec_id){

	if(isset($this->menuArray[$sec_id])){

	 $sth = $this->db->prepare('delete from sekce where sekce_id=?');
     if (PEAR::isError($sth))  throw new ExHandler($sth->getMessage(),"admin_ex_db");

	  foreach($this->menuArray[$sec_id] as $k=>$h){

	    $res =& $this->db->execute($sth,$k);
        if (PEAR::isError($res))  throw new ExHandler($res->getMessage(),"admin_ex_db");

		if(isset($this->menuArray[$k])) $this->deleteChild($k);

	  }

	}

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
  *samotne sestaveni menu, nejvyssi sekce Home ma id = 1
  *
  *@param int $p_id id rodice
  *@param string $level uroven ve ktere prave jsme
  * @return void
  */
  private function builtMenu($p_id=1,$level=''){


	if(isset($this->menu[$p_id])){

	 foreach($this->menu[$p_id] as $sec_id=>$text){

	     $this->vrat .= $this->createLink($level,$sec_id,$text);
		 if(isset($this->menu[$sec_id])) $this->builtMenu($sec_id,($level.'&nbsp;&nbsp;&nbsp;')); //kdyz ma zvoleny prvek potomky

	 }

	}

  }

  /**
  *vraci sestaveny odkaz
  *
  * @return string
  */

  private function createLink($level,$sec_id,$text){
   if(!isset($this->zobrazeno[$sec_id])) $this->zobrazeno[$sec_id] = 0; //prave jsme polozku vlozila a neni v poli obsazena
   return sprintf('%s<input type="text" value="%d" style="width:18px" readonly="1" />&nbsp;<input type="text" name="menu[%d]" maxlength="80" value="%s" />
                     <a href="javascript:newSection(%d);void(0);">+</a>
				     <a href="%s?section=%d&sec_id=%d&act=delete" onclick="if(!confirm(\'Opravdu chcete vymazat položku: %s\')) return false;">-</a>%s<br />',
					 $level,$sec_id,$sec_id,Help::Html($text),$sec_id,HOST,$this->section,$sec_id,Help::Script($text),($this->zobrazeno[$sec_id]?"<a href=\"?section=".$this->section."&sec_id=".$sec_id."&act=hide\">skryt</a>":"<a href=\"?section=".$this->section."&sec_id=".$sec_id."&act=show\">zobrazit</a>"));
  }

 /**
  *nalezeni vsech polozek menu a naplneni do pole
  *
  * @return void
  */
  private function findMenu(){

   /*vyber polozek menu*/
   $sql = "select sekce_id,parent_id,nazev,zobrazeno from sekce order by poradi";
   $res =& $this->db->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky sekce',"admin_ex_db");

   while ($row =& $res->fetchRow()){
	$this->menu[$row['parent_id']][$row['sekce_id']] = $row['nazev'];
	$this->zobrazeno[$row['sekce_id']] = $row['zobrazeno'];
   }

  }

   /**
  *Vraci vystup do tridy main
  *
  * @return string
  */
  public function getContent(){

    return $this->vrat;

  }

  public function __destruct(){

  }

}

?>
