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

try {
	echo "Selecting data...";

	$select = $db->select()
		->from(array('s' => 'sport'), null)
		->join(array('ts'=>'typ_sport'), 's.sport_id=ts.sport_id' , null)
		->join(array('tp'=>'typ_podtyp'), 'tp.typ_id=ts.typ_id AND tp.sport_id = ts.sport_id' , null)
		->joinLeft(array('u'=>'udalost'),'s.sport_id = u.sport_id AND u.udalost_id IN ('.implode($EVENTS,',').')', null)
		->join(array('u2'=>'udalost'),'s.sport_id=u2.sport_id AND u2.udalost_id <> u.udalost_id',null)
		->joinLeft(array('bs'=>'bet_settings'), 'bs.typ_id=ts.typ_id AND bs.podtyp_id=tp.podtyp_id AND bs.udalost_id=u.udalost_id' , null)
		->joinLeft(array('bs2'=>'bet_settings'), 'bs2.typ_id=ts.typ_id AND bs2.podtyp_id=tp.podtyp_id AND bs2.udalost_id=u2.udalost_id' , null)
		->columns(array('s.sport_id','ts.typ_id','tp.podtyp_id','u2.udalost_id','bs.kurz_min','bs.kurz_max','bs.vyhernost_min','bs.vyhernost_max','bs.risk_limit'))
		->where('bs2.udalost_id IS NULL');
		
	$query = $select->query();

	echo "DONE\n";

	while ( null != ($setting = $query->fetch()) ) {

		if ( !isset($setting['vyhernost_min']) ) {
			$setting['kurz_min']      = 1.0;
			$setting['kurz_max']      = 6000.0;
			$setting['vyhernost_min'] = 1.11;
			$setting['vyhernost_max'] = 1.11;
			$setting['risk_limit']    = 200000;
		}
		
		echo "Inserting: sport_id='".$setting['sport_id']."' udalost_id='".$setting['udalost_id']."' typ_id='".$setting['typ_id']."', podtyp_id='".$setting['podtyp_id']."'\n";
		echo "\twith data: kurz_min='".$setting['kurz_min']."'  kurz_max='".$setting['kurz_max']."' vyhernost_min='".$setting['vyhernost_min']."' vyhernost_max='".$setting['vyhernost_max']."' risk_limit='".$setting['risk_limit']."'\n";
		echo "\t...\n";

		$db->insert(
			'bet_settings',
			array(
				'udalost_id'    => $setting['udalost_id'],
				'typ_id'        => $setting['typ_id'],
				'podtyp_id'     => $setting['podtyp_id'],
				'sport_id'      => $setting['sport_id'],
				'kurz_min'      => $setting['kurz_min'],
				'kurz_max'      => $setting['kurz_max'],
				'vyhernost_min' => $setting['vyhernost_min'],
				'vyhernost_max' => $setting['vyhernost_max'],
				'risk_limit'    => $setting['risk_limit'],
			)
		);
			
		echo "\tDONE\n";
		
	}	

	if ( !$test ) 
		It6_DbTransaction::commit($db);
	else
		It6_DbTransaction::rollback($db);
		
	echo "\n\nCOMPLETED SUCCESSFULLY.\n";
} catch ( Exception $e ) {
	echo "\tERROR...rolling back\n";
	It6_DbTransaction::rollback($db);
	throw $e;
}


