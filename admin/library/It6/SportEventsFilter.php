<?php

class It6_SportEventsFilter {

/**
 * @see Webservice_Event::getSportFilterEvents()
 * @param integer|NULL $sportId
 * @return array
 */
public static function getSportEvents($sportId = null) {
	return Zend_Registry::get('ws')->Event->getSportFilterEvents(empty($sportId) ? 0 : $sportId, 1);
}

/**
 * @param array $sports Data in format returned by Webservice_Event::getSportFilterEvents()
 * @param integer|NULL $sportId [optional] Sport ID to be selected, empty value for no selection
 * @param integer|array|NULL $eventId [optional] One event ID or array of ID(s) to be selected, empty value for no selection
 * @param boolean|string $addAllSportsOpt [optional] TRUE for default "All sports" option, string is explicit translation tag, empty value for no option
 * @param boolean|string $addAllEventsOpt [optional] TRUE for default "All events" option, string is explicit translation tag, empty value for no option
 * @param string $eventValueCol The name of the column the value of which should be passed into the "value" argument of the <option> tag
 * @return array array(htmlForSportOpts, htmlForEventOpts)
 */
public static function getSportFilterHtml($sports, $sportId=null, $eventId=null, $addAllSportsOpt=true, $addAllEventsOpt=true, $eventValueCol='eventId') {
	$htmlSports = '';
	$htmlEvents = '';
	$matchSport = false;
	$matchEvent = false;
	if (!is_array($eventId))
		$eventId = (empty($eventId) ? array() : array(intval($eventId)));
	foreach ($sports as $s) {
		if ($sportId == $s[0]) {
			$matchSport = true;
			$selSport = ' selected="selected"';
		}
		else
			$selSport = '';
		$htmlSports .= "<option value=\"{$s[0]}\"$selSport>" . htmlspecialchars($s[1]). '</option>';
		if (!empty($s[2]) && 0 < count($s[2])) {
			$htmlEvents .= '<optgroup class="event-filter sport" label="' . htmlspecialchars($s[1]) . '">';
			foreach ($s[2] as $r) {
				$htmlEvents .= '<optgroup class="event-filter region" label="' . htmlspecialchars($r[1]) . '">';
				foreach ($r[2] as $e) {
					if (in_array($e['eventId'], $eventId)) {
						$matchEvent = true;
						$selEvent = ' selected="selected"';
					}
					else
						$selEvent = '';
					$htmlEvents .= '<option value="'.$e[$eventValueCol].'"'.$selEvent.'>'.htmlspecialchars($e['name']).'</option>';
				}
				$htmlEvents .= '</optgroup>';
			}
			$htmlEvents .= '</optgroup>';
		}
	}
	
	if (!empty($addAllSportsOpt)) {
		$selSport = ($matchSport ? '' : ' selected="selected"');
		$htmlSports = '<option value="0"' . $selSport . '>' . I18n::tr('all_sports') . '</option>' . $htmlSports;
	}
	if (!empty($addAllEventsOpt)) {
		$selEvent = ($matchEvent ? '' : ' selected="selected"');
		$htmlEvents = '<option value="0"' . $selEvent . '>' . I18n::tr('all_events') . '</option>' . $htmlEvents;
	}
	return array($htmlSports, $htmlEvents);
}



/** 
 * name: loadEventsBySport
 * @param $sportId
 * @param $addAllEventsOpt
 * @param string $eventValueCol The name of the column the value of which should be passed into the "value" argument of the <option> tag
 */
public static function loadEventsBySport($sportId, $addAllEventsOpt=true, $eventValueCol=null) {
	if($eventValueCol == null)
		$eventValueCol = 'eventId';
	if($addAllEventsOpt == 'false')
		$addAllEventsOpt = false;

	$sports = Zend_Registry::get('ws')->Event->getSportFilterEvents($sportId, DEFAULT_LANG_ID, $eventValueCol);
	list($_, $html) = static::getSportFilterHtml($sports, $sportId, null, false, $addAllEventsOpt, $eventValueCol);
	return $html;
}

} // class
