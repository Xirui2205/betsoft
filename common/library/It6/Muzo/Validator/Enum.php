<?php

class It6_Muzo_Validator_Enum implements It6_Muzo_Validator {

	protected $values = null;

	public function __construct($params) {
		if (is_array($params)) {
			$this->values = $params;
		}
	}

	public function isValid($value) {
		if (isset($this->values))
			return in_array($value, $this->values);
		else
			return true;
	}

	public function getValidationErrorMessage() {
		return 'value must be one of: "' . implode('", "', $this->values) . '"';
	}

}
