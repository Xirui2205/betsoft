<?php

class It6_Cron_Job_Email extends It6_Cron_Job_Abstract {

	const PARAM_EMAIL_TYPE = 'email_type';

	// paths will be appended to ROOT constant
	const EMAIL_CONTROLLER_PATH = "cronjob/email-templates/controller";
	const EMAIL_VIEW_PATH = "cronjob/email-templates/view";
	const EMAIL_LAYOUT_PATH = "cronjob/email-templates/layout";

	const ERROR_NOT_SENT = 1;

	public function execute(array $params = array(), &$errorMessage = null){

		$result = parent::execute($params, $errorMessage);
		if (0 != $result)
			return $result;

		if (empty($params[self::PARAM_EMAIL_TYPE]))
			throw new Exception('Email type not specified');
		$emailType = $params[self::PARAM_EMAIL_TYPE];
		if ( is_numeric($emailType) ) {
			//$emailType = 'Emailtype' . $emailType;
			It6_Log::err('Numeric email type are deprecated: ' . $emailType);
			return self::ERROR_NOT_SENT;
		}
		$controllerFile = ROOT . self::EMAIL_CONTROLLER_PATH . '/' . $emailType . '.php';
		if (!file_exists($controllerFile))
			throw new Exception('Email controller script not found: "' . $controllerFile . '"');
		require_once($controllerFile);

		$email = new $emailType($params);

		if($email->prepareEmail() !== false) {
			$bodyText = $email->getBodyText();
			$subject = $email->getSubject();
			$BccField = $email->getBccField();
			$CcField = $email->getCcField();
			$ToField = $email->getToField();
			$bodyHtml = $email->getBodyHtml();
			$addAttach = $email->getAttachment();

			$mail = new Zend_Mail('UTF-8');
			$mail->addTo($ToField);
			$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
			$mail->setReplyTo(REPLY_TO_ADDRESS,REPLY_TO_NAME);
			$mail->setSubject(MAIL_SUBJECT_PREFIX. ' ' . $subject);
			$mail->setBodyText($bodyText);
			if(!empty($bodyHtml))
				$mail->setBodyHtml($bodyHtml);
			if(!empty($BccField))
				$mail->addBcc($BccField);
			if(!empty($CcField))
				$mail->addCc($CcField);
			if(!empty($addAttach))
				$mail->createAttachment($addAttach[0], $addAttach[1], $addAttach[2], $addAttach[3], $addAttach[4]);

			if($mail->send())
				return 0;
			else {
				if (isset($errorMessage))
					$errorMessage = 'Mail not sent.';
				return self::ERROR_NOT_SENT;
			}
		}
		else
			return 0;
	}
}