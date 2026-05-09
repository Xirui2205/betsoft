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
if (($handle = fopen(ROOT."/db/newletters_registrace/Adresy_nova_registrace1.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        $row++;

        if ( $data[3] != 'Email' && !empty($data[3]) ) {

            $sql = 'SELECT * FROM uzivatel WHERE email="'.$data[3].'"';
            $uzivatel = $db->query($sql)->fetchAll();
            echo "\n" . $data[3] . "\n";
            if ( count($uzivatel) > 0 ) {
	            echo "( " . $uzivatel[0]['jmeno'] . " " . $uzivatel[0]['prijmeni'] . " )\n";
            	echo "datum registrace: ". $uzivatel[0]['datum_registrace']. "\n";
            	echo "datum aktivace: ". $uzivatel[0]['datum_aktivace'] . "\n";

	            $sql = 'SELECT * FROM ticket WHERE user_id="'.$uzivatel[0]['user_id'].'"';
	            $tickets = $db->query($sql)->fetchAll();

	            if ( count($tickets) > 0 ) {
	            	$castka = $vyplacen = 0;
	            	foreach ($tickets as $ticket) {
            			$castka = $castka + $ticket['castka'];
            			if ( $ticket['vyplacen'] == 1 ) {
            				$vyplacen = $vyplacen + $ticket['win_real'];
            			}
	            	}
            		echo "vsazeno: ". count($tickets). " tiketu\n";	            		
         	  		echo "vsazena castka: ". number_format($castka, 2, ',', ' '). " Kč\n";	            		
         	  		echo "vyplaceno: ". number_format($vyplacen, 2, ',', ' '). " Kč\n";	            		
	            } else {
            		echo "--- uzivatel jeste nesazel --- \n";	            	
	            }

 			} else {
            	echo "--- uzivatel nenalezen v databazi --- \n";
 			}
        
        }
    }
    fclose($handle);
}