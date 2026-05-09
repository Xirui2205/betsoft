<?php

class It6_Muzo_Validator_NotEmpty implements It6_Muzo_Validator {

	public function __construct($params) {}

	public function isValid($value) {
		return !empty($value);
	}

	public function getValidationErrorMessage() {
		return 'value must not be empty';
	}

}
