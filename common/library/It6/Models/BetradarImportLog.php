<?php

class It6_Models_BetradarImportLog extends It6_Models_DbDependent {

protected static $_registryEntryDb = 'admindb';

protected static $_table = 'betradar_import_log';
protected static $_columns = array(
	'id' => 'id',
	'sport' => 'sport',
	'region' => 'region',
	'event' => 'event',
	'brTournamentId' => 'br_tournament_id',
	'importedAt' => 'imported_at',
);
protected static $_primaryKey = 'id';

protected static $_importTimestamp = null;
protected static $_logs = null;

/**
 * Initializes new import
 * @param string|array $timestamp string formated as standard BR import XML filename
 *                     (eg. "xml_2011-09-06-16-09-32_0.xml")
 *                     or structure with keys: year, month, day, hour, minute, second
 *                     same as BR import, local time zone is assumed
 */
public static function init($timestamp) {
	if (!is_array($timestamp)) {
		if (!preg_match(
			'/(?:^|\\D)(\\d{4})-(\\d{2})-(\\d{2})-(\\d{2})-(\\d{2})-(\\d{2})(?:\\D|$)/',
			(string)$timestamp, $matches
		))
			$timestamp = false;
		else {
			$timestamp = array(
				'year' => $matches[1],
				'month' => $matches[2],
				'day' => $matches[3],
				'hour' => $matches[4],
				'minute' => $matches[5],
				'second' => $matches[6],
			);
		}
	}
	if (empty($timestamp))
		static::$_importTimestamp = time();
	else
		static::$_importTimestamp = mktime(
			$timestamp['hour'],
			$timestamp['minute'],
			$timestamp['second'],
			$timestamp['month'],
			$timestamp['day'],
			$timestamp['year']
		);
	static::$_logs = array();
}

/**
 * Registers imported record to be saved later using save()
 * @param string $sport
 * @param string $region
 * @param string $event
 * @param integer $brTournamentId
 */
public static function log($sport, $region, $event, $brTournamentId) {
	static::$_logs[$sport][$region][$event][intval($brTournamentId)] = true;
}

/**
 * Gets current content of log buffer
 * @return array (sportName => regionName => eventName => brEventId => TRUE)
 */
public static function getCurrentLog() {
	return static::$_logs;
}

/**
 * Save all logged records (through log() method) into database
 * @param boolean $useTrasanction
 * @param Zend_Db_Adapter $db
 * @return boolean|number Number of inserted records or FALSE on error
 */
public static function save($useTransaction = true, &$db = null) {
	$n = 0;
	if (!empty(static::$_logs)) {
		static::assureDbParam($db);
		if ($useTransaction)
			It6_DbTransaction::begin($db);
		try {
			$importedAt = It6_Date::timestampToDb(static::$_importTimestamp);
			foreach (static::$_logs as $sport => $_sport) {
				foreach ($_sport as $region => $_region) {
					foreach ($_region as $event => $_event) {
						foreach ($_event as $brTournamentId => $_) {
							$log = array(
								'sport' => $sport,
								'region' => $region,
								'event' => $event,
								'br_tournament_id' => $brTournamentId,
								'imported_at' => $importedAt,
							);
							if ($db->insert(static::$_table, $log))
								++$n;
						}
					}
				}
			}
			if ($useTransaction)
				It6_DbTransaction::commit($db);
		}
		catch (Exception $e) {
			if ($useTransaction)
				It6_DbTransaction::rollback($db);
			return false;
		}
	}
	return $n;
}

/**
 * @param integer $maxAge Max age of log record to be returned (in seconds)
 * @param Zend_Db_Adapter $db
 * @return array log record structures
 */
public static function getLatest($maxAge = null, &$db = null) {
	if (empty($maxAge)) {
		$maxAge = It6_Models_Parameter::getDataByName(static::PARAM_DEFAULT_AGE);
		if (empty($maxAge))
			$maxAge = 60 * 60 * 24 * 3; // three days
	}
	static::assureDbParam($db);
	$logs = $db->select()
		->from(static::$_table, static::$_columns)
		->where(static::$_columns['importedAt'] . '>=?', It6_Date::dbNow(-$maxAge))
		->query()
		->fetchAll();
	return $logs;
}

} // class
