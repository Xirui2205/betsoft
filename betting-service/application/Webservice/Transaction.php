<?php
/**
 * Transaction related static methods
 * @author Pavel Klinger
 * @see Entities_Transaction
 *
 */
class Webservice_Transaction extends Webservice_AbstractWebService  {

	const STATUS_OK = 'ok';
	const STATUS_PENDING = 'pending';
	const STATUS_CANCELED = 'canceled';
	const STATUS_PRE_DEPOSIT = 'pre-deposit';
	const STATUS_NO_DEPOSIT = 'no-deposit';

	const FEE_ACCOUNT = '668';

	public static $TABLE 				= "financial_transaction";
	public static $TABLE_PREFIX			= "ft";
	public static $BALANCE_LOG_TABLE 	= "financial_transaction_history";
	public static $HANDLE_TABLE 		= "financial_transaction_handle_sequence";
	public static $USER_IM_DATA_TABLE	= "uzivatel_im_data";
	public static $ENTITY_NAME			= "Entities_Transaction";
	public static $IDENTITY				= "transaction_id";
	protected static $CONV = array(
		'ft.transaction_id'			=> 'transactionId',
		'ft.handle'					=> 'handle',
		'canceled_transaction_id'	=> 'canceledTransactionId',
		'ft.host_id'				=> 'hostId',
		'ft.ticket_id'				=> 'ticketId',
		'value'						=> 'value',
		'balance'					=> 'balance',
		'ft.type_id'				=> 'typeId',
		'ft.time'					=> 'time',
		'DATE(ft.time)'				=> 'date',
		'okTime'					=> 'okTime',
		'cancelTime'				=> 'cancelTime',
		'deposit_time'				=> 'depositTime',
		'ft.mena_id'				=> 'currencyId',
		'note'						=> 'notes',
		'ft.status'					=> 'status',
		'fee' 						=> 'fee',
		'fee_transaction_id'		=> 'feeTransactionId',

		'tt.name'					=> 'typeName',
		'cur.mena_text'				=> 'currencyCode',

		'IF(ISNULL(ft.account_type),tt.account_type,ft.account_type)'
									=> 'accountType',

		'IF(ISNULL(ft.need_confirm),tt.need_confirm,ft.need_confirm)'
									=> 'needConfirm',

		'IF(ISNULL(ft.late_deposit),tt.late_deposit,ft.late_deposit)'
									=> 'lateDeposit',

		'IF(ISNULL(ft.`from`),tt.`from`,ft.`from`)'
									=> 'from',
		'tt.from_sub'				=> 'fromSub',

		'IF(ISNULL(ft.`to`),tt.`to`,ft.`to`)'
									=> 'to',
		'tt.to_sub'					=> 'toSub',

		'ft.export_date'			=> 'exportDate',
		'usr.user_id'				=> 'userId',
		"IF(usr.anonymous,'anonymous',usr.nick)" => 'userNick',
		'usr.handle'				=> 'userHandle',
		'usr.anonymous'				=> 'anonymous',
		'br.id'						=> 'branchId',
		'br.handle'					=> 'branchHandle',
		'br.name'					=> 'branchName',
		'ft.host_id'				=> 'hostId',
		'hs.name'					=> 'hostName',
		't.handle'					=> 'ticketHandle',
		'lt.handle'					=> 'liveTicketHandle',
		'tt.account_type'			=> 'accountType',
		"IF(tt.account_type = 'user',uba.account_number,a.account_number)"		=> 'bankAccountNumber',
		"IF(tt.account_type = 'user',uba.account_prefix,a.account_prefix)"		=> 'bankAccountPrefix',
		"IF(tt.account_type = 'user',bnk.bank_code,a.bank_code)"				=> 'bankAccountBankCode',
		'ft.admin_create'			=> 'createAdminId',
		'CONCAT(cra.first_name, \' \', cra.surname)' => 'createAdmin',
		'ft.admin_confirm'			=> 'confirmAdminId',
		'CONCAT(coa.first_name, \' \', coa.surname)' => 'confirmAdmin',
		'ft.admin_deposit'			=> 'depositAdminId',
		'CONCAT(dea.first_name, \' \', dea.surname)' => 'depositAdmin',
		'ft.balance_changed'		=> 'balanceChanged',
		'tt.status_changing_balance' => 'typeStatusChangingBalance',
	);

