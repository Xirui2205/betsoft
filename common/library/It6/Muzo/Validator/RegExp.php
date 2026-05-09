<?php

class It6_Muzo_Validator_RegExp implements It6_Muzo_Validator {

	protected $pattern = null;
	protected $errorMessage = null;

	public function __construct($params) {
		if (is_array($params)) {
			if (isset($params['pattern']))
				$this->pattern = $params['pattern'];
			if (isset($params['errorMessage']))
				$this->errorMessage = $params['errorMessage'];
		}
	}

	public function isValid($value) {
		if (!empty($this->pattern))
			return (1 == preg_match($this->pattern, $value));
		else
			return true;
	}

	public function getValidationErrorMessage() {
		if (isset($this->errorMessage))
			return $this->errorMessage;
		else
			return 'value must match regular expression pattern: "' . $this->pattern . '"';
	}

}
