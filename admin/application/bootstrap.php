<?php

error_reporting(E_ALL|E_STRICT);

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

//Pridal Martin aby mohl vypnout cachovani v Promo. 15.4. 2010
define('CACHING', 'off');


//TODO this should be loaded dynakmicli by user profile
//FIXME LC_ALL chnges the decimal point to comma after calling floatval
//TODO this is not acceptable way how to set locale (who can read let's see PHP manual)
//setlocale(LC_TIME,'cs_CZ.utf8');
//date_default_timezone_set('Europe/Prague');

require_once 'common/config.php';
require_once 'common/class/class.Help.php';
require_once 'common/class/class.Constant.php';
require_once 'common/class/Ip.php';
//require_once 'common/class/class.Session.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader::loadClass('Zend_Debug');
Zend_Loader::loadClass('Zend_Controller_Front');

$autoloader = Zend_Loader_Autoloader::getInstance();
Zend_Registry::set('autoloader', $autoloader);
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('DecoratorForms_');
$autoloader->pushAutoloader(new It6_AutoloaderAdmin());

//TODO: use user's language or language from session
Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
Zend_Registry::set('langId', 1);
date_default_timezone_set('Europe/Prague');
mb_internal_encoding('UTF-8');

It6_Log::initialize();



//$form = Models_Branch::getBranchInfoForm();
$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
//TODO: use constant from config instead of class constants It6_WS::* here
Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));

// FirePHP log; usage: Zend_Registry::get('fl')->info($myVariable);
Zend_Registry::set('fl', new Zend_Log(new Zend_Log_Writer_Firebug()));

//$autoloader->autoload('Models_Session_SesClass');
//$autoloader->autoload('Models_Session_Session');
//$autoloader->autoload('Models_Helpers_Help');

/*
session_set_save_handler (
    array("Models_Session_Session", "open"),
    array("Models_Session_Session", "close"),
    array("Models_Session_Session", "read"),
    array("Models_Session_Session", "write"),
    array("Models_Session_Session", "destroy"),
    array("Models_Session_Session", "gc"));
*/

session_set_save_handler (
	array("It6_Session_Admin", "open"),
	array("It6_Session_Admin", "close"),
	array("It6_Session_Admin", "read"),
	array("It6_Session_Admin", "write"),
	array("It6_Session_Admin", "destroy"),
	array("It6_Session_Admin", "gc")
);

register_shutdown_function("session_write_close");

$layout = Zend_Layout::startMvc(array('layoutPath' => ROOT . 'admin/application/views/layouts'));

$frontController = Zend_Controller_Front::getInstance();

$route = new Zend_Controller_Router_Route(
         ':lang/:controller/:action/*',
         array('controller'=>'index',
               'action' => 'index',
               'module'=>'default',
               'lang'=>'browser'));
$router = $frontController->getRouter();
$router->addRoute('default', $route);
$frontController->setRouter($router);

$frontController->registerPlugin(new Zend_Controller_Plugin_DbPLugin())
                ->registerPlugin(new It6_Controller_Plugin_SetController());

$frontController->throwExceptions(true);
$frontController->setControllerDirectory(ROOT . 'admin/application/controllers/');
$layout->getView()->addHelperPath('It6/View/Helper', 'It6_View_Helper_');

// Laděnka
/*require ROOT.'common/library/tracy/src/tracy.php';
use Tracy\Debugger;
if (getAppEnv() !== 'BBAS_PRODUCTION') {
	Debugger::enable();
}*/

try {
	$frontController->dispatch();
}
catch (Exception $e) {
}

//SesClass::close();
It6_Session_Admin::end();

if (isset($e))
	throw $e;
