<?php

abstract class It6_Models_Abstract extends It6_Models_DbDependent {

//BEGIN: don't forget to define following variable(s) in derived classes
//protected static $_cache = array();
//END: don't forget to define following variable(s) in derived classes

//BEGIN: override in derived class if needed (that's most of them)
/**
 * Database name of table
 * @var string $_table
 */
protected static $_table = null;

/**
 * Array of that maps object names to database names ( objectName => dbName ).
 * Also it is list of properties oxposed by this class, also column list for SELECT.
 * @var array $_columns
 */
protected static $_columns = array();

/**
 * Object name of primary key column. (default 'id')
 * @var string $_primaryKey
 */
protected static $_primaryKey = 'id';

/**
 * Array of objectName => tableName and objectName => array('table' => tableName, 'column' => dbName).
 * (Second version required when not part of $_columns.)
 * Table names will be dynamically replaced by reserved prefixes.
 * @var array $_joinedColumns
 */
protected static $_joinedColumns = array();

/**
 * Array tableName => joinConstraints.
 * @see addJoin() for description of joinConstraints.
 * @var array $_joinConstraints
 */
protected static $_joinConstraints = array();

// following variables are updated dynamically (use same initialization values if you don't know what are you doing)
protected static $_joinPrefix = false;
protected static $_joinPrefixes = false;
protected static $_readDataModifiers = array(); // see modifySelect()
protected static $_readDataAllModifiers = array();  // see modifySelectAll()
//END: override in derived class if needed (that's most of them)

//BEGIN: don't override
protected static $_reservedJoinPrefixes = array();
protected static $_cachedClasses = array();
//END: don't override

protected static function reserveJoinPrefix($table = null) {
	$knownTable = !empty($table);
	if ($knownTable && array_key_exists($table, static::$_reservedJoinPrefixes))
		return static::$_reservedJoinPrefixes[$table];
	$prefix = '_t' . (count(static::$_reservedJoinPrefixes) + 1);
	if (!$knownTable)
		$table = $prefix;
	static::$_reservedJoinPrefixes[$table] = $prefix;
	return $prefix;
}

/**
 * To be overriden in derived class.
 * Call reserveJoinPrefix() for all joined tables and store
 */
protected static function reserveJoinPrefixes() {
	if (false === static::$_joinPrefix)
		static::$_joinPrefix = static::reserveJoinPrefix(static::$_table);
	if (false === static::$_joinPrefixes) {
		static::$_joinPrefixes = array();
		foreach (static::$_joinedColumns as $c => $t) {
			if (is_array($t))
				$t = $t['table'];
			static::$_joinPrefixes[$t] = '';
		}
		foreach (static::$_joinPrefixes as $t => &$p) {
			$p = static::reserveJoinPrefix($t);
		}
		foreach (static::$_joinedColumns as $c => &$t) {
			if (is_array($t))
				$t['table'] = static::$_joinPrefixes[$t['table']];
			else
				$t = static::$_joinPrefixes[$t];
		}
	}
}

/**
 * To be overriden in derived class.
 * @returns Zend_Db_Select
 */
public static function createSelect(&$db = null) {
	static::assureDbParam($db);
	static::reserveJoinPrefixes();
	$select = $db->select()
		->from(array(static::$_joinPrefix => static::$_table), array_diff_key(static::$_columns, static::$_joinedColumns));
	return static::addJoins($select);
//static::addJoins($select);
//file_put_contents('/tmp/php-debug.log', print_r($select->assemble(), true) . "\n", FILE_APPEND);
//return $select;
}

/**
 * Default behavior. Called from createSelect().
 * @param Zend_Db_Select $select Select to add joins to
 * @returns Zend_Db_Select
 */
public static function addJoins(Zend_Db_Select $select) {
	foreach (static::$_joinPrefixes as $t => $p) {
		if (!array_key_exists($t, static::$_joinConstraints))
			throw new Exception('Missing join constraint for table: ' . $t);
		static::addJoin($select, $t, static::$_joinConstraints[$t]);
	}
	return $select;
}

/**
 * Helper function for default way of adding a join.
 * @param Zend_Db_Select $select Select to add join to
 * @param string $tableName Database name of joined table
 * @param string $joinConstraint SQL 'ON' clause (must be translated to full database names) or array:
 *               array (
 *                  ['left',] // to perform left join instead of inner join
 *                  array('table_name_1' => 'objectName1', 'table_name2' => 'objectName2' [, 'optionName1' => 'option1', 'optionName2' => 'option2', ...]),
 *                  array('table_name_1' => 'objectName1', 'value' [, 'optionName1' => 'option1', 'optionName2' => 'option2', ...]),),
 *                  ...
 *               )
 *               known options:
 *                  '(operator)' ... SQL operator
 *               inner array will be transformed to 'prefix1.dbName1 operator prefix2.dbName2' or 'prefix1.dbName1 operator value'
 *                   values can be array for coma separated list (don't forget to use 'IN' operand)
 *               inner arrays will be ANDed together
 * @returns Zend_Db_Select $select Select with join added
 */
protected static function addJoin(Zend_Db_Select $select, $table, $joinConstraint) {
	static $options = array('(operator)');
	$prefix = static::tableToPrefix($table);
	$left = false;
	if (is_array($joinConstraint)) {
		$db = $select->getAdapter();
		$constraints = array();
		foreach ($joinConstraint as $constraint) {
			if (!is_array($constraint))
				continue;
			$operand = 0;
			$expr = '';
			$operator = (array_key_exists('(operator)', $constraint) ? $constraint['(operator)'] : '=');
			foreach ($constraint as $t => $c) {
				if (in_array($t, $options))
					continue;
				if (1 < ++$operand)
					$expr .= $operator;
				if (is_numeric($t)) {
					$v = $db->quote($c);
					if (is_array($c))
						$v = '(' . $v . ')';
					$expr .= $v;
				}
				else
					$expr .= $db->quoteIdentifier(static::objectNameToFullColumn($c, $t));
			}
			$constraints[] = $expr;
		}
		$_joinConstraint = '';
		foreach ($constraints as $constraint) {
			if (!empty($_joinConstraint))
				$_joinConstraint .= ' AND ';
			$_joinConstraint .= $constraint;
		}
		$left = in_array('left', $joinConstraint);
	}
	else
		$_joinConstraint = $joinConstraint;

	$columns = array();
	foreach (static::$_joinedColumns as $col => $colPrefix) {
		$_prefix = (is_array($colPrefix) ? $colPrefix['table'] : $colPrefix);
		if ($_prefix == $prefix &&  array_key_exists($col, static::$_columns)) // only columns for select and in required table
			$columns[$col] = $_prefix . '.' . static::$_columns[$col];
	}
	$fn = ($left ? 'joinLeft' : 'join');
	return $select->$fn(array($prefix => $table), $_joinConstraint, $columns);
}

/**
 * Modifies select created by createSelect() during call of readData(). Override.
 * @param Zend_Db_Select $select
 * @param array $modifiers Data influencing how select should be modified.
 * @returns Zend_Db_Select modified select
 */
protected static function modifySelect(Zend_Db_Select $select, array $modifiers) {
	return $select;
}

/**
 * Searches cached data
 * @param scalar|array $id One or more cache IDs
 * @param array $missing Missed cache IDs are appended to this array (only if $id was array)
 * @return null|array If $id is scalar then NULL (not found) or cached data,
 *                    if $id is array then array of all found cached data (empty array if none found).
 */
public static function findInCache($id, array &$missing = null) {
	if (!is_array($id)) {
		if (array_key_exists($id, static::$_cache))
			return static::$_cache[$id];
		else
			return null;
	}
	$found = array();
	if (!isset($missing))
		$missing = array();
	foreach ($id as $_id) {
		if (array_key_exists($_id, static::$_cache))
			$found[] = static::$_cache[$_id];
		else
			$missing[] = $_id;
	}
	return $found;
}

/**
 * Searches cached data not using primary key
 * @param scalar|array $value One or more values for given field
 * @param string $field Field in cached data
 * @param array $missing Missed cache IDs are appended to this array (only if $id was array)
 * @return null|array If $id is scalar then NULL (not found) or array of all found cached data,
 *                    if $id is array then array of all found cached data (empty array if none found).
 */
public static function findInCacheByField($value, $field, array &$missing = null) {
	$more = is_array($value);
	if (!$more)
		$value = array($value);
	$found = array();
	$foundValues = array();
	foreach (static::$_cache as $item) {
		if (in_array($item[$field], $value)) {
			$found[$item[static::$_primaryKey]] = $item;
			$foundValues[$item[$field]] = true;
		}
	}
	if (!$more)
		return (empty($found) ? null : $found);
	if (!isset($missing))
		$missing = array();
	foreach ($value as $_value) {
		if (!isset($foundValues[$_value]))
			$missing[] = $_value;
	}
	return $found;
}

/**
 * Reads and saves data in cache
 * @param Zend_Db_Select $select Prepared select that has all needed columns
 * @param bool $multiple Result are multiple rows
 * @param lambda $rowCallback [optional] Row postprocessing anonymous function,
 *               called with row array as only parameter before cacheing (you can modify it if callback accepts reference)
 * @return array|null Row or rows or NULL if one row should be returned and was not queried
 */
protected static function _readData($select, $multiple, $rowCallback = null) {
	static::_registerCachedClass();
	$res = $select->query();
	$data = array();
	while ($row = $res->fetch()) {
		if (!empty($rowCallback))
			$rowCallback($row);
		$_id = $row[static::$_primaryKey];
		static::$_cache[$_id] = $row;
		$data[$_id] = $row;
	}
	if ($multiple)
		return $data;
	else if (!empty($data))
		return array_shift($data);
	else
		return null;
}

/**
 * Default implementation.
 * Override in derived classes or override createSelect().
 * @param scalar|array $id Function should be capable to retrieve more values at once.
 * @returns array If $id is scalar then simply data, otherwise array(id_1 => data_1, id_2 => data_2, ...).
 */
public static function readData($id, &$db = null) {
	static::_registerCachedClass();
	$more = is_array($id);
	if (!$more)
		$id = array($id);
	foreach ($id as $_id)
		unset(static::$_cache[$_id]);
	$select = static::createSelect($db)->where(static::objectToDatabase(static::$_primaryKey) . ' IN (?)', $id);
	$select = static::modifySelect( $select , static::$_readDataModifiers );
	return static::_readData($select, $more);
}

/**
 * Modifies select created by createSelect() during call of readDataAll(). Override.
 * @param Zend_Db_Select $select
 * @param array $modifiers Data influencing how select should be modified. Default implementation supports:
 *              'orderBy' => array( columnObjName => ('DESC' | 'ASC') | columnObjName , ... ) // order matters
 *              'limit' => array( ( 'count' => count_of_rows, ['offset' => offset] ) | ( 'page' => zero_based_page_num, 'pageSize' => page_size ) )
 * @returns Zend_Db_Select modified select
 */
protected static function modifySelectAll(Zend_Db_Select $select, array $modifiers) {
	// orderBy
	if (array_key_exists('orderBy', $modifiers)) {
		$columns = array();
		foreach ($modifiers['orderBy'] as $key => $value) {
			if (is_numeric($key)) {
				$column = $value;
				$dir = 'ASC';
			}
			else {
				$column = $key;
				$dir = $value;
			}
			$columns[] = static::objectToDatabase($column) . ' ' . $dir;
		}
		$select->order($columns);
	}
	// limit
	if (array_key_exists('limit', $modifiers)) {
		$limit = $modifiers['limit'];
		if (array_key_exists('count', $limit)) {
			$offset = (array_key_exists('offset', $limit) ? $limit['offset'] : 0);
			$select->limit($limit['count'], $offset);
		}
		else if (array_key_exists('page', $limit) && array_key_exists('pageSize', $limit)) {
			$select->limitPage($limit['page'], $limit['pageSize']);
		}
	}
	return $select;
}

/**
 * Default implementation.
 * Override in derived classes or override createSelect().
 * @param scalar|array $id Function should be capable to retrieve more values at once.
 * @returns array If $id is scalar then simply data, otherwise array(id_1 => data_1, id_2 => data_2, ...).
 */
public static function readDataAll(&$db = null) {
	static::_registerCachedClass();
	static::$_cache = array();
	$select = static::modifySelectAll( static::createSelect($db), static::$_readDataAllModifiers );
	$res = $select->query();
	while ($row = $res->fetch()) {
		$_id = $row[static::$_primaryKey];
		static::$_cache[$_id] = $row;
	}
	return static::$_cache;
}

/**
 * @returns all the cached data (cache is not updated)
 */
public static function getDataAll() {
	return static::$_cache;
}

/**
 * Wrapper for readData that caches data (using $id as key).
 */
public static function getData($id, &$db = null) {
	$known = static::findInCache($id, $unknown);
	if (is_array($id)) {
		if (!empty($unknown)) {
			$learned = static::readData($unknown, $db);
			foreach ($learned as $key => $value)
				$known[$key] = $value;
		}
		return $known;
	}
	else {
		if (empty($known))
			return static::readData($id, $db);
		else
			return $known;
	}
}

/**
 * Retrieve data by given field value/s
 * @param scalar|array $value One or more values to be matched
 * @param string $field Field that should have given value
 * @param bool $grouped TRUE if data should be grouped like array( value => array (data) )
 * @param Zend_Db_Adapter $db
 * @return array|null NULL or data or array of data
 */
public static function getDataByField($value, $field, $grouped = false, &$db = null) {
	$more = is_array($value);
	$known = static::findInCacheByField($value, $field, $unknown);
	if (null === $known) {
		$unknown = $value;
		$known = array();
	}
	if (!empty($unknown)) {
		$learned = static::_readData(
			static::createSelect($db)->where(static::objectToDatabase($field) . ' IN (?)', $unknown),
			is_array($unknown)
		);
		if (!empty($learned)) {
			if (is_array($unknown))
				$known = array_merge($known, $learned);
			else
				$known[] = $learned;
		}
	}
	if ($grouped) {
		$groups = array();
		foreach ($known as $item) {
			if (!array_key_exists($item[$field], $groups))
				$groups[$item[$field]] = array($item);
			else
				$groups[$item[$field]][] = $item;
		}
		return $groups;
	}
	else
		return ($more ? $known : $known[0]);
}

/**
 * Retrieve data by given field value/s
 * @param scalar|array $value One or more values to be matched
 * @param string $field Field that should have given value
 * @param Zend_Db_Adapter $db
 * @return array|null For scalar value : NULL or data (one row), for array of values (value => row, ...)
 */
public static function getDataByUniqueField($value, $field, &$db = null) {
	$result = static::getDataByField($value, $field, true, $db);
	foreach ($result as &$data) {
		if (1 != count($data))
			throw new Exception('Duplicit field value found. table="'
				. static::$_table . '" field="' . $field . '" value="' . $data[$field] . '"');
		$data = array_shift($data);
	}
	if (is_array($value))
		return $result;
	else if (!empty($result))
		return $result[$value];
	else
		return null;
}

/**
 * Similar to getData() but returns only one value or array(id_1 => one_value_1, ...).
 * @param scalar|array $id
 * @param scalar $value
 */
public static function get($id, $name, &$db = null) {
	$data = static::getData($id, $db);
	if (empty($data))
		return null;
	if (is_array($id)) {
		$result = array();
		foreach ($id as $_id) {
			if (array_key_exists($_id, $data))
				$result[$_id] = (array_key_exists($name, $data[$_id]) ? $data[$_id][$name] : null);
			else
				$result[$_id] = null;
		}
		foreach ($data as $_id => $values) {
			if (array_key_exists($name, $values))
				$result[$_id] = (array_key_exists($name, $values) ? $values[$name] : null);
		}
		return $result;
	}
	else {
		if (array_key_exists($name, $data))
			return $data[$name];
		else
			return null;
	}
}

// protected static function findData($where) {
// 	//search cache
// }
// 
// public static function getDataBy($where, &$db = null) {
// 	static::assureDbParam($db);
// 	$known = static::findData($where);
// }
// 
// public static function getBy($name, $where, &$db = null) {
// 	$data = static::getDataBy($where, $db);
// 	if (empty($data))
// 		return null;
// 	$result = array();
// 	$more = is_array($name);
// 	foreach ($data as $id => $values) {
// 		if ($more) {
// 			$_values = array();
// 			foreach ($name as $_name) {
// 				if (array_key_exists($_name, $values))
// 					$_values[$_name] = $values[$_name];
// 			}
// 			$result[ $values[static::$_primaryKey] ] = $_values;
// 		}
// 		else if (array_key_exists($name, $values))
// 			$result[ $values[static::$_primaryKey] ] = $values[$name];
// 	}
// 	return $result;
// }

private static function tableToPrefix($table) {
	if (!isset($table) || $table == static::$_table)
		return static::$_joinPrefix;
	foreach (static::$_joinPrefixes as $t => $p) {
		if ($t == $table)
			return $p;
	}
	return $table;
}

/**
 * @param string $objName Object name of column
 */
protected static function objectNameToColumn($objName) {
	if (array_key_exists($objName, static::$_columns))
		return static::$_columns[$objName];
	else
		return $objName;
}

/**
 * @param string $objName Object name of column
 * @param string $table [optional] Explicit table name for column
 */
protected static function objectNameToFullColumn($objName, $table = null) {
	$prefix = (empty($table) ? '' : static::tableToPrefix($table));
	$selected = array_key_exists($objName, static::$_columns);
	$join = array_key_exists($objName, static::$_joinedColumns);
	if ($selected)
		$dbName = static::$_columns[$objName];
	else if ($join) { // must be an array
		$j = static::$_joinedColumns[$objName];
		if (is_array($j)) {
			if (empty($prefix))
				$prefix = $j['table'];
			$dbName = $j['column'];
		}
	}
	else
		$dbName = $objName;
	if (empty($prefix))
		$prefix = static::$_joinPrefix;
	if (!empty($prefix))
		return $prefix . '.' . $dbName;
	else
		return $dbName;
}

public static function objectToDatabase($objData, $fullNames = true) {
	if (is_array($objData)) {
		$dbData = array();
		foreach (static::$_columns as $objName => $dbName) {
			if (array_key_exists($objName, $objData)) {
				$fullDbName = ($fullNames ?
					static::objectNameToFullColumn($objName)
					: static::objectNameToColumn($objName));
				$dbData[$fullDbName] = $objData[$objName];
			}
		}
		return $dbData;
	}
	else if ($fullNames)
		return static::objectNameToFullColumn($objData);
	else
		return static::objectNameToColumn($objData);
}

/**
 * Clears chached values.
 * @param scalar|array $id [optional] if not set, all values will be cleared, otherwise will be cleared specified ID(s) only.
 */
public static function clearCache($id = null) {
	if (!isset($id))
		static::$_cache = array();
	else {
		if (!is_array($id))
			$id = array($id);
		foreach ($id as $_id)
			unset(static::$_cache[$_id]);
	}
}

public static function clearCacheForAll() {
	foreach (static::$_cachedClasses as $class)
		call_user_func(array($class, 'clearCache'));
	static::$_cachedClasses = array();
}

protected static function _registerCachedClass() {
	$c = get_called_class();
	if (!in_array($c, static::$_cachedClasses))
		static::$_cachedClasses[] = $c;
} 

public static function create($data, &$db = null) {
	static::assureDbParam($db);
	$dbData = static::objectToDatabase($data, false);
	$db->insert(static::$_table, $dbData);
	return $db->lastInsertId();
}

/**
 * @param scalar|array $id Primary key for automatic WHERE (ignored when $dontAddPrimaryKeyToWhere is TRUE)
 * @param array $data objectName => value
 * @param Zend_Db_adapter $db [optional]
 * @param scalar|array $where [optional] Additional row constraints, use Zend convention for update's WHERE param ( use DB column names -- use objectToDatabase() )
 * @param bool $dontAddPrimaryKeyToWhere [optional] TRUE if primary key constraint should not be added. Default FALSE.
 * @return int number of rows affected
 */
public static function update($id, $data, &$db = null, $where = null, $dontAddPrimaryKeyToWhere = false) {
	static::assureDbParam($db);
	$dbData = static::objectToDatabase($data, false);
	$_where = (empty($where) ? array() : $where);
	if (!$dontAddPrimaryKeyToWhere) {
		if (is_array($id))
			$_where[static::objectToDatabase(static::$_primaryKey, false) . ' IN (?)'] = $id;
		else
			$_where[static::objectToDatabase(static::$_primaryKey, false) . '=?'] = $id;
	}
	static::clearCache(empty($where) && !$dontAddPrimaryKeyToWhere ? $id : null);
	return $db->update(
		static::$_table,
		$dbData,
		$_where
	);
}

/**
 * @param scalar|array $id Primary key for automatic WHERE (ignored when $dontAddPrimaryKeyToWhere is TRUE)
 * @param Zend_Db_adapter $db [optional]
 * @param scalar|array $where [optional] Additional row constraints, use Zend convention for update's WHERE param ( use DB column names -- use objectToDatabase() )
 * @param bool $dontAddPrimaryKeyToWhere [optional] TRUE if primary key constraint should not be added. Default FALSE.
 * @return int number of rows affected
 */
public static function delete($id, &$db = null, $where = null, $dontAddPrimaryKeyToWhere = false) {
	static::assureDbParam($db);
	$_where = (empty($where) ? array() : $where);
	if (!$dontAddPrimaryKeyToWhere) {
		if (is_array($id))
			$_where[static::objectToDatabase(static::$_primaryKey, false) . ' IN (?)'] = $id;
		else
			$_where[static::objectToDatabase(static::$_primaryKey, false) . '=?'] = $id;
	}
	static::clearCache(empty($where) && !$dontAddPrimaryKeyToWhere ? $id : null);
	return $db->delete(
		static::$_table,
		$_where
	);
}

} //class It6_Models_Abstract
