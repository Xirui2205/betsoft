<?php
set_time_limit(120);
if(!defined("ROOT")) define("ROOT", dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/');

define('LIVE_CLIENT', 1);

set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'common/library/' . PATH_SEPARATOR .
		ROOT . 'betting-service/application/' . PATH_SEPARATOR .
		ROOT . 'betting-service/library/' . PATH_SEPARATOR .
		ROOT . 'admin/library/' . PATH_SEPARATOR
);

//header("Content-type:text/json; charset=UTF-8");

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');

require_once(ROOT.'betting-service/config_local.php');
require_once ROOT.'common/config.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
error_reporting(0);

//TODO this should be loaded dynakmicli by user profile
//TODO this is not acceptable way how set locale (who can read let's see PHP manual)
//setlocale(LC_ALL,'cs_CZ.utf8');
//date_default_timezone_set('Europe/Prague');

$autoloader = Zend_Loader_Autoloader::getInstance();
Zend_Registry::set('autoloader', $autoloader);
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('It6_');
$autoloader->pushAutoloader(new It6_Autoloader());

//TODO: use user's language or language from session
Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
date_default_timezone_set('Europe/Prague');

include_once ROOT . 'common/init-global-cache.inc.php';

It6_Log::initialize();
//It6_Log::emerg("test",It6_Log::TAG_PHP);

$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
$admindb = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);
$dbSes = Zend_Controller_Plugin_DbPLugin::initDbConnection('dbSes', Zend_Controller_Plugin_DbPLugin::CONFIG_SESSION, false);

Zend_Registry::set('translate', new It6_Translate_Ws());

$client = new Krixon_JsonRpc_Client("https://svc-compbet.com/services/funds.php");
//$client = new Krixon_JsonRpc_Client("http://svc.testbook.cz/services/funds.php");
// $client = new Krixon_JsonRpc_Client("https://admin.betservice.bbas.cz/_isp_export/funds.php");
try { 
	/*$result = $client->call("fundHost", array(array(
		array(
		"amount" => "15000.36", 
		"hostId" => 391,
		"transactionIspId" => 100005
		),
		array(
		"amount" => -5000, 
		"hostId" => 392,
		"transactionIspId" => 100006
		))));*/
	//$result = $client->call("getBranchLiveBalancing", array(5018, null, null));
	$result = $client->call('getCalculation2', array(5039, '2013-01-01', '2013-02-20', true));
	//$result = $client->call("getTransactionStatus", array(array(135)));
}
catch(SoapFault $e){
	echo $e->getMessage();
}
//$e = $client->call('getBetResults', array(10));

//var_dump($client->getLastRequest());
print_r($result);
/*
$branches = (array) $result->result;

foreach ($branches as $branchId => $branch) {

	foreach($branch->hosts as $hostId => $host){
			$balance = $host->startBalance + $host->ticketAmount - $host->stornoTicketAmount + $host->deposits + $host->userDeposits - $host->withdraws - $host->userWithdraws - $host->collectTicketAmount;
			if ( abs($host->endBalance - $balance) > 0)
				sprintf("hostId: %d - expectedEndBalance: %d - realEndBalance: %d", $hostId, $balance, $host->endBalance);
	}

}
*/