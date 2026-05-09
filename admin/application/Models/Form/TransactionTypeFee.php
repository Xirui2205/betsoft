<?php

class Models_Form_TransactionTypeFee extends It6_Models_DecoratedForm_Table{

	public function __construct($updateSectionId, $indexSectionId){
		parent::__construct();

		$this->setName('TransactionTypeForm');

		$idEl = $this
			->createElement('hidden', 'transactionTypeId');
		$this->addElement($idEl);

		$feeFixEl = $this
			->createElement('text', 'feeFix')
			->setLabel(i18n::tr('Fixed Fee Value'))
			->addFilter(new It6_Filter_Float(array('precision' => 2), $this))
			->addValidator(new Zend_Validate_Float());
		$this->addElement($feeFixEl);

		$feeRelEl = $this
			->createElement('text', 'feeRel')
			->setLabel(i18n::tr('Realtive Fee Value'))
			->addValidator(new Zend_Validate_Float())
			->addFilter(new It6_Filter_Float(array('precision' => 2), $this));
		$this->addElement($feeRelEl);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick','submitTransactionType("'.$this->getName().'","fees-detail", '.$updateSectionId.', '.$indexSectionId.', "feeList", "&save=1")');
		$this->addElement($submit);
	}
}
