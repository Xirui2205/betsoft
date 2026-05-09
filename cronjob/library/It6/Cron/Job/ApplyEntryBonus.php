<?php

class It6_Cron_Job_ApplyEntryBonus extends It6_Cron_Job_Abstract {

	public function execute(array $params = array(), &$errorMessage = null){
		$ws = Zend_Registry::get('ws');
		$result = 0;
		$userIds = $ws->User->getAllForEntryBonus(true);
		if (!empty($userIds)) {
			foreach ($userIds as $userId) {
				try {
					$ws->Campaign->applyEntryBonus($userId);
				}
				catch (Exception $e) {
					++$result;
					It6_Log::err(
						'Entry bonus check failed. userId=%userId%',
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
