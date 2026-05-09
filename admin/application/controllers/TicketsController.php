<?php

class TicketsController extends It6_Controller_Abstract {

const DUPLICATE_TICKETS_SECTION_ID = 273;
const TICKET_DETAIL_SECTION_ID = 275;

/**
 * How old tickets are showed in last window, on start up (seconds)
 */
const LAST_TICKETS_TIME_WINDOW = 600;

/**
 * How many tickets are showed in last window
 */
const LAST_TICKETS_COUNT_LIMIT = 100;

/**
 * How often are last tickets updated (seconds)
 */
const LAST_TICKETS_REFRESH_IN = 15;
const ONLINE_RISKLIMIT_REFRESH_IN = 15;

const ONLINE_RISKLIMIT_ITEMS_COUNT = 30;

public function duplicateTicketsAction() {
	$ws = Zend_Registry::get('ws');
	
	$this->view->form = new Models_Form_DuplicateTicketsFilter((static::DUPLICATE_TICKETS_SECTION_ID));

	if ( !$this->view->form->isValid($this->getRequest()->getParams()) ) {
		//TODO add validation message
	}

	$values = $this->view->form->getValues();
	$this->view->form->populate($values);

	if ( !empty($values['minDuplicity']) ) {
		$this->view->duplicates = $ws->Ticket->getDuplicateTickets($values['minDuplicity']);
	}
}


public function detailAction() {
	require_once(ROOT . 'admin/application/controllers/FinanceController.php');
	$this->_helper->layout->setLayout('empty');
	$ticketId = $this->_request->getParam('ticketId');
	$tickets = Zend_Registry::get('ws')->Ticket->getByIdAndUserComplete($ticketId, null, CZ_LANG_ID, true);
	$tickets = It6_ArrayWrapper::toNativeArray($tickets);
	$acl = Zend_Registry::get('acl');
	$canUpdate = $acl->isResourceAllowed('section:275', 'update');
	$this->view->showSummary = !$acl->userHasRole(It6_Acl_Admin::ROLE_GOV_SUPERVISOR);
	$this->view->showTransactions = $this->view->showSummary;
	foreach ($tickets as &$ticket) {
		//$ticket['state'] = It6_Models_Ticket::getRealTicketState($ticket);
		$ticket['groups'] = It6_Models_Ticket::sortBetsByGroup($ticket['groups'], $ticket['type']);
		if ($canUpdate) {
			foreach ($ticket['groups'] as $groupId => $group) {
				foreach ($group['tips'] as $tipId => $tip) {
					$actions = array();
					if (empty($ticket['paidOut'])) {
						if (!empty($tip['canceled']))
							$actions['renew'] = true;
						else
							$actions['rate1'] = true;
					}
					$ticket['groups'][$groupId]->tips[$tipId]->actions = $actions;
				}
			}
		}
		if ($this->view->showTransactions) {
			$ticket['transactionsUri'] = '/?section=' . FinanceController::TRANSACTIONS_ID . '&filter[ticketId]=' . $ticket['ticketId'];
		}
	}
	
	if (0 == count($tickets)) {
		$this->view->ticket = array();
		$this->view->currency = '';
	}
	else {
		$this->view->ticket = $tickets[0];
		$this->view->currency = $tickets[0]['currencyName'];
	}
}

public function lastAction() {
	$this->view->ajax = $this->_request->getParam('ajax');
	$this->view->from = $this->_request->getParam('from');
	$this->view->to = $this->_request->getParam('to');
	
	$this->view->stakeFrom = $this->_request->getParam('stakeFrom');
	$this->view->stakeTo = $this->_request->getParam('stakeTo');
	$this->view->rateFrom = $this->_request->getParam('rateFrom');
	$this->view->rateTo = $this->_request->getParam('rateTo');
	$this->view->winFrom = $this->_request->getParam('winFrom');
	$this->view->winTo = $this->_request->getParam('winTo');
	
	if ( empty($this->view->from) )
		$this->view->from = 
				It6_Date::nowAsTimestamp() - 2678400*15.65; //static::LAST_TICKETS_TIME_WINDOW;
	if ( empty($this->view->to) )
		$this->view->to = It6_Date::nowAsTimestamp();

	if ( !empty($this->view->ajax) ) {
		$this->_helper->layout()->disableLayout();
	}

	$ws = Zend_Registry::get('ws');
	$this->view->ws = $ws;
	$this->view->acl = Zend_Registry::get('acl');
	$this->view->last = $this->view->ws->Ticket->getLastTickets(
		$this->view->from,
		$this->view->to,
		self::LAST_TICKETS_COUNT_LIMIT,
		$this->view->stakeFrom,
		$this->view->stakeTo,
		$this->view->rateFrom,
		$this->view->rateTo,
		$this->view->winFrom,
		$this->view->winTo);

	$branchIds = explode(';', $ws->Parameter->getGlobalParameter('branch.inspectTickets'));
	$this->view->inspectedBranches = $branchIds;

	$this->registerJsInclude('commonAjax');
}

public function onlineRisklimitAction() {
	$this->view->ajax = $this->_request->getParam('ajax');
	if ( !empty($this->view->ajax) ) {
		$this->_helper->layout()->disableLayout();
	}
	$ws = Zend_Registry::get('ws');
	$this->view->data = $ws->Bet->getTopRisklimit(static::ONLINE_RISKLIMIT_ITEMS_COUNT);
}

public function liveTicketStornoAction() {
	if ( !empty($_POST['ticketId']) ) {
		try {
			Webservice_Livebetting::cancelNotConfirmedTicket($_POST['ticketId']);
			$this->view->msg = I18n::tr('live_ticket_cancelation_ok');
		}
		catch( Exception $e ) {
			$this->view->msg = $e->getMessage();
		}
		
	}
}




}
