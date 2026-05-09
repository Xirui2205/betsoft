<?php

require_once ROOT . 'common/library/Zend/Registry.php';

abstract class It6_Models_DbDependent {

protected static $_registryEntryDb = 'db';
protected static $_cache = array();

protected static function assureDbParam(&$db = null) {
	if (!isset($db))
		$db = Zend_Registry::get(static::$_registryEntryDb);
}

} // class It6_Models_DbDependent