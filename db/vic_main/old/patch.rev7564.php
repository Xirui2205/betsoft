<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

// Spoustet po SQL skriptu a aplikovani vsech PHP skriptu (ktere by mely byt aplikovany se zastavenymi daemony).

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
// $db->query("SET time_zone='GMT'");

if ($test)
	echo "Running in test mode, database won't be updated.\n";

$stmt = $db->select()->from(array('s' => 'sazky'), array('betId' => 'sazka_id'))
	->join(array('pts' => 'podtyp_sloupce'), 's.podtyp_id=pts.podtyp_id', array('columnId' => 'sloupec_id'))
	->joinLeft(array('bc' => 'bet_column'), 's.sazka_id=bc.sazka_id AND pts.sloupec_id=bc.sloupec_id', array())
	->where('bc.risk_limit_balance IS NULL')
	->query();

$insertCount = 0;
while ($row = $stmt->fetch()) {
	if (!$test) {
		$db->insert('bet_column', array(
			'sazka_id' => $row['betId'],
			'sloupec_id' => $row['columnId'],
			'risk_limit_balance' => 0,
		));
	}
	++$insertCount;
}
echo "Inserted initial balances: $insertCount row(s)\n";

$rows = $db->select()->from('ticket', array('ticketId' => 'ticket_id'))
	->where('zruseno=0')
	//->where('ticket_id<?', ?)
	->query()
	->fetchAll();

if (empty($rows))
	echo "No tickets found.\n";
else
	echo count($rows) . " ticket(s) will be processed\n";

$updateCount = 0;
foreach ($rows as $row) {
	$ticketId = $row['ticketId'];
	try {
		$helper = It6_Models_TicketFactory::newTicket($ticketId, It6_Models_Ticket::DATA_ADMIN_TICKET, true, true, $db);
		foreach ($helper->bets as $bet) {
			$betId = $bet['id'];
			$columnId = $bet['column'];
			$balance = floatval($bet['riskAmount']);
			if ($test)
				echo "Virtually changing row: ticketId=$ticketId betId=$betId columnId=$columnId balanceChange=$balance\n";
			else {
				$n = $db->update(
					'bet_column',
					array('risk_limit_balance' => new Zend_Db_Expr("risk_limit_balance+$balance")),
					array('sazka_id=?' => $betId, 'sloupec_id=?' => $columnId)
				);
				++$updateCount;
				if (!$n)
					echo "WARNING: no row was changed: ticketId=$ticketId betId=$betId columnId=$columnId balanceChange=$balance\n";
			}
		}
	}
	catch (Exception $e) {
		echo ' ERROR! : ' . $e->getMessage() . "\n";
	}
}

echo "$updateCount row(s) updated.\nDONE.\n";
