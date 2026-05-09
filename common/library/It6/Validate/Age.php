<?php

class It6_Validate_Age extends Zend_Validate_Abstract {

	const MSG_AGE = 'msgAge';

	private $day;
	private $month;

	protected $_messageTemplates = array(
		self::MSG_AGE => "'%value%' indicates that you are too young."
	);

	public function __construct($day, $month) {
		$this->month	= $month;
		//One is 18 at the day of his birthday, not the next day...
		$this->day		= $day - 1;
	}

	public function isValid($year) {
		$this->_setValue($year);

		//Martinova kokotarna!!!!
		if(!is_numeric($year))
			$year = 0;


		$curMonth		= date('n');
		$curDay			= date('j');
		$curYear		= date('Y');
		$curDateTmpStmp	= mktime(0, 0, 0, $curMonth, $curDay, $curYear);
		$setDateTmpStmp	= mktime(0, 0, 0, $this->month, $this->day, $year);


		if(strtotime('-18 year', $curDateTmpStmp) <= $setDateTmpStmp) {
			$this->_error(self::MSG_AGE);
			return false;
		}

		return true;
	}
}
