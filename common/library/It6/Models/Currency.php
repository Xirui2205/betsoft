<?php

class It6_Models_Currency extends It6_Models_Abstract {

protected static $_cache = array();

protected static $_table = 'mena';
protected static $_columns = array(
	'id' => 'mena_id',
	'name' => 'mena_text',
	'isoCode' => 'mena_isonum',
	'allowed' => 'mena_allow',
	'webpayMerchant' => 'webpay_merchant',
	'smallestUnit' => 'smallest_unit',
	'smallestCash' => 'smallest_cash',
	'rate' => 'kurz',
);
protected static $_joinedColumns = array( 'rate' => 'mena_kurz' );
protected static $_joinConstraints = array(
	'mena_kurz' => array(
		array('mena' => 'id', 'mena_kurz' => 'id')
	),
);
protected static $_primaryKey = 'id';
protected static $_readDataModifiers = array();
protected static $_readDataAllModifiers = array();
protected static $_joinPrefix = false;
protected static $_joinPrefixes = false;

const ROUND_DOWN = -1;
const ROUND_MATH = 0;
const ROUND_UP = 1;

const ROUND_PARAM_CURRENCY = 1;
const ROUND_PARAM_USER = 2;
const ROUND_PARAM_UNIT = 3;

public static function createSelect(&$db = null) {
	$select = parent::createSelect($db);
//JOIN (
//SELECT mena_id,MAX(`timestamp`) AS `timestamp` FROM mena_kurz WHERE `timestamp` <= '2011-02-10 12:00:00' GROUP BY mena_id HAVING MAX(`timestamp`)
//) mmk
//ON mena_kurz.mena_id=mmk.mena_id AND mena_kurz.`timestamp`=mmk.`timestamp`
	$ts = $db->quoteIdentifier('timestamp');
	$pr1q = $db->quoteIdentifier(static::reserveJoinPrefix('mena_kurz'));
	$pr2 = static::reserveJoinPrefix('mena_kurz_tmp');
	$pr2q = $db->quoteIdentifier($pr2);
	$select->join(
		array($pr2 => new Zend_Db_Expr("(SELECT mena_id,MAX($ts) AS ts FROM mena_kurz WHERE "
			. " $ts <= " . $db->quote(It6_Date::dbNow())
			. " GROUP BY mena_id HAVING MAX($ts))"
		)),
		"$pr1q.mena_id=$pr2q.mena_id AND $pr1q.`timestamp`=$pr2q.`ts`",
		array()
	);
	return $select;
}


/**
 * Reads data for all currencies.
 */
// public static function readDataAll(&$db = null) {
// 	static::assureDbParam($db);
// 	static::$_cache = array();
// 	$rows = static::getSelect($db)->query()->fetchAll();
// 	foreach ($rows as $row) {
// 		$row['rate'] = floatval($row['rate']);
// 		static::$_cache[$row['id']] = $row;
// 	}
// 	return static::$_cache;
// }

/**
 * Cache as array( currency_id => array( 'rate' => currency_rate ) )
 */
// public static function readData($currencyId, &$db = null) {
// 	static::assureDbParam($db);
// 	$more = is_array($currencyId);
// 	if (!$more)
// 		$currencyId = array($currencyId);
// 	foreach ($currencyId as $id)
// 		unset(static::$_cache[$id]);
// 	$rows = static::getSelect($db)
// 		->where('m.mena_id IN (?)', $currencyId)
// 		->query()
// 		->fetchAll();
// 	$result = array();
// 	foreach ($rows as $row) {
// 		$row['rate'] = floatval($row['rate']);
// 		static::$_cache[$row['id']] = $row;
// 		$result[$row['id']] = $row;
// 	}
// 	if ($more)
// 		return $result;
// 	else {
// 		$currencyId = $currencyId[0];
// 		return (array_key_exists($currencyId, $result) ? $result[$currencyId] : null);
// 	}
// }

public static function convertUsingRates($amount, $rateFrom, $rateTo) {
	return $rateTo / $rateFrom * $amount;
}

/**
 * Converts amount from one given currency to other given currency. Rates are cached (request scope).
 * @param int $currencyIdFrom
 * @param int $currencyIdTo
 * @param float $amount
 */
public static function convert($currencyIdFrom, $currencyIdTo, $amount, &$db = null) {
	if (empty($currencyIdFrom) || empty($currencyIdTo))
		return null;

	$rateFrom = static::get($currencyIdFrom, 'rate', $db);
	if (empty($rateFrom))
		return null;

	$rateTo = static::get($currencyIdTo, 'rate', $db);
	if (empty($rateTo))
		return null;

	return static::convertUsingRates($amount, $rateFrom, $rateTo);
}

/**
 * Converts amount between given currency and central currency. Rates are cached (request scope).
 * NOTE: function could be extended to accept $amount as array
 * @param int|array $userId
 * @param float $amount
 * @param bool $toCentral TRUE=(user's -> central), FALSE=(central -> user's)
 */
public static function convertAmountFromCurrency($currencyId, $amount, $toCentral, &$db = null) {
	$rate = static::get($currencyId, 'rate', $db);

	if (empty($currencyId) || empty($rate))
		return null;

	return ($toCentral ? $amount / $rate : $amount * $rate);
}

/**
 * Converts amount between user's currency and central currency. Rates are cached (request scope).
 * NOTE: function could be extended to accept $amount as array
 * @param int|array $userId
 * @param float $amount
 * @param bool $toCc TRUE=(central -> user's), FALSE=(user's -> central)
 */
public static function convertAmount($userId, $amount, $toCentral, &$db = null) {
	if (is_array($userId)) {
		$result = array();
		foreach ($userId as $id) {
			$r = 0;
			if (array_key_exists($id, $currencyId)) {
				$userCurrencyId = $currencyId[$id];
				if (array_key_exists($userCurrencyId, $rate))
					$r = $rate[$userCurrencyId];
			}
			if (0 == $r)
				$result[$id] = null;
			else
				$result[$id] = ($toCentral ? $amount / $r : $amount * $r);
		}
		return $result;
	}
	else {
		$currencyId = It6_Models_User::get($userId, 'currencyId', $db);
		return static::convertAmountFromCurrency($currencyId, $amount, $toCentral, $db);
	}
	
}

/**
 * Converts amount from user's currency to central currency.
 */
public static function convertAmountToCentralCurrency($userId, $amount, &$db = null) {
	return static::convertAmount($userId, $amount, true, $db);
/*
	static $stmt = null;
	static $cache = array();
	if (isset($cache[$userId]) && isset($cache[$userId][$amount]))
		return $cache[$userId][$amount];
	if (!isset($stmt)) {
		static::assureDbParam($db);
		$stmt = $db->prepare('SELECT fn_currency_user2central(?, ?) AS amount');
	}
	if (!$stmt->execute(array($userId, $amount)))
		return false;
	$row = $stmt->fetchAll();
	$cc = $row[0]['amount'];
	if (!isset($cache[$userId]))
		$cache[$userId] = array();
	$cache[$userId][$amount] = $cc;
	return $cc;
*/
}

/**
 * Converts amount from given currency to central currency.
 */
public static function convertAmountToCentralCurrencyFromCurrency($currencyId, $amount, &$db = null) {	
	return static::convertAmountFromCurrency($currencyId, $amount, true, $db);
}

/**
 * Converts amount from central currency to user's currency.
 */
public static function convertAmountToUserCurrency($userId, $amount, &$db = null) {
	return static::convertAmount($userId, $amount, false, $db);
}

/**
 * Converts amount from central currency to given currency.
 */
public static function convertAmountToCurrency($currencyId, $amount, &$db = null) {
	return static::convertAmountFromCurrency($currencyId, $amount, false, $db);
}

public static function getRegisteredName($prependSpace = true) {
	$s = ($prependSpace ? ' ' : '');
	return (Zend_Registry::isRegistered('mena') ? $s . Zend_Registry::get('mena') : '');
}

public static function getCentralCurrencyId(&$db = null) {
	static $id = false;
	if (false === $id)
		$id = static::getCurrencyIdByIso(CENTRAL_CURRENCY_ISO, $db);
	if (empty($id))
		throw new Exception('Central currency not found!'); // that's fatal
	return $id;
}

public static function getCurrencyIdByIso($iso, &$db = null) {
	foreach (static::$_cache as $currency) {
		if ($currency['isoCode'] == $iso)
			return $currency['id'];
	}
	static::assureDbParam($db);
	$rows = static::createSelect($db)
		->where(static::objectToDatabase('isoCode') . '=?', $iso)
		->query()
		->fetchAll();
	if (empty($rows))
		return false;
	if (count($rows) > 1)
		throw new Exception('More currencies for ISO code found. ISO=' . $iso); // that's fatal
	$row = $rows[0];
	static::$_cache[ $row[static::$_primaryKey] ] = $row;
	return $row['id'];
}

public static function getCurrencyByName($name, &$db = null) {
	return static::getDataByUniqueField($name, 'name', $db);
}

/**
 * Uses currency's smallest unit to use proper precision when rounding.
 * @param float $amount Amount to round
 * @param integer $how One of It6_Models_Currency::ROUND_{MATH|UP|DOWN} constants
 * @param mixed $param Data for currency identification, based on value of $currencySource:
 *                  It6_Models_Currency::ROUND_PARAM_CURRENCY ... given currency ID, if null then central currency is used
 *                  It6_Models_Currency::ROUND_PARAM_USER ... user's currency by given user ID, if null then user ID is read from registry
 *                  It6_Models_Currency::ROUND_PARAM_UNIT ... value of smallest currency unit (number)
 * @param integer $paramType One of It6_Models_Currency::ROUND_PARAM_{CURRENCY|USER|UNIT}
 * @param boolean $cash Use smallest unit for cash or not
 * @param Zend_Db_Adapter $db [optional] Not used if $paramType is It6_Models_Currency::ROUND_PARAM_UNIT
 * @returns Rounded value
 */
public static function round($amount, $how = It6_Models_Currency::ROUND_MATH, $param, $paramType, $cash, &$db = null) {
	$currencyId = false;
	$unit = false;
	if (self::ROUND_PARAM_CURRENCY == $paramType) {
		if (empty($param))
			$currencyId = static::getCentralCurrencyId($db);
		else
			$currencyId = $param;
	}
	else if (self::ROUND_PARAM_USER == $paramType) {
		if (empty($currency))
			$userId = Zend_Registry::get('user_id');
		else
			$userId = $param;
		$currencyId = It6_Models_User::get($userId, 'currencyId', $db);
	}
	else if (self::ROUND_PARAM_UNIT == $paramType)
		$unit = floatval($param);
	else
		throw new Exception('Unknown param type');

	if (false === $unit) {
		if (empty($currencyId))
			throw new Exception('Unknown currency');
		$unit = floatval(static::get($currencyId, $cash ? 'smallestCash' : 'smallestUnit', $db));
	}
	if (0 == $unit)
		$unit = 1;
	switch ($how) {
	case self::ROUND_MATH:
		return round($amount * $unit) / $unit;
	case self::ROUND_UP:
		return ceil($amount * $unit) / $unit;
	case self::ROUND_DOWN:
		return floor($amount * $unit) / $unit;
	default:
		throw new Exception('Unknown type of rounding:' . $how);
	}
}

public static function formatAmount($amount, $currencyId, $cash = false, &$db = null) {
	if (empty($currencyId))
		throw new Exception('Unknown currency');
	$unit = floatval(static::get($currencyId, $cash ? 'smallestCash' : 'smallestUnit', $db));
	if ($unit <= 0)
		$precision = 0;
	else
		$precision = max(0, log10($unit));
	return sprintf('%01.' . $precision . 'f', $amount);
}

} // class It6_Models_Currency
