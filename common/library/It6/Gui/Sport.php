<?php

class It6_Gui_Sport {

	public static function getMultiSelect($size = 50) {
		$sql =
		'SELECT
			s.sport_id,
			ag.name AS agsport,
			s.nazev
		FROM sport s
		LEFT JOIN approval_group ag ON s.approval_group_id = ag.id
		ORDER BY s.pozice';

		//ON b.udalost_id=u.udalost_id AND b.platna_od<='$now' AND b.platna_do>='$now'
		$db = Zend_Registry::get('db');
		$res = $db->query($sql);

		$sports = array();
		while ($row = $res->fetch()) {
			$sportId = $row['sport_id'];
			if (!array_key_exists($sportId, $sports))
				$sports[$sportId] = array('name' => $row['nazev'], 'ag' => $row['agsport']);
		}

			// translate all resources at once
		$dictionary = array();
		foreach ($sports as &$sport)
			$dictionary[$sport['name']] = true;
		$dictionary = It6_Models_Translator::translate(array_keys($dictionary), CZ_LANG_ID, $db);
		foreach ($sports as &$sport)
			$sport['name'] = $dictionary[$sport['name']];

		unset($dictionary);

		$lastSportId = false;
		$sportOpts = '';
		foreach ($sports as $sportId => &$sport) {
			$sportOpts .= "<option value=".$sportId.">".Help::Html($sport['name'])."</option> ";

		}

		//$this->view->sportOpts = '<select size="10">'.$sportOpts.'</select>';
		//$this->view->eventOpts =

		return '<select name="sports[]" multiple size="'.$size.'">'.$sportOpts.'</select>';
	}

	public static function getSelect($name = NULL, $size = 50, $extra='') {
		$sql =
		'SELECT
			s.sport_id,
			ag.name AS agsport,
			s.nazev
		FROM sport s
		LEFT JOIN approval_group ag ON s.approval_group_id = ag.id
		ORDER BY s.pozice';

		//ON b.udalost_id=u.udalost_id AND b.platna_od<='$now' AND b.platna_do>='$now'
		$db = Zend_Registry::get('db');
		$res = $db->query($sql);

		$sports = array();
		while ($row = $res->fetch()) {
			$sportId = $row['sport_id'];
			if (!array_key_exists($sportId, $sports))
				$sports[$sportId] = array('name' => $row['nazev'], 'ag' => $row['agsport']);
		}

			// translate all resources at once
		$dictionary = array();
		foreach ($sports as &$sport)
			$dictionary[$sport['name']] = true;
		$dictionary = It6_Models_Translator::translate(array_keys($dictionary), CZ_LANG_ID, $db);
		foreach ($sports as &$sport)
			$sport['name'] = $dictionary[$sport['name']];

		unset($dictionary);

		$lastSportId = false;
		$sportOpts = '';
		foreach ($sports as $sportId => &$sport) {
			$sportOpts .= '<option  value="' . $sportId .'" '
					. (isset($_POST[$name]) && $_POST[$name] == $sportId ? 'selected="selected"' : '') . '>'
					. Help::Html($sport['name']) .'</option>';

		}

		//$this->view->sportOpts = '<select size="10">'.$sportOpts.'</select>';
		//$this->view->eventOpts =

		return '<select id="'.$name.'" name="'.$name.'" '.$extra.'>'.$sportOpts.'</select>';
	}
}
