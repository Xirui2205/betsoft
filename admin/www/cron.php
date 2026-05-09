<?php
/* slouží ke spouštění metod pro cron
 * 
 * příklad url pro spuštění metody:
 * http://admin.testbook.cz/cron.php?run=CurrencyRates
 */

// inicializace

if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');
header("Content-type:text/html; charset=UTF-8");
set_time_limit(0);
require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');
require_once(ROOT.'admin/config_local.php');
include "common.php";
include "template/class.TemplatePower.inc.php";
error_reporting(E_ALL|E_STRICT);

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

define('CACHING', 'off');

require_once 'common/config.php';
require_once 'common/class/class.Help.php';
require_once 'common/class/class.Constant.php';
require_once 'common/class/Ip.php';
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

Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
Zend_Registry::set('langId', 1);
date_default_timezone_set('Europe/Prague');
mb_internal_encoding('UTF-8');

It6_Log::initialize();

$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));

$dbPlugin = new Zend_Controller_Plugin_DbPLugin();
$db = $dbPlugin->initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
$dbAdmin = $dbPlugin->initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);
Zend_Registry::set('db', $db);
Zend_Registry::set('zdb_game', $db);

// -----------------------------------------------------------------------------

if (!isset($_GET['run'])) {
	exit;
}

switch ($_GET['run']) {
	case 'CurrencyRates':
		$ccru = new It6_Cron_Job_CurrencyRatesUpdate();
		$ccru->execute();
		exit('CurrencyRates - done');

	default:
		break;
}