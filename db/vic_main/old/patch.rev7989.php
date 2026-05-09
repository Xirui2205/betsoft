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
It6_DbTransaction::begin($db);


if(!$test) {
	echo "Clearing values in table bet_columns.\n";
		$n = $db->update(
		'bet_column',
		array(
			'risk_limit_balance' => 0,
			'bet_count' => 0,
			'absolute_stake' => 0,
		)
	);
	echo "Values in table bet_columns cleared.\n";
}


if ($test)
	echo "Running in test mode, database won't be updated.\n";

$rows = $db->select()->from('ticket', array('ticketId' => 'ticket_id'))
	->where('zruseno=0')
	->query()
	->fetchAll();

if (empty($rows))
	echo "No tickets found.\n";
else
	echo count($rows) . " ticket(s) will be processed\n";

$updateCount = 0;

try {
	foreach ($rows as $row) {
		$ticketId = $row['ticketId'];
		$helper = It6_Models_TicketFactory::newTicket($ticketId, It6_Models_Ticket::DATA_ADMIN_TICKET, true, true, $db);
		switch ($helper->type) {
			case 'simple':
				foreach ($helper->bets as $bet) {
					$riskLimitBalance = $bet['amount'];
					$absoluteStake = $bet['amount'];
					updateBetColumns($db, $bet['id'], $bet['column'], $riskLimitBalance, $absoluteStake, $test);
					$updateCount++; 
				}
				break;
			case 'kombi':
				$rateTotal = 0;
				foreach ($helper->bets as $bet) {
					$rateTotal += $bet['rate'] - 1;
				}
				
				foreach ($helper->bets as $bet) {
					if($rateTotal == 0)
						$riskLimitBalance = 0;
					else
						$riskLimitBalance = $helper->stake * ($bet['rate'] - 1) / $rateTotal;

					$absoluteStake = $helper->stake;
					updateBetColumns($db, $bet['id'], $bet['column'], $riskLimitBalance, $absoluteStake, $test);
					$updateCount++; 
				}
				break;
			case 'maxikombi':
			
				foreach ($helper->bets as &$bet) {
					$bet['riskLimitBalance'] = 0;
					$bet['absoluteStake'] = 0;
					$bet['betCount'] = 0;
					$rateTotal += $bet['rate'] - 1;
				}
				unset($bet);
				//var_dump($helper->bets);exit;
				$helper->getGroups();
				$hasGroupT = $helper->hasGroupT();
				$groupIds = array();
				foreach (array_keys($helper->groups) as $id) {
					if (0 != $id)
						$groupIds[] = $id;
					$helper->groups[$id]['riskAmount'] = 0.0;
					$helper->groups[$id]['inTicketCount'] = 0;
				}
				$n = $helper->getGroupCount(false);
				foreach ($helper->combinations as $k => &$comb) {
					$cStake = $comb['stake'];
					$cRateOverOne = 0;
					$cs = It6_Array::getCombinations($n, $k, $groupIds);
					
					
					
					foreach ($cs as $c) {
						$cBets = array();
						foreach ($c as $g) {
							$cBets = array_merge($cBets, $helper->getGroupBets($g));
						}
						if ($hasGroupT) {
							$cBets = array_merge($cBets, $helper->getGroupBets(0));
						}
						
						$helper->stake += $cStake;
					}

					foreach($helper->bets as $bet){
						if(in_array($bet['id'], $cBets))
							$cRateOverOne += $bet['rate'] - 1;
					}
					$comb['rateOverOne'] = $cRateOverOne;
				}
				unset($comb);

				foreach ($helper->combinations as $k => &$comb) {
					if (!$helper->isCombinationUsed($k))
						continue;
					foreach ($comb['data'] as &$c) {
						foreach ($helper->bets as &$bet) {
							if (in_array($bet['id'], $c['bets'])) {
								if($comb['rateOverOne'] == 0)
									$riskLimitBalance = 0;
								else
									$riskLimitBalance = $comb['stake'] * ($bet['rate'] - 1) / $comb['rateOverOne'];
								$bet['riskLimitBalance'] += $riskLimitBalance;
								$bet['betCount']++;
								$bet['absoluteStake'] += $comb['stake'];
							}
						}
						unset($bet);
					}
					unset($c);
				}
				unset($comb);

				foreach($helper->bets as $bet) {
					updateBetColumns($db, $bet['id'], $bet['column'], $bet['riskLimitBalance'], $bet['absoluteStake'], $test, $bet['betCount']);
					$updateCount++;
				}
				break;
			default:
				break;
		}
	}
	It6_DbTransaction::commit($db);
}
catch (Exception $e) {
	echo ' ERROR! : ' . $e->getMessage() . "\n";
	It6_DbTransaction::rollback($db);
}	

echo "$updateCount row(s) updated.\nDONE.\n";


function updateBetColumns($db, $betId, $columnId, $riskLimitBalance, $absoluteStake, $test, $betCount=1) {
	if(!$test) {
		if (empty($riskLimitBalance))
			$riskLimitBalance = 0;
		if (empty($absoluteStake))
			$absoluteStake = 0;
		if (empty($betCount))
			$betCount = 0;
		echo "Updating bet_column: betId=$betId colId=$columnId rikLimitBalanceChange=$riskLimitBalance absoluteStakeChange=$absoluteStake\n";
		$n = $db->update(
			'bet_column',
			array(
				'risk_limit_balance' => new Zend_Db_Expr("risk_limit_balance+$riskLimitBalance"),
				'bet_count' => new Zend_Db_Expr("bet_count+$betCount"),
				'absolute_stake' => new Zend_Db_Expr("absolute_stake+$absoluteStake"),
			),
			array('sazka_id=?' => $betId, 'sloupec_id=?' => $columnId)
		);
	}
	else
		echo "Would be updating bet_column: betId=$betId colId=$columnId rikLimitBalanceChange=$riskLimitBalance absoluteStakeChange=$absoluteStake\n";
}

