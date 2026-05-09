<?php
class Models_Helpers_Registration {

	public static function createToken($view) {
		$token = new It6_Form_Element_Hash('registrationToken');
		if (isset($view)) {
			$view->registrationToken = $token->getHash();
		}
		return $token;
	}

	/**
	 * Kontrola formulare
	 * @param object $view view object
	 * @return array
	 */
	public static function validate($view, $partial = false) {
		try {
			$form = new Zend_Form;
			$form
				->setAction('./')
				->setMethod('post');

			$firstName = $form
				->createElement( 'text', 'jmeno')
				->setRequired(true)
				->addValidator(new It6_Validate_FirstName(), true);
			$form->addElement($firstName);

			$lastName = $form
				->createElement( 'text', 'prijmeni')
				->setRequired(true)
				->addValidator(new It6_Validate_LastName(), true);
			$form->addElement($lastName);

			$idCard = $form
				->createElement('text', 'op')
				->addValidator(new It6_Validate_IdCard(), true)
				->setRequired(true);
			$form->addElement($idCard);

			$nick = new Zend_Form_Element_Text('username');
			$form->addElement($nick);
			$nick->setRequired(true);
			$nick->addValidator(new It6_Validate_Username(), true);
			$nick->addValidator(new It6_Validate_UsernameCheckDb(), true);

			$pass = new Zend_Form_Element_Password('pass_new');
			$form->addElement($pass);
			$pass->setRequired(true);
			$pass->addValidator(new It6_Validate_Password(), true);

			$pass2 = new Zend_Form_Element_Password('pass_again');
			$form->addElement($pass2);
			$pass2->setRequired(true);
			$pass2->addValidator(new It6_Validate_Password(), true);

			$bdayDay = $form
				->createElement('select', 'bday_day')
				->addMultiOptions(It6_Models_Form_Util::getDateCmbDays())
				->addValidator(new It6_Validate_DateDay(
					Zend_Controller_Front::getInstance()->getRequest()->getParam('bday_month'),
					Zend_Controller_Front::getInstance()->getRequest()->getParam('bday_year')
				), true);
			$form->addElement($bdayDay);

			$bdayMonth = $form
				->createElement('select', 'bday_month')
				->addMultiOptions(It6_Models_Form_Util::getDateCmbMonth())
				->addValidator(new It6_Validate_DateMonth(), true);
			$form->addElement($bdayMonth);

			$bdayYear = $form
				->createElement('select', 'bday_year')
				->addMultiOptions(It6_Models_Form_Util::getDateCmbYears(-18))
				->addValidator(new It6_Validate_DateYear(), true)
				->addValidator(new It6_Validate_Age(
					Zend_Controller_Front::getInstance()->getRequest()->getParam('bday_day'),
					Zend_Controller_Front::getInstance()->getRequest()->getParam('bday_month')
				), true);
			$form->addElement($bdayYear);

			$email = $form
				->createElement('text', 'email')
				->setRequired(true)
				->addValidator('EmailAddress', true)
				->addValidator(new It6_Validate_Email, true);
			$form->addElement($email);

			$areaCode = new Zend_Form_Element_Text('areaCode');
			$areaCode->setRequired(true);
			$form->addElement($areaCode);

			$phone = $form
				->createElement('text', 'telefon')
				->addValidator(new It6_Validate_PhoneNumber(), true)
				->setRequired(true);
			$form->addElement($phone);

			$formtimer = new Zend_Form_Element_Text('loadtime');
			$form->addElement($formtimer);
			$formtimer->addValidator(new It6_Validate_FormTimer('loadtime'), true);

			$honeypot = new Zend_Form_Element_Text('hrnecmedu');
			$form->addElement($honeypot);
			$honeypot->addValidator(new It6_Validate_HoneyPot('hrnecmedu'), true);

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
				$incomplete_user = self::getEntityIncompleteRegistration($form);

				try {
					$incomplete_user->sessionId = Zend_Registry::get('ws')->IncompleteRegistration->Insert($incomplete_user);
				}
				catch (Exception $e) {
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

	public static function getEntityIncompleteRegistration(Zend_Form $form) {
		$incomplete_user = new Entities_IncompleteRegistration();

		//from form
		$incomplete_user->sessionId			= Zend_Session::getId();
		$incomplete_user->firstName			= $form->getValue('jmeno');
		$incomplete_user->lastName			= $form->getValue('prijmeni');
		$incomplete_user->citizenId			= $form->getValue('op');
		$incomplete_user->username			= $form->getValue('username');
		$incomplete_user->password			= Models_Helpers_Help::cryptPass( Models_Helpers_Help::DecodeUnicodeUrl($form->getValue('pass_new')) );
		$incomplete_user->email				= $form->getValue('email');
		$incomplete_user->birthDate			= It6_Date::toDbAsDate($form->getValue('bday_day').'.'.$form->getValue('bday_month').'.'.$form->getValue('bday_year'));
		$incomplete_user->areaCode			= $form->getValue('areaCode');
		$incomplete_user->phone				= $form->getValue('telefon');
		$incomplete_user->registrationTime	= It6_Date::dbNow();
		return $incomplete_user;
	}
}