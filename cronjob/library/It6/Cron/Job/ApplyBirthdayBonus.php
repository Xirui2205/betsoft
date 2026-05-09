<?php

class It6_Cron_Job_ApplyBirthdayBonus extends It6_Cron_Job_Abstract {

	public function execute(array $params = array(), &$errorMessage = null){
		$ws = Zend_Registry::get('ws');
		$result = 0;
		$userIds = $ws->User->getAllForBirthdayBonus(false);
		if (!empty($userIds)) {
			foreach ($userIds as $userId) {
				try {
					$ws->Campaign->applyBirthdayBonus($userId);
				}
				catch (Exception $e) {
					++$result;
					It6_Log::err(
						'Birthday bonus check failed. userId=%userId%',
						It6_Log::TAG_CRONJOB,
						array('userId' => $user['userId']),
						$e
					);
				}
			}
		}
		return $result;
	}

}