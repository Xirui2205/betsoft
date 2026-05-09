<?php

class Models_NodeComm {

/**
 * @param NULL|integer $langId
 * @return array List of page structures with fields: controllerId, text, controller, action
 */
public static function getAllWebPages($langId = null) {
	$db = Zend_Registry::get('db');
	if (empty($langId)) {
		$langId = DEFAULT_LANG_ID;
	}
	return $db->select()
		->from('controller_convert', array(
			'controllerId' => 'c_id',
			'text' => 'text',
			'controller' => 'req_controller',
			'action' => 'req_action',
		))
		->where('lang_id=?', $langId)
		->order('req_controller')
		->order('req_action')
		->query()
		->fetchAll();
		
}

} // class
