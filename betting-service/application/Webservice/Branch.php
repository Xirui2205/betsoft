<?php
	/**
	 * Branch related static methods. Internet is special case of branch.
	 * @author Pavel Klinger
	 * @see Entities_Branch
	 */
	class Webservice_Branch extends Webservice_AbstractWebService {
	
		public static $TABLE						= "branch";
		public static $TABLE_PREFIX					= "br";
		public static $TABLE_BRANCH_PARAM			= "branch_param";
		public static $TABLE_BRANCH_HAS_PARAMETER	= "branch_has_parameter";
		public static $TABLE_BRANCH_OPENING_HOURS	= "branch_opening_hours";
		public static $TABLE_BRANCH_HAS_BANK_ACC		= "branch_has_bank_account";
		public static $TABLE_BRANCH_HAS_BANK_ACC_PREFIX	= "bba";
		public static $TABLE_BANK_ACC					= "bank_account";
		public static $TABLE_BANK_ACC_PREFIX			= "ba";
		public static $IDENTITY						= "id";
		public static $ENTITY_NAME 					= "Entities_Branch";
		public static $BRANCH_CALCULATION_TYPE		= "branch_calculation";
		public static $BRANCH_NET_CALCULATION_TYPE	= "branch_net_calculation";
	
		protected static $CONV = array(
			'br.id'				=> 'branchId',
			'br.name'			=> 'name',
			'handle'			=> 'handle',
			'ticket_header'		=> 'ticketHeader',
			'type_id'			=> 'typeId',
			'br.place'		=> 'place',
			'street'			=> 'street',
			'town'				=> 'town',
			'zip'				=> 'zip',
			'branch_location_id'=> 'branchLocationId',
			'email'				=> 'email',
			'phone'				=> 'phone',
			'provider_name'		=> 'providerName',
			'provider_address'	=> 'providerAddress',
			'provider_ic'		=> 'providerIc',
			'provider_dic'		=> 'providerDic',
			'provider_email'	=> 'providerEmail',
			'status_id'			=> 'statusId',
			'currency_id'		=> 'currencyId',
			'banned'			=> 'banned',
			'is_listed'			=> 'isListed',
			'is_testing'		=> 'isTesting',
			'is_active'			=> 'isActive',
			'mp'				=> 'mp',
			'mp_win'			=> 'mpWin',
			'note'				=> 'note',
			'info'				=> 'info',
			'is_top'			=> 'isTop',
			'br.longitude'		=> 'longitude',
			'br.latitude'		=> 'latitude',
			'bl.name'			=> 'locationName',
			'bt.name'			=> 'typeName',
			'bs.name'			=> 'statusName',
		
			'ct.name'			=> 'contractName',
			'ba.account_number'	=> 'accountNumber',
			'ba.account_prefix'	=> 'accountPrefix',
			'ba.bank_code'		=> 'bankCode',
			'br.stem_id'		=> 'stemId',
			'br.calculation'	=> 'calculation',
			'br.calculation_net' 		=> 'calculationNet',
			'br.calculation_net_type' 	=> 'calculationNetType',
			'br.correspondence_address'	=> 'correspondenceAddress'
		);
	
		const ID_INTERNET = It6_Models_Branch::ID_INTERNET;
	
		protected static function getDb() {
			return static::getAdminDb();
		}
	
		public static function toEntity($dbArray, $columns = null) {
			foreach (array('mp', 'mpWin') as $key) {
				if (array_key_exists($key, $dbArray))
					$dbArray[$key] = floatval($dbArray[$key]);
			}
	
			if(!empty($dbArray['branchId']))
				$dbArray['openingHours'] = static::getOpeningHoursByBranchId($dbArray['branchId']);
			return parent::toEntity($dbArray, $columns);
		}
	
		/**
		 * Returns all branches in the system.
		 * $param array $columns array of culumns requested
		 * @return array array of the branch structures
		 * @see Entities_Branch
		 */
		public static function getAllColumns($columns, $extensions = null) {
			//return $columns;
			return parent::getAllColumns($columns, $extensions);
		}
	
		/**
		 * Returns all branches in the system that are allowed to withdraw from. Excluding branch Internet.
		 * Implementation note: must be consistent with isWithdrawEnabled().
		 * @return array array of the branch structures
		 * @see Entities_Branch
		 */
		public static function getAllWithdrawEnabled() {
			$parName = 'branch.branchWithdraw';
			$parId = 5; //obfuscation
				try {
				$ret = static::getDb()->select()
					->from(
						array('b' => Webservice_Branch::$TABLE))
					->joinLeft(
						array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
						"bp.branch_id = b.".Webservice_Branch::$IDENTITY." AND bp.parameter_id= $parId",
						null)
					->joinLeft(
						array('p' => Webservice_Parameter::$TABLE),
						"bp.parameter_id = p.".Webservice_Branch::$IDENTITY." AND p.name = '$parName' AND p.is_branch = 1",
						null)
					->where('b.type_id <> 1')
					// three days before banning will take effect, branch cannot provide withdraws
					->where('b.banned IS NULL OR DATE_SUB(b.banned, INTERVAL 3 DAY)>=?', It6_Date::dbNowAsDate())
					->columns(array(
						'branchId' => 'b.'.Webservice_Branch::$IDENTITY,
						'name' => 'b.name',
						'value' => 'bp.value',
						))
					->order(array('(TRIM(b.town)) ASC', '(TRIM(b.street)) ASC'))
					->query()->fetchAll();
	
				$gVal = Webservice_Parameter::getGlobalParameter($parName);
	
				$availBranches = array();
				foreach ($ret as $r) {
					if ((($r['value']===NULL) || ($r['value']===1)) && ($gVal == 1)) {
						$availBranches[] = $r;
					}
				}
				return $availBranches;
			}
			catch (Exception $e) {
				throw new It6_XmlRpc_Exception($e);
			}
		}
	
		/**
		 * Determines if withdrawals are enabled for given branch (branch can be given by host)
		 * Implementation note: must be consistent with getAllWithdrawEnabled().
		 * @param integer $id Branch ID or host ID
		 * @param boolean $isHostId TRUE if given ID is host ID, otherwise ID is branch ID (default)
		 * @return boolean TRUE if withrawals are enabled
		 * @throws It6_XmlRpc_Exception
		 */
		public static function isWithdrawEnabled($id, $isHostId = false) {
			$parName = 'branch.branchWithdraw';
			try {
				$select = static::getDb()->select()
					->from(array('b' => Webservice_Branch::$TABLE), array())
					->joinLeft(
						array('p' => Webservice_Parameter::$TABLE),
						"p.name='$parName' AND p.is_branch=1",
						array('gpValue' => 'value')
					)
					->joinLeft(
						array('bp' => Webservice_Branch::$TABLE_BRANCH_HAS_PARAMETER),
						"bp.parameter_id=p.id AND bp.branch_id=b.id",
						array('bpValue' => 'value')
					)
					->where('b.type_id <> 1')
					// three days before banning will take effect, branch cannot provide withdraws
					->where('b.banned IS NULL OR DATE_SUB(b.banned, INTERVAL 3 DAY)>=?', It6_Date::dbNowAsDate());
				if ($isHostId) {
					$select->join(
						array('h' => 'host'),
						'h.branch_id=b.id',
						array()
					)
					->where('h.id=?', $id);
				}
				else {
					$select->where('b.id=?', $id);
				}
				
				$rows = $select->query()->fetchAll();
				if (empty($rows)) {
					return false;
				}
				if (empty($rows[0]['gpValue'])) {
					return false;
				}
				return (!isset($rows[0]['bpValue']) || $rows[0]['bpValue']);
			}
			catch (Exception $e) {
				throw new It6_XmlRpc_Exception($e);
			}
		}
	
		protected static function defaultJoins($query) {
			$query = parent::defaultJoins($query);
	
			$query
				->join(
					array(Webservice_BranchLocation::$TABLE_PREFIX => Webservice_BranchLocation::$TABLE),
					self::$TABLE_PREFIX.".branch_location_id = ".Webservice_BranchLocation::$TABLE_PREFIX.".id",
					null
				)
				->join(
					array(Webservice_BranchType::$TABLE_PREFIX => Webservice_BranchType::$TABLE),
					self::$TABLE_PREFIX .".type_id = ".Webservice_BranchType::$TABLE_PREFIX .".id",
					null
				)
				->join(
					array(Webservice_BranchStatus::$TABLE_PREFIX => Webservice_BranchStatus::$TABLE),
					self::$TABLE_PREFIX.".status_id = ".Webservice_BranchStatus::$TABLE_PREFIX.".id",
					null
				)
				->joinLeft(
					array(Webservice_Contract::$TABLE_PREFIX => Webservice_Contract::$TABLE),
					self::$TABLE_PREFIX.".id = ".Webservice_Contract::$TABLE_PREFIX.".branch_id
					AND ".Webservice_Contract::$TABLE_PREFIX.".date_canceled IS NULL
					AND ".Webservice_Contract::$TABLE_PREFIX.".date_signed IS NOT NULL
					AND ".Webservice_Contract::$TABLE_PREFIX.".date_valid_from < '".It6_Date::dbNow()."'
					AND ".Webservice_Contract::$TABLE_PREFIX.".date_valid_to >= '".It6_Date::dbNow()."'",
					null
				)
				->joinLeft(
					array(Webservice_Contract::$TABLE_CONTRACT_TEMPLATE_PREFIX => Webservice_Contract::$TABLE_CONTRACT_TEMPLATE),
					Webservice_Contract::$TABLE_CONTRACT_TEMPLATE_PREFIX.".id = ".Webservice_Contract::$TABLE_PREFIX.".template_id",
					null
				)
				->joinLeft(
					array(self::$TABLE_BRANCH_HAS_BANK_ACC_PREFIX => self::$TABLE_BRANCH_HAS_BANK_ACC),
					self::$TABLE_BRANCH_HAS_BANK_ACC_PREFIX.'.branch_id = '.self::$TABLE_PREFIX.'.id
					AND '.self::$TABLE_BRANCH_HAS_BANK_ACC_PREFIX.'.is_current = 1
					AND '.self::$TABLE_BRANCH_HAS_BANK_ACC_PREFIX.'.bank_account_type = ' . Webservice_BankAccountType::TYPE_BALANCE,
					null
				)
				->joinLeft(
					array(self::$TABLE_BANK_ACC_PREFIX => self::$TABLE_BANK_ACC),
					self::$TABLE_BANK_ACC_PREFIX.'.account_id = '.self::$TABLE_BRANCH_HAS_BANK_ACC_PREFIX.'.bank_account_id',
					null
				);
	
			return $query;
		}
	
		/**
		 * Find branch by given identifier.
		 * @param integer $branchId identifier of the branch
		 * @return struct branch structure
		 * @see Entities_Branch
		 */
		public static function getById($branchId, $extensions = null) {
			return parent::getById($branchId, $extensions);
		}

		/**
		 * Find branch name by given identifier.
		 * @param integer $branchId or $branchHandle identifier of the branch
		 * @return struct branch name
		 * @see Entities_Branch
		 */
		public static function getNameByIdOrHandle($branchId = null, $branchHandle = null, $extensions = null) {
			if (!empty($branchId)) $where = array('branchId = ?' => $branchId);
			if (!empty($branchHandle)) $where = array('handle = ?' => $branchHandle);

			return parent::getOneWhereColumns($where, array('name'));
		}

	    /**
		 * Find all branches.
		 * @param $extensions array of all extensions
		 * @return struct branch structure
		 * @see Entities_Branch
		 */
		public static function getAll($extensions=null) {
			return parent::getAll($extensions);
		}
	
		/**
		 * Find branch employees.
		 * @param integer $branchId identifier of the branch
		 * @param boolean $secondaryBranch if admins linked by secondary branch should be queried too, default is FALSE
		 * @return array array of admin structures
		 * @see Entities_Admin
		 */
		public static function getEmployees($branchId, $secondaryBranch = false) {
			if ($secondaryBranch) {
				$adminIds = Webservice_Admin::getIdsFromBranchIncludingSecondary($branchId);
				if (empty($adminIds))
					return array();
				$where['adminId IN (?)'] = $adminIds;
			}
			else
				$where = array('branchId = ?' => $branchId);
			return Webservice_Admin::getAllWhere($where);
		}
	
		/**
		 * Return id of branch based on its handle
		 * @param string $handle handle of the branch
		 * @return integer branch identifier
		 * @see Entities_Branch
		 */
		public static function getIdByHandle($branchHandle) {
			if (!isset($branchHandle)) 
				throw new Exception('Invalid branchHandle');

			$ret = static::getDb()->select()
					->from(
						array('b' => Webservice_Branch::$TABLE))
					->columns(array(
						'branchId' => 'b.'.Webservice_Branch::$IDENTITY,
						))
					->where('b.handle = ?', $branchHandle)
					->query()->fetch();

			return $ret["branchId"];
		}
	
	
	
		/**
		 * Insert new branch. Value of the branch identifier is ignored and new
		 * is generated.
		 * @param struct $branch structure of the branch
		 * @return integer|bool branch identifier of the created branch or false on error
		 * @see Entities_Branch
		 */
		public static function insert($branch) {
			$branchId = parent::insert($branch);
			if ($branchId)
				It6_GlobalCache_Invalidator::Branch_update($branchId);
			return $branchId;
		}
	
	
	
		/**
		 * Update branch.
		 * @param struct $branch structure of the branch
		 * @return true on success
		 * @see Entities_Branch
		 */
		public static function update($branch) {
			if (It6_ArrayWrapper::keyExists('banned', $branch) && empty($branch['banned']))
				$branch['banned'] = new Zend_Db_Expr('NULL');
			if(empty($branch['latitude']))
				$branch['latitude'] = new Zend_Db_Expr('NULL');
			if(empty($branch['longitude']))
				$branch['longitude'] = new Zend_Db_Expr('NULL');
	
			$res = parent::update($branch);
			It6_GlobalCache_Invalidator::Branch_update($branch['branchId']);
			return $res;
		}
	
	
	
		/**
		 * Delete branch.
		 * @param struct $branchId idenetifier of the branch.
		 * @return true on success
		 * @see Entities_Branch
		 */
		public static function delete($branchId) {
	
			//FIXME parametry by mely bejt smaany take.
			$res = parent::delete($branchId);
			It6_GlobalCache_Invalidator::Branch_update($branchId);
			return $res;
		}
	
		/**
		 * Returns all hosts in the given branch.
		 * @param integer $branchId identifier of the branch
		 * @return array array of host structs
		 * @see Entities_Host
		 */
		public static function getAllHosts($branchId) {
			//TODO implement me
			throw new It6_XmlRpc_Exception("Unimplemented");
		}
	
	
	
		/** Returns all tickets of the branch.
		 * @param integer $branchId Identifier of the branch
		 * @param integer $userId null is all users
		 * @param string $dateFrom null is -infinity
		 * @param string $dateTo null is infinity
		 * @return array array of the ticket structures
		 * @see Entities_Ticket
		 */
		public static function getAllTickets($branchId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
			$filter = array('branchId = ?' => $branchId);
			if ( !empty($userId) ) $filter['userId = ?'] = $userId;
			if ( !empty($dateFrom) ) $filter['createdTime >= ?'] = $dateFrom;
			if ( !empty($dateTo) ) $filter['createdTime <= ?'] = $dateTo;
			return Webservice_Ticket::getAllWhere($filter, $extensions);
		}
	
	
	
		/**
		 * Returns all opened (not canceled and not payed off) tickets of the branch.
		 * @param integer $branchId Identifier of the branch
		 * @param integer $userId null is all users
		 * @param string $dateFrom null is -infinity
		 * @param string $dateTo null is infinity
	
		 * @return array array of the ticket structures
		 * @see Entities_Ticket
		 */
		public static function getOpenedTickets($branchId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
			$filter = array(
				'branchId = ?' => $branchId,
				'canceled = ?' => 0,
				'paidOut = ?' => 0);
			if ( !empty($userId) ) $filter['userId = ?'] = $userId;
			if ( !empty($dateFrom) ) $filter['createdTime >= ?'] = $dateFrom;
			if ( !empty($dateTo) ) $filter['createdTime <= ?'] = $dateTo;
			return Webservice_Ticket::getAllWhere($filter, $extensions);
		}
	
	
	
		/**
		 * Returns all canceled tickets of the given branch.
		 * @param integer $branchId Identifier of the branch
		 * @param integer $userId null is all users
		 * @param string $dateFrom null is -infinity
		 * @param string $dateTo null is infinity
		 * @return array array of the ticket structures
		 * @see Entities_Ticket
		 */
		public static function getCanceledTickets($branchId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
			$filter = array(
				'branchId = ?' => $branchId,
				'canceled <> ?' => 0);
			if ( !empty($userId) ) $filter['userId = ?'] = $userId;
			if ( !empty($dateFrom) ) $filter['createdTime >= ?'] = $dateFrom;
			if ( !empty($dateTo) ) $filter['createdTime <= ?'] = $dateTo;
			return Webservice_Ticket::getAllWhere($filter, $extensions);
		}
	
	
		/**
		 * Returns all paid out tickets of the given branch.
		 * @param integer $branchId Identifier of the branch
		 * @param integer $userId null is all users
		 * @param string $dateFrom null is -infinity
		 * @param string $dateTo null is infinity
		 * @return array array of the ticket structures
		 * @see Entities_Ticket
		 */
		public static function getPaidOutTickets($branchId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
			$filter = array(
				'branchId = ?' => $branchId,
				'paidOut <> ?' => 0);
			if ( !empty($userId) ) $filter['userId = ?'] = $userId;
			if ( !empty($dateFrom) ) $filter['paidOutTime >= ?'] = $dateFrom;
			if ( !empty($dateTo) ) $filter['paidOutTime <= ?'] = $dateTo;
			return Webservice_Ticket::getAllWhere($filter, $extensions);
		}
	
		/**
		 * Returns all collected tickets of the given branch.
		 * @param integer $branchId Identifier of the branch
		 * @param integer $userId null is all users
		 * @param string $dateFrom null is -infinity
		 * @param string $dateTo null is infinity
		 * @return array array of the ticket structures
		 * @see Entities_Ticket
		 */
		public static function getCollectedTickets($branchId, $userId = null, $dateFrom = null, $dateTo = null, $extensions = null) {
			$filter = array(
				'collectionBranchId = ?' => $branchId,
				'paidOut <> ?' => 0);
			if ( !empty($userId) ) $filter['userId = ?'] = $userId;
			if ( !empty($dateFrom) ) $filter['collectionTime >= ?'] = $dateFrom;
			if ( !empty($dateTo) ) $filter['collectionTime <= ?'] = $dateTo;
			return Webservice_Ticket::getAllWhere($filter, $extensions);
		}
	
	
	
		/**
		 * Returns count of tickets per branch in given peeriod
		 * @param string $from if null -infinity is taken
		 * @param string $to if null infinity is taken
		 * @return struct
		 */
		public static function getTicketsCount($from = null , $to = null) {
			$db = static::getDb();
			$select = $db->select()
				->from(static::$TABLE, null)
				->joinLeft(
					Webservice_Host::$TABLE,
					Webservice_Host::$TABLE . '.branch_id = ' . static::$TABLE . '.id',
					null)
				->joinLeft(
					'vic_main.'. Webservice_Ticket::$TABLE,
					Webservice_Ticket::$TABLE . '.host_id = ' . Webservice_Host::$TABLE . '.id',
					null)
				->group(static::$TABLE . '.id')
				->columns(array(
						'branchId' => static::$TABLE . '.id',
				 		'name'    => static::$TABLE . '.name',
						'count'   => "COUNT('ticket_id')"
					))
				->order('count DESC');
	
			if ( !empty($from) ) {
				$select = $select->where('zalozen > ?', $from);
			}
	
			if ( !empty($to) ) {
				$select = $select->where('zalozen < ?', $to);
			}
	
			return $select->query()->fetchAll();
		}

	/**
	 * Returns branch provisions.
	 * @return array provisions
	 */
	public static function getProvisions($extensions = array()) {
		//$filtersToDrop = array('calculation', 'active','templateId', 'branchValidFromFrom', 'branchValidFromTo', 'branchValidToFrom', 'branchValidToTo', 'stemId', 'branchHandle', 'branchId');
		$filter = array();
		$brExts = array();

		/* 
		foreach ( $extensions as $ext ) {
			if ( 'Filter' == $ext['class'] ) {
				$brExt = $ext;
				$br = false;
				foreach ( $ext['params']['filter'] as $k => $_ ) {
					if ( !is_numeric($k) ) continue;
					if ( empty($_['?']['handle']) ) {
						unset($brExt['params']['filter'][$k]);
						if ( !empty($_['?']['dateFrom']) ) {
							$dateFrom = $_['?']['dateFrom'];
						} else if ( !empty($_['?']['dateTo']) ) {
							$dateTo = $_['?']['dateTo'];
						} else if (!empty($_['?']['active'])) {
							$_['?']['active'] = ($_['?']['active'] == y) ? 1 : 0;
						}
						$filter_item = $_['?']; //isset($_['?']) ? $_['?'] : '';
						if (is_array($filter_item) && in_array(key($filter_item), $filtersToDrop)) {
							$filter[key($filter_item)] = current($filter_item);
						}
					} else {
						$br = true;
					}
				}
				if ( $br ) {
					$brExts[] = $brExt;
				}
			}
		}
		*/

		$noIncludeToFilter = array('>', '?');
		$activeContract = null;
        $_filter = 'branch.id IS NOT NULL';
		foreach ( $extensions as $ext ) {
			if ( 'Filter' == $ext['class'] ) {
				foreach ( $ext['params']['filter'] as $key => $value ) {
					if ( !is_numeric($key) ) continue;
					foreach ($value as $key2 => $value2) {
   						if ( $key2 == "?" ) {
            				foreach ($value2 as $key3 => $value3) {
            					//echo "key:".$key3." value:".$value3."<br/>";
            					switch ($key3) {
            						case in_array($key3, $noIncludeToFilter):
            							break;

            						case 'dateFrom':
									    $dateFrom = $value3;
									    break;

            						case 'dateTo':
									    $dateTo = $value3;
									    break;

            						case 'templateId':
									    $_filter .= ' AND c.template_id = ' . $value3 . ' AND c.date_canceled IS NULL AND c.date_valid_to >= CURDATE()';
									    break;

            						case 'active':
	            						$activeContract = ($value3 == "y") ? 1 : 0;
            							break;
            						
            						case 'is_active':
									    $_filter .= ($value3 == "y") ? ' AND branch.is_active = 1' : ' AND branch.is_active = 0';
            							break;

            						case 'calculation':
									    $_filter .= ($value3 == "y") ? ' AND branch.calculation = 1' : '';
            							break;

            						case 'calculation_net':
									    $_filter .= ($value3 == "y") ? ' AND branch.calculation_net = 1' : '';
            							break;

            						case 'handle':
            							$value3 = str_replace(";", ",", trim($value3));
            							$value3 = str_replace(" ", ",", $value3);
									    $_filter .= ' AND ' . $key3 . ' IN ( ' . $value3 . ')';
            							break;

            						default:
									    $_filter .= ' AND ' . $key3 . ' = ' . $value3;
            							break;
            					}
            				}
						}
					}
				}
			}
		}

		$_branches = static::getDb()->select()
			->from(self::$TABLE)
			->distinct()
            ->where($_filter)
	     	->joinLeft(array('c' => 'contract'),
	        			     'c.branch_id = branch.id',
	          		   array('c.template_id', 'c.date_canceled'))
	     	->group('branch_id')
		    ->order('branch.handle ASC')
		    ->query()
		    ->fetchAll();

	    $branches = array();
	    $i = 0;
	    foreach ($_branches as $value) {
			$aActiveContract = It6_ArrayWrapper::toNativeArray(Webservice_Contract::getActiveByBranch($value['id']));

			$branches[$i] = $value;
			$branches[$i]['branchId'] = $value['id'];
			$branches[$i]['stemId'] = $value['stem_id'];
			$branches[$i]['contract'] = "-";

            if ( $activeContract == 0 ) {
        		if (empty($aActiveContract)) {
					$branches[$i]['contract'] = "no active conract";
        		}
            }

            if ( $activeContract == 1 ) {
        		if (!empty($aActiveContract)) {
					$branches[$i]['contract'] = "conract ID:".$aActiveContract[0]['contractId'];
        		}
            }
            	
			$i++;
	    }

	    if ( count($branches) == 0 ){
	    	return;
	    }

		for ( $i = 0; $i < count($branches); ++$i )
			$branchIds[] = $branches[$i]['branchId'];
		$db = static::getMainDb();

		//roundMp
		$select = $db->select()
			->from(
				array('t' => Webservice_Ticket::$TABLE),
				array(
					'branchId' => 'b.id',
					'amount' => 'SUM(win_real)',
					'mpWin' => 'SUM(mp_win_amount)'))
			->join(
				array('hs' => 'vic_admin.'.Webservice_Host::$TABLE),
				't.host_id = hs.id',
				null)
			->join(
				array('b' => 'vic_admin.'.Webservice_Branch::$TABLE),
				'hs.branch_id = b.id',
				null)
			->where('hs.branch_id IN (?)', $branchIds)
			->where('collection_time IS NOT NULL')
			->group('hs.branch_id');

		if ( !empty($dateFrom) )
			$select->where('collection_time >= ?', $dateFrom);
		if ( !empty($dateTo) )
			$select->where('collection_time <= ?', $dateTo);

		$collects = It6_ArrayWrapper::toAssocLikeArray($select->query()->fetchAll(), 'branchId');

		$select = $db->select()
			->from(
				array('t' => Webservice_Ticket::$TABLE),
				array(
					'branchId' => 'b.id',
					'amount' => 'SUM(win_real)',
					'mpWin' => 'SUM(mp_win_amount)'))
			->join(
				array('hs' => 'vic_admin.'.Webservice_Host::$TABLE),
				't.host_id = hs.id',
				null)
			->join(
				array('b' => 'vic_admin.'.Webservice_Branch::$TABLE),
				'hs.branch_id = b.id',
				null)
			->where('hs.branch_id IN (?)', $branchIds)
			->where('vyplacen = 1')
			->where('is_loss <> 1')
			->group('hs.branch_id');

		if ( !empty($dateFrom) )
			$select->where('vyplacen_date >= ?', $dateFrom);
		if ( !empty($dateTo) ) {
			$select->where('vyplacen_date <= ?', $dateTo);
			//$select->where('(collection_time > ? OR collection_time IS NULL) ', $dateTo);
		}

		$payouts = It6_ArrayWrapper::toAssocLikeArray($select->query()->fetchAll(), 'branchId');

		$sqlInSumConds = array();
		$sqlOutSumConds = array('vyplacen=1', 'is_loss=0');
		if (!empty($dateFrom)) {
			$sqlInSumConds[] = $db->quoteInto('zalozen>=?', $dateFrom);
			$sqlOutSumConds[] = $db->quoteInto('vyplacen_date>=?', $dateFrom);
		}
		if (!empty($dateTo)) {
			$sqlInSumConds[] = $db->quoteInto('zalozen<=?', $dateTo);
			$sqlOutSumConds[] = $db->quoteInto('vyplacen_date<=?', $dateTo);
		}
		$sqlInSumConds = implode(' AND ', $sqlInSumConds);
		$sqlOutSumConds = implode(' AND ', $sqlOutSumConds);
		$sqlInSum = (empty($sqlInSumConds) ? 'castka': "IF($sqlInSumConds, castka, 0)");
		$sqlOutSum = (empty($sqlOutSumConds) ? 'win_real' : "IF($sqlOutSumConds, win_real, 0)");
		$select = $db->select()
			->from(
				array('t' => Webservice_Ticket::$TABLE),
				array(
					'branchId' => 'b.id',
					'in' => "SUM($sqlInSum)",
					'out' => "SUM($sqlOutSum)",
				))
			->join(
				array('u' => 'vic_main.'.Webservice_User::$TABLE),
				't.user_id = u.user_id',
				null)
			->join(
				array('b' => 'vic_admin.'.Webservice_Branch::$TABLE),
				'u.branch_id = b.id',
				null)
			->where('u.branch_id IN (?)', $branchIds)
			->where('t.host_id = ?',It6_Models_Host::ID_INTERNET)
			->group('u.branch_id');

		$sqlWhereConds = array(); // expressions to be OR-ed
		if (!empty($sqlInSumConds)) {
			$sqlWhereConds[] = "($sqlInSumConds)";
		}
		if (!empty($sqlOutSumConds)) {
			$sqlWhereConds[] = "($sqlOutSumConds)";
		}
		if (!empty($sqlWhereConds)) {
			$select->where('(' . implode(' OR ', $sqlWhereConds) . ')');
		}

		$internet = It6_ArrayWrapper::toAssocLikeArray($select->query()->fetchAll(), 'branchId');

		//roundMp
		$select = $db->select()
			->from(
				array('t' => Webservice_Ticket::$TABLE),
				array(
					'branchId' => 'hs.branch_id',
					'amount' => 'SUM(castka)',
					'mp' => 'SUM(CEIL(castka*mp))'))
			->join(
				array('hs' => 'vic_admin.'.Webservice_Host::$TABLE),
				't.host_id = hs.id',
				null)
			->where('t.zruseno <> 1')
			->where('hs.branch_id IN (?)', $branchIds)
			->group('hs.branch_id');

		if ( !empty($dateFrom) )
			$select->where('zalozen >= ?', $dateFrom);
		if ( !empty($dateTo) )
			$select->where('zalozen <= ?', $dateTo);

		$tickets = It6_ArrayWrapper::toAssocLikeArray($select->query()->fetchAll(), 'branchId');

		/*if (isset($dateFrom) && isset($dateTo)) {
		 $contracts = It6_ArrayWrapper::toAssocLikeArray(
			Webservice_Contract::getAllWhere(array(
				'date_canceled IS NULL',
				'date_signed IS NOT NULL',
				'date_valid_from < ?' => $dateFrom,
				'date_valid_to >= ?' => $dateFrom,
				'branchId IN (?)' => $branchIds)),
			'branchId'); 
		}*/
		// nactu smlouvy pro jejich hroamdne zobrazeni u kazde pobocky
		$contracts2 = It6_ArrayWrapper::toAssocLikeArray(Webservice_Contract::getAllWhere(array('branchId IN (?)' => $branchIds)), 'branchId');

		$ret = array();
		$c_params = array();

		for ( $i = 0; $i < count($branches); ++$i ) {
			$branch = & $branches[$i];
			$id = $branch['branchId'];

			if ( It6_Models_Branch::ID_INTERNET == $id ) continue;

			$ticketsAmount = empty($tickets[$id][0]) ? 0.0 : (float)$tickets[$id][0]['amount'];
			$collectsAmount = empty($collects[$id][0]) ? 0.0 : (float)$collects[$id][0]['amount'];
			$payoutsAmount = empty($payouts[$id][0]) ? 0.0 : (float)$payouts[$id][0]['amount'];
			$internetBalanceIn = empty($internet[$id][0]) ? 0.0 : (float)$internet[$id][0]['in'];
			$internetBalanceOut = empty($internet[$id][0]) ? 0.0 : (float)$internet[$id][0]['out'];
			$mp = (empty($tickets[$id][0]) ? 0.0 : (float)$tickets[$id][0]['mp']);
			$mpWin = (empty($collects[$id][0]) ? 0.0 : (float)$collects[$id][0]['mpWin']);
			$payoutsMpWin = (empty($payouts[$id][0]) ? 0.0 : (float)$payouts[$id][0]['mpWin']);
			$mpTotal = $mpWin;// + $mp;

			$in = $ticketsAmount;// + $mp;
			$out = $collectsAmount - $mpWin;
			$payoutOut = $payoutsAmount;// - $payoutsMpWin;

			$profit = $in - $out;
			$profitNoMp = $profit - $mpTotal;

			$branchContracts = array();
			$branchActive = 0;
			$constractsString = '';
			$branchValidTo = null;
			$branchValidFrom = null;
			$contractType = null;
			$contractTemplateId = null;

			$activeContract = Webservice_Contract::getActiveByBranch($id);
			$activeContract = It6_ArrayWrapper::toNativeArray($activeContract);

			if (!empty($activeContract)) {
				$activeBranchContract = array_merge(Models_Utils::parseParameters($activeContract[0]['parameters']), $activeContract[0]);
				$branchValidFrom = $activeBranchContract['dateValidFrom'];
				$contractType = $activeBranchContract['name'] . self::getParamByContract($activeBranchContract);
				$contractTemplateId = $activeBranchContract['templateId'];
				$branchActive = 1;
			}

			if ( !empty($contracts2[$id]) ) {
				foreach ($contracts2[$id] as $contract){
					$constractsString .= sprintf("%s - %s<br />(%s%s) <br />", It6_Date::fromDbAsDate($contract['dateValidFrom']), It6_Date::fromDbAsDate($contract['dateValidTo']), $contract['templateName'],  self::getParamByContract($contract));	
	
					if(is_null($branchValidFrom) && is_null($branchValidTo)){
						$branchValidFrom = $contract['dateValidFrom'];
						$branchValidTo = $contract['dateValidTo'];
					}

					if (strtotime($contract['dateValidFrom']) < strtotime($branchValidFrom)){
						$branchValidFrom = $contract['dateValidFrom'];
					}

					if (strtotime($contract['dateValidTo']) > strtotime($branchValidTo)){
						$branchValidTo = $contract['dateValidTo'];
					}
				}
			}

			$stemName = '';
			$stem = array();

			if ($branch['stemId']) {
				$stem = It6_ArrayWrapper::toNativeArray(Webservice_Stem::getById($branch['stemId']));
				$stemName = $stem['name'];	
			}
			if ( $contractTemplateId ) {
				$params = It6_ArrayWrapper::toAssocArray($contracts2[$id][0]['parameters'],'name','%value%');

				switch ( $contractTemplateId ) {
					case Webservice_ContractTemplate::TEMPLATE_VSAZENO_VYPLACENO:
						//[(IN-OUT)-(IN-OUT)*21%] * X
						$provision = ($in - $payoutOut)
								* (1 - $params['odvod-statu'])
								* $params['vyse-zalohy'];
						$tax = $profit * $params['odvod-statu'];

						$provision = 0;

						break;
					case Webservice_ContractTemplate::TEMPLATE_VSAZENO_PREDEPSANO:
						//[(IN-OUT) – (IN-OUT)*Y - (IN-OUT)*21%] * X - Z
						//$provision = ($in - $out)
						//		* (1 - $params['sleva-na-zaloze'] - $params['odvod-statu'])
						//		* $params['vyse-zalohy'] - $params['pausal'];

						$vyplaceno = ($in - $payoutOut);
						$dph = $vyplaceno * $params['odvod-statu'];
						$bez_dph = $vyplaceno - $dph;
						$fond_rizik = ($bez_dph *  Constant::get('RISK_LIMIT_PCT')) / 100;
						$zaklad_prozive = $bez_dph - $fond_rizik;
						$provision = $zaklad_prozive * $params['vyse-zalohy'];

						$tax = $profit * $params['odvod-statu'];
						break;
					case Webservice_ContractTemplate::TEMPLATE_PAUSAL:
						$provision = 0;
						$tax = 0;
						break;
					case Webservice_ContractTemplate::TEMPLATE_NABER:
						//IN * X
						if(isset($params['vyse-naberu']) && is_numeric($params['vyse-naberu'])){
							$provision = $ticketsAmount * $params['vyse-naberu'];
							$pctNaber = $params['vyse-naberu'] * 100;
						} else {
							$provision = 0;
						}
						$tax = 0;

						break;
				}
			} else {
				$provision = 0;
				//TODO tax should be obtained other way then from contract parameters
				$tax = 0;
			}

			$ret[] = array(
				'branchHandle'         => $branch['handle'],
				'branchName'           => $branch['name'],
				'is_active'            => $branch['is_active'],
				'stem'				   => $stemName,
				'contracts'			   => $constractsString,
				'templateId'           => isset($contracts2[$id][0]['templateId']) ? $contracts2[$id][0]['templateId'] : 0,
				'branchValidFrom'	   => $branchValidFrom,
				'branchValidTo'		   => (($branchActive === 0) ? $branchValidTo : ''),
				'contractType'		   => $contractType,
				'branchActive'		   => $branchActive,
				'branchIn'             => $in,
				'branchOut'            => $out,
				'branchMp'             => 0, //$mpTotal,
				'branchInMinusOut'     => $in - $out, // + $mpTotal,
				'branchPayoutOut'      => $payoutOut,
				'branchPayoutMp'       => $payoutsMpWin,
				'branchProfit'         => $profit,
				'branchProfitNoMp'     => $profitNoMp,
				'internetBalance'      => $internetBalanceIn - $internetBalanceOut,
				'internetBalanceIn'    => $internetBalanceIn,
				'internetBalanceOut'   => $internetBalanceOut,
				'provision'            => $provision,
				'tax'                  => $tax,
				'taxValue'             => isset($params['odvod-statu']) ? $params['odvod-statu'] : 0, 
				'vyse-zalohy'          => isset($params['vyse-zalohy']) ? $params['vyse-zalohy'] : 0, 
				'pctNaber'			   => isset($pctNaber) ? $pctNaber : 0
			);
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

		//$key = (isset($key)) ? $key : 'branchHandle';
		//$desc = (isset($desc)) ? $desc : 'branchHandle';
		if ( !empty($key) && !empty($desc) ) {
			usort($ret, function ($a, $b) use ($key, $desc) {
				if ($desc) {
					$_ = $b;
					$b = $a;
					$a = $_;
				}
				if ( isset($a[$key]) && isset($b[$key]) ) {
					return (is_numeric($a[$key]) && is_numeric($b[$key])) ? $a[$key] > $b[$key] : strcmp($a[$key],$b[$key]);
				}
			});
		}

		return $ret;
	}

		public static function getTownInitials() {
			$sql = static::getAdminDb()->query("
				SELECT IF( SUBSTR(town,1,2) = 'Ch','Ch',SUBSTR(town,1,1) ) AS initial, COUNT(id)
				FROM branch
				WHERE type_id <> 1
				GROUP BY initial
				ORDER BY town
			");
	
			$initials = $sql->fetchAll();
	
			return $initials;
		}
		
		public static function getTowns() {
			$sql = static::getAdminDb()->query("
				SELECT DISTINCT town, MAX(id) id, MAX(longitude) longitude, MAX(latitude) latitude
				FROM branch
				WHERE type_id <> 1
				AND is_active <> 0
				AND is_listed <> 0
				AND is_testing <> 1 
				GROUP BY town
				ORDER BY town
				");
		
		$towns = $sql->fetchAll();
		
		return $towns;
		}
	
		public static function getOpeningHoursByBranchId($branchId) {
			$hours = array(
				'0'=>array(),
				'1'=>array(),
				'2'=>array(),
				'3'=>array(),
				'4'=>array(),
				'5'=>array(),
				'6'=>array(),
			);
	
			$res = static::getDb()->select()
				->from(self::$TABLE_BRANCH_OPENING_HOURS)
				->where('branch_id = ?', $branchId)
				->order('day_number')
				->query()->fetchAll();
	
			foreach($res as $row) {
				$hours[$row['day_number']][] = array(
					'from'	=> substr($row['from'], 0, -3),
					'to'	=> substr($row['to'], 0, -3),
				);
			}
			
			return $hours;
		}
	
	
		public static function updateOpeningHours($branchId, $hours) {
			$dbEntries	= array();
			$db			= static::getDb();
	
			It6_DbTransaction::begin($db);
			try {
				$db->delete(self::$TABLE_BRANCH_OPENING_HOURS, array('branch_id = ?' => $branchId));
				foreach($hours as $day => $dayHours) {
					$dayHours = explode(';', $dayHours);
					foreach($dayHours as $intervals) {
						if(!empty($intervals)) {
							$intervalsArr = explode(',', $intervals);
							foreach($intervalsArr as $interval) {
								$intervalArr = explode('-', $interval);
								$data = array(
									'branch_id'		=> $branchId,
									'day_number'	=> $day,
									'from'			=> $intervalArr[0],
									'to'			=> $intervalArr[1],
								);
								$db->insert(self::$TABLE_BRANCH_OPENING_HOURS, $data);
							}
						}
					}
				}
				It6_DbTransaction::commit($db);
				return true;
			}
			
			catch ( Exception $e ) {
				It6_DbTransaction::rollback($db);
				throw new It6_XmlRpc_Exception("Can not update branch opening hours.", 0, $e);
			}
		}
	
		/**
		 * Check if branch is system branch
		 * @param integer|NULL $branchId
		 * @return boolean
		 */
		public static function isSystem($branchId = null) {
			if ( empty($branchId) ) {
				$acl = Zend_Registry::get('acl');
				$branchId = $acl->getIdentity(It6_Acl::IDNAME_BRANCH);
			}
			return It6_Models_Branch::isSystemId($branchId);
		}
		
		private static function getParamByContract($contract){
			$param = '';
			switch($contract['templateName']){
				case 'naber':
					if (isset($contract['param11']))
						$param = It6_Filter_Float::commaToPoint($contract['param11'])*100;
					else $param = It6_Filter_Float::commaToPoint($contract['parameters'][0]['value'])*100;
					$char = '%';
					break;
					
				case 'vsazeno-vyplaceno':
					if (isset($contract['param1']))
						$param = It6_Filter_Float::commaToPoint($contract['param1'])*100;
					else $param = It6_Filter_Float::commaToPoint($contract['parameters'][0]['value'])*100;
					$char = '%';
					break;
					
				case 'vsazeno-predepsano':
					if (isset($contract['param5']))
						$param = It6_Filter_Float::commaToPoint($contract['param5'])*100;
					else $param = It6_Filter_Float::commaToPoint($contract['parameters'][0]['value'])*100;
					$char = '%';
					break;
					
				case 'pausal':
					if (isset($contract['param9']))
						$param = intval($contract['param9']);
					else $param = $contract['parameters'][0]['value'];
					$char = 'Kč';
					break;
			}	
			
			return " " . $param . " " . $char;
		}

		/**
		 * Gets data for report
		 * @return array
		 */
		public static function getDataForReport($dateFrom = null, $dateTo = null, $group = false, $hostId = null) {
			try {
				$db = static::getAdminDb();

				$select = $db->select()
					->from(
						array('B' => static::$TABLE),
						array(
							!empty($group) ? 'B.id' : '',
							'count'              => 'Sum(IF(point_type_id IS NULL,1,0))',
							'amount'             => 'Sum(IF(point_type_id IS NULL,castka,0))',
							'countPoints'        => 'Sum(IF(point_type_id IS NULL,0,1))',
							'amountPoints'       => 'Sum(IF(point_type_id IS NULL,0,castka))',
							'countCancel'        => 'Sum(IF(point_type_id IS NULL AND zruseno=1,1,0))',
							'amountCancel'       => 'Sum(IF(point_type_id IS NULL AND zruseno=1,castka,0))',
							'countCancelPoints'  => 'Sum(IF(point_type_id IS NOT NULL AND zruseno=1,1,0))',
							'amountCancelPoints' => 'Sum(IF(point_type_id IS NOT NULL AND zruseno=1,castka,0))',
						)
					)
					->join(array('H' => 'vic_admin.host'), 'B.id = H.branch_id', null)
					->join(array('T' => 'vic_main.ticket'), 'T.host_id = H.id', null);

				if (!empty($dateFrom)) $select = $select->where('zalozen >= ?', $dateFrom);
				if (!empty($dateTo)) $select = $select->where('zalozen < ?', $dateTo);
				if (!empty($hostId)) $select = $select->where('host_id = ?', $hostId);

				if (!empty($group)) {
					$select = $select->group('B.id');
					$select = $select->query()->fetchAll();
				} else {
					$select = $select->limit(1);
					$select = $select->query()->fetch();
				}

				return $select;

			} catch ( Exception $e) {
				throw new It6_XmlRpc_Exception("Branch::getDataForReport.", 0, $e);
			}
		}

		/**
		 * Gets payout tickets for report
		 * @return array
		 */
		public static function getPayoutTicketsForReport($dateFrom, $dateTo, $branchId = null, $collect = false) {
			try {
				$db = static::getAdminDb();

				$select = $db->select()
					->from(
						array('B' => static::$TABLE),
						array(
							'countCollection'  => 'Count(*)',
							'amountCollection' => 'Sum(win_real-mp_win_amount)',
						)
					)
					->join(array('H' => 'vic_admin.host'), 'H.branch_id = B.id', null)
					->join(array('T' => 'vic_main.ticket'), 'T.host_id = H.id', null)
					->where('vyplacen = 1')
					->where('is_loss = 0')
					->where('cash = 1');

				if ($collect === true) {
					$select = $select
						->where('collection_time IS NOT NULL')
						->where('collection_time < ?', $dateTo, $dateTo)
						->where('collection_time >= ?', $dateFrom, $dateFrom);
				} else {
					$select = $select
						->where('vyplacen_date IS NOT NULL')
						->where('vyplacen_date < ?', $dateTo, $dateTo)
						->where('vyplacen_date >= ?', $dateFrom, $dateFrom);
				}

				$select = $select->limit(1)->query()->fetch();

				if (empty($branchId) and empty($collect)) {
					$select_net = $db->select()
						->from(
							array('B' => static::$TABLE),
							array(
								'countCollectionNet'  => 'Count(*)',
								'amountCollectionNet' => 'Sum(win_real-mp_win_amount)',
							)
						)
						->join(array('H' => 'vic_admin.host'), 'H.branch_id = B.id', null)
						->join(array('T' => 'vic_main.ticket'), 'T.host_id = H.id', null)
						->where('vyplacen = 1')
						->where('is_loss = 0')
						->where('cash = 0')
						->where('vyplacen_date < ?', $dateTo, $dateTo)
						->where('vyplacen_date >= ?', $dateFrom, $dateFrom)
						->limit(1)
						->query()->fetch();

					$select += $select_net; 
				}

				return $select;

			} catch ( Exception $e) {
				throw new It6_XmlRpc_Exception("Branch::getPayoutTicketsForReport.", 0, $e);
			}
		}

		/**
		 * Gets data for stem report
		 * @return array
		 */
		public static function getDataStemBranch($stemId = null, $dateFrom, $dateTo, $monthStart = null) {
			try {
				$db = static::getAdminDb();

				$select = $db->select()
					->from(
						array('B' => static::$TABLE),
						array(
							"id" => "B.id",
							"handle" => "handle",
							"street" => "street",
							"town" => "town",
							"desc" => "S.desc",
							"count" => "Sum(IF(point_type_id IS NULL AND zalozen >= '".$dateFrom."' AND zalozen < '".$dateTo."',1,0))",
							"amount" => "Sum(IF(point_type_id IS NULL AND zalozen >= '".$dateFrom."' AND zalozen < '".$dateTo."',castka,0))",
							!empty($monthStart) ? "Sum(IF(point_type_id IS NULL AND zalozen >= '".$monthStart."' AND zalozen < '".$dateTo."',1,0)) AS count_month" : '',
							!empty($monthStart) ? "Sum(IF(point_type_id IS NULL AND zalozen >= '".$monthStart."' AND zalozen < '".$dateTo."',castka,0)) AS amount_month" : '',
							!empty($monthStart)
								? "Sum(IF(collection_time IS NOT NULL AND collection_time >= '".$monthStart."' AND collection_time < '".$dateTo."',win_real-mp_win_amount,0)) AS payout_month"
								: "Sum(IF(collection_time IS NOT NULL AND collection_time >= '".$dateFrom."' AND collection_time < '".$dateTo."',win_real-mp_win_amount,0)) AS payout_month"
						)
					)
					->joinLeft(array('S' => 'vic_admin.stem'), 'B.stem_id = S.id', null)
					->joinLeft(array('H' => 'vic_admin.host'), 'B.id = H.branch_id', null)
					->joinLeft(array('T' => 'vic_main.ticket'), 'T.host_id = H.id', null)
					->where('B.is_active = ?', '1')
					->where('B.id <> ?', '1');

				if (!empty($stemId)) $select = $select->where('stem_id = ?', $stemId);

				$select = $select->group('B.handle');

				$select = $select->query()->fetchAll(Zend_Db::FETCH_OBJ);
				return $select;

			} catch ( Exception $e) {
				throw new It6_XmlRpc_Exception("Branch::getDataForStemBranch.", 0, $e);
			}
		}

		/**
		 * Gets withdraws for stem report
		 * @return array
		 */
		public static function getWithdrawsForReport($branchHandle) {
			try {
				$onTheWayDeposit = 0;
				$onTheWayWithdraw = 0;

				$hosts = Webservice_Host::getByHostOrBranch(null, null, $branchHandle);

				foreach ($hosts as $host) {
					$onTheWay = Webservice_Host::getResourcesOnTheWay($host->hostId);
					$onTheWayDeposit += $onTheWay['deposit'];
					$onTheWayWithdraw += $onTheWay['withdraw'];
				}

				return array(
					'onTheWayDeposit' => $onTheWayDeposit,
					'onTheWayWithdraw' => $onTheWayWithdraw,
				);

			} catch ( Exception $e) {
				throw new It6_XmlRpc_Exception("Branch::getWithdrawsForReport.", 0, $e);
			}
		}
	}