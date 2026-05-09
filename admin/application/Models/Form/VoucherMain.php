<?php

class Models_Form_VoucherMain extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $voucherId=0, $insert = false) {
		parent::__construct();

		$this->setName('VoucherMainForm');

		
		$handle = $this
			->createElement('text', 'handle')
			->setLabel(i18n::tr('handle'));
		$this->addElement($handle);

		$validFrom = $this
			->createElement('text', 'validFrom')
			->setLabel(i18n::tr('valid_from'))
			->addValidator(new It6_Validate_Date())
			->setAttrib('class', 'dateTime')
			->setDecorators($this->getDateTimeDecorator());
		$this->addElement($validFrom);

		$validTo = $this
			->createElement('text', 'validTo')
			->setLabel(i18n::tr('valid_to'))
			->addValidator(new It6_Validate_Date())
			->setAttrib('class', 'dateTime')
			->setDecorators($this->getDateTimeDecorator())
			->setRequired(true);
		$this->addElement($validTo);

		$amount = $this
			->createElement('text', 'amount')
			->setLabel(i18n::tr('amount'))
			->addValidator(new It6_Validate_Float())
			->setRequired(!$insert);
		$this->addElement($amount);
		
		
		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitVoucherMainForm($section, $voucherId)");
		$this->addElement($submit);
	}
}
