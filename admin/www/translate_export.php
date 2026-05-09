<?php
define('RUNNING_FROM_CLI', true);
define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');

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

//$test = in_array('test', $argv);

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

// APPLICATION_ENVIRONMENT=DEVEL_LOCAL php translate_export.php
// http://admin.testbook.cz/translate_export.php

/**
* seznam jazyku
*/
$jazykyQuery = $db->select()->from("jazyky");
$_jazyky = $db->fetchAll($jazykyQuery);

$jazyky = array();
foreach ($_jazyky as $_jazyk) {
	$jazyky[$_jazyk["lang_id"]] = $_jazyk["alt_text"];
}

/**
* seznam prekladu
*/
$translateQuery = $db->select()->from("preklady");
if (isset($_GET['maxId'])) {
	$translateQuery->where('preklad_id <= ' . $_GET['maxId']);
}
$_preklady = $db->fetchAll($translateQuery);

$preklady = array();
foreach ($_preklady as $preklad) {
	$preklady[$preklad["index_pole"]][$preklad["lang_id"]] = $preklad["text"];
}

/**
* vytvoreni csv souboru
*/
$delimiter = "~";
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=preklady.csv");

//header('Content-Type: text/html; charset=utf-8'); // pro výstup na stránku

echo "index" . $delimiter;
echo $jazyky[1] . $delimiter;
echo $jazyky[2] . $delimiter;
echo $jazyky[16] . $delimiter;
echo "\n";

foreach ($preklady as $key => $value) {
	echo $key . $delimiter;

	echo isset($value[1]) ? $value[1].$delimiter : $delimiter;
	echo isset($value[2]) ? $value[2].$delimiter : $delimiter;
	echo isset($value[16]) ? $value[16].$delimiter : $delimiter;
	
	echo "\n";
}

//convert_to_csv($arrayToCSV, 'preklady.csv', ',');

function convert_to_csv($input_array, $output_file_name, $delimiter)
{
    $temp_memory = fopen('php://memory', 'w');
    foreach ($input_array as $line) {
        fputcsv($temp_memory, $line, $delimiter);
    }
    fseek($temp_memory, 0);
    header('Content-Type: application/csv');
    header('Content-Disposition: attachement; filename="' . $output_file_name . '"');
    fpassthru($temp_memory);
}
?>
