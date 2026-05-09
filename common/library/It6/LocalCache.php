<?php

/**
 * This files implements common local cache interface with APC backend for non-CLI scripts or array backend for CLI scripts.
 */

//abstract class It6_LocalCache_Abstract {
//}

if (defined('RUNNING_FROM_CLI')) {
	
	class It6_LocalCache { //extends It6_LocalCache_Abstract {

		private static $cache = array();

		public static function set($key, $data, $ttl = 0) {
			$to = (0 == $ttl ? false : time() + $ttl);
			self::$cache[$key] = array($to, $data);
			return true;
		}

		public static function add($key, $data, $ttl = 0) {
			// from CLI we assume no more than one thread, it's easy
			$_ = self::get($key, $fetched);
			if ($fetched)
				return false;
			else
				return self::set($key, $data, $ttl);
		}

		public static function get($key, &$fetched = null) {
			if (!array_key_exists($key, self::$cache)) {
				$fetched  = false;
				return false;
			}
			$item = self::$cache[$key];
			$to = $item[0];
			if (false !== $to && time() > $to) {
				$fetched  = false;
				return false;
			}
			else {
				$fetched = true;
				return $item[1];
			}
		}

		public static function delete($key) {
			unset(self::$cache[$key]);
			return true;
		}
		
		/**
		 * Delete given keys from cache
		 * @param string|array $keys REGEXP or list of keys for keys to be deleted
		 */
		public static function deleteKeys($keys) {
			if (is_array($keys)) {
				foreach ($keys as $key)
					unset(self::$cache[$key]);
			}
			else {
				foreach (array_keys(self::$cache) as $key) {
					if (1 == preg_match($keys, $key))
						unset(self::$cache[$key]);
				}
			}
		}

		public static function flush() {
			static::$cache = array();
			return true;
		}
	}

}
else {

	class It6_LocalCache { //extends It6_LocalCache_Abstract {
		
		//BEGIN: helper methods, not part of interface
		public static function getKeyPrefix() {
			static $prefix = false;
			if (false === $prefix)
				$prefix = (defined('LOCAL_CACHE_KEY_PREFIX') ? LOCAL_CACHE_KEY_PREFIX : '0');
			return $prefix;
		}

		/**
		 * Adds prefix to key(s) that is specific for application environment
		 * @param string|array $key One key or key/data associative array
		 * @return string|array Key or new_key/data, keys with prefix prepended
		 */
		public static function addPrefixToKey($key) {
			$prefix = self::getKeyPrefix();
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
		* Removes application environment specific prefix from key(s)
		* @param string|array $key One key or key/data associative array
		* @return string|array Key or new_key/data, keys with prefix removed
		*/
		public static function removePrefixFromKey($key) {
			$prefixLen = strlen(self::getKeyPrefix());
			if (is_array($key)) {
				$data = array();
				foreach ($key as $_key => $value)
					$data[ substr($_key, $prefixLen) ] = $value;
				return $data;
			}
			else
				return substr($key, $prefixLen);
		}
		//END: helper methods, not part of interface

		public static function set($key, $data, $ttl = 0) {
			return apc_store(self::addPrefixToKey($key), $data, $ttl);
		}

		public static function add($key, $data, $ttl = 0) {
			return apc_add(self::addPrefixToKey($key), $data, $ttl);
		}
		
		public static function get($key, &$fetched = null) {
			return apc_fetch(self::addPrefixToKey($key), $fetched);
		}
		
		public static function delete($key) {
			return apc_delete(self::addPrefixToKey($key));
		}
		
		/**
		 * Delete given keys from cache
		 * @param string|array $keys Key prefix to match (only safe characters, will be part of REGEXP exactly as "/^cache_prefix:{$keys}/")
		 *                           or list of keys for keys to be deleted
		 */
		public static function deleteKeys($keys) {
			if (!is_array($keys)) {
				$keyList = array();
				$keys = self::addPrefixToKey($keys);
				$apc = new APCIterator('user', "/^$keys/");
				foreach ($apc as $key => $value) {
					$keyList[] = $key;
				}
			}
			else {
				$keyList = array();
				foreach ($keys as $key)
					$keyList = self::addPrefixToKey($key);
			}
			foreach ($keyList as $key)
				apc_delete($key);
		}

		/**
		 * Deletes all the keys in current application environment
		 */
		public static function flush() {
			static::deleteKeys('');
			return true;
		}

	}

}
