<?php

class Models_MyAccount_LiveTicket {

//public static $THEAD_LAYOUT	= 'standard-thead-noorder';
public static $THEAD_LAYOUT	= 'live-tickets-thead';
public static $TBODY_LAYOUT	= 'live-tickets-tbody';

private static $filterData		= array();
private static $paginatorData	= array('recsPerPage' => 10);
private static $orderData		= array('createdTime DESC');

public static function getAllByUser($values, $view) {
	$extensions	= array();
	$states		= array();

	//get list of states selected in filter
	//TODO: should be done differently, but the vic_main.ticket is not really set up for it
	if(!empty($values['filter']['open'])) {
		$states[] = 1;
		$states[] = 5;
	}
	if(!empty($values['filter']['win'])) {
		$states[] = 2;
		$states[] = 6;
	}
	if(!empty($values['filter']['loss']))
		$states[] = 3;
	if(!empty($values['filter']['canceled']))
		$states[] = 4;
	//if required ticket state hasnt been specified, then get all
	// really? wouldn't be better to not pass anything?
	if(empty($states))
		$states = array(1,2,3,4,5,6);

	//prepare wsForm data from $_POST
	if(!empty($values['filter']))
		self::$filterData = $values['filter'];
	if(!empty($values['paginator']))
		self::$paginatorData = $values['paginator'];

	//create filter
	$filter = new It6_WsForm_Filter(array(
		array('winning_tickets', 'win', 'checkbox', array()),
		array('created_from', 'timeFrom', 'date',
			array(array(array('DATE(?)' => 'timeCreated'), '>= DATE(?)')), 'It6_Validate_Date'),
		array('opened_tickets', 'open', 'checkbox', array()),
		array('created_before', 'timeTo', 'date',
			array(array(array('DATE(?)' => 'timeCreated'), '<= DATE(?)')), 'It6_Validate_Date'),
		array('loss_tickets', 'loss', 'checkbox', array()),
		array('ticket_id', 'ticketHandle', 'text', array(array('handle', '=')),
			'Zend_Validate_Digit', null, null, null, 'input110'),
		array('canceled_tickets', 'canceled', 'checkbox', array())
	));
	$filter->getExtension(self::$filterData, $extensions);

	//create pagination
	$paginator = new It6_WsForm_Paginator(self::$paginatorData['recsPerPage']);
	$paginator->getExtension(self::$paginatorData, $extensions);

	//create table
	$table = new It6_WsForm_Table(array(
		array('created', 'timeCreated'),
		array('ticket_id', 'handle'),
		array('type', 'type'),
		array('amount', 'stake', 'right'),
		array('rate', 'rate', 'right'),
		array('total_win', 'won', 'right'),
		array('status', 'status')
	));
	$table->getColumnsExtension($extensions);
	$table->getOrderExtension(array('timeCreated DESC'), $extensions);

	$userId = Zend_Registry::get('user_id');
	$tickets = Zend_Registry::get('ws')->ext($extensions)->Livebetting->getAllByState(
		$states, array('userId = ?' => $userId)
	);
	$tickets = It6_ArrayWrapper::toNativeArray($tickets);
	if (!empty($tickets)) {
		$currencyName = Zend_Registry::get('mena');
		foreach ($tickets as &$ticket) {
			$ticket['currencyName'] = $currencyName;
			//$ticket['realState'] = It6_Models_Ticket::getRealTicketState($ticket);
		}
	}

	$view->tickets		= $tickets;
	$view->filter		= $filter->getLayout(null, self::$filterData);
	$view->paginator	= $paginator->getLayout(null, null, $extensions['paginator']->getResponse());
	$view->thead		= $table->getTheadLayout(self::$THEAD_LAYOUT);
	$view->tbody		= $table->getTbodyLayout(self::$TBODY_LAYOUT, $tickets);
}

public static function getTicketByHandleAndUser(&$view, $handle) {
	$userId	= Zend_Registry::get('user_id');
	$ticket = Zend_Registry::get('ws')->Livebetting->getByHandleAndUser($handle, $userId);
	$ticket = It6_ArrayWrapper::toNativeArray($ticket);

	if (empty($ticket))
		$view->err = 'ticket_not_found';
	else {
		$view->currency		= Zend_Registry::isRegistered('mena') ? Zend_Registry::get('mena') : It6_Models_Currency::getCentralCurrencyId();
		//deprecated
		//$view->ticketState	= $ticket['state']; //It6_Models_Ticket::getRealTicketState($ticket);
		$view->printUrl		= $view->UrlSet(76).'/t/'.$handle;
		$view->ticket		= $ticket;
		$view->ticketHandle	= $handle;
	}
}

public static function getTicketByHandle(&$view, $handle) {
	$ticket = Zend_Registry::get('ws')->Livebetting->getByHandle($handle);
	$ticket = It6_ArrayWrapper::toNativeArray($ticket);

	if (empty($ticket))
		$view->err = 'ticket_not_found';
	else {
		$user = Zend_Registry::get('ws')->User->getById($ticket['userId']);
		$view->currency = $user->currencyName;
		//deprecated
		//$view->ticketState	= $ticket['state']; //It6_Models_Ticket::getRealTicketState($ticket);
		$view->printUrl		= $view->UrlSet(76).'/t/'.$handle;
		$view->ticket		= $ticket;
		$view->ticketHandle	= $handle;
	}
}

} // class
