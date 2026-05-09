<?php

if ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'])
	exit;

if (!empty($_GET['setKeyName'])) {
	$result = apc_store($_GET['setKeyName'], unserialize($_GET['setKeyValue']), empty($_GET['setKeyTtl']) ? 0 : intval($_GET['setKeyTtl']));
	echo ($result ? 1 : 0);
}
else if (!empty($_GET['getKey'])) {
	$result = apc_fetch($_GET['getKey'], $fetched);
	if ($fetched)
		echo '1' . serialize($result);
	else
		echo 0;
}
else if (!empty($_GET['deleteKey'])) {
	echo (apc_delete($_GET['deleteKey']) ? 1 : 0);
}
else if (!empty($_GET['deleteKeys'])) {
	$keys = unserialize($_GET['deleteKeys']);
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
