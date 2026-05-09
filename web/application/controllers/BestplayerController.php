<?php

class BestplayerController extends Zend_Controller_Action {


	public function indexAction() {
		$this->_forward('not-found','static-page');
			return;
	}

	public function ticketGameAction() {
		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
		Models_BasicRender::render($this->view,$this->_request);

		$gameId = $this->_request->getParam('g');
		$ticketRank = $this->_request->getParam('t');
		if (!empty($gameId) && !empty($ticketRank)) {
			$ws = Zend_Registry::get('ws');
			$data = $ws->Campaign->getTicketGameTicketByRank($gameId, $ticketRank, $_SESSION['lang_id']);
			if (!empty($data)) {
				$ticket = It6_ArrayWrapper::toNativeArray($data['ticket']);
				$this->view->rank = $ticketRank;
				$this->view->gameName = $data['gameName'];
				$this->view->ticketState = It6_Models_Ticket::getRealTicketState($ticket);
				$this->view->currency = $ticket['currencyName'];
				$this->view->username = $data['username'];
			}
			$this->view->ticket = $ticket;
			$this->view->hideHandle = true;
			return;
		}
		$this->_forward('not-found', 'static-page');
	}

}
