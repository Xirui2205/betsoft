<?php

class It6_Campaign_TicketGame_IPadEuro2012 extends It6_Campaign_TicketGame_HandlerClass {

protected static $_eventIds = null;

public function __construct($game) {
	parent::__contruct($game);
}

/**
 * Loads static list of event IDs for this game, loads only if
 * $_eventIds static class variable is not set already.
 * @param Zend_Db_Adapter $db
 */
protected function _loadEventIds($db = null) {
	if (!isset(static::$_eventIds)) {
		if (!isset($db)) {
			$db = Zend_Registry::get('db');
		}
		$rows = $db->select()->from(
				'ticket_game_event',
				array('eventId' => 'udalost_id')
			)
			->where('game_id=?', $this->game['gameId'])
			->query()
			->fetchAll();
		static::$_eventIds = array_map(function($i) { return $i['eventId']; }, $rows);
	}	
}

/**
 * @see It6_Campaign_TicketGame_HandlerClass
 */
protected function _evaluateTicket($ticket, $db = null) {
	if (!isset($ticket->anonymous)) {
		It6_Log::err(
			'Ticket anonymous flag is not set',
			It6_Log::TAG_CAMPAIGN,
			array('ticketId' => $ticket->id)
		);
		return false;
	}
	if ($ticket->anonymous) {
		return false;
	}
	if (!$ticket->paidOut) {
		return false;
	}
	$validFrom = $this->game['gameValidFrom'];
	if (!is_numeric($validFrom)) {
		$validFrom = It6_Date::fromDbAsTimestamp($validFrom);
	}
	$validTo = $this->game['gameValidTo'];
	if (!is_numeric($validTo)) {
		$validTo = It6_Date::fromDbAsTimestamp($validTo);
	}
	if ($ticket->payoutTime < $validFrom || $ticket->payoutTime > $validTo) {
		return false;
	}
	if (
		!in_array($ticket->type, array(
			It6_Models_Ticket::TYPE_SIMPLE,
			It6_Models_Ticket::TYPE_COMBI,
		))
	) {
		return false;
	}
	if (It6_Models_Ticket::RESULT_WON != $ticket->result) {
		return false;
	}
	if (100.0 > $ticket->stake) {
		return false;
	}
	// find at least one bet from event associated with game
	static::_loadEventIds($db);
	$eventFound = false;
	$noEventBetIds = array();
	foreach ($ticket->bets as $bet) {
		if (isset($bet['eventId'])) {
			$eventId = $bet['eventId'];
		}
		else {
			$eventId = false;
			$betId = $bet['id'];
			$noEventBetIds[$betId] = $betId;
		}
		if ($eventId && in_array($eventId, static::$_eventIds)) {
			$eventFound = true;
			break;
		}
	}
	if (!$eventFound && !empty($noEventBetIds)) {
		$eventIds = It6_Models_Bet::readBetEventId($noEventBetIds, $db);
		if (!empty($eventIds)) {
			foreach ($eventIds as $eventId) {
				if (in_array($eventId, static::$_eventIds)) {
					$eventFound = true;
					break;
				}
			}
		}
	}
	if (!$eventFound) {
		return false;
	}
	return floatval($ticket->rate);
}

public function prepareView($view) {
	$ws = Zend_Registry::get('ws');
	$view->game = $this->game;
	$view->evaluationName = 'rate';
	$view->tickets = $ws->Campaign->getTicketGameTopTickets($this->game['gameId'], 3);
}

/**
 * This implementation select tickets that were paid out in valid time period
 * and doesn't belong to anonymous user.
 * @see It6_Campaign_TicketGame_HandlerClass::getTicketsForReevaluation()
 */
public function getTicketsForReevaluation(&$db) {
	$rows = $db->select()
		->from(
			array('t' => 'ticket'),
			array('ticketId' => 'ticket_id')
		)
		->join(
			array('u' => 'uzivatel'),
			'u.user_id=t.user_id AND NOT(u.anonymous)',
			array()
		)
		->where('t.vyplacen=1')
		->where('t.vyplacen_date>=?', $this->game['gameValidFrom'])
		->where('t.vyplacen_date<=?', $this->game['gameValidTo'])
		->query()
		->fetchAll();
	return array_map(function($i) { return $i['ticketId']; }, $rows);
}

} // class
