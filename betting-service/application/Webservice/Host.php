<?php
/**
 * Host related static methods. Host is terminal computer in the branch.
 * @author Pavel Klinger
 * @see Entities_Host
 *
 */
class Webservice_Host extends Webservice_AbstractWebService {

	public static $TABLE                  = "host";
	public static $TABLE_PREFIX           = "hs";
	public static $TABLE_HOST_HAS_PARAMETER   = "host_has_parameter";
	public static $IDENTITY               = "id";

	const HOST_BAN_ALL = "host.ban-all";
	const HOST_BAN_ALL_IN = "host.ban-all-in";
	const HOST_BAN_ALL_OUT = "host.ban-all-out";

	const PARAMETER_BRANCH_FUND_RESERVE = "branch.fundReserve";
	const PARAMETER_BRANCH_MINIMAL_DEPOSIT = "branch.minimalDeposit";
	const PARAMETER_BRANCH_MINIMAL_WITHDRAW = "branch.minimalWithdraw";

	protected static $ENTITY_NAME = "Entities_Host";

	protected static $balanceSum = 0;
	protected static $onTheWayDepositSum = 0;
	protected static $onTheWayWithdrawSum = 0;
	protected static $winTicketsCashSum = 0;
	protected static $userWithdrawsCashSum = 0;
	protected static $totalCollectSum = 0;
	protected static $recommendedWithdrawSum = 0;
	protected static $recommendedDepositSum = 0;

