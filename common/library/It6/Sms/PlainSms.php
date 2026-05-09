<?php
class It6_Sms_PlainSms extends It6_Sms_Abstract {
	
	private $soap_connector = null;
	private $toNumber = null;
	private $text = null;
    const SMS_TYPE_AUTHORIZATION = 1;
    const SMS_TYPE_TICKET_RESULT = 2;
    const SMS_TYPE_USER_WITHDRAW_CASH = 3;
    const SMS_TYPE_REGISTRATION_NICK = 4;
    const SMS_TYPE_BAN_UNBAN_USER = 5;
	
	public function __construct() {
		$soap_connector = new Zend_Soap_Client();
		$soap_connector->setSoapVersion(self::SOAP_VERSION);
		$soap_connector->setLocation(self::LOCATION);
		$soap_connector->setUri(self::URI);
		$soap_connector->setHttpLogin(self::HTTP_LOGIN);
		$soap_connector->setHttpPassword(self::HTTP_PASSWORD);
		$soap_connector->setEncoding(self::ENCODING);
		$soap_connector->setHttpsCertificate(self::LOCAL_CERT);
		$soap_connector->setEncodingMethod(self::ENCODING_METHOD);
	
		$this->soap_connector = $soap_connector;
	
		return $soap_connector;
	}
	
	public function setToNumber($toNumber) {
		$this->toNumber = $toNumber;
		return $this;
	}
	
    /**
     * Set text and type
     * @param string $text
     * @param int $type constants: SMS_TYPE_AUTHORIZATION (0), SMS_TYPE_TICKET_RESULT (2)
     * @return \It6_Sms_PlainSms
     */
	public function setText($text, $type) {
		$this->text = $text;
        $this->type = $type;
		return $this;
	}
	
	
	public function sendSms( $priority = false ) {
		if ($this->toNumber === null) {
			throw new Exception('Recipient number was not set!');
		}
		
		if ($this->text === null) {
			throw new Exception('Content of the message was not set!');
		}
		
        if ($this->type === null) {
			throw new Exception('Type of the message was not set!');
		}
        
		$message_id = uniqid($this->generateRandomString(), true);
		$confirm = 0;
        $savedSoap = false;
		if ( $priority === false ) {
			$savedSoap = $this->soap_connector->save_sms($message_id,$this->toNumber,$this->text,$confirm);
		} else {
			$savedSoap = $this->soap_connector->save_prior_sms($message_id,$this->toNumber,$this->text,$confirm, /*priority*/ 1);
		}
        
		$wsSMS = new Webservice_Sms();
		return $wsSMS->insert(array(
			'smsMessageText' => $this->text,
			'smsPhoneNumber' => $this->toNumber,
			'smsTypeIdSms' => $this->type,
			'smsSend' => It6_Date::dbNow()
		));
	}
	
	private function generateRandomString($length = 5) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
}