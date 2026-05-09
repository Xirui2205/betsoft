<?php

/**
 * Livebetting related static methods.
 * @author Pavel Klinger
 * @see Entities_TicketLive
 */
class Webservice_Livebetting extends Webservice_AbstractWebService {

	const STATUS_NEW = 'new';
	const STATUS_PAID = 'paid';
	const STATUS_CANCELED = 'canceled';
	const STATUS_RESETTLED = 'resettled';
	const STATUS_WINING = 'wining';
	const STATUS_UNKNOWN = 'unknown';
	const STATUS_CONFIRM_CREATION = 'confirm';

	const TICKET_STATE_OPEN = 1;
	const TICKET_STATE_WIN = 2;
	const TICKET_STATE_LOSS = 3;
	const TICKET_STATE_CANCELED = 4;
	const TICKET_STATE_RESETTLED = 5;
	const TICKET_STATE_WINING = 6;

	const TYPE_COMBI = 'kombi';
	const TYPE_SIMPLE = 'simple';

	public static $ENTITY_NAME	= 'Entities_TicketLive';
	public static $TABLE = 'ticket_live';
	public static $TABLE_BET = 'ticket_live_bet';
	public static $TABLE_TRANSACTION = 'ticket_live_transaction';
	public static $TABLE_PREFIX	= 'tl';
	public static $IDENTITY = 'id';

	public static $CONV = array(
		'id'             => 'id',
		'id_live'        => 'idLive',
		'handle'         => 'handle',
		'user_id'        => 'userId',
		'stake'          => 'stake',
		'rate'           => 'rate',
		'win'            => 'win',
		'status'         => 'status',
		'is_loss'        => 'isLoss',
		'type'           => 'type',
		'time_created'   => 'timeCreated',
		'time_paid'      => 'timePaid',
		'time_canceled'  => 'timeCanceled',
		'time_resettled' => 'timeResettled',
		'confirmed'      => 'confirmed'
	);

