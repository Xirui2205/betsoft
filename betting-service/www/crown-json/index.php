<?php
session_start();
if(!defined("ROOT")) define("ROOT", dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/');

set_include_path (
	get_include_path() . PATH_SEPARATOR .
	ROOT . 'common/library/' . PATH_SEPARATOR .
	ROOT . 'betting-service/application/' . PATH_SEPARATOR .
	ROOT . 'betting-service/library/' . PATH_SEPARATOR .
	ROOT . 'admin/library/' . PATH_SEPARATOR
);

header("Content-Type: application/json; charset=UTF-8");

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');
require_once(ROOT.'admin/config_local.php');
require_once(ROOT.'web/config_local.php');
require_once ROOT.'common/config.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';

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

$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
$admindb = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);

Zend_Registry::set('translate', new It6_Translate_Ws());

// Instantiate server, etc.
$server = new Zend_Json_Server();
$server->setClass('Webservice_CrownRaw');

try {
	echo $server->handle();
} catch (Exception $e) {
	// handle errors
}