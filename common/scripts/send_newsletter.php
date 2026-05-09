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

$uzivatel = $db->select()
->distinct()
->from('vic_main.uzivatel')
->joinLeft(array('uo' => 'vic_main.uzivatel_osloveni'),
                 'uo.jmeno = uzivatel.jmeno')
->where("anonymous = 0") //AND email = 'info@betservice.eu' OR email = 'josef.tamok@compbet.com'")
->where("datum_aktivace IS NULL")
->query()
->fetchAll();     

$html = new Zend_View();
$html->setScriptPath(ROOT .'files/templates/html/');

foreach ($uzivatel as $u) {
    $sendEmail = empty($u['email']) ? false : true;
    $_SESSION['osloveni'] = $u['osloveni'] . ' ' .$u['vokativ'];
    $branch_name = $branch_street = $branch_town = $branch_zip = "";

        // Doregistrace
        if (empty($u['osloveni']) ) {
            $sendEmail = false;
        }

        $_SESSION['branch_name'] = $_SESSION['branch_street'] = $_SESSION['branch_town'] = $_SESSION['branch_zip'] = "";
        
        if ($u['ulice'] != "" && $u['misto'] != "") {
            $address = urlencode($u['ulice'].",".$u['misto'].",Czech Republic");
            $region = "CZE";
            $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");

            $decoded_adr = json_decode($json);

            if (count($decoded_adr->{'results'}) != 0) {
                $latitude = $decoded_adr->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
                $longitude = $decoded_adr->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

                $sql = "SELECT branch.*, ( 3959 * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( latitude ) ) ) ) AS distance FROM branch HAVING distance < 25 ORDER BY distance LIMIT 0 , 1";
                $branch = $dbAdmin->query($sql)->fetchAll();

                if ( count($branch) != 0 ) {
                    $_SESSION['branch_name'] = $branch[0]['name'];
                    $_SESSION['branch_street'] = $branch[0]['street']; 
                    $_SESSION['branch_town'] = $branch[0]['town'];
                    $_SESSION['branch_zip'] = $branch[0]['zip'];
                }
            }
        } 

        $bodyText = $html->render('newsletter_doregistrace.html');    

    // Sending email
    if ( $sendEmail ) {
        $userEmail = $u['email'];
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