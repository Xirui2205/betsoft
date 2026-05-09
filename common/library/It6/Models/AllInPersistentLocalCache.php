<?php

class It6_Models_AllInPersistentLocalCache extends It6_Models_Abstract {

// derived class must have following constant defined
// it is commented here for easy error detection
//const PERSISTENT_CACHE_KEY = 'A:';

const PERSISTENT_CACHE_TTL = 0; // forever

public static function readDataAll(&$db = null) {
	$fetched = false;
	$data = It6_LocalCache::get(static::PERSISTENT_CACHE_KEY, $fetched);
	if ($fetched) {
		static::_registerCachedClass();
		static::$_cache = $data;
	}
	else {
		parent::readDataAll($db);
		It6_LocalCache::set(static::PERSISTENT_CACHE_KEY, static::$_cache, static::PERSISTENT_CACHE_TTL);
	}
	return static::$_cache;
}

public static function invalidatePersistentCache() {
	return It6_LocalCache::delete(static::PERSISTENT_CACHE_KEY);
}

} // class
	