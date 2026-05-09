<?php
class It6_Cron_Job_Sms_ActivationCode extends It6_Cron_Job_Sms_Abstract {

	private $params;

	public function __construct($params) {
		if (!isset($params["code"]) || empty($params["code"])) 
			throw new Exception("Activation code must be set!");

		if (!isset($params["phoneNumber"]) || empty($params["phoneNumber"]))
			throw new Exception("Phone number must be set!");

		$this->params = $params;
	}

	public function sendSms() {
		$text = sprintf("CompBet: Pro dokonceni registrace do vernostniho klubu zadejte nasledujici kod: %s", $this->params["code"]);

		$sms = new It6_Sms_PlainSms();
		$sms->setToNumber($this->params['phoneNumber'])->setText($text);
		$sms->sendSms();
	}
	
}