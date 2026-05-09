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

if ($test)
	echo "(in testing mode)\n";

$EVENTS=array(
	1687,1041,335,98,2487,1201,226,127,277,2761,1032,1848,1464,2376,2894,1923,
	2463,709,740,288,284,2715,2873,2817,1533,1316,1158,1155,2641
);

It6_DbTransaction::begin($db);

foreach ( $EVENTS as $event ) {

	$settings = $db->select()->from('bet_settings', array('typ_id','podtyp_id','kurz_min','kurz_max','vyhernost_min','vyhernost_max','risk_limit','sport_id'))
		->where('udalost_id = ?', $event)
		->query()->fetchAll();


	echo "Updating sport '".$settings[0]['sport_id']."' from event '$event'\n";
	echo "=================================================================\n";

	foreach ( $settings as $setting ) {
		echo "Updating typ_id ='".$setting['typ_id']."', podtyp_id='".$setting['podtyp_id']."'...";

		$n = $db->update(
				'bet_settings',
				$setting,
				array(
					'typ_id = ?' => $setting['typ_id'],
					'podtyp_id = ?' => $setting['podtyp_id'],
					'sport_id = ?' => $setting['sport_id'],
					'udalost_id <> ?' => $event)
			);
			
		echo "done - Updated '$n' events.\n";
		
		
	}
	
	echo "------------------------------------------------------------------\n\n";

}

if ( !$test ) 
	It6_DbTransaction::commit($db);
else
	It6_DbTransaction::rollback($db);

echo "DONE.\n";
