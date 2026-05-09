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

if (!$test) {
    $stmt = $db->prepare('UPDATE uzivatel SET handle=? WHERE user_id=?');
    foreach ($db->query('SELECT user_id AS id FROM uzivatel') as $row) {
        $id = $row['id'];
        $handle = It6_NineDigitHandle2::makeHandle($id, $db);
        $stmt->execute(array($handle, $id));
    }
}
else {
    $stmt = $db->prepare('UPDATE uzivatel SET handle=? WHERE user_id=?');
    foreach ($db->query('SELECT user_id AS id, handle FROM uzivatel') as $row) {
        $id = $row['id'];
        $handle = $row['handle'];
        if ($id != It6_NineDigitHandle2::decodeHandle($handle, $db))
            echo "ERROR: User ID=$id handle='$handle' ... decoded handle doesn't match!\n";
    }
}
echo "DONE.\n";
