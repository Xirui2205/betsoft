<?php

class It6_Validate_CsrfToken extends Zend_Validate_Abstract {

	const MSG_TOKEN_ERROR	= 'msgTokenError';

	private $requiredHash = '';

	public function __construct($requiredHash) {
		$this->requiredHash = $requiredHash;
	}

	protected $_messageTemplates = array(
		self::MSG_TOKEN_ERROR	=> 'error_csrf_token',
	);


	public function isValid($value) {
		$this->_setValue($value);
		$valid = true;

		if(empty($value) || $this->requiredHash != $value) {
			$this->_error(self::MSG_TOKEN_ERROR);
			$valid = false;
		}

		return $valid;
	}
}
