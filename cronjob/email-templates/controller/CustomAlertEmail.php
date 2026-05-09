<?php

class CustomAlertEmail extends It6_Cron_Job_Email_AlertEmail {

// see params in parent class

	public function prepareEmail() {
		parent::prepareEmail();

		$this->bodyText			= $this->params['bodyText'];
		$this->subject			= $this->params['subject'];
	}

}
