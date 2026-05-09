<?php

if (defined('SMTP_AUTH')) {
	$mailCred = array(
		'port' => SMTP_PORT,
		'auth' => SMTP_AUTH,
		'username' => SMTP_USERNAME,
		'password' => SMTP_PASSWORD
	);
	if(defined(SMTP_SSL))
		$mailCred['ssl'] = SMTP_SSL;
	
	$transport = new Zend_Mail_Transport_Smtp(SMTP_ADDRESS, $mailCred);
}
else
	$transport = new Zend_Mail_Transport_Smtp(SMTP_ADDRESS);
Zend_Mail::setDefaultTransport($transport);
unset($transport);
