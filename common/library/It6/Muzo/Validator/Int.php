<?php

class It6_Muzo_Validator_Int implements It6_Muzo_Validator {

	protected $minValue = null;
	protected $maxValue = null;
	protected $minDigits = null;
	protected $maxDigits = null;

	public function __construct($params) {
		if (is_array($params)) {
			if (isset($params['minValue']))
				$this->minValue = $params['minValue'];
			if (isset($params['maxValue']))
				$this->maxValue = $params['maxValue'];
			if (isset($params['minDigits']))
				$this->minDigits = $params['minDigits'];
			if (isset($params['maxDigits']))
				$this->maxDigits = $params['maxDigits'];
		}
	}

	public function isValid($value) {
		if (1 != preg_match('/^-?[0-9]+$/', $value))
			return false;
		if (isset($this->minValue) && ($value < $this->minValue) )
			return false;
		if (isset($this->maxValue) && ($value > $this->maxValue) )
			return false;
		if (isset($this->minDigits) && (strlen($value) < $this->minDigits) )
			return false;
		if (isset($this->maxDigits) && (strlen($value) > $this->maxDigits) )
			return false;
		return true;
	}

	public function getValidationErrorMessage() {
		$msg = 'value must be an integer';
		if (isset($this->minValue))
			$msg .= ', greater or equal to ' . $this->minValue;
		if (isset($this->maxValue))
			$msg .= ', less or equal to ' . $this->maxValue;
		if (isset($this->minDigits))
			$msg .= ', with at least ' . $this->minDigits . ' digits';
		if (isset($this->maxDigits))
			$msg .= ', with at most ' . $this->maxDigits . ' digits';
		return $msg;
	}

}
