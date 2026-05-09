<?php

define('ROOT', dirname(dirname(dirname(dirname(__FILE__)))) . '/');

include_once(ROOT . 'common/config_util.inc.php');
include_once(ROOT . 'common/includes.inc.php');
set_include_path(
	get_include_path()
	. PATH_SEPARATOR . ROOT . 'betting-service/application/'
	. PATH_SEPARATOR . ROOT . 'betting-service/library/'
);

require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Webservice_');

if (!defined('GLOBAL_CACHE_MAX_EXPIRATION')) define('GLOBAL_CACHE_MAX_EXPIRATION', 18000); // in seconds, 18000 = 5 hours
include_once(ROOT . 'common/ws-security.inc.php');
include_once(ROOT . 'common/init-global-cache-instance.inc.php');

$hostId = null;
$sslData = It6_Acl_Factory_Webservice::getSslData();
if (!empty($sslData['clientVerify']) && 'SUCCESS' == $sslData['clientVerify']) {
	if (defined('WS_SSL_TRUSTED_CA_DN')) {
		if ( in_array($sslData['clientIssuerDN'], explode("\0", WS_SSL_TRUSTED_CA_DN)) ) {
			$cnData = It6_Acl_Factory_Webservice::parseSslCertCommonName($sslData['clientSubjectDN_CN']);
			if (false !== $cnData) {
				if (It6_Acl_Factory_Webservice::CN_TYPE_BRANCH == $cnData['type']) {
					$branchId = $cnData['branchId'];
					$hostId = $cnData['hostId'];
				}
			}
		}
	}
}

if (empty($hostId)) {
	header('HTTP/1.0 503 Forbidden');
	exit;
}

include_once('init-global-cache-instance.inc.php');

$messages = Webservice_HostMessage::getNewByHostFromCache($hostId);
if (false === $messages) {
	include_once(ROOT . 'common/config.php');
	Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);
	$messages = Webservice_HostMessage::getNewByHost($hostId, false);
	$messages = Zend_Json::encode(It6_ArrayWrapper::toNativeArray($messages));
}
header('Content-Type: application/json; charset=utf-8');
echo $messages;
