<?php
include_once(ROOT . 'common/config_util.inc.php');

set_include_path (
	'.' . PATH_SEPARATOR .
	get_include_path() . PATH_SEPARATOR .
	ROOT . 'common/library/'. PATH_SEPARATOR .
	ROOT . 'cronjob/library' . PATH_SEPARATOR .
	ROOT . 'betting-service/application/'
);

$GLOBALS['LOG_CONFIG'] = array(
	"mainlog" => array(
		'writerParams' => array(
			'stream'   => ROOT.'errorlog/cronjob-%F.log'
		)
	)
);

def('WS_WRAPPER', 'Direct'); // one of: XmlRpc Direct
def('SSL_CERT', ROOT . 'web/ssl/branch.1.1.test.pem');
def('SSL_CERT_PASSWD', '');

if ( WS_WRAPPER == 'Direct' ) {
	set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'betting-service/library'
	);
}

def('SMTP_PORT', 25);
//def('SMTP_AUTH', 'login');
//def('SMTP_USERNAME', 'victoria-tip@compbet.com');
//def('SMTP_PASSWORD', '7Dhv1sKS');
def('SMTP_SSL', 'tls');
def('SMTP_ADDRESS', 'compbet.com');
def('MAIL_FROM_ADDRESS', 'no-reply@compbet.com');
def('MAIL_FROM_NAME', 'CompBet');
def('REPLY_TO_ADDRESS', 'no-reply@compbet.com');
def('REPLY_TO_NAME', 'CompBet');
def('ACL_FACTORY_CLASS', 'It6_Acl_Factory_Cronjob');
def('SUBJECT_PREFIX', 'testing: ');