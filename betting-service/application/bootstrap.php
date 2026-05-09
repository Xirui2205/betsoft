<?php
/*
$start = microtime(true);
$fProf = fopen('/tmp/php-profile.2.log', 'a');
function prof($msg) {
	global $fProf, $start;
	fputs($fProf, (microtime(true)-$start) . " : $msg\n");
}
prof('--- START ---');
*/

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');

require_once(ROOT.'betting-service/config_local.php');
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
$logdb = Zend_Controller_Plugin_DbPLugin::initMongoDbConnection('logdb', Zend_Controller_Plugin_DbPLugin::CONFIG_LOG, false);

Zend_Registry::set('translate', new It6_Translate_Ws());

It6_Log::debug(file_get_contents('php://input'),It6_Log::TAG_WEBSERVICE_INPUT);

//TODO: get identity from request and handle exceptions
//file_put_contents('/tmp/php-debug.log', "--- START ---\n", FILE_APPEND);
try {
	$acl = It6_Acl_Factory::newAcl(array('adminDb' => $admindb, 'webDb' => $db));
}
catch (Exception $e) {
	$code = $e->getCode();
	if (empty($code)) {
		$code = It6_XmlRpc_Exception::CODE_ACCESS_DENIED;
	}
	It6_XmlRpc_Server::outputFault($e, $code);
	exit;
}
//file_put_contents('/tmp/php-debug.log', print_r($acl, true), FILE_APPEND);
/*
$acl = new It6_Acl_Admin(
	array(
		It6_Acl::IDNAME_ADMIN => 10, // Jarda Prepazka
		It6_Acl::IDNAME_BRANCH => 8, // Palmovka (homebranch)
		It6_Acl::IDNAME_HOST => 1
	),
	array('name' => It6_Acl_Admin::TREE_BRANCH),
	$admindb
);
*/
Zend_Registry::set('acl', $acl);

$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
if (!empty($hostId))
	It6_GlobalCache::setKey("SHS:$hostId", time());

if ( !Webservice_Host::isAllowed($hostId) ) {
	It6_XmlRpc_Server::outputFault(
		new It6_XmlRpc_Exception('Access denied. Host is not allowed. hostId=' . (empty($hostId) ? 'unknown' : $hostId)),
		It6_XmlRpc_Exception::CODE_ACCESS_DENIED
	);
	exit;
}

// for reusability of code, that can be called from outside WS and is using WS
Zend_Registry::set('ws', new It6_WS(It6_WS::DIRECT));

// FirePHP log; usage: Zend_Registry::get('fl')->info($myVariable);
Zend_Registry::set('fl', new Zend_Log(new Zend_Log_Writer_Firebug()));

//prof('pre server init');
$server = new It6_XmlRpc_Server();

//prof('pre request init');
$request = new Zend_XmlRpc_Request_Http();
if ($request->isFault()) {
	//TODO: invalid request
}

//prof('pre server set classes: ' . $request->getMethod() . ':' . print_r($request->getParams(), true));
$namespace = It6_XmlRpc_RequestHelper::getRequiredNamespace($request, $method);
if (!empty($namespace)) {
	$class = false;
	$methods = defined('LIVE_CLIENT')
		? $SERVICE_METHODS['livebetting']
		: $SERVICE_METHODS['supervisor'];

	foreach($methods as $_class => $classData) {
		if ($namespace == $classData[0]) {
			$class = $_class;
			break;
		}
	}
	if (false !== $class) {
		if (!It6_XmlRpc_Server_Cache::getMethodOnly($class, $server, $method)) {
			$server->setClass($class, $namespace);
			It6_XmlRpc_Server_Cache::save($class, $server);
		}
	}
	//$server->setClasses($SERVICE_METHODS['supervisor']);
	//$server->setClass('Webservice_AccessController', 'AccessController');
}

//prof('pre server handle');
echo $server->handle($request);

//prof('post server handle');

$db->closeConnection();
$admindb->closeConnection();

//fclose($fProf);
