<?php

/**
 * Ticket statistics
 * @author Pavel Klinger
 *
 * Modification methods accept $preloadedData parameter,
 * in fact field 'ticket' doesn't have to be complete
 * ticket entity but only this subset:
 *    userId
 *    branchId
 *    hostId
 *    type
 *    createdTime
 *    paidOutTime
 * If someone would like to use additional fields,
 * particullar modification method(s) calls must
 * be revised.
 */
class Webservice_TicketStatistics {


/**
 * Modifications with ticket creation
 * @param integer $ticketId identifier of the ticket
 * @param struct $preloadedData Struct with optional fields: ticket, helperCc, branch
 * @return boolean true on success
 */
public static function creation($ticketId, $preloadedData = null) {
	return static::_write(
		$ticketId,
		Webservice_Ticket::$STATISTICS_CASHFLOW_TABLE,
		array('amount','win','count'),
		'createdTime',
		1,
		$preloadedData);
}

/**
 * Modifications with ticket cancelation
 * @param integer $ticketId identifier of the ticket
 * @return array
 */
public static function cancelation($ticketId, $preloadedData = null) {
	return static::_write(
		$ticketId,
		Webservice_Ticket::$STATISTICS_CASHFLOW_TABLE,
		array('amount','win','count'),
		'createdTime',
		-1,
		$preloadedData);
}

/**
 * Modifications with ticket payout
 * @param integer $ticketId identifier of the ticket
 * @return boolean true on success
 */
public static function payingout($ticketId, $preloadedData = null) {
	$db = Webservice_AbstractWebService::getMainDb();
	It6_DbTransaction::begin($db);
	try {
		static::_write(
			$ticketId,
			Webservice_Ticket::$STATISTICS_CASHFLOW_TABLE,
			array('won','win_tip_count','lost_tip_count','won_count','lost_count'),
			'createdTime',
			1,
			$preloadedData);
		static::_write(
			$ticketId,
			Webservice_Ticket::$STATISTICS_PAYOUT_TABLE,
			array('amount','win','count','won','win_tip_count','lost_tip_count','won_count','lost_count'),
			'paidOutTime',
			1,
			$preloadedData);
		It6_DbTransaction::commit($db);
		return true;
	}
	catch ( Exception $e ) {
		It6_DbTransaction::rollback($db);
		throw $e;
	}
}

/**
 * Modifications with ticket payout
 * @param integer $ticketId identifier of the ticket
 * @return boolean true on success
 */
public static function payoutCancelation($ticketId, $preloadedData = null) {
	$db = Webservice_AbstractWebService::getMainDb();
	It6_DbTransaction::begin($db);
	try {
		static::_write(
			$ticketId,
			Webservice_Ticket::$STATISTICS_CASHFLOW_TABLE,
			array('won','win_tip_count','lost_tip_count','won_count','lost_count'),
			'createdTime',
			-1,
			$preloadedData);
		static::_write(
			$ticketId,
			Webservice_Ticket::$STATISTICS_PAYOUT_TABLE,
			array('amount','win','count','won','win_tip_count','lost_tip_count','won_count','lost_count'),
			'paidOutTime',
			-1,
			$preloadedData);
		It6_DbTransaction::commit($db);
		return true;
	}
	catch ( Exception $e ) {
		It6_DbTransaction::rollback($db);
		throw $e;
	}
}

protected static function _write($ticketId, $table, $filter, $timeCol,$sign = 1, $preloadedData = null) {
	$db = Webservice_AbstractWebService::getMainDb();
	It6_DbTransaction::begin($db);
	try {
		list($ticket, $helper) = static::_getTicketAndHelper($ticketId, $preloadedData);
		$stats = $helper->getStatistics(false, $db);

		foreach ( $stats as $item ) {
			$select = $db->select()
				->from(
					$table,
					array('amount','win','won','count','win_tip_count','lost_tip_count','won_count','lost_count'));
	
			$wheres = static::_getWheres($item, $ticket, $timeCol);
			foreach ( $wheres as $k => $v )
				$select->where($k, $v);

			$data = $select->query()->fetch();
			if ( empty($data) ) {
				$insertData = static::_getInsertData($item, $ticket, $timeCol);
				$data = static::_getData($item, $sign, $filter);
				$db->insert(
					$table,
					array_merge($insertData, $data));
			}
			else {
				$data = static::_getData($item, $sign, $filter, $data);
				$db->update(
					$table,
					$data,
					$wheres);
			}

			It6_Log::debug(
				"Ticket statistics ('%table%') modified by ticket '%ticketId%.",
				It6_Log::TAG_TICKET_STATISTICS,
				array(
					'ticketId' => $ticketId,
					'table' => $table,
					'data' => $data,
					'wheres' => $wheres
				));
		}

		It6_DbTransaction::commit($db);
		return true;
	}
	catch ( Exception $e ) {
		It6_DbTransaction::rollback($db);
		throw new It6_XmlRpc_Exception("Can't write statistics", 0, $e);
	}
}

protected static function _getTicketAndHelper($ticketId, $preloadedData) {
	$db = Webservice_AbstractWebService::getMainDb();
	if (empty($preloadedData['ticket'])) {
		$ticket = Webservice_Ticket::getById($ticketId);
		if ( empty($ticket) )
			throw new Exception("Unknown ticketId: '$ticketId'");
	}
	else {
		$ticket = $preloadedData['ticket'];
	}

	$ticket =  new It6_ArrayWrapper($ticket);
	if (empty($preloadedData['branch'])) {
		$branch = Webservice_Branch::getById($ticket->branchId);
	}
	else {
		$branch = $preloadedData['branch'];
	}
	$branch =  new It6_ArrayWrapper($branch);
	$ticket->branchLocationId = $branch->branchLocationId;

	if (empty($preloadedData['helperCc'])) {
		$helper = new It6_Models_Ticket(
			$ticket,
			It6_Models_Ticket::DATA_SERVICE,
			'central',
			$db);
		$helper->computeAggregates();
	}
	else {
		$helper = $preloadedData['helperCc'];
	}
	$helper->computeBetWonAndWinDistribution();
	return array($ticket, $helper);
}

protected static function _getWheres($statsItem, $ticket, $timeCol) {
	return array(
		'hour = ?' => floor(It6_Date::fromDbAsTimestamp($ticket->$timeCol)/3600),
		'user_id = ?' => $ticket->userId,
		'branch_id = ?' => $ticket->branchId,
		'host_id = ?' => $ticket->hostId,
		'sport_id = ?' => $statsItem['sportId'],
		'udalost_id = ?' => $statsItem['eventId'],
		'typ_id = ?' => $statsItem['typeId'],
		'type = ?' => $ticket->type,
		'location_id = ?' => $ticket->branchLocationId,
	);
}

protected static function _getInsertData($statsItem, $ticket, $timeCol) {
	return array(
		'hour' => floor(It6_Date::fromDbAsTimestamp($ticket->$timeCol)/3600),
		'user_id' => $ticket->userId,
		'host_id' => $ticket->hostId,
		'branch_id' => $ticket->branchId,
		'sport_id' => $statsItem['sportId'],
		'udalost_id' => $statsItem['eventId'],
		'typ_id' => $statsItem['typeId'],
		'type' => $ticket->type,
		'location_id' => $ticket->branchLocationId,
	);
}

protected static function _getData($statsItem, $sign = 1, $filter = null, $oldData = null) {
	$ret = array(
		'amount' => (!empty($oldData['amount']) ? $oldData['amount'] : 0) + $sign * $statsItem['amount'],
		'won' => (!empty($oldData['won']) ? $oldData['won'] : 0) + $sign * $statsItem['won'],
		'win' => (!empty($oldData['win']) ? $oldData['win'] : 0) + $sign * $statsItem['win'],
		'count' => (!empty($oldData['count']) ? $oldData['count'] : 0) + $sign * $statsItem['count'],
		'win_tip_count' => (!empty($oldData['win_tip_count']) ? $oldData['win_tip_count'] : 0) + $sign * $statsItem['winTipCount'],
		'lost_tip_count' => (!empty($oldData['lost_tip_count']) ? $oldData['lost_tip_count'] : 0) + $sign * $statsItem['lostTipCount'],
		'won_count' => (!empty($oldData['won_count']) ? $oldData['won_count'] : 0) + $sign * $statsItem['wonCount'],
		'lost_count' => (!empty($oldData['lost_count']) ? $oldData['lost_count'] : 0) + $sign * $statsItem['lostCount']
	);

	if ( !empty($filter) )
		$ret = array_intersect_key($ret, array_fill_keys($filter,null));

	return $ret;
}

} //Webservice_TicketStatistics
