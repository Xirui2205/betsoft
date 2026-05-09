<?php

/**
 * PointsTransaction related static methods
 * @author Pavel Klinger
 * @see Entities_Bet
 *
 */
class Webservice_PointsTransaction extends Webservice_AbstractWebService  {

	const PARAM_BALANCE_LIMIT_TOTAL = 'point-transaction.limit.total';
	const PARAM_BALANCE_LIMIT_DAY_INCOME = 'point-transaction.limit.day-income';
	
	public static $TABLE = "point_transaction";
	public static $TABLE_PREFIX = "ptr";
	public static $HANDLE_TABLE = "point_transaction_handle_sequence";
	public static $TABLE_POINT_ACCOUNT = "point_account";
	public static $TABLE_POINT_TYPE = "point_type";
	public static $ENTITY_NAME = "Entities_PointsTransaction";
	public static $IDENTITY = "id";
	protected static $CONV = array(
		'transaction_id' => 'pointsTransactionId',
		'handle'		=> 'handle',
		'u.user_id' => 'userId',
		'u.handle' => 'userHandle',
		'u.nick' => 'userNick',
		'host_id' => 'hostId',
		'ptr.ticket_id' => 'ticketId',
		't.handle' => 'ticketHandle',
		'value' => 'value',
		'value_before_limitation' => 'valueBeforeLimitation',
		'balance' => 'balance',
		'balance_spend' => 'balanceSpend',
		'balance_get' => 'balanceGet',
		'balance_exchange' => 'balanceExchange',
		'ptr.type_id' => 'typeId',
		'tt.name' => 'typeName',
		'tt.point_type_id' => 'pointTypeId',
		'time' => 'time',
		'DATE(time)' => 'date',
		'ptr.note' => 'notes',
		'limited' => 'limited',
		'pt.name' => 'point_Type',
		'wh.branch_id' => 'branchId',
		'b.handle' => 'branchHandle',
	);

	/**
	 * Returns monpoint transaction by ticket and type
	 * @param integer $ticketId
	 * @return struct
	 */
	public static function getMoneyTicketCreateTransaction($ticketId) {
		return static::getOneWhere(array(
			'ticketId = ?' => $ticketId,
			'typeId = ?' => Webservice_PointsTransactionType::CREATE_MONEY_TICKET));
	}
	
	protected static function defaultJoins($query) {
		return parent::defaultJoins($query)
			->join(
				array('tt' => Webservice_PointsTransactionType::$TABLE),
				static::$TABLE_PREFIX . '.type_id = tt.id',
				null)
			->join(
				array('u' => Webservice_User::$TABLE),
				static::$TABLE_PREFIX . '.user_id = u.user_id',
				null)
			->joinLeft(
				array(Webservice_Ticket::$TABLE_PREFIX => 'vic_main.'.Webservice_Ticket::$TABLE),
				self::$TABLE_PREFIX . '.ticket_id = '.Webservice_Ticket::$TABLE_PREFIX.'.ticket_id',
				null)
                        ->join(
                                array('pt' => Webservice_PointsType::$TABLE),
                                'tt.point_type_id = pt.id',
                                null)
                        ->join(
                                array('wh' => MY_DB. '.' .Webservice_Host::$TABLE),
                                static::$TABLE_PREFIX . '.host_id = wh.id',
                                null)
                        ->join(
                                array('b' => MY_DB. '.' .Webservice_Branch::$TABLE),
                                'wh.branch_id = b.id',
                                null);
	}

	/**
	 * Returns all transactions in the system.
	 * @return array array of transaction structs
	 * @see Entities_PointsTransaction
	 */
	public static function getAll($extensions = null) {
		return parent::getAll($extensions);
	}

	/**
	 * Insert new transaction. Value of the transaction identifier is ignored and new
	 * is generated.
	 * @param struct $transaction structure of the transaction
	 * @return integer transaction identifier of the created transaction
	 * @see Entities_PointsTransaction
	 */
	public static function insert($transaction) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Update transaction.
	 * @param struct $transaction structure of the transaction
	 * @return true on success
	 * @see Entities_PointsTransaction
	 */
	public static function update($transaction) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Delete transaction.
	 * @param struct $transactionId idenetifier of the transaction.
	 * @return true on success
	 * @see Entities_PointsTransaction
	 */
	public static function delete($transactionId) {
		//TODO implement me
		throw new It6_XmlRpc_Exception("Unimplemented");
	}

