<?php

if(!defined('ROOT')) define('ROOT',dirname( dirname( dirname(__FILE__) ) ) . '/');

header("Content-type:text/html; charset=UTF-8");
#######Tohle se pak vyhodi jen pro vyvoj##################
/*
$realm = Array("it6"=>"victoriaTip");
$authorized = false;
if (!empty($_SERVER['PHP_AUTH_USER']))
	foreach ($realm as $user => $passwd) {
		if($_SERVER['PHP_AUTH_USER'] == $user && $_SERVER['PHP_AUTH_PW'] == $passwd) {
			$authorized = true;
			break;
	}
}
if (!$authorized)	{
	header("HTTP/1.0 401 Unauthorized");
	header("WWW-Authenticate: Basic realm='Basic'");
	echo "Not authorized access";
	exit;
}
*/

#########################################################

require_once(ROOT.'common/includes.inc.php');
include_once(ROOT.'common/init-global-cache.inc.php');

if(
	mb_strlen($_SERVER['REQUEST_URI']) == 0
	|| $_SERVER['REQUEST_URI'] == '/'
) {
	$GLOBALS['globalCacheOff'] = true;
}

$lock = null;
if ( empty($GLOBALS['globalCacheOff']) && It6_GlobalCache_Starter::begin($lock) ) 
	exit;

require_once(ROOT.'web/config_local.php');
require_once ROOT.'web/application/bootstrap.php';

Bootstrap::run();

if ( empty($GLOBALS['globalCacheOff']) ) {
	It6_GlobalCache::end($lock);
}

Bootstrap::close();

