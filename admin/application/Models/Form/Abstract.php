<?php

class Models_Form_Abstract extends Zend_Form {

	protected $ws;

	public function __construct($method = 'post') {
		$this->setDecorators($this->getFormDecorator());
		$this->setMethod($method);

		$this->ws = Zend_Registry::get('ws');
	}
}
