<?php

class It6_Cron_Job_Alert extends It6_Cron_Job_Abstract {

	public function execute(array $params = array(), &$errorMessage = null){
		Zend_Registry::get('ws')->Alert->assert($params['name'],$params);
	}

}

