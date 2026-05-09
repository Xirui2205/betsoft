<?php

class Models_Form_ApprovalGroupThreshold extends It6_Models_DecoratedForm_Table {

	public function __construct($section,$id) {
		parent::__construct();

		$floatValidator = new Zend_Validate_Float();

		$this->setName('ApprovalGroupThresholdForm');



		$gid = $this
			->createElement('hidden', 'approvalGroupId')
			->setValue($id)
			->setRequired(true);
		$this->addElement($gid);


		$odd = $this
			->createElement('text', 'oddUpperThreshold')
			->setLabel(i18n::tr('Odd upper threshold'))
			->addValidator($floatValidator)
			->setRequired(true);
		$this->addElement($odd);


		$stake = $this
			->createElement('text', 'stakeLowerThreshold')
			->setLabel(i18n::tr('Stake lower threshold'))
			->addValidator($floatValidator)
			->setRequired(true);
		$this->addElement($stake);


		$submit = $this->createElement('button', 'button');
		$submit->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitGeneric('$section&superb=1','".$this->getName()."','inner','&submit=1')");
		$this->addElement($submit);
	}
}
