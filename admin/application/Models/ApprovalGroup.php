<?php

class Models_ApprovalGroup {

	protected static $minStep = 0.01;

	public static function getInterval($thresholdTable){
		$interval = array();
		$interval[0] = array(
			'left' => 0,
			'value' => $thresholdTable[0]['stake_lower_threshold'],
			'right' => $thresholdTable[0]['odd_upper_threshold'],
			'id' => $thresholdTable[0]['id']
		);

		for($i=0;$i<count($thresholdTable);$i++) {
			$interval[$i+1] = array(
				'left' => $thresholdTable[$i]['odd_upper_threshold'] + self::$minStep,
				'value' => $thresholdTable[$i+1]['stake_lower_threshold'],
				'right' => $thresholdTable[$i+1]['odd_upper_threshold'],
				'id' => $thresholdTable[$i+1]['id']
			);
		}
		return $interval;
	}


/*
	public static function getSportsMultiCheckBox($s,$id, $collection) {
		$elm = new Zend_Form_Element_MultiCheckbox('sports',array('size'=>count($s)));
		foreach ($s as $k => $v) {
			//var_dump($v['approvalGroupId']);
			//FIXME: fetch group name instead of id
			$elm->addMultiOption($v['sportId'], $v['name']. ' ('.(array_key_exists($v['approvalGroupId'],$collection) ? $collection[$v['approvalGroupId']] : '-').')');
		}
		return  $elm;
	}

	public static function getSportsComboBox($s,$id, $collection) {
		$elm = new Zend_Form_Element_Select('sportId',array('onChange'=>'loadGeneric("202&superb=1", "inner", { sportId : this.value } )'));
			$elm->addMultiOption('', i18n::tr('Choose sport ..'));
		foreach ($s as $k => $v) {
			//FIXME: fetch group name instead of id
			$elm->addMultiOption($v['sportId'], $v['name']. ' ('.(array_key_exists($v['approvalGroupId'],$collection) ? $collection[$v['approvalGroupId']] : '-').')');
		}
		$elm->setValue(array($id));
		return  $elm;
	}
*/

/*
	public static function getEventsComboBox($s,$id,$collection) {
		$elm = new Zend_Form_Element_MultiCheckbox('events',array('size'=>count($s)));
		foreach ($s as $k => $v) {
			//FIXME: fetch group name instead of id
			$elm->addMultiOption($v['eventId'], $v['name']. ' ('.(array_key_exists($v['approvalGroupId'],$collection) ? $collection[$v['approvalGroupId']] : '-').')');
		}
		return  $elm;
	}
*/

	public static function getGroupComboBox($s) {

		$elm = new Zend_Form_Element_Select('group');
		$elm->addMultiOption(0, '--'.I18n::tr('none').'--');
		foreach ($s as $k => $v) {
			$elm->addMultiOption($v['id'], $v['name']);
		}
		$dec = new It6_Models_DecoratedForm_Plain;
		$elm->setDecorators($dec->getElementDecorator(true, false));
		return $elm;
	}

	public static function getEventsMultiSelectWithApprovalGroup($elmName, $filter = NULL, $size = 50) {
		$where = '';
		if (isset($filter))  {
			if (!empty($filter['sports'])) 
				$where = 's.sport_id IN ('.implode(',',$filter['sports']).') AND';
			else $where = 'TRUE AND';
			if (!empty($filter['regions'])) 	
				$where .= ' u.oblast_id IN ('.implode(',',$filter['regions']).') ';
			else $where .= ' TRUE';

		$where = 'WHERE '.$where;
		}
			
		$sql =
		'SELECT
			s.sport_id,
			ag.name AS agsport,
			ag2.name AS agevent,
			s.nazev,
			u.nazev AS udalost_nazev,
			u.udalost_id
		FROM sport s
		JOIN udalost u ON u.sport_id = s.sport_id
		LEFT JOIN approval_group ag ON s.approval_group_id = ag.id
		LEFT JOIN approval_group ag2 ON u.approval_group_id = ag2.id
		'.$where.'
		GROUP BY s.sport_id, u.udalost_id
		ORDER BY s.pozice, u.pozice';

		//ON b.udalost_id=u.udalost_id AND b.platna_od<='$now' AND b.platna_do>='$now'
		$db = Zend_Registry::get('db');
		$res = $db->query($sql);

		$sports = array();
		$events = array();
		while ($row = $res->fetch()) {
			$sportId = $row['sport_id'];
			$eventId = $row['udalost_id'];
			if (!array_key_exists($sportId, $sports))
				$sports[$sportId] = array('name' => $row['nazev'], 'events' => array($eventId),'ag' => $row['agsport']);
			else
				$sports[$sportId]['events'][] = $eventId;
			$events[$eventId] = array(
				'sportId' => $sportId,
				'name' => $row['udalost_nazev'],
				'ag' => $row['agevent']
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
					$eventOpts .= "<optgroup label=\"".Help::Html($sport['name']).' ('.Help::Html($sport['ag']).')'."\">";
				}

				$eventOpts .= '<option  value="' . $eventId .'" '
					. (isset($_POST['udalost']) && $_POST['udalost'] == $eventId ? 'selected="selected"' : '') . '>'
					. Help::Html($event['name']) .' ('.Help::Html($event['ag']).')</option>';
			}
		}
		if (false !== $lastSportId)
			$eventOpts .= "</optgroup>";

		//$this->view->sportOpts = '<select size="10">'.$sportOpts.'</select>';
		//$this->view->eventOpts =

		if (empty($eventOpts))
			return UiUtil::printWarnings('No event matched filter criteria.');
		else 
			return '<select id="'.$elmName.'" name="'.$elmName.'" multiple size="'.$size.'">'.$eventOpts.'</select>';
	}

	public static function getSportsMultiSelectWithApprovalGroup($elmName, $size = 50) {
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
			$sportOpts .= "<option value=".$sportId.">".Help::Html($sport['name']).' ('.Help::Html($sport['ag']).')'."</option> ";
			
		}
		
		//$this->view->sportOpts = '<select size="10">'.$sportOpts.'</select>';
		//$this->view->eventOpts =

		return '<select id="'.$elmName.'" name="'.$elmName.'" multiple size="'.$size.'">'.$sportOpts.'</select>';
	}

}

