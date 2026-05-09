<?php

set_time_limit(0);
date_default_timezone_set('Europe/Prague');

define('ROOT', dirname(dirname(__FILE__)) . '/');

require_once(ROOT . 'bimportd/config_local.php');
include(ROOT.'common/includes.inc.php');
require_once(ROOT . 'common/config.php');
require_once(ROOT . 'common/class/class.Constant.php');
require_once(ROOT . 'common/library/Zend/Loader.php');
require_once(ROOT . 'common/library/Zend/Loader/Autoloader.php');

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');
Zend_Registry::set('autoloader', $autoloader);

include_once(ROOT.'common/init-global-cache.inc.php');

$dbPlugin = new Zend_Controller_Plugin_DbPLugin();
$db = $dbPlugin->initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
$dbAdmin = $dbPlugin->initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);
//$logdb = Zend_Controller_Plugin_DbPLugin::initMongoDbConnection('logdb', Zend_Controller_Plugin_DbPLugin::CONFIG_LOG, false);
Zend_Registry::set('db', $db);
Zend_Registry::set('zdb_game', $db);
Zend_Registry::set('admindb', $dbAdmin);

$ws = new It6_WS(It6_WS::DIRECT);
Zend_Registry::set('ws', $ws);
It6_Log::initialize();

$translate = new It6_Translate_Admin(1, $db);
Zend_Registry::set('translate', $translate);

include_once(ROOT.'common/init-mail-transport.php');

try {
	$file = $argv[1];
	$br = new It6_Betradar_Import();

//It6_Log::info('ojojds')

	$result = $br->importFile($file);
	if (!$result) {
		if (file_exists($file)) {
			$newPath = BETRADAR_IMPORT_ERROR;
			if ('/' != substr($newPath, -1)) {
				$newPath .= '/';
			}
			$newPath .= basename($file);
			rename($file, $newPath);
		}
	}
}
catch(Exception $e){
	It6_Log::err('Import failed.', It6_Log::TAG_BETRADAR_OPERATION, null, $e);
}
