<?php

/**
 * @see It6_GlobalCache::initialize()
 */
require_once ROOT . 'common/library/It6/Memcached.php';
class It6_GlobalCache extends It6_Memcached {

const DELETE_CACHE = 'GLOBAL_CAGHE_DELETE';

const TMP_DIR = '/tmp';

const KEY_PREFIX_LIVE_CALENDAR_SMALL = 'WK:lvcsm:';
const KEY_PREFIX_LIVE_CALENDAR = 'WK:lvc:';
const KEY_PREFIX_LAST_MINUTE_BETS = 'WK:lmb:';
const KEY_PREFIX_TERNO_BETS = 'WK:tb:';
const KEY_PREFIX_SUPERTIP_BETS = 'WK:sb:';
const KEY_PREFIX_SPORTSBOOK = 'WO:';
const KEY_PREFIX_GPARAM = 'PG:';
const KEY_PREFIX_HOST_MESSAGES = 'SHM:';
const KEY_PREFIX_CONFIRMD = 'CD:';

//const EXTENSION = '.cache';

private static $cache = true;
private static $expiration = GLOBAL_CACHE_MAX_EXPIRATION;
private static $session = false;
private static $userId = false;
private static $code = 200;

public static function createLocalizedKey($prefix, $langIso) {
	return $prefix . $langIso;
}

public static function turnOff() {
	self::$cache = false;
}

/**
 * @param integer $expiration TTL in seconds
 */
public static function maxExpiration($expiration) {
	if ( self::$expiration > $expiration )
		self::$expiration = $expiration;
}

public static function useSession() {
	self::$session = true;
}

public static function setUserTag($userId) {
	if ( false !== self::$userId && $userId != self::$userId )
		throw new Exception('Another user already set.');
	self::$userId = $userId;
}

public static function frame($path) {
	if ( '' != trim($path,'/') )
		$path = '/'. $_SESSION['lang'] . $path;
	echo "<!-- IT6:GCF:\"$path\" -->"; // this is not bulletproof, bacuse path cannot contain
	                                   // double quote, but it should be enough
}

/**
 * Renders frame content into string
 * @param string $path Global cache frame URI
 * @return string Frame content
 */
public static function getFrameContent($path) {
	require_once(ROOT . 'common/library/It6/GlobalCache/Frame.php');
	return It6_GlobalCache_Frame::render($path, true);
}

/**
 * Replaces frame placeholders by their frame content
 * @param string $content
 * @return string New content with frame injected
 */
public static function injectFrames($content) {
	return preg_replace_callback(
		'/<!-- IT6:GCF:\"([^"]*)" -->/',
		function($matches) {
			return It6_GlobalCache::getFrameContent($matches[1]);
		},
		$content
	);
}

public static function end($cacheLock = null) {
	
	$cacheOff = !defined('GLOBAL_CACHE_ENABLED')
			|| 1 != GLOBAL_CACHE_ENABLED
			|| array_key_exists(self::DELETE_CACHE, $_GET) //IT6: what's this!? klingon's madness?
			|| !empty($_POST)
			|| !self::$cache;


//		$unknownSession = true;
//		if ( true === self::$session )
//			$postfix = It6_GlobalCache_Starter::getSessionPartition(null, $unknownSession);
//		if ($unknownSession)
//			$postfix = 'N';

	$keys = It6_GlobalCache_Starter::getCacheKeyPair(true);
	$key = (true === self::$session ? $keys[0] : $keys[1]);

	$contents = ob_get_clean();
	
	if ( !empty($contents) ) {

		if (!$cacheOff) {
			self::setKey(
				$key,
				array(
					'HTTP_STATUS' => (empty(self::$code) ? 200 : self::$code),
					'content' => $contents,
				),
				self::$expiration
			);
		}
//file_put_contents('/tmp/cache-debug.log', strftime('%H:%M:%S ') . "$cacheLock:$fileName:R\n", FILE_APPEND);
//chmod('/tmp/cache-debug.log', 0666);
		It6_GlobalCache_Starter::releaseLock($cacheLock);
		echo static::injectFrames($contents);
	}
	else {
		It6_GlobalCache_Starter::releaseLock($cacheLock);
	}
}

/**
 * @param string|array $partition @see It6_GlobalCache::deleteKeys()
 */
public static function invalidate($partition) {
	return self::deleteKeys('WC', $partition);
}

public static function reset() {
	self::$cache = true;
	self::$expiration = GLOBAL_CACHE_MAX_EXPIRATION;
	self::$session = false;
	self::$userId = false;
	self::$code = 200;
}

public static function setResponseCode($code) {
	self::$code = $code;
}

public static function generateRandomFileName() {
	return tempnam(self::TMP_DIR, 'wc_tmp_');
}

/**
 * If version is not found, it is initialized, but if initialization fails, exception indicating race condition is thrown.
 * <pre>
 * Example: partition ('C' => 'live') // 'live' controller
 *          could get this partition value: 'live#12345'
 * </pre>
 * @param string $namespace Global namespace for partition(s)
 * @param array $partitions One or more partitions for which we are fetching versions (name => value)
 * @return integer|array One version or array of versions array(partition => version)
 */
public static function getVersion($namespace, $partitions) {
	$n = self::addPrefixToKey("V:$namespace:");
	$keys = array();
	foreach ($partitions as $name => $value)
		$keys[$name] = $n . "$name:$value";
	$versions = self::$memcache->getMulti(array_values($keys));
	if (!is_array($versions))
		$versions = array();
	foreach ($keys as $name => &$k) {
		if (!array_key_exists($k, $versions)) {
			$version = time();
			if (!self::$memcache->add($k, $version)) {
				$version = self::$memcache->get($k);
				if (!$version)
					throw new Exception('Race condition');
			}
			$k = $version;
		}
		else
			$k = $versions[$k];
	}
	return $keys;
}

} // It6_GlobalCache
