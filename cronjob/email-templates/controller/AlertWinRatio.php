<?php

class AlertWinRatio extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_USERS = 'users';

	public $users;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->users = isset($this->params[self::PARAM_USERS])
			? $this->params[self::PARAM_USERS] : array();

		if ( is_object($this->users) )
			$this->users = array($this->users);
	}



	public function getBodyHtml($layoutScript=null) {
		return parent::getBodyHtml('plain');
	}
}
