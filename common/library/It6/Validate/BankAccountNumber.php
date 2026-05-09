<?php

class It6_Validate_BankAccountNumber extends Zend_Validate_Abstract {

	const MSG_FORMAT			= 'msgFormat';
	const MSG_MISSING_BANK		= 'msgMissingBank';
	const MSG_MISSING_ACC_PREF	= 'msgMissingPref';

	private $selfFieldName;
	private $bankIdFieldName;
	private $accPrefixFieldName;

	protected $_messageTemplates = array(
		self::MSG_FORMAT			=> "%value% is not in the right format.",
		self::MSG_MISSING_ACC_PREF	=> 'If you specify an account prefix, you must specify an account number.',
		self::MSG_MISSING_BANK		=> 'If you specify your bank, you must specify an account number.',
	);


	public function __cosntruct($selfFieldName='accountNumber', $bankIdFieldName='bankId', $accPrefixFieldName='accountPrefix') {
		$this->selfFieldName		= $selfFieldName;
		$this->bankIdFieldName		= $bankIdFieldName;
		$this->accPrefixFieldName	= $accPrefixFieldName;
	}


	public function isValid($accNumber) {
		$this->_setValue($accNumber);
		$valid = true;

		if(!preg_match('/^(?:|[0-9]{2,10})$/', $accNumber)) {
			$this->_error(self::MSG_FORMAT);
			$valid = false;
		}

		if(!empty($_POST[$this->accPrefixFieldName]) && empty($_POST[$this->selfFieldName])) {
			$this->_error(self::MSG_MISSING_ACC_PREF);
			$valid = false;
		}

		if(!empty($_POST[$this->bankIdFieldName]) && empty($_POST[$this->selfFieldName])) {
			$this->_error(self::MSG_MISSING_BANK);
			$valid = false;
		}

		return $valid;
	}
}
