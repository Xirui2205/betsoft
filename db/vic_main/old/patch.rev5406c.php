<?php

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
// $db->query("SET time_zone='GMT'");

$rows = $db->select()->from('typ', array('typ_id', 'typ_alias_group', 'group_master'))
	->where('typ_alias_group IS NOT NULL')
	->order(array('typ_alias_id'))
	->query()->fetchAll();
$types = array();
$groupTypes = array();
$groupMasters = array();
foreach ($rows as $row) {
	$id = $row['typ_id'];
	$types[$id] = $row;
	$group = $row['typ_alias_group'];
	if (!array_key_exists($group, $groupTypes))
		$groupTypes[$group] = array();
	$groupTypes[$group][] = $id;

	if (!empty($row['group_master']))
		$groupMasters[$group] = $id;
}

$bets = $db->select()
	->from(array('s' => 'sazky'), array('sazka_id', 'parent_id', 'typ_id', 'text'))
	->join(array('t' => 'typ'), 's.typ_id=t.typ_id AND t.typ_alias_group IS NOT NULL AND s.real_typ_id IS NULL', array())
	->where('s.status=0')
	->where('s.platna_do>=?', It6_Date::dbNow())
	->order(array('parent_id', 'sazka_id'))
	->query()->fetchAll();
$betParents = array();
$unparented = array();
foreach ($bets as $bet) {
	$parentId = $bet['parent_id'];
	if (empty($parentId)) {
		$unparented[] = $bet;
		echo "Unparented bet! {$bet['sazka_id']} \"{$bet['text']}\"\n";
		continue;
	}
	if (!array_key_exists($parentId, $betParents))
		$betParents[$parentId] = array();
	$betParents[$parentId][$bet['sazka_id']] = $bet;
}
unset($bets);

$usedTypes = array();
if (!empty($betParents)) {
	$rows = $db->select()->from(array('s' => 'sazky'), array('sazka_id', 'parent_id', 'real_typ_id'))
		->join(array('t' => 'typ'), 's.typ_id=t.typ_id', array('typ_alias_group'))
		->where('s.parent_id IN (?)', array_keys($betParents))
		->where('s.real_typ_id IS NOT NULL')
		->query()->fetchAll();
	foreach ($rows as $row) {
		$parentId = $row['parent_id'];
		$group = $row['typ_alias_group'];
		if (empty($usedTypes[$parentId]))
			$usedTypes[$parentId] = array();
		if (empty($usedTypes[$parentId][$group]))
			$usedTypes[$parentId][$group] = array();
		$usedTypes[$parentId][$group][] = $row['real_typ_id'];
	}
}

if ($test)
	echo "TEST MODE - how would be bets updated:\n";
else
	$db->beginTransaction();
try {
	if (!$test)
		$stmt = $db->prepare('UPDATE sazky SET real_typ_id=? WHERE sazka_id=?');
	$outOfTypeBets = array();
	$updated = 0;
	foreach ($betParents as $parentId => $bets) {
		foreach ($bets as $bet) {
			$typeId = $bet['typ_id'];
			$type = $types[$typeId];
			$group = $type['typ_alias_group'];
			if (empty($usedTypes[$parentId]))
				$usedTypes[$parentId] = array();
			if (empty($usedTypes[$parentId][$group]))
				$usedTypes[$parentId][$group] = array();
			$free = array_diff($groupTypes[$group], $usedTypes[$parentId][$group]);
			$realTypeId = current($free);
			echo 'group: ' . implode(',', $groupTypes[$group])
				. "; used[$parentId][$group]: " . implode(',', $usedTypes[$parentId][$group]) 
				. '; free: ' . implode(',', $free) . "; using: $realTypeId\n";
			if (!empty($realTypeId)) {
				$usedTypes[$parentId][$group][] = $realTypeId;
				if (!$test) {
					$stmt->execute(array($realTypeId, $bet['sazka_id']));
					++$updated;
				}
			}
			else {
				$outOfTypeBets[] = $bet;
				$realTypeId = '!NO FREE TYPE ID!';
			}
			echo "{$bet['sazka_id']}:{$typeId}:$group \"{$bet['text']}\" -> {$realTypeId}\n";
		}
	}
	if (!$test)
		$db->commit();
}
catch (Exception $e) {
	echo $e->getMessage() . "\n";
	if (!$test)
		$db->rollback();
}
echo "Summary: $updated updated, " . count($unparented) . ' unparented, ' . count($outOfTypeBets) . " not updated (no free type ID)\n";
if (!empty($unparented))
	echo 'List of unparented bet IDs: ' . implode( ',', array_map(function($b) { return $b['sazka_id']; }, $unparented) ) . "\n";
if (!empty($outOfTypeBets))
	echo 'List of not upated bets (no free type ID): ' . implode( ',', array_map(function($b) { return $b['sazka_id']; }, $outOfTypeBets) ) . "\n";
echo "DONE.\n";
