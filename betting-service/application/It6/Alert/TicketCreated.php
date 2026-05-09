<?php
class It6_Alert_TicketCreated extends It6_Alert_MailAbstract {

	const PARAM_LIMIT = 'limit';

	private static function getTicketAmount($params) {
		if (isset($params['ticketAmount'])) {
			return $params['ticketAmount'];
		}
		else {
			$ticket = Webservice_Ticket::getById($params['ticketId']);
			return $ticket->amount;
		}
	}
	
	public static function check($params, $userId = null, $branchId = null) {
		$amount = static::getTicketAmount($params);
		$limit = static::getParameter(self::PARAM_LIMIT);
		return ($amount >= $limit);
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$ret['ticketId'] = $params['ticketId'];
		$ret['amount'] = static::getTicketAmount($params);
		return $ret;
	}
}