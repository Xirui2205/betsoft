<?php

class Models_Form_UserProfile extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $userId = null, $mode = null) {
		require_once('controllers/UserProfileController.php');
		parent::__construct();

		$this->setName('UserProfileForm');
		$countries		= Models_Utils::getSelectOptions($this->ws->Country->getAll(), 'countryId');
		$languages		= Models_Utils::getSelectOptions($this->ws->Language->getAll(), 'languageId');
		$currencies		= Models_Utils::getSelectOptions($this->ws->Currency->getAll(), 'currencyId');
		$banks			= $this->ws->Bank->getAllOrder(array('bank_code'));
		$banks 			= It6_ArrayWrapper::toAssocArray($banks, 'bankId', '%bankCode%'. ' - '.'%bankName%', array(0 => '--None--'));
		$codes			= Models_Utils::getSelectOptions($this->ws->AreaCode->getAll(), 'areaCodeId','areaCode');
		$banks[0] = i18n::tr('none');
		$testingOpts	= array(
			'ne'		=> i18n::tr('Ne'),
			'test'		=> i18n::tr('Test'),
			'naseip'	=> i18n::tr('Nase Ip'),
			'auto'		=> i18n::tr('Auto'),
			'pobocka'	=> i18n::tr('Pobocka')
		);
		$blockedIPs		= array();
		if (!is_null($userId)) {
			$blockedIPsArray = It6_ArrayWrapper::toNativeArray($this->ws->User->getBlockedIps($userId));
			foreach ($blockedIPsArray as $blockedIP) {
				$blockedIPs[$blockedIP['block_ip']] = $blockedIP['block_ip'];
			}
		}
		
		$id = $this->createElement('hidden', 'userId');
		$this->addElement($id);


