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
//ziskam vyplacene tikety, ktere mohou byt tiketem mesice a byly vyplaceny tento mesic
$ticket = new Webservice_Ticket();
$tikety = $ticket->getMonthTicketComplete();

foreach($tikety as $tiket)
{
	$db->insert(
			'ticket_game_result',
			array(
					'game_id' => 2,
					'ticket_id' => $tiket['ticketId'],
					'evaluation' => $tiket['rate'],
			)
	);
	It6_Log::info(
			'Ticket #%ticketId% used in ticket game "%gameName%" with evaluation: %evaluation%',
			It6_Log::TAG_CAMPAIGN,
			array(
					'ticketId' => $tiket['ticketId'],
					'gameId' => 2,
					'gameName' => 'TicketOfMonth',
					'evaluation' => $tiket['rate'],
			)
	);
}