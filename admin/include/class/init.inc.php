<?php

header("Content-type:text/html; charset=UTF-8");

include ROOT . 'common/includes.inc.php';
include ROOT . 'admin/config_local.php';
include "common.php";

$dbAdmin = It6_Controller_Plugin_SetController::connectDbAdmin();

session_set_save_handler (
	array("It6_Session_Admin", "open"),
	array("It6_Session_Admin", "close"),
	array("It6_Session_Admin", "read"),
	array("It6_Session_Admin", "write"),
	array("It6_Session_Admin", "destroy"),
	array("It6_Session_Admin", "gc")
);
It6_Session_Admin::start($dbAdmin);

Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
$acl = It6_Acl_Factory::newAcl(array(
	'adminDb' => $dbAdmin, 'adminId' => It6_Session_Admin::getUserData('id')
));
Zend_Registry::set('acl', $acl);
$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));
