<?php
/**
 * Ticket acceptation related static methods.
 * @author Pavel Klinger
 * @see Entities_Coupon
 */
class Webservice_Coupon extends Webservice_AbstractWebService {

public static $TABLE = 'coupon_data';
public static $TABLE_ARCHIVE = 'coupon_data_archive';
public static $TABLE_CANCEL = 'coupon_cancel';
public static $TABLE_PREFIX = 'cd';
public static $IDENTITY = 'coupon_id';

protected static $CONV = array (
	'user_id' => 'userId',
	'admin_id' => 'adminId',
	'host_id' => 'hostId',
	'coupon_id' => 'couponId',
	'data' => 'data',
	'status' => 'status',
	'date' => 'date',
	'modified' => 'modified',
	'live' => 'live',
	'live_confirm' => 'liveConfirm',
	'status_message' => 'statusMessage',
	'confirm_date' => 'confirmDate',
	'bookmaker_id' => 'bookmakerId',
	'prolonged' => 'prolonged',
);

/**
 * Find coupon by given coupon ID.
 * @param integer $couponId identifier of the coupon
 * @return struct coupon structure
 * @see Entities_Coupon
 */
public static function getById($couponId, $extensions = null) {
	$coupon = parent::getById($couponId, $extensions);
	if (empty($coupon))
		return false;
	$coupon['statusMessage'] = self::decodeStatusMessage($coupon['statusMessage']);
	return $coupon;
}

/**
 * Find by user and admin (unique)
 * @param integer $userId
 * @param integer $adminId
 * @return struct|NULL coupon entity
 */
public static function getByUserIdAndAdminId($userId, $adminId, $extensions = null) {
	$where = array('userId=?' => $userId, 'adminId=?' => $adminId);
	$coupons = parent::getAllWhere($where);
	if (count($coupons) > 1)
		throw new It6_XmlRpc_Exception('Duplicate entry found for userId=' . $userId . ' and adminId='. $adminId);
	if (0 != count($coupons))
		return $coupons[0];
	else
		return null;
}

/**
 * Return all coupons with given status, if needed created by given admin
 * @param $status integer|array One or more statuses to fetch
 * @param integer|NULL $adminId If set, only coupons inserted by givan admin will be fetched
 * @return array Array of coupon IDs
 */
public static function getCouponsWithStatusAndAdmin($status, $adminId = null) {
	$db = static::getDb();
	$select = $db->select()
		->from(static::$TABLE, array('id' => 'coupon_id'))
		->where('status in (?)', $status);
	if (isset($adminId))
		$select = $select->where('admin_id=?', $adminId);
	$ids = array();
	$res = $select->query();
	while ($row = $res->fetch())
		$ids[] = $row['id'];
	return new It6_ArrayWrapper($ids);
}

/**
 * Find last coupon archive
 * @param integer $couponId
 * @throws It6_XmlRpc_Exception
 * @return struct|NULL coupon entity
 */
public static function getLastArchiveById($couponId) {
	$coupons = static::fetchAllEntities(
		static::defaultColumns( static::getDb()->select()->from(self::$TABLE_ARCHIVE) )
		->where('coupon_id=?', $couponId)
		->order('archived_at DESC')
		->limit(1)
		->query()
	);
	if (0 != count($coupons))
		return $coupons[0];
	else
		return null;
}

/**
 * Converts ticket data in Webservice structure to Coupon structure.
 * @param struct $ticket Ticket data as used by Webservice_Ticket
 * @param integer|boolean $adminId [optional] if FALSE then internet admin will be used,
 *        if TRUE then get it from ACL, otherwise use passed ID
 * @param integer|boolean $hostId [optional] if FALSE then internet branch host will be used,
 *        if TRUE then get it from ACL, otherwise use passed ID
 * @returns struct Entity of coupon (status will be COUPON_STATUS_NEW)
 */
public static function fromWebserviceTicket($ticket, $adminId = false, $hostId = false) {
	if (false === $adminId)
		$adminId = It6_Models_Admin::ID_INTERNET;
	else if (true === $adminId) {
		$acl = Zend_Registry::get('acl');
		$adminId = $acl->getIdentity(It6_Acl::IDNAME_ADMIN);
		if (!isset($adminId))
			throw new Exception('ACL identity has no user component');
	}
	if (false === $hostId)
		$hostId = It6_Models_Host::ID_INTERNET;
	else if (true === $hostId) {
		if (!isset($acl))
			$acl = Zend_Registry::get('acl');
		$hostId = $acl->getIdentity(It6_Acl::IDNAME_HOST);
		if (!isset($hostId))
			throw new Exception('ACL identity has no host component');
	}
	$ticket['hostId'] = $hostId;
	//if ($hostId != $ticket['hostId'])
	//	throw new Exception('Host identity doesn\'t match host in ticket data');
	$db = static::getDb();
	$now = It6_Date::dbNow();
	$type = $ticket['type'];
	$betCount = array_reduce($ticket['groups'], function($acu, $item) { return $acu + count($item['tips']); }, 0);
	$bets = array();
	$hasGroupT = false;
	$order = 0;
	foreach ($ticket['groups'] as $group) {
		foreach ($group['tips'] as $tip) {
			if (array_key_exists('group', $tip) && (0 == $tip['group']))
				$hasGroupT = true;
			$amount = (empty($tip['amount']) ? 0.0 : $tip['amount']);
			if (empty($amount) && It6_Models_Ticket::TYPE_SIMPLE == $type && 1 == $betCount)
				$amount = $ticket['amount'];
			if (It6_Models_Ticket::TYPE_SIMPLE == $type || It6_Models_Ticket::TYPE_COMBI == $type)
				$groupNum = 1;
			else if (!isset($tip['group']))
				$groupNum = null;
			else
				$groupNum = $tip['group'];

			$order = empty($tip['order']) ? $order + 1 : $tip['order'];

			$bet = array(
				'id_bet' => $tip['betId'],
				'id_col' => $tip['oddsOutcomeId'],
				'rate' => (empty($tip['rate'])
					? It6_Models_Bet::readBetRate($tip['betId'], $tip['oddsOutcomeId'], $now, $db)
					: $tip['rate']),
				'amount' => $amount,
				'visible' => true,
				'banker' => (isset($groupNum) && (0 == $groupNum) ? 1 : 0),
				'group' => $groupNum, //(isset($tip['group']) ? $tip['group'] : null)
				'order' => $order,
			);
			$bets[] = $bet;
		}
	}
	$combs = array();
	if (array_key_exists('combinations', $ticket)) {
		$combs = array();
		$n = count($ticket['groups']);
		if ($hasGroupT)
			--$n;
		for ($k = 1; $k <= $n; ++$k)
			$combs[$k] = array('used' => false, 'stake' => 0.0);
		foreach ($ticket['combinations'] as $comb) {
			if (!empty($comb['used'])) {
				$k = $comb['k'];
				if (1 > $k || $n < $k)
					throw new Exception('Invalid combination number: (' . $n . '/' . $k. ')');
				$combs[$k]['used'] = true;
				if (!empty($comb['stake']))
					$combs[$k]['stake'] = $comb['stake'];
				else if (!empty($comb['amount']))
					$combs[$k]['stake'] = $comb['amount'];
				else
					$combs[$k]['stake'] = 0.0;
			}
		}
	}

	$data = array(
		'type' => $type,
		'totalSum' => (empty($ticket['amount']) ? 0.0 : $ticket['amount']),
		'totalSumInPoints' => (empty($ticket['pointsAmount']) ? null : $ticket['pointsAmount']),
		'mail' => (empty($ticket['mail']) ? 0 : 1),
		'bet' => $bets,
		'combinations' => $combs,
		'cash' => $ticket['cash'],
		'pointType' => (empty($ticket['pointTypeId']) ? null : $ticket['pointTypeId']),
		'rateAdvance' => (empty($ticket['rateAdvance']) ? 0 : $ticket['rateAdvance']),
		'hostId' => $hostId,
	);
	$entity = new Entities_Coupon();
	$entity->userId = $ticket['userId'];
	$entity->adminId = $adminId;
	$entity->hostId = $hostId;
	$entity->data = Zend_Json::encode($data);
	$entity->status = It6_Models_Ticket::COUPON_STATUS_NEW;
	$entity->date = $now;
	return $entity;
}

/**
 * Converts coupon structure to ticket structure
 * $coupon['data'] can contain JSON string or array.
 * @param struct $coupon Coupon structure
 * @returns struct Ticket structure
 */
public static function toWebserviceTicket($coupon) {
	if (empty($coupon))
		return array();
	$db = static::getDb();
	$data = (is_array($coupon['data']) ? $coupon['data'] : Zend_Json::decode($coupon['data']));
	$data['userId'] = $coupon['userId'];
	
	$helper = new It6_Models_Ticket($data, It6_Models_Ticket::DATA_AJAX, 'user', $db);
	if (It6_Models_Ticket::COUPON_STATUS_MODIFIED == $coupon['status'])
		$helper->mergePreapproved($coupon['modified']);
	$helper->computeAggregates();
	$helper->readBetColumnNames($db);
	$ticket = new Entities_Ticket();
	$ticket->ticketId = 0;
	$ticket->ticketHandle = 0;
	$ticket->userId = $coupon['userId'];
	$ticket->amount = $helper->stake;
	$ticket->pointsAmount = empty($data['pointType']) ? null : $helper->stakeInPoints;
	$ticket->createdTime = null; //$coupon['date'];
	$ticket->paidOut = 0;
	$ticket->canceled = 0;
	$ticket->canceledByBookmakerId = null;
	$ticket->canceledByBookmakerNick = null;
	$ticket->reasonOfCancelation = null;
	$ticket->freeBetBonus = null; //TODO:
	$ticket->type = $helper->type;
	$ticket->winAmount = $helper->win;
	$ticket->totalOdds = $helper->rate;
	$ticket->realWinAmount = 0;
	$ticket->realTotalOdds = $ticket->totalOdds;
	$ticket->stats = null;
	$ticket->userStats = null;
	$ticket->paidOutTime = null;
	$ticket->paidOutBookmakerId = null;
	$ticket->mail = (empty($data['mail']) ? 0 : 1);
	$ticket->hostId = (empty($data['hostId']) ? It6_Models_Host::ID_INTERNET : $data['hostId']);
	$ticket->ticketHash = $helper->totalHash;
	$ticket->collectionTime = null;
	$ticket->collectionBranchId = null;
	$ticket->cash = ($helper->cash ? 0 : 1);
	$ticket->pointTypeId = (empty($data['pointType']) ? null : $data['pointType']);
	$ticket->rateAdvance = (empty($data['pointType']) ? $data['rateAdvance'] : 0);
	$ticket->adminId = $coupon['adminId'];
	$ticket->couponId = $coupon['couponId'];
	// bets
	$groups = array();
	foreach ($helper->bets as $bet) {
		if (array_key_exists('visible', $bet) && !$bet['visible'])
			continue;
		$group = (isset($bet['group']) ? $bet['group'] : false);
		$groupKey = (false === $group ? '_' : $group);
		if (!array_key_exists($group, $groups))
			$groups[$groupKey] = array('tips' => array());
		$tip = new Entities_TicketTip();
		$tip->betId = $bet['id'];
		$tip->oddsOutcomeId = $bet['column'];
		$tip->oddsOutcomeName = $bet['columnName'];
		$tip->oddsOutcomeShortcut = $bet['columnName'];
		$tip->rate = (empty($bet['rate']) ?
			It6_Models_Bet::readBetRate($bet['id'], $bet['column'], $coupon['date'])
			: $bet['rate']
		);
		if (false !== $group)
			$tip->group = $groupKey;
		if (!empty($bet['amount']))
			$tip->amount = $bet['amount'];
		if (array_key_exists('ako', $bet))
			$tip->ako = $bet['ako'];
		
		$tip->order = empty($bet['order']) ? 0 : $bet['order'];
		
		$groups[$groupKey]['tips'][] = $tip;
	}
	$ticket['groups'] = array_values($groups);
	// combinations
	$combinations = array();
	foreach ($helper->combinations as $k => $comb) {
		if (!$helper->isCombinationUsed($k))
			continue;
		$combination = new Entities_TicketCombination();
		$combination->k = $k;
		$combination->amount = $comb['stake'];
		$combinations[] = $combination;
	}
	$ticket['combinations'] = $combinations;
	return $ticket;
}

public static function updateStatus($couponId, $status, $updateTimestamp = true, $statusMessage = null) {
	$db = static::getDb();
	$data = array('status' => $status, 'status_message' => self::encodeStatusMessage($statusMessage));
	if ($updateTimestamp)
		$data['date'] = It6_Date::dbNow();
	return $db->update(
		static::$TABLE,
		$data,
		array('coupon_id=?' => $couponId)
	);
}

public static function archiveCoupon($couponId, $move = false) {
	$db = static::getDb();
	$t1 = $db->quoteIdentifier(static::$TABLE_ARCHIVE);
	$t2 = $db->quoteIdentifier(static::$TABLE);
	$db->query(
		"INSERT INTO $t1 SELECT NULL, ?, $t2.* FROM $t2 WHERE coupon_id=?",
		array(It6_Date::dbNow(), $couponId)
	);
	if ($move)
		$db->delete(static::$TABLE, array('coupon_id=?' => $couponId));
}

public static function updateAjaxData($couponId, $data) {
	if (is_array($data))
		$dataArray = $data;
	else if ($data instanceof It6_Models_Ticket)
		$dataArray = $data->getExposedData();
	else if ($data instanceof It6_Array_Wrapper)
		$dataArray = It6_ArrayWrapper::toNativeArray($data);
	else
		throw new It6_XmlRpc_Exception('Unsupported type for serialization');
	return static::getDb()->update(
		static::$TABLE,
		array('data' => Zend_Json::encode($dataArray)),
		array('coupon_id=?' => $couponId)
	);
}

/**
 * Request coupon to be canceled as soon as possible or to cancel a ticket if one was created from coupon meanwhile.
 * @param integer $couponId
 * @return boolean TRUE if request was delivered
 */
public static function requestCancelation($couponId) {
	$db = static::getDb();
	return(0 < $db->insert(
		static::$TABLE_CANCEL,
		array(
			'coupon_id' => $couponId,
			'admin_id' => Zend_Registry::get('acl')->getIdentity(It6_Acl::IDNAME_ADMIN),
			'requested_at' => It6_Date::dbNow()
		)
	));
}

/**
 * Check if coupon is requested to be canceled
 * @param integer $couponId
 * @return boolean|array FALSE or data of cancelation request (array keys: 'couponId', 'adminId', 'time')
 */
public static function isCancelationRequested($couponId) {
	$db = static::getDb();
	$rows = $db->select()
		->from(static::$TABLE_CANCEL,
			array('couponId' => 'coupon_id', 'adminId' => 'admin_id', 'time' => 'requested_at'))
		->where('coupon_id=?', $couponId)
		->query()
		->fetchAll();
	return (empty($rows) ? false : new It6_ArrayWrapper($rows[0]));
}

public static function getCancelationRequests($orphaned = false) {
	$db = static::getDb();
	$select = $db->select()->from(
		array('cc' => static::$TABLE_CANCEL),
		array('couponId' => 'coupon_id', 'adminId' => 'admin_id', 'time' => 'requested_at')
	);
	if ($orphaned) {
		$select->joinLeft(array('cd' => static::$TABLE), 'cc.coupon_id=cd.coupon_id', array())
			->where('cd.coupon_id IS NULL');
	}
	return $select->query()->fetchAll();
}

public static function deleteCancelationRequests($ids) {
	$db = static::getDb();
	return $db->delete(
		static::$TABLE_CANCEL,
		array('coupon_id IN (?)', $ids)
	);
}

/**
 * Cancel coupon or ticket. This method should be called by from confirmation daemon, don't call this method
 * directly, but use requestCancelation() instead.
 * @param array Data of cancelation request. Keys: 'couponId', 'adminId', 'time'
 * @return boolean TRUE if coupon/ticket was canceled
 */
public static function cancel($request) {
	$db = static::getDb();
	It6_DbTransaction::begin($db);
	try {
		$error = false;
		$couponId = $request['couponId'];
		$adminId = $request['adminId'];
		It6_Log::info(
			'Canceling coupon confirmation',
			It6_Log::TAG_TICKET_APPROVAL,
			array('coupon' => $couponId, 'admin' => $adminId)
		);
		$n = $db->delete(static::$TABLE, array('coupon_id=?' => $couponId));
		$ticketIds = Webservice_Ticket::getIdsByCouponId($couponId);
		foreach ($ticketIds as $ticketId) {
			try {
				Webservice_Ticket::cancel($ticketId, 'Canceled because of coupon cancelation request');
				It6_Log::info(
					'Canceled ticket because of coupon cancelation',
					It6_Log::TAG_TICKET_APPROVAL,
					array('coupon' => $couponId, 'ticket' => $ticketId, 'admin' => $adminId)
				);
			}
			catch (Exception $e) {
				$error = true;
				//TODO: report error to be checked by admin
				It6_Log::err(
					'Coupon confirmation not canceled - ticket not canceled',
					It6_Log::TAG_TICKET_APPROVAL,
					array(
						'coupon' => $couponId,
						'admin' => $adminId,
						'ticket' => $ticketId,
					)
				);
			}
		}
		$db->delete(static::$TABLE_CANCEL, array('coupon_id=?' => $couponId));
		It6_DbTransaction::commit($db);
		return !$error;
	}
	catch (Exception $e) {
		It6_DbTransaction::rollback($db);
		throw $e;
	}
}

public static function encodeStatusMessage($message) {
	if (is_array($message))
		return 'json:' . Zend_Json::encode($message);
	else
		return $message;
}

public static function decodeStatusMessage($message) {
	if (is_string($message) && 'json:' == substr($message, 0, 5))
		return Zend_Json::decode(substr($message, 5));
	else
		return $message;
}

// public static function validate($coupon, &$helper = null) {
// 	try {
// 
// 		if ( empty($helper) ) {
// 			$helper = new It6_Models_Ticket($coupon, It6_Models_Ticket::DATA_AJAX); // handle currency param
// 			$helper->computeAggregates();
// 		}
// 
// 		$vhelper = new It6_Models_TicketValidator($helper);
// 
// 		return self::joinValidatorResults(array(
// 			$vhelper->isFreebet(),
// 			$vhelper->numBet(),
// 			$vhelper->isAko(),
// 			$vhelper->isNotSame(),
// 			//TODO: why this is commented out?
// 			//$vhelper->riskLimit(),
// 			$vhelper->isProveTicket(),
// 			$vhelper->limitDay(),
// 			$vhelper->isIndvLimit(),
// 			$vhelper->sameTicket(),
// 			$vhelper->isValidBet(),
// 			$vhelper->userHasMoney()));
// 
// 	} catch (Exception $e) {
// 		It6_Log::err($e);
// 		return $vhelper->newError(-1,"'ticket_er_-1'");
// 	}
// }



	/**
	 * Finds stored coupon by alias and returns data
	 * @param int $couponId
	 * @return boolean|array FALSE or data
	 */
	public static function findSavedCoupon($couponId) {
		$db = static::getDb();
	
		$data = $db->fetchOne($db->select()->from('coupon_saved', 'data')->where('alias = ?', $couponId)->limit(1)->__toString());
	
		return !empty($data) ? Zend_Json::decode($data) : false;
	}
} // class
