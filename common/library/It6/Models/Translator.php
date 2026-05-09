<?php

/**
 * Extends It6_Models_Abstract where ID is key for translation and NAME is langId
 */
class It6_Models_Translator extends It6_Models_Abstract {

const LANGID_ALL = -1;

protected static $_cache = array();
protected static $_langs = null;

protected static $_joinPrefix = false;
protected static $_columns = array(
	'id' => 'index_pole',
	'langId' => 'lang_id',
	'text' => 'text',
);
protected static $_joinedColumns = array();
protected static $_joinConstraints = array();
protected static $_joinPrefixes = false;
protected static $_primaryKey = 'id';
protected static $_readDataModifiers = array();
protected static $_readDataAllModifiers = array();

protected static function readLangs(&$db = null) {
	static::assureDbParam($db);
	$rows = $db->select()
		->from('jazyky', array('lang_id', 'iso'))
		->query()
		->fetchAll();
	$langs = array();
	foreach ($rows as $row) {
		$langs[$row['lang_id']] = array(
			'id' => $row['lang_id'],
			'iso' => $row['iso']
		);
	}
	return $langs;
}

protected static function assureLangs(&$db = null) {
	if (!isset(static::$_langs))
		static::$_langs = static::readLangs($db);
}

public static function readData($key, &$db = null) {
	static::assureDbParam($db);
	static::assureLangs($db);
	$more = is_array($key);
	if (!$more)
		$key = array($key);
	$keys = array();
	foreach ($key as $_key) {
		$lkey = strtolower($_key);
		if (array_key_exists($lkey, $keys))
			$keys[$lkey][] = $_key;
		else
			$keys[$lkey] = array($_key);
	}
	$langs = array();
	$texts = array();
	if (!empty($keys)) { 
		//$res = $db->select()
		//	->from('preklady', array('index_pole', 'lang_id', 'text'))
		//	->where('index_pole IN (?)', array_keys($keys))
		//	->query();
		$keysForSelect = array();
		if(is_array($keys)) {
			foreach(array_keys($keys) as $_key) {
				$keysForSelect[] = (string)$_key;
			}
		}

		$sqlKeys = $db->quote( $keysForSelect );
		$res = $db->query('SELECT `index_pole`,`lang_id`,`text` FROM `preklady` WHERE `index_pole` IN (' . $sqlKeys . ')');
		unset($sqlKeys);
		while ($row = $res->fetch()) {
			$lkey = strtolower($row['index_pole']);
			if (!empty($keys[$lkey])) {
				foreach ($keys[$lkey] as $_key) {
					if (!array_key_exists($_key, $texts))
						$texts[$_key] = array($row['lang_id'] => $row['text']);
					else
						$texts[$_key][$row['lang_id']] = $row['text'];
				}
			}
		}
	}
	// find not translated keys and add them untranslated
	foreach ($key as $_key) {
		if (!array_key_exists($_key, $texts)) {
			$keyTexts = array();
			foreach (static::$_langs as $langId => $lang)
				$keyTexts[$langId] = $_key;
			$texts[$_key] = $keyTexts;
		}
		else {
			foreach (static::$_langs as $langId => $lang) {
				if (!array_key_exists($langId, $texts[$_key]))
					$texts[$_key][$langId] = $_key;
			}
		}
	}
	if ($more)
		return $texts;
	else
		return $texts[$key[0]];
}

public static function getAllLangs(&$db = null) {
	static::assureLangs($db);
	return static::$_langs;
}

/**
 * Wrapper for get() making second parameter optional with current user language as default.
 * NOTE: it may be useful to add $lang special value for retrieving all languages at once (by calling getData() instead of get())
 * @param string|array $key
 * @param int $langId Language ID, use It6_Models_Translator::LANGID_ALL if all languages at once are to be queried
 * @returns array or array of arrays
 */
public static function translate($key, $langId = null, &$db = null) {
	static::assureDbParam($db);
	if (!isset($langId))
		$langId = 1; //TODO: get default value for lang -- application dependent: use Web convention and Admin must supply lang?
	else if (static::LANGID_ALL == $langId)
		return static::getData($key, $db);
	else
		return static::get($key, $langId, $db);
}

} // class It6_Models_Translator
