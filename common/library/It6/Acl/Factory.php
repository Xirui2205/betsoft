<?php

abstract class It6_Acl_Factory {

protected static $_factoryClass = null;

/**
 * Default implementation initializes factory class from constant ACL_FACTORY_CLASS.
 * Factory class should extend class It6_Acl_Factory.
 */
protected static function _initFactory() {
	if (!isset(static::$_factoryClass)) {
		if (defined('ACL_FACTORY_CLASS'))
			static::$_factoryClass = ACL_FACTORY_CLASS;
		else
			throw new Exception('ACL factory class not defined');
	}
}

/**
 * Must be overriden in derived class.
 * @param mixed $params [optional] Parameter(s) for ACL creation
 * @returns It6_Acl new instance
 */
public static function createNewAcl($params = null) {
	throw new Exception('This method must be overriden in derived class');
}

/**
 * Creates new ACL instance.
 * This default implementation delegates call to instance of factory created in _initFactory().
 * @returns It6_Acl child class instance
 */
public static function newAcl($params = null) {
	static::_initFactory();
	$class = static::$_factoryClass;
	return $class::createNewAcl($params);
}

} // class It6_Acl_Factory
