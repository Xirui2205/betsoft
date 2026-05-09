<?php

class Models_Form_TransactionTypeLimit extends It6_Models_DecoratedForm_Table{

	public function __construct($updateSectionId, $indexSectionId){
		parent::__construct();

		$this->setName('TransactionTypeLimit');

		$idEl = $this
			->createElement('hidden', 'transactionTypeId');
		$this->addElement($idEl);

		$lowLimitEl = $this
			->createElement('text', 'lowLimit')
			->setLabel(i18n::tr('Low Limit'))
			->setDecorators(It6_Models_DecoratedForm_Table::getElementDecorator(true, false, 0, 0))
			->addFilter(new It6_Filter_Float(array('precision' => 2), $this))
			->addValidator(new Zend_Validate_Float());
		$this->addElement($lowLimitEl);

		$lowLimitCheckboxEl = $this
			->createElement('checkbox', 'lowLimitCheckbox')
			->setLabel(i18n::tr('No Limit'))
			->setAttrib('onClick', "disableField('lowLimit')")
			->setDecorators(It6_Models_DecoratedForm_Table::getElementDecorator(false, true, 0, 0));
		$this->addElement($lowLimitCheckboxEl);

		$highLimitEl = $this
			->createElement('text', 'highLimit')
			->setLabel(i18n::tr('High Limit'))
			->setDecorators(It6_Models_DecoratedForm_Table::getElementDecorator(true, false, 0, 0))
			->addFilter(new It6_Filter_Float(array('precision' => 2), $this))
			->addValidator(new Zend_Validate_Float());
		$this->addElement($highLimitEl);

		$highLimitCheckboxEl = $this
			->createElement('checkbox', 'highLimitCheckbox')
			->setLabel(i18n::tr('No Limit'))
			->setAttrib('onClick', "disableField('highLimit')")
			->setDecorators(It6_Models_DecoratedForm_Table::getElementDecorator(false, true, 0, 0));;
		$this->addElement($highLimitCheckboxEl);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick','submitTransactionType("'.$this->getName().'", "limits-detail", '.$updateSectionId.', '.$indexSectionId.', "limitList", "&save=1")')
			->setDecorators(It6_Models_DecoratedForm_Table::getButtonDecorator(true, true, 2, 1));
		$this->addElement($submit);
	}
}
