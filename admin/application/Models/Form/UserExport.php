<?php

class Models_Form_UserExport extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $userId = null) {
		parent::__construct(array('form', 'multicol-sub-form'));
		$this
			->setAction('?section=299')
			->setDecorators($this->getFormDecorator(array('no-table')))
			->setName('userExportForm');
		
		$columnsSubForm = new Zend_Form_SubForm();
		$columnsSubForm
			->setDescription(i18n::tr('columns'))
			->setDecorators($this->getSubFormDecorator());
		
			$userId = $this
				->createElement('checkbox', 'userId')
				->setLabel(i18n::tr('user_id'))
				->setValue(1);
			$columnsSubForm->addElement($userId);
			
			$userHandle = $this
				->createElement('checkbox', 'userHandle')
				->setLabel(i18n::tr('user_handle'))
				->setValue(1);
			$columnsSubForm->addElement($userHandle);
			
			$firstName = $this
				->createElement('checkbox', 'firstName')
				->setLabel(i18n::tr('first_name'))
				->setValue(1);
			$columnsSubForm->addElement($firstName);
			
			$lastName = $this
				->createElement('checkbox', 'lastName')
				->setLabel(i18n::tr('last_name'))
				->setValue(1);
			$columnsSubForm->addElement($lastName);
			
			$username = $this
				->createElement('checkbox', 'username')
				->setLabel(i18n::tr('username'))
				->setValue(1);
			$columnsSubForm->addElement($username);
			
			$sex = $this
				->createElement('checkbox', 'sex')
				->setLabel(i18n::tr('sex'))
				->setValue(1);
			$columnsSubForm->addElement($sex);
			
			$birthDate = $this
				->createElement('checkbox', 'birthDate')
				->setLabel(i18n::tr('birth_date'))
				->setValue(1);
			$columnsSubForm->addElement($birthDate);
			
			$email = $this
				->createElement('checkbox', 'email')
				->setLabel(i18n::tr('email'))
				->setValue(1);
			$columnsSubForm->addElement($email);
			
			$phone = $this
				->createElement('checkbox', 'phone')
				->setLabel(i18n::tr('phone'))
				->setValue(1);
			$columnsSubForm->addElement($phone);
			
			$street = $this
				->createElement('checkbox', 'street')
				->setLabel(i18n::tr('street'))
				->setValue(1);
			$columnsSubForm->addElement($street);
			
			$town = $this
				->createElement('checkbox', 'town')
				->setLabel(i18n::tr('town'))
				->setValue(1);
			$columnsSubForm->addElement($town);
			
			$zip = $this
				->createElement('checkbox', 'zip')
				->setLabel(i18n::tr('zip'))
				->setValue(1);
			$columnsSubForm->addElement($zip);
			
			$currencyName = $this
				->createElement('checkbox', 'currencyName')
				->setLabel(i18n::tr('currency_name'))
				->setValue(1);
			$columnsSubForm->addElement($currencyName);
			
			$branchId = $this
				->createElement('checkbox', 'branchId')
				->setLabel(i18n::tr('branch_id'))
				->setValue(1);
			$columnsSubForm->addElement($branchId);
			
			$registrationTime = $this
				->createElement('checkbox', 'registrationTime')
				->setLabel(i18n::tr('Registration Date'))
				->setValue(1);
			$columnsSubForm->addElement($registrationTime);
			
			$activationTime = $this
				->createElement('checkbox', 'activationTime')
				->setLabel(i18n::tr('Activation Date'))
				->setValue(1);
			$columnsSubForm->addElement($activationTime);
			
			$sendNewsletter = $this
				->createElement('checkbox', 'sendNewsletter')
				->setLabel(i18n::tr('send_newsletter'))
				->setValue(1);
			$columnsSubForm->addElement($sendNewsletter);
			
			$clientCardNumber = $this
				->createElement('checkbox', 'clientCardNumber')
				->setLabel(i18n::tr('client_card_number'))
				->setValue(1);
			$columnsSubForm->addElement($clientCardNumber);
			
			$countryName = $this
				->createElement('checkbox', 'countryName')
				->setLabel(i18n::tr('country_name'))
				->setValue(1);
			$columnsSubForm->addElement($countryName);
			
