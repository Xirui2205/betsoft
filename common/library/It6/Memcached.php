<?php
class It6_Memcached {
	
/**
 * Current implementation of memory cache is Memcached
 * @var Memcached
 */
protected static $memcache = false;

/**
 * Call It6_GlobalCache::initialize() on entry point of web app.
 * @param array $config Data for memcached client initialization
 * </pre>
 *    array(
 *       'servers' => Memcached server pool (see Memcached::addServers()),
 *    )
 * </pre>
 */
public static function initialize(array $config) {
	if (false === self::$memcache) {
		self::$memcache = new Memcached();
		self::$memcache->addServers($config['servers']);
	}
}

// *** MEMORY CACHE ROUTINES ***
// NOTE: implementation of some methods is bound to Memcached specifics

public static function getKeyPrefix() {
	static $prefix = false;
	if (false === $prefix)
		$prefix = (defined('GLOBAL_CACHE_KEY_PREFIX') ? GLOBAL_CACHE_KEY_PREFIX : '0');
	return $prefix;
}

/**
 * Adds prefix to key(s) that is specific for application environment
 * @param string|array $key One key or key/data associative array
 * @return string|array Key or new_key/data, keys with prefix prepended
 */
public static function addPrefixToKey($key) {
	$prefix = static::getKeyPrefix();
	if (is_array($key)) {
		$data = array();
		foreach ($key as $_key => $value)
			$data["$prefix:$_key"] = $value;
		return $data;
	}
	else
		return "$prefix:$key";
}

/**
 * Adds prefix to all keys in the list
 * @param array $keys List of keys
 * @return array List with prefixed keys
 */
public static function addPrefixToKeyList($keys) {
	$prefix = self::getKeyPrefix();
	foreach ($keys as &$key) {
		$key = "$prefix:$key";
	}
	return $keys;
}

/**
 * Removes application environment specific prefix from key(s)
 * @param string|array $key One key or key/data associative array
 * @return string|array Key or new_key/data, keys with prefix removed
 */
public static function removePrefixFromKey($key) {
	$prefixLen = strlen(static::getKeyPrefix()) + 1;
	if (is_array($key)) {
		$data = array();
		foreach ($key as $_key => $value)
			$data[ substr($_key, $prefixLen) ] = $value;
		return $data;
	}
	else
		return substr($key, $prefixLen);
}
	
public static function addKey($key, $data, $ttl = 0) {
	return static::$memcache->add(static::addPrefixToKey($key), $data, $ttl);
}

public static function setKey($key, $data, $ttl = 0) {
	return static::$memcache->set(static::addPrefixToKey($key), $data, $ttl);
}

public static function setKeys($data, $ttl = 0) {
	return static::$memcache->setMulti(static::addPrefixToKey($data), $ttl);
}

public static function getKey($key, &$fetched = null) {
	$value = static::$memcache->get(static::addPrefixToKey($key));
	if ($value)
		$fetched = true;
	else
		$fetched = (Memcached::RES_NOTFOUND != static::$memcache->getResultCode());
	return $value;
}

/**
 * @param array $keys List of keys to fetch
 * @return array|boolean FALSE or fetched data, keys not fetched will be missing
 */
public static function getKeys($keys) {
	$data = static::$memcache->getMulti(static::addPrefixToKeyList($keys));
	return static::removePrefixFromKey($data);
}

public static function deleteKey($key) {
	return static::$memcache->delete(static::addPrefixToKey($key));
}

/**
 * NOTE: Memcached doesn't support iteration of stored data, searching or smilar features, other approach is used (versioning),
 * Delete given keys from cache
 * @param string $namespace Global namespace (eg. "WC" for web content)
 * @param array $partitions Versioning codes for partition
 *              eg. "C" for "controller" partition in "WC" global namespace with value 'live' would be array('C' => 'live')
 */
public static function deleteKeys($namespace, $partitions) {
	//file_put_contents('/tmp/cache-debug.log', strftime('%H:%M:%S ') . 'delete namespace: ' . $namespace . ' ' . print_r($partitions, true), FILE_APPEND);
	$n = static::addPrefixToKey("V:$namespace:");
	foreach ($partitions as $name => $value) {
		$k = $n . "$name:$value";
		if (!static::$memcache->increment($k))
			static::$memcache->add($k, time());
	}
}

public static function getResultCode() {
	return static::$memcache->getResultCode();
}

/**
 * NOTE: this method flushes all environments using same Memcached server
 */
public static function flush() {
	return static::$memcache->flush();
}

}