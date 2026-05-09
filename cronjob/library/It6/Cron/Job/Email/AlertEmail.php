<?php

abstract class It6_Cron_Job_Email_AlertEmail extends It6_Cron_Job_Email_ControllerAbstract {

	const PARAM_TO = "to";
	
	public function __construct(array $params) {
		$this->params = $params;
	}

	public function prepareEmail() {
		parent::prepareEmail();
	}

	public function getToField() {
		return $this->params[self::PARAM_TO];
	}

}
