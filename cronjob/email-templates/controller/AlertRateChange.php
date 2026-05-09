<?php

class AlertRateChange extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_BET_ID = 'betId';
	const PARAM_BOOKMAKER = 'bookmaker';
	const PARAM_CHANGES = 'changes';
	
	public $blacklist;
	
	public function prepareEmail() {
		parent::prepareEmail();
		$this->betId = $this->params[self::PARAM_BET_ID];
		$this->bookmaker = $this->params[self::PARAM_BOOKMAKER];
		$this->changes = $this->params[self::PARAM_CHANGES];
	}

}
