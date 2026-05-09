<?php
class It6_Alert_BranchTicketsCount extends It6_Alert_MailAbstract {

	public static function check($params, $userId = null, $branchId = null) {
		return true;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);

		$from = It6_Date::timestampToDb(It6_Date::nowAsTimestamp() - 86400);

		$ret['branches'] = Webservice_Branch::getTicketsCount($from);
		return $ret;
	}
}