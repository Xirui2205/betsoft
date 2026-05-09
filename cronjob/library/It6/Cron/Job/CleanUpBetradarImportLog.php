<?php

class It6_Cron_Job_CleanUpBetradarImportLog extends It6_Cron_Job_Abstract {

	public function execute(array $params = array(), &$errorMessage = null){
		Zend_Registry::get('ws')->BetradarImportLog->cleanUp();
	}

}
