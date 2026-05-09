<?php

class Models_Form_HappyHourVisitConfirm extends It6_Models_DecoratedForm_Table {


	public function __construct($view) {
		parent::__construct();
		$this
			->setName('HappyHourVisitConfirm')
			->setAction($view->UrlSet(90))
			->setDecorators($this->getFormDecorator(array('no-table', 'no-form')))
			->setAttrib('class', 'form');
	}
}
