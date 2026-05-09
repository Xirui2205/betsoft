<?php
class Webservice_Alert {

	const ALERT_NAMESPACE = 'It6_Alert_';

	/**
	 * @param string $name
	 * @param struct $params
	 * @param integer $branchId
	 * @param integer $userId
	 * @return boolean
	 */
	public static function assert($name, $params, $userId = null, $branchId = null) {
		try {
			$fullName = static::getAlertFullName($name);
			
			if ( !Zend_Loader::isReadable(
					str_replace('_', DIRECTORY_SEPARATOR, $fullName) . '.php' ) )
	
				throw new It6_XmlRpc_Exception("Unknown alert type: '$name'");
	
			return call_user_func(
				array($fullName, 'assert'),
				$params, $userId, $branchId);
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception('assert', 0, $e);
		}
	}

	/**	 
	 * @param string $name
	 * @param struct $params
	 * @param integer $branchId
	 * @param integer $userId
	 * @return boolean
	 */
	public static function check($name, $params, $userId = null, $branchId = null) {
		try {
			$fullName = static::getAlertFullName($name);
			
			if ( !Zend_Loader::isReadable(
					str_replace('_', DIRECTORY_SEPARATOR, $fullName) . '.php' ) )
	
				throw new It6_XmlRpc_Exception("Unknown alert type: '$name'");
	
			return call_user_func(
				array($fullName, 'check'),
				$params, $userId, $branchId);
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception('check', 0, $e);
		}
	}

	protected static function getAlertFullName($name) {
		return static::ALERT_NAMESPACE . $name;
	}
	
}