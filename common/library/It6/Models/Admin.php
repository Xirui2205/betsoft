<?php

class It6_Models_Admin extends It6_Models_Abstract {

const ID_INTERNET = 1;
const ID_INTERNET_LIVE = 10;

protected static $_cache = array();
protected static $_registryEntryDb = 'zdb_admin';

protected static $_table = 'admin';
protected static $_joinPrefix = false;
protected static $_columns = array(
	'id' => 'admin_id',
	'username' => 'username',
	'firstName' => 'first_name',
	'surname' => 'surname',
	'passwd' => 'passwd',
	'phone' => 'phone',
	'email' => 'email',
	'access' => 'access',
	'block' => 'block',
	'blockIp' => 'block_ip',
	'key' => 'key',
	'ldapUsername' => 'ldap_username',
	'lastLogin' => 'last_login',
	'branchId' => 'branch_id'
);
protected static $_joinedColumns = array();
protected static $_joinConstraints = array();
protected static $_joinPrefixes = false;
protected static $_primaryKey = 'id';
protected static $_readDataModifiers = array();
protected static $_readDataAllModifiers = array();

public static function readDataAll(&$db = null) {
	static::$_readDataAllModifiers = array();
	return parent::readDataAll($db);
}

public static function readDataAllOrdered($orderBy = null, $orderDir = 'ASC', $limit = null, &$db = null) {
	static::$_readDataAllModifiers = array();
	if (!empty($orderBy))
		static::$_readDataAllModifiers['orderBy'] = array($orderBy => $orderDir); 
	if (!empty($limit))
		static::$_readDataAllModifiers['limit'] = $limit;
	return parent::readDataAll($db);
}

/**
 * Povoli/zakaze pristup admina
 * @param int $userId Admin's db ID
 * @param bool $acces TRUE=povolit, FALSE=zakazat
 */
public static function setAccess($userId, $access, &$db = null) {
	static::assureDbParam($db);
	return $db->update('admin',
		array('access' => ($access ? 0 : 1)),
		array('admin_id=?' => $userId)
	);
}

public static function deleteAdmin($userId, &$db = null) {
	try {
		static::assureDbParam($db);
		if ( !It6_Models_Acl::deleteRole(It6_Acl_Admin::roleNameFromUserId($userId), $db) )
			return false;
		return parent::delete($userId, $db);
	}
	catch (Exception $e) {
		return false;
	}
}

public static function adminExists($username, &$db = null) {
	static::assureDbParam($db);
	$rows = $db->select()
		->from( 'admin', array('c' => new Zend_Db_Expr('COUNT(admin_id)')) )
		->where('username=?', $username)
		->query()
		->fetchAll();
	return (0 != $rows[0]['c']);
}

public static function cryptPasswd($passwd) {
	require_once "Crypt/Rc4.php";
	$key = RC4CRYPTKEY;
	$rc4 = new Crypt_RC4;
	$rc4->key($key);
	$rc4->crypt($passwd);
	return md5($passwd);
}

public static function create($data, &$db = null) {
	if (array_key_exists('passwd', $data))
		$data['passwd'] = static::cryptPasswd($data['passwd']);
	$adminId = parent::create($data, $db);
	if (!$adminId)
		return false;
	$roleId = It6_Models_Acl::createRole(It6_Acl_Admin::roleNameFromUserId($adminId), false);
	return $adminId;
}

/**
 * @returns Array of rows for all matched users (should be zero or one row)
 */
public static function readDataByUsername($username, &$db = null) {
	static::assureDbParam($db);
	return $db->select()
		->from(static::$_table, static::$_columns)
		->where(static::$_columns['username'] . '=?', $username)
		->query()
		->fetchAll();
}

/**
 * @param string $passwd Password in plain text or hash to be tested
 * @param string $encPasswd Password hash that must be matched
 * @returns boolean TRUE if password matches
 */
public static function passwdMatches($passwd, $encPasswd) {
	return ($passwd == $encPasswd || static::cryptPasswd($passwd) == $encPasswd);
}

/**
 * Returns all admins that has given roles.
 * @param string|array $roleName Role(s) that admin must have to be returned
 * @param boolean $allOfTheRoles Set to true if admin must have all of the given roles, false if any of them is sufficient.
 * @param Zend_Db_Adapter $db
 * @return array matched admins' data (empty array if no admin was matched)
 */
public static function readDataAllForRoles($roleName, $allOfTheRoles = true, &$db = null) {
	static::assureDbParam($db);
	if (!is_array($roleName))
		$roleName = array($roleName);
	$all = static::readDataAll($db);
	$matched = array();
	$acl = Zend_Registry::get('acl');
	foreach ($all as $admin) {
		$match = $allOfTheRoles;
		foreach ($roleName as $name) {
			try {
				$hasRole = $acl->userHasRole($name, $admin['id']);
			}
			catch (Exception $e) {
				$hasRole = false;
			}
			if ($hasRole) {
				if (!$allOfTheRoles) {
					$match = true;
					break;
				}
			}
			else { // user hasn't the role
				if ($allOfTheRoles) {
					$match = false;
					break;
				}
			}
		}
		if ($match)
			$matched[] = $admin;
	}
	return $matched;
}

} // class It6_Models_Admin
