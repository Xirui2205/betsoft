<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

// Spoustet po SQL skriptu a aplikovani vsech PHP skriptu (ktere by mely byt aplikovany se zastavenymi daemony).

define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');
define('RUNNING_FROM_CLI', true);

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

$rows = $db->select()->from('typ', array(
		'typeId' => 'typ_id',
		'isMaster' => 'group_master',
		'aliasGroup' => 'typ_alias_group',
	))
	->where('typ_alias_group IS NOT NULL')
	->order('typ_alias_id')
	->query()
	->fetchAll();

$masters = array();
$typeGroups = array();
foreach ($rows as $row) {
	$typeId = $row['typeId'];
	$aliasGroup = $row['aliasGroup'];
	if ($row['isMaster'])
		$masters[$typeId] = $aliasGroup;
	$typeGroups[$aliasGroup][] = $typeId;
}

//if (!$test)
//	It6_DbTransaction::begin($db);

try {

	$stmt = $db->select()->from('sazky', array(
			'betId' => 'sazka_id',
			'typeId' => 'typ_id',
			'parentId' => 'parent_id', 
		))
		->where('typ_id IN (?)', array_keys($masters))
		->where('real_typ_id IS NULL')
		->query();
	$bets = array();
	while ($row = $stmt->fetch()) {
		$parentId = (empty($row['parentId']) ? $row['betId'] : $row['parentId']);
		$bets[$parentId][$row['typeId']][] = $row;
	}
	$updates = array();
	$failures = array();
	foreach ($bets as $parentId => $types) {
		foreach ($types as $typeId => $_bets) {
			$freeRealTypeIds = It6_Models_BetType::getParentBetFreeDerivedTypeIds($parentId, $typeId);
			sort($freeRealTypeIds);
			foreach ($_bets as $bet) {
				$betId = $bet['betId'];
				if (empty($freeRealTypeIds))
					$failures[] = $betId;
				else
					$updates[$betId] = array_shift($freeRealTypeIds);
			}
		}
	}

	$fixed = 0;
	foreach ($updates as $betId => $realTypeId) {
		echo "Fixing: betId=$betId, realTypeId=$realTypeId\n";
		if (!$test) {
			try {
				$db->update('sazky', array('real_typ_id' => $realTypeId), array('sazka_id=?' => $betId));
			}
			catch (Exception $e) {
				echo ' ERROR! : ' . $e->getMessage() . "\n";
				$failures[] = $betId;
			}
		}
		++$fixed;
	}

	echo "$fixed bet(s) fixed.\n";
	
	if (empty($failures))
		echo "No unfixable bets found.\n";
	else
		echo count($failures) . " unfixable bet(s) found! IDs=[" . implode(',', $failures) . "]\n";
	
//	if (!$test)
//		It6_DbTransaction::commit($db);
	echo "DONE.\n";
	exit(0);
}
catch (Exception $e) {
	echo ' ERROR! : ' . $e->getMessage() . "\n";
//	if (!$test)
//		It6_DbTransaction::rollback($db);
	exit(1);
}
