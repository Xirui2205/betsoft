<?php

class AlertHostDeposits extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_TRANSACTIONS = 'transactions';
	
	public $transactions;
	
	public function prepareEmail() {
		parent::prepareEmail();
		$this->transactions = isset($this->params[self::PARAM_TRANSACTIONS])
			? $this->params[self::PARAM_TRANSACTIONS] : array();

		if ( is_object($this->transactions) )
			$this->transactions = array($this->transactions); 
	}

}
