<?php

class Models_Form_AdminPrivileges extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $adminId) {
		parent::__construct();

		$this->setName('AdminPrivilegesForm');

		$id = $this->createElement('hidden', 'adminId')
			->setValue($adminId);
		$this->addElement($id);

		//It6_Models_Acl::
		/*
		$branches = Zend_Registry::get('ws')->Branch->getAll();
		$branches = It6_ArrayWrapper::toAssocArray($branches,'branchId','%name%',array(0 => '--None--'));

		$branchId = $this
			->createElement('select', 'branchId')
			->addMultiOptions($branches)
			->setLabel(i18n::tr('Branch'))
			->setRequired(true);
		$this->addElement($branchId);
		*/
		
		$acl = Zend_Registry::get('acl');
		if ($acl->userHasRole(It6_Acl_Admin::ROLE_SALES)) {
			$roles = array(
					It6_Acl_Admin::ROLE_BRANCH_EMPLOYEE => It6_Acl_Admin::ROLE_BRANCH_EMPLOYEE,
					It6_Acl_Admin::ROLE_BRANCH_OWNER => It6_Acl_Admin::ROLE_BRANCH_OWNER
				);
		}
		else $roles = It6_Models_Acl::getAssignableRoles();
		
		array_unshift($roles,'--none--');
		$roles = array_combine($roles,$roles);
		$role = $this
			->createElement('multiselect', 'role')
			->setMultiOptions($roles)
			->setLabel(i18n::tr('Role'))
			->setRequired(true)
			->setAttrib("size", 8);
		$this->addElement($role);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitAdminPrivilegesForm($section)");
		$this->addElement($submit);
	}
}
