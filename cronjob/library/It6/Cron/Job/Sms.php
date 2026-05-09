<?php

class It6_Cron_Job_Sms extends It6_Cron_Job_Abstract {

	// paths will be appended to ROOT constant
	const EMAIL_CONTROLLER_PATH = "cronjob/sms-templates/controller";
	const EMAIL_VIEW_PATH = "cronjob/sms-templates/view";
	
	const PARAM_SMS_TYPE = 'sms_type';
	const ERROR_NOT_SENT = 1;

	public function execute(array $params = array(), &$errorMessage = null) {

		$result = parent::execute($params, $errorMessage);
		if (0 != $result)
			return $result;

		if ( empty($params[self::PARAM_SMS_TYPE]) ) {
			throw new Exception('SMS type not specified');
		}
		
		$smsType = $params[self::PARAM_SMS_TYPE];
		
		//switch - how to send different types of short messages
		switch ($smsType) {
			case 'TicketResults':
				$ticketsResults = new It6_Cron_Job_Sms_TicketsResults($params['ticketIds']);
				$ticketsResults->createSmsBatch();
				$ticketsResults->sendSmsBatch();
				break;

			case 'ActivationCode':
				$activationCode = new It6_Cron_Job_Sms_ActivationCode($params);
				$activationCode->sendSms();
				break;

			default:
				It6_Log::err('I dont send these types of text messages: ' . $smsType);
		}
		
		
		
	}

}
