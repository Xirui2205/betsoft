<?php

class It6_Translate_Admin extends It6_Translate {

private $langId = null;
private $db = null;

public function __construct($langId, &$db = null) {
	$this->langId = $langId;
	if (isset($db))
		$this->db = &$db;
	else
		$this->db = Zend_Registry::get('zdb_admin');
}

public function trans($key, $section = null, $dictionary = null, $wrapUnfoundTag=true) {
	return It6_Models_Translator::translate($key, $this->langId, $this->db);
}

public function hasTranslation($key, $section = null, $dictionary = null) {
	return true;
}

public function getCurrentLangId() {
	return $this->langId;
}

} // It6_Translate_Admin
