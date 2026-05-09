<?php

class Models_Helpers_ProviderRegistration {


	public static function processForm($values) {
		unset($values['submit']);
		unset($values['captcha']);

		$params = array(
			It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'ProviderRegistrationEmail'
		);
		$cronJob = array(
			'type' 		=> 1,
			'date'		=> It6_Date::dbNow(),
			'params'	=> array_merge($values, $params)
		);

		if(Zend_Registry::get('ws')->CronJob->insert($cronJob)) {
			It6_Log::info(
				'Branch Provider Registerd',
				It6_Log::TAG_WEB,
				$values
			);
		}
	}

}
