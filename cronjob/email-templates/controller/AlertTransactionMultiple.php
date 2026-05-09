<?php
class AlertTransactionMultiple extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_TRANSACTIONS = 'transactions';

	public $transactions;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->transactions = $this->params[self::PARAM_TRANSACTIONS];
	}
}