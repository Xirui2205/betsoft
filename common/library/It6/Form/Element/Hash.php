<?php

class It6_Form_Element_Hash extends Zend_Form_Element_Hash {


	public function __construct($elementName) {
		parent::__construct($elementName);
		$this->initCsrfValidator();
		$this->initCsrfToken();
		$this->removeDecorator('DtDdWrapper')->removeDecorator('Label');
	}

	public function initCsrfValidator() {
		$session = $this->getSession();
		if (isset($session->hash)) {
			$rightHash = $session->hash;
		}
		else {
			$rightHash = null;
		}

		$this->addValidator(new It6_Validate_CsrfToken($rightHash));
		return $this;
	}

	public function isValid($value, $context = null) {
		$valid = parent::isValid($value, $context);
		$this->setValue($this->getSession()->hash);
		return $valid;
	}
}
