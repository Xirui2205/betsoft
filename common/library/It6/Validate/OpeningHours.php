<?php

class It6_Validate_OpeningHours extends Zend_Validate_Abstract {

	const MSG_FORMAT		= 'msgFormat';


	protected $_messageTemplates = array(
		self::MSG_FORMAT	=> 'The information is in the wrong format',
	);


	public function isValid($value) {
		$this->_setValue($value);
		$valid = true;

		if(!preg_match('/^([0-2]?[0-9]:[0-5][0-9]-[0-2]?[0-9]:[0-5][0-9])((,|;)[0-2]?[0-9]:[0-5][0-9]-[0-2]?[0-9]:[0-5][0-9])*$/', $value)) {
			$this->_error(self::MSG_FORMAT);
			$valid = false;
		}

		return $valid;
	}
}
