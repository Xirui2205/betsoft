<?php

//TODO: replace web's Models_Helpers_User and Models_Helpers_UserImData with this class (used widely through web)

class It6_Models_User extends It6_Models_Abstract {

const ID_INTERNET_ANONYMOUS = 0;

protected static $_cache = array();

protected static $_table = 'uzivatel';
protected static $_columns = array(
	'id' => 'user_id',
	'handle' => 'handle',
	'gender' => 'pohlavi',
	'name' => 'jmeno',
	'surname' => 'prijmeni',
	'street' => 'ulice',
	'zipCode' => 'psc',
	'place' => 'misto',
	'birthDate' => 'datum_narozeni',
	'email' => 'email',
	'phone' => 'telefon',
	'nick' => 'nick',
	'password' => 'heslo',
	'newsletter' => 'newsletter',
	'registrationDateTime' => 'datum_registrace',
	'langId' => 'lang_id',
	'countryId' => 'zeme_id',
	'currencyId' => 'mena_id',
	'activationDate' => 'datum_aktivace',
	'watched' => 'watched',
	'maxBet' => 'max_bet',
	'balance' => 'zustatek',
	'chips' => 'zetony',
	'debt' => 'dluh',
	'balanceBonus' => 'zustatek_bonus',
	'currencyName' => 'mena_text',
	'anonymous' => 'anonymous',
	'ebBase' => 'entry_bonus_base',
	'ebAmount' => 'entry_bonus_amount',
	'ebFrom' => 'entry_bonus_from',
	'ebApplied' => 'entry_bonus_applied',
	'ebAppliedAt' => 'entry_bonus_applied_at',
	'ebBalance' => 'entry_bonus_balance',
	'ebVersion' => 'entry_bonus_version',
);
protected static $_joinedColumns = array(
	'balance' => 'uzivatel_im_data',
	'chips' => 'uzivatel_im_data',
	'debt' => 'uzivatel_im_data',
	'balanceBonus' => 'uzivatel_im_data',
	'currencyName' => 'mena',
);
protected static $_joinConstraints = array(
	'uzivatel_im_data' => array(
		array('uzivatel' => 'id', 'uzivatel_im_data' => 'id')
	),
	'mena' => array(
		array('uzivatel' => 'currencyId', 'mena' => 'currencyId')
	),
);
protected static $_primaryKey = 'id';

protected static $_joinPrefix = false;
protected static $_joinPrefixes = false;
protected static $_readDataModifiers = array();
protected static $_readDataAllModifiers = array();

/*
public static function readData($userId, &$db = null) {
	static::assureDbParam($db);
	$more = is_array($userId);
	if (!$more)
		$userId = array($userId);
	foreach ($userId as $id)
		unset(static::$_cache[$id]);
	$rows = $db->select()
		->from(array('u' => 'uzivatel'), array(
			'id' => 'user_id',
			'gender' => 'pohlavi',
			'name' => 'jmeno',
			'surname' => 'prijmeni',
			'street' => 'ulice',
			'zipCode' => 'psc',
			'place' => 'misto',
			'birthDate' => 'datum_narozeni',
			'email' => 'email',
			'phone' => 'telefon',
			'nick' => 'nick',
			'password' => 'heslo',
			'newsletter' => 'newsletter',
			'registrationDateTime' => 'datum_registrace',
			'countryId' => 'zeme_id',
			'currencyId' => 'mena_id',
			'activationDate' => 'datum_aktivace',
			'watched' => 'watched'
		))
		->join(array('d' => 'uzivatel_im_data'), 'u.user_id=d.user_id', array(
			'balance' => 'zustatek',
			'chips' => 'zetony',
			'debt' => 'dluh',
			'balanceBonus' => 'zustatek_bonus',
		))
		->joinLeft(array('m' => 'mena'), 'u.mena_id=m.mena_id', array('currencyName' => 'mena_text'))
		->where('u.user_id IN (?)', $userId)
		->query()
		->fetchAll();
	$result = array();
	foreach ($rows as $row) {
		static::$_cache[$row['id']] = $row;
		$result[$row['id']] = $row;
	}
	if ($more)
		return $result;
	else {
		$userId = $userId[0];
		return (array_key_exists($userId, $result) ? $result[$userId] : null);
	}
}
*/

/**
 * Increase user's individual sport limit ballances by particular value in given array
 * @param integer $userId
 * @param array $sportLimitChanges array(sport_id => value_to_add_in_CC, ...)
 * @param boolean $useTransaction
 * @param Zend_Db_Adapter $db
 */
public static function updateSportLimits($userId, array $sportLimitChanges, $useTransaction = true, $db = null) {
	if ($useTransaction)
		$db->beginTransaction();
	try {
		$rows = $db->select()
			->from('limity_user', array('sport_id', 'vycerpal'))
			->where('user_id=?', $userId)
			->where('sport_id IN (?)', array_keys($sportLimitChanges))
			->query()
			->fetchAll();
		$sportLimitBalances = array();
		foreach ($rows as $row)
			$sportLimitBalances[$row['sport_id']] = $row['vycerpal'];
		foreach ($sportLimitChanges as $sportId => $amount) {
			if (!array_key_exists($sportId, $sportLimitBalances))
				$db->insert('limity_user', array('vycerpal' => $amount, 'sport_id' => $sportId, 'user_id' => $userId));
			else
				$db->update(
					'limity_user',
					array( 'vycerpal' => new Zend_Db_Expr('vycerpal+' . $db->quote($amount, Zend_Db::FLOAT_TYPE)) ),
					array('sport_id=?' => $sportId, 'user_id=?' => $userId)
				);
		}
		if ($useTransaction)
			$db->commit();
		return true;
	}
	catch (Exception $e) {
		if ($useTransaction)
			$db->rollback();
		throw new Exception($e);
	}
}

public static function getPointAccountBalance($userId, $pointTypeId, &$db = null, $clearCache = false) {
	static $balanceCache = array();
	static::assureDbParam($db);
	if ($clearCache || !array_key_exists($pointTypeId, $balanceCache)) {
		$rows = $db->select()->from('point_account', 'balance')
			->where('user_id=?', $userId)
			->where('point_type_id=?', $pointTypeId)
			->query()
			->fetchAll();
		$balanceCache[$pointTypeId] = (empty($rows) ? 0 : $rows[0]['balance']);
	}
	return $balanceCache[$pointTypeId];
}

public static function cryptPassword($passwd){
	require_once "Crypt/Rc4.php";

	$key = RC4CRYPTKEY;
	$rc4 = new Crypt_RC4;
	$rc4->key($key);
	$rc4->crypt($passwd);
	return md5($passwd);
}

/**
 * Generates new (pseudo)random password
 * @param boolean $encrypted TRUE if returned value should be encrypted by cryptPassword()
 * @param integer $length [optional] Count of characters in generated password (default is 10)
 * @return string Generated password
 */
public static function generatePassword($encrypted, $length = 10) {
	static $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+-_!';
	srand(time() - 45062);
	$n = strlen($chars);
	$passwd = '';
	for ($i = 0; $i < $length; ++$i) {
		$passwd .= $chars[rand(0, $n - 1)];
	}
	return ($encrypted ? self::cryptPassword($passwd) : $passwd);
}

public static function getDataByHandle($handle, &$db = null) {
	It6_NineDigitHandle::fixHandle($handle);
	return static::getDataByUniqueField($handle, 'handle', $db);
}

public static function getByHandle($handle, $name, &$db = null) {
	$user = static::getDataByHandle($handle, $db);
	if (empty($user) || !array_key_exists($name, $user))
		return false;
	return $user[$name];
}

/**
 * Balance users for monthly report
 * @param date $dateFrom, date $dateTo, instance db
 * return array
 */
public static function getBalanceUsers($dateFrom, $dateTo, &$db = null) {
	try {
		$rows = $db->select()
			->from(
				array('t' => 'ticket'),
				array(
					'user_id',
					'u.nick',
					'amount' => 'SUM(castka)',
					'win' => 'SUM(win_real)',
					'yield' => 'ROUND(((SUM(win_real)-SUM(castka))/SUM(castka))*100, 2)'
				)
			)
			->join(
				array('u' => 'uzivatel'),
				'u.user_id = t.user_id',
				null
			)
			->where('t.vyplacen = 1')
			->where('u.anonymous = 0')
			->where('vyplacen_date >= ?', $dateFrom)
			->where('vyplacen_date < ?', $dateTo)
			->group('u.user_id')
			->having('amount <= win')
			->order(array('yield DESC'))
			->query()->fetchAll();

		return $rows;
	}
	catch (Exception $e) {
		throw new Exception($e);
	}
}

} // class It6_Models_User