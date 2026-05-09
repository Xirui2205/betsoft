<?php

/*
 * Zpracování translate.csv - soubor překladů bez indexů
 * výstupem je sql soubor s inserty a updaty (pokud je $debug=false).
 * 
 * Je nutné nejprve spustit skript s $debug=true
 * - vypíšou se fráze, které je nutné řešit ručně.
 * 
 * Pro další jazyky se předpokládá úprava skriptu.
 * 
 * Na zálohy tabulky preklady je určen adresář:
 * 		/home/m/DocumentRoot/Kuchbet/db/vic_main/backup-preklady
 * LIVE SÁZKY~LIVE IN-PLAY~Live~LIVE SÁZKY~LIVE STÁVKY
 * LIVE SÁZKY~LIVE bets~~LIVE SÁZKY~LIVE STÁVKY
 * 
 */
$debug = true;

// inicializace

if (!defined("ROOT"))
	define("ROOT", dirname(dirname(dirname(__FILE__))) . '/');
if ($debug)
	header("Content-type:text/html; charset=UTF-8");
set_time_limit(0);
require_once(ROOT . 'common/includes.inc.php');
include_once(ROOT . 'common/init-global-cache.inc.php');
require_once(ROOT . 'admin/config_local.php');
include "common.php";
include "template/class.TemplatePower.inc.php";
error_reporting(E_ALL | E_STRICT);

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

define('CACHING', 'off');

require_once 'common/config.php';
require_once 'common/class/class.Help.php';
require_once 'common/class/class.Constant.php';
require_once 'common/class/Ip.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader::loadClass('Zend_Debug');
Zend_Loader::loadClass('Zend_Controller_Front');

$autoloader = Zend_Loader_Autoloader::getInstance();
Zend_Registry::set('autoloader', $autoloader);
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('DecoratorForms_');
$autoloader->pushAutoloader(new It6_AutoloaderAdmin());

Zend_Registry::set('Zend_Locale', new Zend_Locale('cs_CZ'));
Zend_Registry::set('langId', 1);
date_default_timezone_set('Europe/Prague');
mb_internal_encoding('UTF-8');

It6_Log::initialize();

$client = new Zend_XmlRpc_Client(WEB_SERVICE_URL);
Zend_Registry::set('ws', new It6_WS(WS_WRAPPER, $client));

$dbPlugin = new Zend_Controller_Plugin_DbPLugin();
$db = $dbPlugin->initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN, true);
$dbAdmin = $dbPlugin->initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN, false);
Zend_Registry::set('db', $db);
Zend_Registry::set('zdb_game', $db);

// -----------------------------------------------------------------------------

function insertDefaultRowsSql($indexPole, $langIds) {
	$sql = '';
	$db = Zend_Registry::get('db');
	$prekladId = $db->select()
			->from('preklady', array('preklad_id'))
			->where('index_pole = ?', $indexPole)
			->query()
			->fetch(Zend_Db::FETCH_COLUMN, 0);

	foreach ($langIds as $langId) {
		$rowCount = $db->select()
				->from('preklady', array('COUNT(*) as cnt'))
				//->where('preklad_id = ?', $prekladId) // preklad_id nakonec ne, někde jsou i různé hodnoty preklad_id pro daný primární klíč (lang_id, index_pole)
				->where('lang_id = ?', $langId)
				->where('index_pole = ?', $indexPole)
				->query()
				//->__toString();
				->fetch(Zend_Db::FETCH_COLUMN, 0);

		if ($rowCount === '0') {
			$sql .= "REPLACE INTO `preklady` SET "
					. "lang_id=$langId,"
					. "index_pole='" . addslashes($indexPole);
			if (!empty($prekladId)) {
				$sql .= "'," . "preklad_id=$prekladId;\n";
			} else {
				$sql .= ";\n";
			}
		}
	}
	return $sql;
}