	/**
	 * NOTE: this function is direct projection of database logic for vic_main.financial_transaction_type.status_changing_balance
	 * @param struct $transanction
	 * @param string|NULL $status Transaction status to be used instead of status from $transaction struct
	 * @param struct $type Type data to be used if transaction hasn't all needed type data included in itself
	 * @return boolean
	 */
	protected static function isStatusChangingBalance($transaction, $status = null, $type = null) {
		static $statusOrder = array(
			self::STATUS_PENDING => 0,
			self::STATUS_PRE_DEPOSIT => 1,
			self::STATUS_OK => 2,
		);
		if (!empty($transaction['balanceChanged']))
			return false;
		if (isset($transaction['forceBalanceChange']))
			return $transaction['forceBalanceChange'];
		if (empty($status))
			$status = $transaction['status'];
		if (isset($transaction['typeStatusChangingBalance']))
			$typeChangingStatus = $transaction['typeStatusChangingBalance'];
		else if (isset($type))
			$typeChangingStatus = $type['statusChangingBalance'];
		else
			$typeChangingStatus = 'pending';
		return ($statusOrder[$status] >= $statusOrder[$typeChangingStatus]);
	}

	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->join(
				array(Webservice_Currency::$TABLE_PREFIX => Webservice_Currency::$TABLE),
				self::$TABLE_PREFIX . '.mena_id = '.Webservice_Currency::$TABLE_PREFIX.'.mena_id',
				null)
			->join(
				array(Webservice_TransactionType::$TABLE_PREFIX => Webservice_TransactionType::$TABLE),
				self::$TABLE_PREFIX . '.type_id = '.Webservice_TransactionType::$TABLE_PREFIX.'.id',
				null)
			->joinLeft(
				array(Webservice_User::$TABLE_PREFIX => Webservice_User::$TABLE),
				self::$TABLE_PREFIX . '.user_id = '.Webservice_User::$TABLE_PREFIX.'.user_id',
				null)
			->joinLeft(
				array(Webservice_User::$TABLE_USER_BANK_ACC_PREFIX => Webservice_User::$TABLE_USER_BANK_ACC),
				Webservice_User::$TABLE_USER_BANK_ACC_PREFIX.'.user_id = '.Webservice_User::$TABLE_PREFIX.'.user_id
				AND '.Webservice_User::$TABLE_USER_BANK_ACC_PREFIX.'.is_current = 1',
				null)
			->joinLeft(
				array(Webservice_User::$TABLE_BANK_PREFIX => Webservice_User::$TABLE_BANK),
				Webservice_User::$TABLE_USER_BANK_ACC_PREFIX.'.bank_id = '.Webservice_User::$TABLE_BANK_PREFIX.'.bank_id',
				null)
			->joinLeft(
				array(Webservice_Host::$TABLE_PREFIX => 'vic_admin.'.Webservice_Host::$TABLE),
				self::$TABLE_PREFIX . '.host_id = '.Webservice_Host::$TABLE_PREFIX.'.id',
				null)
			->joinLeft(
				array(Webservice_Branch::$TABLE_PREFIX => 'vic_admin.'.Webservice_Branch::$TABLE),
				Webservice_Host::$TABLE_PREFIX . '.branch_id = '.Webservice_Branch::$TABLE_PREFIX.'.id',
				null)
			->joinLeft(
				array('bha' => 'vic_admin.'.Webservice_BankAccount::$TABLE_BRANCH_HAS_BANK_ACCOUNT),
				Webservice_Branch::$TABLE_PREFIX . '.id = bha.branch_id AND bha.bank_account_type = 2 AND bha.is_current = 1',
				null)
			->joinLeft(
				array(Webservice_BankAccount::$TABLE_PREFIX => 'vic_admin.'.Webservice_BankAccount::$TABLE),
				Webservice_BankAccount::$TABLE_PREFIX . '.account_id = bha.bank_account_id',
				null)
			->joinLeft(
				array(Webservice_Ticket::$TABLE_PREFIX => 'vic_main.'.Webservice_Ticket::$TABLE),
				self::$TABLE_PREFIX . '.ticket_id = '.Webservice_Ticket::$TABLE_PREFIX.'.ticket_id',
				null)
			->joinLeft(
				array('lt' => 'vic_main.'.Webservice_Livebetting::$TABLE),
				self::$TABLE_PREFIX . '.ticket_id = lt.id',
				null)
			->joinLeft(
				array('cra' => 'vic_admin.'.Webservice_Admin::$TABLE),
				self::$TABLE_PREFIX . '.admin_create = cra.admin_id',
				null)
			->joinLeft(
				array('coa' => 'vic_admin.'.Webservice_Admin::$TABLE),
				self::$TABLE_PREFIX . '.admin_confirm = coa.admin_id',
				null)
			->joinLeft(
				array('dea' => 'vic_admin.'.Webservice_Admin::$TABLE),
				self::$TABLE_PREFIX . '.admin_deposit = dea.admin_id',
				null);
	}

	/**
	 * Returns all transactions in the system.
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Sends CSV file with exported data of all transactions in the system.
	 * @see Entities_Transaction
	 */
	public static function export($filename, $extensions = null) {
		try {
			$tmpFile = tmpfile();
			$size = 0;
			$header = null;
			$hookRows = function($stmt) use ($tmpFile, &$size, &$header) {
				$fmtDate = function(&$output, $value, $keyTime, $keyDate) {
					$t = It6_Date::fromDbAsTimestamp($value);
					$output[$keyDate] = It6_Date::timestampToDate($t);
					$output[$keyTime] = It6_Date::timestampToTime($t);
				};
				while ($row = $stmt->fetch()) {
					unset($row['date']);
					$values = array();
					foreach ($row as $key => $value) {
						switch ($key) {
						case 'time':
							$fmtDate($values, $value, 'time', 'date');
							break;
						case 'okTime':
							$fmtDate($values, $value, 'okTime', 'okDate');
							break;
						case 'cancelTime':
							$fmtDate($values, $value, 'cancelTime', 'cancelDate');
							break;
						case 'depositTime':
							$fmtDate($values, $value, 'depositTime', 'depositDate');
							break;
						default:
							$values[$key] = $value;
						}
					}
					unset($row);
					if (empty($header)) {
						$header = array_keys($values);
						array_walk($header, function(&$value) { $value = str_replace('"', '""', $value); });
						$line = '"' . implode('";"', $header) . "\"\r\n";
						$size += fwrite($tmpFile, $line);
					}
					array_walk($values, function(&$value) { $value = str_replace('"', '""', $value); });
					$line = '"' . implode('";"', $values) . "\"\r\n";
					$size += fwrite($tmpFile, $line);
				}
				return false;
			};
			static::getAllWhereInjected(array(), array('fetchRows' => $hookRows), $extensions);
			//static::dayBookQuery($callback, $extensions);
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Length: ' . $size);
			header('Content-Disposition: attachement; filename=' . $filename);
			fseek($tmpFile, 0);
			fpassthru($tmpFile);
			fclose($tmpFile);
			exit; // this function breaks XML-RPC and serves file directly
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('Transactions export failed', 0, $e);
		};
	}

	/**
	 * Returns all ok transactions in the system.
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAllOk($extensions = null) {
		return static::getAllWhere(array('status = ?' => self::STATUS_OK), $extensions);
	}

	/**
	 * Returns all canceled transactions in the system.
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAllCanceled($extensions = null) {
		return static::getAllWhere(array('status = ?' => self::STATUS_CANCELED), $extensions);
	}

	/**
	 * Returns all pending transactions in the system.
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAllPending($extensions = null) {
		return static::getAllWhere(array('status = ?' => self::STATUS_PENDING), $extensions);
	}

	/**
	 * Returns all pending transactions in the system.
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAllPreDeposit($extensions = null) {
		return static::getAllWhere(array('status = ?' => self::STATUS_PRE_DEPOSIT), $extensions);
	}

	/**
	 * Returns all pending transactions in the system.
	 * @return array array of transaction structs
	 * @see Entities_Transaction
	 */
	public static function getAllNoDeposit($extensions = null) {
		return static::getAllWhere(array('status = ?' => self::STATUS_NO_DEPOSIT), $extensions);
	}

	/**
	 * Find transaction with the same given parameters
	 * @param sturct $transaction
	 * @param integer $hours how many hours to the past will look up.
	 * @return struct|boolean transaction struct
	 * @see Entities_Transaction
	 */
	public static function findSimilar($transaction, $hours = 48 , $extensions = null) {
		$where = array();
		foreach ( $transaction as $k => $v ) {
			$where[$k . '= ?'] = $v;
		}
		$where["time > DATE_SUB('".It6_Date::dbNow()."', INTERVAL ? HOUR)"] = $hours;

		$ret = static::getAllWhere($where, $extensions);
		if ( !empty($ret) )
			return $ret[0];
		else
			return false;

		return false;
	}

	/**
	 * Find transaction by given identifier.
	 * @param integer $id identifier of the transaction
	 * @return struct team structure
	 * @see Entities_Transaction
	 */
	public static function getById($id, $extensions = null) {
		return parent::getById($id, $extensions);
	}

	public static function insert($transaction) {
		throw new It6_XmlRpc_Exception("Forbidden");
	}

	public static function update($transaction) {
		throw new It6_XmlRpc_Exception("Forbidden");
	}

	public static function delete($transactionId) {
		throw new It6_XmlRpc_Exception("Forbidden");
	}

	protected static function changeUserLimitActualAmount($transaction, $sign = -1) {
		$user = Webservice_User::getById($transaction->userId);

		if ( false == $user ) throw new Exception('Unknown user id.\''.$transaction->userId.'\'');

		switch ($transaction->typeName) {
			case Webservice_TransactionType::NAME_USER_TICKET_CREATE:
				break;
			case Webservice_TransactionType::NAME_USER_TICKET_CREATE_MP:
				break;
			case Webservice_TransactionType::NAME_USER_TICKET_CANCEL:
				break;
			case Webservice_TransactionType::NAME_USER_TICKET_CANCEL_MP:
				break;
			default:
				return false;
		}

		$value = Webservice_Currency::exchange($transaction->currencyId, $user->currencyId , $sign * $transaction->value);

		$userLimit = Webservice_SettingLimits::getUserLimits($transaction->userId);
		if (!empty($userLimit)) {
			$update = Webservice_SettingLimits::updateActualAmount($userLimit->limitId, $value);
			if (!empty($update)) {
				It6_Log::info(
					"Transaction: Current amount of user-limit '%limitId%' user '%userId%' has been updated from '%oldAmount%' to '%newAmount%'.",
					It6_Log::TAG_USER_LIMIT_ACTUALISATION,
					array(
						'limitId' => $userLimit->limitId,
						'oldAmount' => $userLimit->actualAmount,
						'newAmount' => $userLimit->actualAmount + ($sign * $transaction->value),
						'userId' => empty($transaction['userId']) ? null : $transaction['userId']
					)
				);
			}
		}
	}

	protected static function changeBalance($transaction, $type, $debiting = null, $sign = 1, &$bonusChanged = null, &$cache = null) {
		$admindb = static::getAdminDb();
		$db = static::getMainDb();

		

		try {

		if ( null == $debiting ) $debiting = $type->debiting;
		if (!isset($cache)) {
			$cache = array();
		}
		if ( !empty($transaction->canceledTransactionId) )
			$debiting = true;

		$accountType = !empty($transaction->accountType)
						? $transaction->accountType
						: $type->accountType;

		if ( Webservice_TransactionType::ACCOUNT_TYPE_USER == $accountType ) {
			
 			if ( isset($transaction->userId) ) {
	 			if (!array_key_exists('user', $cache)) {
					$cache['user'] = Webservice_User::getById($transaction->userId);
	 			}
	 			$user = &$cache['user'];

				$user = Webservice_User::getById($transaction->userId);
			}

			if ( false == $user )
				throw new Exception('Unknown user id.\''.$transaction->userId.'\'');
			
			It6_DbTransaction::lock($db,It6_Synchronize::getInstance(array('changeBalanceOfUserId' => $user->userId)));

			$value = Webservice_Currency::exchange(
						$transaction->currencyId, $user->currencyId , $sign * $transaction->value);

			$finalBalance = $user->balance + $value;

			It6_Log::info(
				"Transaction %transactionId%: Trying to change user '%userId%' balance from '%oldAmount%' to '%newAmount%'.",
				It6_Log::TAG_TRANSACTION,
				array(
					'transactionId' => empty($transaction['transactionId']) ? null : $transaction['transactionId'],
					'oldAmount' => $user->balance,
					'newAmount' => $finalBalance,
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['hostId']));

			if ( $finalBalance > MAX_BALANCE || $finalBalance < MIN_BALANCE) {
				throw new It6_XmlRpc_Exception("MAX or MIN possible balance overflowed.");
			}

			if ( !$debiting && $value < 0 && $finalBalance < 0 ) {
				throw new It6_XmlRpc_Exception("Out of user money.");
			}

			$db->update(
				static::$USER_IM_DATA_TABLE,
				array('zustatek' => new Zend_Db_Expr('zustatek+' . floatval($value))),
				array( 'user_id = ?' => $transaction->userId));

			$forceChangingBonus = !empty($transaction->forceChangingBonus);
			if ($forceChangingBonus || (!$debiting && $value > 0 && $type->changingBonus)) {
				$bonusChanged = Webservice_User::setEntryBonusBase($transaction->userId, $value, !$forceChangingBonus, !$forceChangingBonus);
			}
			else
				$bonusChanged = false;

			It6_GlobalCache_Invalidator::Transaction_changeBalance($transaction->userId);

			$user->balance = $finalBalance;

			//It6_DbTransaction::commit($db);
			//It6_DbTransaction::commit($admindb);
			
			return  $finalBalance;

		}
		else if ( Webservice_TransactionType::ACCOUNT_TYPE_HOST == $accountType ) {
			It6_DbTransaction::begin($db);
			It6_DbTransaction::begin($admindb);
			if ( $transaction->value < 0 && !Webservice_Host::isOutAllowed($transaction->hostId)  )
				throw new Exception("Out transactions are disabled for this host.");

			if ( $transaction->value > 0 && !Webservice_Host::isInAllowed($transaction->hostId)  )
				throw new Exception("In transactions are disabled for this host.");

			// Balance can't be cached, has to be up to date
 			if (!array_key_exists('host', $cache)) {
 				$cache['host'] = Webservice_Host::getById($transaction->hostId);
 			}
 			$host = &$cache['host'];
			if (!$host) {
				throw new Exception('Unknown host id.\''.$transaction->hostId.'\'');
			}
			
			if (!array_key_exists('branch', $cache)) {
				$cache['branch'] = Webservice_Branch::getById($host->branchId);
			}
			$branch = &$cache['branch'];

			It6_DbTransaction::lock($db,It6_Synchronize::getInstance(array('changeBalanceOfHostId' => $host->hostId)));
			
			$value = Webservice_Currency::exchange(
						$transaction->currencyId, $branch->currencyId , $sign * $transaction->value);

			$finalBalance = $host->balance + $value;

			It6_Log::info(
				"Transaction %transactionId%: Trying to change host '%hostId%' balance from '%oldAmount%' to '%newAmount%'.",
				It6_Log::TAG_TRANSACTION,
				array(
					'transactionId' => empty($transaction['transactionId']) ? null : $transaction['transactionId'],
					'oldAmount' => $host->balance,
					'newAmount' => $finalBalance,
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));

			if ( $finalBalance > MAX_BALANCE || $finalBalance < MIN_BALANCE) {
				throw new It6_XmlRpc_Exception("MAX or MIN possible balance overflowed.");
			}

			if ( !$debiting && $finalBalance < 0 ) {
				throw new It6_XmlRpc_Exception("Out of host money.");
			}

			$admindb->update(
				Webservice_Host::$TABLE,
				array('balance' => new Zend_Db_Expr('balance+' . floatval($value))),
				array( 'id = ?' => $transaction->hostId));

			$host->balance = $finalBalance;


			It6_DbTransaction::commit($db);
			It6_DbTransaction::commit($admindb);

			return $finalBalance;

		}

		return null;

		}
		catch (Exception $e) {
				It6_DbTransaction::rollback($db);
				It6_DbTransaction::rollback($admindb);
		}
	}

	protected static function currentBalance($transaction, $type, &$cache = null) {
		$accountType = !empty($transaction->accountType) ? $transaction->accountType : $type->accountType;

		if ( Webservice_TransactionType::ACCOUNT_TYPE_USER == $accountType ) {

			if (is_array($cache) && array_key_exists('user', $cache)) {
				$user = &$cache['user'];
			} else {
				$user = Webservice_User::getById($transaction->userId);
			}

			if ( false == $user ) throw new Exception('Unknown user id.\''.$transaction->userId.'\'');
			return $user->balance;

		} else if ( Webservice_TransactionType::ACCOUNT_TYPE_HOST == $accountType ) {

			if (is_array($cache) && array_key_exists('host', $cache)) {
				$host = &$cache['host'];
			} else {
				$host = Webservice_Host::getById($transaction->hostId);
			}

			if ( false == $host ) throw new Exception('Unknown host id.\''.$transaction->hostId.'\'');
			return $host->balance;

		}
		return null;
	}

	/**
	 * Checks if transaction has fee, if it has then makes extra transaction for fee.
	 * @param struct $transaction
	 * @param struct $type [optional] fetched transaction type data for transaction (will be fetched from DB if not set)
	 * @param boolean $forceBalanceChange [optional] To override balanceChanged flag in transaction type
	 * @param boolean $forceChangingBonus [optional] To be able to change bonus if related transaction changed bonus
	 * @return integer id of created transaction or if non false
	 */
	protected static function makeFee($transaction, $type = null, $forceBalanceChange = null, $forceChangingBonus = null, &$transactionCache = null) {

		It6_Log::info(
			"Making fee for transaction '%transactionId%'...",
			It6_Log::TAG_TRANSACTION,
			array(
				'transactionId' => $transaction['transactionId'],
				'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
				'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
				'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));

		if (!isset($type))
			$type = Webservice_TransactionType::getByIdAndCurrency($transaction->typeId, $transaction->currencyId);
			if ( false == $type )
				throw new Exception('Unknown transaction type id.\''.$transaction->typeId.'\'');
		if (isset($transaction->fee))
			$fee = $transaction->fee;
		else {
			$fee = 0;
			if (isset($type->feeFix))
				$fee += Webservice_Currency::exchangeFromSystem($transaction->currencyId, $type->feeFix);
			if (isset($type->feeRel))
				$fee += $type->feeRel * $transaction->value;
		}
		if (Webservice_TransactionType::HAS_FEE_NEVER == $type->hasFee) {
			if (0 != $fee) {
				throw new It6_XmlRpc_Exception(
					'Transaction must not have fee. transaction: ' . $transaction->transactionId . '; fee: ' . $fee . ';'
				);
			}
			return false;
		}
		else if (Webservice_TransactionType::HAS_FEE_ALWAYS == $type->hasFee) {
			if (0 == $fee) {
				throw new It6_XmlRpc_Exception(
					'Transaction must have non-zero fee. transaction: ' . $transaction->transactionId
				);
			}
		}
		else if (Webservice_TransactionType::HAS_FEE_SOMETIMES == $type->hasFee) {
			if (0 == $fee)
				return false;
		}
		$feeType = Webservice_TransactionType::getByName(Webservice_TransactionType::NAME_FEE_GENERAL);
		if (empty($feeType))
			throw new It6_XmlRpc_Exception('Transaction type not found. name: "' . Webservice_TransactionType::NAME_FEE_GENERAL . '"');

		$ret = static::make(
			array(
				'userId' => !empty($transaction->userId) ? $transaction->userId : null,
				'hostId' => !empty($transaction->hostId) ? $transaction->hostId : null,
				'ticketId' => !empty($transaction->ticketId) ? $transaction->ticketId : null,
				'currencyId' => $transaction->currencyId,
				'accountType' => $transaction->accountType,
				'needConfirm' => $transaction->needConfirm,
				'lateDeposit' => $transaction->lateDeposit,
				'typeId' => $feeType->transactionTypeId,
				'value' => -$fee,
				'from' => empty($transaction->from) ? $type->from : $transaction->from,
				'to' => static::FEE_ACCOUNT,
				'forceBalanceChange' => $forceBalanceChange,
				'forceChangingBonus' => $forceChangingBonus,
			),
			$transactionCache
		);

		return $ret;
	}

	/**
	 * Confirms transaction fee
	 * @param struct $transaction
	 * @param boolean $forceBalanceChange
	 * @param boolean $forceChangingBonus
	 * @returns boolean true on success, false if fee transaction is null
	 */
	protected static function confirmFee($transaction, $forceBalanceChange = null, $forceChangingBonus = null) {

		if ( empty($transaction->feeTransactionId) )
			return false;

		It6_Log::info(
			"Confirming fee for transaction '%transactionId%'...",
			It6_Log::TAG_TRANSACTION,
			array(
				'transactionId' => $transaction['transactionId'],
				'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
				'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
				'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));

		return static::confirm($transaction->feeTransactionId, $forceBalanceChange, $forceChangingBonus);
	}

	/**
	 * Cancels transaction fee
	 * @param struct $transaction
	 * @returns boolean true on success
	 */
	protected static function cancelFee($transaction) {

		if ( empty($transaction->feeTransactionId) )
			return false;

		It6_Log::info(
			"Canceling fee for transaction '%transactionId%'...",
			It6_Log::TAG_TRANSACTION,
			array(
				'transactionId' => $transaction['transactionId'],
				'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
				'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
				'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));

		return static::cancel($transaction->feeTransactionId);
	}

	/**
	 *
	 * @param struct $transaction
	 * @param boolean $deposited TRUE if deposit was made, FALSE otherwise
	 * @param boolean $forceBalanceChange
	 * @param boolean $forceChangingBonus
	 * @returns boolean true on success
	 */
	protected static function depositFee($transaction, $deposited, $forceBalanceChange = null, $forceChangingBonus = null) {
		if ( empty($transaction->feeTransactionId) )
			return  false;

		It6_Log::info(
			"Depositing fee for transaction '%transactionId%'...",
			It6_Log::TAG_TRANSACTION,
			array(
				'transactionId' => $transaction['transactionId'],
				'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
				'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
				'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));

		return static::deposit($transaction->feeTransactionId, $deposited, $forceBalanceChange, $forceChangingBonus);
	}

	/**
	 * Make new transaction. Value of the transaction identifier is ignored and new
	 * is generated.
	 * Required fields in transaction structure:
	 *    typeId/typeName
	 *    value
	 *    currencyId
	 * @param struct $transaction structure of the transaction
	 * @param struct $transactionCache cached data fetched during this transaction
	 *               (passed as parameter, because this function is recursive)
	 * @return integer transaction identifier of the created transaction
	 * @see Entities_Transaction
	 */
	public static function make($transaction, &$transactionCache = null) {

		It6_Log::info(
			"Making transaction...'",
			It6_Log::TAG_TRANSACTION,
			$transaction);

		$admindb = static::getAdminDb();
		$db = static::getMainDb();

		It6_DbTransaction::begin($db);
		It6_DbTransaction::begin($admindb);

		try {
			if (!isset($transactionCache)) {
				$transactionCache = array();
			}
			$data = array();

			$transaction = new It6_ArrayWrapper($transaction);
			if (!isset($transaction->userId) && isset($transaction->userHandle)) {
				$user = It6_Models_User::getDataByHandle($transaction->userHandle, $db);
				if (!empty($user))
					$transaction->userId = $user['id'];
			}

			if ( empty($transaction->typeId) && empty($transaction->typeName) ) {
				throw new Exception('Missing transaction typeId or typeName.');
			}

			if ( empty($transaction->typeId) && !empty($transaction->typeName) ) {
				$transaction->typeId = Webservice_TransactionType::getByName($transaction->typeName);
				if ( empty($transaction->typeId) )
					throw new Exception('Unknown transaction typeName \''.$transaction->typeName.'\'');
				$transaction->typeId = $transaction->typeId['transactionTypeId'];
			}

			if ( empty($transaction->value) && $transaction->typeName != Webservice_TransactionType::NAME_CROWN_TICKET_COLLECT_NOCASH)
				throw new Exception("Missing value.");
			if ( empty($transaction->currencyId) )
				throw new Exception('Missing currencyId.');

			$superAdmin = Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_SUPERADMIN);
			if ($transaction->typeName == Webservice_TransactionType::NAME_BRANCH_TICKET_CANCEL_CASH && !$superAdmin) {
				$cancelTransaction = self::getOneWhere(
					array(
						'typeName = ?' => Webservice_TransactionType::NAME_BRANCH_TICKET_CANCEL_CASH,
						'ticketId = ?' => $transaction->ticketId,
						'value < 0',
					)
				);

				if (!empty($cancelTransaction))
					throw new Exception('This cancel transaction is already maked.');
			}

			$type = Webservice_TransactionType::getByIdAndCurrency($transaction->typeId, $transaction->currencyId);

			if ( false == $type )
				throw new Exception('Invalid transaction type id.\''.$transaction->typeId.'\'');

			// this is bad wrong test, right test is already written in cancelOkTransaction method
			//if ( self::STATUS_OK != $transaction['status'] && !empty($transaction['canceledTransactionId']) ) {
			//	throw new Exception('Invalid transaction status for cancel \''.$transaction['status'].'\'');
			//}

			$valueInCentral = Webservice_Currency::exchangeToSystem(
					$transaction->currencyId,$transaction->value);

			if ( empty($transaction['canceledTransactionId'])  ) {
				if ( isset($type->lowLimit) && $valueInCentral < $type->lowLimit )
					throw new Exception("Value is under low limit ($valueInCentral ($transaction->value) < $type->lowLimit).");

				if ( isset($type->highLimit) && $valueInCentral > $type->highLimit )
					throw new Exception("Value is over high limit ($valueInCentral ($transaction->value) > $type->highLimit (CENTRAL)).");
			}

			if ( empty($transaction->accountType) )
				$transaction->accountType = $type->accountType;

			if ( empty($transaction->needConfirm) )
				$transaction->needConfirm = $type->needConfirm;

			if ( empty($transaction->lateDeposit) )
				$transaction->lateDeposit = $type->lateDeposit;

			//TODO: better way to do this is make all changes in $data using Object names (DB independent)
			//      and then only call fromEntity() just before database query
			$data = static::fromEntity($transaction);
			unset($data[static::$IDENTITY]);

			unset($data['okTime']);
			unset($data['cancelTime']);
			unset($data['deposit_time']);

			$data['account_type'] = $transaction->accountType;
			$data['need_confirm'] = $transaction->needConfirm;
			$data['late_deposit'] = $transaction->lateDeposit;

			if ( !empty($transaction->from) )
				$data['from'] = $transaction->from;
			if ( !empty($transaction->to) )
				$data['to'] = $transaction->to;

			$time = It6_Date::dbNow();
			$data['time'] = $time;

			if ( empty($data['canceledTransactionId']) && $transaction->needConfirm )
				$data['status'] = self::STATUS_PENDING;
			else if ( empty($data['canceledTransactionId']) && $transaction->lateDeposit ) {
				$data['status'] = self::STATUS_PRE_DEPOSIT;
				$data['deposit_time'] = $time;
			}
			else if ( empty($data['status']) ) {
				$data['status'] = self::STATUS_OK;
				$data['okTime'] = $time;
			}

			$data = static::removeTableNames($data);

			unset($data['name']);

			unset($data['balance']);
			$bonusChanged = false;
			if (static::isStatusChangingBalance($transaction, $data['status'], $type)) {
				$data['balance'] = static::changeBalance($transaction, $type, null, 1, $bonusChanged, $transactionCache);
				static::changeUserLimitActualAmount($transaction);
				$data['balance_changed'] = 1;
			}
			else
				$data['balance_changed'] = 0;

			if ( empty($transaction->canceledTransactionId) ) {
				$fee = static::makeFee(
					$transaction, $type, $data['balance_changed'], $bonusChanged, $transactionCache
				);

				if ( false !== $fee ) {
					$data['fee_transaction_id'] = $fee;
				}
			}

			$startBalance = static::currentBalance($transaction, $type, $transactionCache);

			if ( !isset($data['balance']) )
				$data['balance'] = $startBalance;

			$handle = $db->select()
				->from(static::$HANDLE_TABLE,'handle')
				->where('user_id = ?', empty($transaction->userId) ? 0 : $transaction->userId)
				->limit(1)
				->query()->fetchObject();

			if ( empty($handle) ) {
				$data['handle'] = 1;
				$db->insert(
					static::$HANDLE_TABLE,
					array('handle' => $data['handle'] + 1, 'user_id' => empty($transaction->userId) ? 0 : $transaction->userId));
			}
			else {

				$data['handle'] = $handle->handle;

				$db->update(
					static::$HANDLE_TABLE,
					array('handle' => $data['handle'] + 1),
					array('user_id = ?' => empty($transaction->userId) ? 0 : $transaction->userId));
			}

			$data['admin_create'] = (
				!empty($transaction->createAdminId)
				? $transaction->createAdminId
				: Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN)
			);

			$db->insert(static::$TABLE, $data);

			$ret = $db->lastInsertId();

			static::balanceLog($ret, $data['status'], $startBalance, $data['balance'], $time);

			$transaction->transactionId = $ret;

			$data['transactionId'] = $ret;
			$data['typeName'] = $type->name;

			It6_Log::info(
				"Transaction '%transactionId%' ('%typeName%') with amount %value%  made.",
				It6_Log::TAG_TRANSACTION,
				$data
			);

			// TOTO JE BLBE
			It6_DbTransaction::commit($db);
			$dbRollbackDone = true;
			It6_DbTransaction::commit($admindb);
		}
		catch ( Exception $e ) {
			if ( empty($dbRollbackDone) )
				It6_DbTransaction::rollback($db);
			else
				It6_Log::emerg(
					"Transaction with type '%typeName%' commited halfly!!!",
					It6_Log::TAG_TRANSACTION,
					$transaction
				);

			It6_DbTransaction::rollback($admindb);

			It6_Log::notice(
				"Transaction with type '%typeName%' and amount '%value%' failed.",
				It6_Log::TAG_TRANSACTION,
				$transaction
			);
			throw new It6_XmlRpc_Exception("Can't make transaction", 0, $e);
		}

		try {
			if ( !empty($transaction->hostId) ) {
				$branchId = Webservice_Host::getById($transaction->hostId);
				$branchId = $branchId->branchId;
			} else {
				$branchId = null;
			}

			Webservice_Alert::assert(
				'Transaction',
				array('id' => $ret, 'typeId' => $transaction->typeId),
				$transaction->userId,
				$branchId
			);

			Webservice_Alert::assert(
				'TransactionMultiple',
				array('id' => $ret, 'typeId' => $transaction->typeId, 'value' => $transaction->value, 'time' => $time),
				$transaction->userId,
				$branchId
			);

		}
		catch ( Exception $e ) {
			It6_Log::err(
				"Alert sending failed for transaction '%transactionId%'.",
				It6_Log::TAG_ALERT,
				array('transactionId' => $ret)
			);
		}
		return $ret;
	}

	/**
	 * Cancel already made (is in status ok) transaction.
	 * @param integer $transactionId transaction to be canceled
	 */
	public static function cancelOkTransaction($transactionId) {

		It6_Log::info("Canceling ok transaction '%transactionId%'",
			It6_Log::TAG_TRANSACTION,
			array('transactionId' => $transactionId)
		);

		try {
			$transaction = static::getById($transactionId);

			$transaction = new It6_ArrayWrapper($transaction);

			if ( false == $transaction )
				throw new Exception('Unknown transaction id \''.$transactionId.'\'');

			if ( $transaction->status != static::STATUS_OK )
				throw new Exception('Invalid transaction status');

			if ( !empty($transaction->canceledTransactionId) )
				throw new Exception('Cancel transaction can not be canceled');

			$type = Webservice_TransactionType::getByIdAndCurrency($transaction->typeId, $transaction->currencyId);
			if ( false == $type )
				throw new Exception('Unknown transaction type id.\''.$transaction->typeId.'\'');

			$cancelTransactionId = static::make(array(
				'value' => -$transaction->value,
				'typeId' => $transaction->typeId,
				'canceledTransactionId' => $transactionId,
				'from' => empty($transaction->from) ? $type->from : $transaction->from,
				'to' => empty($transaction->to) ? $type->to : $transaction->to,
				'userId' => !empty($transaction->userId) ? $transaction->userId : null,
				'hostId' => !empty($transaction->hostId) ? $transaction->hostId : null,
				'ticketId' => !empty($transaction->ticketId) ? $transaction->ticketId : null,
				'currencyId' => !empty($transaction->currencyId) ? $transaction->currencyId : null,
			));

			It6_Log::info(
				"Transaction '%transactionId%' canceled by transaction '%cancelTransactionId%'",
				It6_Log::TAG_TRANSACTION,
				array(
					'transactionId' => $transactionId,
					'cancelTransactionId' => $cancelTransactionId,
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']
				)
			);

			return $cancelTransactionId;

		} catch ( Excetpion $e ) {
			It6_Log::notice(
				"Transaction '%transactionId%' cancelation by transaction '%cancelTransactionId%' failed",
				It6_Log::TAG_TRANSACTION,
				array(
					'transactionId' => $transactionId,
					'cancelTransactionId' => $cancelTransactionId,
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']
				)
			);
			throw new It6_XmlRpc_Exception("Can not cancel transaction", 0, $e);
		}
	}

	/**
	 * Confirm transaction (transaction that type need confirmation)
	 * @param integer $transactionId identifier of the transaction
	 * @param boolean $forceBalanceChange
	 * @param boolean $forceChangingBonus
	 * @return boolean true on success
	 */
	public static function confirm($transactionId, $forceBalanceChange = null, $forceChangingBonus = null) {
		It6_Log::info("Confirming transaction '%transactionId%'...",
			It6_Log::TAG_TRANSACTION,
			array('transactionId' => $transactionId));

		$db = static::getDb();
		It6_DbTransaction::begin($db);

		try {
			$transaction = static::getById($transactionId);
			if ( false == $transaction )
				throw new Exception('Unknown transaction id.\''.$transactionId.'\'');

			if ( self::STATUS_PENDING != $transaction->status )
				throw new Exception("Transaction status is not 'pending'.");

			$type = Webservice_TransactionType::getByIdAndCurrency($transaction->typeId, $transaction->currencyId);
			if ( false == $type )
				throw new Exception('Unknown transaction type id.\''.$transaction->typeId.'\'');

			$lateDeposit = empty($transaction->lateDeposit)
				? $type->lateDeposit
				: $transaction->lateDeposit;

			if ($lateDeposit) {
				$status = self::STATUS_PRE_DEPOSIT;
				$timeColumn = 'deposit_time';
			}
			else {
				 $status = self::STATUS_OK;
				 $timeColumn = 'okTime';
			}

			$transaction->forceBalanceChange = $forceBalanceChange;
			$transaction->forceChangingBonus = $forceChangingBonus;
			if (static::isStatusChangingBalance($transaction, $status, $type)) {
				$balance = static::changeBalance($transaction, $type, null, 1, $bonusChanged);
				$balanceChanged = 1;
			}
			else {
				$balance = null;
				$balanceChanged = 0;
				$bonusChanged = false;
			}

			static::confirmFee($transaction, $balanceChanged, $bonusChanged);

			$startBalance = static::currentBalance($transaction, $type);

			if (!isset($balance))
				$balance = $startBalance;

			$adminId = (
				!empty($transaction->confirmAdminId)
				? $transaction->confirmAdminId
				: Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN)
			);

			$time = It6_Date::dbNow();
			$data = array(
				'balance' => $balance,
				'status' => $status,
				'admin_confirm' => $adminId,
				$timeColumn => $time,
			);
			if ($balanceChanged)
				$data['balance_changed'] = $balanceChanged;

			$db->update(
				static::$TABLE,
				$data,
				array(static::$IDENTITY .' = ?' => $transactionId));

			static::balanceLog($transactionId, $status, $startBalance, $balance, $time);

			It6_DbTransaction::commit($db);

			$langId = Zend_Registry::get('translate')->getCurrentLangId();
			$aEmailSubject = It6_ArrayWrapper::toNativeArray(array(It6_Models_Translator::get('email_user_withdraw_cash_subject', $langId, $db)));
			$aEmailMsg = It6_ArrayWrapper::toNativeArray(array(It6_Models_Translator::get('email_user_withdraw_cash', $langId, $db)));
			$aSmsMsg = It6_ArrayWrapper::toNativeArray(array(It6_Models_Translator::get('sms_user_withdraw_cash', $langId, $db)));

			$aTransaction = It6_ArrayWrapper::toNativeArray(self::getById($transactionId));

			switch ( $aTransaction['typeName'] ) {
				case Webservice_TransactionType::NAME_USER_WITHDRAW_CASH:

					if ( isset($aTransaction['userId']) ) {
						$aUser = It6_ArrayWrapper::toNativeArray(Webservice_User::getById($aTransaction['userId']));

						if ( !empty($aUser['email']) ) {
							$mail = new Zend_Mail('UTF-8');
							$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
							$mail->addTo(trim($aUser['email']));
							$mail->setSubject($aEmailSubject[0]);
							$mail->setBodyText($aEmailMsg[0]);

							try {
								if ($mail->send()) It6_Log::info('Email was sent to adress: '.$aUser['email']);
							}
							catch (Exception $e) {
								It6_Log::err('Email not be sent. Exception: '.$e->getMessage());
							}
						}

						if ( !empty($aUser['phone']) ) {
							$phone = $aUser['phone'];
							$text = sprintf($aSmsMsg[0]);
							$plainSms = new It6_Sms_PlainSms();

							try {
								if ( $plainSms->setToNumber($phone)->setText($text,It6_Sms_PlainSms::SMS_TYPE_USER_WITHDRAW_CASH)->sendSms() )
									It6_Log::info('SMS sent successfully to number: ' . $phone, It6_Log::TAG_MOBILEM_API);
							}
							catch (Exception $e) {
								It6_Log::err('SMS not sent. Exception: '.$e->getMessage());
							}
						}
					}
				break;
			}

			It6_Log::info(
				"Transaction '%transactionId%' confirmed.",
				It6_Log::TAG_TRANSACTION,
				array(
					'transactionId' => $transaction['transactionId'],
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId'])
				);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
				"Transaction '%transactionId%' confirmation failed.",
				It6_Log::TAG_TRANSACTION,
				$transaction);
			throw new It6_XmlRpc_Exception("Can not confirm transaction", 0, $e);
		}
	}

	/**
	 * Transaction storno (transaction that type need confirmation)
	 * @param integer $transactionId identifier of the transaction
	 * @return boolean true on success
	 */
	public static function cancel($transactionId) {

		It6_Log::info("Canceling transaction '%transactionId%'...",
			It6_Log::TAG_TRANSACTION,
			array('transactionId' => $transactionId));

		$admindb = static::getAdminDb();
		$db = static::getMainDb();
		It6_DbTransaction::begin($db);
		It6_DbTransaction::begin($admindb);
		try {
			$transaction = static::getById($transactionId);
			if ( false == $transaction )
				throw new Exception('Unknown transaction id \''.$transactionId.'\'');

			$type = Webservice_TransactionType::getByIdAndCurrency($transaction->typeId, $transaction->currencyId);
			if ( false == $type )
				throw new Exception('Unknown transaction type id \''.$transaction->typeId.'\'');

			if ( self::STATUS_PENDING != $transaction->status )
				throw new Exception("Transaction status is not 'pending'.");

			self::cancelFee($transaction);

			$startBalance = static::currentBalance($transaction, $type);

			if (!empty($transaction['balanceChanged']))
				$balance = static::changeBalance($transaction, $type, true, -1);
			else
				$balance = $startBalance;
			
			$adminId = (
				!empty($transaction->confirmAdminId)
				? $transaction->confirmAdminId
				: Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN)
			);

			$time = It6_Date::dbNow();
			$db->update(
					static::$TABLE,
					array(
						'balance' => $balance,
						'status' => self::STATUS_CANCELED,
						'admin_confirm' => $adminId,
						'cancelTime' => $time,
						'balance_changed' => 0,
					),
					array(static::$IDENTITY .' = ?' => $transactionId));

			static::balanceLog($transactionId, self::STATUS_CANCELED, $startBalance, $balance, $time);

			It6_DbTransaction::commit($admindb);
			$adminDbCommited = true;
			It6_DbTransaction::commit($db);

			It6_Log::info(
				"Transaction '%transactionId%' canceled.",
				It6_Log::TAG_TRANSACTION,
				array(
					'transactionId' => $transaction['transactionId'],
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));

			return true;
		}
		catch ( Exception $e ) {
			if (empty($adminDbCommited))
				It6_DbTransaction::rollback($admindb);
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
				"Transaction '%transactionId%' cancelation failed.",
				It6_Log::TAG_TRANSACTION,
				$transaction);
			throw new It6_XmlRpc_Exception("Can not cancel transaction", 0, $e);
		}
	}

	/**
	 * Finish transaction with status STATUS_PRE_DEPOSIT (transaction status will become STATUS_OK or STATUS_NO_DEPOSIT)
	 * @param integer $transactionId identifier of the transaction
	 * @param boolean $deposited TRUE if deposit was made, FALSE otherwise
	 * @param boolean $forceBalanceChange
	 * @param boolean $forceChangingBonus
	 * @return boolean true on success
	 */
	public static function deposit($transactionId, $deposited, $forceBalanceChange = null, $forceChangingBonus = null) {
		It6_Log::info("Depositing transaction '%transactionId%' with result " . ($deposited ? 'ok' : 'rejected').'...',
			It6_Log::TAG_TRANSACTION,
			array('transactionId' => $transactionId));

		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$transaction = static::getById($transactionId);
			if ( false == $transaction )
				throw new Exception('Unknown transaction id.\''.$transactionId.'\'');

			$type = Webservice_TransactionType::getByIdAndCurrency($transaction->typeId, $transaction->currencyId);
			if ( false == $type )
				throw new Exception('Unknown transaction type id.\''.$transaction->typeId.'\'');

			if ( self::STATUS_PRE_DEPOSIT != $transaction->status )
				throw new Exception("Transaction status is not 'pre-deposit'.");

			$time = It6_Date::dbNow();

			$transaction->forceBalanceChange = $forceBalanceChange;
			$transaction->forceChangingBonus = $forceChangingBonus;
			$bonusChanged = false;
			if ($deposited ) {
				$status = self::STATUS_OK;
				$timeColumn = 'okTime';
				if (static::isStatusChangingBalance($transaction, $status, $type)) {
					$balance = static::changeBalance($transaction, $type, null, 1, $bonusChanged);
					$balanceChanged = 1;
				}
				else
					$balanceChanged = 0;
			}
			else {
				$status =  self::STATUS_NO_DEPOSIT;
				$timeColumn = 'cancelTime';
				$balance = (
					empty($transaction->balanceChanged)
					? null
					: static::changeBalance($transaction, $type, true, -1)
				);
				$balanceChanged = null;
			}

			static::depositFee($transaction, $deposited, $balanceChanged, $bonusChanged);

			$adminId = (
				!empty($transaction->depositAdminId)
				? $transaction->depositAdminId
				: Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN)
			);

			$startBalance = static::currentBalance($transaction, $type);

			$data = array('status' => $status, 'admin_deposit' => $adminId, $timeColumn => $time);

			if (isset($balance))
				$data['balance'] = $balance;

			$db->update(
					static::$TABLE,
					$data,
					array(static::$IDENTITY . ' = ?' => $transactionId)
			);

			static::balanceLog($transactionId, $status, $startBalance, isset($data['balance']) ? $data['balance'] : $startBalance, $time);

			It6_DbTransaction::commit($db);

			It6_Log::info(
				"Transaction '%transactionId%' deposit result: " . ($deposited ? 'ok' : 'rejected'),
				It6_Log::TAG_TRANSACTION,
				array(
					'transactionId' => $transaction['transactionId'],
					'userId' => empty($transaction['userId']) ? null : $transaction['userId'],
					'hostId' => empty($transaction['hostId']) ? null : $transaction['hostId'],
					'ticketId' => empty($transaction['ticketId']) ? null : $transaction['ticketId']));

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
				"Transaction '%transactionId%' deposit failed, result: " . ($deposited ? 'ok' : 'rejected'),
				It6_Log::TAG_TRANSACTION,
				$transaction);
			throw new It6_XmlRpc_Exception("Cannot update transaction deposit status", 0, $e);
		}
	}

	/**
	 * @param function $rowCallback Anonymous function to be called for every fetched row; one parameter is row data array
	 * @param array|NULL $extensions Standard extensions or NULL
	 * @param array $resultData Resulting data (should be filled by rowCallback), will be postprocessed by extensions if specified
	 * @return integer Number of rows processed
	 */
	private static function dayBookQuery($rowCallback, $extensions = null, &$resultData = null, $getSum = false) {
		$db = static::getDb();
		

		
		$extensions = static::createExtensions($extensions);

		$columns = array(
			'id' => 'transaction_id',
			'name' => 'IF(1<>T.failures_only,T.name,CONCAT(T.name,\'-cancel\'))',
			'noteDaybook' => 'IF(1<>T.failures_only,T.note_daybook,CONCAT(\'STORNO:\',T.note_daybook))',
			'date' => '(IF(1=T.failures_only,'
				. 'CASE WHEN \'no-deposit\'=T.status_for_accounting_str THEN B.deposit_time ELSE B.cancelTime END,'
				. 'CASE WHEN \'pending\'=T.status_for_accounting_str THEN `time` WHEN \'pre-deposit\'=T.status_for_accounting_str THEN B.deposit_time ELSE B.okTime END))',
			'dateChange' => 'okTime',
			'order' => 'T.order',
			'account' => 'IF(NOT(T.order=1 XOR T.inverse_accounting=1),COALESCE(B.`from`,T.from),COALESCE(B.`to`,T.to))',
			'subaccount' => 'IF(NOT(T.order=1 XOR T.inverse_accounting=1),T.from_sub,T.to_sub)',
			'daybookCollection' => 'T.daybook_collection',
			'amount' => 'IF(1=T.inverse_accounting XOR B.canceled_transaction_id IS NOT NULL,-1,1)*IF(1=T.failures_only,-1,1)*ABS(value)',
			'description' => 'IF(1<>T.failures_only AND B.canceled_transaction_id IS NULL,T.name,CONCAT(T.name,\'-cancel\'))',
			'varSymbol' => "B.ticket_id",
			'ticketId' => 'B.ticket_id',
			'ticketHandle' => 'TK.handle',
			'userId' => 'user_id',
			'userHandle' => 'U.handle',
			'hostId' => 'B.host_id',
			'branchHandle' => 'BR.handle',
			'hostName' => 'H.name',
			'currencyCode' => 'C.mena_text',
			'branchName' => 'BR.name',
			'branchHandle' => 'BR.handle',
			'createAdminId' => 'B.admin_create',
			'confirmAdminId' => 'B.admin_confirm',
			'depositAdminId' => 'B.admin_deposit',
			'createAdmin' => 'CONCAT(cra.first_name, \' \', cra.surname)',
			'confirmAdmin' => 'CONCAT(coa.first_name, \' \', coa.surname)',
			'depositAdmin' => 'CONCAT(dea.first_name, \' \', dea.surname)',
		);

		$CONV = array_flip($columns);
		
		//Using custom select with STRAIGHT_JOIN to speed up querry. The MySQL optimizer kept ordering the joins it in a
		//non-optimal manner. Maybe not the cleanest solution, but time is in short supply.
		//If it will cause problems in future the ->straightJoin() call can be removed safely. 
		$select = new It6_Db_Select($db);
		$select->straightJoin()
			->from(array('B' => static::$TABLE), null)
			->join(
				array('C' => Webservice_Currency::$TABLE),
				'B.mena_id = C.mena_id', null)
			->joinLeft(
				array('U' => Webservice_User::$TABLE),
				'B.user_id=U.user_id', null)
			->joinLeft(
				array('TK' => Webservice_Ticket::$TABLE),
				'TK.ticket_id = B.ticket_id', null)
			->joinLeft(
				array('H' => 'vic_admin.'.Webservice_Host::$TABLE),
				'H.id = B.host_id', null)
			->joinLeft(
				array('BR' => 'vic_admin.'.Webservice_Branch::$TABLE),
				'H.branch_id = BR.id', null)
			->join(
				array('T' => new Zend_Db_Expr(
				/*
					"(SELECT id, name, note_daybook,daybook_collection, 0 AS `order`,`from` AS `account`, `from_sub` AS subaccount
						FROM financial_transaction_type
					UNION
						SELECT id, name, note_daybook,daybook_collection, 0, `thru`,`thru_sub`
						FROM financial_transaction_type
						WHERE `thru` IS NOT NULL
					UNION
						SELECT id, name, note_daybook,daybook_collection, 1, `thru`,`thru_sub`
						FROM financial_transaction_type
						WHERE `thru` IS NOT NULL
					UNION
						SELECT id, name, note_daybook,daybook_collection, 1, `to`,`to_sub`
						FROM financial_transaction_type)"
				*/
					"(SELECT 0 AS failures_only, id, name, note_daybook,daybook_collection, 0 AS `order`,`from`,`from_sub`,`to`,`to_sub`, (`status_for_accounting`+0) AS status_for_accounting,status_for_accounting AS status_for_accounting_str,inverse_accounting
						FROM financial_transaction_type
					UNION SELECT 0, id, name, note_daybook,daybook_collection, 1,`from`,`from_sub`,`to`,`to_sub`,(`status_for_accounting`+0) AS status_for_accounting,status_for_accounting AS status_for_accounting_str,inverse_accounting
						FROM financial_transaction_type
					UNION SELECT 1, id, name, note_daybook,daybook_collection, 0,`from`,`from_sub`,`to`,`to_sub`,(`status_for_accounting`+0) AS status_for_accounting,status_for_accounting AS status_for_accounting_str,inverse_accounting
						FROM financial_transaction_type
						WHERE status_for_accounting<>'ok'
					UNION SELECT 1, id, name, note_daybook,daybook_collection, 1,`from`,`from_sub`,`to`,`to_sub`,(`status_for_accounting`+0) AS status_for_accounting,status_for_accounting AS status_for_accounting_str,inverse_accounting
						FROM financial_transaction_type
						WHERE status_for_accounting<>'ok')"
				)),
				'B.type_id = T.id AND ( 0=T.failures_only OR (T.status_for_accounting<0+B.status AND B.status IN (\'cancel\',\'no-deposit\')) )',
				null)
			->joinLeft(
				array('cra' => 'vic_admin.'.Webservice_Admin::$TABLE),
				'B.admin_create = cra.admin_id',
				null)
			->joinLeft(
				array('coa' => 'vic_admin.'.Webservice_Admin::$TABLE),
				'B.admin_confirm = coa.admin_id',
				null)
			->joinLeft(
				array('dea' => 'vic_admin.'.Webservice_Admin::$TABLE),
				'B.admin_deposit = dea.admin_id',
				null)

			->columns($columns)
			->where('0+T.status_for_accounting<=B.status+0');

		//IT6: next three tests are some klingon mess again?
		if ( !empty($accounts) )
			$select = $select->where('account IN (?)',$accounts);
		else
			$select = $select->where('IF(NOT(T.order=1 XOR T.inverse_accounting=1),COALESCE(B.`from`,T.from),COALESCE(B.`to`,T.to)) IS NOT NULL');

		if ( !empty($fromDate) ) {
			$select = $select->where('date >= ?',$fromDate);
		}

		if ( !empty($toDate) ) {
			$select = $select->where('date <= ?',$toDate);
		}

		
		
		$input = array();
		$metadata = array(
				It6_WsExtension_Server_Query::META_COL_CONV
					=> function($_) use ($CONV) { return Webservice_Transaction::convQuery($_,$CONV); },
				It6_WsExtension_Server_Columns::META_CONV
					=> $CONV);

		static::preprocessExtensions($extensions, $input, $metadata, $select);

		$select = $select->order('date');
		$select = $select->order('id');

		$stmt = $select->query();

		$n = 0;
		while ($item = $stmt->fetch()) {
			++$n;
			$rowCallback($item);
		}

		if (isset($resultData))
			static::postprocessExtensions($extensions, $resultData, $metadata, $select);

		if ( $getSum ) {
			$sumSelect = $select
					->reset(Zend_Db_Select::COLUMNS)
					->reset(Zend_Db_Select::ORDER)
					->columns(array('sum' => 'Sum(IF(1=T.inverse_accounting XOR B.canceled_transaction_id IS NOT NULL,-1,1)*IF(1=T.failures_only,-1,1)*ABS(value))'));
			$sum = $sumSelect->query()->fetch();
			$sum = $sum['sum'];
		}
			
		return $getSum ? array($n,$sum) : $n;
	}

	/**
	 * Format account number for accounting outputs
	 * @param integer $account Account for accounting
	 * @return string
	 */
	public static function formatAccountForAccounting($account) {
		static $filter = false;
		if (false === $filter)
			$filter=  new It6_Filter_AccountingAccount();
		return $filter->filter($account);
	}

	public static function exportDayBook($filename, $extensions = null) {
		try {
			$tmpFile = tmpfile();
			$size = 0;
			$callback = function($item) use ($tmpFile, &$size) {
				$formatter = new Zend_Log_Formatter_Simple($item['subaccount']);
				$item['subaccount'] = Webservice_Transaction::formatAccountForAccounting( $formatter->format($item) );
				$item['account'] = Webservice_Transaction::formatAccountForAccounting($item['account']) . $item['subaccount'];
				
				if ( !empty($item['noteDaybook']) ) {
					$formatter = new Zend_Log_Formatter_Simple($item['noteDaybook']);
					$item['noteDaybook'] = $formatter->format($item);
				}
				$date = It6_Date::timestampToHeliosDate( It6_Date::dbDatetimeToTimestamp($item['date']) );
				$line = array(
					'Sbornik'           => $item['daybookCollection'],
					'Ev.číslo'          => $item['id'],
					'Dat. účto'         => $date,
					'Poř.'              => $item['order'],
					'Účet'              => $item['account'],
					'Obrat MD'          => 0 == $item['order'] ? $item['amount'] : '',
					'Obrat Dal'         => 0 == $item['order'] ? '' : $item['amount'],
					'Popis'             => $item['noteDaybook'],
					'Kurs'              => 1,
					'RČ/IČO'            => $item['branchHandle'],
					'Partner'           => It6_Models_Host::ID_INTERNET == $item['hostId'] ? 'Internet('.$item['userId'].')' : $item['hostName'],
					'Kód měny'          => 'CZK',
					'Fáze'              => '2',
					'Stav'              => '0',
					'Druh Data'         => '3',
					'PZ'                => $item['branchHandle'],//It6_Models_Branch::formatHandleForExport($item['branchHandle']),
					'Obrat MD v měně'   => $item['order'] == 0 ? $item['amount'] : 0,
					'Obrat DAL v měně'  => $item['order'] == 1 ? $item['amount'] : 0,
				);
				$line = '"' . implode('";"', $line) . "\"\r\n";
				$size += fwrite($tmpFile, $line);
			};
			$line = '"Sbornik";"Ev.číslo";"Dat. účto";"Poř.";"Účet";"Obrat MD";"Obrat Dal";"Popis";"Kurs";"RČ/IČO";"Partner";'
				. '"Kód měny";"Fáze";"Stav";"Druh Data";"PZ";"Obrat MD v měně";"Obrat DAL v měně"' . "\r\n";
			$size += fwrite($tmpFile, $line);
			static::dayBookQuery($callback, $extensions);
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Length: ' . $size);
			header('Content-Disposition: attachement; filename=' . $filename);
			fseek($tmpFile, 0);
			fpassthru($tmpFile);
			fclose($tmpFile);
			exit; // this function breaks XML-RPC and serves file directly
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('exportDayBook', 0, $e);
		}
	}
	
	public static function getDayBook($getSum, $extensions = null) {
		try {
			$rows = array();
			$rowCallback = function($item) use (&$rows) {
				$formatter = new Zend_Log_Formatter_Simple($item['subaccount']);
				$item['subaccount'] = Webservice_Transaction::formatAccountForAccounting( $formatter->format($item) );
				$item['account'] = Webservice_Transaction::formatAccountForAccounting($item['account']) . $item['subaccount'];
				
				if ( !empty($item['noteDaybook']) ) {
					$formatter = new Zend_Log_Formatter_Simple($item['noteDaybook']);
					$item['noteDaybook'] = $formatter->format($item);
				}
				$rows[] = $item;
			};
			
			$ret = static::dayBookQuery($rowCallback, $extensions, $rows, $getSum);

			return $getSum ? array($rows,$ret[1]) : $rows;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception('getDayBook', 0, $e);
		}
	}

	public static function getAllHistory($extensions = null) {
		try {
			$input = array();

			$extensions = static::createExtensions($extensions);
			$metadata = array(
					It6_WsExtension_Server_Query::META_COL_CONV
						=> get_called_class().'::convQuery',
					It6_WsExtension_Server_Columns::META_CONV
						=> static::$CONV);

			$select = static::defaultQuery(static::getDb()->select());

			$select = $select->join(
					array('bh' => static::$BALANCE_LOG_TABLE),
					'bh.transaction_id = ft.transaction_id',
					array(
						'bhTime'       => 'bh.time',
						'bhStatus'     => 'bh.status',
						'startBalance' => 'start_balance',
						'endBalance'   => 'end_balance'));

			$select->order('bh.time DESC');
			$select->order('bh.id DESC');

			static::preprocessExtensions($extensions, $input, $metadata, $select);

			$output = static::fetchAllEntities($select->query());

			static::postprocessExtensions($extensions, $output, $metadata, $select);

			return $output;
		}
		catch (Exception $e) {
			throw new It6_XmlRpc_Exception(get_called_class().'::getAllHistory(\''.Zend_Json::encode($where).'\',\''.Zend_Json::encode($extensions).'\')', 0, $e);
		}
	}

	protected static function balanceLog($transactionId, $status, $startBalance, $endBalance, $time) {
		if (!isset($startBalance)) {
			It6_Log::info(
				"Transaction not inserted to history table.",
				It6_Log::TAG_TRANSACTION,
				array(
					'transactionId' => $transactionId,
					'status' => $status,
					'startBalance' => $startBalance,
					'endBalance' => $endBalance,
					'time' => $time
				)
			);
			return;
		}

		if (!isset($endBalance)) $endBalance = $startBalance;

		$db = static::getDb();
		$db->insert(
			static::$BALANCE_LOG_TABLE,
			array(
				'transaction_id' => $transactionId,
				'time' => $time,
				'status' => $status,
				'start_balance' => $startBalance,
				'end_balance' => $endBalance,
			)
		);
	}

	/**
	 * Fetch all transactions for banking export
	 * @param unknown_type $extensions
	 * @return array Array of extended structures Entities_Transaction
	 */
	public static function getAllForExport($extensions = null) {
		$result = array();
		$types = array(
			array(Webservice_TransactionType::NAME_USER_WITHDRAW_BANK, 'ok'),
			array(Webservice_TransactionType::NAME_BRANCH_DEPOSIT, 'pre-deposit'),
		);
		$typeStatuses = implode( ",", array_map(function($t) { return "('{$t[0]}','{$t[1]}')"; }, $types) );
		return static::getAllWhere(array('exportDate IS NULL', 'value<>0', "(typeName,status) IN ($typeStatuses)"), $extensions);
		/*
		$ts = static::getAllWhere(array('exportDate IS NULL', "(typeName,status) IN ($typeStatuses)"), $extensions);
		if (!empty($ts) && 0 < count($ts)) {
			$ts = It6_ArrayWrapper::toNativeArray($ts);
			//TODO: filter this and/or something else or nothing?
			$ts = array_filter($ts, function($t) { return (0 != $t['value']); });
			if (0 < count($ts)) {
				$users = array();
				$branches = array();
				foreach ($ts as $t) {
					if (Webservice_TransactionType::NAME_USER_WITHDRAW_BANK == $t['typeName']) {
						if (!empty($t['userId']))
							$users[$t['userId']] = true;
					}
					else if (Webservice_TransactionType::NAME_BRANCH_DEPOSIT == $t['typeName']) {
						if (!empty($t['branchId']))
							$branches[$t['branchId']] = true;
					}
				}
				if (!empty($users)) {
					$userIds = implode(',', array_keys($users));
					$userData = Webservice_User::getAllWhereColumns(
						array("userId IN ($userIds)"),
						array('userId', 'accountPrefix', 'accountNumber', 'bankCode')
					);
					foreach ($userData as $u)
						$users[$u['userId']] = It6_ArrayWrapper::toNativeArray($u);
				}
				if (!empty($branches)) {
					$branchIds = implode(',', array_keys($branches));
					$branchData = Webservice_Branch::getAllWhereColumns(
						array("branchId IN ($branchIds)"),
						array('branchId', 'accountPrefix', 'accountNumber', 'bankCode')
					);
					foreach ($branchData as $b)
						$branches[$b['branchId']] = It6_ArrayWrapper::toNativeArray($b);
				}
				foreach ($ts as $t) {
					if (Webservice_TransactionType::NAME_USER_WITHDRAW_BANK == $t['typeName'] && !empty($t['userId'])) {
						$user = &$users[$t['userId']];
						$user['vsField'] = 'userHandle';
						if (!empty($user['accountNumber'])) {
							$result[] = array_merge($t, $user);
						}
					}
					else if (Webservice_TransactionType::NAME_BRANCH_DEPOSIT == $t['typeName'] && !empty($t['branchId'])) {
						$branch = &$branches[$t['branchId']];
						$user['vsField'] = 'branchHandle';
						if (!empty($branch['accountNumber'])) {
							$result[] = array_merge($t, $branch);
						}
					}
				}
			}
		}
		return $result;
		*/
	}

	/**
	 * Updates export date for given transactions
	 * @param array $ids Array of transaction IDs
	 * @param integer $timestamp UNIX timestamp of export, if empty value is given, current timestamp is used
	 * @return boolean
	 */
	public static function setExported($ids, $timestamp) {
		if (empty($ids))
			return true;
		if (empty($timestamp))
			$timestamp = time();
		try {
			static::getDb()->update(
				static::$TABLE,
				array('export_date' => It6_Date::timestampToDb($timestamp)),
				array('transaction_id IN (?)' => $ids)
			);
			return true;
		}
		catch (Exception $e) {
			throw new It6_XmlRopc_Exception('Export date for transactions not updated');
		}
	}
}