<?php
/**
 * @package    help
 */

/**
 * Trida pro spravu obsahu "statickych" stranek
 *
 * @package    main
 */

class StatickeStranky extends AbstractSection {

const PARAM_NEW = 'new';
const PARAM_EDIT = 'edit';
const PARAM_DELETE = 'delete';
const PARAM_ACTION = 'action';
const PARAM_IMPORTFILE = 'importFile';
const PARAM_ONLYNEW = 'onlynew';

const ACTION_EXPORT = 'export';
const ACTION_IMPORT = 'import';

const CONTROLLER = It6_Models_ControllerConvert::CONTROLLER_STATIC_PAGES;
const SCRIPT_PATH ='web/application/views/scripts/';

const DEFAULT_REQ_CONTROLLER = 'sp';

/**
 * spojeni na databazi game
 * @access private
 * @var DB
 */
private  $dbGame;

/**
 * instance LanguageTabs
 */
private $langTabs;

/**
 * pole statickych stranek
 * @access private
 * @var array
 */
private $pages;

/**
 * Mapa DB_name -> form_name
 * @var array
 */
private $fieldMap;

/**
* Mapa DB_name -> form_name for page metadata table
* @var array
*/
private $fieldMapMeta;

/**
 * Konstruktor
 *
 * Pokud neni identifikator spojeni predan vytvori se nove spojeni
 *
 * @param int $section id aktualni sekce
 * @param PEAR::DB $dbGame objekt spojeni s databazi
 * @param PEAR::DB $db objekt spojeni s databazi
 */
public function __construct($section){
	parent::__construct($section);
	$this->dbGame = DbUtil::connectWebDb();
	$this->langTabs = new LanguageTabs(null, $this->dbGame);
	$this->fieldMap = array(
		'page_id' => 'pageId',
		'page_name' => 'pageName',
		//'lang_id' => 'lang',
		'template' => 'pageTemplate',
		'visible' => 'pageVisible',
		'page_content' => 'pageContent',
		'layout_name' => 'layoutName',
		'layout_id' => 'layoutId'
	);
	$this->fieldMapMeta = array(
		'c_id' => 'controllerId',
		//'lang_id' => 'lang',
		'title' => 'pageTitle',
		'description' => 'pageDescription',
		'keywords' => 'pageKeywords',
	);
}

/**
 * Metoda spousti jednitlove metody podle stavu
 * @return void
 */
public  function runAction(){
	if (!empty($_POST[self::PARAM_ACTION])) {
		$action = $_POST[self::PARAM_ACTION];
		if (self::ACTION_EXPORT == $action) {
			$pages = $_POST['chkStatPage'];
			$this->exportStaticPages($pages);
		}
		else if (self::ACTION_IMPORT == $action)
			$this->importStaticPages();
	}
	else if (!empty($_POST[self::PARAM_NEW]))
		$this->createStaticPage($_POST);
	else if (!empty($_POST[self::PARAM_EDIT]))
		$this->editStaticPage($_POST[self::PARAM_EDIT], $_POST);
	else if (!empty($_GET[self::PARAM_DELETE]))
		$this->deleteStaticPage($_GET[self::PARAM_DELETE]);
	else if (!empty($_GET[self::PARAM_NEW]))
		$this->showStaticPageForm(true);
	else if (!empty($_GET[self::PARAM_EDIT]))
		$this->editStaticPage($_GET[self::PARAM_EDIT]);
	$this->showAllPages();
	$this->dbGame->disconnect();
}

private function getAllPages($langId = null) {
	if (empty($langId))
		$res = $this->dbGame->query(
			'SELECT p.*,c.title,c.description,c.keywords FROM static_page p LEFT JOIN controller_convert c '
			. 'ON c.lang_id=p.lang_id AND c.real_controller=\'static-page\' '
  			. 'AND c.real_action COLLATE utf8_general_ci=p.page_name ORDER BY p.page_name ASC'
		);
	else
		$res = $this->dbGame->query(
			'SELECT p.*,c.title,c.description,c.keywords FROM static_page p LEFT JOIN controller_convert c '
			. 'ON c.lang_id=p.lang_id AND c.real_controller=\'static-page\' '
  			. 'AND c.real_action COLLATE utf8_general_ci=p.page_name  WHERE p.lang_id=? '
  			. 'ORDER BY p.page_name ASC',
			$langId
		);
	DbUtil::testResult($res);

	$pages = array();
	if (0 < $res->numRows()) {
		while ($row = $res->fetchRow()) {
			$pages[] = $row;
		}
	}
	return $pages;
}

private function getPage($pageName, $langId = null) {
	if (empty($pageName))
		return array();
	if (empty($langId)) {
		$stmt = $this->dbGame->prepare('
			SELECT sp.*,spl.*,c.title,c.description,c.keywords
			FROM static_page sp
			LEFT JOIN static_page_layout spl
				ON sp.layout_id = spl.layout_id
 			LEFT JOIN controller_convert c 
				ON c.lang_id=sp.lang_id AND c.real_controller=\'static-page\'
  				AND c.real_action COLLATE utf8_general_ci=sp.page_name
			WHERE page_name=?'
		);
		$res = $this->dbGame->execute($stmt, $pageName);
	}
	else  {
		$stmt = $this->dbGame->prepare('
			SELECT sp.*,spl.*,c.title,c.description,c.keywords
			FROM static_page sp
			LEFT JOIN static_page_layout spl
				ON sp.layout_id = spl.layout_id
 			LEFT JOIN controller_convert c 
				ON c.lang_id=sp.lang_id AND c.real_controller=\'static-page\'
  				AND c.real_action COLLATE utf8_general_ci=sp.page_name
			WHERE page_name=? AND sp.lang_id=?'
		);
		$res = $this->dbGame->execute($stmt, array($pageName, $langId));
	}
	DbUtil::testResult($res);

	$pages = array();
	if (0 < $res->numRows()) {
		$langs = $this->langTabs->getLanguages();
		$pages['page_name'] = $pageName;
		while ($row = $res->fetchRow()) {
			if (empty($langId)) {
				if(!empty($langs[$row['lang_id']])) {
					$langIso = $langs[$row['lang_id']]['iso'];
					$pages[$langIso] = $row;
				}
			}
			else {
				return $row;
			}
		}
	}
	return $pages;
}

/**
 * Returns value from array $values trying key as $this->fieldMap[$dbName].'_'.$langId or just as $dbName, if none such key exists $default values is returned.
 * In other words $values can be request parameters array or just database values array.
 */
private function findValue($langId, $values, $dbName, $default = '') {
	$value = $default;
	if (!empty($values)) {
		if (!empty($this->fieldMap[$dbName]))
			$field =  $this->fieldMap[$dbName];
		else if (!empty($this->fieldMapMeta[$dbName]))
			$field =  $this->fieldMapMeta[$dbName];
		
		if (!empty($langId))
			$field .= "_$langId";
		if (isset($values[$field]))
			$value = $values[$field];
		else if (isset($values[$dbName]))
			$value = $values[$dbName];
		else if ( !empty($values[$langId]) && isset($values[$langId][$dbName]) )
			$value = $values[$langId][$dbName];
	}
	return $value;
}

/**
 * Returns $this->findValue(...) escaped for HTML.
 */
private function findValueX($langId, $values, $dbName, $default = '') {
	return htmlspecialchars($this->findValue($langId, $values, $dbName, $default));
}

private function fixPageName($name) {
	return substr(preg_replace('/[^a-zA-Z0-9_-]+/', '', $name), 0, 64);
}

private function invalidateControllerConvert($remote = true) {
	It6_Models_ControllerConvert::clearCache();
	if ($remote)
		It6_NodeComm::execCommand(It6_NodeComm::CMD_INVALIDATE_CC);	
}

private function getStaticPageFormFields($langId, $values = array()) {
	$ret = '<table class="table-detail"><tr><td>';

	$ret .= '<label for="pageVisible_' . $langId . '">' . I18n::tr('Povoleno zobrazení') . ':</label>';

	$ret .= '</td><td>';
	
	$visible = ($this->findValue($langId, $values, 'visible', 1) ? ' checked="checked"' : '');
	$ret .= '<input type="checkbox" id ="pageVisible_' . $langId . '" name="pageVisible_' . $langId . "\"$visible\" /><br />\n";

	$ret .= '</td></tr>';
	$ret .= '<tr><td>';
	
	$ret .= '<label for="pageTemplate_' . $langId . '">' . I18n::tr('Jméno šablony') . ':</label>';

	$ret .= '</td><td>';
	
	$ret .= '<select name="pageTemplate_' . $langId . '" id="pageTemplate_' . $langId . '">';
		foreach($this->getTemplates() as $template) {
			$ret .= '
				<option
					value="'.$template.'"
					'.($template == $this->findValueX($langId, $values, 'template', 'index') ? 'selected="selected"' : '').'
				>'.$template.'</option>';
		}
	$ret .= "</select><br />\n";

	$ret .= '</td></tr>';
	$ret .= '<tr><td>';
	
	$ret .= '<label for="pageTemplate_' . $langId . '">' . I18n::tr('Jméno layoutu') . ':</label>';

	$ret .= '</td><td>';

	$ret .= '<select name="layoutId_' . $langId . '" id="layoutId_' . $langId . '">';
		foreach($this->getLayouts() as $layout) {
			$ret .= '
				<option
					value="'.$layout['layout_id'].'"
					'.($layout['layout_name'] == $this->findValueX($langId, $values, 'layout_name', 'index') ? 'selected="selected"' : '').'
				>'.$layout['layout_name'].'</option>';
		}
	$ret .= "</select><br />\n";

	$ret .= '</td></tr>';
	$ret .= '<tr><td>';

	$ret .= '<label for="pageTitle_' . $langId . '">' . I18n::tr('Titulek') . ':</label>';
	$ret .= '</td><td>';
	$ret .= '<input type="text" id ="pageTitle_' . $langId . '" name="pageTitle_' . $langId . '" size="40" value="' . $this->findValueX($langId, $values, 'title') . "\" /><br />\n";

	$ret .= '</td></tr>';
	$ret .= '<tr><td>';
	
	$ret .= '<label for="pageDescription_' . $langId . '">' . I18n::tr('Popis') . ':</label>';
	$ret .= '</td><td>';
	$ret .= '<input type="text" id ="pageDescription_' . $langId . '" name="pageDescription_' . $langId . '" size="40" value="' . $this->findValueX($langId, $values, 'description') . "\" /><br />\n";

	$ret .= '</td></tr>';
	$ret .= '<tr><td>';
	
	$ret .= '<label for="pageKeywords_' . $langId . '">' . I18n::tr('Klíčová slova') . ':</label>';
	$ret .= '</td><td>';
	$ret .= '<input type="text" id ="pageKeywords_' . $langId . '" name="pageKeywords_' . $langId . '" size="40" value="' . $this->findValueX($langId, $values, 'keywords') . "\" /><br />\n";

	$ret .= '</td></tr>';
	$ret .= '<tr><td>';
	
	$ret .= '<label for="pageContent_' . $langId . '">' . I18n::tr('Obsah') . ':</label><br />';
	$ret .= '</td><td>';
	$ret .= '<textarea id ="pageContent_' . $langId . '" name="pageContent_' . $langId . '" class="tinymce" cols="80" rows="30">' . $this->findValueX($langId, $values, 'page_content') . "</textarea><br />\n";
	$ret .= '</td></tr></table>';
	return $ret;
}

function showStaticPageForm($new, $values = array()) {
	$pageNameX = $this->findValueX(null, $values, 'page_name');
	$this->vrat .= '<h3>' . I18n::tr($new ? 'Nová statická stránka' : 'Upravit stránku') . "</h3>\n";
	$this->vrat .= '<form method="post" action="' . $this->url->getUrl(true) . "\">\n";
	$this->vrat .= '<input type="hidden" name="' . ($new ? self::PARAM_NEW : self::PARAM_EDIT) . '" value="' . ($new ? 1 : $pageNameX) ."\" />\n";
	$languages = $this->langTabs->getLanguages();
	$this->vrat .= '<table><tr><td>';
	
	$this->vrat .= '<label for="pageName">' . I18n::tr('Identifikátor stránky (pomlčkový zápis např. moje-stranka)') . ':</label></td><td><input type="text" id="pageName" name="pageName" value="' . $pageNameX .'" size="16" maxlength="64" /><br />' . "\n";
	$this->vrat .= '</td></tr></table>';
	
	$this->vrat .= $this->langTabs->getOutputLinks();
	$this->vrat .= '<div class="tab-container">';
	$data = array();
	foreach ($this->langTabs->getLanguageIsoValues() as $iso) {
		$this->vrat .= Utils::processTemplate('Template/tinymce.phtml', array('target'=>'textarea#pageContent_'.$iso, 'defLang' => array('isoCode' => $iso)), true);
		$data[$iso] = $this->getStaticPageFormFields($iso, $values);
	}
	$this->vrat .= $this->langTabs->getOutputTabs($data);
	$this->vrat .= '<input type="submit" value="' . I18n::tr($new ? 'Přidat' : 'Upravit') . '" />' . "\n";
	$this->vrat .= "</form><br/>\n";
	$this->vrat .= $this->langTabs->getOutputJs();
	$this->vrat .= '</div>';
}

/**
 * Create record in controller_convert table for given static page for storing page metadata
 * @param string $pageName Textual page ID, matches real action
 * @param string $pageReqController Controller part of request URL 
 * @param string $pageReqAction Action part of request URL
 * @param integer $langId
 * @param integer|NULL $controllerId If non-empty value is passed controller ID will be used else new controller ID will be created
 * @param boolean $invalidateCache
 * @return integer Controller ID, empty value on error
 */
private function createControllerConvert(
	$pageName, $reqController, $reqAction, $title, $description, $keywords, $langId, $controllerId = null, $invalidateCache = true
) {
	if (empty($controllerId)) {
		$row = $this->dbGame->query(
			'SELECT c_id FROM controller_convert WHERE req_controller=? AND req_action=? GROUP BY c_id',
			array($reqController, $reqAction)
		)->fetchRow();
		if ($row)
			$controllerId = $row['c_id'];
	}
	if (empty($controllerId)) {
		$row = $this->dbGame->query('SELECT MAX(c_id) AS id FROM controller_convert')->fetchRow();
		if (empty($row))
			$controllerId = 1;
		else {
			$controllerId = intval($row['id']) + 1;
		}
		if ($controllerId < 5000)
			$controllerId = 5000;
	}
	$res = $this->dbGame->query(
		'INSERT INTO controller_convert (c_id,lang_id,parent_id,poradi,'
		. 'zobrazeno,cols,left_side,right_side,title,description,keywords,text,req_controller,'
		. 'req_action,real_controller,real_action,after_login,actionless) VALUES '
		. '(?,?,0,1000,1,3,NULL,NULL,?,?,?,\'\',?,?,?,?,1,0)',
		array(
			$controllerId, $langId, $title, $description, $keywords, $reqController,
			$reqAction, It6_Models_ControllerConvert::CONTROLLER_STATIC_PAGES, $pageName,
		)
	);
	if (DB::isError($res))
		return 0;
	$this->invalidateControllerConvert();
	return $controllerId;
}

/**
 * Update page metadata
 * @param integer $controllerId
 * @param integer $langId
 * @param string $title
 * @param string $description
 * @param string $keywords
 * @param boolean invalidateCache
 * @return integer|boolean Count of changed rows or FALSE on error
 */
private function updateControllerConvert($controllerId, $langId, $title, $description, $keywords, $invalidateCache = true) {
	$res = $this->dbGame->query(
		'UPDATE controller_convert SET title=?,description=?,keywords=? WHERE c_id=? AND lang_id=?',
		array($title, $description, $keywords, $controllerId, $langId)
	);
	if (DB::isError($res))
		return false;
	$this->invalidateControllerConvert();
	return $res;
}

function createStaticPage($data) {
	$error = false;
	$newPageName = trim($this->findValue(null, $data, 'page_name'));
	if (!empty($newPageName)) {
		$newPageName = It6_Text::camelCaseToDashed( $this->fixPageName( $this->findValue(null, $data, 'page_name') ) );
		// this should make possible double-way conversions
		$newPageName = It6_Text::camelCaseToDashed( It6_Text::dashedToCamelCase($newPageName) );
	}
	if (empty($newPageName)) {
		$this->vrat .= '<div class="errormsg">' . I18n::tr('Stránka nebyla vytvořena! (chybný idetifikátor stránky)') . '</div>';
		$this->showStaticPageForm(true, $data);
		return false;
	}
	$this->dbGame->autoCommit(false);
	$languages = $this->langTabs->getLanguages();
	$ccs = array();
	$controllerId = null;
	$res = $this->dbGame->query(
		'SELECT c_id,lang_id FROM controller_convert WHERE real_controller=? AND real_action=?',
		array(It6_Models_ControllerConvert::CONTROLLER_STATIC_PAGES, $newPageName)
	);
	if (DB::isError($res))
		$error = true;
	else {
		while ($row = $res->fetchRow()) {
			$ccs[$row['c_id']][] = $row['lang_id']; 
		}
		if (!empty($ccs)) {
			$controllerId = array_keys($ccs);
			$controllerId = $controllerId[0];
			$controllerLangs = $ccs[$controllerId];
		}
		foreach ($languages as $lang) {
			$langId = $lang['lang_id'];
			$langIso = $lang['iso'];
			$title = trim($this->findValue($langIso, $data, 'title'));
			$description = trim($this->findValue($langIso, $data, 'description'));
			$keywords = trim($this->findValue($langIso, $data, 'keywords'));
			if (empty($ccs[$controllerId]) || !in_array($langId, $ccs[$controllerId])) {
				$id = $this->createControllerConvert(
					$newPageName,
					self::DEFAULT_REQ_CONTROLLER,
					$newPageName,
					$title,
					$description,
					$keywords,
					$langId,
					$controllerId
				);
				if (empty($id)) {
					$error = true;
					break;
				}
				$controllerId = $id;
			}
			else {
				$res = $this->updateControllerConvert($controllerId, $langId, $title, $description, $keywords);
				if (false === $res) {
					$error = true;
					break;
				}
			}
		}
	}
	if (!$error) {
		$stmt = $this->dbGame->prepare(
			'INSERT INTO static_page(page_name,lang_id,template,visible,page_content)'
			. ' VALUES (?,?,?,?,?)'
		);
		if (DB::isError($stmt))
			$error = true;
		foreach ($languages as $lang) {
			$langId = $lang['iso'];
			$visible = $this->findValue($langId, $data, 'visible', 0);
			$page = array(
				$newPageName,
				$lang['lang_id'],
				$this->findValue($langId, $data, 'template'),
				(empty($visible) ? 0 : 1),
				$this->findValue($langId, $data, 'page_content')
			);
			$res = $this->dbGame->execute($stmt, $page);
			if (DB::isError($res))
				$error = true;
		}
		$res = $this->dbGame->commit();
		if (DB::isError($res))
			$error = true;
	}
	if ($error) {
		$this->dbGame->rollback();
		$this->vrat .= '<div class="errormsg">' . I18n::tr('Stránka nebyla vytvořena!') . '</div>';
		$this->showStaticPageForm(true, $data);
	}
	else
		$this->vrat .= '<div class="okmsg">' . I18n::tr('Stránka byla vytvořena.') . '</div>';
	$this->dbGame->autoCommit(true);
	return !$error;
}

function editStaticPage($pageName, $data = array()) {
	if (!$this->update) {
		$this->vrat .=  '<div class="errormsg">' . I18n::tr('Nedostatečné oprávnění!') . '</div>';
		return;
	}
	if (empty($data)) {
		$pages = $this->getPage($pageName);
		if (empty($pages))
			$this->vrat .= '<div class="errormsg">' . I18n::tr('Stránka nenalezena!') . '</div>';
		else
			$this->showStaticPageForm(false, $pages);
	}
	else {
		$error = false;
		$stmt = $this->dbGame->prepare(
			'UPDATE static_page SET page_name=?,template=?,layout_id=?,visible=?,page_content=? WHERE page_name=? AND lang_id=?'
		);
		if (DB::isError($stmt))
			$error = true;
		$stmt2 = $this->dbGame->prepare(
			'UPDATE controller_convert SET real_action=?,title=?,description=?,keywords=? WHERE lang_id=? AND real_controller=? AND real_action=?'
		);
		if (DB::isError($stmt2))
			$error = true;
		$languages = $this->langTabs->getLanguages();
		$this->dbGame->autoCommit(false);
		$newPageName = It6_Text::camelCaseToDashed( $this->fixPageName( $this->findValue(null, $data, 'page_name') ) );
		// this should make possible double-way conversions
		$newPageName = It6_Text::camelCaseToDashed( It6_Text::dashedToCamelCase($newPageName) );
		$ccDirty = false;
		foreach ($languages as $lang) {
			$langId = $lang['iso'];
			$visible = $this->findValue($langId, $data, 'visible', 0);
			$page = array(
				$newPageName,
				$this->findValue($langId, $data, 'template'),
				$this->findValue($langId, $data, 'layout_id'),
				(empty($visible) ? 0 : 1),
				$this->findValue($langId, $data, 'page_content'),
				$pageName,
				$lang['lang_id']
			);
			$res = $this->dbGame->execute($stmt, $page);
			if (DB::isError($res))
				$error = true;
			else {
				$ctlConv = array(
					$newPageName,
					$this->findValue($langId, $data, 'title'),
					$this->findValue($langId, $data, 'description'),
					$this->findValue($langId, $data, 'keywords'),
					$lang['lang_id'],
					self::CONTROLLER,
					It6_Text::camelCaseToDashed($pageName)
				);
				$res = $this->dbGame->execute($stmt2, $ctlConv);
				if (DB::isError($res))
					$error = true;
				else
					$ccDirty = true;
			}
		}
		$res = $this->dbGame->commit();
		if (DB::isError($res))
			$error = true;
		$this->dbGame->autoCommit(true);
		if ($error) {
			$this->vrat .= '<div class="errormsg">' . I18n::tr('Stránka nebyla upravena!') . '</div>';
			$this->showStaticPageForm(false, $data);
		}
		else {
			$this->vrat .= '<div class="okmsg">' . I18n::tr('Stránka upravena.') . '</div>';
			It6_GlobalCache_Invalidator::StaticPage_update($newPageName);
			if ($ccDirty)
				$this->invalidateControllerConvert();
		}
	}
}

function deleteStaticPage($pageName) {
	if (!$this->delete) {
		$this->vrat .=  '<div class="errormsg">' . I18n::tr('Nedostatečné oprávnění!') . '</div>';
		return;
	}
	$res = $this->dbGame->query('DELETE FROM static_page WHERE page_name=?', $pageName);
	if (DB::isError($res))
		$this->vrat .=  '<div class="errormsg">' . I18n::tr('Stránka nemohla být smazána!') . '</div>';
	else if (0 == $this->dbGame->affectedRows())
		$this->vrat .=  '<div class="errormsg">' . I18n::tr('Stránka nenalezena!') . '</div>';
	else
		$this->vrat .= '<div class="okmsg">' . I18n::tr('Stránka smazána.') . '</div>';
	It6_GlobalCache_Invalidator::StaticPage_update($pageName);
}

private function writeTmpFile($f, $str, &$size) {
	fwrite($f, $str);
	$size += strlen($str);
} 

/**
 * Export selected pages in XML format (bewastatipages.xsd)
 * @param array $names array of page names to export
 */
function exportStaticPages(array $names) {
	if (empty($names))
		return;
	$db = Zend_Registry::get('zdb_game');
	$res = $db->select()
		->from(array('p' => 'static_page'), array(
			'name' => 'page_name',
			'langId' => 'lang_id',
			'template' => 'template',
			'layoutId' => 'layout_id',
			'visible' => 'visible',
			'content' => 'page_content'
		))
		->joinLeft(
			array('c' => 'controller_convert'),
			"c.real_controller=" . $db->quote(It6_Models_ControllerConvert::CONTROLLER_STATIC_PAGES)
				. " AND c.real_action COLLATE utf8_general_ci=p.page_name AND p.lang_id=c.lang_id",
			array('title', 'description', 'keywords')
		)
		->where('p.page_name IN (?)', $names)
		->query();
	$tmpFile = tmpfile();
	$size = 0;
	$this->writeTmpFile($tmpFile, "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n<data>\n<pages>\n", $size);
	while ($row = $res->fetch()) {
		$this->writeTmpFile($tmpFile, 
			"<page><name>" . htmlspecialchars($row['name']) . "</name>"
				. "<langId>{$row['langId']}</langId>"
				. "<template>" . htmlspecialchars($row['template']) . "</template>"
				. "<layoutId>{$row['layoutId']}</layoutId>"
				. "<visible>" . (empty($row['visible']) ? 0 : 1) . "</visible>"
				. "<title>" . htmlspecialchars($row['title']) . "</title>"
				. "<description>" . htmlspecialchars($row['description']) . "</description>"
				. "<keywords>" . htmlspecialchars($row['keywords']) . "</keywords>"
				. "<content>" . htmlspecialchars($row['content']) . "</content></page>\n",
		 	$size
		 );
	}
	$this->writeTmpFile($tmpFile, "</pages>\n</data>\n", $size);
	rewind($tmpFile);
	header('Content-Type: application/xml');
	header('Content-Disposition: attachment; filename="staticke-stranky-' . strftime('%Y-%m-%d-%H-%M-%S') . '.xml');
	header('Content-Length: ' . $size);
	fpassthru($tmpFile);
	fclose($tmpFile);
	It6_Session_Admin::end();
	exit();
}

function importStaticPages() {
	if (empty($_FILES[self::PARAM_IMPORTFILE]) || mb_strlen($_FILES[self::PARAM_IMPORTFILE]['name']) < 1) {
		$this->vrat .= UiUtil::printErrors('Nebyl vybrán soubor');
		return;
	}
	$fileName = $_FILES[self::PARAM_IMPORTFILE]['tmp_name'];
	$doc = null;
	if (!It6_Xml_XmlSchema::isValidBbasStaticPagesDocument($fileName, $doc)) {
		$this->vrat .= UiUtil::printErrors('Nahraný soubor není validní dokument pro import statických stránek');
		return;
	}

	$xpath = new DOMXPath($doc);
	$onlyNew = !empty($_POST[self::PARAM_ONLYNEW]);
	$db = Zend_Registry::get('zdb_game');

	// TRANSLATION RESOURCES
	$pages = array();
	// XML node name -> key in array
	$map = array(
		'name' => 'name',
		'langId' => 'langId',
		'template' => 'template',
		'layoutId' => 'layoutId',
		'visible' => 'visible',
		'title' => 'title',
		'description' => 'description',
		'keywords' => 'keywords',
		'content' => 'content'
	);
	foreach ($xpath->query('/data/pages/page') as $node) {
		$page = array();
		foreach ($node->childNodes as $child) {
			$name = $child->nodeName;
			if (array_key_exists($name, $map))
				$page[$map[$name]] = $child->textContent;
		}
		$pages[ $page['name'] ][ $page['langId'] ] = $page;
	}
	if ($onlyNew) {
		$pks = array();
		foreach ($pages as $name => $langs) {
			foreach ($langs as $langId => $page)
				$pks[] = '(' . $db->quote(array($name, $langId)) . ')';
		}
		// already present (name, langId) pairs
		$dbPages = array();
		$res = $db->select()
			->from('static_page', array('name' => 'page_name', 'langId' => 'lang_id'))
			->where('(page_name,lang_id) IN (' . implode(',', $pks) .  ')')
			->query();
		while ($row = $res->fetch()) {
			$name = $row['name'];
			$langId = $row['langId'];
			if (array_key_exists($name, $dbPages))
				$dbPages[$name][$langId] = true;
			else
				$dbPages[$name] = array($langId => true);
		}
		foreach (array_keys($pages) as $name) {
			if (array_key_exists($name, $dbPages)) {
				$langs = &$pages[$name];
				foreach (array_keys($langs) as $langId) {
					if (array_key_exists($langId, $dbPages[$name]))
						unset($langs[$langId]); 
				};
				if (empty($langs))
					unset($pages[$name]);
			}
		}
		unset($langs);
	}
	if (empty($pages))
		$this->vrat .= UiUtil::printMessages('Nenalezeny žádné nové statické stránky.');
	else {
		$sql = array();
		$names = array();
		$ccPages = array();
		$ccTuples = array();
		foreach ($pages as $langs) {
			foreach ($langs as $p) {
				$names[] = "{$p['name']}:{$p['langId']}";
				$ccTuples[] = '(' . $db->quote($p['name']) . ',' . intval($p['langId']) . ')';
				$ccPages[$p['name']][$p['langId']] = array(
					'title' => $p['title'],
					'description' => $p['description'],
					'keywords' => $p['keywords'],
				);
				$sql[] = '(' . $db->quote(array(
					$p['name'],
					$p['langId'],
					$p['template'],
					$p['layoutId'],
					$p['visible'],
					$p['content'],
				)) . ')';
			}
		}
		unset($pages);
		$sql = 'REPLACE INTO `static_page`(`page_name`,`lang_id`,`template`,'
			. '`layout_id`,`visible`,`page_content`)VALUES' . implode(',', $sql);
		$db->query($sql);
		$ccTuples = implode(',', $ccTuples);
		$rows = $db->select()
			->from('controller_convert', array(
				'name' => 'real_action',
				'langId' => 'lang_id',
				'id' => 'c_id',
			))
			->where('real_controller=?', It6_Models_ControllerConvert::CONTROLLER_STATIC_PAGES)
			->where("(real_action,lang_id) IN ($ccTuples)")
			->query()
			->fetchAll();
		$dbCcs = array();
		foreach ($rows as $row) {
			$dbCcs[$row['name']][$row['langId']] = $row['id'];
		}
		unset($rows);
		foreach ($ccPages as $name => $langs) {
			foreach ($langs as $langId => $data) {
				if (isset($dbCcs[$name][$langId])) {
					$this->updateControllerConvert(
						$dbCcs[$name][$langId],	$langId,
						$data['title'], $data['description'], $data['keywords'],
						false
					);
				}
				else {
					$this->createControllerConvert(
						$name, self::DEFAULT_REQ_CONTROLLER, $name,
						$data['title'], $data['description'], $data['keywords'],
						$langId, null, false
					);
				}
			}
		}
		It6_GlobalCache_Invalidator::StaticPage_update($names);
		$this->invalidateControllerConvert();
		$this->vrat .= UiUtil::printMessages('Statické stránky byly naimportovány');
	}

}

function showAllPAges($data = null) {
	if (!isset($data))
		$data = $this->getAllPages(CZ_LANG_ID);
	$this->vrat .=
"<script type=\"text/javascript\">
	function onExportClick() {
		if (0 == $(':checkbox[name=\"chkStatPage[]\"]:checked').length)
			return false;
		$('#statPagesAction').val('" . self::ACTION_EXPORT . "');
		return true;
	}
</script>\n";

	$this->vrat .= '<h3>' . I18n::tr('Statické stránky') . "</h3>\n";
	$this->vrat .= '<div class="actions">';
	$this->vrat .= '<button onclick="window.location=\'' . $this->url->getUrl(true, array(self::PARAM_NEW => 1)) . '\'">' . I18n::tr('Založit novou statickou stránku') . "</button>\n";
	$this->vrat .= '</div>';
	$this->vrat .= "<form action=\"\" method=\"post\">\n";
	$this->vrat .= "<input type=\"hidden\" name=\"" . self::PARAM_ACTION . "\" id=\"statPagesAction\" value=\"\" />\n";
	$this->vrat .= "<table class=\"table-list\">\n";
	$this->vrat .= '<thead><tr><th></th><th>' . I18n::tr('Identifikátor') . '</th><th>' . I18n::tr('Titulek') . "</th><th></th><th></th></tr></thead>\n";
	foreach ($data as $page) {
		$this->vrat .= "<tr>\n";
		$this->vrat .= "<td><input type=\"checkbox\" name=\"chkStatPage[]\" value=\"{$page['page_name']}\" /></td>";
		$this->vrat .= "<td>{$page['page_name']}</td><td>{$page['title']}</td>";
		$this->vrat .= "<td>";
		if ($this->update) {
			$hrefUpdate = $this->url->getUrl(true, array(self::PARAM_EDIT => $page['page_name']));
			$this->vrat .= " <a href=\"$hrefUpdate\">" . I18n::tr('Edit') . "</a>";
		}
		$this->vrat .= "</td><td>";
		if ($this->delete) {
			$hrefDelete = $this->url->getUrl(true, array(self::PARAM_DELETE => $page['page_name']));
			$this->vrat .= "<a href=\"$hrefDelete\" onclick=\"javascript: if (!window.confirm('" . I18n::tr('Opravdu smazat?') . "')) return false;\">"
				. I18n::tr('Delete') . "</a>";
		}
		$this->vrat .= "</td>\n";
		$this->vrat .= "</tr>\n";
	}
	$this->vrat .= "</table>\n";
	$this->vrat .= "<div class=\"actions\">\n";
	$this->vrat .= "<input type=\"button\" value=\"Označit vše\" onclick=\"$(':checkbox[name=\\'chkStatPage[]\\']').attr('checked','checked');\" />\n";
	$this->vrat .= "<input type=\"button\" value=\"Zrušit výběr\" onclick=\"$(':checkbox[name=\\'chkStatPage[]\\']').attr('checked','');\" />\n";
	$this->vrat .= "<input type=\"submit\" name=\"submit\" value=\"Export\" onclick=\"return onExportClick();\" />\n";
	$this->vrat .= "</div>\n";
	$this->vrat .= "</form>\n";

	$this->vrat .= "<form action=\"\" method=\"post\" enctype=\"multipart/form-data\">\n";
	$this->vrat .= "<input type=\"hidden\" name=\"" . self::PARAM_ACTION . "\" value=\"" . self::ACTION_IMPORT . "\" />\n";
	$this->vrat .= "<div class=\"actions\">
Import:
<input type=\"file\" style=\"width:280px;\"  name=\"" . self::PARAM_IMPORTFILE . "\"  />
<input type=\"submit\" name=\"submit\" value=\"Import\" />
Import pouze neexistujicich: <input type=\"checkbox\" name=\"" . self::PARAM_ONLYNEW . "\" class=\"no\" />
</div>\n";
	$this->vrat .= "</form>\n";
}

private function getLayouts() {
	$stmt = $this->dbGame->prepare('
			SELECT *
			FROM static_page_layout
		');
	$res = $this->dbGame->execute($stmt);
	DbUtil::testResult($res);

	$layouts = array();
	while($row = $res->fetchRow()) {
		$layouts[] = $row;
	}
	return $layouts;
}

private function getTemplates() {
	$dir		= self::getTemplateDir();
	$files		= scandir($dir);
	$templates	= array();

	foreach($files as $file) {
		if(strchr($file, '.phtml') == '.phtml')
			$templates[] = substr($file, 0, -6);
	}

	return $templates;
}

private static function getTemplateDir() {
	return ROOT . self::SCRIPT_PATH . self::CONTROLLER . '/';
}
} // class StatickeStranky
