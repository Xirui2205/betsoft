<?php

class Models_Form_BranchMain extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $branchId=null) {
		parent::__construct();

		$this->setName('BranchMainForm');

		$emailValidator				= new Zend_Validate_EmailAddress();
		$alnumWhiteSpaceValidator	= new Zend_Validate_Alnum(true);
		$latCfg	= array(
			'min'		=> -90,
			'max'		=> 90,
		);
		$longCfg	= array(
			'min'		=> -180,
			'max'		=> 180,
		);


		//$id = $this->createElement('hidden', 'branchId');


		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('name'))
			->addValidator(new It6_Validate_BranchName($branchId))
			->setRequired(true);
		$this->addElement($name);


		$handle = $this
			->createElement('text', 'handle')
			->setlabel(i18n::tr('branch id'))
			->addValidator(new It6_Validate_BranchHandle($branchId))
			->setRequired(true);
		$this->addElement($handle);


		$ticket_header = $this
			->createElement('text', 'ticketHeader')
			->setLabel(i18n::tr('ticket_header'))
			->setRequired(true);
		$this->addElement($ticket_header);


		$street = $this
			->createElement('text', 'street')
			->setLabel(i18n::tr('Street'))
			->setRequired(true);
		$this->addElement($street);


		$town = $this
			->createElement('text', 'town')
			->setLabel(i18n::tr('Town'))
			->setRequired(true);
		$this->addElement($town);

		$place = $this
			->createElement('text', 'place')
			->setLabel(i18n::tr('place'));
		$this->addElement($place);

		$zip = $this
			->createElement('text', 'zip')
			->setLabel(i18n::tr('ZIP'))
			->addValidator('alnum')
			->setRequired(true);
		$this->addElement($zip);


		$longitude = $this
			->createElement('text', 'longitude')
			->setLabel(i18n::tr('longitude'));
		$this->addElement($longitude);
		//	->addValidator(new It6_Validate_Float($longCfg));


		$latitude = $this
			->createElement('text', 'latitude')
			->setLabel(i18n::tr('latitude'));
		$this->addElement($latitude);
		//	->addValidator(new It6_Validate_Float($latCfg));


		$email = $this
			->createElement('text', 'email')
			->setLabel(i18n::tr('Email'))
			->addValidator($emailValidator)
			->setRequired(true);
		$this->addElement($email);


		$phone = $this
			->createElement('text', 'phone')
			->setLabel(i18n::tr('Phone'))
			->addValidator('alnum')
			->setRequired(true);
		$this->addElement($phone);



		$type = $this
			->createElement('select', 'typeId')
			->setLabel(i18n::tr('branch_type'))
			->addMultiOptions(Models_Branch::getBranchTypes());
		$type
			->removeMultiOption(1); // remove Internet
		$this->addElement($type);

		$type = $this
			->createElement('select', 'stemId')
			->setLabel(i18n::tr('stem'))
			->addMultiOptions(It6_ArrayWrapper::toAssocArray(Webservice_Stem::getAll(), 'stemId', '%name%'));
		$this->addElement($type);

		$region = $this
			->createElement('select', 'branchLocationId')
			->setLabel(i18n::tr('branch_location'))
			->addMultiOptions(Models_Branch::getBranchLocations());
		$this->addElement($region);


		$provider_name = $this
			->createElement('text', 'providerName')
			->setLabel(i18n::tr('provider_name'))
			->setRequired(true);
		$this->addElement($provider_name);


		$provider_address = $this->createElement('text', 'providerAddress');
		$provider_address
			->setLabel(i18n::tr('provider_address'))
			->setRequired(true);
		$this->addElement($provider_address);

		$correspondenceAddress = $this
			->createElement('textarea', 'correspondenceAddress')
			->setLabel(i18n::tr('correspondence_address'));
		$this->addElement($correspondenceAddress);

		$provider_ic = $this
			->createElement('text', 'providerIc')
			->setLabel(i18n::tr('provider_ic'))
			->addValidator('alnum')
			->setRequired(true);
		$this->addElement($provider_ic);


		$provider_dic = $this
			->createElement('text', 'providerDic')
			->setLabel(i18n::tr('provider_dic'))
			->addValidator('alnum');
		$this->addElement($provider_dic);


		$provider_email = $this
			->createElement('text', 'providerEmail')
			->setLabel(i18n::tr('provider_email'))
			->addValidator($emailValidator);
		$this->addElement($provider_email);

		$mp = $this
			->createElement('text', 'mp')
			->setLabel(i18n::tr('manipulation_fee') . ' (%)')
			->addFilter(new It6_Filter_Float(array('precision' => 2), $this))
			->addValidator(new It6_Validate_Float());
		$this->addElement($mp);

		$mpWin = $this
			->createElement('text', 'mpWin')
			->setLabel(i18n::tr('manipulation_fee_win') . ' (%)')
			->addFilter(new It6_Filter_Float(array('precision' => 2), $this))
			->addValidator(new It6_Validate_Float());
		$this->addElement($mpWin);

		$calculation = $this
			->createElement('checkbox', 'calculation')
			->setLabel(i18n::tr('calculation'));
		$this->addElement($calculation);

		$calculationNet = $this
			->createElement('checkbox', 'calculationNet')
			->setLabel(i18n::tr('calculation_net'));
		$this->addElement($calculationNet);

		$calculationNetType = $this
			->createElement('select', 'calculationNetType')
			->setLabel(i18n::tr('calculation_net_type'))
			->addMultiOptions(array('NEW' => i18n::tr('new'), 'OLD' => i18n::tr('old')));
		$this->addElement($calculationNetType);

		$note = $this
			->createElement('textarea', 'note')
			->setLabel(i18n::tr('note'));
		$this->addElement($note);

		$info = $this
			->createElement('textarea', 'info')
			->setLabel(i18n::tr('info'));
		$this->addElement($info);

		$banned = $this
			->createElement('text', 'banned')
			->setLabel(i18n::tr('banned'))
			->addValidator(new It6_Validate_Date())
			->setDecorators($this->getDateTimeDecorator())
			->setAttrib('class', 'dateOnly')
			->setRequired(false);
		$this->addElement($banned);
		
		$isListed = $this
			->createElement('select', 'isListed')
			->setLabel(i18n::tr('show_on_www'))
			->addMultiOptions(array(0 => i18n::tr('no'), 1 => i18n::tr('yes')));
		$this->addElement($isListed);

		$isActive = $this
			->createElement('select', 'isActive')
			->setLabel(i18n::tr('is_active'))
			->addMultiOptions(array(0 => i18n::tr('no'), 1 => i18n::tr('yes')));
		$this->addElement($isActive);
		
		$isTop = $this
			->createElement('select', 'isTop')
			->setLabel(i18n::tr('is_top'))
			->addMultiOptions(array(0 => i18n::tr('no'), 1 => i18n::tr('yes')));
		$this->addElement($isTop);
		
		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '193&', '".$this->getName()."', 'branch-main', '&submit=1','#formUsersFilter');return false;");
		$this->addElement($submit);


	}
}
