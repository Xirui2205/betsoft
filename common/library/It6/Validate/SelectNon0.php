<?php

class It6_Validate_SelectNon0 extends Zend_Validate_Abstract {

	const MSG_EMPTY	= 'msgEmpty';

	protected $_messageTemplates = array(
		self::MSG_EMPTY	=> "You must select an option.",
	);


	public function isValid($value) {
		$this->_setValue($value);
		$valid = true;

		if(empty($value)) {
			$this->_error(self::MSG_EMPTY);
			$valid = false;
		}

		return $valid;
	}
}
