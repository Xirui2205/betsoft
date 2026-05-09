<?php
/**
 * @see It6_WsExtesion_Client_Filter
 * @author h3poun
 */
class It6_WsExtension_Server_Filter extends It6_WsExtension_Server_Query implements It6_WsExtension_Filter {

public function __construct($id, $params, $preprocessing = null, $postprocessing = null) {
	parent::__construct($id, $params, $preprocessing, $postprocessing);
}

public function modifyQuery(&$select, $colConv, $metadata) {
	if (array_key_exists(static::PARAM_FILTER, $this->params)) {
		$db = $select->getAdapter();
		$where = $this->_walkFilter(
			$this->params[static::PARAM_FILTER],
			array_flip($metadata[It6_WsExtension_Server_Columns::META_CONV]),
			$db
		);
		return $select->where($where);
	}
}

/**
 * Recursive function for translating filter data to SQL WHERE expression
 * @param array $filter Current group or condition definition
 * @param array $colConv Dictionary objName => dbName
 * @param Zend_Db_Adapter $db used for quoting
 * @return string SQL WHERE expression given by filter data
 */
protected function _walkFilter($filter, $colConv, &$db) {
	$where = '';
	if (It6_WsExtension_Client_Filter::isGroupDefinition($filter)) {
		$op = (array_key_exists(static::DEF_OP, $filter) ? $filter[static::DEF_OP] : 'AND');
		$neg = (array_key_exists(static::DEF_NEG, $filter) ? $filter[static::DEF_NEG] : false);
		if ($neg)
			$where .= 'NOT';
		$where .= '(';
		$first = true;
		foreach ($filter as $key => $value) {
			if (is_numeric($key)) {
				if ($first)
					$first = false;
				else
					$where .= " $op ";
				$where .= $this->_walkFilter($value, $colConv, $db);
			}
		}
		$where .= ')';
	}
	else {
		$op = (array_key_exists(static::DEF_OP, $filter) ? $filter[static::DEF_OP] : '=');
		//TODO: check operator is valid?
		$neg = (array_key_exists(static::DEF_NEG, $filter) ? $filter[static::DEF_NEG] : false);
		$columnExpr = (array_key_exists(static::DEF_EXPR, $filter) ? $filter[static::DEF_EXPR] : false);
		$field = $filter[static::DEF_FIELD];
		
		if(is_array($field)) {
			$objName = key($field);
			$value = $field[$objName];
		}
		else
			$objName = $field;
			
		$dbName = $colConv[$objName];
		if ( 0 == preg_match("/\\.|\\(/", $dbName ) )
			$dbName = $db->quoteIdentifier($colConv[$objName]);
		else
			$dbName = $colConv[$objName];
		
		if (empty($columnExpr))
			$expr = $dbName;
		else
			$expr = str_replace('?', $dbName, $columnExpr);
		$expr .= ' ' . $op;
		
		if(is_array($field)) {
			if (false !== strpos($op, '?')) // contains placeholder?
				$expr = $db->quoteInto($expr, $value);
			else
				$expr .= ' ' . $db->quote($value);
		}
		
		if ($neg)
			$expr = "NOT($expr)";
		$where .= $expr;
	}
	return $where;
}

} // class It6_WsExtension_Server_Filter
