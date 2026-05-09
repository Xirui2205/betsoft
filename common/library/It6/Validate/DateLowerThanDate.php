<?php
class It6_Validate_DateLowerThanDate extends Zend_Validate_Date {

	const DATE_INVALID = 'dateInvalid';

	public $date;

	protected $_messageTemplates = array(
		self::DATE_INVALID => "date must be lower than '%value%'"
	);

	public function __construct($date) {
		$this->date = $date;
	}

	public function isValid($value) {
		$this->_setValue($this->date);
		$value = It6_Date::toDb($value);
		$date = It6_Date::toDb($this->date);

		if ($value > $date) {
			$this->_error(self::DATE_INVALID);
			return false;
		}

		return true;
	}
}