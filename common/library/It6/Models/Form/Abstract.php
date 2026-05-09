<?php

class It6_Models_Form_Abstract extends Zend_Form {

	protected $ws;

	public function __construct($method = 'post',$name = 'dd') {
		$this->setDecorators($this->getFormDecorator());
		$this->setMethod($method);
		$this->setName($name);
		$this->ws = Zend_Registry::get('ws');
	}
}
