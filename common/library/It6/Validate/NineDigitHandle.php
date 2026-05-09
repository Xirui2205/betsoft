<?php

class It6_Validate_NineDigitHandle extends Zend_Validate_Regex {

protected $_messageTemplates = array(
	Zend_Validate_Regex::NOT_MATCH => 'Invalid handle (9 digits)'
);

public function __construct($options = null) {
	parent::__construct(array('pattern' => '/^\\d{9}$/'));
}

public function isValid($value, $context = null) {
	if (1 == preg_match('/^0*$/', $value))
		$value = '';
	return parent::isValid($value);
}

public static function isValidString($value) {
	return ctype_digit($value);
}

} // class
