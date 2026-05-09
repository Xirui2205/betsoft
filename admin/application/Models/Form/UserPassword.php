<?php

class Models_Form_UserPassword extends It6_Models_DecoratedForm_Plain {

	public function __construct($section) {
		parent::__construct();

		$this->setName('userPasswordForm');



		$id = $this
			->createElement('hidden', 'userId');
		$this->addElement($id);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Send new password'))
			->setAttrib('onClick', "submitGeneric($section, '".$this->getName()."', 'user-password', '&save=1')");
		$this->addElement($submit);
	}
}
