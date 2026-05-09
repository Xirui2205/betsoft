<?php
/**
 * @package cislenik
*/


class UzivatelskeMenu extends Template{

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
 * pole s menu id a parent id pro title
 * @access private
 * @var array
 */
private  $title = array();

/**
 * pole s menu id a parent id pro title na wap
 * @access private
 * @var array
 */
private  $title_wap = array();

/**
 * pole s menu id a parent id pro keyword
 * @access private
 * @var array
 */
private  $keyword = array();

/**
 * navratova hodnota z tridy
 * @access private
 * @var string
 */
private  $vrat = "";

/**
 * pomocna hodnota
 * @access private
 * @var string
 */
private  $vrat2 = "";

/**
 * bude zde ulozen formular pro novou polozku menu
 * @access private
 * @var string
 */
private  $newForm = "";

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
 * pole zobrazeno/nezobrazeno
 * @access private
 * @var array
 */
private  $zobrazeno =  array();

/**
 * pole existujicich jazyku
 * @access private
 * @var array
 */
private  $jazyky =  array();

/**
 * pole textu prekladu pro polozky menu
 * @access private
 * @var array
 */
private  $preklady =  array();

/**
 * pole rodice k danemu menu
 * @access private
 * @var array
 */
private  $parent =  array();

/**
 * pole controlleru a actions
 * @access private
 * @var array
 */
private  $cnt =  array();

/**
* Konstruktor
*
*
* @param int $section id aktualni sekce
*/
  public function __construct($section_id){

    $this->section = $section_id;

   $this->dbGame = DB::connect(GDATABASE ."://". GMY_USER .":". GMY_PASS ."@". GMY_HOST ."/". GMY_DB);
   if (DB::isError($this->dbGame)) {
     throw new ExHandler($this->dbGame->getMessage(),"admin_ex_db");
   }
   $this->dbGame->setFetchMode(DB_FETCHMODE_ASSOC);
   $sql = "set names 'utf8'";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se navazat komunikaci v UTF-8',"admin_ex_db");

 }

