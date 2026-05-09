<?php

/**
 * <dl>
 *   <dt>STATUS_* constants<dt>
 *   <dd>
 *     bet status in changelog joins It6_Models_Bet status
 *     with other flag columns, so there are more values
 *   </dd>
 *   <dt>TYPE_* constants</dt>
 *   <dd>
 *     set of flags, what bet properties was changed 
 *   </dd>
 *   <dt>Field format of <em>correlated</em></dt>
 *   <dd>
 *     string, semicolon separated bet IDs preceded by '+' (added) or '-' (removed),
 *     eg. "+1234;-2345;-3456" 
 *   </dd>
 *   <dt>Field format of <em>rates</em></dt>
 *   <dd>
 *     string, semicolon separated (column ID, rate) pairs, pairs use colon as separator "columnId:rate"
 *     eg. "138:1.23;139:2.31;140:4.56" 
 *   </dd>
 * </p>
 */
class It6_Models_BetChangelog extends It6_Models_Abstract {

const STATUS_NEW = 0;
const STATUS_CANCELED = 1;
const STATUS_SUSPENDED = 2;
const STATUS_EVALUATED = 3;
const STATUS_CONFIRMED = 4;
const STATUS_PAIDOUT = 5;

/**
 * Bet was created,
 * fields: none (maybe will be changed to all)
 */
const TYPE_NEW = 'new';
/**
 * Bet status changed,
 * fields: status 
 */
const TYPE_STATUS = 'status';
/**
 * Bet validity time interval changed,
 * fields: (validFrom | validTo)
 */
const TYPE_VALIDITY = 'validity';
/**
 * Bet text changed,
 * fields: text, ticketText, textNote 
 */
const TYPE_TEXT = 'text';
/**
 * Multiplicity constraints (AKO count, "simple bet" flag)
 * fields: ako, simple
 */
const TYPE_MULTIPLICITY = 'multiplicity';
/**
 * Rate(s) of column changed,
 * fields: rates
 */
const TYPE_RATE = 'rates';
/**
 * Bet's correlated bet changed (some added or removed),
 * fields: correlated
 */
const TYPE_CORRELATED = 'correlated';

/**
 * Bet status -> change status
 * Must be one-to-one.
 * @var array
 */
protected static $betStatusMap = array(
	It6_Models_Bet::STATUS_NEW => self::STATUS_NEW,
	It6_Models_Bet::STATUS_CANCELED => self::STATUS_CANCELED,
	It6_Models_Bet::STATUS_SUSPENDED => self::STATUS_SUSPENDED,
	It6_Models_Bet::STATUS_EVALUATED => self::STATUS_EVALUATED,
);

protected static $_cache = array();

/**
 * Database name of table
 * @var string $_table
 */
protected static $_table = 'bet_changelog';

/**
 * Array of that maps object names to database names ( objectName => dbName ).
 * Also it is list of properties oxposed by this class, also column list for SELECT.
 * @var array $_columns
 */
protected static $_columns = array(
	'changeId' => 'change_id',
	'changeType' => 'change_type',
	'changeTime' => 'change_time',
	'betId' => 'sazka_id',
	'status' => 'status',
	'validFrom' => 'platna_od',
	'validTo' => 'platna_do',
	'text' => 'text',
	'ticketText' => 'ticket_text',
	'textNote' => 'text_note',
	'simple' => 'jednoducha',
	'ako' => 'ako',
	'rates' => 'rates',
	'correlated' => 'correlated',
);

/**
 * Object name of primary key column. (default 'id')
 * @var string $_primaryKey
 */
protected static $_primaryKey = 'changeId';

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

protected static $_joinPrefix = false;
protected static $_joinPrefixes = false;
protected static $_readDataModifiers = array(); // see modifySelect()
protected static $_readDataAllModifiers = array();  // see modifySelectAll()


/**
 * Encodes changes in bet correlations into safe string to be stored in DB.
 * @param array|NULL $addedBetIds List of correlated bets that were added 
 * @param array|NULL $deletedBetIds List of correlated bets that were deleted
 * @return string DB safe string with changes encoded
 */
public static function encodeCorrelatedBetIds($addedBetIds, $deletedBetIds) {
	$fnConvList = function($list, $prefix) {
		$_list = array();
		foreach ($list as $id) {
			$id = intval(trim($id));
			if (0 < $id) {
				$_list[$id] = "$prefix$id";
			}
		}
		return implode(';', $_list);
	};
	$subs = array();
	if (!empty($addedBetIds)) {
		$subs[] = $fnConvList($addedBetIds, '+');
	}
	if (!empty($deletedBetIds)) {
		$subs[] = $fnConvList($deletedBetIds, '-');
	}
	return implode(';', $subs);
}

/**
* Decodes changes in bet correlations string (properly formated) into two bet ID lists.
* @param string $s String with bet correlations' change encoded
* @return array (array listOfAddedIds, array listOfDeletedIds)
*/
public static function decodeCorrelatedBetIds($s) {
	$addedBetIds = array();
	$deletedBetIds = array();
	$tokens = explode(';', $s);
	foreach ($tokens as $token) {
		$token = trim($token);
		$id = intval(trim(substr($token, 1)));
		if (0 < $id) {
			$prefix = substr($token, 0, 1);
			if ('+' == $prefix) {
				$addedBetIds[$id] = $id;
			}
			else if ('-' == $prefix) {
				$deletedBetIds[$id] = $id;
			}
		}
	}
	return array(array_values($addedBetIds), array_values($deletedBetIds));
}

/**
 * Encodes bet rates into string
 * @param array $rates (integer columnId -> float rate)
 * @return string String containing all the rates from bet
 */
public static function encodeRates($rates) {
	$_rates = array();
	foreach ($rates as $columnId => $rate) {
		$columnId = intval($columnId);
		$rate = floatval($rate);
		if (!empty($columnId) && !empty($rate)) {
			$_rates[] = "$columnId:$rate";
		}
	}
	return implode(';', $_rates);
}

/**
 * Decodes bet rates encoded into string by encodeRates()
 * @param string $s
 * @return array (integer colun ID => float rate)
 */
public static function decodeRates($s) {
	$rates = array();
	$pairs = explode(';', $s);
	foreach ($pairs as $pair) {
		list($columnId, $rate) = explode(':', $pair);
		$columnId = intval($columnId);
		$rate = floatval($rate);
		if (!empty($columnId) && !empty($rate)) {
			$rates[$columnId] = $rate;
		}
	}
	return $rates;
}

/**
 * Converts three bet status fields into one change status field
 * @param integer $status
 * @param integer $confirmed (boolean)
 * @param integer $paidOut (boolean)
 * @return integer|boolean Change status value or FALSE on error
 */
public static function betStatusToStatusOfChange($status, $confirmed, $paidOut) {
	if ($paidOut) {
		return self::STATUS_PAIDOUT;
	}
	else if ($confirmed) {
		return self::STATUS_CONFIRMED;
	}
	else if (isset(self::$betStatusMap[$status])) {
		return self::$betStatusMap[$status];
	}
	else {
		return false;
	}
}

/**
 * Converts change status value into three bet status values
 * @param integer $status Status value of change
 * @return array|boolean Triplet (bet status, confirmed, paid out) or FALSE on error
 */
public static function statusOfChangeToBetStatus($status) {
	switch ($status) {
	case self::STATUS_PAIDOUT:
		return array(It6_Models_Bet::STATUS_EVALUATED, 1, 1);
	case self::STATUS_CONFIRMED:
		return array(It6_Models_Bet::STATUS_EVALUATED, 1, 0);
	default:
		$revMap = array_flip(self::$betStatusMap);
		if (isset($revMap[$status])) {
			return array($revMap[$status], 0, 0);
		}
	}
	return false;
}

/**
 * Detects all the changes in the bet data and saves.
 * Passed bet structures must contain following fields:
 * <ul>
 *   <li>integer betId</li>
 *   <li>integer status</li>
 *   <li>integer confirmed ... 0/1</li>
 *   <li>integer paidOut ... 0/1</li>
 *   <li>integer|string validFrom ... UNIX timestamp or DB datetime</li>
 *   <li>integer|string validTo ... UNIX timestamp or DB datetime</li>
 *   <li>string text</li>
 *   <li>string|NULL ticketText</li>
 *   <li>string|NULL textNote</li>
 *   <li>integer simple ... 0/1</li>
 *   <li>integer ako</li>
 *   <li>array rates ... (integer column ID => float rate)</li>
 *   <li>array correlated ... list of integer bet IDs of correlated bets</li>
 * </ul>
 * @param struct|NULL $oldBet Bet data before update, NULL/empty if bet was created
 * @param struct $newBet Bet data after update
 * @return array|boolean Change structure with appropriate fields (empty array if nothing changed;
 *                       time fields will be in DB datetime format) or FALSE on error
 */
public static function getBetChange($oldBet, $newBet) {
	$fnDbDateTime = function($t) {
		return (ctype_digit($t)	? It6_Date::timestampToDb($t) : $t);
	};
	$change = array();
	if (empty($oldBet)) {
		$change['changeType'] = self::TYPE_NEW;
		$change['changeTime'] = It6_Date::dbNow();
		$change['betId'] = $newBet['betId'];
		$change['status'] = self::betStatusToStatusOfChange(
			$newBet['status'], $newBet['confirmed'], $newBet['paidOut']
		);
		$change['validFrom'] = $fnDbDateTime($newBet['validFrom']);
		$change['validTo'] = $fnDbDateTime($newBet['validTo']);
		$change['text'] = $newBet['text'];
		$change['ticketText'] = $newBet['ticketText'];
		$change['textNote'] = $newBet['textNote'];
		$change['simple'] = $newBet['simple'];
		$change['ako'] = $newBet['ako'];
		$change['rates'] = self::encodeRates($newBet['rates']);
		$change['correlated'] = self::encodeCorrelatedBetIds($newBet['correlated'], null);
	}
	else {
		if ($oldBet['betId'] != $newBet['betId']) {
			// bet ID change is not supported
			return false;
		}
		$types = array();
		$oldValue = self::betStatusToStatusOfChange(
			$oldBet['status'], $oldBet['confirmed'], $oldBet['paidOut']
		);
		$newValue = self::betStatusToStatusOfChange(
			$newBet['status'], $newBet['confirmed'], $newBet['paidOut']
		);
		if ($oldValue != $newValue) {
			$types[self::TYPE_STATUS] = self::TYPE_STATUS;
			$change['status'] = $newValue;
		}
		$oldValue = $fnDbDateTime($oldBet['validFrom']);
		$newValue = $fnDbDateTime($newBet['validFrom']);
		if ($oldValue != $newValue) {
			$types[self::TYPE_VALIDITY] = self::TYPE_VALIDITY;
			$change['validFrom'] = $newValue;
		}
		$oldValue = $fnDbDateTime($oldBet['validTo']);
		$newValue = $fnDbDateTime($newBet['validTo']);
		if ($oldValue != $newValue) {
			$types[self::TYPE_VALIDITY] = self::TYPE_VALIDITY;
			$change['validTo'] = $newValue;
		}
		if ($oldBet['text'] != $newBet['text']) {
			$types[self::TYPE_TEXT] = self::TYPE_TEXT;
			$change['text'] = $newBet['text'];
		}
		if ($oldBet['ticketText'] != $newBet['ticketText']) {
			$types[self::TYPE_TEXT] = self::TYPE_TEXT;
			$change['ticketText'] = $newBet['ticketText'];
		}
		if ($oldBet['textNote'] != $newBet['textNote']) {
			$types[self::TYPE_TEXT] = self::TYPE_TEXT;
			$change['textNote'] = $newBet['textNote'];
		}
		$oldValue = ($oldBet['simple'] ? 1 : 0);
		$newValue = ($newBet['simple'] ? 1 : 0);
		if ($oldValue != $newValue) {
			$types[self::TYPE_TEXT] = self::TYPE_MULTIPLICITY;
			$change['simple'] = $newValue;
		}
		$oldValue = intval($oldBet['ako']);
		$newValue = intval($newBet['ako']);
		if ($oldValue != $newValue) {
			$types[self::TYPE_TEXT] = self::TYPE_MULTIPLICITY;
			$change['ako'] = $newValue;
		}
		if (self::ratesChanged($oldBet['rates'], $newBet['rates'])) {
			$types[self::TYPE_RATE] = self::TYPE_RATE;
			$change['rates'] = self::encodeRates($newBet['rates']);
		}
		$common = array_intersect($oldBet['correlated'], $newBet['correlated']);
		$deleted = array_diff($oldBet['correlated'], $common);
		$added = array_diff($newBet['correlated'], $common);
		if (!empty($deleted) || !empty($added)) {
			$types[self::TYPE_CORRELATED] = self::TYPE_CORRELATED;
			$change['correlated'] = self::encodeCorrelatedBetIds($added, $deleted);
		}
		if (!empty($types)) {
			$change['changeType'] = array_values($types);
			$change['changeTime'] = It6_Date::dbNow();
			$change['betId'] = $newBet['betId'];
		}
	}
	return $change;
}

/**
 * Compares two (column => rate) maps and indicates change occurance
 * @param array $oldRates Original (column ID => rate) map
 * @param array $newRates New (column ID => rate) map
 * @return boolean TRUE if maps are equal FALSE otherwise
 */
public static function ratesChanged($oldRates, $newRates) {
	$ratesChanged = false;
	$oldColumns = array_keys($oldRates);
	$newColumns = array_keys($newRates);
	sort($oldColumns);
	sort($newColumns);
	if ($oldColumns != $newColumns) {
		$ratesChanged = true;
	}
	if (!$ratesChanged) {
		foreach ($oldRates as $oldColumnId => $oldRate) {
			if ($oldRate != $newRates[$oldColumnId]) {
				$ratesChanged = true;
				break;
			}
		}
	}
	return $ratesChanged;
}

/**
 * Saves changes of other bets that were generated from given bet change
 * (eg. changes of correlated bets).
 * @param struct $change Original change
 * @param Zend_Db_Adapter $db
 * @return array List of change IDs, can be empty
 */ 
public static function saveCoupledChanges($change, &$db = null) {
	$changeIds = array();
	$changeType = (
		is_array($change['changeType'])
		? $change['changeType']
		: explode(',', $change['changeType'])
	);
	if (in_array(static::TYPE_CORRELATED, $changeType)) {
		$betId = $change['betId'];
		$coupledChange = array(
			'betId' => 0,
			'changeType' => static::TYPE_CORRELATED,
			'changeTime' => $change['changeTime'],
			'correlated' => '',
		);
		list($added, $deleted) = static::decodeCorrelatedBetIds($change['correlated']);
		foreach ($added as $corrBetId) {
			if ($corrBetId != $betId) { // just for sure
				$coupledChange['betId'] = $corrBetId;
				$coupledChange['correlated'] = "+$betId";
				$changeIds[] = static::create($coupledChange, $db);
			}
		}
		foreach ($deleted as $corrBetId) {
			if ($corrBetId != $betId) { // just for sure
				$coupledChange['betId'] = $corrBetId;
				$coupledChange['correlated'] = "-$betId";
				$changeIds[] = static::create($coupledChange, $db);
			}
		}
	}
	return $changeIds;
}

/**
 * Saves bet changelog record in DB.
 * Note that saveCoupledChanges() is called.
 * @param struct $change Structure with data of bet change
 * @param Zend_Db_Adapter $db
 * @return integer|boolean ID of change stored in DB or FALSE if there was nothing to be saved
 * @throws Zend_Db_Exception
 */
public static function saveBetChange($change, &$db = null) {
	if (empty($change) || empty($change['changeType'])) {
		return false;
	}
	if (is_array($change['changeType'])) {
		$changeType = $change['changeType'];
		$change['changeType'] = implode(',', $change['changeType']);
	}
	else {
		$changeType = explode(',', $change['changeType']);
	}
	$changeId = static::create($change, $db);
	$change['changeType'] = $changeType;
	$coupledChangeIds = static::saveCoupledChanges($change, $db);
	return $changeId;
}

/**
 * Shorthand method for status only change
 * @param integer $betId
 * @param integer $status Bet status
 * @param integer $confirmed 0/1
 * @param integer $paidOut 0/1
 * @param Zend_Db_adapter $db
 * @return integer ID of change stored in DB
 */
public static function saveBetChangeOfStatus($betId, $status, $confirmed, $paidOut, &$db = null) {
	$change['betId'] = $betId;
	$change['changeType'] = self::TYPE_STATUS;
	$change['changeTime'] = It6_Date::dbNow();
	$change['status'] = self::betStatusToStatusOfChange($status, $confirmed, $paidOut);
	return static::saveBetChange($change, $db);
}

/**
 * Shorthand method for validity only change
 * @param integer $betId
 * @param integer|string|NULL $validFrom UNIX timestamp or DB datetime or empty for no change
 * @param integer|string|NULL $validTo UNIX timestamp or DB datetime or empty for no change
 * @param Zend_Db_adapter $db
 * @return integer ID of change stored in DB or FALSE if there was nothing to be saved
 */
public static function saveBetChangeOfValidity($betId, $validFrom, $validTo, &$db = null) {
	if (empty($validFrom) && empty($validTo)) {
		return false;
	}
	$change['betId'] = $betId;
	$change['changeType'] = self::TYPE_VALIDITY;
	$change['changeTime'] = It6_Date::dbNow();
	if (!empty($validFrom)) {
		$change['validFrom'] = (ctype_digit($validFrom)	? It6_Date::timestampToDb($validFrom) : $validFrom);
	}
	if (!empty($validTo)) {
		$change['validTo'] = (ctype_digit($validTo)	? It6_Date::timestampToDb($validTo) : $validTo);
	}
	return static::saveBetChange($change, $db);
}

/**
 * Shorthand method for rates only change
 * @param integer $betId
 * @param array $rates (column ID => new rate value)
 * @param Zend_Db_adapter $db
 * @return integer ID of change stored in DB or FALSE if there was nothing to be saved
 */
public static function saveBetChangeOfRates($betId, $rates, &$db = null) {
	if (empty($rates)) {
		return false;
	}
	$change['betId'] = $betId;
	$change['changeType'] = self::TYPE_RATE;
	$change['changeTime'] = It6_Date::dbNow();
	$change['rates'] = self::encodeRates($rates);
	return static::saveBetChange($change, $db);
}

/**
 * Shorthand method for correlated bets only change
 * @param integer $betId
 * @param array $added List of correlated bets that was added
 * @param array $deleted List of correlated betd that was deleted
 * @param Zend_Db_adapter $db
 * @return integer ID of change stored in DB or FALSE if there was nothing to be saved
 */
public static function saveBetChangeOfCorrelated($betId, $added, $deleted, &$db = null) {
	if (empty($added) && empty($deleted)) {
		return false;
	}
	$change['betId'] = $betId;
	$change['changeType'] = self::TYPE_CORRELATED;
	$change['changeTime'] = It6_Date::dbNow();
	$change['correlated'] = self::encodeCorrelatedBetIds($added, $deleted);
	return static::saveBetChange($change, $db);
}

/**
 * Merges newer change into current change data, it is only sensible to pass changes
 * for one bet (no check that two changes has same bet ID is performed).
 * @param struct $currentChange Change data from DB
 * @param struct $nextChange Change data from DB newer than $currentChange
 * @param array $fieldMap Map (defaultFieldName => returnedFieldName) for returned structure(s)
 *                        field names translation 
 * @return struct Merged change data
 */
private static function mergeChanges($currentChange, $nextChange, $fieldMap = null) {
	static $fnAssign = null;
	if (!isset($fnAssign)) {
		$fnAssign = function($key, &$dst, $src) use ($fieldMap) {
			if (isset($src[$key])) {
				$field = (isset($fieldMap[$key]) ? $fieldMap[$key] : $key);
				$dst[$field] = $src[$key];
			}
		};
	}
	$types = array_flip(explode(',', $nextChange['changeType']));
	if (isset($types[self::TYPE_NEW])) {
		$currentChange = array();
		foreach (self::$_columns as $srcField => $_) {
			$field = (
					isset($fieldMap[$srcField])
					? $fieldMap[$srcField]
					: $srcField
			);
			$currentChange[$field] = $nextChange[$srcField];
		}
		$currentChange['rates'] = self::decodeRates($nextChange['rates']);
		list($added, $deleted) = self::decodeCorrelatedBetIds($nextChange['correlated']);
		$currentChange['correlated'] = array('added' => $added, 'deleted' => $deleted);
	}
	if (isset($types[self::TYPE_STATUS])) {
		$fnAssign('status', $currentChange, $nextChange);
	}
	if (isset($types[self::TYPE_VALIDITY])) {
		$fnAssign('validFrom', $currentChange, $nextChange);
		$fnAssign('validTo', $currentChange, $nextChange);
	}
	if (isset($types[self::TYPE_TEXT])) {
		$fnAssign('text', $currentChange, $nextChange);
		$fnAssign('ticketText', $currentChange, $nextChange);
		$fnAssign('textNote', $currentChange, $nextChange);
	}
	if (isset($types[self::TYPE_MULTIPLICITY])) {
		$fnAssign('simple', $currentChange, $nextChange);
		$fnAssign('ako', $currentChange, $nextChange);
	}
	if (isset($types[self::TYPE_RATE])) {
		$currentChange['rates'] = self::decodeRates($nextChange['rates']);
	}
	if (isset($types[self::TYPE_CORRELATED])) {
		list($added, $deleted) = self::decodeCorrelatedBetIds($nextChange['correlated']);
		$currentChange['correlated'] = array('added' => $added, 'deleted' => $deleted);
	}
	$currentChange['changeId'] = $nextChange['changeId'];
	$currentChange['changeTime'] = $nextChange['changeTime'];
	return $currentChange;
}

/**
 * Retrieves bet changes made after given "from" time
 * (can be limited also with "to" time)and merges them into one.
 * @param integer|array|NULL $betId One or more bet IDs or NULL if all bets
 * @param integer|string $from UNIX timestamp or datetime in DB format to take
 *                       changes logged after this time
 * @param integer|string|NULL $to UNIX timestamp or datetime in DB format to take
 *                            changes logged before this time, if NULL then not limited
 * @param array $fieldMap Map (defaultFieldName => returnedFieldName) for returned structure(s)
 *                        field names translation 
 * @param Zend_Db_Adapter $db
 * @return array|struct If one bet was requested, one structure is returned,
 *                      if more/all bets was requested map (betId => struct) is returned.
 *                      Structure can have following fields (only fields that was changed
 *                      are present, therefor structure can be empty), untranslated versions:
 *                      <dl>
 *                       <dt>changeTime</dt>
 *                       <dd>DB datetime of last change, present only if any change was found</dd>
 *                       <dt>changeId</dt>
 *                       <dd>ID of last change, it is unique and ascending, present only if any change was found</dd>
 *                       <dt>status</dt>
 *                       <dd>see It6_Models_BetChangelog::STATUS_* constants</dd>
 *                       <dt>validFrom<dt>
 *                       <dd>bet validity from, DB datetime</dd>
 *                       <dt>validTo</dt>
 *                       <dd>bet validity to, DB datetime</dd>
 *                       <dt>text</dt>
 *                       <dd>bet text</dd>
 *                       <dt>ticketText</dt>
 *                       <dd>bet text for ticket</dd>
 *                       <dt>textNote</dt>
 *                       <dd>bet text note</dd>
 *                       <dt>simple</dt>
 *                       <dd>bet "can be only at simple ticket" flag, integer 0/1</dd>
 *                       <dt>ako</dt>
 *                       <dd>bet AKO count, integer</dd>
 *                       <dt>rates</dt>
 *                       <dd>map (columnId =&gt; rate)</dd>
 *                       <dt>correlated</dt>
 *                       <dd>
 *                         struct with two fields, list of bets IDs each:
 *                         <ul>
 *                           <li>added ... new correlated bets</li>
 *                           <li>deleted ... bets no longer being correlated</li>
 *                         </ul>
 *                       </dd>
 *                      </dl>
 */
public static function getMergedChanges($betId, $from, $to = null, $fieldMap = null, &$db = null) {
	static::assureDbParam($db);
	if (is_array($betId) && empty($betId)) {
		return array();
	} 
	$dbFrom = (ctype_digit($from) ? It6_Date::timestampToDb($from) : $from);
	$select = $db->select()
		->from(self::$_table, self::$_columns)
		->where('change_time>=?', $dbFrom)
		->order('change_id ASC');
	if (isset($to)) {
		$dbTo = (ctype_digit($to) ? It6_Date::timestampToDb($to) : $to);
		$select->where('platna_do<=?', $dbTo);
	}
	if (isset($betId)) {
		$select->where('sazka_id IN (?)', $betId);
	}
	$stmt = $select->query();
	$change = array();
	while ($row = $stmt->fetch()) {
		$_betId = $row['betId'];
		$change[$_betId] = static::mergeChanges(
				(isset($change[$_betId]) ? $change[$_betId] : array()),
				$row,
				$fieldMap
		);
	}
	if (!isset($betId)) {
		return $change;
	}
	else if (is_array($betId)) {
		// assure that all requested bet IDs will have its item in returned map
		foreach ($betId as $_betId) {
			if (!isset($change[$_betId])) {
				$change[$_betId] = array();
			}
		}
		return $change;
	}
	else {
		return (isset($change[$betId]) ?  $change[$betId] : array());
	}
}

/**
 * @param integer|struct $betIds Integer after change ID for all bets or map (bet ID => after change ID)
 * @param array $fieldMap Map (defaultFieldName => returnedFieldName) for returned structure(s)
 *                        field names translation 
 * @param Zend_Db_Adapter $db
 * @return struct Map (betID => merged changes struct), see getMergedChanges for description of change structure
 * @see It6_Models_BetChangelog::getMergedChanges()
 */
public static function getMergedChangesAfterId($betIds, $fieldMap = null, &$db = null) {
	static::assureDbParam($db);
	if (!is_array($betIds) || empty($betIds)) {
		return array();
	}
	$betConds = array();
	foreach ($betIds as $betId => $changeId) {
		$betConds[] = '(sazka_id=' . intval($betId) . ' AND change_id>' . intval($changeId) . ')';
	}
	$select = $db->select()
		->from(self::$_table, self::$_columns)
		->order('change_id ASC')
		->where(implode('OR', $betConds));
	$stmt = $select->query();
	$change = array();
	while ($row = $stmt->fetch()) {
		$_betId = $row['betId'];
		$change[$_betId] = static::mergeChanges(
				(isset($change[$_betId]) ? $change[$_betId] : array()),
				$row,
				$fieldMap
		);
	}
	// assure that all requested bet IDs will have its item in returned map
	foreach ($betIds as $betId) {
		if (!isset($change[$betId])) {
			$change[$betId] = array();
		}
	}
	return $change;
}

} // class
