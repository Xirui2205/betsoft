<?php
// 

define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');
//var_dump(ROOT);
include(ROOT . 'common/includes.inc.php');
require_once('Zend/Loader/Autoloader.php');
$autoloader = Zend_Loader_Autoloader::getInstance();


//include '/opt/DocumentRoot/web/application/views/scripts/deposit-feedback/muzo.phtml';

error_reporting();

$controller = new DepositFeedbackController();

$controller->muzoAction();