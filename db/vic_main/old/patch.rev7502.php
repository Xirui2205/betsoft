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
	echo "Running in test mode, database won't be updated.\n";
else
	$db->beginTransaction();
	
try {

	$max = $db->select()
		->from('udalost',array('max' => 'MAX(pozice_offergen)'))
		->query()->fetchAll();
	$count = $db->select()
		->from('sport', array('count' => 'COUNT(*)'))
		->query()->fetchAll();
	
	$db->query( 'UPDATE udalost SET pozice_offergen = pozice_offergen + '.( max(1000 * ($count[0]['count'] + 1), $max[0]['max'] + 1) ) );
	
	$db->insert(
		'database_patch',
		array(
			'revision' => '7502',
			'not_reapplicable' => null,
			'note' => 'Offergen event ordering by sport. (mantis:562)'
		)
	);

	
	$sports = $db->select()
		->from('sport',array('id' => 'sport_id'))
		->order('pozice')
		->query()->fetchAll();

	$s = 0;
	foreach ( $sports as $sport ) {
		++$s;
		$e = 0;
		$events = $db->select()
			->from('udalost',array('id' => 'udalost_id'))
			->where('sport_id = ?', $sport['id'])
			->order('pozice_offergen')
			->query()->fetchAll();
		foreach( $events as $event ) {
			++$e;
			//echo $event['id'] . "\n";
			
			$order = $s*1000 + $e;
			
			echo "Updating udalost of `udalost_id` = '$e' `pozice_offergen` to '$order'...";
			
			$db->update(
				'udalost',
				array('pozice_offergen' => $order),
				array(
					'udalost_id = ?' =>  $event['id'],
					'sport_id = ?' =>  $sport['id']
				)
			);
			
			echo "done\n";
				
		}
	}

	

	if (!$test)
		$db->commit();
		
	echo "COMPLETED SUCCESSFULLY.\n";
}
catch (Exception $e) {
	echo $e->getMessage();
	$db->rollback();
}
