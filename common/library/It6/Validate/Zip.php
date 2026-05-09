<?php
class It6_Validate_Zip extends It6_Validate_AbstractCheckDb {

	const MSG_LONG = 'msgLong';
	const MSG_SHORT = 'msgShort';
	const MSG_NOT_EXISTS = 'msgNotExists';
	const MSG_CHARS = 'msgChars';

	protected $maxchars = 6;
	protected $minchars = 5;

	protected $_messageVariables = array(
		'maxchars' => 'maxchars',
		'minchars' => 'minchars',
	);

	protected $_messageTemplates = array(
		self::MSG_LONG	=> "Zip code must have no more then '%maxchars%' characters.",
		self::MSG_SHORT	=> "Zip code must be at least '%minchars' characters.",
		self::MSG_NOT_EXISTS => "Entered zipcode does not exist.",
		self::MSG_CHARS => "Your zip code is not in the valid format."
	);

	public function isValid($value) {
		$this->_setValue($value);
		$valid = true;

		if(strlen($value) > $this->maxchars) {
			$this->_error(self::MSG_LONG);
			$valid = false;
		}

		if(strlen($value) < $this->minchars) {
			$this->_error(self::MSG_SHORT);
			$valid = false;
		}

		if(preg_match('/^\d{3} ?\d{2}$/', $value) == 0) {
			$this->_error(self::MSG_CHARS);
			$valid = false;
		}

		$value = str_replace(" ", "", $value);

		if ($this->checkDb('ZipCode', 'zipCodeId', array('zipCode' => $value), array()) == false) {
			$this->_error(self::MSG_NOT_EXISTS);
			$valid = false;
		}
		return $valid;
	}
}