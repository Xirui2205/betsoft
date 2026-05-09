<?php
class Models_Helpers_RegistrationNextstep {

	public static function createToken($view) {
		$token = new It6_Form_Element_Hash('registrationNextstepToken');
		if (isset($view)) {
			$view->registrationNextstepToken = $token->getHash();
		}
		return $token;
	}

	public static function validate($view, $partial = false) {
		try {
			$form = new Zend_Form;
			$form->setAction('./')->setMethod('post');

			$fields_check = array('ulice', 'cp', 'psc', 'misto');

			$userCheck = $form->createElement('hidden', 'userCheck')
				->addValidator(new It6_Validate_UserCheck($fields_check), true)
				->setValue('userCheck');
			$form->addElement($userCheck);

			$ulice = $form
				->createElement( 'text', 'ulice')
				->setRequired(true);
			$form->addElement($ulice);

			$cp = $form
				->createElement('text', 'cp')
				->setRequired(true);
			$form->addElement($cp);

			$psc = $form
				->createElement('text', 'psc')
				->addValidator(new It6_Validate_Zip(), true)
				->setRequired(true);
			$form->addElement($psc);

			$misto = $form
				->createElement( 'text', 'misto')
				->addValidator(new It6_Validate_Town(), true)
				->setRequired(true);
			$form->addElement($misto);

			$formtimer = new Zend_Form_Element_Text('loadtime');
			$form->addElement($formtimer);
			$formtimer->addValidator(new It6_Validate_FormTimer('loadtime'), true);

			$honeypot = new Zend_Form_Element_Text('hrnecmedu');
			$form->addElement($honeypot);
			$honeypot->addValidator(new It6_Validate_HoneyPot('hrnecmedu'), true);

			$confirm = new Zend_Form_Element_Radio('confirm');
			$confirm->setRegisterInArrayValidator(false);
			$confirm->setRequired(true);
			$form->addElement($confirm);

			$newsletter = new Zend_Form_Element_Checkbox('news');
			$newsletter->setCheckedValue('yes');
			$form->addElement($newsletter);

			if ($partial == false) $form->addElement(self::createToken($view));

			//smazu mezery na zacatku a konci hodnot POSTu
			self::trimValues($_POST);

			if ($partial == true) {

				$isValid = $form->isValidPartial($_POST);
				$er = $form->getMessages();
				$view->error = $er;
				return $isValid;

			} elseif ($form->isValid($_POST)) {

				$regStatus = true;
				$user = self::getUserEntity($form);

				try {
					$user->userId = Zend_Registry::get('ws')->User->Insert($user);
					if (!empty($user->userId)) Webservice_IncompleteRegistration::deleteIncompleteRegistration(Zend_Session::getId());
					session_regenerate_id();

					$sess = new Zend_Session_Namespace('Affiliate');

					if (!empty($user->userId) && !empty($sess->partner) && !empty($sess->banner)) {
						if (Webservice_AffiliatePartner::insertUser($user->userId, $sess->partner, $sess->banner)) {
							It6_Log::info(
								'New user assigned to affiliate partner.',
								It6_Log::TAG_USER_OPERATION,
								array('userId' => $user->userId, 'partnerId' => $sess->partner, 'bannerId' => $sess->banner)
							);
							$sess->unsetAll();
						}
					}

					$text = str_replace("{username}", $user->username, Zend_Registry::get('translate')->trans("sms_registration_nick_text", null, null));

					$plainSms = new It6_Sms_PlainSms();
					$smsId = $plainSms->setToNumber($user->phone)
						->setText($text, It6_Sms_PlainSms::SMS_TYPE_REGISTRATION_NICK)
						->sendSms();

				} catch (Exception $e) {
					$regStatus = false;
					$form->addError($view->trans($e->getMessage()));
				}

				if ($regStatus == false) {
					$view->registration = 'FAILED';
					$view->values = $form->getValues();
					$view->error = $form->getMessages();
				} else {
					$view->registration = 'OK';
					return true;
				}

			} else {

				$values = $form->getValues();
				$er = $form->getMessages();
				$view->error = $er;
				$view->values = $values;

			}
			return false;
		}

		catch ( Exception $e ) {
			Models_Exception_Handler::handle($e);
			return false;
		}
	}

	public static function trimValues(&$values) {
		foreach ($values as $key => $value){
			if (is_array($value)) self::trimValues($value);
			else $values[$key] = trim($value);
		}
	}

	public static function getUserEntity(Zend_Form $form) {
		$user = new Entities_User();

		// Get data from form part one
		$data = Webservice_IncompleteRegistration::getIncompleteReg(Zend_Session::getId());

		$user->firstName = $data["jmeno"];
		$user->lastName = $data["prijmeni"];
		$user->username = $data["nick"];
		$user->password = $data["heslo"];
		$user->citizenId = $data["citizen_id"];
		$user->birthDate = $data["datum_narozeni"];
		$user->email = $data["email"];
		$user->phone = $data["telefon"];
		$user->areaCode = $data["area_code"];

		//form form
		$user->userId = null;
		$user->street = $form->getValue('ulice') . ' ' . $form->getValue('cp');
		$user->town = $form->getValue('misto');
		$user->zip = $form->getValue('psc');
		$user->registrationTime = It6_Date::dbNow();
		$user->sendNewsletter = $form->getElement('news')->isChecked() ? 1 : 0;
		$user->countryId = 3; //CZ
		$user->branchId = It6_Models_Branch::ID_INTERNET;
		return $user;
	}

	public static function sendConfirmEmail($user) {
		$params = array(
			It6_Cron_Job_Email::PARAM_EMAIL_TYPE => 'NewUserRegistration',
			'user_id' => $user->id
		);
		$cronJob = array(
			'type' => 1,
			'date' => It6_Date::dbNow(),
			'params' => $params
		);

		if (Zend_Registry::get('ws')->CronJob->insert($cronJob)) return true;
		else return false;
	}
}