		if (UserProfileController::MODE_BOOKMAKER != $mode) {
			$isForbiden = $this
				->createElement('select', 'isForbiden')
				->setLabel(i18n::tr('User Forbiden'))
				->addMultiOptions(array('0'=>i18n::tr('no'), '1'=>i18n::tr('yes')));
			$this->addElement($isForbiden);
			
			
			$isTesting = $this
				->createElement('select', 'isTesting')
				->setLabel(i18n::tr('User testing'))
				->addMultiOptions($testingOpts);
			$this->addElement($isTesting);
			
			$isWatched = $this
				->createElement('select', 'watched')
				->setLabel(i18n::tr('User is watched'))
				->addMultiOptions(array('0'=>i18n::tr('no'), '1'=>i18n::tr('yes')));
			$this->addElement($isWatched);
			
			$canWithdraw = $this
				->createElement('select', 'canWithdraw')
				->setLabel(i18n::tr('Certified for Withdrawal'))
				->addMultiOptions(array('0'=>i18n::tr('no'), '1'=>i18n::tr('yes')));
			$this->addElement($canWithdraw);
			
			
			$maxBet = $this
				->createElement('text', 'maxBet')
				->setLabel(i18n::tr('Individual Bet Limit'))
				->addValidator('float')
				->setRequired(true);
			$this->addElement($maxBet);
			
			if (!empty($blockedIPs)) {
				$blockedIPsElement = $this
					->createElement('multiselect', 'removeBlockedIPs')
					->setDecorators($this->getElementDecorator(true, false))
					->setLabel(i18n::tr('blocked_ips')
							. '<br>' . i18n::tr('remove_blocked_ips_info'))
					->addMultiOptions($blockedIPs)
					->setRequired(false);
				$blockedIPsElement->setRegisterInArrayValidator(false);
				$this->addElement($blockedIPsElement);
			}
			
			$firstName = $this
				->createElement('text', 'firstName')
				->setLabel(i18n::tr('First Name'))
				->addValidator(new It6_Validate_FirstName())
				->setRequired(true);
			$this->addElement($firstName);
			
			
			$lastName = $this
				->createElement('text', 'lastName')
				->setLabel(i18n::tr('Last Name'))
				->addValidator(new It6_Validate_LastName())
				->setRequired(true);
			$this->addElement($lastName);
			
			
			/*
			 $username = $this
				->createElement('text', 'username')
				->setLabel(i18n::tr('Username'))
				->addValidator(new It6_Validate_Username($userId))
				->setRequired(true);
			$this->addElement($username);
			*/
			
			
			$citizenNumber = $this
				->createElement('text', 'citizenId')
				->setLabel(i18n::tr('Citizen Id'))
				->setAllowEmpty(false);
			$this->addElement($citizenNumber);
			
			$email = $this
				->createElement('text', 'email')
				->setLabel(i18n::tr('Email'))
				->addValidator(new It6_Validate_Email($userId))
				->setRequired(true);
			$this->addElement($email);
			
			$currency = $this
				->createElement('select', 'currencyId')
				->setLabel(i18n::tr('Currency'))
				->addMultioptions($currencies);
			$this->addElement($currency);
			
			
			$accountPrefix = $this
				->createElement('text', 'accountPrefix')
				->setLabel(i18n::tr('Account Prefix'))
				->addValidator(new It6_Validate_BankAccountPrefix());
			$this->addElement($accountPrefix);
			
			
			$accountNumber = $this
				->createElement('text', 'accountNumber')
				->setLabel(i18n::tr('Account Number'))
				->setAllowEmpty(false)
				->addValidator(new It6_Validate_BankAccountNumber());
			$this->addElement($accountNumber);
			
			
			$bank = $this->createElement('select', 'bankId');
			$bank
				->setLabel(i18n::tr('Bank'))
				->addValidator(new It6_Validate_BankId())
				->addMultiOptions($banks);
			$this->addElement($bank);
			
			
			$language = $this->createElement('select', 'languageId');
			$language
				->setLabel(i18n::tr('Language'))
				->addMultiOptions($languages);
			$this->addElement($language);
			
			
			$sex = $this
				->createElement('select', 'sex')
				->setLabel(i18n::tr('Sex'))
				->addMultiOptions(array('m'=>i18n::tr('Male'), 'f'=>i18n::tr('Female')));
			$this->addElement($sex);
			
			
			$birthDate = $this
				->createElement('text', 'birthDate')
				->setLabel(i18n::tr('Birth Date'))
				->setAttrib('class', 'dateOnly')
				->setDecorators($this->getDateTimeDecorator())
				->addValidator(new It6_Validate_BirthDate())
				->setRequired(true);
			$this->addElement($birthDate);
			
			
			$street = $this
				->createElement('text', 'street')
				->setLabel(i18n::tr('Street Address'))
				->setRequired(true);
			$this->addElement($street);
			
			
			$town = $this
				->createElement('text', 'town')
				->setLabel(i18n::tr('Town'))
				->addValidator(new It6_Validate_Town())
				->setRequired(true);
			$this->addElement($town);
			
			
			$zip = $this
				->createElement('text', 'zip')
				->setLabel(i18n::tr('Zip'))
				->setRequired(true);
			$this->addElement($zip);
			
			
			$country = $this->createElement('select', 'countryId');
			$country
				->setLabel(i18n::tr('Country'))
				->addMultiOptions($countries);
			$this->addElement($country);
			
			
			$newsletter = $this
				->createElement('select', 'sendNewsletter')
				->setLabel(i18n::tr('Newsletter'))
				->addMultiOptions(array('0'=>i18n::tr('no'), '1'=>i18n::tr('yes')));
			$this->addElement($newsletter);
			
			$areaCode = $this
				->createElement('select', 'areaCode')
				->setLabel(I18n::tr('area_code'))
				->addMultiOptions($codes);
			$this->addElement($areaCode);
			
			$phoneNumber = $this
				->createElement('text', 'phone')
				->setLabel(i18n::tr('Telefon'))
				->addValidator(new It6_Validate_PhoneNumber());
			$this->addElement($phoneNumber);
			
			$activationTime = $this
				->createElement('text', 'activationTime')
				->setLabel(i18n::tr('Activation Date'))
				->setAttrib('class', 'dateTime')
				->setDecorators($this->getDateTimeDecorator());
			$this->addElement($activationTime);
			
			$clientCardNumber = $this
				->createElement('text', 'clientCardNumber')
				->setLabel(i18n::tr('Client card number'));
			$this->addElement($clientCardNumber);
				
				
			$canPrintAgreement = $this
				->createElement('select', 'canPrintAgreement')
				->setLabel(i18n::tr('Can print agreement'))
				->addMultiOptions(array('0'=>i18n::tr('no'), '1'=>i18n::tr('yes')));
			$this->addElement($canPrintAgreement);
		}
		else { // mode == bookmaker
			$isWatched = $this
				->createElement('select', 'watched')
				->setLabel(i18n::tr('User is watched'))
				->addMultiOptions(array('0'=>i18n::tr('no'), '1'=>i18n::tr('yes')));
			$this->addElement($isWatched);
			
			$maxBet = $this
				->createElement('text', 'maxBet')
				->setLabel(i18n::tr('Individual Bet Limit'))
				->addValidator('float')
				->setRequired(true);
			$this->addElement($maxBet);
		}

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			//->setAttrib('onClick',"submitUserProfileForm($section)");
			->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '40&', '".$this->getName()."', 'user-profile', '&submit=1','#formUsersFilter');return false;");
		$this->addElement($submit);
	}
}
