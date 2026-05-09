<?php


date_default_timezone_set('Europe/Prague');

require_once(ROOT . 'cronjob/config_local.php');
require_once ROOT . 'common/config.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
require_once 'common/class/class.Help.php';

Zend_Loader::loadClass('Zend_Debug');
Zend_Loader::loadClass('Zend_Controller_Front');

$autoloader = Zend_Loader_Autoloader::getInstance();
Zend_Registry::set('autoloader', $autoloader);
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');

require_once(ROOT . 'common/class/class.I18n.php');

Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));

It6_Log::initialize();

ini_set('short_open_tag',1);

$client = null;
if (It6_WS::XML_RPC == WS_WRAPPER) {
	$sslAdapter = new Zend_Http_Client_Adapter_Socket();
	$sslAdapter->setStreamContext(array(
		'ssl' => array('local_cert' => SSL_CERT, 'passphrase' => SSL_CERT_PASSWD)
	));
	$httpClient = new Zend_Http_Client();
	$httpClient->setAdapter($sslAdapter);
	$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL, $httpClient);
	//$client->setSkipSystemLookup(true);
}
Zend_Registry::set('ws',new It6_WS(WS_WRAPPER, $client));

// FirePHP log; usage: Zend_Registry::get('fl')->info($myVariable);
Zend_Registry::set('fl', new Zend_Log(new Zend_Log_Writer_Firebug()));

$dbPlugin = new Zend_Controller_Plugin_DbPLugin();
$dbPlugin->connect();

$acl = It6_Acl_Factory::newAcl(array(
	'adminDb' => Zend_Registry::get('admindb'),
));
Zend_Registry::set('acl', $acl);

include_once(ROOT.'common/init-mail-transport.php');

$dispatcher = new It6_Cron_Job_Dispatcher();
$dispatcher->dispatch();
