<?php

class It6_Models_BetType extends It6_Models_Abstract {

protected static $_cache = array();

protected static $_table = 'typ';
protected static $_columns = array(
	'id' => 'typ_id',
	'alias' => "(LPAD(typ_alias_id,2,'0'))",
	'name' => 'nazev',
	'visible' => 'zobrazeno',
	'order' => 'poradi',
	'offerCategoryId' => 'offer_category_id',
	'aliasGroup' => 'typ_alias_group',
	'groupMaster' => 'group_master',
//	'nameLocal' => 'text',
);
protected static $_primaryKey = 'id';
protected static $_joinedColumns = array(
//	'nameLocal' => 'preklady',
//	'transKey' => array('table' => 'preklady', 'column' => 'index_pole')
);
protected static $_joinConstraints = array(
//	'preklady' => array('left', array('preklady' => 'transKey', 'typ' => 'name')),
);

// following variables are updated dynamically (use same initialization values if you don't know what are you doing)
protected static $_joinPrefix = false;
protected static $_joinPrefixes = false;
protected static $_readDataModifiers = array(); // see modifySelect()
protected static $_readDataAllModifiers = array();  // see modifySelectAll()

protected static function _readData($select, $multiple, $rowCallback = null) {
	if (empty($rowCallback))
		$rowCallback = function(&$row) { $row['alias'] = str_pad($row['alias'], 2, '0', STR_PAD_LEFT); };
	return parent::_readData($select, $multiple, $rowCallback);
}

/**
 * Reads all type IDs for given event including derived types (types that are linked by type group relationship)
 * Not cached.
 * @param integer|array $eventId
 * @param Zend_Db_Adapter $db [optional]
 * @return array List of type IDs 
 */
public static function getEventTypeIds($eventId, &$db = null) {
	//SELECT COALESCE(td.typ_id,tm.typ_id) AS id
	// FROM typ_sport ts
	// JOIN typ tm
	// ON tm.typ_id=ts.typ_id AND (tm.typ_alias_group IS NULL OR tm.group_master=1)
	// LEFT JOIN typ td
	// ON tm.typ_alias_group=td.typ_alias_group
	// WHERE ts.sport_id IN (?);
	static::assureDbParam($db);
	$res = $db->select()
		->from(
			array('tu' => 'typ_udalost'),
			array('id' => '(COALESCE(td.typ_id,tm.typ_id))')
		)
		->join(  // master or non-group
			array('tm' => 'typ'),
			'tm.typ_id=tu.typ_id AND (tm.typ_alias_group IS NULL OR tm.group_master=1)',
			array()
		)
		->joinLeft(  // derived from group
			array('td' => 'typ'),
			'tm.typ_alias_group=td.typ_alias_group',
			array()
		)
		->where('tu.udalost_id IN (?)', $eventId)
		->where('tu.is_binded=1')
		->query();
	$ids = array();
	while ($row = $res->fetch())
		$ids[] = $row['id'];
	return $ids;
}

public static function getAllForEvent($eventId = null, $langId = null, &$db = null) {
	
	static::assureDbParam($db);
	$select = static::createSelect();
	$langId = intval($langId);
	$collation = '';
	$colName = static::objectNameToFullColumn('name');
	$colAliasGroup = static::objectNameToFullColumn('aliasGroup');
	if (!empty($langId)) {
		$collation = It6_Models_Language::get($langId, 'collation', $db);
		$collation = (empty($collation) ? '' : " COLLATE $collation");
		$pt1 = static::reserveJoinPrefix('preklady');
		$pt2 = static::reserveJoinPrefix();
		$select->joinLeft(
				array($pt1 => 'preklady'),
				"$pt1.lang_id=$langId AND $pt1.index_pole=$colName",
				array('nameLocal' => "(COALESCE($pt1.text," . static::objectNameToFullColumn('name') . '))') 
			)
			->joinLeft(
				array($pt2 => 'preklady'),
				"$pt2.lang_id=$langId AND $pt2.index_pole=$colAliasGroup",
				array('aliasGroupLocal' => "(COALESCE($pt2.text,$colAliasGroup))") 
			);
		$select->order("(TRIM(COALESCE($pt2.text,$colAliasGroup,$pt1.text,$colName)))$collation");
	}
	else
		$select->order("(TRIM(COALESCE($colAliasGroup,$colName)))$collation");
	if (!empty($eventId)) {
		$typeIds = static::getEventTypeIds($eventId);
		if (empty($typeIds))
			return array();
		$select->where(static::objectNameToFullColumn('id') . ' IN (?)', $typeIds);
	}
	return static::_readData($select, true);
}

public static function isDerived(array $type) {
	return (isset($type['aliasGroup']) && empty($type['groupMaster']));
}

/**
 * Constructs 3 arrays from given type data:
 * 1st: passed array filtered to contain only non-group and master types
 * 2nd: array( group_1 => group_1_master_type_id, ... )
 * 3rd: array( group_1 => array( list_of_all_type_IDs_in_group_1 ), ... ) 
 * @param array $types array( typeId => typeData, ... )
 * @return array list(filtered_master_types, group_masters, group_types)
 */
public static function getNonderivedAndGroups(array $types) {
	$nonderived = array();
	$masters = array();
	$groups = array();
	foreach ($types as $id => $type) {
		if (!static::isDerived($type)) {
			$nonderived[$id] = $type;
			if (!empty($type['groupMaster']))
				$masters[$type['aliasGroup']] = $id;
		}
		if (!empty($type['aliasGroup'])) {
			$group = $type['aliasGroup'];
			if (!array_key_exists($group, $groups))
				$groups[$group] = array($id);
			else
				$groups[$group][] = $id;
		}
	}
	return array($nonderived, $masters, $groups);
}

/**
 * Retrieves free derived type IDs for bet
 * @param integer|array $betId One or more bet IDs, bet should be of derived (grouped) type (by typeId)
 * @param Zend_Db_Adapter $db
 * @return array If $betId is array then returned array has form array(betId => array(typeIds...),...),
 *               if $betId is single ID then array(typeIds...) is returned.
 */
public static function getSiblingBetFreeDerivedTypeIds($betId, &$db = null) {
	if (empty($betId))
		return array();
	$free = array();
	static::assureDbParam($db);
	$res = $db->select()
		->from(array('s' => 'sazky'), array('betId' => 'sazka_id'))
		->join(
			array('t' => static::$_table),
			't.typ_id=s.typ_id AND t.typ_alias_group IS NOT NULL',
			array()
		)
		->join(
			array('dt' => static::$_table),
			'dt.typ_alias_group=t.typ_alias_group',
			array('typ_id')
		)
		->joinLeft(
			array('ss' => 'sazky'),
			'ss.parent_id=s.parent_id AND ss.real_typ_id=dt.typ_id',
			array()
		)
		->where('s.sazka_id IN (?)', $betId)
		->where('ss.sazka_id IS NULL')
		->group(array('s.sazka_id', 'dt.typ_id'))
		->query();
	while ($row = $res->fetch()) {
		$_betId = $row['betId'];
		if (!array_key_exists($_betId, $free))
			$free[$_betId] = array();
		$free[$_betId][] = $row['typeId'];
	}
	if (!is_array($betId))
		return (empty($free[$betId]) ? array() : $free[$betId]);
	else {
		foreach ($betId as $_betId) {
			if (!array_key_exists($_betId, $free))
				$free[$_betId] = array();
		}
		return $free;
	}
}

/**
 * Retrieves free derived type IDs for bet
 * @param integer $betId Parent bet ID
 * @param integer $typeId Grouped type to check free derived types for
 * @param Zend_Db_Adapter $db
 * @return array Array of free type IDs
 */
public static function getParentBetFreeDerivedTypeIds($betId, $typeId, &$db = null) {
	if (empty($betId))
		return array();
	$free = array();
	static::assureDbParam($db);
	$res = $db->select()
		->from(array('t' => static::$_table), array())
		->join(
			array('dt' => static::$_table),
			't.typ_id=' . intval($typeId) . ' AND t.typ_alias_group IS NOT NULL AND dt.typ_alias_group=t.typ_alias_group',
			array('typeId' => 'typ_id')
		)
		->joinLeft(
			array('s' => 'sazky'),
			's.parent_id=' . intval($betId) . ' AND s.real_typ_id=dt.typ_id',
			array()
		)
		->where('s.sazka_id IS NULL OR s.alias_released = 1')
		
		->group(array('s.sazka_id', 'dt.typ_id'))
		->query();
	while ($row = $res->fetch())
		$free[]= $row['typeId'];
	return $free;
}

/**
 * Returns list of type IDs which should NOT have valid_to updated together with parent bet
 * @return array|boolean List of integer IDs to exclude from update, empty list if all should be updated, FALSE if none should be updated
 */
public static function getAllToNotUpdateValidToTimeByParent() {
	// types with valid to offset (will be updated):
	// 17, 27, 28, 89, 90, 139, 140, 141, 142, 157, 158, 159, 161, 162
	return array(21, 29, 31, 32, 34, 36, 38, 39, 40, 41, 42, 43, 44, 49, 52, 54, 55, 98, 171);
}

/**
 * Suggests if two bet types (given by type IDs) should not be correlated
 * @param integer $typeId1
 * @param integer $typeId2
 * @return boolean TRUE if should not be correlated, FALSE if not known
 */
public static function shouldNotBeCorrelated($typeId1, $typeId2) {
	// list of type IDs that should not be correlated if both given type IDs are equal
	static $notCorrelatedSelf = array(143, 116); // shooters; team scores
	// just sets of type IDs where any two from one set should not be correlated 
	static $notCorrelatedSimple = array(
		//array(1, 2, ...), // ...
	);
	// list of groups which each has lists of types, where types from different lists of same group should not be correlated
	// eg. P if "period" group where any 1P item should not be correlated with any 2P or 3P item etc.
	static $notCorrelatedGrouped = array(
		'H' => array( // half time
			'1H' => array(16, 51), // 3way, over/under
			'2H' => array(17, 157), // 3way, over/under
		),
		'P' => array( // period
			'1P' => array(26, 88, 160), // 3way, over/under, double chance
			'2P' => array(27, 89, 161), // 3way, over/under, double chance
			'3P' => array(28, 90, 162), // 3way, over/under, double chance
		),
	);

	if ($typeId1 == $typeId2)
		return in_array($typeId1, $notCorrelatedSelf);

	foreach ($notCorrelatedSimple as $list) {
		if (in_array($typeId1, $list) && in_array($typeId2, $list))
			return true;
	}
	
	foreach ($notCorrelatedGrouped as $group) {
		$list1 = false;
		$list2 = false;
		foreach ($group as $listName => $list) {
			if (in_array($typeId1, $list))
				$list1 = $listName;
			if (in_array($typeId2, $list))
				$list2 = $listName;
		}
		if ($list1 !== false && $list2 !== false && $list1 != $list2)
			return true;
	}
	return false;
}

} // class