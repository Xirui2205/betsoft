<?php

if(!defined('ROOT')) define('ROOT', dirname( dirname( dirname(__FILE__) ) ) . '/');

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');
require_once(ROOT.'common/library/It6/Captcha/Image.php');

if (!empty($_GET['id'])) {
	$id = $_GET['id'];
	$key = It6_Captcha_Image::getGlobalCacheKey($id);
	$image = It6_GlobalCache::getKey($key);
	if (!empty($image)) {
		header('Content-Type: ' . $image['mime']);
		header('Content-Length: ' . $image['size']);
		echo $image['data'];
		exit;
	}
}

header('HTTP/1.0 404 Not Found');
