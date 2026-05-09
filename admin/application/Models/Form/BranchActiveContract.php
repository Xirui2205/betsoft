<?php

class Models_Form_BranchActiveContract extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {
		parent::__construct();

		$this->setName('BranchActiveContractForm');



		$branchId = $this
			->createElement('hidden', 'branchId');
		$this->addElement($branchId);


		$contractId = $this
			->createElement('hidden', 'contractId');
		$this->addElement($contractId);


		$dateValidTo = $this
			->createElement('text', 'dateValidTo')
			->setLabel(i18n::tr('date-valid-to'))
			->setDecorators($this->getDateTimeDecorator(true, true, 0, 2))
			->setAttrib('class', 'dateOnly');
		$this->addElement($dateValidTo);


		$dateSigned = $this
			->createElement('hidden', 'dateSigned');
		$this->addElement($dateSigned);

		$dateValidFrom = $this
			->createElement('hidden', 'dateValidFrom');
		$this->addElement($dateValidFrom);

		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitGeneric($section, 'BranchActiveContractForm', 'branch-contract', '&save=update')")
			->setDecorators($this->getButtonDecorator(true, true, 2, 1));
		$this->addElement($submit);
	}
}
