<?php

class It6_Acl_Admin extends It6_Acl {

const SEPARATOR = ':';

const IDSOURCE_IDENTITY = 1;
const IDSOURCE_ROLENAME = 2;
const IDSOURCE_ROLENAME_USERHOMEBRANCHMAP = 3;

const TREE_ADMIN = 'admin';
const TREE_BRANCH = 'branch';
const TREE_WS = 'ws';

//NOTE: all prefixes should be included in static variable $reservedResourcePrefixes
const PREFIX_ROLE_USER = 'user'; // all admin application users; PARAMS=(userId)
private static $reservedRolePrefixes = array(
	self::PREFIX_ROLE_USER
);

//NOTE: all prefixes should be included in static variable $reservedResourcePrefixes
const PREFIX_RESOURCE_TREE = 'tree';
const PREFIX_RESOURCE_SECTION = 'section';
const PREFIX_RESOURCE_KASA = 'kasa';
const PREFIX_RESOURCE_WS = 'ws';
private static $reservedResourcePrefixes = array(
	self::PREFIX_RESOURCE_TREE, self::PREFIX_RESOURCE_SECTION, self::PREFIX_RESOURCE_KASA, self::PREFIX_RESOURCE_WS
);

//const ROLE_THIS_SESSION = '_session'; //TODO: what's this? maybe a dead end, I don't remeber :_(
const ROLE_GUEST = 'guest';
const ROLE_SUPERADMIN = 'superadmin';
const ROLE_ADMIN = 'admin';
const ROLE_SUPERBOOKMAKER = 'superbookmaker';
const ROLE_BOOKMAKER = 'bookmaker';
const ROLE_BRANCH_EMPLOYEE = 'branch-employee';
const ROLE_BRANCH_TECHNICIAN = 'branch-technician';
const ROLE_BRANCH_ADMIN = 'branch-admin';
const ROLE_BRANCH_OWNER = 'branch-owner';
const ROLE_INTERNET = 'internet';
const ROLE_GOV_SUPERVISOR = 'gov-supervisor';
const ROLE_SALES = 'sales';
const ROLE_CALLCENTRUM = 'callcentrum';
const ROLE_SUPERVISION = 'supervision';
const ROLE_ACCOUNTANT = 'accountant';

const RESOURCETYPE_GENERAL = 'general';
const RESOURCETYPE_TREE = 'acl-tree';
const RESOURCETYPE_SECTION = 'section';
const RESOURCETYPE_KASA = 'kasa';

const PERSISTENT_CACHE_TTL = 0; // forever
const PERSISTENT_CACHE_PREFIX_TREES_INSTANCE = 'A:ACL_TI';
const PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST = 'A:ACL_TI_LIST';
const PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST_LOCK = 'A:ACL_TI_LIST_LOCK';

// role_prefix => array( identity_component_name_1, identity_component_name_2, ...)
// role name will be built from prefix and components in specified order all glued together using SEPARATOR
private static $reservedRolesNameDefs = array(
	self::PREFIX_ROLE_USER => array(It6_Acl::IDNAME_ADMIN),
);

private $db = null;
private $identity = null;
private $trees = null;
private $user = null;
private $roles = null;
//private $resourceTypes = null;
private $resources = null;

/**
 * @return array Default value for $trees parameter for constructing ACL instance
 */
private static function getDefaultTrees() {
	return array( array('name' => self::TREE_ADMIN) );
}

/**
 * @see __construct()
 */
private function init($identity, array $trees = null, &$db = null) {
	if (!is_array($identity))
		$identity = array(It6_Acl::IDNAME_ADMIN => $identity);
	$this->identity = $identity;
	$this->user = It6_Models_Admin::getData($this->identity[It6_Acl::IDNAME_ADMIN], $db);
	if (!empty($trees))
		$this->trees = $trees;
	else
		$this->trees = self::getDefaultTrees();
	
	if (!isset($db))
		$db = Zend_Registry::get('zdb_admin');
	$this->db = $db;

	It6_Models_AclTree::readDataAll($db);
	It6_Models_AclResourceType::readDataAll($db);
}

/**
 * @param int|array $identity Admin's userId or array(id_name => id, ...). Key It6_Acl::IDNAME_ADMIN is mandatory.
 * @param array $trees [optional] ACL trees to load. array( array('name' => tree_name_1) [, ...] )
 *                     If empty then admin tree will be loaded.
 * @param Zend_Db_Adapter $db [optional]
 */
public function __construct($identity, array $trees = null, &$db = null) {
	$this->init($identity, $trees, $db);
	$this->readRoles();
	$this->readResources();
}

public static function initFromCache($identity, array $trees = null, &$db = null) {
	if (empty($trees))
		$trees = self::getDefaultTrees();
	$treeNames = array_unique( array_map(function($t) { return strtolower($t['name']); }, $trees) );
	sort($treeNames);
	$treeNames = implode(':', $treeNames);
	$key = self::PERSISTENT_CACHE_PREFIX_TREES_INSTANCE . ':' . $treeNames;
	$data = It6_GlobalCache::getKeys(array($key, self::PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST));
	if (isset($data[$key])) {
		$acl = unserialize($data[$key]);
		$acl->init($identity, $trees, $db);
	}
	else {
		$acl = new It6_Acl_Admin($identity, $trees, $db);
		It6_GlobalCache::setKey($key, serialize($acl), self::PERSISTENT_CACHE_TTL);
		self::_registerTreesCache($treeNames);
	}
	return $acl;
} 

public function mapUserIdToHomebranch($userId = null, &$db = null) {
	static $cache = false;
	if (false === $cache) {
		if (!isset($db))
			$db = $this->db;
		$cache = array();
		$res = $db->select()->from('admin', array('admin_id', 'branch_id'))->query();
		while($row = $res->fetch())
			$cache[$row['admin_id']] = $row['branch_id'];
	}
	if (!isset($userId))
		$userId = $this->identity[It6_Acl::IDNAME_ADMIN];
	return (array_key_exists($userId, $cache) ? $cache[$userId] : false);
}

private function reservedRoleCanBeAdded($roleName) {
	if (!self::isReservedRoleName($roleName, $nameData))
		return true;
	$prefix = array_shift($nameData);
	$nameDef = self::$reservedRolesNameDefs[$prefix];
	$n = count($nameDef);
	if (count($nameData) != $n)
		throw new Exception('Invalid count of components in reserved role name: role="' . $roleName . '" (Expected: ' . $n . ')');
	for ($i = 0; $i < $n; ++$i) {
		$idName = $nameDef[$i];
		$idValue = $nameData[$i];
		if (!array_key_exists($idName, $this->identity)
			|| $idValue != $this->identity[$idName])
			return false;
	}
	return true;
}

private function readRoles() {
	$db = $this->db;
	$res = $db->select()
		->from(array('r' => 'acl_role'))
		->joinLeft(array('p' => 'acl_role_has_parent'), 'r.acl_role_id=p.acl_role_id', array('parent_id', 'parent_order'))
		->order(array('p.parent_id', 'p.parent_order'))
		->query();
	$this->roles = array();
	while ($row = $res->fetch()) {
		$name = $row['acl_role_name'];
		$id = $row['acl_role_id'];
		$parent = $row['parent_id'];
		if (!array_key_exists($id, $this->roles)) {
			$this->roles[$id] = array(
				'id' => $id,
				'name' => $name,
				'parents' => (empty($parent) ? array() : array($parent)),
				'assignable' => $row['assignable'],
				'authorized' => $this->reservedRoleCanBeAdded($name),
				'added' => false
			);
		}
		else
			$this->roles[$id]['parents'][] = $parent;
	}
	do {
		$roleAdded = false;
		$roleLeft = false;
		foreach ($this->roles as $id => &$role) {
			if ($role['added'])
				continue;
			$roleName = $role['name'];
			//if (!$role['toAdd'])
			//	continue;
			$parentNames = $this->roleParentsAllAdded($id);
			if (false !== $parentNames) {
				$this->addRole(new Zend_Acl_Role($roleName), $parentNames);
				$role['added'] = true;
				$roleAdded = true;
			}
			else
				$roleLeft = true;
		}
	} while ($roleLeft && $roleAdded);
}

public function getIdentity($idName = null) {
	if (!isset($idName))
		$idName = It6_Acl::IDNAME_ADMIN;
	if (array_key_exists($idName, $this->identity))
		return $this->identity[$idName];
	else
		return null;
}

public function getCompleteIdentity() {
	return $this->identity;
}

public function setHttpAuthForWs(Zend_Http_Client $client, $method = Zend_Http_Client::AUTH_BASIC) {
	$client->setAuth($this->user['username'], $this->user['passwd'], $method);
}

private function getUserRoleName($userId = null) {
	if (!isset($userId))
		$userId = $this->identity[It6_Acl::IDNAME_ADMIN];
	return static::roleNameFromUserId($userId);
}

public static function roleNameFromUserId($userId) {
	if (empty($userId))
		return self::ROLE_GUEST;
	else
		return self::PREFIX_ROLE_USER . self::SEPARATOR . $userId;
}

private function getTreeResourceName($tree = null) {
	if (empty($tree))
		$tree = $this->trees[0]['name'];
	return self::PREFIX_RESOURCE_TREE . self::SEPARATOR . $tree;
}

private static function hasReservedPrefix($name, $prefixes, &$nameData = null) {
	if (false !== strpos($name, self::SEPARATOR)) {
		foreach ($prefixes as $prefix) {
			if (0 === strpos($name, $prefix . self::SEPARATOR)) {
				$nameData = array($prefix);
				$suffix = substr($name, strlen($prefix . self::SEPARATOR));
				foreach (explode(self::SEPARATOR, $suffix) as $part)
					$nameData[] = $part;
				return true;
			}
		}
	}
	return false;
}

public static function isReservedResourceName($name, &$nameData = null) {
	return self::hasReservedPrefix($name, self::$reservedResourcePrefixes, $nameData);
}

public static function isReservedRoleName($name, &$nameData = null) {
	return self::hasReservedPrefix($name, self::$reservedRolePrefixes, $nameData);
}

public function getRoleById($id) {
	return (array_key_exists($id, $this->roles) ? $this->roles[$id]['name'] : null);
}

public function getRoleByName($name) {
	foreach ($this->roles as $id => $role) {
		if ($name == $role['name'])
			return $role;
	}
	return null;
}

private function roleParentsAllAdded($roleId) {
	$names = array();
	foreach ($this->roles[$roleId]['parents'] as $parent) {
		$parentRole = $this->roles[$parent];
		if (false === $parentRole['added'])
			return false;
		$names[] = $parentRole['name'];
	}
	return $names;
}

public function getResourceTypeId($typeName) {
	return It6_Models_AclResourceType::getIdByName($name);
}

private function readResources() {
	$db = $this->db;
	$treeNames = array();
	foreach ($this->trees as $tree)
		$treeNames[$tree['name']] = true;
	$treeNames = array_keys($treeNames);
	$data = array();
	if (!empty($treeNames)) {
		$treeIds = It6_Models_AclTree::getIdByName($treeNames);
		$treeIdMap = array_flip($treeIds);
		if (empty($treeIds) || count($treeNames) != count($treeIds))
			throw new Exception('One or more invalid tree name(s): "' . implode(',', $treeNames) . '"');
		// common privileges for all resources in trees
		// (saved in tree resource, with particular tree ID and with particular tree resource name)
		foreach ($treeNames as $treeName)
			$data[$treeName] = array('treeResources' => array(), 'resources' => array());
		$res = $db->select()->from(array('rs' => 'acl_resource'), array('acl_tree_id'))
			->join(
				array('p' => 'acl_role_resource_privilege'),
				'rs.acl_resource_id=p.acl_resource_id'
				. ' AND rs.acl_tree_id IN (' . $db->quote(array_values($treeIds)) . ')'
				. ' AND rs.acl_resource_type_id=' . $db->quote(It6_Models_AclResourceType::getIdByName(self::RESOURCETYPE_TREE, $db))
				. ' AND rs.acl_resource_name=' . $db->quote($this->getTreeResourceName()),
				array('privilege_name', 'privilege_set')
			)
			->join(array('rl' => 'acl_role'), 'p.acl_role_id=rl.acl_role_id', array('acl_role_name'))
			->query();
		while ($row = $res->fetch()) {
			$data[ $treeIdMap[$row['acl_tree_id']] ]['treeResources'][] = $row;
		}
	}
	foreach ($data as $treeName => $tree) {
		foreach ($tree['treeResources'] as $row) {
			$role = $row['acl_role_name'];
			if ($this->hasRole($role))
				$this->setPrivilege($role, null, $row['privilege_name'], $row['privilege_set']);
		}
	}
	if (!empty($treeIds)) {
		// read all resources in given tree
		$res = $db->select()
			->from(array('rs' => 'acl_resource'), array('acl_tree_id', 'acl_resource_id', 'acl_resource_name', 'acl_resource_parent_id'))
			->joinLeft(
				array('p' => 'acl_role_resource_privilege'),
				'rs.acl_resource_id=p.acl_resource_id',
				array('privilege_name', 'privilege_set')
			)
			->joinLeft(array('rl' => 'acl_role'), 'p.acl_role_id=rl.acl_role_id', array('acl_role_name'))
			->where('rs.acl_tree_id IN (?)', $treeIds)
			->order('rs.acl_resource_parent_id')
			->query();
		while ($row = $res->fetch())
			$data[ $treeIdMap[$row['acl_tree_id']] ]['resources'][] = $row;
	}
	$this->resources = array();
	foreach ($data as $treeName => $tree) {
		foreach ($tree['resources'] as $row) {
			$roleName = $row['acl_role_name'];
			$id = $row['acl_resource_id'];
			$privilegeName = $row['privilege_name'];
			$privilegeSet = $row['privilege_set'];
			if (!array_key_exists($id, $this->resources)) {
				$this->resources[$id] = array(
					'id' => $id,
					'name' => $row['acl_resource_name'],
					'added' => false,
					'parent' => $row['acl_resource_parent_id'],
					'privileges' => array(),
				);
				if (!empty($roleName)) {
					$this->resources[$id]['privileges'][$roleName] =
						( empty($privilegeName) ? array() : array($privilegeName => $privilegeSet) );
				}
			}
			else {
				if (!array_key_exists($roleName, $this->resources[$id]['privileges']))
					$this->resources[$id]['privileges'][$roleName] = array($privilegeName => $privilegeSet);
				else
					$this->resources[$id]['privileges'][$roleName][$privilegeName] = $privilegeSet;
			}
		}
	}
	unset($data);
	// process resources hierarchically -- add resources to ACL and set privileges
	do {
		$rsrcAdded = false;
		$rsrcLeft = false;
		foreach ($this->resources as $id => &$resource) {
			$parent = null;
			if ($resource['added'])
				continue;
			else if (!empty($resource['parent'])) {
				$parent = $this->resources[ $resource['parent'] ];
				if (!$parent['added']) {
					$rsrcLeft = true;
					continue;
				}
				$parent = $parent['name'];
			}
			$rsrcName = $resource['name'];
			$this->add(new Zend_Acl_Resource($rsrcName, $parent));
			$resource['added'] = true;
			$rsrcAdded = true;
			foreach ($resource['privileges'] as $roleName => $privileges) {
				foreach ($privileges as $privilege => $set)
					$this->setPrivilege($roleName, $rsrcName, $privilege, $set);
			}
		}
	} while ($rsrcLeft && $rsrcAdded);
}

public static function getSectionResourceName($section) {
	return self::PREFIX_RESOURCE_SECTION . self::SEPARATOR . $section;
}

private function setPrivilege($role, $resource, $privilegeName, $privilegeValue) {
	if (isset($privilegeValue)) {
		$method = ($privilegeValue ? 'allow' : 'deny');
		$this->$method($role, $resource, $privilegeName);
	}
}

public function isResourceAllowed($resource, $privilege = null, $resourceIsId = false) {
	if (!isset($privilege))
		$privilege = 'read';
	if ($resourceIsId) {
		if (!array_key_exists($resource, $this->resources))
			return false;
		$resource = $this->resources[$resource]['name'];
	}
	try {
		return $this->isAllowed($this->getUserRoleName(), $resource, $privilege);
	}
	catch (Exception $e) {
		return false;
	}
}

public function userHasRole($role, $userId = null) {
	try {
		return $this->inheritsRole($this->getUserRoleName($userId), $role);
	}
	catch (Exception $e) {
		return false;
	}
}

//TODO: dohodnout s Pavlem, co se ma vsechno vracet
public function getAllResourcesPrivileges() {
	static $testPrivileges = array('read');
	$result = array();
	foreach ($this->resources as $id => $resource) {
		$name = $resource['name'];
		$privileges = array();
		foreach ($testPrivileges as $privilege) {
			if ($this->isResourceAllowed($name, $privilege))
				$privileges[] = $privilege;
		}
		$result[$name] = $privileges;
	}
	return $result;
}

/**
 * Invalidates cached ACL instance(s)
 * @param string|array|NULL $trees One tree name or list of tree names or NULL/empty if all trees should be invalidated.
 */
public static function invalidatePersistentCache($trees = null) {
	if (empty($trees)) {
		$keys = It6_GlobalCache::getKey(self::PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST);
		$keys = (empty($keys) ? array() : explode('|', $keys));
	}
	else if (is_array($trees)) {
		sort($trees);
		$keys = array(implode(':', $trees));
	}
	else {
		$keys = array($trees);
	}
	foreach ($keys as $key) { 
		It6_GlobalCache::deleteKey(self::PERSISTENT_CACHE_PREFIX_TREES_INSTANCE . ':' . $key);
	}
	It6_Models_AclResourceType::invalidatePersistentCache();
	It6_Models_AclTree::invalidatePersistentCache();
}

/**
 * Adds tree combination into list of known tree combinations 
 * @param array|string $trees List of tree names (array or into string encoded array)
 */
protected static function _registerTreesCache($trees) {
	try {
		$waitTo = time() + 10;
		do {
			$locked = It6_GlobalCache::addKey(self::PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST_LOCK, 1, 10);
			if ($locked) {
				$treeSets = It6_GlobalCache::getKey(self::PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST, $fetched);
				if ($fetched) {
					$treeSets = explode('|', $treeSets);
				}
				else {
					$treeSets = array();
				}
				if (is_array($trees)) {
					sort($trees);
					$treeSet = implode(':', $trees);
				}
				else {
					$treeSet = $trees;
				}
				if (!in_array($treeSet, $treeSets)) {
					$treeSets[] = $treeSet;
					$treeSets = implode('|', $treeSets);
					It6_GlobalCache::setKey(self::PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST,	$treeSets, 0);
				}
				It6_GlobalCache::deleteKey(self::PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST_LOCK);
				$locked = false;
				return;
			}
		} while (time() < $waitTo); 
		It6_Log::warn('Wait timeout for cache lock', TAG_DEFAULT);
	}
	catch (Exception $e) {
		It6_Log::err('Cannot update cache', TAG_DEFAULT);
		if ($locked) {
			It6_GlobalCache::deleteKey(self::PERSISTENT_CACHE_KEY_TREES_INSTANCE_LIST_LOCK);
		}
	}
}

} //class It6_Acl_Admin
