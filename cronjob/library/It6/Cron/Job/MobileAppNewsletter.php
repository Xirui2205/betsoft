<?php

class It6_Cron_Job_MobileAppNewsletter extends It6_Cron_Job_Abstract {
	
	public function execute(array $params = array(), &$errorMessage = null) {
		$ws = Zend_Registry::get('ws');

		$db = Zend_Registry::get('db');
		$round = $db->query("select round from newsletter_types where id = 2")->fetch();
		$round = $round["round"];

		$uzivatel = $db->select()
				->distinct()
				->from(array("u" => 'vic_main.uzivatel'))
				->where("anonymous = 0")
				->where("email != ''")
				->where("user_id NOT IN (select user_id from newsletter_history where round = $round AND newsletter_type_id = 2)")
				->limit(50)
				->query()
				->fetchAll();

		$html = new Zend_View();
		$html->setScriptPath(ROOT . 'files/templates/html/');

		foreach ($uzivatel as $u) {
			$userEmail = $u['email'];
			// zkontroluji je li email validni
			$emailValidator = new Zend_Validate_EmailAddress();
			if (!$emailValidator->isValid($userEmail)) {
				$db->insert("newsletter_history", array(
					"user_id" => $u["user_id"],
					"sent" => It6_Date::dbNow(),
					"newsletter_type_id" => 2,
					"round" => $round,
					"success" => 0,
					"error" => "email $userEmail not valid"
				));
				continue;
			}
			
			$unsubscribeHash = It6_Models_Newsletter::getHash($u['user_id'], $userEmail);
			$html->unsubscribeLink = PROTOCOL . WEBHOST . '/cs/newsletter/odhlasit/x/'.$unsubscribeHash.'/email/'.$u['email'];
			
			$bodyText = $html->render('newsletter_mobilni_aplikace.html');

			// Sending email
			$mail = new Zend_Mail('UTF-8');
			$mail->addTo($userEmail);
			$mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
			$mail->setReplyTo(REPLY_TO_ADDRESS, REPLY_TO_NAME);
			$mail->addHeader('List-Unsubscribe', $html->unsubscribeLink);
			$mail->setSubject("Vsaďte si s novou mobilní aplikací CompBet!");
			$mail->setBodyHtml($bodyText);
			
			try {
				if ($mail->send()) {
					It6_Log::info('Newsletter email about new mobile app was sent to adress: ' . $userEmail);
					$db->insert("newsletter_history", array(
						"user_id" => $u["user_id"],
						"sent" => It6_Date::dbNow(),
						"newsletter_type_id" => 2,
						"round" => $round,
						"success" => 1
					));
				}
			} catch (Exception $e) {
				$db->insert("newsletter_history", array(
					"user_id" => $u["user_id"],
					"sent" => It6_Date::dbNow(),
					"newsletter_type_id" => 2,
					"round" => $round,
					"success" => 0,
					"error" => $e->getMessage()
				));
				It6_Log::err('Newsletter email about new mobile app not be sent. Exception:' . $e->getMessage());
			}
		}

		$users = $db->select()
				->distinct()
				->from('vic_main.uzivatel')
				->where("anonymous = 0")
				->where("email != ''")
				->where("user_id NOT IN (select user_id from newsletter_history where round = $round AND newsletter_type_id = 2)")
				->query()
				->fetchAll();

		if (count($users) > 0) {
			It6_Models_CronJob::scheduleJobNamed("MobileAppNewsletter", array(), time());
		}
	}

}
