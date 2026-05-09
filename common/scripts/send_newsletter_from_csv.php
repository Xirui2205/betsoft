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


$html = new Zend_View();
$html->setScriptPath(ROOT .'files/templates/html/');

$row = 1;
if (($handle = fopen(ROOT."/db/newletters_registrace/Adresy_nova_registrace.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        $row++;

        if ( $data[3] != 'Email' && !empty($data[3]) ) {
        	$_SESSION['osloveni'] = "";

            $sql = 'SELECT * FROM uzivatel_osloveni WHERE jmeno="'.$data[1].'"';
            $uzivatelOsloveni = $db->query($sql)->fetchAll();
            if ( count($uzivatelOsloveni) > 0 ) {
 				$_SESSION['osloveni'] = $uzivatelOsloveni[0]['osloveni'] . '\t' .$uzivatelOsloveni[0]['vokativ'];
 			} else {
	            if ( strpos(trim($data[2]), "ová") || strpos(trim($data[2]), "ova" ) ) {
    				$_SESSION['osloveni'] = "Vážená paní " . $data[1]; 				
	            } else {
    				$_SESSION['osloveni'] = "Vážený pane " . $data[1]; 				
	            }
 			}

 			$_SESSION["osloveni"] .= ",";

			$bodyText = $html->render('newsletter_registrace_novych.html');     
            
 			$userEmail = $data[3];
	 	    $mail = new Zend_Mail('UTF-8');
			$mail->setFrom(INFOMAIL, "CompBet");
			$mail->addTo($userEmail);			
			$mail->setSubject("Získejte bonus až 5000 Kč!");
	    	$mail->setBodyHtml($bodyText);
			try {
				if ($mail->send()) It6_Log::info('Email was sent to adress: '.$userEmail);
			}
			catch (Exception $e) {
				It6_Log::err('Email not be sent. Exception:'.$e->getMessage());
			}
        }
    }

    fclose($handle);
}
