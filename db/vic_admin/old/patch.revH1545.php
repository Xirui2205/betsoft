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

$kmeny = array();
$kmeny[1] = 'V.I.P.';
$kmeny[2] = 'Lukáš Belo';
$kmeny[3] = 'Bohouš Kopecký';
$kmeny[4] = 'Josef Plucha';
$kmeny[5] = 'Petr Jadrníček';
$kmeny[6] = 'Radim Veselý';

try {
	$db->beginTransaction();
	$dbAdmin->beginTransaction();
	
	$dbAdmin->insert(
		'database_patch',
		array(
				'revision' => '1545',
				'not_reapplicable' => 1,
				'note' => 'Kmeny k pobocce. (mantis:1054,1055,1056)'
			)
	);

	
	echo 'TEST BEGIN.\n';
	
	foreach ($kmeny as $id => $name) {
		$dbAdmin->insert(
			'stem',
			array(
					'id' => $id,
					'name' => $name,
				)
			
		);
		echo "INSERTED stem $id with name $name.\n";
	}
	
	print 'ALL STEMS INSERTED!';
	
	$handle = fopen(dirname(__FILE__) . '/import_data/import_patch.revH1545.csv', 'r');
	while($line = fgetcsv($handle, 1024)) {
		$dbAdmin
			->update('branch',array('stem_id' => $line[3]), array('id = ?' => $line[0]));
			echo "UPDATED BRANCH_ID:$line[0] WITH STEM_ID:$line[3]\n";
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
