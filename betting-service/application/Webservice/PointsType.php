<?php

/**
 * PointsType related static methods
 * @author Pavel Klinger
 * @see Entities_Bet
 * 
 */
class Webservice_PointsType extends Webservice_AbstractWebService  {

	const DEFAULT_POINT_TYPE_ID = 1;
	
	public static $TABLE = "point_type";
	public static $TABLE_PREFIX = "ptp";	
	public static $ENTITY_NAME = "Entities_PointsType";
	public static $IDENTITY = "id";
	
	protected static $CONV = array(
		'id' => 'pointsTypeId',
		'name' => 'name',
		'rate' => 'rate'
	);

	/**
	 * Returns all point types in the system.
	 * @return array array of transaction structs
	 * @see Entities_PointsTransaction
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Insert new point type. Value of the type identifier is ignored and new
	 * is generated. 
	 * @param struct $transaction structure of the transaction
	 * @return integer transaction identifier of the created transaction
	 * @see Entities_PointsType	 
	 */
	public static function insert($type) {
		parent::insert($type);
	}

	/**
	 * Update type,
	 * @param struct $type structure of the type
	 * @return true on success
	 * @see Entities_PointsType
	 */
	public static function update($type) {
		parent::update($type);
	}

	/**
	 * Delete type.
	 * @param struct $transactionId idenetifier of the transaction.
	 * @return true on success
	 * @see Entities_PointsType
	 */
	public static function delete($typeId) {
		parent::delete($typeId);
	}
	
	/**
	 * Find type by given identifier.
	 * @param integer $id identifier of the type
	 * @return struct type structure	 
	 * @see Entities_PointsType	 
	 */
	public static function getById($id, $extensions = null) {
		return parent::getById($id, $extensions);
	}

	/**
	 * Find points type by given name.
	 * @param string $name unique name of the points type
	 * @return struct
	 * @see Entities_PointsType
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
	
	/**
	 * Exchange points to money
	 * @param integer $currencyId identifier of the currency
	 * @param integer $pointTypeId identifier of the point type
	 * @param float $amount amount of points
	 * @return float amount of the exchanged money in given currency
	 */
	public static function exchangeToMoney($currencyId, $pointTypeId, $amount) {
		$rate = static::getById($pointTypeId);
		if ( false == $rate )
			throw new It6_XmlRpc_Exception('Unknown point type id.\''.$pointTypeId.'\'');
		$rate = $rate['rate'];
		return Webservice_Currency::exchangeFromSystem($currencyId, $amount/$rate);
	}

	/**
	 * Returns rate
	 * @param integer $pointTypeId identifier of the point type
	 * @return float point rate to central currncy
	 */
	public static function getRate($pointTypeId) {
		$rate = static::getById($pointTypeId);
		$rate = $rate['rate'];
		return $rate;
	}
	
	
	/**
	 * Exchange money to points
	 * @param integer $currencyId identifier of the currency
	 * @param integer $pointTypeId identifier of the point type
	 * @param float $amount amount of money
	 * @return float amount of the points
	 */
	public static function exchangeFromMoney($currencyId, $pointTypeId, $amount) {
		$rate = static::getById($pointTypeId);
		if ( false == $rate )
			throw new It6_XmlRpc_Exception('Unknown point type id.\''.$pointTypeId.'\'');
		$rate = $rate['rate'];
		
		return Webservice_Currency::exchangeToSystem($currencyId, $amount) * $rate;
	}


}