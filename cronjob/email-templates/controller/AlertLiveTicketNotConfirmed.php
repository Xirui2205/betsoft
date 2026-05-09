<?php

class AlertLiveTicketNotConfirmed extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_TICKETS = 'tickets';

	public $tickets;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->tickets = isset($this->params[self::PARAM_TICKETS])
			? $this->params[self::PARAM_TICKETS] : array();

		if ( is_object($this->tickets) )
			$this->tickets = array($this->tickets);
	}



	public function getBodyHtml($layoutScript=null) {
		return parent::getBodyHtml('plain');
	}
}
