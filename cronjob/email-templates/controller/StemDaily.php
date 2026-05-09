<?php
class StemDaily extends It6_Cron_Job_Email_ReportEmail {

	const PARAM_SUBJECT = 'subject';
	
	public $subject;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->subject = $this->params[self::PARAM_SUBJECT];
	}
}