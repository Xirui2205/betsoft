<?php

class ReturnedBetEmail extends It6_Cron_Job_Email_ReturnedBetEmail {

// see params in parent class

	public function prepareEmail() {
		parent::prepareEmail();

		$db = Zend_Registry::get('db');
		$userData = It6_Models_User::getData($this->params[static::PARAM_USER_ID], $db);
		$this->langId = $userData['langId'];
		$betInfo = $this->params[static::PARAM_RETURNED_BET_INFO];


		$keys = array(
			'type'		=> $betInfo['type'],
			'event'		=> $betInfo['event'],
			'text'		=> $betInfo['text'],
			'region'	=> $betInfo['region'],
			'sport'		=> $betInfo['sport'],
		);
		$translations = It6_Models_Translator::translate($keys, $this->langId, $db);

		foreach ($keys as $key => $text) {
			if (!empty($translations[$text]) && !empty($translations[$text]))
				$betInfo[$key] = $translations[$text];
		}

		$this->nick			= $userData['nick'];
		$this->firstName	= $userData['name'];
		$this->surname		= $userData['surname'];
		//$this->currencyId = $userData['currencyId'];
		$this->currencyName = $userData['currencyName'];
		$this->losses		= array(); //$this->params[static::PARAM_LOSSES];
		$this->debts		= array(); //$this->params[static::PARAM_DEBTS];
		$this->betInfo		= $betInfo;
		$this->tickets		= $this->params[It6_Cron_Job_Email_ReturnedBetEmail::PARAM_TICKETS];
	}

}
