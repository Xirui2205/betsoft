<?php

/**
 * Type related static methods.
 * @see Entities_Type
 *
 */
class Webservice_Type extends Webservice_AbstractWebService  {
	
	public static $TABLE = "typ";
	public static $TABLE_PREFIX = "tp";
	public static $IDENTITY = "typ_id";
	public static $TYPE_EVENT_TABLE = 'typ_udalost';
	public static $TYPE_EVENT_PREFIX = 'typ_udalost';
	public static $ORDER_TYPE_TABLE = 'typ_order_type';
	public static $ORDER_TYPE_PREFIX = 'tot';

	protected static $ENTITY_NAME = "Entities_Typ";
	protected static $CONV = array(
		'typ_id' => 'typeId',
		'typ_alias_id' => 'typeAliasId',
		'nazev' => 'name',
		'poradi' => 'order',
		'zobrazeno' => 'visible',
		'offer_category_id' => 'offerCategoryId',
		'typ_alias_group' => 'typeAliasGroup',
		'group_master' => 'groupMaster',
		'order_type_id' => 'orderTypeId',
		'betradar_time_offset' => 'betradarTimeOffset',
		
		'oc.name' => 'offerCategoryName',
		'tot.name' => 'orderTypeName',
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->joinLeft(
				array(Webservice_OfferCategory::$TABLE_PREFIX => Webservice_OfferCategory::$TABLE),
				static::$TABLE_PREFIX.'.offer_category_id = '.Webservice_OfferCategory::$TABLE_PREFIX.'.id',
				array()
			)
			->joinLeft(
				array(self::$ORDER_TYPE_PREFIX => self::$ORDER_TYPE_TABLE),
				static::$TABLE_PREFIX.'.order_type_id = '.self::$ORDER_TYPE_PREFIX.'.id',
				array()
			);
	}
	
	/**
	 * Returns all types in the system.
	 * @return array array of the type structures
	 * @see Entities_Type
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Find type by given identifier.
	 * @param integer $typeId identifier of the type
	 * @return struct type structure	 
	 * @see Entities_Type
	 */
	public static function getById($typeId, $extensions = null) {
		return parent::getById($typeId, $extensions);
	}
	
	/**
	 * Insert new type.
	 * @param struct $type structure of the type
	 * @return integer type identifier of the created type
	 * @see Entities_Type 
	 */
	public static function insert($values) {
		$values = self::prepareDataForDb($values);
		$res = parent::insert($values);
		return $res;
	}
	
	/**
	 * Update type.  
	 * @param struct $type structure of the type 
	 * @return true on success
	 * @see Entities_Type
	 */
	public static function update($values) {
		$values = self::prepareDataForDb($values);
		$entity = new It6_ArrayWrapper($values);
		$data = static::removeTableNames(static::fromEntity($entity));
		if (empty($data['order_type_id'])) {
			$orderType = array(array_search('orderTypeId', self::$CONV) => null);
			$data = array_merge($data, $orderType);
		}
		$where = array('typ_id = ?' => $values['typeId']);
		return self::getDb()->update(array(self::$TABLE_PREFIX => self::$TABLE), $data, $where);
	}
	
	/**
	 * Delete type.  
	 * @param struct $typeId idenetifier of the type
	 * @return true on success	 
	 * @see Entities_Type
	 */
	public static function delete($typeId) {
		$db = self::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->delete('typ_podtyp', array('typ_id = ?' => $typeId));
			$db->delete('typ_udalost', array('typ_id = ?' => $typeId));
			$res = parent::delete($typeId);
			It6_DbTransaction::commit($db);
			return $res;
		}
		catch(Exception $e) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception($e);
			return false;
		}
	}
	
	/**
	 * Returns list of order types.
	 * @return array of order type structures
	 */
	public static function getOrderTypes() {
		$orderTypes = self::getDb()->select()
			->from(static::$ORDER_TYPE_TABLE)
			->order('id')
			->query()->fetchAll();
		
		return $orderTypes;
	}
	
	/**
	 * Prepares data about type to be entered into db
	 * @param struct $values
	 * @return struct $values 
	 */
	private static function prepareDataForDb($values) {
		unset($values['save']);
		if ($values['typeAliasGroup'] == '') $values['typeAliasGroup'] = null;
		if ($values['orderTypeId'] == '') $values['orderTypeId'] = null;
		return $values;
	}
}
