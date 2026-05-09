<?php
class It6_Alert_HostInOutStatus extends It6_Alert_MailAbstract {

	public static function check($params, $userId = null, $branchId = null) {
		return true;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);

		$hosts = Webservice_Host::getAllInOuts();
		foreach($hosts as $i => $host) {
			if($host['onTheWayDeposit'] + $host['onTheWayWithdraw'] == 0)
				unset($hosts[$i]);
		}

		$ret['hosts'] = $hosts;
		return $ret;
	}
}
