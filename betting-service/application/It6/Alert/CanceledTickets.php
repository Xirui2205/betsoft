<?php
class It6_Alert_CanceledTickets extends It6_Alert_MailAbstract {

	const DURATION = 86400;
	const PARAM_LIMIT = 'limit'; 
	
	public static function check($params, $userId = null, $branchId = null) {
		return true;
	}
	
	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$ret['canceledTickets'] = Webservice_Ticket::getAllWhere(array(
			'createdTime >= ?' => It6_Date::timestampToDb(time()-self::DURATION),
			'amount >= ?' => static::getParameter(self::PARAM_LIMIT),
			'canceled = 1'));

		return $ret;
	}
}