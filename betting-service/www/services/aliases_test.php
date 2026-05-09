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

$alias = '1248';
$oddsTypeAliasId = '1';
$bet = Webservice_Bet::getByAlias($alias, $oddsTypeAliasId);
$ret = Webservice_Bet::getByAliasWithCorrelated($alias, $oddsTypeAliasId);
echo "<b>Test s hodnotami - alias: ".$alias.", alias typu: ".$oddsTypeAliasId."<br /></b>";
echo "====================================================================<br />";
echo "<b>Vystup fce getByAlias:</b><br />";
print_r($bet);
echo "<br /><br /><b>Vystup fce getByAliasWithCorrelated:</b><br />";
print_r($ret);
echo "<br />";
echo "<br />";

$alias = '105';
$oddsTypeAliasId = '66';
$bet = Webservice_Bet::getByAlias($alias, $oddsTypeAliasId);
$ret = Webservice_Bet::getByAliasWithCorrelated($alias, $oddsTypeAliasId);
echo "<b>Test s hodnotami noveho aliasu - rozdeleno na alias: ".$alias.", a ".$oddsTypeAliasId."<br /></b>";
echo "====================================================================<br />";
echo "<b>Vystup fce getByAlias:</b><br />";
print_r($bet);
echo "<b><br /><br />Vystup fce getByAliasWithCorrelated:</b><br />";
print_r($ret);