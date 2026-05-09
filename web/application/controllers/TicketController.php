<?php

class TicketController extends Zend_Controller_Action {

	const MAX_TICKET_SEARCH_NOT_FOUNDS = 10;
	const MAX_TICKET_SEARCH_NOT_FOUNDS_TIME_WINDOW = 600;


	public function indexAction() {
		It6_GlobalCache::turnOff();
		Models_BasicRender::render($this->view,$this->_request);
		$key = 'ticket_search_not_found_'.It6_Php::getRemoteAddr();
		$value = It6_GlobalCache::getKey($key);
		
		if ( empty($value) )
			$value = 0;

		if ( $value > static::MAX_TICKET_SEARCH_NOT_FOUNDS )
			$this->view->err = 'ticket_search_blocked';
		else {
			$handle	= $this->_request->getParam('t');

			Models_MyAccount_Ticket::getTicketByHandle($this->view, $handle);

			if ( empty($this->view->ticket) || empty($this->view->ticket['state']) ) {
				Models_MyAccount_LiveTicket::getTicketByHandle($this->view, $handle);
				
				if ( !is_array($this->view->ticket) || count($this->view->ticket) == 0 ) {
					$oldTicket = Models_Ticket_OldTicket::findOldTicket($handle);
					if ( empty($oldTicket) ) {
						$this->view->err = 'ticket_not_found';
						It6_GlobalCache::setKey($key,$value + 1,static::MAX_TICKET_SEARCH_NOT_FOUNDS_TIME_WINDOW);
					}
					else
						$this->view->oldTicket = $oldTicket;
				}
				else
					$this->view->live = true;
			}
			else
				$this->view->live = false;
		}

		Models_Helpers_Panels::leftAndRightColAndNews($this->view);
	}
}
