<?php

class It6_Models_Language extends It6_Models_Abstract {

protected static $_cache = array();
protected static $_table = 'jazyky';
protected static $_columns = array(
	'id' => 'lang_id',
	'isoCode' => 'iso',
	'altText' => 'alt_text',
	'regionIdForFlag' => 'flag_oblast_id',
	'currencyId' => 'mena_id',
	'collation' => 'collation',
);
protected static $_primaryKey = 'id';
protected static $_joinedColumns = array();
protected static $_joinConstraints = array();

public static function getDataByIso($iso, &$db = null) {
	foreach (static::$_cache as $lang) {
		if ($lang['isoCode'] == $iso)
			return $lang;
	}
	static::assureDbParam($db);
	$rows = static::createSelect()
		->where(static::objectNameToFullColumn('isoCode') . '=?', $iso)
		->query()
		->fetchAll();
	if (empty($rows))
		return null;
	$lang = $rows[0];
	static::$_cache[$lang['id']] = $lang;
	return $lang;
}

/**
 * Returns all displayed languages IDs and ISO codes. Cached at request level.
 * @param Zend_Db_Adapter $db
 * @return array (ID => ISO)
 */
public static function getAllDisplayed(&$db = null) {
	static $cache = false;
	if (false === $cache) {
		static::assureDbParam($db);
		$rows = $db->query('SELECT lang_id AS id, iso FROM jazyky WHERE zobrazeno=1')->fetchAll();
		$cache = array();
		foreach ($rows as $row)
			$cache[$row['id']] = $row['iso'];
	}
	return $cache;
}

} // class It6_Models_Language