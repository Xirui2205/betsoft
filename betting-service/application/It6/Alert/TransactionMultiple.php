<?php
class It6_Alert_TransactionMultiple extends It6_Alert_MailAbstract {

	const PARAM_DURATION = 'duration';

	public static function check($params, $userId = null, $branchId = null) {
		if (empty($params['typeId'])) {
			$transaction = Webservice_Transaction::getById($params['id']);
			$typeId = $transaction['typeId'];
		} else {
			$typeId = $params['typeId'];
		}

		if ( $typeId != 11 && $typeId != 12) return false;

		$duration = static::getParameter(self::PARAM_DURATION, $userId, $branchId);
		$count = Webservice_Transaction::getAllWhereCount(
			array(
				'typeId = ?' => $typeId,
				'userId = ?' => $userId,
				'value = ?' => $params["value"],
				'time >= ?' => It6_Date::timestampToDb(time() - $duration)));
		if ($count > 1) return true;
		else return false;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$duration = static::getParameter(self::PARAM_DURATION, $userId, $branchId);

		$ret['transactions'] = Webservice_Transaction::getAllWhere(
			array('typeId = ?' => $params["typeId"], 'time >= ?' => It6_Date::timestampToDb(time() - $duration))
		);

		return $ret;
	}

	public static function getParameterDuration() {
		return static::getParameter(self::PARAM_DURATION);
	}
}