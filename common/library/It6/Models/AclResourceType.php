<?php

class It6_Models_AclResourceType extends It6_Models_AllInPersistentLocalCache {

const PERSISTENT_CACHE_KEY = 'A:ACL_RESOURCE_TYPE_ALL';
const PERSISTENT_CACHE_TTL = 0; // forever
	
protected static $_registryEntryDb = 'zdb_admin';
protected static $_cache = array();
protected static $_table = 'acl_resource_type';
protected static $_joinPrefix = false;
protected static $_columns = array(
	'id' => 'acl_resource_type_id',
	'name' => 'acl_resource_type_name',
);
protected static $_joinedColumns = array();
protected static $_joinConstraints = array();
protected static $_joinPrefixes = false;
protected static $_primaryKey = 'id';
protected static $_readDataModifiers = array();
protected static $_readDataAllModifiers = array();

public static function getIdByName($name, &$db = null) {
	if (empty(static::$_cache))
		static::readDataAll($db);
	foreach (static::$_cache as $id => $data) {
		if ($name == $data['name'])
			return $id;
	}
	return null;
}

} // class It6_Models_AclResourceType
