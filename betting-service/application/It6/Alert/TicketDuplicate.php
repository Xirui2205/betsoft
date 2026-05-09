<?php
class It6_Alert_TicketDuplicate extends It6_Alert_MailAbstract {

	const PARAM_LIMIT = 'limit';

	public static function check($params, $userId = null, $branchId = null) {
		list($ticketId, $ticket, $helper, $db) = static::_init($params, $userId, $branchId);

		$maxDuplicit = static::_getMaxDuplicit($userId);

		$counts = $helper->getTicketHashCounts($db);
		$counts = array_filter($counts, function($item) use ($maxDuplicit) { return $item['count'] == $maxDuplicit; });

		return !empty($counts);
	}

	protected static function _getMaxDuplicit($userId) {
		$ws = Zend_Registry::get('ws');
		$acl = Zend_Registry::get('acl');
		$identity = array(
			'user' => $userId,
			'admin' => $acl->getIdentity(It6_Acl::IDNAME_ADMIN),
			'host' => $acl->getIdentity(It6_Acl::IDNAME_HOST),
			'branch' => $acl->getIdentity(It6_Acl::IDNAME_BRANCH)
		);
		$ret = $ws->Parameter->getEffectiveValue(It6_Models_Parameter::NAME_TICKET_DUPLICATE_COUNT, $identity);
		return intval($ret);
	}

	protected static function _init($params, $userId = null, $branchId = null) {
		$ticketId = $params['ticketId'];
		$ticket = Webservice_Ticket::getById($ticketId);
		$ticket = new It6_ArrayWrapper($ticket);
		$helper = new It6_Models_Ticket($ticket, It6_Models_Ticket::DATA_SERVICE);
		$helper->computeAggregates($db);
		$db = Webservice_AbstractWebService::getMainDb();

		return array($ticketId, $ticket, $helper, $db);
	}

	protected static function getMailParameters($params, $userId = null, $branchId = null) {
		list($ticketId, $ticket, $helper, $db) = static::_init($params, $userId, $branchId);
		$ret = parent::getMailParameters($params, $userId, $branchId);
		$ret['tickets'] = Webservice_Ticket::getAllWhere(array(
			'ticketHash IN (?)' => $helper->hashes,
			'userId = ?' => $userId));
		return $ret;
	}
}