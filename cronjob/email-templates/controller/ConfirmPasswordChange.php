<?php

class ConfirmPasswordChange extends It6_Cron_Job_Email_UserEmail {

	const PARAM_HASH = 'hash';

	public function prepareEmail() {
		parent::prepareEmail();

		$row = Zend_Registry::get('ws')->UserAction->getByHash($this->params[self::PARAM_HASH]);

		$this->actionHash   = $this->params[self::PARAM_HASH];
		$this->requestTime  = It6_Date::fromDbAsTime($row['validFrom']);
		$this->requestDate  = It6_Date::fromDbAsDate($row['validFrom']);
	}

}
