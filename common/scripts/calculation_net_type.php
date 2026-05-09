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
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');
$autoloader->registerNamespace('WarpTurn_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN);
$dbAdmin = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);


$row = 1;
if (($handle = fopen(ROOT."PROVIZE_ČERVENEC_2013_1_1.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        $row++;

        /*
        0 - id
		1 - calculation_net_type
		*/
		
		if ( !empty($data[0]) ) {

			//$typeValue = $data[1] == 1 ? '"NEW"' : '"OLD"';

	        $sql = 'UPDATE branch SET calculation_net_type = ' . (int)$data[1] .' WHERE branch.id = ' . (int)$data[0] . ' LIMIT 1';
	        echo $sql."\n";
			$dbAdmin->query($sql);
		}

    }

    fclose($handle);
}
