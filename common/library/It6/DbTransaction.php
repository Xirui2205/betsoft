<?php

class It6_DbTransaction {

	private static $stack = array();
	private static $rollBack = array();
	private static $synchronizes = array();
	
	public function __destruct() {
		$stack = &self::getStack($db);
		if (!empty($stack))
			throw new Exception('Uncommited transaction detected.');
	}

	public static function begin($db = null, $synchronize = null) {
		$stack = &self::getStack($db);

		if ( self::isRollBack($db) ) {
			throw new Exception("Can not start transaction after rollback.");
		}
		
		if ( !is_null($synchronize) ) {
			static::lock($db, $synchronize);
		}

		if ( empty($stack) ) {
			$db->beginTransaction();
		}
		array_push( $stack, empty($stack) );
	}

	public static function lock($db, $synchronize) {		
		$synchronize->lock();

		if ( !in_array($synchronize,self::$synchronizes) ) {
			self::$synchronizes[] = $synchronize;
		}
	}
	
	public static function commit($db = null) {
		$stack = &self::getStack($db);
		if ( self::isRollBack($db) ) {
			throw new Exception("Can not commit after rollback.");
		}
		$t = array_pop( $stack );
		if ( true ===  $t ) {
			try {
				$db->commit();
				foreach (self::$synchronizes as $s) {
					$s->_unlockAll(false);
				}
			} catch (Exception $e) {
				array_push($stack, $t);
				throw $e;
			}
		}
		else if ( null === $t ) {
			throw new Exception("No transaction to commit.");
		}
	}

	public static function rollback($db = null) {
		//TODO: klinger: refactor all occurences of this function to rollback() (note the lowercase 'b'), consecutively remove this function
		//      Old function name was a real disappointment...
		$stack = &self::getStack($db);
		$t = array_pop( $stack );
		self::setRollBack($db);
		if ( true ===  $t ) {
			try {
				$db->rollBack();
				foreach (self::$synchronizes as $s) {
					$s->_unlockAll(false);
				}
				self::resetRollBack($db);
			} catch (Exception $e) {
				array_push($stack, $t);
				throw $e;
			}
		}
		else if ( null === $t ) {
			throw new Exception("No transaction to rollback.");
		}
	}

	private static function &getStack(&$db) {
		if ( null == $db ) $db = Zend_Registry::get('db');
		if ( !array_key_exists(self::getDbHash($db), self::$stack) ) {
			self::$stack[self::getDbHash($db)] = array();
		}

		return self::$stack[self::getDbHash($db)];
	}

	private static function isRollBack($db) {
		return array_key_exists(self::getDbHash($db), self::$rollBack);
	}

	private static function setRollBack($db) {
		self::$rollBack[self::getDbHash($db)] = true;
	}

	private static function resetRollBack($db) {
		unset(self::$rollBack[self::getDbHash($db)]);
	}

	private static function getDbHash($db) {
		return spl_object_hash($db);
	}
}
