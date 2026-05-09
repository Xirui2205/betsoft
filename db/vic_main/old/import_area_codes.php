#!/usr/bin/env php
<?php

// !!! tento soubor patri do db/vic_admin/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

define('RUNNING_FROM_CLI', true);
define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'common/library/' . PATH_SEPARATOR .
		ROOT . 'betting-service/application/' . PATH_SEPARATOR .
		ROOT . 'betting-service/library/' . PATH_SEPARATOR .
		ROOT . 'admin/library/' . PATH_SEPARATOR
);

include(ROOT . 'common/config_util.inc.php');

$appEnv = getAppEnv();
if (empty($appEnv)) {
    echo "APPLICATION_ENVIRONMENT environment variable not set!\n";
    exit(1);
}

$test = in_array('test', $argv);

include(ROOT . 'common/config.php');
include(ROOT . 'common/includes.inc.php');

require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('It6_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN);
$dbAdmin = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);

if ($test)
	echo "Running in test mode, database won't be updated.\n";

try {
	$db->beginTransaction();
	$dbAdmin->beginTransaction();
	
	/*$dbAdmin->insert(
		'database_patch',
		array(
				'revision' => '1545',
				'not_reapplicable' => 1,
				'note' => 'Uprava registrace . (mantis:1236)'
			)
	);*/

	
	echo 'TEST BEGIN.\n';
	
	$handle = fopen(dirname(__FILE__) . '/import_data/predcisli.csv', 'r');
	while($line = fgetcsv($handle, 1024, ";")) {
		if(Count(explode(', ',$line[1]))>1)
		{
			foreach (explode(', ', $line[1]) as $code){
				$db->insert(
						'area_codes',
						array(
								'area' => $line[0],
								'code' => "+".$code
						)
				);
				echo "INSERTED AREA:$line[0] WITH CODE:$code\n";
			}
		}
		else{
			$db->insert(
			'area_codes',
				array(
						'area' => $line[0],
						'code' => "+".$line[1]
						)	
			);
			echo "INSERTED AREA:$line[0] WITH CODE:$line[1]\n";
		}
	}
	fclose($handle);

	if ($test) {
		$db->rollback();
		$dbAdmin->rollback();
	} else {
		$db->commit();
		$dbAdmin->commit();
	}
		
	echo "COMPLETED SUCCESSFULLY.\n";
}
catch (Exception $e) {
	echo $e->getMessage();
	$db->rollback();
	$dbAdmin->rollback();
}
