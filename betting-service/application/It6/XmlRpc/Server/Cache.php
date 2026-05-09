<?php

class It6_XmlRpc_Server_Cache extends Zend_XmlRpc_Server_Cache {

const CACHE_PREFIX = 'SM';
const CACHE_TIMEOUT = 86400; // 24 hours

protected static function getKeyName($class) {
	return self::CACHE_PREFIX . ":$class";
}

public static function save($class, Zend_Server_Interface $server) {

	$methods = $server->getFunctions();

	if ($methods instanceof Zend_Server_Definition) {
		$definition = new Zend_Server_Definition();
		foreach ($methods as $method) {
			if (!in_array($method->getName(), self::$_skipMethods)) {
				$definition->addMethod($method);
				$methods = $definition;
			}
		}
	}
	return It6_LocalCache::set(self::getKeyName($class), $methods, self::CACHE_TIMEOUT);
}

public static function get($class, Zend_Server_Interface $server) {
	return self::getMethodOnly($class, $server);
}

/**
 * Same as get() but can load specified method(s) only
 * @param string $class
 * @param Zend_Server_Interface $server
 * @param string|array $methodName If empty, all methods will be loaded, otherwise only those matching method name(s)
 * @return boolean
 */
public static function getMethodOnly($class, Zend_Server_Interface $server, $methodName = null) {
	$methods = It6_LocalCache::get(self::getKeyName($class));
	if (false === $methods)
		return false;
	if (empty($methodName)) {
		$_methods = $methods;
	}
	else {
		$_methods = new Zend_Server_Definition();
		foreach ($methods as $m) {
			list($mNamespace, $mName) = It6_XmlRpc_RequestHelper::parseNamespace($m['name']);
			$load = false;
			if (is_array($methodName)) {
				if (in_array($mName, $methodName)) {
					$load = true;
				}
			}
			else {
				if ($mName == $methodName) {
					$load = true;
				}
			}
			if ($load) {
				$_methods->addMethod($m);
			}
		}
	}
	$server->loadFunctions($_methods);
	return true;
}

public static function delete($class) {
	return It6_LocalCache::delete(self::getKeyName($class));
}

public static function deleteAll() {
	return It6_LocalCache::deleteKeys(self::CACHE_PREFIX . ':');
}

} // class