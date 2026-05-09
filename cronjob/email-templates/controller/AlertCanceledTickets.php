<?php

class AlertCanceledTickets extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_CANCELED_TICKETS = 'canceledTickets';
	
	public $canceledTickets;
	
	public function prepareEmail() {
		parent::prepareEmail();
		$this->canceledTickets = isset($this->params[self::PARAM_CANCELED_TICKETS])
			? $this->params[self::PARAM_CANCELED_TICKETS] : array();

		if ( is_object($this->canceledTickets) )
			$this->canceledTickets = array($this->canceledTickets); 
	}

}
