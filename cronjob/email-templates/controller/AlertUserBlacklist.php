<?php

class AlertUserBlacklist extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_BLACKLIST = 'blacklist';

	public $blacklist;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->blacklist = isset($this->params[self::PARAM_BLACKLIST])
			? $this->params[self::PARAM_BLACKLIST] : array();

		if ( is_object($this->blacklist) )
			$this->blacklist = array($this->blacklist);
	}


	public function getBodyHtml($layoutScript=null) {
		return parent::getBodyHtml('plain');
	}

}
