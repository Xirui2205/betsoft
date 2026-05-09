<?php
class It6_Alert_RiskLimit extends It6_Alert_MailAbstract {

	public static function check($params, $userId = null, $branchId = null) {
		$count = Webservice_Bet::getAllWhereCount(
			array(
				'betId = ?' => $params["betId"],
				'riskLimit <= riskLimitBalance'
			)
		);
		if ($count > 0) return true;
		else return false;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);

		$ret['bet'] = Webservice_Bet::getOneWhere(
			array(
				'betId = ?' => $params["betId"],
				'riskLimit <= riskLimitBalance'
			)
		);

		return $ret;
	}
}