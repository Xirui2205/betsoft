<?php

/**
 * Happy hour related static methods.
 * @author Pavel Klinger
 * @see Entities_HappyHour
 *
 */
class Webservice_HappyHour extends Webservice_AbstractWebService  {

public static $TABLE					= "happy_hour";
public static $TABLE_PREFIX				= "hh";
public static $ENTITY_NAME				= "Entities_HappyHour";
public static $IDENTITY					= "id";


protected static $CONV = array(
	'id'               => 'happyHourId',
	'type'             => 'type',
	'time_from'        => 'timeFrom',
	'time_to'          => 'timeTo',
	'amount'           => 'amount',
	'users_count'      => 'usersCount',
	'users_init_count' => 'usersInitCount'
);
	

/**
 * Generates happy hour randomly in the given time interval
 * @param string $type type of the happy hour one of the ('visit')
 * @param string $timeFrom
 * @param string $timeTo
 * @param integer $duration Duration of the happy hour in seconds (null means end the day)
 * @param float $amount 
 * @param integer $usersCount
 */
public static function generate($type, $timeFrom, $timeTo, $duration, $amount, $usersCount) {
	$db = static::getDb();
	 
	$timestamp = rand(
		It6_Date::dbDatetimeToTimestamp($timeFrom),
		It6_Date::dbDatetimeToTimestamp($timeTo)
	);
	
	$tF = It6_Date::timestampToDb($timestamp);
	if ( !empty($duration) ) {
		$tT = It6_Date::timestampToDb($timestamp + $duration);
	} 
	else
		$tT = null;
	
	$db->insert(
		static::$TABLE,
		array(
			'type' => $type,
			'time_from' => $tF,
			'time_to' => $tT,
			'amount' => $amount,
			'users_count' => 0,
			'users_init_count' => $usersCount
		)
	);
}

/**
 * Is happy hour with given type running at this time?
 * @param string $type type of the happy hour one of the ('visit')
 * @return boolean|struct false if not running otherwise
 */
public static function getRunning($type, $extensions = null) {
	$where = static::_getHappyHourWhereQuery($type);
	$ret = static::getOneWhere($where, $extensions);
	return empty($ret) ? false : $ret;
}

/** 
 * Increases user happy hour counter
 * @param string $type type of the happy hour one of the ('visit')
 */
public static function useHappyHour($type) {
	$db = static::getDb();
	$where = static::_getHappyHourWhereQuery($type);
	$db->update(
		static::$TABLE,
		array('users_count' => new Zend_Db_Expr('users_count + 1')),
		$where
	);
	
	It6_GlobalCache_Invalidator::HappyHour_userHappyHour($type);
}

protected static function _getHappyHourWhereQuery($type) {
	$now = It6_Date::dbNow();
	list($from, $_) = It6_Date::todayToDbInterval();
	return array(
		'type = ?' => $type,
		'time_from > ?' => $from,
		'time_from < ?' => $now,
		'time_to IS NULL OR time_to > ?' => $now,
		'users_count < users_init_count',
	); 
}

} // Webservice_HappyHoour
