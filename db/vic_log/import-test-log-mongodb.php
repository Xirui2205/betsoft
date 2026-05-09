#!/usr/bin/env php
<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI


define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');
define('BULK_SIZE',10000);

include(ROOT . 'common/config_util.inc.php');

$appEnv = getAppEnv();
if (empty($appEnv)) {
    echo "APPLICATION_ENVIRONMENT environment variable not set!\n";
    exit(1);
}
        
include(ROOT . 'common/config.php');
include(ROOT . 'common/includes.inc.php');
        
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('It6_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);

$m = new Mongo();
$m->vic_log->drop();
$logdb = $m->vic_log;
$log = $logdb->log;
$log->ensureIndex(array('log_id' => 1));
$log->ensureIndex(array('tag' => 1));
$log->ensureIndex(array('ip' => 1));
$log->ensureIndex(array('user' => 1));
$log->ensureIndex(array('admin_id' => 1));
$log->ensureIndex(array('host_id' => 1));
$log->ensureIndex(array('bet_id' => 1));
$log->ensureIndex(array('ticket_id' => 1));
$log->ensureIndex(array('coupon_id' => 1));
$log->ensureIndex(array('priority' => 1));
$log->ensureIndex(array('time' => -1));
$log->ensureIndex(array('message' => 1));
$log->ensureIndex(array('args' => 1));
$log->ensureIndex(array('exception' => 1));
$log->ensureIndex(array('file' => 1));
$log->ensureIndex(array('line' => 1));
                
try {
    //$logdb->dbCreate('log');
    
    //$dbs = $logdb->allDbs();
    
    //var_dump($dbs);
    
    //$result = $logdb->view('log','admin',null,array('key'=>1));

    

    echo "Selecting log...";
    $query = $db->select()
	->from('log')
	->limit(300000)
	->query();
	
    $i = 0;
    $docSet = null;
    echo "done\n";
    while ( $row = $query->fetch()  ) {
		if ( 0 == $i % BULK_SIZE ) {
			if ( isset($start) ) {                                                                                                                                                                     
				$f = floatval(BULK_SIZE)/(microtime(true) - $start);
				printf("Rows converted: %d, freq: %d Hz\r",$i,intval($f));
			}
			$start = microtime(true);
		}

		$row['time'] = new MongoDate(strtotime($row['time']));
		$log->insert($row);
		
		++$i;
	}
    
    echo "\n\nCOMPLETED SUCCESSFULLY.\n";
} catch ( Exception $e ) {
    echo "\tERROR...rolling back\n";
	
    throw $e;
}
    
