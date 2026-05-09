<?php

class It6_Validate_BankAccountPrefix extends Zend_Validate_Regex {

	public function __construct() {
		parent::__construct('/^[0-9]{0,6}$/');
	}
}
