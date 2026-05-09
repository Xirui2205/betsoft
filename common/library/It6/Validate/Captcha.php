<?php

class It6_Validate_Captcha extends Zend_Validate_Abstract {

	const MSG_INVALID = 'msgInvalid';

	protected $_messageTemplates = array(
		self::MSG_INVALID => "'%value%' is not valid."
	);



	public function isValid($captcha) {
		$this->_setValue($captcha);

		if($_SESSION['captcha'] != $_POST['kod']) {
			$this->_error(self::MSG_INVALID);
			return false;
		}

		return true;
	}
}
