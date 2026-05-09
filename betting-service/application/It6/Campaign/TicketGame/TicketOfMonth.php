<?php

class It6_Campaign_TicketGame_TicketOfMonth extends It6_Campaign_TicketGame_HandlerClass {

protected static $_eventIds = null;

public function __construct($game) {
	parent::__contruct($game);
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
	return floatval($ticket->rate);
}

public function prepareView($view) {
	$ws = Zend_Registry::get('ws');
	$view->game = $this->game;
	$view->evaluationName = 'rate';
	$view->tickets = $ws->Campaign->getTicketGameTopTickets($this->game['gameId'], Webservice_Parameter::getGlobalParameter('web.monthTicketCount'));
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
/**
 * Delete all tickets, that are not in curent game period
 * @param unknown_type $db
 */
public function cleanUp(&$db){
	$tstrc = It6_Date::toTimestruct(time());
	$tstrc["tm_sec"] = 0;
	$tstrc["tm_min"] = 0;
	$tstrc["tm_hour"] = 0;
	$tstrc["tm_mday"] = 1;
	$ts = It6_Date::toTimestamp($tstrc);
	$t = It6_Date::timestampToDb($ts);
	
	$sql = "DELETE r.* FROM ticket_game_result r JOIN ticket t ON r.ticket_id=t.ticket_id WHERE t.vyplacen_date<? AND r.game_id=?";
	
	$db->query($sql, array($t, 2));
}

} // class
