<?php

class Models_Form_AdminMain extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $adminId=null) {
		parent::__construct();

		$this->setName('AdminMainForm');

		$emailValidator				= new Zend_Validate_EmailAddress();
		$alnumWhiteSpaceValidator	= new Zend_Validate_Alnum(true);


		if ( !empty($adminId) ) {
			$id = $this->createElement('hidden', 'adminId')
				->setValue($adminId);
			$this->addElement($id);
		}

		$loginName = $this
			->createElement('text', 'loginName')
			->setLabel(i18n::tr('Login name'))
			->addValidator(new It6_Validate_AdminUsername($adminId))
			->setRequired(true);
		$this->addElement($loginName);

		$exts = array(
			new It6_WsExtension_Client_Columns('columns', array('branchId', 'name')),
			new It6_WsExtension_Client_Order('order', array('(name COLLATE utf8_czech_ci)')),
		);
		$branches = Zend_Registry::get('ws')->ext($exts)->Branch->getAll();
		$branches = It6_ArrayWrapper::toAssocArray($branches,'branchId','%name%',array(0 => '--None--'));

		$branchId = $this
			->createElement('select', 'branchId')
			->addMultiOptions($branches)
			->setLabel(i18n::tr('Branch'))
			->setRequired(true);
		$this->addElement($branchId);

		unset($branches[0]);
		$secBranches = $this
			->createElement('multiselect', 'secondaryBranches')
			->addMultiOptions($branches)
			->setLabel(i18n::tr('secondary_branches'))
			->setRequired(false);
		$this->addElement($secBranches);

		$password = $this
			->createElement('password', 'password')
			->setLabel(i18n::tr('Password'))
			->addValidator(new It6_Validate_AdminPassword());
			if(empty($adminId))
				$password->setRequired(true);
		$this->addElement($password);

		$passwordAgain = $this
			->createElement('password', 'passwordAgain')
			->setLabel(i18n::tr('Password again'))
			->addValidator(new It6_Validate_AdminPassword());
			if(empty($adminId))
				$passwordAgain->setRequired(true);
		$this->addElement($passwordAgain);

		$firstName = $this
			->createElement('text', 'firstName')
			->setLabel(i18n::tr('First name'))
			->addValidator(new It6_Validate_FirstName())
			->setRequired(true);
		$this->addElement($firstName);

		$lastName = $this
			->createElement('text', 'lastName')
			->setLabel(i18n::tr('Last name'))
			->addValidator(new It6_Validate_LastName())
			->setRequired(true);
		$this->addElement($lastName);

		$phone = $this
			->createElement('text', 'phone')
			->setLabel(i18n::tr('Phone'))
			->addValidator(new It6_Validate_PhoneNumber());
		$this->addElement($phone);
		
		$email = $this
			->createElement('text', 'email')
			->setLabel(i18n::tr('Email'))
			->addValidator(new Zend_Validate_EmailAddress());
		$this->addElement($email);
		
		$isBanned = $this
			->createElement('checkbox', 'isBanned')
			->setLabel(i18n::tr('Is banned'))
			->setRequired(true);
		$this->addElement($isBanned);
		
		$isBlocked = $this
			->createElement('checkbox', 'block')
			->setLabel(i18n::tr('Block'))
			->setRequired(true);
		$this->addElement($isBlocked);

		$blockIP = $this
			->createElement('text', 'blockIp')
			->setLabel(i18n::tr('Block IP'));
		$this->addElement($blockIP);

		$exts = array();
		$exts = array(
			new It6_WsExtension_Client_Columns('columns', array('affiliatePartnerId', 'name')),
			new It6_WsExtension_Client_Order('order', array('(name COLLATE utf8_czech_ci)')),
		);
		$partners = Zend_Registry::get('ws')->ext($exts)->AffiliatePartner->getAll();
		$partners = It6_ArrayWrapper::toAssocArray($partners, 'affiliatePartnerId', '%name%', array(0 => '--None--'));

		$partnerId = $this
			->createElement('select', 'partnerId')
			->addMultiOptions($partners)
			->setLabel(i18n::tr('partner'));
		$this->addElement($partnerId);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '281&', '".$this->getName()."', 'admin-main', '&submit=1','#formUsersFilter');return false;");
		$this->addElement($submit);
	}
}
