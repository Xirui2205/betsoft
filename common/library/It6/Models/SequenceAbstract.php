<?php

/**
 * Workaround class for MySQL "feature" how InnoDb engine handles AUTO_INCREMENT.
 * (After server restart makes MySQL for InnoDb tables SELECT MAX(auto_col) query
 * and resets AUTO_INCREMENT value, that's bad if you delete often in table...)
 */
abstract class It6_Models_SequenceAbstract extends It6_Models_DbDependent {

// override static variables in derived classes
protected static $_table = null;
protected static $_pk = null;
	
/**
 * This method cleans up sequence table after getting next ID.
 * @returns integer Next free ID value
 */
public static function nextId(&$db = null) {
	static::assureDbParam($db);
	$db->insert(static::$_table, array(static::$_pk => null));
	$id = $db->lastInsertId();
	$db->delete(static::$_table);
	return $id;
}

} // class It6_Models_SequenceAbstract
