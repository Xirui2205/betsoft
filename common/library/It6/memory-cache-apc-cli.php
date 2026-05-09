<?php

/*
 APC cannot access 'user' cache, so we must call some proxy scripting served by webserver.
 URL of proxy must be defined in MEMORYCACHE_APC_CLI_PROXY.
*/

function it6_memorycache_set_key($key, $data, $ttl = 0) {
	$result = file_get_contents(
		MEMORYCACHE_APC_CLI_PROXY
		. '?setKeyName=' . urlencode($key)
		. '&setKeyValue=' . urlencode(serialize($data))
		. '&setKeyTtl=' . urlencode(intval($ttl))
	);
	return ('1' == trim($result));
}

function it6_memorycache_get_key($key, &$fetched = null) {
	$result = file_get_contents(
		MEMORYCACHE_APC_CLI_PROXY
		. '?getKey=' . urlencode($key)
	);
	$fetched = ('1' == substr($result, 0, 1));
	if (!$fetched)
		return false;
	else
		return unserialize(substr($result, 1));
}

function it6_memorycache_delete_key($key) {
	$result = file_get_contents(
		MEMORYCACHE_APC_CLI_PROXY
		. '?deleteKey=' . urlencode($key)
	);
	return ('1' == trim($result));
}

/**
 * Delete given keys from cache
 * @param string|array $keys REGEXP or list of keys for keys to be deleted
 */
function it6_memorycache_delete_keys($keys) {
	$result = file_get_contents(
		MEMORYCACHE_APC_CLI_PROXY
		. '?deleteKeys=' . urlencode(serialize($keys))
	);
}
