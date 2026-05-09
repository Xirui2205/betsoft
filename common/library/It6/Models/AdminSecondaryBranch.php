<?php

class It6_Models_AdminSecondaryBranch extends It6_Models_DbDependent {

protected static $_registryEntryDb = 'admindb';

protected static $_table = 'admin_secondary_branch';
protected static $_columns = array(
	'adminId' => 'admin_id',
	'branchId' => 'branch_id',
);

/**
 * Retrieve admin's secondary branch IDs
 * @param integer|array $adminId
 * @param boolean $complete TRUE if more data should be returned (FALSE is default)
 * @return array Array of branch IDs if $adminId was scalar or Array of arrays of branch IDs if $adminId was array
 */
public static function getAdminBranches($adminId, $complete = false, &$db = null) {
	if (empty($adminId))
		return array();

	static::assureDbParam($db);
	$more = is_array($adminId);
	$adminIds = ($more ? $adminId : array($adminId));
	$select = $db->select()
		->from(array('s' => static::$_table), static::$_columns)
		->where('admin_id IN (?)', $adminIds);
	if ($complete)
		$select = $select->join(array('b' => 'branch'), 's.branch_id=b.id', array('b.name', 'b.handle'));
	$rows = $select->query()->fetchAll();
	$result = array();
	foreach ($rows as $row) {
		if ($complete) {
			$result[ $row['adminId'] ][ $row['branchId'] ] = array(
				'id' => $row['branchId'],
				'handle' => $row['handle'],
				'name' => $row['name'],
			);
		}
		else
			$result[ $row['adminId'] ][ ] = $row['branchId'];
	}
	foreach ($adminIds as $id) {
		if (!isset($result[$id]))
			$result[$id] = array();
	}
	return ($more ? $result : $result[$adminId]);
}

/**
 * Sets admin's secondary branches to be given set
 * @param integer $adminId Admin ID
 * @param array $branchIds List of branch IDs
 * @param Zend_Db_Adapter $db
 * @return boolean TRUE on success, FALSE otherwise
 */
public static function setAdminBranches($adminId, array $branchIds, $useTransaction = true, &$db = null) {
	static::assureDbParam($db);
	$branchIds = array_unique($branchIds);
	if ($useTransaction)
		It6_DbTransaction::begin($db);
	try {
		$db->delete(self::$_table, array('admin_id=?' => $adminId));
		if (!empty($branchIds)) {
			foreach ($branchIds as $branchId)
				$db->insert(self::$_table, array('admin_id' => $adminId, 'branch_id' => $branchId));
		}
		if ($useTransaction)
			It6_DbTransaction::commit($db);
		return true;
	}
	catch (Exception $e) {
		It6_Log::err('Admin secondary branches not set', It6_Log::TAG_DEFAULT, $e);
		if ($useTransaction)
			It6_DbTransaction::rollback($db);
		return false;
	}
}

} // class
