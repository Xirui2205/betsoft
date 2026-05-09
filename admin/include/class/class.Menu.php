<?php
/*


Tato trida vytvori menu a pro prihlaseneho uzivatele zjisti prava k nednotlivym prvkum menu

prava

read : zakladni pravo pokud je 0 nastaveni dalsich prav neni dulezite, Uzivatel vubec sekci nevidi
update
delete

*/

class Menu{

const ITEM_ROOT = 'root'; // ID for root item

const ACCESS_NONE = 0;      // invisible to user
const ACCESS_VISIBLE = 1;   // visible but not accessible
const ACCESS_FULL = 2;      // accessible

private $db;                  //objekt databaze
private $priv = array();      //pole prav k jednotlivym sekcim
public $privAll = array();      //pole prav k jednotlivym sekcim pro vsechny adminy
private $menu =  array();     //pole s menu id a parent id
private $parent =  array();   //pole s menu id a parent id ale obracene dulezite pro hledani rodicu
private $htmlMenu = "";       //zde bude sestavene menu, ktere se vraci na vystup do prohlizece
public $zobrazeno = array();  //zobrazena/nezobrazena polozka menu
public $section;
private $acl = null;

/**
 * pole jmen menu
 * @access private
 * @var Page
 */
private $names = array();

  public function __construct($db,$section, &$acl = null){
	$this->db = $db;
	$this->section = $section;
	if (!empty($acl))
		$this->acl = $acl;
	else if (Zend_Registry::isRegistered('acl'))
		$this->acl = Zend_Registry::get('acl');

	$this->runAction();

  }

