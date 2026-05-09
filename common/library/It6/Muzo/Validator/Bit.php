<?php

class It6_Muzo_Validator_Bit implements It6_Muzo_Validator {
	public function __construct($params) {
	}

	public function isValid($value) {
		return ($value == 0) || ($value == 1);
	}

	public function getValidationErrorMessage() {
		return 'value must be 0 or 1';
	}

}
