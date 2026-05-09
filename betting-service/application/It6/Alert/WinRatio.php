<?php
class It6_Alert_WinRatio extends It6_Alert_MailAbstract {

	const PARAM_DURATION = 'duration';
	const PARAM_LIMIT = 'limit';

	public static function check($params, $userId = null, $branchId = null) {
		return true;
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$duration = 86400 * static::getParameter(self::PARAM_DURATION);
		$limit = static::getParameter(self::PARAM_LIMIT);
		$users = Webservice_User::getAll();
		$ret['users'] = array();
		foreach ( $users as $user ) {
			$winRatio = Webservice_User::getWinRatio(
					$user['userId'],
					It6_Date::timestampToDb( time() - $duration));

			if ( $winRatio >= $limit / 100 )
				$ret['users'][] = array(
					'userId' => $user['userId'],
					'loginName' => $user['username'],
					'winRatio' => round($winRatio, 4)
				);
		}

		return $ret;
	}
}
