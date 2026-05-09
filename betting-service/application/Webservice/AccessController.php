<?php

/**
 * ACL related static methods.
 * @author Petr Stastny
 */
class Webservice_AccessController extends Webservice_AbstractWebService {

public static $TABLE_ROLE = 'acl_role';
public static $TABLE_ROLE_HAS_PARENT = 'acl_role_has_parent';

public static function getDb() {
	return static::getAdminDb();
}

protected static function _getAcl() {
	return Zend_Registry::get('acl');
}

public static function getPrivilege($userId, $usecase) {
	$acl = static::_getAcl();
	//TODO what is usecase?
	throw new It6_XmlRpc_Exception("Unimplemented");
}

/**
 * TODO: $adminId param is not needed, it is queried for current admin
 * @param integer $adminId dummy (maybe for future use?)
 * @returns array
 */
public static function getAllPrivileges($adminId) {
	try {
		$acl = static::_getAcl();
		$resources = $acl->getAllResourcesPrivileges();
		$result = array();
		foreach ($resources as $name => $privileges) {
			if (1 == preg_match('/^('.It6_Acl_Admin::PREFIX_RESOURCE_KASA.'):(.+)$/', $name, $match)) {
				$result[$match[2]] = (empty($privileges) ? 0 : 1);
			}
		}
		return new It6_ArrayWrapper($result);
	}
	catch( Exception $e ) {
		throw new It6_XmlRpc_Exception('getAllPrivileges.', 0, $e); 
	}
}

/**
 * Returns identity from ACL (eg. hostId and branchId for branches)
 * @return struct Complete identity data (component_name => identity_value, ...); names: branch,host,admin
 */
public static function getIdentity() {
	try {
		$acl = static::_getAcl();
		return new It6_ArrayWrapper($acl->getCompleteIdentity());
	}
	catch (Exception $e) {
		throw new It6_XmlRpc_Exception('getIdentity', 0, $e);
	}
}

/* What is this? it has duplicit signature
public static function getAllPrivileges($groupId) {
	$acl = static::_getAcl();
	//TODO implement me
	throw new It6_XmlRpc_Exception("Unimplemented");
}
*/

/**
 * Sets parent role for given admin.
 * Creates user role for admin if necessary, can replace previous parent role(s) or add new parent role(s).
 * @param integer $adminId ID of admin to assign parent role
 * @param string|integer|array $role Role name or role ID to set as parent role for admin, can be list or scalar value
 * @param boolean $replace TRUE if old parent roles shouldn't be preserved, otherwise new roles will be added only (after existing roles)
 * @param boolean $invalidateCache TRUE (default) if ACL cache should be invalidated
 * @return boolean TRUE on success
 */
public static function setParentRole($adminId, $role, $replace, $invalidateCache = true) {
	if (empty($role)) {
		return false;
	}
	$db = static::getDb();
	It6_DbTransaction::begin($db);
	try {
		$userRoleName = It6_Acl_Admin::roleNameFromUserId($adminId);
		$userRoleId = null;
		$newRoleIds = array();
		$newRoleNames = array();
		$newRoles = array();
		$oldRoles = array();
		$order = 0;
		if (!It6_ArrayWrapper::isArray($role)) {
			$role = array($role);
		}
		foreach ($role as $_role) {
			if (ctype_digit($_role)) {
				$newRoleIds[] = $_role;
			}
			else {
				$newRoleNames[] = $_role;
			}
		}
		$select = $db->select()
			->from(static::$TABLE_ROLE, array('roleId' => 'acl_role_id', 'roleName' => 'acl_role_name'))
			->where('acl_role_name=?', $userRoleName);
		if (!empty($newRoleIds)) {
			$select->orWhere('acl_role_id IN (?)', $newRoleIds);
		}
		if (!empty($newRoleNames)) {
			$select->orWhere('acl_role_name IN (?)', $newRoleNames);
		}
		$rows =	$select->query()->fetchAll();
		foreach ($rows as $row) {
			$roleName = $row['roleName'];
			$roleId = $row['roleId'];
			if (!strcasecmp($roleName, $userRoleName)) {
				$userRoleId = intval($roleId);
			}
			else {
				$newRoles[intval($roleId)] = $roleName;
			}
		}
		$unknown = array();
		foreach ($role as $_role) {
			if (ctype_digit($_role)) {
				if (!isset($newRoles[$_role])) {
					$unknown[] = $_role;
				}
			}
			else {
				if (!in_array($_role, $newRoles)) {
					$unknown[] = $_role;
				}
			}
		}
		if (!empty($unknown)) {
			throw new It6_XmlRpc_Exception('Unknown role(s): ' . implode(',', $unknown));
		}
		if (empty($userRoleId)) {
			$n = $db->insert(
				static::$TABLE_ROLE,
				array(
					'acl_role_name' => $userRoleName,
					'assignable' => 0,
				)
			);
			if ($n) {
				$userRoleId = $db->lastInsertId();
			}
			if (empty($userRoleId)) {
				throw new It6_XmlRpc_Exception("Cannot create user role: $userRoleName");
			}
		}
		$rows = $db->select()
			->from(array('p' => static::$TABLE_ROLE_HAS_PARENT), array('order' => 'parent_order'))
			->join(
				array('r' => static::$TABLE_ROLE),
				'p.parent_id=r.acl_role_id',
				array(
					'roleId' => 'acl_role_id',
					'roleName' => 'acl_role_name',
				)
			)
			->where('p.acl_role_id=?', $userRoleId)
			->query()
			->fetchAll();
		foreach ($rows as $row) {
			$oldRoles[$row['roleId']] = $row['roleName'];
			if ($order < $row['order']) {
				$order = $row['order'];
			}
		}
		if ($replace) {
			$db->delete(
				static::$TABLE_ROLE_HAS_PARENT,
				array('acl_role_id=?' => $userRoleId)
			);
			$addRoleIds = array_keys($newRoles);
			$order = 0; // when replacing, parent order will be reset
		}
		else {
			$addRoleIds = array();
			foreach ($newRoles as $roleId => $roleName) {
				if (!isset($oldRoles[$roleId])) {
					$addRoleIds[] = $roleId;
				}
			}
		}
		if (!empty($addRoleIds)) {
			foreach ($addRoleIds as $roleId) {
				$db->insert(
					static::$TABLE_ROLE_HAS_PARENT,
					array(
						'acl_role_id' => $userRoleId,
						'parent_id' => $roleId,
						'parent_order' => ++$order,
					)
				);
			}
		}
		It6_DbTransaction::commit($db);
		$dirty = false;
		if (count($oldRoles) != count($newRoles)) {
			$dirty = true;
		}
		else {
			foreach ($newRoles as $roleId => $roleName) {
				if (!isset($oldRoles[$roleId])) {
					$dirty = true;
					break;
				}
			}
		}
		It6_Log::info(
			($dirty ? 'Admin parent role changed' : 'Admin parent role already same'),
			It6_Log::TAG_ADMIN_OPERATION,
			array('roleAdminId' => $adminId, 'oldRoles' => $oldRoles, 'newRoles' => $newRoles)
		);
		if ($invalidateCache && $dirty) {
			It6_Acl_Admin::invalidatePersistentCache();
		}
		return true;
	}
	catch (Exception $e) {
		It6_DbTransaction::rollback($db);
		It6_Log::err(
			'Failed to set admin parent role',
			It6_Log::TAG_ADMIN_OPERATION,
			array('roleAdminId' => $adminId, 'role' => $role)
		);
		if ($e instanceof It6_XmlRpc_Exception) {
			throw $e;			
		}
		else {
			throw It6_XmlRpc_Exception('Cannot set parent role for admin', 0, $e);
		}
	}
}

} // class Webservice_AccessController