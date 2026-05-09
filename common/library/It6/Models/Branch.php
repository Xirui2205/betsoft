<?php

class It6_Models_Branch extends It6_Models_DbDependent {

/**
 * Id of Internet branch
 */
const ID_INTERNET = 1;
const ID_INTERNET_LIVE = 7;


const HANDLE_PADDING_SIZE = 3;

protected static $_cache = array();

/**
 * @param int $branchId ID of branch to which user belongs to
 * @returns int ID of anonynous user that belongs to branch or FALSE when no such found
 * TODO: move to It6_Models_User?
 */
public static function getAnonymousUser($branchId, &$db = null) {
	static $userIds = array();
	if (!array_key_exists($branchId, $userIds)) {
		static::assureDbParam($db);
		$users = $db->select()
			->from('uzivatel', 'user_id')
			->where('branch_id=?', $branchId)
			->where('anonymous=1')
			->query()
			->fetchAll();
		$result = (empty($users) ? false : $users[0]['user_id']);
		$userIds[$branchId] = $result;
	}
	else
		$result = $userIds[$branchId];
	return $result;
}

/**
 * @param string $handle branch handle
 * @returns string formated handle
 */
public static function formatHandleForExport($handle) {
	return str_pad($handle,static::HANDLE_PADDING_SIZE,'0',STR_PAD_LEFT);
}

public static function isSystemId($branchId) {
	return (self::ID_INTERNET == $branchId || self::ID_INTERNET_LIVE == $branchId);
}

} // class It6_Models_Branch
