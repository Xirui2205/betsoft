<?php

class It6_Cron_Job_CleanDbLog extends It6_Cron_Job_Abstract {
	
	const LOG_LIVESPAN = 30; //number of days of logging to keep in db

	public function execute(array $params = array(), &$errorMessage = null){
		$sql = 'DELETE FROM vic_admin.log WHERE time < DATE_SUB(NOW(), INTERVAL '. self::LOG_LIVESPAN .' DAY)';
		Zend_Registry::get('admindb')->query($sql);
	}

}
