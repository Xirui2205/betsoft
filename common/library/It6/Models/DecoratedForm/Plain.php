<?php

class It6_Models_DecoratedForm_Plain extends It6_Models_DecoratedForm_Abstract {


	/**
	 * Returns decorators for Zend_Form
	 * 
	 * @param array $options elements can have values
	 * - 'no-form'
	 * @return array Zend_Form decorators
	 */
	public function getFormDecorator($options=array()) {
		$decorators[] = 'FormElements';
		
		if(!in_array('no-form', $options))
			$decorators[] = 'Form';
		
		return $decorators;
	}


	/**
	 * Returns decorators for Zend_Form_Element
	 * 
	 * @return array Zend_Form_Element decorators
	 */
	public function getElementDecorator() {
		return array(
			array('Label', array('escape' => false)),
			'ViewHelper',
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description'))
		);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Text that has a javascript date picker attached
	 * 
	 * @return array Zend_Form_Element_Text decorators
	 */
	public function getDateDecorator() {
		return array(
			array('Label', array('escape' => false, 'placement' => 'append' )),
			'ViewHelper',
			array(array('Calendar' => 'HtmlTag'),array('tag'=>'img', 'src'=>'/images/ico/calendar.gif', 'alt'=>'calendar icon', 'class'=>'calendar-icon','placement' => 'append')),
			array('Errors', array('escape' => false)),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description')),
		);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Button
	 * 
	 * @return array Zend_Form_Element_Button decorators
	 */
	public function getButtonDecorator() {
		return array(
			'ViewHelper',
			'Errors'
		);
	}


	/**
	 * Returns decorators for Zend_Form_Element_File
	 * 
	 * @return array Zend_Form_Element_File decorators
	 */
	public function getFileDecorator() {
		return array(
			array('Label', array('escape' => false)),
			'File',
			array('Errors', array('escape' => false)),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description'))
		);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Hidden
	 * 
	 * @return array Zend_Form_Element_Hidden decorators
	 */
	public function getHiddenDecorator() {
		return array(
			'ViewHelper'
		);
	}


	/**
	 * Returns decorators for Zend_Form_Element_Text that has a javascript date and time picker attached
	 * 
	 * @return array Zend_Form_Element_Text decorators
	 */
	public function getDateTimeDecorator() {
		return array(
			array('Label', array('escape' => false, 'placement' => 'append' )),
			'ViewHelper',
			array('Errors', array('escape' => false)),
			array(array('Calendar' => 'HtmlTag'),array('tag'=>'img', 'src'=>'/images/ico/calendar.gif', 'alt'=>'calendar icon', 'class'=>'calendar-icon','placement' => 'append')),
			array('Description', array('tag' => 'div', 'escape' => false, 'class' => 'description'))
		);
	}


	/**
	 * Returns decorators for Zend_Form_SubForm
	 * 
	 * @return array Zend_Form_SubForm decorators
	 */
	public function getSubFormDecorator() {
		return array(
			'formElements'
		);
	}


	/**
	 * Returns decorators for It6_Form_Element_Captcha
	 * 
	 * @return array It6_Form_Element_Captcha decorators
	 */
	public function getCaptchaDecorator() {
		$decorators = array(
			array('Label', array('escape' => false, 'placement' => 'append' )),
			new It6_Form_Decorator_Captcha,
			array('Errors', array('escape' => false)),
		);

		return $decorators;
	}
}