  /*Spusteni vsech akci*/
  public function runAction(){

	$this->htmlMenu = '';

	$this->setMenu();

    $this->buildMenu();

  }
/*
	private function &findTreeItemParent(array &$tree, $itemId, &$parentId) {
		if (array_key_exists($itemId, $tree))
			return $tree;
		foreach ($tree as $id => &$subtree) {
			$parentId = $id;
			$foundTree = &$this->findTreeItemParent($subtree, $itemId, $parentId);
			if (isset($foundTree))
				return $foundTree;
		}
		$parentId = false;
		$null = null;
		return $null;
	}

	private function &findTreeItem(array &$tree, $itemId) {
		foreach ($tree as $id => &$subtree) {
			if ($itemId == $id)
				return $subtree;
			else {
				$foundTree = &$this->findTreeItem($subtree, $itemId);
				if (isset($foundTree))
					return $foundTree;
			}
		}
		$null = null;
		return $null;
	}

	private function insertIntoTree($itemId, $parentId) {
		// test if already exists => relocation
		$prevParentId = 0;
		$parentTree = &$this->findTreeItemParent($this->tree, $itemId, $prevParentId);
		if (isset($parentTree)) {
			if ($prevParentId == $parentId)
				return;
			$itemTree = &$parentTree[$itemId];
			unset($parentTree[$itemId]);
		}
		else
			$itemTree = array();

		if (0 != $parentId) {
			$parentTree = &$this->findTreeItem($this->tree, $parentId);
			if (!isset($parentTree)) {
				$this->tree[$parentId] = array();
				$parentTree = &$this->tree[$parentId];
			}
		}
		else
			$parentTree = &$this->tree;

		//echo '<pre>'.$itemId."\n".print_r($parentTree,true).'</pre>';
		$parentTree[$itemId] = $itemTree;
	}
*/
/**
 * if empty item['acl'] ... item gains ACCESS_NONE (can be overriden to ACCESS_VISIBLE)
 * otherwise ... parents (up to root) are overriden to ACCESS_VISIBLE, item itself gains ACCESS_FULL
 */
private function updateSectionsAccess(&$item = null, array &$path = array()) {
	if (!isset($item))
		$item = &$this->items[self::ITEM_ROOT];
	if (!$item['visible']) {
		$item['access'] = self::ACCESS_NONE;
		return;
	}
	if (!empty($item['acl'])) {
		$item['access'] = self::ACCESS_FULL;
		foreach ($path as $id) {
			if (self::ACCESS_VISIBLE == $this->items[$id]['access'])
				break;
			else if (self::ACCESS_NONE == $this->items[$id]['access'])
				$this->items[$id]['access'] = self::ACCESS_VISIBLE;
		}
	}
	else
		$item['access'] = self::ACCESS_NONE;
	if (!empty($item['children'])) {
		array_unshift($path, $item['id']);
		foreach (array_keys($item['children']) as $childId)
			$this->updateSectionsAccess($this->items[$childId], $path);
		array_shift($path);
	}
}

/**
 * zjisteni aktualniho menu pro prihlasenoho uzivatele a nastaveni prav k jednotlivym sekcim
 */
private function setMenu(){
	//set_time_limit(10);
	$db = Zend_Registry::get('admindb');
	// fetch all sections
	$res = $db->select()->from('sekce')->query();
	$this->items = array( self::ITEM_ROOT => array(
		'id' => self::ITEM_ROOT,
		'visible' => true,
		'parents' => array(),
		'children' => array(),
		'acl' => true,
		'access' => self::ACCESS_FULL,
	));
	while ($row = $res->fetch()) {
		$id = $row['sekce_id'];
		$aclId = $row['acl_resource_id'];
		$this->items[$id] = array(
			'id' => $id,
			'aclId' => $aclId,
			'controller' => $row['controller'],
			'action' => $row['action'],
			'title' => $row['nazev'],
			'visible' => $row['zobrazeno'],
			'parents' => array(),
			'children' => array(),
			'acl' => $this->acl->isResourceAllowed($aclId, null, true),
			'access' => self::ACCESS_NONE,
		);
	}
	// fetch hierarchy of sections
	$res = $db->select()->from('sekce_has_parent')->query();
	while ($row = $res->fetch()) {
		$id = $row['sekce_id'];
		$parent = $row['parent_id'];
		if (empty($parent))
			$parent = self::ITEM_ROOT;
		$this->items[$id]['parents'][$parent] = $row['sekce_order'];
		$this->items[$parent]['children'][$id] = $row['sekce_order'];
	}
	// sort children by their order
	foreach ($this->items as &$item) {
		$id = $item['id'];
		if (self::ITEM_ROOT != $id && empty($item['parents'])) {
			$item['parents'][self::ITEM_ROOT] = 0;
			$this->items[self::ITEM_ROOT]['children'][$id] = 0;
		}
		else
			asort($item['parents']);
		asort($item['children']);
	}
	// update ACL data according to hierarchy
	$this->updateSectionsAccess();
	return;

/*
		$sql = "SELECT * FROM admin_prava_sekce ORDER BY sekce_id";
		$res =& $this->db->query($sql);
		if(DB::isError($res)){
			throw new ExHandler($sql.'<br>Nepodarilo se provest dotaz: vyber z pohledu admin_prava_sekce',"admin_ex_db");
		}

		while ($row =& $res->fetchRow()){

			$this->privAll[$row['sekce_id']]['read'][$row['admin_id']]   = $row['readx'];
			$this->privAll[$row['sekce_id']]['update'][$row['admin_id']] = $row['updatex'];
			$this->privAll[$row['sekce_id']]['delete'][$row['admin_id']] = $row['deletex'];
			$this->names[$row['sekce_id']] = $row['nazev'];

			if(isset($_SESSION['user_id']) && intval($_SESSION['user_id']) == $row['admin_id']){
				$this->priv[$row['sekce_id']]['read']   = $row['readx'];
				$this->priv[$row['sekce_id']]['update'] = $row['updatex'];
				$this->priv[$row['sekce_id']]['delete'] = $row['deletex'];

				$this->zobrazeno[$row['sekce_id']] = $row['zobrazeno'];
				$this->menu[$row['parent_id']][$row['sekce_id']] = $row['nazev'];
				$this->parent[$row['sekce_id']] = $row['parent_id'];
			}
		}
*/
  }

