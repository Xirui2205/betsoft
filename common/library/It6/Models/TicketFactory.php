<?php

class It6_Models_TicketFactory extends It6_Models_DbDependent {

/**
 * Selected columns are named for particular data type. Column names related to bet (not ticket) are prefixed by 'b__', column names to ignore by 'x__'.
 */
private static function getSelectForDataAdmin($id, &$betIdColumn, $db) {
	if (!is_array($id))
		$id = array($id);
	$betIdColumn = 'b__sazka_id';
/*
	return $db->select()
		->from('ticket_pohled', array(
			'ticket_id' => 'ticket_id',
			'type' => 'type',
			'totalSum' => 'castka',
			'user_id' => 'user_id',
			'cash' => 'cash',
			'paidOut' => 'vyplacen',
			'x__status' => 'status',
			'x__ticket_sazka_zrusena' => 'ticket_sazka_zrusena',
			'x__zruseno' => 'zruseno',
			'b__sazka_id' => 'sazka_id',
			'b__id_col' => 'sloupec_id',
			'b__rate' => 'kurz',
			 //NOTE: simple ticket must have only one bet because in DB are no stake for individual bets => (totalSum = amount)
			'b__amount' => 'castka',
			//'b__visible' => new Zend_Db_Expr(1),
			'b__group' => 'group_id',
		))
		->where('ticket_id IN (?)', $id)
		->order('ticket_id ASC');
*/
	return $db->select()
		->from(array('t' => 'ticket'), array(
			'ticket_id' => 'ticket_id',
			'type' => 'type',
			'totalSum' => 'castka',
			'user_id' => 'user_id',
			'cash' => 'cash',
			'paidOut' => 'vyplacen',
			'payoutTime' => 'vyplacen_date',
			'mp' => 'mp',
			'mpWin' => 'mp_win',
			'x__zruseno' => 'zruseno',
		))
		->join(array('u' => 'uzivatel'), 'u.user_id=t.user_id', array(
			'anonymous' => 'anonymous',
		))
		->join(array('tr' => 'ticket_kurz'), 't.ticket_id=tr.ticket_id', array(
			'x__ticket_sazka_zrusena' => 'ticket_sazka_zrusena',
			'b__sazka_id' => 'sazka_id',
			'b__id_col' => 'sloupec_id',
			'b__rate' => 'rate',
			'b__group' => 'group_id',
			 //NOTE: simple ticket must have only one bet because in DB are no stake for individual bets => (riskAmount = amount)
			'b__amount' => 'amount',
		))
		->join(array('b' => 'sazky'), 'tr.sazka_id=b.sazka_id', array(
			'x__status' => 'status',
			'b__vysledek' => 'vysledek',
			//'b__visible' => new Zend_Db_Expr(1),
		))
		->where('t.ticket_id IN (?)', $id)
		->order('t.ticket_id ASC');
}

/**
 * Reads needed data from DB and creates new instance of It6_Models_Ticket.
 * @param int|array $id One or more ticket IDs
 * @param $dataType one of It6_Models_Ticket::DATA_* constants, currently only It6_Models_Ticket::DATA_ADMIN_TICKET implemented
 * @param bool $useCentralCurrency TRUE for automatic currency conversion
 * @param bool $compute TRUE for automatic class of It6_Models_Ticket::computeAggregates()
 * @returns Array with data or array of arrays with data for each ticket ID: array ( ticket_ID => array( ...data... ) )
 */
public static function newTicket($id, $dataType, $useCentralCurrency, $compute, &$db = null) {
	It6_Models_Abstract::assureDbParam($db);
	if (It6_Models_Ticket::DATA_ADMIN_TICKET == $dataType) {
		$select = static::getSelectForDataAdmin($id, $betIdColumn, $db);
	}
	else
		throw new Exception('Not implemented for given data type (see It6_Models_Ticket::DATA_*): ' . $dataType);
	$res = $select->query();
	$tickets = array();
	while ($row = $res->fetch()) {
		$ticketId = $row['ticket_id'];
		$betId = $row[$betIdColumn];
		if (!array_key_exists($ticketId, $tickets)) {
			$tickets[$ticketId] = array('userId' => $row['user_id']);
			$data = &$tickets[$ticketId];
			foreach ($row as $column => $value) {
				if ( !in_array(substr($column, 0, 3), array('b__', 'x__')) )
					$data[$column] = $value;
			}
			$data['bet'] = array();
		}
		$bet = array();
		foreach ($row as $column => $value) {
			if ('b__' == substr($column, 0, 3))
				$bet[substr($column, 3)] = $value;
		}
		$bet['canceled'] = (1 == $row['x__zruseno'] || 1 == $row['x__ticket_sazka_zrusena'] || 1 == $row['x__status']);
		$data['bet'][$betId] = $bet;
	}
	$helpers = array();
	foreach ($tickets as $ticketId => $ticket) {
		$helper = new It6_Models_Ticket($ticket, $dataType, 'user', $db);
		if ($useCentralCurrency)
			$helper->convertStakesToCentralCurrency($ticket['userId'], $db);
		if ($compute)
			$helper->computeAggregates();
		$helpers[$ticketId] = $helper;
	}
	if (is_array($id))
		return $helpers;
	else if (!empty($helpers))
		return array_shift($helpers);
	else
		return null;
}

/**
 * @NOTE: Current implementation does not use cache, but calls constructor anyway.
 * @NOTE: Places in code that depends on functional cache are marked by IT6:TICKET_CACHE comment tag.
 * If passed $cache parameter is empty call will be delegated to It6_Models_Ticket constructor,
 * otherwise It6_Models_Ticket instance will be constructed from passed $cache value.
 * @param string|NULL $cache Serialized It6_Models_Ticket
 * @param boolean $compute TRUE to call computeAggregates() if new instance was constructed
 * @param array $data
 * @param string $dataType
 * @param string|integer $currency
 * @param Zend_Db_Adapter $db
 * @return It6_Models_Ticket
 */
public static function newTicketCached($cache, $compute, $data, $dataType = It6_Models_Ticket::DATA_AJAX, $currency = null, &$db = null) {
	$ticket = new It6_Models_Ticket($data, $dataType, $currency, $db);
	if ($compute)
		$ticket->computeAggregates($db);
	return $ticket;

//	if (empty($cache)) {
//		$ticket = new It6_Models_Ticket($data, $dataType, $currency, $db);
//		if ($compute)
//			$ticket->computeAggregates($db);
//		return $ticket;
//	}
//	else
//		return unserialize($cache);
}

} // class It6_Models_TicketFactory