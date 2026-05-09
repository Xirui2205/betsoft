<?php
class CompetitionsController extends It6_Controller_Abstract {

	const COMPETITIONS_SECTION_ID = 339;
	const TICKETS_GAME_TBODY_LAYOUT = 'tickets-game-tbody';

	private $filterData = array();
	private $paginatorData = array('recsPerPage' => 30);
	private $orderData = array();

	public function ticketsAction() {

		$ws = Zend_Registry::get('ws');

		$games = array();
		$games_s = It6_ArrayWrapper::toNativeArray($ws->Campaign->getAllTicketGame());
		foreach ( $games_s as $game ) {
			$games[$game["gameId"]] = i18n::tr($game["gameName"]);
		}

		$inData = $this->getRequest()->getParams();
		$extensions = array();

		$this->filterData['cancelled'] = 1;
		$this->filterData['allowed'] = 1;
		// prepare wsForm data from $_GET
		if (!empty($inData['filter'])) $this->filterData = $inData['filter'];
		if (!empty($inData['paginator'])) $this->paginatorData = $inData['paginator'];
		if (empty($inData['order'])) $inData['order'] = array('evaluation_DESC' => 1);
		$this->orderData = array_keys($inData['order']);

		if (isset($inData['cancell'])) {
			$ticket = array_keys($inData['cancell']);
			$ticketId = reset($ticket);
			if ($ws->Campaign->cancellTicket($ticketId, $this->filterData['games'])) {
				$this->view->feedbackMsg = UiUtil::printMessages('ticket_cancell_ok , ids: {0}', $ticketId, TRUE);
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors('ticket_cancell_error, ids: {0}', $ticketId, TRUE);
			}
		} else if (isset($inData['allow'])) {
			$ticket = array_keys($inData['allow']);
			$ticketId = reset($ticket);
			if ($ws->Campaign->allowTicket($ticketId, $this->filterData['games'])) {
				$this->view->feedbackMsg = UiUtil::printMessages('ticket_allow_ok , ids: {0}', $ticketId, TRUE);
			} else {
				$this->view->feedbackMsg = UiUtil::printErrors( 'ticket_allow_error , ids: {0}', $ticketId, TRUE);
			}
		}

		$filter = new It6_WsForm_Filter(
			array(
				array('Game', 'games', 'select', array(array('gameId', '=', '?')), null, null, $games),
				array('Ticket handle', 'handle', 'text', array(array('handle', '=', '?')), null, null, null),
				array('Cancelled', 'cancelled', 'checkbox', array(array('cancelled', '='))),
				array('Allowed', 'allowed', 'checkbox', array(array('cancelled', '='))),
				array(i18n::tr('dateStart'), 'dateStart', 'date', array(array('zalozen', '>=', '?')), 'It6_Validate_Date', null, 'start'),
				array(i18n::tr('dateTo'), 'dateTo', 'date', array(array('zalozen', '<', '?')), 'It6_Validate_Date', null, 'to'),
			)
		);
		$filter->getExtension($this->filterData, $extensions);

		$filter->getFilterForm()->getElement('submit')
			->setAttrib('onClick',"this.form.action='?section=".self::COMPETITIONS_SECTION_ID."; return true;");

		//create table
		$table = new It6_WsForm_Table(array(
			array('Ticket', 'ticketId'),
			array('Zadáno', 'created'),
			array('Uživatel', 'nick'),
			array('Vsazeno', 'cash'),
			array('Kurz', 'rate'),
			array('Výhra', 'win'),
			array('Evaluation', 'evaluation'),
			array('Zrušen', 'cancelled'),
			array('Akce', null),
		));
		$table->getColumnsExtension($extensions);
		$table->getOrderExtension($this->orderData, $extensions);

		if ( !empty($_REQUEST['filter']) ) {
			$this->view->tickets = $ws->Campaign->getTicketsByGame(
				$this->filterData['games'], 
				$this->filterData['dateStart'], 
				$this->filterData['dateTo'], 
				$this->orderData, 
				$this->filterData['cancelled'], 
				$this->filterData['allowed'],
				$this->filterData['handle']
			);
		} else {
			$this->view->tickets = array();
			$this->view->notFilter = true;
		}

		$items = It6_ArrayWrapper::toNativeArray($this->view->tickets);
		$this->view->filter = $filter->getLayout(null, $this->filterData);
		$this->view->tHead = $table->getTheadLayout(null, $this->orderData);
		$this->view->tBody = $table->getTbodyLayout(static::TICKETS_GAME_TBODY_LAYOUT, $items);
	}
}