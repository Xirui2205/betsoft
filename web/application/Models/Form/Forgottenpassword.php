<?php

class Models_Form_Forgottenpassword extends It6_Models_DecoratedForm_Table {


	public function __construct($view) {
		parent::__construct();
		$this
			->setAction($view->UrlSet(25));

		// username - ne
		/*$companyName = $this
			->createElement('text', 'nickFp')
			->setLabel('username')
			->setRequired(true);
		$this->addElement($companyName);*/

		$companyAddress = $this
			->createElement('text', 'email')
			->setLabel('reg_email')
			->addValidator('EmailAddress', true)
			->addErrorMessage('wrong_email_format')
			->setRequired(true);
		$this->addElement($companyAddress);
		
		$this->addElement(new It6_Form_Element_Captcha('forgottenPassCaptcha'));
		$this->getElement('forgottenPassCaptcha')
			->setLabel('captcha')
			->addErrorMessage('wrong_captcha');
		
		$submit = $this
			->createElement('submit', 'submit')
			->setLabel('submit')
				->setAttrib('class', 'btn');
		$this->addElement($submit);

		$this->addElement(new It6_Form_Element_Hash('forgottenPasswordToken'));
	}
}
