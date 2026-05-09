<?php

class It6_Validate_IdCard extends It6_Validate_AbstractCheckDb {

	const MSG_SHORT		= 'msgShort';
	const MSG_LONG		= 'msgLong';
	const MSG_CHARS		= 'msgChars';
	const MSG_EXISTS	= 'msgCitizenIdExists';

	protected $minchars = 8;
	protected $maxchars = 10;

	protected $_messageVariables = array(
		'minchars' => 'minchars',
		'maxchars' => 'maxchars',
	);

	protected $_messageTemplates = array(
		self::MSG_SHORT		=> "Id card number must have at least '%minchars%' characters.",
		self::MSG_LONG		=> "Id card number must have no more then '%maxchars%' characters.",
		self::MSG_CHARS		=> 'Your identification card number is not in the correct format.',
		self::MSG_EXISTS	=> "'%value%' is alreade associated with another account",
	);

	public function isValid($value) {
		$this->_setValue($value);
		$valid = true;

		if(strlen($value) < $this->minchars) {
			$this->_error(self::MSG_SHORT);
			$valid = false;
		}

		if(strlen($value) > $this->maxchars) {
			$this->_error(self::MSG_LONG);
			$valid = false;
		}

		if (preg_match('/^[0-9]+$/', $value) == 0) {
			$this->_error(self::MSG_CHARS);
			$valid = false;
		}

		if ($this->checkDb('User', 'citizenId', array('citizenId' => $value), array())) {
			$this->_error(self::MSG_EXISTS);
			$valid = false;
		}

		return $valid;
	}
}