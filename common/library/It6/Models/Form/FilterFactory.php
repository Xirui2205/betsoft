<?php

class It6_Models_Form_FilterFactory {

	public static function getForm($decoratedForm, $formFields, $trans) {
		$tr				= Zend_Registry::get('translate');
		//var_dump($tr);exit;
		$formClassName	= 'It6_Models_DecoratedForm_'.$decoratedForm;
		$form			= new $formClassName;
		$evenOdd		= 0;
		$spacer			= 0;
		$label			= '';

		$subform = new Zend_Form_SubForm();
		$subform
			->setElementsBelongTo('filter')
			->setDecorators($form->getSubFormDecorator(array('no-table')));

		$form->setDecorators($form->getFormDecorator(array('no-form')));

		foreach($formFields as $fieldArr) {
			//prepares params for form decorators so that the table is valid
			$validator		= null;
			$validatorParam	= null;
			$decorator		= null;
			$type			= null;
			$evenOdd++;

			if($evenOdd % 2 == 0) {
				$open	= false;
				$close	= true;
			}
			else {
				$open	= true;
				$close	= false;
			}
			if($evenOdd >= count($formFields) && $evenOdd % 2 == 1) {
				$close	= true;
				$spacer	= 4;
			}

			if(is_array($fieldArr[2])) {
				$arrKeys = array_keys($fieldArr[2]);
				$fieldType = reset($arrKeys);
			}
			else {
				$fieldType = $fieldArr[2];
			}
			
			if($trans) $label = $tr->trans($fieldArr[0]);
			else $label = $fieldArr[0];

			//prepares params for element definition
			if($fieldType == 'dateTime') {
				if(empty($fieldArr[5]))
					$validatorParam = null;
				$field = $form
					->createElement('text', $fieldArr[1])
					->setLabel($label)
					->setDecorators($form->getDateTimeDecorator($open, $close, 0, $spacer))
					->setAttrib('class', 'dateTime')
					->addValidator(new $fieldArr[4]($validatorParam));
			}
			elseif($fieldType == 'date') {
				$validator = (empty($fieldArr[5])
					? new $fieldArr[4]()
					: new $fieldArr[4]($fieldArr[5])
				);
				$field = $form
					->createElement('text', $fieldArr[1])
					->setLabel($label)
					->setDecorators($form->getDateDecorator($open, $close, 0, $spacer))
					->setAttrib('class', 'dateOnly')
					->addValidator($validator);
			}
			elseif($fieldType == 'select') {
				$field = $form
					->createElement('select', $fieldArr[1])
					->setLabel($label)
					->setMultiOptions($fieldArr[6])
					->setDecorators($form->getElementDecorator($open, $close, 0, $spacer));
			}
			elseif($fieldType == 'multiselect') {
				$field = $form
					->createElement('multiselect', $fieldArr[1])
					->setLabel($label)
					->setMultiOptions($fieldArr[6])
					->setDecorators($form->getElementDecorator($open, $close, 0, $spacer));
			}
			elseif($fieldType == 'textarea') {
				$evenOdd++;
				$field = $form
					->createElement($fieldType, $fieldArr[1])
					->setLabel($label)
					->setDecorators($form->getElementDecorator(true, true, 0, $spacer, 3));
			}
			else {
				$field = $form
					->createElement($fieldType, $fieldArr[1])
					->setLabel($label)
					->setDecorators($form->getElementDecorator($open, $close, 0, $spacer));
			}
			// custom element class
			if (isset($fieldArr[8]) && is_string($fieldArr[8])) {
				if(!empty($field->class)) {
					$field->class .= ' ' . $fieldArr[8];
				} else {
					$field->class = $fieldArr[8];
				}
			}
			$subform->addElement($field);
		}

		$form->addSubForm($subform, 'filter');



		$submit = $form
			->createElement('submit', 'submit', array('class' => 'btn'))
			->setLabel($tr->trans('Search'))
			->setDecorators($form->getButtonDecorator(true, true, 3, 0));
		$form->addElement($submit);

		return $form;

	}

	private static function getElement($form, $fieldArr) {
		$field = $form
			->createElement($type, $fieldArr[1])
			->setLabel($tr->trans($fieldArr[0]));
	}
}