  /**
   * samotne sestaveni menu
   * nejvyssi sekce Home ma id = 1
   * param1 p_id: id rodice
   * param2 level: uroven ve ktere prave jsme
   */
	/*
	private function buildMenu($p_id=1,$level=1){
		if(isset($this->menu[$p_id])) {
			foreach ($this->menu[$p_id] as $sec_id => $text) {
				$active = ""; //pomocna promenna pro zjisteni jeslti obarvit link
				if($this->priv[$sec_id]['read']) { //admin ma pravo cist tuto sekci
					if ($this->section == $sec_id || $this->isParent($sec_id)) {
						$active = "active";
					}
					if($this->zobrazeno[$sec_id]) { //pokud je polozka nastavena na zobrazeno
						$this->htmlMenu .= $this->createLink($level, $sec_id, $text, $active);
						//if((isset($this->menu[$sec_id]) && $this->section == $sec_id) || $this->isParent($sec_id)) $this->builtMenu($sec_id,($level+1)); //kdyz ma zvoleny prvek potomky
						if ((isset($this->menu[$sec_id])) || $this->isParent($sec_id)) { //kdyz ma zvoleny prvek potomky
							$this->htmlMenu .= '<ul>';$this->buildMenu($sec_id,($level+1));$this->htmlMenu .= '</ul></li>';
						} //kdyz ma zvoleny prvek potomky
					}
				}
			}
		}
	}
	*/
	/*
	private function buildMenu(array &$parent = null, $level = 1) {
		if (!isset($parent)) {
			foreach ($this->tree as $id => &$root)
				$this->buildMenu($root, $level = 1);
		}
		else {
			foreach ($parent as $id => &$subtree) {
				$item = &$this->items[$id];
				if (empty($this->acl) || !$this->acl->isSectionAllowed($id) || !$item['visible'])
					continue;
				$active = ($this->section == $id);
				$hasSubmenu = !empty($subtree);
				$this->htmlMenu .= '<li>' . $this->createLink($level, $id, $item['title'], $hasSubmenu, $active);
				if ($hasSubmenu) {
					$this->htmlMenu .= '<ul>';
					$this->buildMenu($subtree, $level + 1);
					$this->htmlMenu .= '</ul>';
				}
				$this->htmlMenu .= '</li>';
			}
		}
	}
	*/

	private function buildMenu(array &$item = null, $level = 1) {
		$isRoot = !isset($item);
		if ($isRoot)
			$item = &$this->items[self::ITEM_ROOT];
		if (self::ACCESS_NONE == $item['access'])
			return '';
		$id = $item['id'];
		$childHtml = '';
		foreach (array_keys($item['children']) as $childId)
			$childHtml .= $this->buildMenu($this->items[$childId], $level + 1);
		$active = ($this->section == $id) ? 'active' : '';
		if ($isRoot)
			$this->htmlMenu .= $childHtml;
		else {

			$html = '<li>';
			if (self::ACCESS_VISIBLE == $item['access'])
				$html .= '<span>' . $item['title'] . '</span>';
			else if (self::ACCESS_FULL == $item['access'])
				$html .= $this->createLink($level, $id, $item['title'], !empty($childHtml), $active);
			if (!empty($childHtml))
				$html .= '<ul>' . $childHtml . '</ul>';
			$html .= '</li>';
			return $html;
		}
	}

	public function getBreadCrumb($sectionId) {
	if($sectionId) {
		$breadcrumb = '';
		$html = '';
		foreach (array_keys($this->items[$sectionId]['parents']) as $parentId)
			$breadcrumb .= $this->getBreadCrumb($parentId);

		if ($sectionId != 'root') {
	//		if ($sectionId==$this->section) {
				$menu_item = '<li>%s</li>';
				$html = sprintf($menu_item, Help::Html(I18n::tr($this->items[$sectionId]['title'])));
		/*	}
			else {
				$menu_item = '<li><a href="%s?section=%d">%s</a></li>';
				$html = sprintf($menu_item, HOST, $sectionId, Help::Html($this->items[$sectionId]['title']));
			}*/
		}
		
		
		if (!empty($breadcrumb)) {
			$html =  $breadcrumb . '&#187;' . $html ;
		}
	
		return $html;
	}
	}

	public function isSectionAllowed($sectionId) {
		if (!array_key_exists($sectionId, $this->items))
			return false;
		return (self::ACCESS_FULL == $this->items[$sectionId]['access']);
	}

  /*Zjisti jestli je predane id je rodic aktualni sekce*/
  private function isParent($sec_id){

   if($this->section != 0 && $this->section != 1){
     $p = $this->parent[$this->section];
	 while($p != 1 && $p != 0){ //pokracuje dokud id rodice neni 1 coz je HOME a 0 coz je root
	  if($p == $sec_id) return true;  //kdyz prvek menu je rodicem aktualni sekce
	  $p = $this->parent[$p];     //jdeme o uroven vys
	 }
   }

   return false;
  }

