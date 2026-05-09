<?php

class I18n {

	/**
	 * Additional params can be passed as message params.
	 * @param string $key Key to be translated
	 */
	public static function tr($key /* , ... */) {
			if (func_num_args() > 1)
				return Zend_Registry::get('translate')->transParam($key, array_slice(func_get_args(), 1), null, null);
			else
				return Zend_Registry::get('translate')->trans($key);
	}

	/**
	 * Additional params can be passed as message params.
	 * @param string $key Key to be translated
	 * @param string $section
	 * @param string $dictionary
	 */
	public static function trans($key, $section = null, $dictionary = null /* , ... */) {
			$params = array_slice(func_get_args(), 4);
			if (empty($params))
				return Zend_Registry::get('translate')->trans($key, $section, $dictionary);
			else
				return self::transParam($key, $params, $section, $dictionary);
	}

	/**
	 * @param string $key Key to be translated
	 * @param mixed $param
	 * @param string $section
	 * @param string $dictionary
	 */
	public static function transParam($key, $param, $section = null, $dictionary = null) {
		return Zend_Registry::get('translate')->transParam($key, $param, $section, $dictionary);
	}

	public static function trC($key) {
//TODO: Just a temp solution. The lang_id should probably somhow be tied in to
//the preferences of the particular admin.

//TODO: function returns false to make sure that all resources do have translation.
//Maybe there should be exception thrown or something else though. This functionality
//(returning false on failed lookup) is used in bookmaker->nastaveni dat->turnaj/udalost

//TODO: Temp solution - talk to Petr!

		$sql = "SELECT text FROM preklady WHERE index_pole='$key' AND lang_id='1'";
		$res = DbUtil::connectWebDb()->query($sql);
		DbUtil::testResult($res);
		if($retdata = $res->fetchRow()){
			return $retdata['text'];
		}
		else
			return false;
	}

}
