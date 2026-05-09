<?php

class It6_Models_AclTree extends It6_Models_AllInPersistentLocalCache {

const PERSISTENT_CACHE_KEY = 'A:ACL_TREE_ALL';
const PERSISTENT_CACHE_TTL = 0; // forever

protected static $_registryEntryDb = 'zdb_admin';
protected static $_cache = array();
protected static $_table = 'acl_tree';
protected static $_joinPrefix = false;
protected static $_columns = array(
	'id' => 'acl_tree_id',
	'name' => 'acl_tree_name',
);
protected static $_joinedColumns = array();
protected static $_joinConstraints = array();
protected static $_primaryKey = 'id';
protected static $_readDataModifiers = array();
protected static $_readDataAllModifiers = array();

/**
 * This function requires readDataAll() to be called before.
 * @param string|array $name one or more names
 * @param Zend_Db_Adapter $db [optional]
 * @returns if $name was string then id or null for unknown name,
 *          if $name was array then array(name_1 => id_1, ...) where unknown names won't be included
 */
public static function getIdByName($name, &$db = null) {
	$more = is_array($name);
	if (!$more)
		$name = array($name);
	$ids = array();
	foreach (static::$_cache as $id => $data) {
		$_name = $data['name'];
		if (in_array($_name, $name)) {
			$ids[$_name] = $id;
			if (count($name) == count($ids))
				break;
		}
	}
	if ($more)
		return $ids;
	else {
		$name = $name[0];
		return (array_key_exists($name, $ids) ? $ids[$name] : null);
	}
}

} // class It6_Models_AclTree
