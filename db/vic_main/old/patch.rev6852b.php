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
// $db->query("SET time_zone='GMT'");

// SELECT `event_id`,`type`,`url`,COUNT(event_id) AS c,GROUP_CONCAT(event_id) AS u FROM seo_url GROUP BY `lang_id`,`type`,`url` HAVING c>1;
// 
// SELECT `lang_id`,`type`,`url`,COUNT(event_id) AS c AS u FROM seo_url GROUP BY `lang_id`,`type`,`url` HAVING c>1;

if ($test)
	echo "Running in test mode, database won't be updated.\n";
else
	$db->beginTransaction();
try {
	$select = $db->select()->from('seo_url', array(
			'langId' => 'lang_id',
			'type',
			'url',
			'c' => new Zend_Db_Expr('COUNT(event_id)'),
			//'eventIds' => new Zend_Db_Expr('GROUP_CONCAT(event_id)'),
		))
		->group(array('lang_id', 'type', 'url'))
		->having('c>1');
	$rows = $select->query()->fetchAll();

	if (empty($rows))
		echo "No duplicate URLs found.\n";
	else
		echo count($rows) . " duplicate URLs found\nlangId:type:url:count\n"; //:eventIds\n";
	foreach ($rows as $row) {
		$langId = $row['langId'];
		$type = $row['type'];
		$url = rtrim($row['url'], '/');
		$count = $row['c'];
		$eventIds = $db->select()->from('seo_url', 'event_id')
			->where('lang_id=?', $langId)
			->where('type=?', $type)
			->where('url=?', "$url/")
			->query()
			->fetchAll();
		$eventIds = array_map(function($r) { return $r['event_id']; }, $eventIds);
		$urls = $db->select()->from('seo_url', 'url')
			->where('lang_id=?', $langId)
			->where('type=?', $type)
			->where('url LIKE ?', "$url%")
			->group('url')
			->query()
			->fetchAll();
		$urls = array_map(function($r) { return $r['url']; }, $urls);
		$n = count($eventIds);
		$suff = 1;
		$newUrls = array();
		for ($i = 1; $i < $n; ++$i) {
			$eventId = $eventIds[$i];
			while (true) {
				$newUrl = "$url-{$suff}/";
				$freeUrl = true;
				foreach ($urls as $_url) {
					if (0 == strcasecmp($_url, $newUrl)) {
						$freeUrl = false;
						break;
					}
				}
				if ($freeUrl)
					break;
				++$suff;
			}
			$urls[] = $newUrl;
			$newUrls[$eventId] = $newUrl;
			if (!$test) {
				$db->update(
					'seo_url',
					array('url' => $newUrl),
					array(
						'lang_id=?' => $langId,
						'type=?' => $type,
						'event_id=?' => $eventId,
					)
				);
			}
		}
		echo "$langId:$type:$url:$count\n"; //:{$row['eventIds']}\n";
		foreach ($newUrls as $eventId => $newUrl)
			echo "\t$eventId: \"$newUrl\"\n";
	}

	if (!$test)
		$db->commit();
}
catch (Exception $e) {
	$db->rollback();
}

echo "DONE.\n";
