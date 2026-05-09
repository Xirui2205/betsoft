<?php

class Models_Form_Newspapers extends It6_Models_DecoratedForm_Table {

	public function __construct() {
		parent::__construct();			

			$validTo = $this
				->createElement('text', 'validTo')
				->setLabel(i18n::tr('Valid to'))
				->setAttrib('class', 'dateOnly')
				->setDecorators($this->getDateTimeDecorator())
				->addValidator(new It6_Validate_Date())
				->setRequired(true);
			$this->addElement($validTo);

			$file = $this
				->createElement('file','fileName')
				->setLabel(i18n::tr('File'))
				->setRequired(true);
			$this->addElement($file);

		$submit = $this
			->createElement('submit', 'new')
			->setLabel(i18n::tr('Submit'));
			//->setAttrib('onClick',"submitUserProfileForm($section)");
			// ->setAttrib('onClick',"submitAndReloadGeneric('".$section."', '40&', '".$this->getName()."', 'user-profile', '&submit=1','#formUsersFilter');return false;");
		$this->addElement($submit);
	}
}
