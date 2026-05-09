<?php
class It6_Validate_DigitsNonZero extends Zend_Validate_Abstract {

	const MSG_FORMAT	= 'msgFormat';

	protected $_messageTemplates = array(
		self::MSG_FORMAT	=> "'%value%' is not in the right format.",
	);


	public function isValid($value) {
		$this->_setValue($value);

        if(!preg_match('/^[1-9][0-9]*$/', $value)) {
			$this->_error(self::MSG_FORMAT);
			return false;
		}

		return true;
	}
}
