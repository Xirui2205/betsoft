<?php
class TotalReport extends It6_Cron_Job_Email_ReportEmail {

	const PARAM_DATA_ALL = 'data_all';
	const PARAM_DATA_INTERNET = 'data_internet';
	const PARAM_DATA_BRANCH = 'data_branch';
	const PARAM_DATA_LIVE = 'data_live';

	public $data_all;
	public $data_internet;
	public $data_branch;
	public $data_live;

	public function prepareEmail() {
		parent::prepareEmail();
		$this->data_all = $this->params[self::PARAM_DATA_ALL];
		$this->data_internet = $this->params[self::PARAM_DATA_INTERNET];
		$this->data_branch = $this->params[self::PARAM_DATA_BRANCH];
		$this->data_live = $this->params[self::PARAM_DATA_LIVE];
	}

	public function getBodyHtml($layoutScript = null) {
		return parent::getBodyHtml('plain');
	}
}