	/**
	 * Find transaction by given identifier.
	 * @param integer $transactionId identifier of the transaction
	 * @return struct transaction structure
	 * @see Entities_PointsTransaction
	 */
	public static function getById($id, $extensions = null) {
		return parent::getById($id, $extensions);
	}

	/**
	 * Cretates.all points account for the new user
	 * @param integer $userId identifier of the user
	 * @return boolean true on success
	 */
	public static function createPointAccounts($userId) {

		$db = static::getDb();

		It6_DbTransaction::begin($db);
		try {
			$db->query("
				INSERT ".self::$TABLE_POINT_ACCOUNT." (user_id, point_type_id, balance)
					(SELECT '".$userId."' AS `user_id`, `id` AS `point_type-id`, '0' AS `balance` FROM `".self::$TABLE_POINT_TYPE."`)
				");
			It6_DbTransaction::commit($db);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can not create points accounts.", 0, $e);
		}
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
	 * Returns value limits
	 * @param integer $userId
	 * @param integer $pointTypeId
	 */
	public static function getValueLimits($userId, $pointTypeId) {
		$db = static::getDb();
		$balance = $db->select()
			->from(static::$TABLE_POINT_ACCOUNT,array('balance_get','balance'))
			->where('user_id = ?', $userId)
			->where('point_type_id = ?', $pointTypeId)
			->limit(1)
			->query()->fetch();
		$getBalance = $balance['balance_get'];
		$balance = $balance['balance'];
		
		$balanceLimitTotal = Webservice_Parameter::getUserParameter(static::PARAM_BALANCE_LIMIT_TOTAL, $userId);
		
		$value = $balanceLimitTotal - $balance;
		
		if ( $value <= 0 )
			return 0;
		
		list($startTime, $endTime) = It6_Date::todayToDbInterval();

		$startGetBalance = $db->select()
			->from(
				array('t' => static::$TABLE),
					array(
						'startGetBalance' => 'balance_get',
						'value' => 'value',
					)
				)
				->join(
					array('tt' => Webservice_PointsTransactionType::$TABLE),
					'tt.id = t.type_id',
					null
				)
			->where('user_id = ?',$userId)
			->where('tt.kind = ?','get')
			->where('tt.point_type_id = ?', $pointTypeId)
			->where('tt.limited_by_day = 1')
			->where('time >= ?', $startTime)
			->order('time ASC')
			->limit(1)
			->query()->fetch();
		
		$startGetBalance = empty($startGetBalance)
			? $getBalance
			: $startGetBalance['startGetBalance'] - $startGetBalance['value'];
		
		$balanceLimitDayIncome = Webservice_Parameter::getUserParameter(static::PARAM_BALANCE_LIMIT_DAY_INCOME,$userId);
		
		$value2 =  $balanceLimitDayIncome - $getBalance + $startGetBalance;
		
		if ( $value2 <= 0 )
			return 0;
		
		return $value2 < $value ? $value2 : $value;
	}

	/**
	 * Make new point transaction. Value of the points transaction identifier is ignored and new
	 * is generated.
	 * TODO: there must be a way to make transaction with only pointTypeId given instead of typeId (of transaction)
	 * @param struct $transaction structure of the point transaction
	 * @return integer identifier of the created points transaction
	 * @see Entities_PointsTransaction
	 */
	public static function make($transaction) {

		It6_Log::info(
			"Making point transaction...",
			It6_Log::TAG_TRANSACTION,
			$transaction
		);
		
		$db = static::getDb();

		It6_DbTransaction::begin($db);

		try {

			$ret = null;
			
			$transaction = new It6_ArrayWrapper($transaction);
			$transaction->value = round($transaction->value);

			if (!empty($transaction->pointTypeId) && !empty($transaction->typeName)) {
				$type2 = Webservice_PointsTransactionType::getByNameAndPointsTypeId($transaction->typeName, $transaction->pointTypeId);
				if (empty($type2)) {
					throw new It6_XmlRpc_Exception('Unknown point transaction type by name and point type: name='
						 . $transaction->typeName . ' pointTypeId=' . $transaction->pointTypeId
					);
				}
			}
			if (!empty($transaction->typeId)) {
				$type = Webservice_PointsTransactionType::getById($transaction->typeId);
				if (empty($type))
					throw new It6_XmlRpc_Exception ('Unknown type id: \''. $transaction->typeId . '\'');
				if (isset($type2) && $type->pointsTransactionTypeId != $type2->pointsTransactionTypeId)
					throw new It6_XmlRpc_Exception('Inkonsistent transaction type and point type');
				unset($type2);
			}
			else if (!empty($type2)) {
				$type = $type2;
				unset($type2);
			}
			else
				throw new It6_XmlRpc_Exception('Point transaction type and/or point type must be specified');
			$transaction->typeId = $type->pointsTransactionTypeId;

			if ( isset($type->lowLimit) && $transaction->value < $type->lowLimit )
				throw new It6_XmlRpc_Exception("Value is under low limit ($transaction->value < $type->lowLimit).");

			if ( isset($type->highLimit) && $transaction->value > $type->highLimit )
				throw new It6_XmlRpc_Exception("Value is over high limit ($transaction->value < $type->lowLimit).");

			//TODO: better way to do this is make all changes in $data using Object names (DB independent)
			//      and then only call fromEntity() just before database query
			$data = static::fromEntity($transaction);
			unset($data[static::$IDENTITY]);
			unset($data['balance']);
			unset($data['name']); // this piece of sh.t is necessary, it's just design of this DAO
			unset($data['point_type_id']);
			$data['time'] = It6_Date::dbNow();

			$balance = $db->select()
				->from(static::$TABLE_POINT_ACCOUNT)
				->where('user_id = ?', $transaction->userId)
				->where('point_type_id = ?', $type->pointTypeId)
				->limit(1)
				->query()->fetch();
			$getBalance = $balance['balance_get'];
			$spendBalance = $balance['balance_spend'];
			$exchangeBalance = $balance['balance_exchange'];
			$balance = $balance['balance'];


			if ( !$type->debiting && $transaction->value < 0 && $balance + $transaction->value < 0 ) {
				throw new It6_XmlRpc_Exception("Out of user points.");
			}
			
			$value = $transaction->value;
			$limited = 'no';
			if ( $value > 0  ) {
				$balanceLimitTotal = Webservice_Parameter::getUserParameter(static::PARAM_BALANCE_LIMIT_TOTAL, $transaction->userId);
				
				if ( $balance + $value > $balanceLimitTotal ) {
					$limited = 'total';
					$value = $balanceLimitTotal - $balance;
					
					if ( $value > 0 ) {
					
						It6_Log::info(
							"Point transaction limited by total limit. Old value: '%oldValue%', New value: '%newValue%",
							It6_Log::TAG_TRANSACTION,
							array(
								'userId' => $transaction->userId,
								'oldValue' => $transaction->value,
								'newValue' => $value,
								'limit' => $balanceLimitTotal
							)
						);
					}
				}	
				if ( $value > 0 && $type->limitedByDay ) {
						
					list($startTime, $endTime) = It6_Date::todayToDbInterval();
					
					if ( Webservice_PointsTransactionType::KIND_GET == $type->kind ) {
						$startGetBalance = $db->select()
							->from(
								array('t' => static::$TABLE),
								array(
									'startGetBalance' => 'balance_get',
									'value' => 'value',
								)
							)
							->join(
								array('tt' => Webservice_PointsTransactionType::$TABLE),
								'tt.id = t.type_id',
								null
							)
							->where('user_id = ?',$transaction->userId)
							->where('tt.point_type_id = ?', $type->pointTypeId)
							->where('tt.limited_by_day = 1')
							->where('time >= ?', $startTime)
							->order('time ASC')
							->limit(1)
							->query()->fetch();
						
						$startGetBalance = empty($startGetBalance)
											? $getBalance
											: $startGetBalance['startGetBalance'] - $startGetBalance['value'];

						$balanceLimitDayIncome = Webservice_Parameter::getUserParameter(static::PARAM_BALANCE_LIMIT_DAY_INCOME,$transaction->userId);
						
						if ( $getBalance - $startGetBalance + $value > $balanceLimitDayIncome ) {
							$limited = 'day';
							$value =  $balanceLimitDayIncome - $getBalance + $startGetBalance;
							if ( $value > 0 ) {
								It6_Log::info(
									"Point transaction limited by day income limit. Old value: '%oldValue%', New value: '%newValue%",
									It6_Log::TAG_TRANSACTION,
									array(
										'userId' => $transaction->userId,
										'oldValue' => $transaction->value,
										'newValue' => $value,
										'limit' => $balanceLimitDayIncome
									)
								);
							}
						}
							
					}
				}
				
			}
			
			if ( $transaction->value > 0  && $value <= 0 ) {
				switch ( $limited ) {
					case 'day':
						It6_Log::info(
							"Point transaction doesn't made, day income limit is already reached.",
							It6_Log::TAG_TRANSACTION,
							It6_ArrayWrapper::toNativeArray($transaction)
								+ array('limit' => $balanceLimitDayIncome, 'dayGetBalance' => $getBalance - $startGetBalance, 'startTime' => $startTime)
						);
						break;
					case 'all':
						It6_Log::info(
							"Point transaction doesn't made, total limit is already reached.",
							It6_Log::TAG_TRANSACTION,
							It6_ArrayWrapper::toNativeArray($transaction)
								+ array('limit' => $balanceLimitTotal, 'balance' => $balance)
						);
						break;
				}
				
			}
			else {
				$newBalance = $balance + $value;
	
				if ( Webservice_PointsTransactionType::KIND_GET == $type->kind && $type->limitedByDay )
					$newGetBalance = $getBalance + $value;
				else
					$newGetBalance = $getBalance;
	
				if ( Webservice_PointsTransactionType::KIND_SPEND == $type->kind )
					$newSpendBalance = $spendBalance - $value;
				else
					$newSpendBalance = $spendBalance;
	
				if ( Webservice_PointsTransactionType::KIND_EXCHANGE == $type->kind )
					$newExchangeBalance = $exchangeBalance - $value;
				else
					$newExchangeBalance = $exchangeBalance;
	
				if ( $newBalance > MAX_BALANCE || $newBalance < MIN_BALANCE ||
						$newSpendBalance > MAX_BALANCE || $newSpendBalance < MIN_BALANCE ||
						$newGetBalance > MAX_BALANCE || $newGetBalance < MIN_BALANCE ||
						$newExchangeBalance > MAX_BALANCE || $newExchangeBalance < MIN_BALANCE )
	
					throw new It6_XmlRpc_Exception("MAX or MIN possible balance overflowed.");
	
				$db->update(
					static::$TABLE_POINT_ACCOUNT,
					array(
						'balance'    => $newBalance,
						'balance_get' => $newGetBalance,
						'balance_spend' => $newSpendBalance,
						'balance_exchange' => $newExchangeBalance),
					array(
						'user_id = ?' => $transaction->userId,
						'point_type_id = ?' => $type->pointTypeId));
	
				$data['balance'] = $newBalance;
				$data['balance_get'] = $newGetBalance;
				$data['balance_spend'] = $newSpendBalance;
				$data['balance_exchange'] = $newExchangeBalance;
				
				$data['limited'] = $limited;
				
				if ( $limited != 'no' ) {
					$data['value_before_limitation'] = $transaction->value;
					$data['value'] = $value;
				}
	
				if ( null == $data['balance'] ) unset($data['balance']);
	
				$data = static::removeTableNames($data);
	
				$handle = $db->select()
					->from(static::$HANDLE_TABLE,'handle')
					->where('user_id = ?', empty($transaction->userId) ? 0 : $transaction->userId)
					->limit(1)
					->query()->fetchObject();
	
				if ( empty($handle) ) {
					$data['handle'] = 1;
					$db->insert(
						static::$HANDLE_TABLE,
						array('handle' => $data['handle'] + 1, 'user_id' => $transaction->userId)
						);
				}
				else {
	
					$data['handle'] = $handle->handle;
	
					$db->update(
						static::$HANDLE_TABLE,
						array('handle' => $data['handle'] + 1),
						array('user_id = ?' => empty($transaction->userId) ? 0 : $transaction->userId));
				}
	
				$db->insert(static::$TABLE, $data);
	
				$ret = $db->lastInsertId();
				$data['id'] = $ret;
				$data['typeName'] = $type->name;
	
				It6_GlobalCache_Invalidator::PointsTransaction_changeBalance($transaction->userId);

				It6_Log::info(
					"Point transaction '%id%' ('%typeName%') with amount %value%  made.",
					It6_Log::TAG_TRANSACTION,
					$data);
			}

			It6_DbTransaction::commit($db);

			return $ret;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::notice(
				"Point transaction '%id%' ('%typeName%') with amount %value%  failed.",
				It6_Log::TAG_TRANSACTION,
				$data
			);
			throw new It6_XmlRpc_Exception("Can not make point transaction.", 0, $e);
		}
	}

}
