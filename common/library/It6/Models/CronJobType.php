<?php

class It6_Models_CronJobType extends It6_Models_Abstract { 

const TYPENAME_EMAIL = 'Email';
const TYPENAME_SMS = 'Sms';
const TYPENAME_ALERT = 'Alert';
const TYPENAME_RELEASEALIASES = 'ReleaseAliases';

protected static $_cache = array();

protected static $_table = 'cronjob_type';
protected static $_columns = array(
	'id' => 'type_id',
	'name' => 'type_name'
);
protected static $_primaryKey = 'id';
protected static $_joinedColumns = array();
protected static $_joinConstraints = array();

protected static $_joinPrefix = false;
protected static $_joinPrefixes = false;
protected static $_readDataModifiers = array(); // see modifySelect()
protected static $_readDataAllModifiers = array(); // see modifySelectAll()

public static function getTypeIdByName($typeName, &$db = null) {
	foreach (static::$_cache as $id => $data) {
		if ($data['name'] == $typeName)
			return $id;
	}
	$select = static::createSelect($db);
	$select->where(static::objectNameToFullColumn('name') . '=?', $typeName);
	$data = $select->query()->fetch();
	if (empty($data))
		return false;
	$cache[$data[static::$_primaryKey]] = $data;
	return $data['id'];
}

} // class It6_Models_CronJobType
