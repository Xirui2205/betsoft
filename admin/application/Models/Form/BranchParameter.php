<?php

class Models_Form_BranchParameter extends It6_Models_DecoratedForm_Table {

	public function __construct($section, $branchId) {
		parent::__construct();

		$this->setName('BranchParameterForm');

		$defParams		= $this->ws->Parameter->getDefaultBranchParameters();
		$paramFields	= $this->ws->Parameter->getBranchParameterNamesForSettings($branchId);



		$id = $this->createElement('hidden', 'branchId');
		$this->addElement($id);


		foreach($paramFields as $field) {
			$localParam = "";
			if ( $field['manually_inserted'] == 1 && $field['local_parameter'] == 1 ) {
				$localParam = "<label style='color:green;'> (local parameter)</label>";
			}
			if ( $field['local_parameter'] == 2 ) {
				$localParam = " <label style='color:red;'> (changed value)</label>";
			}

			$element = $this
				->createElement('text', 'param'.$field['parameterId'])
				->setLabel(i18n::tr($field['name']).$localParam)
				->setDecorators($this->getElementDecorator(true, false));
			$this->addElement($element);

			$useDef = $this
				->createElement('checkbox', 'isDefault'.$field['parameterId'])
				->setDecorators($this->getElementDecorator(false, true))
				->setLabel(i18n::tr('Use Default [{0}]', $defParams[$field['parameterId']]['value']))
				->setAttrib('onChange', "disableField('param".$field['parameterId']."')");
			$this->addElement($useDef);				
		}


		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitGeneric($section, '".$this->getName()."', 'branch-parameter', '&save=1')")
			->setDecorators($this->getButtonDecorator(true, true, 2, 1));
		$this->addElement($submit);
	}
}
