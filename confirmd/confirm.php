<?php

set_time_limit(0);
date_default_timezone_set('Europe/Prague');

define('ROOT', dirname(dirname(__FILE__)) . '/');



require_once(ROOT . 'confirmd/config_local.php');
include(ROOT.'common/includes.inc.php');
require_once(ROOT . 'common/config.php');
require_once(ROOT . 'common/class/class.Constant.php');
require_once('Zend/Loader.php');
require_once('Zend/Loader/Autoloader.php');

$autoloader = Zend_Loader_Autoloader::getInstance();
Zend_Registry::set('autoloader', $autoloader);
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');

include_once(ROOT.'common/init-global-cache.inc.php');

//require_once(ROOT . 'common/class/class.I18n.php');

It6_Log::initialize();

ini_set('short_open_tag',1);

$dbPlugin = new Zend_Controller_Plugin_DbPLugin();
$db = $dbPlugin->initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
$dbSes = $dbPlugin->initDbConnection('dbSes', Zend_Controller_Plugin_DbPLugin::CONFIG_SESSION, false);
$dbAdmin = $dbPlugin->initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);
//$logdb = Zend_Controller_Plugin_DbPLugin::initMongoDbConnection('logdb', Zend_Controller_Plugin_DbPLugin::CONFIG_LOG, false);

$acl = It6_Acl_Factory::newAcl(array(
	'adminDb' => $dbAdmin,
));
Zend_Registry::set('acl', $acl);

/*
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
$ws = new It6_WS(WS_WRAPPER, $client);
Zend_Registry::set('ws', $ws);
*/

$ws = new It6_WS(It6_WS::DIRECT);
Zend_Registry::set('ws', $ws);

$wd = ROOT . 'confirmd/';
$log = ROOT . 'errorlog/confirmd-activity.log';
$counter = 60;
$statusToWatch = array(
	It6_Models_Ticket::COUPON_STATUS_MARKED_AS_IN_ACCEPTATION,
	It6_Models_Ticket::COUPON_STATUS_MARKED_AS_ACCEPTED,
	It6_Models_Ticket::COUPON_STATUS_MARKED_AS_REJECTED,
	It6_Models_Ticket::COUPON_STATUS_IN_ACCEPTATION,
	It6_Models_Ticket::COUPON_STATUS_PROLONGED,
	It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_ACCEPTED,
	It6_Models_Ticket::COUPON_STATUS_MARKED_AS_MODIFIED_REJECTED,
	It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_ACCEPTED,
	It6_Models_Ticket::COUPON_STATUS_MARKED_AS_DELAYED_REJECTED,
);

$tStart = time();
$sStart = strftime("%Y-%m-%d %H:%M:%S", $tStart);
file_put_contents($log, "$sStart START\n");
try {
	$db->getConnection();
	$couponIds = $ws->Coupon->getCouponsWithStatusAndAdmin($statusToWatch);
	foreach ($couponIds as $couponId) {
		It6_DbTransaction::begin($db);
		It6_DbTransaction::begin($dbAdmin);
		try {
			$dbAdminCommited = false;
			$cancelation = $ws->Coupon->isCancelationRequested($couponId);
			if (empty($cancelation)) {
				$creationTime = time();
				$status = $ws->Ticket->checkAuthorisationProcess($couponId, $creationTime);
				if (It6_ArrayWrapper::isArray($status)) {
					$ws->Coupon->updateStatus($couponId, It6_Models_Ticket::COUPON_STATUS_INTERRUPTED_ACCEPTATION, true, 'Validation failed.');
					It6_Log::debug('Validation failed.', It6_Log::TAG_TICKET_APPROVAL, array('couponId' => $couponId, 'errors' => $status));
				}
			}
			else {
				$ws->Coupon->cancel($cancelation);
			}
			It6_DbTransaction::commit($dbAdmin);
			$dbAdminCommited = true;
			It6_DbTransaction::commit($db);
		}
		catch (Exception $e) {
			if (empty($dbAdminCommited))
				It6_DbTransaction::rollback($dbAdmin);
			else
				It6_Log::emerg('Partial rollback', It6_Log::TAG_TICKET_APPROVAL);
			It6_DbTransaction::rollback($db);
			$ws->Coupon->updateStatus($couponId, It6_Models_Ticket::COUPON_STATUS_INTERRUPTED_ACCEPTATION, true, $e->getMessage());
			It6_Log::err($e->getMessage());
		}
	}
	$cancelations = $ws->Coupon->getCancelationRequests();
	foreach ($cancelations as $cancelation) {
		$ws->Coupon->cancel($cancelation);
	}
	//TODO: delete old cancelation requests
}
catch (Exception $e) {
	It6_Log::err($e->getMessage());
}
$tEnd = time();
$sEnd = strftime("%Y-%m-%d %H:%M:%S", $tEnd);
file_put_contents($log, "$sEnd END\n", FILE_APPEND);
if ($tEnd - $tStart > 10)
	file_put_contents(
		ROOT . 'errorlog/confirmd-longrun.log',
		"$sStart - $sEnd : " . ($tEnd - $tStart) . " s\n",
		FILE_APPEND
	);
