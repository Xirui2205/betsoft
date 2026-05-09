<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="no-js">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

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

//$sql = "TRUNCATE TABLE preklady";
//$db->query($sql);

$row = 1;
if (($handle = fopen(ROOT."/db/servis/preklady.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, "~")) !== FALSE) {        
        $row++;
//setlocale(LC_ALL, 'czech');
//setlocale(LC_CTYPE, 'cs_CZ.UTF-8');
        
        /*
	    [0] => index
	    [1] => Český
	    [2] => English
	    [3] => Slovensky
		*/
        
        //echo "<pre>";
        //print_r($data);

//mb_convert_encoding($data[1], 'UTF-8', 'windows-1250');
//mb_convert_encoding($data[1], 'ISO-8859-1','utf-8');
//echo iconv('CP1250', 'UTF-8', $data[1]);
//echo iconv('utf-8', 'us-ascii//TRANSLIT', $data[1]);
//echo iconv("windows-1256", "utf-8//TRANSLIT//IGNORE", $data[1]);

//echo iconv("UTF-8", "ASCII//TRANSLIT", $data[1]);
//echo iconv("UTF-8", "ASCII//TRANSLIT", mb_convert_case($data[1], MB_CASE_UPPER, "UTF-8"));

//echo iconv("utf-8", "us-ascii//TRANSLIT", $data[1]);
//echo iconv("CP852", "UTF-8//IGNORE", $data[1]); 
//echo iconv("ISO-8859-2","UTF-8", $data[1]);

echo $data[1]."<br/>";
//echo iconv("UTF-8","ISO-8859-2//TRANSLIT//IGNORE", $data[1]);
echo iconv("UTF-8","ISO-8859-2//TRANSLIT//IGNORE", $data[1]);

echo "<br/><br/>";
//$cz = iconv("UTF-8//TRANSLIT","ISO-8859-2", $data[1]);

$sql = "INSERT INTO `vic_main`.`preklady` (`lang_id`,
										   `index_pole`,
										   `short_text`,
										   `text`,
										   `translate`,
										   `preklad_id`) 
			VALUES ('1',
					'" . $data[0] . "',
					'',
					'" . $data[1] . "',
					'1',
					'" . $row . "')";

		//$db->query($sql);

    }
    fclose($handle);
}
?>
</html>