header('Content-Type: text/html; charset=utf-8');
$file = fopen("translate.csv", "r");
$cnt = 0;
$nenalezeno = 0;
$nenalezenoArr = array();
$sql = '';
while (!feof($file)) {
	$cnt++;
	$line = fgetcsv($file, 0, '~');
	// [0] => Česky Honza(aj) [1] => English Honza(aj) [2] => Slovensky Honza(aj) [3] => Česky Kalivoda(sj) [4] => Slovensky Kalivoda(sj)
	// najít hodnotu / hodnoty index_pole
	$indexPoleArr = array();
	if (!empty($line[0])) {
		// primárně z Česky Honza(aj)
		$indexPoleArr = $db->select()
				->from('preklady', array('index_pole'))
				->where('lang_id = ?', 1)
				->where('text = ?', $line[0])
				->query()
				->fetchAll(Zend_Db::FETCH_COLUMN, 0);
		//print_r($indexPoleArr);
	} else if (!empty($line[2])) {
		// sekundárně z Slovensky Honza(aj)
		$indexPoleArr = $db->select()
				->from('preklady', array('index_pole'))
				->where('lang_id = ?', 16)
				->where('text = ?', $line[2])
				->query()
				->fetchAll(Zend_Db::FETCH_COLUMN, 0);
		//echo $line[0].' ~ '.$line[1].' ~ '.$line[2].' ~ '.$line[3].' ~ '.$line[4].'<br>';
		//print_r($indexPoleArr);
	} else if (!empty($line[3])) {
		// nebo ještě z Česky Kalivoda(sj)
		$indexPoleArr = $db->select()
				->from('preklady', array('index_pole'))
				->where('lang_id = ?', 1)
				->where('text = ?', $line[3])
				->query()
				->fetchAll(Zend_Db::FETCH_COLUMN, 0);
		//echo $line[0].' ~ '.$line[1].' ~ '.$line[2].' ~ '.$line[3].' ~ '.$line[4].'<br>';
		//print_r($indexPoleArr);
	}

	if (!empty($indexPoleArr)) {
		$indexPoleAddSlashes = array();

		foreach ($indexPoleArr as $indexPole) {
			// chybí řádek pro jazyk?
			$sql .= insertDefaultRowsSql($indexPole, array(1, 2, 16));
			$indexPoleAddSlashes[] = addslashes($indexPole);
		}
		// angličtina
		$sql .= "UPDATE `preklady` SET `text` = '" . addslashes((trim($line[1]))) . "'"
				. " WHERE index_pole IN ('" . implode("', '", $indexPoleAddSlashes) . "'" . ") AND lang_id = 2;\n";
		// slovenština
		$sql .= "UPDATE `preklady` SET `text` = '" . addslashes((trim($line[4]))) . "'"
				. " WHERE index_pole IN ('" . implode("', '", $indexPoleAddSlashes) . "'" . ") AND lang_id = 16;\n";
		// čeština
		$sql .= "UPDATE `preklady` SET `text` = '" . addslashes((trim($line[3]))) . "'"
				. " WHERE index_pole IN ('" . implode("', '", $indexPoleAddSlashes) . "'" . ") AND lang_id = 1;\n";

		/* if ($cnt==20) {
		  goto summary;
		  } */
	} else if (!empty($line[1]) || !empty($line[3]) || !empty($line[4])) {
		if ($debug)
			echo $line[0] . ' ~ ' . $line[1] . ' ~ ' . $line[2] . ' ~ ' . $line[3] . ' ~ ' . $line[4] . '<br>';
		$nenalezeno++;
		$nenalezenoArr[] = $cnt;
	}
}

fclose($file);

summary:

if ($debug) {
	$sql = htmlspecialchars($sql);
	echo "Zpracovaný počet řádků: $cnt<br>
--------<br>
Nenalezeno a nutno řešit ručně: $nenalezeno, řádky: " . implode(', ', $nenalezenoArr) . "<br>
--------<br>
SQL:
--------<br>
$sql";
} else {
	header('Content-Type: text/plain');
	header('Content-Disposition: attachment; filename="preklady.sql"');
	/* header('Content-Transfer-Encoding: binary');
	  header('Accept-Ranges: bytes');
	  header('Cache-Control: private');
	  header('Pragma: private'); */
	//header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	echo $sql;
}
