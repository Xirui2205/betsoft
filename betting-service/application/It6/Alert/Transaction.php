<?php
class It6_Alert_Transaction extends It6_Alert_MailAbstract {

	const PARAM_LIMIT = 'limit';
	const PARAM_DURATION = 'duration';

	public static function check($params, $userId = null, $branchId = null) {
		if (empty($params['typeId'])) {
			$transaction = Webservice_Transaction::getById($params['id']);
			$typeId = $transaction['typeId'];
		}
		else {
			$typeId = $params['typeId'];
		}
		if ( $typeId != 9 ) 
			return false;

		$limit = static::getParameter(self::PARAM_LIMIT, $userId, $branchId);
		$duration = static::getParameter(self::PARAM_DURATION, $userId, $branchId) * 60;
		$count = Webservice_Transaction::getAllWhereCount(
			array(
				'typeId = ?' => 9,
				'time >= ?' => It6_Date::timestampToDb(time() - $duration)));

		return $limit <= $count;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);

		$duration = static::getParameter(self::PARAM_DURATION, $userId, $branchId) * 60;

		$ret['transactions'] = Webservice_Transaction::getAllWhere(
			array(
				'typeId = ?' => 9,
				'time >= ?' => It6_Date::timestampToDb(time() - $duration)));

		return $ret;
	}
}