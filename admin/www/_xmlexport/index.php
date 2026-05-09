<?php
if(!defined("ROOT")) define("ROOT",dirname(dirname( dirname( dirname(__FILE__) ) ) ) . '/');

set_time_limit(0);
error_reporting(E_ALL);

//if(!defined("ROOTXML")) define("ROOTXML","/var/www/html/admin/include/");
include(ROOT.'admin/config_local.php');

$STRICT_ERRORS_FILTER = array(
	'filterName' => 'RegExp',
	'filterNamespace' => 'It6_Log_Filter',
	'filterParams' => array(
		'pattern' => array(
			'/\bDB::.*should not be called statically/',
			'/\bSesClass::.*should not be called statically/',
			'/\bmain::.*should not be called statically/',
			'/\bPEAR::.*should not be called statically/',
			//'/^Only variables should be assigned by reference$/',
			//'/^Undefined index: /',
			//'/^Undefined offset: /',
			//'/^Undefined variable: /',
			'/^Directive \'magic_quotes_gpc\' is deprecated/',
		)
	)
); 

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
			'stream'   => ROOT.'errorlog/betradar-export-%F.log'
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
Zend_Loader::loadClass('Zend_Registry'); 
try{
   $ob = new XMLExportServer();
   $ob->PrepareData();
}
catch(exHandler $e){
   $e->produceAllError();
}
