<?php

class It6_Gui_Event {

	public static function getMultiSelect($name = NULL, $size = 50) {
		$sql =
		'SELECT
			s.sport_id,
			s.nazev,
			u.nazev AS udalost_nazev,
			u.udalost_id,
			u.platne_do
		FROM sport s
		JOIN udalost u ON u.sport_id = s.sport_id
		WHERE u.platne_do >= "' . It6_Date::dbNow() . '"
		GROUP BY s.sport_id, u.udalost_id
		ORDER BY s.pozice, u.pozice';

		$db = Zend_Registry::get('db');
		$res = $db->query($sql);

		$sports = array();
		$events = array();
		while ($row = $res->fetch()) {
			$sportId = $row['sport_id'];
			$eventId = $row['udalost_id'];
			if (!array_key_exists($sportId, $sports))
				$sports[$sportId] = array('name' => $row['nazev'], 'events' => array($eventId));
			else
				$sports[$sportId]['events'][] = $eventId;
			$events[$eventId] = array(
				'sportId' => $sportId,
				'name' => $row['udalost_nazev'],
			);
		}

			// translate all resources at once
		$dictionary = array();
		foreach ($sports as &$sport)
			$dictionary[$sport['name']] = true;
		foreach ($events as &$event)
			$dictionary[$event['name']] = true;
		$dictionary = It6_Models_Translator::translate(array_keys($dictionary), CZ_LANG_ID, $db);
		foreach ($sports as &$sport)
			$sport['name'] = $dictionary[$sport['name']];
		foreach ($events as &$event)
			$event['name'] = $dictionary[$event['name']];

		unset($dictionary);

		$lastSportId = false;
		$eventOpts = '';
		$sportOpts = '';
		foreach ($sports as $sportId => &$sport) {
			//$sportOpts .= "<option value=".$sportId.">".Help::Html($sport['name'])."</option> ";
			foreach ($sport['events'] as $eventId) {
				$event = &$events[$eventId];
				if ($lastSportId != $sportId) {
					if (false !== $lastSportId)
						$eventOpts .= "</optgroup>";
					$lastSportId = $sportId;
					$eventOpts .= '<optgroup label="'.Help::Html($sport['name']).'" id="optgroup-'.$sportId.'">';
				}
				$eventOpts .= '<option  value="' . $eventId .'" '
					. (isset($_POST[$name]) && ($_POST[$name] == $eventId || in_array($eventId,$_POST[$name]) ) ? 'selected="selected"' : '') . '>'
					. Help::Html($event['name']) .'</option>';
			}
		}
		if (false !== $lastSportId)
			$eventOpts .= "</optgroup>";

		//$this->view->sportOpts = '<select size="10">'.$sportOpts.'</select>';
		//$this->view->eventOpts =

		return '<select id="'.$name.'" name="'.$name.'[]" multiple size="'.$size.'">'.$eventOpts.'</select>';
	}
}
