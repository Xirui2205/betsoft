<?php

class RegistrationController extends Zend_Controller_Action {

	public function init() {

		$this->view->oneColumn = true;

		$this->view->addHelperPath('views/helpers/', 'My_View_Helper');

		Models_BasicRender::render($this->view, $this->_request); //musi byt zde az druhy
		if ($this->view->log == 2 || $this->view->log == 3) {
			header('Location: ' . PROTOCOL . $_SERVER['HTTP_HOST']);
			exit;
		}
		require_once "class/class.Date.php";
		It6_GlobalCache::turnOff();
	}

	public function indexAction() {
		$db = Zend_Registry::get('db');
		$ws = Zend_Registry::get('ws');
		
		// testovací hodnoty ---------------------------------------------------
		$this->view->test = isset($_GET['nikola']);
		if ($this->view->test) {
			$testData = array(
				'email' => 'delete'.time().'@example.com',
				'salutation' => 'f',
				'name' => 'Nikola'.time(),
				'surname' => 'Brown',
				'address' => 'Ve Smečkách 1, Praha-Praha 1, Česká republika',
				'lat' => '50.0774656',
				'lng' => '14.426385699999969',
				'postal_code' => '110 00',
				'misto' => 'Praha 1, Praha',
				'country' => '3',
				'ulice' => 'Ve Smečkách 606/1',
				'date_of_birth' => '29.10.1996',
				'date_of_birth_system_format' => '1996-10-29',
				'predvolba' => '3',
				'phone_number' => '123123123',
				'mena_id' => '8',
				'confirm_terms' => '0',
				'newsletter' => '1'
			);
			foreach ($testData as $key => $value) {
				if (!isset($_POST[$key])) {
					$_POST[$key] = $value;
				}
			}
			$pass = rand(100000, 999999);
			$this->view->typePassJs = "$('#password').val('$pass');$('#password_check').val('$pass');";
		}
		// test konec ----------------------------------------------------------
		
		$signUpForm = new Zend_Form(array(
			'action' => $_SERVER['REQUEST_URI']/*'./'*/,
			'method' => Zend_Form::METHOD_POST,
			'id' => 'regform'
		));
		$emailElement = new Zend_Form_Element_Text('email');
		$emailElement->setOptions(array(
			'label' => 'reg_email',
			'placeholder' => 'email@example.com',
			'required' => true,
			'filters' => array('StringTrim'),
			'validators' => array(
				array('EmailAddress', true, array(
						'messages' => array('emailAddressInvalidFormat' => 'reg_email_invalid'))),
				array('Db_NoRecordExists', true, array(
						'table' => 'uzivatel',
						'field' => 'email',
						'messages' => array('recordFound' => 'reg_e_msgemailexists'))),
				array('stringLength', false, array(0, 60))
		)));

		$passwordElement = new Zend_Form_Element_Password('password');
		$passwordElement->setLabel('reg_pass')
				->setRequired()
				->addValidator('stringLength', false, array(6, 40));

		$passwordCheckElement = new Zend_Form_Element_Password('password_check');
		$passwordCheckElement->setLabel('reg_pass_again')
				->addValidator('Identical', false, array('token' => $_POST['password'],
					'messages' => array('notSame' => 'reg_e_msgneqpass2')));

		$salutationElement = new Zend_Form_Element_Radio('salutation');
		$salutationElement->setLabel('reg_salutation')
				->setMultiOptions(array(
					'm' => 'reg_mr',
					'f' => 'reg_ms'
				))
				->setValue('m')
				->setRequired();
		
		$nameElement = new Zend_Form_Element_Text('name');
		$nameElement->setLabel('reg_name')
				->setRequired()
				->addValidator('stringLength', false, array(2, 50));

		$surnameElement = new Zend_Form_Element_Text('surname');
		$surnameElement->setLabel('reg_surname')
				->setRequired()
				->addValidator('stringLength', false, array(2, 50));

		// maps autocomplete naplní hidden inputy (kód v pohledu)
		$addressElement = new Zend_Form_Element_Text('address', array(
			'placeholder' => $this->view->trans('reg_address_placeholder')
		));
		$addressElement->setLabel('reg_address')
				->setRequired()
				->addValidator('stringLength', false, array(2, 255));
		$latElement = new Zend_Form_Element_Hidden('lat');
		$lngElement = new Zend_Form_Element_Hidden('lng');
		$postalCodeElement = new Zend_Form_Element_Hidden('postal_code');
		$locationElement = new Zend_Form_Element_Hidden('misto');
		$countryElement = new Zend_Form_Element_Hidden('country');
		$streetElement = new Zend_Form_Element_Hidden('ulice');
		
		$latElement->setRequired();
		$lngElement->setRequired();
		$streetElement->setRequired();
		$countryElement->setRequired();
		// (některé státy nemají PSČ)

		$dateOfBirthElement = new Zend_Form_Element_Text('date_of_birth', array(
			'readonly' => 'readonly'
		));
		$dateOfBirthElement->setLabel('reg_narozeni');
		$dateOfBirthSystemFormatElement = new Zend_Form_Element_Hidden('date_of_birth_system_format');
		$dateOfBirthSystemFormatElement->setRequired()
				->addValidator(new It6_Validate_BirthDate2());
		
		$callingCodes = array();
		
		// zakázané země
		$forbiddenCountries = array();
		foreach ($ws->Country->getForbiddenCountries() as $fc) {
			$forbiddenCountries[] = $fc['code'];
		}
		$this->view->forbiddenCountries = json_encode($forbiddenCountries);
		$forbiddenCountriesIds = array();
		
		$countryIdCodeCurrencyArray = array();
		foreach ($ws->Country->getAll() as $country) {
			$callingCodes[$country['countryId']] = ($country['callingCode'] != 'NONE' ? '+' : '')
					. $country['callingCode'];
			$countryIdCodeCurrencyArray[$country['code']] = array($country['countryId'], $country['currencyId']);
			if (in_array($country['code'], $forbiddenCountries)) {
				$forbiddenCountriesIds[] = $country['countryId'];
			}
		}
		$this->view->countryJson = json_encode($countryIdCodeCurrencyArray);
		$callCodeElement = new Zend_Form_Element_Select('predvolba');
		$callCodeElement->setDisableTranslator(true) // aby se nepřekládaly options
				->setLabel($this->view->trans('reg_callcode'))
				->setMultiOptions($callingCodes)
				->setRequired(false);
		$phoneNumberElement = new Zend_Form_Element_Text('phone_number', array(
			'placeholder' => $this->view->trans('reg_phone_placeholder'),
			'style' => 'width: 55%',
			'maxlength' => 20
		));
		$phoneNumberElement->setLabel('reg_tel')
				->setRequired(false)
				->addValidator('Digits', false, array(
					'messages' => array(
						'notDigits' => 'only_numbers'
					)
				))
				->addValidator('stringLength', false, array(1, 20));

		$currencyIdName = array();
		
		$exts = array(
			new It6_WsExtension_Client_Order('order', array('(mena_text)'))
		);
		foreach ($ws->ext($exts)->Currency->getAll() as $currency) {
			$currencyIdName[$currency['currencyId']] = strtoupper($currency['name']);
		}
		$currencyElement = new Zend_Form_Element_Select('mena_id');
		$currencyElement->setDisableTranslator(true) // aby se nepřekládaly options
				->setLabel($this->view->trans('reg_currency'))
				->setMultiOptions($currencyIdName)
				->setRequired();

		$confirmTermsElement = new Zend_Form_Element_Checkbox('confirm_terms');
		$confirmTermsElement->setLabel('reg_confirm')
				->setUncheckedValue(null)
				->setRequired()
				->setValue(true);

		$confirmNewsElement = new Zend_Form_Element_Checkbox('newsletter');
		$confirmNewsElement->setLabel('reg_news')
				->setValue(true);

		$captchaElement = new Zend_Form_Element_Captcha('captcha', array(
			'label' => 'captcha_label',
			'placeholder' => $this->view->trans('captcha_label'),
			'captcha' => array(
				'captcha' => 'Image',
				'wordLen' => 4,
				'fontSize' => 25,
				'timeout' => 1200,
				'width' => 294,
				'height' => 55,
				'imgUrl' => '/captcha',
				'imgDir' => ROOT . 'web/www/captcha',
				'font' => ROOT . 'web/www/fonts/Arial_Bold.ttf')
			));
		
		$sendElement = new Zend_Form_Element_Submit('send', array(
			'label' => 'reg_submit',
			'class' => 'btn btn-biggest'
		));
		
		// pořadí elementů je v pohledu, ne zde
		$elements = array(
			$emailElement,
			$passwordElement,
			$passwordCheckElement,
			$salutationElement,
			$nameElement->removeDecorator('DtDdWrapper')->removeDecorator('Label'),
			$surnameElement,
			$addressElement,
			$latElement->removeDecorator('Label'),
			$lngElement->removeDecorator('Label'),
			$postalCodeElement->removeDecorator('Label'),
			$locationElement->removeDecorator('Label'),
			$countryElement->removeDecorator('Label'),
			$streetElement->removeDecorator('Label'),
			$dateOfBirthElement,
			$dateOfBirthSystemFormatElement->removeDecorator('Label'),
			$callCodeElement,
			$phoneNumberElement,
			$currencyElement,
			$confirmTermsElement,
			$confirmNewsElement,
			$captchaElement,
			$sendElement->removeDecorator('DtDdWrapper')
		);
		foreach ($elements as $element) {
			$element->removeDecorator('DtDdWrapper')
					->removeDecorator('Label')
					->removeDecorator('Errors')
					->removeDecorator('Br')
					->removeDecorator('HtmlTag');
		}
		$signUpForm->addElements($elements);
		
		$this->view->generalError = '';
		if ($_POST
				&& $signUpForm->isValid($_POST)
				// pro jistotu i zakázaná země, pokud by změnil útočník hodnotu v hidden
				&& !in_array($this->_getParam('country'), $forbiddenCountriesIds) ) {
			$userData1 = array(
				'firstName' => $this->_getParam('name'),
				'lastName' => $this->_getParam('surname'),
				'street' => $this->_getParam('ulice'),
				'zip' => $this->_getParam('postal_code'),
				'birthDate' => $this->_getParam('date_of_birth_system_format')
			);
			if ($ws->User->dulicateRegistration($userData1)) {
				$this->view->generalError = $this->view->trans('error_check_user');
			}else if ($userId = $ws->User->insert(array_merge($userData1, array(
				'email' => $this->_getParam('email'),
				'password' => $this->_getParam('password'),
				'sex' => $this->_getParam('salutation'),
				'phone' => $this->_getParam('phone_number'),
				'address' => $this->_getParam('address'),
				'lat' => $this->_getParam('lat'),
				'lng' => $this->_getParam('lng'),
				'town' => $this->_getParam('misto'),
				'countryId' => $this->_getParam('country'),
				'currencyId' => $this->_getParam('mena_id'),
				'languageId' => isset($_SESSION['lang_id']) ? $_SESSION['lang_id'] : 2/*en*/,
				'sendNewsletter' => $this->_getParam('newsletter'),				
				'areaCode' => $this->_getParam('predvolba')
			)))) {
				$ws->User->sendActivationMail($userId, $this->_getParam('email'), $this->view->urlSet(41), $this->view);
				$this->_redirect($this->view->urlSet(108)); // potvrzení registrace
			}else{
				// die('insert error');
				// asi chyba db, co se dá dělat
				It6_Log::error('Registration insert error!');
			}
		}else if($_POST){
			$this->view->generalError = $this->view->trans('reg_general_error');
		}
		$this->view->signUpFormObject = $signUpForm;
	}

	public function confirmAction() {
	}

	public function activateAction() {
		$ws = Zend_Registry::get('ws');
		$this->view->isAuthorizedForActivation = false;
		$this->view->alreadyActivated = false;
		if ($userId = $ws->User->isAuthorizedForActivation($this->_getParam('email'), $this->_getParam('q'))) {
			$this->view->isAuthorizedForActivation = true;
			if ($ws->User->activate($userId)) {
				// ok
				// automaticky přihlásit? bezpečnostní riziko
			}else{
				$this->view->alreadyActivated = true;
			}
		}
	}

	public function depositAction() {
		
	}

	public function playAction() {
		
	}

}
