<?php

abstract class Webservice_AbstractWebService {

	protected static function mapDbArray2Entity($dbArray, $entity, $CONV) {
		if ( empty($dbArray) ) return null;
		foreach ( $CONV as $k => $v ) {
			if ( array_key_exists( $k, $dbArray ) ) {
				$entity->$v = $dbArray[$k];
			}
		}

		return $entity;
	}

	protected static function mapEntity2DbArray($entity, $dbArray, $CONV) {
		//FIXME: this is another klinger mess, you are fucked up with propagating NULL values (forget of SQL queries like "... SET x=NULL ... ") 
		//       (typical example of misuse of isset() function)
		//       this is probably never going to be fixed, because of all the things that could brake up :-(
		foreach ( $CONV as $k => $v ) {
			if ( isset( $entity->$v ) ) {
				$keyArr = explode('.', $k);
				if( 1 == count($keyArr) ) {
					$dbArray[$keyArr[0]] = $entity->$v;
				}
				elseif( 2 == count($keyArr) ){
					$dbArray[$keyArr[1]] = $entity->$v;
				}
			}
		}
		return $dbArray;
	}


	public static function toEntity($dbArray, $columns = null) {
		if (false === $dbArray)
			return false;
		else
			return new It6_ArrayWrapper($dbArray);
	}

	public static function fromEntity($entity) {
		$dbArray = array();
		return static::mapEntity2DbArray($entity, $dbArray, static::$CONV);
	}

	public static function fetchAllEntities($query, $columns = null) {
		$ret = array();
		while ( $row = $query->fetch() )
			$ret[] = static::toEntity($row, $columns);
		return $ret;
	}

	/**
	 * @deprecated use fetchAllEntities
	 * @param array $dbArray
	 * @return array()
	 */
	public static function toEntities($dbArray, $columns=null, $options=false) {
		$ret = array();

		$args = array(null, $columns);

		if(!empty($options))
			$args[] = $options;

		foreach ( $dbArray as $row ) {
			$args[0] = $row;
			$ret[] = call_user_func_array('static::toEntity', $args);
		}
		return $ret;
	}

	public static function fromEntities($entities) {
		$ret = array();

		foreach ( $entities as $entity ) {
			$ret[] = static::fromEntity($entity);
		}
		return $ret;
	}

	protected static function getDb() {
		return static::getMainDb();
	}

	public static function getAdminDb() {
		if ( Zend_Registry::isRegistered('zdb_admin') )
			return Zend_Registry::get('zdb_admin');

		return Zend_Registry::get('admindb');
	}

	public static function getSessionDb() {
		return Zend_Registry::get('dbSes');
	}

	public static function getMainDb() {
		if ( Zend_Registry::isRegistered('zdb_game') )
			return Zend_Registry::get('zdb_game');

		return Zend_Registry::get('db');
	}

	protected static function defaultFrom($query) {
		return $query->from(static::getTable(), null);
	}

	protected static function defaultColumns($query, $CONV = null) {
		if ( empty($CONV) )
			$CONV = static::$CONV;
		return $query->columns( array_flip($CONV) );
	}

	protected static function defaultJoins($query) {
		return $query;
	}

	protected static function defaultWheres($query) {
		return $query;
	}

	protected static function defaultOrders($query) {
		return $query;
	}

	protected static function defaultQuery($query) {
		return static::defaultOrders(
			static::defaultWheres(
				static::defaultJoins(
					static::defaultColumns(
						static::defaultFrom($query)))));
	}

	/**
	 * Prepares select using given injected functions or default ones or none.
	 * @param mixed $query
	 * @param array $injections callbacks for call_user_func/forward_static_call,
	 *                          callback takes one parameter passed $query
	 *                          and returns (un)modified query,
	 *                          static call is made if callback is array and first of two argument is string,
	 *                          don't set key to use default handler, set to empty value to skip
	 * @return mixed $query modified or not
	 */
	protected static function defaultQueryInjected($query, array $injections) {
		$thisClass = get_called_class();
		$knownHooks = array('from', 'columns', 'joins', 'wheres', 'orders');
		$ignoredPrefixes = array('fetch');
		$defaultHooks = array();
		foreach ($knownHooks as $hook) {
			if (!array_key_exists($hook, $injections))
				$defaultHooks[] = $hook;
		}
		foreach ($defaultHooks as $hook)
			$query = forward_static_call(array($thisClass, 'default' . ucfirst($hook)), $query);
		foreach ($injections as $hook => &$injection) {
			$ignore = false;
			foreach ($ignoredPrefixes as $prefix) {
				if ($prefix == substr($hook, 0, strlen($prefix))) {
					$ignore = true;
					break;
				}
			}
			if ($ignore)
				continue;
			if (!empty($injection)) {
				if (is_array($injection) && 1 < count($injection) && is_string($injection[0]))
					$query = forward_static_call($injection, $query);
				else
					$query = call_user_func($injection, $query);
			}
		}
		return $query;
	}

