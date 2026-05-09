<?php

define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');
//error_reporting(0);
set_include_path (
		get_include_path() . PATH_SEPARATOR .
		ROOT . 'common/library/' . PATH_SEPARATOR .
		ROOT . 'betting-service/application/' . PATH_SEPARATOR .
		ROOT . 'betting-service/library/' . PATH_SEPARATOR .
		ROOT . 'admin/library/' . PATH_SEPARATOR
);

include(ROOT . 'common/config_util.inc.php');
$appEnv = getAppEnv();

if (empty($appEnv)) {
    echo "APPLICATION_ENVIRONMENT environment variable not set!\n";
    exit(1);
}

include(ROOT . 'common/config.php');
include(ROOT . 'common/includes.inc.php');

require_once 'Zend/Loader.php';
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Models_');
$autoloader->registerNamespace('It6_');
$autoloader->registerNamespace('Webservice_');
$autoloader->registerNamespace('Entities_');
$autoloader->registerNamespace('WarpTurn_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN);
$dbAdmin = Zend_Controller_Plugin_DbPLugin::initDbConnection('admindb', Zend_Controller_Plugin_DbPLugin::CONFIG_ADMIN);

//nactu vsechny pobocky

$branches = Webservice_Branch::getAll();
$branchesArray = It6_ArrayWrapper::toNativeArray($branches);
foreach($branchesArray as $branch) {
$uri = 'http://maps.googleapis.com/maps/api/geocode/json';
$client = new Zend_Http_Client();
$client->setParameterGet(
			array(
				'address' => $branch["street"] . ', ' . str_replace(" - MP", "", $branch["town"]) . " " . $branch["zip"] . ", Česká Republika",
				'sensor'=> 'false'
			)
		);
$response = $client->setUri($uri)->request('GET')->getRawBody();
$obj = Zend_Json::decode($response);

// // print_r($obj['results'][0]['geometry']['location']);
// print('Latitude: ' . $obj['results'][0]['geometry']['location']['lat'] . "\n");
// print('Longtitude: ' . $obj['results'][0]['geometry']['location']['lng'] . "\n");
	
	if((empty($branch["longitude"]) || empty($branch["latitude"])) && $branch["branchId"] != 1) {

		$dbAdmin->update("branch", array(
				"latitude" => $obj['results'][0]['geometry']['location']['lat'],
				"longitude" => $obj['results'][0]['geometry']['location']['lng']
			),
		array("id = ?" => $branch["branchId"])
		);
	}

}
//print_r($branchesArray);
