<?php

function it6_memorycache_set_key($key, $data, $ttl = 0) {
	return apc_store($key, $data, $ttl);
}

function it6_memorycache_get_key($key, &$fetched = null) {
	return apc_fetch($key, $fetched);
}

function it6_memorycache_delete_key($key) {
	return apc_delete($key);
}

/**
 * Delete given keys from cache
 * @param string|array $keys REGEXP or list of keys for keys to be deleted
 */
function it6_memorycache_delete_keys($keys) {
	if (!is_array($keys)) {
		$keyList = array();
		$apc = new APCIterator('user', $keys);
		foreach ($apc as $key => $value)
			$keyList[] = $key;
	}
	else
		$keyList = $keys;
	foreach ($keyList as $key)
		apc_delete($key);
}
