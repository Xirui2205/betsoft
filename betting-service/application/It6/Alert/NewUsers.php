<?php
class It6_Alert_NewUsers extends It6_Alert_MailAbstract {

	const DURATION = 3600; 
	
	public static function check($params, $userId = null, $branchId = null) {
		$tmp = static::getUsers();
		return count($tmp) > 0;
	}
	
	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$ret['newUsers'] = static::getUsers();
		return $ret;
	}
	
	protected static function getUsers() {
		return Webservice_User::getAllWhere(array(
			'activationTime >= ?' => It6_Date::timestampToDb(time()-self::DURATION)));
	}
}