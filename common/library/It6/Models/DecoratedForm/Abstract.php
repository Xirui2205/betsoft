<?php

abstract class It6_Models_DecoratedForm_Abstract extends It6_Models_Form_Abstract {

	abstract public function getFormDecorator();
	abstract public function getButtonDecorator();
	abstract public function getFileDecorator();
	abstract public function getHiddenDecorator();
	abstract public function getElementDecorator();
	abstract public function getSubFormDecorator();
	abstract public function getCaptchaDecorator();


	public function createElement($type, $name, $options = null) {
		if($type == 'button' || $type == 'submit' || $type == 'image')
			$options['decorators'] = $this->getButtonDecorator(true, true, 1);
		elseif($type == 'file')
			$options['decorators'] = $this->getFileDecorator();
		elseif($type == 'hidden')
			$options['decorators'] = $this->getHiddenDecorator();
		else
			$options['decorators'] = $this->getElementDecorator();

		return parent::createElement($type, $name, $options);
	}



	public function addElement($element, $name=null, $options=null) {
		if($element instanceof Zend_Form_Element_Captcha)
			$element->setDecorators($this->getCaptchaDecorator());

		return parent::addElement($element, $name, $options);
	}
}
