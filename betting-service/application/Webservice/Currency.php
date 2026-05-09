<?php
/**
 * Currency related static methods
 * @author Pavel Klinger
 * @see Entities_Currency
 *
 */
class Webservice_Currency extends Webservice_AbstractWebService  {

	public static $TABLE 				= "vic_main.mena";
	public static $TABLE_PREFIX			= "cur";
	public static $CURRENCY_RATE_TABLE	= "mena_kurz";
	public static $ENTITY_NAME			= "Entities_Currency";
	public static $IDENTITY				= "mena_id";

	public static $CONV = array(
		'mena_id'		=> 'currencyId',
		'mena_isonum'	=> 'iso',
		'mena_text'		=> 'name',
		'mena_info'		=> 'description',
	);

	/**
	 * Returns all currencies in the system.
	 * @return struct object of the currency structures
	 * @see Entities_Currency
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Returns all currencies in the system - just given columns
	 * $param array $columns array of the desired columns
	 * @return struct object of the currency structures
	 * @see Entities_Currency
	 */
	public static function getAllColumns($columns, $extensions = null) {
		return parent::getAllColumns($columns, $extensions);
	}

	/**
	 * Find currency by given identifier.
	 * @param integer $currencyId identifier of the currency
	 * @return struct currency structure
	 * @see Entities_Currency
	 */
	public static function getById($currencyId, $extensions = null) {
		static $cache = array();
		if (!isset($extensions)) {
			if (!array_key_exists($currencyId, $cache))
				$cache[$currencyId] = parent::getById($currencyId);
			return $cache[$currencyId];
		}
		else
			return parent::getById($currencyId, $extensions);
	}


	/**
	 * Insert new currency. Value of the currency identifier is ignored and new
	 * is generated.
	 * @param struct $currency structure of the currency
	 * @return integer currency identifier of the created currency
	 * @see Entities_Currency
	 */
	public static function insert($currency) {
		return parent::insert($currency);
	}

	/**
	 * Update currency.
	 * @param struct $currency structure of the currency
	 * @return true on success
	 * @see Entities_Currency
	 */
	public static function update($currency) {
		return parent::update($currency);
	}

	/**
	 * Delete currency.
	 * @param struct $currencyId idenetifier of the currency.
	 * @return true on success
	 * @see Entities_Currency
	 */
	public static function delete($currencyId) {
		return parent::delete($currencyId);
	}

	/**
	 * All users using this currency.
	 * @return array array of user structures
	 * @see Entities_User
	 */
	public static function getAllUsers() {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns current rate of given currency.
	 * @param integer $currencyId identifier of the currency
	 * @return float
	 */
	public static function getCurrentRate($currencyId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Return rate of the gicen currency for given date.
	 * @param integer $currencyId
	 * @param date $date
	 * @return float
	 */
	public static function getRateForDate($currencyId, $date) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns system currency
	 * @return struct Entities_Currency
	 */
	public static function getSystem() {
		return static::getById(static::getSystemId());
	}

	/**
	 * Returns system currency id
	 * @return integer identifier of the central currency
	 */
	public static function getSystemId() {
		$db = static::getDb();
		return It6_Models_Currency::getCentralCurrencyId($db);
	}
	
	/**
	 * Returns rate
	 * @param integer $pointTypeId identifier of the point type
	 * @return float point rate to central currncy
	 */
	public static function getRate($currencyId) {
		$rate = static::getById($currencyId);
		$rate = $rate['rate'];
		return $rate;
	}
	
	/**
	 * Converts money from one currency to another
	 * @param integer $from identifier of the given value currency
	 * @param integer $to identifier of the returned value currency
	 * @param float $value money to exchange
	 * @return float exchanged money
	 */
	public static function exchange($from, $to, $value) {
		static $cache = array();

		if ($from == $to)
			return $value;

		if (!array_key_exists($from, $cache))
			$cache[$from] = static::getById($from);
		$fromCurrency = $cache[$from];
		if (!array_key_exists($to, $cache))
			$cache[$to] = static::getById($to);
		$toCurrency = $cache[$to];

		if ( false == $fromCurrency )
			throw new It6_XmlRpc_Exception("Invalid 'from' currency.");
		if ( false == $toCurrency )
			throw new It6_XmlRpc_Exception("Invalid 'to' currency.");


		return ($value * $toCurrency->rate) / $fromCurrency->rate;
	}

	/**
	 * Converts money to system currency
	 * @param integer $from identifier of the given value currency
	 * @param float $value money to exchanget
	 * @return float exchanged money in the system currency
	 */
	public static function exchangeToSystem($from, $value) {
		$systemCurrency = static::getSystem();
		return static::exchange($from, $systemCurrency->currencyId, $value);
	}

	/**
	 * Converts money from system currency
	 * @param integer $to identifier of the returned currency
	 * @param float $value money to exchange from the system currency
	 * @return float exchanged money in the given currency
	 */
	public static function exchangeFromSystem($to, $value) {
		$systemCurrency = static::getSystem();
		return static::exchange($systemCurrency->currencyId, $to, $value);
	}

	public static function toEntity($currency, $columns = null) {

		if ( empty($currency) ) return null;

		try {
			$db = static::getDb();
			$ret = parent::toEntity($currency, $columns);
			
			if ( empty($columns) || in_array('rate',$columns) ) {
				$currentRate = $db->select()
					->from( static::$CURRENCY_RATE_TABLE )
					->where('mena_id = ?', $ret->currencyId)
					->where('timestamp <= ?', It6_Date::dbNow())
					->order(array('timestamp DESC'))
					->limit(1,0)
					->query()->fetch();
	
				if ( empty($currentRate) ) throw new Exception('No currency rate data');
				if ( empty($currentRate['kurz']) ) throw new Exception('Zero rate');
	
				$ret['rate'] = $currentRate['kurz'];
			}

			return $ret;

		} catch (Exception $e) {
			throw new It6_XmlRpc_Exception('Can not convert to currency entity', 0, $e);
		}

	}

}

