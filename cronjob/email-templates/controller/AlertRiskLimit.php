<?php
class AlertRiskLimit extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_BET = 'bet';

	public $bet;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->bet = $this->params[self::PARAM_BET];
	}

	public function getBodyHtml($layoutScript=null) {
		return parent::getBodyHtml('plain');
	}
}