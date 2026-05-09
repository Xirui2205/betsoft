<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

/*
 * This patch updates branch geo location data based on their adress. it uses googles geocoding services.
 */

define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

include(ROOT . 'common/config_util.inc.php');

$appEnv = getAppEnv();
if (empty($appEnv)) {
    echo "APPLICATION_ENVIRONMENT environment variable not set!\n";
    exit(1);
}

$test		= in_array('test', $argv);
$verbose	= in_array('verbose', $argv);

include(ROOT . 'common/config.php');
include(ROOT . 'common/includes.inc.php');

require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('It6_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);
$db->query('SET NAMES utf8');





$TABLE = 'branch';


if ($test)
	echo "(in testing mode)\n";
else
	It6_DbTransaction::begin($db);

try {

	$errors		= array();
	$brCoords	= array();
	echo "Usage: branch-long-lat.php [test] [verbose]";

	echo "\n\n\nGETTING LIST OF BRANCHES FROM DB\n\n";
	$branches = $db->select()
		->from(
			'branch',
			array('street','town','id')
		)
		->where('id NOT IN (?)', It6_Models_Branch::ID_INTERNET,It6_Models_Branch::ID_INTERNET_LIVE)
		->where('longitude IS NULL')
		->where('latitude IS NULL')
		->query()->fetchAll();


	echo "\n\n\nSTARTING TO ACQUIRE DATA FROM GOOGLE\n\n";
	foreach($branches as $i => $branch) {
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($branch['street'].' '.$branch['town']).'&sensor=false';
		
		$response	= file_get_contents($url);
		$response	= json_decode($response);



		if($response->status == 'OK') {
			$geoCoords	= $response->results[0];
			$geoCoords	= $geoCoords->geometry->location;
			$lat		= $geoCoords->lat;
			$long		= $geoCoords->lng;
			
			$brCoords[$i]['latitude']	= $lat;
			$brCoords[$i]['longitude']	= $long;
			$brCoords[$i]['branchId']	= $branch['id'];

			if ($verbose)
				echo "Geo coordinates for branch id: ".$branch['id']." acquired.\n";
		}
		else {
			$errors[] = array(
				'branchId'	=> $branch['id'],
				'error'		=> $response->status,
			);
			
			echo "ERROR for branch id: ".$branch['id']." -> ".$response->status."\n";
		}
		sleep(1);
	}
	
	echo "\n\n\nSTARTING TO UPDATE DB\n\n";
	if($test) {
		echo "Had this not been test mode, this data would have been entered into db:\n\n";
		foreach($brCoords as $branch) {
			echo "id: ".$branch['branchId']."\n";
			echo "lat: ".$branch['latitude']."\n";
			echo "long: ".$branch['longitude']."\n";
			echo "\n";
		}
	}
	else {
		foreach($brCoords as $branch) {
			$data = array(
				'longitude'	=> $branch['longitude'],
				'latitude'	=> $branch['latitude'],
			);
			$db->update($TABLE, $data, array('id = ?' => $branch['branchId']));
			
			if ($verbose)
				echo "Geo coordinates for branch id: ".$branch['branchId']." put in db.";
		}
	}
	
	echo "branches processed: ".count($brCoords)."\n";
	echo "errors: ".count($errors)."\n";
	echo "\n\n\n";
	echo "ERRORS:\n";
	foreach($errors as $err) {
		echo "branchId: ".$err['branchId']."\n";
		echo "error: ".$err['error']."\n";
		echo "\n";
	}

	
	
	
	if (!$test)
		It6_DbTransaction::commit($db);
	
	echo "SUCCESS!! all done!.\n";
}

catch ( Exception $e ) {
	if (!$test) {
		echo "FATAL ERROR: Transaction was rolled back.\n";
		It6_DbTransaction::rollback($db);
	}
	else
		echo "FATAL ERROR: Had this not been a test run, the transaction would have been rolled back.\n";
	
	throw $e;
}


