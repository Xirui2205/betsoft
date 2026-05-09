<?php

class Models_Form_ProviderRegistration extends It6_Models_DecoratedForm_Table {


	public function __construct($view) {
		parent::__construct();
		$this
			->setName('ProviderRegistrationForm')
			->setAction($view->UrlSet(78))
			->setDecorators($this->getFormDecorator(array('no-table', 'no-form')))
			->setAttrib('class', 'form');

		$branchTypes = Zend_Registry::get('ws')->BranchType->getAll();
		$branchTypes = It6_ArrayWrapper::toMultiOption($branchTypes, 'name', 'name');

		$this->addElement(new It6_Form_Element_Hash('providerRegistrationToken'));

		$companyName = $this
			->createElement('text', 'companyName')
			->setLabel('company_name')
			->setRequired(true);
		$this->addElement($companyName);

		$companyAddress = $this
			->createElement('text', 'companyAddress')
			->setLabel('company_address')
			->setRequired(true);
		$this->addElement($companyAddress);

		$companyRepresentative = $this
			->createElement('text', 'companyRepresentative')
			->setlabel('company_representative')
			->setRequired(true);
		$this->addElement($companyRepresentative);

		$companyPhone = $this
			->createElement('text', 'companyPhone')
			->setLabel('company_phone')
			->setRequired(true)
			->addValidator(new It6_Validate_PhoneNumber);
		$this->addElement($companyPhone);

		$companyMobile = $this
			->createElement('text', 'companyMobile')
			->setLabel('company_mobile')
			->addValidator(new It6_Validate_PhoneNumber);
		$this->addElement($companyMobile);

		$companyFax = $this
			->createElement('text', 'companyFax')
			->setLabel('company_fax')
			->addValidator(new It6_Validate_PhoneNumber);
		$this->addElement($companyFax);

		$companyEmail = $this
			->createElement('text', 'companyEmail')
			->setLabel('company_email')
			->addValidator(new Zend_Validate_EmailAddress())
			->setRequired(true);
		$this->addElement($companyEmail);

		$companyIc = $this
			->createElement('text', 'companyIc')
			->setLabel('company_ic')
			->setRequired(true);
		$this->addElement($companyIc);

		$companyDic = $this
			->createElement('text', 'companyDic')
			->setLabel('company_dic')
			->setRequired(true);
		$this->addElement($companyDic);

		$this->addDisplayGroup(
			array(
				'companyName',
				'companyAddress',
				'companyRepresentative',
				'companyPhone',
				'companyMobile',
				'companyFax',
				'companyEmail',
				'companyIc',
				'companyDic'
			),
			'companyInfo',
			array(
				'legend' => 'company_info',
				'decorators'=>$this->getDisplayGroupDecorator()
			)
		);



		$locationName = $this
			->createElement('text', 'locationName')
			->setLabel('location_name')
			->setRequired(true);
		$this->addElement($locationName);


		$locationAddress = $this
			->createElement('text', 'locationAddress')
			->setLabel('location_address')
			->setRequired(true);
		$this->addElement($locationAddress);

		$locationType = $this
			->createElement('radio', 'locationType')
			->setLabel('location_type')
			->addMultiOptions($branchTypes)
			->setRequired(true);
		$this->addElement($locationType);

		$locationPhone = $this
			->createElement('text', 'locationPhone')
			->setLabel('location_phone')
			->addValidator(new It6_Validate_PhoneNumber)
			->setRequired(true);
		$this->addElement($locationPhone);

		$locationMobile = $this
			->createElement('text', 'locationMobile')
			->setLabel('location_mobile')
			->addValidator(new It6_Validate_PhoneNumber);
		$this->addElement($locationMobile);

		$locationFax = $this
			->createElement('text', 'locationFax')
			->setLabel('location_fax')
			->addValidator(new It6_Validate_PhoneNumber);
		$this->addElement($locationFax);

		$locationEmail = $this
			->createElement('text', 'locationEmail')
			->setLabel('location_email')
			->addValidator(new Zend_Validate_EmailAddress())
			->setRequired(true);
		$this->addElement($locationEmail);

		$this->addDisplayGroup(
			array(
				'locationName',
				'locationAddress',
				'locationType',
				'locationPhone',
				'locationMobile',
				'locationFax',
				'locationEmail',
			),
			'locationInfo',
			array(
				'legend' => 'location_info',
				'decorators'=>$this->getDisplayGroupDecorator()
			)
		);



		$openingHoursMon = $this
			->createElement('text', 'openingHoursMon')
			->addValidator(new It6_Validate_OpeningHours())
			->setLabel('monday');
		$this->addElement($openingHoursMon);

		$openingHoursTue = $this
			->createElement('text', 'openingHoursTue')
			->addValidator(new It6_Validate_OpeningHours())
			->setLabel('tuesday');
		$this->addElement($openingHoursTue);

		$openingHoursWed = $this
			->createElement('text', 'openingHoursWed')
			->addValidator(new It6_Validate_OpeningHours())
			->setLabel('wednesday');
		$this->addElement($openingHoursWed);

		$openingHoursThu = $this
			->createElement('text', 'openingHoursThu')
			->addValidator(new It6_Validate_OpeningHours())
			->setLabel('thursday');
		$this->addElement($openingHoursThu);

		$openingHoursFri = $this
			->createElement('text', 'openingHoursFri')
			->addValidator(new It6_Validate_OpeningHours())
			->setLabel('friday');
		$this->addElement($openingHoursFri);

		$openingHoursSat = $this
			->createElement('text', 'openingHoursSat')
			->addValidator(new It6_Validate_OpeningHours())
			->setLabel('saturday');
		$this->addElement($openingHoursSat);

		$openingHoursSun = $this
			->createElement('text', 'openingHoursSun')
			->addValidator(new It6_Validate_OpeningHours())
			->setLabel('sunday');
		$this->addElement($openingHoursSun);

		$this->addDisplayGroup(
			array(
				'openingHoursMon',
				'openingHoursTue',
				'openingHoursWed',
				'openingHoursThu',
				'openingHoursFri',
				'openingHoursSat',
				'openingHoursSun',
			),
			'openingHours',
			array(
				'legend' => 'opening_hours',
				'decorators'=>$this->getDisplayGroupDecorator(),
				'description'=>'branch_opening_hours_edit_description'
			)
		);
	}
}