  /*vraci sestaveny odkaz*/
	private function createLink($level, $sec_id, $text, $hasSubmenu, $active = '') {
		//if ((isset($isactive)) && ($sec_id == $_GET['section'] || $this->isParent($sec_id))) $active = 'active';
		
		// nezobrazení sekce pobočky. Je to prozatím na úrovní css v databázi jsem to nechtěl vymazat, aby se v budoucnu dalo rychle zviditelnit.
		$aNotVisibled = array(270,325,257,159,268);
		$_style = "";
		if (in_array($sec_id, $aNotVisibled)) {
			$_style = "style='display:none;'";
		}
		
		if ($hasSubmenu)
			$menu_item = '<a  id="'.$sec_id.'" href="%s?section=%d" class="inactive menu%d'.$active.'" '.$_style.' onclick="return false;"><img src="_img/plus.png"/> %s%s</a>';
		else
			$menu_item = '<a href="%s?section=%d" class="single menu%d '.$active.'" '.$_style.'>%s%s</a>';
		
		$submenuIndicator = ($hasSubmenu ? '' : '');
		return sprintf($menu_item, HOST, $sec_id, $level, Help::Html(I18n::tr($text)), $submenuIndicator);
	}

   /**
 * metoda vraci menu pro bookmakery
 * @return void
 */

  public function GetBMenu(){

	$bmenu['b1'] = array('Nastavení dat','',1);
	$bmenu['b2'] = array('Sporty','b1',1);
	$bmenu['b23'] = array('Oblast','b1',1);
	$bmenu['b3'] = array('Turnaj/Událost','b1',1);
	$bmenu['b10'] = array('Práva','b3',1);
	$bmenu['b4'] = array('Druhy sázek','b1',1);
	$bmenu['b5'] = array('Typy sázek','b1',1);
	$bmenu['b6'] = array('Výhernost/Min/Max','b1',1);
	$bmenu['b18'] = array('Podpůrnost typ','b1',1);
	$bmenu['b19'] = array('Ligy','b1',1);
	//$bmenu['b6'] = array('Statistiky','',1);
	// $bmenu['b7'] = array('VĂ˝sledek','',1);
	//$bmenu['b8'] = array('TĂ˝my/Sportovci','',0);
	$bmenu['b9'] = array('Sázky','',0);
	$bmenu['b11'] = array('Vytvořit sázku','b9',0);
	$bmenu['b12'] = array('Vytvořit multi-sázku','b9',0);
	$bmenu['b13'] = array('Obrazovka sázek','b9',0);
	$bmenu['b14'] = array('Proplatit sázky','b9',1);
	$bmenu['b15'] = array('Tikety','b9',1);
	$bmenu['b17'] = array('On-line sledování','',0);
	$bmenu['b20'] = array('Povolování tiketů','',1);
	$bmenu['b26'] = array('Povolování tiketů Betradar Live','',1);
	$bmenu['b24'] = array('Povolování tiketů statistiky','',1);
	$bmenu['b21'] = array('Terno sázky','',1);

	$bmenu['b22'] = array('LIVE sázky','',1);
	$bmenu['b16'] = array('Vytvořit LIVE-sázku','b22',0);
	$bmenu['b123'] = array('Zobrazit LIVE-sázku','b22',0);
	$bmenu['b125'] = array('BETRADAR LIVE','b22',0);
	$bmenu['b27'] = array('Poznámky','',1);
   return $bmenu;

  }

 /**
 * metoda vytvori menu pro bookmakery
 * @return void
 */
  public function CreateBookMenu(){

   $bmenu = $this->GetBMenu();

   $sec = (isset($_GET['section']) && isset($bmenu[$_GET['section']])?$_GET['section']:'b0');
   $sechelp = $sec;
   $parent = array();

   $parent[] = '';

   if(isset($bmenu[$sechelp])){

    while($bmenu[$sechelp][1] != ''){

     $parent[] = $bmenu[$sechelp][1];
     $sechelp = $bmenu[$sechelp][1];

    }

   }

   $parent = array_reverse ($parent);
   array_unshift ($parent, "");

	$level = 1;
	reset($parent);

	$this->BuildBookMenu($bmenu,$parent,$sec,0,1);



  }

