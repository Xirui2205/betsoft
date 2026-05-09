<?php

class It6_Models_Sportsbook extends It6_Models_DbDependent {

const CACHE_TIMEOUT = 3600; // in seconds

// values for DB table column vic_main.seo_url.type
const URL_TYPE_SPORT = 1;
const URL_TYPE_REGION = 2;
const URL_TYPE_EVENT = 3;

/**
 * Retrives URI path part for givem sport or region or event. Cached at request level.
 * @param integer $id
 * @param integer $type Use one of URL_TYPE_* constants
 * @param Zend_Db_Adapter $db
 * @return URI path part (all slashes trimmed)
 */
public static function getAllLangsUrls($id, $type, &$db = null) {
	static $cache = array(); // type => eventId => langIso => url
	if (isset($cache[$type][$id]))
		return $cache[$type][$id];

	static::assureDbParam($db);
	$langs = It6_Models_Language::getAllDisplayed($db);
	$rows = $db->query(
			'SELECT u.lang_id, j.iso, u.url FROM seo_url u JOIN jazyky j ON u.lang_id=j.lang_id'
			. ' WHERE u.lang_id IN (' . implode(',', array_keys($langs)) . ') AND u.type=' . intval($type)
			. ' AND u.event_id=' . intval($id)
		)->fetchAll();
	$result = array();
	foreach ($rows as $row)
		$result[ $row['iso'] ] = trim($row['url'], '/');
	$cache[$type][$id] = $result;
	return $result;
}

/**
 * Given event ID returns full URL or path part only for event odds in sportsbook.
 * @param integer|array $eventId
 * @param boolean $pathOnly
 * @param Zend_Db_Adapter $db
 * @return string|array one or map of URLs (eventId => URL); URLs full with protocol and host or path parts only
 */
public static function getEventUrl($eventId, $pathOnly = false, &$db = null) {
	static::assureDbParam($db);
	$langId = $_SESSION['lang_id'];
	$rows = $db->query(
		'SELECT u.udalost_id,s.url,s.type FROM seo_url s JOIN udalost u'
		. ' ON (s.event_id=u.udalost_id AND s.type=' . self::URL_TYPE_EVENT . ')'
		. ' OR (s.event_id=u.oblast_id AND s.type=' . self::URL_TYPE_REGION . ')'
		. ' OR (s.event_id=u.sport_id AND s.type=' . self::URL_TYPE_SPORT . ')'
		. ' WHERE u.udalost_id IN (' . $db->quote($eventId) .') AND s.lang_id=' . intval($langId)
	)->fetchAll();
	$parts = array();
	foreach ($rows as $row)
		$parts[ $row['udalost_id'] ][ $row['type'] ] = trim($row['url'], '/');
	$urlStart = ($pathOnly ? '' : PROTOCOL . WEBHOST);
	$cc = It6_Models_ControllerConvert::getByIdAndLang(84, $langId, $db);
	$urls = array();
	foreach ($parts as $_eventId => $_parts) {
		$url = $urlStart . '/' . $_SESSION['lang'] . '/' . $cc['reqController'] . '/';
		if (!empty($_parts[self::URL_TYPE_SPORT])) {
			$url .= $_parts[self::URL_TYPE_SPORT] . '/';
			if (!empty($_parts[self::URL_TYPE_REGION])) {
				$url .= $_parts[self::URL_TYPE_REGION] . '/';
				if (!empty($_parts[self::URL_TYPE_EVENT]))
					$url .= $_parts[self::URL_TYPE_EVENT] . '/';
			}
		}
		$urls[ $_eventId ] = $url;
	}
	return (is_array($eventId) ? $urls : $urls[$eventId]);
}

/**
 * @param string $sport Sport URL part
 * @param string $region Region URL part
 * @param string $event Event URL part
 * @return string|NULL URL of next odds group or NULL if no more group is available
 */
public static function getNextOddsUrl($sport, $region, $event) {
	//TODO: use global cache
	
}

} // class