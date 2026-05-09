<?php

class It6_Cron_Job_CleanUpSession extends It6_Cron_Job_Abstract {

	public function execute(array $params = array(), &$errorMessage = null){
		Zend_Registry::get('ws')->Session->cleanUp();
	}

}
