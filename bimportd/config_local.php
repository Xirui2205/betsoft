<?php
include_once(ROOT . 'common/config_util.inc.php');

define('RUNNING_FROM_CLI', 1);
define('BETRADAR_IMPORT_TMP',ROOT . '/files/betradar/import/tmp/');
define('BETRADAR_IMPORT_QUEUE',ROOT . '/files/betradar/import/queue/');
define('BETRADAR_IMPORT_ERROR',ROOT . '/files/betradar/import/errors/');

set_include_path (
	'.' . PATH_SEPARATOR
	. get_include_path() . PATH_SEPARATOR
	. ROOT . 'common/library/'. PATH_SEPARATOR
	. ROOT . 'betting-service/application/' . PATH_SEPARATOR
	. ROOT . 'betting-service/library/'
	//. ROOT . 'cronjob/library' . PATH_SEPARATOR
);

$LOG_CONFIG = array(
	"mainlog" => array(
		'writerParams' => array(
			'stream'   => ROOT.'errorlog/bimportd-%F.log'
		)
	)
);

def('SMTP_PORT', 25);
def('SMTP_AUTH', 'login');
def('SMTP_USERNAME', 'info@compbet.com');
def('SMTP_PASSWORD', '7Dhv1sKS');
def('SMTP_SSL', 'tls');
def('SMTP_ADDRESS', 'compbet.com');
def('MAIL_FROM_ADDRESS', 'info@compbet.com');
def('MAIL_FROM_NAME', 'CompBet');
def('REPLY_TO_ADDRESS', 'info@compbet.com');
def('REPLY_TO_NAME', 'CompBet');

def('SLEEP_TIME', 10); // in seconds

if (!defined('MIN_WON')) define('MIN_WON', 1.11); //Minimalni vyhernost