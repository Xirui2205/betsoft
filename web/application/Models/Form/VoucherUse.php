<?php

class Models_Form_VoucherUse extends It6_Models_DecoratedForm_Table {


	public function __construct($view) {
		parent::__construct();
		$this
			->setAction($view->UrlSet(91));

		$handle = $this
			->createElement('text', 'handle')
			->setLabel('voucher_handle')
			->setRequired(true)
			->setAttrib('class', 'input110');
		
		$this->addElement($handle);
		
		$this->addElement(new It6_Form_Element_Captcha('forgottenPassCaptcha'));
		$this->getElement('forgottenPassCaptcha')
			->setLabel('captcha')
			->addErrorMessage('wrong_captcha');
		
		$submit = $this
			->createElement('submit', 'submit')
			->setLabel('submit')
				->setAttrib('class', 'btn');
		$this->addElement($submit);

		$this->addElement(new It6_Form_Element_Hash('voucherToken'));
	}
}
