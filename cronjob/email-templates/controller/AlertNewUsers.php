<?php

class AlertNewUsers extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_NEW_USERS = 'newUsers';
	
	public $newUsers;
	
	public function prepareEmail() {
		parent::prepareEmail();
		$this->newUsers = isset($this->params[self::PARAM_NEW_USERS])
			? $this->params[self::PARAM_NEW_USERS] : array();
		if ( is_object($this->newUsers) )
			$this->newUsers = array($this->newUsers); 
	}

}
