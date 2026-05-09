<?php

class It6_Cron_Job_CleanSavedCoupons extends It6_Cron_Job_Abstract {
	
	public function execute(array $params = array(), &$errorMessage = null){
		$db = Zend_Registry::get('db');
		$now = It6_Date::dbNow();
		$db->query('delete from coupon_saved WHERE platny_do < SUBDATE(?, 3)', $now);
	}
}
