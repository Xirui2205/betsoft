<?php

class Models_Form_UserParameter extends It6_Models_DecoratedForm_Table {

	public function __construct($section) {
		parent::__construct();

		$this->setName('UserParameterForm');

		$paramFields	= $this->ws->Parameter->getUserParameterNames();
		$defParams		= $this->ws->Parameter->getDefaultUserParameters();



		$id = $this->createElement('hidden', 'userId');
		$this->addElement($id);


		foreach($paramFields as $field) {
			$element = $this
				->createElement('text', 'param'.$field['id'])
				->setLabel(i18n::tr($field['name']))
				->setDecorators($this->getElementDecorator(true, false));
			$this->addElement($element);

			$useDef = $this
				->createElement('checkbox', 'isDefault'.$field['id'])
				->setDecorators($this->getElementDecorator(false, true))
				->setLabel(i18n::tr('Use Default({0})', $defParams[$field['id']]['value']))
				->setAttrib('onChange', "disableField('param".$field['id']."')");
			$this->addElement($useDef);
		}


		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick', "submitGeneric($section, '".$this->getName()."', 'user-parameters', '&save=1')")
			->setDecorators($this->getButtonDecorator(true, true, 2, 1));
		$this->addElement($submit);
	}
}
