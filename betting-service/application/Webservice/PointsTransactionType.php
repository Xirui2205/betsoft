<?php

/**
 * Points transaction type related static methods
 * @author Pavel Klinger
 * @see Entities_TransactionType
 * 
 */
class Webservice_PointsTransactionType extends Webservice_AbstractWebService  {

	const KIND_GET = "get";
	const KIND_SPEND = "spend";
	const KIND_EXCHANGE = "exchange";
	
	const CREATE_MONEY_TICKET = '4';

	public static $TABLE = "point_transaction_type";
	public static $ENTITY_NAME = "Entities_PointTransactionType";
	public static $IDENTITY = "id";
	protected static $CONV = array(
		'id' => 'pointsTransactionTypeId',
		'name' => 'name',
		'kind' => 'kind',
		'point_type_id' => 'pointTypeId',
		'note' => 'note',
		'from' => 'from',
		'thru' => 'thru',
		'to' => 'to',
		'debiting' => 'debiting',
		'low_limit' => 'lowLimit',
		'high_limit' => 'highLimit',
		'limited_by_day' => 'limitedByDay'
	);


	/**
	 * Returns all transaction types in the system.
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find transaction type by given identifier.
	 * @param integer $id identifier of the transaction type
	 * @return struct team structure
	 * @see Entities_Transaction
	 */
	public static function getById($id, $extensions = null) {
		static $cache = array();
		if (!array_key_exists($id, $cache)) {
			$cache[$id] = parent::getById($id, $extensions);
		}
		return $cache[$id];
	}

	/**
	 * Find transaction type by given name.
	 * @param string $name unique name of the transaction type
	 * @return struct
	 * @see Entities_Transaction
	 */
	public static function getByName($name) {
		try {
			$entity = static::defaultQuery(static::getDb()->select())
					->where('name = ?', $name)
					->query()->fetch();
			return static::toEntity($entity);
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('getByName', 0, $e);
		}
	}

	public static function getByNameAndPointsTypeId($name, $pointsTypeId) {
		if (1 != $pointsTypeId)
			throw new It6_XmlRpc_Exception('getByNameAndPointsTypeId: Unsupported point type id; id=' . $pointsTypeId);
		try {
			//TODO: !!! implement for more point types !!!
			return static::getByName($name);
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('getByNameAndPointsTypeId', 0, $e);
		}
		
	}
}