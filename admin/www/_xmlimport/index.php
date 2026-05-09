<?php
if(!defined("ROOT")) define("ROOT",dirname(dirname( dirname( dirname(__FILE__) ) ) ) . '/');

set_time_limit(0);
error_reporting(E_ALL);

include(ROOT.'admin/config_local.php');
include_once(ROOT . 'bimportd/config_local.php');

$LOG_CONFIG = array(
	"phpOutput" => array(
		'filterParams' => array(
			'filters' => array(
				$STRICT_ERRORS_FILTER
			)
		)
	),
	"mainlog" => array(
		'writerParams' => array(
			'stream'   => ROOT.'errorlog/betradar-postxml-%F.log'
		),
		'filterParams' => array(
			'filters' => array(
				$STRICT_ERRORS_FILTER
			)
		)
	)
);

include(ROOT.'common/includes.inc.php');
include('common.php');

$dbPlugin = new Zend_Controller_Plugin_DbPLugin();
$db = $dbPlugin->initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
$dbAdmin = $dbPlugin->initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);
Zend_Registry::set('db', $db);
  
Zend_Loader::loadClass('Zend_Registry');

It6_Log::initialize();

try {
	$input = file_get_contents('php://input');
	$xmlData = urldecode($input);
	
	$ord = 0;
	$fName = 'xml_' . strftime('%Y-%m-%d-%H-%M-%S') .'_0.xml';
	while(file_exists(BETRADAR_IMPORT_QUEUE.$fName)) {
		$fName = 'xml_' . strftime('%Y-%m-%d-%H-%M-%S') .'_' . $ord++ . '.xml';
	}
	
	if (file_put_contents(BETRADAR_IMPORT_TMP.$fName, $xmlData, LOCK_EX)) {
		It6_Log::notice(
			"Betradar XML POST received and saved %file%",
			It6_Log::TAG_BETRADAR_OPERATION,
			array('file' => $fName)
		);
		if (copy(BETRADAR_IMPORT_TMP.$fName,BETRADAR_IMPORT_QUEUE.$fName)) {
			unlink(BETRADAR_IMPORT_TMP.$fName);
		}
	} else {
		It6_Log::warn(
			"Betradar XML POST saving error %file%",
			It6_Log::TAG_BETRADAR_OPERATION,
			array('file' => $fName)
		);
	}
}
catch(exHandler $e){
	throw new Exception($e);
}
