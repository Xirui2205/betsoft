<?php

class Models_Form_EditProfile extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {
		parent::__construct();

		$this->setAction('./')->setMethod('post')->setAttrib('cssClass', 'table-filter');;
		
		$sectionE = $this
			->createElement('hidden', 'section')
			->setValue($section);
		$this->addElement($sectionE);
		
		$oldPass = $this->createElement('password','old_pass');
		$this->addElement($oldPass);
		$oldPass->setLabel(i18n::tr('old_pass'));
		$oldPass->setRequired(true);

		$pass = $this->createElement('password','pass');
		$this->addElement($pass);
		$pass->setLabel(i18n::tr('new_pass'));
		$pass->setRequired(true);
		//$pass->addValidator(new It6_Validate_Password());

		$pass2 = $this->createElement('password','pass_again');
		$this->addElement($pass2);
		$pass2->setLabel(i18n::tr('new_pass_again'));
		$pass2->setRequired(true);
		//$pass2->addValidator(new It6_Validate_Password());

		$submit = $this
			->createElement('submit', 'submit')
			->setLabel(i18n::tr('Edit_profile'));
		$this->addElement($submit);

	}
}
