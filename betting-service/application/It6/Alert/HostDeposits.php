<?php
class It6_Alert_HostDeposits extends It6_Alert_MailAbstract {

	const BRANCH_DEPOSIT_TRANSACTION = 22;
	
	public static function check($params, $userId = null, $branchId = null) {
		return true;
	}
	
	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$ret['transactions'] = Webservice_Transaction::getAllWhere(array(
				'typeId = ?' => self::BRANCH_DEPOSIT_TRANSACTION,
				'okTime > ?' => It6_Date::timestampToDb(It6_Date::nowAsTimestamp() - 86400)
			));
		return $ret;
	}
}