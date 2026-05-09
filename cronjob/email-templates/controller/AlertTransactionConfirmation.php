<?php

class AlertTransactionConfirmation extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_HOSTS = 'transactions';

	public $transactions;
	
	private $transactionTypes = array(); //ids of transactions that require confirmation

	public function prepareEmail() {
		parent::prepareEmail();
		$this->transactions = isset($this->params[self::PARAM_HOSTS])
			? $this->params[self::PARAM_HOSTS] : array();

		if ( is_object($this->transactions) )
			$this->transactions = array($this->transactions);
	}



	public function getBodyHtml($layoutScript=null) {
		return parent::getBodyHtml('plain');
	}
}
