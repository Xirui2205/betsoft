<?php

/**
* Dont'd use setlocale()!!! Use date_default_timezone_set
* All dates/times are converted from current time zone to GMT before passing to DB.
* All dates/times are converted from GMT to current timezone.
* @author Pavel Klinger
*
*/
class It6_Date {

const DB_DATETIME_FORMAT = "%F %T";
const DB_DATE_FORMAT = "%F";
const DB_TIME_FORMAT = "%T";
const DATETIME_FORMAT = "%x %X";
const DATE_FORMAT = "%x";
const TIME_FORMAT = "%X";
const BETRADAR_FORMAT = "%Y-%m-%dT%H:%M:%S";

/**
 * Default time format eg. "12:01:59"
 */
const PART_TIME = 'T';

/**
* Time format without seconds eg. "12:01"
*/
const PART_TIME_SHORT = 't';

/**
 * Default date format eg. "15.3.2001"
 */
const PART_DATE = 'D'; 

/**
 * Default datetime format eg. "15.3.2001 12:01:59"
 */
const PART_DATETIME = 'I';

/**
 * Default short datetime format eg. "15.3 12:01"
 */
const PART_DATETIME_SHORT = 'd';

const DICTIONARY_NAME = 'date';
// MySQL is piece of shit that cannot handle time zone offset
// so we have to use predefined DB server timezone and convert values ourselves...
// Zend has support for ISO 8601 (yyyy-MM-dd HH:mm:sszzzz) format but not for formats for DB DATE and TIME column types.
// Zend also lacks in strict format strings handling, so we rather use for DB strings gmstrftime and strptime functions
// that don't try to fix anything when parsing.
// ISO formats for DB (could be used with Zend):
//const DB_TIMEZONE = 'GMT';
//const DB_DATETIME_FORMAT = 'yyyy-MM-dd HH:mm:ss';
//const DB_DATE_FORMAT = 'yyyy-MM-dd';
//const DB_TIME_FORMAT = 'HH:mm:ss';

/**
 * This function returns default format string that can be used
 * with date() function. This is partial solution
 * with performance in mind, only format without locale specific
 * strings (eg. month names) are supported, locale specific
 * separators and grouping are considered enough.
 * @param string $part Use It6_Date::PART_* constants
 * @param string|Zend_Locale $locale Locale for format string,
 *                           if NULL speficified Zend_Registry
 *                           entry 'Zend_Locale' is tried,
 *                           then DEFAULT_LOCALE constant is tried,
 *                           finally fallbacks to 'en'.
 * @return string|boolean Format string or FALSE on error
 */
public static function getPhpFormat($part, $locale = null) {
	static $formats = array(
		'en' => array(
				self::PART_TIME => 'G:i:s',
				self::PART_TIME_SHORT => 'G:i',
				self::PART_DATE => 'Y/j/n',
				self::PART_DATETIME => 'Y/j/n G:i:s',
				self::PART_DATETIME_SHORT => 'j/n G:i',
			),
		'cs' => array(
				self::PART_TIME => 'G:i:s',
				self::PART_TIME_SHORT => 'G:i',
				self::PART_DATE => 'j.n.Y',
				self::PART_DATETIME => 'j.n.Y G:i:s',
				self::PART_DATETIME_SHORT => 'j.n. G:i',
			),
		'sk' => array(
				self::PART_TIME => 'G:i:s',
				self::PART_TIME_SHORT => 'G:i',
				self::PART_DATE => 'j.n.Y',
				self::PART_DATETIME => 'j.n.Y G:i:s',
				self::PART_DATETIME_SHORT => 'j.n. G:i',
			),
	);
	if (!isset($locale)) {
		if (Zend_Registry::isRegistered('Zend_Locale')) {
			$locale = Zend_Registry::get('Zend_Locale');
		}
		else if (defined('DEFAULT_LOCALE')) {
			$locale = DEFAULT_LOCALE;
		}
		else {
			$locale = 'en';
		}
	}
	if ($locale instanceof Zend_Locale) {
		$locale = $locale->getLanguage();
	}
	else {
		if (preg_match('/^([a-z]+)[^a-z]/i', (string)$locale, $matches)) {
			$locale = $matches[1];
		}
	}
	$locale = strtolower(trim($locale));
	if (empty($formats[$locale])) {
		$locale = 'en';
	}
	if (empty($formats[$locale][$part])) {
		return false;
	}
	else {
		return $formats[$locale][$part];
	}
}

/**
 * name: dbNow
 * @param $offset integer offset in secounds
 * @return formated date
 */
public static function dbNow($offset=null) {
	if($offset === null)
		$tmpStamp = time();
	else
		$tmpStamp = time() + $offset;

	return gmstrftime(self::DB_DATETIME_FORMAT, $tmpStamp);
}

public static function dbNowAsDate() {
	return gmstrftime(self::DB_DATE_FORMAT);
}

public static function dbNowAsTime() {
	return gmstrftime(self::DB_TIME_FORMAT);
}

public static function now() {
	return date(self::getPhpFormat(self::PART_DATETIME));
}

public static function nowAsDate() {
	return date(self::getPhpFormat(self::PART_DATE));
}

public static function nowAsTime() {
	return date(self::getPhpFormat(self::PART_TIME));
}

public static function nowAsTimestamp() {
	return time();
}

public static function checkFormat($datetime) {
	//return false !== strptime($datetime, self::DATETIME_FORMAT);
	return Zend_Date::isDate( $datetime, Zend_Locale_Format::getDateTimeFormat(Zend_Registry::get('Zend_Locale')) );
}

public static function checkDateFormat($datetime) {
	//return false !== strptime($datetime, self::DATE_FORMAT);
	return Zend_Date::isDate( $datetime, Zend_Locale_Format::getDateFormat(Zend_Registry::get('Zend_Locale')) );
}

public static function checkTimeFormat($datetime) {
	//return false !== strptime($datetime, self::TIME_FORMAT);
	return Zend_Date::isDate( $datetime, Zend_Locale_Format::getTimeFormat(Zend_Registry::get('Zend_Locale')) );
}

public static function checkFormatDB($datetime) {
	return false !== strptime($datetime, self::DB_DATETIME_FORMAT);
	//return Zend_Locale_Format::checkDateFormat($datetime, array('date_format' => self::DB_DATETIME_FORMAT, 'fix_date' => false));
}

public static function checkDateFormatDB($datetime) {
	return false !== strptime($datetime, self::DB_DATE_FORMAT);
	//return Zend_Locale_Format::checkDateFormat($datetime, array('date_format' => self::DB_DATE_FORMAT, 'format' => self::DB_DATE_FORMAT, 'fix_date' => false));
}

public static function checkTimeFormatDB($datetime) {
	return false !== strptime($datetime, self::DB_TIME_FORMAT);
	//return Zend_Locale_Format::checkDateFormat($datetime, array('date_format' => self::DB_TIME_FORMAT, 'format' => self::DB_TIME_FORMAT, 'fix_date' => false));
}

public static function fromDb($dbDatetime) {
	if ( empty($dbDatetime) )
		return null;

	return self::timestampToDateTime(self::fromDbAsTimestamp($dbDatetime));
}

public static function fromDbShort($dbDatetime) {
	if ( empty($dbDatetime) )
		return null;

	return self::timestampToDateTimeShort(self::fromDbAsTimestamp($dbDatetime));
}

public static function fromDbAsDate($dbDatetime) {
	if ( empty($dbDatetime) )
		return null;

	return self::timestampToDate(self::fromDbAsTimestamp($dbDatetime));
}

public static function fromDbAsTime($dbDatetime, $format = self::PART_TIME) {
	if ( empty($dbDatetime) )
		return null;

	return self::timestampToTime(self::fromDbAsTimestamp($dbDatetime), $format);
}

public static function fromDbAsTimestamp($dbDatetime) {
	if ( empty($dbDatetime) )
		return null;

	//NOTE: strptime is tolerant when not all the input conforms (eg. '2000-12-11 something' is parsed as date successfully)
	try { $array = strptime($dbDatetime, self::DB_DATETIME_FORMAT); }
	catch (Exception $e) { $array = false; }
	if ( false === $array ) {
		try { $array = strptime($dbDatetime, self::DB_DATE_FORMAT); }
		catch (Exception $e) { $array = false; }
		if ( false === $array ) {
			try { $array = strptime($dbDatetime, self::DB_TIME_FORMAT); }
			catch (Exception $e) { $array = false; }
		}
	}
	if (false === $array)
		return 0;
	else
		return gmmktime(
			$array['tm_hour'], $array['tm_min'], $array['tm_sec'],
			$array['tm_mon'] + 1, $array['tm_mday'], $array['tm_year'] + 1900);
}

/**
 * cas chodi z Betradaru v lokalnim DB formatu, musime prevest do UTC
 * @param boolean $asTimestamp If TRUE then UNIX timestamp is returned, formated DB datetime string otherwise
 */
public static function fromBetradarToDb($betradarDatetime, $asTimestamp = false) {
	try {
		$array = strptime($betradarDatetime, self::BETRADAR_FORMAT);
	}
	catch (Exception $e) {
		$array = false;
		return null;
	}

	if (false === $array)
		return null;
	else {
		$timestamp = mktime(
			$array['tm_hour'], $array['tm_min'], $array['tm_sec'],
			$array['tm_mon'] + 1, $array['tm_mday'], $array['tm_year'] + 1900);
		return ($asTimestamp ? $timestamp : self::timestampToDb($timestamp));
	}
}

public static function timestampToDb($timestamp) {
	if ( empty($timestamp) )
		return null;

	return gmstrftime(self::DB_DATETIME_FORMAT, $timestamp);
}

public static function timestampToDateTime($timestamp) {
	if ( empty($timestamp) )
		return null;

	return date(self::getPhpFormat(self::PART_DATETIME), $timestamp);
}

public static function timestampToDateTimeShort($timestamp) {
	if ( empty($timestamp) )
		return null;

	return date(self::getPhpFormat(self::PART_DATETIME_SHORT), $timestamp);
}

public static function timestampToDate($timestamp) {
	if ( empty($timestamp) )
		return null;

	return date(self::getPhpFormat(self::PART_DATE), $timestamp);
}

public static function timestampToTime($timestamp, $format = self::PART_TIME) {
	if ( empty($timestamp) )
		return null;

	return date(self::getPhpFormat($format), $timestamp);
}

public static function toDbAsTime($time) {
	if ( empty($time) )
		return null;

	return gmstrftime(
		self::DB_TIME_FORMAT,
		self::toTimestamp($time));
}

public static function toDbAsDate($datetime) {
	if ( empty($datetime) )
		return null;

	return strftime(
		self::DB_DATE_FORMAT,
		self::toTimestamp($datetime));
}

public static function toDb($datetime) {
	if ( empty($datetime) )
		return null;

	return gmstrftime(
		self::DB_DATETIME_FORMAT,
		self::toTimestamp($datetime));
}

/**
 * Convert web datetime (formated or timestamp) to time struct
 * @param string|integer $datetime Web formated datetime/date/time string or UNIX timestamp
 * @return FALSE|array Time struct (see PHP manual of localtime() for time struct description)
 */
public static function toTimestruct($datetime) {
	if (is_int($datetime))
		return localtime($datetime, true);
	else {
		//NOTE: strptime is tolerant when not all the input conforms (eg. '11.12.2000 something' is parsed as date successfully)
		//TODO: handle format dynamically from locale
		$tm = strptime($datetime, '%d.%m.%Y %H:%M:%S');
		if ( false === $tm ) {
			$tm = strptime($datetime, '%d.%m.%Y');
			if ( false === $tm ) {
				$tm = strptime($datetime, '%H:%M:%S');
			}
		}
		return $tm;
	}
}

/**
 * Convert web datetime to UNIX timestamp
 * @param string|array $datetime Web formated datetime/date/time string or time struct (see PHP manual of localtime() for time struct description)
 * @return NULL|integer
 */
public static function toTimestamp($datetime) {
	if ( empty($datetime) )
		return null;
	if (is_array($datetime))
		$tm = $datetime;
	else
		$tm = self::toTimestruct($datetime);
	if ( false === $tm )
		return null;
	return mktime(
		$tm['tm_hour'], $tm['tm_min'], $tm['tm_sec'], $tm['tm_mon'] + 1, $tm['tm_mday'], $tm['tm_year'] + 1900
	);
/* Zend_Date is totally incompetent when handling timezones, daylight saving etc.
	if (self::checkFormat($datetime))
		$d = new Zend_Date($datetime, Zend_Date::DATETIME);
	else if (self::checkDateFormat($datetime))
		$d = new Zend_Date($datetime, Zend_Date::DATES);
	else if (self::checkTimeFormat($datetime))
		$d = new Zend_Date($datetime, Zend_Date::TIMES);
	else
		return null;
	return $d->getTimestamp();
*/
}

/**
 * Convert DB datetime to time struct
 * @param string|integer $datetime
 * @return FALSE|array
 */
public static function dbDatetimeToTimestruct($datetime) {
	if ( empty($datetime) )
		return null;
	if (is_int($datetime)) {
		// why there is not equivalent of localtime() for GMT?
		$tm = explode(';', gmstrftime('%S;%M;%H;%d;%m;%Y;%w;%j', $datetime));
		return array(
			'tm_sec' => ltrim($tm[0], '0'),
			'tm_min' => ltrim($tm[1], '0'),
			'tm_hour' => ltrim($tm[2], '0'),
			'tm_mday' => ltrim($tm[3], '0'),
			'tm_mon' => ltrim($tm[4], '0') - 1,
			'tm_year' => $tm[5] - 1900,
			'tm_wday' => $tm[6],
			'tm_yday' => ltrim($tm[7], '0') - 1,
		);
	}
	else {
		$tm = strptime($datetime, '%Y-%m-%d %H:%M:%S');
		if (false === $tm) {
			$tm = strptime($datetime, '%Y-%m-%d');
			if (false === $tm) {
				$tm = strptime($datetime, '%H:%M:%S');
			}
		}
		return $tm;
	}
}

/**
 * Convert DB datetime to UNIX timestamp
 * @param string|array $datetime DB formated string or time struct
 * @return NULL|integer
 */
public static function dbDatetimeToTimestamp($datetime) {
	if ( empty($datetime) )
		return null;
	if (is_array($datetime))
		$tm = $datetime;
	else
		$tm = self::dbDatetimeToTimestruct($datetime);
	if ( false === $tm )
		return null;
	return gmmktime(
		$tm['tm_hour'], $tm['tm_min'], $tm['tm_sec'], $tm['tm_mon'] + 1, $tm['tm_mday'], $tm['tm_year'] + 1900
	);
}

public static function isNullDbDatetime($datetime) {
	return (empty($datetime) || '0000-00-00 00:00:00' == $datetime);
}

public static function isNullDbDate($date) {
	return (empty($date) || '0000-00-00' == $date);
}

public static function getListOfMonth() {
	$tr = Zend_Registry::get('translate');
	$dict = self::DICTIONARY_NAME;
	$fn = function ($key) use ($tr, $dict) { return $tr->trans($key, $dict, It6_Translate_Web::DICTIONARY); };
	return array(
		1 => $fn('January'),
		2 => $fn('February'),
		3 => $fn('March'),
		4 => $fn('April'),
		5 => $fn('May'),
		6 => $fn('June'),
		7 => $fn('July'),
		8 => $fn('August'),
		9 => $fn('September'),
		10 => $fn('October'),
		11 => $fn('November'),
		12 => $fn('December'),
	);
}

/**
 * Returns database time interval from given offsets relative to today (just today if no offsets specified)
 * @param integer $startOffset Move start of interval by given days after today (use negative number for days before)
 * @param integer $endOffset Move end of interval by given days after today (use negative number for days before)
 * @param boolean $asTimestamp Set to TRUE if UNIX timestamp should be returned, formated DB string is returned otherwise (FALSE is default)
 * @return array (start_time, end_time) returns database times (string datetime or integer timestamp) for interval of days
 */
public static function todayToDbInterval($startOffset = 0, $endOffset = 0, $asTimestamp = false) {
	$tm = localtime(time(), true);
	$start = mktime(0, 0, 0, $tm['tm_mon'] + 1, $tm['tm_mday'] + $startOffset, $tm['tm_year'] + 1900);
	if (!$asTimestamp)
		$start = self::timestampToDb($start);
	$end = mktime(23, 59, 59, $tm['tm_mon'] + 1, $tm['tm_mday'] + $endOffset, $tm['tm_year'] + 1900);
	if (!$asTimestamp)
		$end = self::timestampToDb($end);
	return array($start, $end);
}

/**
 * Returns database time interval from given date(s)
 * @param string $startDate Start of interval (local date)
 * @param string $endDate End of interval (local date), same as start if not specified
 * @param boolean $asTimestamp Set to TRUE if UNIX timestamp should be returned, formated DB string is returned otherwise (FALSE is default)
 * @return array (start_time, end_time) returns database times for interval of days
 */
public static function dateToDbInterval($startDate, $endDate = null, $asTimestamp = false) {
	$s = self::toTimestamp($startDate);
	$tm = localtime($s, true);
	$start = mktime(0, 0, 0, $tm['tm_mon'] + 1, $tm['tm_mday'], $tm['tm_year'] + 1900);
	if (!$asTimestamp)
		$start = self::timestampToDb($start);
	$e = (isset($endDate) ? self::toTimestamp($endDate) : $s);
	$tm = localtime($e, true);
	$end = mktime(23, 59, 59, $tm['tm_mon'] + 1, $tm['tm_mday'], $tm['tm_year'] + 1900);
	if (!$asTimestamp)
		$end = self::timestampToDb($end);
	return array($start, $end);
}

/**
 * Converts UNIX timestamp to date for import into Helios
 * @param integer $timestatmp UNIX timestamp
 * @return string Helios formated date
 */
public static function timestampToHeliosDate($timestamp) {
	return strftime('%Y%m%d', $timestamp);
}

} // class It6_Date
