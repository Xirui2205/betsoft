<?php
class It6_Validate_DateYear extends Zend_Validate_Abstract {

	const MSG_EMPTY = 'msgEmpty';

	protected $_messageTemplates = array(
		self::MSG_EMPTY => "You must select year."
	);

	public function __construct() {}

	public static function isValidYear($year, &$error = null) {
		if (empty($year)) {
			$error = static::MSG_EMPTY;
			return false;
		}
		return true;
	}

	public function isValid($value) {
		$this->_setValue($value);
		$valid = static::isValidYear($value, $msg);
		if (!$valid) $this->_error($msg);
		return $valid;
	}
}