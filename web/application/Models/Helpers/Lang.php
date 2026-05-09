<?php

class Models_Helpers_Lang {
	 
	public static function getDisplayedLangs() {
		static $cache = false;
		if (false === $cache) {
			$select = Zend_Registry::get('db')->select()->from(array('jazyky'),array('lang_id','iso'))
			->where('zobrazeno=?',1);
			$res  = $select->query()->fetchAll();

			$cache = array();
			foreach ($res as $k=>$v) {
				$cache[$v['lang_id']] = $v['iso'];
			}
		}
		return $cache;
	}

}
