<?php

class Models_Form_BranchHost extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {
		parent::__construct();
		$isCallcenter = Zend_Registry::get('acl')->userHasRole(It6_Acl_Admin::ROLE_CALLCENTRUM);
		$this
			->setName('BranchHostForm');
			//->setAction('?section='.$section)



		/*$branchId = $this->createElement('hidden', 'branchId')->setValue(1);*/


		$name = $this
			->createElement('text', 'name')
			->setLabel(i18n::tr('host_name'))
			->setRequired(true);
		if ($isCallcenter) $name->setAttrib('readonly', 'readonly');
		$this->addElement($name);


		$ip = $this
			->createElement('text', 'ip')
			->setLabel(i18n::tr('ip_address'))
			->setRequired(true);
		$this->addElement($ip);


		$version = $this
			->createElement('text', 'version')
			->setLabel(i18n::tr('Version'))
			->setRequired(true);
		$this->addElement($version);


		$banned = $this
			->createElement('text', 'banned')
			->setLabel(i18n::tr('Banned'));
		if ($isCallcenter) $banned->setAttrib('readonly', 'readonly');
		$this->addElement($banned);


		$hardware = $this
			->createElement('text','hardware')
			->setLabel(i18n::tr('hardware'));
		$this->addElement($hardware);


		$display = $this
			->createElement('text','display')
			->setLabel(i18n::tr('display'));
		$this->addElement($display);


		$printer = $this
			->createElement('text', 'printer')
			->setLabel(i18n::tr('printer'));
		$this->addElement($printer);


		$winSn = $this
			->createElement('text', 'winSn')
			->setLabel(i18n::tr('windows_serial'));
		$this->addElement($winSn);


		$providerDns = $this
			->createElement('text', 'providerDns')
			->setLabel(i18n::tr('provider_dns'));
		$this->addElement($providerDns);


		$providerGateway = $this
			->createElement('text', 'providerGateway')
			->setLabel(i18n::tr('provider_gateway'));
		$this->addElement($providerGateway);


		$providerIp = $this
			->createElement('text', 'providerIp')
			->setLabel(i18n::tr('provider_ip'));
		$this->addElement($providerIp);


		$providerUsername = $this
			->createElement('text', 'providerUsername')
			->setLabel(i18n::tr('provider_username'));
		$this->addElement($providerUsername);


		$providerPassword = $this
			->createElement('text', 'providerPassword')
			->setLabel(i18n::tr('provider_password'));
		$this->addElement($providerPassword);


		$vicEmail = $this
			->createElement('text', 'vicEmail')
			->setLabel(i18n::tr('vic_email'));
		$this->addElement($vicEmail);


		$vicEmailPassword = $this
			->createElement('text', 'vicEmailPassword')
			->setLabel(i18n::tr('vic_email_password'));
		$this->addElement($vicEmailPassword);


		$vicAdminPassword = $this
			->createElement('text', 'vicAdminPassword')
			->setLabel(i18n::tr('vic_admin_password'));
		$this->addElement($vicAdminPassword);


		$vicEmployeePassword1 = $this
			->createElement('text', 'vicEmployeePassword1')
			->setLabel(i18n::tr('vic_employee_password1'));
		$this->addElement($vicEmployeePassword1);


		$vicEmployeePassword2 = $this
			->createElement('text', 'vicEmployeePassword2')
			->setLabel(i18n::tr('vic_employee_password2'));
		$this->addElement($vicEmployeePassword2);

/*
		'id' => 'hostId',
		'branch_id' => 'branchId',
		'name' => 'name',
		'ip' => 'ip',
		'version' => 'version',
		'allowed' => 'allowed',
		'version' => 'version',
		'is_online' => 'isOnline',
		'hardware' => 'hardware',
		'display' => 'display',
		'printer' => 'printer',
		'win_sn' => 'winSn',
		'provider_dns' => 'providerDns',
		'provider_gateway' => 'providerGateway',
		'provider_ip' => 'providerIp',
		'provider_username' => 'providerUsername',
		'provider_password' => 'providerPassword',
		'vic_email' => 'vicEmail',
		'vic_email_password' => 'vicEmailPassword',
		'vic_admin_password' => 'vicAdminPassword',
		'vic_employee_password_1' => 'vicEmployeePassword1',
		'vic_employee_password_2' => 'vicEmployeePassword2',
		'note' => 'note'
*/

		$note = $this
			->createElement('text', 'note')
			->setLabel(i18n::tr('Note'));
		$this->addElement($note);


		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitBranchHostForm($section)");
		$this->addElement($submit);
	}
}
