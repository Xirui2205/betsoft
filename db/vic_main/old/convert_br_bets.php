<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

/*
 * This patch upgrades database Betradar data from original implementation (updates new betradar_* columns).
 */

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
else
	It6_DbTransaction::begin($db);

$betTypeConfig = array(
	18 => array('oddsType' => "01", 'specialValue' => 'nameSuffix'),
	19 => array('oddsType' =>  "10"),
	20 => array('oddsType' =>  "60", 'specialValue' => 'nameSuffix'),
	22 => array('oddsType' =>  "20"),
	23 => array('oddsType' =>  "02"),
	24 => array('oddsType' =>  "10"),
	25 => array('oddsType' =>  "70", 'specialValue' => 'nameDoubleSuffix', 'params' => 'spread'),
);

try {

	$stmt = $db->select()
		->from('sazky', array(
			'betId' => 'sazka_id',
			'typeId' => 'typ_id',
			'name' => 'text',
			'brBetId' => 'betradar_sazka_id',
			'brMatchId' => 'betradar_match_id',
		))
		->where('betradar_match_id IS NOT NULL')
		->where('betradar_odds_type IS NULL')
		->query();
	$oBets = array();
	$eBets = array();
	while ($row = $stmt->fetch()) {
		$betId = $row['betId'];
		$typeId = $row['typeId'];
		$betName = $row['name'];
		$brBetId = $row['brBetId'];
		$brMatchId = $row['brMatchId'];
		$lm = strlen($brMatchId);
		if (!empty($brBetId)) {
			$_brMatchId = substr($brBetId, 0, $lm);
			if ($_brMatchId != $brMatchId) {
				$eBets[] = $betId;
				echo "WARNING: BR match ID mismatch [$brMatchId/$_brMatchId] (bet:$betId)\n";
				continue;
			}
		}
		if (empty($betTypeConfig[$typeId])) {
			$eBets[] = $betId;
			echo "WARNING: unknown oddstype [betType:$typeId] (bet:$betId)\n";
			continue;
		}
		$oddsType = $betTypeConfig[$typeId]['oddsType'];
		if (!empty($brBetId)) {
			$lod = strlen($oddsType);
			$_oddsType = substr($brBetId, $lm, $lod);
			if ($_oddsType != $oddsType) {
				$eBets[] = $betId;
				echo "WARNING: odds type mismatch [$oddsType/$_oddsType] (bet:$betId)\n";
				continue;
			}
		}
		$specialValue = '';
		if (!empty($betTypeConfig[$typeId]['specialValue'])) {
			$svType = $betTypeConfig[$typeId]['specialValue'];
			if ('nameSuffix' == $svType) {
				if (1 == preg_match('/\s(\S+)$/', $betName, $matches))
					$specialValue = $matches[1];
			}
			else if ('nameDoubleSuffix' == $svType) {
				// not implemented
			}
			if ('' == $specialValue) {
				$eBets[] = $betId;
				echo "WARNING: special value not parsed [$svType] (bet:$betId)\n";
				continue;
			}
		}
		$brParams = null;
		if (!empty($betTypeConfig[$typeId]['params'])) {
			$paramType = $betTypeConfig[$typeId]['param'];
			if ('spread' == $paramType) {
				// not implemented
			}
		}
		if (!$test) {
			$db->update(
				'sazky',
				array(
					'betradar_bet_type' => 'match',
					'betradar_odds_type' => $oddsType,
					'betradar_special_value' => $specialValue,
					'betradar_competitor_id' => null,
					'betradar_params' => $brParams,
				),
				array('sazka_id=?' => $betId)
			);
		}
		echo "update: betId=$betId: betradar_bet_type='match', betradar_oddstype='$oddsType', betradar_special_value='$specialValue',"
			. " betrader_competitor_id=NULL, betradar_params=" . (isset($brParams) ? "'$brParams'" : 'NULL') . "\n";
		
		$oBets[] = $betId;
	}
	echo "Bets with ERROR: " . implode(',', $eBets) . " (" . count($eBets) . " bet(s))\n";
	echo "Bets with success: " . count($oBets) . " bet(s)\n";
	echo "\nDONE.\n";
	if (!$test)
		It6_DbTransaction::commit($db);
} catch ( Exception $e ) {
	if (!$test) {
		echo "FATAL ERROR: Transaction was rolled back.\n";
		It6_DbTransaction::rollback($db);
	}
	throw $e;
}


