<?php

/**
 * Class to allow to overload default select to accomodate for parts of sql that zend doesnt support
 * @author Martin Bohal
 *
 */
class It6_Db_Select extends Zend_Db_Select {
	const STRAIGHT_JOIN = 'straightJoin';
	
	const SQL_STRAIGHT_JOIN = 'STRAIGHT_JOIN';
		
	protected static $_partsInitLoc = array(
		self::STRAIGHT_JOIN     => false
	);
	
	
	public function __construct(Zend_Db_Adapter_Abstract $adapter) {
		parent::$_partsInit = array_merge(self::$_partsInitLoc, parent::$_partsInit);
		parent::__construct($adapter);
	}
	
	/**
	 * Render STRAIGHT_JOIN clause
	 *
	 * @param string   $sql SQL query
	 * @return string
	 */
	protected function _renderStraightJoin($sql)
	{
		if ($this->_parts[self::STRAIGHT_JOIN]) {
			$sql .= ' ' . self::SQL_STRAIGHT_JOIN;
		}
	
		return $sql;
	}
	
	/**
	 * Makes the query SELECT STRAIGHT_JOIN.
	 *
	 * @param bool $flag Whether or not the SELECT is STRAIGHT_JOIN (default true).
	 * @return Zend_Db_Select This Zend_Db_Select object.
	 */
	public function straightJoin($flag = true)
	{
		$this->_parts[self::STRAIGHT_JOIN] = (bool) $flag;
		return $this;
	}
}