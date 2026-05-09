<?php
// Pro validaci věku při registraci (hidden input zapsaný pomocí datepickeru)
class It6_Validate_BirthDate2 extends Zend_Validate_Abstract {

	const MSG_AGE		= 'msgAge';
	const MSG_FORMAT	= 'msgFormat';

	private $day;
	private $month;

	protected $_messageTemplates = array(
		self::MSG_FORMAT	=> "reg_birthday_format",
		self::MSG_AGE		=> "reg_young"
	);


	public function isValid($value) {
		$this->_setValue($value);

		$curMonth		= date('n');
		$curDay			= date('j');
		$curYear		= date('Y');
		$curDateTmpStmp	= mktime(0, 0, 0, $curMonth, $curDay, $curYear);
		$setDateTmpStmp = strtotime($value);
		//One is 18 at the day of his birthday, not the next day...
		$setDateTmpStmp = strtotime('-1 day', $setDateTmpStmp);

        if(!preg_match('/^[0-9]{4}[-][0-9]{1,2}[-][0-9]{1,2}$/', $value)) {
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
