<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

// Spoustet po SQL skriptu a aplikovani vsech PHP skriptu (ktere by mely byt aplikovany se zastavenymi daemony).

define('RUNNING_FROM_CLI', true);
define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

include(ROOT . 'common/config_util.inc.php');

define ('BBAS_STARTING_POINTS_TRANS_TYPE',15);

$appEnv = getAppEnv();
if (empty($appEnv)) {
    echo "APPLICATION_ENVIRONMENT environment variable not set!\n";
    exit(1);
}

$test = in_array('test', $argv);
if ($test)
	throw new Exception('Test mode is not supported');

include(ROOT . 'common/config.php');
include(ROOT . 'common/includes.inc.php');

set_include_path (
	get_include_path() . PATH_SEPARATOR .
	ROOT . 'betting-service/application/' . PATH_SEPARATOR .
	ROOT . 'betting-service/library/'
);

require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');
It6_Log::initialize();
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN);
$dbAdmin = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);
$dbSes = Zend_Controller_Plugin_DbPLugin::initDbConnection('dbSes', Zend_Controller_Plugin_DbPLugin::CONFIG_SESSION);
$db->query('SET NAMES utf8');
// $db->query("SET time_zone='GMT'");


$GLOBALS['LOG_CONFIG'] = array(
	'mainlog' => array(
		'writerName' => 'Stream',
		'writerNamespace' => 'It6_Log_Writer',
		'writerParams' => array(
			'stream'   => ROOT.'errorlog/betting-service-%F.log'
		),
	),
);

$ws = new It6_WS(It6_WS::DIRECT, null);

It6_DbTransaction::begin($db);
It6_DbTransaction::begin($dbAdmin);
try {

	$users = $ws->User->getAllWhere(array(
		'activationTime IS NOT NULL',
		'anonymous = 0'
	));

	foreach ( $users as $user ) {
		$row = $db->select()
			->from('ticket','SUM(castka) AS amount')
			->where('zruseno = 0')
			->where('user_id = ?', $user['userId'])
			->query()
			->fetch();
		
		$ws->Campaign->putOn(
			'UserActivation',
			'get',
			array('userId'  => $user['userId'])
		);
		
		if ( !empty($row['amount']) ) {
			$amount = round($row['amount'] * 0.01);
			$ws->PointsTransaction->make(array(
				'value'  => $amount,
				'userId' => $user['userId'],
				'typeId'   => BBAS_STARTING_POINTS_TRANS_TYPE
			));
			echo $user['username']."\t".$amount . "\n";
		}
	}
	
	It6_DbTransaction::commit($db);
	It6_DbTransaction::commit($dbAdmin);
} catch ( Exception $e ) {
	It6_DbTransaction::rollback($db);
	It6_DbTransaction::rollback($dbAdmin);
	
}






echo "DONE.\n";
