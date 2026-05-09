<?php
class It6_Validate_DateMonth extends Zend_Validate_Abstract {

	const MSG_EMPTY = 'msgEmpty';

	protected $_messageTemplates = array(
		self::MSG_EMPTY => "You must select a month."
	);

	public function __construct() {}

	public static function isValidMonth($month, &$error = null) {
		if (empty($month)) {
			$error = static::MSG_EMPTY;
			return false;
		}
		return true;
	}

	public function isValid($value) {
		$this->_setValue($value);
		$valid = static::isValidMonth($value, $msg);
		if (!$valid) $this->_error($msg);
		return $valid;
	}
}