<?php

class It6_Models_PointType extends It6_Models_Abstract {

const ID_RATEADVANCE = 1; // ID of point type used for rate advance cost

protected static $_cache = array();

protected static $_table = 'point_type';
protected static $_columns = array(
	'id' => 'id',
	'name' => 'name',
	'rate' => 'rate'
);

protected static $_primaryKey = 'id';
protected static $_readDataModifiers = array();
protected static $_readDataAllModifiers = array();
protected static $_joinPrefix = false;
protected static $_joinPrefixes = false;

/**
 * Converts amount from one given points to other given points.
 * @param int $pointTypeIdFrom
 * @param int $pointTypeIdTo
 * @param float $amount
 */
public static function convert($currencyIdFrom, $currencyIdTo, $amount, &$db = null) {
	if (empty($pointTypeIdFrom) || empty($pointTypeIdTo))
		return null;
	$rateFrom = static::get($pointTypeIdFrom, 'rate', $db);
	if (empty($rateFrom))
		return null;

	$rateTo = static::get($pointTypeIdTo, 'rate', $db);
	if (empty($rateTo))
		return null;

	return It6_Models_Currency::convertUsingRates($amount, $rateFrom, $rateTo);
}

/**
 * Converts amount between given points and central currency. Rates are cached (request scope).
 * NOTE: function could be extended to accept $amount as array
 * @param int|array $userId
 * @param float $amount
 * @param bool $toCc TRUE=(central -> user's), FALSE=(user's -> central)
 */
public static function convertAmountFromCurrency($pointTypeId, $amount, $toCentral, &$db = null) {
	$rate = static::get($pointTypeId, 'rate', $db);
	
	if (empty($pointTypeId) || empty($rate))
		return null;

	return ($toCentral ? $amount / $rate : $amount * $rate);
}

/**
 * Converts amount from points to central currency
 */
public static function convertAmountToCentralCurrency($pointTypeId, $amount, &$db = null) {	
	return static::convertAmountFromCurrency($pointTypeId, $amount, true, $db);
}

/**
 * Converts amount from central currency to points
 */
public static function convertAmountToPoints($pointTypeId, $amount, &$db = null) {
	return static::convertAmountFromCurrency($pointTypeId, $amount, false, $db);
}

public static function convertCurrencyToPoints($currencyId, $pointTypeId, $amount, &$db = null) {
	$rateFrom = It6_Models_Currency::get($currencyId, 'rate', $db);
	$rateTo = static::get($pointTypeId, 'rate', $db);
	return It6_Models_Currency::convertUsingRates($amount, $rateFrom, $rateTo);
}

public static function convertPointsToCurrency($pointTypeId, $currencyId, $amount, &$db = null) {
	$rateFrom = static::get($pointTypeId, 'rate', $db);
	$rateTo = It6_Models_Currency::get($currencyId, 'rate', $db);
	return It6_Models_Currency::convertUsingRates($amount, $rateFrom, $rateTo);
}

public static function formatAmount($amount, $typeId, &$db = null) {
	if (empty($typeId))
		throw new Exception('Unknown point type');
	$precision = 0;
	return sprintf('%01.' . $precision . 'f', $amount);
}

public static function round($amount) {
	return round($amount);
}

} // class It6_Models_PointType
