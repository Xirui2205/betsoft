<?php

// !!! tento soubor patri do db/vic_main/
// !!! nastavit APPLICATION_ENVIRONMENT pri volani z CLI

//define('ROOT', dirname(dirname(dirname(__FILE__))) . '/');
define('ROOT', '/rest/htdocs/');
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
$autoloader->registerNamespace('It6_');
$db = Zend_Controller_Plugin_DbPLugin::initDbConnection('db', Zend_Controller_Plugin_DbPLugin::CONFIG_MAIN);
$db->query('SET NAMES utf8');


$db->beginTransaction();
try {
	$events = $db->select()
		->from('udalost')
		->query()->fetchAll();

	foreach($events as $event) {
		//make sure all events have translations, if not add them
		$translations = $db->select()
			->from('preklady')
			->where('index_pole = ?', $event['nazev'])
			->query()->fetchAll();

		if(count($translations) != 3) {
			echo 'rewriting translation key '.$event['nazev'];
			echo "\n";

			$db->delete('preklady', array('index_pole = ?' => $event['nazev']));
			$data = array(
				'lang_id'		=> 1,
				'index_pole'	=> $event['nazev'],
				'text'			=> $event['nazev']
			);
			$db->insert('preklady', $data);

			$data['lang_id'] = 2;
			$db->insert('preklady', $data);

			$data['lang_id'] = 16;
			$db->insert('preklady', $data);
		}
	}
	$db->commit();
	echo 'All done! Tables `udalost` and `preklady` are synced now, all translation now have ids.';
}

catch(Exception $e) {
	$db->rollback();
	echo '!!! ERROR !!! '. $e;
}

echo "\n";