	protected static $CONV = array(
		'hs.id' => 'hostId',
		'branch_id' => 'branchId',
		'name' => 'name',
		'ip' => 'ip',
		'version' => 'version',
		'hs.banned' => 'banned',
		'in_allowed' => 'inAllowed',
		'out_allowed' => 'outAllowed',
		'is_online' => 'isOnline',
		'hardware' => 'hardware',
		'display' => 'display',
		'printer' => 'printer',
		'win_sn' => 'winSn',
		'provider_dns' => 'providerDns',
		'provider_gateway' => 'providerGateway',
		'provider_ip' => 'providerIp',
		'provider_username' => 'providerUsername',
		'provider_password' => 'providerPassword',
		'vic_email' => 'vicEmail',
		'vic_email_password' => 'vicEmailPassword',
		'vic_admin_password' => 'vicAdminPassword',
		'vic_employee_password_1' => 'vicEmployeePassword1',
		'vic_employee_password_2' => 'vicEmployeePassword2',
		'note' => 'note',
		'fingerprint' => 'fingerprint',
		'balance' => 'balance',
		'b.name' => 'branchName',
		'hs.name' => 'hostName',
		'b.currency_id' => 'currencyId',
		'b.handle' => 'branchHandle',
		'CONCAT(b.street,", ",b.town,", ",b.zip)' => 'branchAddress',
		'b.phone' => 'branchPhone',
		'b.town' => 'branchTown',
		'b.is_active' => 'branchIsActive',
	);

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)->join(
			array('b' => 'branch'),
			static::$TABLE_PREFIX . '.branch_id = b.id',
			null);
	}

	protected static function getDb() {
		return static::getAdminDb();
	}

	/**
	 * Returns the CONV table for this object
	 * @return array with the CONV values as specified in this class
	 */
	public static function getConvTable() {
		return self::$CONV;
	}

	/**
	* Returns all hosts in the system
	* @return array array of the host structures
	* @see Entities_Host
	*/
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	* Find host by given identifier.
	* @return struct host structure
	* @param integer $hostId identifier if the host
	* @see Entities_Host
	*/
	public static function getById($hostId, $extensions = null) {
		return parent::getById($hostId, $extensions);
	}

	/**
	* Find host by given name.
	* @return struct host structure
	* @param integer $hostId identifier if the host
	* @see Entities_Host
	*/
	public static function getByName($name) {
		//TODO implement
	}

	/**
	* Find host by branchId.
	* @param integer $branchId identifier of the host
	* @return struct host structure
	* @see Entities_Host
	*/
	public static function getByBranchId($branchId) {
		return parent::getAllWhere(array('branch_id=?' => $branchId));
	}

	/**
	* Find active host by branchId.
	* @param integer $branchId identifier of the host
	* @return struct host structure
	* @see Entities_Host
	*/
	public static function getActiveByBranchId($branchId) {
		return parent::getAllWhere(array('branch_id=?' => $branchId,
				"banned IS NULL"));
	}

	/**
	 * Find host by branchHandle.
	 * @param integer $branchHandle identifier of the host
	 * @return struct host structure
	 * @see Entities_Host
	 */
	public static function getByBranchHandle($branchHandle) {
		return parent::getAllWhere(array('b.handle = ?' => $branchHandle));
	}

	/**
	 * Insert new host. Value of the host identifier is ignored and new
	 * is generated.
	 * @param struct $host structure of the host
	 * @return integer host|bool identifier of the created host or false on error
	 * @see Entities_Host
	 */
	public static function insert($host) {
		$host['isOnline'] = 0;
		return parent::insert($host);
	}

	/**
	 * Returns all banned hosts.
	 * @return struct host structure
	 * @see Entities_Host
	 */
	public static function getBanned($extensions = null) {
		return static::getAllWhere(array('banned IS NOT NULL'), $extensions);
	}

	/**
	 * Update host.
	 * @param struct $host structure of the host
	 * @return true on success
	 * @see Entities_Host
	 */
	public static function update($host) {
		if (array_key_exists('banned', $host) && empty($host['banned'])) {
			$host['banned'] = new Zend_Db_Expr('NULL');
		}
		unset($host['isOnline']);
		return parent::update($host);

	}

	/**
	 * Delete host.
	 * @param struct $hostId idenetifier of the host.
	 * @return true on success
	 * @see Entities_Host
	 */
	public static function delete($hostId) {
		$db = static::getDb();

		It6_DbTransaction::begin($db);

		try {
			$data = array('is_deleted' => 1);

			$res = $db->update(
				static::$TABLE,
				$data,
				array(static::$IDENTITY . ' = ?' => $hostId)
			);

			It6_DbTransaction::commit($db);

			return $res;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not delete host.", 0, $e);
		}
	}

	/**
	* Returns all allowed hosts in the system.
	* @return array array of the host structures
	* @see Entities_Host
	* @see Entities_Host#allowed
	*/
	public static function getAllowed($extensions = null) {
		//TODO implement me
		throw new Exception("Unimplemented");
	}

	/**
	* Returns all online hosts.
	* @return array array of the host structures
	* @see Entities_Host
	* @see Entities_Host#isOnline
	*/
	public static function getOnline($extensions = null) {
		//TODO implement me
		throw new Exception("Unimplemented");
	}

	/**
	 * Returns all opened (not canceled and not payed off) tickets of the host.
	 * @param integer $hostId Identifier of the host
	 * @param integer $userId null is all users
	 * @param string $dateFrom null is -infinity
	 * @param string $dateTo null is infinity
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getAllTickets($hostId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
		$filter = array('hostId = ?' => $hostId);
		if ( !empty($userId) ) $filter['userId = ?'] = $userId;
		if ( !empty($dateFrom) ) $filter['createdTime >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['createdTime <= ?'] = $dateTo;
		return Webservice_Ticket::getAllWhere($filter, $extensions);
	}

	/**
	 * Returns all opened (not canceled and not payed off) tickets of the host.
	 * @param integer $hostId Identifier of the host
	 * @param integer $userId null is all users
	 * @param string $dateFrom null is -infinity
	 * @param string $dateTo null is infinity
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getOpenedTickets($hostId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
		//TODO este vyfiltrovat prohrane tikety
		$filter = array(
			'hostId = ?' => $hostId,
			'canceled = ?' => 0,
			'paidOut = ?' => 0);
		if ( !empty($userId) ) $filter['userId = ?'] = $userId;
		if ( !empty($dateFrom) ) $filter['createdTime >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['createdTime <= ?'] = $dateTo;
		return Webservice_Ticket::getAllWhere($filter, $extensions);
	}

	/**
	 * Returns all canceled tickets of the given host.
	 * @param integer $hostId Identifier of the host
	 * @param integer $userId null is all users
	 * @param string $dateFrom null is -infinity
	 * @param string $dateTo null is infinity
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getCanceledTickets($hostId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
		$filter = array(
			'hostId = ?' => $hostId,
			'canceled <> ?' => 0);
		if ( !empty($userId) ) $filter['userId = ?'] = $userId;
		if ( !empty($dateFrom) ) $filter['createdTime >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['createdTime <= ?'] = $dateTo;
		return Webservice_Ticket::getAllWhere($filter, $extensions);
	}

	/**
	 * Returns all paid out tickets of the given host.
	 * @param integer $hostId Identifier of the host
	 * @param integer $userId null is all users
	 * @param string $dateFrom null is -infinity
	 * @param string $dateTo null is infinity
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getPaidOutTickets($hostId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
		$filter = array(
			'hostId = ?' => $hostId,
			'paidOut <> ?' => 0);
		if ( !empty($userId) ) $filter['userId = ?'] = $userId;
		if ( !empty($dateFrom) ) $filter['paidOutTime >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['paidOutTime <= ?'] = $dateTo;
		return Webservice_Ticket::getAllWhere($filter, $extensions);
	}

	/**
	 * Returns all collected tickets of the given host.
	 * @param integer $hostId Identifier of the host
	 * @param integer $userId null is all users
	 * @param string $dateFrom null is -infinity
	 * @param string $dateTo null is infinity
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getCollectedTickets($hostId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
		$filter = array(
			'collectionHostId = ?' => $hostId,
			'paidOut <> ?' => 0);
		if ( !empty($userId) ) $filter['userId = ?'] = $userId;
		if ( !empty($dateFrom) ) $filter['collectionTime >= ?'] = $dateFrom;
		if ( !empty($dateTo) ) $filter['collectionTime <= ?'] = $dateTo;
		return Webservice_Ticket::getAllWhere($filter, $extensions);
	}

	/**
	 * Returns all transactions related to the given host.
	 * @param integer $hostId identifier of the host
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAllTransactions($hostId, $extensions = null) {
		$filter = array(
			'hostId = ?' => $hostId,
			'accountType = ?' => Webservice_TransactionType::ACCOUNT_TYPE_HOST);
		return Webservice_Transaction::getAllWhereOrder($filter,array('time DESC','transactionId DESC'), $extensions);
	}

	/**
	 * Returns filtered transactions related to the given host.
	 * @param integer $hostId identifier of the host
	 * @param string $timeFrom null is -infinity
	 * @param string $timeTo null is infinity
	 * @param string $type name of the transaction type, null is all types
	 * @return Array array of the transaction struct.
	 */
	public static function getTransactions($hostId, $timeFrom = null, $timeTo = null, $type = null, $extensions = null) {

		$filter = array(
			'hostId = ?' => $hostId,
			'accountType = ?' => Webservice_TransactionType::ACCOUNT_TYPE_HOST
		);
		if ( !empty($timeFrom) ) $filter['time >= ?'] = $timeFrom;
		if ( !empty($timeTo) ) $filter['time <= ?'] = $timeTo;
		if ( !empty($type) ) {
			$typeId = Webservice_TransactionType::getByName($type);
			if ( empty($typeId) )
				throw new It6_XmlRpc_Exception("Unknown transaction type name: '$type'", 0, $e);

			$filter['typeId = ?'] = $typeId['transactionTypeId'];
		}

		return Webservice_Transaction::getAllWhereOrder($filter,array('time DESC','transactionId DESC'), $extensions);
	}

	/**
	 * Returns sum of filtered transactions related to the given host.
	 * @param integer $hostId identifier of the host. if is null all hosts are aggregated
	 * @param string $timeFrom null is -infinity
	 * @param string $timeTo null is infinity
	 * @param string $type name of the transaction type/s, null means all types
	 * @return float sum of transaction values.
	 */
	public static function getTransactionsSum($hostId, $timeFrom = null, $timeTo = null, $type = null, $branchId = null, $branchHandle = null) {

		try {
			$db = static::getMainDb();

			$select = $db->select()
				->from(
					array('ft' => Webservice_Transaction::$TABLE),
					array('sum' => 'Sum(value)'))
				->join(
					array('tt' => Webservice_TransactionType::$TABLE),
					'tt.id = ft.type_id',
					null)
				->join(
					array('h' => 'vic_admin.host'),
					'h.id = ft.host_id',
					null)
				->join(
					array('b' => 'vic_admin.branch'),
					'b.id = h.branch_id',
					null)
				->where('status = ?', Webservice_Transaction::STATUS_OK)
				->where('IF(ISNULL(ft.account_type),tt.account_type,ft.account_type) = ?',
					Webservice_TransactionType::ACCOUNT_TYPE_HOST);

			if ( !empty($hostId) )
				$select = $select->where('host_id = ?', $hostId);

			if ( !empty($branchId) )
				$select = $select->where('h.branch_id = ?', $branchId);

			if ( !empty($branchHandle) )
				$select = $select->where('b.handle = ?', $branchHandle);

			if ( !empty($type) ) {
				if ( !is_array($type) )
					$type = array($type);

				$typeIds = array();
				foreach ( $type as $tp ) {
					$typeId = Webservice_TransactionType::getByName($tp);
					if ( empty($typeId) )
						throw new Exception("Unknown transaction type name: '$tp'");
					$typeIds[] = $typeId['transactionTypeId'];
				}

				if ( !empty($typeIds) ) {
					$select->where('ft.type_id IN (?)', $typeIds);
				}
			}

			if ( !empty($timeFrom) )
				$select->where('okTime >= ?',$timeFrom);

			if ( !empty($timeTo) )
				$select->where('okTime < ?',$timeTo);

			$ret = $select->query()->fetch();

			return empty($ret['sum']) ? 0.0 : $ret['sum'];
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("getTransactionSum: '$type'", 0, $e);
		}
	}

	/**
	 * Returns aggregated transaction data per time and transaction type.
	 * @param integer $hostId identifier of the host. if is null all hosts are aggregated
	 * @param string $timeFrom null is -infinity
	 * @param string $timeTo null is infinity
	 * @return struct pairs of transaction type and value
	 */
	public static function getCashBook($hostId, $timeFrom = null, $timeTo = null) {
		try {
			$db = static::getMainDb();

			$select = $db->select()
				->from(
					array('ft' => Webservice_Transaction::$TABLE),
					array(
						'type' => 'tt.name',
						'sum' => 'Sum(value)',
					))
				->join(
					array('tt' => Webservice_TransactionType::$TABLE),
					'tt.id = ft.type_id',
					null)
				->group('tt.name')
				->where('status = ?', Webservice_Transaction::STATUS_OK)
				->where('IF(ISNULL(ft.account_type),tt.account_type,ft.account_type) = ?',
					Webservice_TransactionType::ACCOUNT_TYPE_HOST);

			if ( !empty($hostId) )
				$select = $select->where('host_id = ?', $hostId);

			if ( !empty($timeFrom) )
				$select->where('okTime >= ?',$timeFrom);

			if ( !empty($timeTo) )
				$select->where('okTime < ?',$timeTo);

			$query = $select->query();
			$ret = array();
			while ( $row = $query->fetchObject() )
				$ret[$row->type] = $row->sum;

			return $ret;
		}
		catch ( Exception $e ) {
			throw new It6_XmlRpc_Exception("getCashBook: '$hostId'", 0, $e);
		}
	}

	/**
	 *
	 * @return boolean
	 */
	public static function allBanned() {
		return 1 == Webservice_Parameter::getGlobalParameter(static::HOST_BAN_ALL);
	}

	/**
	 *
	 * @return boolean
	 */
	public static function allInBanned() {
		return 1 == Webservice_Parameter::getGlobalParameter(static::HOST_BAN_ALL_IN);
	}

	/**
	 *
	 * @return boolean
	 */
	public static function allOutBanned() {
		return 1 == Webservice_Parameter::getGlobalParameter(static::HOST_BAN_ALL_OUT);
	}

	/**
	 * Check if host is system host
	 * @param integer|NULL $hostId
	 * @return boolean
	 */
	public static function isSystem($hostId = null) {
		if ( empty($hostId) ) {
			$acl = Zend_Registry::get('acl');
			$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
		}
		return It6_Models_Host::isSystemId($hostId);
	}

	/**
	 * Test if is host allowed
	 * @param integer $hostId
	 * @return boolean
	 * @throws It6_XmlRpc_Exception
	 */
	public static function isAllowed($hostId = null) {

		$db = static::getDb();
		if ( empty($hostId) ) {
			$acl = Zend_Registry::get('acl');
			$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
		}

		if ( !self::isSystem($hostId)
				&& 1 == Webservice_Parameter::getGlobalParameter(static::HOST_BAN_ALL) ) {
			return false;
		}

		if ( !empty($hostId) ) {
			$host = $db->select()
				->from(array('hs' => Webservice_Host::$TABLE))
				->join(array('br' => Webservice_Branch::$TABLE),'branch_id = br.id',null)
				->columns(array('count' => 'count(*)'))
				->where('hs.id = ?', $hostId)
				->where('hs.banned IS NULL')
				->where('br.banned IS NULL')
				->query()->fetchObject();

			return 1 == $host->count;
		}

	}

	/**
	 * Test if is host has collection allowed
	 * @param integer $hostId
	 * @return boolean
	 * @throws It6_XmlRpc_Exception
	 */
	public static function isOutAllowed($hostId = null) {

		$db = static::getDb();
		if ( empty($hostId) ) {
			$acl = Zend_Registry::get('acl');
			$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
		}

		if ( !self::isSystem($hostId)
				&& 1 == Webservice_Parameter::getGlobalParameter(static::HOST_BAN_ALL_OUT) ) {
			return false;
		}

		if ( !empty($hostId) ) {
			$host = $db->select()
				->from(array('hs' => Webservice_Host::$TABLE))
				->columns(array('count' => 'count(*)'))
				->where('hs.id = ?', $hostId)
				->where('hs.out_allowed = 1')
				->query()->fetchObject();

			return 1 == $host->count;
		}
	}

	/**
	 * Test if is host has in allowed
	 * @param integer $hostId
	 * @return boolean
	 * @throws It6_XmlRpc_Exception
	 */
	public static function isInAllowed($hostId = null) {

		$db = static::getDb();
		if ( empty($hostId) ) {
			$acl = Zend_Registry::get('acl');
			$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
		}

		if ( !self::isSystem($hostId)
				&& 1 == Webservice_Parameter::getGlobalParameter(static::HOST_BAN_ALL_IN) ) {
			return false;
		}

		if ( !empty($hostId) ) {

			$host = $db->select()
				->from(array('hs' => Webservice_Host::$TABLE))
				->columns(array('count' => 'count(*)'))
				->where('hs.id = ?', $hostId)
				->where('hs.in_allowed = 1')
				->query()->fetchObject();

			return 1 == $host->count;
		}
	}

	private static function logHostAction($actionName, $result, $host, $adminId = null) {
		if (empty($adminId))
			$adminId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN);
		It6_Log::info(
			"Admin action was performed on host: $actionName",
			It6_Log::TAG_ADMIN_OPERATION,
			array('action' => $actionName, 'result' => $result, 'target' => $host, 'admin' => $adminId)
		 );
	}

	public static function ban($hostId) {
		$db = static::getDb();
		$res = $db->update(
			static::$TABLE,
			array('banned' => It6_Date::dbNow()),
			array(static::$IDENTITY . "= ?" => $hostId));
		static::logHostAction('ban', $res, $hostId);
	}

	public static function allow($hostId) {
		$db = static::getDb();
		$res = $db->update(
			static::$TABLE,
			array('banned' => NULL),
			array(static::$IDENTITY . "= ?" => $hostId));
		static::logHostAction('allow', $res, $hostId);
	}

	public static function banIn($hostId) {
		$db = static::getDb();
		$res = $db->update(
			static::$TABLE,
			array('in_allowed' => 0),
			array(static::$IDENTITY . "= ?" => $hostId));
		static::logHostAction('ban in', $res, $hostId);
	}

	public static function allowIn($hostId) {
		$db = static::getDb();
		$res = $db->update(
			static::$TABLE,
			array('in_allowed' => 1),
			array(static::$IDENTITY . "= ?" => $hostId));
		static::logHostAction('allow in', $res, $hostId);
	}

	public static function banOut($hostId) {
		$db = static::getDb();
		$res = $db->update(
			static::$TABLE,
			array('out_allowed' => 0),
			array(static::$IDENTITY . "= ?" => $hostId));
		static::logHostAction('ban out', $res, $hostId);
	}

	public static function allowOut($hostId) {
		$db = static::getDb();
		$res = $db->update(
			static::$TABLE,
			array('out_allowed' => 1),
			array(static::$IDENTITY . "= ?" => $hostId));
		static::logHostAction('allow out', $res, $hostId);
	}

	public static function clearFingerprint($hostId) {
		$db = static::getDb();
		$res = $db->update(
			static::$TABLE,
			array('fingerprint' => NULL),
			array(static::$IDENTITY . "= ?" => $hostId));
		static::logHostAction('clear fingerprint', $res, $hostId);
	}

	public static function banAll($cancel = false) {
		$res = Webservice_Parameter::setGlobalParameter(static::HOST_BAN_ALL, $cancel ? 0 : 1);
		static::logHostAction($cancel ? 'allow all' : 'ban all', $res, 'all');
	}

	public static function banAllIn($cancel = false) {
		$res = Webservice_Parameter::setGlobalParameter(static::HOST_BAN_ALL_IN, $cancel ? 0 : 1);
		static::logHostAction($cancel ? 'allow all in' : 'ban all in', $res, 'all');
	}

	public static function banAllOut($cancel = false) {
		$res = Webservice_Parameter::setGlobalParameter(static::HOST_BAN_ALL_OUT, $cancel ? 0 : 1);
		static::logHostAction($cancel ? 'allow all out' : 'ban all out', $res, 'all');
	}

	/**
	 * Accept deposit of branch
	 * @param integer $transactionId
	 * @return boolean true on success
	 */
	public static function deposit($transactionId) {
		$db = Webservice_Transaction::getDb();
		It6_DbTransaction::begin($db);
		try {
			$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);

			$transaction = Webservice_Transaction::getOneWhere(array(
				'transactionId = ?' => $transactionId,
				'hostId = ?' => $hostId,
				'status = ?' => Webservice_Transaction::STATUS_PRE_DEPOSIT,
				'typeName = ?' => Webservice_TransactionType::NAME_BRANCH_DEPOSIT));

			if ( empty($transaction) )
				throw new Exception('No such deposit transaction.');

			Webservice_Transaction::deposit($transactionId, true);

			It6_DbTransaction::commit($db);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Host::deposit.", 0, $e);
		}
	}

	/**
	 * Withdraw money from branch.
	 * @param integer $transactionId
	 * @return boolean true on success
	 */
	public static function withdraw($transactionId) {
		$db = Webservice_Transaction::getDb();
		It6_DbTransaction::begin($db);
		try {
			$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);

			$transaction = Webservice_Transaction::getOneWhere(array(
				'transactionId = ?' => $transactionId,
				'hostId = ?' => $hostId,
				'status = ?' => Webservice_Transaction::STATUS_PENDING,
				'typeName = ?' => Webservice_TransactionType::NAME_BRANCH_WITHDRAW));

			if ( empty($transaction) )
				throw new Exception('No such deposit transaction.');

			Webservice_Transaction::confirm($transactionId);

			It6_DbTransaction::commit($db);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Host::withdraw.", 0, $e);
		}
	}

	/**
	 * Withdrow money from branch, initialized by branch.
	 * @param float $value positive number
	 * @return boolean true on success
	 */
	public static function selfWithdraw($value) {
		$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);

		$transactionId = Webservice_Transaction::make(array(
				'value'      => -$value,
				'hostId'     => $hostId,
				'typeName'   => Webservice_TransactionType::NAME_BRANCH_WITHDRAW,
				'currencyId' => Webservice_Currency::getSystemId()
			));

		return static::withdraw($transactionId);
	}

	/**
	 * Gets all waiting host deposits
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getDeposits() {
		$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);
		return Webservice_Transaction::getAllWhere(array(
			'hostId = ?' => $hostId,
			'typeName = ?' => Webservice_TransactionType::NAME_BRANCH_DEPOSIT,
			'status = ?'=> Webservice_Transaction::STATUS_PRE_DEPOSIT
		));
	}

	/**
	 * Gets all waiting host withdraws
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getWithdraws() {
		$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);
		return Webservice_Transaction::getAllWhere(array(
			'hostId = ?' => $hostId,
			'typeName = ?' => Webservice_TransactionType::NAME_BRANCH_WITHDRAW,
			'status = ?' => Webservice_Transaction::STATUS_PENDING
		));
	}

	/**
	 * Gets hosts by branch
	 * @return string of hosts
	 */
	public static function getHostsByBranch($branchId = null, $branchHandle = null) {
		$array_of_hosts = array();

		if (!empty($branchId)) {
			$hosts = static::getByBranchId($branchId);
			foreach ($hosts as $host) {
				array_push($array_of_hosts, $host->hostId);
			}
		} elseif (!empty($branchHandle)) {
			$hosts = static::getByBranchHandle($branchHandle);
			foreach ($hosts as $host) {
				array_push($array_of_hosts, $host->hostId);
			}
		}

		return $array_of_hosts;
	}

	/**
	 *
	 * Return host balance in the given time
	 * @param integer $hostId or $branchId or $branchHandle
	 * @param string $time
	 * @return float
	 */
	public static function getBalance($hostId = null, $time, $eliminateCancelTransactions = false, $branchId = null, $branchHandle = null) {

		try {
			$db = static::getMainDb();

			$hosts = array();

			if (empty($hostId)) $hosts = static::getHostsByBranch($branchId, $branchHandle);
			else array_push($hosts, $hostId);

			$value = 0;
			foreach ($hosts as $host) {
				$ret = $db->select()
					->from(array('bl' => Webservice_Transaction::$BALANCE_LOG_TABLE), array('end_balance'))
					->join(
						array('ft' => Webservice_Transaction::$TABLE),
						'ft.transaction_id=bl.transaction_id AND ft.host_id='.intval($host),
						array()
					)
					->join(array('tt' => Webservice_TransactionType::$TABLE), 'tt.id = ft.type_id', array())
					->where('COALESCE(ft.account_type,tt.account_type) = ?', Webservice_TransactionType::ACCOUNT_TYPE_HOST)
					->where('bl.time<?', $time)
					->order(array('bl.time DESC', 'bl.transaction_id DESC'))
					->limit(1)
					->query()->fetch();

				if ( $eliminateCancelTransactions ) {

					$cancels = $db->select()
						->from(
							array('ft' => Webservice_Transaction::$TABLE),
							array('value' => 'Sum(ft.value)')
						)
						->join(
							array('ftc' => Webservice_Transaction::$TABLE),
							'ft.canceled_transaction_id = ftc.transaction_id',
							array())
						->join(
							array('tt' => Webservice_TransactionType::$TABLE),
							'tt.id = ftc.type_id',
							array())
						->where('COALESCE(ftc.account_type,tt.account_type) = ?', Webservice_TransactionType::ACCOUNT_TYPE_HOST)
						->where('ftc.host_id = ?', $host)
						->where('ft.okTime > ?', $time)
						->where('ftc.okTime < ?', $time)
						->limit(1)
						->query()->fetch();

					if ( empty($ret) ) $ret = array('end_balance' => 0.0);
					$ret['end_balance'] = (float)$ret['end_balance'] + (float)$cancels['value'];
				}
				$value += $ret['end_balance'];
			}
			return $value;

		} catch ( Exception $e) {
			throw new It6_XmlRpc_Exception("Host::getBalance.", 0, $e);
		}
	}

	/**
	 * Return balancing for LIVE betting
	 * @param string $dateFrom newer then (inclusive)
	 * @param string $dateTo older then (exclusive)
	 * @return struct balancing 
	*/
	public static function getLiveBalancing($branchId = null, $dateFrom = null, $dateTo = null, $dateToDb = false) {
		$db = static::getMainDb();
		$admindb = static::getAdminDb();
		$ret = array();

		if ( empty($dateFrom) )
			$dateFrom = '2000-01-01';

		if ( empty($dateTo) )
			$dateTo = It6_Date::dbNowAsDate();

		if (!$dateToDb) {
			$fnFixDate = function($d) {
				if (1 == preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $d, $matches)) {
					return It6_Date::timestampToDate(mktime(0,0,0,$matches[2],$matches[3],$matches[1]));
				} else {
					return $d;
				}
			};

			$dateFrom = $fnFixDate($dateFrom);
			$dateTo = $fnFixDate($dateTo);
			list($dateFrom, $dateTo) = It6_Date::dateToDbInterval($dateFrom, $dateTo);
		}

		// naber
		$select = $db->select()
			->from(array("t" => "ticket_live"), array(
				'count' => 'Sum(1)',
				'amount' => 'Sum(stake)',
			))
			->where('t.time_created > ?', $dateFrom)
			->where('t.time_created < ?', $dateTo);

		if (!empty($branchId)) {
			$select = $select->join(array('U' => 'uzivatel'), 'U.user_id = t.user_id', null)
				->where('U.datum_registrace > DATE_ADD(CURDATE(), INTERVAL -1 YEAR)')
				->where('U.branch_id = ?', $branchId);
		}
		$tmp = $select->query()->fetch();
		$ret = $tmp;

		//stornovane
		$select = $db->select()
			->from(array("t" => "ticket_live"), array(
				'countCancel'	=> 'Sum(IF(time_canceled IS NOT NULL, 1, 0))',
				'amountCancel' 	=> 'Sum(IF(time_canceled IS NOT NULL, stake, 0))',
			))
			->where('t.time_created > ?', $dateFrom)
			->where('t.time_created < ?', $dateTo);

		if (!empty($branchId)) {
			$select = $select->join(array('U' => 'uzivatel'), 'U.user_id = t.user_id', null)
				->where('U.datum_registrace > DATE_ADD(CURDATE(), INTERVAL -1 YEAR)')
				->where('U.branch_id = ?', $branchId);
		}
		$tmp = $select->query()->fetch();
		$ret["countCancel"] = $tmp["countCancel"];
		$ret["amountCancel"] = $tmp["amountCancel"];

		//vyplata LIVE tiketu
		$select = $db->select()
			->from(array("t" => "ticket_live"), array(
				'countPaid' => 'Sum(1)',
				'amountPaid' => 'Sum(win)',
			))
			->where('t.time_paid > ?', $dateFrom)
			->where('t.time_paid < ?', $dateTo)
			->where('t.is_loss = 0')
			->where('t.status = ?', 'paid');

		if (!empty($branchId)) {
			$select = $select->join(array('U' => 'uzivatel'), 'U.user_id = t.user_id', null)
				->where('U.datum_registrace > DATE_ADD(CURDATE(), INTERVAL -1 YEAR)')
				->where('U.branch_id = ?', $branchId);
		}
		$tmp = $select->query()->fetch();
		$ret["countPaid"] = $tmp["countPaid"];
		$ret["amountPaid"] = $tmp["amountPaid"];

		//pocet aktivnich uzivatelu
		$select = $db->select()
			->from(array("U" => "uzivatel"), array(
				"activeUsers" => "Count(distinct T.user_id)" 
			))
			->join(array("T" => "ticket_live"), 'U.user_id = T.user_id',null)
			->where('T.time_paid > ?', $dateFrom)
			->where('T.time_paid < ?', $dateTo);

		if (!empty($branchId)) {
			$select = $select->where('U.branch_id = ?', $branchId);
		}
		$tmp = $select->query()->fetch();
		$ret["activeUsers"] = $tmp["activeUsers"];

		foreach ($ret as $key => $value) {
			if ($value == null) {
				$ret[$key] = 0;
			}
		}
		
		return $ret;
	}

	/**
	 * Returns balancing per gicen period.
	 * @param integer $hostId identifier of the host. If is null it returns aggregation for all hosts.
	 * @param string $dateFrom newer then (inclusive)
	 * @param string $dateTo older then (exclusive)
	 * @return struct balancing
	 */
	public static function getBalancing($hostId, $dateFrom = null, $dateTo = null, $branchId = null, $branchHandle = null, $internetHostProfit = false, $calculation = false) {
		//provize pobocky za tikety z internetu bude filtrovana dle pobocky a ne dle hosta 
		if ($internetHostProfit && !is_null($hostId))
			$hostId = It6_Models_Host::ID_INTERNET;

		$db = static::getMainDb();
		$admindb = static::getAdminDb();
		$ret = array();

		if ( !empty($hostId) )
			$host = static::getById($hostId);

		if ( empty($dateFrom) )
			$dateFrom = '2000-01-01';

		if ( empty($dateTo) )
			$dateTo = It6_Date::dbNowAsDate();

		$fnFixDate = function($d) {
			if (1 == preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $d, $matches)) {
				return It6_Date::timestampToDate(mktime(0,0,0,$matches[2],$matches[3],$matches[1]));
			} else {
				return $d;
			}
		};
		$dateFrom = $fnFixDate($dateFrom);
		$dateTo = $fnFixDate($dateTo);
		list($dateFrom, $dateTo) = It6_Date::dateToDbInterval($dateFrom, $dateTo);

		// Počáteční vklad
		if ( !empty($hostId) || !empty($branchId) || !empty($branchHandle))
			$ret['startBalance'] = static::getBalance($hostId, $dateFrom, true, $branchId, $branchHandle);

		//Počet tiketů
		$select = $db->select()
		->from( 
			array('T' => Webservice_Ticket::$TABLE),
			array(
				'count'              => 'Sum(IF(point_type_id IS NULL,1,0))',
				'countPoints'        => 'Sum(IF(point_type_id IS NULL,0,1))',
				'amount'             => 'Sum(IF(point_type_id IS NULL,castka,0))',
				'amountPoints'       => 'Sum(IF(point_type_id IS NULL,0,castka))',
				'countCancel'        => 'Sum(IF(point_type_id IS NULL AND zruseno=1,1,0))',
				'countCancelPoints'  => 'Sum(IF(point_type_id IS NOT NULL AND zruseno=1,1,0))',
				'amountCancel'       => 'Sum(IF(point_type_id IS NULL AND zruseno=1,castka,0))',
				'amountCancelPoints' => 'Sum(IF(point_type_id IS NOT NULL AND zruseno=1,castka,0))',
				'mp'                 => 'Sum(Ceil(castka * T.mp))'
			)
		)
		->join(array('H' => 'vic_admin.host'), 'T.host_id = H.id',null)
		->join(array('B' => 'vic_admin.branch'), 'H.branch_id = B.id',null)
		->where('zalozen >= ?', $dateFrom)
		->where('zalozen < ?', $dateTo);

		if ($internetHostProfit == true) {
			$select = $select->join(array('U' => Webservice_User::$TABLE), 'T.user_id = U.user_id', null)
						->where('U.branch_id = ?', $branchId)
						->where('T.host_id = ?', It6_Models_Host::ID_INTERNET);
		} else {
			if ( !empty($hostId) )
				$select = $select->where('host_id = ?', $hostId);

			if ( !empty($branchId) )
				$select = $select->where('H.branch_id = ?', $branchId);

			if ( !empty($branchHandle) )
				$select = $select->where('B.handle = ?', $branchHandle);
		};

		$tmp = $select->query()->fetch();

		$ret['ticketCount'] = intval($tmp['count']);
		$ret['ticketCountPoints'] = intval($tmp['countPoints']);
		$ret['ticketAmount'] = floatval($tmp['amount']);
		$ret['ticketAmountPoints'] = floatVal($tmp['amountPoints']);
		$ret['stornoTicketCount'] = intval($tmp['countCancel']);
		$ret['stornoTicketCountPoints'] = intval($tmp['countCancelPoints']);
		$ret['stornoTicketAmount'] = empty($tmp['amountCancel']) ? 0.0 : floatval($tmp['amountCancel']);
		$ret['stornoTicketAmountPoints'] = empty($tmp['amountCancelPoints']) ? 0.0 : floatval($tmp['amountCancelPoints']);
		$ret['mp'] = floatval($tmp['mp']);

		//Počty a sumy tiketu pro Vyučtování 
		if ($calculation == true) {
			$select = $db->select()
			->from( 
				array('T' => Webservice_Ticket::$TABLE),
				array(
					'countCalculation'				=> 'Sum(IF(point_type_id IS NULL,1,0))',
					'countPointsCalculation'		=> 'Sum(IF(point_type_id IS NULL,0,1))',
					'amountCalculation'				=> 'Sum(IF(point_type_id IS NULL,castka,0))',
					'amountPointsCalculation'		=> 'Sum(IF(point_type_id IS NULL,0,castka))',
					'paidCalculation'				=> 'Sum(IF(is_loss = 0,(win_real-mp_win_amount), 0))',
					'countPaidCalculation'			=> 'Sum(IF(is_loss = 0, 1, 0))',
					'mpCalculation'					=> 'Sum(IF(is_loss = 0,mp_win_amount,0))'
				)
			)
			->join(array('H' => 'vic_admin.host'), 'T.host_id = H.id',null)
			->join(array('B' => 'vic_admin.branch'), 'H.branch_id = B.id',null)
			->where('vyplacen_date >= ?', $dateFrom)
			->where('vyplacen_date < ?', $dateTo);

			if ($internetHostProfit == true) {
				$select = $select->join(array('U' => Webservice_User::$TABLE), 'T.user_id = U.user_id', null)
					->where('U.branch_id = ?', $branchId)
					->where('U.datum_registrace > DATE_ADD(CURDATE(), INTERVAL -1 YEAR)')
					->where('T.host_id = ?', It6_Models_Host::ID_INTERNET);
			} else {
				if ( !empty($hostId) )
					$select = $select->where('host_id = ?', $hostId);
				if ( !empty($branchId) )
					$select = $select->where('H.branch_id = ?', $branchId);
				if ( !empty($branchHandle) )
					$select = $select->where('B.handle = ?', $branchHandle);
			};

			$tmp = $select->query()->fetch();

			$ret["countCalculation"] 		= $tmp["countCalculation"];
			$ret["countPointsCalculation"]	= $tmp["countPointsCalculation"];
			$ret["amountCalculation"] 		= $tmp["amountCalculation"];
			$ret["amountPointsCalculation"] = $tmp["amountPointsCalculation"];
			$ret["paidCalculation"]			= $tmp["paidCalculation"];
			$ret["countPaidCalculation"]	= $tmp["countPaidCalculation"];
			$ret["mpCalculation"]			= $tmp["mpCalculation"];
		}

		//Výplata tiketů
		//roundMp
		$select = $db->select()
		->from(
			array('T' => Webservice_Ticket::$TABLE),
			array(
				'count'  => 'Count(*)',
				'amount' => 'Sum(win_real-mp_win_amount)',
				'mp'     => 'Sum(mp_win_amount)'
			)
		)
		->join(array('H' => 'vic_admin.host'), 'T.host_id = H.id', null)
		->join(array('B' => 'vic_admin.branch'), 'H.branch_id = B.id', null)
		->where('vyplacen = 1')
		->where('cash = 1')
		->where('is_loss = 0')
		->where('collection_time IS NOT NULL')
		->where('collection_time >= ?', $dateFrom)
		->where('collection_time < ?', $dateTo);

		if ($internetHostProfit == true) {
			$select = $select->join(array('U' => Webservice_User::$TABLE), 'T.user_id = U.user_id', null)
				->where('U.branch_id = ?', $branchId)
				->where('T.host_id = ?', It6_Models_Host::ID_INTERNET);
		} else {
			if ( !empty($hostId) )
				$select = $select->where('host_id = ?', $hostId);
			if ( !empty($branchId) )
				$select = $select->where('branch_id = ?', $branchId);
			if ( !empty($branchHandle) )
				$select = $select->where('B.handle = ?', $branchHandle);
		}

		$tmp = $select->query()->fetch();
		$ret['collectTicketCount'] = (float)$tmp['count'];
		$ret['collectTicketAmount'] = empty($tmp['amount']) ? 0.0 : (float)$tmp['amount'];
		$ret['winMp'] = empty($tmp['mp']) ? 0.0 : (float)$tmp['mp'];

		//MP
		$ret['mp'] += (float)$tmp['mp'];

		//Výhry pouze vyplacené (ne vybrané)
		$select = $db->select()
			->from(
				array('T' => Webservice_Ticket::$TABLE),
				array(
					'count'		=> 'Count(*)',
					'amount'	=> 'Sum(win_real-mp_win_amount)',
					'mp'		=> 'Sum(mp_win_amount)'
				)
			)
			->join(array('H' => 'vic_admin.host'), 'T.host_id = H.id', null)
			->join(array('B' => 'vic_admin.branch'), 'H.branch_id = B.id', null)
			->where('vyplacen = 1')
			->where('cash = 1')
			->where('is_loss = 0')
			->where('vyplacen_date IS NOT NULL')
			->where('vyplacen_date >= ?', $dateFrom)
			->where('vyplacen_date < ?', $dateTo);

		if ($internetHostProfit == true) {
			$select = $select->join(array('U' => Webservice_User::$TABLE), 'T.user_id = U.user_id', null)
				->where('U.branch_id = ?', $branchId)
				->where('T.host_id = ?', It6_Models_Host::ID_INTERNET);
		} else {
			if ( !empty($hostId) )
				$select = $select->where('host_id = ?', $hostId);
			if ( !empty($branchId) )
				$select = $select->where('branch_id = ?', $branchId);
			if ( !empty($branchHandle) )
				$select = $select->where('B.handle = ?', $branchHandle);
		}

		$tmp_real = $select->query()->fetch();
		$ret['collectTicketCountReal'] = (float)$tmp_real['count'];
		$ret['collectTicketAmountReal'] = empty($tmp_real['amount']) ? 0.0 : (float)$tmp_real['amount'];
		$ret['winMpReal'] = empty($tmp_real['mp']) ? 0.0 : (float)$tmp_real['mp'];

		if ( empty($hostId) || $hostId == It6_Models_Host::ID_INTERNET ) {
			$select = $db->select()
			->from(
				array('T' => Webservice_Ticket::$TABLE),
				array(
					'count'  => 'Count(*)',
					'amount' => 'Sum(win_real-mp_win_amount)',
					'mp'     => 'Sum(mp_win_amount)',
				)
			)
			->join(array('H' => 'vic_admin.host'), 'T.host_id = H.id', null)
			->join(array('B' => 'vic_admin.branch'), 'H.branch_id = B.id', null)
			->where('vyplacen = 1')
			->where('cash = 0')
			->where('is_loss = 0')
			->where('vyplacen_date >= ?', $dateFrom)
			->where('vyplacen_date < ?', $dateTo);
			if ($internetHostProfit == true) {
				$select = $select->join(array('U' => Webservice_User::$TABLE), 'T.user_id = U.user_id', null)
						->where('U.branch_id = ?', $branchId)
						->where('T.host_id = ?', It6_Models_Host::ID_INTERNET);
			} else {
				if ( !empty($hostId) )
					$select = $select->where('host_id = ?', $hostId);
				if ( !empty($branchId) )
					$select = $select->where('branch_id = ?', $branchId);
				if ( !empty($branchHandle) )
					$select = $select->where('B.handle = ?', $branchHandle);
			}

			$tmp = $select->query()->fetch();
			$ret['collectTicketCount'] += (float)$tmp['count'];
			$ret['collectTicketAmount'] += empty($tmp['amount']) ? 0.0 : (float)$tmp['amount'];
			$ret['winMp'] += empty($tmp['mp']) ? 0.0 : (float)$tmp['mp'];
			// real
			$ret['collectTicketCountReal'] += (float)$tmp['count'];
			$ret['collectTicketAmountReal'] += empty($tmp['amount']) ? 0.0 : (float)$tmp['amount'];
			$ret['winMpReal'] += empty($tmp['mp']) ? 0.0 : (float)$tmp['mp'];
		}

		// Dotace
		$ret['deposits'] = static::getTransactionsSum(
			$hostId, $dateFrom, $dateTo, Webservice_TransactionType::NAME_BRANCH_DEPOSIT, $branchId, $branchHandle
		);

		// Odvody
		$ret['withdraws'] = static::getTransactionsSum(
			$hostId, $dateFrom, $dateTo, Webservice_TransactionType::NAME_BRANCH_WITHDRAW, $branchId, $branchHandle
		);

		// Stav pokladny
		if ( !empty($hostId) || !empty($branchId) || !empty($branchHandle) ) {
			$ret['endBalance'] = static::getBalance($hostId, $dateTo, true, $branchId, $branchHandle);
		}

		//Počet VP tiketů
		//Not implemented

		if ( !empty($hostId) && $internetHostProfit == false) {
			// Výplata cizích
			$select = $db->select()
				->from(
					array('T' => Webservice_Ticket::$TABLE),
					array(
						'count' => 'Count(*)',
						'amount' => 'Sum(win_real-mp_win_amount)'
					)
				)
				->join(array('H' => 'vic_admin.host'), 'T.host_id = H.id', null)
				->join(array('B' => 'vic_admin.branch'), 'H.branch_id = B.id', null)
				->where('vyplacen = 1')
				->where('cash = 1')
				->where('is_loss = 0')
				->where('collection_host_id = ?', $hostId)
				->where('collection_time IS NOT NULL')
				->where('collection_time >= ?', $dateFrom)
				->where('collection_time < ?', $dateTo);

			if ( !empty($hostId) ) $select = $select->where('host_id <> ?', $hostId);
			if ( !empty($branchId) ) $select = $select->where('branch_id <> ?', $branchId);
			if ( !empty($branchHandle) ) $select = $select->where('B.handle <> ?', $branchHandle);

			$tmp = $select->query()->fetch();
			$ret['collectForeignTicketCount'] = (float)$tmp['count'];
			$ret['collectForeignTicketAmount'] = empty($tmp['amount']) ? 0.0 : (float)$tmp['amount'];

			// Výplata mých jinde
			$select = $db->select()
				->from(
					array('T' => Webservice_Ticket::$TABLE),
					array(
						'count' => 'Count(*)',
						'amount' => 'Sum(win_real-mp_win_amount)'
					)
				)
				->join(array('H' => 'vic_admin.host'), 'T.host_id = H.id', null)
				->join(array('B' => 'vic_admin.branch'), 'H.branch_id = B.id', null)
				->where('host_id = ?', $hostId)
				->where('vyplacen = 1')
				->where('cash = 1')
				->where('is_loss = 0')
				->where('collection_host_id <> ?', $hostId)
				->where('collection_time IS NOT NULL')
				->where('collection_time >= ?', $dateFrom)
				->where('collection_time < ?', $dateTo);

			if ( !empty($hostId) ) $select = $select->where('host_id = ?', $hostId);
			if ( !empty($branchId) ) $select = $select->where('branch_id = ?', $branchId);
			if ( !empty($branchHandle) ) $select = $select->where('B.handle = ?', $branchHandle);

			$tmp = $select->query()->fetch();
			$ret['collectElsewhereTicketCount'] = (float)$tmp['count'];
			$ret['collectElsewhereTicketAmount'] = empty($tmp['amount']) ? 0.0 : (float)$tmp['amount'];
		}

		//Výběr za definovaným účelem
		//NOT IMPLEMENTED

		// Vklad na konto klienta
		$ret['userDeposits'] = static::getTransactionsSum(
			$hostId, $dateFrom, 
			$dateTo,
			array(
				Webservice_TransactionType::NAME_BRANCH_USER_DEPOSIT_CASH,
				Webservice_TransactionType::NAME_BRANCH_USER_DEPOSIT_CASH_TICKET_WIN,
				Webservice_TransactionType::NAME_USER_DEPOSIT_BANK,
				Webservice_TransactionType::NAME_USER_DEPOSIT_CARD,
				Webservice_TransactionType::NAME_USER_DEPOSIT_MANUAL,
			),
			$branchId,
			$branchHandle
		);

		// Výběr z konta klienta
		$ret['userWithdraws'] = static::getTransactionsSum(
			$hostId, 
			$dateFrom,
			$dateTo,
			array(
				Webservice_TransactionType::NAME_BRANCH_USER_WITHDRAW_CASH,
				Webservice_TransactionType::NAME_USER_WITHDRAW_BANK,
				Webservice_TransactionType::NAME_USER_WITHDRAW_MANUAL,
			),
			$branchId,
			$branchHandle
		);

		// Registrovani uzivatele
		if ( empty($hostId) || $hostId == It6_Models_Host::ID_INTERNET ) {
			$tmp = $db->select()
				->from(
					Webservice_User::$TABLE,
					array('count'  => 'Count(*)')
				)
				->where('datum_registrace >= ?', $dateFrom)
				->where('datum_registrace < ?', $dateTo)
				->query()->fetch();
			$ret['registeredUsers'] = intval($tmp['count']);
		}

		// Aktivovani uzivatele
		if ($internetHostProfit == false) {
			$select = $db->select()
				->from(
					array('U' => Webservice_User::$TABLE),
					array('count'  => 'Count(*)')
				)
				->join(array('B' => 'vic_admin.branch'), 'B.id = U.branch_id', null)
				->join(array('H' => 'vic_admin.host'), 'H.branch_id = B.id', null)
				->where('datum_aktivace >= ?', $dateFrom)
				->where('datum_aktivace < ?', $dateTo)
				->where('anonymous = 0');

			if ( !empty($hostId) ) $select = $select->where('H.id = ?', $hostId);
			if ( !empty($branchId) ) $select = $select->where('B.id = ?', $branchId);
			if ( !empty($branchHandle) ) $select = $select->where('B.handle = ?', $branchHandle);

			$tmp = $select->query()->fetch();
			$ret['activatedUsers'] = $tmp['count'];
		}

		// Aktivni uzivatele
		$select = $db->select()
			->from(
				array('U' => Webservice_User::$TABLE),
				array('count'  => 'Count(DISTINCT U.user_id)')
			)
			->join(array('T' => 'ticket'), 'T.user_id = U.user_id', null)
			->join(array('B' => 'vic_admin.branch'), 'B.id = U.branch_id', null)
			->join(array('H' => 'vic_admin.host'), 'H.branch_id = B.id', null)
			->group('U.user_id')
			->where('zalozen >= ?', $dateFrom)
			->where('zalozen < ?', $dateTo)
			->where('anonymous = 0');
		
		if ($internetHostProfit == true) {
			$select = $select->where('U.branch_id = ?', $branchId)
				->where('T.host_id = ?', It6_Models_Host::ID_INTERNET);
		} else {

			if ( !empty($hostId) ) $select = $select->where('H.id = ?', $hostId);
			if ( !empty($branchId) ) $select = $select->where('B.id = ?', $branchId);
			if ( !empty($branchHandle) ) $select = $select->where('B.handle = ?', $branchHandle);
		}
		$tmp = $db->query("SELECT Count(*) AS count FROM (".$select->assemble().") X LIMIT 1")->fetch();
		$ret['activeUsers'] = $tmp['count'];

		// zustatek
		$select = $db->select()
			->from(
				array('U' => Webservice_User::$TABLE),
				array(
					'suma' => 'I.zustatek',
				)
			)
			->join(array('T' => 'ticket'), 'T.user_id = U.user_id', null)
			->join(array('I' => 'uzivatel_im_data'), 'I.user_id = U.user_id', null)
			->join(array('B' => 'vic_admin.branch'), 'U.branch_id = B.id', null)
			->where('zalozen >= ?', $dateFrom)
			->where('zalozen < ?', $dateTo)
			->where('anonymous = 0')
			->group('I.user_id');//bez tohoto to vracelo n nasobek zustatku uzivatele dle mnozstvi tiketu uzivatele
			if ( !empty($hostId) ) $select = $select->where('T.host_id = ?', $hostId);
			if ( !empty($branchId) ) $select = $select->where('U.branch_id = ?', $branchId);
			if ( !empty($branchHandle) ) $select = $select->where('B.handle = ?', $branchHandle);
		$tmp = $select->query()->fetchAll(); 

		$sumAccountUsers = 0;

		foreach ($tmp as $sumTmp) {
			$sumAccountUsers += $sumTmp["suma"];
		}

		$ret['acountUsers'] = $sumAccountUsers;//number_format($sumAccountUsers, '2', '.', ' ');

		// Vyhernost
		if ( $ret['ticketAmount'] == 0 ) {
			$ret['winRatioCashflow'] = 0.00;
		} else {
			$ret['winRatioCashflow'] = round((($ret['ticketAmount'] - $ret['collectTicketAmount'])/$ret['ticketAmount']), '4');
		}

		return $ret;
	}

	/**
	 * Returns deposit and withdraw on the way (order for transfer was made).
	 * @param integer $hostId
	 * @return struct Fields:
	 *   <ul>
	 *   <li>deposit</li>
	 *   <li>withdraw</li>
	 *   </ul>
	 */
	public static function getResourcesOnTheWay($hostId) {
		$onTheWay = static::getMainDb()->select()
			->from(
				array('ft' => Webservice_Transaction::$TABLE),
				array(
					'sumDeposit' => "SUM(IF(tt.name='" . Webservice_TransactionType::NAME_BRANCH_DEPOSIT . "',value, 0))",
					'sumWithdraw' => "SUM(IF(tt.name='" . Webservice_TransactionType::NAME_BRANCH_WITHDRAW . "',-value, 0))",
				)
			)
			->join(
				array('tt' => Webservice_TransactionType::$TABLE),
				'tt.id = ft.type_id',
				null)
			->where('status IN (?)', array(Webservice_Transaction::STATUS_PENDING, Webservice_Transaction::STATUS_PRE_DEPOSIT))
			->where('tt.name IN (?)', array(Webservice_TransactionType::NAME_BRANCH_DEPOSIT, Webservice_TransactionType::NAME_BRANCH_WITHDRAW))
			->where('host_id = ?', $hostId)
			->query()
			->fetch();
		return array(
			'deposit' => (empty($onTheWay['sumDeposit']) ? 0.0 : floatval($onTheWay['sumDeposit'])),
			'withdraw' => (empty($onTheWay['sumWithdraw']) ? 0.0 : floatval($onTheWay['sumWithdraw'])),
		);
	}

	/**
	 * Find hosts by hostId, branchId or branchHandle.
	 * @return array of struct host structure
	 * @param integer $hostId or $branchId or $branchHandle identifier if the host
	 * @see Entities_Host
	 */
	public static function getByHostOrBranch($hostId, $branchId = null, $branchHandle = null) {
		$hosts_array = array();

		if (empty($hostId) && (isset($branchId) || isset($branchHandle))) {
			$db = static::getDb();
			$select = $db->select()
			->from(
				array('H' => Webservice_Host::$TABLE),
				array('hostId' => 'id')
			);

			if (!empty($branchId)) $select = $select->where('branch_id = ?', $branchId);

			if (!empty($branchHandle)) {
				$select = $select
					->join(
						array('B' => Webservice_Branch::$TABLE),
						'B.id = H.branch_id',
						null
					)
					->where('B.handle = ?', $branchHandle);
			}

			$tmp = $select->query()->fetchAll();

			foreach ($tmp as $value) {
				array_push($hosts_array, parent::getById($value['hostId']));
			}

			return $hosts_array;
		} else {
			array_push($hosts_array, parent::getById($hostId));
			return $hosts_array;
		}
	}

	/**
	 * Returns Sum InOut.
	 * @param integer $hostId or $branchId or $branchHandle identifier of the host.
	 * @return struct sum inout
	 */
	public static function getInOut($hostId, $branchId = null, $branchHandle = null, $hostData = null) {
		$balance = 0;
		$userWithdrawsCash = 0;
		$winTicketsCash = 0;
		$newWinTicketsCash = 0;
		$sources = 0;
		$onTheWayDeposit = 0;
		$onTheWayWithdraw = 0;
		$recommendedDeposit = 0;
		$recommendedWithdraw = 0;
		$reserve = 0;
		$minDeposit = 0;
		$minWithdraw = 0;
		$winTicketsCount = 0;
		$newWinTicketsCount = 0;

		if (!is_null($hostData))
			$hosts = array($hostData);
		else
			$hosts = static::getByHostOrBranch($hostId, $branchId, $branchHandle);

		foreach ($hosts as $host) {
			$sources_one = 0;
			$reserve_one = 0;
			$minDeposit_one = 0;
			$minWithdraw_one = 0;

			$db = static::getMainDb();
			$winTickets = $db->select()
				->from(
					array('t' => Webservice_Ticket::$TABLE),
					array(
						'winTicketsCount' => 'Count(*)',
						'winTicketsCash'  => 'Sum(win_real-mp_win_amount)',
						'newWinTicketsCount' => 'Sum(IF(vyplacen_date > DATE_SUB(NOW(), INTERVAL 24 HOUR),1,0))',
						'newWinTicketsCash'  => 'Sum(IF(vyplacen_date > DATE_SUB(NOW(), INTERVAL 24 HOUR),win_real-mp_win_amount,0))',
					)
				)
				->where('vyplacen = 1')
				->where('forfeit IS NULL')
				->where('is_loss = 0')
				->where('collection_time IS NULL')
				->where('cash <> 0')
				->where('host_id = ?', $host->hostId);

			$winTickets = $winTickets->query()->fetch();
			$onTheWay = static::getResourcesOnTheWay($host->hostId);

			$balance += $host->balance;
			$userWithdrawsCash += static::getNotWithdrawedCash($host->hostId);
			$winTicketsCash += $winTickets['winTicketsCash'];
			$winTicketsCount += $winTickets['winTicketsCount'];
			$newWinTicketsCash += $winTickets['newWinTicketsCash'];
			$newWinTicketsCount += $winTickets['newWinTicketsCount'];

			$sources_one = $host->balance - static::getNotWithdrawedCash($host->hostId) - $winTickets['winTicketsCash'] + $onTheWay['deposit'] - $onTheWay['withdraw'];
			$sources += $sources_one;
			$onTheWayDeposit += $onTheWay['deposit'];
			$onTheWayWithdraw += $onTheWay['withdraw'];

			$reserve_one = Webservice_Parameter::getBranchParameter(static::PARAMETER_BRANCH_FUND_RESERVE, $host->branchId);
			$reserve += $reserve_one;

			$minDeposit_one = Webservice_Parameter::getBranchParameter(static::PARAMETER_BRANCH_MINIMAL_DEPOSIT, $host->branchId);
			$minDeposit += $minDeposit_one;

			$minWithdraw_one = Webservice_Parameter::getBranchParameter(static::PARAMETER_BRANCH_MINIMAL_WITHDRAW, $host->branchId);
			$minWithdraw += $minWithdraw_one;

			$recommendation = $sources_one - $reserve_one;

			if ( $recommendation < -$minDeposit_one && $onTheWay['withdraw'] == 0.0) {
				$recommendedDeposit += round(-$recommendation);
			}
			if ( $recommendation > $minWithdraw_one ) {
				$recommendedWithdraw += round($recommendation);
			}
		}

		return array(
			'balance' => $balance,
			'userWithdrawsCash' => $userWithdrawsCash,
			'winTicketsCash' => $winTicketsCash,
			'sources' => $sources,
			'onTheWayDeposit' => $onTheWayDeposit,
			'onTheWayWithdraw' => $onTheWayWithdraw,
			'recommendedDeposit' => $recommendedDeposit,
			'recommendedWithdraw' => $recommendedWithdraw,
			'reserve' => $reserve,
			'minDeposit' => $minDeposit,
			'minWithdraw' => $minWithdraw,
			'winTicketsCount' => $winTicketsCount,
			'newWinTicketsCount' => $newWinTicketsCount,
			'newWinTicketsCash' => $newWinTicketsCash,
			'totalCollect' => $userWithdrawsCash + $winTicketsCash,

			'branchHandle' => isset($host->branchHandle) ? $host->branchHandle : 0,
			'branchName' => isset($host->branchName) ? $host->branchName : 0,
			'hostName' => isset($host->hostName) ? $host->hostName : 0,
			'branchIsActive' => isset($host->branchIsActive) ? $host->branchIsActive : 0
		);
	}

	/**
	 * Returns array of all in outs.
	 */
	public static function getAllInOuts($extensions) {
		$hsExts = array();
		$balanceSum = 0;

		foreach ( $extensions as $ext ) {
			if ( 'Filter' == $ext['class'] ) {
				$hsExt = $ext;
				$hs = false;
				foreach ( $ext['params']['filter'] as $k => $_ ) {
					if ( !is_numeric($k) ) continue;
					$hs = true;
				}
				if ( $hs ) $hsExts[] = $hsExt;
			}
		}

		$hosts = static::getAll($hsExts);

		$ret = array();
		$recommendedDepositSum = 0;
		foreach ( $hosts as $host ) {
			$ret[] = static::getInOut($host['hostId'], null, null, $host);
		}

		$ret = It6_ArrayWrapper::toNativeArray($ret);

		foreach ($ret as $r) {
			foreach ($r as $key => $value) {
				if ($key == 'balance') static::$balanceSum += intval($value);
				if ($key == 'onTheWayDeposit') static::$onTheWayDepositSum += intval($value);
				if ($key == 'onTheWayWithdraw') static::$onTheWayWithdrawSum += intval($value);
				if ($key == 'winTicketsCash') static::$winTicketsCashSum += intval($value);
				if ($key == 'userWithdrawsCash') static::$userWithdrawsCashSum += intval($value);
				if ($key == 'totalCollect') static::$totalCollectSum += intval($value);
				if ($key == 'recommendedWithdraw') static::$recommendedWithdrawSum += intval($value);
				if ($key == 'recommendedDeposit') static::$recommendedDepositSum += intval($value);
			}
		}

		foreach ( $extensions as $ext ) {
			if ( 'Order' == $ext['class'] and isset($ext['params']['order'][0]) ) {

				$order = $ext['params']['order'][0];
				if ( is_array($order) ) $order = $order[0];

				$_ = explode(' ',$order);
				$key = $_[0];
				if ( !empty($_[1]) && $_[1] == 'DESC' ) $desc = true;
				else $desc = false;
			}
		}

		if ( empty($key) ) $key = 'branchHandle';
		if ( !isset($desc) ) $desc = false;

		usort($ret, function ($a, $b) use ($key, $desc) {
			if ($desc) {
				$_ = $b;
				$b = $a;
				$a = $_;
			}
			return (is_numeric($a[$key]) && is_numeric($b[$key])) ? $a[$key] > $b[$key] : strcmp($a[$key],$b[$key]);
		});

		return $ret;
	}

	/**
	 * Returns amount of the money that host shold payout on user withdrawals.
	 * @param integer $hostId unique identifdier of the host
	 * @return float
	 */
	public static function getNotWithdrawedCash($hostId) {
		$db = Webservice_Transaction::getDb();
		$ret = $db->select()
			->from(
				array('ft' => Webservice_Transaction::$TABLE),
				array('sum' => 'Sum(-value)'))
			->join(
				array('tt' => Webservice_TransactionType::$TABLE),
				'tt.id = ft.type_id',
				null)
			->where('host_id = ?', $hostId)
			->where('status = ?', Webservice_Transaction::STATUS_PRE_DEPOSIT)
			->where('tt.name = ?', Webservice_TransactionType::NAME_USER_WITHDRAW_CASH)
			->query()->fetchObject();
		return (empty($ret->sum) ? 0.0 : $ret->sum);
	}

	/**
	 * Update fingerprint of host identified by SSL certificate from current connection.
	 * @param string $fingerprint GUID of host (enclosed in curly braces or not)
	 */
	public static function setFingerprint($fingerprint) {
		$hostId = Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_HOST);
		$res = 0;
		$fingerprint = trim($fingerprint, '{}');
		if (!empty($fingerprint) && !empty($hostId)) {
			try {
				$res = static::getAdminDb()->update(
					static::$TABLE,
					array('fingerprint' => $fingerprint),
					array('id=?' => $hostId)
				);
				$res = 1;
			}
			catch (Exception $e) {
				$res = 0;
			}
		}
		if (empty($res))
			throw new It6_XmlRpc_Exception('Host fingerprint not updated.');
	}

	/**
	 * Returns array of all sums.
	 */
	public static function getAllSums() {
		$ret[] =  array(
			'branchHandle' => null,
			'branchName' => null,
			'hostName' => null,
			'balanceSum' => static::$balanceSum,
			'onTheWayDepositSum' => static::$onTheWayDepositSum,
			'onTheWayWithdrawSum' => static::$onTheWayWithdrawSum,
			'winTicketsCashSum' => static::$winTicketsCashSum,
			'userWithdrawsCashSum' => static::$userWithdrawsCashSum,
			'totalCollectSum' => static::$userWithdrawsCashSum + static::$winTicketsCashSum,
			'recommendedWithdrawSum' => static::$recommendedWithdrawSum,
			'recommendedDepositSum' => static::$recommendedDepositSum,
		);
		return $ret;
	}

	/**nasledujici metody vznikly kvuli optimalizaci seekce cash overview. prozatim nedodrzuji DRY koncept

	/**
	 * Returns Sum InOut.
	 * @param integer $hostId or $branchId or $branchHandle identifier of the host.
	 * @return struct sum inout
	 */
	public static function getInOutAll($hosts) {
		$balance = 0;
		$userWithdrawsCash = 0;
		$winTicketsCash = 0;
		$newWinTicketsCash = 0;
		$sources = 0;
		$onTheWayDeposit = 0;
		$onTheWayWithdraw = 0;
		$recommendedDeposit = 0;
		$recommendedWithdraw = 0;
		$reserve = 0;
		$minDeposit = 0;
		$minWithdraw = 0;
		$winTicketsCount = 0;
		$newWinTicketsCount = 0;

		$host_ids = $branch_ids = array();

		foreach ($hosts as $host) {
			$host_ids[] = $host["hostId"];
			$branch_ids[] = $host["branchId"];
		}


			$sources_one = 0;
			$reserve_one = 0;
			$minDeposit_one = 0;
			$minWithdraw_one = 0;

			$db = Zend_Registry::get("db");
			$winTicketsRes = $db->select()
				->from(
					array('t' => Webservice_Ticket::$TABLE),
					array(
						'hostId' => 'host_id',
						'winTicketsCount' => 'Count(*)',
						'winTicketsCash'  => 'Sum(win_real-mp_win_amount)',
					)
				)
				->where('vyplacen = 1')
				->where('forfeit IS NULL')
				->where('is_loss = 0')
				->where('collection_time IS NULL')
				->where('cash <> 0')
				->where('host_id IN (?)', $host_ids)
				->group('host_id')
				->query()->fetchAll();

			$winTicketsAll = array();

			foreach ($winTicketsRes as $winTicket) {
				$winTicketsAll[$winTicket["hostId"]] = $winTicket;
			}
			
			$onTheWayRes = $db->select()
			->from(
				array('ft' => Webservice_Transaction::$TABLE),
				array(
					'hostId' => 'host_id',
					'deposit' => "SUM(IF(tt.name='" . Webservice_TransactionType::NAME_BRANCH_DEPOSIT . "',value, 0))",
					'withdraw' => "SUM(IF(tt.name='" . Webservice_TransactionType::NAME_BRANCH_WITHDRAW . "',-value, 0))",
				)
			)
			->join(
				array('tt' => Webservice_TransactionType::$TABLE),
				'tt.id = ft.type_id',
				null)
			->where('status IN (?)', array(Webservice_Transaction::STATUS_PENDING, Webservice_Transaction::STATUS_PRE_DEPOSIT))
			->where('tt.name IN (?)', array(Webservice_TransactionType::NAME_BRANCH_DEPOSIT, Webservice_TransactionType::NAME_BRANCH_WITHDRAW))
			->where('host_id IN (?)', $host_ids)
			->group('host_id')
			->query()
			->fetchAll();

			$onTheWayAll = array();


			foreach ($onTheWayRes as $onTheWayHost) {
				$onTheWayAll[$onTheWayHost["hostId"]] = $onTheWayHost;
			}


		$userWithdrawsCashRes = $db->select()
			->from(
				array('ft' => Webservice_Transaction::$TABLE),
				array(
					'hostId' => "host_id",
					'sum' => 'Sum(-value)'
					))
			->join(
				array('tt' => Webservice_TransactionType::$TABLE),
				'tt.id = ft.type_id',
				null)
			->where('status = ?', Webservice_Transaction::STATUS_PRE_DEPOSIT)
			->where('tt.name = ?', Webservice_TransactionType::NAME_USER_WITHDRAW_CASH)
			->where('host_id IN (?)', $host_ids)
			->group('host_id')
			->query()->fetchAll();

			$userWithdrawsCashAll = array();

			foreach ($userWithdrawsCashRes as $userWithdrawCash) {
				$userWithdrawsCashAll[$userWithdrawCash["hostId"]] = $userWithdrawCash;
			}

			$branchParameters = Webservice_Parameter::getBranchParameters(array(
				Webservice_Host::PARAMETER_BRANCH_FUND_RESERVE,
				Webservice_Host::PARAMETER_BRANCH_MINIMAL_DEPOSIT,
				Webservice_Host::PARAMETER_BRANCH_MINIMAL_WITHDRAW
				), $branch_ids);

			$parameters = array();

			foreach ($branchParameters as $parameter) {
				if (isset($parameter["branch_id"]))
					$parameters[$parameter["branch_id"]][$parameter["name"]] = $parameter["oValue"];

				if ($parameter["name"] == Webservice_Host::PARAMETER_BRANCH_FUND_RESERVE)
					$fundReserveDefault = $parameter["value"];
				elseif ($parameter["name"] == Webservice_Host::PARAMETER_BRANCH_MINIMAL_DEPOSIT)
					$minimalDepositDefault = $parameter["value"];
				elseif ($parameter["name"] == Webservice_Host::PARAMETER_BRANCH_MINIMAL_WITHDRAW)
					$minimalwithdrawDefault = $parameter["value"];
			}



		foreach ($hosts as $host) {

			$recommendedDeposit = 0;
			$recommendedWithdraw = 0;

			$winTicketsCash = (isset($winTicketsAll[$host->hostId])) ? $winTicketsAll[$host->hostId]['winTicketsCash'] : 0;
			$winTicketsCount = (isset($winTicketsAll[$host->hostId])) ? $winTicketsAll[$host->hostId]['winTicketsCount'] : 0;

			$userWithdrawsCash = (isset($userWithdrawsCashAll[$host->hostId])) ? $userWithdrawsCashAll[$host->hostId]["sum"] : 0;

			$onTheWay = (isset($onTheWayAll[$host->hostId])) ? $onTheWayAll[$host->hostId] : array("deposit" => 0, "withdraw" => 0);

			$sources_one = $host->balance - $userWithdrawsCash - $winTicketsCash + $onTheWay['deposit'] - $onTheWay['withdraw'];
			$sources = $sources_one;
			$onTheWayDeposit = $onTheWay['deposit'];
			$onTheWayWithdraw = $onTheWay['withdraw'];

			$reserve_one = (isset($parameters[$host->branchId][Webservice_Host::PARAMETER_BRANCH_FUND_RESERVE])) ? 
							$parameters[$host->branchId][Webservice_Host::PARAMETER_BRANCH_FUND_RESERVE] :
							$fundReserveDefault;
			$reserve += $reserve_one;

			$minDeposit_one = (isset($parameters[$host->branchId][Webservice_Host::PARAMETER_BRANCH_MINIMAL_DEPOSIT])) ? 
							$parameters[$host->branchId][Webservice_Host::PARAMETER_BRANCH_MINIMAL_DEPOSIT] :
							$minimalDepositDefault;
			$minDeposit += $minDeposit_one;

			$minWithdraw_one = (isset($parameters[$host->branchId][Webservice_Host::PARAMETER_BRANCH_MINIMAL_WITHDRAW])) ? 
							$parameters[$host->branchId][Webservice_Host::PARAMETER_BRANCH_MINIMAL_WITHDRAW] :
							$minimalDepositDefault;
			$minWithdraw += $minWithdraw_one;

			$recommendation = $sources_one - $reserve_one;

			if ( $recommendation < -$minDeposit_one && $onTheWay['withdraw'] == 0.0) {
				$recommendedDeposit += round(-$recommendation);
			}
			if ( $recommendation > $minWithdraw_one ) {
				$recommendedWithdraw += round($recommendation);
			}

			$ret[] = array(
				'balance' => $host->balance,
				'userWithdrawsCash' => $userWithdrawsCash,
				'winTicketsCash' => $winTicketsCash,
				'onTheWayDeposit' => $onTheWayDeposit,
				'onTheWayWithdraw' => $onTheWayWithdraw,
				'recommendedDeposit' => $recommendedDeposit,
				'recommendedWithdraw' => $recommendedWithdraw,
				'totalCollect' => $userWithdrawsCash + $winTicketsCash,
				'branchHandle' => isset($host->branchHandle) ? $host->branchHandle : 0,
				'branchName' => isset($host->branchName) ? $host->branchName : 0,
				'hostName' => isset($host->hostName) ? $host->hostName : 0,
			);
		}

		return $ret;
	}

		/**
	 * Returns array of all in outs.
	 */
	public static function getAllInOutsAll($extensions) {
		$hsExts = array();
		$balanceSum = 0;

		foreach ( $extensions as $ext ) {
			if ( 'Filter' == $ext['class'] ) {
				$hsExt = $ext;
				$hs = false;
				foreach ( $ext['params']['filter'] as $k => $_ ) {
					if ( !is_numeric($k) ) continue;
					$hs = true;
				}
				if ( $hs ) $hsExts[] = $hsExt;
			}
		}

		$hosts = Webservice_Host::getAll($hsExts);

		$ret = array();
		$recommendedDepositSum = 0;
		$ret = self::getInOutAll($hosts);

		$ret = It6_ArrayWrapper::toNativeArray($ret);

		foreach ($ret as $r) {
			foreach ($r as $key => $value) {
				if ($key == 'balance') static::$balanceSum += intval($value);
				if ($key == 'onTheWayDeposit') static::$onTheWayDepositSum += intval($value);
				if ($key == 'onTheWayWithdraw') static::$onTheWayWithdrawSum += intval($value);
				if ($key == 'winTicketsCash') static::$winTicketsCashSum += intval($value);
				if ($key == 'userWithdrawsCash') static::$userWithdrawsCashSum += intval($value);
				if ($key == 'totalCollect') static::$totalCollectSum += intval($value);
				if ($key == 'recommendedWithdraw') static::$recommendedWithdrawSum += intval($value);
				if ($key == 'recommendedDeposit') static::$recommendedDepositSum += intval($value);
			}
		}

		foreach ( $extensions as $ext ) {
			if ( 'Order' == $ext['class'] and isset($ext['params']['order'][0]) ) {

				$order = $ext['params']['order'][0];
				if ( is_array($order) ) $order = $order[0];

				$_ = explode(' ',$order);
				$key = $_[0];
				if ( !empty($_[1]) && $_[1] == 'DESC' ) $desc = true;
				else $desc = false;
			}
		}

		if ( empty($key) ) $key = 'branchHandle';
		if ( !isset($desc) ) $desc = false;

		usort($ret, function ($a, $b) use ($key, $desc) {
			if ($desc) {
				$_ = $b;
				$b = $a;
				$a = $_;
			}
			return (is_numeric($a[$key]) && is_numeric($b[$key])) ? $a[$key] > $b[$key] : strcmp($a[$key],$b[$key]);
		});

		return $ret;
	}

}