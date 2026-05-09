<?php
class It6_Validate_Username extends It6_Validate_AbstractCheckDb {

	const MSG_SHORT		= 'msgShort';
	const MSG_LONG		= 'msgLong';
	const MSG_CHARS		= 'msgChars';

	protected $minchars = 4;
	protected $maxchars = 20;

	protected $_messageVariables = array(
		'minchars' => 'minchars',
		'maxchars' => 'maxchars'
	);

	protected $_messageTemplates = array(
		self::MSG_SHORT => "'%value%' is too short. Username must be at least '%minchars%' characters.",
		self::MSG_LONG => "'%value%' is too long. Username must be in '%maxchars%' characters.",
		self::MSG_CHARS => "'%value%' contains invalid characters. Only letters and numbers are allowed.",
	);

	public function isValid($username) {
		$this->_setValue($username);
		$valid = true;

		if(mb_strlen($username,'utf-8') < $this->minchars) {
			$this->_error(self::MSG_SHORT);
			$valid = false;
		}

		if(mb_strlen($username,'utf-8') > $this->maxchars) {
			$this->_error(self::MSG_LONG);
			$valid = false;
		}

		if(!mb_ereg_match("^[a-zA-Z][a-zA-Z0-9]+$", $username)) {
			$this->_error(self::MSG_CHARS);
			$valid = false;
		}

		return $valid;
	}
}