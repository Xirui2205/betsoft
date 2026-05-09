<?php
class It6_Synchronize {

const KEY_PREFIX = 'lock.';
const WAIT_SLEEP_TIME = 20000; //time in microseconds

static protected $_pool = array();
static protected $_shutdownRegistred = false;

protected $_key;
protected $_keyHash;
protected $_expire;
protected $_stack;

/**
 * 
 * @param mix $key keys
 * @param bool $lock if true begins lock the synch process
 * @param int $expire expiration in miliseconds
 */

protected function __construct($key, $keyhash, $lock, $expire) {
	
	$this->_key = $key;
	$this->_keyHash = $keyhash;
	$this->_expire = $expire;
	$this->_stack = array();
	
	if ( $lock ) {
		$this->lock();
	}
}

public function __destruct() {
	$this->_unlockAll();
}

public static function getInstance($key, $lock = true, $expire = 60000) {
	if ( !static::$_shutdownRegistred ) {
		register_shutdown_function(array('It6_Synchronize', 'unlockWholePool'));
		static::$_shutdownRegistred = true;
	}
	
	$keyHash = static::_getKeyHash($key);
	if ( !array_key_exists($keyHash, static::$_pool) ) {
		static::$_pool[$keyHash] = new It6_Synchronize($key, $keyHash, $lock, $expire);
	}
	elseif ( $lock == true ) {
		static::$_pool[$keyHash]->lock($expire);
	}
	
	return static::$_pool[$keyHash];
}

/**
 * Begins synchronized code
 */

public function lock($expire = null) {
	
	if ( !$this->isLocked() ) {
		if ( null == $expire ) {
			$expire = $this->_expire;
		}
		
		while ( !static::_addLockKey($this->_keyHash, $expire) ) {
			usleep(static::WAIT_SLEEP_TIME);
		}
	}
	
	$this->_pushToStack();
}

/**
 * Ends synchronized code
 */

public function unlock() {
	$nestedLevels = $this->_popFromStack();
	
	if ($nestedLevels === false) {
		throw new Exception('Not locked -> can\'t unlock');
	}
	
	if ( $nestedLevels === 1 ) {
		static::_deleteLockKey($this->_keyHash);
	}
}

public function isLocked() {
	return !empty($this->_stack);
}

static protected function _getKeyHash($key) {
	if ( is_array($key) ) {
		ksort($key);
	}
	
	foreach ( $key as $k => &$v ) {
		$v = (string)$v;
	}
	return static::KEY_PREFIX.md5(serialize($key));
}

static protected function _addLockKey($key, $expire) {
	
	if (It6_Memcached::addKey($key, true, ceil($expire/1000)) ){
		return true;
	}
	elseif (It6_Memcached::getResultCode() == Memcached::RES_NOTSTORED) {
		return false;
	}
	else {
		throw new Exception("Memcached : can't store key : ".It6_Memcached::getResultCode());
	}
}

static protected function _deleteLockKey($key) {
	if ( !It6_Memcached::deleteKey($key) ) {
		throw new Exception("Memcached : can't delete key : ".It6_Memcached::getResultCode());
	}
}

protected function _pushToStack() {
	array_push($this->_stack, true);
}

/**
 * @return integer|boolean nested lock levels BEFORE pop or false if there's nothing to pop
 */
protected function _popFromStack() {
	if ( !$this->isLocked() ) {
		return false;
	}
	
	$nestedLevels = count($this->_stack);
	
	array_pop($this->_stack);
	
	return $nestedLevels;
}

public function _unlockAll($warning = true) {
	if ( $warning && $this->isLocked() ) {
		It6_Log::warn(
			'Synchronize %synchname% was not properly unlocked.(remaining locks count was: %count%)', 
			It6_Log::TAG_SYNCHRONIZATION, 
			array('synchname' => $this->_key, 'count' => count($this->_stack))
		);
	}
	
	foreach($this->_stack as $_) {
		$this->unlock();
	}
}

static public function unlockWholePool() {
	foreach ( static::$_pool as $p ) {
		$p->_unlockAll();
	}
}

} //It6_Synchronize