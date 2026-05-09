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

function file_get_contents_curl($url) {
	    $ch = curl_init();
	 
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
	    curl_setopt($ch, CURLOPT_URL, $url);
	 
	    $data = curl_exec($ch);
	    curl_close($ch);
	 
	    return $data;
	}

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

$uzivatel = $db->select()
->from('vic_main.uzivatel', array('jmeno', 'pohlavi'))
->distinct()
->where("anonymous = 0") // AND (email = 'tomas.polz@betservice.eu' OR email = 'josef.tamok@compbet.com')") 
->query()
->fetchAll();      

foreach ($uzivatel as $u) {
	//$userName = urlencode($u['jmeno']).'+'.urlencode($u['prijmeni']);
	$osloveni = $vokativ = "";
	$prijmeni = ($u['pohlavi'] == "m") ? '+Navrátil' : '+Navrátilová';
    $json = file_get_contents_curl("http://86.49.76.195/deklinator.php?i=".urlencode(trim($u['jmeno'])).$prijmeni);
    $decoded = json_decode($json);

    if ( !isset($decoded->{'error'}) && isset($decoded->{'osloveni'}) && isset($decoded->{'vokativ'}) ) {
        $osloveni = $decoded->{'osloveni'};
        $vokativ = explode(" ", $decoded->{'vokativ'});

		It6_DbTransaction::begin($db);
		try {	
			$db->insert("vic_main.uzivatel_osloveni", array(
				'jmeno'            => $u['jmeno'],
				'osloveni'         => $osloveni,
				'vokativ'          => $vokativ[0],
			));
			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
            return $e->getMessage();
		}	            
    }
}