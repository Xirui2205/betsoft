<?php

class AlertBranchTicketsCount extends It6_Cron_Job_Email_AlertEmail {

	const PARAM_BRANCHES = 'branches';
	
	public $branches;
	
	public function prepareEmail() {
		parent::prepareEmail();
		$this->branches = isset($this->params[self::PARAM_BRANCHES])
			? $this->params[self::PARAM_BRANCHES] : array();

		if ( is_object($this->branches) )
			$this->branches = array($this->branches); 
	}

}
