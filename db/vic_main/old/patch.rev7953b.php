<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

// Spoustet po SQL skriptu a aplikovani vsech PHP skriptu (ktere by mely byt aplikovany se zastavenymi daemony).

define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

function turnoffDiacritics($str) {
	$conv = array(
		'á'=>'a','č'=>'c','ď'=>'d','é'=>'e','ě'=>'e','í'=>'i','ň'=>'n',
		'ó'=>'o','ř'=>'r','š'=>'s','ť'=>'t','ú'=>'u','ý'=>'y','ž'=>'z',
		'Á'=>'A','Č'=>'C','Ď'=>'D','É'=>'E','Ě'=>'E','Í'=>'I','Ň'=>'N',
		'Ó'=>'O','Ř'=>'R','Š'=>'S','Ť'=>'T','Ú'=>'U','Ý'=>'Y','Ž'=>'Z'
	);
	foreach ( $conv as $k => $v ) {
		$str = str_replace($k,$v,$str);
	}
	return $str;
}

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
	throw new Excption('Test mode is not supported');

$fileName = dirname(__FILE__).'/'.basename(__FILE__,'.php').'.csv';

$f = @fopen($fileName, "r");
if ($f) {
	It6_DbTransaction::begin($db);
	try {
		$now = It6_Date::dbNow();
		
		$db->insert(
			'voucher_set',
			array(
				'id'=>1,
				'name'=>'body_ekis',
				'valid_from'=>$now,
				'valid_to'=>'2012-01-05 00:00:00',
				'amount'=>0,
				'count'=>0,
				'multiuse'=>1,
				'handle_size'=>0,
			)
		);
	
	
		while (($buffer = fgets($f, 4096)) !== false) {
			if ( strpos($buffer,';') === false ) continue;
			list($handle,$_,$_,$nick,$amount)= explode(';',$buffer);
			
			$handle = intval(trim($handle));
			$amount = intval(trim($amount));
			
			if ( empty($handle) || empty($amount) )
				continue;
			
			
			$nick = mb_substr(trim($nick,'" '),0,3);
			$nick = turnoffDiacritics($nick);
			$nick = mb_strtolower($nick);
			
			$handle = $handle.$nick;
						
			$db->insert(
				'voucher',
				array(
					'valid_from'=>$now,
					'valid_to'=>'2012-05-30 23:59:59',
					'handle'=>$handle,
					'created'=>$now,
					'amount'=>$amount,
					'voucher_set_id'=>1,
					'point_type_id'=>1,
				)
			);
			
			echo "$handle\t$amount\n";
		}
		if (!feof($f)) {
			echo "Error: unexpected fgets() fail\n";
		}
		fclose($f);
		It6_DbTransaction::commit($db);
	}
	catch ( Exception $e ) {
		It6_DbTransaction::rollback($db);
		throw $e;
	}

}



echo "DONE.\n";