	public static function getAll($extensions = null) {
		return static::getAllWhere(array(), $extensions);
	}

	/**
	 * NOTE: this is experimental function and maybe some day getAllWhere will call this function with all default injections
	 * NOTE: maybe $where is reduntant to injection possibilities
	 * @see defaultQueryInjected for injections' description
	 */
	public static function getAllWhereInjected($where, array $injections, $extensions = null) {
		try {
			$input = array('where' => $where);

			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);

			$select = static::defaultQueryInjected(static::getDb()->select(), $injections);

			static::preprocessExtensions($extensions, $input, $metadata, $select);

			//$columns = array_key_exists(It6_WsExtension_Server_Columns::INPUT_COLUMNS, $input)
			//		? $input[It6_WsExtension_Server_Columns::IMPUT_COLUMNS] : null;

			foreach ($where as $k => $v) {
				if ( is_numeric($k) )
					$select = $select->where(static::convQuery($v));
				else
					$select = $select->where(static::convQuery($k), $v);
			}

			$columns = array();
			if (!empty($extensions)) {
				foreach ($extensions as $ext) {
					if ($ext instanceof It6_WsExtension_Server_Columns) {
						$extCols = $ext->getParam(It6_WsExtension_Columns::PARAM_COLUMNS);
						if (!empty($extCols))
							$columns = array_merge($columns, $extCols);
					}
				}
			}
			if (empty($columns))
				$columns = null;
			if (!empty($injections['fetchRows'])) {
				$hook = $injections['fetchRows'];
				$output = $hook($select->query());
				if (false === $output)
					return;
			}
			else
				$output = static::fetchAllEntities($select->query(), $columns);

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception(get_called_class().'::getAllWhereInjected(\''.Zend_Json::encode($where).'\',\''.Zend_Json::encode($extensions).'\')', 0, $e);
		}
	}

	public static function getAllWhere($where, $extensions = null, $joins = null) {
		try {
			$input = array('where' => $where);

			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);
			
			$select = static::defaultQuery(static::getDb()->select());

			if (!is_null($joins)) foreach($joins as $join) {
				$select = self::defaultJoins($select)->join($join[0], $join[1], $join[2]);			
			}
			static::preprocessExtensions($extensions, $input, $metadata, $select);

			//$columns = array_key_exists(It6_WsExtension_Server_Columns::INPUT_COLUMNS, $input)
			//		? $input[It6_WsExtension_Server_Columns::IMPUT_COLUMNS] : null;

			foreach ($where as $k => $v) {
				if ( is_numeric($k) ) {
					$select = $select->where(static::convQuery($v));
				} else {
					$select = $select->where(static::convQuery($k), $v);
				}
			}
			$columns = array();
			if (!empty($extensions)) {
				foreach ($extensions as $ext) {
					if ($ext instanceof It6_WsExtension_Server_Columns) {
						$extCols = $ext->getParam(It6_WsExtension_Columns::PARAM_COLUMNS);
						if (!empty($extCols))
							$columns = array_merge($columns, $extCols);
					}
				}
			}
			if (empty($columns))
				$columns = null;


			$output = static::fetchAllEntities($select->query(), $columns); //$select->getPart(Zend_Db_Select::COLUMNS));

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		
		catch (Exception $e) {
            throw new It6_XmlRpc_Exception(get_called_class().'::getAllWhere(\''.Zend_Json::encode($where).'\',\''.Zend_Json::encode($extensions).'\')', 0, $e);
		}
	}

	public static function getOneWhere($where, $extensions = null) {
		if ( null == $extensions ) $extensions = array();
		$extensions[] = array(
			'class' => 'Pagination',
			'id' => '_limit',
			'params' => array(It6_WsExtension_Pagination::PARAM_LIMIT => 1),
			It6_WsExtension::PARAM_POSTPROCESSING => false);
		$ret = static::getAllWhere($where, $extensions);

		if ( !It6_ArrayWrapper::keyExists(0, $ret) ) return false;

		$extension = (isset($ret[It6_WsExtension::KEY_EXTENSIONS]) ? $ret[It6_WsExtension::KEY_EXTENSIONS] : null);
		$ret = $ret[0];
		if (isset($extension)) {
			$ret[It6_WsExtension::KEY_EXTENSIONS] = $extension;
		}

		return $ret;
	}

	public static function getAllWhereColumns($where, $columns, $extensions = null) {
		if ( null == $extensions ) $extensions = array();
		$extensions[] = array(
			'class' => 'Columns',
			'id' => '_columns',
			'params' => array(It6_WsExtension_Columns::PARAM_COLUMNS => $columns));

		return static::getAllWhere($where, $extensions);
	}

	public static function getOneWhereColumns($where, $columns, $extensions = null) {
		if ( null == $extensions ) $extensions = array();
		$extensions[] = array(
			'class' => 'Columns',
			'id' => '_columns',
			'params' => array(It6_WsExtension_Columns::PARAM_COLUMNS => $columns));

		return static::getOneWhere($where, $extensions);
	}


	public static function getAllOrder($order, $extensions = null) {
		if ( null == $extensions ) $extensions = array();

		$extensions[] = array(
			'class' => 'Order',
			'id' => '_order',
			'params' => array(It6_WsExtension_Order::PARAM_ORDER => $order));

		return static::getAll($extensions);
	}

	public static function getAllColumns($columns, $extensions = null) {
		if ( null == $extensions ) $extensions = array();

		$extensions[] = array(
			'class' => 'Columns',
			'id' => '_columns',
			'params' => array(It6_WsExtension_Columns::PARAM_COLUMNS => $columns));

		return static::getAll($extensions);
	}

	public static function getAllWhereOrder($where, $order, $extensions = null) {
		if ( null == $extensions ) $extensions = array();

		$extensions[] = array(
			'class' => 'Order',
			'id' => '_order',
			'params' => array(It6_WsExtension_Order::PARAM_ORDER => $order));

		return static::getAllWhere($where, $extensions);
	}

	/**
	 * @deprecated
	 */
	public static function getAllWhereOrderColumns($where, $order, $columns, $extensions = null) {
		if ( null == $extensions ) $extensions = array();

		$extensions[] = array(
			'class' => 'Order',
			'id' => '_order',
			'params' => array(It6_WsExtension_Order::PARAM_ORDER => $order));

		$extensions[] = array(
			'class' => 'Columns',
			'id' => '_columns',
			'params' => array(It6_WsExtension_Columns::PARAM_COLUMNS => $columns));

		return static::getAllWhere($where, $extensions);
	}

	public static function getAllWhereCount($where) {
		try {
			$select = static::getDb()->select();
			$select = static::defaultWheres($select);
			$select = static::defaultJoins($select, null);
			$select = static::defaultFrom($select, null);
			$select = $select->columns(array('count' => 'Count(*)'));
			foreach ($where as $k => $v) {
				if ( is_numeric($k) )
					$select = $select->where(static::convQuery($v));
				else
					$select = $select->where(static::convQuery($k), $v);
			}
			$count = $select->query()->fetch();
			return $count['count'];
		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception('getAllWhereCount', 0, $e);
		}
	}

	/**
	 * Translate object field from conversion table to DB column.
	 * @param string $field Object field names
	 * @param boolean $strict This parameter determines what should be returned when $field is not found in convversion table.
	 *                        In that case if parameter is TRUE, FALSE is returned, else original field name is returned (default).
	 * @return string|boolean FALSE if field is unknown and strict mode, DB column name or field name otherwise
	 */
	public static function getDbColumn($field, $strict = false) {
		$dbColumn = array_search($field, static::getConvTable());
		if (false === $dbColumn)
			return ($strict ? false : $field);
		else
			return $dbColumn;
	}

	public static function convQuery($queryStr, $CONV = null) {
		if ( empty($CONV) )
			$CONV = static::$CONV;
		$patterns = array();
		$replace = array();
		foreach ( $CONV as $k => $v ) {
			$patterns[] = '/(^|\W)' . $v . '($|\W)/';
			$replace[] = '\1' . $k . '\2';
		}
		return preg_replace($patterns,$replace, $queryStr);
	}

	public static function getOneBy($value, $column, $extensions = null) {
		return static::getOneWhere(array($column . '= ?' => $value), $extensions);
	}

	public static function getById($id, $extensions = null) {
		return static::getOneBy($id, static::getIdentity(), $extensions);
	}

	public static function insert($entity) {
		$db = static::getDb();

		It6_DbTransaction::begin($db);

		try {
			$entity = new It6_ArrayWrapper($entity);
			$data = static::removeTableNames(static::fromEntity($entity));
			unset($data[static::$IDENTITY]);
			$db->insert(static::$TABLE, $data);
			$ret = $db->lastInsertId();
			It6_DbTransaction::commit($db);
			return $ret;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not insert entity (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	public static function getTable() {
		if ( !empty(static::$TABLE_PREFIX) )
			return array(static::$TABLE_PREFIX => static::$TABLE);
		else
			return static::$TABLE;
	}

	public static function getIdentity() {
		if ( !empty(static::$TABLE_PREFIX) )
			return static::$TABLE_PREFIX . '.' . static::$IDENTITY;
		else
			return static::$IDENTITY;
	}

	public static function update($entity) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$entity = new It6_ArrayWrapper($entity);
			$data = static::removeTableNames(static::fromEntity($entity));

			$id = $data[static::$IDENTITY];
			unset($data[static::$IDENTITY]);

			$db->update(
				static::$TABLE,
				$data,
				array( static::$IDENTITY . '= ?' => $id)
			);

			It6_DbTransaction::commit($db);
			return $id;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update entity. (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	protected static function removeTableNames($data) {
		$ret = array();
		foreach ( $data as $k => $v ) {
			if (1 == preg_match('/\\.?([^.]+)$/', $k, $matches))
				$ret[ $matches[1] ] = $v;
		}
		return $ret;
	}

	public static function delete($id) {
		$db = static::getDb();

		It6_DbTransaction::begin($db);

		try {
			$db->delete(static::$TABLE, array(static::$IDENTITY . ' = ?' => $id));
			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not delete entity (Entity: '".get_called_class()."')", 0, $e);
		}
	}

	protected static function createExtensions($extensions) {
		if ( empty($extensions) || !It6_ArrayWrapper::isArray($extensions) ) return null;

		$ret = array();
		foreach ( $extensions as $extension ) {
			$class = $extension[It6_WsExtension::PARAM_CLASS];
			$id = $extension[It6_WsExtension::PARAM_ID];
			$params = $extension[It6_WsExtension::PARAM_PARAMS];
			$preprocessing = (
				isset($extension[It6_WsExtension::PARAM_PREPROCESSING])
				? $extension[It6_WsExtension::PARAM_PREPROCESSING]
				: null
			);
			$postprocessing = (
				isset($extension[It6_WsExtension::PARAM_POSTPROCESSING])
				? $extension[It6_WsExtension::PARAM_POSTPROCESSING]
				: null
			);

			$ret[] = It6_WsExtension_Server_Factory::newExtension(
				$class, $id, $params, $preprocessing, $postprocessing
			);
		}
		return $ret;
	}

	protected static function preprocessExtensions($extensions, &$inputs, &$metadata, &$handler) {
		if ( empty($extensions) ) return;
		foreach ( $extensions as $extension ) {
			if ($extension->hasPreprocessing()) {
				$extension->preprocess($inputs, $metadata, $handler);
			}
		}
	}

	protected static function postprocessExtensions($extensions, &$outputs, &$metadata, &$handler) {
		if ( empty($extensions) ) return;
		foreach ( $extensions as $extension ) {
			if ($extension->hasPostprocessing()) {
				$extension->postprocess($outputs, $metadata, $handler);
			}
		}
	}

	protected static function insertManyToManyWithParameter($db, $mntable, $id1DbName, $id1, $id2DbName, $id2Name, $paramDbName, $paramName, $insertValues) {

		if ( empty($insertValues) ) return;

		It6_DbTransaction::begin($db);

		try {

			foreach ( $insertValues as $iv ) {

				$db->insert(
					$mntable,
					array(
						$id1DbName => $id1,
						$id2DbName => $iv->$id2Name,
						$paramDbName => $iv->$paramName
				));

			}

			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not insert many to many with parameter.", 0, $e);
		}
	}

	protected static function updateManyToManyWithParameter($db, $mntable, $id1DbName, $id1, $id2DbName, $id2Name, $paramDbName, $paramName, $updateValues) {

		It6_DbTransaction::begin($db);

		try {
			$oldparams = $db->select()
					->from($mntable)
					->where($id1DbName . ' = ?', $id1)
					->query()->fetchAll();

			$OP = array();
			foreach ( $oldparams as $oldparam ) {
				$OP[$oldparam[$id2DbName]] = $oldparam[$paramDbName];
			}

			$NP = array();
			foreach ( $updateValues as $uv ) {
				$NP[$uv->$id2Name] = $uv->$paramName;
			}

			foreach ( array_diff_key($OP, $NP) as $id => $value ) {
				$db->delete(
						$mntable,
						array(
							$id2DbName . '= ?' => $id,
							$id1DbName . '= ?' => $id1 ) );
			}

			foreach ( array_diff_key($NP, $OP) as $id => $value ) {
				$db->insert(
						$mntable,
						array(
							$id2DbName => $id,
							$id1DbName => $id1,
							$paramDbName => $value ) );
			}

			foreach ( array_intersect(array_keys($NP), array_keys($OP)) as $id ) {
				if ( $NP[$id] != $OP[$id] ) {
					$db->update(
						$mntable,
						array($paramDbName => $NP[$id]),
						array(
							$id2DbName . '= ?' => $id,
							$id1DbName . '= ?' => $id1));
				}
			}

			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not update many to many with parameter.", 0, $e);
		}

	}

	public static function getConvTable() {
		return static::$CONV;
	}

	/**
     * Retrieves ID=>text pairs useful eg. for selectboxes.
     * When translation is used, language collation is picked up automatically.
	 * @param string $table Database table name
	 * @param string $idColumn Column with ID (or generally with keys in returned array)
	 * @param string $textColumn Column with texts (should be just column name, not expression)
	 * @param integer|NULL $langId Language id for translation, empty value for no translation
	 * @param array|NULL $where List of string expressions (passed directly to SQL) or tripplets (column, expression, value).
	 *                          Triplet's column must be from given table. Triplet's expression must contain '$' character
	 *                          as placeholder for column and can contain '?' character as placeholder for tripplet's value.
	 *                          WHERE expressions created from triplets will be ANDed.
	 *                          To prevent column names ambiguity (when joining table with translations) you should
	 *                          use 's' table prefix in column names when passing string expressions, column in tripplet
	 *                          is prefixed automatically.
	 *                          Example tripplets: ('alias_id', '$&lt;?', 100), ('typ_alias_group', '$ IS NULL', null).
	 * @return array (id => text) Ordered by text
	 */
	public static function getTableOptions($table, $idColumn, $textColumn, $langId = null, $where = null) {
		$db = static::getDb();
		$select = $db->select()
			->from(
				array('s' => $table),
				array()
			);
		$textColumn = $db->quoteIdentifier("s.$textColumn");
		$columns = array('id' => "s.$idColumn");
		if (empty($langId)) {
			$columns['__name'] = new Zend_Db_Expr("TRIM($textColumn)");
			$langId = DEFAULT_LANG_ID;
		}
		else {
			$columns['__name'] = new Zend_Db_Expr("TRIM(COALESCE(t.text, $textColumn))");
			$select->joinLeft(
				array('t' => 'preklady'),
				"$textColumn=t.index_pole AND t.lang_id=" . intval($langId),
				array()
			);
		}
		if (!empty($where)) {
			foreach ($where as $def) {
				if (is_array($def)) {
					$expr = str_replace('$', $db->quoteIdentifier("s.{$def[0]}"), $def[1]);
					if (count($def) > 2) {
						$expr = $db->quoteInto($expr, $def[2]);
					}
				}
				else {
					$expr = $def;
				}
				$select->where($expr);
			}
		}
		$collation = It6_Models_Language::get($langId, 'collation');
		$collation = (empty($collation) ? '' : " COLLATE $collation");
		$select->columns($columns)->order("(__name)$collation");
		$s = $select->assemble();
		$rows = $select->query()->fetchAll();
		$options = array();
		foreach ($rows as $row) {
			$options[$row['id']] = $row['__name'];
		}
		return $options;
	}

}