  private function BuildBookMenu($bmenu,$parent,$sec,$pl,$level) {

		$bmenu = $bmenu;
		$active = '';

		
		foreach ($bmenu as $k => $h) {

			
			/*if ($k == $_GET['section']) $active = ' active';
			else $active = '';*/

			if($h[2] == 1 && $_SESSION['superbookmaker'] == 0) continue;

			if($k == $parent[($pl+1)]) { //je rodic

				$this->htmlMenu .= '<li><a href="'.HOST.'?superb=1&section='.$k.'" class="menu'.$level.$active.'"  >'.$h[0].' </a><ul>';
				unset($bmenu[$k]);
				

				$this->BuildBookMenu($bmenu,$parent,$sec,($pl+1),($level+1));

				$this->htmlMenu .= '</ul></li>';

			} else if( $h[1] == $parent[$pl]) { //je to aktualni prvek
				$znak = '';
				foreach ($bmenu as $k2 => $h2) {
					if ($h2[1] == $k) {
						$znak = '+';	
					}
				}
				$this->htmlMenu .= '<li><a href="'.HOST.'?superb=1&section='.$k.'" class="menu'.$level.$active.'"  >'.$h[0].' '. $znak .'</a><ul>';
				unset($bmenu[$k]);
				
			
				foreach ($bmenu as $k2 => $h2) {
					if($h2[1] == $k) {
						$this->htmlMenu .= '<li><a href="'.HOST.'?superb=1&section='.$k2.'" class="menu'.($level+1).$active.'"  >'.$h2[0].'</a></li>';
						unset($bmenu[$k2]);

					}
				}
				$this->htmlMenu .= '</ul></li>';

			} else if($h[1] == $parent[$pl]) { //je potomek

				$this->htmlMenu .= '<li><a href="'.HOST.'?superb=1&section='.$k.'" class="menu'.$level.$active.'"  >'.$h[0].'</a></li>';

				unset($bmenu[$k]);


			} 
			
		}
  }

 /**
 * metoda nazev dane sekce
 * @return string
 */
  public function getName($sec_id){

   if(isset($this->names[intval($sec_id)]))
        return $this->names[intval($sec_id)];
   else return "";

  }

public function getChildSections($section = null) {
	if (!isset($section))
		$section = $this->section;
	$childSections = array();
	if (array_key_exists($section, $this->items)) {
		foreach ($this->items[$section]['children'] as $id => $order) {
			$child = $this->items[$id];
			$childSections[$id] = array('title' => $child['title']);
		}
	}
	return $childSections;
}

   /**
 * vytvoreni kratke cesty
 * @return string
 */
  public function shortWay($for_section_id = 0){

  	$use_section = $for_section_id?$for_section_id:$this->section;

	$vrat = "";

    $parent_id = array();

//    $parent_id[] = $this->section;
    $parent_id[] = $use_section;

//	$id = $this->section;
	$id = $use_section;

	if(isset($this->parent[$id])){

	 while($sid = $this->parent[$id]){

	  $parent_id[] = $sid;
	  $id = $sid;

	  if(!isset($this->parent[$id])) break;

	 }

	}

	$parent_id = array_reverse ($parent_id);
    $pocet = count($parent_id);

	for($x=0;$x<$pocet;$x++){

	 if(($x+1) == $pocet && isset($this->names[$parent_id[$x]]))
	 	if($for_section_id) {
	  		$vrat .= "<a href=\"?section=".$for_section_id."\">".$this->names[$parent_id[$x]]."</a>";
	 	} else $vrat .= $this->names[$parent_id[$x]];
	 else if(isset($this->names[$parent_id[$x]]))
	  $vrat .= "<a href=\"?section=".$parent_id[$x]."\">".$this->names[$parent_id[$x]]."</a> -> ";

	}


	return $vrat;

  }


  /**
   Vraci prava k dane sekci
   *param1 section: cislo sekce
   *param2 praco: typ prava (read,update,delete)
  */
	public function getPriv($section,$pravo){
/*
   if((is_numeric($section) && isset($this->priv[$section][$pravo]) && $this->priv[$section][$pravo]) || (isset($_SESSION['superadmin']) && $_SESSION['superadmin']))
     return true;
   else
     return false;
*/
		if (!array_key_exists($section, $this->items))
			return false;
		else
			return (
				!empty($this->acl)
				&& $this->acl->isResourceAllowed($this->items[$section]['aclId'], $pravo, true)
			);
	}

    /*
   Vraci prava k dane sekci pro urciteho admina
   *param1 section: cislo sekce
   *param2 praco: typ prava (read,update,delete)
   *param3 admin_id: id admina
  */
  public function getPrivAll($section,$pravo,$admin_id){

   if((is_numeric($section) && isset($this->privAll[$section][$pravo][$admin_id]) && $this->privAll[$section][$pravo][$admin_id]))
     return true;
   else
     return false;

  }

  /*
   Vraci atribut menu
  */
  public function getMenuArray(){

    return $this->menu;

  }

  /*
   Vraci vysledne menu v html
  */
  public function getMenu(){

    return $this->htmlMenu;

  }

  public function __destruct(){

  }

}


?>
