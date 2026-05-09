<?php

class It6_Gui_Region {

	public static function getMultiSelect($name = 'regions', $size = 50, $extra='') {
		$sql =
		'SELECT oblast_id,nazev,pozice,img,betradar_oblast_id,iso
		FROM oblast
		ORDER BY pozice';

		//ON b.udalost_id=u.udalost_id AND b.platna_od<='$now' AND b.platna_do>='$now'
		$db = Zend_Registry::get('db');
		$res = $db->query($sql);

		$regions = array();
		while ($row = $res->fetch()) {
			$regionId = $row['oblast_id'];
			if (!array_key_exists($regionId, $regions))
				$regions[$regionId] = array('name' => $row['nazev']);
		}

		// translate all resources at once
		$dictionary = array();
		foreach ($regions as &$region)
			$dictionary[$region['name']] = true;
		$dictionary = It6_Models_Translator::translate(array_keys($dictionary), CZ_LANG_ID, $db);
		foreach ($regions as &$region)
			$region['name'] = $dictionary[$region['name']];

		unset($dictionary);

		$regionOpts = '';
		foreach ($regions as $regionId => &$region) {
			$regionOpts .= "<option value=\"".$regionId."\""
			. (isset($_POST[$name]) && ($_POST[$name] == $regionId || in_array($regionId,$_POST[$name]) ) ? ' selected="selected"' : '') . ">".Help::Html($region['name'])."</option> ";
			
		}
		
		return '<select id="'.$name.'" name="'.$name.'[]" multiple size="'.$size.'" '.$extra.'>'.$regionOpts.'</select>';
	}
}
