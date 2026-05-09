<?php

class AlertTicketCreated extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_TICKET_ID = 'ticketId';
	const PARAM_USER_ID = 'userId';
	const PARAM_AMMOUNT = 'ammount';
	
	public $blacklist;
	
	public function prepareEmail() {
		parent::prepareEmail();
		$this->ticketId = $this->params['ticketId'];
		$this->userId = $this->params['userId'];
		$this->amount = $this->params['amount'];
	}

}

