<?php

class AlertHostInOutStatus extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_HOSTS = 'hosts';

	public $hosts;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->hosts = isset($this->params[self::PARAM_HOSTS])
			? $this->params[self::PARAM_HOSTS] : array();

		if ( is_object($this->hosts) )
			$this->hosts = array($this->hosts);
	}



	public function getBodyHtml($layoutScript=null) {
		return parent::getBodyHtml('plain');
	}
}
