<?php

class It6_Models_Acl extends It6_Models_Abstract {

//TODO: this class must be refactored due to earthquake in ACL tables

protected static $_registryEntryDb = 'zdb_admin';

/**
 * @param array|string $role one or more roles names
 * @return
 */
public static function getRoleId($role, &$db = null) {
	static $_cache = array();
	$more = is_array($role);
	if (!$more)
		$role = array($role);
	$unknown = array();
	foreach ($role as $r) {
		if (!array_key_exists($r, $_cache))
			$unknown[] = $r;
	}
	if (!empty($unknown)) {
		static::assureDbParam($db);
		$rows = $db->select()
			->from('acl_role', array('acl_role_id', 'acl_role_name'))
			->where('acl_role_name IN (?)', $unknown)
			->query()
			->fetchAll();
		foreach ($rows as $row)
			$_cache[$row['acl_role_name']] = $row['acl_role_id'];
	}
	$result = array();
	foreach ($role as $r) {
		$result[$r] = (empty($_cache[$r]) ? 0 : $_cache[$r]);
	}
	return ($more ? $result : $result[$role[0]]);
}

public static function getParentRoles($role, &$db = null) {
	static::assureDbParam($db);
	$rows = $db->select()
		->from(array('r' => 'acl_role'), array())
		->join(array('p' => 'acl_role_has_parent'), 'r.acl_role_id=p.acl_role_id', array())
		->join(array('pr' => 'acl_role'), 'p.parent_id=pr.acl_role_id', array('pr.acl_role_name'))
		->where('r.acl_role_name=?', $role)
		->order('p.parent_order')
		->query()
		->fetchAll();
	$parents = array();
	foreach ($rows as $row) {
		$parents[] = $row['acl_role_name'];
	}
	return $parents;
}

/**
 * @param string $role Name of role for which parent should be set.
 * @param array $parentRoles Names of parent roles (order matters!)
 * @return TRUE|FALSE
 */
public static function setParentRoles($role, array $parentRoles, &$db = null, $useTransaction = false) {
	static::assureDbParam($db);
	try {
		if ($useTransaction)
			$db->beginTransaction();
		$roleId = static::getRoleId($role, $db);
		if (empty($roleId))
			return false;
		$parentIds = static::getRoleId($parentRoles);
		$db->delete('acl_role_has_parent', array('acl_role_id=?' => $roleId));
		$i = 0;
		foreach ($parentRoles as $parent) {
			if (empty($parentIds[$parent]))
				continue;
			$db->insert('acl_role_has_parent', array(
				'acl_role_id' => $roleId,
				'parent_id' => $parentIds[$parent],
				'parent_order' => ++$i,
			));
		}
		if ($useTransaction)
			$db->commit();
		return true;
	}
	catch (Exception $e) {
		if ($useTransaction)
			$db->rollback();
		throw $e;
	}
}

/**
 * @param string $role Role name (child)
 * @param mixed $which TRUE|FALSE|string where TRUE=first, FALSE=last, string=parent_name
 * @returns int|FALSE order of parent or FALSE for error
 */
/*
public static function getParentOrder($role, $which, &$db = null) {
	static::assureDbParam($db);
	$row = $db->select()
		->
}

public static function addParentRole($role, $parentRole, $first, &$db = null) {
	try {
		static::assureDbParam($db);
		$roleId = static::getRoleId($role, $db);
		if (!$roleId)
			return false;
		$parentRoleId = static::getRoleId($parentRole, $db);
		$db->insert('role_has_parent', $array(
			'role_id' => $roleId,
			'parent_id' => $parentRoleId,
			'parent_order' => ($first ? 
		));
		return true;
	}
	catch (Exception $e) {
		return false;
	}
}

public static function removeParentRole($role, $parentRole, &$db = null) {
	static::assureDbParam($db);
	try {
		
	}
	catch (Exception $e) {
	}
}
*/

public static function createRole($roleName, $assignable, &$db = null) {
	try {
		static::assureDbParam($db);
		$db->insert('acl_role', array(
			'acl_role_name' => $roleName,
			'assignable' => ($assignable ? 1 : 0),
		));
		return $db->lastInsertId();
	}
	catch (Exception $e) {
		return false;
	}
}

/**
 * You must assure that role is not parent of other role(s)!
 */
public static function deleteRole($roleName, &$db = null) {
	try {
		static::assureDbParam($db);
		$roleId = static::getRoleId($roleName, $db);
		if (!$roleId)
			return false;
		$db->delete('acl_role_has_parent', array('acl_role_id=?' => $roleId));
		$db->delete('acl_role', array('acl_role_id=?' => $roleId));
		return true;
	}
	catch (Exception $e) {
		return false;
	}
}

/**
 * Returns all assignable roles
 */
public static function getAssignableRoles($db = null) {
	static::assureDbParam($db);
	$rows = $db->select()
		->from(array('r' => 'acl_role'), array('acl_role_name'))
		->where('assignable = 1')
		->order('r.acl_role_name')
		->query()
		->fetchAll();

	$roles = array();
	foreach ($rows as $row)
		$roles[] = $row['acl_role_name'];

	return $roles;
}


} // class It6_Models_Acl
