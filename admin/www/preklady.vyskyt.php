<?php

/*
 * Řešení výskytu překladových frází za účelem, aby se nemusela překládat část
 * administrace, pokud to nebude potřeba.
 * Nevýhoda u obecných frází: nevíme, jestli jde o volání přes slovník...
 * 
 * zápis do preklady.vyskyt
 * 0: nenalezeno - nebo se bere přes DB!
 * 1: web nebo společné části (i admin)
 * 2: jen admin - o to hlavně jde, určeno k odfiltrování při tvorbě csv
 * 
 * Tento skript lze spouštět jednorázově před generováním csv nebo cronem.
 * (Běží dlouho.)
 * 
 */
$startTime = time();
// ini -------------------------------------------------------------------------
if (!defined("ROOT"))
	define("ROOT", dirname(dirname(dirname(__FILE__))) . '/');
header("Content-type:text/html; charset=UTF-8");
set_time_limit(0);
require_once(ROOT . 'common/includes.inc.php');
include_once(ROOT . 'common/init-global-cache.inc.php');
require_once(ROOT . 'admin/config_local.php');
include "common.php";
include "template/class.TemplatePower.inc.php";
error_reporting(E_ALL | E_STRICT);

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
ini_set('max_execution_time', 4000);
ini_set('max_execution_time', 0); //0=NOLIMIT
header('Content-Type: text/html; charset=utf-8');
$paths = array(
	1 => array(
		'web',
		'common',
		'betting-service',
		'bimportd',
		'confirmd',
		'cronjob'
	),
	2 => array('admin')
);
$indexArray = $db
		->select()
		->distinct()
		->from('preklady', 'index_pole')
		->order('preklad_id DESC')
		->query()
		->fetchAll(Zend_Db::FETCH_COLUMN);
$processed = 0;
$inAdminCnt = 0;
foreach ($indexArray as $index) {
	$inWeb = false;
	$inAdmin = false;
	$value = 0;
	foreach ($paths as $pathValue => $pathArray) {
		foreach ($pathArray as $subPath) {
			$currPath = ROOT . $subPath;
			$command = 'grep -risw "' . $index . '" ' . $currPath
					. " | head -n1"; // zastaví procházení při prvním výskytu
			echo "$command<br>";
			if (exec($command) !== '') {
				$inWeb = $pathValue === 1;
				$inAdmin = $pathValue === 2;
			}
			if ( in_array($subPath, $paths[1]) && $inWeb) {
				break 2; // je to na webu, zapíšeme a jdeme na další frázi
			}
		}
	}
	if ($inWeb) {
		$value = 1;
	} else if ($inAdmin) {
		$value = 2;
		$inAdminCnt++;
	}
	$db->update('preklady', array('vyskyt' => $value), array('index_pole' => $index));
	echo "<strong>$value</strong> | $index<br>";
	$processed++;
	/*if ($processed == 15) {
		break;
	}*/
}
echo "done ($processed, admin: $inAdminCnt), ". (time() - $startTime)/60 . " minutes";
