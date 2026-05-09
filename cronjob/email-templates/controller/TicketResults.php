<?php

class TicketResults extends It6_Cron_Job_Email_UserEmail {

	const PARAM_TICKET_IDS = 'ticketIds';

	public function prepareEmail(){
		$ws = Zend_Registry::get('ws');
		$ticketIds = $this->params[self::PARAM_TICKET_IDS];
		if (!is_array($ticketIds))
			$ticketIds = array($ticketIds);

		$this->tickets = $ws->Ticket->getByIdAndUserComplete($ticketIds, null, null, true);
		$this->tickets = reset($this->tickets);

		if (empty($this->tickets))
			throw new Exception('Error: The ticket does not exist!');
		else{
			foreach($this->tickets as $key => $ticket) {
				if(
					$ticket['stateNo'] == '1'
					|| $ticket['stateNo'] == '1L'
					|| $ticket['stateNo'] == '1W'
				)
					unset($this->tickets[$key]);
			}

			if(!empty($this->tickets)) {
				$ticket = reset($this->tickets);
				parent::prepareEmail();
				$userId = $ticket['userId'];
				$this->emailLang = $this->getUserLangId($userId);
				$this->terno = It6_Models_Markets::loadTernoMarkets($this->emailLang);
			}
			else
				return false;
		}
	}


	public function getToField() {
		$ticket = reset($this->tickets);
		$userId = $ticket['userId'];
		return $this->getUserEmail($userId);
	}
}
