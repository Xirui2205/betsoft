<?php
class It6_Alert_LiveTicketNotConfirmed extends It6_Alert_MailAbstract {

	const DURATION = 3600; 

	public static function check($params, $userId = null, $branchId = null) {
		$tmp = static::getTickets();
		return count($tmp) > 0;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$tickets = static::getTickets();
		$ret['tickets'] = array_slice($tickets,0,10);
		if ( count($tickets) > 10 ) {
			$ret['nextTicketsCount'] = count($tickets) - 10;
		}
		return $ret;
	}

	protected static function getTickets() {
		return Webservice_Livebetting::getAllWhere(array(
			'timeCreated <= ?' => It6_Date::timestampToDb(time() - self::DURATION),
			'confirmed <> 1',
			'status = ?' => 'new',
		));
	}

}