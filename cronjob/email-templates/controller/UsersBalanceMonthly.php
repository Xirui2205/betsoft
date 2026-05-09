<?php
class UsersBalanceMonthly extends It6_Cron_Job_Email_ReportEmail {

	const PARAM_DATE = 'date';

	public $date;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->date = $this->params[self::PARAM_DATE];
	}

}