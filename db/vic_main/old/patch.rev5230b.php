<?php

// POZOR! tento patch spustit az po alikaci SQL patche 5230a a s common/library/It6/Models/TicketFactory.php na revizi 5232+

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

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
$db->query('SET NAMES utf8');

if ($test)
	echo "(in testing mode)\n";

$ids = $db->select()->from('ticket', array('id' => 'ticket_id'))
	->where('type=?', 'maxikombi')
	->query()->fetchAll();
foreach ($ids as $id) {
	$id = $id['id'];
	echo "Ticket ID: $id\n";
	$helper = It6_Models_TicketFactory::newTicket($id, It6_Models_Ticket::DATA_ADMIN_TICKET, false, true, $db);
	foreach ($helper->combinations as $k => $comb) {
		if ($helper->isCombinationUsed($k)) {
			$cData = array( 'rows' => array() );
			foreach ($comb['data'] as $row) {
				$cData['rows'][] = array(
					'name' => $row['name'],
					'rate' => $row['rate'],
					'win' => $row['win'],
					'won' => $row['won'],
				);
			}
			if ($test) {
				echo "\tk=$k : " . Zend_Json::encode($cData) . "\n";
			}
			else {
				echo "\tUpdating k=$k: ";
				$n = $db->update(
					'ticket_combination',
					array('data' => Zend_Json::encode($cData)),
					array('ticket_id=?' => $id, 'k=?' => $k)
				);
				echo "$n\n";
			}
		}
	}	
}

$res = $db->select()
	->from( 'ticket_combination', array('c' => new Zend_Db_Expr('COUNT(DISTINCT ticket_id)')) )
	->where('LENGTH(data)=0')
	->query()->fetchAll();
$n = $res[0]['c'];
if (0 == $n)
	echo "No row with empty data column found.\n";
else
	echo "!!! $n rows(s) with empty data column found !!!\n";
echo "DONE.\n";
