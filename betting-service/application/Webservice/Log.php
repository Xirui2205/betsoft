<?php

/**
 * Branch type related static methods.
 * @author Martin Bohal
 * @see Entities_Country
 */
class Webservice_Log   {

	const TABLE = 'log';
	
	protected static function getDb() {
		return Zend_Registry::get('logdb');
	}
	
	/**
	 * Returns log records matching given criteria and order by given columns
	 * @return struct structure of the possible types of branch
	 */
	public static function getAllWhereOrder($findQuery,$orderQuery,$limit,$skip) {
		$db = static::getDb();
		return $db->log->find($findQuery)->sort($orderQuery)->limit($limit)->skip($skip);
	}
	
	/**
	 * Returns count of records matching given criteria
	 * @return integer structure of the possible types of branch
	 */
	public static function getAllWhereCount($findQuery) {
		$db = static::getDb();
		return $db->log->find($findQuery)->count();
	}

}
