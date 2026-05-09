<?php
session_start();
if(!defined("ROOT")) define("ROOT", dirname( dirname( dirname( dirname(__FILE__) ) ) ) . '/');

define('LIVE_CLIENT', 1);

set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'common/library/' . PATH_SEPARATOR .
		ROOT . 'betting-service/application/' . PATH_SEPARATOR .
		ROOT . 'betting-service/library/' . PATH_SEPARATOR .
		ROOT . 'admin/library/' . PATH_SEPARATOR
);

header("Content-type:text/json; charset=UTF-8");

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');

require_once(ROOT.'admin/config_local.php');
require_once(ROOT.'web/config_local.php');
require_once ROOT.'common/config.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';

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
//$logdb = Zend_Controller_Plugin_DbPLugin::initMongoDbConnection('logdb', Zend_Controller_Plugin_DbPLugin::CONFIG_LOG, false);

Zend_Registry::set('translate', new It6_Translate_Ws());

$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));

    $acl = new It6_Acl_Admin(
        array(
            //It6_Acl::IDNAME_ADMIN => 31, // Prvni Kasa
            //It6_Acl::IDNAME_BRANCH => 2, // KASA1
            //It6_Acl::IDNAME_HOST => 100, // KASA1 host 1

            It6_Acl::IDNAME_ADMIN => It6_Models_Admin::ID_INTERNET_LIVE,
            It6_Acl::IDNAME_BRANCH => It6_Models_Branch::ID_INTERNET_LIVE,
            It6_Acl::IDNAME_HOST => It6_Models_Host::ID_INTERNET_LIVE,
        ),
        array( array('name' => It6_Acl_Admin::TREE_BRANCH) ),
        $admindb
    );
    Zend_Registry::set('acl', $acl);

// Instantiate server, etc.
$server = new Zend_Json_Server();
$server->setClass('Services_LiveBettingApp');
 
 
// if ("GET" == $_SERVER["REQUEST_METHOD"]) {
//     // Indicate the URL endpoint, and the JSON-RPC version used:
//     $server->setTarget('/services/liveapp.php')
//            ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);
 
//     // Grab the SMD
//     $smd = $server->getServiceMap();
 
//     // Return the SMD to the client
//     header('Content-Type: application/json');
//     echo $smd;
//     return;
// }
 
try {
    echo $server->handle();
} catch (Exception $e) {
    // handle errors
}
