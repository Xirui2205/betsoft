<?php

class It6_Validate_BirthDate extends Zend_Validate_Abstract {

	const MSG_AGE		= 'msgAge';
	const MSG_FORMAT	= 'msgFormat';

	private $day;
	private $month;

	protected $_messageTemplates = array(
		self::MSG_FORMAT	=> "'%value%' is not in the right format.",
		self::MSG_AGE		=> "'%value%' is too young."
	);


	public function isValid($value) {
		$this->_setValue($value);

		$curMonth		= date('n');
		$curDay			= date('j');
		$curYear		= date('Y');
		$curDateTmpStmp	= mktime(0, 0, 0, $curMonth, $curDay, $curYear);
		$setDateTmpStmp = It6_Date::toTimestamp($value." 00:00:00");
		//One is 18 at the day of his birthday, not the next day...
		$setDateTmpStmp = strtotime('-1 day', $setDateTmpStmp);

        if(!preg_match('/^[0-9]{1,2}[.][0-9]{1,2}[.][0-9]{4}$/', $value)) {
			$this->_error(self::MSG_FORMAT);
			return false;
		}

		if(strtotime('-18 year', $curDateTmpStmp) <= $setDateTmpStmp) {
			$this->_error(self::MSG_AGE);
			return false;
		}

		return true;
	}
}
