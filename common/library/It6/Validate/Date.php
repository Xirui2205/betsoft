<?php

class It6_Validate_Date extends Zend_Validate_Date {

public function __construct($options = array()) {
	$this->_locale = Zend_Registry::get('Zend_Locale');
	parent::__construct($options);
	if (empty($this->_format)) {
		$this->_format = It6_Locale_Format::getDateFormat($this->_locale);
		$this->_format = It6_Locale_Format::getLocalizedFormat($this->_format);
	}
}

} // class
