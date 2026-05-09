<?php
if(!defined("ROOT")) define("ROOT",dirname( dirname( dirname(__FILE__) ) ) . '/');

header("Content-type:text/html; charset=UTF-8");
set_time_limit(908);
error_reporting(E_ALL);

include(ROOT.'common/includes.inc.php');

include 'admin/config_local.php';
include "common.php";
include "template/class.TemplatePower.inc.php";
require_once 'common/class/class.Constant.php';
require_once 'common/config.php';
date_default_timezone_set('Europe/Prague');

#Spojeni Zend#
require_once 'Zend/Db.php';
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader::loadClass('Zend_Db_Table');
Zend_Loader::loadClass('Zend_Registry');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('It6_');
$autoloader->pushAutoloader(new It6_AutoloaderAdmin());

$dbWeb = It6_Controller_Plugin_SetController::connectDbWeb();
Zend_Registry::set('db', $dbWeb);
$dbAdmin = It6_Controller_Plugin_SetController::connectDbAdmin();
Zend_Registry::set('admindb', $dbAdmin);
It6_Controller_Plugin_SetController::connectDbSession();
Zend_Registry::set('translate', new It6_Translate_Admin(1, $dbWeb));

$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));

try{
   
	if(isset($_GET['work']) && $_GET['work']==6)
	  $ob = new AjaxServerLiveBet();
    else if(isset($_GET['work']) && $_GET['work']==100){
	  $ob = new Online();
	  $ob->Scanning();
    }
    else if(isset($_GET['work']) && $_GET['work']==8){
	  $ob = new BetRadarLive();
	  $ob->findAction();
    }
	else{
      $ob = new AjaxServer();
	}
}
catch(exHandler $e){

   $e->produceAllError();

}