	/**
	 * Returns client (user) session status. Statuses are:
	 * <table>
	 * <tr><td>0</td><td>not loged</td></tr>
	 * <tr><td>2</td><td>loged and ok</td></tr>
	 * <tr><td>3</td><td>just loged and ok</td></tr>
	 * <tr><td>1</td><td>not logged session expiredk</td></tr>
	 * <tr><td>4/td><td>not loged, special status</td></tr>
	 * <tr><td>5/td><td>not loged, blocked account</td></tr>
	 * </table>
	 * @param string $sessionId
	 * @return array|boolean informations about user false if not found
	 */
	public static function getUserStatus($sessionId) {
		$session = Webservice_Session::getByLiveSession($sessionId);

		$db = static::getDb();

		if ( empty($session) || empty($session->user_id) ) {
			It6_Log::notice(
				"Live user not loged in, missing session.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('session' => $session));
			return false;
		}
		$user = Webservice_User::getById($session->user_id);
		if ( empty($user) ) {
			It6_Log::notice(
				"Live user not loged in, unknown user.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('session' => $session));
			return false;
		}
		Webservice_Session::updateWebSessionTimestamp($sessionId);
		$currencyId = It6_Models_User::get($session->user_id, 'currencyId', $db);
		$ret = array(
				'userId' => $user->userId,
				'currencyId' => $currencyId,
				'balance' => $user->balance,
				'nick' => $user->firstName.' '.$user->lastName.' ('.$user->username.')',
				'status' => $session->status,
			);
		if ( in_array($session->status, array(2,3)) ) {
			It6_Log::info(
				"Live user loged in.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('session' => $session, 'return' => $ret));
		}
		else {
			It6_Log::notice(
				"Live user not loged in.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('session' => $session, 'return' => $ret));
		}
		return $ret;
	}

	private static function makeComplete(&$ticket) {
		if (empty($ticket))
			return;
		$more = It6_ArrayWrapper::isArray($ticket);
		if (!$more)
			$ticket = array($ticket);
		$tickets = array();
		foreach ($ticket as &$_ticket)
			$tickets[ $_ticket['id'] ] = &$_ticket;

		$db = static::getDb();
		$rows = $db->select()
			->from(
				static::$TABLE_BET, 
				array(
					'ticketId' => 'ticket_live_id',
					'id' => 'id',
					'players' => 'players',
					'tip' => 'tip',
					'sport' => 'sport',
					'market' => 'market',
					'resultValid' => 'result_valid',
					'result' => 'result',
					'rate' => 'rate',
					'canceled' => 'canceled'))
			->where('ticket_live_id IN (?)', array_keys($tickets))
			->query()
			->fetchAll();
		foreach ($rows as $row) {
			$_ticket = &$tickets[ $row['ticketId'] ];
			unset($row['ticketId']);
			if (!isset($_ticket['tips']))
				$_ticket['tips'] = array($row);
			else
				$_ticket['tips'][] = $row;
		}
		foreach ($tickets as &$_ticket) {
			if (self::STATUS_CANCELED == $_ticket['status']) {
				$stateNo = 4;
				$won = $_ticket['stake'];
				$paidOut = false;
			}
			else {
				$paidOut = (self::STATUS_PAID == $_ticket['status']);
				$wining = (self::STATUS_WINING == $_ticket['status']);
				//$loss = false;
				//foreach ($_ticket['tips'] as $tip) {
				//	$result = static::betWins($tip);
				//	if (null === $result)
				//		$open = true;
				//	else if (false === $result)
				//		$loss = true;
				//}
				$won = 0;
				if (!$paidOut) {
					$stateNo = $wining ? '1w' : '1';
				}
				else {
					$stateNo = ($_ticket['isLoss'] ? 3 : 2);
					if (!$_ticket['isLoss'])
						$won = $_ticket['win'];
				}
			}

			$_ticket['stateNo'] = $stateNo;
			$_ticket['state'] = 'ticket_state_' . $stateNo;
			$_ticket['won'] = $won;
			$_ticket['paidOut'] = $paidOut;
		}
		if (!$more)
			$ticket = $ticket[0];
	}

	private static function betWins($tip) {
		if (empty($tip['resultValid']))
			return null;
		$results = explode(',', $tip['result']);
		foreach ($results as $result) {
			$result = trim($result);
			if ($result == $tip['tip'])
				return true;
		}
		return false;
	}

	public static function fetchAllEntities($query, $columns = null) {
		$rows = $query->fetchAll();
		static::makeComplete($rows);
		$ret = array();
		foreach ($rows as $row)
			$ret[] = static::toEntity($row, $columns);
		return $ret;
	}

//	public static function toEntities($dbArray, $columns=null, $options=false) {
//		static::completeBets($dbArray);
//		return parent::toEntities($dbArray);
//	}

	/**
	 * Returns all tickets in the system with the given ticket state.
	 * @param integer $state describes the ticket state (0:open, 1:win, 2:loss, 3:canceled)
	 * @return array array of the ticket structures
	 * @see Entities_Ticket
	 */
	public static function getAllByState(array $states=array(), array $where = array(), $extensions = null) {
		if(in_array(self::TICKET_STATE_OPEN, $states))
			$wheres[] = array(
				'status'	=> self::STATUS_NEW,
			);
		if(in_array(self::TICKET_STATE_WIN, $states)) {
			$wheres[] = array(
				'status'	=> self::STATUS_PAID,
				'isLoss'	=> '0'
			);
		}
		if(in_array(self::TICKET_STATE_LOSS, $states)) {
			$wheres[] = array(
				'status'	=> self::STATUS_PAID,
				'isLoss'	=> '1'
			);
		}
		if(in_array(self::TICKET_STATE_CANCELED, $states)) {
			$wheres[] = array(
				'status' => self::STATUS_CANCELED
			);
		}
		if(in_array(self::TICKET_STATE_RESETTLED, $states)) {
			$wheres[] = array(
				'status' => self::STATUS_RESETTLED
			);
		}
		if(in_array(self::TICKET_STATE_WINING, $states)) {
			$wheres[] = array(
				'status'	=> self::STATUS_WINING,
			);
		}

		$whereSql = '';
		$j = 1;
		foreach($wheres as $whereState) {
			$i = 1;
			$whereSql .= '(';
			foreach($whereState as $whereCol => $whereVal) {
				$whereSql .= Zend_Registry::get('db')->quoteInto('('.$whereCol.' = ?)', $whereVal);
				if($i != count($whereState))
					$whereSql .= ' AND ';
				$i++;
			}$whereSql .= ')';
			if($j != count($wheres))
				$whereSql .= ' OR ';
			$j++;
		}

		$where[] = $whereSql;

		return parent::getAllWhere($where, $extensions);
	}

	/**
	 * @param array $where
	 * @param array $order
	 * @return array Array of Entity_Livebetting
	 */
	public static function getAllWhereOrder($where, $order, $extensions = null) {
		return parent::getAllWhereOrder($where, $order, $extensions);
	}

	/**
	 * Find live ticket by given live id
	 * @param integer $liveId
	 * @return struct ticket structure
	 */
	public static function getByIdLive($liveId, $extensions = null) {
		return static::getOneBy($liveId, 'idLive', $extensions);
	}

	/**
	 * Find live ticket by given live id
	 * @param integer $liveId
	 * @return struct ticket structure
	 */
	public static function getById($id, $extensions = null) {
		return parent::getById($id, $extensions);
	}

	/**
	 * Find ticket by given handle
	 * @param string $ticketHandle
	 * @return struct ticket structure
	 */
	public static function getByHandle($ticketHandle, $extensions = null) {
		if (!It6_Validate_NineDigitHandle::isValidString($ticketHandle))
			return null;
		It6_NineDigitHandle::fixHandle($ticketHandle);
		return static::getOneBy($ticketHandle, 'handle', $extensions);
	}

	/**
	 * Find ticket by given handle
	 * @param string $ticketHandle
	 * @return struct ticket structure
	 */
	public static function getByHandleAndUser($handle, $userId, $extensions = null) {
		if (!It6_Validate_NineDigitHandle::isValidString($handle))
			return null;
		It6_NineDigitHandle::fixHandle($handle);
		return static::getOneWhere(array('handle=?' => $handle, 'userId=?' => $userId), $extensions);
	}

	/**
	 * Creates new live ticket. Generates and return id and handle for it.
	 * Status is automtacily set to 'new'. Other times then 'timecreated' are ignored.
	 * Concerning bets: 'canceled', 'result_valid', 'restult' are ignored 
	 * and set to empty or false values.
	 * @param struct $ticket struct of Entities_TicketLive
	 * @throws It6_XmlRpc_Exception On failure
	 * @return struct Structure with two members. 'id' is unique private ideitifier of created the ticket. 'handle' is unique public identifier of created ticket.
	 * @see Entities_TicketLive
	 */
	public static function createTicket($ticket) {
		if ( defined('BETTING_FORBIDDEN') && 1 == BETTING_FORBIDDEN )
			throw new Exception('Betting forbidden.');
		
		$ticket = new It6_ArrayWrapper($ticket);

		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			if ( empty($ticket->idLive) )
				throw new Exception('Missing idLive');
			if ( empty($ticket->userId) )
				throw new Exception('Missing userId');
			if ( empty($ticket->rate) )
				throw new Exception('Missing rate');
			if ( empty($ticket->win) )
				throw new Exception('Missing win');
			if ( empty($ticket->stake) )
				throw new Exception('Missing stake');
			if ( empty($ticket->type) )
				throw new Exception('Missing type');
			if ( !in_array($ticket->type, array(static::TYPE_SIMPLE, static::TYPE_COMBI) ) ) 
				throw new Exception("Unknown type: '{$ticket->type}'");
			if ( empty($ticket->timeCreated) )
				throw new Exception('Missing time created');
			if ( empty($ticket->bets) )
				throw new Exception('Missing bets');

			$ticketId = It6_Models_TicketSequence::nextId($db);
			$ticketHandle = It6_NineDigitHandle::makeHandle($ticketId, $db);

			$data = array(
				'id'            => $ticketId,
				'id_live'       => $ticket->idLive,
				'handle'        => $ticketHandle,
				'user_id'       => $ticket->userId,
				'stake'         => $ticket->stake,
				'rate'          => $ticket->rate,
				'win'           => $ticket->win,
				'status'        => static::STATUS_NEW,
				'type'          => $ticket->type,
				'time_created'  => $ticket->timeCreated,
				'confirmed'     => false
			);

			$db->insert(static::$TABLE, $data);

			foreach ( $ticket->bets as $bet ) {
				$data = array(
					'id'             => $bet->id,
					'ticket_live_id' => $ticketId,
					'players'        => $bet->players,
					'tip'            => $bet->tip,
					'sport'          => $bet->sport,
					'market'         => $bet->market,
					'result_valid'   => 0,
					'result'         => '',
					'rate'           => $bet->rate,
					'canceled'       => 0);

				$db->insert(static::$TABLE_BET, $data);
			}

			$currencyId = It6_Models_User::get($ticket->userId, 'currencyId', $db);

			Webservice_Transaction::make(array(
				'value' => -$ticket->stake,
				'userId' => $ticket->userId,
				'hostId' => It6_Models_Host::ID_INTERNET,
				'ticketId' => $ticketId,
				'typeName' => Webservice_TransactionType::NAME_USER_TICKET_CREATE,
				'currencyId' => $currencyId));

			$logData = array(
				'userId' => $ticket->userId,
				'ticketId' => $ticketId,
				'ticket' => serialize($ticket)
			);
			if (It6_Models_Ticket::TYPE_SIMPLE == $ticket->type)
				$logData['simpleTicketBet'] = $ticket->bets;
			It6_Log::info(
				'Live ticket created',
				It6_Log::TAG_USER_OPERATION,
				$logData
			);

			//Webservice_Alert::assert('TicketCreated',
			//	array('ticketId' => $ticketId), $ticket->userId, It6_Models_Branch::ID_INTERNET);

			It6_DbTransaction::commit($db);
			return array('id' => $ticketId, 'handle' => $ticketHandle);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't insert live ticket:", 0, $e);
		}
		
	}

	/**
	 * Changes ticket status and other variable properties.
	 * Only values that are changing should be set.
	 * @param array $transactionObjects structure of two members: 'ticket' (Entities_TicketLive object) and 'transactionId' (string)
	 * @return array array of transaction results
	 * @see Entities_TicketLive
	 */
	public static function changeTicketStatus($transactionObjects) {
		
		$ret = array();
		$db = static::getDb();
		
		
		
		It6_DbTransaction::begin($db);
		try {
			foreach ($transactionObjects as $tO ) {
				if ( empty($tO['transactionId']) )
					throw new Exception('Missing transactionId.');
				
				if (static::isTransactionAlreadyDone($tO['transactionId']) ) {
					It6_Log::notice("Transaction '%transactionId%' already commited",It6_Log::TAG_DEFAULT,$tO);
					$ret[] = array(
						'transactionId' => $tO['transactionId'],
						'message' => 'Transaction already commited.',
						'success' => true);
					continue;
				}
	
				$db = static::getDb();
				It6_DbTransaction::begin($db);
				try {
					$ticketNew = new It6_ArrayWrapper($tO['ticket']);
					if ( empty($ticketNew->id) )
						throw new It6_XmlRpc_Exception("Missing ticket live id.");
					$ticketLiveId = $ticketNew->id;
	
					$ticketOld = static::getByIdLive($ticketLiveId);

					
					if ( empty($ticketOld) )
						throw new Exception("Unknown ticket id '$ticketLiveId'."); 

					$ticketOld = new It6_ArrayWrapper($ticketOld);
					$ticketId = $ticketOld->id;
					if ( empty($ticketOld) )
						throw new Exception('Unknown ticket live id.');
	
					if ( !empty($ticketNew->status) && $ticketOld->status != $ticketNew->status ) {
						switch( $ticketNew->status ) {
						case static::STATUS_PAID:
							if ( $ticketNew->isLoss && empty($ticketNew->timePaid) )
								$ticketNew->timePaid = It6_Date::dbNow();

							if ( empty($ticketNew->timePaid) )
								throw new Exception('Missing paid time.');
							$ticketOld->win = $ticketNew->win;
							static::payoutTicket($ticketId, $ticketNew->timePaid, $ticketNew->isLoss, $ticketOld);
							break;
						case static::STATUS_WINING:
							static::winingTicket($ticketId, $ticketOld);
							break;
						case static::STATUS_CANCELED:
							if ( empty($ticketNew->timeCanceled) )
								throw new Exception('Missing canceled time.');
							static::cancelTicket($ticketId, $ticketNew->timeCanceled, $ticketOld);
							break;
						case static::STATUS_RESETTLED:
							if ( empty($ticketNew->timeResettled) )
								throw new Exception('Missing resettled time.');
							static::resettleTicket($ticketId, $ticketNew->timeResettled, $ticketOld);
							break;
						case static::STATUS_NEW:
							static::renewCanceledTicket($ticketId, $ticketOld);
							$ticketOld->status = static::STATUS_NEW;
							break;
						case static::STATUS_CONFIRM_CREATION:
							static::confirmTicketCreation($ticketId);
							break;
						default:
							throw new Exception("Uknknown ticket status: '{$ticketNew->status}'");
						}
					}
	
					if ( !empty($ticketNew->bets) ) {
						$cancelBets = array();
						$renewBets = array();
		
						foreach ( $ticketNew->bets as $bet ) {
							$betId = $bet->id;
							if ( isset($bet->canceled) && !empty($bet->canceled) )
								$cancelBets[] = $betId;
							else if ( isset($bet->canceled) )
								$renewBets[] = $betId;
	
							if ( isset($bet->resultValid) && (!empty($bet->result) || 0 == $bet->result)  )
								static::setBetResult($ticketId, $betId, $bet->result);
							else if ( isset($bet->resultValid) && empty($bet->resultValid) )
								static::invalidateBetResult($ticketId, $betId);
						}
	
						if ( !empty($cancelBets) )
							static::cancelBets(
								$ticketId, $cancelBets, 
								$ticketNew->win, $ticketNew->rate, $ticketOld);
	
						if ( !empty($renewBets) )
							static::renewCanceledBets(
								$ticketId, $renewBets, 
								$ticketNew->win, $ticketNew->rate, $ticketOld);
					}
	
					static::setTransactionDone($tO['transactionId']);
					$ret[] = array(
						'transactionId' => $tO['transactionId'],
						'success' => true);
					It6_DbTransaction::commit($db);
				}
				catch ( Exception $e ) {
					It6_DbTransaction::rollback($db);
	
					$ret[] = array(
						'transactionId' => $tO['transactionId'],
						'success' => defined(LIVE_IGNORE_CHANGE_STATUS_ERRORS) && LIVE_IGNORE_CHANGE_STATUS_ERRORS,
						'message' => $e->getMessage());
	
					It6_Log::notice(
						"Can not change status of the ticket '%id%'",
						It6_Log::TAG_LIVEBET_OPERATION,
						$ticketNew, $e);

					if ( !defined(LIVE_IGNORE_CHANGE_STATUS_ERRORS) || !LIVE_IGNORE_CHANGE_STATUS_ERRORS )                                                                                                    
						throw new Exception($e);
					else
						static::setTransactionDone($tO['transactionId']);
				}
			}
			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			It6_Log::err($e);
		}
		return $ret;
	}
	
	/**
	 * Payout live ticket 
	 * @param integer $ticketId
	 * @param string $time
	 * @param boolean $isLoss
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function payoutTicket($ticketId, $time, $isLoss = false, $ticket = null) {
		if ( empty($ticket) ) {
			$ticket = static::getById($ticketId);
			if ( empty($ticket) )
				throw new Exception('Unknown ticket id.');
			$ticket = new It6_ArrayWrapper($ticket);
		}
		
		if ( !in_array($ticket->status,
				array(static::STATUS_NEW, static::STATUS_RESETTLED, static::STATUS_WINING)) ) {

			throw new It6_XmlRpc_Exception( "Can't payout live ticket: Status of the ticket is: '{$ticket->status}'" ); 
		}
		
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE,
				array(
					'status' => static::STATUS_PAID,
					'is_loss' => $isLoss,
					'time_paid' => $time,
				),
				array('id = ?' => $ticketId));

			if ( empty($isLoss) ) {
				$currencyId = It6_Models_User::get($ticket->userId, 'currencyId', $db);
			
				Webservice_Transaction::make(array(
						'ticketId' => $ticketId,
						'value' => $ticket->win,
						'userId' => $ticket->userId,
						'currencyId' => $currencyId,
						'hostId' => It6_Models_Host::ID_INTERNET,
						'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_PAYOUT));

				Webservice_Transaction::make(array(
						'ticketId' => $ticketId,
						'value' => $ticket->win,
						'userId' => $ticket->userId,
						'currencyId' => $currencyId,
						'hostId' => It6_Models_Host::ID_INTERNET,
						'typeName' => Webservice_TransactionType::NAME_USER_TICKET_COLLECT_NOCASH));

				It6_Log::info(
					"Live ticket '%ticket%' was paid out.",
					It6_Log::TAG_LIVEBET_OPERATION,
					array('ticket' => $ticketId));

			}
			else {
				It6_Log::info(
					"Live ticket '%ticket%' was paid out as lost.",
					It6_Log::TAG_LIVEBET_OPERATION,
					array('ticket' => $ticketId));
			}
			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't payout live ticket '$ticketId'", 0, $e);
		}
	}

	/**
	 * Confirm ticket creation 
	 * @param integer $ticketId
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function confirmTicketCreation($ticketId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE,
				array(
					'confirmed' => true,
				),
				array('id = ?' => $ticketId));

			It6_Log::info(
				"Live ticket '%ticket%' creation was confirmed.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('ticket' => $ticketId));

			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't confirm live ticket '$ticketId'", 0, $e);
		}
		return true;
	}
	
	/**
	 * Mark ticket as wining 
	 * @param integer $ticketId
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function winingTicket($ticketId, $ticket = null) {
		if ( empty($ticket) ) {
			$ticket = static::getById($ticketId);
			if ( empty($ticket) )
				throw new Exception('Unknown ticket id.');
			$ticket = new It6_ArrayWrapper($ticket);
		}
		
		if ( !in_array($ticket->status,
				array(static::STATUS_NEW, static::STATUS_RESETTLED, static::STATUS_WINING)) ) {

			throw new It6_XmlRpc_Exception( "Can't mark ticket as wining: Status of the ticket is: '{$ticket->status}'" ); 
		}
		
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE,
				array('status' => static::STATUS_WINING),
				array('id = ?' => $ticketId));
			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't mark live ticket '$ticketId' as wining", 0, $e);
		}
	}

	/**
	 * Cancel new ticket 
	 * @param integer $ticketId
	 * @param string $time time of cancelation
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function cancelTicket($ticketId, $time, $ticket = null) {
		if ( empty($ticket) ) {
			$ticket = static::getById($ticketId);
			if ( empty($ticket) )
				throw new It6_XmlRpc_Exception('Unknown ticket id.');
			$ticket = new It6_ArrayWrapper($ticket);
		}
		
		if ( static::STATUS_CANCELED == $ticket->status )
			return true;

		if ( !in_array($ticket->status,
				array(static::STATUS_NEW, static::STATUS_RESETTLED) ) ) {

			throw new It6_XmlRpc_Exception( "Can't cancel live ticket: Status of the ticket is: '{$ticket->status}'" ); 
		}

		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE,
				array(
					'status' => static::STATUS_CANCELED,
					'is_loss' => 0,
					'time_canceled' => $time,
				),
				array('id = ?' => $ticketId));

			$currencyId = It6_Models_User::get($ticket->userId, 'currencyId', $db);
			Webservice_Transaction::make(array(
					'ticketId' => $ticketId,
					'value' => $ticket->stake,
					'userId' => $ticket->userId,
					'currencyId' => $currencyId,
					'hostId' => It6_Models_Host::ID_INTERNET,
					'typeName' => Webservice_TransactionType::NAME_USER_TICKET_CANCEL));

			It6_Log::info(
				"Live ticket '%ticket%' was canceled.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('ticket' => $ticketId));
			
			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't cancel live ticket '$ticketId'", 0, $e);
		}
	}
	
	public static function cancelNotConfirmedTicket($ticketId) {
		
		$ticket = static::getById($ticketId);
		
		if ( !empty($ticket->confirmed) )
			throw new It6_XmlRpc_Exception("Can't cancel not confirmed live ticket '$ticketId' becaouse is confirmed.", 0, $e);
			
		static::cancelTicket($ticketId,It6_Date::dbNow());
	}
	
	/**
	 * Renew canceled ticket 
	 * @param integer $ticketId
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function renewCanceledTicket($ticketId, $ticket = null) {
		if ( empty($ticket) ) {
			$ticket = static::getById($ticketId);
			if ( empty($ticket) )
				throw new Exception('Unknown ticket id.');
			$ticket = new It6_ArrayWrapper($ticket);
		}
		
		//if ( $ticket->status == static::STATUS_CANCELED )
		//	throw new It6_XmlRpc_Exception( "Can't renew live ticket: Status of the ticket is: '{$ticket->status}'" ); 

		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE,
				array('status' => static::STATUS_NEW, 'is_loss' => 0),
				array('id = ?' => $ticketId));

			$currencyId = It6_Models_User::get($ticket->userId, 'currencyId', $db);
			Webservice_Transaction::make(array(
					'ticketId' => $ticketId,
					'value' => -$ticket->stake,
					'userId' => $ticket->userId,
					'currencyId' => $currencyId,
					'hostId' => It6_Models_Host::ID_INTERNET,
					'typeName' => Webservice_TransactionType::NAME_USER_TICKET_RENEW_CANCELED));

			It6_Log::info(
				"Live ticket '%ticket%' was renewed (canceled -> new).",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('ticket' => $ticketId));

			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't renew live ticket '$ticketId'", 0, $e);
		}
	}
	
	/**
	 * Cancel payout of live ticket 
	 * @param integer $ticketId
	 * @param string $time
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function resettleTicket($ticketId, $time, $ticket = null) {
		if ( empty($ticket) ) {
			$ticket = static::getById($ticketId);
			if ( empty($ticket) )
				throw new It6_XmlRpc_Exception('Unknown ticket id.');
			$ticket = new It6_ArrayWrapper($ticket);
		}
		
		if ( $ticket->status != static::STATUS_PAID && $ticket->status != static::STATUS_WINING ) {
			throw new It6_XmlRpc_Exception( "Can't resettle live ticket: Status of the ticket is: '{$ticket->status}'" ); 
		}
		
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$db->update(
				static::$TABLE,
				array(
					'status' => static::STATUS_RESETTLED,
					'is_loss' => 0,
					'time_resettled' => $time,
				),
				array('id = ?' => $ticketId));

			if ( $ticket->status == static::STATUS_PAID && empty($ticket->isLoss)
					&& floatval($ticket->win) != 0 ) {

				$currencyId = It6_Models_User::get($ticket->userId, 'currencyId', $db);
				Webservice_Transaction::make(array(
						'ticketId' => $ticketId,
						'value' => -$ticket->win,
						'userId' => $ticket->userId,
						'currencyId' => $currencyId,
						'hostId' => It6_Models_Host::ID_INTERNET,
						'typeName' => Webservice_TransactionType::NAME_OTHER_TICKET_PAYOUT_CANCEL));
				
				Webservice_Transaction::make(array(
						'ticketId' => $ticketId,
						'value' => -$ticket->win,
						'userId' => $ticket->userId,
						'currencyId' => $currencyId,
						'hostId' => It6_Models_Host::ID_INTERNET,
						'typeName' => Webservice_TransactionType::NAME_USER_TICKET_COLLECT_NOCASH_CANCEL));
			}

			It6_Log::info(
				"Live ticket '%ticket%' was resettled (payout canceled).",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('ticket' => $ticketId));

			It6_DbTransaction::commit($db);
			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't resettle live ticket '$ticketId'", 0, $e);
		}
	}

	/**
	 * Cancel bets (given in betIds) on given ticket and update win and rate.
	 * @param integer $ticketId
	 * @param array $betIds array of all bets to renew from canceled state on given ticket
	 * @param float $win updated ticket win
	 * @param float $rate updated ticket rate
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function cancelBets($ticketId, $betIds, $win, $rate, $ticket = null) {

		if ( empty($ticket) ) {
			$ticket = static::getById($ticketId);
			if ( empty($ticket) )
				throw new It6_XmlRpc_Exception('Unknown ticket id.');
			$ticket = new It6_ArrayWrapper($ticket);
		}
		
		//if ( $ticket->status != static::STATUS_NEW  )
		//	throw new It6_XmlRpc_Exception( "Can not cancel bet: Status of the ticket is: '{$ticket->status}'" ); 

		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {

			foreach ( $betIds as $betId ) {

				$bet = $db->select()
					->from(static::$TABLE_BET)
					->where('id = ?', $betId)
					->where('ticket_live_id = ?', $ticketId)
					->limit(1)
					->query()->fetchObject();

				if ( empty($bet) )
					throw new Exception("Unknown betId: '$betId'");

				if ( !empty($bet->canceled) )
					continue;

				$db->update(
					static::$TABLE_BET,
					array(
						'canceled' => 1,
						'result' => null,
						'result_valid' => ''),
					array(
						'id = ?' => $betId,
						'ticket_live_id = ?' => $ticketId));
			}

			$db->update(
				static::$TABLE,
				array('win' => $win, 'rate' => $rate),
				array('id = ?' => $ticketId));

			It6_DbTransaction::commit($db);

			It6_Log::info(
				"Live bets '%bets%' on live ticket '%ticket%' ware canceled (rated as 1).",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('ticket' => $ticketId, 'bets' => implode(';', $betIds)));

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't cancel live bets on ticket '$ticketId'", 0, $e);
		}
	}

	/**
	 * Renew canceled bets (given in betIds) on given ticket and update win and rate.
	 * @param integer $ticketId
	 * @param array $betIds array of all bets to renew from canceled state on given ticket
	 * @param float $win updated ticket win
	 * @param float $rate updated ticket rate
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function renewCanceledBets($ticketId, $betIds, $win, $rate, $ticket = null) {
		if ( empty($ticket) ) {
			$ticket = static::getById($ticketId);
			if ( empty($ticket) )
				throw new It6_XmlRpc_Exception('Unknown ticket id.');
			$ticket = new It6_ArrayWrapper($ticket);
		}
		
		//if ( $ticket->status == static::STATUS_PAID )
		//	throw new It6_XmlRpc_Exception( "Can't cancel live bet: Status of the live ticket is: '{$ticket->status}'" ); 

		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {

			foreach ( $betIds as $betId ) {

				$bet = $db->select()
					->from(static::$TABLE_BET)
					->where('id = ?', $betId)
					->where('ticket_live_id = ?', $ticketId)
					->limit(1)
					->query()->fetchObject();

				if ( empty($bet) )
					throw new Exception("Unknown betId: '$betId'");

				if ( empty($bet->canceled) )
					continue;

				$db->update(
					static::$TABLE_BET,
					array('canceled' => 0),
					array(
						'id = ?' => $betId,
						'ticket_live_id = ?' => $ticketId));
			}

			$db->update(
				static::$TABLE,
				array('win' => $win, 'rate' => $rate),
				array('id = ?' => $ticketId));

			It6_DbTransaction::commit($db);

			It6_Log::info(
				"Live bets '%bets%' on live ticket '%ticket%' ware renewed.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('ticket' => $ticketId, 'bets' => implode(';', $betIds)));

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't renew live bets on ticket '$ticketId'", 0, $e);
		}
	}
	
	/**
	 * Set live bet result and result validity to true.
	 * @param integer $ticketId identifier of the ticket
	 * @param integer $betId identifier of the bet
	 * @param string $result
	 * @throws It6_XmlRpc_Exception on failure
	 * @return boolean true on success
	 */
	public static function setBetResult($ticketId, $betId, $result) {

		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			if ( empty($ticketId) )
				throw new Exception("Missing ticketId.");
			if ( empty($betId) )
				throw new Exception("Missing betId.");
			$bet = $db->select()
				->from(static::$TABLE_BET, array('id'))
				->where('id = ?', $betId)
				->where('ticket_live_id = ?', $ticketId)
				->limit(1)
				->query()->fetchObject();

			if ( empty($bet) )
				throw new Exception("Unknown pair betId: '$betId', ticketId: '$ticketId'");

			$db->update(
				static::$TABLE_BET,
				array(
					'result' => $result,
					'result_valid' => 1),
				array(
					'id = ?' => $betId,
					'ticket_live_id = ?' => $ticketId));

			It6_DbTransaction::commit($db);

			It6_Log::info(
				"Live bet '%bet%' result set to '%result%'.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('bet' => $betId, 'result' => $result));

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't set result of the live bet '$betId'", 0, $e);
		}
	}
	
	/**
	 * Invalidate live bet result. Result has to be valid before this call.
	 * @param integer $betId  unique identifier of the bet
	 * @param integer $ticketId  unique identifier of the bet
	 * @throws It6_XmlRpc_Exception onfailure
	 * @return boolean true on success
	 */
	public static function invalidateBetResult($ticketId, $betId) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$bet = $db->select()
				->from(static::$TABLE_BET, array('id', 'result_valid'))
				->where('id = ?', $betId)
				->where('ticket_live_id = ?', $ticketId)
				->limit(1)
				->query()->fetchObject();

			if ( empty($bet) )
				throw new Exception("Unknown pair betId: '$betId', ticketId: '$ticketId'");
				
			if ( empty($bet->result_valid) ) {
				//throw new Exception("Result already invalided: '$betId'");
			}

			$db->update(
				static::$TABLE_BET,
				array('result_valid' => 0),
				array(
					'id = ?' => $betId,
					'ticket_live_id = ?' => $ticketId));

			It6_Log::info(
				"Live bet '%bet%' result invalidated.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('bet' => $betId));

			It6_DbTransaction::commit($db);

			return true;
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't set result of the live  bet '$betId'", 0, $e);
		}
	}

	/**
	 * Check if transaction was already done.
	 * @param string $transactionId
	 * @return boolean
	 */
	public static function isTransactionAlreadyDone($transactionId) {
		$db = static::getDb();
		$tmp = $db->select()
			->from(static::$TABLE_TRANSACTION, array('count' => 'Count(*)'))
			->where('id = ?', $transactionId)
			->query()->fetchObject();

		return $tmp->count > 0;
	}

	protected static function setTransactionDone($transactionId) {
		$db = static::getDb();
		$db->insert(static::$TABLE_TRANSACTION, array('id' => $transactionId));
	}
	
	/** 
	 * Set matches
	 * Only values that are changing should be set.
	 * @param array $transactionObjects structure of two members: 'match' (Entities_MatchtLive object) and 'transactionId' (string)
	 * @param boolean $onlyActive
	 * @return array array of transaction results
	 * @see Entities_MatchLive
	 */
	public static function setMatches($transactionObjects, $onlyActive = false) {
		$ret = array();

		$db = static::getDb();
		$matches = array();
		$matchSpecialIds = array();
		$dupSpIdMatchIds = array();
		
		foreach ($transactionObjects as $tO ) {
			if (static::isTransactionAlreadyDone($tO['transactionId']) )
				continue;

			It6_DbTransaction::begin($db);
			try {
				$match = new It6_ArrayWrapper($tO['match']);
				if ( empty($match->id) )
					throw new It6_XmlRpc_Exception("Missing match id.");

				if(
					!empty($match->specialId)
					&& in_array($match->specialId, $matchSpecialIds)
				) {
					$oldMatchId = array_search($match->specialId, $matchSpecialIds);
					$dupSpIdMatchIds[$match->specialId][$match->id] = $match->id;
					$dupSpIdMatchIds[$match->specialId][$oldMatchId] = $oldMatchId;
				}
				
				$matchSpecialIds[$match->id] = $match->specialId;				
				$matchId = $match->id;
				$matches[] = $matchId;
				$matchOld = Webservice_MatchLive::getById($matchId);

// Commented out by Martin on 31.5.2012
// match should never have the property of delete. Everythong is handled by match_status
//				if ( empty($matchOld) ) {
//					if ( !empty($match->delete) )
//						throw new Exception("Can't delete not existing live match.");
//
//					static::insertMatch($match);
//				}
//				else if ( !empty($match->delete) )
//					static::deleteMatch($matchId);
//				else
//					static::updateMatch($match);

				if ( empty($matchOld) ) {
					static::insertMatch($match);
				}
				else {
					static::updateMatch($match);
				}
				
				static::setTransactionDone($tO['transactionId']);
				$ret[] = array(
					'transactionId' => $tO['transactionId'],
					'success' => true);

				It6_DbTransaction::commit($db);
				It6_GlobalCache_Invalidator::invalidateSportsbookByBet($match->specialId);

			}
			catch ( Exception $e ) {
				It6_DbTransaction::rollback($db);

				$ret[] = array(
					'transactionId' => $transactionObject['transactionId'],
					'success' => false,
					'message' => $e->getMessage());

				It6_Log::err(
					"Can't set live match '%match%'",
					It6_Log::TAG_LIVEBET_OPERATION,
					array('match' => $matchId), $e);
			}
		}

		if(!empty($dupSpIdMatchIds)) {
			foreach($dupSpIdMatchIds as $spId => $matchArr) {
				$key = 'specialId:'.$spId;
				It6_Log::err(
					"Duplicate special_id in one batch.",
					It6_Log::TAG_LIVEBET_OPERATION,
					array($key => implode(';', $matchArr)));
			}
		}

		// Ending all not send matches
		if ( !empty($matches) && !$onlyActive ) {
			It6_DbTransaction::begin($db);
			try {
				$db->update(
					Webservice_MatchLive::$TABLE,
					array('status' => 'END'),
					array('status <> ?' => 'END', 'id NOT IN (?)' => $matches)
				);
				It6_DbTransaction::commit($db);
			}
			catch(Exception $e) {
				It6_DbTransaction::rollback($db);
				It6_Log::err(
						"Can't not end unknown matches",
						It6_Log::TAG_LIVEBET_OPERATION,
						array('matches' => $matches), $e);
				$ret[] = array(
						'success' => false,
						'message' => $e->getMessage());
			}
		}

		It6_GlobalCache_Invalidator::Livebetting_setMatches();

		return $ret;
		
	}

	/** 
	 * Insert new match
	 * @param struct $match Entities_MatchLive
	 * @throws It6_XmlRpc_Exception
	 * @see Entities_MatchLive
	 */
	public static function insertMatch($match) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$match= new It6_ArrayWrapper($match);

			if ( !in_array($match->status, Webservice_MatchLive::$STATUSES) )
				throw new Exception("Unknown status: '{$match->status}'");

			$data = array();
			$data['id'] = $match->id;
			$data['start'] = $match->start;
			$data['status'] = $match->status;
			$data['sport'] = $match->sport;
			$data['sport_id'] = $match->sportId;
			$data['league'] = $match->league;
			$data['home_team'] = $match->homeTeam;
			$data['away_team'] = $match->awayTeam;
			$data['minute'] = $match->minute;
			$data['special_id'] = $match->specialId;
			$data['score_home'] = $match->scoreHome;
			$data['score_away'] = $match->scoreAway;
			$data['region'] = $match->region;
			
			if(!empty($data['special_id'])) {
				self::asureSpecialIdUniqueness($data['special_id']);
			}
			else {
				$data['special_id'] = null;
			}

			$db->insert(Webservice_MatchLive::$TABLE, $data);

			It6_Log::info(
				"Live match '%match%' inserted.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('match' => $match->id, 'sportId'=>$match->sportId));

			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't insert live match '{$match->id}'", 0, $e);
		}
	}

	/** 
	 * Update new match, editable fields are just status, minute, scoreHome and scoreAway.
	 * @param struct $match Entities_MatchLive
	 * @throws It6_XmlRpc_Exception
	 * @see Entities_MatchLive
	 */
	public static function updateMatch($match) {
		$db = static::getDb();
		It6_DbTransaction::begin($db);
		try {
			$match= new It6_ArrayWrapper($match);
			$data = array();

			if ( !empty($match->status) ) {
				if ( !in_array($match->status, Webservice_MatchLive::$STATUSES) )
					throw new Exception("Unknown status: '{$match->status}'");
				$data['status'] = $match->status;
			}

			if ( !empty($match->minute) )
				$data['minute'] = $match->minute;

			if ( !empty($match->homeTeam) )
				$data['home_team'] = $match->homeTeam;

			if ( !empty($match->awayTeam) )
				$data['away_team'] = $match->awayTeam;

			if ( !empty($match->start) )
				$data['start'] = $match->start;
			
			if ( !empty($match->specialId) ) {
				self::asureSpecialIdUniqueness($match->specialId);
				$data['special_id'] = $match->specialId;
			}
			
			if ( isset($match->scoreHome) )
				$data['score_home'] = $match->scoreHome;
			
			if ( isset($match->scoreAway) )
				$data['score_away'] = $match->scoreAway;
			
			if ( !empty($match->region) )
				$data['region'] = $match->region;
			
			$db->update(Webservice_MatchLive::$TABLE, $data, array('id = ?' => $match->id));
			It6_Log::info(
				"Live match '%match%' updated.",
				It6_Log::TAG_LIVEBET_OPERATION,
				array('match' => $match->id));

			It6_DbTransaction::commit($db);
		}
		catch ( Exception $e ) {
			It6_DbTransaction::rollback($db);
			throw new It6_XmlRpc_Exception("Can't update live match '{$match->id}'", 0, $e);
		}
	}
// Commented out by Martin on 31.5.2012
// Since match should never have the property of delete, this emthod should never get called
	/** 
	 * Delete match
	 * @param integer $matchId
	 * @throws It6_XmlRpc_Exception
	 */
//	public static function deleteMatch($matchId) {
//		$db = static::getDb();
//		It6_DbTransaction::begin($db);
//		try {
//			$db->delete(Webservice_MatchLive::$TABLE,array('id = ?' => $matchId));
//			It6_Log::info(
//				"Live match '%match%' deleted.",
//				It6_Log::TAG_LIVEBET_OPERATION,
//				array('match' => $matchId));
//
//			It6_DbTransaction::commit($db);
//		}
//		catch ( Exception $e ) {
//			It6_DbTransaction::rollback($db);
//			throw new It6_XmlRpc_Exception("Can't delete live match '$matchId'", 0, $e);
//		}
//	}
	
	/**
	 * Returns bet to ticket
	 * @param integer $id id of the ticket
	 * @return array
	 */
	public static function getBets($id) {
		$db = static::getDb();
		
		$ret = $db->select()
			->from(
				static::$TABLE_BET, 
				array(
					'id' => 'id',
					'players' => 'players',
					'tip' => 'tip',
					'sport' => 'sport',
					'market' => 'market',
					'resultValid' => 'result_valid',
					'result' => 'result',
					'rate' => 'rate',
					'canceled' => 'canceled'))
			->where('ticket_live_id = ?', $id)
			->query()->fetchAll();
		
		return $ret;
	}
	
	/**
	 * If given special_id exists, it is changed to NULL so that the same special_id can be used with another record.
	 * @param type $specialId The new special_id that is tobe unique
	 */
	private static function asureSpecialIdUniqueness($specialId) {
		$db = static::getDb();
		
		$db->update(Webservice_MatchLive::$TABLE, array('special_id' => null), array('special_id = ?' => $specialId));
	}
}