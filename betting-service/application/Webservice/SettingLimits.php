<?php
class Webservice_SettingLimits extends Webservice_AbstractWebService {

	public static $TABLE = "setting_limits";
	public static $TABLE_PREFIX = "l";
	public static $IDENTITY = 'limit_id';

	protected static $ENTITY_NAME = "Entities_SettingLimits";

	protected static $CONV = array(
		'limit_id' => 'limitId',
		'user_id' => 'userId',
		'time_from' => 'timeFrom',
		'time_to' => 'timeTo',
		'limit_amount' => 'limitAmount',
		'actual_amount' => 'actualAmount',
	);

	/**
	 * Metoda vrací na základě zadaného ID uživatele jeho individuální aktuální limit 
	 * @param int $userId ID uživatele v systému BBAS
	 */
	public static function getUserLimits($userId) {
		$where = array(
			'userId = ?' => $userId,
			'timeFrom < now()',
			'timeTo > now()'
		);
		return parent::getOneWhere($where);
	}

	/**
	 * Metoda vloží individuální limit uživatele
	 * @param array $data data k vložení do db
	 */
	public static function insertUserLimits($entity) {
		parent::insert($entity);
	}

	/**
	 * Metoda aktualizuje aktualni cerpanou castku u aktivniho limitu uzivatele
	 * @param int $limitId id aktualniho limitu, int $value hodnota pricitane castky
	 */
	public static function updateActualAmount($limitId, $value) {
		try {
			$data = array('actual_amount' => new Zend_Db_Expr('actual_amount + '.self::getDb()->quote($value, Zend_Db::FLOAT_TYPE)));
			$where = array(static::$IDENTITY.'=?' => $limitId);
			$ret = self::getDb()->update(
				array(self::$TABLE_PREFIX => self::$TABLE), 
				$data, 
				$where
			);
			return $ret;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception($e);
		}
	}
}