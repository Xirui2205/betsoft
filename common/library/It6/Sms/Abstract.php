<?php

abstract class It6_Sms_Abstract {

	const LOCATION = ''; //https://sms3.wtnet.cz/fx_smsserver/soap.php';
	const URI = ''; //'https://sms3.wtnet.cz/fx_smsserver/soap.php';
	
	const SOAP_VERSION = SOAP_1_1;
	const ENCODING = 'ISO-8859-1';
	const ENCODING_METHOD = SOAP_ENCODED;
	
	//autentifikacni udaje
	const HTTP_LOGIN = 'compbet-bewa';
	const HTTP_PASSWORD = 'gGNJnhRM';
	const LOCAL_CERT = ISP_SMS_GATE_CERT; //cesta k pem klici pro prihlasovani k sms brane na ISP
	
	protected abstract function sendSms();
}