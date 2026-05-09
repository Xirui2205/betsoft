<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

include(ROOT . 'common/config_util.inc.php');

$appEnv = getAppEnv();
if (empty($appEnv)) {
	echo "APPLICATION_ENVIRONMENT environment variable not set!\n";
	exit(1);
}

$test = in_array('test', $argv);

include(ROOT . 'common/config.php');
include(ROOT . 'common/includes.inc.php');

set_include_path(
	get_include_path()
	. PATH_SEPARATOR . ROOT . 'betting-service/application'
	. PATH_SEPARATOR . ROOT . 'betting-service/library'
);


require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Entities_');
$autoloader->registerNamespace('Webservice_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN);
$dbAdmin = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);
$db->query('SET NAMES utf8');
// $db->query("SET time_zone='GMT'");

if ($test)
	echo "Running in test mode, database won't be updated.\n";

$rows = $db->select()->from('uzivatel', array('userId' => 'user_id', 'username' => 'nick'))
	->where('anonymous=0')
	->where('entry_bonus_applied IS NULL')
	->query()
	->fetchAll();

$okCount = 0;
$errCount = 0;
foreach ($rows as $row) {
	$userId = $row['userId'];
	$username = $row['username'];
	echo "Processing user: $username (#$userId) ... ";
	$result = 'OK';
	try {
		if (!$test) {
			Webservice_Campaign::recomputeUserEntryBonusBalances($userId);
		}
		++$okCount;
	}
	catch (Exception $e) {
		$result = "ERROR: " . $e->getMessage();
		++$errCount;
	}
	echo "$result\n";
}

echo "Counts: OK=$okCount ERROR=$errCount TOTAL=" . ($okCount + $errCount) . "\n";
echo "DONE.\n";
