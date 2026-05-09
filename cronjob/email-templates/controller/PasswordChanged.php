<?php

class PasswordChanged extends It6_Cron_Job_Email_UserEmail {

	const PARAM_NEW_PASSWORD = 'new_password';

	public function prepareEmail(){
		parent::prepareEmail();
		$this->newPass = $this->params[self::PARAM_NEW_PASSWORD];
	}

}
