<?php

require_once ROOT . 'common/library/It6/Models/ControllerConvert.php';
require_once ROOT . 'common/library/It6/GlobalCache.php';

class It6_GlobalCache_Starter {

//const CACHE_DIR_BASE = 'cache-global/';
//const FILE_NAME_MAX_SIZE = 240;

private static $locks = array();
private static $keys = array(); // stack of (session key, no session key) pairs
private static $usedKey = null;
private static $tmpFiles = array();

/* DEPRECATED:
public static function CACHE_DIR() {
	// Please don't move to config (performance reason)
	return ROOT.self::CACHE_DIR_BASE.GLOBAL_CACHE_PREFIX;
}

private static function passFile($fileNameFirst, $fileNameSecond, &$fileUsed = null) {
			if ( empty($_POST) ) {
				foreach (array($fileNameFirst, $fileNameSecond) as $fileName) {
					if ( is_dir($fileName) ) {
						$fileName = current(glob($fileName.'/*'));  
						if ( is_readable($fileName) && filemtime($fileName) > time() ) {
							// cache hit
							$fileUsed = $fileName;
							$code = substr($fileName, -3);
							if ( 200 != $code )
								header("HTTP/1.0 $code",true,$code);
							ob_start();
							include $fileName;
							ob_flush();
							return true;
						}
					}
				}
			}
			return false;
}
*/

public static function registerTmpFile($filename) {
	self::$tmpFiles[$filename] = true;
}

public static function passCache(array $keys) {
	foreach ($keys as $key) {
		if (!empty($key)) {
			$value = It6_GlobalCache::getKey($key, $fetched);
			if ($fetched) {
				self::$usedKey = $key;
				$code = $value['HTTP_STATUS'];
				if ( 200 != $code )
					header("HTTP/1.0 $code",true,$code);
				echo It6_GlobalCache::injectFrames($value['content']);
				return true;
			}
		}
	}
	return false;
}

/**
 * IMPORTANT: constants CACHE_LOCK_TIMEOUT and CACHE_LOCK_WAIT_TIMEOUT must be defined!
 * @return boolean TRUE if page was served from cache, FALSE otherwise
 */
public static function begin(&$lockId = null) {

	if (!empty($_POST))
		$keys = false;
	else
		$keys = self::createCacheKeyPairFromUrl();

	if (false !== $keys) {

		$haveLock = false;
		$lockId = '';
		$start = time();
//$first = true;
		do {
			if (self::passCache($keys)) {
//$fileName = self::$usedKey; file_put_contents('/tmp/cache-debug.log', strftime('%H:%M:%S ') . self::$usedKey . ":hit.1\n", FILE_APPEND);
				return true;
			}
			// cache not found => should this thread create cache?
			// try to get lock
			if (empty($lockId))
				$lockId = 'WL:' . md5($keys[0]);
			$haveLock = It6_GlobalCache::addKey($lockId, 1, CACHE_LOCK_WAIT_TIMEOUT);
			if ($haveLock) {
				if (self::passCache($keys)) { // privileged read (preventing race condition)
					It6_GlobalCache::deleteKey($lockId);
//file_put_contents('/tmp/cache-debug.log', strftime('%H:%M:%S ') . "$lockId:" . self::$usedKey . ":hit.2\n", FILE_APPEND);
					return true;
				}
				// yes, this thread should create cache content
//file_put_contents('/tmp/cache-debug.log', strftime('%H:%M:%S ') . "$lockId:{$keys[0]}|{$keys[1]}:L\n", FILE_APPEND);
				self::$locks[$lockId] = time();
				break;
			}
//if ($first)
//  file_put_contents('/tmp/cache-debug.log', strftime('%H:%M:%S ') . "$lockId:{$keys[0]}|{$keys[1]}:W\n", FILE_APPEND);
			usleep(250000); // wait while other thread is probably working for us
//$first = false;
		} while (CACHE_LOCK_WAIT_TIMEOUT > time() - $start);

		self::$keys[] = $keys;
	}
	ob_start();
	return false;
}

public static function arrayHash(array $a, $associative) {
	if ($associative) {
		ksort($a);
		foreach ($a as $key => &$value)
			$value = "$key?". (is_array($value) ? self::arrayHash($value, true) : $value);
	}
	return md5(implode('$', $a));
}

/**
 * Constructs web content cache key pair
 * @param string $url URI of cached content
 * @return array|boolean Pair (session key, no session key) or FALSE if some key was too long
 */
public static function createCacheKeyPairFromUrl($url = null) {
	//It6_Models_ControllerConvert::explodeUrl($url, $lang, $controller, $action, $params, $query);
	if (isset($url)) {
		$path = (
			1 == preg_match('!^https?://[^/]+(/.*|)$!', $url, $matches)
			? $matches[1]
			: $url
		);
	}
	else {
		$path = $_SERVER['REQUEST_URI'];
	}
	$path = explode('?', $path);
	$query = array();
	if (!empty($path[1])) {
		parse_str($path[1], $query);
	}
	$path = $path[0];
	$cc = It6_Models_ControllerConvert::getDataFromUrl($path, $lang, $controller, $action, $params);
	if (empty($cc['nonassocParams'])) {
		$paramNames = array_keys($params);
		$params = array_values($params);
		if (!empty($cc['actionless'])) {
			$action = (empty($paramNames[0]) ? '' : $paramNames[0]);
		}
	}
	else {
		if (!empty($cc['actionless'])) {
			$action = array_shift($params);
		}
	}
	if (empty($action)) {
		$action = 'index';
	}
	$session = self::getSessionPartition();
	return self::createCacheKeyPair($lang, $controller, $action, $params, $query, $session);
}

/**
 * Constructs web content cache key pair
 * @param string $lang Language ISO
 * @param string $controller Controller
 * @param string $action Action
 * @param string $params URL parameteres (= remaining path components) (only values matters)
 * @param array $query Query parameters
 * @param string $session Session ID
 * @return array|boolean Pair (session key, no session key) or FALSE if some key was too long
 */
public static function createCacheKeyPair($lang, $controller, $action, $params = null, $query = null, $session = null) {
	if (empty($params)) {
		$params = array();
	}
	else {
		$params = array_values($params);
	}
	$param1 = (empty($params[0]) ? '' : $params[0]);
	$param2 = (empty($params[1]) ? '' : $params[1]);
	$param3 = (empty($params[2]) ? '' : $params[2]);
	$hashPath = self::arrayHash( array_merge(array($lang, $controller, $action), $params), false );
	$hashQuery = (empty($query) ? '' : self::arrayHash($query, true));
	if (empty($session))
		$session = self::getSessionPartition();
	$partitions = array(
		'G' => 'WC',
		'C' => "$lang:$controller",
		'S' => $session,
		'CA' => "$lang:$controller:$action",
		'CS' => "$lang:$controller:$session",
		'CAS' => "$lang:$controller:$action:$session",
		'CAP1' => "$lang:$controller:$action:$param1",
		'CAP12' => "$lang:$controller:$action:$param1:$param2",
		'CAP123' => "$lang:$controller:$action:$param1:$param2:$param3",
	);
	$vs = It6_GlobalCache::getVersion('WC', $partitions);
	// 9*(10 chars per version + 1 char sep) =  99
	// 3*(32 chars md5 hash)                 =  96
	// 2 (WC) + 3 * (1 sep)                  =   5
	// sum                                   = 200
	// free                                  => 55
	$keyN = "WC:$hashPath:$hashQuery:";
	$keyS = $keyN . $session . "#{$vs['G']}.{$vs['C']}.{$vs['S']}.{$vs['CA']}.{$vs['CS']}.{$vs['CAS']}.{$vs['CAP1']}.{$vs['CAP12']}.{$vs['CAP123']}";
	if (strlen($keyS) > 250)
		return false;
	$keyN = $keyN . 'N' . "#{$vs['G']}.{$vs['C']}.{$vs['CA']}.{$vs['CAP1']}.{$vs['CAP12']}.{$vs['CAP123']}";
	if (strlen($keyN) > 250) // should redundant (N should be always at most same length as S)
		return false;
	return array($keyS, $keyN);
}

public static function getCacheKeyPair($pop) {
	return ($pop ? array_pop(self::$keys) : self::$keys[count(self::$keys) - 1]);
}

/**
 * Creates session partition (caches partition value for default session ID to not call md5 multipletimes)
 * @param boolean $unknown Will be set to TRUE if session ID is unknown, to FALSE otherwise
 * @return string
 */
public static function getSessionPartition($sessionId = null, &$unknown = null) {
	static $cache = false;
	if (!empty($sessionId)) {
		$unknown = false;
		return 'S' . md5($sessionId);
	}
	else {
		if (false === $cache)
			$cache = (array_key_exists('PHPSESSID', $_COOKIE) ? md5($_COOKIE['PHPSESSID']) : '');
		$unknown = empty($cache);
		return 'S' . $cache;
	}
}

/**
 * Release one or all cache locks (ID was obtained through It6_GlobalCache_Starter::begin())
 * @param string|NULL $lockName Lock to be released or NULL when all should be released
 */
public static function releaseLock($lockId = null) {
	if (!empty($lockId)) {
		if (!empty(self::$locks[$lockId])) {
//file_put_contents('/tmp/cache-debug.log', strftime('%H:%M:%S ') . "$lockId:R\n", FILE_APPEND);
			It6_GlobalCache::deleteKey($lockId);
			unset(self::$locks[$lockId]);
		}
	}
	else {
		foreach (self::$locks as $lockId => $time) {
//file_put_contents('/tmp/cache-debug.log', strftime('%H:%M:%S ') . "$lockId:R\n", FILE_APPEND);
			It6_GlobalCache::deleteKey($lockId);
		}
		self::$locks = array();
	}
}

/**
 * Good to be registered as shutdown function...
 */
public static function cleanUp() {
	self::releaseLock();
	foreach (self::$tmpFiles as $file => $_)
		@unlink($file);
}

} // It6_GlobalCache_Starter