			$languageName = $this
				->createElement('checkbox', 'languageName')
				->setLabel(i18n::tr('language_name'))
				->setValue(1);
			$columnsSubForm->addElement($languageName);

			$points = $this
				->createElement('checkbox', 'balance')
				->setLabel(i18n::tr('Balance'))
				->setValue(1);
			$columnsSubForm->addElement($points);

			$branchHandle = $this
			->createElement('checkbox', 'branchHandle')
			->setLabel(i18n::tr('branch_handle'))
			->setValue(1);
			$columnsSubForm->addElement($branchHandle);
			
			$branchName = $this
			->createElement('checkbox', 'branchName')
			->setLabel(i18n::tr('branch_name'))
			->setValue(1);
			$columnsSubForm->addElement($branchName);		
			
			$points = $this
				->createElement('checkbox', 'points')
				->setLabel(i18n::tr('Points'))
				->setValue(1);
			$columnsSubForm->addElement($points);

		$this->addSubForm($columnsSubForm, 'columns');
		
		
		$filtrSubForm = new Zend_Form_SubForm();
		$filtrSubForm
			->setDescription(i18n::tr('filter'))
			->setDecorators($this->getSubFormDecorator());

			$active = $this
				->createElement('multiselect', 'active')
				->setLabel(i18n::tr('active'))
				->addMultiOptions(array('active' => 'active', 'notActive' => 'not_active'))
				->setValue('active');
			$filtrSubForm->addElement($active);

			$testing = $this
				->createElement('multiselect', 'testing')
				->setLabel(i18n::tr('testing'))
				->addMultiOptions(array('testing' => 'testing', 'notTesting' => 'not_testing'))
				->setValue('notTesting');;
			$filtrSubForm->addElement($testing);

			$forbiden = $this
				->createElement('multiselect', 'forbiden')
				->setLabel(i18n::tr('forbiden'))
				->addMultiOptions(array('forbiden' => 'forbiden', 'notForbiden' => 'not_forbiden'))
				->setValue('notForbiden');;
			$filtrSubForm->addElement($forbiden);
			
			$fromDate = $this
				->createElement('text', 'activatedFrom')
				->setLabel(i18n::tr('activated_from_date'))
				->setDecorators($this->getDateTimeDecorator(false, true))
				->setAttrib('class', 'dateOnly');
			$filtrSubForm->addElement($fromDate);
			
			$toDate = $this
				->createElement('text', 'activatedTo')
				->setLabel(i18n::tr('activated_to_date'))
				->setDecorators($this->getDateTimeDecorator(false, true))
				->setAttrib('class', 'dateOnly');
			$filtrSubForm->addElement($toDate);
			
			$fromDate = $this
				->createElement('text', 'registrationFrom')
				->setLabel(i18n::tr('registration_from_date'))
				->setDecorators($this->getDateTimeDecorator(false, true))
				->setAttrib('class', 'dateOnly');
			$filtrSubForm->addElement($fromDate);
			
			$toDate = $this
				->createElement('text', 'registrationTo')
				->setLabel(i18n::tr('registration_to_date'))
				->setDecorators($this->getDateTimeDecorator(false, true))
				->setAttrib('class', 'dateOnly');
			$filtrSubForm->addElement($toDate);

			$branches = Zend_Registry::get('ws')->Branch->getAllOrder(array('name'));
			$branches = It6_ArrayWrapper::toAssocArray($branches, 'handle', '%name%'. ' - ' .'%handle% ',array(0 => '--None--'));
			
			$branchText = $this
				->createElement('text', 'branchText')
				->setLabel(I18n::tr('branch_handle'));
			$filtrSubForm->addElement($branchText);
			
			$branch = $this
			->createElement('select', 'branchHandle')
			->setLabel(i18n::tr('branch'))
			->addMultiOptions($branches);
			$filtrSubForm->addElement($branch);
			
		$this->addSubForm($filtrSubForm, 'filter');
		
		
		$submit = $this
			->createElement('submit', 'submit')
			//->setAttrib('onClick', 'submitUserExportForm()')
			->setLabel(i18n::tr('submit'));
		$this->addElement($submit);
	}
}
