<?php

/**
 * admin related static methods
 * admin means any admin of the backend system (employee, sales manager, ..)
 * @author Pavel Klinger
 * @see Entities_admin
 *
 */
class Webservice_Admin extends Webservice_AbstractWebService  {

	/**
	 * Name of virtual bookmaker used for automatic ticket confirmation etc.
	 * @var string
	 */
	const BOOKMAKER_ROBOT_NAME = 'Internet';
	
	public static $TABLE = "admin";
	public static $TABLE_PREFIX = "ad";
	public static $TABLE_ADMIN_HAS_PARAMETER = "admin_has_parameter";
	public static $TABLE_SECONDARY_BRANCH = 'admin_secondary_branch';

	protected static $IDENTITY = 'admin_id';
	public static $ENTITY_NAME = "Entities_admin";

	public static $MAIN_DATABASE = 'vic_main';

	protected static $CONV = array(
		'ad.admin_id'    => 'adminId',
		'ad.branch_id'   => 'branchId',
		'ad.partner_id'   => 'partnerId',
		'br.name'     => 'branchName',
		'ap.name'     => 'partnerName',
		'first_name'  => 'firstName',
		'surname'     => 'lastName',
		'username'    => 'loginName',
		'passwd'      => 'password',
		'phone'       => 'phone',
		'email'       => 'email',
		'access'      => 'isBanned',
		'block'       => 'block',
		'block_ip'    => 'blockIp',
		'last_login'  => 'lastLogin',
		'GROUP_CONCAT(rlp.acl_role_name)' => 'role',
		'rlp.acl_role_name' => 'role2'
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->joinLeft(
				array('br' => 'branch'),
				static::$TABLE_PREFIX . '.branch_id = br.id',
				null)
			->joinLeft(
				array('rl' => 'acl_role'),
				"CONCAT('user:',ad.admin_id) = rl.acl_role_name",
				null)
			->joinLeft(
				array('rlhp' => 'acl_role_has_parent'),
				"rl.acl_role_id = rlhp.acl_role_id",
				null)
			->joinLeft(
				array('rlp' => 'acl_role'),
				"rlp.acl_role_id = rlhp.parent_id AND rlp.assignable",
				null)
			->joinLeft(
				array('ap' => self::$MAIN_DATABASE.'.affiliate_partner'),
				static::$TABLE_PREFIX . '.partner_id = ap.id',
				null)
			->group(self::$TABLE_PREFIX.'.admin_id');
	}

	protected static function getDb() {
		return static::getAdminDb();
	}

	/**
	 * Returns all admins in the system.
	 * @return array array of the admin structures
	 * @see Entities_admin
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Returns all admins in the system where admin is associated with branch (primary or secondary).
	 * You must 
	 * @return array array of the admin structures
	 * @see Entities_admin
	 */
	public static function getAllWithBranch($branchId, $extensions = null) {
		$adminIds = static::getIdsFromBranchIncludingSecondary($branchId);
		if (empty($adminIds))
			return array();
		return parent::getAllWhere(array('adminId IN (?)' => $adminIds), $extensions);
	}

	/**
	 * Find admin by given identifier.
	 * @param integer $adminId identifier of the admin
	 * @return struct bet structure
	 * @see Entities_admin
	 */
	public static function getById($adminId, $extensions = null) {
		return parent::getById($adminId, $extensions);
	}

