<?php

class It6_Translate_Web extends It6_Translate {

private $langId;
private $controller;
private $action;

/**
 * all dictionaries, lazy load
 * array( controller => array(
 			action => array( key => text, ... ),
 				...
 			),
 		...
 	)
 *    text can be NULL (reported missing), FALSE (reported empty), EMPTY_STRING (not reported missing), NONEMPTY_STRING (translated value)
 */
private $dictionaries;
private $db;

const STATUS_EMPTY = 'empty';
const STATUS_MISSING = 'missing';

const DICTIONARY = 'DICTIONARY';

public function __construct($langId, $controller, $action, array $dictionary = null, &$db = null) {
	$this->langId = $langId;
	$this->controller = $controller;
	$this->action = $action;
	$this->dictionaries = array();
	if (isset($dictionary))
		$this->dictionaries[$controller] = array($action => self::normalizeDictionary($dictionary));
	if (isset($db))
		$this->db = &$db;
	else
		$this->db = Zend_Registry::get('db');
}

public static function normalizeDictionary(array $dictionary) {
	$normalized = array();
	foreach ($dictionary as $key => $value)
		$normalized[ strtolower($key) ] = $value;
	return $normalized;
}

public static function getMissingString($key) {
	return "== N/A $key ==";
}

public static function getEmptyString($key) {
	return "== E $key ==";
}

public function getCurrentLangId() {
	return $this->langId;
}

public function trans($key, $action = null, $controller = null, $wrapUnfoundTag=true) {
	include(ROOT.'web/cz.php');
	
	//echo $this->action;
	$origKey = $key;
	if (
		is_numeric($key) ||
		mb_strlen($key) < 2
	)
		return $key;
	if (empty($key))
		return '';
	$key = strtolower($key);
	
	if (!isset($controller)) {
  		$controller = $this->controller;
 	}
	
	if (!isset($action)) {
		$action = $this->action;
	}
	
	if (!array_key_exists($controller, $this->dictionaries)) {
		$this->dictionaries[$controller] = array();
	} else {
	}

	if (!array_key_exists($action, $this->dictionaries[$controller])) {
		$dictionary = self::loadDictionary($this->langId, $controller, $action);
		if (!is_array($dictionary)) {
			$dictionary = array();
		}
		$this->dictionaries[$controller][$action] = $dictionary;
	} else {
		$dictionary = &$this->dictionaries[$controller][$action];
	}

	$found = array_key_exists($key, $dictionary);
	$text = ($found ? $dictionary[$key] : null);
	if (!$found || false === $text) {
		if (!$found) {
			$this->report($key, self::STATUS_MISSING, $controller, $action);
			if($wrapUnfoundTag === true) {
				//return self::getMissingString($key);
				if (!isset($_main[$key])) {
					$retVal = $key;
				} else {
					$retVal = $_main[$key];
				}
				return $retVal;
			}
		} else {
		    return $key;
		}
	}
	else {
		if ( true === $text || 0 == strlen(trim($text)) ) {
			if (true !== $text)
				$this->report($origKey, self::STATUS_EMPTY, $controller, $action);
			return self::getEmptyString($key);
		}
		else
			return $text;
	}
}

public function hasTranslation($key, $action = null, $controller = null) {
/*
	if (!isset($controller))
		$controller = $this->controller;
	if (!isset($action))
		$action = $this->action;
	if (!array_key_exists($controller, $this->dictionaries))
		return false;
	if (!array_key_exists($action, $this->dictionaries[$controller]))
		return false;
*/
	return true;
}

public function __get($key) {
	return $this->trans($key);
}

/*
	public function load() {
		$this->dictionary = self::loadDictionary($this->langId, $this->controller, $this->action);
		if (false === $this->dictionary) {
			$this->dictionary = array();
			return false;
		}
		else
			return true;
	}
*/

public static function getDictionaryFileName($langId, $controller, $action) {
	$fname = 'translate_' . strtolower($controller) . '_' . strtolower($action) . '_' . strtolower($langId) . '.php';
	return ROOT . "web/application/translate/$fname";
}

public static function loadDictionary($langId, $controller, $action) {
	$fileName = self::getDictionaryFileName($langId, $controller, $action);
	if (!file_exists($fileName))
		return false;
	$values = file_get_contents($fileName);
	eval("\$trans = array($values);");
	$trans = self::normalizeDictionary($trans);

	return $trans;
}

public function report($key, $status, $controller = null, $action = null) {
	try {
		if (!isset($controller))
			$controller = $this->controller;
		if (!isset($action))
			$action = $this->action;
		Zend_Registry::get('db')->query(
			"REPLACE INTO `translate_missing`(`translate_key`, `lang_id`, `controller`, `action`, `status`) VALUES(?,?,?,?,?)",
			array($key, $this->langId, $controller, $action, $status)
		);
		return true;
	}
	catch (Exception $e) {
		echo $e;
		return false;
	}
}

public function checkReported() {
	try {
		$res = $this->db->select()
			->from( array('m' => 'translate_missing') )
			->joinLeft( array('p' => 'translate_pages'),
				'm.translate_key=p.translate_key AND m.controller=p.controller AND m.action=p.action',
				array('translate_page' => 'p.translate_key')
			)
			->joinLeft( array('t' => 'preklady'), 'm.translate_key=t.index_pole AND m.lang_id=t.lang_id', array('text') )
			->query();
		$fixPages = array();
		$fixedKeys = array();
		while ($row = $res->fetch()) {
			$key = $row['translate_key'];
			echo "Checking: $key\n";
			$langId = $row['lang_id'];
			$controller = $row['controller'];
			$action = $row['action'];
			$status = $row['status'];
			if (is_null($row['translate_page'])) {
				Help::assureArrayKeys($fixPages, array($key, $controller));
				if (!in_array($action, $fixPages[$key][$controller]))
					$fixPages[$key][$controller][] = $action;
			}
			if (!is_null($row['text'])) {
				Help::assureArrayKeys($fixedKeys, array($key, $langId, $controller, $action));
				if (empty($row['text']))
					$fixedKeys[$key][$langId][$controller][$action] = false;
				else if (false !== $fixedKeys[$key][$langId][$controller][$action])
					$fixedKeys[$key][$langId][$controller][$action] = true;
			}
		}
		echo "Fixed page associations:\n";
		foreach ($fixPages as $key => $controllers) {
			foreach ($controllers as $controller => $actions) {
				foreach ($actions as $action) {
					echo "\t$key / $controller / $action\n";
					$this->db->insert('translate_pages', array( 'translate_key' => $key, 'controller' => $controller, 'action' => $action));
				}
			}
		}
		echo "Fixed keys:\n";
		foreach ($fixedKeys as $key => $langIds) {
			$key = $this->db->quote($key);
			foreach ($langIds as $langId => $controllers) {
				//$langId = $this->db->quote($langId);
				foreach ($controllers as $controller => $actions) {
					$controller = $this->db->quote($controller);
					foreach ($actions as $action => $fix) {
						$action = $this->db->quote($action);
						echo "\t$key / $langId / $controller / $action : " . ($fix ? 'OK' : 'missing -> empty') . "\n";
						$where = "translate_key=$key AND lang_id=$langId AND controller=$controller AND action=$action";
						if ($fix)
							$this->db->delete('translate_missing', $where);
						else
							$this->db->update('translate_missing', array('status' => self::STATUS_EMPTY), $where);
					}
				}
			}
		}
		return true;
	}
	catch (Exception $e) {
		echo $e;
		return false;
	}
}

private static function escapePhpSingleQuoted($str) {
	return strtr($str, array('\\' => '\\\\', "'" => "\\'"));
}

public function generateDictionaryFiles() {
	try {
		$languages = $this->db->select()->from('jazyky', array('lang_id', 'iso'))->query()->fetchAll();
		$prevController = null;
		$prevAction = null;
		$fDict = array();
		$pages = $this->db->select()
			->from('translate_pages', array('controller', 'action'))
			->group(array('controller', 'action'))
			->order(array('controller', 'action'))
			->query()
			->fetchAll();
		foreach ($pages as $page) {
			$controller = $page['controller'];
			$action = $page['action'];
			foreach ($languages as $lang) {
				$langId = $lang['lang_id'];
				if (!empty($fDict[$langId]))
					fclose($fDict[$langId]);
				$fname = self::getDictionaryFileName($langId, $controller, $action);
				echo "Writing file: $fname\n";
				$fDict[$langId] = fopen($fname, 'w');
			}
			$keys = $this->db->select()
				->from('translate_pages', array('translate_key'))
				->where('controller=?', $controller)
				->where('action=?', $action)
				->order(array('translate_key'))
				->query()
				->fetchAll();
			foreach ($keys as $tk) {
				$key = $tk['translate_key'];
				echo "Key: $key\n";
				$res2 = $this->db->select()
					->from('translate_missing', array('translate_key', 'lang_id', 'controller', 'action', 'status'))
					->where('translate_key=?', $key)
					->query();
				$missing = array();
				while ($row2 = $res2->fetch())
					$missing[$row2['lang_id']] = $row2;
				unset($res2);
				$res2 = $this->db->select()
					->from('preklady', array('lang_id', 'index_pole', 'text'))
					->where('index_pole=?', $key)
					->query();
				$translations = array();
				while ($row2 = $res2->fetch())
					$translations[$row2['lang_id']] = $row2;
				unset($res2);
				$key = static::escapePhpSingleQuoted($key);
				foreach ($languages as $lang) {
					$langId = $lang['lang_id'];
					if (array_key_exists($langId, $translations) && !empty($translations[$langId]['text'])) {
						$value = "'" . static::escapePhpSingleQuoted($translations[$langId]['text']) . "'";
					}
					else if (array_key_exists($langId, $missing)) {
						$status = $missing[$langId]['status'];
						if (self::STATUS_EMPTY == $status)
							$value = 'true';
						else if (self::STATUS_MISSING == $status)
							$value = 'false';
						else
							continue;
					}
					else
						continue;
					$key = strtolower($key);
					echo "\tTranslation: ($langId) '$key'=>$value,\n";
					fputs($fDict[$langId], "'$key'=>$value,\n");
				}
			}
		}
		foreach ($languages as $lang) {
			$langId = $lang['lang_id'];
			if (!empty($fDict[$langId]))
				fclose($fDict[$langId]);
		}
	}
	catch (Exception $e) {
		echo $e;
		return false;
	}
}

} // It6_Translate_Web
