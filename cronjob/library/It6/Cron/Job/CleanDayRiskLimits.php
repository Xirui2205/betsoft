<?php

class It6_Cron_Job_CleanDayRiskLimits extends It6_Cron_Job_Abstract {

	public function execute(array $params = array(), &$errorMessage = null) {
		$ws = Zend_Registry::get('ws');
		//$ws->Bet->cleanDayRiskLimits();
		$ws->User->cleanDayIndividualRiskLimits();
		$ws->User->cleanDayDuplicateTickets();
	}

}