	/**
	 * Login the admin
	 * @param string $login
	 * @param string $password
	 * @param string $fingerprint GUID of host (enclosed in curly braces or not)
	 * @return struct admin structures if succesful otherwise null
	 * @see Entities_Admin
	 */
	public static function login($login, $password, $fingerprint) {

		try {
			$db = static::getDb();

			$encodedPassword = static::encodePassword($password);

			$admins = static::getAllWhere(array(
					'loginName = ?' => $login,
					'password = ?' => $encodedPassword));

			if ( 1 == count($admins) ) {
				$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);
				if (!empty($hostId)) {
					$host = Webservice_Host::getById($hostId);
					if (empty($host))
						throw new It6_XmlRpc_Exception('Host not found: id=' . $hostId);
					if (!empty($host['fingerprint']) && strcasecmp($host['fingerprint'], $fingerprint))
						throw new It6_XmlRpc_Exception('Host fingerprint doesn\'t match. ('.$host['fingerprint'] .'!='.$fingerprint.')');
					if ($admins[0]["isBanned"] == 1  || $admins[0]["block"] > MAX_LOGIN )
						throw new It6_XmlRpc_HostException("Blocked admin!", It6_XmlRpc_Exception::CODE_BLOCKED_ADMIN);
				}
				return static::toEntity($admins[0]);
			}
			else if ( 0 == count($admins) ) {
				throw new It6_XmlRpc_HostException('Invalid username and/or password', It6_XmlRpc_Exception::CODE_WRONG_LOGIN_PASSWORD);
			}
			else {
				It6_Log::warn("Duplicate identities!");
				throw new It6_XmlRpc_HostException('Duplicate admin!', It6_XmlRpc_Exception::CODE_DUPLICATED_ADMIN);
			}
		}
		catch ( It6_XmlRpc_HostException $e){
			throw $e;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("login.", 0, $e);
		}
	}

	/**
	 * Change password of the given admin.
	 * @param integer $adminId identifier of the admin (not login)
	 * @param string $password old password
	 * @param string $newPassword new password
	 * @return boolean true on success otherwise false
	 */
	public static function changePassword($adminId, $password, $newPassword) {

		try {
			$db = static::getDb();

			$encodedPassword = static::encodePassword($password);

			$count = static::getAllWhereCount(array(
					'adminId = ?' => $adminId,
					'password = ?' => $encodedPassword,
					'isBanned = ?'=> 0));

			if ( 0 == $count )
				return false;

			$db->update(
				static::$TABLE,
				array('passwd' => static::encodePassword($newPassword)),
				array( static::$IDENTITY . '= ?' => $adminId)
			);

			return true;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("changePassword.", 0, $e);
		}
	}

	/**
	 * TODO: rewrite
	 * Change password of the given admin.
	 * @param integer $adminId identifier of the admin
	 * @param integer $branchId identifier of the branch
	 * @return array list of admins privileges
	 */
	public static function getPrivileges($adminId, $branchId) {

		try {
			$acl = new It6_Acl_Admin($adminId);
			$acl->getPrivileges($adminId, $branchId);

		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("getPrivileges.", 0, $e);
		}

	}

	/**
	 * Insert new admin. Value of the admin identifier is ignored and new
	 * is generated. Password is automaticly encoded.
	 * @param struct $admin structure of the admin
	 * @return integer admin identifier of the created admin
	 * @see Entities_admin
	 */
	public static function insert($admin) {
		if ( !empty($admin['password']) ) {
			$admin['password'] = static::encodePassword($admin['password']);
		}
		return parent::insert($admin);
	}

	/**
	 * Update admin. Empty password is ignored. Non empty automaticly encoded.
	 * @param struct $admin structure of the admin
	 * @return true on success
	 * @see Entities_admin
	 */
	public static function update($admin) {
		if ( !empty($admin['password']) ) {
			$admin['password'] = static::encodePassword($admin['password']);
		}
		if (isset($admin['secondaryBranches'])) {
			$secBranches = $admin['secondaryBranches'];
			unset($admin['secondaryBranches']);
		}
		else
			$secBranches = array();
		
		$result = parent::update($admin);
		if ($result && is_array($secBranches)) {
			if (!It6_Models_AdminSecondaryBranch::setAdminBranches($admin['adminId'], $secBranches, true, $db))
				$result = false;
		}
		return $result;
	}

	/**
	 * Delete admin.
	 * @param struct $adminId idenetifier of the admin.
	 * @return true on success
	 * @see Entities_admin
	 */
	public static function delete($adminId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns all bets of given admin.
	 * @param integer $adminId
	 * @return array array of the bet structures
	 * @see Entities_Bet
	 */
	public static function getAllBets($adminId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns all tickes canceled by given admin.
	 * @param integer $adminId
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getAllCanceledTickets($adminId, $extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns all tickes payed off by given admin.
	 * @param integer $adminId
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getAllPayedOfTickets($adminId, $extensions = null) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Returns timesheet of a admin by period.
	 * @param string $employeeId identifier of the admin.
	 * @param string $from begin of a period.
	 * @param string $to end of a period.
	 * @return struct array of the admin structures
	 * @see Entities_Admin
	 */
	public static function getTimesheet($employeeId, $from, $to, $extensions = null) {

		return Webservice_Timesheet::getAllWhereOrder(
			array(
				static::$IDENTITY . ' = ?' => $employeeId,
				'day >= ?' => $from,
				'day <= ?' => $to),
			array('day DESC'), $extensions);
	}

	/**
	 * Sets time of arrival for a given admin and a day
	 * @param integer $adminId identifier of the admin.
	 * @param string $day day of attendance.
	 * @param string $arrival time of arrival.
	 * @return string|bool returns arrival or false on error.
	 * @see Entities_admin
	 */
	public static function setArrival($adminId, $day, $arrival) {
		$db = static::getDb();

		try {

			$data = array(
				'admin_id' => $adminId,
				'day' => $day,
				'arrival' => $arrival
			);

			$res = $db->insert(Webservice_Timesheet::$TABLE, $data);

			if ($res)
				return $arrival;
			else
				return false;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("setArrival.", 0, $e);
		}
	}

	/**
	 * Sets time of departure for a given admin and a day
	 * @param integer $adminId identifier of the admin.
	 * @param string $day day of attendance.
	 * @param string $departure time of arrival.
	 * @return string|bool returns arrival or false on error.
	 * @see Entities_admin
	 */
	public static function setDeparture($adminId, $day, $departure) {
		$db = static::getDb();

		try {

			$data = array(
				'departure' => $departure
			);

			$res = $db->update(
				Webservice_Timesheet::$TABLE,
				$data,
				array(static::$IDENTITY . ' = ?' => $adminId, 'day = ?' => $day)
			);

			if ($res)
				return $departure;
			else
				return false;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("setDeparture.", 0, $e);
		}
	}

	private static function encodePassword($password) {
		return It6_Models_Admin::cryptPasswd($password);
	}

	public static function isSystem($adminId) {
		return (It6_Models_Admin::ID_INTERNET == $adminId || It6_Models_Admin::ID_INTERNET_LIVE == $adminId);
	}

	/**
	 * Retrieve secondary branches of given admin
	 * @param integer $adminId Admin ID
	 * @param boolean $complete TRUE if more then just branch IDs should be returned
	 * @return NULL|array <multitype:, unknown>
	 */
	public static function getSecondaryBranches($adminId, $complete = false) {
		if (empty($adminId))
			return null;
		$db = static::getDb();
		return It6_Models_AdminSecondaryBranch::getAdminBranches($adminId, $complete, $db);
	}

	/**
	 * Similar as getById() but returned entity is extended with secondary branches data
	 * @param integer $adminId Admin ID
	 * @param boolean $complete If extended branch data should be included @see It6_Models_AdminSecondaryBranch::getAdminBranches()
	 * @param array $extensions
	 * @return struct Admin entity
	 */
	public static function getByIdWithSecondaryBranches($adminId, $complete = false, $extensions = null) {
		$admin = static::getById($adminId, $extensions);
		if (!empty($admin)) {
			$admin['secondaryBranches'] = static::getSecondaryBranches($adminId, $complete);
		}
		return $admin;
	}

	/**
	 * Retrieves admin ID that have given branch or secondary branch.
	 * @param integer|array $branchId One or list of branch IDs
	 * @return array List of admin IDs
	 */
	public static function getIdsFromBranchIncludingSecondary($branchId) {
		$rows = static::getDb()->select()
			->from(array('a' => static::$TABLE), array('id' => 'admin_id'))
			->joinLeft(array('sb' => static::$TABLE_SECONDARY_BRANCH), 'a.admin_id=sb.admin_id', array())
			->where('sb.branch_id IN (?)', $branchId)
			->orWhere('a.branch_id IN (?)', $branchId)
			->group('a.admin_id')
			->query()
			->fetchAll();
		return array_map(function($r) { return $r['id']; }, $rows);
	}

	/**
	 * Retrieve partner ID for logged admin
	 * @param integer $adminId
	 * @return array integer of partner ID
	 */
	public static function getPartnerByAdmin($adminId) {
		$select = static::getDb()->select()
			->from(array('a' => static::$TABLE), array('partnerId' => 'partner_id'))
			->where('a.admin_id =?', $adminId)
			->limit(1)
			->query()->fetchObject();

		return $select;
	}
}