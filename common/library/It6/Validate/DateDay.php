<?php
class It6_Validate_DateDay extends Zend_Validate_Abstract {

	const MSG_EMPTY = 'msgEmpty';
	const MSG_NO_SUCH_DAY = 'msgNoSuchDay';

	private static $MONTH_30_DAYS = array(4,6,9,11);

	private $month;
	private $year;

	protected $_messageTemplates = array(
		self::MSG_EMPTY => "You must select a day.",
		self::MSG_NO_SUCH_DAY => "Day '%value%' is not valid for the choden month."
	);

	public function __construct($month, $year) {
		$this->month = $month;
		$this->year = $year;
	}

	/**
	 * Checks if day given by three separate parts is valid date (eg. Feb 30 surely isn't).
	 * @param integer $year eg. 2000
	 * @param integer $month 1..12
	 * @param integer $day 1..31
	 * @param string $error Variable will receive error message if validation failed
	 * @return boolean
	 */
	public static function isValidDay($year, $month, $day, &$error = null) {
		if (empty($day)) {
			$error = static::MSG_EMPTY;
			return false;
		}
		/*if (1 > $year || 1 > $month || 12 < $month || 1 > $day || 31 < $day) {
			$error = static::MSG_NO_SUCH_DAY;
			return false;
		}*/
		if (2 == $month) {
			$leapYear = false;
			if (0 == $year % 400)
				$leapYear = true;
			else if (0 != $year % 100 && 0 == $year % 4)
				$leapYear = true;
			if (($leapYear ? 29 : 28) < $day) {
				$error = static::MSG_NO_SUCH_DAY;
				return false;
			}
		} else {
			if (31 == $day && in_array($month, static::$MONTH_30_DAYS)) {
				$error = static::MSG_NO_SUCH_DAY;
				return false;
			}
		}
		return true;
	}

	public function isValid($value) {
		$this->_setValue($value);
		$valid = static::isValidDay($this->year, $this->month, $value, $msg);
		if (!$valid) $this->_error($msg);
		return $valid;
	}
}