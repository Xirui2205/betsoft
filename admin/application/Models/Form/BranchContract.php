<?php

class Models_Form_BranchContract extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $templateId, $thisId, $action) {
		parent::__construct();

		$paramFields	= $this->ws->ContractTemplate->getParamsById($templateId);
		$defParams		= $this->ws->Contract->getDefaultParameters($templateId);

		$floatValidator = new Zend_Validate_Float();
		$alnumValidator = new Zend_Validate_Float();
		$alnumValidator->allowWhiteSpace = true;

		$this->setName('BranchOpenContractForm_'.$thisId);



		$branchId = $this
			->createElement('hidden', 'branchId');
		$this->addElement($branchId);


		$templId = $this
			->createElement('hidden', 'templateId')
			->setValue($templateId);
		$this->addElement($templId);


		$templateId = $this
			->createElement('hidden', 'contractId');
		$this->addElement($templateId);


		$dateValidFrom = $this
			->createElement('text', 'dateValidFrom')
			->setLabel(i18n::tr('date-valid-from'))
			->setDecorators($this->getDateTimeDecorator(true, true, 0, 2))
			->setAttrib('class', 'dateOnly');
		$this->addElement($dateValidFrom);


		$dateValidTo = $this
			->createElement('text', 'dateValidTo')
			->setLabel(i18n::tr('date-valid-to'))
			->setDecorators($this->getDateTimeDecorator(true, true, 0, 2))
			->setAttrib('class', 'dateOnly');
		$this->addElement($dateValidTo);


		$dateSigned = $this
			->createElement('text', 'dateSigned')
			->setLabel(i18n::tr('date-signed'))
			->setDecorators($this->getDateTimeDecorator(true, true, 0, 2))
			->setAttrib('class', 'dateOnly');
		$this->addElement($dateSigned);


		foreach($paramFields as $field) {
			$validator = Models_Branch::$validatorConv[$field['type']];

			$element = $this
				->createElement('text', 'param'.$field['id'])
				->setLabel(i18n::tr($field['name']))
				->setDecorators($this->getElementDecorator(true, false))
				->addValidator($$validator);
			$this->addElement($element);

			$useDef = $this
				->createElement('checkbox', 'isDefault'.$field['id'])
				->setDecorators($this->getElementDecorator(false, true))
				->setLabel(i18n::tr('Use Default({0})', $defParams[$field['id']]))
				->setAttrib('onChange', "disableField('param".$field['id']."')");
			$this->addElement($useDef);
		}


		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitGeneric($section, 'BranchOpenContractForm_$thisId', 'branch-contract', '&save=$action&formId=$thisId')")
			->setDecorators($this->getButtonDecorator(true, true, 2, 1));
		$this->addElement($submit);
	}
}
