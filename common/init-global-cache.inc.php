<?php

// define all global cache related constants
// TODO: make it environment specific

if(!defined('DEFAULT_LANG')) define('DEFAULT_LANG','cs'); // implicitni jazyk iso (napr. "cs")
if(!defined('DEFAULT_LANG_ID')) define('DEFAULT_LANG_ID',1); // implicitni jazyk - id: 1=cs, 2=en

//DEPRECATED:
// Please don't move to config (performance reason)
//if(!defined('GLOBAL_CACHE_PREFIX'))
//	define('GLOBAL_CACHE_PREFIX','web/');

if (!defined('CACHE_LOCK_TIMEOUT')) define('CACHE_LOCK_TIMEOUT', 10); // TTL of cache lock (in seconds)
if (!defined('CACHE_LOCK_WAIT_TIMEOUT')) define('CACHE_LOCK_WAIT_TIMEOUT', 11); // max overall time to wait for cache lock (in seconds)
if (!defined('GLOBAL_CACHE_MAX_EXPIRATION')) define('GLOBAL_CACHE_MAX_EXPIRATION', 18000); // in seconds, 18000 = 5 hours
// TTL in seconds for cache of error pages (4XX, 5XX pages)
// if not defined, default expiration is used; if negative value, no cacheing is performed
if (!defined('GLOBAL_CACHE_ERROR_PAGE_EXPIRATION')) define('GLOBAL_CACHE_ERROR_PAGE_EXPIRATION', 10);
// TTL in seconds for cached value of coupon validation result
// if not defined, cache is turned off
//if (!defined('CACHE_COUPON_VALIDITY_TIMEOUT')) define('CACHE_COUPON_VALIDITY_TIMEOUT', 10);

if (!defined('GLOBAL_CACHE_KEY_PREFIX')) define('GLOBAL_CACHE_KEY_PREFIX', '0'); // for parallel evironments in single cache daemons
if (!defined('LOCAL_CACHE_KEY_PREFIX')) define('LOCAL_CACHE_KEY_PREFIX', '0'); // for parallel evironments in single cache daemons

require_once ROOT . 'common/library/It6/GlobalCache.php';
require_once ROOT.'common/library/It6/GlobalCache/Starter.php';

register_shutdown_function(array('It6_GlobalCache_Starter', 'cleanUp'));

include_once('init-global-cache-instance.inc.php');
