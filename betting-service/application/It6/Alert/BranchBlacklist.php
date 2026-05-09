<?php
class It6_Alert_BranchBlacklist extends It6_Alert_MailAbstract {

	public static function check($params, $userId = null, $branchId = null) {
		return true;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$ret['blacklist'] = Webservice_Host::getBanned();

		return $ret;
	}
}
