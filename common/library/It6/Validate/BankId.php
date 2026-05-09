<?php

class It6_Validate_BankId extends Zend_Validate_Abstract {

	const MSG_MISSING		= 'msgMissing';

	private $selfFieldName;
	private $accNumberFieldName;


	protected $_messageTemplates = array(
		self::MSG_MISSING	=> 'If you specify an account number, you are required to select a bank.',
	);



	public function __construct($selfFieldName = 'bankId', $accNumberFieldName = 'accountNumber') {
		$this->selfFieldName = $selfFieldName;
		$this->accNumberFieldName = $accNumberFieldName;
	}



	public function isValid($bankId) {
		$this->_setValue($bankId);
		$valid = true;

		if(!empty($_POST[$this->accNumberFieldName]) && empty($_POST[$this->selfFieldName])) {
			$this->_error(self::MSG_MISSING);
			$valid = false;
		}

		return $valid;
	}
}