  /**
  *Metoda provede ruzne akce pro praci  s menu
  * @return void
  */
  public function runAction(){

    #Zobrazeni/skryti prvku#
	if(isset($_GET['act']) && isset($_GET['menu_id']) && ($_GET['act'] == "show" || $_GET['act'] == "hide")){

	 if($this->update)
	    $this->ZobrazitSkryt(intval($_GET['menu_id']));
	 else
	    $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

	#Editace existujicich menu#
	else if(isset($_POST['savemenu']) && isset($_POST['menu']) && isset($_POST['poradi']) && is_array($_POST['menu'])){

	 if($this->update)
	    $this->EditMenu();
	 else
	    $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

   #Vytvoreni menu#
	else if(isset($_POST['savenewmenu']) && isset($_POST['menu']) && isset($_POST['parent_id']) && is_array($_POST['menu'])){

	 if($this->update)
	    $this->CreateMenu();
	 else
	    $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro změnu údajů</div>\n";

	}

	#nalezeni menu#
	$this->findMenu();

	#Vymazani polozky menu#
	if(isset($_GET['act']) && $_GET['act'] == 'delete' && isset($_GET['menu_id']) && isset($_GET['poradi'])){

	  if($this->delete)
	    $this->DeleteMenu(intval($_GET['menu_id']));
	 else
	    $this->vrat .= "<div class=\"errormsg\">Nemáte dostatečné právo pro mazání údajů</div>\n";

	}

	#vyber vsech jazyku z db#
    $lang = new Jazyky(0,$this->dbGame);
	$res = $lang->selectData(" where zobrazeno=1 ");

	while ($row =& $res->fetchRow()) $this->jazyky[$row['lang_id']] = $row['alt_text'];

	$this->builtMenu(0,'&nbsp;&nbsp;&nbsp;');

	$this->vrat .= $this->vrat2.$this->newForm;

	$this->dbGame->disconnect();

  }

   /**
  *Vymazani polozky menu
  *param int $menu_id  id menu
  * @return void
  */
  private function DeleteMenu($menu_id){

   $this->dbGame->autocommit(false);

   $sql = "delete from controller_convert where c_id=".$menu_id;
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vymazani polozky menu',"admin_ex_db");

   if(isset($this->menu[$menu_id])){

     $this->CascadeDelete($menu_id);

     unset($this->menu[$menu_id]);
	 unset($this->zobrazeno[$menu_id]);
	 unset($this->preklady[$menu_id]);

   }

   if(isset($this->menu[$this->parent[$menu_id]][$_GET['poradi']]))
	  unset($this->menu[$this->parent[$menu_id]][$_GET['poradi']]);

   $this->dbGame->commit();

		It6_Log::info(
			"Menu item '%item%' was deleted.",
			It6_Log::TAG_ADMIN_OPERATION,
			array('item' => $menu_id)
		);

   $this->vrat .= "<div class=\"okmsg\">Menu včetně všech potomků bylo smazáno</div>\n";

  }

  /**
  *Kaskadove vymazani polozek menu daneho rodice
  *param int $parent_id  id rodice
  * @return void
  */
  private function CascadeDelete($parent_id){

    foreach($this->menu[$parent_id] as $k=>$h){

	   $sql = "delete from controller_convert where menu_id=".intval($h);
       $res =& $this->dbGame->query($sql);
       if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vymazani polozky menu',"admin_ex_db");

	   if(isset($this->menu[$h])){

	     $this->CascadeDelete($h);
	     unset($this->menu[$h]);
	     unset($this->zobrazeno[$h]);
	     unset($this->preklady[$h]);

	   }

    }

  }

   /**
  *Vytvoreni nove polozky menu
  * @return void
  */
  private function CreateMenu(){

   $poradi = 1;

   $this->dbGame->autocommit(false);

   	$sql = "select COALESCE((max(c_id)+1),1) as maximal from controller_convert";
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vybrani maximalni polozky menu',"admin_ex_db");

	if($row =& $res->fetchRow()) $max = intval($row['maximal']);

	$sql = "select COALESCE((max(poradi)+1),1) as maximal from controller_convert where parent_id=".intval($_POST['parent_id']);
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vybrani maximalni polozky menu',"admin_ex_db");

	if($row =& $res->fetchRow()) $poradi = ($row['maximal'] == 0?1:intval($row['maximal']));

	foreach($_POST['menu'] as $k=>$h){

	  $sql = "insert into controller_convert (c_id,lang_id,parent_id,poradi,text,req_controller,req_action,real_controller,real_action)
	  values (".$max.",".$k.",".intval($_POST['parent_id']).",".intval($poradi).",'".Help::slash($h['text'])."',
	  '".Help::slash($h['req_controller'])."','".Help::slash($h['req_action'])."','".Help::slash($h['real_controller'])."','".Help::slash($h['real_action'])."')";
      $res =& $this->dbGame->query($sql);
	  if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: vlozeni do tabulky controller_convert',"admin_ex_db");


	}

   $this->dbGame->commit();


	It6_Log::info(
		"New menu item inserted: '%item%'.",
		It6_Log::TAG_ADMIN_OPERATION,
		array('item' => $max)
	);

   $this->vrat .= "<div class=\"okmsg\">Texty byly aktualizovány</div>\n";

  }

  /**
  *Editace textu polozek menu
  * @return void
  */
  private function EditMenu(){


   $sth = $this->dbGame->prepare('replace into controller_convert (req_controller,req_action,real_controller,real_action,text,c_id,lang_id,poradi,parent_id,zobrazeno,cols,left_side,right_side,title,description) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
   if (PEAR::isError($sth)) {
       throw new ExHandler($sth->getMessage(),"admin_ex_db");
   }


   foreach($_POST['menu'] as $k=>$h){  //$k=menu_id; $h=pole

     foreach($h as $k2=>$h2){   //$k2=lang_id

	  if(mb_strlen(trim($h2['uri'])) <= 240 && mb_strlen(trim($h2['uri'])) <= 60){


	   $res =& $this->dbGame->execute($sth,array($h2['req_controller'],$h2['req_action'],$h2['real_controller'],$h2['real_action'],$h2['text'],intval($k),intval($k2),intval($_POST['poradi']),intval($_POST['parent_id']),intval($_POST['zobrazeno']),intval($_POST['num_cols'][$k]),$_POST['l_col'][$k],$_POST['r_col'][$k],$h2['title'],$h2['description']));
       if (PEAR::isError($res))
          throw new ExHandler($res->getMessage(),"admin_ex_db");

	  }else
	      $this->vrat .= "<div class=\"okmsg\">Všechny texty nebyly aktualizovány. Nastala chyba v délce (uri max. 240 znaků, text max. 60 znaků).</div>\n";

	 }



   }

	It6_Log::info(
		"Menu item Edited: '%item%'.",
		It6_Log::TAG_ADMIN_OPERATION,
		array('item' => $k)
	);
   $this->vrat .= "<div class=\"okmsg\">Texty byly aktualizovány</div>\n";

  }

  /**
  *Zobrazi/Skryje prvek menu
  *@param int $menu_id id menu
  * @return void
  */
  private function ZobrazitSkryt($menu_id){

	$sql = "update controller_convert set zobrazeno=".($_GET['act'] == "show"?1:0)." where c_id=".$menu_id;
    $res =& $this->dbGame->query($sql);
	if(DB::isError($res)) throw new ExHandler('Nepodarilo se provest dotaz: zobrazeni/skryti polozky menu',"admin_ex_db");

	$this->vrat .= "<div class=\"okmsg\">Položka menu byla ůspěšně ".($_GET['act'] == "show"?"zobrazena":"skryta")."</div><br />";

	It6_Log::info(
		"Menu Item '%item%' was '%action%'.",
		It6_Log::TAG_ADMIN_OPERATION,
		array(
			'item'		=> $k,
			'action'	=> ($_GET['act']=="show"?"shown":"hidden")
		)
	);


  }

  /**
  *samotne sestaveni menu, nejvyssi sekce Home ma id = 1
  *
  *Nejvyssi rodic (root) ma menu
  *
  *@param int $p_id id rodice
  *@param string $level uroven ve ktere prave jsme
  * @return void
  */
  private function builtMenu($p_id=0,$level=''){


	if(isset($this->menu[$p_id])){

	 ksort($this->menu[$p_id]);

	 foreach($this->menu[$p_id] as $k=>$h){  //$k = poradi

	       $this->vrat .= $this->createLink($level,$k,$h,$p_id);
		   if(isset($this->menu[$h])) $this->builtMenu($h,($level.'&nbsp;&nbsp;&nbsp;')); //kdyz ma zvoleny prvek potomky

	 }

	}

  }

  /**
  *vraci sestaveny odkaz
  *
  *@param string $level zanoreni
  *@param int $porad paradi polozky
  *@param int $menu_id id menu
  *@param int $parent_id id rodic
  * @return string
  */
  private function createLink($level,$poradi,$menu_id,$parent_id){

   if(!isset($this->zobrazeno[$menu_id])) $this->zobrazeno[$menu_id] = 0; //prave jsme polozku vlozila a neni v poli obsazena

   $zobrazit = '(<a href="?section='.$this->section.'&menu_id='.$menu_id.'&nazev='.$menu_id.'&act='.($this->zobrazeno[$menu_id]?"hide":"show").'">'.($this->zobrazeno[$menu_id]?"Skrýt":"Zobrazit").'</a>)';
   $nova = " <a href=\"javascript:var ob=new getObj('newmenu".$menu_id."');displayObj(ob);ob.style.zIndex=zindex;zindex+=2;void(0);\">+</a>";
   $smazat = ' <a href="?section='.$this->section.'&menu_id='.$menu_id.'&act=delete&poradi='.$poradi.'" onclick="if(!confirm(\'Opravdu si přejete smazat položku menu?\'))return false;">-</a>';

   if(!isset($this->preklady[$menu_id][1]) || mb_strlen(trim($this->preklady[$menu_id][1][key($this->preklady[$menu_id][1])])) < 1) $this->vrat .= $level."<strong>".$menu_id."</strong> <a href=\"javascript:var ob=new getObj('menu".$menu_id."');displayObj(ob);ob.style.zIndex=zindex;zindex+=2;void(0);\">Překlad nenalezen</a> ".$zobrazit.$nova.$smazat;
   else $this->vrat .= $level."<strong>".$menu_id."</strong> <a href=\"javascript:var ob=new getObj('menu".$menu_id."');displayObj(ob);ob.style.zIndex=zindex;zindex+=2;void(0);\">".Help::Html($this->preklady[$menu_id][1][key($this->preklady[$menu_id][1])])."</a> ".$zobrazit.$nova.$smazat;

   $this->vrat .= "<br />";

   $this->vrat2 .= '<table id="menu'.$menu_id.'" style="position:absolute;top:122px;left:350px;border:1px solid #979797;background:#FBE2A9;padding:6px 8px;display:none">
    <form action="?section='.$this->section.'" method="post"><tr><th colspan="3">Id menu: '.$menu_id.'</th></tr><tr><th colspan="2">Reg. Controler/Action : Real Controler/Action</th><th>Text</th></tr>';

   $this->newForm .= '<table id="newmenu'.$menu_id.'" style="position:absolute;top:122px;left:350px;border:1px solid #979797;background:#FBE2A9;padding:6px 8px;display:none">
    <form action="?section='.$this->section.'" method="post"><tr><th colspan="3">Rodič Id menu: '.$menu_id.'</th></tr><tr><th>Jazyk</th><th colspan="2">Reg. Controler/Action : Real Controler/Action</th><th>Text</th></tr>';


   foreach($this->jazyky as $k=>$h){

	 $this->vrat2 .= '<tr><td rowspan="2" valign="top"><strong>'.Help::Html($h).'</strong></td><td>

	 <input type="text" name="menu['.$menu_id.']['.$k.'][req_controller]" value="'.$this->cnt[$menu_id][$k]['req_controller'].'" /></td><td>
	 <input type="text" name="menu['.$menu_id.']['.$k.'][req_action]" value="'.$this->cnt[$menu_id][$k]['req_action'].'" /></td>
	                  <td rowspan="2" valign="top"><input type="text" name="menu['.$menu_id.']['.$k.'][text]" value="'.(isset($this->preklady[$menu_id][$k])?Help::Html($this->preklady[$menu_id][$k][key($this->preklady[$menu_id][$k])]):"").'"/></td></tr>

	 <tr><td> <input type="text" name="menu['.$menu_id.']['.$k.'][real_controller]" value="'.$this->cnt[$menu_id][$k]['real_controller'].'" /></td>     <td>
	 <input type="text" name="menu['.$menu_id.']['.$k.'][real_action]" value="'.$this->cnt[$menu_id][$k]['real_action'].'" /> </td> </tr>

	                  ';

	 $this->vrat2 .= '<tr><td valign="top" colspan="2">Desc. <textarea type="text" cols="30" rows="1"  name="menu['.$menu_id.']['.$k.'][description]">'.Help::Html($this->keyword[$menu_id][$k]).'</textarea></td>';
	 $this->vrat2 .= '<td colspan="2" valign="top">Tit.<textarea  cols="30" rows="1" name="menu['.$menu_id.']['.$k.'][title]"/>'.Help::Html($this->title[$menu_id][$k]).'</textarea>
	 <br />



	 </td></tr><tr><td colspan="5" ><hr /></td></tr>';



	 $this->newForm .= '<tr><td rowspan="2" valign="top">'.Help::Html($h).'</td><td><input type="text" name="menu['.$k.'][req_controller]" value="index" /></td>
	                        <td><input type="text" name="menu['.$k.'][req_action]" value="index" /></td>
	                      <td rowspan="2" valign="top"><input type="text" name="menu['.$k.'][text]" value=""/></td></tr>

	                      <tr><td><input type="text" name="menu['.$k.'][real_controller]" value="index" /></td>
	                        <td><input type="text" name="menu['.$k.'][real_action]" value="index" /></td>
	                      </tr>';

   }



	$this->vrat2 .= '<tr><td colspan="3">';



	$pocet_sloupcu = 3;
	$left_col = $right_col = '';
	$sql = "select cols,left_side,right_side from controller_convert a where a.c_id=".$menu_id;
    $res =& $this->dbGame->query($sql);
    if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky sekce_herna',"admin_ex_db");

	$this->newForm .= '<tr><td colspan="3">';

	if ($row =& $res->fetchRow()){
		$pocet_sloupcu = $row['cols'];
		$right_col = $row['right_side'];
		$left_col  = $row['left_side'];
	}

	$this->vrat2 .= '</td></tr>';
	$this->vrat2 .= '<tr><td>Počet sloupců: </td><td ><input type="text" value="'.$pocet_sloupcu.'" name="num_cols['.$menu_id.']"/></td></tr>';
	$this->vrat2 .= '<tr><td>Levý sloupec: </td><td ><input type="text" value="'.$left_col.'" name="l_col['.$menu_id.']"/></td></tr>';
	$this->vrat2 .= '<tr><td>Pravý sloupec: </td><td ><input type="text" value="'.$right_col.'" name="r_col['.$menu_id.']"/></td></tr>';
    $this->vrat2 .= '<tr><td colspan="3"><input type="submit" name="savemenu" value="Uložit" /></td></tr><input type="hidden" name="parent_id" value="'.$parent_id.'" /><input type="hidden" name="poradi" value="'.$poradi.'" /><input type="hidden" name="zobrazeno" value="'.Help::Html($this->zobrazeno[$menu_id]).'" /></form></table>';



	$this->newForm .= '<tr><td colspan="3">';


	$this->newForm .= '</td></tr>';

    $this->newForm .= '<tr><td colspan="3"><input type="submit" name="savenewmenu" value="Uložit" /></td></tr><input type="hidden" name="parent_id" value="'.$menu_id.'" /></form></table>';

  }

 /**
  *nalezeni vsech polozek menu a naplneni do pole
  *
  * @return void
  */
  private function findMenu(){

    $this->vrat .= "<br />ROOT <a href=\"javascript:var ob=new getObj('newmenu0');displayObj(ob);ob.style.zIndex=zindex;zindex+=2;void(0);\">+</a><br />";

   /*vyber polozek menu*/
   $sql = "select c_id,lang_id,parent_id,text,req_controller,req_action,real_controller,real_action,poradi,zobrazeno,title,description from controller_convert order by parent_id,poradi";
   $res =& $this->dbGame->query($sql);
   if(DB::isError($res)) throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z tabulky controller_convert',"admin_ex_db");

   while ($row =& $res->fetchRow()){

    if($row['c_id'] != $row['parent_id']){

	 $this->menu[$row['parent_id']][$row['poradi']] = $row['c_id'];


	 $this->parent[$row['c_id']] = $row['parent_id'];

	 // this caused warning because of 'uri' index
     	 $this->preklady[$row['c_id']][$row['lang_id']][ (empty($row['uri']) ? '' : $row['uri']) ] = $row['text'];
	 $this->zobrazeno[$row['c_id']] = $row['zobrazeno'];

	}
	$this->title[$row['c_id']][$row['lang_id']] = $row['title'];
    $this->cnt[$row['c_id']][$row['lang_id']]['req_controller'] = $row['req_controller'];
	$this->cnt[$row['c_id']][$row['lang_id']]['req_action'] = $row['req_action'];
	$this->cnt[$row['c_id']][$row['lang_id']]['real_controller'] = $row['real_controller'];
	$this->cnt[$row['c_id']][$row['lang_id']]['real_action'] = $row['real_action'];

	 $this->keyword[$row['c_id']][$row['lang_id']] = $row['description'];

   }

$jazyky = new Jazyky();
  $res = $jazyky->selectData("where zobrazeno=1 ");
  $this->vrat .= '<table id="newmenu0" style="position:absolute;top:122px;left:350px;border:1px solid #979797;background:#FBE2A9;padding:6px 8px;display:none">
   <form action="?section=45" method="post"><tr><th colspan="3">Rodič Id menu: ROOT</th></tr><tr><th>Jazyk</th><th colspan="2">Reg. Controler/Action : Real Controler/Action</th><th>Text</th></tr>';

   while ($row =& $res->fetchRow()){

   	 $this->vrat  .= '<tr><td rowspan="2" valign="top">'.Help::Html($row['alt_text']).'</td><td><input type="text" name="menu['.$row['lang_id'].'][req_controller]" value="index" /></td>
	                        <td><input type="text" name="menu['.$row['lang_id'].'][req_action]" value="index" /></td>
	                      <td rowspan="2" valign="top"><input type="text" name="menu['.$row['lang_id'].'][text]" value=""/></td></tr>

	                      <tr><td><input type="text" name="menu['.$row['lang_id'].'][real_controller]" value="index" /></td>
	                        <td><input type="text" name="menu['.$row['lang_id'].'][real_action]" value="index" /></td>
	                      </tr>';

   }



	$this->vrat .= '<tr><td colspan="3">';



	$this->vrat .= '</td></tr>';


	$this->vrat .= '<tr><td colspan="3"><input type="submit" name="savenewmenu" value="Uložit" /></td></tr></table>
		            <input type="hidden" name="parent_id" value="0" /><input type="hidden" name="poradi" value="'.(count($this->menu[0])+1).'" /></form>';


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
