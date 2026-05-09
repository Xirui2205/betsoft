<?php

class Models_Form_DefaultParameter extends It6_Models_DecoratedForm_Table{

	public function __construct($category, $section, $targetElement){
		parent::__construct();
		$this->setName('BranchParameterForm');

		$funcName		= 'get'.$category.'ParameterNames';
		$paramFields	= $this->ws->Parameter->$funcName();


		foreach($paramFields as $field){
			$element = $this
				->createElement('text', 'param'.$field['id'])
				->setLabel(i18n::tr($field['name']))
				->setAttrib('class', 'wide');
			$this->addElement($element);
		}


		$submit = $this
			->createElement('button', 'button')
			->setLabel(i18n::tr('Submit'))
			->setAttrib('onClick',"submitGeneric($section, 'BranchParameterForm', '$targetElement', '&save=1')");
		$this->addElement($submit);
	}
}
