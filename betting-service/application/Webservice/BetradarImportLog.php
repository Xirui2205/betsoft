<?php

class Webservice_BetradarImportLog extends Webservice_AbstractWebService {

const PARAM_DEFAULT_AGE = 'betradar.ImportLog.defaultMaxAge'; // 3 days in seconds

public static $TABLE = 'betradar_import_log';
public static $TABLE_PREFIX = 'bil';
public static $ENTITY_NAME = 'Entities_BetradarImportLog';
public static $IDENTITY = "id";

public static $CONV = array(
	'id' => 'brImportLogId',
	'sport' => 'sport',
	'region' => 'region',
	'event' => 'event',
	'br_tournament_id' => 'brTournamentId',
	'(MAX(imported_at))' => 'importedAt',
);

public static function getDb() {
	return static::getAdminDb();
}

protected static function defaultQuery($query) {
	return parent::defaultQuery($query)
		->group('br_tournament_id');
}

public static function getMaxAge() {
	$maxAge = intval( Webservice_Parameter::getGlobalParameter(static::PARAM_DEFAULT_AGE) );
	if (empty($maxAge))
		$maxAge = 60 * 60 * 24 * 3; // three days
	return $maxAge;
}

/**
 * @param integer $maxAge Max age of log record to be returned (in seconds)
 * @return array log record structures
 */
public static function getLatest($maxAge = null, $extensions = null) {
	if (empty($maxAge))
		$maxAge = static::getMaxAge();
	//return parent::getAllWhereOrder(
	//	array('imported_at >= ?' => It6_Date::dbNow(-$maxAge)),
	//	array('imported_at DESC', 'id DESC'),
	//	$extensions
	//);
	return parent::getAllWhere(
		array('imported_at >= ?' => It6_Date::dbNow(-$maxAge)),
		$extensions
	);
}

/**
 * Clean old records in database
 * @return integer Error code, 0 = OK
 */
public static function cleanUp() {
	try {
		$maxAge = static::getMaxAge();
		static::getDb()->delete(static::$TABLE, array('imported_at < ?' => It6_Date::dbNow(-2 * $maxAge)));
		return 0;
	}
	catch (Exception $e) {
		It6_Log::err('Betradar import log cleanup failed: ' . $e->getMessage());
		return -1;
	}
}

} // class
